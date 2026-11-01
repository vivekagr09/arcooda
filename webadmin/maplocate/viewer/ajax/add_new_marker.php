<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("../../db/connection.php");
$id_map = (int)$_POST['id_map'];
$name = strip_tags($_POST['name']);
$street = strip_tags($_POST['street']);
$city = strip_tags($_POST['city']);
$postal_code = strip_tags($_POST['postal_code']);
$country = strip_tags($_POST['country']);
$website = strip_tags($_POST['website']);
$phone = strip_tags($_POST['phone']);
$whatsapp = strip_tags($_POST['whatsapp']);
$email = strip_tags($_POST['email']);
$hours = $_POST['h_desc'];
$description = $_POST['description'];
$lat = str_replace(",",".",$_POST['lat']);
$lon = str_replace(",",".",$_POST['lon']);
$array_images = $_POST['array_images'];
$field_1 = strip_tags($_POST['field_1']);
$field_2 = strip_tags($_POST['field_2']);
$field_3 = strip_tags($_POST['field_3']);
$field_4 = strip_tags($_POST['field_4']);
$field_5 = strip_tags($_POST['field_5']);
$field_6 = strip_tags($_POST['field_6']);
$field_7 = strip_tags($_POST['field_7']);
$field_8 = strip_tags($_POST['field_8']);
$field_9 = strip_tags($_POST['field_9']);
$field_10 = strip_tags($_POST['field_10']);
$field_11 = strip_tags($_POST['field_11']);
$field_12 = strip_tags($_POST['field_12']);
$field_13 = strip_tags($_POST['field_13']);
$field_14 = strip_tags($_POST['field_14']);
$field_15 = strip_tags($_POST['field_15']);
$field_16 = strip_tags($_POST['field_16']);
$field_17 = strip_tags($_POST['field_17']);
$field_18 = strip_tags($_POST['field_18']);
$field_19 = strip_tags($_POST['field_19']);
$field_20 = strip_tags($_POST['field_20']);
$active = 1;
$to_validate = 1;
$category = $_POST['category'];
$status = false;
$main_color_hex = "";
$markers_size = 1.1;
$markers_icon_color_hex = "#ffffff";
$markers_color_hex_hover = "";
$markers_icon_color_hex_hover = "";
$markers_icon = "";
$markers_id_icon_library = 0;
$default_extra_fields = array();

$query = "SELECT main_color_hex,markers_size,markers_color_hex,markers_icon_color_hex,markers_color_hex_hover,markers_icon_color_hex_hover,markers_icon,markers_id_icon_library,enable_add_marker,default_extra_fields FROM sml_maps WHERE id=$id_map;";
$result = $mysqli->query($query);
if($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if(empty($row['markers_color_hex'])) {
        if(!empty($row['main_color_hex'])) {
            $markers_color_hex = $row['main_color_hex'];
        }
    } else {
        $markers_color_hex = $row['markers_color_hex'];
    }
    if(!empty($row['markers_size'])) {
        $marker_size = $row['markers_size'];
    }
    if(!empty($row['markers_icon_color_hex'])) {
        $markers_icon_color_hex = $row['markers_icon_color_hex'];
    }
    if(!empty($row['markers_color_hex_hover'])) {
        $markers_color_hex_hover = $row['markers_color_hex_hover'];
    }
    if(!empty($row['markers_icon_color_hex_hover'])) {
        $markers_icon_color_hex_hover = $row['markers_icon_color_hex_hover'];
    }
    if(!empty($row['markers_icon'])) {
        $markers_icon = $row['markers_icon'];
    }
    if(!empty($row['markers_id_icon_library'])) {
        $markers_id_icon_library = $row['markers_id_icon_library'];
    }
    $enable_add_marker = $row['enable_add_marker'];
    if($enable_add_marker==2) $to_validate=0;
    if(!empty($row['default_extra_fields'])) {
        $default_extra_fields = json_decode($row['default_extra_fields'],true);
    } else {
        $default_extra_fields = array();
    }
}

$query = "SELECT (MAX(`order`)+1) as `order` FROM sml_markers WHERE id_map=$id_map;";
$result = $mysqli->query($query);
if($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $order = $row['order'];
}
if(empty($order)) $order=0;
$query = "INSERT INTO sml_markers(id_map,name,street,city,postal_code,country,website,phone,whatsapp,email,hours,description,lat,lon,active,to_validate,`order`,color_hex,marker_size,icon_color_hex,icon,id_icon_library,color_hex_hover,icon_color_hex_hover,extra_field_value_1,extra_field_value_2,extra_field_value_3,extra_field_value_4,extra_field_value_5,extra_field_value_6,extra_field_value_7,extra_field_value_8,extra_field_value_9,extra_field_value_10,extra_field_value_11,extra_field_value_12,extra_field_value_13,extra_field_value_14,extra_field_value_15,extra_field_value_16,extra_field_value_17,extra_field_value_18,extra_field_value_19,extra_field_value_20) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('isssssssssssssiiisdssissssssssssssssssssssss',$id_map,$name,$street,$city,$postal_code,$country,$website,$phone,$whatsapp,$email,$hours,$description,$lat,$lon,$active,$to_validate,$order,$markers_color_hex,$marker_size,$markers_icon_color_hex,$markers_icon,$markers_id_icon_library,$markers_color_hex_hover,$markers_icon_color_hex_hover,$field_1,$field_2,$field_3,$field_4,$field_5,$field_6,$field_7,$field_8,$field_9,$field_10,$field_11,$field_12,$field_13,$field_14,$field_15,$field_16,$field_17,$field_18,$field_19,$field_20);
    $result = $smt->execute();
    if($result) {
        $id_marker = $mysqli->insert_id;
        if(count($default_extra_fields)>0) {
            $query_i = "UPDATE sml_markers SET extra_field_icon_1=?,extra_field_icon_2=?,extra_field_icon_3=?,extra_field_icon_4=?,extra_field_icon_5=?,extra_field_icon_6=?,extra_field_icon_7=?,extra_field_icon_8=?,extra_field_icon_9=?,extra_field_icon_10=?,extra_field_icon_11=?,extra_field_icon_12=?,extra_field_icon_13=?,extra_field_icon_14=?,extra_field_icon_15=?,extra_field_icon_16=?,extra_field_icon_17=?,extra_field_icon_18=?,extra_field_icon_19=?,extra_field_icon_20=? WHERE id=?;";
            if($smt_i = $mysqli->prepare($query_i)) {
                $smt_i->bind_param('ssssssssssssssssssssi',$default_extra_fields[1],$default_extra_fields[2],$default_extra_fields[3],$default_extra_fields[4],$default_extra_fields[5],$default_extra_fields[6],$default_extra_fields[7],$default_extra_fields[8],$default_extra_fields[9],$default_extra_fields[10],$default_extra_fields[11],$default_extra_fields[12],$default_extra_fields[13],$default_extra_fields[14],$default_extra_fields[15],$default_extra_fields[16],$default_extra_fields[17],$default_extra_fields[18],$default_extra_fields[19],$default_extra_fields[20],$id_marker);
                $smt_i->execute();
            }
        }
        $status = true;
    }
}

if($status) {
    foreach ($category as $cat) {
        $query = "INSERT INTO sml_markers_categories_assoc(id_marker,id_category) VALUES(?,?);";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('ii',$id_marker,$cat);
            $smt->execute();
        }
    }
    $main = 1;
    foreach ($array_images as $image) {
        copy(dirname(__FILE__).'/../marker_images_tmp/'.$image,dirname(__FILE__).'/../marker_images/'.$image);
        copy(dirname(__FILE__).'/../marker_images_tmp/thumb/'.$image,dirname(__FILE__).'/../marker_images/thumb/'.$image);
        unlink(dirname(__FILE__).'/../marker_images_tmp/'.$image);
        unlink(dirname(__FILE__).'/../marker_images_tmp/thumb/'.$image);
        $query = "INSERT INTO sml_images(id_marker,main,image) VALUES(?,?,?);";
        if($smt = $mysqli->prepare($query)) {
            $smt->bind_param('iis',$id_marker,$main,$image);
            $smt->execute();
        }
        $main = 0;
    }
    echo json_encode(array("status"=>"ok"));
} else {
    echo json_encode(array("status"=>"error","error"=>$mysqli->error));
}

