<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$action='edit';
$id=intval($posted['id']);
$staff_id=$_GLOBALS['staff']['id'];
$site_id=$_GLOBALS['site_settings']['site_id'];

$item=$section_handler->fromDatabase($id);
$item_lock=$section_handler->getLock($id);
$item_comments=$section_handler->commentSearch(array($section_handler->section_name.'_id'=>$id,'comment_type'=>'comment'));
$item_settings=unserialize($item['settings']);

//Hack for fixed quantities
foreach($item_settings as $k1=>$v1) if(is_array($v1) && is_array($v1['items'])) $item_settings[$k1]['qty']=count($v1['items']);

$item_members_list=$section_handler->mailinglistFromDatabase($id);
$by_status=array();
foreach($item_members_list as $key=>$value) $by_status[$value['email_status']][]=$key;

if(count($item_comments)>5) $item_comments=reset(array_chunk($item_comments,5));

$member_list = $section_handler->mailinglistFromDatabase($id);

?>
<h2>Edit Newsletter <?php echo $section_handler->formatNumber($id) ?> - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<p>Found <?php echo count($member_list) ?> members</p>
<?php

include 'templates/addedit.tpl.php';

include_once('templates/footer.tpl.php');
?>