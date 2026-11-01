<?php
/**
 *	Module: Product Builder
 *
 *	pb_upload_image.php
 *
 *	This ajax request upload the photo for customed photo type option item.
 *
 *	The photo location is: /images/builder/
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */
if (isset($_FILES['img']) && empty($_FILES['img']) === false & empty($_FILES['img']['name']) === false) {
	$img = $_FILES['img'];
	$filenameSplit = explode(".", $_FILES['img']['name']);
	$ext = strtolower(end($filenameSplit));

	// Create image name
	$imgName = md5(uniqid());

	$dir = $_SERVER['DOCUMENT_ROOT'] . '/images/builder/';

	// Check error
	if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
		if (move_uploaded_file($img['tmp_name'], $dir . $imgName . "." . $ext)) {
			echo $imgName . "." . $ext;
		} else {
			echo "UPLOAD_ERROR";
		}
	} else {
		echo "FILE_EXT_ERROR";
	}
}
?>