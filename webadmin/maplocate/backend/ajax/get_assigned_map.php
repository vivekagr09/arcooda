<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$id_user_edit = $_GET['id_user_edit'];

$query = "SELECT v.id,v.name,a.* FROM sml_maps as v 
LEFT JOIN sml_assign_maps as a ON v.id=a.id_map AND a.id_user=$id_user_edit
ORDER BY v.name";
$table = "( $query ) t";
$primaryKey = 'id';

$columns = array(
    array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            return $d;
        }
    ),
    array( 'db' => 'id_map',  'dt' =>0, 'formatter' => function( $d, $row ) {
        if(!empty($d)) {
            $input = '<input class="assigned_map" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="assigned_map" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'name',  'dt' =>1, 'formatter' => function( $d, $row ) {
        return $d;
    }),
    array( 'db' => 'edit_map',  'dt' =>2, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions edit_map" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions edit_map" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'create_markers',  'dt' =>3, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions create_markers" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions create_markers" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'edit_markers',  'dt' =>4, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions edit_markers" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions edit_markers" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'delete_markers',  'dt' =>5, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions delete_markers" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions delete_markers" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'connections',  'dt' =>6, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions connections" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions connections" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'geometries',  'dt' =>7, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions geometries" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions geometries" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'story',  'dt' =>8, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions story" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions story" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'categories',  'dt' =>9, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions categories" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions categories" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'icons_library',  'dt' =>10, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions icons_library" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions icons_library" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'review',  'dt' =>11, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions review" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions review" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
    array( 'db' => 'publish',  'dt' =>12, 'formatter' => function( $d, $row ) {
        if(empty($d)) $d=0;
        if($d==1) {
            $input = '<input class="editor_permissions publish" id="'.$row['id'].'" checked type="checkbox">';
        } else {
            $input = '<input class="editor_permissions publish" id="'.$row['id'].'" type="checkbox">';
        }
        return $input;
    }),
);

$sql_details = array(
    'user' => DATABASE_USERNAME,
    'pass' => DATABASE_PASSWORD,
    'db' => DATABASE_NAME,
    'host' => DATABASE_HOST);

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);