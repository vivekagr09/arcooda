<?php
header('Content-Type: text/rtf');
header('Content-Disposition: attachment; filename="labels112.rtf"');
?>{\rtf1\ansi\ansicpg1252\deff0\deflang3081\deflangfe3081
{\fonttbl{\f0\fswiss\fprq2\fcharset0 Arial;}}
{\colortbl ;\red255\green0\blue0;}
\paperw9845\paperh9333\landscape\margl0\margr0\margt100\margb100\viewkind1\uc1
\b1\fs18

\trowd
\trqc\trrh552\qc
\trpaddl50\trpaddfl3\trpaddt50\trpaddft3\trpaddb50\trpaddfb3\trpaddr50\trpaddfr3
\clvertalc\cellx1231
\clvertalc\cellx2462
\clvertalc\cellx3693
\clvertalc\cellx4924
\clvertalc\cellx6155
\clvertalc\cellx7386
\clvertalc\cellx8617
\clvertalc\cellx9848
\intbl
<?php
include_once("../connection.inc.php");
$product_list=explode('_',$_REQUEST['product_list']);
$total_labels=count($product_list);
$rows=array_chunk($product_list,8);

foreach($rows as $row)
{
	$temp=array();
	foreach($row as $product_id)
		$temp[]='ID#'.substr('00000'.$product_id,-5);
	$row=$temp;
?>
<?= reset($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell\row
<?php
}
?>
}