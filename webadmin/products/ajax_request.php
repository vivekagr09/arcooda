<?php
include_once 'common.inc.php';
$result = $posted;
switch ($posted['action'])
{
case 'productSearch':
	$criteria = $_GLOBALS['criteria_options']['products'];
	foreach($posted as $k => $v)
	{
		if ($k == 'ignore')
		{
			if (is_array($v)) foreach($v as $field) unset($criteria[$field]);
		}
		elseif ($k != 'action') $criteria[$k] = $v;
	}
	$result['result'] = productSearch($criteria);
	if ($posted['return_sort'] != '') $result['result'] = productSort($result['result']);
	if ($posted['return_desc'] != '') $result['result'] = array_reverse($result['result']);
	if ($posted['return_limit'] > 0) $result['result'] = reset(array_chunk($result['result'], $posted['return_limit']));
	break;

case 'productSort':
	$result['result'] = productSort($posted['product_list'], $posted["order_by"], false);
	break;

case 'productFromDatabase':
	$result['result'] = productFromDatabase($posted['id']);
	break;

case 'productDimensionFromDatabase':
	$result['result'] = productDimensionFromDatabase($posted['id'], 0, true);
	break;

case 'productPackingFromDatabase':
	$result['result'] = productPackingFromDatabase($posted['id']);
	break;

case 'productsFromDatabase':
	$result['result'] = productsFromDatabase($posted['id']);
	break;

case 'productGetValue':
	$result['result'] = productGetValue($posted['id'], $posted['field']);
	break;
}
header('Content-Type: text/javascript');
echo json_encode($result);
