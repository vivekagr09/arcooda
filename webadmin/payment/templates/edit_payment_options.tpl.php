<?php
$sites          = array(1 => 'Highwaygames', 2 => 'Highway Entertainment', 9 => 'Converters.tv', 11 => 'Highwaygroup', 16 => 'Car Converters');
$payment_method = paymentMethodsFromDatabase($payment_method_id, $site_id);
$currency_list  = getCurrencyList();
//print_array($currency_list);
$currency_options = array('AUD', 'CAD', 'EUR', 'GBP', 'JPY', 'USD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK', 'DKK', 'PLN', 'NOK', 'HUF', 'CZK');
foreach ($currency_list as $code => $name) {
    if (!in_array($code, $currency_options)) {
        unset($currency_list[$code]);
    }
}

$currency_list = array_chunk($currency_list, 3, true);
?>
<div style="font-size:18px;text-align:center"><b>Edit - <?=$payment_method['payment_method_name']?> on <?=$sites[$site_id]?></div><br />
<form action="" method="post">
	<input type="hidden" name="payment_method_id" value="<?=$payment_method_id?>" />
	<input type="hidden" name="site_id" value="<?=$site_id?>" />
	<input type="hidden" name="action" value="do_edit" />
	<table class="wide" style="border:1px solid #CCCCCC" align="center">
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Name :</td>
			<td style="width:60%"><input style="width:100%" type="text" name="payment_method_name" value="<?=$payment_method['payment_method_name']?>" /></td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Description :</td>
			<td style="width:60%">
				<textarea rows="6" onkeyup="textareaAutoSize(this,3,3,15)" style="width:100%" name="payment_method_description"><?=$payment_method['payment_method_description']?></textarea>
			</td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Details :</td>
			<td style="width:60%">
				<textarea rows="6" onkeyup="textareaAutoSize(this,3,3,15)" style="width:100%" name="payment_method_details"><?=$payment_method['payment_method_details']?></textarea>
			</td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Active :</td>
			<?php $yesno = array('y' => 'Yes', 'n' => 'No')?>
			<td style="width:60%">
				<select style="width:100%" name="payment_method_public"><?=formatHTMLOptions($yesno, $payment_method['payment_method_public'])?></select>
			</td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Charge Percent (%) :</td>
			<td style="width:60%"><input type="text" style="width:100%" name="payment_charge_percent" value="<?=$payment_method['payment_charge_percent']?>"></td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Payment Option Charge Amount :</td>
			<td style="width:60%"><input type="text" style="width:100%" name="payment_charge_amount" value="<?=$payment_method['payment_charge_amount']?>"></td>
		</tr>
		<tr>
			<td style="width:40%;" class="left_title">Accepted Currencies :</td>
			<?php $accepted_currencies = explode(',', $payment_method['accepted_currencies'])?>
			<td style="width:60%">
				<table style="width:100%;border:0px;">
<?php
foreach ($currency_list as $row => $array) {
?>
					<tr>
<?php
foreach ($array as $code => $currency) {
        list($currency, $extra) = explode(' - ', $currency);
?>
						<td><input type="checkbox" name="currencies[]" value="<?=$code?>" <?=in_array($code, $accepted_currencies) ? 'checked' : ''?> /><?=trim($currency)?></td>
<?php
}
?>
					</tr>
<?php
}
?>
				</table>
			</td>
		</tr>
	</table>
	<br />
	<center><input type="submit" value="Update" /></center>
</form>