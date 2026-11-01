<?php

/**
 *	Template: surveyList
 *	Page: viewSurvey
 *
 *	The template displays the list of survey.
 *
 *  Precondition: The survey object has been instantiated and
 *				  the survey data has been retrieved before including this template.
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 6th July 2018
 */

?>

<!-- View Survey Title -->
<br><div class="flagHeader"><span>View Survey</span></div><br>

<!-- List of survey -->
<table class="view-table">
	<thead>
		<tr>
		    <th>Survey ID</th>
			<th><a href="?by=added_date&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Added Date</a></th>

			<th><a href="?by=name&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Name</a></th>

			<th>Link</th>

			<th>Users</th>

			<th><a href="?by=last_updated&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Last Updated</a></th>

			<th>Staff</th>

			<th><a href="?by=status&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Status</a></th>

			<th><a href="?by=available&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Type</a></th>

			<th></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach($surveyData as $v) { ?>

		<tr>
		    <td><?= $v['id'] ?></td> 
			<td><?= date("d/m/Y", strtotime($v['added_date'])) ?></td>
			<td><?= $v['name'] ?></td>
			<td>
				<?php if (isset($v['survey_link']) === true && empty($v['survey_link']) === false) { ?>

				<ul class="clr">
				
					<?php foreach($v['survey_link'] as $link) { ?>
					<li><a href="<?= $link ?>" target="_blank"><?= $link ?></a></li>
					<?php } // #FOREACH ?>
				
				</ul>

				<?php } // #IF ?>
			</td>
			<td>
				<?php
					$n = intval($v['total_participants']);
					if ($n > 0) {
						echo "<a href='/webadmin/survey/index.php?action=view_participant&id=". $v['id'] ."' target='_blank'>{$n}</a>";
					} else {
						echo $n;
					}
				?>
			</td>
			<td><?= date("d/m/Y", strtotime($v['last_updated'])) ?></td>
			<td><?= $all_staff_list[$v['staff']] ?></td>
			<td><?php echo (intval($v['status']) == 1) ? "Active" : "Inactive"; ?></td>
			<td>
				<?php
					if (intval($v['available']) == Survey::SURVEY_MEMBER_ONLY) {
						echo "Member";
					} else if (intval($v['available']) == Survey::SURVEY_TOKEN_ONLY) {
						echo "Token";
					} else {
						echo "Anonymous";
					}
				?>
			</td>
			<?php if (isset($get_vars['return_message']) === true && empty($get_vars['return_message']) === false && $get_vars['return_message'] == "add") { ?>
			<td>
				<button onclick="selectSurvey(<?= $v['id'] ?>, '<?= $v['name'] ?>');">Select</button>
			</td>
			<?php } else { ?>
			<td>
				<a href="/webadmin/survey/report.php?id=<?= $v['id'] ?>">Report</a><br>
				<a href="<?= $v['survey_link'][0] ?>" target="_blank">Preview</a><br>
				<a href="index.php?action=edit_survey&id=<?= $v['id'] ?>">Edit</a>
			</td>
			<?php } ?>
		</tr>

		<?php } ?>

	</tbody>
</table>

<?php
$total = count($allSurveyData);

pagination($offset, $limit, $total);

?>

<script>

function selectSurvey(id, name) {
	
	if(window.opener!=null && window.opener.setSurvey!=null)
		window.opener.setSurvey(name, id);
	else if(window.parent!=null && window.parent.setSurvey!=null)
		window.parent.setSurvey(name, id);
	else
		alert('Unable to send survey back to form');
	window.close();
}

</script>