<?php
namespace app\controllers;

use app\models\TwigIcon;
use app\models\TwigBrand;
use app\models\TwigSlider;
use app\helpers\Image;
use app\cache\CacheService;
//Expects:
// $action

global $_GLOBALS;

switch ($action) {

    case 'image':
        if ($icons = TwigIcon::search(['id' => $_GET['id']])) {
            $icon = reset($icons);
            $test = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/icon/image/' . $icon->img;
            if (is_file($test)) {
                Image::HTTPSendFile($test);
                die();
            }
        }
        break;

    //@todo Move this to a slider controller
    case 'sliderimage':
        global $DB;
        $cacheService = new CacheService();
        $attach = $cacheService->getData('front_slider_images', $_GET['id']);
        if($attach == false){
            $attach = $DB->query_first(sprintf(
                "SELECT * FROM `front_slider_images` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
                $_GET['id']
            ), MYSQLI_ASSOC);
            $cacheService->updateData('front_slider_images', $_GET['id'], $attach);
        }
        $filename  = basename($attach['name']);

        if ($filename) {
            $watermark_file = null;
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                //[70, 70],    // widget-posts.twig
                //[80, 80],    // post-page.twig
                //[664, 0],    // post-page.twig
                //[730, 485],  // post-card.twig
                //[1903, 500], // post-page.twig
                [1150, 550],
            ])) {
                $width = $height = 0;
            }

            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $filename;
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/slider/image/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $attach['id'],
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
            // if (is_file($cache_file)) {
            //     Image::HTTPSendFile($cache_file);
            // }
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

    case 'view':
        if ($icons = TwigIcon::search(['id' => $_GET['id']])) {
            $icon = reset($icons);
            die($twig->load('icon-view.twig')->render(['inputs' => [
                'page_title'       => $icon->title(),
                'page_description' => $icon->description(),
                'page_keywords'    => $icon->keywords(),
                'page_canonical'   => $icon->url(),
                'page_breadcrumbs' => $icon->breadcrumbs(),
                'icon'             => $icon,
            ]]));
        }
        break;

}
