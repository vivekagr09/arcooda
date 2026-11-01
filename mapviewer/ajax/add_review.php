<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("../../db/connection.php");
$id_marker = $_POST['id_marker'];
$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$comment = strip_tags($_POST['comment']);
$rating = $_POST['rating'];
$identifier = uniqid('sml');

$query = "INSERT INTO sml_reviews(id_marker,create_date,name,email,rating,comment,identifier) VALUES(?,NOW(),?,?,?,?,?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('isssss',$id_marker,$name,$email,$rating,$comment,$identifier);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok","identifier"=>$identifier));
    } else {
        echo json_encode(array("status"=>"error"));
    }
}