<?php
include_once('insurance_common.inc.php');
include_once('templates/insurance_header.tpl.php');

$action='edit_claim';
$claim_id=intval($posted['claim_id']);
$staff_id=staffFromUser($_SERVER['REMOTE_USER']);

$insurance_claim=insuranceClaimFromDatabase($claim_id);
$insurance_claim_comments=insuranceClaimCommentsFromDatabase($claim_id);

?>
<h2>Edit Insurance Claim</h2>
<?php

include 'templates/insurance_addedit_claim.tpl.php';
include_once('templates/insurance_footer.tpl.php');
?>