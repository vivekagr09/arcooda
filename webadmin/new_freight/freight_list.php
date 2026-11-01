<?php
include_once ('common.inc.php');
include_once ('header.tpl.php');

$order = $posted['order'];
if ($order != 'asc') $order = 'desc';
$page_id = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['freight'];
unset($criteria['active']);
foreach(freightGetOptionList('all_fields') as $key => $value) if ($posted[$key] != '') $criteria[$key] = explode('|', $posted[$key]);
elseif(array_key_exists($key, $posted)) $criteria[$key] = $posted[$key];
unset($criteria['id']);
if ($_GLOBALS['site_settings']['site_id'] != 11) $criteria['site_id'] = $_GLOBALS['site_settings']['site_id'];
if ($posted['search_for'] != '') $criteria['any_text'] = explode('|', $posted['search_for']);
if ($posted['subset_list'] != '') $criteria['id'] = explode('|', $posted['subset_list']);

/**
 * Navigation by service type and source location
 */
$country_list = freightGetOptionList('service_from');
$sub_criteria = $criteria;
unset($sub_criteria['service_type']);
unset($sub_criteria['service_from']);
$service_list = freightSearch($sub_criteria);
$temp = $get_vars;
unset($temp['service_type']);
unset($temp['service_from']);
$service_url = '?' . http_build_query($temp) . '&service_type=';
?>
<table>
	<tr>
		<th>Country</th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'pp') ?>">Prepaid</a></th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'truck') ?>">Truck</a></th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'acc') ?>">Account</a></th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'sea|sea_air') ?>">Sea Freight</a></th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'air|sea_air') ?>">Air Freight</a></th>
		<th><a href="<?php echo htmlspecialchars($service_url . 'personal') ?>">Personal</a></th>
	</tr>
<?php
$country_url = '?' . http_build_query($temp) . '&service_from=';
foreach(freightGroupCount($service_list, array('service_from', 'service_type')) as $country_id => $sub_list)
{
	$temp['service_from'] = $country_id;
	$service_url = '?' . http_build_query($temp) . '&service_type=';
	if ($sub_list['sea_air'] > 0)
	{
		$sub_list['sea'] += $sub_list['sea_air'];
		$sub_list['air'] += $sub_list['sea_air'];
	}
?>
	<tr>
		<th><a href="<?php echo htmlspecialchars($country_url . $country_id) ?>"><?php echo htmlspecialchars($country_list[$country_id]) ?></a></th>
<?php
	foreach(array('pp', 'truck', 'acc', 'sea', 'air', 'sea_air', 'personal') as $k)
	{
?>
		<td style="text-align:center;"><a href="<?php echo htmlspecialchars($service_url . $k) ?>"><?php echo $sub_list[$k] ?></a></td>
<?php
	}
?>
	</tr>
<?php
}
?>
</table>

<?php
//Find and pagenate the items
$item_list = freightSearch($criteria);
$item_list = freightSort($item_list, $order_by);
if ($order == 'desc') $item_list = array_reverse($item_list);

$items_per_page=50;
if ($_GLOBALS['staff']['per_page']>0) $items_per_page = $_GLOBALS['staff']['per_page'];
$count_total = count($item_list);

$item_list = array_chunk($item_list, $items_per_page);
$count_pages = count($item_list);
$page_id = max(0, min($count_pages - 1, $page_id));
$item_list = $item_list[$page_id];
$count_display = count($item_list);

if ($count_total > 0)
{
	$page_url = $get_vars;
	$page_url['action'] = 'list';
	unset($page_url['page_id']);
	$page_url = 'index.php?' . http_build_query($page_url) . '&page_id=';

	$page_links = formatHTMLPageLinks($page_url,$page_id,$count_display,$count_total,$items_per_page);

	$display_currency = 'USD';
?>
<br />
<form accept-charset="utf-8" action="<?= htmlspecialchars($select_url) ?>" method="post">
<?php
include 'list.tpl.php';
?>
</form>
<br />
<table align="center" cellpadding="7" width="95%"><tr><td bgcolor="#fafafa"><div align="center"><?= $page_links ?></div></td></tr></table>
<br />
<?php
}
else
{
?>
No results were found
<?php
}

if($popup_window)
{
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}

include_once ('footer.tpl.php');
?>