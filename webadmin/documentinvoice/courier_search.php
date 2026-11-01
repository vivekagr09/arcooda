<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('freight');

$invoice = documentinvoiceFromPost($posted);

$country     = $invoice['ship_country'];
$code        = $invoice['ship_postcode'];
$state       = $invoice['ship_state'];
$destination = $invoice['ship_city'];
$weight      = $invoice['ship_weight'];
$goods_value = $invoice['products_offerau'];
$currency    = 'AUD';

$state = str_replace('.', '', $state);

$courier_list = array();

if ('AU' == $country && function_exists('freightDomesticCalculate') && function_exists('freightCalculatePrices')) {
    $code = substr('0000' . intval($code), -4);
    if (0 == $code) {
        $code = '----';
    }

    $search_order = array($destination . ' , ' . $code . ' , ' . $state, $destination . ' , , ' . $state, ' , ' . $code . ' , ' . $state, $destination . ' , ' . $code . ' , ', ' , ' . $code . ' , ', $destination . ' , , ');
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
            $courier_list[$key]['currency'] = 'AUD';
        }

        $courier_list[$key]['Locality']      = $destination;
        $courier_list[$key]['State']         = $state;
        $courier_list[$key]['Pcode']         = $code;
        $courier_list[$key]['Country']       = 'AU';
        $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
    }
}

/*
if($invoice['ship_country']=='AU')
{
$destination=$invoice['ship_city'];
$code=substr('0000'.intval($invoice['ship_postcode']),-4);
$state=$invoice['ship_state'];

//Now make sure they match something in database
if($code>0)
$search_for=$destination.' , '.$code.' , '.$state;
else
$search_for=$destination.' , , '.$state;

$results=postcodeSearch($search_for);
if(count($results)>0)
{
$result=reset($results);
$destination=$result['Locality'];
$state=$result['State'];
$code=$result['Pcode'];
}

$courier_list=freightDomesticCalculate($destination,$state,$code,$weight);
$courier_list=freightCalculatePrices($courier_list,$weight);
foreach($courier_list as $key=>$value)
{
$courier_list[$key]['Locality']=$destination;
$courier_list[$key]['State']=$state;
$courier_list[$key]['Pcode']=$code;
$courier_list[$key]['Country']='AU';
$courier_list[$key]['supplier_name']=getFreightForwarderName($value['supplier']);
}
}
 */

if (count($courier_list) == 0 && function_exists('freightInternationalCalculate') && function_exists('freightCalculateIntPrices')) {
    $courier_list = freightInternationalCalculate($country, $currency, $weight);
    $courier_list = freightCalculateIntPrices($courier_list, $weight, $currency, $goods_value);
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
    //print_array($value);
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
foreach ($add_list as $value) {
    $courier_list[] = $value;
}

if (function_exists('freightSortArray')) {
    $courier_list = freightSortArray($courier_list);
}

if ('AU' == $country) {
    $pick_up      = array(array('id' => 0, 'name' => 'Pick-Up', 'del_time' => 'N/A', 'supplier' => '0', 'supplier_name' => 'Pick-Up', 'track' => 'n', 'track_website' => '', 'tail_gate' => 'n', 'cost_price' => 0, 'insurance' => 'n', 'insurance_value' => 0.00, 'insurance_percent' => 0, 'sell_price' => 0, 'retail_price' => 0));
    $courier_list = $pick_up + $courier_list;
}
?>
<html><head></head><body>
<script type="text/javascript">
<?=formatJSAssoc('data', $courier_list)?>
if(window.opener!=null && window.opener.setCourierData!=null)
	window.opener.setCourierData(data);
else
	if(parent.setCourierData!=null)
		parent.setCourierData(data);
	else
		alert('Unable to send couriers back to form');
//window.close();
</script>
</body></html>