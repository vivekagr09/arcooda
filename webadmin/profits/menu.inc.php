<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
/*if (!moduleExists("profit")) {
    return;
}*/

$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));

$temp =  $_SERVER["DOCUMENT_ROOT"] . "/protected/cache" . $stub . "_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 3600) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}

ob_start();
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b> Profit/Sales Summary</b></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
		<li><a href="#" class="td_options" onclick="toggleSection('menu_profit_salesbreakup');return false">Sales Breakup</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_salesbreakup">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=sales&amp;type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=sales&amp;type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=sales&amp;type=quarterly">Quaterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=sales&amp;type=yearly">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_profitloss');return false">Profit &amp; Loss</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_profitloss">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=summary">View Summaries</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=break_even">Set Break-Even</a></li>
			</ul>
		</li>
		<li><a href="#" class="td_options" onclick="toggleSection('menu_profit_bestsalesproducts');return false">Best Sales Products</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_bestsalesproducts">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_worstsalesproducts');return false">Worst Sales Products</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_worstsalesproducts">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;type=worst&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;type=worst&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;type=worst&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=products_sales&amp;type=worst">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="/webadmin/profits/?action=sales_search">Sales Search</a></li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_countrysales');return false">Country Sales</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_countrysales">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=country_sales&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=country_sales&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=country_sales&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=country_sales">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_freightsales');return false">Freight Sales</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_freightsales">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=freight_sales&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=freight_sales&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=freight_sales&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=freight_sales">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_paymenttypes');return false">Payment Methods</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_paymenttypes">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=payment_types&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=payment_types&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=payment_types&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options"href="/webadmin/profits/?action=payment_types">Yearly Sales</a></li>
			</ul>
		</li>
		<li><a class="td_options" href="#" onclick="toggleSection('menu_profit_customersales');return false">Customer Sales</a></li>
		<li class="td_options" style="display:none;list-style:none;" name="menu_profit_customersales">
			<ul>
				<li><a class="td_options" href="/webadmin/profits/?action=customer_sales&amp;sale_type=weekly">Weekly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=customer_sales&amp;sale_type=monthly">Monthly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=customer_sales&amp;sale_type=quarterly">Quarterly Sales</a></li>
				<li><a class="td_options" href="/webadmin/profits/?action=customer_sales">Yearly Sales</a></li>
			</ul>
		</li>
	<!-- </ul></li> -->
	</ul></td></tr>
</table>
<br /><br />
<?php
file_put_contents($temp, ob_get_flush());
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
