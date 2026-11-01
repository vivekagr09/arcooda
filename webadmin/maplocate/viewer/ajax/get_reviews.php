<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$id_marker = (int)$_POST['id_marker'];
$array_reviews = array();
$average_rating = 0;
$array_perc = [0,0,0,0,0];
$query = "SELECT * FROM sml_reviews WHERE id_marker=? ORDER BY create_date DESC;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_marker);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        $count = count($result);
        if($count>0) {
            while($row = array_shift($result)) {
                $rating = $row['rating'];
                $array_perc[($rating-1)]++;
                $average_rating = $average_rating+$rating;
                $row['create_date'] = timeAgo($row['create_date']);
                $row['comment'] = nl2br($row['comment']);
                $array_reviews[] = $row;
            }
            $average_rating = round($average_rating/$count,1);
            $array_perc[0] = get_percentage($count,$array_perc[0]);
            $array_perc[1] = get_percentage($count,$array_perc[1]);
            $array_perc[2] = get_percentage($count,$array_perc[2]);
            $array_perc[3] = get_percentage($count,$array_perc[3]);
            $array_perc[4] = get_percentage($count,$array_perc[4]);
        }
    }
}
ob_end_clean();
echo json_encode(array("reviews"=>$array_reviews,"average_rating"=>$average_rating,"array_perc"=>$array_perc));

function get_percentage($total, $number) {
    if ( $total > 0 ) {
        return round(($number * 100) / $total, 2);
    } else {
        return 0;
    }
}

function timeAgo($time_ago) {
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    if($days <= 7){
        if($days==0) {
            return "Today";
        }
        elseif($days==1){
            return "Yesterday";
        }else{
            return "$days days ago";
        }
    } else if($weeks <= 4.3){
        if($weeks==1){
            return "1 week ago";
        }else{
            return "$weeks weeks ago";
        }
    } else if($months <=12){
        if($months==1){
            return "1 month ago";
        }else{
            return "$months months ago";
        }
    } else{
        if($years==1){
            return "1 year ago";
        }else{
            return "$years years ago";
        }
    }
}