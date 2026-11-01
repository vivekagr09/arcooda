<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'newsletter_result';
$id = intval($posted['id']);
$item = mediaPartnedNewsletterFromDatabase($id);
$staff_id = $_GLOBALS['staff']['id'];
$site_id = $_GLOBALS['site_settings']['site_id'];
$partner_list = mediaPartnermailinglistFromDatabase($id);
$by_status = array(
	'bounced' => array(),
	'clicked' => array(),
	'error' => array(),
	'listed' => array(),
	'ordered' => array(),
	'sending' => array(),
	'sent' => array(),
	'unsubscribed' => array(),
	'viewed' => array(),
);
foreach($partner_list as $k => $v) $by_status[$v['email_status']][] = $k;
$email_list = array();
if ($posted['email_id'] != '') $email_list = explode(',', $posted['email_id']);

?>
<?php
include 'status.tpl.php';
?>