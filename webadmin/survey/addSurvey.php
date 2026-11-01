<?php

/**
 *	Page: addSurvey
 *
 *	This page controller display survey form.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 6th July 2018
 */

// Include common script
include_once("common.inc.php");

// ===================================
// Variables
// ===================================

$title = "Add New Survey";

$surveyName = "";

//$surveyLinks = array();

$surveyLink = "";

$surveyDescription = "";

$surveyCompletedDescription = "";

$surveyAvailabilityChecked = array(
	Survey::SURVEY_ANONYMOUS => "checked",	// Default
	Survey::SURVEY_MEMBER_ONLY => "",
	Survey::SURVEY_TOKEN_ONLY => ""
);

$qpp = 1;

$surveyStatus = array(
	"active" => "selected",	// Default
	"inactive" => ""
);

$get_vars["action"] = "do_add_survey";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/surveyForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>