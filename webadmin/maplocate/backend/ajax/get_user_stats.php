<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../functions.php");
require_once("../../db/connection.php");
$id_user = $_POST['id_user'];

$stats = array();
$stats['count_maps'] = 0;
$stats['count_markers'] = 0;

$query = "SELECT COUNT(*) as num FROM sml_maps WHERE id_user = ? LIMIT 1;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_user);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) == 1) {
            $row=array_shift($result);
            $num = $row['num'];
            $stats['count_maps'] = $num;
        }
    }
}

$query = "SELECT COUNT(*) as num FROM sml_markers as k
JOIN sml_maps as m ON m.id = k.id_map
WHERE m.id_user = ? LIMIT 1;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i', $id_user);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) == 1) {
            $row=array_shift($result);
            $num = $row['num'];
            $stats['count_markers'] = $num;
        }
    }
}

echo json_encode($stats);