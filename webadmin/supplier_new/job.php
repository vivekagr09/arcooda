<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberTemplateVars, memberJobsFromDatabase, memberFromDatabase, memberJobFromDatabase

include_once __DIR__ . '/header.inc.php';

$action     = 'job';
$job_id     = intval($_REQUEST['job_id']);
$job        = memberJobFromDatabase($job_id);
$member_id  = intval($_REQUEST['member_id']);
$member     = memberFromDatabase($member_id);
if ($member_id != $job['member_id']) {
    die('Mismatching job/member ids');
}


$member_jobs = memberJobsFromDatabase($member_id);

$job_headers = array();
$job_message = explode("\r\n", $job['content']);
while (($row = array_shift($job_message)) != '') {
    $row                                    = explode(':', $row, 2);
    $job_headers[trim(array_shift($row))][] = trim(implode(':', $row));
}
$job_message = implode("\r\n", $job_message);
$job_subject = implode(", ", $job_headers['Subject']);

$member_vars = memberTemplateVars($member);
$job_subject = strtr($job_subject, $member_vars);
$job_message = strtr($job_message, $member_vars);

$process_url = '.?' . http_build_query(array(
	'action' => 'do_job',
	'member_id' => $member_id,
	'job_id' => $job_id,
));
$delete_url = '.?' . http_build_query(array(
	'action' => 'delete_job',
	'member_id' => $member_id,
	'job_id' => $job_id,
));
?>
<h2>Edit Member <?php hecho($member['member_number']) ?> - <?=date('d/m/Y', time())?></h2>

<div>
There are currently <?=count($member_jobs)?> email(s) to be sent for this member<br />
<br />
<form accept-charset="utf-8" action="<?php hecho($process_url) ?>" method="post">

<?= formatHTMLTitle('Preview Email to be sent to the Customer.') ?>

<div>
	<table class="table_fields">
<?php
foreach ($job_headers as $k => $v) {
    switch ($k) {
        case 'Subject':
            break;
		default:
			foreach ($v as $v2) {
?>
		<tr>
			<th><?php hecho($k)?></th>
			<td><?php hecho($v2)?></td>
		</tr>
<?php
			}
			break;
    }
}
?>
		<tr>
			<th>Subject</th>
			<td><input type="text" name="job[subject]" class="wide" value="<?php hecho($job_subject)?>" /></td>
		</tr>
		<tr>
			<th>Request Read Receipt <a href="#" style="color:blue;" onclick="openWindow('read_receipt.html', 400, 400, true);return false">(?)</a></th>
			<td><input type="checkbox" name="job[ConfirmReading]" value="y" /></td>
		</tr>
		<tr>
			<th>Content</th>
			<td><textarea name="job[message]" rows="<?=max(10, substr_count($job_message, "\n") + 3)?>" onkeyup="textareaAutoSize(this, 1, 10);"><?php hecho($job_message)?></textarea></td>
		</tr>
	</table>

	<div style="text-align:center;">
		<input type="submit" style="width:100px;" value="Send Email" />
		<input type="button" style="width:100px;" value="Cancel Email" onclick="<?php hecho('document.location = ' . jecho($delete_url, 1)) ?>" />
	</div>
</form>
<br /><br />

</div>
<?php

include_once __DIR__ . '/footer.inc.php';
