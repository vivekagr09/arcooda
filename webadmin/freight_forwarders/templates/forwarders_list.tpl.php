<!-- forwarders_list.tpl.php -->
<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$temp_url=$get_vars;
$temp_url['null']='void';
unset($temp_url['id']);
unset($temp_url['action']);
$edit_url='index.php?action=edit&'.http_build_query($temp_url).'&id=';
$view_url='index.php?action=view&'.http_build_query($temp_url).'&id=';
$delete_url='index.php?action=delete&'.http_build_query($temp_url).'&id=';
$unlock_url='index.php?action=unlock&'.http_build_query($temp_url).'&id=';
$select_url='index.php?action=select&'.http_build_query($temp_url).'&id=';
unset($temp_url['order_by']);
$temp_url['order']=($order=='desc')?'asc':'desc';
$sort_url='index.php?action=list&'.http_build_query($temp_url).'&order_by=';

$country_list=getCountryList();
?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?php echo $sort_url ?>name">Name of Forwarder</a><?php echo ($order_by=='name')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>country">Country</a><?php echo ($order_by=='country')?$ord_img:'' ?></th>
		<th>Last Contact</th>
		<th>Quotes</th>
		<th>Bookings</th>
		<th>&nbsp;</th>
	</tr>

<?php
foreach($forwarder_list as $forwarder_id)
{
	$forwarder=forwardersFromDatabase($forwarder_id);
	$forwarder_lock=forwardersGetLock($forwarder_id);
	$forwarder_quotes=forwardersQuotesSearch(array('product_id'=>$forwarder_id));
	$forwarder_bookings=forwardersQuotesSearch(array('product_id'=>$forwarder_id,'confirmed_flag'=>'y'));
	$last_contact=forwardersQuotesGetValue(end($forwarder_quotes),'time');

	$class='';
	if($alt=!$alt)		$class=' class="alt"';
	if($forwarder_lock!==false)	$class=' class="locked"';
	if($forwarder_lock['staff_id']==$staff_id)		$class=' class="mylock"';
?>
	<tr<?php echo $class ?>>
		<td style="text-align:center;"><?php hecho($forwarder['name']) ?></td>
		<td style="text-align:center;"><?php hecho($country_list[$forwarder['country']]) ?></td>
		<td style="text-align:center;"><?php echo ($last_contact!=0)?date('d/m/Y',$last_contact):'None' ?></td>
		<td style="text-align:center;"><?php echo count($forwarder_quotes) ?></td>
		<td style="text-align:center;"><?php echo count($forwarder_bookings) ?></td>
		<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
			<a href="<?php echo $select_url.$forwarder['id'] ?>">Select</a>
<?php
	}
	if($forwarder_lock===false || $forwarder_lock['staff_id']==$staff_id)
	{
?>
			<a href="<?php echo $edit_url.$forwarder['id'] ?>" target="_blank">Edit</a>
			<a href="<?php echo $delete_url.$forwarder['id'] ?>">Del</a>
<?php
	}
	else
	{
?>
			<a href="<?php echo $view_url.$forwarder['id'] ?>" target="_blank">View</a>
<?php
	}
	if($forwarder_lock!==false && $forwarder_lock['staff_id']==$staff_id)
	{
?>
			<a href="<?php echo $unlock_url.$forwarder['id'] ?>">Unlock</a>
<?php
	}
?>
		</td>
	</tr>
<?php
}
?>
</table>

<?php
if(!$popup_window && false)
{
?>
<br />
<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="color:red;">red</span> are locked by another user<br />
Rows marked in <span style="color:blue;">blue</span> are locked by you<br />
<?php
}
?>
<!-- //forwarders_list.tpl.php -->
