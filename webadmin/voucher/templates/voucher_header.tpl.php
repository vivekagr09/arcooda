<?php
$popup_window = ('' != $posted['return_message']);
if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Gift Vouchers</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
	<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
	<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
	<style type="text/css">
	<!--
	body,p,td
	{
		font-family:Verdana,Arial,Helvetica,sans-serif;
		font-size:12px;
	}
	</style>
</head>
<body>
<?php
}
$criteria = array();
if ('' != $posted['site_id']) {
    $criteria['site_id'] = $posted['site_id'];
}

$items = voucherSearch($criteria);
?>
<link rel="stylesheet" type="text/css" href="/webadmin/gift_voucher/style.css" />
<script type="text/javascript" src="/webadmin/gift_voucher/voucher_funcs.js.php"></script>
<div align="center" class="gift_content">

<table style="width:100%;" cellpadding="2"><tr style="background-color:#EEEEEE;">
	<td style="width:20%;"><b>Gift Voucher</b></td><td style="text-align:right;width:80%">
		<a href="index.php?action=add">Add New Voucher</a> |
		<a href="index.php?action=search">Search Vouchers</a> |
		<a href="index.php?action=list">List All Vouchers (<?=count($items)?>)</a>
	</td>
</tr></table>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
