<?php
include_once('suppliers_common.inc.php');

$js_data = array();
if (is_array($posted['supplier_list']) && $posted['multiple'] == 'y')
{
	$supplier_list=array_unique($posted['supplier_list']);
	$js_data['supplier_list']=array();
	foreach($supplier_list as $supplier_id)
	{
		$row=supplierFromDatabase($supplier_id,true);
		$row['contacts']=supplierContactsFromDatabase($supplier_id,true);
		if(count($js_data['contacts']) == 0)
		{
			$row['contacts'][0]['name']='';
			$row['contacts'][0]['email']='';
			$row['contacts'][0]['phone']='';
		}
		$default_values=supplierValuesFromDatabase($supplier_id, $posted['prod_section']);
		if(is_array($default_values) && count($default_values))
			$row['values']=reset($default_values);
		$js_data['supplier_list'][]=$row;
	}
}
else
{
	$supplier_id=intval($posted['id']);
	$js_data=supplierFromDatabase($supplier_id,true);
	$js_data['contacts']=supplierContactsFromDatabase($supplier_id,true);
	if(count($js_data['contacts']) == 0)
	{
		$js_data['contacts'][0]['name']='';
		$js_data['contacts'][0]['email']='';
		$js_data['contacts'][0]['phone']='';
	}
	$default_values=supplierValuesFromDatabase($supplier_id, $posted['prod_section']);
	if(is_array($default_values) && count($default_values))
		$js_data['values']=$default_values;
}
$js_data['return_message']=$posted['return_message'];

?>
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($js_data) ?>;
if (window.opener != null && window.opener.setSupplierData != null) window.opener.setSupplierData(data);
else alert('Unable to send supplier back to form');
window.close();
//]]>
		</script>
	</head>
	<body></body>
</html>
