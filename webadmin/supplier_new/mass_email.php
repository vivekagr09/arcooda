<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'mass_email';
?>
<h2>Mass Email System</h2>

<form accept-charset="utf-8" autocomplete="off" method="post" action=".?action=do_mass_email">

<script type="text/javascript">
//<![CDATA[
function validateMassMail() {
    var email_count = $("[name='mass_email_member_list']").val().split(/[^0-9]+0*/).length;

    if (!confirm("Are you sure you want to send " + email_count + " emails?"))
        return false;

    return true;
}
$(function() {
    $("[name='mass_email_member_list']").change(function() {
        var member_list = $(this).val().split(/[^0-9]+0*/).filter((value, index, self) => {
            return self.indexOf(value) === index;
        });
        member_list.sort((a, b) => parseInt(a) - parseInt(b));
        if (member_list[0] === "") member_list.shift();
        $(this).val(member_list.join("\t"));
    });
});
//]]>
</script>

<table class="table_fields">
    <tr>
        <th>Member ID :</th>
        <td><?=formatHTMLTextarea('mass_email_member_list', '', false, ' class="wide"', 20)?></td>
    </tr>
    <tr>
        <th>Send Options :</th>
        <td>
            <input id="mass_email_as_direct_email" type="checkbox" name="mass_email_as_direct_email" value="true" checked="checked" /> <label for="mass_email_as_direct_email">Copy of the email in the member's ID</label><br />
            <input id="mass_email_check_member" type="checkbox" name="mass_email_check_member" value="true" /> <label for="mass_email_check_member">Member Checked Ticked</label><br />
        </td>
    </tr>
</table>
<?php

include __DIR__ . '/direct_email.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
