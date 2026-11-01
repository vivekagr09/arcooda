<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$id_map = (int)$_POST['id_map'];
$array_story = array();
$query = "SELECT s.id_marker,m.lat,m.lon,m.name,m.description,s.zoom,s.description as description_story,GROUP_CONCAT(i.image) as images,s.priority FROM sml_story as s
JOIN sml_markers as m on s.id_marker = m.id
LEFT JOIN sml_images as i on m.id = i.id_marker
WHERE s.id_map=?
GROUP BY s.id_marker, m.lat, m.lon, m.name, m.description, s.zoom, s.description, s.priority
ORDER BY s.priority ASC;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_map);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        $count = count($result);
        if($count>0) {
            while($row = array_shift($result)) {
                if(!empty($row['description_story'])) $row['description']=$row['description_story'];
                unset($row['description_story']);
                if(empty($row['images'])) $row['images']='';
                $array_story[] = $row;
            }
        }
    }
}
ob_end_clean();
echo json_encode(array("story"=>$array_story));