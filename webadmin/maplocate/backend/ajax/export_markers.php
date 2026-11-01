<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
require_once("../functions.php");
require_once("../../db/connection.php");
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$filename = "markers_$id_map.csv";
header('Content-Encoding: UTF-8');
header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename='.$filename, date( 'dmY-His' ) ) );
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
$query = "SELECT m.id, m.lat, m.lon, m.name, m.street, m.city, m.postal_code, m.country, m.website, m.website_caption, m.email, m.phone, m.whatsapp, m.hours, m.description, m.icon, m.id_icon_library, m.active, m.view_directions, m.view_street_view, m.view_review, m.view_share, m.`order`, m.color_hex, m.icon_color_hex, m.color_hex_hover, m.icon_color_hex_hover, m.marker_size, m.extra_field_value_1, m.extra_field_icon_1, m.extra_field_value_2, m.extra_field_icon_2, m.extra_field_value_3, m.extra_field_icon_3, m.extra_field_value_4, m.extra_field_icon_4, m.extra_field_value_5, m.extra_field_icon_5, m.extra_field_value_6, m.extra_field_icon_6, m.extra_field_value_7, m.extra_field_icon_7, m.extra_field_value_8, m.extra_field_icon_8, m.extra_field_value_9, m.extra_field_icon_9, m.extra_field_value_10, m.extra_field_icon_10, m.extra_field_value_11, m.extra_field_icon_11, m.extra_field_value_12, m.extra_field_icon_12, m.extra_field_value_13, m.extra_field_icon_13, m.extra_field_value_14, m.extra_field_icon_14, m.extra_field_value_15, m.extra_field_icon_15, m.extra_field_value_16, m.extra_field_icon_16, m.extra_field_value_17, m.extra_field_icon_17, m.extra_field_value_18, m.extra_field_icon_18, m.extra_field_value_19, m.extra_field_icon_19, m.extra_field_value_20, m.extra_field_icon_20, m.extra_button_value_1, m.extra_button_icon_1, m.extra_button_title_1,min_zoom_level,geofence_radius,geofence_color,GROUP_CONCAT(ca.id_category) as id_categories,m.open_sheet as view_window_detail,m.view_popup as view_popup,m.popup_image_height,m.popup_background,m.popup_color,GROUP_CONCAT(DISTINCT IF(cc.name IS NULL,c.name,CONCAT(cc.name,' - ',c.name))) as categories, m.access_count 
FROM sml_markers as m 
LEFT JOIN sml_markers_categories_assoc as ca ON ca.id_marker=m.id
LEFT JOIN sml_categories as c ON c.id=ca.id_category
LEFT JOIN sml_categories as cc ON c.id_category_parent=cc.id
WHERE m.id_map=?
GROUP BY m.id;";
$flag = false;
$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF" );
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_map);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while($row = array_shift($result)) {
                if(!empty($row['lat']) && is_numeric($row['lat'])) $row['lat']=number_format($row['lat'],6,',','');
                if(!empty($row['lon']) && is_numeric($row['lon'])) $row['lon']=number_format($row['lon'],6,',','');
                if (!$flag) {
                    fputcsv($output, array_keys($row),";",'"');
                    $flag = true;
                }
                fputcsv($output, array_values($row),";",'"');
            }
        }
    }
}
exit;