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

$id = "";

if (isset($get_vars['id']) === true && empty($get_vars['id']) === false ) {
	
	$id = $get_vars['id'];

	if ($survey->participantExists($id) === false) {
		$id = "";
	} 
}

if ($id == "") {
	header("Location: notfound.php");
	exit();
}

// Get Participant data
$participantData = $survey->getParticipantById($id);

// Get survey data
$surveyData = $survey->getSurveyById($participantData['survey_id']);

// Get questions
$questions = $survey->getQuestionBySurveyId($surveyData['id']);

// Some hacks to get invoice id and member details
$link = "";
$invoice_id = 0;
$name = "";

if ($participantData['member_id'] > 0) {
	$tokenData = $DB->query_first("SELECT * FROM `highwaygroup`.`survey_token` WHERE `survey_id` = {$surveyData['id']} AND `member_id` = {$participantData['member_id']}", MYSQL_ASSOC);
	$link = $tokenData['link'];

	$invoiceData = $DB->query_first("SELECT * FROM `highwaygroup`.`invoice_survey` WHERE `link` = '{$link}'", MYSQL_ASSOC);
	$invoice_id = $invoiceData['invoice_id'];

	$memberData = memberFromDatabase($participantData['member_id']);
	$name = $memberData['first_name'] . " " . $memberData['last_name'];
}
// ==========================================
// Filtering order from request
// ==========================================

// Header template
include_once("templates/header.tpl.php");

// HTML output template
include "templates/results.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>