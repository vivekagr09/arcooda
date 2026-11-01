<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_user = $_POST['id_user'];
$id_map = $_POST['id_map'];

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

$duplicated_label = _("duplicated");

$mysqli->query("CREATE TEMPORARY TABLE sml_maps_tmp SELECT * FROM sml_maps WHERE id = $id_map;");
$mysqli->query("UPDATE sml_maps_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_maps),name=CONCAT(name,' ($duplicated_label)'),date_created=NOW(),ga_tracking_id=NULL,friendly_url=NULL;");
$mysqli->query("INSERT INTO sml_maps SELECT * FROM sml_maps_tmp;");
$id_map_new = $mysqli->insert_id;
$code_new = uniqid();
$mysqli->query("UPDATE sml_maps SET code='$code_new' WHERE id=$id_map_new;");
$mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_maps_tmp;");

$mysqli->query("CREATE TEMPORARY TABLE sml_icons_tmp SELECT * FROM sml_icons WHERE id_map = $id_map;");
$mysqli->query("UPDATE sml_icons_tmp SET id=NULL,id_map=$id_map_new;");
$mysqli->query("INSERT INTO sml_icons SELECT * FROM sml_icons_tmp;");
$mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_icons_tmp;");

$array_categories = array();
$result = $mysqli->query("SELECT id FROM sml_categories WHERE id_map=$id_map;");
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_category = $row['id'];
            $mysqli->query("CREATE TEMPORARY TABLE sml_categories_tmp SELECT * FROM sml_categories WHERE id = $id_category;");
            $mysqli->query("UPDATE sml_categories_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_categories),id_map=$id_map_new;");
            $mysqli->query("INSERT INTO sml_categories SELECT * FROM sml_categories_tmp;");
            $id_category_new = $mysqli->insert_id;
            $array_categories[$id_category] = $id_category_new;
            $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_categories_tmp;");
        }
    }
}

foreach ($array_categories as $id_category_new) {
    $result = $mysqli->query("SELECT id,id_category_parent FROM sml_categories WHERE id_category_parent IS NOT NULL AND id=$id_category_new LIMIT 1;");
    if ($result) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $id = $row['id'];
            $id_category_parent = $row['id_category_parent'];
            $id_category_parent_new = $array_categories[$id_category_parent];
            $mysqli->query("UPDATE sml_categories SET id_category_parent=$id_category_parent_new WHERE id=$id");
        }
    }
}

$array_markers = array();
$result = $mysqli->query("SELECT id FROM sml_markers WHERE id_map=$id_map;");
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $id_marker = $row['id'];
            $mysqli->query("CREATE TEMPORARY TABLE sml_markers_tmp SELECT * FROM sml_markers WHERE id = $id_marker;");
            $mysqli->query("UPDATE sml_markers_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_markers),id_map=$id_map_new,access_count=0;");
            $mysqli->query("INSERT INTO sml_markers SELECT * FROM sml_markers_tmp;");
            $id_marker_new = $mysqli->insert_id;
            $array_markers[$id_marker] = $id_marker_new;
            $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_markers_tmp;");
        }
    }
}

foreach ($array_markers as $id_marker=>$id_marker_new) {
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
    $result = $mysqli->query("SELECT id_marker,id_category FROM sml_markers_categories_assoc WHERE id_marker=$id_marker;");
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $id_category = $row['id_category'];
                $id_category_new = $array_categories[$id_category];
                $mysqli->query("CREATE TEMPORARY TABLE sml_markers_categories_assoc_tmp SELECT * FROM sml_markers_categories_assoc WHERE id_marker = $id_marker AND id_category = $id_category;");
                $mysqli->query("UPDATE sml_markers_categories_assoc_tmp SET id_marker=$id_marker_new,id_category=$id_category_new;");
                $mysqli->query("INSERT INTO sml_markers_categories_assoc SELECT * FROM sml_markers_categories_assoc_tmp;");
                $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_markers_categories_assoc_tmp;");
            }
        }
    }
    $result = $mysqli->query("SELECT id,id_marker_source,id_marker_dest FROM sml_markers_connects WHERE id_marker_source=$id_marker;");
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $id = $row['id'];
                $id_marker_source = $row['id_marker_source'];
                $id_marker_dest = $row['id_marker_dest'];
                $id_marker_source_new = $array_markers[$id_marker_source];
                $id_marker_dest_new = $array_markers[$id_marker_dest];
                $mysqli->query("CREATE TEMPORARY TABLE sml_markers_connects_tmp SELECT * FROM sml_markers_connects WHERE id = $id;");
                $mysqli->query("UPDATE sml_markers_connects_tmp SET id=(SELECT MAX(id)+1 as id FROM sml_markers_connects),id_marker_source=$id_marker_source_new,id_marker_dest=$id_marker_dest_new;");
                $mysqli->query("INSERT INTO sml_markers_connects SELECT * FROM sml_markers_connects_tmp;");
                $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_markers_connects_tmp;");
            }
        }
    }
    $result = $mysqli->query("SELECT id_marker,id_map FROM sml_story WHERE id_marker=$id_marker AND id_map=$id_map;");
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $id = $row['id'];
                $id_map = $row['id_map'];
                $id_marker = $row['id_marker'];
                $id_marker_new = $array_markers[$id_marker];
                $mysqli->query("CREATE TEMPORARY TABLE sml_story_tmp SELECT * FROM sml_story WHERE id_marker=$id_marker AND id_map=$id_map;");
                $mysqli->query("UPDATE sml_story_tmp SET id_marker=$id_marker_new,id_map=$id_map_new;");
                $mysqli->query("INSERT INTO sml_story SELECT * FROM sml_story_tmp;");
                $mysqli->query("DROP TEMPORARY TABLE IF EXISTS sml_story_tmp;");
            }
        }
    }
}
ob_end_clean();
echo json_encode(array("status"=>"ok"));
