<?php
/**
 * @var int[]    $staff_super_funds
 * @var string[] $allowed_sections
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (count($staff_super_funds)<1) $staff_super_funds=array(0);

function super_row($scount='[SUPER_COUNT]',$super=array(),$locked=false)
{
	ob_start();
	$lock=($locked)?' disabled="disabled"':'';
?>
<table style="width:75%;">
	<tr>
		<td class="left_title" rowspan="5" style="width:0%;">
			<input type="checkbox" name="fund_active[<?=$scount ?>]"<?=($super['fund_active']=='y')?' checked="checked"':'' ?> onchange="javascript:selectSuperFund(<?=$scount?>);" />
			<input type="hidden" name="fund_id[<?=$scount ?>]" value="<?=intval($super['fund_id'])?>" />
		</td>
		<td class="left_title" style="width:25%;">Super Fund Name</td>
		<td style="width:75%;"><input<?=$lock ?> type="text" style="width:100%;" value="<?=htmlspecialchars($super['fund_name']) ?>" name="fund_name[<?=$scount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">Super Fund Address</td>
		<td><textarea<?=$lock ?> style="width:100%;" name="fund_address[<?=$scount ?>]" rows="5" onkeyup="javascript:textareaAutoSize(this,1,5);"><?=htmlspecialchars($super['fund_address'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Policy Number</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=htmlspecialchars($super['fund_policy']) ?>" name="fund_policy[<?=$scount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">Contact Name</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=htmlspecialchars($super['fund_contact']) ?>" name="fund_contact[<?=$scount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">Contact Phone</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=htmlspecialchars($super['fund_phone']) ?>" name="fund_phone[<?=$scount?>]" /></td>
	</tr>
</table>
<br />
<?php
	return ob_get_clean();
}
?>

<?=formatHTMLTitle(
	'<a href="javascript:toggleSection(\'sectionSuperannuation\');">Superannuation</a>',
	(in_array('USERS',$allowed_sections))?'<a href="javascript:void(0);" onclick="javascript:addSuperFund();">Add Super Fund</a>':''
)?>

<div id="sectionSuperannuation">

<script type="text/javascript">

var countSuper=<?=max(0,count($staff_super_funds))?>;

function selectSuperFund(row)
{
	for (var i=0;i<countSuper;i++)
	{
		flag=getElement('fund_active['+i+']');
		if (flag!=null) flag.checked=(row==i);
	}
}

function addSuperFund()
{
	var scount=String(countSuper);
	var super_table=getElement("sectionSuperannuation");
	if (super_table==null) return;
	super_table.innerHTML+=<?=str_replace('[SUPER_COUNT]', '" + scount + "', json_encode(super_row()))?>;
	countSuper++;
}
</script>

<input type="hidden" name="super_list" value="<?=implode('|',$staff_super_funds)?>" />

<div name="sectionSuperannuation">
<?php
$scount=0;
foreach ($staff_super_funds as $fund_id)
{
	if ($fund_id==0)
		echo super_row($scount++,array('fund_active'=>'y'));
	else
	{
		$super_fund=staffSuperFromDatabase($fund_id);
		if ($super_fund['active']!='n' || in_array('USERS',$allowed_sections))
			echo super_row($scount++,staffSuperFromDatabase($fund_id),true);
	}
}
?>
</div>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionSuperannuation_hideNote">
Superannuation can be viewed by Clicking "Superannuation" above
</div>

<br /><br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
