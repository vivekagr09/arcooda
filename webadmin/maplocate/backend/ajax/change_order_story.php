<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../../config/config.inc.php");

$id_map = $_POST['id_map'];
$id_marker = $_POST['id_marker'];
$direction = $_POST['direction'];

$index_marker = 0;
$index_marker_sel = 0;
$array_order = array();
$query = "SELECT id_marker FROM sml_story WHERE id_map=$id_map ORDER BY priority ASC;";
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows>0) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id = $row['id_marker'];
            $order = $row['order'];
            if($id_marker==$id) $index_marker_sel = $index_marker;
            array_push($array_order,$id);
            $index_marker++;
        }
    }
}

switch ($direction) {
    case 'up':
        $next_index = $index_marker_sel+1;
        if($next_index>=count($array_order)) {
            $next_index = 0;
        }
        array_move($array_order,$index_marker_sel,$next_index);
        break;
    case 'down':
        $prev_index = $index_marker_sel-1;
        if($prev_index<0) {
            $prev_index = count($array_order)-1;
        }
        array_move($array_order,$index_marker_sel,$prev_index);
        break;
}

foreach ($array_order as $order=>$id_marker) {
    $mysqli->query("UPDATE sml_story SET priority=$order WHERE id_marker=$id_marker;");
}

function array_move(&$a, $oldpos, $newpos) {
    if ($oldpos==$newpos) {return;}
    array_splice($a,max($newpos,0),0,array_splice($a,max($oldpos,0),1));
}