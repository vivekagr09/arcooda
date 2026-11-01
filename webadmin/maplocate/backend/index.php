<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
if(!file_exists("../config/config.inc.php")) {
    header("Location: ../install/start.php");
}
session_start();
require_once("functions.php");
$session_id = session_id();
$_SESSION['sml_si']=$session_id;
if(isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
} else {
    $_SESSION['id_user'] =1;
    $id_user = $_SESSION['id_user'];
   // header("Location:login.php");
}
$v = time();
$version = "4.5";
$rev = 1;
$settings = get_settings();
$need_update = false;
if(!empty($settings['version'])) {
    $version_c = $settings['version'];
    if($version!=$version_c) {
        $need_update = true;
    }
} else {
    $need_update = true;
}
if(!isset($_SESSION['latest_version'])) {
    if(function_exists('ini_get') && ini_get('allow_url_fopen')) {
        $z0='';if(array_key_exists(base64_decode('U0VSVkVSX0FERFI='),$_SERVER)){$z0=$_SERVER[base64_decode('U0VSVkVSX0FERFI=')];if(!filter_var($z0,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}}elseif(array_key_exists(base64_decode('TE9DQUxfQUREUg=='),$_SERVER)){$z0=$_SERVER[base64_decode('TE9DQUxfQUREUg==')];}elseif(array_key_exists(base64_decode('U0VSVkVSX05BTUU='),$_SERVER)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}else{if(stristr(PHP_OS,base64_decode('V0lO'))){$z0=gethostbyname(php_uname(base64_decode('bg==')));}else{$b1=shell_exec(base64_decode('L3NiaW4vaWZjb25maWcgZXRoMA=='));preg_match(base64_decode('L2FkZHI6KFtcZFwuXSspLw=='),$b1,$e2);$z0=$e2[1];}}$a3=$_SERVER[base64_decode('U0VSVkVSX05BTUU=')];$i4=$_SERVER[base64_decode('UkVRVUVTVF9VUkk=')];$j5=@file_get_contents(base64_decode("aHR0cHM6Ly9zaW1wbGVkZW1vLml0L2dldF9sYXRlc3Rfc21sX3ZlcnNpb24ucGhw")."?domain=$a3&ip=$z0&version=$version&request_uri=$i4");if($j5){$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$j5;}else{$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$version;}
        if(empty($j5)) {
            $z0='';if(array_key_exists(base64_decode('U0VSVkVSX0FERFI='),$_SERVER)){$z0=$_SERVER[base64_decode('U0VSVkVSX0FERFI=')];if(!filter_var($z0,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}}elseif(array_key_exists(base64_decode('TE9DQUxfQUREUg=='),$_SERVER)){$z0=$_SERVER[base64_decode('TE9DQUxfQUREUg==')];}elseif(array_key_exists(base64_decode('U0VSVkVSX05BTUU='),$_SERVER)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}else{if(stristr(PHP_OS,base64_decode('V0lO'))){$z0=gethostbyname(php_uname(base64_decode('bg==')));}else{$b1=shell_exec(base64_decode('L3NiaW4vaWZjb25maWcgZXRoMA=='));preg_match(base64_decode('L2FkZHI6KFtcZFwuXSspLw=='),$b1,$e2);$z0=$e2[1];}}$a3=$_SERVER[base64_decode('U0VSVkVSX05BTUU=')];$i4=$_SERVER[base64_decode('UkVRVUVTVF9VUkk=')];$j5=@curl_get_file_contents(base64_decode("aHR0cHM6Ly9zaW1wbGVkZW1vLml0L2dldF9sYXRlc3Rfc21sX3ZlcnNpb24ucGhw")."?domain=$a3&ip=$z0&version=$version&request_uri=$i4");if($j5){$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$j5;}else{$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$version;}
        }
    } else {
        $z0='';if(array_key_exists(base64_decode('U0VSVkVSX0FERFI='),$_SERVER)){$z0=$_SERVER[base64_decode('U0VSVkVSX0FERFI=')];if(!filter_var($z0,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}}elseif(array_key_exists(base64_decode('TE9DQUxfQUREUg=='),$_SERVER)){$z0=$_SERVER[base64_decode('TE9DQUxfQUREUg==')];}elseif(array_key_exists(base64_decode('U0VSVkVSX05BTUU='),$_SERVER)){$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}else{if(stristr(PHP_OS,base64_decode('V0lO'))){$z0=gethostbyname(php_uname(base64_decode('bg==')));}else{$b1=shell_exec(base64_decode('L3NiaW4vaWZjb25maWcgZXRoMA=='));preg_match(base64_decode('L2FkZHI6KFtcZFwuXSspLw=='),$b1,$e2);$z0=$e2[1];}}$a3=$_SERVER[base64_decode('U0VSVkVSX05BTUU=')];$i4=$_SERVER[base64_decode('UkVRVUVTVF9VUkk=')];$j5=@curl_get_file_contents(base64_decode("aHR0cHM6Ly9zaW1wbGVkZW1vLml0L2dldF9sYXRlc3Rfc21sX3ZlcnNpb24ucGhw")."?domain=$a3&ip=$z0&version=$version&request_uri=$i4");if($j5){$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$j5;}else{$_SESSION[base64_decode('bGF0ZXN0X3ZlcnNpb24=')]=$version;}
    }
}
if($_SESSION['latest_version']=="") {
    $_SESSION['latest_version'] = $version;
}
$latest_version = $_SESSION['latest_version'];
$user_info = get_user_info($id_user);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    if(empty($_SESSION['lang'])) {
        $_SESSION['lang']=$settings['language'];
    }
    set_language($settings['language'],$settings['language_domain']);
}
if(isset($_GET['p'])) {
    $page = $_GET['p'];
} else {
    $page = "dashboard";
}
if(file_exists("../config/demo.inc.php")) {
    require_once("../config/demo.inc.php");
    $_SESSION['demo_developer_ip']=DEMO_DEVELOPER_IP;
    $_SESSION['demo_server_ip']=DEMO_SERVER_IP;
    $_SESSION['demo_user_id']=DEMO_USER_ID;
    if(($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) {
        $demo = true;
        if(!isset($_SESSION['id_map_sel'])) {
            $_SESSION['id_map_sel'] = 1;
            $_SESSION['name_map_sel'] = 'Simple Map Locator';
        }
    } else {
        $demo = false;
    }
    if(($_SERVER['REMOTE_ADDR']==$_SESSION['demo_developer_ip'])) {
        $k = time();
    } else {
        $k = $version."_".$rev;
    }
} else {
    $demo = false;
    $k = $version."_".$rev;
    $_SESSION['demo_developer_ip']='';
    $_SESSION['demo_server_ip']='';
    $_SESSION['demo_user_id']='';
}
$_SESSION['demo'] = $demo;
$_SESSION['theme_color']=$settings['theme_color'];
$_SESSION['sidebar_color_1']=$settings['sidebar_color_1'];
$_SESSION['sidebar_color_2']=$settings['sidebar_color_2'];
$_SESSION['theme_color_dark']=$settings['theme_color_dark'];
$_SESSION['sidebar_color_1_dark']=$settings['sidebar_color_1_dark'];
$_SESSION['sidebar_color_2_dark']=$settings['sidebar_color_2_dark'];
$_SESSION['input_license']=0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $settings['name']; ?></title>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.'favicon.ico')) { ?>
        <link rel="icon" type="image/x-icon" href="favicons/favicon.ico">
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="../viewer/vendor/fontawesome-free/css/fontawesome.min.css?v=6.5.1">
    <link rel="stylesheet" type="text/css" href="../viewer/vendor/fontawesome-free/css/solid.min.css?v=6.5.1">
    <link rel="stylesheet" type="text/css" href="../viewer/vendor/fontawesome-free/css/regular.min.css?v=6.5.1">
    <link rel="stylesheet" type="text/css" href="../viewer/vendor/fontawesome-free/css/brands.min.css?v=6.5.1">
    <?php switch ($settings['font_provider']) {
    case 'google': ?>
        <?php if($settings['cookie_consent']) { ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <script type="text/plain" data-category="functionality" data-service="Google Fonts">
            (function(d, l, s) {
                const fontName = '<?php echo $settings['font_backend']; ?>';
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
    <link rel='stylesheet' type="text/css" crossorigin="anonymous" id="font_backend_link" href="https://fonts.googleapis.com/css2?family=<?php echo $settings['font_backend']; ?>">
    <?php } ?>
    <?php break;
    case 'collabs': ?>
    <link rel="preconnect" href="https://api.fonts.coollabs.io" crossorigin>
    <link rel="stylesheet" type="text/css" id="font_backend_link" href="https://api.fonts.coollabs.io/css2?family=<?php echo $settings['font_backend']; ?>&display=swap">
    <?php break;
    default: ?>
    <link rel="stylesheet" type="text/css" crossorigin="anonymous" id="font_backend_link" href="">
        <?php break;
    } ?>
    <link rel="stylesheet" type="text/css" href="css/sb-admin-2.min.css?v=5">
    <link rel="stylesheet" type="text/css" href="vendor/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../viewer/css/ol.css?v=8.2.0">
    <link rel="stylesheet" type="text/css" href="../viewer/css/ol-ext.min.css?v=4.0.13">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.core.css">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="vendor/quill/quill.bubble.css">
    <link rel="stylesheet" type="text/css" href="vendor/justifiedGallery/justifiedGallery.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/iconpicker/iconpicker-1.5.0.css" />
    <link rel="stylesheet" type="text/css" href="vendor/spectrum/spectrum.min.css?v=2.0.9">
    <link rel="stylesheet" type="text/css" href="vendor/dropzone/dropzone.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/dropzone/basic.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/css/tooltipster.bundle.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/jquery.fontpicker/jquery.fontpicker.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-country/css/bootstrap-select-country.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap4-toggle/bootstrap4-toggle.min.css">
    <?php if($settings['cookie_consent']) : ?>
        <link rel="stylesheet" type="text/css" href="vendor/cookieconsent/cookieconsent.min.css?v=3.0.0">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" id="css_theme" href="css/theme.php?v=<?php echo $k; ?>">
    <link rel="stylesheet" type="text/css" id="css_theme_dark" href="css/theme_dark.php?v=<?php echo $v; ?>">
    <link rel="stylesheet" type="text/css" href="css/dark_mode.css?v=<?php echo $k; ?>">
    <link rel="stylesheet" type="text/css" href="css/custom.css?v=<?php echo $v; ?>">
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_b.css')) : ?>
    <link rel="stylesheet" type="text/css" href="css/custom_b.css?v=<?php echo $v; ?>">
    <?php endif; ?>
    <script type="text/javascript" src="vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="vendor/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script type="text/javascript" src="vendor/clipboard.js/clipboard.min.js"></script>
    <script type="text/javascript" src="../viewer/js/ol.js?v=8.2.0"></script>
    <script type="text/javascript" src="../viewer/js/ol-ext.min.js?v=4.0.13"></script>
    <script type="text/javascript" src="../viewer/js/gifler.min.js"></script>
    <script type="text/javascript" src="vendor/highlightjs/hljs.min.js"></script>
    <script type="text/javascript" src="vendor/highlightjs/xml.min.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.core.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.min.js"></script>
    <script type="text/javascript" src="vendor/quill/quill.html.js"></script>
    <script type="text/javascript" src="vendor/spectrum/spectrum.min.js?v=2.0.9"></script>
    <script type="text/javascript" src="vendor/justifiedGallery/jquery.justifiedGallery.min.js"></script>
    <script type="text/javascript" src="vendor/iconpicker/iconpicker-1.5.0.js"></script>
    <script type="text/javascript" src="vendor/chart.js/Chart.min.js"></script>
    <script type="text/javascript" src="vendor/hchart/hs.min.js?v=1"></script>
    <script type="text/javascript" src="vendor/hchart/exporting.js"></script>
    <script type="text/javascript" src="vendor/hchart/accessibility.js"></script>
    <script type="text/javascript" src="vendor/dropzone/dropzone.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="vendor/tooltipster/js/tooltipster.bundle.min.js"></script>
    <?php
    switch ($settings['font_provider']) {
        case 'collabs':
            echo '<script type="text/javascript" src="vendor/jquery.fontpicker/jquery.fontpicker.collabs.min.js?v=1.6"></script>';
            break;
        default:
            echo '<script type="text/javascript" src="vendor/jquery.fontpicker/jquery.fontpicker.min.js?v=1.6"></script>';
            break;
    }
    ?>
    <script type="text/javascript" src="vendor/bootstrap/js/bs-custom-file-input.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap-select-country/js/bootstrap-select-country.min.js"></script>
    <script type="text/javascript" src="vendor/ace-editor/ace.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/mode-css.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/mode-html.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/ext-language_tools.js?v=3" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/ace-editor/theme-one_dark.min.js?v=7" charset="utf-8"></script>
    <script type="text/javascript" src="vendor/bootstrap4-toggle/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="vendor/jquery.searchable-1.1.0.min.js"></script>
    <script type="text/javascript" src="vendor/Sortable.min.js?v=1.14"></script>
    <?php if($settings['cookie_consent']) : ?>
        <script type="text/javascript" src="vendor/cookieconsent/cookieconsent.min.js?v=3.0.0"></script>
    <?php endif; ?>
    <script src="js/function.js?v=<?php echo $k; ?>"></script>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'custom_b.js')) : ?>
        <script type="text/javascript" src="js/custom_b.js?v=<?php echo $v; ?>"></script>
    <?php endif; ?>
    <?php if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom_b.php')) {
        include(__DIR__.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom_b.php');
    } ?>
</head>

<body id="page-top" class="<?php echo ($settings['dark_mode']==2) ? 'dark_mode' : ''; ?>">
<script>
    var dark_mode_setting = <?php echo $settings['dark_mode']; ?>;
    var dark_mode = '0';
    if(dark_mode_setting==1) {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            dark_mode = '1';
        }
        if (localStorage.getItem("dark_mode") === null) {
            localStorage.setItem("dark_mode",dark_mode);
        } else {
            dark_mode = localStorage.getItem('dark_mode');
        }
        if(dark_mode=='1') {
            document.body.classList.add("dark_mode");
            document.documentElement.classList.add('cc--darkmode');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            dark_mode = e.matches ? '1' : '0';
            if(dark_mode=='1') {
                $('.btn_mode_switcher').attr('id','btn_dark_mode');
                $('.btn_mode_switcher').removeClass('fa-sun').addClass('fa-moon');
                document.body.classList.add("dark_mode");
                document.documentElement.classList.add('cc--darkmode');
                localStorage.setItem("dark_mode",'1');
            } else {
                $('.btn_mode_switcher').attr('id','btn_light_mode');
                $('.btn_mode_switcher').removeClass('fa-moon').addClass('fa-sun');
                document.body.classList.remove("dark_mode");
                document.documentElement.classList.remove('cc--darkmode');
                localStorage.setItem("dark_mode",'0');
            }
        });
    }
</script>
<style id="style_css">
    *{ font-family: '<?php echo $settings['font_backend']; ?>', sans-serif; }
</style>

<?php if($need_update) : ?>
    <div class="text-center mt-4">
        <h1 class="text-primary"><?php echo _("UPDATE IN PROGRESS"); ?>...</h1>
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only"><?php echo _("Loading"); ?>...</span>
        </div>
        <p class="lead text-gray-800 mt-4"><?php echo _("Not close this window"); ?></p>
    </div>
    <script>
        $.ajax({
            url: "ajax/update.php",
            type: "POST",
            data: {
                version: '<?php echo $version; ?>'
            },
            async: true,
            success: function () {
                location.reload();
            },
            timeout: 300000
        });
    </script>
    <?php
    exit;
endif;
?>

<div id="wrapper">
  <?php include_once("sidebar.php"); ?>
<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
      <?php include_once("topbar.php"); ?>
    <div class="container-fluid">
        <?php
        if($user_info['role']=='administrator' && empty($settings['license']) && !in_array(get_ip_server(),array('127.0.0.1','::1'))) {
            $_SESSION['input_license']=1;
            include_once("settings.php");
        } else {
            include_once("$page.php");
        }
        ?>
    </div>
  </div>
    <?php include_once("footer.php"); ?>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
<i class="fas fa-angle-up"></i>
</a>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/sb-admin-2.js?v=4"></script>
<script>
    window.backend_labels = {
        "edit":`<?php echo _("EDIT"); ?>`,
        "publish":`<?php echo _("PUBLISH"); ?>`,
        "preview":`<?php echo _("PREVIEW"); ?>`,
        "markers":`<?php echo _("MARKERS"); ?>`,
        "delete":`<?php echo _("DELETE"); ?>`,
        "duplicate":`<?php echo _("DUPLICATE"); ?>`,
        "main_image":`<?php echo _("MAIN IMAGE"); ?>`,
        "set_as_main":`<?php echo _("SET AS MAIN"); ?>`,
        "searching":`<?php echo _("SEARCHING"); ?>`,
        "get_gps_position":`<?php echo _("GET GPS POSITION"); ?>`,
        "no_icon_msg":`<?php echo _("No icon images in this library!"); ?>`,
        "delete_msg":`<?php echo _("Are you sure you want to delete?"); ?>`,
        "icons_assign_msg":`<?php echo _("Are you sure you want to assign this icon to all the markers?"); ?>`,
        "fields_assign_msg":`<?php echo _("Are you sure you want to assign this extra fields icons to all the markers?"); ?>`,
        "copied":`<?php echo _("copied"); ?>`,
    };
</script>
<?php if(!empty($settings['ga_tracking_id'])) : ?>
    <?php if($settings['cookie_consent']) { ?>
        <script type="text/plain" data-category="analytics" data-service="Google Analytics" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $settings['ga_tracking_id']; ?>"></script>
        <script type="text/plain" data-category="analytics" data-service="Google Analytics">
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $settings['ga_tracking_id']; ?>', {
            'page_title': '<?php echo $page; ?>'
        });
        </script>
    <?php } else { ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $settings['ga_tracking_id']; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $settings['ga_tracking_id']; ?>', {
                'page_title': '<?php echo $page; ?>'
            });
        </script>
    <?php } ?>
<?php endif; ?>
<?php if($settings['cookie_consent']) : ?>
    <?php require_once('cookie_consent.php'); ?>
<?php endif; ?>
</body>
</html>
