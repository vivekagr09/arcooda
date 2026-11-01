<?php
/**
 * @var string[] $active_staff
 * @var int      $staff_id
 * @var mixed[]  $staff
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php

moduleLoad('staff');

$staff_list = staffGetOptionList('id');

/**
 * @param string $field
 * @param string $name
 * @param string $value
 * @param int    $style
 *
 * @return void
 */
function taxinfoQuickOption($field, $name, $value, $style = 0)
{
    static $alt       = false;
    static $rad_count = 0;
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td><?=$name?></td>
		<td style="text-align:center;">
<?php
switch ($style) {
        default:
?>
			<label for="quickRadio_<?=$rad_count?>">Yes </label><input type="radio" name="<?=$field?>" id="quickRadio_<?=$rad_count++?>" value="Y"<?=('Y' == $value) ? ' checked="checked"' : ''?> />
			<label for="quickRadio_<?=$rad_count?>">No </label><input type="radio" name="<?=$field?>" id="quickRadio_<?=$rad_count++?>" value="N"<?=('N' == $value) ? ' checked="checked"' : ''?> />
<?php
break;
        case 1:
?>
			<select name="<?=$field?>" style="width:96px;"><?=formatHTMLOptions(array(0 => 0, 1, 2), $value)?></select>
<?php
break;
        case 2:
?>
			<input name="<?=$field?>" type="text" value="<?=htmlspecialchars($value)?>" style="width:96px;text-align:right;" />
<?php
break;
    }
?>
		</td>
	</tr>
<?php
}

?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPaymentDetails\');">Payment Details</a>')?>

<div id="sectionPaymentDetails">

<script type="text/javascript">
function bonusChange(newVal)
{
	var theRow=getElement('bonus_row');
	if (theRow==null) return;
	if (newVal=='n')
	{
		setValue('bonus_date','');
		theRow.style.display='none';
	}
	else
		theRow.style.display='';
}

function changedUserType(newValue)
{
	toggleSection('autosheet_row',(newValue=='fixed'));
	toggleSection('tfninfo_table',(newValue=='fixed' || newValue=='paid'));
	toggleSection('abninfo_table',(newValue=='contract'));
	toggleSection('sectionSuperannuation',!(newValue=='contract'));

	if (newValue=='fixed')
		setValue('wage_freq','Week');
	else
		setValue('wage_freq','Hour');
}
</script>

<table style="width:50%;">
<?php
if (in_array('USERS', $allowed_sections)) {
    $employee_types = array(
        'contract' => 'Contractor',
        'fake'     => 'Fake Staff',
        'fixed'    => 'Fixed Wage',
        'paid'     => 'Paid Staff',
        'access'   => 'Webadmin Access Only',
        'work_exp' => 'Work Experience',
    );
    $review_waits = array(
        2629746  => '1 Month',
        7889238  => '3 Months',
        15778476 => '6 Months',
        31556952 => '12 Months',
    );
?>
	<tr>
		<td class="left_title" style="width:25%;">Type of Employee</td>
		<td><select style="width:166px;" name="user_type" onchange="javascript:changedUserType(this.value);">
			<option value=""></option>
			<?=formatHTMLOptions($employee_types, $staff['user_type'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Pay Rate (Per <span name="wage_freq"><?=('fixed' == $staff['user_type']) ? 'Week' : 'Hour'?></span>)</td>
		<td><input style="text-align:right;width:166px;" name="pay_rate" type="text" value="<?=htmlspecialchars($staff['pay_rate'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Effective From (yyyy-mm-dd)</td>
		<td>
			<input type="date" name="pay_date" value="" /><br />
			*Please note that timesheets after this date will need to be manually updated
		</td>
	</tr>
<?php
	$pay_history = staffPayRatesFromDatabase($staff_id);
    if (count($pay_history) > 0) {
?>
	<tr class="alt">
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
	<tr id="autosheet_row"<?=('fixed' == $staff['user_type']) ? '' : ' style="display:none;"'?>>
		<td class="left_title">Auto Add Timesheet</td>
		<td><select style="width:166px;" name="autosheet">
			<?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $staff['autosheet'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Pay Review Date (yyyy-mm-dd)</td>
		<td><input type="date" name="review_date" value="<?=(0 != $staff['review_time']) ? date('Y-m-d', $staff['review_time']) : ''?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Next Review Date</td>
		<td><select name="review_frequency" style="width:166px;">
			<option></option>
			<?=formatHTMLOptions($review_waits, $staff['review_frequency'])?>
<?php
if (0 != $staff['review_frequency'] && !isset($review_waits[$staff['review_frequency']])) {
?>
			<option value="<?=htmlspecialchars($staff['review_frequency'])?>" selected="selected"><?=formatSecondsToTime($staff['review_frequency'])?></option>
<?php
}
?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Pay Review Staff</td>
		<td><select name="reviewer_id" style="width:166px;"><option></option><?=formatHTMLOptions($active_staff, $staff['reviewer_id'])?></select></td>
	</tr>
	<tr>
		<td class="left_title">Work Bonus</td>
		<td><select style="width:166px;" name="pay_bonus" onchange="javascript:bonusChange(this.value);">
			<?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), (0 != $staff['bonus_time']) ? 'y' : 'n')?>
		</select></td>
	</tr>
	<tr id="bonus_row"<?=(0 != $staff['bonus_time']) ? '' : ' style="display:none;"'?>>
		<td class="left_title">Commence Date (yyyy-mm-dd)</td>
		<td>
			<input type="date" name="bonus_date" value="<?=(0 != $staff['bonus_time']) ? date('Y-m-d', $staff['bonus_time']) : ''?>" /><br />
			*Please note that timesheets after this date will need to be manually updated
		</td>
	</tr>
<?php
} else {
?>
	<tr>
		<td class="left_title" style="width:25%;">Pay Rate (Per Hour)</td>
		<td><?=htmlspecialchars($staff['pay_rate'])?></td>
	</tr>
<?php
if (0 != $staff['bonus_time']) {
?>
	<tr>
		<td class="left_title" style="width:25%;">Work Bonus</td>
		<td>Yes</td>
	</tr>
	<tr>
		<td class="left_title">Commence Date (yyyy-mm-dd)</td>
		<td><?=date('Y-m-d', $staff['bonus_time'])?></td>
	</tr>
<?php
}
}
?>
</table>

<br /><br />

<?php
if (in_array('USERS', $allowed_sections)) {
?>
<table class="wide">
	<tr>
		<th>Payment Review Date (yyyy-mm-dd)</th>
		<th>Pay Review Comments</th>
	</tr>
<?php
foreach (staffReviewsFromDatabase($staff_id) as $review) {
?>
	<tr>
		<td class="left_title" style="white-space:nowrap;"><?=date('d/m/Y', $review['review_time'])?></td>
		<td style="width:100%;"><?=nl2br(htmlspecialchars($review['review_comments']))?></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title" style="width:25%;"><input type="date" name="pay_review[review_date]" value="<?=date('Y-m-d')?>" /></td>
		<td><textarea name="pay_review[review_comments]" rows="8" onkeyup="javascript:textareaAutoSize(this,1,8);"></textarea></td>
	</tr>
</table>

<br /><br />

<table style="width:50%;<?=(in_array($staff['user_type'], array('fixed', 'paid'))) ? '' : 'display:none;'?>" id="tfninfo_table">
	<tr>
		<td>Tax File Number</td>
		<td style="text-align:center;"><input style="width:98%;" type="text" name="TFN" value="<?=htmlspecialchars($staff['TFN'])?>" /></td>
	</tr>
<?php
taxinfoQuickOption('TFNABNProvided', 'TFN ABN Provided', $staff['TFNABNProvided']);
    taxinfoQuickOption('TFNABNExempt', 'TFN ABN Exempt', $staff['TFNABNExempt']);
    taxinfoQuickOption('AustralianResident', 'Australian Resident', $staff['AustralianResident']);
    taxinfoQuickOption('TaxFreeThresholdClaimed', 'Tax Free Threshold Claimed', $staff['TaxFreeThresholdClaimed']);
    taxinfoQuickOption('RebateAndFTAAmountClaimed', 'Rebate And FTA Amount Claimed', $staff['RebateAndFTAAmountClaimed'], 2);
    taxinfoQuickOption('MedicareVariation', 'Medicare Variation', $staff['MedicareVariation'], 1);
    taxinfoQuickOption('MedicareReducedRate', 'Medicare Reduced Rate', $staff['MedicareReducedRate']);
    taxinfoQuickOption('HasSpouse', 'Has Spouse', $staff['HasSpouse']);
    taxinfoQuickOption('CombinedIncLessAmtRR', 'Combined Income Less Amt RR', $staff['CombinedIncLessAmtRR']);
    taxinfoQuickOption('DependentChildrenClaimed', 'Dependent Children Claimed', $staff['DependentChildrenClaimed'], 2);
    taxinfoQuickOption('EntitledAnnualLeaveLoading', 'Entitled Annual Leave Loading', $staff['EntitledAnnualLeaveLoading']);
    taxinfoQuickOption('HECSDebt', 'HECS Debt', $staff['HECSDebt']);
    taxinfoQuickOption('SFSSDebt', 'SFSS Debt', $staff['SFSSDebt']);
?>
</table>

<table style="width:50%;<?=(in_array($staff['user_type'], array('contract'))) ? '' : 'display:none;'?>" id="abninfo_table">
	<tr>
		<td>Australian Business Number</td>
		<td style="text-align:center;"><input style="width:98%;" type="text" name="ABN" value="<?=htmlspecialchars($staff['ABN'])?>" /></td>
	</tr>
<?php
taxinfoQuickOption('GSTRegistered', 'GST Registered', $staff['GSTRegistered']);
?>
</table>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPaymentDetails_hideNote">
All Payment Details can be viewed by Clicking "Payment Details" above
</div>

<br /><br />
<?php
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
