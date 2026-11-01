<!-- members_preferences.tpl -->

<?php
moduleLoad('invoice'); //invoiceSort
moduleLoad('product'); //productFromDatabase

$match_array = array(
    'car'  => 16,
    'cnv'  => 9,
    'he'   => 2,
    'hg'   => 1,
    'xbox' => 18,
    'asp'  => 20,
);
$discount_array = array(
    0  => 'Regular',
    2  => "Copper (2% off)",
    5  => 'Bronze (5% off)',
    10 => 'Silver (10% off)',
    15 => 'Gold (15% off)',
    20 => 'Platinum (20%)',
    30 => 'Titanium (30% off)',
);

$mem_type        = explode(",", $member['site_id']);
$he_invoices     = $only_invoices_chunked[2];
$last_he_invoice = array();
$last_he_order   = 'Never';
if (is_array($he_invoices)) {
    $he_invoices     = array_reverse(invoiceSort($he_invoices, 'open_date'));
    $last_he_invoice = $invoice_cache[reset($he_invoices)];
    $last_he_order   = date(_('d/m/Y'), $last_he_invoice['open_time']);
}

$hg_invoices     = $only_invoices_chunked[1];
$last_hg_invoice = array();
$last_hg_order   = 'Never';
if (is_array($hg_invoices)) {
    $hg_invoices     = array_reverse(invoiceSort($hg_invoices, 'open_date'));
    $last_hg_invoice = $invoice_cache[reset($hg_invoices)];
    $last_hg_order   = date(_('d/m/Y'), $last_hg_invoice['open_time']);
}

$cnv_invoices     = $only_invoices_chunked[9];
$last_cnv_invoice = array();
$last_cnv_order   = 'Never';
if (is_array($cnv_invoices)) {
    $cnv_invoices     = array_reverse(invoiceSort($cnv_invoices, 'open_date'));
    $last_cnv_invoice = $invoice_cache[reset($cnv_invoices)];
    $last_cnv_order   = date(_('d/m/Y'), $last_cnv_invoice['open_time']);
}

$car_invoices     = $only_invoices_chunked[16];
$last_car_invoice = array();
$last_car_order   = 'Never';
if (is_array($car_invoices)) {
    $car_invoices     = array_reverse(invoiceSort($car_invoices, 'open_date'));
    $last_car_invoice = $invoice_cache[reset($car_invoices)];
    $last_car_order   = date(_('d/m/Y'), $last_car_invoice['open_time']);
}

$xbox_invoices     = $only_invoices_chunked[18];
$last_xbox_invoice = array();
$last_xbox_order   = 'Never';
if (is_array($xbox_invoices)) {
    $xbox_invoices     = array_reverse(invoiceSort($xbox_invoices, 'open_date'));
    $last_xbox_invoice = $invoice_cache[reset($xbox_invoices)];
    $last_xbox_order   = date(_('d/m/Y'), $last_xbox_invoice['open_time']);
}

$asp_invoices     = $only_invoices_chunked[20];
$last_asp_invoice = array();
$last_asp_order   = 'Never';
if (is_array($asp_invoices)) {
    $asp_invoices     = array_reverse(invoiceSort($asp_invoices, 'open_date'));
    $last_asp_invoice = $invoice_cache[reset($asp_invoices)];
    $last_asp_order   = date(_('d/m/Y'), $last_asp_invoice['open_time']);
}

$arcooda_invoices     = $only_invoices_chunked[39];
$last_arcooda_invoice = array();
$last_arcooda_order   = 'Never';
if (is_array($arcooda_invoices)) {
    $arcooda_invoices     = array_reverse(invoiceSort($arcooda_invoices, 'open_date'));
    $last_arcooda_invoice = $invoice_cache[reset($arcooda_invoices)];
    $last_arcooda_order   = date(_('d/m/Y'), $last_arcooda_invoice['open_time']);
}

//now the last logins...
$last_he_login = $last_cnv_login = $last_car_login = $last_hg_login = $last_asp_login = 'Never';
if (is_array($chunked_logins['he'])) {
    $last_he_login = date(_('d/m/Y'), $chunked_logins['he'][0]['login_time']);
}
if (is_array($chunked_logins['hg'])) {
    $last_hg_login = date(_('d/m/Y'), $chunked_logins['hg'][0]['login_time']);
}
if (is_array($chunked_logins['cnv'])) {
    $last_cnv_login = date(_('d/m/Y'), $chunked_logins['cnv'][0]['login_time']);
}
if (is_array($chunked_logins['car'])) {
    $last_car_login = date(_('d/m/Y'), $chunked_logins['car'][0]['login_time']);
}
if (is_array($chunked_logins['asp'])) {
    $last_asp_login = date(_('d/m/Y'), $chunked_logins['asp'][0]['login_time']);
}
if (is_array($chunked_logins[2])) {
    $last_he_login = date(_('d/m/Y'), $chunked_logins[2][0]['login_time']);
}
if (is_array($chunked_logins[9])) {
    $last_hg_login = date(_('d/m/Y'), $chunked_logins[9][0]['login_time']);
}
if (is_array($chunked_logins[16])) {
    $last_cnv_login = date(_('d/m/Y'), $chunked_logins[16][0]['login_time']);
}
if (is_array($chunked_logins[20])) {
    $last_car_login = date(_('d/m/Y'), $chunked_logins[20][0]['login_time']);
}
if (is_array($chunked_logins[36])) {
    $last_asp_login = date(_('d/m/Y'), $chunked_logins[36][0]['login_time']);
}
if (is_array($chunked_logins[39])) {
    $last_arcooda_login = date(_('d/m/Y'), $chunked_logins[39][0]['login_time']);
}

?>
<script type="text/javascript">
//<![CDATA[
function prefsChecked(type, obj) {
	if (type != "hg" && type != "xbox") {
		getElement(type + "_a").style.display     = (obj.checked) ? "" : "none";
		getElement(type + "_prefs").style.display = "none";
	}
}
function updateOverallDiscount(site_id) {
	var new_val = "";
	if (site_id == 1 || site_id == 18) {
		new_val = "";
	} else {
		new_val = (getElement("member_site[" + site_id + "]").checked) ? getValue("discounts[" + site_id + "]") + "% discount" : "";
	}
	setAllValues("discount_" + site_id, new_val);
}
function addSpecialPrice(site_id, ret) {
	var url      = "members_special_pricing.php?return_message=" + ret + "&member_id=<?=$member_id?>&site_id=" + site_id;
	var features = "scrollbars, resizable";
	window.open(url, "product_popup", features);
}
function setSpecialData(data) {
	var table = getElement(data["return_message"] + "_spec");
	if (table == null) {
		return;
	}
	var newRow = addTableRow(table, "spec_" + data["id"]);
	if (table.rows.length % 2 == 1) {
		newRow.className = "alt";
	}
	addTableCell(newRow, data["product_id"]);
	addTableCell(newRow, data["product_name"]);
	addTableCell(newRow, data["min_order"]);
	addTableCell(newRow, "$" + data["product_onlineau"]);
	addTableCell(newRow, "$" + data["product_online"]);
	var newCell                   = addTableCell(newRow, "$" + data["special_priceau"]);
	newCell.style.backgroundColor = "#7FFFD4";
	newCell                       = addTableCell(newRow, "$" + data["special_price"]);
	newCell.style.backgroundColor = "#7FFFD4";
	addTableCell(newRow, "<input type=\"button\" value=\" Remove \" onclick=\"removeSpecialPrice(&quot;" + data["return_message"] + "&quot;, " + data["id"] + ");return false\" />");
}
function removeSpecialPrice(ret, id) {
	if (confirm("Are you sure you want to delete the Special Pricing??")) {
		var url      = "members_special_pricing.php?action=do_delete&return_message=" + ret + "&id=" + id;
		var features = "scrollbars, resizable, height=20, width=20";
		window.open(url, "product_popup", features);
	}
}
function deleteSpecialPrice(name, row_id) {
	var table = getElement(name + "_spec");
	if (table == null) {
		return;
	}
	var theRow = getElement("spec_" + row_id);
	if (theRow) {
		table.deleteRow(theRow.sectionRowIndex);
	}
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPrefs\'); return false">Website Preferences</a>')?>

<div id="sectionPrefs">
<!--
<table class="wide">
	<tr>
		<td class="left_title">Best Described As :</td>
		<td style="width:75%" class="right_input"><select name="member[best_described]">
			<option value=""></option>
			<?=formatHTMLOptions(memberGetOptionList("best_described"), $member['best_described'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Mailing List :</td>
		<td style="width:75%" class="right_input"><select name="member[mailing_list]">
			<?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $member['mailing_list'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Reseller :</td>
		<td style="width:75%" class="right_input"><select name="member[reseller]">
			<?=formatHTMLOptions(array('y' => 'Yes', 'n' => 'No'), $member['reseller'])?>
		</select></td>
	</tr>
</table>

<br />
-->

<table style="width:95%;border:1px solid #CCC" border="0"><tr>
	<td valign="top"><table class="wide">
<?php
foreach (memberGetOptionList("site_id") as $site_id => $site_name) {
    if ('' != $site_id) {
        $checked = (in_array($site_id, $mem_type)) ? true : false;
        $tmpDis  = intval($member_discounts[$site_id]);
        $s_dis   = ($checked && $tmpDis > 0) ? $tmpDis . "% discount" : "&nbsp;";
?>
		<tr class="pref_row" id="site_pref_<?=$site_id?>"style="<?=($checked) ? 'background-color:#EEFFEE;' : ''?>">
			<td style="border-bottom:1px solid #CCC"><input class="pref_check" type="checkbox" id="member_site_<?=$site_id?>" name="member_site[<?=$site_id?>]" <?=($checked) ? 'checked' : ''?> /></td>
			<td style="border-bottom:1px solid #CCC">
				<img src="/webadmin/images/sites/site_<?=$site_id?>/label.gif" alt="<?=htmlspecialchars($site_name)?>" /><br />
				<span id="discount_span_<?=$site_id?>"><?=$s_dis?></span><br />
				<small><i><a class="view_link" style="<?=($checked && in_array($site_id, array(2, 9, 16, 20, 36, 39))) ? '' : 'display:none'?>" id="a_<?=$site_id?>" href="#">View</a></i></small>
			</td>
		</tr>
<?php
}
}

?>
	</table></td>
	<td style="width:80%;text-align:center" valign="top">

<span id="message_first" style="display:none;"><b>Click on "View" link to view the preferences for a particular site</b></span>

<div id="prefs_2" class="prefs_box" style="display:none">
	<?=formatHTMLTitle('Highway Entertainment Preferences')?>
	<table class="wide">
		<tr>
			<td class="left_title">Member Type :<br /><small style="color:red">Discount applies to Spare Parts only</small></td>
			<td class="right_input" style="width:75%"><select class="discount_select" id="discount_2" name="discounts[2]">
				<?=formatHTMLOptions($discount_array, $member_discounts[2])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Date Joined :</td>
			<td class="right_input" style="width:75%"><?=date(_('d/m/Y'), $member['time_joined'])?></td>
		</tr>
		<tr>
			<td class="left_title">Total Logins</td>
			<td class="right_input" style="width:75%"><?=is_array($chunked_logins['he']) ? count($chunked_logins['he']) : 0?></td>
		</tr>
		<tr>
			<td class="left_title">Last Login :</td>
			<td class="right_input" style="width:75%"><?=$last_he_login?></td>
		</tr>
		<tr>
			<td class="left_title">Total Orders :</td>
			<td class="right_input" style="width:75%"><?=is_array($he_invoices) ? count($he_invoices) : 0?></td>
		</tr>
		<tr>
			<td class="left_title">Last Order :</td>
			<td class="right_input" style="width:75%"><?=$last_he_order?></td>
		</tr>
	</table>
	<br />
<?php
if ('add' != $action) {
?>
	<?=formatHTMLTitle('Highway Entertainment Special Pricing', '<a style="color:black" href="#" onclick="addSpecialPrice(2,\'he\');return false">Add</a>')?>
	<table class="wide" style="text-align:center;width:95%;margin:auto" id="he_spec" name="he_spec">
		<tr>
			<th>ID</th>
			<th>Product</th>
			<th>Qty</th>
			<th>Online (AUD)</th>
			<th>Online (USD)</th>
			<th>Special Price (AUD)</th>
			<th>Special Price (USD)</th>
			<th>&nbsp;</th>
		</tr>
<?php
$he_pricing = $member_specials[2];
    if (is_array($he_pricing)) {
        $count = 0;
        foreach ($he_pricing as $key => $product) {
?>
		<tr>
			<td><?=htmlspecialchars($product['product_id'])?></td>
			<td><?=htmlspecialchars($product['product_name'])?></td>
			<td><?=htmlspecialchars($product['min_order'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_online'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_price'])?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('he', <?=$product['id']?>);return false" /></td>
		</tr>
<?php
$count++;
        }
    }
?>
	</table>
	<br />
<?php
}
?>
</div>

<div id="prefs_9" class="prefs_box" style="display:none">
	<?=formatHTMLTitle('Converters.tv Preferences')?>
	<table class="table_fields">
		<tr>
			<th>Member Type :</th>
			<td><select class="discount_select" id="discount_9" name="discounts[9]">
				<?=formatHTMLOptions($discount_array, $member_discounts[9])?>
			</select></td>
		</tr>
		<tr>
			<th>Date Joined :</th>
			<td><?=date(_('d/m/Y'), $member['time_joined'])?></td>
		</tr>
		<tr>
			<th>Total Logins</th>
			<td><?=is_array($chunked_logins['cnv']) ? count($chunked_logins['cnv']) : 0?></td>
		</tr>
		<tr>
			<th>Last Login :</th>
			<td><?=$last_cnv_login?></td>
		</tr>
		<tr>
			<th>Total Orders :</th>
			<td><?=is_array($cnv_invoices) ? count($cnv_invoices) : 0?></td>
		</tr>
		<tr>
			<th>Last Order :</th>
			<td><?=$last_cnv_order?></td>
		</tr>
	</table>
	<br />
<?php
if ('add' != $action) {
?>
	<?=formatHTMLTitle('Converters.tv Special Pricing', '<a style="color:black" href="#" onclick="addSpecialPrice(9,\'cnv\');return false">Add</a>')?>
	<table class="table_grid" id="cnv_spec" name="cnv_spec">
		<tr>
			<th>ID</th>
			<th style="width:100%">Product</th>
			<th>Qty</th>
			<th>Online</th>
			<th>Special Price</th>
			<th>&nbsp;</th>
		</tr>
<?php
if (array_key_exists(9, $member_specials)) {
        $alt = false;
        foreach ($member_specials[9] as $member_special) {

            $product = null;
            if (9 == $_GLOBALS['site_settings']['site_id']) {
                $product = productFromDatabase($member_special['product_id']);
            }

            if (is_null($product)) {
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=htmlspecialchars($member_special['product_id'])?></td>
			<td>N/A</td>
			<td><?=htmlspecialchars($member_special['min_order'])?></td>
			<td>N/A</td>
			<td>N/A</td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('cnv', <?=$member_special['id']?>);return false" /></td>
		</tr>
<?php
} else {
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=htmlspecialchars($product['product_number'])?></td>
			<td><?=htmlspecialchars($product['product_name'])?></td>
			<td><?=htmlspecialchars($member_special['min_order'])?></td>
			<td style="white-space:nowrap">
				$<?=$product['secondary_price']?> <?=$product['secondary_currency']?>
<?php
if ('y' == $product['on_special']) {
?>
				($<?=$product['primary_price']?> <?=$product['primary_currency']?>)
<?php
}
?>
			</td>
			<td style="background-color:#7FFFD4;white-space:nowrap">
				$<?=$member_special[('AUD' == $product['primary_currency']) ? 'special_priceau' : 'special_price']?> <?=$product['primary_currency']?>
			</td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('cnv', <?=$member_special['id']?>);return false" /></td>
		</tr>
<?php
}
        }
    }
?>
	</table>
<?php
if (array_key_exists(9, $member_specials) && 9 != $_GLOBALS['site_settings']['site_id']) {
?>
	<p style="text-align:center"><b>Note: Converters.TV product information not fully available, use <a href="https://www.converters.tv<?=htmlspecialchars($_SERVER['REQUEST_URI'])?>">Converters.TV Webadmin</a></b></p>
<?php
}
?>
	<br />
<?php
}
?>
</div>

<?php
$car_details = $DB->query_first(sprintf(
    "SELECT `cm`.*, `mi`.`ic_code` FROM `converters`.`car_members` AS `cm` LEFT JOIN `converters`.`car_members_industry` AS `mi` ON `cm`.`member_id` = `mi`.`member_id` WHERE `cm`.`member_id` = %d LIMIT 1",
    $member_id
), MYSQLI_ASSOC);
?>
<div id="prefs_16" class="prefs_box" style="display:none">
	<?=formatHTMLTitle('Car Converters Preferences')?>
	<table class="wide">
		<tr>
			<td class="left_title left_input">Member Type :</td>
			<td colspan="3" class="right_input"><select class="discount_select" id="discount_16" name="discounts[16]">
				<?=formatHTMLOptions($discount_array, $member_discounts[16])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title left_input">Industry Code :</td>
			<td class="right_input"><input type="text" name="car[ic_code]" value="<?=$car_details['ic_code']?>" /></td>
			<td class="left_title left_input">Date Joined :</td>
			<td class="right_input"><?=date(_('d/m/Y'), $member['time_joined'])?></td>
		</tr>
		<tr>
			<td class="left_title left_input">Sales Rep :</td>
			<td class="right_input"><select name="car[sales_rep]">
				<?=formatHTMLOptions(array('' => '') + getSalesRep(), $car_details['sales_rep'])?>
			</select></td>
			<td class="left_title left_input">Total Logins</td>
			<td class="right_input"><?=is_array($chunked_logins['car']) ? count($chunked_logins['car']) : 0?></td>
		</tr>
		<tr>
			<td class="left_title left_input">Mailing List :</td>
			<td class="right_input"><select name="car[send_newsletter]">
				<?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $car_details['send_newsletter'])?>
			</select></td>
			<td class="left_title left_input">Last Login :</td>
			<td class="right_input"><?=$last_car_login?></td>
		</tr>
		<tr>
			<td class="left_title left_input">How Located :</td>
			<td class="right_input"><select name="car[heard_from]">
				<?=formatHTMLOptions(array('' => '') + getHowLocated(), $car_details['heard_from'])?>
			</select></td>
			<td class="left_title">Total Orders :</td>
			<td class="right_input"><?=is_array($car_invoices) ? count($car_invoices) : 0?></td>
		</tr>
		<tr>
			<td class="left_title">Last Order :</td>
			<td colspan="3" class="right_input"><?=$last_car_order?></td>
		</tr>
	</table>
	<br />
<?php
if ('add' != $action) {
?>
	<?=formatHTMLTitle('Car Converters Special Pricing', '<a style="color:black" href="#" onclick="addSpecialPrice(16,\'car\');return false">Add</a>')?>
	<table class="wide" style="text-align:center;width:95%;margin:auto" id="car_spec" name="car_spec">
		<tr>
			<th>ID</th>
			<th>Product</th>
			<th>Qty</th>
			<th>Online (AUD)</th>
			<th>Online (USD)</th>
			<th>Special Price (AUD)</th>
			<th>Special Price (USD)</th>
			<th>&nbsp;</th>
		</tr>
<?php
$car_pricing = $member_specials[16];
    if (is_array($car_pricing)) {
        $count = 0;
        foreach ($car_pricing as $key => $product) {
?>
		<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="spec_<?=$product['id']?>">
			<td><?=htmlspecialchars($product['product_id'])?></td>
			<td><?=htmlspecialchars($product['product_name'])?></td>
			<td><?=htmlspecialchars($product['min_order'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_online'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_price'])?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('car',<?=$product['id']?>);return false" /></td>
		</tr>
<?php
$count++;
        }
    }
?>
	</table>
	<br />
<?php
}
?>
</div>

<div id="prefs_20" class="prefs_box" style="display:none">
	<?=formatHTMLTitle('Arcade Spare Parts Preferences')?>
	<table class="wide">
		<tr>
			<td class="left_title">Member Type :</td>
			<td class="right_input" style="width:75%"><select class="discount_select" id="discount_20" name="discounts[20]">
				<?=formatHTMLOptions($discount_array, $member_discounts[20])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Date Joined :</td>
			<td class="right_input" style="width:75%"><?=date(_('d/m/Y'), $member['time_joined'])?></td>
		</tr>
		<tr>
			<td class="left_title">Total Logins</td>
			<td class="right_input" style="width:75%"><?=is_array($chunked_logins['asp']) ? count($chunked_logins['asp']) : 0?></td>
		</tr>
		<tr>
			<td class="left_title">Last Login :</td>
			<td class="right_input" style="width:75%"><?=$last_asp_login?></td>
		</tr>
		<tr>
			<td class="left_title">Total Orders :</td>
			<td class="right_input" style="width:75%"><?=is_array($asp_invoices) ? count($asp_invoices) : '-'?></td>
		</tr>
		<tr>
			<td class="left_title">Last Order :</td>
			<td class="right_input" style="width:75%"><?=$last_asp_order?></td>
		</tr>
	</table>
	<br />
<?php
if ('add' != $action) {
?>
	<?=formatHTMLTitle('Arcade Spare Parts Special Pricing', '<a style="color:black" href="#" onclick="addSpecialPrice(20, \'asp\');return false">Add</a>')?>
	<table class="wide" style="text-align:center;width:95%;margin:auto" id="asp_spec" name="asp_spec">
		<tr>
			<th>ID</th>
			<th>Product</th>
			<th>Qty</th>
			<th>Online (AUD)</th>
			<th>Online (USD)</th>
			<th>Special Price (AUD)</th>
			<th>Special Price (USD)</th>
			<th>&nbsp;</th>
		</tr>
<?php
$asp_pricing = $member_specials[20];
    if (is_array($asp_pricing)) {
        $count = 0;
        foreach ($asp_pricing as $key => $product) {
?>
		<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="spec_<?=$product['id']?>">
			<td><?=htmlspecialchars($product['product_id'])?></td>
			<td><?=htmlspecialchars($product['product_name'])?></td>
			<td><?=htmlspecialchars($product['min_order'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_online'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_price'])?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('asp', <?=$product['id']?>);return false" /></td>
		</tr>
<?php
$count++;
        }
    }
?>
	</table>
<?php
}
?>
</div>

<div id="prefs_36" class="prefs_box" style="display:none">
	<?=formatHTMLTitle('Highway Gaming Preferences')?>
	<table class="wide">
		<tr>
			<td class="left_title">Registered Products :</td>
			<td class="right_input" style="width:75%">
<?php
if ($DB->query_first(sprintf(
    "SHOW DATABASES LIKE 'hwgamin_hwgamin'"
), MYSQLI_ASSOC)) {
    $split = explode(',', $member['hwgaming_products']);
    if ($query = $DB->query(sprintf(
        "SELECT `id`, `name` FROM `hwgamin_hwgamin`.`products` ORDER BY `name` ASC"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
?>
				<div style="display:inline-block;min-width:320px">
					<input type="checkbox" name="hwgaming_products[]" value="<?=$row['id']?>"<?=(in_array($row['id'], $split)) ? ' checked="checked"' : ''?> style="width:auto" />
					<?=htmlspecialchars($row['name'])?>
				</div>
<?php
        }
        $DB->free_result($query);
    }
?>
				<input type="hidden" name="hwgaming_products_update" value="on" />
<?php
} else {
    echo 'Please use Highway Group webadmin to edit';
}
?>
			</td>
		</tr>
	</table>
</div>

<?php
$customer_type = explode(',', $member['customer_type']);
?>
<div id="prefs_39" class="prefs_box" style="display:block">
	<?=formatHTMLTitle('Arcooda Preferences')?>
	<table class="wide"><tr>
	    <td class="left_title">Wholesale Account :</td>
    	<td class="right_input-" style="width:75%"><input type="checkbox" name="member[wholesale]" value='y'<?=('y' == $member['wholesale']) ? ' checked="checked"' : ''?> /></td>
	</tr></table>
	<table class="wide"><tr>
		<td class="left_title">Agent :</td>
		<td class="right_input-" style="width:75%"><input type="checkbox" name="customer_type[]" value="agent"<?=(in_array('agent', $customer_type)) ? ' checked="checked"' : ''?> /></td>
	</tr></table>
	<table class="wide"><tr>
		<td class="left_title">Distributor :</td>
		<td class="right_input-" style="width:75%"><input type="checkbox" name="customer_type[]" value="distributor"<?=(in_array('distributor', $customer_type)) ? ' checked="checked"' : ''?> /></td>
	</tr></table>
	<br/>
	<br/>
	<table class="wide"><tr>
		<td class="left_title">Newsletter :</th>
		<!-- <td class="right_input-" style="width:75%;<?=('y' == $member_preferences['customer']) ? 'background-color:#90EEA0;' : ''?>"><input type="checkbox" name="member_preferences[customer]" value='y' <?=('y' == $member_preferences['customer']) ? 'checked' : ''?> /></td> -->
		<td class="right_input-" style="width:75%;<?=('y' == $member['mailing_list']) ? 'background-color:#90EEA0;' : ''?>"><input type="checkbox" name="member[mailing_list]" value='y' <?=('y' == $member['mailing_list']) ? 'checked' : ''?> class="newslettercheck"/></td>
	</tr></table><br>
	<table class="wide">
		<tr>
			<td class="left_title">Member Type :</td>
			<td class="right_input" style="width:75%"><select class="discount_select" id="discount_39" name="discounts[39]">
				<?=formatHTMLOptions($discount_array, $member_discounts[39])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Date Joined :</td>
			<td class="right_input" style="width:75%"><?=date(_('d/m/Y'), strtotime($member['created_at']))?></td>
		</tr>
		<tr>
			<td class="left_title">Total Logins</td>
			<td class="right_input" style="width:75%"><?=(is_array($chunked_logins['arcooda']) ? count($chunked_logins['arcooda']) : 0) + (is_array($chunked_logins[39]) ? count($chunked_logins[39]) : 0)?></td>
		</tr>
		<tr>
			<td class="left_title">Last Login :</td>
			<td class="right_input" style="width:75%"><?=$last_arcooda_login?></td>
		</tr>
		<tr>
			<td class="left_title">Total Orders :</td>
			<td class="right_input" style="width:75%"><?=is_array($arcooda_invoices) ? count($arcooda_invoices) : 0?></td>
		</tr>
		<tr>
			<td class="left_title">Last Order :</td>
			<td class="right_input" style="width:75%"><?=$last_arcooda_order?></td>
		</tr>
	</table>
	<br />
<?php
if ('add' != $action) {
?>
	<?=formatHTMLTitle('Arcooda Special Pricing', '<a style="color:black" href="#" onclick="addSpecialPrice(39, \'arcooda\');return false">Add</a>')?>
	<table class="wide" style="text-align:center;width:95%;margin:auto" id="arcooda_spec" name="arcooda_spec">
		<tr>
			<th>ID</th>
			<th>Product</th>
			<th>Qty</th>
			<th>Online (AUD)</th>
			<th>Online (USD)</th>
			<th>Special Price (AUD)</th>
			<th>Special Price (USD)</th>
			<th>&nbsp;</th>
		</tr>
<?php
$arcooda_pricing = $member_specials[39];
    if (is_array($arcooda_pricing)) {
        $count = 0;
        foreach ($arcooda_pricing as $key => $product) {
?>
		<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="spec_<?=$product['id']?>">
			<td><?=htmlspecialchars($product['product_id'])?></td>
			<td><?=htmlspecialchars($product['product_name'])?></td>
			<td><?=htmlspecialchars($product['min_order'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
			<td>$<?=sprintf('%.2f', $product['product_online'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
			<td style="background-color:#7FFFD4">$<?=sprintf('%.2f', $product['special_price'])?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialPrice('arcooda', <?=$product['id']?>);return false" /></td>
		</tr>
<?php
$count++;
        }
    }
?>
	</table>
<?php
}
?>
</div>

	</td>
</tr></table>

<br />
<br />

</div>
<script type="text/javascript">
	$(".newslettercheck").click(function() {
		var checked=($(this).is(":checked"));
		if(checked){
			setAllValues('members_mailing[12][active]','y');
		}else{
			setAllValues('members_mailing[12][active]','n');
		}
	});
</script>
<!-- /members_preferences.tpl -->
