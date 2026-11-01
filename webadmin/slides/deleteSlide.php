<?php

/**
 *	Delete slide image
 */

if (isset($_POST['slide']) && !empty($_POST['slide'])) {
	$slide = trim($_POST['slide']);

	if (file_exists($slide)) {
		unlink($slide);
		header('Location: /webadmin/slides');
		exit();
	}
}

die("Error");

?>