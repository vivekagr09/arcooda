<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$action='view';
$id=intval($posted['id']);
$staff_id=$_GLOBALS['staff']['id'];

$item=$section_handler->fromDatabase($id);
$item_comments=$section_handler->commentSearch(array($section_handler->section_name.'_id'=>$id,'comment_type'=>'comment'));




?>
<h2>Comments <?php echo $section_handler->formatNumber($id) ?> - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php

include 'templates/comments.tpl.php';

include_once('templates/footer.tpl.php');
?>