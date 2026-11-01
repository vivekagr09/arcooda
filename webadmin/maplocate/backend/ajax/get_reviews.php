<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$query = "SELECT m.name as marker,r.* FROM sml_reviews as r
JOIN sml_markers as m ON r.id_marker=m.id
WHERE m.id_map=$id_map
ORDER BY r.create_date DESC";
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
    array( 'db' => 'create_date',  'dt' =>0, 'formatter' => function($d,$row) {
        return '<span style="display:none">'.strtotime($d).'</span>'.date('Y-m-d',strtotime($d));
    }),
    array( 'db' => 'marker',  'dt' =>1 ),
    array( 'db' => 'name',  'dt' =>2 ),
    array( 'db' => 'email',  'dt' =>3 ),
    array( 'db' => 'rating',  'dt' =>4 ),
    array( 'db' => 'comment',  'dt' =>5 ),
    array( 'db' => 'id',  'dt' =>6, 'formatter' => function($d,$row) {
        return "<button onclick='delete_review($d);' style='line-height:1;' class='btn btn-sm btn-danger'>delete</button>";
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