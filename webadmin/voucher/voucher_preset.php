<?php
include 'voucher_common.inc.php';
include 'templates/voucher_header.tpl.php';

// firstly get the voucher preset...
$site_id   = intval($posted['site_id']);
$preset_id = intval($posted['preset_id']);
//if(!$site_id) $site_id=9;
$add    = false;
$preset = $DB->query_first("SELECT * FROM `highwaygroup`.`gift_preset` WHERE id=" . $preset_id, MYSQLI_ASSOC);
if (!$preset) {
    $preset = array();
    $add    = true;}
if ('' == $preset['currency']) {
    $preset['currency'] = 'USD';
}

if (!isset($preset['amount_value'])) {
    $preset['amount_value'] = 25;
}

if (!isset($preset['expiry_value'])) {
    $preset['expiry_value'] = 1;
}

echo formatHTMLTitle("Voucher Presets");
?>
<form action="index.php?action=do_preset<?=($site_id) ? '&site_id=' . $site_id : ''?>" method="post"><table class="wide">
	<input type="hidden" value="<?=$preset['id']?>" name="preset_id" />
<?php
if (!$site_id) {
?>
	<tr><td class="left_title">Site :</td><td><select name="site_id" style="width:100%;"><?=formatHTMLOptions($site_list, $preset['site_id'])?></select></td></tr>
<?php
}
?>
	<tr><td class="left_title">Preset Name :</td><td><input style="width:100%;" type="text" name="name" value="<?=$preset['name']?>" /></td></tr>
	<tr><td class="left_title">Preset Value :</td><td>
		<select onchange="toggleSection('amount_extra',(this.value==0))" name="amount_value" style="width:45%;"><?=formatHTMLOptions($amounts, $preset['amount_value'])?></select>
		<select name="currency" style="width:50%;"><?=formatHTMLOptions($currency_list, $preset['currency'])?></select>
		<br /><input type="text" name="amount_extra" value="<?=$preset['amount']?>" style="width:45%;<?=(0 != $preset['amount_value']) ? 'display:none;' : ''?>" />
	</td></tr>
	<tr><td class="left_title">Preset Terms (Purchase Value) :</td><td><select name="terms" style="width:45%;">
		<?=formatHTMLOptions($terms, $preset['terms'])?>
	</select></td></tr>
	<tr><td class="left_title">Expiry Date :</td><td>
		<select onchange="toggleSection('expiry_extra',(this.value==0))"  name="expiry_value" style="width:45%;"><?=formatHTMLOptions($expiry, $preset['expiry_value'])?></select>
		<br /><span name="expiry_extra" style="<?=(0 != $preset['expiry_value']) ? 'display:none' : ''?>">
			<input type="text" style="width:75%" name="expiry_date" value="<?=$preset['expiry_date']?>">
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('expiry_date'));" />
			<input type="button" value="Clear" onclick="javascript:setValue('expiry_date','');" />
		</span>
	</td></tr>
</table><center><input type="submit" value="<?=($add) ? 'Add' : 'Edit'?>" /></center></form>
<?php
include 'templates/voucher_footer.tpl.php';
?>