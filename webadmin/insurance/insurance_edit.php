<?php
include_once('insurance_common.inc.php');
include_once('templates/insurance_header.tpl.php');

$action='edit';
$insurance_id=intval($posted['insurance_id']);
$staff_id=staffFromUser($_SERVER['REMOTE_USER']);

$insurance=insuranceFromDatabase($insurance_id);
$insurance_lock=insuranceGetLock($insurance_id);
$insurance_rates=insuranceRatesFromDatabase($insurance_id);
$insurance_contracts=insuranceContractsFromDatabase($insurance_id);

$DB->query("UPDATE highwaygroup.insurances SET access_date=".formatSQLDateTime($_SERVER['REQUEST_TIME'])." WHERE insurance_id=".$insurance_id);

?>
<h2>Edit Insurance <?php echo insuranceFormatNumber($insurance_id,$insurance['revision_id'],$insurance['site_id']) ?> - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php

include 'templates/insurance_addedit.tpl.php';
include_once('templates/insurance_footer.tpl.php');
?>