<?php
/**
 *	Module: Product Builder
 *
 *	photo_select.php
 *
 *	This page display all photos uploaded previously.
 *	The photos are displayed from /images/builder/*
 *	The only single selection is allowed.
 *	
 *	Javascript function needed: setPhotoData(data)
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

// image dir
$dir = $_SERVER['DOCUMENT_ROOT'] . '/images/builder/';

$images = scandir($dir);
unset($images[0]);
unset($images[1]);
?>

<br>
<div class="flagHeader"><span>Photos</span></div>
<br>

<?php
if (empty($images) === false) {
	echo "<div style='width: 95%; margin: auto;'>";
	$urlhost=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'];
	foreach($images as $image) {
		echo "<div class='grid10' style='display: inline-block; vertical-align: top; width: 100px;'>";
		echo "<div><img src='".$urlhost."/images/builder//{$image}' width='100' height='100' /></div>";
		echo "<div><button type='button' style='width: 100px;' onclick='selectValue(\"{$image}\")'>Select</button></div>";
		echo "</div>";
	}

	echo "</div>";
} else {
	echo "<div class='grid10' style='text-align:center;'>";
	echo "<span>No Result Found</span>";
	echo "</div>";
}
?>

<script type="text/javascript">
function selectValue(img) {
	if(window.opener!=null && window.opener.setPhotoData!=null)
		window.opener.setPhotoData(img);
	else if(window.parent!=null && window.parent.setPhotoData!=null)
		window.parent.setPhotoData(img);
	else
		alert('Unable to send product back to form');
	window.close();
}
</script>