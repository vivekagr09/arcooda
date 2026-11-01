<?php

/**
 *	Page: editToken
 *
 *	This page controller display token form.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 11 July 2018
 */

// Include common script
include_once("common.inc.php");

// Instantiate token object
$survey = new Survey($DB);

// ===================================
// Validation
// ===================================

$link = "";

if (isset($get_vars['link']) === true && empty($get_vars['link']) === false) {
	$link = $get_vars['link'];

	if ($survey->tokenLinkExists($link) === false) {
		$link = "";
	}
} 

if ($link == "") {
	header("Location: notfound.php");
	exit();
}

// Get survey token
$tokenData = $survey->getTokenByLink($link);

// ===================================
// Variables
// ===================================

$title = "Edit Token: " . $tokenData['token'];

$token = $tokenData['token'];

$link = $tokenData['link'];

$member_id = $tokenData['member_id'];

$survey_id = $tokenData['survey_id'];

$status = intval($tokenData['status']);

$get_vars["action"] = "do_edit_token";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/tokenForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>