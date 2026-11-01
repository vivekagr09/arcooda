<!-- members_contacts.tpl.php -->

<?php
function contact_row($count = '[ROW_COUNT]', $contact = array())
{
	$contact = array_merge(memberContactDefault(), $contact);
    $hidden  = array(
		'contacts[' . $count . '][id]' => (array_key_exists('id', $contact)) ? $contact['id'] : '',
	);
    ob_start();
?>
<tr id="contact_rowa[<?=$count?>]">
	<td class="left_title" rowspan="6" style="width:5%">
		<?=formatHTMLInputs($hidden)?>
		<input onclick="contactSelected('[<?=$count?>]')" type="checkbox" value="y" name="contacts[<?=$count?>][main]"<?=('y' == $contact['main']) ? ' checked="checked"' : ''?> /><br />
		<span id="default_str[<?=$count?>]" style="font-size:10px;<?=('y' == $contact['main']) ? '' : 'display:none'?>">Default</span>
	</td>
	<td class="left_title quarter">First Name :</td>
	<td style="width:65%"><input style="width:99%" type="text" name="contacts[<?=$count?>][name]" value="<?=htmlspecialchars($contact['name'])?>" /></td>
	<td class="left_title" rowspan="6" style="width:5%"><input type="button" value="Delete" onclick="deleteContactRow(<?=$count?>)" /></td>
</tr>
<tr id="contact_rowg[<?=$count?>]">
	<td class="left_title quarter">Last Name :</td>
	<td style="width:65%"><input style="width:99%" type="text" name="contacts[<?=$count?>][last_name]" value="<?=htmlspecialchars($contact['last_name'])?>" /></td>
</tr>
<tr id="contact_rowb[<?=$count?>]" style="text-align:center">
	<td class="left_title quarter">Title :</td>
	<td style="width:65%"><input style="width:99%" type="text" name="contacts[<?=$count?>][title]" value="<?=htmlspecialchars($contact['title'])?>" /></td>
</tr>
<tr id="contact_rowc[<?=$count?>]" style="text-align:center">
	<td class="left_title quarter">Email Address :</td>
	<td style="width:65%"><input style="width:99%" type="text" name="contacts[<?=$count?>][email]" value="<?=htmlspecialchars($contact['email'])?>" /></td>
</tr>
<tr id="contact_rowd[<?=$count?>]" style="text-align:center">
	<td class="left_title quarter">Phone :</td>
	<td style="width:65%"><input style="width:99%" type="text" name="contacts[<?=$count?>][phone]" value="<?=htmlspecialchars($contact['phone'])?>" /></td>
</tr>
<tr id="contact_rowe[<?=$count?>]" style="text-align:center">
	<td class="left_title quarter">Send Emails to this address :</td>
	<td style="width:65%"><select style="width:99%" name="contacts[<?=$count?>][send_email_cc]"><?=formatHTMLOptions(array(
		'y' => 'Yes',
		'n' => 'No',
	), $contact['send_email_cc'])?></select></td>
</tr>
<tr id="contact_rowf[<?=$count?>]" style="text-align:center">
	<td colspan="4" class="left_title"><hr size="1" style="background-color:black;color:black;height:1px" /></td>
</tr>
<?php
    return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var contact_count = <?=max(100, count($member_contacts))?>;
function addContactRow()
{
	$("#contact_table").append(<?=json_encode(contact_row('[ROW_COUNT]'))?>.replace(/\[ROW_COUNT\]/g, contact_count));

	contact_count++;
}
function deleteContactRow(row_id)
{
	if (confirm('Are you sure you want to remove this contact??')) {
		$("tr[id='contact_rowa[" + row_id + "]']").remove();
		$("tr[id='contact_rowb[" + row_id + "]']").remove();
		$("tr[id='contact_rowc[" + row_id + "]']").remove();
		$("tr[id='contact_rowd[" + row_id + "]']").remove();
		$("tr[id='contact_rowe[" + row_id + "]']").remove();
		$("tr[id='contact_rowf[" + row_id + "]']").remove();
		$("tr[id='contact_rowg[" + row_id + "]']").remove();
	}
}
function getContactRows()
{
	var results = [];
	$("tr[id^='contact_rowa[']").each(function() {
		results.push(this.id.substr(12));
	});
	return results;
}
function contactSelected(row_id)
{
	$(getContactRows()).each(function() {
		var test_id = this;
		$("[id='default_str" + test_id + "']").toggle(test_id == row_id);
		$("[name='contacts" + test_id + "[main]']").each(function() {
			this.checked = (test_id == row_id);
		});
	});
}
function updateContactName(row_id)
{
	$("[name='contacts[" + row_id + "][name]']").val($("[name='member[first_name]']").val() + " " + $("[name='member[last_name]']").val());
	$("[name='contacts[" + row_id + "][email]']").val($("[name='member[email_address]']").val());
	$("[name='contacts[" + row_id + "][phone]']").val($("[name='member[phone_no]']").val());

	$("[name='member_authentication[0][email_address1]']").val($("[name='member[email_address]']").val());
}
//]]>
</script>

<?=formatHTMLTitle(
	'<a href="#" onclick="toggleSection(\'sectionContacts\');return false">Customer Contacts</a>',
	'<a href="#" onclick="addContactRow();return false">Add New</a>'
)?>

<div id="sectionContacts"><table class="wide" id="contact_table">
<?php
// if no contacts, then add one based on member's details...
if (count($member_contacts) == 0) {
    $member_contacts[$member_id] = array(
    	'id' => 'abc123',
		'member_id' => $member_id,
		'name'      => $member['first_name'],
		'last_name' => $member['last_name'],
		'phone'     => $member['phone_no'],
		'email'     => $member['email_address'],
		'main'      => 'y',
	);
}

$affected = array();
foreach ($member_contacts as $contact) {
    $affected[] = $contact['id'];
    echo contact_row($contact['id'], $contact);
}
?>
	<input type="hidden" name="contacts_affected" value="<?=implode('|', $affected)?>" />
</table></div>

<br />
