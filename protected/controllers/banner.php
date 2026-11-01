<?php
/**
 * @var string            $action
 * @var app\models\MyTwig $twig
 */
?><?php

namespace app\controllers;

use app\helpers\Image;
use app\models\TwigBanner;

switch ($action) {
    case 'click':
        if ($banners = TwigBanner::search(['id' => $_GET['id']])) {
            $banner = reset($banners);
            $banner->incrementClicks();
            redirect($banner->redirect_url());
            die();
        }
        break;

    case 'image':
        if ($banners = TwigBanner::search(['id' => $_GET['id']])) {
            $banner = reset($banners);

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
            ])) {
                $width = $height = 0;
            }

            $image_file = $banner->image_file();
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/banner/image/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $banner->id,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
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

    case 'blur_image':
        if ($banners = TwigBanner::search(['id' => $_GET['id']])) {
            $banner = reset($banners);

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
            ])) {
                $width = $height = 0;
            }

            $image_file = $banner->blur_file();
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/banner/blur/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $banner->id,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
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

    case 'mobile_image':
        if ($banners = TwigBanner::search(['id' => $_GET['id']])) {
            $banner = reset($banners);

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
            ])) {
                $width = $height = 0;
            }

            $image_file = $banner->mobile_file();
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/banner/mobile/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $banner->id,
                ($width) ? $width : '',
                ($height) ? $height : ''
            );
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
