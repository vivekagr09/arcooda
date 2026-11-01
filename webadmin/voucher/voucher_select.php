<?php
include_once 'voucher_common.inc.php';

$voucher_id = intval($posted['voucher_id']);
if (!$voucher_id) {
    die('Invalid ID Passed');
}

$voucher                   = voucherFromDatabase($voucher_id);
$voucher['return_message'] = $posted['return_message'];
$voucher['formatted_id']   = voucherFormatNumber($voucher_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($voucher)?>;
if(window.opener!=null && window.opener.setVoucherData!=null)
{
	window.opener.setVoucherData(data);
	window.close();
}
else
	alert('Unable to send data back');
//]]>
	</script>
</head>
<body></body>
</html>