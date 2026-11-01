<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action       = $_REQUEST['action'];
$country_from = $_REQUEST['country_from'];
$country_to   = $_REQUEST['country_to'];
$width        = $_REQUEST['width'];
$depth        = $_REQUEST['depth'];
$height       = $_REQUEST['height'];
$mass         = $_REQUEST['mass'];
$currency     = $_REQUEST['currency'];
$goods_value  = $_REQUEST['value'];
$active_only  = (!array_key_exists('active', $_REQUEST) || 'y' === $_REQUEST['active']);

if ('' == $country_from) {
    $country_from = $_GLOBALS['site_settings']['supply_country'];
}
if ('' == $currency) {
    $currency = 'USD';
}

$country_from_list = $section_handler->getOptionList('country_from');
$country_to_list = $section_handler->getOptionList('country_to');

echo formatHTMLTitle('Freight Calculator')?>

<form accept-charset="utf-8" autocomplete="off" method="get" action="" onsubmit="return validateSearch()">
    <table class="table_fields">
        <tr>
            <th style="width:25%;">Source :</th>
            <td><select name="country_from" class="wide">
                <?=formatHTMLOptions($country_from_list, $country_from)?>
            </select></td>
        </tr>
        <tr>
            <th>Destination :</th>
            <td><select name="country_to" class="wide">
                <option value=""></option>
                <?=formatHTMLOptions($country_to_list, $country_to)?>
            </select></td>
        </tr>
        <tr>
            <th>Weight (kg) :</th>
            <td><input type="text" size="5" name="mass" value="<?php hecho($mass)?>" /></td>
        </tr>
        <tr>
            <th>Dimensions (cms) :</th>
            <td>
                <input type="text" size="5" name="width" value="<?php hecho($width)?>" /> <b>Width x</b>
                <input type="text" size="5" name="depth" value="<?php hecho($depth)?>" /> <b>Depth x</b>
                <input type="text" size="5" name="height" value="<?php hecho($height)?>" /> <b>Height</b>
            </td>
        </tr>
        <tr>
            <th>Value of Goods :</th>
            <td><input type="text" size="10" name="value" value="<?php hecho($goods_value)?>" /> <span id="cur_goods" name="cur_goods"><?php hecho($currency)?></span></td>
        </tr>
        <tr>
            <th>Currency :</th>
            <td><select name="currency" class="wide" onchange="getElement('cur_goods').innerHTML = this.value">
                <option value=""></option>
                <?=formatHTMLOptions($section_handler->getOptionList('currency'), $currency)?>
            </select></td>
        </tr>
    </table>
    <div style="text-align:center;"><input type="submit" value="Calculate" /></div>
</form>
<br />

<?php


if ('' != $country_to) {
    $hints = $section_handler->hintInternational($country_from, $country_to, $width, $depth, $height, $mass, $active_only);

    $list = $section_handler->calculateServices($hints, $goods_value, $currency);

    $list = $section_handler->expandServices($list);

    $dims = array();
    $cubic = bcmul(bcmul($width, $height, 3), $depth, 3);
    if (bccomp($mass, '0.000', 3) > 0) $dims[] = bcadd($mass, '0.000', 3) . ' kg';
    if (bccomp($cubic, '0.000', 3) > 0) $dims[] = $cubic . ' cm&sup3;';
?>
<center><h3>Results for sending <?= implode(' / ', $dims) ?> from <?php hecho($country_from_list[$country_from]) ?> to <?php hecho($country_to_list[$country_to]) ?></h3></center>
<?php

    $view_all = $_REQUEST;
    $view_all['active'] = 'y|n';
    $view_all = 'international.php?' . http_build_query($view_all);
    $extra = '<small><i><a name="view_link" style="color:blue" href="' . hecho($view_all, 1) . '">View All</a></i></small>';
?>
<?=formatHTMLTitle('Staff Summary', $extra)?>

<?php
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
<table class="table_grid">
	<tr>
        <th>Provider</th>
        <th>Service</th>
        <th>Buy Rate (<?=$currency?>)</th>
        <th>Retail Price (<?=$currency?>)</th>
        <th>Our Sell Price (<?=$currency?>)</th>
        <th>Profit (<?=$currency?>)</th>
    </tr>
<?php
        $spec_notes = false;
        $currency_symbol = getCurrencySymbol($currency);
        foreach ($list as $id => $array) {
            if (!isset($array['profit'])) {
                $array['profit'] = $array['offer_price'] - $array['cost_price'];
            }

            $urls        = array('pp' => 'express', 'truck' => 'truck', 'acc' => 'account');
            $edit_url    = ".?action=edit&id=" . $array['service_id'];
            $forward_url = "/webadmin/freight_forwarders_/edit_forwarder.php?action=edit&id=" . $array['forwarder_id'];
            if ($array['staff_display']) {
?>
	<tr <?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td><a href="<?=$forward_url?>" target="_blank" onclick="<?=(0 == $array['forwarder_id'] ? 'return false;' : '')?>"><?php hecho($array['forwarder']) ?></a></td>
		<td><a href="<?=$edit_url?>" target="_blank" style="color:#000;"><?php hecho($array['service']) ?></a></td>
		<td><?= $currency_symbol . $array['cost_price'] ?></td>
		<td><?= $currency_symbol . $array['online_price'] ?></td>
		<td><b><?= $currency_symbol . $array['offer_price'] ?></b></td>
		<td><?= $currency_symbol . $array['profit'] ?></td>
	</tr>
<?php
                if ('' != $array['break_up']) {
?>
	<tr name="break_up" id="break_up" style="display:none;background:#eee;">
		<td colspan="6"><?=nl2br($array['break_up'])?></td>
	</tr>
<?php
                }
            }
        }
?>
</table>
<br />

<?=formatHTMLTitle('Customer Summary')?>
<br />
<br />

<table class="wide" align="center" style="width:75%;border:1px solid #000;" cellspacing="0" cellpadding="2" border="1" bordercolor="black">
<?php
        $colspan = 5;
        if ($spec_notes) {
            $colspan = 6;
        }
?>
	<tr style="text-align:center;">
		<td style="font-size:9pt;font-weight:bold" colspan="<?=$colspan?>">
			&nbsp;<br/>Prices for sending <?= implode(' / ', $dims) ?> from <?php hecho($country_from_list[$country_from]) ?> to <?php hecho($country_to_list[$country_to]) ?><br/>&nbsp;
		</td>
	</tr>
	<tr>
        <th>Price (<?=$currency?>)</th>
        <th>Service</th>
        <th>Transit Time</th>
        <th>Tracking</th>
        <th>Insurance<?=(0 == $goods_value) ? ' Available' : ''?></th>
<?php
        if ($spec_notes) {
?>
		<th>Special Notes</th>
<?php
        }
?>
	</tr>
<?php
        $status = array('y' => 'Yes', 'n' => 'No');
        foreach ($list as $id => $array) {
            $ins = (strpos($array['insurance_cost'], '(')) ? $array['insurance_cost'] : (('y' == $array['insurance']) ? 'Included' : 'No');
?>
	<tr style="text-align:center;" class="alt">
        <td style="font-size:8pt;"><b><?= $currency_symbol . convertCurrency($array['offer_price'], $array['currency']) ?></b></td>
		<td style="font-size:8pt;"><?php hecho($array['service']) ?></td>
		<td style="font-size:8pt;"><?php hecho($array['delivery_time']) ?></td>
		<td style="font-size:8pt;"><?=$status[$array['track']]?></td>
		<td style="font-size: 8pt"><?=$ins?></td>
<?php
            if ($spec_notes) {
?>
		<td style="font-size: 8pt"><?=$array['notes']?></td>
<?php
            }
?>
	</tr>
<?php
        }
?>
	<tr class="alt">
		<td colspan="<?=$colspan?>" style="font-size: 9px;font-family:Verdana;text-align: left;padding:10px 0px 10px 25px;">
			<li><b>PRICING :</b> Above prices are in <?=getCurrencyName($currency)?></li>
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
	<center>No Services available for shipping <?= implode(' / ', $dims) ?> from <?php hecho($country_from_list[$country_from]) ?> to <?php hecho($country_to_list[$country_to]) ?></center>
<?php
}
}
?>