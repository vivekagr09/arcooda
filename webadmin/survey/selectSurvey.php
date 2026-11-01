<?php

/**
 *	Page: selectSurvey
 *
 *	This page controller lists all survey from database to select
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 11 July 2018
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
$surveyData = $survey->getAllSurvey($by, $order, $offset, $limit);

// Header template
include "templates/header.tpl.php";

// HTML output template
include "templates/selectSurveyList.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");

?>