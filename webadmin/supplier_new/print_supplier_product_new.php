<?php
include_once __DIR__ . '/common.inc.php';

//include_once __DIR__ . '/header.inc.php';

$supplier_id = $posted['supplier_id'];
$order_by=($_GET['order_by'])?$_GET['order_by']:'id';
$order=($_GET['order'])?$_GET['order']:'asc';
$supplier_stock         = supplierStockFromDatabase($supplier_id,$order_by,$order,false);
$supplier               = supplierFromDatabase($supplier_id);
$cuntry_list=getCurrencyList();
function supplier_products_row($count = '[ROW_COUNT]', $stock = array(),$supplier=[],$cuntry_list=[])
	{
	    global $DB;    			
    				?>
		<tr>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['product_id'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['product_name'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
				<?= $stock['qty']?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['cprice_usd'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['sales_price'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['markup'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['location'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $stock['sup_code'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
	    		<?= $supplier['name'].' '.$supplier['surname'] ?>
			</td>
			<td style="border:2px solid #ccc!important;">
				<?= $stock['cost_price']?>
			</td>
			<td style="border:2px solid #ccc!important;">
				<?php
					echo $stock['sup_currency'];
				?>
			</td>
			<td style="border:2px solid #ccc!important;"><?= $stock['notes']?></td>
			<td style="border:2px solid #ccc!important;">
				<?= ($stock['updated_at'])?date('d-m-Y',strtotime($stock['updated_at'])):'N/A';?>
			</td>
		</tr>
	<?php
	return ob_get_clean();
	}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<head>
        <meta charset="utf-8" />
        <title></title>

        <link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
        <link rel="stylesheet" href="/webadmin/css/favicons.css" type="text/css" />
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var windowObj = this;
function printpage() {
	if (window.print){
		window.onafterprint = window.close;
		window.print();
	}else{
	 alert('To print this page, select Print Frame from the file menu.');
	}
}
function doPrint() {
	if (confirm("Click OK to print this document")) printpage();
}
//]]>
		</script>
		<style type="text/css">
/*<![CDATA[*/


/*]]>*/
		</style>
	</head>
	<body style="background-color:#fff;" onLoad="printpage()">
		<div class="table_grid" style="border: none;padding-bottom: 10px;font-size: 17px;">
			<div style="font-size: 20px;"><b><?= $supplier['company'] ?></b></div>
			<div><?= supplierFormatAddressPrint($supplier) ?></div>
			<div>Ph: <?= $supplier['phone_no']?> , Email:<?= $supplier['email_address']?></div>
			<div><?= $supplier['name'].' '.$supplier['surname'] ?></div>
			<div><?= date('d/m/Y')?></div>
		</div>
		<table class="table_grid" style="border:1px solid gray;width:100%;border-collapse:collapse;">
				<tr>
					<th style="border:2px solid #ccc;background-color: gray!important;">Our ID</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Product Name</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Quantity</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Price USD</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Sales Price USD</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">% markup</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Location</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Supplier Product Id</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Supplier Name</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Cost Price</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Currency</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Note</th>
					<th style="border:2px solid #ccc;background-color: gray!important;">Last Date Checked</th>
				</tr>
				<?php
				$count=0;
				if(count($supplier_stock)){
    				foreach ($supplier_stock as $stock) {
    				echo supplier_products_row($count,$stock,$supplier,$cuntry_list);
    				 $count++; 
    				}
    			} 

    			?>

   		</table>

	</body>
</html>
