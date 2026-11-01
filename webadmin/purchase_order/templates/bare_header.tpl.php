<?php
$invoices_start_time = explode(" ", microtime());
//header("Content-Type: application/xhtml+xml; charset=utf-8");
header("Content-Type: text/html; charset=utf-8");
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:#f00}
/*]]>*/
        </style>
        <script type="text/javascript" src="/webadmin/jscript/common.js"></script>
        <script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
        <script type="text/javascript" src="common.js"></script>
        <link type="text/css" rel="stylesheet" media="screen" href="style.css" />
        <link type="text/css" rel="stylesheet" media="print" href="print.css" />
    </head>
<?php
flush();
?>
    <body>

<iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>
<div class="invoice_content" style="padding:16px;margin:0;width:auto;">

<!-- /bare_header.tpl -->
