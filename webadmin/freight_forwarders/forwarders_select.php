<?php
include_once('forwarders_common.inc.php');

$js_data=array();

if(is_array($posted['forwarders_list']) && $posted['multiple']=='y')
{
	$forwarders_list=array_unique($posted['forwarders_list']);
	$js_data['forwarders_list']=array();
	foreach($forwarders_list as $forwarder_id)
		$js_data['forwarders_list'][]=forwardersFromDatabase($forwarder_id);
}
else
{
	$forwarder_id=intval($posted['id']);
	$js_data=forwardersFromDatabase($forwarder_id);
}
$js_data['return_message']=$posted['return_message'];
?>
<html><head></head><body>
<script type="text/javascript">
var data = <?php jecho($js_data) ?>;
if(window.opener!=null && window.opener.setForwardersData!=null) {
	window.opener.setForwardersData(data);
	window.close();
}
else
	alert('Unable to send forwarders back to form');
</script>
</body></html>