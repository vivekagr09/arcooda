<?php
/*****
 * Script for grabbing data from an old order and inserting it into a new order.
 * Used when searching products.
 *****/

include_once __DIR__ . '/common.inc.php';

require_once("spo_config.php");

$supplier = $_REQUEST['s'];
$product  = $_REQUEST['p'];
$target   = $_REQUEST['d'];

$iframe = "tempif$supplier$product";

$d = $DB->query_first("SELECT qty, price
						FROM new_purchase_order_details AS npod
						INNER JOIN new_purchase_order AS npo ON npod.porder_id=npo.porder_id
						WHERE npod.product_id='$product' AND npo.supplier_id='$supplier'
						ORDER BY npo.creation_date DESC");

echo "<pre>".print_r($d,1)."</pre>";
?>
<script type="text/javascript">
	parent.document.getElementsByName("order[<?=$target;?>][qty]")[0].value = <?=$d['qty'];?>;
	parent.document.getElementsByName("order[<?=$target;?>][price]")[0].value = <?=$d['price'];?>;
	var obj = parent.document.getElementById("<?=$iframe?>")
	obj.parentNode.removeChild(obj);
</script>
