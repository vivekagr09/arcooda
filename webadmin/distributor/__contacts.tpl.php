<!-- contacts.tpl -->

<?php
function item_contact_row($count = '[ROW_COUNT]', $item_contact = array())
{
    global $id;

    $item_contact = array_merge(distributorContactDefault(), $item_contact);

    ob_start();
?>
<table name="item_contacts_rowa[<?=$count?>]" class="table_fields wide">
    <tr>
        <th rowspan="5" style="width:112px;text-align:center;vertical-align:middle">
            <input type="hidden" name="item_contacts[<?=$count?>][id]" value="<?=htmlspecialchars($item_contact['id'])?>" />
            <input type="hidden" name="item_contacts[<?=$count?>][distributor_id]" value="<?=$id?>" />
            <input type="hidden" name="item_contacts[<?=$count?>][main]" value="n" />
            <input type="checkbox" name="item_contacts[<?=$count?>][main]" value="y" onclick="itemContactSelect(<?=$count?>)"<?=('y' == $item_contact['main']) ? ' checked="checked"' : ''?> /><br />
            <small name="item_contacts[<?=$count?>][main_span]"<?=('y' == $item_contact['main']) ? '' : ' style="display:none"'?>>Default</small>
        </th>
        <th style="width:25%">Contact Name :</th>
        <td style="width:auto"><input class="wide" type="text" name="item_contacts[<?=$count?>][name]" value="<?=htmlspecialchars($item_contact['name'])?>" /></td>
        <th rowspan="5" style="width:112px;text-align:center;vertical-align:middle">
            <a href="#" onclick="itemContactDelete(<?=$count?>);return false">Delete</a>
        </th>
    </tr>
    <tr>
        <th>Title :</th>
        <td><input class="wide" type="text" name="item_contacts[<?=$count?>][title]" value="<?=htmlspecialchars($item_contact['title'])?>" /></td>
    </tr>
    <tr>
        <th>Email Address :</th>
        <td><input class="wide" type="text" name="item_contacts[<?=$count?>][email]" value="<?=htmlspecialchars($item_contact['email'])?>" /></td>
    </tr>
    <tr>
        <th>Phone :</th>
        <td><input class="wide" type="text" name="item_contacts[<?=$count?>][phone]" value="<?=htmlspecialchars($item_contact['phone'])?>" /></td>
    </tr>
    <tr>
        <th>Emails sent to this address :</th>
        <td><select class="wide" name="item_contacts[<?=$count?>][send_email_cc]">
            <?=formatHTMLOptions(['y' => 'Yes', 'n' => 'No'], $item_contact['send_email_cc'])?>
        </select></td>
    </tr>
</table>
<?php
    return ob_get_clean();
}

if (isset($item_contacts) && is_array($item_contacts)) {
?>

<script type="text/javascript">
//<![CDATA[
var contacts = 100;

function itemContactAdd()
{
    $("#item_contacts_table").append(<?=strtr(json_encode(item_contact_row()), ["[ROW_COUNT]" => '" + contacts + "'])?>);

    let rows = DistributorContact.rows();
    if (1 == rows.length) {
        itemContactSelect(rows[0]);
    }

    contacts++;
}
function itemContactDelete(row)
{
    if (!confirm('Are You Sure you want to delete this contact??')) {
        return;
    }

    $("#item_contacts_table").append("<input type=\"hidden\" name=\"item_contacts_deleted[]\" value=\"" + DistributorContact.get("id", row) + "\" />");

    DistributorContact.delete(row);

    let rows = DistributorContact.rows();
    if (rows.length) {
        itemContactSelect(rows[0]);
    }
}
function itemContactSelect(row) {
    var con_rows = DistributorContact.rows();
    $(DistributorContact.selector('main')).each(function() {
        if ('checkbox' != this.type) {
            return;
        }
        this.checked = (row == DistributorContact.row(this));
    });
    $(DistributorContact.selector('main_span')).each(function() {
        this.style.display = (row == DistributorContact.row(this)) ? 'inline' : 'none';
    });
}
//]]>
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(\'sectionDistributorContacts\');return false">Distributor Contacts</a>',
    '<a style="color:black;" href="#" onclick="itemContactAdd();return false">Add New Contact</a>'
)?>

<div id="sectionDistributorContacts">
	<div id="item_contacts_table">
<?php
    $count = 0;
    foreach ($item_contacts as $contact) {
        $count++;
        echo item_contact_row($count, $contact);
    }
?>
	</div>
	<br />
    <br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionDistributorContacts_hideNote">Company Contacts can be viewed by Clicking "Company Contacts" above</div>

<?php
}
?>

<!-- /contacts.tpl -->
