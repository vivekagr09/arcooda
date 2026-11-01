<?php
include_once __DIR__ . '/common.inc.php';

//print_array($posted);
$currency = strtoupper($posted['currency']);

$pricing = productPricingFromDatabase($posted['product_id']);

if ('AUD' == $currency) {
    $cost_price = $cost_price_au = $pricing['cost_price_au'];
    if (0 == $cost_price) {
        $cost_price = $cost_price_au = convertCurrency($pricing['cost_price'], 'USD', 'AUD');
    }
} elseif ('USD' == $currency) {
    $cost_price = $pricing['cost_price'];
    if (0 == $cost_price) {
        $cost_price = convertCurrency($pricing['cost_price_au'], 'AUD', 'USD');
    }

    $cost_price_au = $pricing['cost_price_au'];
    if (0 == $cost_price_au) {
        $cost_price_au = convertCurrency($pricing['cost_price'], 'USD', 'AUD');
    }
} else {
    $use  = ('usd' == $pricing['selected']) ? $pricing['cost_price'] : $pricing['cost_price_au'];
    $use2 = ('usd' == $pricing['selected']) ? $pricing['cost_price_au'] : $pricing['cost_price'];

    $useC  = ('usd' == $pricing['selected']) ? 'USD' : 'AUD';
    $useC2 = ('usd' == $pricing['selected']) ? 'AUD' : 'USD';

    if (0 == $use) {
        $use  = $use2;
        $useC = $useC2;
    }
    $cost_price    = convertCurrency($use, $useC, $currency);
    $cost_price_au = $pricing['cost_price_au'];
    if (0 == $cost_price_au) {
        $cost_price_au = convertCurrency($pricing['cost_price'], 'USD', 'AUD');
    }
}

echo $cost_price . ', ' . $cost_price_au;
$new_data['product_cost']    = $cost_price;
$new_data['product_cost_au'] = $cost_price_au;
$new_data['return_message']  = $posted['return_message'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
	</head>
	<body>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($new_data)?>;
if (window.opener != null && window.opener.setCostPrice != null) {
    window.opener.setCostPrice(data);
} else if (window.parent != null && window.parent.setCostPrice != null) {
    window.parent.setCostPrice(data);
} else {
    alert('Unable to set the cost prices...');
}
window.close();
//]]>
		</script>
	</body>
</html>
