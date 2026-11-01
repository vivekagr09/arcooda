<?php
include_once __DIR__ . '/common.inc.php';
$matches = array();
if (preg_match('/^\/mail\/([0-9a-f]{16})(\/.*)$/', $_SERVER['REQUEST_URI'], $matches)) {
    $newsletter_cookie = $matches[1];
    $newsletter_id     = hexdec(substr($newsletter_cookie, -8));
    $newsletter_cookie = substr($newsletter_cookie, 0, -8);
    $member_id         = hexdec(substr($newsletter_cookie, -8));
    $newsletter_cookie = substr($newsletter_cookie, 0, -8);

    $ext = end(explode('.', reset(explode('#', reset(explode('?', $matches[2]))))));

    if ($ext == 'html') {
        $DB->query(sprintf(
			"UPDATE `highwaygroup`.`newsletter_emails` SET `email_status` = 'clicked' WHERE `newsletter_id` = %d AND `member_id` = %d AND `email_status` <> 'ordered' AND `email_status` <> 'unsubscribed' LIMIT 1",
			$newsletter_id,
			$member_id
		));
        $DB->query(sprintf(
			"UPDATE `highwaygroup`.`newsletter_tracking` SET `timestamp` = NOW() WHERE `newsletter_id` = %d AND `member_id` = %d AND `request_uri` = %s LIMIT 1",
			$newsletter_id,
			$member_id,
			formatSQLString($matches[2])
		));
        if (!$DB->affected_rows()) {
            $DB->query(sprintf(
				"INSERT INTO `highwaygroup`.`newsletter_tracking` (`newsletter_id`, `member_id`, `request_uri`) VALUES (%d, %d, %s)",
				$newsletter_id,
				$member_id,
				formatSQLString($matches[2])
			));
        }

    } else {
        $DB->query(sprintf(
			"UPDATE `highwaygroup`.`newsletter_emails` SET `email_status` = 'viewed' WHERE `newsletter_id` = %d AND `member_id` = %d AND `email_status` <> 'clicked' AND `email_status` <> 'ordered' AND `email_status` <> 'unsubscribed' LIMIT 1",
			$newsletter_id,
			$member_id
		));
    }

    custom_setcookie('newsletter_id', $matches[1], 0, '/');
    header('Location: ' . $matches[2]);
} else {
    die('File Not Found');
}
