<?php

/**
 *	Template: questionForm
 *	Page: addQuestion / editQuestion
 *
 *	The template displays survey form
 *
 *  Precondition: 
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

?>

<?php if (notifySuccess()) { ?>
<br/><p style="text-align: center; color: red;">Recorded</p><br/>
<?php } ?>

<h1 class="text-align-center"><?= $title ?></h1>

<form action="index.php?<?= http_build_query($get_vars) ?>" method="post">
	
	<br><div class="flagHeader"><span>Question Information</span></div><br>

	<table class="form-table">
		<tr>
			<td class="left-title">Question </td>
			<td><input type="text" name="question[question]" class="text-input" value="<?= $question ?>" required></td>
		</tr>

		<tr>
			<td class="left-title">Description </td>
			<td><textarea name="question[description]" class="textarea-input" value=""><?= $description ?></textarea></td>
		</tr>

		<tr>
			<td class="left-title">Required </td>
			<td><input type="checkbox" name="question[required]" value="1" <?php echo ($required == 1) ? "checked" : ""; ?>></td>
		</tr>

		<tr>
			<td class="left-title">Type </td>
			<td>
				<input class="question-type-radio" type="radio" name="question[type]" id="question-type-single" onchange="loadSingleAnswer();" value="<?= Survey::SURVEY_QUESTION_SINGLE_ANSWER ?>" <?php echo ($type == Survey::SURVEY_QUESTION_SINGLE_ANSWER) ? "checked" : ""; ?>> <label for="question-type-single">Single Answer</label>
				<input class="question-type-radio" type="radio" name="question[type]" id="question-type-multiple" onchange="loadMultipleAnswer();" value="<?= Survey::SURVEY_QUESTION_MULTIPLE_ANSWER ?>" <?php echo ($type == Survey::SURVEY_QUESTION_MULTIPLE_ANSWER) ? "checked" : ""; ?>> <label for="question-type-multiple">Multiple Answer</label>
				<input class="question-type-radio" type="radio" name="question[type]" id="question-type-open" onclick="return checkConfirm('You are switching to open answer type. All multiple answers will be removed.');" onchange="removeAllMultipleAnswer();" value="<?= Survey::SURVEY_QUESTION_OPEN_ANSWER ?>" <?php echo ($type == Survey::SURVEY_QUESTION_OPEN_ANSWER) ? "checked" : ""; ?>> <label for="question-type-open">Open Answer</label>
				
				<?php if ($get_vars["action"] == "do_add_question" || $get_vars["action"] == "do_edit_question") { // Only for first degree question ?>
				<input class="question-type-radio" type="radio" name="question[type]" id="question-type-based" onchange="loadSingleAnswer();" value="<?= Survey::SURVEY_QUESTION_BASED_ANSWER ?>" <?php echo ($type == Survey::SURVEY_QUESTION_BASED_ANSWER) ? "checked" : ""; ?>> <label for="question-type-based" style="background: yellow;">Questions</label>
				<?php } // #ENDIF ?>
			</td>
		</tr>

	</table>

	<div id="multiple-answer-container" <?php if ($type == Survey::SURVEY_QUESTION_OPEN_ANSWER) echo "style='display: none;'"; ?>>
		<?php if (empty($questionChoice) === false) { ?>
		<div id="question-choice-data">
			<?php foreach($questionChoice as $answer) { ?>
			<div class="question-choice">
				<input type="hidden" name="input_type" value="<?= $answer['input_type'] ?>"/>
				<input type="hidden" name="value" value="<?= $answer['value'] ?>"/>
				<input type="hidden" name="open" value="<?= $answer['open'] ?>"/>
				<input type="hidden" name="checked" value="<?= $answer['checked'] ?>"/>
			</div>
			<?php } // #FOREACH ?>
		</div>
		<?php } // #IF ?>

		<br><div class="flagHeader"><span>Answers</span> <a href="#" onclick="addNewAnswer(); return false;">Add New</a></div><br>
		<table id="answer-sortable-table" class="form-table sortable-table"><tbody id="multiple-answer-loader"></tbody></table>
	</div> <!-- #multiple-answer-container -->

	<br>
	<div class="text-align-center"><input type="submit" value="Save" class="btn"></div>
	<br>
</form>

<!-- Based questions -->
<?php if ($type == Survey::SURVEY_QUESTION_BASED_ANSWER && empty($questionChoice) === false) { ?>

<div id="based-questions-container">
	<br><div class="flagHeader"><span>Questions</span></div><br>
	
	<?php foreach($questionChoice as $answer) { ?>

	<table class="form-table">
		<tr>
			<td class="left-title">Answer</td>
			<td><input type="text" style="width: 70%;" value="<?= $answer['value'] ?>" readonly > <button type="button" style="background: yellow;">Remove</button></td>
		</tr>

		<tr>
			<td class="left-title">Questions</td>
			<td><a href="addBasedQuestion.php?question_choice_id=<?= $answer['id'] ?>&question_id=<?= $id ?>">Add Question</a></td>
		</tr>

		<?php $basedQuestions = $survey->getQuestionByChoiceId($answer['id']); ?>
		<?php if (count($basedQuestions) > 0) { ?>
		<tr>
			<td colspan="2">
				<!-- List of survey -->
				<table class="view-table question-sortable sortable-table based-question-list-table" style="width: 100%;">
					<thead>
						<tr>
							<th>No.</th>
							<th>Question</th>
							<th>Results</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

						<?php foreach($basedQuestions as $question) { ?>

						<tr class="question-list-row" >
							<td class="position-mover"><span class="position-display"><?= $question['position'] ?></span></td>
							<td><?= $question['question'] ?></td>
							<td>0</td>
							<td>
								<a href="editBasedQuestion.php?id=<?= $question['id'] ?>" target="_blank">Edit</a>
								<a href="#" onclick="deleteQuestion(this, '<?= $question['id'] ?>', '<?= $question['question'] ?>', true); return false;">Delete</a>

								<!-- Question ID for sorting. -->
								<input type="hidden" name="question_id" value="<?= $question['id'] ?>" />

								<!-- Type of question -->
								<input type="hidden" name="based" value="1" />

							</td>
						</tr>

						<?php } // #FOREACH ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php } // #ENDIF ?>
	</table> <br>

	<?php } ?>
</div>

<?php } // #ENDIF ?>
<!-- #Based questions -->