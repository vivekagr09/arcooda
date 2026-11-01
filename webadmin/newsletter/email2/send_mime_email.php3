<?php
$title = 'Sending Emails...';
include 'email2/include/header.inc';
include 'email2/include/word_wrap.inc';
if (!isset($DOCUMENT_ROOT)) global $DOCUMENT_ROOT;

$attachment_path = "$DOCUMENT_ROOT/newadmin/email2/attachments/";
include 'email2/uploader.php3';
include 'email2/include/class.html.mime.mail.inc';
$files = fileList();
?>
<blockquote>
	<br />
	<b>Number of emails : <?php echo $db_vars['total'] ?></b><br />
	<br />
	If the mailer stops, <a href="email.php3?page=send">click here to resume</a><br />
	<br />
	COUNT: <?php echo $db_vars['count'] ?><br />
<?php
$where_clause=" response = '' ";
$max_tries=intval($tries);
if($max_tries<=0) $max_tries=3;
if($resume='yes') $where_clause.=" OR response!='Sent' AND tries<".$max_tries;
$statement="SELECT email, name FROM mail_list_tmp WHERE ".$where_clause." LIMIT 0,25";
$result=$DB->query($statement);
echo $DB->error();
if ($DB->num_rows($result) > 0)
{
	$count = $db_vars[count];
	while ($row = $DB->fetch_array($result))
	{
		$count++;
		$full_name = stripslashes($row['name']);
		$full_name = explode('|', $full_name);
		//if (eregi(' ', trim($full_name[0])))
		if (preg_match('/ /', trim($full_name[0])))
		{
			$old_style_name = explode(' ', $full_name[0]);
			$first_name = $old_style_name[0];
			$last_name = $old_style_name[1];
		}
		else
		{
			$first_name = $full_name[0];
			$last_name = $full_name[1];
		}
		//$replaced_message = eregi_replace("\[first\]", $first_name, $db_vars[html_message]);
		$replaced_message = preg_replace("/\\[first\\]/i", $first_name, $db_vars[html_message]);
		//$replaced_text_message = eregi_replace("\[first\]", $first_name, $db_vars[text_message]);
		$replaced_text_message = preg_replace("/\\[first\\]/i", $first_name, $db_vars[text_message]);
		//$replaced_subject = eregi_replace("\[first\]", $first_name, $db_vars[subject]);
		$replaced_subject = preg_replace("/\\[first\\]/i", $first_name, $db_vars[subject]);
		//$replaced_message = eregi_replace("\[last\]", $last_name, $replaced_message);
		$replaced_message = preg_replace("/\\[last\\]/i", $last_name, $replaced_message);
		//$replaced_text_message = eregi_replace("\[last\]", $last_name, $replaced_text_message);
		$replaced_text_message = preg_replace("/\\[last\\]/i", $last_name, $replaced_text_message);
		//$replaced_subject = eregi_replace("\[last\]", $last_name, $replaced_subject);
		$replaced_subject = preg_replace("/\\[last\\]/i", $last_name, $replaced_subject);
		$replaced_message.= $db_vars[parsed_html];
		$replaced_message = $db_vars[header] . $replaced_message;
		$replaced_text_message.= $db_vars[parsed_text];
		$mail = new html_mime_mail('X-Mailer: Highwaygames Mailer');
		if ($replaced_message != '') $mail->add_html($replaced_message, $replaced_text_message);
		else $mail->set_body($replaced_text_message);
		for ($j = 0;$j < count($files);$j++)
		{
			$filename_path = "$attachment_path$files[$j]";
			$attachment = fread($fp = fopen($filename_path, 'r'), filesize($filename_path));
			fclose($fp);
			$mail->add_attachment($attachment, $files[$j], 'application/octet-stream');
		}
		$mail->build_message();
?>
	<br />
	[<?php echo date("Y-m-d H:i:s") ?>] Sending <?php echo $count ?>: <?php echo $first_name ?> <?php echo $last_name ?>&lt;<?php echo $row['email'] ?>&gt;
<?php
		$response = $mail->send($first_name, $row['email'], 'Highwaygames', 'news@highwaygames.com', $replaced_subject);
		$email_response = intval($response) == 1 ? "Sent" : "Error in sending";
		$statement = "UPDATE mail_list_tmp SET timestamp=NOW(), response='".$email_response."', tries=tries+1 WHERE email='" . addslashes($row['email']) . "'";
		$DB->query($statement);
		echo $DB->error();
	}
	$vars['count'] = $count;
	$vars['last_activity'] = last_activity('Sending Emails');
	set_vars($vars);
?>
</blockquote>
<br />
<center><font color="#0000ff" size="2">Please wait, refreshing in 10 seconds</font></center><br />
<br />
<?php
	include 'email2/refresh.inc';
}
else
{
?>
	<br />
	<br />
<?php
	$statement = "SELECT COUNT(*) FROM mail_list_tmp WHERE response!='Sent'";
	$result = $DB->query($statement) or die($DB->error());
	if ($DB->result($result, 0) == 0)
	{
		for ($i = 0;$i < count($files);$i++)
		{
			echo "<LI>[" . date("Y-m-d H:i:s");
			echo "] <B>Deleting Temporary attachement file \"$files[$i]\"</B>\n";
			deleteFile($files[$i]);
		}
?>
</blockquote>
<center><font color="#0000ff" size="4"><b>Mail Sent Successful!</b></font><br />
<br />
<center><font color="#0000ff" size="4"><b><a href="/newadmin/">Click here to return to new admin</a></b></font><br />
<?php
	}
	else
	{
?>
</blockquote>
<center><font color="#ff0000" size="4"><b>Did not finish sending emails</b></font><br />
<br />
<center><font color="#0000ff" size="3"><b><a href="/newadmin/email.php3">Click here for a status report, and how to resume</a></b></font><br />
<?php
	}
	$DB->query("DELETE FROM mail_list_tmp WHERE response = 'Sent'") or die($DB->error());
	$vars[count] = '0';
	$vars[last_activity] = last_activity('Finished Sending');
	set_vars($vars);
}
include 'email2/include/footer.inc';
?>