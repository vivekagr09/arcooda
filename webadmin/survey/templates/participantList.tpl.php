<?php

/**
 *	Template: participantList
 *	Page: viewParticipant
 *
 *	The template displays the list of survey.
 *
 *  Precondition: The survey object has been instantiated and
 *				  the survey data has been retrieved before including this template.
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

?>

<!-- View Survey Title -->
<br><div class="flagHeader"><span>View Participants</span></div><br>

<h1 class="text-align-center">Particpants of Survey: <?= $surveyData['name'] ?></h1>
<br>

<!-- List of survey -->
<table class="view-table">
	<thead>
		<tr>
			<th>Completed Date</th>

			<th>Participant</th>

			<th>Type</th>

			<th></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach($participantData as $v) { ?>

		<?php 
			$member_id = $v['member_id'];
			$participant = $v['id'];

			if ($member_id > 0) {
				$memberData = memberFromDatabase($member_id);
				$name = $memberData['first_name'] . " " . $memberData['last_name'];
				$participant = "<a href='/webadmin/new_members/members_edit.php?member_id={$member_id}' target='_blank'>{$member_id} ({$name})</a>";
			}
		?>

		<tr>
			<td><?= date("d/m/Y", strtotime($v['added_date'])) ?></td>
			<td><?= $participant ?></td>
			<td><?php echo ($member_id > 0) ? "Member" : "Anonymous"; ?></td>
			<td>
				<a href="/webadmin/survey/viewResults.php?id=<?= $v['id'] ?>" target="_blank">View Result</a><br>
			</td>
		</tr>

		<?php } ?>

	</tbody>
</table>

<?php 
$total = count($allParticipantData);
pagination($offset, $limit, $total);
?>