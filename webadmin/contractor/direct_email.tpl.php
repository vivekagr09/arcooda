<!-- direct_email.tpl -->

<?php
$direct_email_addresses = array();
foreach (array($contractor, $contractor_contacts, $contractor_notify, $contractor_shipping) as $v) {
    if (is_array($v)) {
        foreach (array('email_address') as $k) {
            if (array_key_exists($k, $v) && '' != $v[$k] && !in_array($v[$k], $direct_email_addresses)) {
                $direct_email_addresses[] = $v[$k];
            }
        }
    }
}

$vars = contractorTemplateVars($contractor);
foreach ($vars as $k => $v) {
    if (trim($v) == '') {
        unset($vars[$k]);
    }
}
$vars['[STAFF_NAME]'] = "Jody Luo"; // Always Jody

$direct_email_templates = array();
$direct_email_templates_list = array();
foreach (contractorEmailTemplatesList() as $k => $v) {
    $direct_email_templates_list[$k] = $v['template_name'];
    $direct_email_templates[$k] = array(
        'subject' => strtr($v['template_subject'], $vars),
        'message' => strtr($v['template_text'], $vars)
    );
}

$direct_email_template_id = -1;
$direct_email_subject = '';
$direct_email_message = '';
foreach ($direct_email_templates as $k => $v) {
    $direct_email_template_id = $k;
    $direct_email_subject = $v['subject'];
    $direct_email_message = $v['message'];
    break;
}

function email_row($ecount = '[EMAIL_COUNT]', $contact = array())
{
    ob_start();
?>
	<tr>
		<th>eMail To :</th>
		<td>
            <input type="checkbox" name="direct_email[<?= $ecount ?>][enabled]" />
            <select name="direct_email[<?= $ecount ?>][method]" style="width:auto;">
                <option value="Cc:">CC</option>
                <option value="Bcc:">BCC</option>
            </select>
<?php
    if (array_key_exists('email', $contact)) {
?>
            <input type="hidden" name="direct_email[<?= $ecount ?>][email_address]" value="<?php hecho($contact['email'])?>" />
            <?php hecho($contact['name'])?> &lt;<?php hecho($contact['email'])?>&gt;
<?php
    } else {
?>
            <input type="text" name="direct_email[<?= $ecount ?>][email_address]" value="" style="width:80%;" />
            <a class="direct_email_add" href="#" style="display:none;">Add</a>
<?php
    }
?>
        </td>
	</tr>
<?php
    return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    var direct_email_max = <?= count($direct_email_addresses) ?>;
    var direct_email_templates = <?php jecho($direct_email_templates) ?>;

    $(".direct_email_add").click(function() {
        direct_email_max++;
        $(this).closest('tr').after(<?= strtr(jecho(email_row(), 1), array(
            '[EMAIL_COUNT]' => '" + direct_email_max + "',
        )) ?>);
        return false;
    }).toggle(true);
    $(".direct_email_template_id").change(function() {
        $("[name='direct_email[subject]']").val(direct_email_templates[$(this).val()].subject);
        $("[name='direct_email[message]']").val(direct_email_templates[$(this).val()].message);
    });
})
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDirectEmail\');return false">Direct Email</a>') ?>

<div id="sectionDirectEmail">
    <table class="table_fields">
        <tr>
            <th>Email Templates :</th>
            <td>
                <select class="direct_email_template_id" style="width:80%;">
                    <option value="-1"></option>
                    <?= formatHTMLOptions($direct_email_templates_list, $direct_email_template_id) ?>
                </select>
                <a href=".?action=options&section=email_template" target="_blank">Add / Edit</a>
            </td>
        </tr>
<?php
if ('mass_email' !== $action) {
    foreach ($direct_email_addresses as $k => $v) {
        echo email_row($k, array('email' => $v));
    }
    echo email_row(-1);
?>
	<tr>
		<th>Make Inquiry :</th>
		<td><div class="unfinished"><input type="checkbox" name="direct_email[make_inquiry]" /></div></td>
	</tr>
<?php
}
?>
	<tr>
		<th>Subject :</th>
		<td><input type="text" name="direct_email[subject]" class="wide" value="<?php hecho($direct_email_subject) ?>" /></td>
	</tr>
	<tr>
		<th>Message :</th>
		<td><textarea name="direct_email[message]" rows="<?= max(10, substr_count(wordwrap($direct_email_message), "\n") + 3) ?>" onkeyup="textareaAutoSize(this, 1, 10);"><?php hecho($direct_email_message) ?></textarea></td>
	</tr>
</table>
<br /><br />

<?php
if ('mass_email' === $action) {
?>
<input type="button" value="Send Email" onclick="if (validateMassMail()) this.form.submit()" />
<br />
<br />
<?php
} elseif ('add' === $action || 'edit' === $action) {
?>
<input type="button" value="Send Email"<?=($contractor_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validatecontractor()) this.form.submit()"' : ' disabled="disabled"'?> />
<br />
<br />
<?php
}
?>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionDirectEmail_hideNote">
	Direct Email can be viewed by Clicking "Direct Email" above<br />
</div>

<!-- /direct_email.tpl -->
