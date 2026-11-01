<?php
/**
 * @var mixed[] $product
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;

$inven_id  = $item['id'];
$inven_sub = $item['sub_id'];
if ($add) {
    $inven_id = $link_id;
}

if ($product['link_id'] > 0) {
    $inven_id = $item['link_id'];
}

if ($product['same_as'] > 0) {
    $inven_id  = $item['same_as'];
    $inven_sub = $item['same_as_sub'];
}
$inven_product  = productFromDatabase($inven_id);
$res_stock      = productReservedFromDatabase($inven_id);
$reserved_stock = productReservedStock($inven_id);
$stock           = $inven_product['qty'];
$available_stock = $stock - $reserved_stock;
?>

<script type="text/javascript">
//<![CDATA[
function updateCostPrice(new_value)
{
	setValue('pricing[cost_price_au]', new_value);
	setValue('pricing[cost_price]', convertCurrency(new_value, 'AUD', 'USD'));
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionInventory\');">Inventory</a>')?>

<div id="sectionInventory">
	<input type="hidden" name="inventory[supplier_id]" value="<?=$product['supplier']?>" />

	<table class="table_fields" style="margin:auto;width:95%;">
		<tr>
			<th>Current Warehouse Stock</th>
			<td style="width:25%;"><input<?=('y' == $inven_product['alert_flag']) ? ' style="border:3px dashed #f00;"' : ''?> type="text" name="stock" value="<?=$stock?>" readonly="readonly" class="wide number" /></td>
			<th>Alert Level</th>
			<td style="width:25%;"><input type="text" name="products[alert_level]" value="<?=$inven_product['alert_level']?>" class="wide number" /></td>
		</tr>
		<tr>
			<th>Reserved Stock</th>
			<td><input type="text" name="reserved" value="<?=$reserved_stock?>" readonly="readonly" class="wide number" /></td>
			<th></th>
			<td class="unfinished"><i>When stock drops below this level, Email is sent to Purchasing Dept</i></td>
		</tr>
		<tr>
			<th>Available Stock For Sale</th>
			<td style="border:2px solid #f00;"><input type="text" name="available" value="<?=$available_stock?>" readonly="readonly" class="wide number" /></td>
			<th>Enable Alerts</th>
			<td><select name="products[alert_enable]" class="wide">
				<option value=""></option>
				<?=formatHTMLOptions(productGetOptionList('alert_enable'), $inven_product['alert_enable'])?>
			</select></td>
		</tr>
		<tr>
			<th></th>
			<td></td>
			<th>Recommended Purchase Quantity</th>
			<td><input type="text" name="products[alert_recommend]" value="<?=$inven_product['alert_recommend']?>" class="wide number" /></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td></td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Make Adjustment (+ / -)</th>
			<td><input name="inventory[movement]" type="text" onchange="adjustment()" class="wide number" /></td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Unit Purchase Price (AUD)</th>
			<td><input name="inventory[unit_value_aud]" type="text" class="wide number" /></td>
			<th>Unit Purchase Price (USD)</th>
			<td><input name="inventory[unit_value_usd]" onchange="" type="text" class="wide number" /></td>
		</tr>
		<tr>
			<th></th>
			<td><input type="button" onclick="updatePrice('unit_value','AUD','USD')" value="Update(USD)" class="wide" /></td>
			<th></th>
			<td><input type="button" onclick="updatePrice('unit_value','USD','AUD')" value="Update(AUD)" class="wide" /></td>
		</tr>
		<tr>
			<th>Unit Freight Cost (AUD)</th>
			<td><input name="inventory[unit_freight_aud]" type="text" class="wide number" /></td>
			<th>Unit Freight Cost (USD)</th>
			<td><input name="inventory[unit_freight_usd]" type="text" class="wide number" /></td>
		</tr>
		<tr>
			<th></th>
			<td><input type="button" onclick="updatePrice('unit_freight','AUD','USD')" value="Update(USD)" class="wide" /></td>
			<th></th>
			<td><input type="button" onclick="updatePrice('unit_freight','USD','AUD')" value="Update(AUD)" class="wide" /></td>
		</tr>
		<tr>
			<th>Comments</th>
			<td colspan="3"><input name="inventory[comments]" type="text" class="wide" /></td>
		</tr>
	</table>

<div id="powerSupply" style="display:none">
<table class="wide" align="center" style="width:95%">
	<tr>
		<td class="left_title">Type of Power Supply :</td>
		<td colspan="3">
			<input type="radio" name="power_supply" value="110" checked="checked" /> 110v <input type="radio" name="power_supply" value="240"> 240v
			<input type="radio" name="power_supply" value="none" /> None
		</td>
	</tr>
</table>
</div>
<div id="stockSold" style="display:none">
<table class="wide" align="center" style="width:95%">
	<tr>
		<td class="left_title">Stock :</td>
		<td colspan="3">
			<input type="radio" name="stock_sold" value="new" checked="checked" /> New <input type="radio" name="stock_sold" value="used"> Floor/Used
		</td>
	</tr>
</table>
</div>
	<br /><br />

<?php
if (!isset($sections) || !is_array($sections)) {
    $sections = userPermittedSections($_SERVER['REMOTE_USER'], 9);
}

$o_inventory = productInventoryFromDatabase($inven_id);
if (count($o_inventory) > 0 && !$add) {
?>
<table class="table_grid wide" style="width:95%" border="1" cellspacing="0" cellpadding="4" align="center">
	<tr><th colspan="<?=(in_array('ADMIN', $sections)) ? 5 : 4?>" align="center"><strong>Inventory Control</strong></th></tr>
	<tr style="background-color:#E3EBF7;text-align:center;">
		<td style="width:5%;"><b>Date</b></td>
		<td style="width:1<?=(in_array('ADMIN', $sections)) ? '0' : '5'?>%"><b>Movement</b></td>
		<td style="width:<?=(in_array('ADMIN', $sections)) ? '65' : '70'?>%"><b>Description</b></td>
		<td style="width:10%;"><b>Available</b></td>
<?php
if (in_array('ADMIN', $sections)) {
?>
		<td style="width:10%;"><b>Edit Pricing</b></th>
<?php
}
?>
	</tr>
<?php
$count     = 0;
    $inventory = array_chunk($o_inventory, 5);
    $inventory = $inventory[0];
    foreach ($inventory as $each) {
        $movement = $each['movement'];
        if ($movement < 0) {
            $movement = '- ';
        } else {
            $movement = '+ ';
        }

        $movement .= abs($each['movement']);
        $comments = htmlspecialchars($each['comments']);

        if (preg_match("/(I([A-Z][A-Z])?|Q[A-Z][A-Z])#([0-9]+)/", $comments, $matches)) {
            $invoice_id = intval($matches[3]);
            $invoice    = invoiceFromDatabase($invoice_id);
            if ('' != $invoice['invoice_id']) {
                $tooltip   = array();
                $tooltip[] = '<div style="font-size:9px;">Invoice To :<br />' . nl2br($invoice['bill_address_full']) . '</div>';
                foreach (invoiceProductsFromDatabase($invoice_id) as $prod) {
                    $tooltip[] = '<div style="font-size:9px;width:110px;">' . $prod['product_qty'] . ' x ' . productFormatNumber($prod['product_id'], $prod['sub_id']) . ': ' . $prod['product_name'] . ' @ ' . convertCurrency($prod['product_offer'], $invoice['invoice_currency']) . ' = ' . getCurrencySymbol($invoice['invoice_currency'], 'HTML') .' '. convertCurrency($prod['product_offer'] * $prod['product_qty'], $invoice['invoice_currency'])  . $invoice['invoice_currency'] . '</div>';
                }

                if ('' != $invoice['ship_product_name']) {
                    $tooltip[] = '<div style="font-size:10px;">1 x Freight: ' . $invoice['ship_product_name'] . ' @ ' . convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) . ' = ' . getCurrencySymbol($invoice['invoice_currency'], 'HTML') . ' ' . convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) .  $invoice['invoice_currency'] . '</div>';
                }

                $tooltip  = '<div style="width:120px;">' . implode('', $tooltip) . '</div>';
                $comments = str_replace($matches[0], '<a href="/webadmin/invoice/?action=view&amp;invoice_id=' . $invoice_id . '" target="_blank" data-bs-toggle="tooltip" data-bs-html="true" title="' . htmlspecialchars($tooltip) . '">' . $matches[0] . '</a>', $comments);
            }
        }
?>
	<tr style="text-align:center;" <?=($count++ % 2 == 1) ? 'class="alt"' : ''?>>
		<td style="font-size:11px;"><?=date("d/m/Y", strtotime($each['created_at']))?></td>
		<td style="font-size:11px;"><?=$movement?></td>
		<td style="text-align:left;font-size:11px;"><?=$comments?></td>
		<td style="font-size:11px;"><?=$each['available']?></td>
<?php
if (in_array('ADMIN', $sections)) {
?>
		<td>
<?php
if ($each['movement'] > 0) {
?>
			<input type="button" value="EDIT"<?=($each['stock_left'] <= 0) ? ' disabled="disabled"' : ''?> onclick="openWindow('product_modify_inventory.php?inventory_id=<?=$each['id']?>', 800, 300, true);return false" />
<?php
}
?>
		</td>
<?php
}
?>
	</tr>
<?php
}
?>
</table>

<div class="text-center" style="text-align:center;margin-top: 5px;">
<?php
//if (count($o_inventory) > 5) {
?>
	<input type="button" value="View All (<?=count($o_inventory)?>)" onclick="window.open('products_inventory.php?id=<?=$inven_id?>&sub_id=<?=$inven_sub?>')" />
<?php
//}
?>
	<input type="button" value="Print" onclick="window.open('products_inventory.php?print=true&id=<?=$inven_id?>&sub_id=<?=$inven_sub?>')" />
</div>

<?php
}
if (!$add && $reserved_stock > 0) {
    // display the reserved stock in a table form...
?>
	<br /><table class="wide" style="width:95%" border="1" bordercolor="#CCCCCC" cellspacing="0" cellpadding="4" align="center">
		<tr><th colspan="4" align="center"><b>Reserved Stock</b></td></tr>
		<tr style="background-color:#E3EBF7">
			<td width="15%" align="center" nowrap="nowrap"><b>Date</b></td>
			<td width="15%" align="center" nowrap="nowrap"><b>Amount Reserved</b></td>
			<td width="70%" align="center" nowrap="nowrap"><b>Invoice Details</b></td>
		</tr>
<?php
$count = 0;
    // firstly have to sort the invoice list...
    $inCache = array();
    $times   = array();
    foreach ($res_stock as $invoice_id => $qty) {
        $tmp                  = invoiceFromDatabase($invoice_id);
        $inCache[$invoice_id] = $tmp;
        $times[$invoice_id]   = $tmp['open_time'];
    }
    arsort($times);
    foreach ($times as $invoice_id => $time) {
        $qty                = $res_stock[$invoice_id];
        $invoice            = $inCache[$invoice_id];
        $order_products     = invoiceProductsFromDatabase($invoice_id);
        $products_tooltip   = array();
        $products_tooltip[] = '<tr style="font-size:10px;"><td colspan="8">Invoice To :<br />' . nl2br($invoice['bill_address_full']) . '</td></tr>';
        foreach ($order_products as $prod) {
            $products_tooltip[] = '<tr style="font-size:10px;">' . '<td style="text-align:right;">' . $prod['product_qty'] . '</td>' . '<td>x</td>' . '<td nowrap="nowrap">#' . $prod['product_id'] . ': ' . $prod['product_name'] . '</td>' . '<td>@</td>' . '<td style="text-align:right;">' . convertCurrency($prod['product_offer'], $invoice['invoice_currency']) . '</td>' . '<td>=' . $symbol . '</td>' . '<td style="text-align:right;">' . convertCurrency($prod['product_offer'] * $prod['product_qty'], $invoice['invoice_currency']) . '</td>' . '<td>' . $invoice['invoice_currency'] . '</td>' . '</tr>';
        }
        $ship_offer = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
        if ('' != $invoice['ship_product_name']) {
            $products_tooltip[] = '<tr style="font-size:10px;"><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $invoice['invoice_currency'] . '</td></tr>';
        }

        $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
?>
		<tr style="text-align:center;"<?=($count++ % 2 == 1) ? ' class="alt"' : ''?>>
			<td><?=date('d/m/Y', $invoice['open_time'])?></td>
			<td><?=$qty?></td>
			<td style="text-align:left;" data-bs-toggle="tooltip" data-bs-html="true" title="<?=htmlspecialchars($products_tooltip)?>">Stock Reserved for <a href="/webadmin/invoice/?action=view&invoice_id=<?=$invoice_id?>" target="_blank"><?=invoiceFormatNumber($invoice_id, 0, $invoice['site_id'])?></a> : <?=$invoice['bill_name']?></td>
		</tr>
<?php
}
?>
		<tr style="text-align:center;"<?=($count++ % 2 == 1) ? ' class="alt"' : ''?>><td><b>Total Reserved Stock :</b></td><td><b><?=$reserved_stock?></b></td><td></td></tr>
	</table>
<?php
}
?>

<div style="width: 95%; margin: 10px auto; text-align: left; border: 2px solid red; padding: 10px;">
	<b style="color: red">Inventory Check</b> <input type="checkbox" value="true" name="inventory_check" onchange="inventoryCheck(this);" />

<?php
if ($inven_product['inventory_check_by']) {
?>
		<span class="inventory-check-label" id="inventory-check-old">
			<b><?=date('d/m/Y', strtotime($inven_product['inventory_check_date']))?></b> (by <b><?=getStaffName($inven_product['inventory_check_by'])?></b>)
		</span>
<?php
}
?>

	<span class="inventory-check-label" id="inventory-check-new" style="display: none">
		<b><?=date('d/m/Y')?></b> (by <b><?=htmlspecialchars(ucwords($_GLOBALS['staff']['first_name']))?></b>)
	</span>

	<span style="font-size: 10px;">(Please make sure product location is also checked and confirmed)</span>

	<script>
		function inventoryCheck(input)
		{
			if ($(input).is(":checked")) {
				$('.inventory-check-label').show();
			} else {
				$('.inventory-check-label').hide();
				$('#inventory-check-old').remove();
			}
		}
	</script>

</div>

</div><br />

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInventory_hideNote">
	Product Inventory can be viewed by Clicking "Inventory" above<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
