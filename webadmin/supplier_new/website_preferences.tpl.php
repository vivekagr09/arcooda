<!-- website_preferences.tpl -->

<?php
moduleLoad('invoice'); //invoiceSort, invoiceSearch, invoiceFromDatabase
moduleLoad('member'); //memberGetOptionList

if (is_array($member_preferences) || is_array($member_discounts)) {
    $discount_array  = array(
        0 => 'Regular',
        2 => "Copper (2% off)",
        5 => 'Bronze (5% off)',
        10 => 'Silver (10% off)',
        15 => 'Gold (15% off)',
        20 => 'Platinum (20%)',
        30 => 'Titanium (30% off)',
    );
?>

<script type="text/javascript">
//<![CDATA[
function prefsChecked(type, obj) {
    if (type != 'hg' && type != 'xbox') {
        getElement(type + '_a').style.display = (obj.checked) ? "" : 'none';
        getElement(type + '_prefs').style.display = 'none';
    }
}
function updateOverallDiscount(site_id) {
    var new_val = '';
    if (site_id == 1 || site_id == 18) new_val = '';
    else new_val = (getElement('member_type[' + site_id + ']').checked) ? getValue('discounts[' + site_id + ']') + '% discount' : '';
    setAllValues('discount_' + site_id, new_val);
}
function addSpecialPrice(site_id, ret) {
    var url = '/webadmin/member/members_special_pricing.php?return_message=' + ret + '&member_id=<?=$member_id?>&site_id=' + site_id;
    var features = 'scrollbars, resizable';
    window.open(url, 'product_popup', features);
}
function setSpecialData(data) {
    var table = getElement(data['return_message'] + '_spec');
    if (table == null) return;
    var newRow = addTableRow(table, 'spec_' + data['id']);
    if (table.rows.length % 2 == 1) newRow.className = 'alt';
    addTableCell(newRow, data['product_id']);
    addTableCell(newRow, data['product_name']);
    addTableCell(newRow, data['min_order']);
    addTableCell(newRow, '$' + data['product_onlineau']);
    addTableCell(newRow, '$' + data['product_online']);
    var newCell = addTableCell(newRow, '$' + data['special_priceau']);
    newCell.style.backgroundColor = '#7FFFD4';
    newCell = addTableCell(newRow, '$' + data['special_price']);
    newCell.style.backgroundColor = '#7FFFD4';
    addTableCell(newRow, '<input type="button" value=" Remove " onclick="removeSpecialPrice(\'' + data['return_message'] + '\',' + data['id'] + ');return false;" />');
}
function removeSpecialPrice(ret, id) {
    if (confirm("Are you sure you want to delete the Special Pricing??")) {
        var url = '/webadmin/member/members_special_pricing.php?action=do_delete&return_message=' + ret + '&id=' + id;
        var features = 'scrollbars, resizable, height=20, width=20';
        window.open(url, 'product_popup', features);
    }
}
function deleteSpecialPrice(name, row_id) {
    var table = getElement(name + '_spec');
    if (table == null) return;
    var theRow = getElement('spec_' + row_id);
    if (theRow) table.deleteRow(theRow.sectionRowIndex);
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionWebsitePreferences\'); return false;">Website Preferences</a>')?>

<div id="sectionWebsitePreferences">
<?php if (false) { ?><!--
    <table class="table_fields">
        <tr>
            <th>Best Described As :</th>
            <td style="width:75%;"><select name="member[best_described]">
                <option value=""></option>
                <?=formatHTMLOptions(memberGetOptionList("best_described"), $member['best_described'])?>
            </select></td>
        </tr>
        <tr>
            <th>Mailing List :</th>
            <td style="width:75%;"><select name="member[mailing_list]"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $member['mailing_list'])?></select></td>
        </tr>
        <tr>
            <th>Reseller :</th>
            <td style="width:75%;"><select name="member[reseller]"><?=formatHTMLOptions(array('y' => 'Yes', 'n' => 'No'), $member['reseller'])?></select></td>
        </tr>
    </table>
    <br />
--><?php } ?>
    <table class="table_grid"><tr>
    	<td style="vertical-align:top;width:256px;"><table style="width:100%;">
<?php
    $member_type        = explode(",", $member['site_id']);
    foreach (supplierGetOptionList("site_id") as $site_id => $site_name) {

        $site_last_invoice = array();
        $site_last_order = 'N/A';
        if (moduleLoad('invoice')) {
            $site_invoices = invoiceSort(invoiceSearch(array(
                'site_id' => $site_id,
                'invoice_status' => array('sales', 'proforma', 'processed'),
            )), 'open_date');
            $site_last_order = 'Never';
            if (count($site_invoices)) {
                $site_last_invoice = invoiceFromDatabase(reset($site_invoices));
                $site_last_order = date('d/m/Y', $site_last_invoice['open_time']);
            }
        }

        $site_last_login = 'N/A';
        if (false) { //TODO: Magic
            $site_last_login = 'Never';
            if (false) { //TODO: Magic
                $site_last_login = date('d/m/Y');
            }
        }

        $checked = in_array($site_id, $member_type);
?>
            <tr class="pref_row" id="site_pref_<?=$site_id?>"style="<?=($checked) ? 'background-color:#EEFFEE;' : ''?>">
                <td><input class="pref_check" type="checkbox" id="member_type_<?=$site_id?>" value="<?= $site_id ?>" name="member_type[<?=$site_id?>]" <?=($checked) ? 'checked' : ''?> /></td>
                <td>
                    <img src="/webadmin/images/sites/site_<?=$site_id?>/label.gif" alt="<?php hecho($site_name)?>" />
                    <!--<br />
                    <span id="discount_span_<?=$site_id?>"><?= (is_array($member_discounts) && array_key_exists($site_id, $member_discounts) && $member_discounts[$site_id]) ? sprintf("%d%% discount", $member_discounts[$site_id]) : '&nbsp;' ?></span>
                    <br />
                    <small><i><a class="view_link" style="<?=($checked) ? '' : 'display:none'?>" id="a_<?=$site_id?>" href="#">View</a></small></i>-->
                </td>
            </tr>
<?php
    }
?>
        </table></td>
        <td style="text-align:center;vertical-align:top;">
            <!-- <span id="message_first"><b>Click on "View" link to view the preferences for a particular site</b></span> -->

<?php
    //for ($site_id = 1;$site_id < 40;$site_id++) {
    foreach ($websites_list as $site_id => $site_name) {
        $fname = __DIR__ . '/website_preferences_' . $site_id . '.tpl.php';
        if (is_file($fname)) {
?>
            <div id="prefs_<?=$site_id?>" class="prefs_box" style="">
<?php
            include $fname;
?>
            </div>
<?php
        }
    }
?>

        </td>
    </tr></table>

    <br />
    <br />

</div>

<?php
}
?>

<!-- /website_preferences.tpl -->
