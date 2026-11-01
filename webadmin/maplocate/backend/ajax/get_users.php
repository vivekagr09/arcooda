<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$query = "SELECT u.* FROM sml_users as u";
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
    array( 'db' => 'username',  'dt' =>0 ),
    array( 'db' => 'role',  'dt' =>1, 'formatter' => function( $d, $row ) {
        return ucfirst($d);
    }),
    array( 'db' => 'active',  'dt' =>2, 'formatter' => function( $d, $row ) {
        if($d) {
            return "<i class='fa fa-check'></i>";
        } else {
            return "<i class='fa fa-times'></i>";
        }
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