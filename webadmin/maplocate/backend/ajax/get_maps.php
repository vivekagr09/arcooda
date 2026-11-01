<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_user = $_POST['id_user'];
$array = array();

$settings = get_settings();
$user_info = get_user_info($id_user);
if(!isset($_SESSION['lang'])) {
    if(!empty($user_info['language'])) {
        $language = $user_info['language'];
    } else {
        $language = $settings['language'];
    }
} else {
    $language = $_SESSION['lang'];
}

$where = "";
$role = get_user_role($id_user);
switch($role) {
    case 'administrator':
        $where = " WHERE 1=1 ";
        break;
    case 'customer':
        $where = " WHERE 1=1 AND m.id_user=? ";
        break;
    case 'editor':
        $where = " WHERE 1=1 AND m.id IN (0) ";
        $query = "SELECT GROUP_CONCAT(id_map) as ids FROM sml_assign_maps WHERE id_user=$id_user;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $ids = $row['ids'];
                if(!empty($ids)) {
                    $where = " WHERE 1=1 AND m.id IN ($ids) ";
                }
            }
        }
        break;
}

$query = "SELECT m.id,m.code,UPPER(m.name) as name,m.date_created,u.username,m.map_style as style,m.background_image,(SELECT COUNT(id) FROM sml_markers WHERE id_map=m.id AND to_validate=0) as count_markers 
            FROM sml_maps as m 
            LEFT JOIN sml_users as u ON u.id=m.id_user
            $where ORDER BY date_created DESC";
if($smt = $mysqli->prepare($query)) {
    if($role=='customer') {
        $smt->bind_param('i',$id_user);
    }
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while($row = array_shift($result)) {
                if($user_info['role']=='editor') {
                    $editor_permissions = get_editor_permissions($id_user,$row['id']);
                    if($editor_permissions['edit_map']==1) {
                        $row['edit_permission']=true;
                    } else {
                        $row['edit_permission']=false;
                    }
                    if($editor_permissions['publish']==1) {
                        $row['publish_permission']=true;
                    } else {
                        $row['publish_permission']=false;
                    }
                } else {
                    $row['edit_permission']=true;
                    $row['publish_permission']=true;
                }
                $row['date_created'] = formatTime("dd MMM y",$language,strtotime($row['date_created']));
                if(empty($row['background_image'])) $row['background_image']='';
                $array[]=$row;
            }
        }
    }
}
ob_end_clean();
echo json_encode($array);