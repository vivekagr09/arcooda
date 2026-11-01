<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$query = "SELECT mc.id,m1.id as id_marker_source,m2.id as id_marker_dest,m1.name as marker_source,m2.name as marker_dest,mc.color,mc.width,mc.title,mc.description FROM sml_markers_connects as mc
JOIN sml_markers as m1 ON m1.id=mc.id_marker_source
JOIN sml_markers as m2 ON m2.id=mc.id_marker_dest
WHERE id_marker_source IN (SELECT id FROM sml_markers WHERE id_map=$id_map)";
$table = "( $query ) t";
$primaryKey = 'id';

$columns = array(
    array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'id_marker_source',
        'dt' => 'DT_RowIdMarkerSource',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'id_marker_dest',
        'dt' => 'DT_RowIdMarkerDest',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'color',
        'dt' => 'DT_RowColor',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'width',
        'dt' => 'DT_RowWidth',
        'formatter' => function($d,$row) {
            return $d;
        }
    ),
    array(
        'db' => 'title',
        'dt' => 'DT_RowTitle',
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
    array( 'db' => 'marker_source',  'dt' =>0 ),
    array( 'db' => 'color',  'dt' =>1, 'formatter' => function($d,$row) {
        $w = $row['width'];
        return "<div style='width:100%;height:".$w."px;background-color:$d;vertical-align:middle'>";
    }),
    array( 'db' => 'marker_dest',  'dt' =>2 )
);

$sql_details = array(
    'user' => DATABASE_USERNAME,
    'pass' => DATABASE_PASSWORD,
    'db' => DATABASE_NAME,
    'host' => DATABASE_HOST);

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);