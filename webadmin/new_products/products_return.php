<?php
include_once('products_common.inc.php');

$posted = array_merge($_GET, $_POST);

$get_vars=$posted;
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value))
			unset($get_vars[$key]);

$site_id=(isset($posted['site_id']))?$posted['site_id']:9;
$product_id=(isset($posted['product_id']))?$posted['product_id']:0;
$currency=(isset($posted['currency']))?$posted['currency']:'USD';

$product=productFromDatabase($product_id,$site_id);
?>
<html><head></head><body>
<script type="text/javascript">
var data = <?=json_encode($product)?>;
if(window.opener!=null && window.opener.setProductData!=null)
	window.opener.setProductData(<?=json_encode($posted['return_message'])?>, data);
else
	alert('Unable to send product back to form');
window.close();
</script>
</body></html>