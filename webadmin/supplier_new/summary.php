<?php
include_once __DIR__ . '/common.inc.php';

$supplier_id = $posted['supplier_id'];
$supplier    = supplierFromDatabase($supplier_id);
$supplier_comments = supplierCommentSearch(array('supplier_id' => $supplier_id, 'comment_type' => 'admin'));
$staff_list = supplierGetOptionList('staff_id');
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var windowObj = this;
function printpage() {
	if (window.print) window.print();
	else alert('To print this page, select Print Frame from the file menu.');
}
function doPrint() {
	if (confirm("Click OK to print this document")) printpage();
}
//]]>
		</script>
		<style type="text/css">
/*<![CDATA[*/
body {width:7in;margin:0.5in auto;}
table {width:100%;border-collapse:collapse;}
td,th {border:1px solid #ccc;}
/*]]>*/
		</style>
	</head>
	<body style="background-color:#fff;" onLoad="doPrint()">

<div class="member_content"><div class="wide">
	<center><h2>Supplier Summary</h2></center>

	<h3>Supplier Details:</h3>
	<table>
		<tr>
			<th style="width:25%;">Supplier ID :</th>
			<td><?php hecho(SupplierFormatNumber($supplier['supplier_id']))?></td>
		</tr>
<?php
if ('' != $supplier['company']) {
?>
		<tr>
			<th>Company :</th>
			<td><?php hecho($supplier['company'])?></td>
		</tr>
<?php
}
?>
		<tr>
			<th>Contact Details :</th>
			<td><?php hecho($supplier['full_name'])?><br /><?=nl2br(hecho($supplier['address_full'], 1))?><br />Mobile: <?php hecho($supplier['mobile_number'])?></td>
		</tr>
	</table>
	<br />

	<h3>Comments:</h3>
	<table>
		<tr>
			<table class="table_grid">
				<tr style="background-color:#eef;">
					<th style="width:10%;">Date</th>
					<th style="width:20%;">User</th>
					<th style="width:70%;">Comments</th>
				</tr>
		<?php
			if (count($supplier_comments) > 0) {
		        foreach ($supplier_comments as $comment_id) {
		            $comment = supplierCommentFromDatabase($comment_id);
		?>
				<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
					<td><?=date("d M, Y", ($comment['comment_time']))?></td>
					<td><?php hecho($staff_list[$comment['staff_id']])?></td>
					<td><?=nl2br(hecho($comment['comment_text'], 1))?></td>
				</tr>
		<?php
				}
		    }
		?>
			</table>
		</tr>


</table>

	</body>
</html>
