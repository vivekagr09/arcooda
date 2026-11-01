<?php

/**
 *	Template: results
 *	Page: viewResults
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

<h1 class="text-align-center">Result of Survey: <?= $surveyData['name'] ?></h1>
<br>

<!-- Participant details -->
<br><div class="flagHeader"><span>Participant Details</span></div><br>
<table class="form-table">
	<tr>
		<td class="left-title">Participant Unique ID</td>
		<td><?= $id ?></td>
	</tr>

	<tr>
		<td class="left-title">Member</td>
		<td><?php echo ($participantData['member_id'] > 0) ? "<a target='_blank' href='/webadmin/new_members/members_edit.php?member_id={$participantData['member_id']}'>{$participantData['member_id']} ({$name})</a>" : "Anonymous"; ?></td>
	</tr>

	<?php if ($surveyData['available'] == Survey::SURVEY_TOKEN_ONLY) { ?>
	<tr>
		<td class="left-title">Token Link</td>
		<td><?= $link ?></td>
	</tr>

	<tr>
		<td class="left-title">Invoice ID</td>
		<td><?php echo ($invoice_id > 0) ? "<a target='_blank' href='/webadmin/invoice/invoice_edit.php?invoice_id={$invoice_id}'>{$invoice_id}</a>" : "-"; ?></td>
	</tr>
	<?php } ?>


</table>

<!-- List of survey results -->
<br><div class="flagHeader"><span>Survey Result</span></div><br>
<?php foreach($questions as $question) { ?>

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
			<table class="result-table">
				<tr>
					<td colspan="2"><b>Answers</b></td>
				</tr>

				<?php if ($question['type'] == Survey::SURVEY_QUESTION_OPEN_ANSWER) { ?>
					<tr><td><?= nl2br(htmlspecialchars($participantData['answers'][$question['id']][0]['value'])) ?></td></tr>
				<?php } else { ?>

					<?php foreach($question['question_choice'] as $choice) { ?>

					<?php 
						$checked = "";
						$openValue = "";

						foreach($participantData['answers'][$question['id']] as $pa) {
							if ($pa['value'] == $choice['id']) {
								$checked = "checked";
								$openValue = $pa['open_value'];
								break;
							}
						}
					?>
					<tr class="<?= $checked ?>">
						<td><input type="checkbox" disabled <?= $checked ?>></td>
						<td>
							<?php 
								echo $choice['value'];

								if ($openValue != "") {
									echo ": " . htmlspecialchars($openValue);
								}
							?>
						</td>
					</tr>

					<?php } // #FOREACH ?>

				<?php } // #IF ?>

			</table>
		</td>
	</tr>
</table>

<br>

<?php } ?>

<div style="text-align: center; background: yellow;">
<button>Print</button>
</div>