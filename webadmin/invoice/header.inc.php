<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
} else {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
		<style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:#f00}
/*]]>*/
		</style>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="/phpjs/phpjs.js"></script>
		<script type="text/javascript" src="/webadmin/jscript/common.js" defer="defer"></script>
		<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
	</head>
	<body>
<?php
}

?>
<link rel="stylesheet" type="text/css" href="/webadmin/invoice/style.css" />

<div class="invoice_content">
	<?php if($_GET['section'] != 'harmonised_code'){ ?>
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">
				<div style="display:flex;">
<?php
if ($display_both) {
    echo 'Invoices/Inquiries';
} else {
    echo ($display_inquiry) ? 'Inquiries' : 'Invoices';
}
 $total   = $DB->query_first("SELECT count(invoice_id) as total FROM `highwaygroup`.`invoices` WHERE `invoice_action`='Working_on_solution'");
?>

			<a href=".?action=oprduct_ordered" target="_blank" style="width: 150px;">Product Ordered</a>
			<?php if($total['total'] > 0){ ?>
			<a href=".?invoice_action=Working_on_solution" target="_blank" style="width: 70px;color: red;">Repairs(<?= $total['total']?>)</a>
			<a href="/webadmin/freight_tracking/shipments.php" target="_blank" style="width: 150px;">Open Shipments</a>
		<?php } ?>
		</div>
			</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;">
<?php
if ($display_both) {
?>
				<!-- <a href=".?action=add&amp;status=sales">Add New Invoice</a> |
				<a href=".?action=add&amp;status=inquiry">Add New Inquiry</a> | -->
<?php
} else {
?>
				<!-- <a href=".?action=add&amp;status=<?=($display_inquiry) ? 'inquiry' : 'sales'?>">Add New <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?></a> | -->
<?php
}
?>
				<a href=".?action=add&amp;status=inquiry">Add New Inquiry</a> |
				<a href=".?action=add&amp;status=sales">Add New Invoice</a> |
				<a href=".?action=list&amp;handler=<?=$_GLOBALS['staff']['id']?>&amp;status=<?=($display_inquiry) ? 'inquiry' : 'proforma|sales|return'?>">List <?=htmlspecialchars($_GLOBALS['staff']['name'])?> <?=($display_inquiry) ? 'Inquiries' : 'Invoices'?></a> |

				<a href=".?action=list&amp;status=<?=($display_inquiry) ? 'inquiry|return' : 'proforma|sales|return'?>">List Open <?=($display_inquiry) ? 'Inquiries' : 'Invoices'?></a> |

				<a href=".?action=list&amp;status=<?=($display_inquiry) ? 'inquiry|closed|return|completed' : 'proforma|sales|processed|cancelled|return|completed'?>">List All <?=($display_inquiry) ? 'Inquiries' : 'Invoices'?></a><br />

				<a href=".?action=list&amp;status=inquiry|proforma|sales|return&amp;handler=<?=$_GLOBALS['staff']['id']?>">List <?=htmlspecialchars($_GLOBALS['staff']['name'])?> Open Inquiries+Invoices</a> |

				<a href=".?action=list&amp;status=inquiry|proforma|sales|return">List Open Inquiries+Invoices</a>
			</td>
		</tr>
	</table>
<?php } ?>
	<div align="center">
		<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
