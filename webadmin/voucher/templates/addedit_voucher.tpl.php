<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('invoice'); //invoiceFormatNumber

$site_id = intval($posted['site_id']);
//if(!$site_id) $site_id=9;
$preset = $names = array();
$res    = $DB->query("SELECT * FROM `highwaygroup`.`gift_preset` WHERE site_id=" . $site_id);
while ($row = $DB->fetch_assoc($res)) {
    $preset[$row['id']] = $row;
    $names[$row['id']]  = hecho($row['name'], 1);
}
$dUser = (in_array($_SERVER['REMOTE_USER'], array('Jwalit', 'Daniel', 'steve', 'Steve')));
?>
<script type="text/javascript">
//<![CDATA[
var preset = <?php jecho($preset)?>;
function searchMember()
{
	var url='/webadmin/<?=($dUser) ? 'new_' : ''?>members/?return_message=select&<?=$dUser ? 'search_for' : 'query'?>='+escape(getValue('member_name'))+"&action=<?=($dUser) ? 'list' : 'do_search&active=y'?>";
	openWindow(url,'600','400',true,'scrollbars, resizable');
}
function deleteMember()
{
	setValue('member_id',0);
	setValue('member_name','');
	getElement('member_name').readOnly=false;
}
function setMemberData(data)
{
	if(data['member_id']!=0)
	{
		setValue('member_name', data['first_name']+' '+data['last_name']);
		setValue('member_id', data['member_id']);
		getElement('member_name').readOnly=true;
	}
}
function setPresetValues(set)
{
	id=getValue('preset_id');
	var am=getElement('amount_value'); var ame=getElement('amount_extra'); var crn=getElement('currency');
	var trm=getElement('terms'); var ex=getElement('expiry_value'); var exd=getElement('expiry_date');
	var plist=getElement('preset_list');
	if(set && preset[id]['id']!=null)
	{
		getElement('expiry_extra').style.display='none';
		plist.style.display='';
		am.value=preset[id]['amount_value']; ame.value=preset[id]['amount']; crn.value=preset[id]['currency'];
		trm.value=preset[id]['terms']; ex.value=preset[id]['expiry_value']; exd.value=preset[id]['expiry_date'];
		setValue('hidden_amount_value',preset[id]['amount_value']); setValue('hidden_amount_extra',preset[id]['amount']);
		setValue('hidden_currency',preset[id]['currency']); setValue('hidden_terms',preset[id]['terms']);
		setValue('hidden_expiry_value',preset[id]['expiry_value']); setValue('hidden_expiry_date', preset[id]['expiry_date']);
		am.disabled=ame.disabled=crn.disabled=trm.disabled=ex.disabled=exd.disabled=true;
	}
	else if(!set)
	{
		plist.style.display='none';
		am.disabled=ame.disabled=crn.disabled=trm.disabled=ex.disabled=exd.disabled=false;
	}
}
//]]>
</script>
<?php
if ($voucher_lock['staff_id'] != $_GLOBALS['staff']['staff_id'] && 'add' != $action) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $voucher_lock) {
        echo 'This Gift Coupon is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $voucher_lock['expiry_time'] - time();
        echo 'This Gift Coupon is currently locked to ' . getStaffName($voucher_lock['staff_id']) . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
	</td></tr>
</table>
<br /><br />
<?php
}
$sql   = " SELECT UNIX_TIMESTAMP(date) AS message_time, message FROM `highwaygroup`.`gift_messages` WHERE voucher_id=" . intval($voucher_id) . " AND `date`>" . formatSQLDateTime(time() - 255) . " AND type='change' ORDER BY date DESC";
$query = $DB->query($sql);
//echo $sql;
if ($DB->num_rows($query) && !$popup_window) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
while ($message = $DB->fetch_assoc($query)) {
        $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?=nl2br(hecho($message['message'], 1))?></li>
</div>
<br />
<?php
}
}

if ('' == $voucher['currency']) {
    $voucher['currency'] = 'USD';
}

if (!isset($voucher['amount_value'])) {
    $voucher['amount_value'] = 25;
}

if (!isset($voucher['expiry_value'])) {
    $voucher['expiry_value'] = 1;
}

$disabled = ('preset' == $voucher['voucher_type']);
?>
<form action="index.php?action=do_<?=$action . (($site_id) ? '&site_id=' . $site_id : '')?>" method="post">
<input type="hidden" name="voucher_id" value="<?=$voucher_id?>" />
<?=formatHTMLTitle('Gift Coupon')?>
<table class="wide" style="width:98%;" align="center">
	<input type="hidden" name="hidden_amount_extra" value="<?=$voucher['amount']?>" />
	<input type="hidden" name="hidden_amount_value" value="<?=$voucher['amount_value']?>" />
	<input type="hidden" name="hidden_currency" value="<?=$voucher['currency']?>" />
	<input type="hidden" name="hidden_terms" value="<?=$voucher['terms']?>" />
	<input type="hidden" name="hidden_expiry_value" value="<?=$voucher['expiry_value']?>" />
	<input type="hidden" name="hidden_expiry_date" value="<?=$voucher['expiry_date']?>" />
<?php
if (!$site_id) {
?>
	<tr><td class="left_title">Site :</td><td><select name="site_id"><?=formatHTMLOptions($site_list, $voucher['site_id'])?></select></td></tr>
<?php
}
?>
	<tr><td class="left_title">Name of Person / Member ID :</td><td>
		<input type="text" name="member_name" value="<?=$voucher['member_name']?>" style="width:65%"<?=($voucher['member_id'] > 0) ? ' readonly="readonly"' : ''?>/>
		<input type="hidden" name="member_id" value="<?=$voucher['member_id']?>" />
		<input type="button" value="Search" onclick="searchMember();" />
		<input type="button" value="Delete" onclick="deleteMember()" />
	</td></tr>
	<tr><td class="left_title">Coupon Type :</td><td>
		<input onclick="setPresetValues(true)" type="radio" name="voucher_type" value="preset"<?=('preset' == $voucher['voucher_type']) ? ' checked' : ''?> /> Preset Values<br />
		<input onclick="setPresetValues(false)" type="radio" name="voucher_type" value="custom"<?=('custom' == $voucher['voucher_type']) ? ' checked' : ''?> /> Custom Values
	</td></tr>
	<tr id="preset_list" style="<?=('preset' == $voucher['voucher_type']) ? '' : 'display:none'?>"><td class="left_title">Select a Preset Value :</td><td><select style="width:100%;" onchange="setPresetValues(true)" name="preset_id"><?=formatHTMLOptions($names, $voucher['preset_id'])?></select></td></tr>
	<tr><td class="left_title">Coupon Value :</td><td>
		<select onchange="toggleSection('amount_extra',(this.value==0))" name="amount_value" style="width:45%;"<?=($disabled) ? ' disabled' : ''?>><?=formatHTMLOptions($amounts, $voucher['amount_value'])?></select>
		<select name="currency" style="width:50%;"<?=($disabled) ? ' disabled' : ''?>><?=formatHTMLOptions($currency_list, $voucher['currency'])?></select>
		<br /><input type="text" name="amount_extra" value="<?=$voucher['amount']?>" style="width:45%;<?=(0 != $voucher['amount_value']) ? 'display:none;' : ''?>"<?=($disabled) ? ' disabled' : ''?> />
	</td></tr>
	<tr><td class="left_title">Terms (Purchase Value) :</td><td><select name="terms" style="width:45%;"<?=($disabled) ? ' disabled' : ''?>>
		<?=formatHTMLOptions($terms, $voucher['terms'])?>
	</select></td></tr>
	<tr><td class="left_title">Expiry Date :</td><td>
		<select onchange="toggleSection('expiry_extra',(this.value==0))"  name="expiry_value" style="width:45%;"<?=($disabled) ? ' disabled' : ''?>><?=formatHTMLOptions($expiry, $voucher['expiry_value'])?></select>
		<br /><span name="expiry_extra" style="<?=(0 != $voucher['expiry_value']) ? 'display:none' : ''?>">
			<input type="text" style="width:45%" name="expiry_date" value="<?=$voucher['expiry_date']?>"<?=($disabled) ? ' disabled' : ''?>>
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('expiry_date'));" />
			<input type="button" value="Clear" onclick="javascript:setValue('expiry_date','');" />
		</span>
	</td></tr>
	<tr><td class="left_title">Status :</td><td><select name="status" style="width:100%;">
		<?=formatHTMLOptions($status, $voucher['status'])?>
	</select></td></tr>
<?php
if ('processed' == $voucher['status']) {
?>
	<tr><td class="left_title">&nbsp;</td><td style="background-color:#FFEECC;">Voucher Processed On: <?=date('d/m/Y', $voucher['process_time'])?> - Against Invoice ID: <a href="/webadmin/invoice/index.php?action=edit&invoice_id=<?=$voucher['invoice_id']?>" target="_blank"><?=invoiceFormatNumber($voucher['invoice_id'], 0, 9)?></td></tr>
<?php
}
?>
</table>
<p><b>NB: To make an unlimited Voucher, select "Other" from the "Expiry Date" drop-down and leave the date blank.</b></p>
<center><input type="submit" value="<?=('add' == $action) ? 'Add' : 'Update'?> Voucher"<?=($voucher_lock['staff_id'] == $_GLOBALS['staff']['staff_id'] || 'add' == $action) ? '' : ' disabled="disabled"'?> /></center>
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
