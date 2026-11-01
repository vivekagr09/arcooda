<?php
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if(!isset($_SESSION['check_update'])) {
    require_once(__DIR__."/../services/check_update.php");
    $_SESSION['check_update'] = false;
}
require_once(__DIR__."/../backend/functions.php");
require_once(__DIR__."/../db/connection.php");
$session_id = session_id();
$_SESSION['sml_ma']=$session_id;
$v = time();
if(is_ssl()) {
    $geolocation_disabled = "";
} else {
    $geolocation_disabled = "disabled";
}
$current_map = array();
$auto_start = 1;
$show_list = 0;
$default_zoom = 0;
$selected_zoom = 18;
$density_color = 0;
$password = false;
$map_language='';
$code = '';
$currentPath = $_SERVER['PHP_SELF'];
$pathInfo = pathinfo($currentPath);
$hostName = $_SERVER['HTTP_HOST'];
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$url = $protocol."://".$hostName.$pathInfo['dirname']."/";
$url_map = $url;
$url_logo = "";
$logo_height = 40;
$logo_height_mobile = 30;
$logo_link = "";
$font_provider = 'google';
$direction_map = 'google';
$streetview_map = 'google';
$loading_background_color = '#343434';
$loading_text_color = '#fffff';
$enable_intro = 0;
if(isset($_GET['ignore_embedded'])) {
    $ignore_embedded = $_GET['ignore_embedded'];
} else {
    $ignore_embedded = 0;
}
if((isset($_GET['furl'])) || (isset($_GET['code']))) {
    if (isset($_GET['furl'])) {
        $furl = $_GET['furl'];
        $query = "SELECT * FROM sml_maps WHERE friendly_url=? AND active=1 LIMIT 1;";
    }
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $query = "SELECT * FROM sml_maps WHERE code=? AND active=1 LIMIT 1;";
    }
    if($smt = $mysqli->prepare($query)) {
        if (isset($_GET['furl'])) {
            $smt->bind_param('s',$furl);
        } else {
            $smt->bind_param('s',$code);
        }
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)==1) {
                $current_map = array_shift($result);
                if(isset($current_map['show_list'])) $show_list = $current_map['show_list'];
                if(isset($current_map['default_zoom'])) $default_zoom = $current_map['default_zoom'];
                if(isset($current_map['selected_zoom'])) $selected_zoom = $current_map['selected_zoom'];
                if(isset($current_map['density_color'])) $density_color = $current_map['density_color'];
                if(isset($current_map['password'])) {
                    if(!empty($current_map['password'])) $password=true;
                }
                $map_language = $current_map['language'];
                $code = $current_map['code'];
                $url_map = $url."index.php?code=".$code;
                if(!empty($current_map['logo'])) {
                    $url_logo = $url."logos/".$current_map['logo'];
                }
                if(empty($map_language)) $map_language='';
                $darker_color_hex = adjustBrightness($current_map['main_color_hex'],-0.3);
                $query_log = "INSERT INTO sml_access_log(id_map,date_time) VALUES(?,NOW());";
                if($smt_log = $mysqli->prepare($query_log)) {
                    $smt_log->bind_param('i', $current_map['id']);
                    $smt_log->execute();
                }
                if(empty($current_map['street_basemap'])) $current_map['street_basemap']='';
                if(empty($current_map['satellite_basemap'])) $current_map['satellite_basemap']='';
                if(empty($current_map['street_maxzoom'])) $current_map['street_maxzoom']=20;
                if(empty($current_map['satellite_maxzoom'])) $current_map['satellite_maxzoom']=20;
                if(empty($current_map['geoJSON'])) $current_map['geoJSON']='';
                if(empty($current_map['meta_title'])) {
                    $meta_title = $current_map['name'];
                } else {
                    $meta_title = $current_map['meta_title'];
                }
                if(empty($current_map['meta_description'])) {
                    $meta_description = '';
                } else {
                    $meta_description = $current_map['meta_description'];
                }
                $background_image = $current_map['background_image'];
                $background_image_mobile = $current_map['background_image_mobile'];
                if(empty($background_image_mobile)) {
                    $background_image_mobile = $background_image;
                }
                if(empty($current_map['meta_image'])) {
                    $meta_image = $background_image;
                } else {
                    $meta_image = $current_map['meta_image'];
                }
                $logo_height = $current_map['logo_height'];
                $logo_height_mobile = $current_map['logo_height_mobile'];
                $logo_link = $current_map['logo_link'];
                $direction_map = $current_map['direction_map'];
                $streetview_map = $current_map['streetview_map'];
                $loading_background_color = $current_map['loading_background_color'];
                $loading_text_color = $current_map['loading_text_color'];
                $auto_start = $current_map['auto_start'];
                $array_share_providers = explode(",",$current_map['share_providers']);
                $enable_intro = ($current_map['enable_intro']) ? 1 : 0;
            }
        }
    }
}
if(count($current_map)==0) {
    die("Invalid link");
}
$num_markers = 0;
$id_map = $current_map['id'];
$query = "SELECT COUNT(id) as num FROM sml_markers WHERE id_map=$id_map AND to_validate=0;";
$result = $mysqli->query($query);
if($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $num_markers = $row['num'];

}
$cookie_policy = "";
$query = "SELECT language,language_domain,font_provider,share_providers,cookie_policy FROM sml_settings LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows==1) {
        $row=$result->fetch_array(MYSQLI_ASSOC);
        $font_provider = $row['font_provider'];
        $cookie_policy = $row['cookie_policy'];
        $array_share_providers_settings = explode(",",$row['share_providers']);
        if($map_language!='') {
            $language = $map_language;
        } else {
            $language = $row['language'];
        }
        switch($language) {
            case 'pt_BR':
                $lang_code='pt-BR';
                break;
            case 'pt_PT':
                $lang_code='pt-PT';
                break;
            case 'zh_CN':
                $lang_code='zh-CN';
                break;
            case 'zh_TW':
                $lang_code='zh-TW';
                break;
            case 'zh_HK':
                $lang_code='zh-hk';
                break;
            default:
                $lang_code = substr($language, 0, 2);
                break;
        }
        if (function_exists('gettext')) {
            if(defined('LC_MESSAGES')) {
                $result = setlocale(LC_MESSAGES, $language);
                if(!$result) {
                    setlocale(LC_MESSAGES, $language.'.UTF-8');
                }
                $result = putenv('LC_MESSAGES='.$language);
                if(!$result) {
                    putenv('LC_MESSAGES='.$language.'.UTF-8');
                }
            } else {
                $result = putenv('LC_ALL='.$language);
                if(!$result) {
                    putenv('LC_ALL='.$language.'.UTF-8');
                }
            }
            $domain = $row['language_domain'];
            $result = bindtextdomain($domain, "../locale");
            if(!$result) {
                $domain = "default";
                bindtextdomain($domain, "../locale");
            }
            bind_textdomain_codeset($domain, 'UTF-8');
            textdomain($domain);
        } else {
            function _($a) {
                return $a;
            }
        }
    }
}
$ga_tracking_id = $current_map['ga_tracking_id'];
if(isset($_GET['cat'])) {
    $cat_sel = $_GET['cat'];
} else {
    $cat_sel = 0;
}
if(isset($_GET['m'])) {
    $marker_sel = $_GET['m'];
} else {
    $marker_sel = 0;
}
if(isset($_GET['coord'])) {
    $coordinates = $_GET['coord'];
} else {
    $coordinates = '';
}
if(isset($_GET['zoom'])) {
    $default_zoom = $_GET['zoom'];
}
$add_marker_open = 0;
if(isset($_GET['addm'])) {
    $add_marker_open = $_GET['addm'];
    if($current_map['enable_add_marker']==0) {
        $add_marker_open=0;
    }
}
if($meta_image!='') {
    $meta_image_url = $url."content/".$meta_image;
}
$url_map_s = $url_map;
if($marker_sel!=0) {
    $url_map_s = $url_map."&m=".$marker_sel;
    $query = "SELECT m.id,m.name,m.description,i.image FROM sml_markers as m
                LEFT JOIN sml_images as i ON m.id=i.id_marker AND i.main=1
                WHERE m.id=$marker_sel
                GROUP BY m.id,m.name,i.image;";
    $result = $mysqli->query($query);
    if($result) {
        if($result->num_rows==1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $marker_name = $row['name'];
            $marker_description = strip_tags($row['description']);
            $marker_image = $row['image'];
            if(!empty($marker_image)) {
                $meta_image_url = $url."marker_images/".$marker_image;
            }
            if(!empty($marker_description)) {
                $meta_description = $marker_description;
            }
            $meta_title = $meta_title." - ".$marker_name;
        }
    }
}
$query = "SELECT COUNT(c.id) as count_categories FROM sml_categories as c WHERE c.id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $current_map['id']);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) == 1) {
            $row = array_shift($result);
            if($row['count_categories']==0) $current_map['enable_categories']=0;
        }
    }
}
if(!empty($current_map['default_extra_fields'])) {
    $default_extra_fields = json_decode($current_map['default_extra_fields'],true);
} else {
    $default_extra_fields = array();
}
if(!empty($current_map['default_am_fields'])) {
    $default_am_fields = json_decode($current_map['default_am_fields'],true);
} else {
    $default_am_fields = array();
}
if(!empty($current_map['default_am_placeholders'])) {
    $default_am_placeholders = json_decode($current_map['default_am_placeholders'],true);
} else {
    $default_am_placeholders = array();
}
if(!empty($current_map['intro_params'])) {
    $intro_params = json_decode($current_map['intro_params'],true);
} else {
    $intro_params = array();
}
$cookie_consent = $current_map['cookie_consent'];
?>
    <!DOCTYPE HTML>
    <html lang="<?php echo $lang_code; ?>" style="background-color:<?php echo $loading_background_color; ?>">
    <head>
        <title><?php echo $meta_title; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
        <meta property="og:type" content="website">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="og:url" content="<?php echo $url_map_s; ?>">
        <meta property="twitter:url" content="<?php echo $url_map_s; ?>">
        <meta itemprop="name" content="<?php echo $meta_title; ?>">
        <meta property="og:title" content="<?php echo $meta_title; ?>">
        <meta property="twitter:title" content="<?php echo $meta_title; ?>">
        <?php if($meta_image!='') : ?>
            <meta itemprop="image" content="<?php echo $meta_image_url; ?>">
            <meta property="og:image" content="<?php echo $meta_image_url; ?>" />
            <meta property="twitter:image" content="<?php echo $meta_image_url; ?>">
        <?php endif; ?>
        <?php if($meta_description!='') : ?>
            <meta itemprop="description" content="<?php echo $meta_description; ?>">
            <meta name="description" content="<?php echo $meta_description; ?>"/>
            <meta property="og:description" content="<?php echo $meta_description; ?>" />
            <meta property="twitter:description" content="<?php echo $meta_description; ?>">
        <?php endif; ?>
        <meta property="og:locale" content="<?php echo $language; ?>">
        <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.$code.DIRECTORY_SEPARATOR.'favicon.ico')) { ?>
        <link rel="icon" type="image/x-icon" href="favicons/<?php echo $code; ?>/favicon.ico">
        <?php } else if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.'favicon.ico')) { ?>
        <link rel="icon" type="image/x-icon" href="favicons/favicon.ico">
        <?php } ?>
        <?php switch ($font_provider) {
        case 'google': ?>
            <?php if($cookie_consent) { ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <script type="text/plain" data-category="functionality" data-service="Google Fonts">
                (function(d, l, s) {
                    const fontName = '<?php echo $current_map['font_viewer']; ?>';
                        const e = d.createElement(l);
                        e.rel = s;
                        e.type = 'text/css';
                        e.href = `https://fonts.googleapis.com/css2?family=${fontName}`;
                        e.id = 'font_viewer_link';
                        d.head.appendChild(e);
                      })(document, 'link', 'stylesheet');
            </script>
        <?php } else { ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel='stylesheet' type="text/css" crossorigin="anonymous" id="font_viewer_link" href="https://fonts.googleapis.com/css2?family=<?php echo $current_map['font_viewer']; ?>">
        <?php } ?>
        <?php break;
        case 'collabs': ?>
        <link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>
        <link rel="stylesheet" type="text/css" id="font_viewer_link" href="https://api.fonts.coollabs.io/css2?family=<?php echo $current_map['font_viewer']; ?>&display=swap">
            <?php break;
        } ?>
        <link type="text/css" rel="stylesheet" href="css/ol.css?v=8.2.0">
        <link type="text/css" rel="stylesheet" href="css/ol-ext.min.css?v=4.0.13">
        <link type="text/css" rel="stylesheet" href="vendor/clusterize/clusterize.css">
        <link rel="stylesheet" type="text/css" href="vendor/fontawesome-free/css/fontawesome.min.css?v=6.5.1">
        <link rel="stylesheet" type="text/css" href="vendor/fontawesome-free/css/solid.min.css?v=6.5.1">
        <link rel="stylesheet" type="text/css" href="vendor/fontawesome-free/css/regular.min.css?v=6.5.1">
        <link rel="stylesheet" type="text/css" href="vendor/fontawesome-free/css/brands.min.css?v=6.5.1">
        <link type="text/css" rel="stylesheet" href="vendor/Gallery/css/blueimp-gallery.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/jquery_modal/jquery.modal.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/tooltipster/css/tooltipster.bundle.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/fancybox/jquery.fancybox.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/owl/assets/owl.carousel.min.css">
        <link type="text/css" rel="stylesheet" href="vendor/owl/assets/owl.theme.default.min.css">
        <link type="text/css" rel="stylesheet" href="css/jquery.dialog.min.css">
        <link type="text/css" rel="stylesheet" href="css/progress.css">
        <link type="text/css" rel="stylesheet" href="vendor/ion.rangeSlider/ion.rangeSlider.min.css">
        <link href="https://cesium.com/downloads/cesiumjs/releases/1.108/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="vendor/jquery-confirm/jquery-confirm.min.css">
        <?php if($cookie_consent) : ?>
            <link rel="stylesheet" type="text/css" href="../backend/vendor/cookieconsent/cookieconsent.min.css?v=3.0.0">
        <?php endif; ?>
        <link type="text/css" rel="stylesheet" href="css/index.css?v=<?php echo $v; ?>">
        <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom.css')) : ?>
            <link type="text/css" rel="stylesheet" href="css/custom.css?v=<?php echo $v; ?>">
        <?php endif; ?>
        <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_'.$code.'.css')) : ?>
            <link type="text/css" rel="stylesheet" href="css/custom_<?php echo $code; ?>.css?v=<?php echo $v; ?>">
        <?php endif; ?>
        <script type="text/javascript" src="js/ol.js?v=8.2.0"></script>
        <script type="text/javascript" src="js/ol-ext.min.js?v=4.0.13"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="vendor/dragscroll.js"></script>
        <script type="text/javascript" src="js/jquery.searchable.min.js"></script>
        <script type="text/javascript" src="vendor/jquery_modal/jquery.modal.min.js"></script>
        <script type="text/javascript" src="js/gifler.min.js"></script>
        <script type="text/javascript" src="js/jquery-captcha.min.js"></script>
        <script type="text/javascript" src="vendor/tooltipster/js/tooltipster.bundle.min.js"></script>
        <script type="text/javascript" src="https://api.mapbox.com/mapbox.js/plugins/arc.js/v0.1.0/arc.js"></script>
        <script type="text/javascript" src="vendor/fancybox/jquery.fancybox.min.js"></script>
        <script type="text/javascript" src="vendor/clusterize/clusterize.min.js"></script>
        <script type="text/javascript" src="vendor/owl/owl.carousel.min.js"></script>
        <script type="text/javascript" src="js/jquery.dialog.min.js"></script>
        <script type="text/javascript" src="js/progress.min.js"></script>
        <script type="text/javascript" src="vendor/ion.rangeSlider/ion.rangeSlider.min.js"></script>
        <script src="https://cesium.com/downloads/cesiumjs/releases/1.100/Build/Cesium/Cesium.js"></script>
        <script type="text/javascript" src="js/olcesium.js?v=2.17"></script>
        <script type="text/javascript" src="vendor/jquery-confirm/jquery-confirm.min.js"></script>
        <?php if($cookie_consent) : ?>
            <script type="text/javascript" src="../backend/vendor/cookieconsent/cookieconsent.min.js?v=3.0.0"></script>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom.php')) {
            include(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom.php');
        } ?>
        <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom_'.$code.'.php')) {
            include(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom_'.$code.'.php');
        } ?>
    </head>
    <body id="fullscreen">
    <div class="map_container">
        <style>
            *{ font-family: '<?php echo $current_map['font_viewer']; ?>', sans-serif; }
            .progress:after,.progress-2:after,.progress-3:after,.progress-4:after,.progress-5:after{
                background: <?php echo $current_map['main_color_hex']; ?>;
            }
            .ol-control button {
                color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .ol-control button:focus, .ol-control button:hover {
                background-color: <?php echo $current_map['main_color_hex']; ?>;
                color: #ffffff;
            }
            .nav_marker_next, .nav_marker_prev {
                color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .nav_marker_next:focus, .nav_marker_next:hover, .nav_marker_prev:focus, .nav_marker_prev:hover {
                background-color: <?php echo $current_map['main_color_hex']; ?>;
                color: #ffffff;
            }
            .tooltipster-sidetip.tooltipster-borderless .tooltipster-box {
                background: <?php echo $current_map['main_color_hex']; ?>;
            }
            .tooltipster-sidetip.tooltipster-borderless.tooltipster-bottom .tooltipster-arrow-border {
                border-bottom-color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .tooltipster-sidetip.tooltipster-borderless.tooltipster-left .tooltipster-arrow-border {
                border-left-color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .tooltipster-sidetip.tooltipster-borderless.tooltipster-right .tooltipster-arrow-border {
                border-right-color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .tooltipster-sidetip.tooltipster-borderless.tooltipster-top .tooltipster-arrow-border {
                border-top-color: <?php echo $current_map['main_color_hex']; ?>;
            }
            .direction_profile {
                color: <?php echo $current_map['main_color_hex']; ?>;
                border: 1px solid <?php echo $current_map['main_color_hex']; ?>;
            }
            .direction_profile_active {
                background-color: <?php echo $current_map['main_color_hex']; ?>;
            }
            <?php if(!$current_map['sheet_detail']) : ?> .list_block { cursor: default; pointer-events: none; } <?php endif; ?>
        </style>
        <div class="loading hidden" style="color:<?php echo $loading_text_color; ?>">
            <i onclick="start_map();" id="icon_play" class="fas fa-play"></i>
            <div class="protect">
                <?php if($password) { ?>
                    <span class="protect_title noselect"><?php echo _("This Map is password protected"); ?></span><br>
                    <span class="protect_description noselect"></span>
                    <div class="password-wrapper cf">
                        <input autocomplete="new-password" placeholder="<?php echo _("type your password"); ?>" id="map_password" type="text" style="box-shadow:none;color:<?php echo $loading_text_color; ?>" />
                        <button style="background-color:<?php echo $loading_text_color; ?>;color:<?php echo $loading_background_color; ?>" id="btn_check_password" onclick="check_password_map();"><i class="fas fa-sign-in-alt"></i></button>
                    </div>
                <?php } ?>
            </div>
            <div class="progress-circle noselect"></div>
            <div class="progress_map">
                <?php if($current_map['logo']!='') : ?>
                    <img src="logos/<?php echo $current_map['logo']; ?>" />
                <?php endif; ?>
                <h3 class="noselect" id="name_map"><?php echo $current_map['name']; ?></h3>
            </div>
        </div>
        <div id="background_loading" class="background_opacity"></div>
        <?php if($enable_intro) : ?>
        <div style="background-color:<?php echo $intro_params['background']; ?>;" class="intro noselect">
            <div class="intro_content noselect">
                <?php if(!empty($intro_params['title'])) : ?>
                <div class="intro_title noselect"><?php echo $intro_params['title']; ?></div>
                <style>
                    .intro_title {
                        font-size:<?php echo $intro_params['title_size']; ?>px;
                        color:<?php echo $intro_params['title_color']; ?>;
                    }
                    @media screen and (max-width: 600px) {
                        .intro_title {
                            font-size:<?php echo $intro_params['title_size']/1.3; ?>px;
                        }
                    }
                </style>
                <?php endif; ?>
                <?php if(!empty($intro_params['description'])) : ?>
                <div class="intro_description noselect"><?php echo nl2br($intro_params['description']); ?></div>
                <style>
                    .intro_description {
                        font-size:<?php echo $intro_params['description_size']; ?>px;
                        color:<?php echo $intro_params['description_color']; ?>;
                    }
                    @media screen and (max-width: 600px) {
                        .intro_description {
                            font-size:<?php echo $intro_params['description_size']/1.3; ?>px;
                        }
                    }
                </style>
                <?php endif; ?>
                <?php if(!empty($current_map['intro_image'])) : ?>
                <div class="intro_image noselect"><img src="content/<?php echo $current_map['intro_image']; ?>" /></div>
                <style>
                    .intro_image img {
                        max-height:<?php echo $intro_params['image_height']; ?>px;
                    }
                    @media screen and (max-width: 600px) {
                        .intro_image img {
                            max-height:<?php echo $intro_params['image_height']/1.3; ?>px;
                        }
                    }
                </style>
                <?php endif; ?>
                <?php if(!empty($intro_params['button'])) { ?>
                <div style="" onclick="close_intro();" class="intro_button noselect"><?php echo $intro_params['button']; ?></div>
                <style>
                    .intro_button {
                        font-size:<?php echo $intro_params['button_size']; ?>px;
                        background:<?php echo $intro_params['button_background']; ?>;
                        color:<?php echo $intro_params['button_color']; ?>;
                        border: 2px solid <?php echo $intro_params['button_color']; ?>;
                    }
                    .intro_button:hover {
                        background: <?php echo $intro_params['button_color']; ?> !important;
                        color: <?php echo $intro_params['button_background']; ?> !important;
                        border: 2px solid <?php echo $intro_params['button_background']; ?> !important;
                    }
                    @media screen and (max-width: 600px) {
                        .intro_button {
                            font-size:<?php echo $intro_params['button_size']/1.3; ?>px;
                        }
                    }
                </style>
                <?php } else { ?>
                    <script>
                        document.getElementsByClassName('intro')[0].addEventListener('click', function(event) {
                            close_intro();
                        });
                    </script>
                <?php } ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="map_render">
            <div style="pointer-events: <?php echo ($current_map['logo']!='' && !empty($logo_link)) ? 'initial' : 'none'; ?>" class="logo">
                <?php if($current_map['logo']!='') : ?>
                    <?php if(!empty($logo_link)) { echo "<a target='_blank' href='$logo_link'>"; } ?>
                    <img style="height:<?php echo $logo_height; ?>px" src="logos/<?php echo $current_map['logo']; ?>">
                    <?php if(!empty($logo_link)) { echo "</a>"; } ?>
                <?php endif; ?>
            </div>
            <div style="pointer-events: <?php echo ($current_map['logo_mobile']!='' && !empty($logo_link)) ? 'initial' : 'none'; ?>" class="logo_mobile">
                <?php if($current_map['logo_mobile']!='') : ?>
                    <?php if(!empty($logo_link)) { echo "<a target='_blank' href='$logo_link'>"; } ?>
                    <img style="height:<?php echo $logo_height_mobile; ?>px" src="logos/<?php echo $current_map['logo_mobile']; ?>">
                    <?php if(!empty($logo_link)) { echo "</a>"; } ?>
                <?php endif; ?>
            </div>
            <div class="weather">
                <div id="w_icon"></div>
                <span>--.- &#8451;</span>
            </div>
            <div onclick="geolocate()" class="my_position_icon noselect <?php echo $geolocation_disabled; ?>">
                <i class="fas fa-crosshairs"></i>
            </div>
            <div class="categories_div noselect">

            </div>
            <div class="center_right_controls">
                <div onclick="prev_marker()" class="nav_marker_prev noselect disabled <?php echo ($current_map['nav_markers']==0) ? 'hidden' : ''; ?>">
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div onclick="next_marker()" class="nav_marker_next noselect disabled <?php echo ($current_map['nav_markers']==0) ? 'hidden' : ''; ?>">
                    <i class="fas fa-chevron-down"></i>
                </div>
                <?php if($current_map['enable_story']) : ?>
                    <div onclick="show_story();" class="btn_story noselect disabled hidden">
                        <i class="fas fa-list-ol"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="center_left_controls">
                <?php if($current_map['enable_share_map']) : ?>
                    <div onclick="toggle_share_map();" class="btn_share_map noselect">
                        <i class="fas fa-share-nodes"></i>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($current_map['enable_share_map']) : ?>
            <div class="share_map_section noselect">
                <div class="a2a_kit a2a_kit_size_32 a2a_default_style share-buttons">
                    <?php if(in_array('copy_link',$array_share_providers_settings)) : ?><a class="a2a_button_copy_link <?php echo (in_array('copy_link',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('email',$array_share_providers_settings)) : ?><a class="a2a_button_email <?php echo (in_array('email',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('whatsapp',$array_share_providers_settings)) : ?><a class="a2a_button_whatsapp <?php echo (in_array('whatsapp',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('facebook',$array_share_providers_settings)) : ?><a class="a2a_button_facebook <?php echo (in_array('facebook',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('threads',$array_share_providers_settings)) : ?><a class="a2a_button_threads <?php echo (in_array('threads',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('twitter',$array_share_providers_settings)) : ?><a class="a2a_button_x <?php echo (in_array('twitter',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('linkedin',$array_share_providers_settings)) : ?><a class="a2a_button_linkedin <?php echo (in_array('linkedin',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('telegram',$array_share_providers_settings)) : ?><a class="a2a_button_telegram <?php echo (in_array('telegram',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('facebook_messenger',$array_share_providers_settings)) : ?><a class="a2a_button_facebook_messenger <?php echo (in_array('facebook_messenger',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('pinterest',$array_share_providers_settings)) : ?><a class="a2a_button_pinterest <?php echo (in_array('pinterest',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('reddit',$array_share_providers_settings)) : ?><a class="a2a_button_reddit <?php echo (in_array('reddit',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('line',$array_share_providers_settings)) : ?><a class="a2a_button_line <?php echo (in_array('line',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('viber',$array_share_providers_settings)) : ?><a class="a2a_button_viber <?php echo (in_array('viber',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('vk',$array_share_providers_settings)) : ?><a class="a2a_button_vk <?php echo (in_array('vk',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('qzone',$array_share_providers_settings)) : ?><a class="a2a_button_qzone <?php echo (in_array('qzone',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                    <?php if(in_array('wechat',$array_share_providers_settings)) : ?><a class="a2a_button_wechat <?php echo (in_array('wechat',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="bottom_left_controls">
                <div class="switch_style">
                    <img onclick="change_map_style('default_style');" id="default_style" style="display: none" src="images/street.jpg" />
                    <img onclick="change_map_style('satellite_style');" id="satellite_style" src="images/satellite.jpg" />
                </div>
                <div onclick="toggle_globe()" class="globe_icon noselect hidden">
                    <i class="fas fa-globe"></i>
                </div>
                <div onclick="toggle_connections()" class="connections_icon noselect active_c hidden">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div onclick="toggle_featured()" class="featured_icon noselect active_c hidden">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>
            <?php if($current_map['enable_add_marker']!=0) : ?>
                <div style="opacity:0;pointer-events:none" onclick="open_add_new_marker()" class="new_marker_btn noselect">
                    <i style="vertical-align:middle"  class="fas fa-plus-circle"></i>&nbsp;&nbsp;<?php echo _("add new marker"); ?>
                </div>
            <?php endif; ?>
            <div class="new_marker_msg noselect">
                <i style="vertical-align:middle"  class="fas fa-mouse"></i>&nbsp;&nbsp;<?php echo _("click on the map and place your marker"); ?>&nbsp;&nbsp;<i onclick="close_new_marker(false)" class="new_marker_cancel fas fa-times-circle"></i>
            </div>
            <div class="drag_marker_msg noselect">
                <i style="vertical-align:middle"  class="far fa-hand-rock"></i>&nbsp;&nbsp;<?php echo _("drag the marker to adjust the position"); ?>&nbsp;&nbsp;<span onclick="confirm_drag_new_marker()" class="new_marker_confirm"><i class="fas fa-check"></i>&nbsp;&nbsp;<?php echo _("confirm"); ?></span>&nbsp;&nbsp;<i onclick="close_new_marker(false)" class="new_marker_cancel fas fa-times-circle"></i>
            </div>
            <div id="featured_container"></div>
            <div class="sheet_detail dragscroll">
                <div class="sheet_detail_container">
                    <div onclick="close_sheet_detail()" id="close_btn_sd" class="<?php echo ($current_map['enable_search']) ? 'hidden' : ''; ?>">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="sheet_detail_image noselect" style="background-image:url('images/placeholder.jpg');"></div>
                    <div id="store_name" class="sheet_detail_name noselect"></div>
                    <div id="button_actions">
                        <div title="<?php echo _("DIRECTIONS"); ?>" class="btn_direction <?php echo (!$current_map['enable_directions']) ? 'hidden' : ''; ?>"><i class="fas fa-directions"></i></div>
                        <div title="<?php echo _("STREET VIEW"); ?>" class="btn_streetview <?php echo (!$current_map['enable_streetview']) ? 'hidden' : ''; ?>"><i class="fas fa-street-view"></i></div>
                        <div title="<?php echo _("WRITE A REVIEW"); ?>" onclick="open_modal_new_review();" class="btn_review disabled <?php echo (!$current_map['enable_reviews']) ? 'hidden' : ''; ?>"><i class="fas fa-feather-alt"></i></div>
                        <div style="display: none;" title="" onclick="" class="btn_custom_1"><i class=""></i></div>
                    </div>
                    <hr>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div id="store_address" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="fas fa-link"></i></div>
                        <div id="store_website" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="far fa-envelope"></i></div>
                        <div id="store_email" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="fas fa-phone"></i></div>
                        <div id="store_phone" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="fab fa-whatsapp"></i></div>
                        <div id="store_whatsapp" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="far fa-clock"></i></div>
                        <div id="store_hours" class="features_value"></div>
                    </div>
                    <div class="sheet_detail_features noselect">
                        <div class="features_icon"><i class="far fa-comment-alt"></i></div>
                        <div id="store_description" class="features_value"></div>
                    </div>
                    <?php for($k=1;$k<=20;$k++) { ?>
                        <div class="sheet_detail_features noselect" style="display: none">
                            <div id="icon_e<?php echo $k; ?>" class="features_icon"><i class=""></i></div>
                            <div id="field_e<?php echo $k; ?>" class="features_value"></div>
                        </div>
                    <?php } ?>
                    <div class="sheet_detail_features sheet_detail_feature_images noselect">
                        <div class="features_icon" style="margin-top:5px;"><i class="far fa-images"></i></div>
                        <div class="features_value">
                            <div id="sheet_images" style="margin-left:-5px;"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="share_section noselect <?php echo (!$current_map['enable_share']) ? 'hidden' : ''; ?>">
                        <div onclick="alert_share_cookie_denied();" class="btn_share_cookie_denied noselect">
                            <i class="fas fa-share-nodes"></i>
                        </div>
                        <div class="a2a_kit a2a_kit_size_32 a2a_default_style share-buttons">
                            <?php if(in_array('copy_link',$array_share_providers_settings)) : ?><a class="a2a_button_copy_link <?php echo (in_array('copy_link',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('email',$array_share_providers_settings)) : ?><a class="a2a_button_email <?php echo (in_array('email',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('whatsapp',$array_share_providers_settings)) : ?><a class="a2a_button_whatsapp <?php echo (in_array('whatsapp',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('facebook',$array_share_providers_settings)) : ?><a class="a2a_button_facebook <?php echo (in_array('facebook',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('threads',$array_share_providers_settings)) : ?><a class="a2a_button_threads <?php echo (in_array('threads',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('twitter',$array_share_providers_settings)) : ?><a class="a2a_button_x <?php echo (in_array('twitter',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('linkedin',$array_share_providers_settings)) : ?><a class="a2a_button_linkedin <?php echo (in_array('linkedin',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('telegram',$array_share_providers_settings)) : ?><a class="a2a_button_telegram <?php echo (in_array('telegram',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('facebook_messenger',$array_share_providers_settings)) : ?><a class="a2a_button_facebook_messenger <?php echo (in_array('facebook_messenger',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('pinterest',$array_share_providers_settings)) : ?><a class="a2a_button_pinterest <?php echo (in_array('pinterest',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('reddit',$array_share_providers_settings)) : ?><a class="a2a_button_reddit <?php echo (in_array('reddit',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('line',$array_share_providers_settings)) : ?><a class="a2a_button_line <?php echo (in_array('line',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('viber',$array_share_providers_settings)) : ?><a class="a2a_button_viber <?php echo (in_array('viber',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('vk',$array_share_providers_settings)) : ?><a class="a2a_button_vk <?php echo (in_array('vk',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('qzone',$array_share_providers_settings)) : ?><a class="a2a_button_qzone <?php echo (in_array('qzone',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                            <?php if(in_array('wechat',$array_share_providers_settings)) : ?><a class="a2a_button_wechat <?php echo (in_array('wechat',$array_share_providers) ? '' : 'hidden'); ?>"></a><?php endif; ?>
                        </div>
                        <script>
                            function share_config(share_data) {
                                var marker_url = window.url_map+'&m='+window.marker_current_id;
                                return {
                                    url: marker_url
                                };
                            }
                            var a2a_config = a2a_config || {};
                            a2a_config.callbacks = a2a_config.callbacks || [];
                            a2a_config.callbacks.push({
                                share: share_config,
                            });
                        </script>
                        <hr>
                    </div>
                    <div class="review-section noselect <?php echo (!$current_map['enable_reviews']) ? 'hidden' : ''; ?>">
                        <div class="rating-part">
                            <div class="average-rating">
                                <h2>-</h2>
                                <span></span>
                                <p><?php echo _("Average Rating"); ?></p>
                            </div>
                            <div class="loder-ratimg">
                                <style id="rating_bars_style"></style>
                                <div class="progress"></div>
                                <div class="progress-2"></div>
                                <div class="progress-3"></div>
                                <div class="progress-4"></div>
                                <div class="progress-5"></div>
                            </div>
                            <div class="start-part">
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <br>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <br>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <br>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <br>
                                <i aria-hidden="true" class="fas fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                                <i aria-hidden="true" class="far fa-star"></i>
                            </div>
                            <div style="clear: both;"></div>
                            <hr>
                            <div id="div_comments"></div>
                        </div>
                    </div>
                </div>
                <div onclick="resize_sheet_detail('up');" class="resize_sheet_icon"><i class="fas fa-caret-up"></i></div>
            </div>
            <div class="direction_detail">
                <div class="direction_detail_container">
                    <div id="direction_loading">
                        <div class="direction_features noselect">
                            <div class="features_icon"><i class="fas fa-spin fa-circle-notch"></i></div>
                            <div class="features_value">
                                <?php echo _("loading route in progress"); ?>...
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" id="direction_detail_div">
                        <div class="direction_features noselect">
                            <div data-profile="driving-car" onclick="change_ors_direction('driving-car');" class="direction_profile direction_profile_active"><i class="fas fa-car"></i></div>
                            <div data-profile="foot-walking" onclick="change_ors_direction('foot-walking');" class="direction_profile"><i class="fas fa-walking"></i></div>
                            <div data-profile="cycling-regular" onclick="change_ors_direction('cycling-regular');" class="direction_profile"><i class="fas fa-bicycle"></i></div>
                            <div data-profile="wheelchair" onclick="change_ors_direction('wheelchair');" class="direction_profile"><i class="fas fa-wheelchair"></i></div>
                            <div data-profile="driving-hgv" onclick="change_ors_direction('driving-hgv');" class="direction_profile"><i class="fas fa-bus"></i></div>
                        </div>
                        <div class="direction_features noselect">
                            <div class="features_icon"><div style="background-color:green;">A</div></div>
                            <div class="features_value">
                                <input autofill="off" autocomplete="off" id="direction_start_point" tyoe="text" readonly value="<?php echo _("My Location"); ?>" />
                            </div>
                        </div>
                        <div class="direction_features noselect">
                            <div class="features_icon"><div style="background-color:red;">B</div></div>
                            <div class="features_value">
                                <input autofill="off" autocomplete="off" id="direction_end_point" tyoe="text" readonly value="" />
                            </div>
                        </div>
                        <div class="direction_features noselect" style="margin-top: 10px">
                            <div class="features_icon"><i class="fas fa-clock"></i></div>
                            <div class="features_value">
                                <b id="direction_duration">--</b>
                            </div>
                        </div>
                        <div class="direction_features noselect">
                            <div class="features_icon"><i class="fas fa-arrows-alt-h"></i></div>
                            <div class="features_value">
                                <b id="direction_length">--</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="new_sheet_detail">
                <div class="new_sheet_detail_container">
                    <form autofill="off" autocomplete="off">
                        <div class="new_sheet_detail_image noselect <?php echo (array_key_exists('images',$default_am_fields)) ? (($default_am_fields['images']==1) ? '' : 'hidden') : ''; ?>" style="background-image:url('images/placeholder.jpg');"></div>
                        <div id="new_store_name" class="sheet_detail_name noselect">
                            <input autofill="off" autocomplete="off" tabindex="1" id="new_name" tyoe="text" placeholder="<?php echo _("Name"); ?> *" />
                        </div>
                        <div id="button_actions">
                            <div onclick="close_new_marker(false)" title="<?php echo _("CANCEL"); ?>"class="btn_cancel_marker" ><i class="fas fa-times"></i></div>
                            <div onclick="add_new_marker();" title="<?php echo _("ADD"); ?>" class="btn_add_marker" ><i class="fas fa-plus"></i></div>
                        </div>
                        <hr>
                        <div class="sheet_detail_features noselect">
                            <div class="features_icon"><i class="fas fa-globe"></i></div>
                            <div id="new_store_coordinates" class="features_value">
                                <input autofill="off" autocomplete="off" style="margin-right: 5px;" id="new_latitude" tyoe="text" placeholder="<?php echo _("Latitude"); ?> *" />
                                <input autofill="off" autocomplete="off" id="new_longitude" tyoe="text" placeholder="<?php echo _("Longitude"); ?> *" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('categories',$default_am_fields)) ? (($default_am_fields['categories']==1) ? '' : 'hidden') : ''; ?> <?php echo ($current_map['enable_categories']==0) ? 'hidden' : ''; ?>">
                            <div class="features_icon"><i class="fas fa-list"></i></div>
                            <div id="new_category" class="features_value">
                                <select multiple id="new_category">
                                    <?php echo get_categories($current_map['id'],0); ?>
                                </select>
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect">
                            <div class="features_icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div id="new_store_street" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="2" id="new_street" tyoe="text" placeholder="<?php echo _("Street"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect">
                            <div class="features_icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div id="new_store_city" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="3" id="new_city" tyoe="text" placeholder="<?php echo _("City"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect">
                            <div class="features_icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div id="new_store_postal_code" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="4" id="new_postal_code" tyoe="text" placeholder="<?php echo _("Postal Code"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect">
                            <div class="features_icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div id="new_store_country" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="5" id="new_country" tyoe="text" placeholder="<?php echo _("Country"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('website',$default_am_fields)) ? (($default_am_fields['website']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="fas fa-link"></i></div>
                            <div id="new_store_website" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="6" id="new_website" tyoe="text" placeholder="<?php echo _("Website"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('email',$default_am_fields)) ? (($default_am_fields['email']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="far fa-envelope"></i></div>
                            <div id="new_store_email" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="7" id="new_email" tyoe="text" placeholder="<?php echo _("E-mail"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('phone',$default_am_fields)) ? (($default_am_fields['phone']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="fas fa-phone"></i></div>
                            <div id="new_store_phone" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="8" id="new_phone" tyoe="text" placeholder="<?php echo _("Phone"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('whatsapp',$default_am_fields)) ? (($default_am_fields['whatsapp']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="fab fa-whatsapp"></i></div>
                            <div id="new_store_whatsapp" class="features_value">
                                <input autofill="off" autocomplete="off" tabindex="9" id="new_whatsapp" tyoe="text" placeholder="<?php echo _("Whatsapp"); ?>" />
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('hours',$default_am_fields)) ? (($default_am_fields['hours']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="far fa-clock"></i></div>
                            <div id="new_store_hours" class="features_value">
                                <textarea autofill="off" autocomplete="off" tabindex="10" id="new_hours" rows="2" placeholder="<?php echo _("Hours"); ?>"></textarea>
                            </div>
                        </div>
                        <div class="sheet_detail_features noselect <?php echo (array_key_exists('description',$default_am_fields)) ? (($default_am_fields['description']==1) ? '' : 'hidden') : ''; ?>">
                            <div class="features_icon"><i class="far fa-comment-alt"></i></div>
                            <div id="new_store_description" class="features_value">
                                <textarea autofill="off" autocomplete="off" tabindex="11" id="new_description" rows="2" placeholder="<?php echo _("Description"); ?>"></textarea>
                            </div>
                        </div>
                        <?php for($i=1;$i<=20;$i++) { ?>
                            <div class="sheet_detail_features noselect <?php echo (array_key_exists('field_'.$i,$default_am_fields)) ? (($default_am_fields['field_'.$i]==1) ? '' : 'hidden') : 'hidden'; ?>">
                                <div class="features_icon"><i class="<?php echo (array_key_exists($i,$default_extra_fields)) ? $default_extra_fields[$i] : 'far fa-circle'; ?>"></i></div>
                                <div id="new_store_field_<?php echo $i; ?>" class="features_value new_store_field">
                                    <textarea autofill="off" autocomplete="off" tabindex="<?php echo (11+$i); ?>" id="new_field_<?php echo $i; ?>" rows="2" placeholder="<?php echo (array_key_exists($i,$default_am_placeholders)) ? (!empty($default_am_placeholders[$i]) ? $default_am_placeholders[$i] : '') : ''; ?>"></textarea>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                    <div class="sheet_detail_features sheet_detail_feature_images noselect <?php echo (array_key_exists('images',$default_am_fields)) ? (($default_am_fields['images']==1) ? '' : 'hidden') : ''; ?>">
                        <div class="features_icon"><i class="far fa-images"></i></div>
                        <div class="features_value">
                            <div id="sheet_images" style="margin-left:-5px;margin-top: -5px;">
                                <div id="uploaded_images"></div>
                                <form autofill="off" autocomplete="off" id="frm" action="ajax/upload_new_marker_image.php" method="POST" enctype="multipart/form-data">
                                    <label for="txtFile">
                                        <div id="new_image_btn"><i class="fas fa-upload"></i></div>
                                    </label>
                                    <input accept="image/*" style="display: none;" type="file" class="form-control" id="txtFile" name="txtFile" />
                                    <input style="display: none;" type="submit" class="btn btn-block btn-success" id="btnUpload" />
                                </form>
                                <button onclick="delete_images();" id="btn_delete_images" style="clear:both;display:block;pointer-events:none;opacity:0.5"><?php echo _("delete images"); ?></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div id="modal_new_review" class="modal">
                <form autofill="off" autocomplete="off" id="form_new_review" action="" method="post" onsubmit="add_review();return false;">
                    <fieldset class="noselect" id="new_rating">
                        <span><?php echo _("Your Rating"); ?></span>&nbsp;&nbsp;
                        <i onclick="set_rating(1);" aria-hidden="true" class="far fa-star"></i>
                        <i onclick="set_rating(2);" aria-hidden="true" class="far fa-star"></i>
                        <i onclick="set_rating(3);" aria-hidden="true" class="far fa-star"></i>
                        <i onclick="set_rating(4);" aria-hidden="true" class="far fa-star"></i>
                        <i onclick="set_rating(5);" aria-hidden="true" class="far fa-star"></i>
                    </fieldset>
                    <fieldset>
                        <input autofill="off" autocomplete="off" id="review_name" placeholder="<?php echo _("Your Name"); ?> *" type="text" tabindex="1" required autofocus>
                    </fieldset>
                    <fieldset>
                        <input autofill="off" autocomplete="off" id="review_email" placeholder="<?php echo _("Your Email Address (optional)"); ?>" type="email" tabindex="2">
                    </fieldset>
                    <fieldset>
                        <textarea autofill="off" autocomplete="off" id="review_comment" placeholder="<?php echo _("Type your Review here"); ?> *" tabindex="3" required></textarea>
                    </fieldset>
                    <fieldset>
                        <canvas id="captcha_canvas"></canvas>
                        <input autofill="off" autocomplete="off" id="captcha_code" type="text" placeholder="<?php echo _("Type the code above"); ?> *" tabindex="4" required />
                    </fieldset>
                    <fieldset>
                        <button id="review_submit" name="submit" type="submit" class="disabled"><?php echo _("Submit"); ?></button>
                    </fieldset>
                </form>
                <div id="review_feedback"><?php echo _("Thanks for your review!"); ?></div>
            </div>
            <div class="search_div noselect <?php echo (!$current_map['enable_search'] && !$current_map['enable_list'] && !$current_map['enable_categories']) ? 'hidden' : ''; ?>">
                <input id="searchbox" type="search" class="<?php echo (!$current_map['enable_search']) ? 'hidden' : ''; ?>" placeholder="<?php echo _("Search"); ?>..." />
                <div onclick="open_list_detail();" class="list_icon disabled noselect <?php echo (!$current_map['enable_list']) ? 'hidden' : ''; ?>">
                    <i class="fas fa-bars"></i>
                </div>
                <div onclick="toggle_categories();" class="filter_icon noselect <?php echo (!$current_map['enable_categories']) ? 'hidden' : ''; ?>">
                    <span style="opacity:0;" id="count_categories">0</span>
                    <i class="fas fa-filter"></i>
                </div>
                <div class="search_icon noselect <?php echo (!$current_map['enable_search']) ? 'hidden' : ''; ?>">
                    <span onclick="open_radius_m();" id="search_radius_m">--</span><i class="fas fa-search"></i>
                </div>
                <div id="search_radius_container">
                    <div style="float:left;width:calc(100% - 64px);margin-left:6px;">
                        <input id="search_radius_slider" type="text" class="js-range-slider"
                               data-type="single"
                               data-min="1000"
                               data-max="100000"
                               data-step="1000"
                               data-from="<?php echo ($current_map['search_radius_m']==-1) ? '100000' : $current_map['search_radius_m']; ?>"
                               data-grid="false"
                               data-hide-min-max="true"
                               data-hide-from-to="true"
                               data-skin="round"
                               data-disable="<?php echo ($current_map['search_radius_m']==-1) ? 'true' : 'false'; ?>"
                        />
                    </div>
                    <div style="float:right;width:49px;text-align:right;margin-right:6px;">
                        <label>
                            <input onchange="change_radius_infinity();" id="search_radius_infinity" type="checkbox" <?php echo ($current_map['search_radius_m']==-1) ? 'checked' : ''; ?>>&nbsp;<i style="display:inline-block" class="fa-solid fa-infinity"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div id="list_area" class="list_detail dragscroll clusterize-scroll noselect">
                <div id="list" class="clusterize-content "></div>
            </div>
            <div id="blueimp-gallery" class="blueimp-gallery" aria-label="image gallery" aria-modal="true" role="dialog">
                <div class="slides" aria-live="polite"></div>
                <a class="prev" aria-controls="blueimp-gallery" aria-label="previous slide" aria-keyshortcuts="ArrowLeft">‹</a>
                <a class="next" aria-controls="blueimp-gallery" aria-label="next slide" aria-keyshortcuts="ArrowRight">›</a>
                <a class="close" aria-controls="blueimp-gallery" aria-label="close" aria-keyshortcuts="Escape">×</a>
                <ol class="indicator"></ol>
            </div>
            <div id="map" class="map"></div>
            <div class="popup noselect">
                <img class="popup_image" src="" />
                <p class="popup_title"></p>
                <p class="popup_rating <?php echo (!$current_map['enable_reviews']) ? 'hidden' : ''; ?>"></p>
                <p class="popup_address"></p>
            </div>
            <div class="popup_c noselect">
                <p class="popup_c_title"></p>
                <p class="popup_c_description"></p>
            </div>
        </div>
    </div>
    <div id="story" class="dragscroll noselect">
        <div id="story_content"></div>
    </div>
    <script type="text/javascript" src="js/index.js?v=<?php echo $v; ?>"></script>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'custom.js')) : ?>
        <script type="text/javascript" src="js/custom.js?v=<?php echo time(); ?>"></script>
    <?php endif; ?>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'custom_'.$code.'.js')) : ?>
        <script type="text/javascript" src="js/custom_<?php echo $code; ?>.js?v=<?php echo time(); ?>"></script>
    <?php endif; ?>
    <script src="vendor/Gallery/js/blueimp-gallery.min.js"></script>
    <?php if($current_map['enable_share'] || $current_map['enable_share_map']) : ?>
        <?php if($cookie_consent) { ?>
        <script type="text/plain" data-category="functionality" data-service="Social Share (AddToAny)" async src="https://static.addtoany.com/menu/page.js"></script>
    <?php } else { ?>
        <script async src="https://static.addtoany.com/menu/page.js"></script>
    <?php } ?>
    <?php endif; ?>
    <script>
        (function ($) {
            'use strict';
            window.url_map = '<?php echo $url_map; ?>';
            window.map_name = `<?php echo $current_map['name']; ?>`;
            window.id_map = '<?php echo $current_map['id']; ?>';
            window.map_mode = '<?php echo $current_map['map_type']; ?>';
            window.num_markers = parseInt(<?php echo $num_markers; ?>);
            window.auto_start = <?php echo $auto_start; ?>;
            window.ignore_embedded = <?php echo $ignore_embedded; ?>;
            switch(window.auto_start) {
                case 0:
                    window.auto_start=false;
                    break;
                case 1:
                    window.auto_start=true;
                    break;
                case 2:
                    if ((window.self !== window.top) && (window.ignore_embedded==0)) {
                        window.auto_start=false;
                    } else {
                        window.auto_start=true;
                    }
                    break;
            }
            window.order_by = '<?php echo $current_map['order_by']; ?>';
            window.main_color_hex = '<?php echo $current_map['main_color_hex']; ?>';
            window.darker_color_hex = '<?php echo $darker_color_hex; ?>';
            window.markers_color_hex = '<?php echo $current_map['markers_color_hex']; ?>';
            window.map_style = '<?php echo $current_map['map_style']; ?>';
            window.map_style_satellite = '<?php echo $current_map['map_style_satellite']; ?>';
            window.enable_map_street = <?php echo ($current_map['enable_map_street']) ? 1 : 0; ?>;
            window.enable_map_satellite = <?php echo ($current_map['enable_map_satellite']) ? 1 : 0; ?>;
            window.street_basemap = '<?php echo $current_map['street_basemap']; ?>';
            window.street_attributions = `<?php echo $current_map['street_attributions']; ?>`;
            window.use_street_attributions =  <?php echo ($current_map['use_street_attributions']) ? 1 : 0; ?>;
            window.satellite_basemap = '<?php echo $current_map['satellite_basemap']; ?>';
            window.satellite_attributions = `<?php echo $current_map['satellite_attributions']; ?>`;
            window.use_satellite_attributions =  <?php echo ($current_map['use_satellite_attributions']) ? 1 : 0; ?>;
            window.street_maxzoom = <?php echo $current_map['street_maxzoom']; ?>;
            window.satellite_maxzoom = <?php echo $current_map['satellite_maxzoom']; ?>;
            window.maptiler_api = '<?php echo $current_map['maptiler_api']; ?>';
            window.maptiler_api_satellite = '<?php echo $current_map['maptiler_api_satellite']; ?>';
            window.here_api = '<?php echo $current_map['here_api']; ?>';
            window.here_api_satellite = '<?php echo $current_map['here_api_satellite']; ?>';
            window.map_street_provider = '<?php echo $current_map['map_street_provider']; ?>';
            window.map_satellite_provider = '<?php echo $current_map['map_satellite_provider']; ?>';
            window.show_list = <?php echo $show_list; ?>;
            window.default_zoom = <?php echo $default_zoom; ?>;
            window.selected_zoom = <?php echo $selected_zoom; ?>;
            window.density_color = <?php echo $density_color; ?>;
            window.cat_sel = <?php echo $cat_sel; ?>;
            window.marker_sel = <?php echo $marker_sel; ?>;
            window.marker_current_id = 0;
            window.initial_coordinates = '<?php echo $coordinates; ?>';
            window.markers_size = <?php echo $current_map['markers_size']; ?>;
            window.enable_reviews = <?php echo $current_map['enable_reviews']; ?>;
            window.cluster_distance = <?php echo $current_map['cluster_distance']; ?>;
            window.cat_filter = <?php echo $current_map['cat_filter']; ?>;
            window.sheet_detail = <?php echo $current_map['sheet_detail']; ?>;
            window.default_view = '<?php echo $current_map['default_view']; ?>';
            window.quality = '<?php echo $current_map['quality']; ?>';
            window.my_location_zoom = <?php echo $current_map['my_location_zoom']; ?>;
            window.default_my_location = <?php echo $current_map['default_my_location']; ?>;
            window.enable_list = <?php echo $current_map['enable_list']; ?>;
            window.enable_search = <?php echo $current_map['enable_search']; ?>;
            window.enable_search_location = <?php echo $current_map['enable_search_location']; ?>;
            window.enable_categories = <?php echo $current_map['enable_categories']; ?>;
            window.custer_color_tolerance = <?php echo $current_map['density_color_tolerance']; ?>;
            window.search_highlight = <?php echo $current_map['search_highlight']; ?>;
            window.captcha = null;
            window.first_image_upload = false;
            window.weather = '<?php echo $current_map['weather']; ?>';
            window.geoJSON = '<?php echo $current_map['geoJSON']; ?>';
            window.cat_filter_type = '<?php echo $current_map['cat_filter_type']; ?>';
            window.globe = <?php echo $current_map['enable_globe']; ?>;
            window.saved_geometries = '<?php echo $current_map['draw_geometries']; ?>';
            var enable_add_marker = <?php echo (int)$current_map['enable_add_marker']; ?>;
            window.ebable_popup = <?php echo (int)$current_map['enable_popup']; ?>;
            window.sheet_mobile = '<?php echo $current_map['sheet_mobile']; ?>';
            window.logo_mobile = '<?php echo $current_map['logo_mobile']; ?>';
            window.direction_map = '<?php echo $direction_map; ?>';
            window.streetview_map = '<?php echo $streetview_map; ?>';
            window.background_image = '<?php echo $background_image; ?>';
            window.background_image_mobile = '<?php echo $background_image_mobile; ?>';
            window.loading_text_color = '<?php echo $loading_text_color; ?>';
            window.password_protected = <?php echo ($password) ? 1 : 0; ?>;
            window.add_marker_open = <?php echo ($add_marker_open) ? 1 : 0; ?>;
            if(window.enable_search==1) {
                window.search_radius_enabled = <?php echo ($current_map['search_radius_enabled']) ? 1 : 0; ?>;
            } else {
                window.search_radius_enabled = 0;
            }
            window.search_radius_m = <?php echo $current_map['search_radius_m']; ?>;
            window.enable_intro = <?php echo $enable_intro; ?>;
            window.viewer_labels = {
                "cancel_new_marker":`<?php echo _("Are you sure you want to CANCEL the request to insert this marker?"); ?>`,
                "confirm_new_marker":`<?php echo _("Are you sure you want to SEND the request to insert this marker?"); ?>`,
                "ok_new_marker":`<?php echo _("Thanks for inserting the marker! As soon as it is approved it will be visible on the map."); ?>`,
                "error_new_marker":`<?php echo _("An error has occurred, please try again later."); ?>`,
                "reset":`<?php echo _("reset"); ?>`,
                "start_story":`<?php echo _("Start, Scroll down"); ?>`,
                "end_story":`<?php echo _("End"); ?>`,
                "searching":`<?php echo _("Searching"); ?>`,
                "no_result":`<?php echo _("No results found."); ?>`,
                "add_marker_title":`<?php echo _("Add New Marker"); ?>`,
                "add_marker_location":`<?php echo _("Do you want to use my position to add the marker?"); ?>`,
                "no":`<?php echo _("No"); ?>`,
                "yes":`<?php echo _("Yes"); ?>`,
                "addtoany_social_share":`<?php echo _("Social Share")." (AddToAny)"; ?>`,
                "cookie_denied_msg":`<?php echo _("To use this function you must enable the cookies relating to it."); ?>`,
                "open_cookie_pref":`<?php echo _("Open Cookie Preferences"); ?>`,
            };
            if(enable_add_marker==2) {
                window.viewer_labels.confirm_new_marker = `<?php echo _("Are you sure you want to insert this marker?"); ?>`;
                window.viewer_labels.ok_new_marker = `<?php echo _("Thanks for inserting the marker! Please reload the map to view it."); ?>`;
            }
            $(document).ready(function () {
                check_map();
            });
        })(jQuery);
        $('#txtFile').change(function() {
            $('#frm').submit();
        });
        $('body').on('submit','#frm',function(e){
            $('#new_image_btn i').removeClass();
            $('#new_image_btn i').addClass('fas').addClass('fa-spin').addClass('fa-circle-notch');
            $('#new_image_btn').addClass('disabled');
            e.preventDefault();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){

            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    alert(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        $('#uploaded_images').append('<img data-image="'+evt.target.responseText+'" class="sheet_image" src="marker_images_tmp/thumb/'+evt.target.responseText+'" />');
                        if(!window.first_image_upload) {
                            $('.new_sheet_detail_image').css('background-image','url("marker_images_tmp/'+evt.target.responseText+'")');
                            $('#btn_delete_images').css('pointer-events','initial');
                            $('#btn_delete_images').css('opacity',1);
                            window.first_image_upload=true;
                        }
                    }
                }
                $('#new_image_btn i').removeClass();
                $('#new_image_btn i').addClass('fas').addClass('fa-upload');
                $('#new_image_btn').removeClass('disabled');
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                $('#new_image_btn i').removeClass();
                $('#new_image_btn i').addClass('fas').addClass('fa-upload');
                $('#new_image_btn').removeClass('disabled');
            },false);
            ajax.addEventListener('abort',function(evt){
                $('#new_image_btn i').removeClass();
                $('#new_image_btn i').addClass('fas').addClass('fa-upload');
                $('#new_image_btn').removeClass('disabled');
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });
    </script>
    <?php if($ga_tracking_id!='') : ?>
        <?php if($cookie_consent) { ?>
            <script type="text/plain" data-category="analytics" data-service="Google Analytics" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_tracking_id; ?>"></script>
            <script type="text/plain" data-category="analytics" data-service="Google Analytics">
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?php echo $ga_tracking_id; ?>');
            </script>
        <?php } else { ?>
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_tracking_id; ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?php echo $ga_tracking_id; ?>');
            </script>
        <?php } ?>
    <?php endif; ?>
    <?php if($cookie_consent) : ?>
        <div data-cc="show-consentModal" id="cookie_consent_preferences"><i class="fa-solid fa-cookie-bite"></i><span>&nbsp;&nbsp;<?php echo _("Cookie Preferences"); ?></span></div>
    <?php endif; ?>
    <?php if(!empty($cookie_policy) && $cookie_policy!='<p></p>') : ?>
        <div id="modal_cookie_policy_v" style="display:none;max-width:calc(100% - 40px);">
            <?php echo $cookie_policy; ?>
        </div>
    <?php endif; ?>
    <?php if($cookie_consent) : ?>
        <?php require_once('cookie_consent.php'); ?>
    <?php endif; ?>
    </body>
    </html>

<?php
function adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');
    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }
    $hexCode = array_map('hexdec', str_split($hexCode, 2));
    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }
    return '#'.implode($hexCode);
}
?>