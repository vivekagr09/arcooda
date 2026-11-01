<?php
include_once dirname(__FILE__) . "/common.inc.php";

$popup_window = ($posted['return_message'] != '');
if (!$popup_window) include_once($_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php');
else
{
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Gift Vouchers</title>

		<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
		<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
		<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
		<style type="text/css">
body,p,td	{ font-family:Verdana,Arial,Helvetica,sans-serif; font-size:12px; }
		</style>
	</head>
	<body>
<?php
}
$criteria = array();
 $criteria['site_id'] = 39;
$items = giftcouponSearch($criteria);
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<div align="center" class="gift_content">
	<table style="width:100%;" cellpadding="2"><tr style="background-color:#eee;">
		<td style="width:20%;"><b>Discount Coupons</b></td>
		<td style="text-align:right;width:80%;">
			<a href="index.php?action=preset_add">Add New Coupon</a> |
			<a href="index.php?action=list">List All Coupon (<?php echo count($items) ?>)</a>
		</td>
	</tr></table>
