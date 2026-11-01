<?php

/**
 *	Page: editQuestion
 *
 *	This page controller display question form to edit.
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

	if ($survey->questionExists($id) === false) {
		$id = 0;
	}
} 

if ($id == 0) {
	header("Location: notfound.php");
	exit();
}

// Get survey data
$questionData = $survey->getQuestionById($id);

// ===================================
// Variables
// ===================================

$title = "Edit Question ID: " . $id;

$question = $questionData['question'];

$description = $questionData['description'];

$required = $questionData['required'];

$type = $questionData['type'];

$questionChoice = array();

if (isset($questionData['question_choice']) === true && empty($questionData['question_choice']) === false) {
	$questionChoice = $questionData['question_choice'];
}

$get_vars["action"] = "do_edit_question";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/questionForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>