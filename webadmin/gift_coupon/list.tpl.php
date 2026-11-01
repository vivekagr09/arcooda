<?php
$sort_url=$get_vars;
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url='?'.http_build_query($sort_url);

$select_url=$get_vars;
$select_url['action']='select';
unset($select_url['id']);
$select_url='index.php?'.http_build_query($select_url);

$edit_url=$get_vars;
$edit_url['action']='preset_edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url);

$unlock_url=$get_vars;
$unlock_url['action']='unlock';
$unlock_url='index.php?'.http_build_query($unlock_url);
$unlock_url.='&id=';

$staff_id=$_GLOBALS['staff']['staff_id'];

function formatPercent($str_percent){
	$t = (float)$str_percent;
	if ( $t > 0)
		echo ((float)$str_percent *100).'%';
	else
		echo "-";
}

function printSiteID($siteID, $siteList){
	$name = "";
	if ($siteID)
		$name = $siteList[$siteID];
	else
		$name = "All Sites";
	echo '<td>'.$name.'</td>';
}
?>
<table class="wide" style="width:95%" align="center">
	<tr>
		<th><a href="<?php echo $sort_url ?>&order_by=id&order=<?php echo  ($order_by=='id' && $order=='asc')?'desc':'asc' ?>">Company</a></th>
		<th><a>Title</a></th>
		<th><a>Coupon Code</a></th>
		<th><a>% Discount</a></th>
		<th><a>% Bonus</a></th>
		<th><a>Expiry Date</a></th>
		<th><a>Status</a></th>
		<th style="width:36pt;">&nbsp;</th>
	</tr>
<?php
$active_list = giftcouponGetOptionList("active");
$site_list = giftcouponGetOptionList("site_id");

$alt=true;
foreach($item_list as $id)
{
	$item=giftcouponFromDatabase($id);


	$giftcoupon_lock=giftcouponGetLock($id);

	// echo '<pre>';
	// 	print_r($item);
	// echo '</pre>';
	

	$activeColor = ($item['active'] == 'y' && $item["expired"] != "y") ? "background: #90EEA0; border-bottom: 1px solid #ffffff;" : "";

	$class='';
	if($alt=!$alt)		$class='alt';
	if($giftcoupon_lock!==false)	$class='locked';
	if($giftcoupon_lock['staff_id']==$staff_id)		$class='mylock';
	$links=array();
	if($popup_window) $links[]= '<a href="'.$select_url.'&id='.$id.'">Select</a>';
	$links[]='<a href="'.$edit_url.'&preset_id='.$id.'" target="_blank">Edit</a>';
	if($product_lock!==false && $giftcoupon_lock['staff_id']==$staff_id)
		$links[]= '<a href="'.$unlock_url.$id.'">Unlock</a>';

?>
<tr style="text-align:center; <?= $activeColor ?>" class="<?php echo $class?>">
	<?php printSiteID($item['site_id'],$site_list) ;?>
	<td><?php echo $item['title'] ?></td>
	<td><?php echo $item['name'] ?></td>
	<td><?php formatPercent($item['value'])?></td>
	<td><?php formatPercent($item['bonus'])?></td>
	<td><?php echo ($item['expiry_time'])?date('d/m/y',$item['expiry_time']):'' ?></td>
	<td>
<?php
	//if ($item["expired"] == 'y') echo "EXPIRED";
	 echo strtoupper($active_list[$item['active']]);
?>
	</td>
	<td><?php echo implode(' | ',$links) ?></td>
</tr>
<?php 
}
?>
</table><br /><br />