<?php
include_once __DIR__ . '/common.inc.php';

$inventory_id = $_REQUEST['inventory_id'];
$action       = $_REQUEST['action'];

function editInventory($inventory_id)
{
    $supplier_list = productGetOptionList('supplier_id');

    $inventory_id = intval($inventory_id);
    if (!$inventory_id) {
        die("Invalid Id passed to this page.");
    }

    $inventory        = productInventorySingleFromDatabase($inventory_id);
    $id               = intval($inventory['product_id']);
    $formatted_number = productFormatNumber($id);
    $item             = productFromDatabase($id);
    $dPoints          = $item['precision'];
?>
<html>
    <head>
        <title>Modify Inventory for Product ID# <?= $formatted_number ?></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
    </head>
    <body>
        <div class="product_content">
        <h4>Modify Inventory for Product ID# <?= $formatted_number ?></h4>
        <form action="" method="POST">
        <table class="table_fields">
            <input type="hidden" name="inventory_id" value="<?= $inventory_id ?>" />
            <input type="hidden" name="product_id" value="<?= $id ?>" />
            <input type="hidden" name="action" value="do_edit" />

            <tr>
                <th>Units Bought :</th>
                <td style="width:25%;"><?= $inventory['movement'] ?></td>
                <th>Total Available (when added) :</th>
                <td style="width:25%;"><?= $inventory['available'] ?></td>
            </tr>
            <tr>
                <th>Bought From :</th>
                <td style="width:25%;"><?php hecho($supplier_list[$inventory['supplier_id']]) ?></td>
                <th>Stock Left :</th>
                <td style="width:25%;"><?= $inventory['stock_left'] ?></td>
            </tr>
            <tr>
                <th>Date Added :</th>
                <td style="width:25%;"><?= date(_('Y-m-d'), strtotime($inventory['created_at'])) ?></td>
                <th>Added By :</th>
                <td style="width:25%;"><?= getStaffName($inventory['staff']) ?></td>
            </tr>
            <tr>
                <th>Comments :</th>
                <td rowspan="2" colspan="3"> <input type="text" name="inventory[comments]" value="<?php hecho($inventory['comments']);?>" style="width:100%;"/></td>
            </tr>
            <tr><th>&nbsp;</th></tr>
            <tr>
                <th>Unit Purchase Price :</th>
                <td><input name="inventory[unit_cost]" type="text" style="width:100%;" value="<?= convertCurrency($inventory['unit_cost'], $inventory['unit_currency']) ?>"></td>
                <th>Unit Freight Costs :</th>
                <td><input name="inventory[unit_freight]" type="text" style="width:100%;" value="<?= convertCurrency($inventory['unit_freight'], $inventory['unit_currency']) ?>"></td>
            </tr>
            <tr>
                <th>Unit Purchase Currency :</th>
                <td colspan="3"><select name="inventory[unit_currency]" style="width:100%;"><?= formatHTMLOptions(getCurrencyList(false), $inventory['unit_currency']) ?></select></td>
            </tr>
            <tr><td colspan="4" style="text-align:center;"><input type="submit" style="width:150px;" value=" Update " /><br /><i><small>NB: Overwriting these values will automatically adjust the Average Cost Price for this product</small></i></td></tr>
        </table>
        </form>
        </div>
    </body>
</html>
<?php
}

function doEdit($posted)
{
    global $_GLOBALS;
    global $DB;
    $inventory    = $posted['inventory'];
    $inventory_id = intval($posted['inventory_id']);
    if (is_array($inventory) && count($inventory) && $inventory_id > 0) {
        $id               = intval($posted['product_id']);
        $formatted_number = productFormatNumber($id);
        $dPoints          = productGetValue($id, 'precision');
        $unit_cost_usd    = convertCurrency($inventory['unit_cost'], $inventory['unit_currency'], 'USD', false, $dPoints);
        $unit_freight_usd = convertCurrency($inventory['unit_freight'], $inventory['unit_currency'], 'USD', false, $dPoints);
        $cur              = $inventory['unit_currency'];
        $comments         = $inventory['comments'] . ' - modified ' . date(_('Y-m-d')) . ', by ' . $_GLOBALS['staff']['name'] . ' - New Prices (Cost: $' . $inventory['unit_cost'] . ' ' . $cur . ' + Freight: $' . $inventory['unit_freight'] . ' ' . $cur . (('USD' != $cur) ? ', Cost: $' . $unit_cost_usd . ' USD + Freight: $' . $unit_freight_usd . ' USD' : '') . ')';

        productInventorySingleToDatabase([
            'id'               => $inventory_id,
            'unit_cost'        => $inventory['unit_cost'],
            'unit_cost_usd'    => $unit_cost_usd,
            'unit_freight'     => $inventory['unit_freight'],
            'unit_freight_usd' => $unit_freight_usd,
            'unit_currency'    => $inventory['unit_currency'],
            'comments'         => $comments,
        ]);
?>
<html>
    <head>
        <title>Modify Inventory for Product ID# <?= $formatted_number ?></title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript">
//<![CDATA[
function updateCostPrices()
{
    var cost_price='<?= $needed[0] ?>';
    if(window.opener!=null && window.opener.updateCostPrice!=null)
        window.opener.updateCostPrice(cost_price);
}
//]]>
        </script>
    </head>
    <body>
        <div class="product_content" style="width:100%;">
            <div style="background:#ECECEC;border:1px solid blue;text-align:center;">
                Inventory for product (<?= $formatted_number ?>) has been updated successfully. You will see the updated cost prices when you click the "Close Window" button below.<br />
                <input type="button" value=" Close Window " onclick="updateCostPrices();window.close();" />
            </div>
        </div>
    </body>
</html>
<?php

    } else {
        die('Invalid arguments passed to this page..');
    }

}

// main switch-case
switch ($action) {
    case 'do_edit':
        doEdit($_REQUEST);
        break;
    case '':
    default:
        editInventory($inventory_id);
        break;
}
