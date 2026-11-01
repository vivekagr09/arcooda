<?php

/**
 *	Page: viewParticipant
 *
 *	This page controller lists all participants from database
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

// Default value of filter parameter
$offset = 0;
$limit  = 50;

if (isset($posted['offset']) === true && empty($posted['offset']) === false) {
	$offset = intval($posted['offset']);
}

// Get participant data from database
$participantData = $survey->getAllParticipantBySurveyId($id, $offset, $limit);

// Get all participant data from database
$allParticipantData = $survey->getAllParticipantBySurveyId($id, 0, 0);

// Header template
include_once("templates/header.tpl.php");

// HTML output template
include "templates/participantList.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>