<?php
include_once('currency_common.inc.php');
?>
<html>
<head>
<title>Exchange Rates <?= date('jS F Y') ?></title>
<style type="text/css">
a, th
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
	<font size="+1"><b><u>Currency History - <?= $currency_from ?> to <?= $currency_to ?></u></b></font>
</div>
<br />
<?php

$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['code']!='') $criteria['code']=$posted['code'];

$amount_from=floatval($posted['amount_from']);
$currency_from=$posted['currency_from'];
$currency_to=$posted['currency_to'];

//Find the affected dates
if($currency_from=='USD')
	$from_history=array(array('usto'=>1.0));
else
	$from_history=currencyHistoryFromDatabase($currency_from);
if($currency_to=='USD')
	$to_history=array(array('usto'=>1.0));
else
	$to_history=currencyHistoryFromDatabase($currency_to);


$date_list=array();
foreach($from_history as $key=>$row)
	$date_list[$row['change_time']]['from']=$key;
foreach($to_history as $key=>$row)
	$date_list[$row['change_time']]['to']=$key;
ksort($date_list);
$from=-1;
$to=-1;
foreach($date_list as $key=>$row)
{
	if(!isset($row['from'])) $date_list[$key]['from']=$from; else $from=$row['from'];
	if(!isset($row['to'])) $date_list[$key]['to']=$to; else $to=$row['to'];
}
foreach($date_list as $key=>$row)
{
	$date_list[$key]['from']=$from_history[$row['from']];
	$date_list[$key]['to']=$to_history[$row['to']];
}
if($order=='desc') $date_list=array_reverse($date_list,true);

$items_per_page=80;
$page_id=floor($page_id/4);
$count_total=count($date_list);

$date_list=array_chunk($date_list,$items_per_page,true);
$count_pages=count($date_list);
$page_id=max(0,min($count_pages-1,$page_id));
$date_list=$date_list[$page_id];
$count_display=count($date_list);

if($count_display>40)
	$chunked_list=array_chunk($date_list,ceil($count_display/2),true);
else
	$chunked_list=array($date_list);

if($count_total>0)
{
?>
<table align="center"><tr>
<?php
	foreach($chunked_list as $date_list)
	{
?>
<td style="vertical-align:top;border-style:none;">
<?php include 'templates/currency_history.tpl.php'; ?>
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