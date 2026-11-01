<!-- website_preferences_3.tpl -->

<?php
moduleLoad('member'); //memberGetOptionList, memberPreferencesFromDatabase
moduleLoad('product'); //productFromDatabase
?>

<?=formatHTMLTitle('Coinop Express Member Preferences')?>

<table class="table_fields"><tr>
    <th>Wholesale Account :</th>
    <td><input type="checkbox" name="member[wholesale]" value='y' <?=('y' == $member['wholesale']) ? 'checked' : ''?> /></td>
</tr></table>
<table class="table_fields"><tr>
    <th>Newsletter :</th>
    <td><input type="checkbox" name="member_preferences[customer]" value='y' <?=('y' == $member_preferences['customer']) ? 'checked' : ''?> /></td>
</tr></table>

<?php
if ('add' != $action && is_array($member_specials)) {
?>

<?=formatHTMLTitle('Coinop Express Special Pricing', '<a class="unfinished" style="color:black;" href="#" onclick="addSpecialPrice(3,\'coe\');return false">Add</a>')?>

<table class="table_grid" id="coe_spec" name="coe_spec">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Online (USD)</th>
        <th>Special Price (USD)</th>
        <th>&nbsp;</th>
    </tr>
<?php
    $coe_pricing = $member_specials[3];
    if (is_array($coe_pricing)) {
        foreach ($coe_pricing as $k => $pricing) {
            if (moduleLoad('product')) {
                $product = productFromDatabase($pricing['product_id']);
                $pricing['product_name'] = $product['product_name'];
                $pricing['product_onlineus'] = convertCurrency($product['primary_price'], $product['primary_currency'], 'USD');
            }
            $pricing['special_priceus'] = convertCurrency($pricing['special_price'], $pricing['currency'], 'USD');
?>
    <tr>
        <td><?php hecho($pricing['product_id']) ?></td>
        <td><?php hecho($pricing['product_name']) ?></td>
        <td><?php hecho($pricing['min_order']) ?></td>
        <td>$<?=$pricing['product_onlineus']?></td>
        <td style="background-color:#7FFFD4;">$<?=$pricing['special_priceus']?></td>
        <td><input type="button" value=" Remove " onclick="removeSpecialPrice('coe', <?=$pricing['id']?>);return false" /></td>
    </tr>
<?php
        }
    }
?>
</table>
<?php
}
?>

<br /><br/>

<?php
$customer_type = explode(',', $member['customer_type']);
?>
<table class="table_fields">
    <tr>
        <th>Customer Type :</th>
        <td>
            <div style="width:128px;display:inline-block;"><input type="checkbox" name="customer_type[]" value="home"<?= (in_array('home', $customer_type)) ? ' checked="checked"' : '' ?>/> Home User</div>
            <div style="width:128px;display:inline-block;"><input type="checkbox" name="customer_type[]" value="reseller"<?= (in_array('reseller', $customer_type)) ? ' checked="checked"' : '' ?> /> Reseller</div>
            <div style="width:128px;display:inline-block;"><input type="checkbox" name="customer_type[]" value="distributor"<?= (in_array('distributor', $customer_type)) ? ' checked="checked"' : '' ?> /> Distributor</div>
            <div style="width:128px;display:inline-block;"><input type="checkbox" name="customer_type[]" value="manufacturer"<?= (in_array('manufacturer', $customer_type)) ? ' checked="checked"' : '' ?> /> Manufacturer</div>
            <div style="width:128px;display:inline-block;"><input type="checkbox" name="customer_type[]" value="competitor"<?= (in_array('competitor', $customer_type)) ? ' checked="checked"' : '' ?> /> Competitor</div>
        </td>
    </tr>
<?php
/*
?>
    <tr>
        <th>Mailing List :</th>
        <td><select name="member_preferences[3][mailing_list]">
            <option value="n">No</option>
            <option value="y"<?=('y' == $member_pref['mailing_list']) ? ' selected="selected"' : ''?>>Yes</option>
        </select></td>
    </tr>
<?php
*/
?>
</table>

<br />
<br />

<script type="text/javascript">
//<![CDATA[
function preferences_3_check(input) {
    if ($(input).is(":checked")) {
        $('#member-check-new').show();
        $('#member-check-old').hide();
    } else {
        $('#member-check-new').hide();
        $('#member-check-old').show();
    }
}
//]]>
</script>
<div class="wide" style="border:2px solid #f00;padding:10px;text-align:left;">
	<b style="color:#f00;">Member Checked :</b> <input type="checkbox" value="true" name="member_check" onchange="preferences_3_check(this);" />
<?php
if ($member['check_staff_id']) {
    $staff_list = getStaffList();
?>
    <span class="member-check-label" id="member-check-old">
        <b><?=date('d/m/Y', strtotime($member['check_date']))?></b> (by <b><?php hecho($staff_list[$member['check_staff_id']])?></b>)
    </span>
<?php
}
?>
	<span class="member-check-label" id="member-check-new" style="display:none;">
		<b><?=date('d/m/Y')?></b> (by <b><?=htmlspecialchars(ucwords($_GLOBALS['staff']['first_name']))?></b>)
	</span>
</div>

<br />
<br />

<?php
//$member_pref = memberPreferencesFromDatabase($member_id);
$survey_types   = array();
$survey_answers = array();
?>

<?php
/*
?>
<table class="table_fields" style="display:none;">
<?php
foreach ($survey_types as $type_id => $type_name) {
?>
    <tr>
        <th><?=$type_name?> :</th>
        <td><select name="member_preferences[3][survey][<?=$type_id?>]">
            <option value=""></option>
            <?=formatHTMLOptions($survey_answers[$type_id], $member_pref['survey_' . $type_id])?>
        </select></td>
    </tr>
<?php
}
?>
    <tr>
        <th>Currency :</th>
        <td><select name="member_preferences[3][currency]">
            <option value=""></option>
            <?=formatHTMLOptions(memberGetOptionList('currency'), $member_pref['currency'])?>
        </select></td>
    </tr>
</table>
<br /><br />
<?php
*/
?>

<!-- /website_preferences_3.tpl -->
