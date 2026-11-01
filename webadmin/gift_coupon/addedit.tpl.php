<?php
$site_id    = intval($posted['site_id']);
$section_id = intval($posted['section_id']);
if (!isset($section_id)) {
    $section_id = 1;
}

$preset = $names = array();
$res    = $DB->query("SELECT * FROM highwaygroup.gift_coupon WHERE site_id=" . $site_id);
while ($row = $DB->fetch_assoc($res)) {
    $preset[$row['id']] = $row;
    $names[$row['id']]  = hecho($row['name'], 1);
}
$dUser = (in_array($_SERVER['REMOTE_USER'], array('Jwalit', 'Daniel', 'steve', 'Steve')));
?>
<script type="text/javascript">
//<![CDATA[
var preset = <?php jecho($preset)?>;

function setPresetValues(set) {
    id = getValue('preset_id');
    var am = getElement('amount_value');
    var ame = getElement('amount_extra');
    var crn = getElement('currency');
    var trm = getElement('terms');
    var ex = getElement('expiry_value');
    var exd = getElement('expiry_date');
    var plist = getElement('preset_list');
    if (set && preset[id]['id'] != null) {
        getElement('expiry_extra').style.display = 'none';
        plist.style.display = '';
        am.value = preset[id]['amount_value'];
        ame.value = preset[id]['amount'];
        crn.value = preset[id]['currency'];
        trm.value = preset[id]['terms'];
        ex.value = preset[id]['expiry_value'];
        exd.value = preset[id]['expiry_date'];
        setValue('hidden_amount_value', preset[id]['amount_value']);
        setValue('hidden_amount_extra', preset[id]['amount']);
        setValue('hidden_currency', preset[id]['currency']);
        setValue('hidden_terms', preset[id]['terms']);
        setValue('hidden_expiry_value', preset[id]['expiry_value']);
        setValue('hidden_expiry_date', preset[id]['expiry_date']);
        am.disabled = ame.disabled = crn.disabled = trm.disabled = ex.disabled = exd.disabled = true;
    }
    else if (!set) {
        plist.style.display = 'none';
        am.disabled = ame.disabled = crn.disabled = trm.disabled = ex.disabled = exd.disabled = false;
    }
}
//]]>
</script>
<?php
if ($giftcoupon_lock['staff_id'] != $_GLOBALS['staff']['staff_id'] && 'add' != $action) {
    ?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $giftcoupon_lock) {
        echo 'This Gift Coupon is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $giftcoupon_lock['expiry_time'] - $_SERVER['REQUEST_TIME'];
        echo 'This Gift Coupon is currently locked to ' . getStaffName($giftcoupon_lock['staff_id']) . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
    ?>
	</td></tr>
</table>
<br /><br />
<?php
}

if (!isset($giftcoupon['value'])) {
    $giftcoupon['value'] = 0.1;
}

if (!isset($giftcoupon['bonus'])) {
    $giftcoupon['bonus'] = 0.1;
}

if (!isset($giftcoupon['expiry_value'])) {
    $giftcoupon['expiry_value'] = 1;
}

?>
<form action="index.php?action=do_<?=$action . (($site_id) ? '&site_id=' . $site_id : '')?>" method="post">
<input type="hidden" name="id" value="<?=$id?>" />
<?=formatHTMLTitle('Gift Coupon')?>
<table class="wide" style="width:98%;" align="center">
	<!-- <input type="hidden" name="hidden_amount_extra" value="<?=$giftcoupon['value']?>" /> -->
	<input type="hidden" name="hidden_amount_value" value="<?=$giftcoupon['value']?>" />
	<input type="hidden" name="hidden_bonus_value" value="<?=$giftcoupon['bonus']?>" />
	<input type="hidden" name="hidden_expiry_value" value="<?=$giftcoupon['expiry_value']?>" />
	<input type="hidden" name="hidden_expiry_date" value="<?=$giftcoupon['expiry_date']?>" />
<?php
if (!$site_id) {
    $site_list = giftcouponGetOptionList("site_id");
    ?>
	<tr><td class="left_title">Company :</td><td><select name="site_id"><?=formatHTMLOptions($site_list, $giftcoupon['site_id'])?></select></td></tr>
<?php
}

$section_list = giftcouponGetOptionList("section");
?>
	<tr><td class="left_title">Section :</td><td><select name="section_id"><?=formatHTMLOptions($section_list, $giftcoupon['section'])?></select></td></tr>
<?php
$value_list = giftcouponGetOptionList("value");
?>
	<tr><td class="left_title">Coupon Value :</td><td>
		<select onchange="toggleSection('amount_extra',(this.value==-123))" name="amount_value" style="width:45%;">
			<?=formatHTMLOptions($value_list, $giftcoupon['value'])?>
		</select>
		<br />
		<input type="text" name="amount_extra" value="<?=$giftcoupon['value']?>" style="width:45%;<?=(0 != $giftcoupon['value']) ? 'display:none;' : ''?>" />
	</td></tr>
<?php
$expiry_value_list = giftcouponGetOptionList("expiry_value");
?>
	<tr>
		<td class="left_title">Expiry Date :</td>
		<td>
			<select onchange="toggleSection('expiry_extra',(this.value==0))"  name="expiry_value" style="width:45%;"><?=formatHTMLOptions($expiry_value_list, $giftcoupon['expiry_value'])?>
			</select>
			<br />
			<span name="expiry_extra" style="<?=(0 != $giftcoupon['expiry_value']) ? 'display:none' : ''?>">
			<input type="text" style="width:45%" name="expiry_date" value="<?=$giftcoupon['expiry_date']?>" />
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('expiry_date'));" />
			<input type="button" value="Clear" onclick="javascript:setValue('expiry_date','');" />
			</span>
		</td>
	</tr>
<?php
$active_list = giftcouponGetOptionList("active");
?>
	<tr>
		<td class="left_title">Status :</td>
		<td>
			<select name="active" style="width:100%;">
				<?=formatHTMLOptions($active_list, $giftcoupon['active'])?>
			</select>
		</td>
	</tr>
<?php

?>
</table>
<p><b>NB: To make an unlimited Coupon, select "Other" from the "Expiry Date" drop-down and leave the date blank.</b></p>

<center>
	<input type="submit" value="<?=('add' == $action) ? 'Add' : 'Update'?> Coupon"<?=($giftcoupon_lock['staff_id'] == $_GLOBALS['staff']['staff_id'] || 'add' == $action) ? '' : ' disabled="disabled"'?> />
</center>
</form>