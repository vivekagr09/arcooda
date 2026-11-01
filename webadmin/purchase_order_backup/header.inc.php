<?php
include_once __DIR__ . "/common.inc.php";

$invoices_start_time = explode(" ", microtime());
$popup_window        = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
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
        <script type="text/javascript" src="/webadmin/jscript/common.js" defer="defer"></script>
        <script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
    </head>
    <body>
<?php
}
?>
        <link type="text/css" rel="stylesheet" href="style.css" />
        <iframe style="width:168px;height:202px;visibility:hidden;z-index:65535;position:absolute;top:-202px;border:2px ridge;" name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0"></iframe>

<div class="invoice_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">
<?php
global $_GLOBALS;
$staff_id = $_GLOBALS['staff']['id'];
if ($display_both) {
    echo 'Supplier Invoices/Inquiries';
} else {
    echo ($display_inquiry) ? 'Supplier Inquiries' : 'Supplier Invoices';
}
?>
			</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
<?php
if ($display_both) {
?>
				<a href="index.php?action=add&amp;status=proforma">Add New Supplier Invoice</a> |
				<a href="index.php?action=add&amp;status=inquiry">Add New Supplier Inquiry</a> |
<?php
} else {
?>
				<a href="index.php?action=add&amp;status=<?= ($display_inquiry) ? 'inquiry' : 'sales' ?>">Add New <?= ($display_inquiry) ? 'Inquiry' : 'Invoice' ?></a> |
<?php
}
?>
				<a href="index.php?action=list&amp;handler=<?= $staff_id ?>&amp;status=<?= ($display_inquiry) ? 'inquiry' : 'proforma|sales|return' ?>">List <?php hecho($all_staff_list[$staff_id])?> <?= ($display_inquiry) ? 'Inquiries' : 'Invoices' ?></a> |

				<a href="index.php?action=list&amp;status=<?= ($display_inquiry) ? 'enquiry|inquiry|return' : 'proforma|sales|return' ?>">List Open <?= ($display_inquiry) ? 'Inquiries' : 'Invoices' ?></a> |

				<a href="index.php?action=list&amp;status=<?= ($display_inquiry) ? 'inquiry|closed|return|completed' : 'proforma|sales|processed|cancelled|return|completed' ?>">List All <?= ($display_inquiry) ? 'Inquiries' : 'Invoices' ?></a><br />

				<a href="index.php?action=list&amp;status=enquiry|inquiry|proforma|sales|return&amp;handler=<?= $staff_id ?>">List <?php hecho($all_staff_list[$staff_id])?> Open Inquiries+Invoices</a> |

				<a href="index.php?action=list&amp;status=enquiry|inquiry|proforma|sales|return">List Open Inquiries+Invoices</a>
			</td>
		</tr>
	</table>

	<div align="center">
		<br />
