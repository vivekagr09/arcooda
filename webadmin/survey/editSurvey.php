<?php

/**
 *	Page: editSurvey
 *
 *	This page controller display survey form for editing.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 6th July 2018
 */

// Include common script
include_once("common.inc.php");

// Instantiate Survey object
$survey = new Survey($DB);

// ===================================
// Validation
// ===================================

$id = 0;

if (isset($get_vars['id']) === true && empty($get_vars['id']) === false) {
	$id = intval($get_vars['id']);

	if ($survey->surveyExists($id) === false) {
		$id = 0;
	}
} 

if ($id == 0) {
	header("Location: notfound.php");
	exit();
}

// Get survey data
$surveyData = $survey->getSurveyById($id);

// Get survey questions
$surveyQuestions = $survey->getQuestionBySurveyId($id);

// ===================================
// Variables
// ===================================

/**
 *	Title of page
 */
$title = "Edit Survey: ID-" . $id;

/**
 *	Survey name
 */
$surveyName = $surveyData['name'];

/**
 *	Survey links
 */

$surveyLink = "";

//$surveyLinks = array();

if (isset($surveyData['survey_link']) === true && empty($surveyData['survey_link']) === false) {
	$surveyLink = $surveyData['survey_link'][0];
}

/**
 *	Survey description
 */
$surveyDescription = $surveyData['description'];
$surveytype = $surveyData['survey_type'];
$surveyImage = $surveyData['image'];
/**
 *	Completed survey description
 */
$surveyCompletedDescription = $surveyData['completed_description'];

/**
 *	Survey availabiility
 */
$surveyAvailabilityChecked = array(
	Survey::SURVEY_ANONYMOUS => "checked",	// Default
	Survey::SURVEY_MEMBER_ONLY => "",
	Survey::SURVEY_TOKEN_ONLY => ""
);

if ($surveyData['available'] == Survey::SURVEY_ANONYMOUS) {
	$surveyAvailabilityChecked[Survey::SURVEY_ANONYMOUS] = "checked";
} else if ($surveyData['available'] == Survey::SURVEY_MEMBER_ONLY) {
	$surveyAvailabilityChecked[Survey::SURVEY_MEMBER_ONLY] = "checked";
} else {
	$surveyAvailabilityChecked[Survey::SURVEY_TOKEN_ONLY] = "checked";
}

/**
 *	Question per page
 */
$qpp = intval($surveyData['qpp']);

/**
 *	Status of survey
 */
$surveyStatus = array(
	"active" => "selected",	// Default
	"inactive" => ""
);

if ($surveyData['status'] == 1) {
	$surveyStatus['active'] = "selected";
} else {
	$surveyStatus['inactive'] = "selected";
}

$get_vars["action"] = "do_edit_survey";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/surveyForm.tpl.php";
include "templates/questionList.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>