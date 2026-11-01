<!-- summary_information.tpl -->

<?php
moduleLoad('invoice'); //invoiceSumValue, invoiceSearch

$year = date('Y', time());

$graph_data = array();
foreach ($section_handler->Search(array('style_id' => $item['style_id'], 'active' => 'y')) as $prev_id) {
    $prev = $section_handler->fromDatabase($prev_id);

    $counts = array();

    $mail_list = $section_handler->mailinglistFromDatabase($prev_id);

    foreach ($mail_list as $v) {
        $counts[$v['email_status']]++;
    }

    $counts['total_visitors1'] = intval($counts['clicked'] + $counts['ordered']);
    $counts['total_visitors2'] = intval($counts['clicked'] + $counts['ordered'] + $counts['unsubscribed']);
    $counts['total_views']     = intval($counts['viewed'] + $counts['clicked'] + $counts['ordered'] + $counts['unsubscribed']);
    $counts['total_sent']      = intval($counts['error'] + $counts['sent'] + $counts['bounced'] + $counts['viewed'] + $counts['clicked'] + $counts['ordered'] + $counts['unsubscribed']);
    $counts['cart_added']      = 0;
    if ($query = $DB->query(sprintf(
		"SELECT DISTINCT `member_id` FROM `highwaygroup`.`cart_tracking` WHERE `newsletter_id` = %d",
		$prev_id
	))) {
		while ($row = $DB->fetch_assoc($query)) {
			$counts['cart_added']++;
		}
		$DB->free_result($query);
	}

    if (date('Y', $prev['time_added']) > ($year - 4)) {
        $graph_data[] = array(
            date('d/m/Y', $prev['time_added']),
            intval(date('Y', $prev['time_added'])),
            $counts['total_sent'],
            $counts['total_views'],
            $counts['total_visitors2'],
            (0 != $counts['total_sent']) ? round(100.0 * $counts['total_visitors2'] / $counts['total_sent'], 3) : 0,
            $counts['cart_added'],
            intval($counts['ordered']),
            floatval(invoiceSumValue(invoiceSearch(array('newsletter_id' => $prev_id)), 'invoice_offerus')),
            (0 != $counts['total_sent']) ? round(100.0 * $counts['ordered'] / $counts['total_sent'], 3) : 0,
        );
    }

}

if (count($graph_data)) {
?>
<script type="text/javascript">
//<![CDATA[
$(function() {google.setOnLoadCallback(function() {
	var storage = <?php jecho($graph_data) ?>;
	var chart = new google.visualization.LineChart($('#sectionSummaryInformation_LineChart')[0]);

	$("#sectionSummaryInformation :checkbox[name='summary[]']").change(function() {
		var keys=[];
		$("#sectionSummaryInformation :checked[name='summary[]']").each(function() {
			keys.push(this.value);
		});

		var new_rows=[];
		$(storage).each(function() {
			var src_row=this;
			var match=false;
			var new_row=[];
			new_row.push(src_row[0]);
			for(var i=0;i<keys.length;i++) switch(keys[i])
			{
			case "count_sent":		new_row.push(src_row[2]);	break;
			case "count_viewed":	new_row.push(src_row[3]);	break;
			case "count_clicked":	new_row.push(src_row[4]);	break;
			case "click_rate":		new_row.push(src_row[5]);	break;
			case "added_to_cart":	new_row.push(src_row[6]);	break;
			case "count_purchased":	new_row.push(src_row[7]);	break;
			case "purchase_total":	new_row.push(src_row[8]);	break;
			case "sales_rate":		new_row.push(src_row[9]);	break;
			default:	if(keys[i].substr(0,5)=="year_" && keys[i].substr(5)==src_row[1]) match=true;
			}
			if(match) new_rows.push(new_row);
		});

		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
		for(var i=0;i<keys.length;i++) switch(keys[i])
		{
		case 'count_sent':		data.addColumn('number', 'Total Emails Sent');		break;
		case 'count_viewed':	data.addColumn('number', 'Total Emails Viewed');	break;
		case 'count_clicked':	data.addColumn('number', 'Total Visitors');			break;
		case 'click_rate':		data.addColumn('number', '% Click Rate');			break;
		case 'added_to_cart':	data.addColumn('number', 'Added To Cart');			break;
		case 'count_purchased':	data.addColumn('number', '# Visitors Purchased');	break;
		case 'purchase_total':	data.addColumn('number', 'Value of Purchases');		break;
		case 'sales_rate':		data.addColumn('number', '% Overall Sales Rate');	break;
		}
		data.addRows(new_rows);
		chart.draw(data, {width:720, height:300, hAxis:{title:''}, vAxis:{title:''}, title:''});

		return true;
	}).eq(0).change();
});});
//]]>
</script>
<?php
}

$summary = $posted['summary'];
if (!is_array($summary)) {
    $summary = explode('|', $summary);
}

foreach ($summary as $k => $v) {
    if ('' == $v) {
        unset($summary[$k]);
    }
}

?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSummaryInformation\');">Summary Information</a>')?>

<div id="sectionSummaryInformation">
	<table class="wide">
		<tr<?=($popup_window) ? ' style="display:none;"' : ''?>>
			<td style="width:33%;"><input name="summary[]" value="year_<?=$year?>"<?=(in_array('year_' . $year, $summary) || count($summary) == 0) ? ' checked="checked"' : ''?> type="checkbox" /><?=$year--?> Results</td>
			<td style="width:33%;"><input name="summary[]" value="count_sent"<?=(in_array('count_sent', $summary)) ? ' checked="checked"' : ''?> type="checkbox" />Total Emails Sent</td>
			<td style="width:33%;"><input name="summary[]" value="added_to_cart"<?=(in_array('added_to_cart', $summary)) ? ' checked="checked"' : ''?> type="checkbox" />Added To Cart</td>
		</tr>
		<tr<?=($popup_window) ? ' style="display:none;"' : ''?>>
			<td><input name="summary[]" value="year_<?=$year?>"<?=(in_array('year_' . $year, $summary)) ? ' checked="checked"' : ''?> type="checkbox" /><?=$year--?> Results</td>
			<td><input name="summary[]" value="count_viewed"<?=(in_array('count_viewed', $summary)) ? ' checked="checked"' : ''?> type="checkbox" />Total Emails Viewed</td>
			<td><input name="summary[]" value="count_purchased"<?=(in_array('count_purchased', $summary)) ? ' checked="checked"' : ''?> type="checkbox" /># Visitors Purchased</td>
		</tr>
		<tr<?=($popup_window) ? ' style="display:none;"' : ''?>>
			<td><input name="summary[]" value="year_<?=$year?>"<?=(in_array('year_' . $year, $summary)) ? ' checked="checked"' : ''?> type="checkbox" /><?=$year--?> Results</td>
			<td><input name="summary[]" value="count_clicked"<?=(in_array('count_clicked', $summary) || count($summary) == 0) ? ' checked="checked"' : ''?> type="checkbox" />Total Visitors</td>
			<td><input name="summary[]" value="purchase_total"<?=(in_array('purchase_total', $summary) || count($summary) == 0) ? ' checked="checked"' : ''?> type="checkbox" />Value of Purchases</td>
		</tr>
		<tr<?=($popup_window) ? ' style="display:none;"' : ''?>>
			<td><input name="summary[]" value="year_<?=$year?>"<?=(in_array('year_' . $year, $summary)) ? ' checked="checked"' : ''?> type="checkbox" /><?=$year--?> Results</td>
			<td><input name="summary[]" value="click_rate"<?=(in_array('click_rate', $summary)) ? ' checked="checked"' : ''?> type="checkbox" />% Click Rate</td>
			<td><input name="summary[]" value="sales_rate"<?=(in_array('sales_rate', $summary)) ? ' checked="checked"' : ''?> type="checkbox" />% Overall Sales Rate</td>
		</tr>
<?php
if (count($graph_data)) {
?>
		<tr><td colspan="3" style="text-align:center;"><div id="sectionSummaryInformation_LineChart"></div></td></tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionSummaryInformation_hideNote">SummaryInformation can be viewed by Clicking "SummaryInformation" above<br /></div>

<!-- /summary_information.tpl -->
