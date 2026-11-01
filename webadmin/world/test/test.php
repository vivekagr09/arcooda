<?php
$target_path = "maps/";
$target_path = $target_path . basename($_FILES['map_file']['name']);
echo 'Target path = ' . $target_path . '<br>';
if (move_uploaded_file($_FILES['map_file']['tmp_name'], $target_path)) {
    echo "The map file " . basename($_FILES['map_file']['name']) . " has been uploaded";
} else {
    echo "There was an error uploading the file, please try again!";
}

function LoadGif($imgname)
{
    $im = @imagecreatefromgif($imgname); /* Attempt to open */
    if (!$im) {
        /* See if it failed */
        $im  = imagecreatetruecolor(150, 30); /* Create a blank image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}

$im = LoadGif($target_path);
if (function_exists("imagegif")) {
    header("Content-type: image/gif");
    imagegif($im);
} elseif (function_exists("imagejpeg")) {
    header("Content-type: image/jpeg");
    imagejpeg($im, "", 0.5);
} elseif (function_exists("imagepng")) {
    header("Content-type: image/png");
    imagepng($im);
} elseif (function_exists("imagewbmp")) {
    header("Content-type: image/vnd.wap.wbmp");
    imagewbmp($im);
} else {
    die("No image support in this PHP server");
}
