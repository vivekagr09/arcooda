<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if(isset($_POST['version']) && !empty($_POST['version'])) {
    $version = $_POST['version'];
    require_once("../../db/connection.php");
    require_once("../../services/check_update.php");
    $mysqli->query("UPDATE sml_settings SET `version`='$version';");
}