<?php

/**
 *	Template: report
 *	Page: report
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

<!-- List of survey results -->

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
			<?php if ($question['type'] == Survey::SURVEY_QUESTION_OPEN_ANSWER) { ?>
			
			<a target='_blank' href="/webadmin/survey/viewResponses.php?id=<?= $id ?>&qid=<?=$question['id']?>&open=1">View Responses (<?= $report[$question['id']]['responses'] ?>)</a>
			
			<?php } else { ?>

			<table class="report-answer-table">
				<tr>
					<td colspan="2"><b>Answers</b></td>
					<td class="nv"><b>No.</b></td>
					<td class="nv"><b>%</b></td>
				</tr>

				<?php foreach($question['question_choice'] as $k => $choice) { ?>

				<tr>
					<td class="nv"><?= ($k + 1) ?></td>
					<td>
						<?php 
							echo $choice['value'];
						?>
					</td>
					<td class="nv"><?php echo "<a target='_blank' href='/webadmin/survey/viewResponses.php?id={$id}&qid={$question['id']}&cid={$choice['id']}&open=0&answer_value={$choice['value']}'>{$report[$question['id']]['answers'][$choice['id']]}</a>"; ?></td>
					<td class="nv"><?= number_format(((intval($report[$question['id']]['answers'][$choice['id']]) / $surveyData['total_participants']) * 100), 2) ?>%</td>
				</tr>

				<?php } ?>

			</table>

			<?php } ?>
		</td>
	</tr>
</table>

<br>

<?php } ?>

<div style="text-align: center; background: yellow;">
<button>Print</button>
</div>