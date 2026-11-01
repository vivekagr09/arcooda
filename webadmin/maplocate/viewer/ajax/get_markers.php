<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
ini_set("memory_limit",-1);
ini_set('max_execution_time', 9999);
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$settings = get_settings();
if(!empty($settings['timezone'])) {
    date_default_timezone_set($settings['timezone']);
}
$id_map = (int)$_POST['id_map'];
$order_by = $_POST['order_by'];
$array_markers = array();
$array_base64 = array();
switch($order_by) {
    case 'priority':
        $order_by = "ORDER BY m.order ASC, m.id DESC";
        break;
    case 'name':
        $order_by = "ORDER BY m.name ASC, m.id DESC";
        break;
    case 'city':
        $order_by = "ORDER BY m.city ASC, m.id DESC";
        break;
}
$markers_size = 0;
$query = "SELECT markers_size FROM sml_maps WHERE id=$id_map LIMIT 1;";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows == 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $markers_size = $row['markers_size'];
    }
}
$markers_images = array();
$query = "SELECT * FROM sml_images WHERE id_marker IN (SELECT id FROM sml_markers WHERE id_map=$id_map);";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_m = $row['id_marker'];
            if(!array_key_exists($id_m,$markers_images)) {
                $markers_images[$id_m] = array();
                $markers_images[$id_m]['main_image'] = "";
                $markers_images[$id_m]['images'] = array();
            }
            if($row['main']==1) {
                $markers_images[$id_m]['main_image'] = $row['image'];
            }
            $row['image'] = 'marker_images/'.$row['image'];
            $markers_images[$id_m]['images'][] = $row['image'];
        }
    }
}
$icons = array();
$query = "SELECT id,image FROM sml_icons;";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $icons[$row['id']]=$row['image'];
        }
    }
}
$categories_assoc = array();
$query = "SELECT DISTINCT id_marker,id_category FROM sml_markers_categories_assoc WHERE id_marker IN (SELECT id FROM sml_markers WHERE id_map=$id_map);";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $categories_assoc[$row['id_marker']].=$row['id_category'].",";
        }
    }
}
$ratings = array();
$query = "SELECT id_marker,AVG(rating) as rating FROM sml_reviews WHERE id_marker IN (SELECT id FROM sml_markers WHERE id_map=$id_map) GROUP BY id_marker;";
$result = $mysqli->query($query);
if($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $ratings[$row['id_marker']]=$row['rating'];
        }
    }
}
$loop_results = true;
$date_now = date('Ymd');
$hour_now = date('Hi');
$day_now = date('N');
$j=0;
do {
    $offset=$j*10000;
    $limit=10000;
    $j++;
    $query = "SELECT m.* FROM sml_markers as m 
                WHERE m.id_map=? AND m.to_validate=0
                $order_by LIMIT ?,?";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('iii',$id_map,$offset,$limit);
        $result = $smt->execute();
        if($result) {
            $result = get_result($smt);
            if(count($result)>0) {
                while($row = array_shift($result)) {
                    $schedule = $row['schedule'];
                    if(!empty($schedule)) {
                        $schedule = json_decode($row['schedule'],true);
                        if(!empty($schedule['from_date']) && !empty($schedule['to_date'])) {
                            $date_from = date('Ymd',strtotime($schedule['from_date']));
                            $date_to = date('Ymd',strtotime($schedule['to_date']));
                            if(($date_now<$date_from) || ($date_now>$date_to)) {
                                continue;
                            }
                        }
                        if(!empty($schedule['days'])) {
                            $days = explode(",",$schedule['days']);
                            $alldays_zero = 1;
                            for($i=0;$i<count($days);$i++) {
                                if($days[$i]==1) {
                                    $alldays_zero = 0;
                                }
                            }
                            if($days[$day_now-1]==0 && $alldays_zero==0) {
                                continue;
                            }
                        }
                        if(!empty($schedule['from_hour']) && !empty($schedule['to_hour'])) {
                            $hour_from = date('Hi',strtotime($schedule['from_hour']));
                            $hour_to = date('Hi',strtotime($schedule['to_hour']));
                            if(($hour_now<$hour_from) || ($hour_now>=$hour_to)) {
                                continue;
                            }
                        }
                    }
                    $row['lat'] = str_replace(",",".",$row['lat']);
                    $row['lon'] = str_replace(",",".",$row['lon']);
                    if(!is_numeric($row['lat']) || !is_numeric($row['lon'])) {
                        continue;
                    }
                    $row['address'] = "";
                    if(!empty($row['street'])) {
                        $row['address'] .= $row['street'].", ";
                    }
                    if(!empty($row['city'])) {
                        $row['address'] .= $row['city'].", ";
                    }
                    if(!empty($row['postal_code'])) {
                        $row['address'] .= $row['postal_code'].", ";
                    }
                    if(!empty($row['country'])) {
                        $row['address'] .= $row['country'].", ";
                    }
                    $row['address'] = rtrim($row['address'],", ");
                    if(empty($row['website_caption'])) $row['website_caption']='';
                    if(empty($row['whatsapp'])) $row['whatsapp']='';
                    if(empty($row['hours'])) $row['hours']='';
                    if(empty($row['description'])) $row['description']='';
                    if(empty($row['website_caption'])) $row['website_caption']='';
                    if(empty($row['website'])) $row['website']='';
                    if(empty($row['email'])) $row['email']='';
                    if(empty($row['phone'])) $row['phone']='';
                    $row['main_image'] = "";
                    $row['images'] = array();
                    if ($row['hours'] == "<p><br></p>") {
                        $row['hours'] = "";
                    }
                    if ($row['description'] == "<p><br></p>") {
                        $row['description'] = "";
                    }
                    try {
                        $row['icon'] = explode("|",$row['icon'])[1];
                    } catch (Exception $e) {
                        $row['icon'] = '';
                    }
                    if(empty($row['color_hex'])) $row['color_hex']='#ffffff';
                    if(empty($row['name'])) $row['name']='';
                    if(empty($row['description'])) $row['description']='';
                    if(empty($row['icon_image'])) $row['icon_image']='';
                    for($i=1;$i<=20;$i++) {
                        if(empty($row['extra_field_value_'.$i])) {
                            unset($row['extra_field_value_'.$i]);
                            unset($row['extra_field_icon_'.$i]);
                        } else {
                            $row['extra_field_value_'.$i] = htmlspecialchars_decode($row['extra_field_value_'.$i]);
                            $row['extra_field_value_'.$i] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_field_value_'.$i]);
                            if($row['extra_field_icon_'.$i]=='fa-circle') {
                                unset($row['extra_field_icon_'.$i]);
                            }
                        }
                    }
                    if(empty($row['extra_button_value_1'])) {
                        unset($row['extra_button_value_1']);
                        unset($row['extra_button_icon_1']);
                    } else {
                        $row['extra_button_value_1'] = htmlspecialchars_decode($row['extra_button_value_1']);
                        $row['extra_button_value_1'] = str_replace(["\r\n","\r","\n"], "<br>", $row['extra_button_value_1']);
                        if($row['extra_button_icon_1']=='fa-circle') {
                            unset($row['extra_button_icon_1']);
                        }
                    }
                    if(array_key_exists($row['id'],$markers_images)) {
                        $row['main_image'] = $markers_images[$row['id']]['main_image'];
                        $row['images'] = $markers_images[$row['id']]['images'];
                    }
                    if(empty($row['rating'])) $row['rating']=0;
                    $row['darker_color_hex'] = adjustBrightness($row['color_hex'],-0.3);
                    if(!empty($row['color_hex_hover'])) {
                        $row['darker_color_hex_hover'] = adjustBrightness($row['color_hex_hover'],-0.3);
                    }
                    $row['default_marker_size']=$markers_size;
                    if($row['marker_size']==0) $row['marker_size']=$row['default_marker_size'];
                    $row['icon_base64']='';
                    if(!empty($row['icon_image'])) {
                        $ext = substr(strrchr($row['icon_image'], '.'), 1);
                        if(($ext=='gif') || ($ext=='svg')) {
                            $row['icon_base64']='';
                        } else {
                            $row['icon_base64']=resize_image($row['icon_image'],$row['marker_size']);
                        }
                    }
                    $row['icon_image']="";
                    $row['rating']=0;
                    $row['id_categories'] = '';
                    $id_icon_library = $row['id_icon_library'];
                    if(array_key_exists($id_icon_library,$icons)) {
                        $row['icon_image']=$icons[$id_icon_library];
                    }
                    if(array_key_exists($row['id'],$ratings)) {
                        $row['rating']=$ratings[$row['id']];
                    }
                    if(array_key_exists($row['id'],$categories_assoc)) {
                        $row['id_categories']=rtrim($categories_assoc[$row['id']],",");
                    }
                    $array_markers[] = $row;
                }
            } else {
                $loop_results = false;
            }
        }
    }
} while($loop_results);
$array_categories = array();
$query = "SELECT c.*,(SELECT COUNT(DISTINCT ca.id_marker) FROM sml_markers_categories_assoc as ca LEFT JOIN sml_categories as c1 ON c1.id=ca.id_category LEFT JOIN sml_categories as c2 ON c2.id=ca.id_category JOIN sml_markers as m ON m.id=ca.id_marker WHERE m.id_map=? AND m.to_validate=0 AND m.active=1 AND (c1.id=c.id OR c2.id_category_parent=c.id)) as count_m FROM sml_categories as c WHERE c.id_map=? AND (SELECT COUNT(DISTINCT ca.id_marker) FROM sml_markers_categories_assoc as ca LEFT JOIN sml_categories as c1 ON c1.id=ca.id_category LEFT JOIN sml_categories as c2 ON c2.id=ca.id_category WHERE c1.id=c.id OR c2.id_category_parent=c.id)>0 ORDER BY c.name ASC;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ii', $id_map,$id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) > 0) {
            while ($row = array_shift($result)) {
                if(empty($row['id_category_parent'])) $row['id_category_parent']=0;
                $array_categories[] = $row;
            }
        }
    }
}

$array_markers_conn = array();
$query = "SELECT mc.color,mc.width,m1.lat as lat_source,m1.lon as lon_source,m2.lat as lat_dest,m2.lon as lon_dest,m1.id as id_source,m2.id as id_dest,mc.title,mc.description FROM sml_markers_connects as mc
JOIN sml_markers as m1 ON m1.id=mc.id_marker_source
JOIN sml_markers as m2 ON m2.id=mc.id_marker_dest
WHERE id_marker_source IN (SELECT id FROM sml_markers WHERE id_map=?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) > 0) {
            while ($row = array_shift($result)) {
                if(empty($row['title'])) $row['title']='';
                if(empty($row['description'])) $row['description']='';
                $row['description'] = htmlspecialchars_decode($row['description']);
                $row['description'] = str_replace("\'","'",$row['description']);
                $row['description'] = str_replace(["\r\n","\r","\n"], "<br>", $row['description']);
                $array_markers_conn[] = $row;
            }
        }
    }
}
$row_pin = array();
$row_pin['pin_marker_enable']=false;
$query = "SELECT m.pin_marker_enable,m.pin_marker_size,m.pin_marker_icon,m.pin_marker_color_hex,m.pin_marker_icon_color_hex,m.pin_marker_align,i.image as pin_marker_icon_image FROM sml_maps as m
            LEFT JOIN sml_icons as i ON i.id=m.pin_marker_id_icon_library
            WHERE m.id=?";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) == 1) {
            $row_pin = array_shift($result);
            try {
                $row_pin['pin_marker_icon'] = explode("|",$row_pin['pin_marker_icon'])[1];
            } catch (Exception $e) {
                $row_pin['pin_marker_icon'] = '';
            }
            if(empty($row_pin['pin_marker_icon_image'])) $row_pin['pin_marker_icon_image']='';
            $row_pin['pin_marker_icon_base64']='';
            if(!empty($row_pin['pin_marker_icon_image'])) {
                $ext = substr(strrchr($row_pin['pin_marker_icon_image'], '.'), 1);
                if(($ext=='gif') || ($ext=='svg')) {
                    $row_pin['pin_marker_icon_base64']='';
                } else {
                    $row_pin['pin_marker_icon_base64']=resize_image($row_pin['pin_marker_icon_image'],$row_pin['pin_marker_size']);
                }
            }
        }
    }
}
ob_end_clean();
echo json_encode(array("markers"=>$array_markers,"markers_connections"=>$array_markers_conn,"categories"=>$array_categories,"pin_marker"=>$row_pin));

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

$k=0;
function resize_image($img_name,$scale) {
    global $k, $array_base64;
    if(array_key_exists($img_name,$array_base64)) {
        return $array_base64[$img_name];
    }
    $base64 = '';
    list($width, $height, $type, $attr) = getimagesize("../icons/$img_name");
    $new_w = ($width/10)*$scale;
    $new_h = ($height/10)*$scale;
    $ext = substr(strrchr($img_name, '.'), 1);
    if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext)){
        $src_img=imagecreatefromjpeg("../icons/$img_name");
    }
    if(!strcmp("png",$ext)){
        $src_img=imagecreatefrompng("../icons/$img_name");
    }
    $old_x=imageSX($src_img);
    $old_y=imageSY($src_img);
    if($new_h <= 0){
        $wRatio = $new_w / $old_x ;
        $thumb_h = ceil($wRatio * $old_y);
        $thumb_w = $new_w;
    }else{
        $thumb_w = $new_w;
    }
    if($new_w <= 0){
        $hRatio = $new_h / $old_y ;
        $thumb_w = ceil($hRatio * $old_x);
        $thumb_h = $new_h;
    }else{
        $thumb_h = $new_h;
    }
    $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
    if(!strcmp("png",$ext)) {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
    }
    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
   $dest_file = '../icons/tmp_'.time().$k.'.'.$ext;
    if(!strcmp("gif",$ext)){
        imagegif($dst_img,$dest_file);
    } else if(!strcmp("png",$ext)){
        imagepng($dst_img,$dest_file);
    } else{
        imagejpeg($dst_img,$dest_file);
    }
    $type = pathinfo($dest_file, PATHINFO_EXTENSION);
    $data = file_get_contents($dest_file);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    unlink($dest_file);
    imagedestroy($dst_img);
    imagedestroy($src_img);
    $k++;
    if(!array_key_exists($img_name,$array_base64)) {
        $array_base64[$img_name] = $base64;
    }
    return $base64;
}