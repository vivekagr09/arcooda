<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$query = "SELECT s.id_marker,s.zoom,s.description,s.priority,m.name,m.icon,m.color_hex,m.icon_color_hex,i.image as icon_image FROM sml_story as s JOIN sml_markers as m ON m.id=s.id_marker LEFT JOIN sml_icons as i ON i.id=m.id_icon_library WHERE s.id_map=$id_map";
$table = "( $query ) t";
$primaryKey = 'id_marker';

$columns = array(
    array(
        'db' => 'id_marker',
        'dt' => 'DT_RowId',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'zoom',
        'dt' => 'DT_RowZoom',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'description',
        'dt' => 'DT_RowDescription',
        'formatter' => function($d,$row) {
            $d = htmlspecialchars_decode($d);
            $d = str_replace("\'","'",$d);
            return $d;
        }
    ),
    array( 'db' => 'name',  'dt' =>0, 'formatter' => function($d,$row) {
        $icon = $row['icon'];
        $color_hex = $row['color_hex'];
        $icon_color_hex = $row['icon_color_hex'];
        $icon_image = $row['icon_image'];
        if(empty($icon_image)) {
            $icon = explode("|",$icon)[0];
            $icon_h = "<div style='display:inline-block;vertical-align:middle;background-color:$color_hex;border-radius:50%;width:20px;height:20px;line-height:16px;text-align:center'><i style='font-size:8px;color:$icon_color_hex;' class='$icon'></i></div>";
        } else {
            $icon_h = "<img style='height:15px;vertical-align:middle' src='../viewer/icons/$icon_image' />";
        }
        return $icon_h."&nbsp;&nbsp;".$d;
    }),
    array( 'db' => 'zoom',  'dt' =>1 ),
    array( 'db' => 'priority',  'dt' =>2, 'formatter' => function($d,$row) {
        $html = "<i onclick='change_order_story(".$row['id_marker'].",\"up\");' class='order_icon fa fa-caret-up'></i>&nbsp;&nbsp;$d&nbsp;&nbsp;<i onclick='change_order_story(".$row['id_marker'].",\"down\");' class='order_icon fa fa-caret-down'></i>";
        return "$html";
    })
);

$sql_details = array(
    'user' => DATABASE_USERNAME,
    'pass' => DATABASE_PASSWORD,
    'db' => DATABASE_NAME,
    'host' => DATABASE_HOST);

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);