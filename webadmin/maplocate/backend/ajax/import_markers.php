<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require('spreadsheet-reader/php-excel-reader/excel_reader2.php');
require('spreadsheet-reader/SpreadsheetReader.php');
require_once("../../db/connection.php");
ini_set('memory_limit','256M');
ini_set('max_execution_time', 240);
ini_set('max_input_time', 240);
if (!file_exists(dirname(__FILE__).'/../tmp/')) {
    mkdir(dirname(__FILE__).'/../tmp/', 0775);
}
if(!isset($_SESSION['id_map_sel'])) {
    echo "ERROR: retry later.";
    exit;
}
$id_map = $_SESSION['id_map_sel'];
$array_exist_markers = array();
$query = "SELECT id FROM sml_markers WHERE id_map=$id_map;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows>0) {
        while($row=$result->fetch_array(MYSQLI_ASSOC)) {
            $id_m = $row['id'];
            array_push($array_exist_markers,$id_m);
        }
    }
}
$array_exist_categories = array();
$query = "SELECT id FROM sml_categories WHERE id_map=$id_map;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows>0) {
        while($row=$result->fetch_array(MYSQLI_ASSOC)) {
            $id_c = $row['id'];
            array_push($array_exist_categories,$id_c);
        }
    }
}
$count = 0;
$count_i = 0;
$count_insert = 0;
$count_update = 0;
$error = '';
if(isset($_FILES) && !empty($_FILES['file']['name'])){
    $allowed_ext = array('csv','xls','xlsx');
    $filename = $_FILES['file']['name'];
    $ext = explode('.',$filename);
    $ext = strtolower(end($ext));
    if(in_array($ext,$allowed_ext)){
        $fname = "markers_".time().".$ext";
        $moved = move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/../tmp/'.$fname);
        if($moved) {
            try {
                $Reader = new SpreadsheetReader(dirname(__FILE__).'/../tmp/'.$fname);
            } catch (Exception $e) {
                echo 'ERROR: error parsing file.';
                exit;
            }
            foreach ($Reader as $Row) {
                if($Row[0]=='id') continue;
                $id = $Row[0];
                $lat = str_replace(",",".",$Row[1]);
                $lon = str_replace(",",".",$Row[2]);
                if(!is_numeric($lat)) {
                    $lat = "";
                }
                if(!is_numeric($lon)) {
                    $lon = "";
                }
                $name = strip_tags($Row[3]);
                $street = strip_tags($Row[4]);
                $city = strip_tags($Row[5]);
                $postal_code = strip_tags($Row[6]);
                $country = strip_tags($Row[7]);
                $website = strip_tags($Row[8]);
                $website_caption = strip_tags($Row[9]);
                $email = strip_tags($Row[10]);
                $phone = strip_tags($Row[11]);
                $whatsapp = strip_tags($Row[12]);
                $hours = $Row[13];
                $description = $Row[14];
                $icon = $Row[15];
                $id_icon_library = $Row[16];
                if(empty($id_icon_library)) $id_icon_library=0;
                $active = $Row[17];
                if(empty($active)) $active=1;
                $view_directions = $Row[18];
                if(empty($view_directions)) $view_directions=1;
                $view_street_view = $Row[19];
                if(empty($view_street_view)) $view_street_view=1;
                $view_review = $Row[20];
                if(empty($view_review)) $view_review=1;
                $view_share = $Row[21];
                if(empty($view_share)) $view_share=1;
                $order = $Row[22];
                if(empty($order)) $order=0;
                $color_hex = $Row[23];
                if(empty($color_hex)) $color_hex='';
                $icon_color_hex = $Row[24];
                if(empty($icon_color_hex)) $icon_color_hex='#ffffff';
                $color_hex_hover = $Row[25];
                if(empty($color_hex_hover)) $color_hex_hover='';
                $icon_color_hex_hover = $Row[26];
                if(empty($icon_color_hex_hover)) $icon_color_hex_hover='';
                $marker_size = $Row[27];
                if(empty($marker_size)) $marker_size=0;
                $field_e1 = $Row[28];
                $field_e1_icon = $Row[29];
                $field_e2 = $Row[30];
                $field_e2_icon = $Row[31];
                $field_e3 = $Row[32];
                $field_e3_icon = $Row[33];
                $field_e4 = $Row[34];
                $field_e4_icon = $Row[35];
                $field_e5 = $Row[36];
                $field_e5_icon = $Row[37];
                $field_e6 = $Row[38];
                $field_e6_icon = $Row[39];
                $field_e7 = $Row[40];
                $field_e7_icon = $Row[41];
                $field_e8 = $Row[42];
                $field_e8_icon = $Row[43];
                $field_e9 = $Row[44];
                $field_e9_icon = $Row[45];
                $field_e10 = $Row[46];
                $field_e10_icon = $Row[47];
                $field_e11 = $Row[48];
                $field_e11_icon = $Row[49];
                $field_e12 = $Row[50];
                $field_e12_icon = $Row[51];
                $field_e13 = $Row[52];
                $field_e13_icon = $Row[53];
                $field_e14 = $Row[54];
                $field_e14_icon = $Row[55];
                $field_e15 = $Row[56];
                $field_e15_icon = $Row[57];
                $field_e16 = $Row[58];
                $field_e16_icon = $Row[59];
                $field_e17 = $Row[60];
                $field_e17_icon = $Row[61];
                $field_e18 = $Row[62];
                $field_e18_icon = $Row[63];
                $field_e19 = $Row[64];
                $field_e19_icon = $Row[65];
                $field_e20 = $Row[66];
                $field_e20_icon = $Row[67];
                $button_b1 = $Row[68];
                $button_b1_icon = $Row[69];
                $button_b1_title = $Row[70];
                $min_zoom_level = $Row[71];
                $geofence_radius = $Row[72];
                $geofence_color = $Row[73];
                $id_categories = $Row[74];
                $open_sheet = $Row[75];
                $view_popup = $Row[76];
                $popup_image_height = $Row[77];
                $popup_background = $Row[78];
                $popup_color = $Row[79];
                if(empty($min_zoom_level)) $min_zoom_level=0;
                if($min_zoom_level<0) $min_zoom_level=0;
                if($min_zoom_level>20) $min_zoom_level=20;
                if(empty($geofence_radius)) $geofence_radius=0;
                if(empty($geofence_color)) $geofence_color='rgba(0,0,255,0.1)';
                if(empty($open_sheet)) $open_sheet=1;
                if(empty($view_popup)) $view_popup=1;
                if(empty($popup_image_height)) $popup_image_height=60;
                if(empty($popup_background)) $popup_background='#ffffff';
                if(empty($popup_color)) $popup_color='#000000';
                $count++;
                if(!in_array($id,$array_exist_markers)) {
                    $query = "INSERT INTO sml_markers(id_map,name,street,city,postal_code,country,website,website_caption,phone,email,whatsapp,hours,description,icon,lat,lon,active,view_directions,view_street_view,view_review,view_share,`order`,id_icon_library,marker_size,color_hex,icon_color_hex,extra_field_value_1,extra_field_value_2,extra_field_value_3,extra_field_value_4,extra_field_value_5,extra_field_value_6,extra_field_value_7,extra_field_value_8,extra_field_value_9,extra_field_value_10,extra_field_value_11,extra_field_value_12,extra_field_value_13,extra_field_value_14,extra_field_value_15,extra_field_value_16,extra_field_value_17,extra_field_value_18,extra_field_value_19,extra_field_value_20,extra_field_icon_1,extra_field_icon_2,extra_field_icon_3,extra_field_icon_4,extra_field_icon_5,extra_field_icon_6,extra_field_icon_7,extra_field_icon_8,extra_field_icon_9,extra_field_icon_10,extra_field_icon_11,extra_field_icon_12,extra_field_icon_13,extra_field_icon_14,extra_field_icon_15,extra_field_icon_16,extra_field_icon_17,extra_field_icon_18,extra_field_icon_19,extra_field_icon_20,extra_button_value_1,extra_button_icon_1,extra_button_title_1,min_zoom_level,geofence_radius,geofence_color,open_sheet,view_popup,popup_image_height,popup_background,popup_color,color_hex_hover,icon_color_hex_hover) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
                    if($smt = $mysqli->prepare($query)) {
                        $smt->bind_param('isssssssssssssssiiiiiiidsssssssssssssssssssssssssssssssssssssssssssssiisiiissss',$id_map,$name,$street,$city,$postal_code,$country,$website,$website_caption,$phone,$email,$whatsapp,$hours,$description,$icon,$lat,$lon,$active,$view_directions,$view_street_view,$view_review,$view_share,$order,$id_icon_library,$marker_size,$color_hex,$icon_color_hex,$field_e1,$field_e2,$field_e3,$field_e4,$field_e5,$field_e6,$field_e7,$field_e8,$field_e9,$field_e10,$field_e11,$field_e12,$field_e13,$field_e14,$field_e15,$field_e16,$field_e17,$field_e18,$field_e19,$field_e20,$field_e1_icon,$field_e2_icon,$field_e3_icon,$field_e4_icon,$field_e5_icon,$field_e6_icon,$field_e7_icon,$field_e8_icon,$field_e9_icon,$field_e10_icon,$field_e11_icon,$field_e12_icon,$field_e13_icon,$field_e14_icon,$field_e15_icon,$field_e16_icon,$field_e17_icon,$field_e18_icon,$field_e19_icon,$field_e20_icon,$button_b1,$button_b1_icon,$button_b1_title,$min_zoom_level,$geofence_radius,$geofence_color,$open_sheet,$view_popup,$popup_image_height,$popup_background,$popup_color,$color_hex_hover,$icon_color_hex_hover);
                        $result = $smt->execute();
                        if($result) {
                            $id_insert = $mysqli->insert_id;
                            if(!empty($id_categories)) {
                                $array_cat = explode(",",$id_categories);
                                foreach ($array_cat as $id_cat) {
                                    if(in_array($id_cat,$array_exist_categories)) {
                                        $mysqli->query("INSERT INTO sml_markers_categories_assoc(id_marker,id_category) VALUES($id_insert,$id_cat)");
                                    }
                                }
                            }
                            $count_insert++;
                            $count_i++;
                        } else {
                            echo 'ERROR: '.$mysqli->error;
                            exit;
                        }
                    } else {
                        echo 'ERROR: '.$mysqli->error;
                        exit;
                    }
                } else {
                    $query = "UPDATE sml_markers SET name=?,street=?,city=?,postal_code=?,country=?,website=?,website_caption=?,phone=?,whatsapp=?,email=?,hours=?,description=?,lat=?,lon=?,icon=?,active=?,view_directions=?,view_street_view=?,view_review=?,view_share=?,color_hex=?,icon_color_hex=?,marker_size=?,id_icon_library=?,extra_field_icon_1=?,extra_field_icon_2=?,extra_field_icon_3=?,extra_field_icon_4=?,extra_field_icon_5=?,extra_field_icon_6=?,extra_field_icon_7=?,extra_field_icon_8=?,extra_field_icon_9=?,extra_field_icon_10=?,extra_field_icon_11=?,extra_field_icon_12=?,extra_field_icon_13=?,extra_field_icon_14=?,extra_field_icon_15=?,extra_field_icon_16=?,extra_field_icon_17=?,extra_field_icon_18=?,extra_field_icon_19=?,extra_field_icon_20=?,extra_field_value_1=?,extra_field_value_2=?,extra_field_value_3=?,extra_field_value_4=?,extra_field_value_5=?,extra_field_value_6=?,extra_field_value_7=?,extra_field_value_8=?,extra_field_value_9=?,extra_field_value_10=?,extra_field_value_11=?,extra_field_value_12=?,extra_field_value_13=?,extra_field_value_14=?,extra_field_value_15=?,extra_field_value_16=?,extra_field_value_17=?,extra_field_value_18=?,extra_field_value_19=?,extra_field_value_20=?,extra_button_icon_1=?,extra_button_title_1=?,extra_button_value_1=?,`order`=?,min_zoom_level=?,geofence_radius=?,geofence_color=?,open_sheet=?,view_popup=?,popup_image_height=?,popup_background=?,popup_color=?,color_hex_hover=?,icon_color_hex_hover=? WHERE id=? AND id_map=?;";
                    if($smt = $mysqli->prepare($query)) {
                        $smt->bind_param('sssssssssssssssiiiiissdisssssssssssssssssssssssssssssssssssssssssssiiisiiissssii',$name,$street,$city,$postal_code,$country,$website,$website_caption,$phone,$whatsapp,$email,$hours,$description,$lat,$lon,$icon,$active,$view_directions,$view_street_view,$view_review,$view_share,$color_hex,$icon_color_hex,$marker_size,$id_icon_library,$field_e1_icon,$field_e2_icon,$field_e3_icon,$field_e4_icon,$field_e5_icon,$field_e6_icon,$field_e7_icon,$field_e8_icon,$field_e9_icon,$field_e10_icon,$field_e11_icon,$field_e12_icon,$field_e13_icon,$field_e14_icon,$field_e15_icon,$field_e16_icon,$field_e17_icon,$field_e18_icon,$field_e19_icon,$field_e20_icon,$field_e1,$field_e2,$field_e3,$field_e4,$field_e5,$field_e6,$field_e7,$field_e8,$field_e9,$field_e10,$field_e11,$field_e12,$field_e13,$field_e14,$field_e15,$field_e16,$field_e17,$field_e18,$field_e19,$field_e20,$button_b1_icon,$button_b1_title,$button_b1,$order,$min_zoom_level,$geofence_radius,$geofence_color,$open_sheet,$view_popup,$popup_image_height,$popup_background,$popup_color,$color_hex_hover,$icon_color_hex_hover,$id,$id_map);
                        $result = $smt->execute();
                        if($result) {
                            if($mysqli->affected_rows==1) {
                                $mysqli->query("DELETE FROM sml_markers_categories_assoc WHERE id_marker=$id;");
                                if(!empty($id_categories)) {
                                    $array_cat = explode(",",$id_categories);
                                    foreach ($array_cat as $id_cat) {
                                        if(in_array($id_cat,$array_exist_categories)) {
                                            $mysqli->query("INSERT INTO sml_markers_categories_assoc(id_marker,id_category) VALUES($id,$id_cat)");
                                        }
                                    }
                                }
                                $count_update++;
                                $count_i++;
                            }
                        } else {
                            echo 'ERROR: '.$mysqli->error;
                            exit;
                        }
                    } else {
                        echo 'ERROR: '.$mysqli->error;
                        exit;
                    }
                }
            }
            echo "Imported <b>".$count_i."</b> (".$count_insert." inserted, ".$count_update." updated) of ".$count. " markers.";
        } else {
            echo 'ERROR: code:'.$_FILES["file"]["error"];
        }
    } else {
        echo 'ERROR: Only csv, xls and xlsx files are supported';
    }
}else{
    echo 'ERROR: file not provided';
}
exit;
