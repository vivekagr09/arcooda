<?php

/**
 *	Page: view_responses
 *
 *	This page controller list all responses of survey questions
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
$qid = 0;
$cid = 0;
$open = 0;
$answer_value = "";
$responses = array();

if (isset($get_vars['id']) === true && empty($get_vars['id']) === false &&
	isset($get_vars['open']) === true &&
	isset($get_vars['qid']) === true && empty($get_vars['qid']) === false) {
	
	$id = intval($get_vars['id']);
	$qid = intval($get_vars['qid']);
	$open = intval($get_vars['open']);

	if ($survey->surveyExists($id) === false || $survey->questionExists($qid) === false) {
		$id = 0;
	} else {

		if ($open == 0) {
			if (isset($get_vars['cid']) === true && empty($get_vars['cid']) === false && 
				isset($get_vars['answer_value']) === true && empty($get_vars['answer_value']) === false) {
				
				$cid = intval($get_vars['cid']);
				$answer_value = $get_vars['answer_value'];

				$responses = $survey->getChoiceAnswerResponse($qid, $cid);

			} else {
				$id = 0;
			}
		} else {
			$responses = $survey->getOpenAnswerResponse($qid);
		}
	}
}

if (count($responses) == 0) {
	$id = 0;
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
$question = $survey->getQuestionById($qid);

// Header template
include_once("templates/header.tpl.php");

// HTML output template
include "templates/response.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>