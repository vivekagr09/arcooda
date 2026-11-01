<!-- statistics.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceSumValue

$staff_list   = $section_handler->getOptionList('staff_id');
$invoice_list = invoiceSearch(array('newsletter_id' => $id));
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionStatistics\');">Statistics</a>')?>

<div id="sectionStatistics">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Date :</td>
			<td style="width:25%;"><?=date('d/m/Y', $item['time_added'])?></td>
			<td class="left_title" style="width:25%;">Total Visitors :</td>
			<td><?=intval($item_counts['total_visitors1'])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Campaign / Newsletter Name :</td>
			<td style="width:25%;"><?php hecho($item['subject'])?></td>
			<td class="left_title" style="width:25%;">Visitors Purchased :</td>
			<td><?=intval($item_counts['ordered'])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Sent By :</td>
			<td style="width:25%;"><?php hecho($staff_list[$item['staff_id']])?></td>
			<td class="left_title" style="width:25%;">Value of Purchases :</td>
			<td><a href="/webadmin/invoice/?action=list&amp;invoice_list=|<?=implode('|', $invoice_list)?>">$<?=convertCurrency(invoiceSumValue($invoice_list, 'invoice_offerus'), 'USD')?> USD</a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Total Emails Sent :</td>
			<td style="width:25%;"><?=intval($item_counts['total_sent'])?></td>
			<td class="left_title" style="width:25%;">% Sales Rate :</td>
			<td><?=sprintf('%.3f%%', (0 != $item_counts['total_sent']) ? (100 * $item_counts['ordered'] / $item_counts['total_sent']) : 0)?></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionStatistics_hideNote">Statistics can be viewed by Clicking "Statistics" above<br /></div>

<!-- /statistics.tpl -->
