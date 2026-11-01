<?php
include_once __DIR__ . '/world_common.inc.php';

$map_id  = $_GET['map_id'];
$plug_id = $_GET['plug_id'];

if ('' != $map_id) {
    $row = $DB->query_first(sprintf(
		"SELECT `file_path` FROM `world`.`map` WHERE `id` = %d LIMIT 1",
		$map_id
	), MYSQLI_ASSOC);
	header('content-type: image/gif');
    readfile($row['file_path']);
} else {
    $row = $DB->query_first(sprintf(
		"SELECT `plug_path` FROM `world`.`country_plugs` WHERE `id` = %d",
		$plug_id
	), MYSQLI_ASSOC);
	header('content-type: image/gif');
    readfile($row['plug_path']);
}
