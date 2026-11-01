<?php
if(!file_exists("config/config.inc.php")) {
    header("Location: install/start.php");
} else {
    if(!file_exists("index.html")) {
        require_once('db/connection.php');
        $query = "SELECT code,name,meta_title,meta_description,meta_image,background_image FROM sml_maps WHERE show_in_first_page=1 AND active=1 LIMIT 1;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $code = $row['code'];
                $name = $row['name'];
                if(empty($row['meta_title'])) {
                    $meta_title = $name;
                } else {
                    $meta_title = $row['meta_title'];
                }
                if(empty($row['meta_description'])) {
                    $meta_description = '';
                } else {
                    $meta_description = $row['meta_description'];
                }
                $background_image = $row['background_image'];
                if(empty($row['meta_image'])) {
                    $meta_image = $background_image;
                } else {
                    $meta_image = $row['meta_image'];
                }
                $html_meta_image = "";
                $html_meta_description = "";
                if(!empty($meta_image)) {
                    $html_meta_image = '<meta itemprop="image" content="viewer/content/'.$meta_image.'">
                                        <meta property="og:image" content="viewer/content/'.$meta_image.'" />
                                        <meta property="twitter:image" content="viewer/content/'.$meta_image.'">';
                }
                if(!empty($meta_description)) {
                    $html_meta_description = '<meta itemprop="description" content="'.$meta_description.'">
                                            <meta name="description" content="'.$meta_description.'"/>
                                            <meta property="og:description" content="'.$meta_description.'" />
                                            <meta property="twitter:description" content="'.$meta_description.'">';
                }
                if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.$code.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "viewer/favicons/$code/favicon.ico";
                } else if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "viewer/favicons/favicon.ico";
                } else {
                    $favicon_url = "favicon.ico";
                }
                $html = <<<HTML_CODE
<html>
    <head>
        <title>$meta_title</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
        <link rel="icon" type="image/x-icon" href="$favicon_url">
        <meta property="og:type" content="website">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="og:url" content="viewer/index.php?code=$code">
        <meta property="twitter:url" content="viewer/index.php?code=$code">
        <meta itemprop="name" content="$meta_title">
        <meta property="og:title" content="$meta_title">
        <meta property="twitter:title" content="$meta_title">
        $html_meta_image
        $html_meta_description
        <style>
            html, body { margin: 0; padding: 0; overflow: hidden; }
            iframe { height: 100%; }
        </style>
    </head>
    <body>
        <iframe allowfullscreen allow="accelerometer; camera; display-capture; fullscreen; geolocation; gyroscope; magnetometer; microphone; midi; xr-spatial-tracking;" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="viewer/index.php?code=$code"></iframe>
    </body>
</html>
HTML_CODE;
                echo $html;
                exit;
            }
        }

        $query = "SELECT code,name,meta_title,meta_description,meta_image FROM sml_showcases WHERE show_in_first_page=1 LIMIT 1;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $code_showcase = $row['code'];
                $name_showcase = $row['name'];
                if(empty($row['meta_title'])) {
                    $meta_title = $name_showcase;
                } else {
                    $meta_title = $row['meta_title'];
                }
                $meta_description = $row['meta_description'];
                $meta_image = $row['meta_image'];
                $html_meta_image = "";
                $html_meta_description = "";
                if(!empty($meta_image)) {
                    $html_meta_image = '<meta itemprop="image" content="viewer/content/'.$meta_image.'">
                                        <meta property="og:image" content="viewer/content/'.$meta_image.'" />
                                        <meta property="twitter:image" content="viewer/content/'.$meta_image.'">';
                }
                if(!empty($meta_description)) {
                    $html_meta_description = '<meta itemprop="description" content="'.$meta_description.'">
                                            <meta name="description" content="'.$meta_description.'"/>
                                            <meta property="og:description" content="'.$meta_description.'" />
                                            <meta property="twitter:description" content="'.$meta_description.'">';
                }
                $url_iframe = "showcase/index.php?code=$code_showcase";
                if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'showcase'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.$code_showcase.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "showcase/favicons/$code_showcase/favicon.ico";
                } else if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'showcase'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "showcase/favicons/favicon.ico";
                } else {
                    $favicon_url = "favicon.ico";
                }
                $html = <<<HTML_CODE
<html>
    <head>
        <title>$meta_title</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
        <link rel="icon" type="image/x-icon" href="$favicon_url">
        <meta property="og:type" content="website">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="og:url" content="$url_iframe">
        <meta property="twitter:url" content="$url_iframe">
        <meta itemprop="name" content="$meta_title">
        <meta property="og:title" content="$meta_title">
        <meta property="twitter:title" content="$meta_title">
        $html_meta_image
        $html_meta_description
        <style>
            html, body { margin: 0; padding: 0; overflow: hidden; }
            iframe { height: 100%; }
        </style>
    </head>
    <body>
        <iframe allow="accelerometer; camera; display-capture; fullscreen; geolocation; gyroscope; magnetometer; microphone; midi; xr-spatial-tracking;" width="100%" height="100%" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="$url_iframe"></iframe>
    </body>
</html>
HTML_CODE;
                echo $html;
                exit;
            }
        }
        header("Location: backend/login.php");
    } else {
        header("Location: index.html");
    }
}