<!-- forwarders_quotes_list.tpl.php -->
<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$temp_url=$get_vars;
$temp_url['null']='void';
unset($temp_url['id']);
unset($temp_url['action']);
$view_url='index.php?action=view_quote&'.http_build_query($temp_url).'&quote_id=';
$select_url='index.php?action=select_quote&'.http_build_query($temp_url).'&id=';
unset($temp_url['order_by']);
$temp_url['order']=($order=='desc')?'asc':'desc';
$sort_url='index.php?action=search&'.http_build_query($temp_url).'&order_by=';

$country_list=getCountryList();
?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?php echo $sort_url ?>id">ID</a><?php echo ($order_by=='id')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>date">Date</a><?php echo ($order_by=='date')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>country_from">From</a><?php echo ($order_by=='country_from')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>country_to">To</a><?php echo ($order_by=='country_to')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>shipping_weight">Weight (kgs)</a><?php echo ($order_by=='shipping_weight')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>confirmed_flag">Type</a><?php echo ($order_by=='confirmed_flag')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>container_flags">Service</a><?php echo ($order_by=='container_flags')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>container_flags">Container Type</a><?php echo ($order_by=='container_flags')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>transit_time">Transit Time</a><?php echo ($order_by=='transit_time')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>sale_price">Price (<?php echo $currency ?>)</a><?php echo ($order_by=='sale_price')?$ord_img:'' ?></th>
		<th>&nbsp;</th>
	</tr>
<?php
foreach($quote_list as $quote_id)
{
	$quote=forwardersQuoteFromDatabase($quote_id);
	$class='';
	if($alt=!$alt)		$class=' class="alt"';
?>
	<tr<?php echo $class ?>>
		<td style="text-align:center;">ID#<?php echo $quote_id ?></td>
		<td style="text-align:center;"><?php echo date('d/m/y',$quote['time']) ?></td>
		<td style="text-align:center;"><?php hecho($country_list[$quote['country_from']]) ?></td>
		<td style="text-align:center;"><?php hecho($country_list[$quote['country_to']]) ?></td>
		<td style="text-align:center;"><?php echo ($quote['shipping_weight'])?$quote['shipping_weight']:'N/A' ?></td>
		<td style="text-align:center;"><?php echo ($quote['confirmed_flag']=='y')?'Invoice':'Quote' ?></td>
		<td style="text-align:center;"><?php echo (in_array($quote['container_flags'],array('direct', 'indirect')))?ucfirst($quote['container_flags']):'N/A' ?></td>
		<td style="text-align:center;"><?php echo (in_array($quote['container_flags'],array('lcl', '20ft', '40ft', '40hq')))?strtoupper($quote['container_flags']):'N/A' ?></td>
		<td style="text-align:center;"><?php echo ($quote['transit_time']>0)?formatSecondsToText($quote['transit_time']):'N/A' ?></td>
		<td style="text-align:center;"><?php echo convertCurrency($quote['sale_price'],$quote['quote_currency'],$currency) ?></td>
		<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
			<a href="<?php echo $select_url.$quote['id'] ?>">Select</a>
<?php
	}
	else
	{
?>
			<a href="<?php echo $view_url.$quote_id ?>" target="_blank">View</a>
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
<!-- //forwarders_quotes_list.tpl.php -->
