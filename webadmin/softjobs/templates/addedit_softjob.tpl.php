<?php
global $_GLOBALS;

$units_list=array('seconds'=>'seconds', 'minutes'=>'minutes', 'hours'=>'hours', 'days'=>'days', 'weeks'=>'weeks', 'months'=>'months', 'years'=>'years');
$time_value=$softjob['time_guess'];
$time_units='seconds';
if($time_value>60)
{
	$time_value=intval($time_value/60);
	$time_units='minutes';
	if($time_value>60)
	{
		$time_value=intval($time_value/60);
		$time_units='hours';
		if($time_value>24)
		{
			$time_value=intval($time_value/24);
			$time_units='days';
			if($time_value>365)
			{
				$time_value=intval($time_value/365.25);
				$time_units='years';
			}
			elseif($time_value>30)
			{
				$time_value=intval($time_value/30.445833);
				$time_units='months';
			}
			elseif($time_value>7)
			{
				$time_value=intval($time_value/7);
				$time_units='weeks';
			}
		}
	}
}

if ($query = $DB->query(sprintf(
	"SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_notes` FROM `highwaygroup`.`bug_messages` WHERE `bug_id` = %d AND `message_date` > %s AND `staff_id` = %d ORDER BY `message_date` DESC",
	$softjob_id,
	formatSQLDateTime(time() - 255),
	$staff_id
))) {
	while($message=$DB->fetch_assoc($query))
	{
		$shade=substr('00'.dechex(min(255,(time()-$message['message_time']))),-2);
?>
<div style="color:#FF<?= $shade.$shade ?>;text-align:left;width:95%;">
	<?= nl2br(htmlspecialchars($message['message_notes'])) ?><br /><br />
</div>
<?php
	}
	$DB->free_result($query);
}

$example_url=($refer=='true') ? $_SERVER['HTTP_REFERER'] : htmlspecialchars($softjob['example_url']);
?>
<input type="hidden" value="<?= $softjob_id ?>" name="softjob[id]" />
<input type="hidden" value="<?= $softjob['reporter'] ?>" name="softjob[reporter]" />
<input type="hidden" value="<?= $_GLOBALS['site_settings']['site_id'] ?>" name="softjob[website]" />
<input type="hidden" value="<?= $_GLOBALS['staff']['id'] ?>" name="softjob[reporter_txt]" />
<br />
<?= formatHTMLTitle('Job Details') ?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Reporter</td>
		<td><?= htmlspecialchars($all_staff_list[$softjob['reporter']]) ?> - <?= date('d/m/Y',$softjob['time_added']) ?></td>
	</tr>
	<tr>
		<td class="left_title">Software Job Title</td>
		<td><input<?= ($softjob['title']!='')?' readonly="readonly"':'' ?> class="intext" type="text" name="softjob[title]" value="<?= htmlspecialchars($softjob['title']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Example URL</td>
		<td><input<?= ($example_url!='')?' readonly="readonly"':'' ?> class="intext" type="text" name="softjob[example_url]" value="<?= $example_url ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Description</td>
		<td><textarea<?= ($softjob['description']!='')?' readonly="readonly"':'' ?> name="softjob[description]" rows="<?= max(8,count(explode("\n",$softjob['description']))) ?>" onkeyup="javascript:textareaAutoSize(this,1,8);"><?= htmlspecialchars($softjob['description']) ?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Copy of error</td>
		<td><textarea<?= ($softjob['error_log']!='')?' readonly="readonly"':'' ?> name="softjob[error_log]" rows="<?= max(8,count(explode("\n",$softjob['error_log']))) ?>" onkeyup="javascript:textareaAutoSize(this,1,8);"><?= htmlspecialchars($softjob['error_log']) ?></textarea></td>
	</tr>
</table>

<br />
<br />

<?php
if(isset($softjob_assets) && $action!='add') include('attachments.tpl.php');

if(is_array($softjob_notes) && count($softjob_notes)>0)
{
?>
<?= formatHTMLTitle('Previous Comments') ?>

<table>
	<tr>
		<th>Date</th>
		<th>User</th>
		<th>Job Note</th>
		<th>Days Open</th>
	</tr>
<?php
	foreach($softjob_notes as $note_id)
	{
		$note=softjobNoteFromDatabase($note_id);
?>
	<tr<?= ($alt=!$alt)?'':' class="alt"' ?>>
		<td style="text-align:center;"><?= date('d/m/Y',$note['time_added']) ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($all_staff_list[$note['user_id']]) ?></td>
		<td><?= nl2br($note['comment']);//htmlspecialchars() ?></td>
		<td style="text-align:center;"><?= floor(($note['time_added']-$softjob['time_added'])/(60*60*24)) ?></td>
	</tr>
<?php
	}
?>
</table>

<br />
<br />

<?php
}
?>

<?= formatHTMLTitle('New Comment') ?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Comment</td>
		<td><textarea name="comment" rows="8" onkeyup="javascript:textareaAutoSize(this,1,8);"></textarea></td>
	</tr>
</table>

<br />
<br />

<?= formatHTMLTitle('Job Status') ?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Priority</td>
		<td><select name="softjob[priority]" required>
			<option value=""></option>
			<?= formatHTMLOptions(softjobGetOptionList('priority'),$softjob['priority']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Assigned To</td>
		<td><select name="softjob[assigned]" required>
			<option value=""></option>
			<?php foreach ($active_staff as $key => $value) {
				echo '<option value="'.$key.'" '.(($softjob['assigned']==$key)?"selected":"").' '.((152==$key)?"style='background-color:#90d990'":"").'>'.$value.'</option>';
			}?>
			
		</select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Time Required</td>
		<td>
			<input type="text" name="softjob[time_value]" value="<?= intval($time_value) ?>" style="width:64px;" />
			<select name="softjob[time_units]" style="width:auto;"><?= formatHTMLOptions($units_list,$time_units) ?></select>
		</td>
	</tr>
</table>

<br />
<br />

<select name="softjob[status]" style="width:auto;">
	<?= formatHTMLOptions(array_reverse($status_list,true),$softjob['status']) ?>
</select>
<?php
if('y' == $_GLOBALS['staff']['developer'])
{
?>
<br />
<br />
<input type="checkbox" name="skip_emails" />Do not send notifications
<?php
}
?>

<br />
<br />

<input type="submit" value="Save Software Job" /><br />
