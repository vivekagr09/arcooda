<?php
include_once __DIR__ . '/common.inc.php';
moduleLoad('template');
moduleLoad('phpmailer');
use PHPMailer\PHPMailer\PHPMailer;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$templater       = new Template();
$vars            = socialMediaPartnerTemplateVars($_GLOBALS['staff']);
foreach ($vars as $key => $value) {
    if (trim($value) == '') {
        unset($vars[$key]);
    }
}

$templater->set_vars($vars);
$templater->set_contents($_POST['content']);
$message = $templater->apply();
$templater->set_contents($_POST['subject']);
$subject  = $templater->apply();
$site_list = getWebsitesList();
$from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
$mailer           = new PHPMailer();
try{
$mailer->From     = 'media@arcooda.com';
$mailer->FromName = $from_name;
$mailer->Sender   = 'no-reply@highwaygroup.net';
$mailer->CharSet  = 'utf-8';
$mailer->Subject  = $subject;
$mailer->SMTPDebug = 2; 
$mailer->isHTML(true);
$mailer->AddAddress($_GLOBALS['staff']['email'], trim($_GLOBALS['staff']['first_name']));
// $mailer->AddAddress('testingsyrus@yopmail.com');
$mailer->AddAddress('vivek@yopmail.com','vivek agrawal');
$dir       = socialMediaPartnerGetOptionList('screenshot_path');
if(isset($_POST['attached_file'])){
	foreach ($_POST['attached_file'] as $key => $file) {
		 $fdata = explode('-', $file);
		 $mailer->addAttachment($dir . $fdata[0],$fdata[1]);
	}
}
$mailer->CharSet = "utf-8";
$mailer->Body    = nl2br($message);
$mailer->AltBody = $message;
if($mailer->Send()){
	$mailer->ClearAllRecipients();
	$mailer->ClearCustomHeaders();
}else{
	echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
	echo 'not send'; die;
}
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
}
