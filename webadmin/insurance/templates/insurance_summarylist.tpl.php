<!-- insurance_summarylist.tpl.php -->

<?php
$package_total=array();
foreach($package_list as $group_total)
	foreach($group_total as $key=>$value)
		$package_total[$key]+=$value;

$show_hours=true;
$group_title='Staff Name';
if($grouped_by=='weekly' || $grouped_by=='monthly' || $grouped_by=='quarterly' || $grouped_by=='yearly')
{
	$group_title='Date Range';
	$show_hours=false;
}
if($force_hours) $show_hours=true;
?>
<table class="spacetable">
	<tr class="alt">
		<td></td>
<?php
if($show_hours)
{
?>
		<td></td>
<?php
}
?>
		<td style="text-align:center;"><?php echo intval($package_total['invoices_qty']) ?></td>
		<td style="text-align:center;"><?php echo intval($package_total['uptake_qty']) ?></td>
		<td style="text-align:center;"><?php echo intval($package_total['claims_qty']) ?></td>
		<td style="text-align:right;"><?php echo ($package_total['invoices_qty']>0)?sprintf('%.2f',$package_total['uptake_qty']*100.0/$package_total['invoices_qty']):'-' ?>%</td>
		<td style="text-align:right;">$<?php echo convertCurrency($package_total['uptake_amount'],'AUD') ?></td>
		<td style="text-align:right;">$<?php echo convertCurrency($package_total['contract_premium'],'AUD') ?></td>
		<td style="text-align:right;">$<?php echo convertCurrency($package_total['uptake_amount']-$package_total['contract_premium'],'AUD') ?></td>
	</tr>
	<tr>
		<th><?php echo $group_title ?></th>
<?php
if($show_hours)
{
?>
		<th>Hours Worked</th>
<?php
}
?>
		<th>Qty Invoices</th>
		<th>Qty with Insurance</th>
		<th>Qty of Claims</th>
		<th>% Uptake</th>
		<th>Insurance Received</th>
		<th>Insurance Cost</th>
		<th>Profit / Loss</th>
	</tr>
<?php
foreach($package_list as $group_id=>$group_total)
{
	$group_name=date('d/m/y',$group_total['from_time']).' - '.date('d/m/y',$group_total['to_time']-1);
	if($group_url!='') $group_name='<a href="'.$group_url.$group_id.'" target="_blank">'.$group_name.'</a>';

	$group_class='';
	if($alt=!$alt) $group_class=' class="alt"';
	if($group_total['unlocked']>0) $group_class=' class="highlight"';
?>
	<tr<?php echo $group_class ?>>
		<td style="text-align:center;white-space:nowrap;"><?php echo $group_name ?></td>
<?php
if($show_hours)
{
?>
		<td style="text-align:right;"><?php echo formatSecondsToTime($group_total['net_time'],2) ?></td>
<?php
}
?>
		<td style="text-align:center;"><?php echo intval($group_total['invoices_qty']) ?></td>
		<td style="text-align:center;"><?php echo intval($group_total['uptake_qty']) ?></td>
		<td style="text-align:center;"><?php echo intval($group_total['claims_qty']) ?></td>
		<td style="text-align:right;"><?php echo ($group_total['invoices_qty']>0)?sprintf('%.2f',$group_total['uptake_qty']*100.0/$group_total['invoices_qty']):'0.00' ?>%</td>
		<td style="text-align:right;">$<?php echo convertCurrency($group_total['uptake_amount'],'AUD') ?></td>
		<td style="text-align:right;">$<?php echo convertCurrency($group_total['contract_premium'],'AUD') ?></td>
		<td style="text-align:right;">$<?php echo convertCurrency($group_total['uptake_amount']-$group_total['contract_premium'],'AUD') ?></td>
	</tr>
<?php
}
?>
</table>

<!-- /insurance_summarylist.tpl.php -->
