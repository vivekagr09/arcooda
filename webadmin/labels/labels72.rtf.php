<?php
header('Content-Type: text/rtf');
header('Content-Disposition: attachment; filename="labels72.rtf"');
?>{\rtf1\ansi\ansicpg1252\deff0\deflang3081\deflangfe3081
{\fonttbl{\f0\fswiss\fprq2\fcharset0 Arial;}}
{\colortbl ;\red255\green0\blue0;}
\paperw10698\paperh9389\landscape\margl0\margr0\margt0\margb0\viewkind1\uc1

\trowd
\trqc\trrh860\qc
\trpaddl42\trpaddfl3\trpaddt42\trpaddft3\trpaddb42\trpaddfb3\trpaddr42\trpaddfr3
\clvertalc\cellx1337
\clvertalc\cellx2674
\clvertalc\cellx4012
\clvertalc\cellx5349
\clvertalc\cellx6686
\clvertalc\cellx8023
\clvertalc\cellx9361
\clvertalc\cellx10698
\intbl
\b1\fs18

<?php
include_once("../connection.inc.php");
$product_list=explode('_',$_REQUEST['product_list']);
$total_labels=count($product_list);
$rows=array_chunk($product_list,8);

foreach($rows as $row)
{
	$temp=array();
	foreach($row as $product_id)
		$temp[]='COE#'.substr('00000'.$product_id,-5)."\\par\\par\nQty: ______";
	$row=$temp;
?>
<?= reset($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell\row
<?php
}
?>
}