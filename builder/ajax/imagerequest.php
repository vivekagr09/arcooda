<?php
$path = realpath(dirname(__FILE__) . "/..");
include $path . "/inc/common.inc.php";
function createslug($name){
   return (trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));
}

$image=null;
if($_POST['product_id'] && $_POST['color_id']){
	$row = $DB->query_first(sprintf(
        "SELECT * FROM `products_images` WHERE `deleted_at` IS NULL AND `product_id`=%d AND `color_id`=%d order by id desc limit 1" ,
        $_POST['product_id'],
        $_POST['color_id']
    ), MYSQLI_ASSOC);
    if($row){
    	$image='/colourimages/'.createslug($row['file_orig']).'/'.createslug($row['caption']).'-'.$row['id'].'.jpg';
    }
}
echo $image;

?>