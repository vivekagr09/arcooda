<?php
include_once('common.inc.php');

$_GET['return_message']='popup';
include_once('templates/header.tpl.php');

$action='statistics';
$id=intval($posted['id']);
$staff_id=$_GLOBALS['staff']['id'];
$site_id=$_GLOBALS['site_settings']['site_id'];

$item=$section_handler->fromDatabase($id);
?>
<h2>Newsletter Statistics - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php
include 'templates/summary_information.tpl.php';

$_GET['return_message']='popup';
include_once('templates/footer.tpl.php');
?>