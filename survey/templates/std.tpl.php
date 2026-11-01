<nav class="navbar navbar-expand-lg navbar-light">
	<a class="navbar-brand" href="<?= $domain ?>">
		<img id="logo" src="<?= $logo ?>" height="50" alt="<?= $domain ?>">
	</a>

	<div class="collapse navbar-collapse justify-content-center">
		<h4 id="survey-title">Survey - <?= $surveyName ?></h4>
	</div>
</nav>

<div id="main-container" class="d-none">

	<form action="" method="post">

		<div class="container survey-question-container mt-5" id="survey-question-container-0">
			<p class="mt-3 mb-4"><?= $surveyDescription ?></p>

			<div class="btn-container text-center">
				<button type="button" class="btn btn-primary" onclick="nextOf(0, false);" style="width: 200px">Start</button>
			</div>
		</div>

		<?php
		foreach($surveyQuestions as $question) {
			
			$required = ($question['required'] == 1) ? "required" : "";

			switch($question['type']) {
				case Survey::SURVEY_QUESTION_SINGLE_ANSWER:
					echo "
						<div class='container survey-question-container' id='survey-question-container-". $question['position'] ."'>
							<h6 class='text-secondary'>QUESTION ". intval($question['position']) ."</h6>
							<h4>". htmlspecialchars($question['question']) ."</h4>
							<p class='font-weight-light text-secondary'>". nl2br(htmlspecialchars($question['description'])) ."</p><br>
						";

					foreach($question['question_choice'] as $choice) {
						$defaultChecked = (intval($choice['checked']) == 1) ? "checked" : "";
						$checkedStyle = (intval($choice['checked']) == 1) ? "form-checked" : "";

						echo "
							<div class='form-check {$checkedStyle}'>
								<input class='form-check-input ". $required ."' type='radio' value='". $choice['id'] ."' id='question-choice-". $choice['id'] ."' name='survey_answer[single][". $question['position'] ."][survey_question_choice_id]' ". $defaultChecked .">
								<label class='form-check-label' for='question-choice-". $choice['id'] ."'><span>". $choice['value'] ."</span>
						";

						if ($choice['open'] == 1) {
							echo " <input type='text' class='choice-open-input' name='survey_answer[single][". $question['position'] ."][choice_open_value]'>";
						}

						echo "
								</label>
							</div>
						";
					}

					echo "<input type='hidden' name='survey_answer[single][". $question['position'] ."][survey_question_id]' value='". $question['id'] ."'>";

					break;

				case Survey::SURVEY_QUESTION_MULTIPLE_ANSWER:
					echo "
						<div class='container survey-question-container' id='survey-question-container-". $question['position'] ."'>
							<h6 class='text-secondary'>QUESTION ". intval($question['position']) ."</h6>
							<h4>". htmlspecialchars($question['question']) ."</h4>
							<p class='font-weight-light text-secondary'>". nl2br(htmlspecialchars($question['description'])) ."</p><br>
						";

					foreach($question['question_choice'] as $choice) {
						$defaultChecked = (intval($choice['checked']) == 1) ? "checked" : "";
						$checkedStyle = (intval($choice['checked']) == 1) ? "form-checked" : "";

						echo "
							<div class='form-check {$checkedStyle}'>
								<input class='form-check-input ". $required ."' type='checkbox' value='". $choice['id'] ."' id='question-choice-". $choice['id'] ."' name='survey_answer[multi][". $question['position'] ."][". $choice['id'] ."][survey_question_choice_id]' ". $defaultChecked .">
								<label class='form-check-label' for='question-choice-". $choice['id'] ."'><span>". $choice['value'] ."</span>
						";

						if ($choice['open'] == 1) {
							echo " <input type='text' class='choice-open-input' name='survey_answer[multi][". $question['position'] ."][". $choice['id'] ."][choice_open_value]'>";
						}

						echo "
								</label>
							</div>
						";
					}

					echo "<input type='hidden' name='survey_answer[multi][". $question['position'] ."][survey_question_id]' value='". $question['id'] ."'>";

					break;

				case Survey::SURVEY_QUESTION_OPEN_ANSWER:
					echo "
						<div class='container survey-question-container' id='survey-question-container-". $question['position'] ."'>
							<h6 class='text-secondary'>QUESTION ". intval($question['position']) ."</h6>
							<h4>". htmlspecialchars($question['question']) ."</h4>
							<p class='font-weight-light text-secondary'>". nl2br(htmlspecialchars($question['description'])) ."</p><br>

							<div class='form-group'><textarea class='form-control ". $required ."' rows='8' name='survey_answer[open][". $question['position'] ."][value]'></textarea></div>
					";

					echo "<input type='hidden' name='survey_answer[open][". $question['position'] ."][survey_question_id]' value='". $question['id'] ."'>";

					break;

				default:
					break;
			}

			echo "<div class='alert alert-danger d-none' role='alert'></div>";

			// Button container
			echo "<div class='btn-container text-right'>";
			
			echo "<button type='button' class='btn btn-secondary mr-2' onclick='prevOf(". intval($question['position']) .");'>Previous</button>";

			if ($question['position'] == count($surveyQuestions)) {
				echo "<input type='hidden' name='submit_token' value='". $_SESSION['submit_token'] ."'>";
				echo "<input type='submit' value='Submit' class='btn btn-primary'>";
			} else {
				echo "<button type='button' class='btn btn-primary' onclick='nextOf(". intval($question['position']) .",". (($question['type'] == Survey::SURVEY_QUESTION_OPEN_ANSWER) ? 'true' : 'false') .");'>Next</button>";
			}
			
			echo "</div>"; // #btn-container

			echo "</div>"; // #container
		}
		?>
	
	</form>
</div>

<div id="loading-page">
	<div class="loading helper"></div><div class="loading center">
		Loading
	</div>
</div>