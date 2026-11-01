<!-- forwarders_accounts.tpl.php -->

<?php
function account_row($acount='[ACCOUNT_COUNT]',$account=array())
{
	ob_start();
?>
<table style="width:100%;">
	<tr>
		<td><input type="hidden" name="account[<?php echo $acount ?>][id]" value="<?php hecho($account['id']) ?>" /><textarea rows="10" style="width:100%;" name="account[<?php echo $acount ?>][details]"><?php hecho($account['details']) ?></textarea></td>
	</tr>
</table>
<?php
	return ob_get_clean();
}
?>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAccounts\');">Bank Accounts</a>','<a href="javascript:addAccount();">Add New Account</a>') ?>
<div id="sectionAccounts">

<script type="text/javascript">
var firstAccount=1;
var lastAccount=<?php echo max(0,count($forwarder_accounts)) ?>;

function addAccount()
{
	lastAccount++;
	var acount=String(lastAccount);
	var cont_table=getElement('account_table');
	if(cont_table==null) return;

	var newRow1=addTableRow(cont_table,'account_row['+acount+']');
	addTableCell(newRow1,<?php echo str_replace('[acount]', '" + acount + "', jecho(account_row('[acount]'), 1)) ?>);
}

function deleteAccount(account_id)
{
	alert('Not Yet Implemented');
}
</script>

<table class="wide" id="account_table">
<?php
$acount=0;
$account_affected=array();
if(count($forwarder_accounts)>0)
	foreach($forwarder_accounts as $account)
	{
		echo '<tr id="account_row['.$acount.']"><td>'.account_row($acount++,$account).'</td></tr>'."\n";
		$account_affected[]=$account['id'];
	}
else
	echo '<tr id="account_row[0]"><td>'.account_row(0,$forwarder).'</td></tr>'."\n";
?>
</table>

<input type="hidden" name="account_affected" value="<?php echo implode(',',$account_affected) ?>" />

<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAccounts_hideNote">
Bank Accounts can be viewed by Clicking "Bank Accounts" above
<br />
</div>

<!-- /forwarders_accounts.tpl.php -->
