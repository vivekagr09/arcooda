<?php

/**
 *	Template: response
 *	Page: viweResponse
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
<br><div class="flagHeader"><span>Survey Report</span></div><br>

<h1 class="text-align-center">Report of Survey: <?= $surveyData['name'] ?></h1>
<br>

<table class="form-table">
	<tr>
		<td class="left-title" style="width: 25%; padding: 5px 2px;">Question <?= $question['position'] ?></td>
		<td><b><?= $question['question'] ?></b></td>
	</tr>

	<tr>
		<td class="left-title" style="width: 25%; padding: 5px 2px;"></td>
		<td><?= nl2br(htmlspecialchars($question['description'])) ?></td>
	</tr>

	<tr>
		<td class="left-title" style="width: 25% padding: 5px 2px;"></td>
		<td>

			<table class="report-answer-table">
				<tr>
					<?php if ($open == 1) { ?>
					<td colspan="2"><b>Responses</b></td>
					<?php } else { ?>
					<td colspan="2"><b>Responses to answer: <?= $answer_value ?></b></td>
					<?php } ?>
				</tr>

				<?php $n = 0; foreach($responses as $response) { ?>

				<tr>
					<td class="nv"><?= ++$n; ?></td>
					<td style="padding: 5px 2px;">
						
						<?php
							echo ($response['value'] != "") ? nl2br(htmlspecialchars($response['value'])) . " - " : "";

							if ($response['member_id'] > 0) {
								$memberData = memberFromDatabase($response['member_id']);
								$name = $memberData['first_name'] . " " . $memberData['last_name'];

								echo "<a target='_blank' href='https://www.highwaygroup.net/webadmin/new_members/members_edit.php?member_id={$response['member_id']}'>{$response['member_id']} ({$name})</a>";
							} else {
								echo "Anonymous";
							}
						?>
					</td>
				</tr>

				<?php } ?>

			</table>

		</td>
	</tr>
</table>