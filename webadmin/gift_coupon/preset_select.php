<?php
include_once 'common.inc.php';

$product_id=intval($posted['product_id']);
$sub_id=intval($posted['sub_id']);
$return_message=$posted['return_message'];
$fromsite = $posted['fromsite']; //as a API for highway group gitf_coupon
if(!$product_id) die('Invalid parameters passed to this page..');

$img_name = $product_id.($sub_id>0?'_'.$sub_id : '');
$img_ext = ".jpg";
$img_baseurl = "https://static.highway.net.au/images/products";
$img_url = $img_baseurl.'/'.$return_message.'/'.$img_name.$img_ext;

$data=array('product_id'=>$product_id, 'sub_id'=>$sub_id,'return_message'=>$return_message,'fromsite'=>$fromsite,'img_name'=>$img_name.$img_ext,'img_url'=>$img_url); // add key = fromsite

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
var data=<?php echo formatJSON($data) ?>;
if(window.opener!=null && window.opener.setCouponProductData!=null)
{
	window.opener.setCouponProductData(data);
	// alert("yes");
	window.close();
}
else
	alert('Cannot send information, please follow proper steps');
//]]>
	</script>
</head>
<body></body>
</html>