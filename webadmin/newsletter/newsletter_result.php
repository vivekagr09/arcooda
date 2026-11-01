<?php
include_once ('common.inc.php');
include_once ('templates/header.tpl.php');

$action = 'newsletter_result';
$id = intval($posted['id']);
$item = $section_handler->fromDatabase($id);
$staff_id = $_GLOBALS['staff']['id'];
$site_id = $_GLOBALS['site_settings']['site_id'];
$member_list = $section_handler->mailinglistFromDatabase($id);
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
foreach($member_list as $k => $v) $by_status[$v['email_status']][] = $k;
$email_list = array();
if ($posted['email_id'] != '') $email_list = explode(',', $posted['email_id']);

?>
<div style="text-align:left;">
<?php
if (count($email_list))
{
?>
	Sent emails to:
	<ul>
<?php
	$matchup = array(
		'listed' => 'Listed',
		'error' => '<span style="color:red;">ERROR</span>',
		'sent' => '<span style="color:green;">SUCCESS</span>',
		'sending' => '<span style="color:orange;">SENDING</span>',
	);
	foreach($email_list as $email_id)
	{
		if ($member_list[$email_id]['member_id'] == 0)
		{
			$member_list[0]['email_status'] = 'sent';
			$member = array(
				'first_name' => $_GLOBALS['staff']['first_name'],
				'last_name' => $_GLOBALS['staff']['last_name'],
				'email_address' => $_GLOBALS['staff']['email'],
			);
		}
		else
		{
			$member = memberFromDatabase($member_list[$email_id]['member_id']);
		}
		echo '		<li>' . formatHTMLSafe($member['email_address']) . ' - ' . $matchup[$member_list[$email_id]['email_status']] . "</li>\n";
	}
?>
	</ul>
<?php
}
?>
</div>
<?php
include 'templates/status.tpl.php';
?>
<script type="text/javascript">
//<![CDATA[
function continueNewsletter()
{
	window.location = <?php jecho('index.php?action=cont_newsletter&id=' . $id) ?>;
}
function safeContinueNewsletter()
{
	if(getElement('auto_send').checked) continueNewsletter();
}
//]]>
</script>
<input type="button" onclick="continueNewsletter()" value="Continue Sending" /><br />
<input type="checkbox" name="auto_send"<?php echo 	((count($by_status['sending']) || count($by_status['listed'])) && $posted['email_id'] != '') ? ' checked="checked"' : '' ?> /> Automatically send after 5 seconds<br />
<br />
<script type="text/javascript">
//<![CDATA[
window.setTimeout(safeContinueNewsletter,5000);
//]]>
</script>
<?php
include_once ('templates/footer.tpl.php');
?>