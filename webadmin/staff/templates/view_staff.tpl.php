<?php
/**
 * @var mixed[] $staff
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('staff');
moduleLoad('timesheet');

$staff_list = staffGetOptionList('id');

?>
<script type="text/javascript">
//<![CDATA[
function onchangeName()
{
	var fromEmail=getValue('email');
	var lastName=getValue('last_name');
	var firstName=getValue('first_name');
	if (fromEmail!='' || lastName=='' || firstName=='') return;

	setValue('email',(firstName.charAt(0)+lastName).toLowerCase()+'@highwaygroup.org');
}

function icqSearch()
{
	window.open('staff_icqsearch.php?first_name=' + escape(getValue('first_name')) + '&last_name=' + escape(getValue('last_name')) + '&email=' + escape(getValue('email')));
}
//]]>
</script>

<?php
include __DIR__ . '/staff_personal.tpl.php';
include __DIR__ . '/staff_taxinfo.tpl.php';
include __DIR__ . '/staff_bankinfo.tpl.php';
include __DIR__ . '/staff_superinfo.tpl.php';
include __DIR__ . '/staff_yearsummary.tpl.php';
include __DIR__ . '/staff_administration.tpl.php';
include __DIR__ . '/admin_history.tpl.php';
include __DIR__ . '/staff_history.tpl.php';
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
<?php
return;
?>

<br />

<?=formatHTMLTitle('Personal Details')?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Last Name</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['last_name'])?></td>
		<td class="left_title" style="width:25%;">Email - Work</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['email'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">First Name</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['first_name'])?></td>
		<td class="left_title" style="width:25%;">Email - Home</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['home_email'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Address</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['address'])?></td>
		<td class="left_title" style="width:25%;">ICQ# (<a href="javascript:void(0);" onclick="javascript:icqSearch();">Search</a>)</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['icq_no'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Post Code</td>
		<td style="width:25%;"><?=htmlspecialchars($staff['post_code'])?></td>
		<td class="left_title" style="width:25%;"></td>
		<td style="width:25%;"></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Country</td>
		<td style="width:25%;"><select class="unfinished"><?=formatHTMLOptions(getCountryList())?></select></td>
		<td class="left_title" style="width:25%;"></td>
		<td style="width:25%;"></td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Home Phone</td>
		<td style="height:1px;width:25%;"><?=htmlspecialchars($staff['home_phone'])?></td>
		<td colspan="2" rowspan="4" style="text-align:right;vertical-align:top;">
<?php
if ('' != $staff['photo'] && is_file($_SERVER['DOCUMENT_ROOT'] . '/images/staff/' . $staff['photo'])) {
?>
			<img name="img_photo" src="/images/staff/<?=$staff['photo']?>" />
<?php
}
?>
		</td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Mobile Phone</td>
		<td style="height:1px;width:25%;"><?=htmlspecialchars($staff['mobile'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Fax Number</td>
		<td style="height:1px;width:25%;"></td>
	</tr>
	<tr>
		<td class="left_title" style="height:auto;"></td>
		<td style="height:auto;"></td>
	</tr>
</table>

<br />

<?=formatHTMLTitle('Payment Details')?>

<table style="width:auto;">
	<tr>
		<td class="left_title">Pay Rate (Per Hour)</td>
		<td style="width:96px;"><?=htmlspecialchars($staff['pay_rate'])?></td>
	</tr>
<?php
$pay_history = staffPayRatesFromDatabase($staff_id);
if (count($pay_history) > 0) {
?>
	<tr>
		<td colspan="2" class="alt" style="text-align:center;">
<?php
foreach ($pay_history as $pay_rate) {
?>
			<?=date('d/m/Y', $pay_rate['change_time'])?> - Set to <?=$pay_rate['pay_rate']?> by <?=htmlspecialchars($staff_list[$pay_rate['change_staff']] ?? 'Unknown')?><br />
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
</table>

<br />
<br />

<?php
function quickHTMLOption($field, $name, $value, $style = 0)
{
    static $alt = false;
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td><?=$name?></td>
		<td style="text-align:center;">
<?php
switch ($style) {
        default:
            if ('Y' == $value) {
                echo 'Yes';
            }

            if ('N' == $value) {
                echo 'No';
            }

            break;
        case 1:
            echo $value;
            break;
        case 2:
            echo htmlspecialchars($value);
            break;
    }
?>
		</td>
	</tr>
<?php
}
?>

<table class="spacetable" style="width:50%;">
<?php
quickHTMLOption('TFNABNProvided', 'TFN ABN Provided', $staff['TFNABNProvided']);
quickHTMLOption('TFNABNExempt', 'TFN ABN Exempt', $staff['TFNABNExempt']);
quickHTMLOption('AustralianResident', 'Australian Resident', $staff['AustralianResident']);
quickHTMLOption('TaxFreeThresholdClaimed', 'Tax Free Threshold Claimed', $staff['TaxFreeThresholdClaimed']);
quickHTMLOption('RebateAndFTAAmountClaimed', 'Rebate And FTA Amount Claimed', $staff['RebateAndFTAAmountClaimed'], 2);
quickHTMLOption('MedicareVariation', 'Medicare Variation', $staff['MedicareVariation'], 1);
quickHTMLOption('MedicareReducedRate', 'Medicare Reduced Rate', $staff['MedicareReducedRate']);
quickHTMLOption('HasSpouse', 'Has Spouse', $staff['HasSpouse']);
quickHTMLOption('CombinedIncLessAmtRR', 'Combined Income Less Amt RR', $staff['CombinedIncLessAmtRR']);
quickHTMLOption('DependentChildrenClaimed', 'Dependent Children Claimed', $staff['DependentChildrenClaimed'], 2);
quickHTMLOption('EntitledAnnualLeaveLoading', 'Entitled Annual Leave Loading', $staff['EntitledAnnualLeaveLoading']);
quickHTMLOption('HECSDebt', 'HECS Debt', $staff['HECSDebt']);
quickHTMLOption('SFSSDebt', 'SFSS Debt', $staff['SFSSDebt']);
?>
</table>

<br />
<br />

<table class="spacetable">
	<tr>
		<th>Year</th>
		<th>Total Received</th>
		<th>Total Hours Worked</th>
		<th>Gross Wage</th>
		<th>Super Payments</th>
		<th>Work Bonus</th>
		<th>Commission</th>
	</tr>
<?php
for ($year = idate('Y') + ((idate('m') >= 7) ? 1 : 0) - 1; $year > 2003; $year--) {
    $wage = timesheetStaffSummary($staff_id, mktime(0, 0, 0, 7, 1, $year));
?>
	<tr>
		<td style="text-align:center;"><?=$year?>-<?=$year + 1?></td>
		<td style="text-align:right;">$<?=sprintf('%.2f', $wage['NetPay'])?></td>
		<td style="text-align:center;"><?=$wage['net_hours']?></td>
		<td style="text-align:right;">$<?=sprintf('%.2f', $wage['gross_pay'])?></td>
		<td style="text-align:right;">$<?=sprintf('%.2f', $wage['gross_pay'] * 0.09)?></td>
		<td style="text-align:right;">$<?=sprintf('%.2f', $wage['gross_pay'] / 13.0)?></td>
		<td style="text-align:center;">-</td>
	</tr>
<?php
}
?>
</table>

<br />
<br />
<input type="button" value="View Timesheets" class="unfinished" onclick="window.open('/webadmin/timesheets/?action=list&staff_id=')" />
<input type="button" value="View Work Bonus" class="unfinished" />
<input type="button" value="View Year Summary" class="unfinished" />

<br />

<?=formatHTMLTitle('Administration')?>

<table style="width:50%;">
	<tr>
		<td class="left_title">Date Started (yyyy-mm-dd)</td>
		<td><?=(0 != $staff['start_time']) ? date('Y-m-d', $staff['start_time']) : ''?></td>
	</tr>
	<tr>
		<td class="left_title">Date Finished (yyyy-mm-dd)</td>
		<td><?=(0 != $staff['end_time']) ? date('Y-m-d', $staff['end_time']) : ''?></td>
	</tr>
<?php
if (0 != $staff['start_time']) {
?>
	<tr>
		<td class="left_title">Time at Highway</td>
		<td>
			<?=($staff['years_here'] > 0) ? ($staff['years_here'] . ' years') : ''?>
			<?=($staff['months_here'] > 0) ? ($staff['months_here'] . ' months') : ''?>
			<?=($staff['days_here'] > 0) ? ($staff['days_here'] . ' days') : ''?>
		</td>
	</tr>
<?php
}
?>
</table>

<br />
<br />

<?php
$comments = staffCommentsFromDatabase($staff_id);
if (count($comments) > 0) {
?>
<br />
<table class="spacetable">
	<tr><th>Date</th><th>Admin</th><th style="width:100%;">Comment</th></tr>
<?php
foreach ($comments as $row) {
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td style="white-space:nowrap;"><?=date('d/m/Y', $row['comment_time'])?></td>
		<td style="white-space:nowrap;"><?=$row['comment_user']?></td>
		<td><?=nl2br(htmlspecialchars($row['comment_text']))?></td>
	</tr>
<?php
}
?>
</table>
<?php
}
?>

<br />
<br />

<table style="width:50%;">
	<tr>
		<td class="left_title" style="width:50%;">Status</td>
		<td><select name="active">
			<option value="y"<?=('y' == $staff['active']) ? ' selected="selected"' : ''?>>Active</option>
			<option value="n"<?=('n' == $staff['active']) ? ' selected="selected"' : ''?>>Inactive</option>
		</select></td>
	</tr>
</table>

<br />
<br />
