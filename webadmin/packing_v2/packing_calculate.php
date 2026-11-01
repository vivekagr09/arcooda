<?php
include_once('packing_common.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$error_list=array();

$packing=array();
$packing['container_list']=packingFromPost($posted,$error_list);
$packing['packing_string']='b64_gz_ser:'.base64_encode(gzcompress(serialize($packing['container_list'])));
$packing['return_message']=$posted['return_message'];
if(count($error_list))
{
	//include_once('templates/packing_header.tpl.php');
	include 'templates/packing_error_list.tpl.php';
?>
<script type="text/javascript">
function packingIgnoreError()
{
	var data = <?=json_encode($packing)?>;
	if(window.opener!=null && window.opener.setPackingDataNew!=null)
		window.opener.setPackingDataNew(data);
	else if(window.parent!=null && window.parent.setPackingDataNew!=null)
		window.parent.setPackingDataNew(data);
	else
		alert('Unable to send packing back to form');
	window.close();
}
</script>
<input type="button" value="Ignore Errors and Continue" onclick="javascript:packingIgnoreErrorNew();" />
<?php
	include_once('templates/packing_footer.tpl.php');
}
else
{
	header('Content-Type: application/json');
	echo json_encode($packing);
}
?>