<?php
include_once __DIR__ . '/wanted_common.php';

moduleLoad('invoice'); //invoiceFormatNumber
moduleLoad('member'); //memberGetValue, memberFromDatabase
moduleLoad('product');

include_once __DIR__ . '/templates/wanted_header.tpl.php';

?>
<style>
	.highlight-stock-stock_sample{
		background-color: yellow!important;
	}
</style>
<?php
$order = $posted['order'];
if ($order != 'asc') $order = 'desc';
$page = intval($posted['page']);
$order_by = (isset($posted['order_by'])) ? $posted['order_by'] : 'id';

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['wanted'];
unset($criteria['active']);
unset($criteria["active_on"]);
foreach(wantedGetOptionList('all_fields') as $k => $v) if (is_string($posted[$k])) $criteria[$k] = explode('|', $posted[$k]);
if ($posted['search_for'] != '') $criteria['any_text'] = explode('|', $posted['search_for']);
if ($posted['show'] != '') $criteria[$posted['show']] = explode('|', $posted['show_id']);

//Find and pagenate the wanted
$wanted_list = wantedSearch($criteria);
if (true)
{
?>
<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="<?=htmlspecialchars('toggleSection("sectionViewByItemTypes");return false')?>"><i class="fas fa-caret-right"></i> <b>List by Category</b></a>
    <br /><br />
</div>
<div id="sectionViewByItemTypes" class="child" style="display:none;">
<?php
$list_by1     = 'section_id';
$list_by2     = '';
$list_columns = 3;
include __DIR__ . '/_wanted_navigate.tpl.php';
?>
</div>
<?php
}
$wanted_list = wantedSort($wanted_list, $order_by);
if ($order == 'desc') $wanted_list = array_reverse($wanted_list);
$items_per_page = 50;
if ($_GLOBALS['staff']['wanted_per_page'] > 0) $items_per_page = $_GLOBALS['staff']['wanted_per_page'];
$count_total = count($wanted_list);
?>
<?php echo formatHTMLTitle('Wanted') ?>
<?php
$wanted_list = array_chunk($wanted_list, $items_per_page);
$count_pages = count($wanted_list);
$page = max(0, min($count_pages - 1, $page));
$wanted_list = $wanted_list[$page];
$count_display = count($wanted_list);
if ($count_total > 0)
{
	$page_url = $get_vars;
	unset($page_url['page']);
	$page_url = '?' . http_build_query($page_url);
	$page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
?>
<br />
<?php
	//Begin list_wanted.tpl.php
	$alt = false;
	$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';
	$sort_url = $get_vars;
	$sort_url['action'] = 'list';
	$sort_url['order'] = ($order == 'desc') ? 'asc' : 'desc';
	unset($sort_url['order_by']);
	$sort_url = 'index.php?' . http_build_query($sort_url) . '&order_by=';
	$edit_url = $get_vars;
	$edit_url['action'] = 'edit';
	unset($edit_url['wanted_id']);
	$edit_url = 'index.php?' . http_build_query($edit_url) . '&wanted_id=';
	$select_url = $get_vars;
	$select_url['action'] = 'select';
	unset($select_url['wanted_id']);
	$select_url = 'index.php?' . http_build_query($select_url) . '&wanted_id=';
	$status_url = $get_vars;
	unset($status_url['wanted_id']);
	$status_url = 'index.php?' . http_build_query($status_url) . '&wanted_id=';
	$active_list = wantedGetOptionList('active');
	$section_list=productGetOptionList('category_section');
?>
<table class="table_grid" style="width:95%;margin:auto;">
	<tr>
		<th><a href="<?php hecho($sort_url . 'date_added') ?>">Date</a><?php echo ($order_by == 'date_added') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'text') ?>">Product Name</a><?php echo ($order_by == 'text') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'section_id') ?>">category</a><?php echo ($order_by == 'section_id') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'target_price') ?>">Target price</a><?php echo ($order_by == 'target_price') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'qty_needed') ?>">Qty Needed</a><?php echo ($order_by == 'qty_needed') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'duration_days') ?>">Time Needed</a><?php echo ($order_by == 'duration_days') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'pref_location') ?>">Location</a><?php echo ($order_by == 'pref_location') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'customer_name') ?>">Customer</a><?php echo ($order_by == 'customer_name') ? $ord_img : '' ?></th>
		<th><a href="">Offer</a></th>
		<th><a href="<?php hecho($sort_url . 'approved') ?>">Approved</a><?php echo ($order_by == 'approved') ? $ord_img : '' ?></th>
		<th><a href="<?php hecho($sort_url . 'active') ?>">Status</a><?php echo ($order_by == 'active') ? $ord_img : '' ?></th>
		<th><a >Our Reference</a></th>
		<th></th>
	</tr>
<?php
	$countrylist=getCountryList();
	foreach($wanted_list as $wanted_id)
	{
		$wanted = wantedFromDatabase($wanted_id);
		$wanted_offers=wantedOferDromDataBase($wanted['id']);
		$class = '';
		$member=memberFromDatabase($wanted['member_id']);
		if ($alt = !$alt) $class = ' class="alt"';
		$class = 'highlight-modified';
		if($wanted['updated_at']!='0000-00-00 00:00:00'){
			$class = 'highlight-modified';
		}
		if($wanted['duration_days']!='unlimited'){
			if($wanted['duration_days']=='2 weeks'){
				$date = strtotime("+14 days",strtotime($wanted['date_added']));
				$date=formatDateTime($date);
				if($date<formatDateTime()){
					$class = 'highlight-stock-stock_sample';
				}
			}
			if($wanted['duration_days']=='4 weeks'){
				$date = strtotime("+28 days",strtotime($wanted['date_added']));
				$date=formatDateTime($date);
				if($date<formatDateTime()){
					$class = 'highlight-stock-stock_sample';
				}
			}	
		}
		if ('' != $class)
	    {
	        $class = ' class="' . $class.'"';
	    }

		$member_id   = intval($wanted['member_id']);
		$member_name = 'N/A';
		if ($member_id && function_exists('memberGetValue')) {
			$member_name = memberGetValue($member_id, 'full_name') ?? $member_id;
		}
?>
	<tr<?php echo $class ?>>
		<td><?php echo date('d/m/Y',strtotime($wanted['date_added']));?></td>
		<td style="text-align:left;white-space:wrap;"><?=($wanted['item_id'])?'<a href="/webadmin/products_new/edit.php?&id='.$wanted['item_id'].'" target="_blank"">':'' ?><?php hecho($wanted['item']) ?><?=($wanted['item_id'])?'</a>':'' ?></td>
		<td><?php echo $section_list[$wanted['section_id']];?></td>
		<td><?php echo $wanted['target_price'].' '.$wanted['target_currency'];?></td>
		<td><?php echo $wanted['qty_needed'];?></td>
		<td><?php echo $wanted['duration_days'];?></td>
		<td><?php echo explode('-', $countrylist[$wanted['pref_location']])[0];?></td>
		<td><a href="/webadmin/member/?action=edit&member_id=<?=$member_id?>" target="_blank"><?=htmlspecialchars($member_name)?></a></td>
		<td><?= count($wanted_offers)?></td>
		<td><?php echo $active_list[$wanted['approved']] ?></td>
		<td><?php echo $active_list[$wanted['active']] ?></td>
		<td><?= ($wanted['invoice_id'])?'<a href="/webadmin/invoice/?action=edit&amp;invoice_id='.$wanted['invoice_id'].'" target="_blank">'.invoiceFormatNumber($wanted['invoice_id']).'</a>':'';?></td>
<?php
		$links = array();
		if ($posted['return_message'] != '')
		{
			$links[] = '<a href="' . htmlspecialchars($select_url . $wanted_id) . '">Select</a>';
		}
		else
		{
			$links[] = '<a href="' . htmlspecialchars($edit_url . $wanted_id) . '" target="_blank">Edit</a>';
			/*$word = (($wanted['active'] == 'yes') ? "De-" : "") . "Activate";
			$anc = strtolower(str_replace('-', '', $word));
			$links[] = '<a href="' . htmlspecialchars($status_url . $wanted_id . '&action=' . $anc) . '">' . $word . '</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this wanted?\')" href="' . htmlspecialchars($status_url . $wanted_id . '&action=delete') . '">Delete</a>';
			$links[] = '<a href="index.php?action=clickReset&wanted_id=' . $wanted_id . '">Reset Total Clicks</a>';
			$links[] = '<a href="index.php?action=viewReset&wanted_id=' . $wanted_id . '">Reset Total Views</a>';*/
		}
?>
		<td style="white-space:nowrap;"><?php echo implode('<br />', $links) ?></td>
	</tr>
<?php
	}
?>
</table>
<div style="text-align:center;margin-top: 50px;">
        <span style="font-style:italic;">Notes:</span><br />
        Rows highlighted in <span class="highlight-modified">green</span> have been new request<br />
     
        Cells highlighted in <span class="highlight-stock-stock_sample">light orange</span> Near to expire.<br />
    </div>
    <br /><br />
<?php
	//End list_wanted.tpl.php

?>
<br />
<?php echo $page_links
?><br />
<br />
<?php
	if (!$popup_window)
	{
?>
<?php
	}
	else
	{
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
	}
}
else
{
?>
No results were found
<?php
}
include_once ('templates/wanted_footer.tpl.php');
?>