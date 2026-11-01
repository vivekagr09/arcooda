<?php

/**
 *	Page: addQuestion
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

$survey_id = 0;

if (isset($get_vars['survey_id']) === true && empty($get_vars['survey_id']) === false) {
	$survey_id = intval($get_vars['survey_id']);

	if ($survey->surveyExists($survey_id) === false) {
		$survey_id = 0;
	}
} 

if ($survey_id == 0) {
	header("Location: notfound.php");
	exit();
}

// Get survey data
$surveyData = $survey->getSurveyById($survey_id);

// ===================================
// Variables
// ===================================

$title = "Survey: " . $surveyData['name'];

$question = "";

$description = "";

$required = 0;

$type = Survey::SURVEY_QUESTION_OPEN_ANSWER;

$get_vars["action"] = "do_add_question";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/questionForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>