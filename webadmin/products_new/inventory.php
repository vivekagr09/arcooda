<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceFromDatabase

global $_GLOBALS;

$get_vars['return_message'] = true;

include_once __DIR__ . '/header.inc.php';

if (!isset($allowed_sections) || !is_array($allowed_sections)) {
    $allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
}
?>

<script type="text/javascript">
//<![CDATA[
function printpage()
{
    if (window.print) window.print();
    else alert("To print this page, select Print Frame from the file menu.");
}
function doPrint()
{
    if (confirm("Click OK to print this document")) printpage();
}
//]]>
</script>

<?php
if ('' != $_REQUEST['print']) {
    $style = "background:#fff;";
?>
<body onLoad="doPrint()" style="<?=$style?>">
<?php
}
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
<?php
$id        = intval($_REQUEST['id']);
$inventory = productInventoryFromDatabase($id);
if (count($inventory) > 0) {
?>
<table class="table_grid">
    <tr><th style="<?=$style?>" colspan="<?=(has_permission('STOCK', $allowed_sections)) ? 5 : 4?>"><b>Inventory Control for Product ID <?=$id?></b></td></tr>
    <tr style="<?=('' != $style) ? $style : 'background:#E3EBF7'?>">
        <td width="5%"><b>Date</b></td>
        <td width="1<?=(has_permission('STOCK', $allowed_sections)) ? '0' : '5'?>%"><b>Movement</b></td>
        <td width="<?=(has_permission('STOCK', $allowed_sections)) ? '65' : '70'?>%"><b>Comments</b></td>
        <td width="5%"><b>Available</b></td>
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
        <td style="width:10%;"><b>Edit Pricing</b></th>
<?php
}
?>
    </tr>
<?php
$count = 0;
    foreach ($inventory as $each) {
        $movement = $each['movement'];
        if ($movement < 0) {
            $movement = '- ';
        } else {
            $movement = '+ ';
        }

        $movement .= abs($each['movement']);
        $comments = hecho($each['comments'], 1);

        if (preg_match("/(I([A-Z][A-Z])?|Q[A-Z][A-Z])#([0-9]+)/", $comments, $matches)) {
            $invoice_id = intval($matches[3]);
            $invoice    = invoiceFromDatabase($invoice_id);
            if ($invoice['id']) {
                $invoice_products = invoiceProductsFromDatabase($invoice_id);

                ob_start();
                include __DIR__ . '/__inventory_invoice_tooltip.tpl.php';
                $invoice_tooltip = ob_get_clean();

                $comments = str_replace($matches[0], '<a href="/webadmin/invoice/?action=view&amp;invoice_id=' . $invoice_id . '" target="_blank" onmouseover="' . hecho('tt_in(' . jecho($invoice_tooltip, 1) . ')', 1) . '" onmouseout="tt_out()">' . $matches[0] . '</a>', $comments);
            }
        }
?>
    <tr style="text-align:center;" <?=($count++ % 2 == 1) ? 'class="alt"' : ''?>>
        <td><?=date(_('Y-m-d'), strtotime($each['date']))?></td>
        <td><?=$movement?> <?=$each['stock_type']?></td>
        <td style="text-align:left;"><?=$comments?></td>
        <td><?=$each['available']?></td>
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
        <td><input type="button" value="EDIT"<?=($each['stock_left'] <= 0) ? ' disabled="disabled"' : ''?> onclick="openWindow('item_modify_inventory.php?inventory_id=<?=$each['id']?>', 800, 300, true);return false" /></td>
<?php
}
?>
    </tr>
<?php
}
?>
</table>
<?php
}
?>
</div>
<?php
$get_vars['return_message'] = true;

include_once __DIR__ . '/footer.inc.php';
