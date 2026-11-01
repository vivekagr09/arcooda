<!-- sales_statistics.tpl -->

<?php
$staff_list = $section_handler->getOptionList('staff_id');
$invoice_list = invoiceSearch(array('newsletter_id'=>$id));

$graph_data = array();
foreach($section_handler->Search(array('style_id' => $item['style_id'], 'active' => 'y')) as $prev_id)
{
	$counts = array();
	foreach($section_handler->mailinglistFromDatabase($prev_id) as $v) $counts[$v['email_status']]++;
	unset($counts['listed']);
	unset($counts['sending']);

	$graph_data[] = array(
		date('d/m/Y',$section_handler->getValue($prev_id,'time_added')),
		intval(array_sum($counts)),
		floatval(invoiceSumValue($invoice_list, 'invoice_offerus')),
	);
}

if(count($graph_data))
{
?>
<script type="text/javascript">
//<![CDATA[
google.setOnLoadCallback(function() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Date');
	data.addColumn('number', 'Total Visitors');
	data.addColumn('number', 'Total $USD Purchases');
	data.addRows(<?php echo formatJSON($graph_data) ?>);
	var chart = new google.visualization.LineChart($('#sectionSalesStatistics_LineChart')[0]);
	chart.draw(data, {width:336, height:280, hAxis:{title:'Date'}, vAxis:{title:'Quantity'}, title:'Last 8 Newsletter Results'});
});
//]]>
</script>
<?php
}
?>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSalesStatistics\');">Sales Results</a>') ?>

<div id="sectionSalesStatistics">
	<table class="wide">
<?php
	if(count($graph_data))
	{
?>
		<tr>
			<td rowspan="8" style="width:50%;text-align:center;"><div id="sectionSalesStatistics_LineChart"></div></td>
			<td class="left_title"><br /><br /><br /><br /><br /><br /></td>
			<td></td>
		</tr>
<?php
	}
?>
		<tr>
			<td class="left_title" style="width:25%;">Total Visitors :</td>
			<td><a href="index.php?action=view_list&id=<?php echo $id ?>&amp;email_status=clicked|ordered" target="_blank"><?php echo intval($item_counts['total_visitors1']) ?></a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Pages Viewed Per Visit :</td>
			<td class="unfinished"></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Added to Cart :</td>
			<td><a target="_blank" href="index.php?action=view_list&amp;id=<?php echo $id ?>&amp;member_id=<?php echo implode("|",$cart_added) ?>"><?php echo count($cart_added) ?></a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">No Visitors Purchased :</td>
			<td><a href="index.php?action=view_list&amp;id=<?php echo $id ?>&amp;email_status=ordered" target="_blank"><?php echo intval($item_counts['ordered']) ?></a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Total Value of Purchases :</td>
			<td><a href="/webadmin/invoice/?action=list&amp;invoice_list=|<?php echo implode('|',$invoice_list) ?>" target="_blank">$<?php echo convertCurrency(invoiceSumValue($invoice_list,'invoice_offerus'),'USD') ?> USD</a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">% Visitor Sales Rate :</td>
			<td><?php echo sprintf('%.3f%%',($item_counts['total_visitors2']!=0)?(100*$item_counts['ordered']/$item_counts['total_visitors2']):0) ?></td>
		</tr>
<?php
	if(count($graph_data))
	{
?>
		<tr>
			<td class="left_title"><br /><br /><br /><br /><br /><br /></td>
			<td></td>
		</tr>
<?php
	}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionSalesStatistics_hideNote">SalesStatistics can be viewed by Clicking "SalesStatistics" above<br /></div>

<!-- /sales_statistics.tpl -->
