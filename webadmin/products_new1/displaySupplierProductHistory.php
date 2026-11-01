<?php
include_once __DIR__ . '/common.inc.php';
$supplier_id      = intval($_REQUEST['supplier_id']);
$product_id      = intval($_REQUEST['product_id']);
$item_history_full=getsupplieritemfullhistory($supplier_id,$product_id,'');
$summary = productPurchaseSummary($id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consignment Data</title>
    <link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<style type="text/css">
    .border-class-table  td {
  border: 1px solid black;
  text-align: center;
}
.border-class-table  th {
  border: 1px solid black;
  text-align: center;
}
</style>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
	<table class="table_grid border-class-table">
		<tr>
            <th>Date</th>
            <th>Cost (Currency)</th>
            <th>Cost USD</th>
            <th>Sell Price USD</th>
            <th>Markup %</th>
            <th>Movement</th>
        </tr>
        <?php foreach ($item_history_full as $history) { ?>
            <tr>
                <td><?= date('d-m-Y',strtotime($history['created_at']))?></td>
                <td><?= $history['cost_price']?> (<?= $history['sup_currency']?>)</td>
                <td><?= $history['cprice_usd']?></td>
                <td><?= $history['sales_price']?></td>
                <td><?= $history['markup']?> %</td>
                 <td><?= $history['movement']?> %</td>
            </tr>
        <?php } ?>
	</table>
</div>
</body>
</html>