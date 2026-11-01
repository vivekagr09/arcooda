<!-- currency_search.tpl.php -->

<script type="text/javascript">
function onSearchChange(changed)
{
	var amount_from=getValue('amount_from');
	var currency_from=getValue('currency_from');
	var currency_to=getValue('currency_to');
	if(currency_from!='' && currency_to!='')
		setValue('amount_to',convertCurrency(amount_from,currency_from,currency_to));
	else
		setValue('amount_to','');
}
</script>

<table align="center" style="border:1px solid #FF9900;width:95%;">
	<form accept-charset="utf-8" autocomplete="off" method="post" action="index.php?action=history">
	<tr>
		<td rowspan="4" style="width:50%;background-color:#FFFFCC;"></td>
		<td colspan="2" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Calculate Exchange Rate</h2></td>
		<td rowspan="4" style="width:50%;background-color:#FFFFCC;"></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;white-space:nowrap;">Currency From:</td>
		<td style="text-align:left;background-color:#FFFFCC;white-space:nowrap;">
			<input type="text" size="8" name="amount_from" value="<?= htmlspecialchars($amount_from) ?>" style="text-align:right;" onchange="onSearchChange('amount_from');" />
			<select name="currency_from" style="width:256px;" onchange="onSearchChange('currency_from');">
				<option value=""></option>
				<?= formatHTMLOptions(getCurrencyList(),$currency_from) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;white-space:nowrap;">Currency To:</td>
		<td style="text-align:left;background-color:#FFFFCC;white-space:nowrap;">
			<input type="text" readonly="readonly" size="8" name="amount_to" value="<?= convertCurrency($amount_from,$currency_from,$currency_to) ?>" style="text-align:right;" />
			<select name="currency_to" style="width:256px;" onchange="onSearchChange('currency_to');">
				<option value=""></option>
				<?= formatHTMLOptions(getCurrencyList(),$currency_to) ?>
			</select>
		</td>
	</tr>
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><br /><input type="submit" value="History" /><br /></td></tr>
	</form>
</table>

<!-- /currency_search.tpl.php -->
