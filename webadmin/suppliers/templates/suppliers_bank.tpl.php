<div align="center">
<?php
	$extra = '<small><i><a style="color:blue" href="#" onclick="javascript:chooseBank();return false;">Add New Account Details</a></i></small>';
	echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionBank\');">'.$dis_name.' Bank Details</a>',$extra);

	function addBankPayment($bank)
	{
		global $currency_list;
?>
	<table class="wide" align="center" style="width:100%" border=0>
		<tr>
			<th colspan=4><?=($bank['title']) ? ucfirst($bank['title']) : 'Bank Payment'?><input type="hidden" name="bank['+bank+'][type]" value="b"></th>
		</tr>
		<tr>
			<td class="left_title">Beneficiary :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][beneficiary]" value="<?=$bank['beneficiary']?>"></td>
			<td class="left_title">Routing # :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][routing_no]" value="<?=$bank['routing_no']?>"></td>
		</tr>
		<tr>
			<td class="left_title">Beneficiary Address:</td>
			<td class="in">
				<textarea class="in" name="bank[<?=$bank['id']?>][beneficiary_address]" value="<?=$bank['beneficiary_address']?>" onkeypress="resize(this)"></textarea>
			</td>
			<td class="left_title">SWIFT Code :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][swift_code]" value="<?=$bank['swift_code']?>"></td>
		<tr>
			<td class="left_title">Account # :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][account_no]" value="<?=$bank['account_no']?>"></td>
			<td class="left_title">Currency :</td>
			<td class="in">
				<select name="bank[<?=$bank['id']?>][currency]" class="in">
					<option value=""></option><?=formatHTMLOptions($currency_list, $bank['currency'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left_title">Bank Name :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][bank]" value="<?=$bank['bank']?>"></td>
			<td class="left_title">Bank Branch :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][branch]" value="<?=$bank['branch']?>"></td>
		</tr>
		<tr>
			<td class="left_title">Bank Address :</td>
			<td class="in"><input type="text" class="in" name="bank[<?=$bank['id']?>][address]" value="<?=$bank['address']?>"></td>
			<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Comments :</td>
			<td colspan=3>
				<textarea name="bank[<?=$bank['id']?>][details]" style="width:100%;" onkeypress="resize(this)"><?=htmlspecialchars($bank['details'])?></textarea>
			</td>
		</tr>
	</table>
<?php
	}
?>
</div>
<div id="sectionBank">
<?php
$banks = supplierBanksFromDatabase($supplier_id); ?>
<table id="bank" class="wide" align="center" style="width:98%">
<?php
if(count($banks) > 0) {
	foreach($banks as $bank) { ?>
	<tr>
		<td>
<?php
			if($bank['type'] == 'b') addBankPayment($bank);
			else { ?>
				<table class="wide" align="center" style="width:100%" border=0>
					<tr>
						<th>
							<input type="hidden" name="bank['+bank+'][type]" value="<?=$bank['type']?>">
							<?=($bank['title']) ? ucfirst($bank['title']) : (($bank['type']=='p') ? 'PayPal' : 'Western Union')?>
						</th>
					</tr>
					<tr><td>
						<textarea name="bank['+bank+'][details]" style="width:100%;height: 100%" onkeyup="resize(this)"><?=htmlspecialchars($bank['details'])?></textarea>
					</td></tr>
				</table>
<?php
}?>
		</td>
	</tr>
		<!--
		<!--<input type="hidden" value="<?=$bank['id']?>" name="bank[<?=$bank['id']?>][id]">-->
<?php
}
}
?>
</table>
</div>
<br />