<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

moduleLoad('member'); //memberMailingListSubscribe, memberMailingListUnsubscribe

$style_id = $_GLOBALS['site_settings']['newsletter_style'] ?? 9;

if (!empty($_POST) && (isset($_GLOBALS["member"]) && !empty($_GLOBALS["member"]) && $_GLOBALS["member"]["member_id"] > 0)) {
    if (1 == $_POST['data']) {
        memberMailingListSubscribe($_GLOBALS['member']['id'], $style_id);

        echo 'You have successfully subscribed to notifications';
    } else {
        memberMailingListUnsubscribe($_GLOBALS['member']['id'], $style_id);

        echo 'You have successfully unsubscribed from notifications';
    }
} else {
    echo 'Please login to subscribe notification';
}
