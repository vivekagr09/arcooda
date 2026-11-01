<?php
include_once('packing_common.inc.php');

$packing=array();
$packing['container_list']=packingFromPost($posted);
$packing['packing_string']='b64_gz_ser:'.base64_encode(gzcompress(serialize($packing['container_list'])));
$packing['return_message']=$posted['return_message'];


if(count($error_list))
{
	include_once('templates/packing_header.tpl.php');
	include 'templates/packing_error_list.tpl.php';
?>
<script type="text/javascript">
function packingIgnoreError()
{
	var data = <?=json_encode($packing)?>;
	if(window.opener!=null && window.opener.setPackingData!=null)
		window.opener.setPackingData(data);
	else if(window.parent!=null && window.parent.setPackingData!=null)
		window.parent.setPackingData(data);
	else
		alert('Unable to send packing back to form');
	window.close();
}
</script>
<input type="button" value="Ignore Errors and Continue" onclick="javascript:packingIgnoreError();" />
<?php
	include_once('templates/packing_footer.tpl.php');
}
else
{
?>
<html><head></head><body>
<script type="text/javascript">
var data = <?=json_encode($packing)?>;
if(window.opener!=null && window.opener.setPackingData!=null)
	window.opener.setPackingData(data);
else if(window.parent!=null && window.parent.setPackingData!=null)
	window.parent.setPackingData(data);
else
	alert('Unable to send packing back to form');
window.close();
</script>
</body></html>
<?php
}
?>