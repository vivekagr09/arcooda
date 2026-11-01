<?php

/**
 *	Add slide image
 */

if (isset($_FILES['slide']) && !empty($_FILES['slide']['name'])) {
	$img = $_FILES['slide'];
	$chunk = explode('.', $img['name']);
	$ext = end($chunk);
	unset($chunk[count($chunk) - 1]);
	$name = implode('.', $chunk);
	$fname = $img['name'];

	if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/slides/' . $fname)) {
		$fname = $name . '-' . uniqid() . '.'. $ext;
	}

	if (move_uploaded_file($img['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/slides/' . $fname)) {
		header('Location: /webadmin/slides');
		exit();
	}
}

die("Error");

?>