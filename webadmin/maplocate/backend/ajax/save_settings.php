<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
unset($_SESSION['timezone']);
session_write_close();
$id_user = $_SESSION['id_user'];
unset($_SESSION['lang']);
if(!get_user_role($id_user)=='administrator') {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
    exit;
}
$logo_exist = "";
$query = "SELECT logo FROM sml_settings LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows==1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $logo_exist = $row['logo'];
    }
}
$name_app = strip_tags($_POST['name']);
$name = strip_tags($_POST['name']);
$theme_color = strip_tags($_POST['theme_color']);
if(empty($theme_color)) $theme_color='#0b5394';
$sidebar = strip_tags($_POST['sidebar']);
$theme_color_dark = strip_tags($_POST['theme_color_dark']);
if(empty($theme_color_dark)) $theme_color_dark=null;
$dark_mode = (int)$_POST['dark_mode'];
$sidebar_color_1 = strip_tags($_POST['sidebar_color_1']);
if(empty($sidebar_color_1)) $sidebar_color_1=null;
$sidebar_color_2 = strip_tags($_POST['sidebar_color_2']);
if(empty($sidebar_color_2)) $sidebar_color_2=null;
$sidebar_color_1_dark = strip_tags($_POST['sidebar_color_1_dark']);
if(empty($sidebar_color_1_dark)) $sidebar_color_1_dark=null;
$sidebar_color_2_dark = strip_tags($_POST['sidebar_color_2_dark']);
if(empty($sidebar_color_2_dark)) $sidebar_color_2_dark=null;
$font_provider = $_POST['font_provider'];
$font_backend = $_POST['font_backend'];
if(empty($font_backend)) $font_backend='Nunito';
$welcome_msg = $_POST['welcome_msg'];
if($welcome_msg=='<p><br></p>') $welcome_msg="";
$logo = $_POST['logo'];
$background = $_POST['background'];
$timezone = $_POST['timezone'];
$language = $_POST['language'];
$language_domain = $_POST['language_domain'];
$language_domain = str_replace("_lang","",$language_domain);
$languages_enabled = $_POST['languages_enabled'];
$css_array = json_decode($_POST['css_array'],true);
$js_array = json_decode($_POST['js_array'],true);
$head_array = json_decode($_POST['head_array'],true);
$footer_link_1 = strip_tags($_POST['footer_link_1']);
$footer_link_2 = strip_tags($_POST['footer_link_2']);
$footer_link_3 = strip_tags($_POST['footer_link_3']);
$footer_value_1 = $_POST['footer_value_1'];
$footer_value_2 = $_POST['footer_value_2'];
$footer_value_3 = $_POST['footer_value_3'];
if(strpos(get_string_between($footer_value_1, '<p>', '</p>'), 'http') === 0) {
    $footer_value_1 = str_replace(['<p>','</p>'],'',$footer_value_1);
}
if(strpos(get_string_between($footer_value_2, '<p>', '</p>'), 'http') === 0) {
    $footer_value_2 = str_replace(['<p>','</p>'],'',$footer_value_2);
}
if(strpos(get_string_between($footer_value_3, '<p>', '</p>'), 'http') === 0) {
    $footer_value_3 = str_replace(['<p>','</p>'],'',$footer_value_3);
}
$style_login = $_POST['style_login'];
$openrouteservice_api_key = strip_tags($_POST['openrouteservice_api_key']);
$share_providers = strip_tags($_POST['share_providers']);
$ga_tracking_id = strip_tags($_POST['ga_tracking_id']);
$terms_and_conditions = $_POST['terms_and_conditions'];
if($terms_and_conditions=='<p><br></p>' || $terms_and_conditions=='<p></p>') $terms_and_conditions="";
$privacy_policy = $_POST['privacy_policy'];
if($privacy_policy=='<p><br></p>' || $privacy_policy=='<p></p>') $privacy_policy="";
$cookie_policy = $_POST['cookie_policy'];
if($cookie_policy=='<p><br></p>' || $cookie_policy=='<p></p>') $cookie_policy="";
if(strpos(get_string_between($terms_and_conditions, '<p>', '</p>'), 'http') === 0) {
    $terms_and_conditions = str_replace(['<p>','</p>'],'',$terms_and_conditions);
}
if(strpos(get_string_between($privacy_policy, '<p>', '</p>'), 'http') === 0) {
    $privacy_policy = str_replace(['<p>','</p>'],'',$privacy_policy);
}
if(strpos(get_string_between($cookie_policy, '<p>', '</p>'), 'http') === 0) {
    $cookie_policy = str_replace(['<p>','</p>'],'',$cookie_policy);
}
$cookie_consent = (int)$_POST['cookie_consent'];
$furl_blacklist = strip_tags($_POST['furl_blacklist']);
$furl_blacklist = strtolower($furl_blacklist);
$furl_blacklist = str_replace(" ","",$furl_blacklist);
foreach ($css_array as $name=>$content) {
    if($name=='custom_b') {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom_b.css';
    } else {
        $url_css = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$name.'.css';
    }
    if(file_exists($url_css) && $content=='') {
        @unlink($url_css);
    } else {
        if($content!='') {
            @file_put_contents($url_css,$content);
        }
    }
}
foreach ($js_array as $name=>$content) {
    if($name=='custom_b') {
        $url_js = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'custom_b.js';
    } else {
        $url_js = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.$name.'.js';
    }
    if(file_exists($url_js) && $content=='') {
        @unlink($url_js);
    } else {
        if($content!='') {
            @file_put_contents($url_js,$content);
        }
    }
}
foreach ($head_array as $name=>$content) {
    if($name=='custom_b') {
        $url_head = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.'custom_b.php';
    } else {
        $url_head = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'header'.DIRECTORY_SEPARATOR.$name.'.php';
    }
    if(file_exists($url_head) && $content=='') {
        @unlink($url_head);
    } else {
        if($content!='') {
            @file_put_contents($url_head,$content);
        }
    }
}
$query = "UPDATE sml_settings SET name=?,theme_color=?,theme_color_dark=?,font_backend=?,font_provider=?,welcome_msg=?,logo=?,background=?,language=?,language_domain=?,languages_enabled=?,footer_link_1=?,footer_link_2=?,footer_link_3=?,footer_value_1=?,footer_value_2=?,footer_value_3=?,style_login=?,timezone=?,share_providers=?,ga_tracking_id=?,cookie_policy=?,cookie_consent=?,terms_and_conditions=?,privacy_policy=?,furl_blacklist=?,sidebar=?,dark_mode=?,sidebar_color_1=?,sidebar_color_2=?,sidebar_color_1_dark=?,sidebar_color_2_dark=?";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param("sssssssssssssssssissssissssissss",$name_app,$theme_color,$theme_color_dark,$font_backend,$font_provider,$welcome_msg,$logo,$background,$language,$language_domain,$languages_enabled,$footer_link_1,$footer_link_2,$footer_link_3,$footer_value_1,$footer_value_2,$footer_value_3,$style_login,$timezone,$share_providers,$ga_tracking_id,$cookie_policy,$cookie_consent,$terms_and_conditions,$privacy_policy,$furl_blacklist,$sidebar,$dark_mode,$sidebar_color_1,$sidebar_color_2,$sidebar_color_1_dark,$sidebar_color_2_dark);
    $result = $smt->execute();
    if($result) {
        if($openrouteservice_api_key!="keep_openrouteservice_api_key") {
            $query = "UPDATE sml_settings SET openrouteservice_api_key=?;";
            if($smt = $mysqli->prepare($query)) {
                $smt->bind_param('s',$openrouteservice_api_key);
                $smt->execute();
            }
        }
        ob_end_clean();
        echo json_encode(array("status"=>"ok"));
    } else {
        ob_end_clean();
        echo json_encode(array("status"=>"error"));
    }
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}