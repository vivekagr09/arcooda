<?php
/**
 * @var int[] $staff_bank_accounts
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('staff');

if (count($staff_bank_accounts)<1) $staff_bank_accounts=array(0);

function bank_row($bcount='[BANK_COUNT]',$bank=array(),$locked=false)
{
	ob_start();
	$lock=($locked)?' disabled="disabled"':'';
?>
<table style="width:75%;">
	<tr>
		<td class="left_title" rowspan="4" style="width:0%;">
			<input type="checkbox" name="bank_active[<?=$bcount ?>]"<?=($bank['bank_active']=='y')?' checked="checked"':'' ?> onchange="javascript:selectBankAccount(<?=$bcount?>);" />
			<input type="hidden" name="bank_id[<?=$bcount ?>]" value="<?=intval($bank['bank_id'])?>" />
		</td>
		<td class="left_title" style="width:25%;">Name of Account</td>
		<td style="width:75%;"><input<?=$lock ?> type="text" style="width:100%;" value="<?=$bank['bank_name'] ?>" name="bank_name[<?=$bcount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">Bank</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=$bank['bank_bank'] ?>" name="bank_bank[<?=$bcount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">BSB</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=$bank['bank_bsb'] ?>" name="bank_bsb[<?=$bcount?>]" /></td>
	</tr>
	<tr>
		<td class="left_title">Account Number</td>
		<td><input<?=$lock ?> type="text" style="width:100%;" value="<?=$bank['bank_account'] ?>" name="bank_account[<?=$bcount?>]" /></td>
	</tr>
</table>
<br />
<?php
	return ob_get_clean();
}

?>

<?=formatHTMLTitle(
	'<a href="javascript:toggleSection(\'sectionBankAccountDetails\');">Bank Account Details</a>',
	(in_array('USERS',$allowed_sections))?'<a href="javascript:void(0);" onclick="javascript:addBankAccount();">Add Bank Account</a>':''
)?>

<div id="sectionBankAccountDetails">

<script type="text/javascript">

var countBank=<?=max(0,count($staff_bank_accounts))?>;

function selectBankAccount(row)
{
	for (var i=0;i<countBank;i++)
	{
		flag=getElement('bank_active['+i+']');
		if (flag!=null) flag.checked=(row==i);
	}
}

function addBankAccount()
{
	var bcount=String(countBank);
	var bank_table=getElement("bank_table");
	if (bank_table==null) return;
	bank_table.innerHTML+=<?=str_replace('[BANK_COUNT]', '" + bcount + "', json_encode(bank_row()))?>;
	countBank++;
}
</script>

<input type="hidden" name="bank_list" value="<?=implode('|',$staff_bank_accounts)?>" />

<div name="bank_table">
<?php
$bcount=0;
foreach ($staff_bank_accounts as $bank_id)
{
	if ($bank_id==0)
		echo bank_row($bcount++,array('bank_active'=>'y'));
	else
	{
		$bank=staffBankFromDatabase($bank_id);
		if ($bank['active']!='n' || in_array('USERS',$allowed_sections))
			echo bank_row($bcount++,$bank,true);
	}
}
?>
</div>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionBankAccountDetails_hideNote">
All Bank Account Details can be viewed by Clicking "Bank Account Details" above
</div>

<br /><br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
