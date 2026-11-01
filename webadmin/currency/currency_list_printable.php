<?php
include_once('currency_common.inc.php');
?>
<html>
<head>
<title>Exchange Rates <?= date('jS F Y') ?></title>
<style type="text/css">
a
{
	font-weight: bold;
	font-size: 10pt;
	color:#FFFFFF;
	text-decoration:none;
}
table
{
	border-collapse:collapse;
}
th
{
	background-color: #555555;
	border:1px solid black;
	text-align: center;
}
td
{
	border:1px solid black;
	font-size: 10pt;
	text-align: center;
	padding:2px 2px 2px 2px;
}
</style>
</head>
<body>
<div align="center">
	<font size="+1"><b><u>Exchange Rates <?= date('jS F Y') ?></u></b></font>
</div>
<br />
<?php

$order=$posted['order'];
if($order!='desc') $order='asc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['code']!='') $criteria['code']=$posted['code'];
if($posted['code_first_letter']!='') $criteria['code_first_letter']=substr($posted['code_first_letter'],0,1);
if($posted['name_first_letter']!='') $criteria['name_first_letter']=substr($posted['name_first_letter'],0,1);

//Find and pagenate the currencys
$currency_list=currencySearch($criteria);
$currency_list=currencySort($currency_list,$order_by);
if($order=='desc') $currency_list=array_reverse($currency_list);

$items_per_page=50;
$count_total=count($currency_list);

if($count_total>43)
	$chunked_list=array_chunk($currency_list,ceil($count_total/2));
else
	$chunked_list=array($currency_list);

if($count_total>0)
{
?>
<table align="center"><tr>
<?php
	foreach($chunked_list as $currency_list)
	{
?>
<td style="vertical-align:top;border-style:none;">
<?php include 'templates/currency_list.tpl.php'; ?>
</td>
<?php
	}
?>
</tr></table>
<br />
<?php
}
else
{
?>
No results were found
<?php
}
?>
</body>
</html>
