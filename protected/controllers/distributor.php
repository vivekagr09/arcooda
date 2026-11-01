<?php
namespace app\controllers;

use app\helpers\Image;
use app\models\TwigDistributor;

//Expects:
// $action

global $_GLOBALS;

switch ($action) {
    case 'image':
        if ($_GLOBALS['staff']['id'] ?? false) {
            unset($_GLOBALS['criteria_options']['distributor']);
        }

        if ($distributors = TwigDistributor::search(['id' => $_GET['id']])) {
            $distributor = reset($distributors);
            $watermark_file = null;
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if($width == 0 && $height == 0){
                $width  = 101; // block-distributors.twig
                $height = 54;
            }
            if(isset($_GET['type']) && $_GET['type'] == 'mapimage'){
                 $image_file = $distributor->map_image_file();
                 $cache_file = sprintf(
                        '%s/protected/cache/distributor/image/%s_%sx%s.jpg',
                        $_SERVER['DOCUMENT_ROOT'],
                        ('map'.$distributor->id),
                        ($width) ? $width : 500,
                        ($height) ? $height :500
                    );

            }else{
                $image_file = $distributor->image_file();
                 $cache_file = sprintf(
                    '%s/protected/cache/distributor/image/%d_%sx%s.jpg',
                    $_SERVER['DOCUMENT_ROOT'],
                    $distributor->id,
                    ($width) ? $width : '',
                    ($height) ? $height : ''
                );
            }
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

           
            // if (is_file($cache_file)) {
            //     Image::HTTPSendFile($cache_file);
            //     die();
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
}
