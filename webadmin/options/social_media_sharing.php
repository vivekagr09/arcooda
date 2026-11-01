<?php
include_once __DIR__ . '/common.inc.php';

include_once "options_functions.inc.php";

// Upload facebook image
if (isset($_FILES['fb'])) {
    $name      = $_FILES['fb']['name'];
    $nameChunk = explode('.', $name);
    $ext       = end($nameChunk);
    $ext       = strtolower($ext);

    if (isExtValid($ext)) {

        $uploadName = "default." . $ext;

        fbImgDelete('default');

        if (!move_uploaded_file($_FILES['fb']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $uploadName)) {
            $errors['fb'][] = "Error uploading. Please try again.";
        } else {
            header('Location: /webadmin/options/social_media_sharing.php');
            exit();
        }
    } else {
        $errors['fb'][] = "Only allow the following extensions: " . implode(', ', $allowedExt) . ".";
    }
}

// Upload twitter image
if (isset($_FILES['tt'])) {
    $name      = $_FILES['tt']['name'];
    $nameChunk = explode('.', $name);
    $ext       = end($nameChunk);
    $ext       = strtolower($ext);

    if (isExtValid($ext)) {

        $uploadName = "default." . $ext;

        ttImgDelete('default');

        if (!move_uploaded_file($_FILES['tt']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $uploadName)) {
            $errors['tt'][] = "Error uploading. Please try again.";
        } else {
            header('Location: /webadmin/options/social_media_sharing.php');
            exit();
        }
    } else {
        $errors['tt'][] = "Only allow the following extensions: " . implode(', ', $allowedExt) . ".";
    }
}

include_once __DIR__ . '/header.inc.php';
?>

<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">Social Media Sharing</div>
		</td>

	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>

<div id="optcontent">

<?php

if (isset($errors['fb'])) {
    echo "<ul style='color: red;'>";
    foreach ($errors['fb'] as $e) {
        echo "<li>{$e}</li>";
    }
    echo "</ul>";
}

include "fb.inc.php";

if (isset($errors['tt'])) {
    echo "<ul style='color: red;'>";
    foreach ($errors['tt'] as $e) {
        echo "<li>{$e}</li>";
    }
    echo "</ul>";
}

include "tt.inc.php";
?>
</div>
