<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if(!file_exists("../config/config.inc.php")) {
    header("Location: ../install/start.php");
}
$session_id = session_id();
$_SESSION['sml_si_l']=$session_id;
require_once("functions.php");
$settings = get_settings();
if(isset($_GET['lang'])) {
    if(check_language_enabled($_GET['lang'],$settings['languages_enabled'])) {
        $lang = $_GET['lang'];
        $_SESSION['lang']=$lang;
        header("Location: login.php");
        exit;
    }
}
set_language($settings['language'],$settings['language_domain']);
$style_login = $settings['style_login'];
if(empty($_SESSION['lang'])) {
    $lang = $settings['language'];
} else {
    $lang = $_SESSION['lang'];
}
$_SESSION['theme_color']=$settings['theme_color'];
$autologin = 0;
$remember_me = false;
if (isset($_COOKIE['sml_username']) && isset($_COOKIE['sml_password'])) {
    $username = encrypt_decrypt('decrypt',$_COOKIE['sml_username'],'sml');
    $password = encrypt_decrypt('decrypt',$_COOKIE['sml_password'],'sml');
    if(!empty($username) && !empty($password)) {
        $remember_me = true;
        if(isset($_COOKIE['sml_login'])) {
            $autologin = 1;
        }
    }
} else {
    $username = "";
    $password = "";
}
session_write_close();
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
    <link href="../viewer/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
        <link rel="stylesheet" type="text/css" href="https://api.fonts.coollabs.io/css2?family=<?php echo $settings['font_backend']; ?>&display=swap">
        <?php break;
    } ?>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <?php if($settings['cookie_consent']) : ?>
        <link rel="stylesheet" type="text/css" href="vendor/cookieconsent/cookieconsent.min.css?v=3.0.0">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="css/theme.php?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="css/theme_dark.php?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="css/dark_mode.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/custom.css?v=<?php echo time(); ?>">
</head>

<body class="bg_login <?php echo ($settings['background']!='' && $style_login==2) ? 'bg_image' : 'bg-gradient-primary' ; ?>" style="<?php echo ($settings['background']!='' && $style_login==2) ? 'background-image: url(assets/'.$settings['background'].') !important;'  : '' ; ?>">
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
        if(dark_mode=='1') document.body.classList.add("dark_mode");
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            dark_mode = e.matches ? '1' : '0';
            if(dark_mode=='1') {
                document.body.classList.add("dark_mode");
                localStorage.setItem("dark_mode",'1');
            } else {
                document.body.classList.remove("dark_mode");
                localStorage.setItem("dark_mode",'0');
            }
        });
    }
</script>
<style>
    *{ font-family: '<?php echo $settings['font_backend']; ?>', sans-serif; }
</style>
<div class="container">
    <div class="row">
        <?php if(!empty($settings['logo'])) { ?>
            <div class="col-md-12 text-white mt-4 text-center">
                <img style="max-height:100px;max-width:200px;width:auto;height:auto" src="assets/<?php echo $settings['logo']; ?>" />
            </div>
        <?php } else { ?>
            <div class="col-md-12 text-white mt-4 text-center title_name_login">
                <h3 class="mb-0"><?php echo strtoupper($settings['name']); ?></h3>
            </div>
        <?php } ?>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="<?php echo ($style_login==1 && $settings['background']!='') ? 'col-xl-10 col-lg-12 col-md-9' : 'col-xl-6 col-lg-8 col-md-9'; ?>" style="<?php echo ($style_login==2) ? 'max-width:540px' : ''; ?>">
            <div class="card o-hidden border-0 shadow-lg my-2 <?php echo ($style_login==2) ? 'glass_effect' : ''; ?>">
                <div class="card-body p-0">
                    <div class="row" style="min-height: 530px;">
                        <div style="<?php echo ($settings['background']!='' && $style_login==1) ? 'background-image: url(assets/'.$settings['background'].');'  : '' ; ?>" class="d-none bg-login-image <?php echo ($style_login==1 && $settings['background']!='') ? 'col-lg-6 d-lg-block' : ''; ?>"></div>
                        <div class="<?php echo ($style_login==1 && $settings['background']!='') ? 'col-lg-6' : 'col-md-12'; ?> pl-0">
                            <div class="p-5">
                                <li class="nav-item dropdown no-arrow lang_switcher_login" style="<?php echo ($style_login==2 || $settings['background']=='') ? 'left:10px' : ''; ?>">
                                    <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php echo ($settings['languages_count']==1) ? 'style="cursor:default;pointer-events:none;"' : ''; ?> >
                                        <img style="height: 14px;" src="img/flags_lang/<?php echo $lang; ?>.png" />
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left shadow" aria-labelledby="langDropdown">
                                        <?php
                                        if(check_language_enabled('en_US',$settings['languages_enabled']) && check_language_enabled('en_GB',$settings['languages_enabled'])) {
                                            $en_gb = "English (British)";
                                            $en_us = "English (American)";
                                        } else if(!check_language_enabled('en_US',$settings['languages_enabled']) && check_language_enabled('en_GB',$settings['languages_enabled'])) {
                                            $en_gb = "English";
                                            $en_us = "English";
                                        } else if(check_language_enabled('en_US',$settings['languages_enabled']) && !check_language_enabled('en_GB',$settings['languages_enabled'])) {
                                            $en_gb = "English";
                                            $en_us = "English";
                                        }
                                        ?>
                                        <?php if(check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ar_SA');" class="<?php echo ($lang=='ar_SA') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ar_SA.png?v=2" /> <span class="ml-2">العربية</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_CN');" class="<?php echo ($lang=='zh_CN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_CN.png?v=2" /> <span class="ml-2">简体中文</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_HK');" class="<?php echo ($lang=='zh_HK') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_HK.png?v=2" /> <span class="ml-2">繁體中文（香港）</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_TW');" class="<?php echo ($lang=='zh_TW') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_TW.png?v=2" /> <span class="ml-2">繁體中文（台灣）</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('cs_CZ');" class="<?php echo ($lang=='cs_CZ') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/cs_CZ.png?v=2" /> <span class="ml-2">Čeština</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('nl_NL');" class="<?php echo ($lang=='nl_NL') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/nl_NL.png?v=2" /> <span class="ml-2">Nederlands</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('en_US',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('en_US');" class="<?php echo ($lang=='en_US') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/en_US.png?v=2" /> <span class="ml-2"><?php echo $en_us; ?></span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('en_GB',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('en_GB');" class="<?php echo ($lang=='en_GB') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/en_GB.png?v=2" /> <span class="ml-2"><?php echo $en_gb; ?></span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fil_PH');" class="<?php echo ($lang=='fil_PH') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fil_PH.png?v=2" /> <span class="ml-2">Filipino</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fr_FR');" class="<?php echo ($lang=='fr_FR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fr_FR.png?v=2" /> <span class="ml-2">Français</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('de_DE',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('de_DE');" class="<?php echo ($lang=='de_DE') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/de_DE.png?v=2" /> <span class="ml-2">Deutsch</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('hi_IN');" class="<?php echo ($lang=='hi_IN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/hi_IN.png?v=2" /> <span class="ml-2">हिंदी</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('hu_HU');" class="<?php echo ($lang=='hu_HU') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/hu_HU.png?v=2" /> <span class="ml-2">Magyar</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('rw_RW',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('rw_RW');" class="<?php echo ($lang=='rw_RW') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/rw_RW.png?v=2" /> <span class="ml-2">Kinyarwanda</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ko_KR');" class="<?php echo ($lang=='ko_KR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ko_KR.png?v=2" /> <span class="ml-2">한국어</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('id_ID',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('id_ID');" class="<?php echo ($lang=='id_ID') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/id_ID.png?v=2" /> <span class="ml-2">Bahasa Indonesia</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('it_IT',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('it_IT');" class="<?php echo ($lang=='it_IT') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/it_IT.png?v=2" /> <span class="ml-2">Italiano</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ja_JP');" class="<?php echo ($lang=='ja_JP') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ja_JP.png?v=2" /> <span class="ml-2">日本語</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fa_IR');" class="<?php echo ($lang=='fa_IR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fa_IR.png?v=2" /> <span class="ml-2">فارسی</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('fi_FI',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fi_FI');" class="<?php echo ($lang=='fi_FI') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fi_FI.png?v=2" /> <span class="ml-2">Suomen Kieli</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pl_PL');" class="<?php echo ($lang=='pl_PL') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pl_PL.png?v=2" /> <span class="ml-2">Polski</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pt_BR');" class="<?php echo ($lang=='pt_BR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pt_BR.png?v=2" /> <span class="ml-2">Português Brasileiro</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pt_PT');" class="<?php echo ($lang=='pt_PT') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pt_PT.png?v=2" /> <span class="ml-2">Português Europeu</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('es_ES',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('es_ES');" class="<?php echo ($lang=='es_ES') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/es_ES.png?v=2" /> <span class="ml-2">Español</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ro_RO');" class="<?php echo ($lang=='ro_RO') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ro_RO.png?v=2" /> <span class="ml-2">Română</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ru_RU');" class="<?php echo ($lang=='ru_RU') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ru_RU.png?v=2" /> <span class="ml-2">Русский</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('sv_SE');" class="<?php echo ($lang=='sv_SE') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/sv_SE.png?v=2" /> <span class="ml-2">Svenska</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('tg_TJ');" class="<?php echo ($lang=='tg_TJ') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/tg_TJ.png?v=2" /> <span class="ml-2">Тоҷикӣ</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('th_TH',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('th_TH');" class="<?php echo ($lang=='th_TH') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/th_TH.png?v=2" /> <span class="ml-2">ไทย</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('tr_TR');" class="<?php echo ($lang=='tr_TR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/tr_TR.png?v=2" /> <span class="ml-2">Türkçe</span></span> <?php endif; ?>
                                        <?php if(check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('vi_VN');" class="<?php echo ($lang=='vi_VN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/vi_VN.png?v=2" /> <span class="ml-2">Tiếng Việt</span></span> <?php endif; ?>
                                    </div>
                                </li>
                                <div class="text-center">
                                    <h4 id="title_login" class="h4 text-gray-900 mb-0"><?php echo _("Welcome!"); ?></h4>
                                    <p id="description_login" class="mb-3"><?php echo _("Login to your account here"); ?></p>
                                </div>
                                <form class="user">
                                    <div class="form-group">
                                        <input tabindex="1" autofocus type="text" class="form-control form-control-user" id="username_l" aria-describedby="emailHelp" value="<?php echo $username; ?>" placeholder="<?php echo _("Username"); ?>">
                                    </div>
                                    <div class="form-group position-relative">
                                        <input tabindex="2" type="password" class="form-control form-control-user" id="password_l" value="<?php echo $password; ?>" placeholder="<?php echo _("Password"); ?>">
                                        <i onclick="show_hide_password('password_l');" style="position:absolute;top:50%;right:15px;transform:translateY(-50%);cursor:pointer;" class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </div>
                                    <div class="form-group text-center">
                                        <div class="form-check">
                                            <input tabindex="3" type="checkbox" id="remember_l" <?php echo ($remember_me) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="remember_l">
                                                <?php echo _("Remember me"); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <a tabindex="3" href="#" id="btn_login" onclick="login();return false;" class="btn btn-primary btn-user btn-block">
                                        <?php echo _("Login"); ?>
                                    </a>
                                    <hr>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("footer_login.php"); ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<?php if($settings['cookie_consent']) : ?>
    <script type="text/javascript" src="vendor/cookieconsent/cookieconsent.min.js?v=3.0.0"></script>
<?php endif; ?>
<script src="js/function.js?v=<?php echo time(); ?>"></script>
<script>
    (function($) {
        "use strict"; // Start of use strict
        window.autologin = <?php echo $autologin; ?>;
        if(autologin==1) {
            login();
        }
        $(document).keyup(function(event) {
            if (event.key == "Enter") {
                event.preventDefault();
                $("#btn_login").trigger('click');
            }
        });
    })(jQuery); // End of use strict

    function show_hide_password(id) {
        if($('#'+id).attr("type") == "text"){
            $('#'+id).attr('type', 'password');
            $('#'+id).parent().find('i').addClass( "fa-eye-slash" );
            $('#'+id).parent().find('i').removeClass( "fa-eye" );
        }else if($('#'+id).attr("type") == "password"){
            $('#'+id).attr('type', 'text');
            $('#'+id).parent().find('i').removeClass( "fa-eye-slash" );
            $('#'+id).parent().find('i').addClass( "fa-eye" );
        }
    }
</script>
<?php if(!empty($settings['ga_tracking_id'])) : ?>
    <?php if($settings['cookie_consent']) { ?>
        <script type="text/plain" data-category="analytics" data-service="Google Analytics" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $settings['ga_tracking_id']; ?>"></script>
        <script type="text/plain" data-category="analytics" data-service="Google Analytics">
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $settings['ga_tracking_id']; ?>', {
            'page_title': 'login'
        });
        </script>
    <?php } else { ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $settings['ga_tracking_id']; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $settings['ga_tracking_id']; ?>', {
                'page_title': 'login'
            });
        </script>
    <?php } ?>
<?php endif; ?>
<?php if($settings['cookie_consent']) : ?>
    <?php require_once('cookie_consent.php'); ?>
<?php endif; ?>
</body>
</html>
