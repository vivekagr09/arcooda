<?php 
include_once __DIR__ . '/common.inc.php';
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
require_once $_SERVER['DOCUMENT_ROOT'] . '/protected/models/PHPMailerEmail.php';
use app\models\PHPMailerEmail;
$member    = memberFromDatabase($_GET['id']);
if(!isset($member['id'])){
    die('Not a valid request member bot found');
}
moduleLoad('member');


if(isset($_POST['user_email'])){
    $expire_time = strtotime('+2 hours');
    memberSetValue($member['id'], 'rand_num', $_POST['randnum']);
    memberSetValue($member['id'], 'link_expire_time',$expire_time);
    $email          = new PHPMailerEmail();
    $email->Subject = $_POST['subject'];
    $email->Body    = ParsedownExtra::instance()->setBreaksEnabled(true)->text($_POST['email_content']);
    $email->AltBody = $_POST['email_content'];
    $email->AddAddress($_POST['user_email'], $_POST['name']);
    if($email->Send()){
        ?>
        <html>
            <head>
            </head>
            <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
            <table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
            <tr><td style="text-align:center;"><b>Email sent to the provided candidate successfully. Need to check the email. </b></td></tr>
            <tr><td align="center"><input type="button" onclick="window.close();" value="Close Window" /></td></tr>
            </table>
            </body>
        </html>
    <?php
    die;
    }
}else{
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@*?!(&.$+-#_';
    $password = '';
    $max      = strlen($keyspace) - 1;
    for ($i = 0; $i < 16; $i++) {
        $password .= $keyspace[random_int(0, $max)];
    }
    $randnum =  mt_rand(0, 2147483647);
    $message = strtr($_GLOBALS['site_options']['RESET_PASS_EMAIL'], [
        '[cust_name]' => $member['full_name'],
        '[password]'  => $password,
        '[link]'      => relative_url(sprintf(
            '/account/autologin?member_id=%d&rand_num=%d',
            $member['id'],
            $randnum
        )),
    ] + $member);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<style>
    .cke_notification_warning{
        display: none;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-9 col-lg-7 col-xl-6 col-xxl-3" style="margin:auto;">
            <h4>Reset Password Form</h4>
            <p style="color:red"><i>Please use this form to request a password reset.</i></p>
           
            <form class="reset_form" id="resetpassword_form" method="post" action="">
                <div class="form-group">
                    <label for="_user_email"><b>Email</b> <span style="color:red">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="_user_email" name="user_email" value="<?= $member['email_address'] ?>" placeholder="Enter your email" required />
                    <input type="hidden" name="name" value="<?= $member['full_name']?>">
                    <input type="hidden" name="randnum" value="<?= $randnum?>">
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="subject"><b>Subject</b> <span style="color:red">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="subject" name="subject" value="Password Reset Request" placeholder="Enter subject" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="email_content"><b>Mail Content</b> <span style="color:red">*</span></label>
                            <textarea class="form-control form-control-lg" id="email_content" name="email_content" rows="6" placeholder="Enter your message" required><?= nl2br($message) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="send-btn" name="action" value="send">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
<link rel="stylesheet" type="text/css" href="/webadmin/jquery-te/jquery-te-1.4.0.css" />
<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/js/all.min.js" integrity="sha256-IKPvaXm75eTeGvrsxwPh00y8Xjzqs203hTlQYydpLXI=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script type="text/javascript" src="/phpjs/*.js?1729014003"></script>
<script type="text/javascript" src="/webadmin/jscript/common.php.js?1729014003" defer="defer"></script>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js?1729014003"></script>
<script type="text/javascript" src="/webadmin/htmleditor/editor.js?1729014003"></script>
<script type="text/javascript" src="/webadmin/jquery-te/jquery-te-1.4.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
      CKEDITOR.replace('email_content', {

            language : 'en',
            baseHref : "https://www.arcooda.com/",
            allowedContent: true,
            format_h2 : {
                element : "h2",
                attributes : {
                    style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
                }
            },
            contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
            bodyClass : "page_body",
            bodyId : "bd"
        });
});
//]]>
</script>
</html>