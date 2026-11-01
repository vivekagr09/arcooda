<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
$id_map = $_SESSION['id_map_sel'];
require_once("../../db/connection.php");
require_once("../functions.php");
$id_marker = $_POST['id'];
$name = strip_tags($_POST['name']);
if(isset($_POST['id_categories'])) {
    $id_categories = $_POST['id_categories'];
} else {
    $id_categories = [];
}
$street = strip_tags($_POST['street']);
$city = strip_tags($_POST['city']);
$postal_code = strip_tags($_POST['postal_code']);
$country = strip_tags($_POST['country']);
$website = strip_tags($_POST['website']);
$website_caption = strip_tags($_POST['website_caption']);
$phone = strip_tags($_POST['phone']);
$whatsapp = strip_tags($_POST['whatsapp']);
$email = strip_tags($_POST['email']);
$hours = $_POST['h_desc'];
$description = $_POST['description'];
$lat = str_replace(",",".",$_POST['lat']);
$lon = str_replace(",",".",$_POST['lon']);
if(empty($lat)) $lat=NULL;
if(empty($lon)) $lon=NULL;
$array_images = json_decode($_POST['array_images'],true);
$active = $_POST['active'];
$open_sheet = $_POST['open_sheet'];
$view_popup = $_POST['view_popup'];
$to_validate = $_POST['to_validate'];
$centered = $_POST['centered'];
$featured = $_POST['featured'];
$view_directions = $_POST['view_directions'];
$view_street_view = $_POST['view_street_view'];
$view_review = $_POST['view_review'];
$view_share = $_POST['view_share'];
$color_hex = $_POST['color_hex'];
$icon_color_hex = $_POST['icon_color_hex'];
if(empty($icon_color_hex)) $icon_color_hex="#ffffff";
$color_hex_hover = $_POST['color_hex_hover'];
$icon_color_hex_hover = $_POST['icon_color_hex_hover'];
$marker_size = $_POST['marker_size'];
$id_icon_library = $_POST['id_icon_library'];
$icon = strip_tags($_POST['icon']);
$icon_e1 = $_POST['icon_e1'];
$field_e1 = htmlspecialchars_decode($_POST['field_e1']);
$icon_e2 = $_POST['icon_e2'];
$field_e2 = htmlspecialchars_decode($_POST['field_e2']);
$icon_e3 = $_POST['icon_e3'];
$field_e3 = htmlspecialchars_decode($_POST['field_e3']);
$icon_e4 = $_POST['icon_e4'];
$field_e4 = htmlspecialchars_decode($_POST['field_e4']);
$icon_e5 = $_POST['icon_e5'];
$field_e5 = htmlspecialchars_decode($_POST['field_e5']);
$icon_e6 = $_POST['icon_e6'];
$field_e6 = htmlspecialchars_decode($_POST['field_e6']);
$icon_e7 = $_POST['icon_e7'];
$field_e7 = htmlspecialchars_decode($_POST['field_e7']);
$icon_e8 = $_POST['icon_e8'];
$field_e8 = htmlspecialchars_decode($_POST['field_e8']);
$icon_e9 = $_POST['icon_e9'];
$field_e9 = htmlspecialchars_decode($_POST['field_e9']);
$icon_e10 = $_POST['icon_e10'];
$field_e10 = htmlspecialchars_decode($_POST['field_e10']);
$icon_e11 = $_POST['icon_e11'];
$field_e11 = htmlspecialchars_decode($_POST['field_e11']);
$icon_e12 = $_POST['icon_e12'];
$field_e12 = htmlspecialchars_decode($_POST['field_e12']);
$icon_e13 = $_POST['icon_e13'];
$field_e13 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e13']));
$icon_e14 = $_POST['icon_e14'];
$field_e14 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e14']));
$icon_e15 = $_POST['icon_e15'];
$field_e15 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e15']));
$icon_e16 = $_POST['icon_e16'];
$field_e16 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e16']));
$icon_e17 = $_POST['icon_e17'];
$field_e17 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e17']));
$icon_e18 = $_POST['icon_e18'];
$field_e18 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e18']));
$icon_e19 = $_POST['icon_e19'];
$field_e19 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e19']));
$icon_e20 = $_POST['icon_e20'];
$field_e20 = str_replace("'","\'",htmlspecialchars_decode($_POST['field_e20']));
$icon_b1 = $_POST['icon_b1'];
$title_b1 = str_replace("'","\'",$_POST['title_b1']);
$value_b1 = str_replace("'","\'",htmlspecialchars_decode($_POST['value_b1']));
$min_zoom_level = $_POST['min_zoom_level'];
$geofence_radius = $_POST['geofence_radius'];
$geofence_color = $_POST['geofence_color'];
$open_extra_content = (int)$_POST['open_extra_content'];
if(empty($geofence_radius)) $geofence_radius=0;
if($geofence_radius<0) $geofence_radius=0;
if(empty($geofence_color)) $geofence_color="rgba(0,0,255,0.1)";
$popup_image_height = $_POST['popup_image_height'];
$popup_background = $_POST['popup_background'];
if(empty($popup_background)) $popup_background='#ffffff';
$popup_color = $_POST['popup_color'];
if(empty($popup_color)) $popup_color='#000000';
$schedule = $_POST['schedule'];

$map = get_map($id_map,$_SESSION['id_user']);
if($marker_size==$map['markers_size']) {
    $marker_size = 0;
}

$status = false;
if($id_marker==0) {
    $query_order = "SELECT (MAX(`order`)+1) as `order` FROM sml_markers WHERE id_map=$id_map;";
    $result_order = $mysqli->query($query_order);
    if($result_order) {
        $row_order = $result_order->fetch_array(MYSQLI_ASSOC);
        $order = $row_order['order'];
    }
    if(empty($order)) $order=0;
    $query = "INSERT INTO sml_markers(id_map,name,street,city,postal_code,country,website,website_caption,phone,whatsapp,email,hours,description,lat,lon,icon,active,centered,featured,view_directions,view_street_view,view_review,view_share,color_hex,icon_color_hex,marker_size,id_icon_library,extra_field_icon_1,extra_field_icon_2,extra_field_icon_3,extra_field_icon_4,extra_field_icon_5,extra_field_icon_6,extra_field_icon_7,extra_field_icon_8,extra_field_icon_9,extra_field_icon_10,extra_field_icon_11,extra_field_icon_12,extra_field_icon_13,extra_field_icon_14,extra_field_icon_15,extra_field_icon_16,extra_field_icon_17,extra_field_icon_18,extra_field_icon_19,extra_field_icon_20,extra_field_value_1,extra_field_value_2,extra_field_value_3,extra_field_value_4,extra_field_value_5,extra_field_value_6,extra_field_value_7,extra_field_value_8,extra_field_value_9,extra_field_value_10,extra_field_value_11,extra_field_value_12,extra_field_value_13,extra_field_value_14,extra_field_value_15,extra_field_value_16,extra_field_value_17,extra_field_value_18,extra_field_value_19,extra_field_value_20,extra_button_icon_1,extra_button_title_1,extra_button_value_1,`order`,min_zoom_level,geofence_radius,geofence_color,open_sheet,view_popup,popup_image_height,popup_background,popup_color,color_hex_hover,icon_color_hex_hover,open_extra_content,schedule) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('isssssssssssssssiiiiiiissdisssssssssssssssssssssssssssssssssssssssssssiiisiiissssis',$id_map,$name,$street,$city,$postal_code,$country,$website,$website_caption,$phone,$whatsapp,$email,$hours,$description,$lat,$lon,$icon,$active,$centered,$featured,$view_directions,$view_street_view,$view_review,$view_share,$color_hex,$icon_color_hex,$marker_size,$id_icon_library,$icon_e1,$icon_e2,$icon_e3,$icon_e4,$icon_e5,$icon_e6,$icon_e7,$icon_e8,$icon_e9,$icon_e10,$icon_e11,$icon_e12,$icon_e13,$icon_e14,$icon_e15,$icon_e16,$icon_e17,$icon_e18,$icon_e19,$icon_e20,$field_e1,$field_e2,$field_e3,$field_e4,$field_e5,$field_e6,$field_e7,$field_e8,$field_e9,$field_e10,$field_e11,$field_e12,$field_e13,$field_e14,$field_e15,$field_e16,$field_e17,$field_e18,$field_e19,$field_e20,$icon_b1,$title_b1,$value_b1,$order,$min_zoom_level,$geofence_radius,$geofence_color,$open_sheet,$view_popup,$popup_image_height,$popup_background,$popup_color,$color_hex_hover,$icon_color_hex_hover,$open_extra_content,$schedule);
        $result = $smt->execute();
        if($result) {
            $id_marker = $mysqli->insert_id;
            $status = true;
        }
    }
} else {
    $query = "UPDATE sml_markers SET name=?,street=?,city=?,postal_code=?,country=?,website=?,website_caption=?,phone=?,whatsapp=?,email=?,hours=?,description=?,lat=?,lon=?,icon=?,active=?,to_validate=?,centered=?,featured=?,view_directions=?,view_street_view=?,view_review=?,view_share=?,color_hex=?,icon_color_hex=?,marker_size=?,id_icon_library=?,extra_field_icon_1=?,extra_field_icon_2=?,extra_field_icon_3=?,extra_field_icon_4=?,extra_field_icon_5=?,extra_field_icon_6=?,extra_field_icon_7=?,extra_field_icon_8=?,extra_field_icon_9=?,extra_field_icon_10=?,extra_field_icon_11=?,extra_field_icon_12=?,extra_field_icon_13=?,extra_field_icon_14=?,extra_field_icon_15=?,extra_field_icon_16=?,extra_field_icon_17=?,extra_field_icon_18=?,extra_field_icon_19=?,extra_field_icon_20=?,extra_field_value_1=?,extra_field_value_2=?,extra_field_value_3=?,extra_field_value_4=?,extra_field_value_5=?,extra_field_value_6=?,extra_field_value_7=?,extra_field_value_8=?,extra_field_value_9=?,extra_field_value_10=?,extra_field_value_11=?,extra_field_value_12=?,extra_field_value_13=?,extra_field_value_14=?,extra_field_value_15=?,extra_field_value_16=?,extra_field_value_17=?,extra_field_value_18=?,extra_field_value_19=?,extra_field_value_20=?,extra_button_icon_1=?,extra_button_title_1=?,extra_button_value_1=?,min_zoom_level=?,geofence_radius=?,geofence_color=?,open_sheet=?,view_popup=?,popup_image_height=?,popup_background=?,popup_color=?,color_hex_hover=?,icon_color_hex_hover=?,open_extra_content=?,schedule=? WHERE id=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('sssssssssssssssiiiiiiiissdisssssssssssssssssssssssssssssssssssssssssssiisiiissssisi',$name,$street,$city,$postal_code,$country,$website,$website_caption,$phone,$whatsapp,$email,$hours,$description,$lat,$lon,$icon,$active,$to_validate,$centered,$featured,$view_directions,$view_street_view,$view_review,$view_share,$color_hex,$icon_color_hex,$marker_size,$id_icon_library,$icon_e1,$icon_e2,$icon_e3,$icon_e4,$icon_e5,$icon_e6,$icon_e7,$icon_e8,$icon_e9,$icon_e10,$icon_e11,$icon_e12,$icon_e13,$icon_e14,$icon_e15,$icon_e16,$icon_e17,$icon_e18,$icon_e19,$icon_e20,$field_e1,$field_e2,$field_e3,$field_e4,$field_e5,$field_e6,$field_e7,$field_e8,$field_e9,$field_e10,$field_e11,$field_e12,$field_e13,$field_e14,$field_e15,$field_e16,$field_e17,$field_e18,$field_e19,$field_e20,$icon_b1,$title_b1,$value_b1,$min_zoom_level,$geofence_radius,$geofence_color,$open_sheet,$view_popup,$popup_image_height,$popup_background,$popup_color,$color_hex_hover,$icon_color_hex_hover,$open_extra_content,$schedule,$id_marker);
        $result = $smt->execute();
        if($result) {
            $status = true;
        }
    }
}

if($status) {
    $query = "DELETE FROM sml_markers_categories_assoc WHERE id_marker=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_marker);
        $smt->execute();
    }
    if($centered==1) {
        $query = "UPDATE sml_markers SET centered=0 WHERE id!=?;";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('i',$id_marker);
            $smt->execute();
        }
    }
    foreach ($id_categories as $id_category) {
        $query = "INSERT INTO sml_markers_categories_assoc(id_marker,id_category) VALUES(?,?);";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('ii',$id_marker,$id_category);
            $smt->execute();
        }
    }
    foreach ($array_images as $image) {
        $id = $image['id'];
        $action = $image['action'];
        $main = $image['main'];
        $foto = $image['image'];
        switch($action) {
            case 'insert':
                $query = "INSERT INTO sml_images(id_marker,main,image) VALUES(?,?,?);";
                if($smt = $mysqli->prepare($query)) {
                    $smt->bind_param('iis',$id_marker,$main,$foto);
                    $smt->execute();
                }
                break;
            case 'update':
                $query = "UPDATE sml_images SET main=? WHERE id=?;";
                if($smt = $mysqli->prepare($query)) {
                    $smt->bind_param('ii',$main,$id);
                    $smt->execute();
                }
                break;
            case 'delete':
                $query = "DELETE FROM sml_images WHERE id=?;";
                if($smt = $mysqli->prepare($query)) {
                    $smt->bind_param('i',$id);
                    $smt->execute();
                }
                break;
        }
    }
    ob_end_clean();
    echo json_encode(array("status"=>"ok"));
    exit;
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error","error"=>$mysqli->error));
    exit;
}