<?php

/**
 *	Page: viewSurvey
 *
 *	This page controller lists all survey from database
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

// Include common script
include_once("common.inc.php");

// Instantiate Survey object
$survey = new Survey($DB);

// ==========================================
// Filtering order from request
// ==========================================

// Default value of filter parameter
$offset = 0;
$limit  = 20;
$by	    = "added_date";
$order  = "DESC";

// If the current order is DESC, next order is ASC and vice versa
$nextOrder = array(
	"DESC" => "asc",
	"ASC"  => "desc"
);

// Get filter parameter from URL

if (isset($posted['offset']) === true && empty($posted['offset']) === false) {
	$offset = intval($posted['offset']);
}

if (isset($posted['by']) === true && empty($posted['by']) === false) {
	$by = $posted['by'];
}

if (isset($posted['order']) === true && empty($posted['order']) === false) {
	$order = strtoupper($posted['order']);
}

// Make sure order is correct
if ($order != "ASC" && $order != "DESC") {
	$order = "DESC";
}

// Get survey data from database
if (isset($get_vars['search_for']) === true && empty($get_vars['search_for']) === false) {
	$surveyData = $survey->searchSurveyByName($get_vars['search_for'], $by, $order, $offset, $limit);
} else {
	$surveyData = $survey->getAllSurvey($by, $order, $offset, $limit);
}

$surveyData = array_filter($surveyData, function($survey) {
    return isset($survey['site_id']) && $survey['site_id'] == 39;
});
$surveyData = array_values($surveyData);
// Get all survey data
$allSurveyData = $survey->getAllSurvey();

// Filter to include only surveys with site_id = 39
$allSurveyData = array_filter($allSurveyData, function($survey) {
    return isset($survey['site_id']) && $survey['site_id'] == 39;
});

$allSurveyData = array_values($allSurveyData);
//  print_r($surveyData);exit;
// Header template
include_once("templates/header.tpl.php");

// HTML output template
include "templates/surveyList.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>