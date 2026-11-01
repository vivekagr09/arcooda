<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('phpmailer');

$action             = 'spamcheck';
$item               = $section_handler->fromPost($posted['newsletter']);
$temp               = $section_handler->styleFromDatabase($item['style_id']);
$item['site_id']    = $temp['site_id'];
$item['sender']     = $temp['sender'];
$item['time_added'] = $_SERVER['REQUEST_TIME'];
if (isset($posted['newsletter_settings'])) {
    $item['settings'] = serialize($posted['newsletter_settings']);
}

$item_settings = unserialize($item['settings']);

$file = 'newsletters/style_' . $item['style_id'] . '.tpl.php';
if ("Daniel" == $_COOKIE["REMOTE_USER"] && is_file('newsletters/style_' . $item['style_id'] . '_new.tpl.php')) {
    $file = 'newsletters/style_' . $item['style_id'] . '_new.tpl.php';
}

if (is_file($file)) {
    ob_start();
    $_STACK[] = $item;
    include $file;
    $item                = array_pop($_STACK);
    $item['description'] = $section_handler->generateMailMerge(ob_get_clean(), array(
        'staff_name' => $_GLOBALS['staff']['first_name'],
    ));
}

$replace = array(
    'first_name'    => $_GLOBALS['staff']['first_name'],
    'last_name'     => $_GLOBALS['staff']['last_name'],
    'email_address' => $_GLOBALS['staff']['email'],
    'tracker_key'   => '',
);

$newsletter_content   = $item['description'];
$newsletter_content   = $section_handler->negotiateContent($newsletter_content, $replace);
$newsletter_content   = $section_handler->generateMailMerge($newsletter_content, $replace);
$newsletter_content   = $section_handler->generateMailMerge($newsletter_content);
$newsletter_alternate = $section_handler->generateAlternateText($newsletter_content);

$site_list = getWebsitesList();
$from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
if ('' != $site_list[$item['site_id']]) {
    $from_name = $site_list[$item['site_id']];
}

$mailer           = new PHPMailer();
$mailer->From     = $item['sender'];
$mailer->FromName = $from_name;
$mailer->Sender   = 'no-reply@highwaygroup.net';
$mailer->CharSet  = 'utf-8';
$mailer->Subject  = $item['subject'];
$mailer->isHTML(true);
$mailer->AddAddress($_GLOBALS['staff']['email'], trim($_GLOBALS['staff']['first_name'] . ' ' . $_GLOBALS['staff']['last_name']));
$mailer->Body    = $newsletter_content;
$mailer->AltBody = $newsletter_alternate;

header("Content-Type:application/json; charset=utf-8");
$mailer->preSend();
$content = "Received: by john.ourhunter.com (Postfix, from userid 48)\r\n";
$content .= "	id " . md5(time()) . "; " . date("D, j M Y H:i:s O (T)\r\n");
$content .= $mailer->GetSentMIMEMessage();

$ret = array();
exec("echo " . escapeshellarg($content) . " | /usr/bin/spamc -R", $ret);

$score = reset(explode("/", array_shift($ret))) - 1.9;
while (count($ret) && substr(array_shift($ret), 0, 4) != "----");
foreach ($ret as $k => $v) {
    if (strpos($v, " NO_RELAYS ") !== false || "" == $v) {
        unset($ret[$k]);
    } else {
        $ret[$k] = preg_split("/ +/", trim($v), 3);
    }
}

echo "[" . $score . "," . jecho(array_values($ret), 1) . "]";

$mailer->ClearAllRecipients();
$mailer->ClearCustomHeaders();
