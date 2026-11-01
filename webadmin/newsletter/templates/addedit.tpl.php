<!-- addedit.tpl.php -->

<?php
$form_action = 'list';
if ($action == 'edit') $form_action = 'do_edit';
if ($action == 'add') $form_action = 'do_add';
if ($action == 'view') $form_action = 'view';
?>

<script type="text/javascript">
//<![CDATA[
function validateSectionForm()
{
	var subject=getElement('<?php echo $section_name ?>[subject]');
	if(subject!=null && subject.value=='')
	{
		alert("Please enter a subject");
		if(subject.select!=null) subject.select();
		if(subject.focus!=null) subject.focus();
		return false;
	}
	var desc=getElement('<?php echo $section_name ?>[description]',false);
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
	$('form#<?php echo $section_name ?>_form').eq(0).each(function() {this.submit();});
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
function sectionSpamCheck(theFrm)
{
	var data = $(theFrm).serializeArray();
	$.post('spamcheck.php', data, function(data, textStatus) {
		var msg = [
			"Potential Spam Rating: " + data[0],
			"",
			"Reasons:",
			"-1.9 Well configured mail server",
		];
		for (var i = 0;i < data[1].length;i++) msg.push(data[1][i][0] + " " + data[1][i][2] + " (" + data[1][i][1] + ")");
		alert(msg.join("\n"));
	});
}
function sectionSendPreview()
{
	$('form#<?php echo $section_name ?>_form').eq(0).each(function() {var action=this.action;var target=this.target;this.action='index.php?action=do_preview';this.target='_blank';this.submit();this.action=action;this.target=target;});
}
function sectionSendNewsletter()
{
	$('form#<?php echo $section_name ?>_form').eq(0).attr('action','index.php?action=do_newsletter').each(function() {this.submit();});
}
//]]>
</script>

<form id="<?php echo $section_name ?>_form" method="post" action="index.php?action=<?php echo $form_action ?>">
	<input type="hidden" name="<?php echo $section_name ?>[id]" value="<?php echo $id ?>" />
	<input type="hidden" name="<?php echo $section_name ?>[site_id]" value="<?php echo $item['site_id'] ?>" />
	<input type="hidden" name="<?php echo $section_name ?>[staff_id]" value="<?php echo $item['staff_id'] ?>" />
<?php
if($action!='view')
{
	// need to display the changes that were made in the last 5 mins or so.
	if ($action != 'add')
	{
		$recent_list=$section_handler->commentSearch(array($section_name.'_id'=>$id,'comment_type'=>'sql'),time()-255);
		if (count($recent_list))
		{
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
			foreach($recent_list as $message_id)
			{
				$message=$section_handler->commentFromDatabase($message_id);
				$shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
				$notes = explode(" ", $message['message_notes']);
				foreach($notes as $key => $string) $notes[$key] = trim(chunk_split($string, 80));
				$notes = implode(" ", $notes);
?>
<div style="color:#FF<?php echo $shade . $shade ?>;text-align:left;width:95%;">
	<li><?php echo nl2br(formatHTMLSafe($notes)) ?></li>
</div>
<br /><br />
<?php
			}
		}
	}
?>

<?php
	if ($item_lock['staff_id'] != $staff_id && $action != 'add')
	{
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
		if ($item_lock === false) echo 'This newsletter is not locked to you; You cannot submit any changes.';
		else
		{
			$time_to_live = $item_lock['expiry_time'] - $_SERVER['REQUEST_TIME'];
			echo 'This newsletter is currently locked to ' . $all_staff_list[$item_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
		}
?>
	</td></tr>
</table>
<br /><br />
<?php
	}
}

switch ($form_action)
{
case 'view':
case 'do_edit':
	include "templates/description.tpl.php";
	//include "templates/list_builder.tpl.php";
	include "templates/configure.tpl.php";
	include "templates/status.tpl.php";
	break;
case 'do_add':
	include "templates/description.tpl.php";
	//include "templates/list_builder.tpl.php";
	include "templates/configure.tpl.php";
	break;
default:
	break;
}

?>
<div class="checkTemplate" style="display:block;">
<input type="hidden" name="<?php echo $section_name ?>[subscr_list]" value="<?php hecho($item['subscr_list']) ?>" />
<input type="hidden" name="<?php echo $section_name ?>[country]" value="<?php hecho($item['country']) ?>" />
<input type="hidden" name="<?php echo $section_name ?>[address_type]" value="<?php hecho($item['address_type']) ?>" />
<?php
if($action=='add')
{
?>
<input type="hidden" name="<?php echo $section_name ?>_list_rebuild" value="on" />
<?php
}
if($action!='view')
{
?>
<input type="button" value="Save Changes"<?php echo ($item_lock['staff_id'] == $staff_id || $action == 'add') ? ' onclick="if(validateSectionForm()) sectionSubmitChanges()"' : ' disabled="disabled"' ?> />
<!-- <input type="button" value="Send a Preview to <?php hecho($_GLOBALS['staff']['email']) ?>" onclick="sectionSendPreview()" /> -->
<input type="button" value="Preview" onclick="sectionPreview(this.form,false)" />
<?php
	if($_GLOBALS['staff']['email'] != '')
	{
?>
<input type="button" value="Send Preview to '<?php hecho($_GLOBALS['staff']['email']) ?>'" onclick="sectionPreview(this.form,true)" />
<?php
	}
?>
<input type="button" value="Preview Spam Rating" onclick="sectionSpamCheck(this.form)" />
<br /><br />
<input style="color:#080;font-weight:bold;" type="button" value="Send Newsletter"<?php echo ($item_lock['staff_id'] == $staff_id || $action == 'add') ? ' onclick="if(confirm(\'Are you sure you are ready to send this newsletter?\')) sectionSendNewsletter()"' : ' disabled="disabled"' ?> />
<?php
}
?>
</div>
</form>

<!-- /addedit.tpl.php -->