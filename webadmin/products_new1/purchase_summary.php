<?php
include_once __DIR__ . '/common.inc.php';
?>

<script type="text/javascript">
//<![CDATA[
function printpage()
{
    if (window.print) window.print();
    else alert('To print this page, select Print Frame from the file menu.');
}
function doPrint()
{
    if (confirm("Click OK to print this document")) printpage();
}
//]]!>
</script>

<?php
if ('' != $_REQUEST['print']) {
    $style = "background:#fff;";
?>
<body onLoad="doPrint()" style="<?=$style?>">
<?php
}

$id      = intval($_REQUEST['id']);
$summary = productPurchaseSummary($id);?>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
<table class="table_grid">
    <tr>
        <th style="<?=$style?>" colspan="6"><strong>Purchase Summary for Product ID <?=$id?></strong></td>
    </tr>
    <tr style="<?=('' != $style) ? $style : 'background:#E3EBF7'?>">
        <td width="16%"><b>Date</b></td>
        <td width="16%"><b>Qty</b></td>
        <td width="16%"><b>Unit Cost (USD)</b></td>
        <td width="16%"><b>Unit Freight Cost (USD)</b></td>
        <td width="16%"><b>Unit Total Cost (USD)</b></td>
        <td width="16%"><b>Total Order Cost (USD)</b></td>
    </tr>
<?php
$count = 0;
foreach ($summary as $sum) {
    $total_cost = $sum['unit_cost'] + $sum['unit_freight'];
    $total      = $total_cost * $sum['movement'];
?>
    <tr <?=($count % 2 == 1) ? 'class="alt"' : ''?>>
        <td style="text-align:center"><?=date(_('Y-m-d'), strtotime($sum['date']))?></td>
        <td style="text-align:center"><?=$sum['movement']?></td>
        <td style="text-align:center">$<?=$sum['unit_cost']?></td>
        <td style="text-align:center">$<?=$sum['unit_freight']?></td>
        <td style="text-align: center">$<?=number_format($total_cost, 2, '.', '')?></td>
        <td style="text-align:center">$<?=number_format($total, 2, '.', '')?></td>
    </tr>
<?php
$count++;
}
?>
</table>
</div>