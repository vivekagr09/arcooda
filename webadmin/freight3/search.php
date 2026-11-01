<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "search";

$cubic_divisor   = "5000.0";
$dead_weight     = bcadd("0.0", $_REQUEST["weight"], 2);
$volume   = bcdiv(bcmul(bcmul($_REQUEST["width"], $_REQUEST["depth"], 4), $_REQUEST["height"], 4), $cubic_divisor, 2);
$shipping_weight = (bccomp($dead_weight, $volume, 2) > 0) ? $dead_weight : $volume;


$count = 0;
$invoice_courier_quotes = array();
if (bccomp($shipping_weight, "0.0000", 4))
{
	$flag = false;
	$found = array();
	foreach(freightSortArray(freightSearchCouriers($invoice["ship_country"], $invoice["ship_postcode"], $invoice["ship_state"], $invoice["ship_city"], $invoice["ship_weight"], $invoice["products_offer"], $invoice["invoice_currency"])) as $v)
	{
		$key = $v["ship_number"];
		$v["packing_string"] = "";

		$container_list = array(true);
		if (moduleLoad("packing"))
		{
			list($width, $depth, $height) = array($v['width'], $v['depth'], $v['height']);
			if (bccomp($width, "0.0", 1) > 0 && bccomp($depth, "0.0", 1) > 0)
			{
				$packing = array();
				$packing['packing_products'] = $invoice_products;
				$packing['satchel'] = array('width' => $v['width'], 'depth' => $v['depth'], 'height' => $v['height'], 'weight' => $v['weight'], 'name' => $v['name']);
				$packing['packing_action'] = 'checkout_satchel';
				$packing['spareparts'] = true;
				$error_list = array();
				$container_list = packingFromCache($packing);
				if (count($error_list)) $container_list = array();
				if (count($container_list) == 1) $v["packing_string"] = "b64_gz_ser:" . base64_encode(gzcompress(serialize($container_list)));
			}
		}


		if (!array_key_exists($key, $found) && count($container_list) == 1)
		{
			/**
			 * Stupid fucking price rounding, argh
			 */
			$dollars = convertCurrency($v["sell_price"], $v["currency"], $invoice["invoice_currency"]);
			$cents = bcmod(bcmul($dollars, "100", 0), "100");
			if (bccomp($cents, "0", 0) > 0) $dollars = bcadd($dollars, bcmul(bcsub("100", $cents, 0), "0.01", 2), 2);

			$invoice_courier_quotes[] = array(
				"ship_number" => $v["ship_number"],
				"service_id" => $v["id"],
				"express" => array_key_exists("express", $v) ? $v["express"] : "n",
				"receipted" => array_key_exists("receipted", $v) ? $v["receipted"] : "n",
				"registered" => array_key_exists("registered", $v) ? $v["registered"] : "n",

				"width" => $v["width"],
				"depth" => $v["depth"],
				"height" => $v["height"],
				"weight" => $v["weight"],

				"quote_count" => $count,
				"flag" => ($v["active"] == "y" && !$flag) ? "y" : "n",
				"name" => $v["name"],
				"del_time" => $v["del_time"],
				"track" => $v["track"],
				"insurance" => $v["insurance"],
				"tail_gate" => $v["tail_gate"],
				"cost_price" => convertCurrency($v["cost_price"], $v["currency"], $invoice["invoice_currency"]),
				"retail_price" => convertCurrency($v["retail_price"], $v["currency"], $invoice["invoice_currency"]),
				"cost_priceau" => convertCurrency($v["cost_price"], $v["currency"], "AUD"),
				"cost_priceus" => convertCurrency($v["cost_price"], $v["currency"], "USD"),
				"supplier" => $v["supplier"],
				"supplier_name" => $v["supplier_name"],

				// Continued: Stupid fucking rounding
				"sell_price" => $dollars,
				"sell_priceau" => convertCurrency($dollars, $invoice["invoice_currency"], "AUD"),
				"sell_priceus" => convertCurrency($dollars, $invoice["invoice_currency"], "USD"),

				"active" => $v["active"],
				"packing_string" => $v["packing_string"],
				"Locality" => array_key_exists("Locality", $v) ? $v["Locality"] : "",
				"State" => array_key_exists("State", $v) ? $v["State"] : "",
				"Pcode" => array_key_exists("Pcode", $v) ? $v["Pcode"] : "",
				"Country" => $v["Country"],
				"qty" => ($v["type"] == "pp") ? $v["qty"] : "-",
			);
			if ($v["active"] == "y") $flag = true;
			$found[$key] = $key;
			$count++;
		}
	}
}


?>

<script type="text/javascript">
//<![CDATA[
function freightSearchValidate() {
    return true;
}
//]]>
</script>

<?= formatHTMLTitle('Freight Calculator') ?>

<form accept-charset="utf-8" autocomplete="off" method="get" onsubmit="return freightSearchValidate()">
    <table class="table_fields">
        <tr>
            <th style="width:25%;"><?php hecho(_('City')) ?> :</th>
            <td><input type="text" name="Locality" value="<?php hecho($_REQUEST['Locality']) ?>" style="width:100%;" /></td>
            <td style="width:25%;">
<?php
	if (!array_key_exists("Locality", $varies))
	{
?>
                <span style="color:#f00;">WARNING: No match found</span>
<?php
	}
	elseif (strtoupper(reset($varies["Locality"])) != strtoupper($_REQUEST['Locality']))
	{
?>
                <span style="color:#f00;">WARNING: Using closest match '<?php hecho(reset($varies["Locality"])) ?>'</span>
<?php
	}
	else
	{
?>
                <span style="color:#0f0;">Match found</span>
<?php
	}
?>
			</td>
		</tr>
		<tr>
			<th><?php hecho(_('State')) ?> :</th>
			<td><input type="text" name="State" value="<?php hecho($_REQUEST['State']) ?>" style="width:100%;" /></td>
			<td>
<?php
	if (!array_key_exists("State", $varies))
	{
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
	}
	elseif (strtoupper(reset($varies["State"])) != strtoupper($_REQUEST['State']))
	{
?>
				<span style="color:#f00;">WARNING: Using closest match '<?php hecho(reset($varies["State"])) ?>'</span>
<?php
	}
	else
	{
?>
				<span style="color:#0f0;">Match found</span>
<?php
	}
?>
			</td>
		</tr>
		<tr>
			<th><?php hecho(_('Postcode')) ?> :</th>
			<td><input type="text" name="Pcode" value="<?php hecho($_REQUEST['Pcode']) ?>" style="width:100%;" /></td>
			<td>
<?php
	if (!array_key_exists("Pcode", $varies))
	{
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
	}
	elseif (intval(reset($varies["Pcode"])) != intval($_REQUEST['Pcode']))
	{
?>
				<span style="color:#f00;">WARNING: Using closest match '<?php hecho(reset($varies["Pcode"])) ?>'</span>
<?php
	}
	else
	{
?>
				<span style="color:#0f0;">Match found</span>
<?php
	}
?>
			</td>
		</tr>
		<tr>
			<th><?php hecho(_('Country')) ?> :</th>
			<td><select name="Country" style="width:100%;">
				<option value=""></option>
				<?= formatHTMLOptions(getCountryListNested(), $_REQUEST['Country']) ?>
			</select></td>
			<td></td>
		</tr>
        <tr>
            <th><?php hecho(_('Shipment Weight (kg)')) ?> :</th>
            <td><input type="text" size="6" name="weight" value="<?php hecho($_REQUEST['weight']) ?>" style="width:100%;" /></td>
			<td></td>
        </tr>
        <tr>
            <th><?php hecho(_('Shipment Dimensions (cm)')) ?> :</th>
            <td>
                <input type="text" size="6" name="width" value="<?php hecho($_REQUEST['width']) ?>" /> <b><?= _('Width') ?> x</b>
                <input type="text" size="6" name="depth" value="<?php hecho($_REQUEST['depth']) ?>" /> <b><?= _('Depth') ?> x</b>
                <input type="text" size="6" name="height" value="<?php hecho($_REQUEST['height']) ?>" /> <b><?= _('Height') ?></b>
            </td>
			<td></td>
        </tr>
        <tr>
            <th><?php hecho(sprintf(_('Insurance Value (%s)'), $_REQUEST['currency'])) ?> :</th>
            <td><input type="text" size="6" name="value" value="<?php hecho($_REQUEST['value']) ?>" style="width:100%;" /></td>
			<td></td>
        </tr>
        <tr>
            <th>Currency :</th>
            <td><select style="width:100%;" name="currency">
                <option value=""></option>
                <?= formatHTMLOptions($section_handler->getOptionList('currency'), $_REQUEST['currency']) ?>
            </select></td>
			<td></td>
        </tr>
    </table>
    <div style="text-align:center;"><input type="submit" value="Calculate" /></div>
</form>
<br />


<?php

$dest = $posted['destination'];
if ('' != $posted['destination']) {
    $list = freightInternationalCalculate($destination, $currency, $shipping_weight);
    $list = freightCalculateIntPrices($list, $shipping_weight, $currency, $posted['value']);
    foreach ($list as $id => $array) {
        $list[$id]['staff_display'] = true;
        if ('y' == $array['insurance'] && $array['insurance_cost'] >= 1 && !strpos($array['insurance_cost'], '(')) {
            $new_array                  = $array;
            $new_array['sell_price']    = $new_array['sell_price'] + $new_array['insurance_cost'];
            $new_array['insurance']     = 'y';
            $new_array['staff_display'] = false;
            $list[$id]['insurance']     = 'n';
            $list[]                     = $new_array;
        }
    }
    $list = freightSortArray($list);
    if (count($list) > 0) {
        $spec_notes = false;
        $eNote      = false;
        $alt        = false;
        foreach ($list as $id => $array) {
            if ('' != $array['notes']) {
                $spec_notes = true;
            }
        }

?>
<center><h3>Results for sending <?php echo $shipping_weight ?> kgs from Australia to <?php echo ucwords(getCountryName($destination)) ?></h3></center>
<?php
$extra = '<small><i><a name="view_link" style="color:blue" href="#" onclick="viewAll(false);return false;">View All</a><a name="hide_link" style="color:blue;display: none" href="#" onclick="viewAll(true);return false;">Hide</a></i></small>';?>
<center><?php echo formatHTMLTitle('Staff Summary', $extra) ?></center>
<table class="wide" align="center" style="width: 98%;" cellspacing=0 cellpadding=2 border=1 bordercolor="#CCCCCC">
	<tr><th>Provider</th><th>Service</th><th>Buy Rate (<?php echo $currency ?>)</th><th>Retail Price (<?php echo $currency ?>)</th><th>Our Sell Price (<?php echo $currency ?>)</th><th>Profit (AUD)</th></tr>
<?php
$spec_notes = false;
        foreach ($list as $id => $array) {
            if (!isset($array['profit'])) {
                $array['profit'] = $array['sell_price'] - $array['cost_price'];
            }

            $urls        = array('pp' => 'express', 'truck' => 'truck', 'acc' => 'account');
            $edit_url    = "/webadmin/" . $urls[$array['type']] . "/index.php?action=edit&product_id=" . $array['id'];
            $forward_url = "/webadmin/forwarders/index.php?action=edit&forwarder_id=" . $array['supplier'];
            if ($array['staff_display']) {
?>
	<tr <?php echo ($alt = !$alt) ? '' : ' class="alt"' ?> style="text-align: center">
		<td>
<?php
if ('truck' != $array['type']) {
?>
				<a href="<?php echo $forward_url ?>" target="_blank" onclick="<?php echo (0 == $array['supplier'] ? 'return false;' : '') ?>">
					<?php echo getFreightForwarderName($array['supplier']) ?>
				</a>
<?php
} else {
?>
				N/A
<?php
}
?>
		</td>
		<td><a href="<?php echo $edit_url ?>" target="_blank" style="color: black"><?php echo $array['name'] ?></a></td>
<?php
$cost   = convertCurrency($array['cost_price'], 'AUD', $posted['currency']);
                $cost   = ('acc' == $array['type']) ? ceil($cost) : round($cost);
                $retail = convertCurrency($array['retail_price'], 'AUD', $posted['currency']);
                $retail = ('acc' == $array['type']) ? ceil($retail) : round($retail);
                $sell   = convertCurrency($array['sell_price'], 'AUD', $posted['currency']);
                $sell   = ('acc' == $array['type']) ? ceil($sell) : round($sell);
                $profit = ('acc' == $array['type']) ? ceil($array['profit']) : round($array['profit']);
?>
		<td>$<?php echo sprintf("%.2f", $cost) ?></td>
		<td>$<?php echo sprintf("%.2f", $retail) ?></td>
		<td><b>$<?php echo sprintf("%.2f", $sell) ?></b></td>
		<td>$<?php echo sprintf("%.2f", $profit) ?></td>
	</tr>
<?php
if ('' != $array['break_up']) {?>
	<tr name="break_up" id="break_up" style="display: none;background-color: #F7F7F7;<?php echo ('IE' == $browser['browser']) ? 'border-collapse: collapse' : '' ?>">
		<td colspan=6><?php echo nl2br($array['break_up']) ?></td>
	</tr>
<?php
}
            }?>
<?php
}?>
</table>
<br />
<center><?php echo formatHTMLTitle('Customer Summary') ?></center><br /><br/>
<table class="wide" align="center" style="width:75%;border:1px solid black;" cellspacing="0" cellpadding="2" border="1" bordercolor="black">
<?php
$colspan = 5;
        if ($spec_notes) {
            $colspan = 6;
        }

?>
	<tr style="text-align: center;">
		<td style="font-size: 9pt;font-weight:bold" colspan="<?php echo $colspan ?>">
			&nbsp;<br/>Prices for sending <?php echo $shipping_weight ?> kgs from Australia to <?php echo ucwords(getCountryName($destination)) ?><br/>&nbsp;
		</td>
	</tr>
	<tr><th>Price (<?php echo $posted['currency'] ?>)</th><th>Service</th><th>Transit Time</th><th>Tracking</th><th>Insurance<?php echo (0 == $posted['value']) ? ' Available' : '' ?></th>
<?php
if ($spec_notes) {
?>
		<th>Special Notes</th>
<?php
}
?>
	</tr>
<?php
foreach ($list as $id => $array) {
            $name = explode('-', $array['name']);
?>
	<tr style="text-align:center;" class="alt">
		<td style="font-size: 8pt"><b>$<?php echo sprintf("%.2f", ceil(convertCurrency($array['sell_price'], 'AUD', $posted['currency']))) ?></b></td>
		<td style="font-size:8pt;"><?php echo trim($name[0]) ?></td>
		<td style="font-size:8pt;"><?php echo $array['del_time'] ?></td>
		<td style="font-size:8pt;"><?php echo $status[$array['track']] ?></td>
<?php
$ins = (strpos($array['insurance_cost'], '(')) ? $array['insurance_cost'] : (('y' == $array['insurance']) ? 'Included' : 'No');
?>
		<td style="font-size: 8pt"><?php echo $ins ?></td>
<?php
if ($spec_notes) {
?>
		<td style="font-size: 8pt"><?php echo $array['notes'] ?></td>
<?php
}
?>
	</tr>
<?php
}
?>
	<tr class="alt">
		<td colspan="<?php echo $colspan ?>" style="font-size: 9px;font-family:Verdana;text-align: left;padding:10px 0px 10px 25px;">
			<li><b>PRICING :</b> Above prices are in <?php echo getCurrencyName($currency) ?></li>
			<li><b>DELIVERY :</b> Please note the transit times are estimates only.</li>
			<li><b>TRACKING :</b> Some couriers give you to the facility to Track the status of your parcel over the Internet. (A tracking number & website will be emailed to you once your goods have been shipped, if available).
			</li>
			<li><b>INSURANCE :</b> Some couriers also provide Insurance. The cost of insurance is usually based on some percent of the total value of the goods.</li>
		</td>
	</tr>
</table>
<?php
} else {
?>
	<center>No Services available for shipping <?php echo $shipping_weight ?> kgs from Australia to <?php echo ucwords(getCountryName($posted['destination'])) ?></center>
<?php
}
}
?>
<?php
include_once __DIR__ . '/footer.inc.php';
?>

<?php

if (!array_key_exists("return_message", $_GET)) $_GET["return_message"] = " ";

$active = array();
$inactive = array();
foreach($invoice_courier_quotes as $k => $v) if ($v["active"] == "y") $active[$k] = $v;
else $inactive[$k] = $v;

include_once __DIR__ . '/header.inc.php';
?>
<style type="text/css">
.ac_results	{ padding:0; border:1px solid #000; background-color:#fff; overflow:hidden; z-index:99999; }
.ac_results ul	{ width:100%; list-style-position:outside; list-style:none; padding:0; margin:0; }
.ac_results li	{ margin:0; padding:2px 5px; cursor:default; display:block; font:menu; font-size:12px; line-height:16px; overflow:hidden; }
.ac_loading	{ background:#fff url(/images/loadingAnimation.gif) right center no-repeat; }
.ac_odd	{ background-color:#eee; }
.ac_over	{ background-color:#0A246A; color:#fff; }

.active_n td	{ background:#fcc; }
.alt.active_n td	{ background:#fbb; }
.highlight.active_n td	{ background:#fb7; }
</style>

<script type="text/javascript" src="/jquery/autocomplete&skip_deps.js"></script>

<script type="text/javascript">
//<![CDATA[
var all_data = <?php jecho($invoice_courier_quotes) ?>;
var active_data = <?php jecho($active) ?>;
<?php
if (count($invoice_courier_quotes))
{
?>
if (window.opener != null && window.opener.setCourierData != null) window.opener.setCourierData(active_data);
else if(parent.setCourierData != null) parent.setCourierData(active_data);
<?php
}
?>

$(function() {
	$("[name='checkout[ship_number]']").change(function() {
		if ($(this).attr("checked"))
		{
			$(this).closest("table").find(".highlight").removeClass("highlight");
			$(this).closest("tr").addClass("highlight");
		}
	});
	$("[name='courier_search_accept']").click(function() {
		var selected_number = $("[name='checkout[ship_number]']:checked").val();
		for (var k in all_data) if (all_data[k].ship_number == selected_number)
		{
			if (window.opener != null && window.opener.setShipToCourierQuote != null) window.opener.setShipToCourierQuote(all_data[k]);
			else if(parent.setShipToCourierQuote != null) parent.setShipToCourierQuote(all_data[k]);
			else alert('Unable to send freight quote back to form');
			window.close();
			break;
		}
	}).fadeIn();
	$("[name='shipto_country[0]']").change(function() {
		$(".ship_country_AU").stop().toggle($(this).val() == "AU");
	}).change();
	$("[name='shipto_city[0]'], [name='shipto_state[0]'], [name='shipto_postcode[0]']").autocomplete("postcode_search.json.php", {
		"formatItem":function(row, i, max) {
			return row.Locality + ", " + row.State + " " + row.Pcode;
		},
		"matchContains":true,
		"scroll":true,
		"parse":function parse(data) {
			var parsed = [];
			for (var i = 0;i < data.length;i++) parsed.push({
				data: data[i],
				value: data[i].Locality + ", " + data[i].State + " " + data[i].Pcode,
				result: null
			});
			return parsed;
		}
	}).result(function(event, data, formatted) {
		$("[name='shipto_city[0]']").val(data.Locality);
		$("[name='shipto_state[0]']").val(data.State);
		$("[name='shipto_postcode[0]']").val(data.Pcode);
	});
});

//]]>
</script>

<form accept-charset="utf-8" autocomplete="off" method="post" action="">
<?php
$inputs = $posted;
foreach(array("shipto_country", "shipto_postcode", "shipto_state", "shipto_city", "ship_weight", "products_offer", /*"invoice_currency", */) as $v) if (array_key_exists($v, $inputs)) unset($inputs[$v]);
echo formatHTMLInputs($inputs);

$varies = array();
foreach($invoice_courier_quotes as $k1 => $v1) foreach($v1 as $k2 => $v2) $varies[$k2][$v2] = $v2;

?>
	<table style="width:75%;">
<?php
if ($_GLOBALS["site_settings"]["site_id"] != 20)
{
?>
	</table>
	<input type="submit" />
</form>

<?php
if (count($problem_products))
{
?>
<div style="border:3px solid #f00;background:#fbb;width:95%;margin:auto;text-align:left;"><div style="padding:16px;">
	Correct shipping weights are unavailable for:
	<ul>
<?php
	foreach($problem_products as $v)
	{
?>
		<li><?php hecho((($v["sub_id"] != 0) ? sprintf("%05d-%04d", $v["product_id"], $v["sub_id"]) : sprintf("%05d", $v["product_id"])) . " - " . $v["product_name"]) ?></li>
<?php
	}
?>
	</ul>
</div></div>
<?php
}

if (moduleLoad("freight")) foreach(freightWarningsSearch(array("country" => $invoice["ship_country"])) as $id)
{
	$warning = freightWarningFromDatabase($id);
?>
<div style="background:pink;border:3px solid red;text-align:center;padding:16px;margin:16px;"><b style="color:#f00;">Notice:</b> <?php echo nl2br(hecho($warning["notes"], 1)) ?></div>
<?php
}

if (!moduleLoad("packing"))
{
?>
<div style="background:pink;border:3px solid red;text-align:center;padding:16px;margin:16px;"><b style="color:#f00;">Notice:</b> Packing dimension calculations were unsuccessful, please ensure products will fit your selected box or satchel before continuing</div>
<?php
}

$courier_list = array();
if (function_exists("getFreightServices") || moduleLoad("freight")) $courier_list = getFreightServices('personal', '', true);

/*
$varies = array();
foreach($invoice_courier_quotes as $k1 => $v1) foreach($v1 as $k2 => $v2) $varies[$k2][$v2] = $v2;
unset($varies["packing_string"]);
foreach($varies as $k => $v) if (count($v) == 1) unset($varies[$k]);
print_table($varies);
*/
?>

<?php echo formatHTMLTitle("Available Freight Quotes", '<a href="#" onclick="' . hecho("$(\".active_n\").toggle();return false", 1) . '">Show/Hide Inactive Services</a>') ?>

<table class="wide spacetable table_grid">
	<tr>
		<th>ID</th>
		<th>Service</th>
		<th>Transit Time</th>
		<th>Tracking</th>
		<th>Insurance</th>
		<th>Tail Gate</th>
		<th>Qty</th>
		<th>Cost<br />Price<br />(<?php hecho($invoice["invoice_currency"]) ?>)</th>
		<th>Sell<br />Price<br />(<?php hecho($invoice["invoice_currency"]) ?>)</th>
<?php
if (in_array($invoice["invoice_currency"], array("AUD", "USD")) || $invoice["site_id"] != 20)
{
	if ($invoice["invoice_currency"] != "AUD")
	{
?>
		<th>Cost<br />Price<br />(AUD)</th>
		<th>Sell<br />Price<br />(AUD)</th>
<?php
	}
}
elseif ($invoice["invoice_currency"] != "USD")
{
?>
		<th>Cost<br />Price<br />(USD)</th>
		<th>Sell<br />Price<br />(USD)</th>
<?php
}
?>
	</tr>
<?php
$alt = true;
if (/*!$issues["weight"] &&*/ count($invoice_courier_quotes)) foreach($invoice_courier_quotes as $k => $v)
{
	$alt = !$alt;
	$key = "checkout_invoice_courier_quote_" . $k;
	$class = array();
	$class[] = "active_" . $v["active"];
	if ($v["flag"] == "y") $class[] = "highlight";
	if ($alt) $class[] = "alt";
?>
	<tr<?php echo (count($class)) ? (' class="' . implode(" ", $class) . '"') : '' ?> <?php echo ($v["active"] == "n") ? ' style="display:none;"' : '' ?>>
		<td><input type="radio" name="checkout[ship_number]"<?php echo ($v["flag"] == "y") ? ' checked="checked"' : '' ?> value="<?php hecho($v["ship_number"]) ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php hecho($v["ship_number"]) ?></label></td>
		<td style="white-space:nowrap;"><label for="<?php echo $key ?>"><?php hecho($v["name"]) ?></label></td>
		<td style="text-align:center;"><label for="<?php echo $key ?>"><?php hecho($v["del_time"]) ?></label></td>
		<td style="text-align:center;"><label for="<?php echo $key ?>"><?php echo ($v["track"] == "y") ? "Yes" : "No" ?></label></td>
		<td style="text-align:center;"><label for="<?php echo $key ?>"><?php echo ($v["insurance"] == "y") ? "Yes" : "No" ?></label></td>
		<td style="text-align:center;"><label for="<?php echo $key ?>"><?php echo ($v["tail_gate"] == "y") ? "Yes" : "No" ?></label></td>
		<td style="text-align:center;"><label for="<?php echo $key ?>"><?php echo $v["qty"] ?></label></td>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho(getCurrencySymbol($invoice["invoice_currency"]) . $v["cost_price"]) ?></b></label></td>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho(getCurrencySymbol($invoice["invoice_currency"]) . $v["sell_price"]) ?></b></label></td>
<?php
if (in_array($invoice["invoice_currency"], array("AUD", "USD")) || $invoice["site_id"] != 20)
{
	if ($invoice["invoice_currency"] != "AUD")
	{
?>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["cost_priceau"]) ?></b></label></td>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["sell_priceau"]) ?></b></label></td>
<?php
	}
}
elseif ($invoice["invoice_currency"] != "USD")
{
?>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["cost_priceus"]) ?></b></label></td>
		<td style="text-align:right;"><label for="<?php echo $key ?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["sell_priceus"]) ?></b></label></td>
<?php
}
?>
	</tr>
<?php
}
?>
</table>
<br />
<br />

<div style="text-align:center;">
	<input type="button" name="courier_search_accept" value="Add Selection" style="display:none;" />
</div>
<?php
}
include_once __DIR__ . '/footer.inc.php';
?>