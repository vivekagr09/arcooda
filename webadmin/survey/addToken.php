<?php

// Disable it. Only add from member section or invoice.
die();

/**
 *	Page: addToken
 *
 *	This page controller display token form.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 11 July 2018
 */

// Include common script
include_once("common.inc.php");

// ===================================
// Variables
// ===================================

$title = "Add New Token";

$token = "";

$link = "";

$member_id = 0;

$survey_id = 0;

$status = 1;

$get_vars["action"] = "do_add_token";

// ------------------------------------

// Header template
include_once("templates/header.tpl.php");

// Template
include "templates/tokenForm.tpl.php";

// Footer template
include_once("templates/footer.tpl.php");
?>