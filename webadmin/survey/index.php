<?php

/**
 *	Entry script
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 6th July 2018
 */

// Include common script
include_once("common.inc.php");

// Action requested
$action = $posted['action'];

// Instantiate Survey Object
$survey = new Survey($DB);

// ==========================================
// Controller: Logic Action
// ==========================================

switch($action) {

	/**
	 *	Add new survey
	 */
	case "do_add_survey" :

		if (isset($posted['survey']) === true && empty($posted['survey']) === false) {
			
			// Add staff id
			$posted['survey']['staff'] = $staff_id;
			// Handle file upload
			if (isset($_FILES['survey']['name']['image']) && !empty($_FILES['survey']['name']['image'])) {
				$uploadDir = '/home/common/images/surveysimage/';
				$fileName = sprintf(
            'img_%d_%d_sml.jpg',
            rand(),
            time(),
        );
				$targetFilePath = $uploadDir . $fileName;
				// Move the uploaded file to the target directory
				if ($_FILES['survey']['name']['image']) {
					move_uploaded_file($_FILES['survey']['tmp_name']['image'], $targetFilePath);
					// Store the file name in the data array to save it in the database
					$posted['survey']['image'] = $fileName;
				} else {
					// Handle the error if the file could not be uploaded
					$_SESSION['error_message'] = "Failed to upload the survey image.";
					$action = "add_survey";
					break;
				}
			} else {
				$posted['survey']['image'] = null;
			}

			$id = $survey->addSurvey($posted['survey']);

			if ($id !== false) {
				$get_vars['id'] = $id;
				$action = "edit_survey";

				// Store the success token to notify editor that data has been saved successfully
				$get_vars['success'] = generateSuccessToken();

			} else {
				session_start();
				if (isset($_SESSION['error_message'])) {
                        $action = "add_survey"; 
                    } else {
                      $action = "view_survey";
                    }
			}
		} else {
			
			$action = "view_survey";
		}

		break;

	/**
	 *	Edit survey
	 */
	case "do_edit_survey" :

		// Id of survey
		$id = intval($get_vars['id']);

		if ($survey->surveyExists($id) === true && isset($posted['survey']) === true && empty($posted['survey']) === false) {

			// Add staff id
			$posted['survey']['staff'] = $staff_id;
			
			// Survey ID
			$posted['survey']['id'] = $id;

			// Get survey data
			$surveyData = $survey->getSurveyById($id);
			// Handle file upload
			if (isset($_FILES['survey']['name']['image']) && !empty($_FILES['survey']['name']['image'])) {
				$uploadDir = '/home/common/images/surveysimage/';
				$fileName = sprintf(
              'img_%d_%d_sml.jpg',
              rand(),
              time(),
          );
				$targetFilePath = $uploadDir . $fileName;
				// Move the uploaded file to the target directory
				if ($_FILES['survey']['name']['image']) {
					move_uploaded_file($_FILES['survey']['tmp_name']['image'], $targetFilePath);
					// Store the file name in the data array to save it in the database
					$posted['survey']['image'] = $fileName;
				} else {
					// Handle the error if the file could not be uploaded
					$_SESSION['error_message'] = "Failed to upload the survey image.";
					$action = "add_survey";
					break;
				}
			} else {
				$posted['survey']['image'] = $surveyData['image'];
			}
			// Data should be be changed
			$posted['survey']['added_date'] = $surveyData['added_date'];

			$survey->editSurvey($posted['survey']);
			//print_r($survey);exit;

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();
		}

		$action = "edit_survey";

		break;

	/**
	 *	Add new question
	 */
	case "do_add_question" :

		// Survey id
		if (isset($get_vars['survey_id']) === true && empty($get_vars['survey_id']) === false &&
			isset($posted['question']) === true && empty($posted['question']) === false &&
			$survey->surveyExists(intval($get_vars['survey_id'])) === true) {

			// Survey ID
			$posted['question']['survey_id'] = intval($get_vars['survey_id']);

			// Get the number of questions in this survey
			$posted['question']['position'] = count($survey->getQuestionBySurveyId(intval($get_vars['survey_id']))) + 1;

			// Check if required is set
			if (isset($posted['question']['required']) === false || empty($posted['question']['required']) === true) {
				$posted['question']['required'] = 0;
			}

			// Check open selector and default selector if question is not open type
			if (isset($posted['question']['question_choice']) === true && empty($posted['question']['question_choice']) === false) {
				foreach($posted['question']['question_choice'] as &$answer) {
					
					// Check open selector
					if (isset($answer['open']) === false || empty($answer['open']) === true) {
						$answer['open'] = 0;
					}

					// Check default selector
					if (isset($answer['checked']) === false || empty($answer['checked']) === true) {
						$answer['checked'] = 0;
					}
				}
			}

			// Add question into database
			$survey->addQuestion($posted['question']);

			$action = "edit_survey";
			$get_vars['id'] = $get_vars['survey_id'];
			unset($get_vars['survey_id']);

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();

		} else {
			$action = "add_question";
		}

		break;

	/**
	 *	Edit question
	 */
	case "do_edit_question" :

		if (isset($get_vars['id']) === true && empty($get_vars['id']) === false &&
			isset($posted['question']) === true && empty($posted['question']) === false &&
			$survey->questionExists(intval($get_vars['id'])) === true) {

			// Get original question data
			$data = $survey->getQuestionById(intval($get_vars['id']));

			// Add id
			$posted['question']['id'] = intval($data['id']);

			// Add survey id
			$posted['question']['survey_id'] = intval($data['survey_id']);

			// Add position
			$posted['question']['position'] = intval($data['position']);

			// Check if required is set
			if (isset($posted['question']['required']) === false || empty($posted['question']['required']) === true) {
				$posted['question']['required'] = 0;
			}

			// Check open selector and default selector if question is not open type
			if (isset($posted['question']['question_choice']) === true && empty($posted['question']['question_choice']) === false) {
				foreach($posted['question']['question_choice'] as &$answer) {
					
					// Check open selector
					if (isset($answer['open']) === false || empty($answer['open']) === true) {
						$answer['open'] = 0;
					}

					// Check default selector
					if (isset($answer['checked']) === false || empty($answer['checked']) === true) {
						$answer['checked'] = 0;
					}
				}
			}

			// Edit question in database
			$survey->editQuestion($posted['question']);

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();

		} else {
			$get_vars['return_message'] = "edit_error";
		}

		$action = "edit_question";

		break;

	/**
	 *	Add new based question
	 */
	case "do_add_based_question" :

		// Survey id
		if (isset($get_vars['question_choice_id']) === true && empty($get_vars['question_choice_id']) === false &&
			isset($get_vars['question_id']) === true && empty($get_vars['question_id']) === false &&
			isset($posted['question']) === true && empty($posted['question']) === false &&
			count($survey->getQuestionChoiceById($get_vars['question_choice_id'])) > 0) {

			// Survey ID
			$posted['question']['survey_question_choice_id'] = intval($get_vars['question_choice_id']);

			// Get the number of questions in this survey
			$posted['question']['position'] = count($survey->getQuestionByChoiceId(intval($get_vars['question_choice_id']))) + 1;

			// Check if required is set
			if (isset($posted['question']['required']) === false || empty($posted['question']['required']) === true) {
				$posted['question']['required'] = 0;
			}

			// Check open selector and default selector if question is not open type
			if (isset($posted['question']['question_choice']) === true && empty($posted['question']['question_choice']) === false) {
				foreach($posted['question']['question_choice'] as &$answer) {
					
					// Check open selector
					if (isset($answer['open']) === false || empty($answer['open']) === true) {
						$answer['open'] = 0;
					}

					// Check default selector
					if (isset($answer['checked']) === false || empty($answer['checked']) === true) {
						$answer['checked'] = 0;
					}
				}
			}

			// Add question into database
			$survey->addQuestion($posted['question'], Survey::SURVEY_BASED_QUESTION);

			$action = "edit_question";
			$get_vars['id'] = $get_vars['question_id'];
			unset($get_vars['survey_id']);
			unset($get_vars['question_id']);

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();

		} else {
			$action = "add_question";
		}

		break;

	/**
	 *	Edit based question
	 */
	case "do_edit_based_question" :

		if (isset($get_vars['id']) === true && empty($get_vars['id']) === false &&
			isset($posted['question']) === true && empty($posted['question']) === false &&
			$survey->questionExists(intval($get_vars['id']), Survey::SURVEY_BASED_QUESTION) === true) {

			// Get original question data
			$data = $survey->getQuestionById(intval($get_vars['id']), Survey::SURVEY_BASED_QUESTION);

			// Add id
			$posted['question']['id'] = intval($data['id']);

			// Add survey id
			$posted['question']['survey_question_choice_id'] = intval($data['survey_question_choice_id']);

			// Add position
			$posted['question']['position'] = intval($data['position']);

			// Check if required is set
			if (isset($posted['question']['required']) === false || empty($posted['question']['required']) === true) {
				$posted['question']['required'] = 0;
			}

			// Check open selector and default selector if question is not open type
			if (isset($posted['question']['question_choice']) === true && empty($posted['question']['question_choice']) === false) {
				foreach($posted['question']['question_choice'] as &$answer) {
					
					// Check open selector
					if (isset($answer['open']) === false || empty($answer['open']) === true) {
						$answer['open'] = 0;
					}

					// Check default selector
					if (isset($answer['checked']) === false || empty($answer['checked']) === true) {
						$answer['checked'] = 0;
					}
				}
			}

			// Edit question in database
			$survey->editQuestion($posted['question'], Survey::SURVEY_BASED_QUESTION);

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();

		} else {
			$get_vars['return_message'] = "edit_error";
		}

		$action = "edit_based_question";

		break;

	/*case "do_add_token":

		if (isset($posted['token']) === true && empty($posted['token']) === false) {
			// Add staff id
			$posted['token']['staff'] = $staff_id;

			$id = $survey->addToken($posted['token']);

			if ($id === false) {
				$action = "view_token";
			} else {
				$action = "edit_token";
			}

			$get_vars['id'] = $id;

		}

		break;*/

	case "do_edit_token" :

		if (isset($posted['token']) === true && empty($posted['token']) === false && isset($get_vars['link']) === true && empty($get_vars['link']) === false && $survey->tokenLinkExists($get_vars['link']) === true) {

			$survey->editToken($posted['token']);

			// Store the success token to notify editor that data has been saved successfully
			$get_vars['success'] = generateSuccessToken();
		}

		$action = "edit_token";

		break;

}

// ==========================================
// Controller: Page View Action
// ==========================================

// Parameter to be kept
$keep_only = array('id', 'survey_id', 'return_message', 'success', 'search_for', 'link', 'question_choice_id', 'question_id');

// Remove all other unnecessary things other than keep_only
if (count($get_vars) > 0) {
	foreach($get_vars as $key => $value) {
		if (is_array($value) || !in_array($key, $keep_only)) {
			unset($get_vars[$key]);
		}
	}
}

switch($action) {

	/**
	 *	Redirect to add survey page
	 */
	case "add_survey" :
		header("Location: addSurvey.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to edit survey page
	 */
	case "edit_survey" :
		header("Location: editSurvey.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to add question page
	 */
	case "add_question" :
		header("Location: addQuestion.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to edit question page
	 */
	case "edit_question" :
		header("Location: editQuestion.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to edit based question page
	 */
	case "edit_based_question" :
		header("Location: editBasedQuestion.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to add token page
	 */
	/*case "add_token" :
		header("Location: addToken.php?" . http_build_query($get_vars));
		exit();
		break;*/

	/**
	 *	Redirect to edit token page
	 */
	case "edit_token" :
		header("Location: editToken.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to view participant page
	 */
	case "view_participant" :
		header("Location: viewParticipant.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to view token page
	 */
	/*case "view_token" :
		header("Location: viewToken.php?" . http_build_query($get_vars));
		exit();
		break;*/

	/**
	 *	Redirect to 404
	 */
	case "not_found" :
		header("Location: notfound.php?" . http_build_query($get_vars));
		exit();
		break;

	/**
	 *	Redirect to view survey page. It is default page.
	 */
	case "view_survey" :
	default :
		header("Location: viewSurvey.php?" . http_build_query($get_vars));
		exit();
		break;
}

?>