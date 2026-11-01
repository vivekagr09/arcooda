<?php
include_once __DIR__ . '/common.inc.php';

$id = $_GET['id'] ?? 0;

$item = distributorFromDatabase($id) ?? distributorDefault();

if (empty($item['item_image'])) {
    header('404 Not Found');
    die();
}

$path = distributorGetOptionList('item_image_path') . '/' . $item['item_image'];

if (!is_file($path)) {
    header('404 Not Found');
    die();
}

/*
if (!!($_GET['thumb'] ?? false)) {
    $img1  = imagecreatefromstring(file_get_contents($path));
    $scale = min(96.0 / imagesx($img1), 96.0 / imagesy($img1));
    $img2 = imagecreatetruecolor(imagesx($img1) * $scale, imagesy($img1) * $scale);
    imagecopyresampled($img2, $img1, 0, 0, 0, 0, imagesx($img2), imagesy($img2), imagesx($img1), imagesy($img1));
    header('Content-Type: image/jpeg');
    imagejpeg($img2);
    die();
}
*/

$content_type = mime_content_type($path);
header('Content-Type: ' . $content_type);
readfile($path);
die();
