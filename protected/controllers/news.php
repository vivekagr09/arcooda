<?php
namespace app\controllers;

use app\helpers\Image;
use app\models\TwigBloags;
use app\models\TwigNews;
use app\models\TwigNewsLetter;
//Expects:
// $action

global $_GLOBALS;

// @todo We need a better staff preview detection method
if (!empty($_COOKIE['REMOTE_USER'] ?? false)) {
    unset($_GLOBALS['criteria_options']['news']);
}

switch ($action) {
    case 'view':
        if ($newses = TwigNews::search(['id' => $_GET['id']])) {
            $news = reset($newses);
            die($twig->load('news-view.twig')->render(['inputs' => [
                'page_title'       => strip_tags($news->title()),
                'page_description' => strip_tags($news->description()),
                'page_keywords'    => strip_tags($news->keywords()),
                'page_canonical'   => $news->url(),
                'page_breadcrumbs' => $news->breadcrumbs(),
                'posts'            => [$news, $news->previous(), $news->next()],
            ]]));
        }
        break;

    case 'image':
        if ($newses = TwigNews::search(['id' => $_GET['id']])) {
            $news = reset($newses);

            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [70, 70],    // widget-posts.twig
                [280, 250],
                [80, 80],    // post-page.twig
                [664, 0],    // post-page.twig
                [730, 485],  // post-card.twig
                [1903, 500], // post-page.twig
            ])) {
                $width = $height = 250;
            }

            $image_file = $news->image_file();
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            // @note this feels wrong, but it does the job
            if ($width > $height) {
                Image::$fit_strategy = 'app\helpers\Image::fitCover';
            }

            $cache_file = sprintf(
                '%s/protected/cache/news/image/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $news->id,
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

    case 'extra':
        global $DB;

        $news_id = 0;
        if ( ! preg_match('/^-?\d+$/', $_GET['id']) ) {
            if ($extra = $DB->query_first(sprintf(
                "SELECT * FROM `news_images` WHERE `name` = %s LIMIT 1",
                $DB->escape($_GET['id'])
            ), MYSQLI_ASSOC)) {
                $news_id = $extra['news_id'];
            }
        }else{
             if ($extra = $DB->query_first(sprintf(
                "SELECT * FROM `news_images` WHERE `id` = %d LIMIT 1",
                $_GET['id']
            ), MYSQLI_ASSOC)) {
                $news_id = $extra['news_id'];
            }
        }
        if ($newses = TwigNews::search(['id' => $news_id])) {
            $news = reset($newses);

            $watermark_file = $_SERVER['DOCUMENT_ROOT'] . '/protected/watermark.png';
            $profile_file   = $_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim';

            $width  = intval($_GET['size'] ?? $_GET['width'] ?? 0);
            $height = intval($_GET['size'] ?? $_GET['height'] ?? 0);
            if (!in_array([$width, $height], [
                [70, 70],
                [280, 250],
                [80, 80],    // post-page.twig
                [664, 0],    // post-page.twig
                [300, 300],     // widget-posts.twig
                [250, 250],     // widget-posts.twig
                [730, 485],  // post-card.twig
                [1903, 500], // post-page.twig
            ])) {
                $width  = 730;
                $height = 485;
            }
            $image_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/extra/' . $extra['name'];
            if (!is_file($image_file)) {
                redirect('/noimage.png?' . http_build_query($_GET));
                die();
            }

            $cache_file = sprintf(
                '%s/protected/cache/news/extra/%d_%sx%s.jpg',
                $_SERVER['DOCUMENT_ROOT'],
                $extra['id'],
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

    // @todo move this to a blog controller
    case 'ownernews':
        if ($newses = TwigBloags::search(['id' => $_GET['id']])) {
            $news = reset($newses);
            die($twig->load('ownernews-view.twig')->render(['inputs' => [
                'page_title'       => strip_tags($news->title()),
                'page_description' => strip_tags($news->description()),
                'page_keywords'    => strip_tags($news->keywords()),
                'page_canonical'   => $news->url(),
                'page_breadcrumbs' => $news->breadcrumbs(),
                'posts'            => [$news, 0, 0],
                'product_id'       => $_GET['product_id'] ?? 0,
            ]]));
        }
        break;
    case 'newsletter':
        $criteria['active'] = 'y';
        $criteria['site_id'] = 39;
        $criteria['display_online'] = 'y';
        
        $newsletter_list = TwigNewsLetter::search($criteria);
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Newsletters', 'url' => '/newsletters'],
        ];
        die($twig->load('site-news-letter.twig')->render(['inputs' => [
            'page_title'       => 'Arcooda Newsletters',
            'page_description' => 'Arcooda Newsletters',
            'page_keywords'    => 'Arcooda Newsletters',
            'page_canonical'   => 'Arcooda Newsletters',
            'page_breadcrumbs' => $breadcrumbs,
             'newsletter_list' => $newsletter_list  
        ]]));
    break;

     case 'newsletter-detail':
        $newsletter = new TwigNewsLetter($_GET['id']);
        $want_time = filemtime(__FILE__);
        $want_time = max($want_time, $newsletter->timestamp_time());
        header('Date: ' . gmdate(DATE_RFC1123, $_SERVER['REQUEST_TIME']));
        header('Last-Modified: ' . gmdate(DATE_RFC1123, $want_time));
        header('Content-Length: ' . strlen($newsletter->newsletter_content()));
        header('Content-Type: text/html; charset=UTF-8');
        header('Expires: ' . gmdate(DATE_RFC1123, gmmktime(gmdate('H') + 168)));
        echo $newsletter->newsletter_content();
        die;
    break;
}
