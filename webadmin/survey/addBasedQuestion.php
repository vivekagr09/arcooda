<?php

/**
 *	Page: addBasedQuestion
 *
 *	This page controller display question form.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

// Include common script
include_once("common.inc.php");

// Instantiate Survey object
$survey = new Survey($DB);

// ===================================
// Validation
// ===================================

$question_choice_id = 0;

$question_choice = array();

if (isset($get_vars['question_choice_id']) === true && empty($get_vars['question_choice_id']) === false) {
	$question_choice_id = intval($get_vars['question_choice_id']);

	$question_choice = $survey->getQuestionChoiceById($question_choice_id);
} 

if ($question_choice_id == 0 || count($question_choice) == 0) {
	header("Location: notfound.php");
	exit();
}

// Get survey data
//$surveyData = $survey->getSurveyById($survey_id);

// ===================================
// Variables
// ===================================

$title = "Question for answer: " . $question_choice['value'];

$question = "";

$description = "";

$required = 0;

$type = Survey::SURVEY_QUESTION_OPEN_ANSWER;

$get_vars["action"] = "do_add_based_question";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/questionForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>