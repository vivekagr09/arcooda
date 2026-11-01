<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_marker = (int)$_POST['id_marker'];
$duplicate_target_map = (int)$_POST['duplicate_target_map'];
if($duplicate_target_map!=0) {
    $name_map_sel = get_map($duplicate_target_map,$_SESSION['id_user'])['name'];
    $_SESSION['id_map_sel'] = $duplicate_target_map;
    $_SESSION['name_map_sel'] = $name_map_sel;
}
session_write_close();
$id_map = $_POST['id_map'];
$id_user = $_SESSION['id_user'];
$settings = get_settings();
$user_info = get_user_info($id_user);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    set_language($settings['language'],$settings['language_domain']);
}
if(get_user_role($id_user)=='administrator') {
    $query = "SELECT * FROM sml_maps WHERE id=$id_map; ";
} else {
    $query = "SELECT * FROM sml_maps WHERE id_user=$id_user AND id=$id_map; ";
}
$result = $mysqli->query($query);
if($result) {
    if($result->num_rows==0) {
        ob_end_clean();
        echo json_encode(array("status"=>"unauthorized"));
        exit;
    }
}
if($duplicate_target_map!=0) {
    $query_order = "SELECT (MAX(`order`)+1) as `order` FROM sml_markers WHERE id_map=$duplicate_target_map;";
} else {
    $query_order = "SELECT (MAX(`order`)+1) as `order` FROM sml_markers WHERE id_map=$id_map;";
}
$result_order = $mysqli->query($query_order);
if($result_order) {
    $row_order = $result_order->fetch_array(MYSQLI_ASSOC);
    $order = $row_order['order'];
}
if(empty($order)) $order=0;
$mysqli->query("CREATE TEMPORARY TABLE sml_markers_tmp SELECT * FROM sml_markers WHERE id = $id_marker;");
if($duplicate_target_map!=0) {
    $mysqli->query("UPDATE sml_markers_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_markers),id_map=$duplicate_target_map,access_count=0,`order`=$order;");
} else {
    $duplicated_label = _("duplicated");
    $mysqli->query("UPDATE sml_markers_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_markers),name=CONCAT(name,' ($duplicated_label)'),access_count=0,`order`=$order;");
}
$result = $mysqli->query("INSERT INTO sml_markers SELECT * FROM sml_markers_tmp;");
$id_marker_new = $mysqli->insert_id;
$error = $mysqli->error;
$mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_markers_tmp;");
$result = $mysqli->query("SELECT id FROM sml_images WHERE id_marker=$id_marker;");
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_image = $row['id'];
            $mysqli->query("CREATE TEMPORARY TABLE sml_images_tmp SELECT * FROM sml_images WHERE id = $id_image;");
            $mysqli->query("UPDATE sml_images_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_images),id_marker=$id_marker_new;");
            $mysqli->query("INSERT INTO sml_images SELECT * FROM sml_images_tmp;");
            $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_images_tmp;");
        }
    }
}
if($duplicate_target_map==0) {
    $result = $mysqli->query("SELECT id_marker,id_category FROM sml_markers_categories_assoc WHERE id_marker=$id_marker;");
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $id_category = $row['id_category'];
                $mysqli->query("CREATE TEMPORARY TABLE sml_markers_categories_assoc_tmp SELECT * FROM sml_markers_categories_assoc WHERE id_marker = $id_marker AND id_category = $id_category;");
                $mysqli->query("UPDATE sml_markers_categories_assoc_tmp SET id_marker=$id_marker_new;");
                $mysqli->query("INSERT INTO sml_markers_categories_assoc SELECT * FROM sml_markers_categories_assoc_tmp;");
                $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_markers_categories_assoc_tmp;");
            }
        }
    }
}
ob_end_clean();
if(!$result) {
    echo json_encode(array("status"=>"error","msg"=>$error));
} else {
    echo json_encode(array("status"=>"ok"));
}