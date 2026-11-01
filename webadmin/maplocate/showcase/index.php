<?php
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("../db/connection.php");
require_once("../backend/functions.php");
$v = time();
$array_map = array();
$array_cat = array();
$header_html = '';
$footer_html = '';
$open_target = 'self';
if((isset($_GET['furl'])) || (isset($_GET['code']))) {
    if (isset($_GET['furl'])) {
        $furl = str_replace("'","\'",$_GET['furl']);
        $query = "SELECT id,code,name,banner,logo,bg_color,header_html,footer_html,meta_title,meta_description,meta_image,sort_settings,open_target,cookie_consent,ga_tracking_id FROM sml_showcases WHERE (friendly_url='$furl' OR code='$furl');";
    }
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $query = "SELECT id,code,name,banner,logo,bg_color,header_html,footer_html,meta_title,meta_description,meta_image,sort_settings,open_target,cookie_consent,ga_tracking_id FROM sml_showcases WHERE code='$code';";
    }
    $result = $mysqli->query($query);
    if ($result) {
        if ($result->num_rows==1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $id_s = $row['id'];
            $code = $row['code'];
            $name_s = $row['name'];
            $banner_s = $row['banner'];
            $logo_s = $row['logo'];
            $bg_color_s = $row['bg_color'];
            $header_html = $row['header_html'];
            $footer_html = $row['footer_html'];
            $sort_settings = $row['sort_settings'];
            $cookie_consent = $row['cookie_consent'];
            $ga_tracking_id = $row['ga_tracking_id'];
            if(empty($row['meta_title'])) {
                $meta_title = $name_s;
            } else {
                $meta_title = $row['meta_title'];
            }
            if(empty($row['meta_description'])) {
                $meta_description = '';
            } else {
                $meta_description = $row['meta_description'];
            }
            if(empty($row['meta_image'])) {
                $meta_image = $row['banner'];
            } else {
                $meta_image = $row['meta_image'];
            }
            $open_target = $row['open_target'];
            $query_list = "SELECT v.id,v.date_created,s.priority,v.code,v.name as title,v.map_style,v.background_image as image,COUNT(al.id) as total_access
                            FROM sml_showcase_list as s
                            JOIN sml_maps as v ON s.id_map=v.id
                            LEFT JOIN sml_access_log as al ON al.id_map=v.id
                            WHERE s.id_showcase=$id_s AND v.active=1
                            GROUP BY v.id,v.date_created,s.priority,v.code,v.name,v.background_image;";
            $result_list = $mysqli->query($query_list);
            if($result_list) {
                if($result_list->num_rows>0) {
                    while($row_list = $result_list->fetch_array(MYSQLI_ASSOC)) {
                        $id_map = $row_list['id'];
                        if(empty($row_list['image'])) {
                            $row_list['image']='img/default_s.jpg';
                        } else {
                            $row_list['image']='../viewer/content/'.$row_list['image'];
                        }
                        $row_list['date']=strtotime($row_list['date_created']);
                        $array_map[] = $row_list;
                    }
                }
            } else {
                die("Invalid Link");
            }
        } else {
            die("Invalid Link");
        }
    } else {
        die("Invalid Link");
    }
} else {
    die("Invalid Link");
}
$font_provider = 'google';
$font_backend = "";
$cookie_policy = "";
$query = "SELECT language,language_domain,font_provider,font_backend,cookie_policy  FROM sml_settings LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows==1) {
        $row=$result->fetch_array(MYSQLI_ASSOC);
        $font_provider = $row['font_provider'];
        $font_backend = $row['font_backend'];
        $cookie_policy = $row['cookie_policy'];
        $language = $row['language'];
        if (function_exists('gettext')) {
            if(defined('LC_MESSAGES')) {
                $result = setlocale(LC_MESSAGES, $language);
                if(!$result) {
                    setlocale(LC_MESSAGES, $language.'.UTF-8');
                }
                if (function_exists('putenv')) {
                    $result = putenv('LC_MESSAGES='.$language);
                    if(!$result) {
                        putenv('LC_MESSAGES='.$language.'.UTF-8');
                    }
                }
            } else {
                if (function_exists('putenv')) {
                    $result = putenv('LC_ALL='.$language);
                    if(!$result) {
                        putenv('LC_ALL='.$language.'.UTF-8');
                    }
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
$count_order=0;

$font_provider = "google";
$query = "SELECT font_provider FROM sml_settings LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $font_provider = $row['font_provider'];
    }
}

$currentPath = $_SERVER['PHP_SELF'];
$pathInfo = pathinfo($currentPath);
$hostName = $_SERVER['HTTP_HOST'];
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$url = $protocol."://".$hostName.$pathInfo['dirname']."/";
$url = str_replace("/showcase/","/",$url);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $meta_title; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
    <meta property="og:type" content="website">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="og:url" content="<?php echo $url."showcase/index.php?code=".$code; ?>">
    <meta property="twitter:url" content="<?php echo $url."showcase/index.php?code=".$code; ?>">
    <meta itemprop="name" content="<?php echo $meta_title; ?>">
    <meta property="og:title" content="<?php echo $meta_title; ?>">
    <meta property="twitter:title" content="<?php echo $meta_title; ?>">
    <?php if($meta_image!='') : ?>
        <meta itemprop="image" content="<?php echo $url."viewer/content/".$meta_image; ?>">
        <meta property="og:image" content="<?php echo $url."viewer/content/".$meta_image; ?>" />
        <meta property="twitter:image" content="<?php echo $url."viewer/content/".$meta_image; ?>">
    <?php endif; ?>
    <?php if($meta_description!='') : ?>
        <meta itemprop="description" content="<?php echo $meta_description; ?>">
        <meta name="description" content="<?php echo $meta_description; ?>"/>
        <meta property="og:description" content="<?php echo $meta_description; ?>" />
        <meta property="twitter:description" content="<?php echo $meta_description; ?>">
    <?php endif; ?>
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
                const fontName = '<?php echo $font_backend; ?>';
                            const e = d.createElement(l);
                            e.rel = s;
                            e.type = 'text/css';
                            e.href = `https://fonts.googleapis.com/css2?family=${fontName}`;
                            e.id = 'font_backend_link';
                            d.head.appendChild(e);
                          })(document, 'link', 'stylesheet');
        </script>
    <?php } else { ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel='stylesheet' type="text/css" crossorigin="anonymous" id="font_backend_link" href="https://fonts.googleapis.com/css2?family=<?php echo $font_backend; ?>">
    <?php } ?>
    <?php break;
    case 'collabs': ?>
    <link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>
    <link rel="stylesheet" type="text/css" id="font_backend_link" href="https://api.fonts.coollabs.io/css2?family=<?php echo $font_backend; ?>&display=swap">
    <?php break;
    default: ?>
    <link rel="stylesheet" type="text/css" crossorigin="anonymous" id="font_backend_link" href="">
        <?php break;
    } ?>
    <link rel="stylesheet" type='text/css' href="../viewer/vendor/fontawesome-free/css/all.min.css?v=5.15.4">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <?php if($cookie_consent) : ?>
        <link rel="stylesheet" type="text/css" href="../backend/vendor/cookieconsent/cookieconsent.min.css?v=3.0.0">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="css/index.css?v=<?php echo $v; ?>">
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_'.$code.'.css')) : ?>
        <link rel="stylesheet" type="text/css" href="css/custom_<?php echo $code; ?>.css?v=<?php echo $v; ?>">
    <?php endif; ?>
    <script type="text/javascript" src="js/jquery-3.6.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <?php if($cookie_consent) : ?>
        <script type="text/javascript" src="../backend/vendor/cookieconsent/cookieconsent.min.js?v=3.0.0"></script>
    <?php endif; ?>
</head>
<body style="background: <?php echo $bg_color_s; ?>">
<style>
    :root {
        --bg_color: <?php echo $bg_color_s; ?>;
    }
    .header:before {
        <?php if(!empty($banner_s)) { ?>
        background-image: url('../viewer/content/<?php echo $banner_s; ?>');
        <?php } else { ?>
        background-color: rgba(0,0,0,0.4);
        <?php } ?>
    }
    .frame_banner:before {
    <?php if(!empty($banner_s)) : ?>
        background-image: url('../viewer/content/<?php echo $banner_s; ?>');
    <?php endif; ?>
    }
    <?php if(empty($banner_s)) { ?>
    .header {
        height: auto;
        min-height: 100px;
    }
    .info {
        padding-top: 25px;
    }
    .info h1 {
        margin-bottom: 0;
    }
    .header:after {
        background: none;
    }
    .logo img {
        margin-top: 10px;
        margin-bottom: 25px;
    }
    .frame_banner:before {
        background: none;
    }
    <?php } ?>
</style>
<div class="showcase noselect">
    <div class="header">
        <div class="info">
            <h1><?php echo $name_s; ?></h1>
            <?php if(!empty($logo_s)) : ?>
                <div class="logo">
                    <img src="../viewer/content/<?php echo $logo_s; ?>" />
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="custom_header <?php echo (empty($header_html)) ? 'd-none' : ''; ?>">
        <?php echo html_entity_decode($header_html); ?>
    </div>
    <?php
    if(!empty($sort_settings)) {
        $sort_settings = json_decode($sort_settings,true);
    } else {
        $sort_settings = array();
        $sort_settings['default']='date|asc';
    }
    $default_sort_type = explode("|",$sort_settings['default'])[0];
    $default_sort_by = explode("|",$sort_settings['default'])[1];
    ?>
    <section>
        <div style="opacity:0" class="container">
            <div class="d-flex flex-row flex-wrap">
                <?php foreach ($array_map as $map) {
                    $count_order++;
                    ?>
                    <div id="vt_<?php echo $map['id']; ?>" style="order: <?php echo $count_order; ?>;" class="col-xl-3 col-lg-4 col-sm-6 col-xs-12">
                        <div data-id="<?php echo $map['id']; ?>" data-name="<?php echo $map['title']; ?>" data-author="<?php echo $map['author']; ?>" data-image="<?php echo $map['image']; ?>" data-priority="<?php echo $map['priority']; ?>" data-views="<?php echo $map['total_access']; ?>" data-date="<?php echo $map['date']; ?>" data-code="<?php echo $map['code']; ?>" class="card map-card">
                            <div class="card-img-block">
                                <div class="overlay"></div>
                                <i class="fas fa-play-circle"></i>
                                <?php if(empty($map['image'])) { ?>
                                    <div style="height: 180px;background-color: darkgrey" class="card-img-top"></div>
                                <?php } else { ?>
                                    <img class="card-img-top" src="<?php echo $map['image']; ?>" alt="card image">
                                <?php } ?>
                                <div class="card-access"><i class="far fa-eye"></i> <?php echo $map['total_access']; ?></div>
                            </div>
                            <div class="card-body pt-0">
                                <h5 class="card-title"><?php echo $map['title']; ?></h5>
                                <p class="card-author"><?php echo $map['author']; ?></p>
                                <p class="card-text"><?php echo $map['description']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <div class="custom_footer <?php echo (empty($footer_html)) ? 'd-none' : ''; ?>">
        <?php echo html_entity_decode($footer_html); ?>
    </div>
    <?php if($cookie_consent) : ?>
        <div data-cc="show-consentModal" id="cookie_consent_preferences"><i class="fa-solid fa-cookie-bite"></i><span>&nbsp;&nbsp;<?php echo _("Cookie Preferences"); ?></span></div>
    <?php endif; ?>
</div>
<div class="map_viewer">
    <i class="fa fa-spin fa-circle-notch loading_icon"></i>
    <div class="frame_banner noselect">
        <?php if(!empty($logo_s)) : ?>
            <img src="../viewer/content/<?php echo $logo_s; ?>" />
        <?php endif; ?>
        <span><?php echo $name_s; ?></span>
        <i onclick="show_showcase()" class="fas fa-arrow-circle-left"></i>
    </div>
    <iframe referrerpolicy="origin" allow="accelerometer; camera; display-capture; fullscreen; geolocation; gyroscope; magnetometer; microphone; midi; xr-spatial-tracking;" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
</div>
<div class="ripple-wrap"><div class="ripple"><i class="fa fa-spin fa-circle-notch"></i></div></div>

<?php if(!empty($cookie_policy) && $cookie_policy!='<p></p>') : ?>
    <div id="modal_cookie_policy_b" class="modal" tabindex="-1" role="dialog">
        <div style="max-width: 1280px;" class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo _("Cookie Policy"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $cookie_policy; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    window.sort_type = '<?php echo $default_sort_type; ?>';
    window.sort_by = '<?php echo $default_sort_by; ?>';
    window.array_map = [];
    window.open_target = '<?php echo $open_target; ?>';
    $(document).ready(function() {
        populate_array_map();
        switch(window.sort_by) {
            case 'asc':
                $('#btn_sort_by_order i').removeClass('fa-sort-alpha-up').addClass('fa-sort-alpha-down');
                break;
            case 'desc':
                $('#btn_sort_by_order i').removeClass('fa-sort-alpha-down').addClass('fa-sort-alpha-up');
                break;
        }
        change_sorty_by_type(window.sort_type);
        $('.container').css('opacity',1);
        var height_footer = $('.custom_footer ').outerHeight();
        $('section').css('margin-bottom',height_footer+'px');
        var ripple_wrap = $('.ripple-wrap'), rippler = $('.ripple'), finish = false, map_code='', type='viewer', image_sel='',
        monitor = function(el) {
            var computed = window.getComputedStyle(el, null),
                borderwidth = parseFloat(computed.getPropertyValue('border-left-width'));
            if (!finish && borderwidth >= 1500) {
                el.style.WebkitAnimationPlayState = "paused";
                el.style.animationPlayState = "paused";
            }
            if (finish) {
                el.style.WebkitAnimationPlayState = "running";
                el.style.animationPlayState = "running";
                return;
            } else {
                window.requestAnimationFrame(function() {monitor(el)});
            }
        };
        rippler.bind("webkitAnimationEnd oAnimationEnd msAnimationEnd mozAnimationEnd animationend", function(e){
            $('.ripple i').hide();
            ripple_wrap.removeClass('goripple');
        });
        $('body').on('click', '.map-card', function(e) {
            map_code = $(this).attr('data-code');
            if(window.open_target=='new') {
                window.open('../viewer/index.php?code='+map_code,'_blank');
            } else {
                image_sel = $(this).attr('data-image');
                $('body').css('overflow-y','hidden');
                $('.map_viewer').css('height','100vh');
                $('.ripple i').show();
                rippler.css('left', e.clientX + 'px');
                rippler.css('top', e.clientY + 'px');
                e.preventDefault();
                finish = false;
                ripple_wrap.addClass('goripple');
                setTimeout(function () {
                    swapContent();
                },1000);
                window.requestAnimationFrame(function() {monitor(rippler[0])});
            }
        });
        function swapContent() {
            $('.map_viewer iframe').attr('src','../viewer/index.php?code='+map_code);
            $('.map_viewer iframe').attr('scrolling','no');
            $('.map_viewer').show();
            $('.showcase').hide();
            if(!image_sel.includes('preview')) {
                $('.map_viewer').css('background-image','url('+image_sel+')');
            } else {
                $('.map_viewer').css('background-image','none');
            }
            $('.ripple i').fadeOut(500);
            setTimeout(function() {
                finish = true;
            },500);
        }
    });
    var show_showcase = function() {
        $('.map_viewer').fadeOut(function () {
            $('.map_viewer iframe').attr('src','');
            $('.showcase').fadeIn();
            $('body').css('overflow-y','auto');
        });
    };

    function change_sorty_by_order() {
        if(window.sort_by == 'asc') {
            window.sort_by='desc';
        } else if(window.sort_by == 'desc') {
            window.sort_by='asc';
        }
        switch(window.sort_by) {
            case 'asc':
                $('#btn_sort_by_order i').removeClass('fa-sort-alpha-up').addClass('fa-sort-alpha-down');
                break;
            case 'desc':
                $('#btn_sort_by_order i').removeClass('fa-sort-alpha-down').addClass('fa-sort-alpha-up');
                break;
        }
        change_sorty_by_type(window.sort_type);
    }

    function change_sorty_by_type(type) {
        $('#btn_sort_by_type').html($('#sort_'+type).html());
        var array_map_tmp = array_map;
        var reverse = false;
        if(sort_by=='desc') reverse = true;
        switch(type) {
            case 'name':
                array_map_tmp.sort(sort_by_f('name', reverse, (a) =>  a.toUpperCase()));
                break;
            case 'author':
                array_map_tmp.sort(sort_by_f('author', reverse, (a) =>  a.toUpperCase()));
                break;
            case 'category':
                array_map_tmp.sort(sort_by_f('category', reverse, (a) =>  a.toUpperCase()));
                break;
            case 'date':
                array_map_tmp.sort(sort_by_f('date', reverse, parseInt));
                break;
            case 'views':
                array_map_tmp.sort(sort_by_f('views', reverse, parseInt));
                break;
            case 'relevance':
                array_map_tmp.sort(sort_by_f('relevance', reverse, parseInt));
                break;
        }
        window.sort_type = type;
        jQuery.each(array_map_tmp, function (index,vt) {
            var id = vt.id;
            $('#vt_'+id).css('order',index);
        });
    }

    function populate_array_map() {
        $('.map-card').each(function () {
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var author = $(this).attr('data-author');
            var date = $(this).attr('data-date');
            var views = $(this).attr('data-views');
            var priority = $(this).attr('data-priority');
            var tmp = {};
            tmp['id']=id;
            tmp['name']=name;
            tmp['author']=author;
            tmp['date']=date;
            tmp['views']=views;
            tmp['relevance']=priority;
            array_map.push(tmp);
        });
    }

    const sort_by_f = (field, reverse, primer) => {
        const key = primer ?
            function(x) {
                return primer(x[field])
            } :
            function(x) {
                return x[field]
            };
        reverse = !reverse ? 1 : -1;
        return function(a, b) {
            return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
        }
    }
</script>
<?php if(!empty($ga_tracking_id)) : ?>
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
    <?php require_once('cookie_consent.php'); ?>
<?php endif; ?>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('service-worker.js', {
            scope: '.'
        });
    }
</script>
</body>
</html>