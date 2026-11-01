<?php
include_once __DIR__ . '/common.inc.php';

$invoice = invoiceFromPost($posted);

$site_id      = $invoice['site_id'];
$country      = $invoice['ship_country'];
$code         = $invoice['ship_postcode'];
$state        = $invoice['ship_state'];
$destination  = $invoice['ship_city'];
$weight       = $invoice['ship_weight'];
$goods_value  = (in_array($site_id, array(3, 20, 26))) ? $invoice['products_offerus'] : $invoice["products_offerau"];
$currency     = (in_array($site_id, array(3, 20, 26))) ? 'HKD' : 'AUD';
$state        = str_replace('.', '', $state);
$courier_list = array();
$domestic     = ((in_array($site_id, array(3, 20, 26)) && 'HK' == $country) || (!in_array($site_id, array(3, 20, 26)) && 'AU' == $country));
if ($domestic) {
    $code = substr('0000' . intval($code), -4);
    if (0 == $code) {
        $code = '----';
    }

    $search_order = array(
        strtoupper($destination) . ' , ' . $code . ' , ' . $state,
        strtoupper($destination) . ' , , ' . $state,
        ' , ' . $code . ' , ' . $state,
        strtoupper($destination) . ' , ' . $code . ' , ',
        ' , ' . $code . ' , ',
        strtoupper($destination) . ' , , ',
    );
    foreach ($search_order as $search_for) {
        if (count($results = postcodeSearch($search_for))) {
            break;
        }
    }

    if (count($results)) {
        $result      = reset($results);
        $destination = $result['Locality'];
        $state       = $result['State'];
        $code        = $result['Pcode'];
    }

    $courier_list = freightDomesticCalculate($destination, $state, $code, $weight);
    $courier_list = freightCalculatePrices($courier_list, $weight, $goods_value);
    foreach ($courier_list as $key => $value) {
        if ('' == $value['currency']) {
            $courier_list[$key]['currency'] = ((in_array($site_id, array(20, 26))) ? 'HKD' : 'AUD');
        }

        $courier_list[$key]['Locality']      = $destination;
        $courier_list[$key]['State']         = $state;
        $courier_list[$key]['Pcode']         = $code;
        $courier_list[$key]['Country']       = ((in_array($site_id, array(20, 26))) ? 'HK' : 'AU');
        $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
    }
}
if (count($courier_list) == 0) {
    $courier_list = freightInternationalCalculate($country, 'hk');
    $courier_list = freightCalculateIntPrices($courier_list, $weight, $currency, $goods_value, 'hk');
    foreach ($courier_list as $key => $value) {
        if ('' == $value['currency']) {
            $courier_list[$key]['currency'] = $currency;
        }

        $courier_list[$key]['Country']       = $country;
        $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
    }
}
$add_list = array();
foreach ($courier_list as $key => $value) {
    if ('y' == $value['insurance'] && $value['insurance_cost'] > 0) {
        $value['sell_price'] += $value['insurance_cost'];
        $value['cost_price'] += $value['insurance_cost'];
        $value['retail_price'] += $value['insurance_cost'];
        $value['insurance'] = 'y';
        $add_list[]         = $value;
    }
    if (0.0 != $value['insurance_cost']) {
        $courier_list[$key]['insurance'] = 'n';
        if ($value['insurance_cost'] < 1.0) {
            unset($courier_list[$key]);
        }

    }
}
foreach ($add_list as $value) {
    $courier_list[] = $value;
}

$add_list = array();
foreach ($courier_list as $key => $value) {
    if (0.0 != $value['tail_gate_charge'] && $weight > 25) {
        $value['sell_price'] += $value['tail_gate_charge'];
        $value['cost_price'] += $value['tail_gate_charge'];
        $value['retail_price'] += $value['tail_gate_charge'];
        $value['tail_gate'] = 'y';
        $value['notes']     = trim($value['notes'] . "\n" . 'Tail Gate Included');
        $add_list[]         = $value;
    }
    if (0.0 != $value['tail_gate_charge']) {
        $courier_list[$key]['tail_gate'] = 'n';
        if ($value['tail_gate_charge'] < 1.0) {
            unset($courier_list[$key]);
        }

    }
}

/**
 * Drop the parcel airmail services when small packet available
 */
if (in_array($site_id, array(3, 20, 26))) {
    $temp = array();
    foreach (array_reverse($courier_list, true) as $k => $v) {
        if (in_array($v["product_id"], array(18, 17, 12, 11, 10, 9))) {
            $key = $v["track"] . $v["registered"] . $v["insurance"];
            if (in_array($key, $temp)) {
                unset($courier_list[$k]);
            } else {
                $temp[] = $key;
            }

        }
    }

}

foreach ($add_list as $value) {
    $courier_list[] = $value;
}

$courier_list = freightSortArray($courier_list);
$pick_up      = array(array('id' => -1, 'name' => 'Pick-Up', 'del_time' => 'N/A', 'supplier' => '0', 'supplier_name' => 'Pick-Up', 'track' => 'n', 'track_website' => '', 'tail_gate' => 'n', 'cost_price' => 0, 'insurance' => 'n', 'insurance_value' => 0.00, 'insurance_percent' => 0, 'sell_price' => 0, 'retail_price' => 0));

$courier_list = freightSearchCouriers($country, $code, $state, $destination, $weight, $goods_value, $currency);
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($courier_list)?>;
if (window.opener != null && window.opener.setCourierData != null) window.opener.setCourierData(data);
else if (parent.setCourierData != null) parent.setCourierData(data);
else alert('Unable to send couriers back to form');
//]]>
		</script>
	</head>
	<body></body>
</html>
