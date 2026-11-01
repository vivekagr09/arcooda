<div align="center">
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMessageStaff\');">Message To Staff</a>') ?>
</div>
<div id="sectionMessageStaff">
<table class="wide" style="width:98%" align="center" style="text-align: center">
	<tr>
		<td class="left_title" style="width:20%;">Message To :</td>
		<td><select name="email_target_staff[0]"><option value=""></option><?= formatHTMLOptions($active_staff_list) ?></select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:20%">Message To :</td>
		<td><select name="email_target_staff[1]"><option value=""></option><?= formatHTMLOptions($active_staff_list) ?></select></td>
	</tr>
	<tr>
		<td class="left_title">Message<br /><span style="font-size:9px;color:red;font-weight:normal;">This message will be sent by email to the staff member(s) selected</span></td>
		<td><textarea name="email_message_staff" rows="3" onkeypress="resize(this)"></textarea></td>
	</tr>
</table>
</div>
<br /><br />
<?php
if($posted['site_id']==1)
{
	$supplier_emails=supplierChangesFromDatabase($supplier_id,'email');
	$flag_email_list=array('cc'=>'CC', 'bcc'=>'BCC');
	$scount=100;
?>
<div align="center"><?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionEmail\');">Email Manufacturer</a>') ?></div>
<div id="sectionEmail"><table class="wide" style="width:98%;" align="center">
<?php
	if($supplier['email']!='')
	{
?>
	<tr><td class="left_title">Email To :</td><td><input type="checkbox" name="emailTo[<?= $scount ?>]" /> <select style="width:56px;" name="email_flag[<?= $scount ?>]"><?= formatHTMLOptions($flag_email_list) ?></select> <input name="email_manufacturer[<?= $scount ?>]" size="48" value="<?= $supplier['email'] ?>" type="text"></td></tr>
<?php
		$scount++;
	}

	foreach($supplier_contacts as $contact) if($contact['email'] != '')
	{
?>
	<tr><td class="left_title">Email To :</td><td><input type="checkbox" name="emailTo[<?= $scount ?>]" /> <select style="width:56px;" name="email_flag[<?= $scount ?>]"><?= formatHTMLOptions($flag_email_list) ?></select> <input name="email_manufacturer[<?= $scount ?>]" size="48" value="<?= $contact['email'] ?>" type="text"></td></tr>
<?php
		$scount++;
	}
?>
	<tr><td class="left_title">Email To :</td><td><input type="checkbox" name="emailTo[0]" /> <select style="width:56px;" name="email_flag[0]"><?= formatHTMLOptions($flag_email_list) ?></select> <input name="email_manufacturer[0]" size="48" value="" type="text"></td></tr>
	<tr><td class="left_title">Subject :</td><td><input type="text" name="subject" style="width:100%;" /></td></tr>
	<tr><td class="left_title">Message :</td><td><textarea style="width:100%;" name="message" rows="5" onkeypress="textareaAutoSize(this,3,3,25);"></textarea></td></tr>
</table></div>
<?php
	if(count($supplier_emails))
	{
		$scount=count($supplier_emails);
		$supplier_emails=array_chunk($supplier_emails,5);
		$supplier_emails=$supplier_emails[0];
		echo '<div align="center"'.formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionHist\')">Email History</a>').'</div>';
?>
<div id="sectionHist">
<?php
		include 'list_email_history.tpl.php';
		if($scount>5) echo "\n".'<center><input type="button" onclick="window.open(\'suppliers_emails.php?supplier_id='. $supplier_id .'\'); return false;" /></center>'."\n";
?>
</div>
<?php
	}
}
?>