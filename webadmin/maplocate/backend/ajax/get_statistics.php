<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../functions.php");
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$elem = $_POST['elem'];
$id_user = $_SESSION['id_user'];
$unique = false;
if(isset($_SESSION['statistics_type'])) {
    if($_SESSION['statistics_type']=="unique") {
        $unique = true;
    }
}
$settings = get_settings();
$user_info = get_user_info($_SESSION['id_user']);
if(!isset($_SESSION['lang'])) {
    if(!empty($user_info['language'])) {
        $language = $user_info['language'];
    } else {
        $language = $settings['language'];
    }
} else {
    $language = $_SESSION['lang'];
}
set_language($language,$settings['language_domain']);

$where = '';
switch($user_info['role']) {
    case 'administrator':
        $where = " WHERE 1=1 ";
        break;
    case 'customer':
        $where = " WHERE 1=1 AND m.id_user=$id_user ";
        break;
    case 'editor':
        $where = " WHERE 1=1 AND m.id IN () ";
        $query = "SELECT GROUP_CONCAT(id_map) as ids FROM sml_assign_maps WHERE id_user=$id_user;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $ids = $row['ids'];
                $where = " WHERE 1=1 AND m.id IN ($ids) ";
            }
        }
        break;
}
if($id_map!='all') {
    $where .= " AND m.id=$id_map ";
}

$stats = array();

switch ($elem) {
    case 'chart_visitor_map':
        $stats['data'] = array();
        if($id_map=='all') {
            $query = "SELECT COUNT(*) as num,DAY(date_time) as d,MONTH(date_time) as m,YEAR(date_time) as y FROM sml_access_log 
                JOIN sml_maps as m on sml_access_log.id_map = m.id
                $where
                GROUP BY DAY(date_time),MONTH(date_time),YEAR(date_time);";
        } else {
            $query = "SELECT COUNT(*) as num,DAY(date_time) as d,MONTH(date_time) as m,YEAR(date_time) as y FROM sml_access_log 
                WHERE id_map=$id_map
                GROUP BY DAY(date_time),MONTH(date_time),YEAR(date_time);";
        }
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows>0) {
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $date_time = strtotime($row['y']."-".$row['m']."-".$row['d'])*1000;
                    $num = intval($row['num']);
                    $tmp = array();
                    $tmp[0]=$date_time;
                    $tmp[1]=$num;
                    $stats['data'][] = $tmp;
                }
            }
        }
        usort($stats['data'], 'sortByOrder');
        break;
    case 'chart_marker_views':
        $stats['markers'] = array();
        $stats['total_markers'] = 0;
        if($id_map=='all') {
            $query = "SELECT mk.name,mk.access_count,m.name as map_name FROM sml_markers as mk
                    JOIN sml_maps as m ON m.id=mk.id_map
                    $where AND mk.access_count>0 AND mk.to_validate=0
                    GROUP BY mk.id
                    ORDER BY mk.access_count DESC;";
        } else {
            $query = "SELECT name,access_count,'' as map_name FROM sml_markers
                    WHERE id_map=$id_map AND access_count>0 AND to_validate=0
                    GROUP BY id
                    ORDER BY access_count DESC;";
        }
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows>0) {
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $stats['total_markers'] = $stats['total_markers'] + $row['access_count'];
                    array_push($stats['markers'],$row);
                }
            }
        }
        break;
}
ob_end_clean();
echo json_encode($stats);

function sortByOrder($a, $b) {
    return $a[0] - $b[0];
}