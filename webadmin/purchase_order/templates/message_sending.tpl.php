<!-- message_sending.tpl -->

<?php
//if (!moduleLoad("template")) return;

$invoice_labels  = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
$email_templates = invoiceEmailTemplatesList();
$templater       = new Template();
$vars            = invoiceTemplateVars($invoice);
foreach ($vars as $key => $value) {
    if (trim($value) == '') {
        unset($vars[$key]);
    }
}

if ($invoice['site_id'] < 1) {
    foreach (getTableColumns('`highwaygroup`.`company_info`') as $key) {
        unset($vars['[' . strtoupper($key) . ']']);
    }
}

if ($invoice_id < 1) {
    unset($vars['[INVOICE_NUMBER]']);
}

$vars['[STAFF_NAME]'] = "Jody Luo"; // Always Jody
$templater->set_vars($vars);
foreach ($email_templates as $template_id => $template) {
    foreach ($template as $key => $value) {
        $templater->set_contents($value);
        $email_templates[$template_id][$key] = $templater->apply();
    }
}

$email_names    = array();
$email_subjects = array();
$email_bodies   = array();
foreach ($email_templates as $template) {
    $email_names[]    = $template['template_name'];
    $email_subjects[] = $template['template_subject'];
    $email_bodies[]   = $template['template_text'];
}
$invoice['email_message_customer'] = reset($email_bodies);
function email_row($ecount = '[EMAIL_COUNT]', $contact = array('email' => '[EMAIL_ADDRESS]'))
{
    ob_start();
?>
	<tr name="email_row[<?= $ecount ?>]">
		<td class="left_title">eMail To:</td>
		<td><input type="checkbox" name="flag_email_customer[<?= $ecount ?>]" /> <select name="flag_email_bcc[<?= $ecount ?>]" style="width:auto;"><option value="cc">CC</option><option value="bcc">BCC</option></select> <input type="hidden" name="email_customer[<?= $ecount ?>]" value="<?php hecho($contact['email'])?>" /> <input type="hidden" name="send_email_cc[<?= $ecount ?>]" value="<?php hecho($contact['send_email_cc'])?>" /> <?php hecho($contact['name'])?> &lt;<?php hecho($contact['email'])?>&gt;</td>
	</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var firstEmail = 1;
var lastEmail = <?= max(1, (count($member_contacts) + 1)) ?>;

function addContactEmail(data)
{
	lastEmail++;
	var ecount = String(lastEmail);
	var email_table = getElement("email_table");
	if (email_table == null) return;

	var email_address = data['email'];
	if (email_address == null) email_address = data['email_address'];
	if (email_address == null) return;
	var full_name = '';
	if (data['first_name'] != null) full_name = data['first_name'];
	var send_email_cc = 'y';
	if (data['send_email_cc'] != null) send_email_cc = data['send_email_cc'];

	var newRow1 = addTableRow(email_table, 'email_row[' + ecount + ']', 1);
<?php
$count   = 0;
$replace = array(
    "[EMAIL_COUNT]"   => '" + ecount + "',
    "[EMAIL_ADDRESS]" => '" + email_address + "',
    "[EMAIL_NAME]"    => '" + full_name + "',
    '[SEND_EMAIL_CC]' => '" + send_email_cc + "',
);
foreach (explode("\n", email_row('[EMAIL_COUNT]', array(
    'email'         => '[EMAIL_ADDRESS]',
    'name'          => '[EMAIL_NAME]',
    'send_email_cc' => '[SEND_EMAIL_CC]',
))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "	addTableCell(newRow" . $count . ", " . str_replace(array_keys($replace), array_values($replace), jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 23) == '<td class="left_title">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace), array_values($replace), jecho(substr($row, 23, -5), 1)) . ").className='left_title';\n";
    }

}
?>
}

function clearContactEmails()
{
	for (var i = firstEmail;i <= lastEmail;i++)
	{
		var theCheck = getElement('flag_email_customer[' + i + ']');
		if (theCheck != null) theCheck.checked = false;
		var theRow = getElement("email_row[" + i + "]");
		if (theRow != null) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
	firstEmail = lastEmail;
}

var autoTick = true;
var textChange = false;
function emailChangeTemplate(new_id)
{
	var template_subjects = <?php jecho(array_values($email_subjects))?>;
	var templates = <?php jecho(array_values($email_bodies))?>;

	if (autoTick)
	{
		var checkbox = getElement('flag_email_customer[1]');
		if (checkbox != null) checkbox.checked = true;
		for(var i = firstEmail;i <= lastEmail;i++)
		{
			var checkbox = getElement('flag_email_customer[' + String(i) + ']');
			var auto_check = getValue('send_email_cc[' + String(i) + ']');
			if(checkbox != null && auto_check != 'n') checkbox.checked = true;
		}
		autoTick = false;
	}

	if (template_subjects[new_id] != null)
	{
		var subject = template_subjects[new_id];

		var productnames = document.getElementsByClassName('invoice_product_name');

		if (productnames.length > 0) {
			subject = subject.replace(/\[FIRST_PRODUCT\]/, productnames[0].value);
		}

		//setValue('email_title_customer', template_subjects[new_id]);
		setValue('email_title_customer', subject);
		setValue('email_message_customer', templates[new_id]);
		var msgbox = getElement('email_message_customer');
		if (msgbox != null) textareaAutoSize(msgbox, 1, 10);
	}
	textChange = true;
}

function checkEmailAccountSelected()
{
	var obj_selected = false;
	if (textChange && getValue('email_message_customer') != '')
	{
		var first = getElement('flag_email_customer[0]');
		var checkbox = first;
		if (checkbox != null && checkbox.checked) obj_selected = true;
		for (var i = firstEmail;i <= lastEmail;i++)
		{
			checkbox = getElement('flag_email_customer[' + String(i) + ']');
			if (checkbox != null && checkbox.checked) obj_selected = true;
		}
		if (!obj_selected)
		{
			alert('Please select an email address to send the email to');
			if (first.select) first.select();
			if (first.focus) first.focus();
		}
		return obj_selected;
	}
	return true;
}

function textChanged(value)
{
	if (value != '') textChange = true;
}

$(function() {
	var $button = $("<a href=\"#\">Add</a>").click(function() {
		lastEmail++;
		$("[name='flag_attachments_customer']").closest("tr").before("<tr><td class=\"left_title\">eMail To:</td><td><input type=\"checkbox\" name=\"flag_email_customer[" + lastEmail + "]\" /> <select name=\"flag_email_bcc[" + lastEmail + "]\" style=\"width:auto;\"><option value=\"cc\">CC</option><option value=\"bcc\">BCC</option></select> <input type=\"text\" name=\"email_customer[" + lastEmail + "]\" size=\"48\" value=\"\" /></td></tr>");
		return false;
	});
	$("[name='flag_email_customer[0]']").closest("td").append($button);
});
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMemberNotes\');return false">Email to Supplier</a>', '<a href="#" onclick="editMember();return false">Add Member Email</a>') ?>
<div id="sectionMemberNotes">
<table class="wide" id="email_table">
	<tr>
		<td class="left_title" style="width:25%;">Email Templates:</td>
		<td>
			<select onchange="emailChangeTemplate(this.value);" style="width:80%;">
				<option value=""></option>
				<?= formatHTMLOptions($email_names, -1) ?>
			</select>
			<a href="invoice_options.php?section=email_template" target="_blank">Add / Edit</a>
		</td>
	</tr>
<?php
$ecount = 1;
if ('' != $invoice['bill_email']) {
    echo email_row($ecount++, array('email' => $invoice['bill_email'], 'send_email_cc' => 'y'));
}

foreach ($member_contacts as $contact) {
    if ($contact['email'] != $invoice['bill_email'] && '' != $contact['email']) {
        echo email_row($ecount++, array('email' => $contact['email'], 'send_email_cc' => $contact['send_email_cc']));
    }
}

?>
	<tr>
		<td class="left_title" style="width:25%;">eMail To:</td>
		<td>
			<input type="checkbox" name="flag_email_customer[0]" />
			<select name="flag_email_bcc[0]" style="width:auto;">
				<option value="cc">CC</option>
				<option value="bcc">BCC</option>
			</select>
			<input type="text" name="email_customer[0]" size="48" value="" />
		</td>
	</tr>
<?php
if ('edit' == $action) {
?>
	<tr>
		<td class="left_title">Send Attachments:</td>
		<td><input type="checkbox" name="flag_attachments_customer" /></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title">Attach Purchase Order:</td>
		<td><input type="checkbox" name="flag_quotation_customer" /></td>
	</tr>
<?php
if (shipinvoiceExists($invoice_id)) {
?>
	<tr>
		<td class="left_title">Attach Dummy Shipping Invoice :</td>
		<td><input type="checkbox" name="flag_dummy_customer" /></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title">Subject</td>
		<td><input type="text" name="email_title_customer" style="width:98%;" value="" /></td>
	</tr>
	<tr>
		<td class="left_title">Message</td>
		<td><textarea name="email_message_customer" rows="<?= max(10, count(explode("\n", $invoice['email_message_customer']))) ?>" onkeyup="textareaAutoSize(this,1,10);" onchange="if(this.value!='') emailChangeTemplate(); textChanged(this.value);"><?= $invoice['email_message_customer'] ?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Include Message From Supplier In Message To Supplier</td>
		<td><input type="checkbox" name="flag_reply_customer" /></td>
	</tr>
	<tr>
		<td class="left_title">Received From Supplier<br /><span style="font-size:9px;color:red;font-weight:normal;">Paste the email received from the Supplier here</span></td>
		<td><textarea name="email_received_customer" rows="10" onkeyup="textareaAutoSize(this,1,10);"></textarea></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberNotes_hideNote">
	Member Notes can be viewed by Clicking "Member Notes" above<br />
</div>



<script type="text/javascript">
//<![CDATA[
var short_staff_list_options = <?php jecho(formatHTMLOptions($active_staff_list, "", false))?>;
function addStaffEmail() {
	var table = getElement('staff_email');
	if (table === null || !table) return false;
	var newRow = addTableRow(table, '');
	var newCell = addTableCell(newRow, 'Message To');
	newCell.className = 'left_title';
	newCell = addTableCell(newRow, '<select name="email_target_staff[]"><option value=""></option>' + short_staff_list_options + '</select>');
	return false;
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMessageStaff\');">Message To Staff</a>', '<a href="#" onclick="return addStaffEmail()">Add Staff Email</a>') ?>
<div id="sectionMessageStaff">
<table class="wide">
	<tbody id="staff_email">
		<tr>
			<td class="left_title" style="width:25%;">Message To</td>
			<td><select name="email_target_staff[]">
				<option value=""></option>
				<?= formatHTMLOptions($active_staff_list) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Message To</td>
			<td><select name="email_target_staff[]">
				<option value=""></option>
				<?= formatHTMLOptions($active_staff_list) ?>
			</select></td>
		</tr>
	</tbody>
	<tr>
		<td class="left_title">Message<br /><span style="font-size:9px;color:red;font-weight:normal;">This message will be sent by email to the staff member(s) selected</span></td>
		<td><textarea name="email_message_staff" rows="8" onkeyup="textareaAutoSize(this,1,8);"></textarea></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMessageStaff_hideNote">
	Messages to Staff section can be viewed by Clicking "Message To Staff" above<br />
</div>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdminNotes\');">Administration Notes</a>') ?>
<div id="sectionAdminNotes">
<table class="wide">
	<tr><td class="left_title" style="width:25%;">Message<br /><span style="font-size:9px;color:red;font-weight:normal;">Admin notes are never displayed on the main site</span></td><td><textarea name="admin_notes" rows="8" onkeyup="textareaAutoSize(this,1,8);"></textarea></td></tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdminNotes_hideNote">
	All Administration Notes can be viewed by Clicking "Administration Notes" above<br />
</div>

<!-- /message_sending.tpl -->
