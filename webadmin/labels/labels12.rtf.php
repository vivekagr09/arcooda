<?php
header('Content-Type: text/rtf');
header('Content-Disposition: attachment; filename="labels12.rtf"');
?>{\rtf1\ansi\ansicpg1252\deff0\deflang3081\deflangfe3081
{\fonttbl{\f0\fswiss\fprq2\fcharset0 Arial;}}
{\colortbl ;\red255\green0\blue0;}
\paperw11210\paperh7853\landscape\margl32\margr0\margt132\margb0\viewkind1\uc1

\trowd
\trqc\trrh1821\qc
\trpaddl86\trpaddfl3\trpaddt38\trpaddft3\trpaddb38\trpaddfb3\trpaddr86\trpaddfr3
\clvertalc\cellx3737
\clvertalc\cellx7473
\clvertalc\cellx11210
\intbl
<?php
include_once("../connection.inc.php");
$product_list=explode('_',$_REQUEST['product_list']);
$total_labels=count($product_list);
$rows=array_chunk($product_list,3);

foreach($rows as $row)
{
?>
<?= reset($row) ?>\cell <?= next($row) ?>\cell <?= next($row) ?>\cell\row
<?php
}
?>
}