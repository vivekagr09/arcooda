<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/common.inc.php');

switch ($page) 
{
case 'display_results':
	$no_cache = true;
	include "header.php3";
	include 'email2/display_results.php3';
	include "footer.php3";
	break;

case 'email':
	$no_cache = true;
	include "header.php3";
	include 'email2/email.php3';
	include "footer.php3";
	break;

case 'preview_email':
	include "header.php3";
	include 'email2/preview_email.php3';
	include "footer.php3";
	break;

case 'send':
	include 'email2/send_mime_email.php3';
	break;

default:
	include "header.php3";
	include 'email2/index.php3';
	include "footer.php3";
}
?>