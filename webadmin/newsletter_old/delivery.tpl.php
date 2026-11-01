<!-- delivery.tpl -->

<?php
$view_list_url = $get_vars;
unset($view_list_url['email_status']);
$view_list_url['id']     = $id;
$view_list_url['action'] = 'view_list';
$view_list_url           = 'index.php?' . http_build_query($view_list_url) . '&email_status=';
?>

<script type="text/javascript">
//<![CDATA[
google.setOnLoadCallback(function() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Status');
	data.addColumn('number', 'Qty');
	data.addRows(<?php jecho(array(
    array('Errors', intval($item_counts['error'])),
    array('Bounces', intval($item_counts['bounced'])),
    array('Unopened', intval($item_counts['sent'])),
    array('Opens', intval($item_counts['viewed'])),
    array('Remaining', intval($item_counts['listed'] + $item_counts['sending'])),
    array('Click Rate', intval($item_counts['clicked'] + $item_counts['ordered'] + $item_counts['unsubscribed'])),
)) ?>);
	var chart = new google.visualization.PieChart($('#sectionDelivery_PieChart')[0]);
	chart.draw(data, {width:336, height:280, is3D:true, title:<?php jecho(number_format(intval($item_counts['error'] + $item_counts['sent'] + $item_counts['bounced'] + $item_counts['viewed'] + $item_counts['clicked'] + $item_counts['ordered'] + $item_counts['unsubscribed'])) . ' Emails Sent') ?>});
});
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionDelivery\');">Delivery</a>')?>

<div id="sectionDelivery">
	<table class="wide">
		<tr>
			<td style="width:50%;text-align:center;" rowspan="11"><div id="sectionDelivery_PieChart"></div></td>
			<td class="left_title"><br /><br /><br /><br /><br /></td>
			<td></td>
		</tr>
		<tr>
			<td style="width:25%;" class="left_title">Total Emails Sent :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'error|sent|bounced|viewed|clicked|ordered|unsubscribed')?>"><?=intval($item_counts['total_sent'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Emails Viewed :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'viewed|clicked|ordered|unsubscribed')?>"><?=intval($item_counts['total_views'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Email Errors :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'error')?>"><?=intval($item_counts['error'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Emails Bounced :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'bounced')?>"><?=intval($item_counts['bounced'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Emails Unopened :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'sent')?>"><?=intval($item_counts['sent'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Unsubscribed :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'unsubscribed')?>"><?=intval($item_counts['unsubscribed'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">Total Emails Clicked :</td>
			<td><a target="_top" href="<?php hecho($view_list_url . 'clicked|ordered|unsubscribed')?>"><?=intval($item_counts['total_visitors2'])?></a></td>
		</tr>
		<tr>
			<td class="left_title">% Click Rate :</td>
			<td><?=sprintf('%.0f%%', (0 != $item_counts['total_sent']) ? (100.0 * $item_counts['total_visitors2'] / $item_counts['total_sent']) : 0)?></td>
		</tr>
		<tr>
			<td class="left_title">Overall % Sales Rate :</td>
			<td><?=sprintf('%.0f%%', (0 != $item_counts['total_sent']) ? (100.0 * $item_counts['ordered'] / $item_counts['total_sent']) : 0)?></td>
		</tr>
		<tr>
			<td class="left_title"><br /><br /><br /><br /><br /></td>
			<td></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionDelivery_hideNote">Delivery can be viewed by Clicking "Delivery" above<br /></div>

<!-- /delivery.tpl -->
