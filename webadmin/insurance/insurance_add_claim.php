<?php
include_once('insurance_common.inc.php');
include_once('templates/insurance_header.tpl.php');

$action='add_claim';
$insurance_id=intval($posted['insurance_id']);
$staff_id=staffFromUser($_SERVER['REMOTE_USER']);

$insurance_claim=array('insurance_id'=>$insurance_id);
$insurance_claim_comments=array();

?>
<h2>Add New Insurance Claim - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php

include 'templates/insurance_addedit_claim.tpl.php';
include_once('templates/insurance_footer.tpl.php');
?>