<?php
include_once dirname(__FILE__) . "/common.inc.php";

$id=intval($posted['id']);
if(!$id) die('Invalid ID Passed');
$giftcoupon=giftcouponFromDatabase($id);
$giftcoupon['return_message']=$posted['return_message'];
$giftcoupon['formatted_id']=giftcouponFormatNumber($id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($giftcoupon) ?>;
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