<!-- addedit.tpl.php -->

<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

if ('view' == $action) {
    $form_action = 'view';
}
?>

<script type="text/javascript">
//<![CDATA[

function prefsChecked(site_id, obj) {
    if (site_id != 'hg' && site_id != 'xbox') {
        getElement('site_' + site_id + '_a').style.display = (obj.checked) ? "" : 'none';
        getElement('site_' + site_id + '_prefs').style.display = 'none';
    }
}
function updateLoginName() {
    if (getElement('login[name]').value != getElement('member[email_address]').value)
        setValue('login[name]', getElement('member[email_address]').value);
}
function updateSuppleirName() {
    if (getElement('supplier[name]').value != getElement('suppliersupplier[email_address]').value)
        setValue('supplier[email_address]', getElement('login[name]').value);
}
//]]>
</script>
<form accept-charset="utf-8" autocomplete="off" method="post" action=".?action=<?=$form_action?>">
	<input type="hidden" name="supplier[supplier_id]" value="<?=$supplier_id?>" />
<?php
if ('view' != $action) {
    // need to display the changes that were made in the last 5 mins or so.
    if ('add' != $action) {
        $query = $DB->query(sprintf(
            "SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_notes` FROM `highwaygroup`.`new_members_messages` AS `mm` WHERE `mm`.`member_id` = %d AND `mm`.`message_date` > %s AND `mm`.`staff_id` = %d ORDER BY `mm`.`message_date` DESC",
            $member_id, formatSQLDateTime(time() - 255), $staff_id
        ));
        if ($DB->num_rows($query)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
            while ($message = $DB->fetch_assoc($query)) {
                $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
                $notes = explode(" ", $message['message_notes']);
                foreach ($notes as $key => $string) {
                    $notes[$key] = trim(chunk_split($string, 80));
                }

                $notes = implode(" ", $notes);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?=nl2br(hecho($notes, 1))?></li>
</div>
<br /><br />
<?php
            }
        }
    }
?>

<?php
    if ($supplier_lock['staff_id'] != $_GLOBALS["staff"]["id"] && 'add' != $action) {
?>
<div class="wide" style="border:1px solid black;font-weight:bold;background-color:pink;text-align:center;padding:8px;">
<?php
    if (false === $supplier_lock) {
        echo 'This supplier is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $supplier_lock['expiry_time'] - time();
        echo 'This supplier is currently locked to ' . $all_staff_list[$supplier_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
</div>
<br /><br />
<?php
    }
}
switch ($form_action) {
    case 'view':
    case 'do_edit':
        include __DIR__ . "/summary.tpl.php";
        include __DIR__ . "/details.tpl.php";
        include __DIR__ . "/contacts.tpl.php";
        include __DIR__ . "/shipping.tpl.php";
        include __DIR__ . "/bankdetail.tpl.php";
        include __DIR__ . "/supplierstock.tpl.php";
        include __DIR__ . "/_supplier_invoice_list.php";
        include __DIR__ . "/statement.tpl.php";
        include __DIR__ . "/orders.tpl.php";
        //include __DIR__ . "/inquiries.tpl.php";
        include __DIR__ . "/attachments.tpl.php";
        include __DIR__ . "/comments.tpl.php";
        include __DIR__ . "/requests.tpl.php";
        include __DIR__ . "/direct_email.tpl.php";
        include __DIR__ . "/direct_email_history.tpl.php";
        include __DIR__ . '/status.tpl.php';
        break;
    case 'do_add':
        include __DIR__ . "/details.tpl.php";
        include __DIR__ . "/contacts.tpl.php";
        include __DIR__ . "/shipping.tpl.php";
        include __DIR__ . "/bankdetail.tpl.php";
        include __DIR__ . "/comments.tpl.php";
        include __DIR__ . "/requests.tpl.php";
        include __DIR__ . "/direct_email.tpl.php";
        include __DIR__ . '/status.tpl.php';
        break;
    default:
        break;
}

if ("edit" == $action) {
?>
<table style="width:auto;margin:auto;">
	<tr>
		<td style="text-align:center;">
			<a href=".?action=supplier_detail_english&amp;supplier_id=<?= $supplier_id ?>" target="_blank" title="Open Supplier Detaail" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><img src="/webadmin/images/icon_shipto.png" alt=English /></a>
		</td>
		<td style="text-align:center;">
			<a href=".?action=supplier_detail_chinese&amp;supplier_id=<?= $supplier_id ?>" target="_blank" title="Open Supplier Detaail" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><img src="/webadmin/images/icon_notify.png" alt="Chinese" /></a>

		</td>
	</tr>
    <tr>
        <td style="text-align:center;">
            <span>English</span>
        </td>
        <td style="text-align:center;">
            <span>Chinese</span>
        </td>
    </tr>
</table>
<?php
}
?>
<br />

<?php
if ('view' != $action) {
?>
<p style="padding: 40px;">
<input type="submit" value="Submit Changes"<?=($supplier_lock['staff_id'] == $staff_id || 'add' == $action)?> style="margin-right: 68px; margin-left: 125px; width: 136px;"/>
<input type="button" value="Print Supplier Summary" onclick="window.open('.?action=view&amp;supplier_id=<?=$supplier_id?>')" />
<?php
}
?>
</p>
</form>
<!-- /addedit.tpl.php -->