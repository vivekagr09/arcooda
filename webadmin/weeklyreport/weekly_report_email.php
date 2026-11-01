<?php
use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('phpmailer');

$mail = new PHPMailer();
$mail->From = 'reports@coinopexpress.com';
$mail->Sender = $mail->From;
$mail->FromName = "Weekly Reports";
$mail->Mailer = "sendmail";

function emailreports($ids, $fancydate, $from, $to, $namearray)
{
	echo mailem($ids, $fancydate, $from, $to, $namearray);
}
function mailem($ids, $fancydate, $from, $to, $namearray)
{
	global $DB, $ids, $mail, $subject;
	$toname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $to . "'");
	$fromname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $from . "'");
	/* message */
	$message = '<html><head><title>' . $subject . '</title></head><body>';
	$statement = "SELECT *, DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `id` IN(" . $ids . ")";
	if (!isset($order)) $order = 'asc';
	if (!isset($by)) $by = 'date';
	$order_by = " ORDER BY $by $order";
	$result = $DB->query($statement . $order_by);
	$shaded = true;
	while ($row = $DB->fetch_array($result)) $message.= show_result_string($row, $shaded = !$shaded);
	$message.= '</body></html>';
	/* and now mail it */
	$errors = '';
	$result = $DB->query($statement . $order_by);
	//$namearray = array_combine($namearray);
	foreach($namearray as $value)
	{
		$headers = '';
		$user = $value;
		$sendto = $DB->query_first("SELECT * FROM `highwaygroup`.`users` WHERE `user` = '" . $user . "'");
		//echo;
		// To send HTML mail, you can set the Content-type header.
		$headers = "MIME-Version: 1.0\r\n";
		$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
		// additional headers
		$headers.= "To: " . niceusername($user) . " <" . $sendto['email'] . ">\r\n";
		$headers.= "From: Weekly Reports <reports@coinopexpress.com>\r\n";
		$headers.= $subject;
		// HTML body
		// Plain text body (for mail clients that cannot read HTML)
		$text_body = "Please find attached the Weekly Report \n\n";
		$mail->CharSet = "utf-8";
		$mail->Subject = "Weekly " . $fromname['website'] . " Report for " . $toname['website'] . " " . $fancydate;
		$mail->Body = $message;
		$mail->AltBody = $text_body;
		$mail->AddAddress($sendto['email'], niceusername($user));
		$mail->AddStringAttachment($message, "Report.html", "7bit");
		//$mail->AddCustomHeader($headers);
		if ($mail->Send()) $errors.= '<h2>Report Successfully Mailed to ' . niceusername($user) . ' at ' . $sendto['email'] . '</h2>';
		else $errors.= '<h2>Mailed Failed Sending to ' . niceusername($user) . ' at ' . $sendto['email'] . '</h2>';
		// Clear all addresses and attachments for next loop
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		//$errors .= '  h:'.$headers.' s:'.$subject

	}
	return $errors;
}
?>