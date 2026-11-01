<?php
include_once ('common.inc.php');
include_once ('header.tpl.php');

$service_from = $posted['service_from'];
$currency = $posted['goods_currency'];
$destination = $posted['destination'];
$weight = $posted['weight'];
$goods_value = $posted['goods_value'];
if ($currency == '') $currency = 'USD';

include 'international_calculator.tpl.php';

if ($destination != '' && $service_from != '')
{
	$product_list = freightInternationalProducts($destination, $service_from);
	$price_list = freightCalculatePrices($product_list, $weight, 0, 0, 0, $currency, $goods_value);
	$price_list = freightPricingSort($price_list);

	if (count($price_list) > 0)
	{

		include 'staff_summary.tpl.php';
		include 'customer_summary.tpl.php';
	}
	else
	{
		$option_list = array();
		$option_list['destination'] = freightGetOptionList('destination');
		$option_list['service_from'] = freightGetOptionList('service_from');
?>
<center>No Services available for shipping <?php echo htmlspecialchars($weight) ?> kgs from <?php echo htmlspecialchars($option_list['service_from'][$service_from]) ?> to <?php echo htmlspecialchars($option_list['destination'][$destination]) ?></center>
<?php
	}
}

include_once ('footer.tpl.php');
?>