<!-- menu.inc -->

<?php
global $_GLOBALS;

$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/payment_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 600) > $_SERVER["REQUEST_TIME"]) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    moduleLoad('payment');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#00CCFF">
		<td nowrap="nowrap" align="center">
			<b>Payments</b>
		</td>
	</tr>
	<tr>
		<td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
<?php
    if (function_exists('paymentSearch')) {
?>
			<li><a class="td_options" href="/webadmin/payment/?action=list_cc">View Credit Payment Receipts (<?=count(paymentSearch())?>)</a></li>
<?php
    }
?>
			<br /><br />
			<li>Visa Card<br />
				<ul style="padding:0px 0px 0px 16px;margin:0px;">
					<li><a class="td_options" href="/webadmin/payment/?action=add_cc&amp;Card=VC">Make New Payment</a></li>
					<li><a class="td_options" href="/webadmin/payment/?action=list_cc&amp;Card=VC">View All Payments</a></li>
				</ul>
			</li>
			<br />
			<li>Mastercard<br />
				<ul style="padding:0px 0px 0px 16px;margin:0px;">
					<li><a class="td_options" href="/webadmin/payment/?action=add_cc&amp;Card=MC">Make New Payment</a></li>
					<li><a class="td_options" href="/webadmin/payment/?action=list_cc&amp;Card=MC">View All Payments</a></li>
				</ul>
			</li>
			<br />
			<li>American Express<br />
				<ul style="padding:0px 0px 0px 16px;margin:0px;">
					<li><a class="td_options" href="/webadmin/payment/?action=add_cc&amp;Card=AE">Make New Payment</a></li>
					<li><a class="td_options" href="/webadmin/payment/?action=list_cc&amp;Card=AE">View All Payments</a></li>
				</ul>
			</li>
			<br />
			<li><a class="td_options" href="/webadmin/payment/payment_options.php">Payment Options</a></li>
		</ul></td>
	</tr>
</table>
<br />
<?php
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
