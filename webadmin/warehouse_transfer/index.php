<?php
include_once __DIR__ . '/../products_new/common.inc.php';

global $DB, $_GLOBALS;

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (!function_exists('warehouseTransferWarehouses')) {
    function warehouseTransferWarehouses()
    {
        global $DB;

        $warehouses = array();
        if ($query = $DB->query(
            "SELECT `id`, `company`, `country` FROM `tbl_warehouse` WHERE `active` = 'y' ORDER BY `company` ASC"
        )) {
            while ($row = $DB->fetch_assoc($query)) {
                $name = trim((string) $row['company']);
                if ($name === '') {
                    $name = function_exists('getCountryName') ? getCountryName($row['country']) : $row['country'];
                }
                $warehouses[intval($row['id'])] = $name;
            }
            $DB->free_result($query);
        }

        return $warehouses;
    }
}

if (!function_exists('warehouseTransferStock')) {
    function warehouseTransferStock($productId, $warehouseId)
    {
        $summary = productWarehouseAverageCost($productId);
        if (isset($summary['warehouses'][$warehouseId])) {
            return $summary['warehouses'][$warehouseId];
        }

        return array(
            'warehouse_id' => intval($warehouseId),
            'warehouse'    => '',
            'stock'        => 0,
            'average_cost' => '0.00',
        );
    }
}

if (!function_exists('warehouseTransferLookup')) {
    function warehouseTransferLookup($productId, $fromWarehouse, $toWarehouse)
    {
        $product = productFromDatabase($productId);
        if (!$product) {
            return array('ok' => false, 'message' => 'Product not found');
        }

        $from = warehouseTransferStock($productId, $fromWarehouse);
        $to = warehouseTransferStock($productId, $toWarehouse);
        $weight = isset($product['weight']) ? $product['weight'] : '';
        if ($weight === '' || $weight === null) {
            $dimension = productDimensionFromDatabase($productId);
            if (is_array($dimension) && isset($dimension['weight'])) {
                $weight = $dimension['weight'];
            }
        }

        return array(
            'ok'           => true,
            'product_id'   => intval($productId),
            'product_name' => (string) $product['product_name'],
            'weight'       => ($weight === null) ? '' : (string) $weight,
            'from_stock'   => intval($from['stock']),
            'from_cost'    => $from['average_cost'],
            'to_stock'     => intval($to['stock']),
            'to_cost'      => $to['average_cost'],
        );
    }
}

if (!function_exists('warehouseTransferApply')) {
    function warehouseTransferApply($stock, $lines, $warehouses)
    {
        global $_GLOBALS;

        $errors = array();
        $from = intval(isset($stock['from_warehouse']) ? $stock['from_warehouse'] : 0);
        $to = intval(isset($stock['to_warehouse']) ? $stock['to_warehouse'] : 0);
        $reference = trim(isset($stock['reference']) ? $stock['reference'] : '');
        $currency = strtoupper(trim(isset($stock['currency']) ? $stock['currency'] : ''));
        $comment = trim(isset($stock['comment']) ? $stock['comment'] : '');

        if ($from < 1 || !isset($warehouses[$from])) {
            $errors[] = 'Choose the warehouse to transfer from.';
        }
        if ($to < 1 || !isset($warehouses[$to])) {
            $errors[] = 'Choose the warehouse to transfer to.';
        }
        if ($from > 0 && $from === $to) {
            $errors[] = 'Choose a different destination warehouse.';
        }
        if ($reference === '') {
            $errors[] = 'Enter an invoice or reference number.';
        }
        if ($currency === '') {
            $errors[] = 'Choose the invoice currency.';
        }

        $clean = array();
        $qtyByProduct = array();
        foreach ((array) $lines as $line) {
            $productId = intval(isset($line['product_id']) ? $line['product_id'] : 0);
            $qty = intval(isset($line['qty']) ? $line['qty'] : 0);
            $unitCost = trim(isset($line['unit_cost']) ? $line['unit_cost'] : '');
            $unitFreight = trim(isset($line['unit_freight']) ? $line['unit_freight'] : '');
            if ($productId < 1 && $qty < 1 && $unitCost === '') {
                continue;
            }
            if ($productId < 1 || !productFromDatabase($productId)) {
                $errors[] = 'Enter a valid product ID.';
                continue;
            }
            if ($qty < 1) {
                $errors[] = 'Enter the transfer quantity for product ' . $productId . '.';
                continue;
            }
            if ($unitCost === '' || !is_numeric($unitCost)) {
                $errors[] = 'Enter the unit price for product ' . $productId . '.';
                continue;
            }
            $transferPriceInput = trim(isset($line['transfer_price']) ? $line['transfer_price'] : '');
            if ($transferPriceInput === '' || !is_numeric($transferPriceInput)) {
                $errors[] = 'Enter the transfer price for product ' . $productId . '.';
                continue;
            }
            $transferStock = intval(isset($line['transfer_stock']) ? $line['transfer_stock'] : 0);
            $moveQty = ($transferStock > 0) ? $transferStock : $qty;
            $clean[] = array(
                'product_id'     => $productId,
                'qty'            => $qty,
                'move_qty'       => $moveQty,
                'unit_cost'      => $unitCost,
                'unit_freight'   => ($unitFreight === '') ? '0' : $unitFreight,
                'weight'         => trim(isset($line['weight']) ? $line['weight'] : ''),
                'transfer_stock' => $transferStock,
                'transfer_price' => trim(isset($line['transfer_price']) ? $line['transfer_price'] : ''),
                'current_stock'  => intval(isset($line['current_stock']) ? $line['current_stock'] : 0),
            );
            if (!isset($qtyByProduct[$productId])) {
                $qtyByProduct[$productId] = 0;
            }
            $qtyByProduct[$productId] += $moveQty;
        }
        if (!count($clean)) {
            $errors[] = 'Add at least one product to transfer.';
        }

        $summaries = array();
        if ($from > 0) {
            foreach ($qtyByProduct as $productId => $qty) {
                $summary = productWarehouseAverageCost($productId);
                $summaries[$productId] = $summary;
                $available = isset($summary['warehouses'][$from]) ? intval($summary['warehouses'][$from]['stock']) : 0;
                if ($qty > $available) {
                    $errors[] = 'Product ' . $productId . ' has ' . $available . ' available in the source warehouse.';
                }
            }
        }
        if (count($errors)) {
            return $errors;
        }

        $totalInvoice = '0.000';
        $calcWeight = '0.000';
        $prepared = array();
        foreach ($clean as $line) {
            $summary = $summaries[$line['product_id']];
            $unitCost = $line['unit_cost'];
            $unitUsd = convertCurrency($unitCost, $currency, 'USD', false, 4);
            $transferPrice = (isset($line['transfer_price']) && is_numeric($line['transfer_price'])) ? $line['transfer_price'] : $unitUsd;
            $freight = is_numeric($line['unit_freight']) ? $line['unit_freight'] : '0';
            $freightUsd = convertCurrency($freight, $currency, 'USD', false, 4);
            $totalCost = bcadd((string) $transferPrice, (string) $freightUsd, 4);
            $lineWeight = is_numeric($line['weight']) ? $line['weight'] : '0';
            $totalInvoice = bcadd($totalInvoice, bcmul((string) $line['move_qty'], (string) $transferPrice, 3), 3);
            $calcWeight = bcadd($calcWeight, bcmul((string) $line['qty'], (string) $lineWeight, 3), 3);
            $fromStock = isset($summary['warehouses'][$from]) ? intval($summary['warehouses'][$from]['stock']) : 0;
            $detail = sprintf(
                '(%s, Qty: %d, Price: %s USD)',
                $reference,
                $line['move_qty'],
                $transferPrice
            );
            $outNote = sprintf(
                'Minus Stock: Transfer from %s to %s %s',
                $warehouses[$from],
                $warehouses[$to],
                $detail
            );
            $inNote = sprintf(
                'Add Stock: Transfer from %s to %s %s',
                $warehouses[$from],
                $warehouses[$to],
                $detail
            );
            if ($comment !== '') {
                $outNote .= "\n" . $comment;
                $inNote .= "\n" . $comment;
            }
            $base = array(
                'product_id'             => $line['product_id'],
                'comments'               => $outNote,
                'available'              => intval(productGetValue($line['product_id'], 'qty')),
                'unit_cost'              => $unitCost,
                'unit_cost_usd'          => $transferPrice,
                'unit_cost_aud'          => convertCurrency($unitUsd, 'USD', 'AUD', false, 4),
                'unit_freight'           => $freight,
                'unit_freight_usd'       => $freightUsd,
                'unit_freight_aud'       => convertCurrency($freightUsd, 'USD', 'AUD', false, 4),
                'unit_currency'          => $currency,
                'unit_freight_currency'  => $currency,
                'stock_type'             => 'NEW',
                'currency'               => $currency,
                'color_id'               => 0,
                'supplier_id'            => 0,
                'total_cost'             => $totalCost,
            );
            $out = $base;
            $out['movement'] = 0 - $line['move_qty'];
            $out['warehouse_id'] = $from;
            $out['stock_left'] = 0;
            $in = $base;
            $in['movement'] = $line['move_qty'];
            $in['warehouse_id'] = $to;
            $in['stock_left'] = $line['move_qty'];

            $prepared[] = array(
                'line'     => $line,
                'unitCost' => $unitCost,
                'unitUsd'  => $unitUsd,
                'freight'  => $freight,
                'freightUsd' => $freightUsd,
                'weight'   => $lineWeight,
                'fromStock'=> $fromStock,
                'transferPrice' => $transferPrice,
                'out'      => $out,
                'in'       => $in,
                'inNote'   => $inNote,
            );
        }

        $header = array(
            'from_warehouse_id' => $from,
            'to_warehouse_id'   => $to,
            'reference'         => $reference,
            'comment'           => $comment,
            'currency'          => $currency,
            'freight_cost'      => (isset($stock['freight_cost']) && is_numeric($stock['freight_cost'])) ? $stock['freight_cost'] : '0',
            'total_cost'        => $totalInvoice,
            'calc_weight'       => $calcWeight,
            'total_weight'      => (isset($stock['total_weight']) && is_numeric($stock['total_weight'])) ? $stock['total_weight'] : $calcWeight,
        );
        $transferId = productGenericToDatabase($header, '`products_stock_transfer`');
        if (!$transferId) {
            return array('The transfer could not be saved.');
        }

        foreach ($prepared as $row) {
            $item = array(
                'transfer_id'      => $transferId,
                'product_id'       => $row['line']['product_id'],
                'qty'              => $row['line']['qty'],
                'unit_cost'        => $row['unitCost'],
                'unit_cost_usd'    => $row['unitUsd'],
                'unit_freight'     => $row['freight'],
                'unit_freight_usd' => $row['freightUsd'],
                'weight'           => $row['weight'],
                'from_stock'       => $row['fromStock'],
            );
            productGenericToDatabase($item, '`products_stock_transfer_item`');
            $outId = productGenericToDatabase($row['out'], '`products_inventory`');
            $row['in']['comments'] = $row['inNote'];
            $inId = productGenericToDatabase($row['in'], '`products_inventory`');
            if (!$outId || !$inId) {
                return array('The transfer for product ' . $row['line']['product_id'] . ' could not be saved.');
            }
            warehouseTransferAdjustStock($row['line']['product_id'], $from, $to, $row['line']['move_qty']);
            $history = array(
                'product_id'         => $row['line']['product_id'],
                'from_warehouse_id'  => $from,
                'to_warehouse_id'    => $to,
                'reference'          => $reference,
                'comment'            => $comment,
                'currency'           => $currency,
                'qty'                => $row['line']['qty'],
                'unit_cost'          => $row['unitCost'],
                'unit_cost_usd'      => $row['unitUsd'],
                'transfer_stock'     => $row['line']['move_qty'],
                'transfer_price'     => $row['transferPrice'],
                'current_stock'      => $row['line']['current_stock'],
                'from_inventory_id'  => $outId,
                'to_inventory_id'    => $inId,
            );
            productGenericToDatabase($history, '`warehouse_transfer_history`');
        }

        return array();
    }
}

if (!function_exists('warehouseTransferAdjustStock')) {
    function warehouseTransferAdjustStock($productId, $fromWarehouseId, $toWarehouseId, $moveQty)
    {
        global $_GLOBALS;

        $changes = array(
            intval($fromWarehouseId) => 0 - intval($moveQty),
            intval($toWarehouseId)   => intval($moveQty),
        );
        foreach ($changes as $warehouseId => $delta) {
            $qty = intval(inventoryStockGetValue($productId, 0, 'qty', $warehouseId)) + $delta;
            if ($qty < 0) {
                $qty = 0;
            }
            inventoryStockadd_update(array(
                'product_id'    => $productId,
                'color_id'      => 0,
                'warehouse_id'  => $warehouseId,
                'qty'           => $qty,
                'user_id'       => intval($_GLOBALS['staff']['id']),
                'supplier_id'   => 0,
                'factory_id'    => '',
                'when_sold_out' => 'active',
            ));
        }
    }
}

if ($action === 'lookup') {
    header('Content-Type: application/json');
    echo json_encode(warehouseTransferLookup(
        intval(isset($_GET['product_id']) ? $_GET['product_id'] : 0),
        intval(isset($_GET['from_warehouse']) ? $_GET['from_warehouse'] : 0),
        intval(isset($_GET['to_warehouse']) ? $_GET['to_warehouse'] : 0)
    ));
    exit;
}

$warehouses = warehouseTransferWarehouses();
$stock = array(
    'from_warehouse' => '',
    'to_warehouse'   => '',
    'reference'      => '',
    'comment'        => '',
    'currency'       => 'USD',
    'freight_cost'   => '',
    'total_weight'   => '',
);
$lines = array(array(
    'product_id'   => '',
    'qty'          => '',
    'unit_cost'    => '',
    'unit_freight' => '',
    'weight'       => '',
));
$errors = array();

if ($action === 'do_transfer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock = array_merge($stock, (array) (isset($_POST['stock']) ? $_POST['stock'] : array()));
    $lines = array_values((array) (isset($_POST['lines']) ? $_POST['lines'] : array()));
    if (!count($lines)) {
        $lines[] = array('product_id' => '', 'qty' => '', 'unit_cost' => '', 'unit_freight' => '', 'weight' => '');
    }
    $errors = warehouseTransferApply($stock, $lines, $warehouses);
    if (!count($errors)) {
        header('Location: list.php?saved=1');
        exit;
    }
}

$rates = function_exists('getExchangeRateList') ? getExchangeRateList() : array();
if (!isset($rates['USD'])) {
    $rates['USD'] = 1;
}
$currencies = productGetOptionList('cost_currency');

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
include __DIR__ . '/bar.inc.php';
?>

<?php if (count($errors)) { ?>
<div style="margin:0 auto 12px auto;width:95%;padding:8px;background:#ffd;border:1px solid #c00;color:#900;">
    <?php foreach ($errors as $error) { ?>
        <div><?=htmlspecialchars($error)?></div>
    <?php } ?>
</div>
<?php } ?>

<form id="transfer_form" action="index.php?action=do_transfer" method="post">
<?=formatHTMLTitle('Warehouse Inventory Transfer')?>
<div id="sectionTransfer">
    <table class="table_fields">
                    <tr>
                        <th>FROM</th>
                        <td>
                            <select class="required" name="stock[from_warehouse]" id="from_warehouse">
                                <option value=""></option>
                                <?=formatHTMLOptions($warehouses, $stock['from_warehouse'])?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>TO</th>
                        <td>
                            <select class="required" name="stock[to_warehouse]" id="to_warehouse">
                                <option value=""></option>
                                <?=formatHTMLOptions($warehouses, $stock['to_warehouse'])?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Invoice #/Reference #</th>
                        <td><input class="required wide" type="text" name="stock[reference]" value="<?=htmlspecialchars($stock['reference'])?>" /></td>
                    </tr>
                    <tr>
                        <th>Comments</th>
                        <td><?=formatHTMLTextarea('stock[comment]', $stock['comment'], true, 'class="wide"', 1)?></td>
                    </tr>
                    <tr>
                        <th>Invoice Currency</th>
                        <td>
                            <select class="required" name="stock[currency]" id="invoice_currency">
                                <option value=""></option>
                                <?=formatHTMLOptions($currencies, $stock['currency'])?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Freight Cost</th>
                        <td>
                            <input type="text" name="stock[freight_cost]" id="freight_cost" value="<?=htmlspecialchars($stock['freight_cost'])?>" size="10" />
                            <span class="currency-label"><?=htmlspecialchars($stock['currency'])?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Invoice Shipping Weight</th>
                        <td><input type="text" name="stock[total_weight]" id="total_weight" value="<?=htmlspecialchars($stock['total_weight'])?>" size="10" /></td>
                    </tr>
    </table>
    <br />
</div>

<?=formatHTMLTitle('Transfer', '<a href="#" id="add_product">Add Product</a>')?>
<div id="sectionProducts">
    <table class="table_grid" id="transfer_table" style="width:98%;margin:auto;">
        <tr>
            <th>Product ID</th>
            <th style="width:100%;">Product Name</th>
            <th>Qty</th>
            <th>Unit Cost Price<br />(<span class="currency-label"><?=htmlspecialchars($stock['currency'])?></span>)</th>
            <th>Unit Cost Price<br />(USD)</th>
            <th>Unit Weight<br />(kgs)</th>
            <th>Unit Freight Cost<br />(<span class="currency-label"><?=htmlspecialchars($stock['currency'])?></span>)</th>
            <th>Unit Freight Cost<br />(USD)</th>
            <th>Current Warehouse Stock</th>
            <th>Transfer Warehouse Stock</th>
            <th>Transfer Price</th>
            <th></th>
        </tr>
        <?php foreach ($lines as $index => $line) { ?>
        <tr class="transfer-row" data-row="<?=intval($index)?>">
            <td><input class="product-id" type="text" name="lines[<?=intval($index)?>][product_id]" value="<?=htmlspecialchars($line['product_id'])?>" size="8" /></td>
            <td><span class="product-name"></span></td>
            <td><input class="qty" style="text-align:right;" type="text" name="lines[<?=intval($index)?>][qty]" value="<?=htmlspecialchars($line['qty'])?>" size="4" /></td>
            <td><input class="unit-cost" style="text-align:right;" type="text" name="lines[<?=intval($index)?>][unit_cost]" value="<?=htmlspecialchars($line['unit_cost'])?>" size="8" /></td>
            <td style="text-align:right;"><span class="unit-cost-usd">0.00</span></td>
            <td><input class="weight" style="text-align:right;" type="text" readonly="readonly" name="lines[<?=intval($index)?>][weight]" value="<?=htmlspecialchars(isset($line['weight']) ? $line['weight'] : '')?>" size="8" /></td>
            <td><input class="unit-freight" style="text-align:right;" type="text" name="lines[<?=intval($index)?>][unit_freight]" value="<?=htmlspecialchars($line['unit_freight'] === '' ? '0' : $line['unit_freight'])?>" size="8" /></td>
            <td style="text-align:right;"><span class="unit-freight-usd">0.00</span></td>
            <td><input class="current-stock" type="text" readonly="readonly" name="lines[<?=intval($index)?>][current_stock]" value="" size="6" /></td>
            <td><input class="from-stock" style="text-align:right;" type="text" name="lines[<?=intval($index)?>][transfer_stock]" value="" size="6" /></td>
            <td><input class="from-cost" style="text-align:right;" type="text" name="lines[<?=intval($index)?>][transfer_price]" value="" size="8" /></td>
            <td><a href="#" class="remove-row">Delete</a></td>
        </tr>
        <?php } ?>
        <tr class="totals">
            <th></th>
            <th style="text-align:right;">TOTALS:</th>
            <th style="text-align:right;" id="total_qty">0</th>
            <th style="text-align:right;" id="total_cost">0.00</th>
            <th style="text-align:right;" id="total_cost_usd">0.00</th>
            <th style="text-align:right;" id="total_line_weight">0.000</th>
            <th style="text-align:right;" id="total_freight">0.00</th>
            <th style="text-align:right;" id="total_freight_usd">0.00</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </table>
    <br />
    <div style="width:98%;text-align:right;">
        <input type="button" id="adjust_freight" value="Automatically adjust freight costs" />
    </div>
    <br />
</div>
<div style="text-align:center;"><input id="transfer_button" type="button" value="Transfer Inventory" /></div>
</form>

<script type="text/javascript">
var transferRates = <?=json_encode($rates)?>;
var transferRowCount = <?=count($lines)?>;

function transferMoney(value) {
    var amount = parseFloat(value);
    if (isNaN(amount)) {
        return '0.00';
    }
    return amount.toFixed(2);
}

function transferToUsd(value) {
    var currency = $('#invoice_currency').val() || 'USD';
    var rate = parseFloat(transferRates[currency]);
    var usdRate = parseFloat(transferRates.USD);
    if (isNaN(rate) || rate === 0) {
        rate = 1;
    }
    if (isNaN(usdRate) || usdRate === 0) {
        usdRate = 1;
    }
    return transferMoney((parseFloat(value) || 0) * rate / usdRate);
}

function transferAdjustFreight() {
    var shipmentFreight = parseFloat($('#freight_cost').val());
    if (isNaN(shipmentFreight)) {
        shipmentFreight = 0;
    }
    var shipmentWeight = 0;
    $('.transfer-row').each(function () {
        shipmentWeight += (parseFloat($(this).find('.qty').val()) || 0) * (parseFloat($(this).find('.weight').val()) || 0);
    });
    if (shipmentWeight <= 0) {
        alert('Enter a product weight before splitting the freight cost.');
        return;
    }
    $('.transfer-row').each(function () {
        var weight = parseFloat($(this).find('.weight').val()) || 0;
        $(this).find('.unit-freight').val(transferMoney(shipmentFreight * weight / shipmentWeight));
    });
    transferRecalc();
}

function transferRecalc() {
    var qtyTotal = 0;
    var costTotal = 0;
    var costUsdTotal = 0;
    var weightTotal = 0;
    var freightTotal = 0;
    var freightUsdTotal = 0;
    var currency = $('#invoice_currency').val() || 'USD';
    $('.currency-label').text(currency);

    $('.transfer-row').each(function () {
        var qty = parseInt($(this).find('.qty').val(), 10);
        var cost = parseFloat($(this).find('.unit-cost').val());
        var weight = parseFloat($(this).find('.weight').val());
        var freight = parseFloat($(this).find('.unit-freight').val());
        if (isNaN(qty)) qty = 0;
        if (isNaN(cost)) cost = 0;
        if (isNaN(weight)) weight = 0;
        if (isNaN(freight)) freight = 0;
        var costUsd = transferToUsd(cost);
        var freightUsd = transferToUsd(freight);
        $(this).find('.unit-cost-usd').text(costUsd);
        $(this).find('.unit-freight-usd').text(freightUsd);
        qtyTotal += qty;
        costTotal += qty * cost;
        costUsdTotal += qty * parseFloat(costUsd);
        weightTotal += qty * weight;
        freightTotal += qty * freight;
        freightUsdTotal += qty * parseFloat(freightUsd);
    });

    $('#total_qty').text(qtyTotal);
    $('#total_cost').text(transferMoney(costTotal));
    $('#total_cost_usd').text(transferMoney(costUsdTotal));
    $('#total_line_weight').text(weightTotal.toFixed(3));
    $('#total_freight').text(transferMoney(freightTotal));
    $('#total_freight_usd').text(transferMoney(freightUsdTotal));
    if (!$('#total_weight').data('touched')) {
        $('#total_weight').val(weightTotal ? weightTotal.toFixed(3) : '');
    }
}

function transferLookup($row, keepCost) {
    var productId = $.trim($row.find('.product-id').val());
    if (productId === '') {
        $row.find('.product-name').text('');
        $row.find('.current-stock, .from-stock, .from-cost').val('');
        return;
    }
    $.getJSON('index.php', {
        action: 'lookup',
        product_id: productId,
        from_warehouse: $('#from_warehouse').val(),
        to_warehouse: $('#to_warehouse').val()
    }, function (data) {
        if (!data || !data.ok) {
            $row.find('.product-name').text(data && data.message ? data.message : 'Product not found');
            $row.find('.current-stock, .from-stock').val('0');
            $row.find('.from-cost').val('');
            return;
        }
        $row.find('.product-name').text(data.product_name);
        if (!$row.find('.weight').val() && data.weight) {
            $row.find('.weight').val(data.weight);
        }
        if (parseInt(data.from_stock, 10) < 1) {
            alert('This product has no stock in the selected warehouse.');
            $row.find('input').val('');
            $row.find('.unit-freight').val('0');
            $row.find('.product-name').text('');
            transferRecalc();
            return;
        }
        $row.find('.current-stock, .from-stock').val(data.from_stock);
        transferRecalc();
    });
}

function transferAddRow() {
    var index = transferRowCount++;
    var row = ''
        + '<tr class="transfer-row" data-row="' + index + '">'
        + '<td><input class="product-id" type="text" name="lines[' + index + '][product_id]" value="" size="8" /></td>'
        + '<td><span class="product-name"></span></td>'
        + '<td><input class="qty" style="text-align:right;" type="text" name="lines[' + index + '][qty]" value="" size="4" /></td>'
        + '<td><input class="unit-cost" style="text-align:right;" type="text" name="lines[' + index + '][unit_cost]" value="" size="8" /></td>'
        + '<td style="text-align:right;"><span class="unit-cost-usd">0.00</span></td>'
        + '<td><input class="weight" style="text-align:right;" type="text" readonly="readonly" name="lines[' + index + '][weight]" value="" size="8" /></td>'
        + '<td><input class="unit-freight" style="text-align:right;" type="text" name="lines[' + index + '][unit_freight]" value="0" size="8" /></td>'
        + '<td style="text-align:right;"><span class="unit-freight-usd">0.00</span></td>'
        + '<td><input class="current-stock" type="text" readonly="readonly" name="lines[' + index + '][current_stock]" value="" size="6" /></td>'
        + '<td><input class="from-stock" style="text-align:right;" type="text" name="lines[' + index + '][transfer_stock]" value="" size="6" /></td>'
        + '<td><input class="from-cost" style="text-align:right;" type="text" name="lines[' + index + '][transfer_price]" value="" size="8" /></td>'
        + '<td><a href="#" class="remove-row">Delete</a></td>'
        + '</tr>';
    $('#transfer_table tr.totals').before(row);
    return index;
}

function setProductData(dataval) {
    var data = (dataval.item_list && dataval.item_list.length) ? dataval.item_list[0] : dataval;
    var rowId = dataval.row_id;
    var $row = $('.transfer-row').filter(function () {
        return String($(this).attr('data-row')) === String(rowId);
    });
    if (!$row.length) {
        $row = $('.transfer-row').last();
    }
    if (data.warehouse_id && String($('#from_warehouse').val()) !== String(data.warehouse_id)) {
        alert('Select the product again for the current source warehouse.');
        return;
    }
    if (data.warehouse_stock !== undefined && parseInt(data.warehouse_stock, 10) < 1) {
        alert('This product has no stock in the selected warehouse.');
        return;
    }
    var productQty = (data.qty !== undefined && data.qty !== null) ? data.qty : '';
    var productCost = data.product_costus || data.product_cost || data.cost_price || '';
    if (productCost !== '' && !isNaN(parseFloat(productCost))) {
        productCost = parseFloat(productCost).toFixed(2);
    }
    $row.find('.product-id').val(data.product_id || data.id || '');
    $row.find('.product-name').text(data.product_name || '');
    $row.find('.qty').val(productQty);
    $row.find('.unit-cost').val(productCost);
    $row.find('.from-cost').val('');
    $row.find('.weight').val(data.product_weight || data.weight || '');
    if (data.warehouse_stock !== undefined) {
        $row.find('.current-stock, .from-stock').val(data.warehouse_stock);
    }
    transferLookup($row, true);
}

function transferValidate() {
    var pass = true;
    $('#transfer_form .required').each(function () {
        if ($(this).val() === '') {
            pass = false;
            $(this).css('background', '#faa').css('border', '2px solid #f00');
        } else {
            $(this).css('background', '').css('border', '');
        }
    });
    if ($('#from_warehouse').val() !== '' && $('#from_warehouse').val() === $('#to_warehouse').val()) {
        pass = false;
        $('#to_warehouse').css('background', '#faa').css('border', '2px solid #f00');
        alert('Choose a different destination warehouse.');
        return false;
    }
    var products = 0;
    var stockPass = true;
    $('.transfer-row').each(function () {
        var productId = $.trim($(this).find('.product-id').val());
        var qty = parseInt($(this).find('.qty').val(), 10);
        if (productId === '' && (isNaN(qty) || qty < 1)) {
            return;
        }
        products++;
        var unitCost = $.trim($(this).find('.unit-cost').val());
        var transferPrice = $.trim($(this).find('.from-cost').val());
        if (productId === '' || isNaN(qty) || qty < 1 || unitCost === '' || transferPrice === '') {
            pass = false;
            $(this).find('.product-id, .qty, .unit-cost, .from-cost').css('background', '#faa');
        }
        var moveQty = parseInt($(this).find('.from-stock').val(), 10);
        if (isNaN(moveQty) || moveQty < 1) {
            moveQty = qty;
        }
        var available = parseInt($(this).find('.current-stock').val(), 10);
        if (!isNaN(available) && moveQty > available) {
            stockPass = false;
            $(this).find('.from-stock, .qty').css('background', '#faa');
        }
    });
    if (!pass) {
        alert('Fill the highlighted fields before transferring.');
        return false;
    }
    if (!products) {
        alert('Add at least one product to transfer.');
        return false;
    }
    if (!stockPass) {
        alert('One product quantity is higher than the source warehouse stock.');
        return false;
    }
    return confirm('Transfer this inventory between the selected warehouses?');
}

$(function () {
    $('#add_product').click(function () {
        if ($('#from_warehouse').val() === '') {
            alert('Select the warehouse to transfer from before adding a product.');
            $('#from_warehouse').css('background', '#faa').css('border', '2px solid #f00');
            return false;
        }
        $('#from_warehouse').css('background', '').css('border', '');
        var $empty = $('.transfer-row').filter(function () {
            return $.trim($(this).find('.product-id').val()) === '';
        }).first();
        var rowId = $empty.length ? $empty.attr('data-row') : transferAddRow();
        window.open('/webadmin/products_new/master_products.php?return_message=addstock&row_id=' + encodeURIComponent(rowId) + '&from_warehouse=' + encodeURIComponent($('#from_warehouse').val()), 'transfer_products', 'scrollbars=yes,resizable=yes,width=1100,height=700');
        return false;
    });
    $(document).on('change', '.product-id', function () {
        var $row = $(this).closest('tr');
        if ($('#from_warehouse').val() === '') {
            alert('Select the warehouse to transfer from before adding a product.');
            $(this).val('');
            $row.find('.product-name').text('');
            $('#from_warehouse').css('background', '#faa').css('border', '2px solid #f00');
            return;
        }
        $row.find('.unit-cost').val('');
        $row.find('.weight').val('');
        transferLookup($row, false);
    });
    $(document).on('input change', '.qty, .unit-cost, .unit-freight', transferRecalc);
    $('#from_warehouse, #to_warehouse').change(function () {
        $('.transfer-row').each(function () {
            transferLookup($(this), true);
        });
    });
    $('#invoice_currency, #freight_cost').on('change input', transferRecalc);
    $('#adjust_freight').click(transferAdjustFreight);
    if ($('#total_weight').val() !== '') {
        $('#total_weight').data('touched', 1);
    }
    $('#total_weight').on('input', function () {
        $(this).data('touched', 1);
    });
    $(document).on('click', '.remove-row', function () {
        var $rows = $('.transfer-row');
        if ($rows.length === 1) {
            $rows.find('input').val('');
            $rows.find('.unit-freight').val('0');
            $rows.find('.product-name').text('');
        } else {
            $(this).closest('tr').remove();
            var $first = $('.transfer-row').first();
            if ($.trim($first.find('.product-id').val()) !== '') {
                transferLookup($first, true);
            }
        }
        transferRecalc();
        return false;
    });
    $('#transfer_button').click(function () {
        if (transferValidate()) {
            $('#transfer_form').submit();
        }
    });
    $('.transfer-row').each(function () {
        if ($.trim($(this).find('.product-id').val()) !== '') {
            transferLookup($(this), true);
        }
    });
    transferRecalc();
});
</script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
