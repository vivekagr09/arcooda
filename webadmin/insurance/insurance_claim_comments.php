<?php
include_once('insurance_common.inc.php');
include_once('templates/insurance_header.tpl.php');

$action='history';
$claim_id=intval($posted['claim_id']);
$staff_id=staffFromUser($_SERVER['REMOTE_USER']);

$insurance_claim_comments=insuranceClaimCommentsFromDatabase($claim_id);

?>
<h2>Comments for claim <?php echo intval($claim_id) ?> - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>
<?php

include 'templates/insurance_claims_comments.tpl.php';
include_once('templates/insurance_footer.tpl.php');
?>