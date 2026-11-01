<?php

/**
 *	Page: report
 *
 *	This page controller generate survey report
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

// ==========================================
// Filtering order from request
// ==========================================

// Get survey data from database
$report = $survey->generateReport($id);

// Get survey questions
$questions = $survey->getQuestionBySurveyId($id);

// Header template
include_once("templates/header.tpl.php");

// HTML output template
include "templates/report.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>