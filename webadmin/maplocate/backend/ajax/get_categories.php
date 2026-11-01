<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require(__DIR__.'/ssp.class.php');
require(__DIR__.'/../../config/config.inc.php');

$id_map = empty($_GET['id_map'])? 0 :$_GET['id_map'];
$query = "SELECT c.id,c.default_selected,IF(cc.name IS NULL,c.name,CONCAT(cc.name,' - ',c.name)) as category
                FROM sml_categories as c
                LEFT JOIN sml_categories as cc ON c.id_category_parent=cc.id
                WHERE c.id_map=$id_map 
                ORDER BY category";
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
    array( 'db' => 'category',  'dt' =>0 ),
    array( 'db' => 'default_selected',  'dt' =>1, 'formatter' => function($d,$roe) {
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