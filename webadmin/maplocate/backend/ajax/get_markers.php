<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');
require(__DIR__.'/../functions.php');

$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$count_markers = count_markers($id_map);
if($count_markers<=1000) {
    $query = "SELECT m.id,m.lat,m.lon,m.name,m.street,m.city,m.postal_code,m.country,m.active,m.to_validate,m.centered,m.featured,m.`order`,m.icon,m.color_hex,m.icon_color_hex,GROUP_CONCAT(DISTINCT IF(cc.name IS NULL,c.name,CONCAT(cc.name,' - ',c.name))) as category,i.image as icon_image,CONCAT(m.city,' ',m.street,' ',m.postal_code,' ',m.country) as full_address,m.schedule FROM sml_markers as m 
LEFT JOIN sml_markers_categories_assoc as ca ON ca.id_marker=m.id
LEFT JOIN sml_categories as c ON ca.id_category=c.id
LEFT JOIN sml_categories as cc ON c.id_category_parent=cc.id
LEFT JOIN sml_icons as i ON i.id=m.id_icon_library
WHERE m.id_map=$id_map 
GROUP BY m.id,m.order 
ORDER BY m.id,m.order DESC";
} else {
    $query = "SELECT m.id,m.lat,m.lon,m.name,m.street,m.city,m.postal_code,m.country,m.active,m.to_validate,m.centered,m.featured,m.`order`,CONCAT(m.city,' ',m.street,' ',m.postal_code,' ',m.country) as full_address FROM sml_markers as m 
WHERE m.id_map=$id_map 
GROUP BY m.id,m.order 
ORDER BY m.id,m.order DESC";
}

$table = "( $query ) t";
$primaryKey = 'id';

if($count_markers<=1000) {
    $columns = array(
        array(
            'db' => 'id',
            'dt' => 'DT_RowId',
            'formatter' => function($d,$row) {
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
        array( 'db' => 'category',  'dt' =>1 ),
        array( 'db' => 'full_address',  'dt' =>2, 'formatter' => function($d,$row) {
            if(!is_numeric($row['lat']) || !is_numeric($row['lon'])) {
                $icon = '<i style="color:darkred"; class="fas fa-map-marker-alt"></i>';
            } else {
                $icon = '<i style="color:darkgreen"; class="fas fa-map-marker-alt"></i>';
            }
            $address = "";
            if(!empty($row['street'])) {
                $address .= $row['street'].", ";
            }
            if(!empty($row['city'])) {
                $address .= $row['city'].", ";
            }
            if(!empty($row['postal_code'])) {
                $address .= $row['postal_code'].", ";
            }
            if(!empty($row['country'])) {
                $address .= $row['country'].", ";
            }
            $address = rtrim($address,", ");
            return $icon." ".$address;
        }),
        array( 'db' => 'active',  'dt' =>3, 'formatter' => function($d,$row) {
            $schedule = $row['schedule'];
            $schedule_icon = "";
            if(!empty($schedule)) {
                $schedule_icon = "&nbsp;&nbsp;<i class='fa fa-clock'></i>";
            }
            if($d) {
                return "<i class='fa fa-check'></i> $schedule_icon";
            } else {
                return "<i class='fa fa-times'></i> $schedule_icon";
            }
        }),
        array( 'db' => 'to_validate',  'dt' =>4, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-times'></i>";
            } else {
                return "<i class='fa fa-check'></i>";
            }
        }),
        array( 'db' => 'centered',  'dt' =>5, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-check'></i>";
            } else {
                return "<i class='fa fa-times'></i>";
            }
        }),
        array( 'db' => 'featured',  'dt' =>6, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-check'></i>";
            } else {
                return "<i class='fa fa-times'></i>";
            }
        }),
        array( 'db' => 'order',  'dt' =>7, 'formatter' => function($d,$row) {
            $html = "<i onclick='change_order(".$row['id'].",\"up\");' class='order_icon fa fa-caret-up'></i>&nbsp;&nbsp;$d&nbsp;&nbsp;<i onclick='change_order(".$row['id'].",\"down\");' class='order_icon fa fa-caret-down'></i>";
            return "$html";
        }),
        array( 'db' => 'id',  'dt' =>8, 'formatter' => function($d,$row) {
            return "<button onclick='modal_clone_marker($d);' style='line-height:1;' class='btn btn-sm btn-secondary btn_delete_marker'><i class=\"fas fa-clone\"></i></button> <button onclick='modal_delete_marker($d);' style='line-height:1;' class='btn btn-sm btn-danger btn_duplicate_marker'><i class=\"fas fa-trash\"></i></button><input style='display:none;' id='marker_$d' class='check_bulk_marker' type='checkbox' />";
        })
    );
} else {
    $columns = array(
        array(
            'db' => 'id',
            'dt' => 'DT_RowId',
            'formatter' => function($d,$row) {
                return $d;
            }
        ),
        array( 'db' => 'name',  'dt' =>0),
        array( 'db' => 'full_address',  'dt' =>1, 'formatter' => function($d,$row) {
            if(!is_numeric($row['lat']) || !is_numeric($row['lon'])) {
                $icon = '<i style="color:darkred"; class="fas fa-map-marker-alt"></i>';
            } else {
                $icon = '<i style="color:darkgreen"; class="fas fa-map-marker-alt"></i>';
            }
            $address = "";
            if(!empty($row['street'])) {
                $address .= $row['street'].", ";
            }
            if(!empty($row['city'])) {
                $address .= $row['city'].", ";
            }
            if(!empty($row['postal_code'])) {
                $address .= $row['postal_code'].", ";
            }
            if(!empty($row['country'])) {
                $address .= $row['country'].", ";
            }
            $address = rtrim($address,", ");
            return $icon." ".$address;
        }),
        array( 'db' => 'active',  'dt' =>2, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-check'></i>";
            } else {
                return "<i class='fa fa-times'></i>";
            }
        }),
        array( 'db' => 'to_validate',  'dt' =>3, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-times'></i>";
            } else {
                return "<i class='fa fa-check'></i>";
            }
        }),
        array( 'db' => 'centered',  'dt' =>4, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-check'></i>";
            } else {
                return "<i class='fa fa-times'></i>";
            }
        }),
        array( 'db' => 'featured',  'dt' =>5, 'formatter' => function($d,$row) {
            if($d) {
                return "<i class='fa fa-check'></i>";
            } else {
                return "<i class='fa fa-times'></i>";
            }
        }),
        array( 'db' => 'order',  'dt' =>6, 'formatter' => function($d,$row) {
            $html = "<i onclick='change_order(".$row['id'].",\"up\");' class='order_icon fa fa-caret-up'></i>&nbsp;&nbsp;$d&nbsp;&nbsp;<i onclick='change_order(".$row['id'].",\"down\");' class='order_icon fa fa-caret-down'></i>";
            return "$html";
        }),
        array( 'db' => 'id',  'dt' =>7, 'formatter' => function($d,$row) {
            return "<button onclick='modal_clone_marker($d);' style='line-height:1;' class='btn btn-sm btn-secondary btn_delete_marker'><i class=\"fas fa-clone\"></i></button> <button onclick='modal_delete_marker($d);' style='line-height:1;' class='btn btn-sm btn-danger btn_duplicate_marker'><i class=\"fas fa-trash\"></i></button><input style='display:none;' id='marker_$d' class='check_bulk_marker' type='checkbox' />";
        })
    );
}

$sql_details = array(
    'user' => DATABASE_USERNAME,
    'pass' => DATABASE_PASSWORD,
    'db' => DATABASE_NAME,
    'host' => DATABASE_HOST);

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);