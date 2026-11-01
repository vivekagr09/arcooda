<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
require_once("../../webadmin/maplocate/db/connection.php");
require_once("../../webadmin/maplocate/backend/functions.php");
$settings = get_settings();
$openrouteservice_api_key = $settings['openrouteservice_api_key'];
$requestData = json_decode(file_get_contents('php://input'), true);
if($requestData) {
    $profile = $requestData['profile'];
    $source_lat = $requestData['s_lat'];
    $source_lon = $requestData['s_lon'];
    $dest_lat = $requestData['d_lat'];
    $dest_lon = $requestData['d_lon'];
    $apiDirectionsUrl = 'https://api.openrouteservice.org/v2/directions/'.$profile.'?api_key='.$openrouteservice_api_key.'&start='.$source_lon.','.$source_lat.'&end='.$dest_lon.','.$dest_lat;
    if(function_exists('ini_get') && ini_get('allow_url_fopen')) {
        $response = file_get_contents($apiDirectionsUrl);
        if(empty($response)) {
            $response = curl_get_file_contents($apiDirectionsUrl);
        }
    } else {
        $response = curl_get_file_contents($apiDirectionsUrl);
    }
    header('Content-Type: application/json');
    ob_end_clean();
    echo $response;
}