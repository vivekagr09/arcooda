<?php
include_once ('wanted_common.php');
$js_data = array();
if (is_array($posted['wanted_list']) && $posted['multiple'] == 'y')
{
	$wanted_list = array_unique($posted['wanted_list']);
	$js_data['wanted_list'] = array();
	foreach($wanted_list as $wanted_id)
	{
		$row = wantedFromDatabase($wanted_id);
		$row['date_str'] = date('d/m/Y', $row['time_added']);
		$js_data['wanted_list'][] = $row;
	}
}
else
{
	$wanted_id = intval($posted['wanted_id']);
	$js_data = wantedFromDatabase($wanted_id);
	$js_data['date_str'] = date('d/m/Y', $row['time_added']);
}
$js_data['return_message'] = $posted['return_message'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
if(window.opener!=null && window.opener.setWantedData!=null)
{
	window.opener.setWantedData(<?php jecho($js_data) ?>);
	window.close();
}
else
	alert('Unable to send wanted back to form');
//]]>
	</script>
</head>
<body></body>
</html>