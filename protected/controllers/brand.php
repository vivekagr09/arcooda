<?php
namespace app\controllers;

use app\helpers\Image;
use app\models\TwigBrand;

//Expects:
// $action

global $_GLOBALS;

switch ($action) {
    case 'image':
        if ($_GLOBALS['staff']['id'] ?? false) {
            unset($_GLOBALS['criteria_options']['brand']);
        }

        if ($brands = TwigBrand::search(['id' => $_GET['id']])) {
            $brand = reset($brands);

            $watermark_file = null;
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
            ])) {
                $width  = 103; //block-brands-carousel.twig
                $height = 36;
            }
            $image_file = $brand->image_file();
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/brand/image/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $brand->id,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            if (is_file($cache_file)) {
                Image::HTTPSendFile($cache_file);
                die();
            }
            Image::copyToStatic(
                $image_file,
                $cache_file,
                $profile_file,
                $watermark_file,
                $width,
                $height
            );
            if (!is_file($cache_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            Image::HTTPSendFile($cache_file);
            die();
        }
        break;
}
