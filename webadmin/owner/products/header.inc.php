<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = (isset($_GET['return_message'])) ? true : false;
if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
?>
<html>
    <head>
        <style type="text/css">
/*<![CDATA[*/
body,p,td {font-family:Arial;}
a:hover {color:#FF0000}
/*]]>*/
        </style>
    </head>
    <body>
<?php
}

?>
<script type="text/javascript" src="common.js"></script>
