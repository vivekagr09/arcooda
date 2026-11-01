<?php
include_once 'softjob_common.inc.php';
include_once 'templates/printout_header.tpl.php';

$softjob_list=explode('_',$posted['id']);

if(count($softjob_list)>0)
{
?>
<center>
	<h2>HIGHWAY GROUP Software Job Work Sheet</h2>
<?php
	if($posted['assigned']>0)
	{
?>
	<h4>
		Programmer: <?= htmlspecialchars($all_staff_list[$posted['assigned']]) ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		if($posted['priority']>0)
		{
?>
		Priority: <?= htmlspecialchars($priority_list[$posted['priority']]) ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		Date: <?= date('d/m/y') ?>
	</h4>
<?php
	}
?>
</center>
<?php

	foreach($softjob_list as $key=>$job_id)
	{
		$str_cmts='';
		$job=softjobFromDatabase($job_id);
		$comments=softjobNotesFromDatabase($job_id);
		foreach(array_reverse($comments) as $id)
		{
			$comment=softjobNoteFromDatabase($id);
			$str_cmts.=date('d/m/y',$comment['time_added']).': ('.htmlspecialchars($all_staff_list[$comment['user_id']]).') '.htmlspecialchars($comment['comment'])."\n<br /><br />\n";
		}
?>
<table style="width:100%;">
	<tr>
		<th style="width:33%;background-color:#DEDEDE">Title</th>
		<th colspan="2" style="width:33%;background-color:#DEDEDE">Section</th>
		<th style="width:33%;background-color:#DEDEDE">Status</th>
	</tr>
	<tr style="text-align:center">
		<td><?= htmlspecialchars($job['title']) ?></td>
		<td colspan="2"><?= htmlspecialchars($website_list[$job['website']]) ?></td>
		<td><?= htmlspecialchars($status_list[$job['status']]) ?></td>
	</tr>
	<tr>
		<td colspan="4" style="padding:8px;"><span style="font-size:10pt;"><b>Job:</b></span> <?= htmlspecialchars($job['description']) ?></td>
	</tr>
	<tr>
		<td><b>Assigned To: </b><?= htmlspecialchars($all_staff_list[$job['assigned']]) ?></td>
		<td colspan="2"><b>Estimated Time: </b><?= ($job['time_guess']>60)?floor($job['time_guess']/60):'' ?></td>
		<td><b>Reported By: </b><?= htmlspecialchars($all_staff_list[$job['reporter']]) ?></td>
	</tr>
	<tr>
		<td colspan="2" style="width:50%;height:200px;" valign="top"><i>New Comments:</i></td>
		<td colspan="2" style="width:50%;height:200px;" valign="top"><i>Job History:</i><br/><?= $str_cmts ?></td>
	</tr>
</table>
<?php
	}
}
else
{
?>
No Software Jobs were selected on the previous page.
<?php
}
?>

<?php
include_once('templates/printout_footer.tpl.php');
?>