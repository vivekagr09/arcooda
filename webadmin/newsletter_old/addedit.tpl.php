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
function validateSectionForm()
{
	var subject=getElement('<?=$section_name?>[subject]');
	if(subject!=null && subject.value=='')
	{
		alert("Please enter a subject");
		if(subject.select!=null) subject.select();
		if(subject.focus!=null) subject.focus();
		return false;
	}
	var desc=getElement('<?=$section_name?>[description]',false);
	if(desc!=null && desc.disabled!=true && desc.value=='')
	{
		alert("Please enter a description or choose another template");
		if(desc.select!=null) desc.select();
		if(desc.focus!=null) desc.focus();
		return false;
	}
	return true;
}
function sectionSubmitChanges()
{
	$('form#<?=$section_name?>_form').eq(0).each(function() {this.submit();});
}
function sectionPreview(theFrm,email)
{
	if (email)
	{
		var data=$(theFrm).serializeArray();
		data.push({'name':'send_copy','value':'true'});
		$.post('preview.php',data,function(data,textStatus) {
			alert(textStatus);
		});
	}
	else
	{
		var oldact=theFrm.action;
		var oldtrg=theFrm.target;
		theFrm.action="preview.php";
		theFrm.target="_blank";
		theFrm.submit();
		theFrm.action=oldact;
		theFrm.target=oldtrg;
	}
}
function sectionSendPreview()
{
	$('form#<?=$section_name?>_form').eq(0).each(function() {var action=this.action;var target=this.target;this.action='index.php?action=do_preview';this.target='_blank';this.submit();this.action=action;this.target=target;});
}
function sectionSendNewsletter()
{
	$('form#<?=$section_name?>_form').eq(0).attr('action','index.php?action=do_newsletter').each(function() {this.submit();});
}
//]]>
</script>

<form accept-charset="utf-8" id="<?=$section_name?>_form" method="post" action="index.php?action=<?=$form_action?>">
	<input type="hidden" name="<?=$section_name?>[id]" value="<?=$id?>" />
	<input type="hidden" name="<?=$section_name?>[site_id]" value="<?=$item['site_id']?>" />
	<input type="hidden" name="<?=$section_name?>[staff_id]" value="<?=$item['staff_id']?>" />
<?php
if ('view' != $action) {
    // need to display the changes that were made in the last 5 mins or so.
    if ('add' != $action) {
        $recent_list = $section_handler->commentSearch(array($section_name . '_id' => $id, 'comment_type' => 'sql'), time() - 255);
        if (count($recent_list)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
foreach ($recent_list as $message_id) {
                $message = $section_handler->commentFromDatabase($message_id);
                $shade   = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
                $notes   = explode(" ", $message['message_notes']);
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
if ($item_lock['staff_id'] != $staff_id && 'add' != $action) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $item_lock) {
            echo 'This newsletter is not locked to you; You cannot submit any changes.';
        } else {
            $time_to_live = $item_lock['expiry_time'] - time();
            echo 'This newsletter is currently locked to ' . $all_staff_list[$item_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
        }
?>
	</td></tr>
</table>
<br /><br />
<?php
}
}

switch ($form_action) {
    case 'view':
    case 'do_edit':
        include __DIR__ . "/_description.tpl.php";
        //include __DIR__ . "/_list_builder.tpl.php";
        include __DIR__ . "/_configure.tpl.php";
        include __DIR__ . "/_status.tpl.php";
        break;
    case 'do_add':
        include __DIR__ . "/_description.tpl.php";
        //include __DIR__ . "/_list_builder.tpl.php";
        include __DIR__ . "/_configure.tpl.php";
        break;
    default:
        break;
}

?>
<input type="hidden" name="<?=$section_name?>[subscr_list]" value="<?php hecho($item['subscr_list'])?>" />
<input type="hidden" name="<?=$section_name?>[country]" value="<?php hecho($item['country'])?>" />
<input type="hidden" name="<?=$section_name?>[best_described]" value="<?php hecho($item['best_described'])?>" />
<?php
if ('add' == $action) {
?>
<input type="hidden" name="<?=$section_name?>_list_rebuild" value="on" />
<?php
}
if ('view' != $action) {
?>
<input type="button" value="Save Changes"<?=($item_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validateSectionForm()) sectionSubmitChanges()"' : ' disabled="disabled"'?> />
<!-- <input type="button" value="Send a Preview to <?php hecho($_GLOBALS['staff']['email'])?>" onclick="sectionSendPreview()" /> -->
<input type="button" value="Preview" onclick="sectionPreview(this.form,false)" />
<input type="button" value="Send Newsletter"<?=($item_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(confirm(\'Are you sure you are ready to send this newsletter?\')) sectionSendNewsletter()"' : ' disabled="disabled"'?> />
<?php
if ('' != $_GLOBALS['staff']['email']) {
?>
<input type="button" value="Send Preview to '<?php hecho($_GLOBALS['staff']['email'])?>'" onclick="sectionPreview(this.form,true)" />
<?php
}
}
?>

</form>

<!-- /addedit.tpl.php -->