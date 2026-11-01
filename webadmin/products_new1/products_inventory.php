<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$get_vars['return_message'] = true;

include_once __DIR__ . '/header.inc.php';

if (!isset($sections) || !is_array($sections)) {
    $sections = userPermittedSections($_SERVER['REMOTE_USER'], 2);
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
    $style = "background-color: #FFFFFF";
?>
<body onLoad="doPrint()" style="<?=$style?>">
<?php
}
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
<?php
$product_id = intval($posted['id']);
$sub_id     = intval($posted['sub_id']);
$inventory  = productInventoryFromDatabase($product_id, $sub_id);
if (count($inventory) > 0) {
?>
<table class="table_grid wide">
	<tr><th style="<?=$style?>" colspan="<?=(in_array('ADMIN', $sections)) ? 5 : 4?>" align="center" class="box"><b>Inventory Control for Product ID <?=$product_id?></b></td></tr>
	<tr style="font-size:12px;<?=('' != $style) ? $style : 'background-color:#E3EBF7'?>">
		<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
		<td width="1<?=(in_array('ADMIN', $sections)) ? '0' : '5'?>%" align="center" nowrap="nowrap" class="bottomLeft"><b>Movement</b></td>
		<td width="<?=(in_array('ADMIN', $sections)) ? '65' : '70'?>%" align="center" nowrap="nowrap" class="bottomLeft"><b>Comments</b></td>
		<td width="5%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Available</b></td>
<?php
if (in_array('ADMIN', $sections)) {
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
        $comments = htmlspecialchars($each['comments']);

        if (preg_match("/(I([A-Z][A-Z])?|Q[A-Z][A-Z])#([0-9]+)/", $comments, $matches)) {
            $invoice_id = intval($matches[3]);
            $invoice    = invoiceFromDatabase($invoice_id);
            if ('' != $invoice['invoice_id']) {
                $tooltip   = array();
                $tooltip[] = '<tr style="font-size:10px;"><td colspan="8">Invoice To :<br />' . nl2br($invoice['bill_address_full']) . '</td></tr>';
                foreach (invoiceProductsFromDatabase($invoice_id) as $prod) {
                    $tooltip[] = '<tr style="font-size:10px;"><td style="text-align:right;">' . $prod['product_qty'] . '</td><td>x</td><td nowrap="nowrap">' . productFormatNumber($prod['product_id'], $prod['sub_id']) . ': ' . $prod['product_name'] . '</td><td>@</td><td style="text-align:right;">' . convertCurrency($prod['product_offer'], $invoice['invoice_currency']) . '</td><td>=' . getCurrencySymbol($invoice['invoice_currency'], 'HTML') . '</td><td style="text-align:right;">' . convertCurrency($prod['product_offer'] * $prod['product_qty'], $invoice['invoice_currency']) . '</td><td>' . $invoice['invoice_currency'] . '</td></tr>';
                }

                if ('' != $invoice['ship_product_name']) {
                    $tooltip[] = '<tr style="font-size:10px;"><td style="text-align:right;">1</td><td>x</td><td nowrap="nowrap">Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) . '</td><td>=' . getCurrencySymbol($invoice['invoice_currency'], 'HTML') . '</td><td style="text-align:right;">' . convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) . '</td><td>' . $invoice['invoice_currency'] . '</td></tr>';
                }

                $tooltip  = '<table>' . implode('', $tooltip) . '</table>';
                $comments = str_replace($matches[0], '<a href="/webadmin/invoice/?action=view&amp;invoice_id=' . $invoice_id . '" target="_blank" data-bs-toggle="tooltip" data-bs-html="true" title="' . htmlspecialchars($tooltip) . '">' . $matches[0] . '</a>', $comments);
            }
        }
?>
	<tr style="text-align:center;" <?=($count++ % 2 == 1) ? 'class="alt"' : ''?>>
		<td style="font-size:11px;"><?=date("d/m/Y", strtotime($each['date']))?></td>
		<td style="font-size:11px;"><?=$movement?> <?=$each['stock_type']?></td>
		<td style="text-align:left;font-size:11px;"><?=$comments?></td>
		<td style="font-size:11px;"><?=$each['available']?></td>
<?php
if (in_array('ADMIN', $sections)) {
?>
		<td><input type="button" value="EDIT"<?=($each['stock_left'] <= 0) ? ' disabled="disabled"' : ''?> onclick="openWindow('product_modify_inventory.php?inventory_id=<?=$each['id']?>', 800, 300, true);return false" /></td>
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
