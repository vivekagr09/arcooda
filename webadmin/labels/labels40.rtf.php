<?php
header('Content-Type: text/rtf');
header('Content-Disposition: attachment; filename="labels40.rtf"');
?>{\rtf1\ansi\ansicpg1252\deff0\deflang3081\deflangfe3081
{\fonttbl{\f0\fswiss\fprq2\fcharset0 Arial;}}
{\colortbl ;\red255\green0\blue0;}
\paperw9105\paperh11950\margl0\margr0\margt0\margb0\viewkind1\uc1
\b1\fs18

\trowd
\trqc\trrh1012\qc
\trpaddl86\trpaddfl3\trpaddt86\trpaddft3\trpaddb86\trpaddfb3\trpaddr86\trpaddfr3
\clvertalc\clbrdrl\brdrw10\brdrs\clbrdrt\brdrw10\brdrs\cellx2276
\clvertalc\clbrdrl\brdrw10\brdrs\clbrdrt\brdrw10\brdrs\cellx4552
\clvertalc\clbrdrl\brdrw10\brdrs\clbrdrt\brdrw10\brdrs\cellx6829
\clvertalc\clbrdrl\brdrw10\brdrs\clbrdrt\brdrw10\brdrs\clbrdrr\brdrw10\brdrs\cellx9105
\intbl
<?php
include_once("../connection.inc.php");
$product_list=explode('_',$_REQUEST['product_list']);
$total_labels=count($product_list);
$rows=array_chunk($product_list,4);

foreach($rows as $row)
{
	$temp=array();
	foreach($row as $product_id)
	{
		$name=$DB->query_first("SELECT product_name FROM products WHERE id=".$product_id);
		$temp[]='COE#'.substr('00000'.$product_id,-5)."\\par\n".str_replace("\\","\\\\",$name['product_name'])."\\par\nQty: ______";
	}
	$row=$temp;
?>
<?= reset($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell\row
<?php
}
?>
}