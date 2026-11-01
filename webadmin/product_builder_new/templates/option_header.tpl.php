<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
    include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');
else
{
?>
<html>
<head>
<style type="text/css">
body,p,td
{
    font-family:Arial;
    font-size:12px;
}
a:hover
{
    color:#FF0000;
}
</style>
<link rel="stylesheet" type="text/css" href="/webadmin/admin.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
</head>
<?php
}
?>
<script>
var need_to_add_shipping=false;
var need_to_add_notify=false;
</script>
<style>
td.quarter { width:25%; }
td.left_input { width:20%; }
td.right_input { width:30%; }
td.right_input input, td.right_input textarea, td.right_input select { width:98%; }
</style>
<body onload="">

<link rel="stylesheet" type="text/css" href="/webadmin/product_builder_new/css/style.css" />

<div>
	<!-- Header -->
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td class="pdx10" style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">
                Options
            </td>

            <td class="pdx10 printHide" style="text-align:right;width:80%;background-color:#eeeeee;">
                <a href="index.php?action=add_option">Add New Option</a> |
                <a href="index.php?action=search_option">Search Options</a> |
                <a href="index.php?action=view_option">View All</a>
            </td>
        </tr>
    </table>
</div>
