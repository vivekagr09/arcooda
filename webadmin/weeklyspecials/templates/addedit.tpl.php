<!-- addedit.tpl.php -->

<?php
$form_action = 'list';
if ($action == 'edit') $form_action = 'do_edit';
if ($action == 'add') $form_action = 'do_add';
if ($action == 'view') $form_action = 'view';
?>

<script type="text/javascript">
//<![CDATA[
function validateWeeklySpecial()
{
	var desc=getElement('weeklyspecial[description]');
	if(desc!=null && desc.value=='')
	{
		alert("Please enter a description");
		if(desc.select!=null) desc.select();
		if(desc.focus!=null) desc.focus();
		return false;
	}
	return true;
}
//]]>
</script>

<form method="post" action="index.php?action=<?= $form_action ?>">
	<input type="hidden" name="weeklyspecial[id]" value="<?= $id ?>" />
<?php
if($action!='view')
{
	// need to display the changes that were made in the last 5 mins or so.
	if ($action != 'add')
	{
		$recent_list=$section_handler->commentSearch(array('weeklyspecial_id'=>$id,'comment_type'=>'sql'),time()-255);
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
<div style="color:#FF<?= $shade . $shade ?>;text-align:left;width:95%;">
	<li><?= nl2br(htmlspecialchars($notes)) ?></li>
</div>
<br /><br />
<?php
			}
		}
	}
?>

<?php
	if ($weeklyspecial_lock['staff_id'] != $staff_id && $action != 'add')
	{
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
		if ($weeklyspecial_lock === false) echo 'This weeklyspecial is not locked to you; You cannot submit any changes.';
		else
		{
			$time_to_live = $weeklyspecial_lock['expiry_time'] - time();
			echo 'This weeklyspecial is currently locked to ' . $all_staff_list[$weeklyspecial_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
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
	break;
case 'do_add':
	include "templates/description.tpl.php";
	break;
default:
	break;
}

if($action!='view')
{
?>
<input type="button" value="Submit Changes"<?= ($weeklyspecial_lock['staff_id'] == $staff_id || $action == 'add') ? ' onclick="javascript:if(validateWeeklySpecial()) this.form.submit();"' : ' disabled="disabled"' ?> />
<?php
}
?>

</form>

<!-- /addedit.tpl.php -->