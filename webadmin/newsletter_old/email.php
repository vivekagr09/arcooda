<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/common.inc.php');

switch ($page)
{
case 'display_results':
	$no_cache = true;
	include "header.php";
	include 'email2/display_results.php';
	include "footer.php";
	break;

case 'email':
	$no_cache = true;
	include "header.php";
	include 'email2/email.php';
	include "footer.php";
	break;

case 'preview_email':
	include "header.php";
	include 'email2/preview_email.php';
	include "footer.php";
	break;

case 'send':
	include 'email2/send_mime_email.php';
	break;

default:
	include "header.php";
	include 'email2/index.php';
	include "footer.php";
}
?>