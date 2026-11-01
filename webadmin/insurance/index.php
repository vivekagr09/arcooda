<?php
include_once('insurance_common.inc.php');

$action=$posted['action'];
$staff_id=staffFromUser($_SERVER['REMOTE_USER']);
$insurance_id=intval($posted['insurance_id']);

//Update Actions
switch($action)
{
case 'do_add':
	unset($posted['insurance_id']);
case 'do_edit':
	$insurance=insuranceFromPost($posted);
	$insurance_rates=insuranceRatesFromPost($posted);
	$old_insurance=insuranceFromDatabase($insurance['insurance_id']);
	$old_insurance_rates=insuranceRatesFromDatabase($insurance['insurance_id']);
	$insurance_exists=insuranceExists($insurance['insurance_id']);
	$posted['insurance_id']=insuranceToDatabase($insurance);
	$insurance_id=intval($posted['insurance_id']);
	insuranceRatesToDatabase($insurance_id,$insurance_rates);

	$contract=insuranceContractFromPost($posted);
	if($contract['contract_commence_time']>0 && $contract['contract_complete_time']>0 && $contract['contract_premium']>0)
		insuranceContractToDatabase($insurance_id,$contract);
	
	$action='edit';
	break;
case 'do_add_claim':
	unset($posted['insurance_id']);
case 'do_edit_claim':
	$insurance_claim=insuranceClaimFromPost($posted);
	$old_insurance_claim=insuranceClaimFromDatabase($insurance_claim['claim_id']);
	$insurance_claim_exists=insuranceClaimExists($insurance_claim['claim_id']);
	$posted['claim_id']=insuranceClaimToDatabase($insurance_claim);

	if($posted['insurance_claim_comments']!='')
	{
		$comment=array(
			'claim_id'=>$insurance_claim['claim_id'],
			'comment_text'=>$posted['insurance_claim_comments'],
			'comment_time'=>$_SERVER['REQUEST_TIME'],
			'comment_date'=>substr(formatSQLDateTime($_SERVER['REQUEST_TIME']),1,-1),
			'staff_id'=>$staff_id
		);
		insuranceClaimCommentToDatabase($posted['claim_id'],$comment);
	}

	$action='claims';
	break;
case 'lock':
	insuranceSetLock($insurance_id,$staff_id);
	$action='list';
	break;
case 'unlock':
	insuranceSetLock($insurance_id);
	$action='list';
	break;
}

$get_vars=$posted;
$keep_only=array('insurance_id', 'contract_id', 'claim_id', 'order_by', 'search_for', 'page', 'order', 'return_message', 'group_by', 'start_time');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(!in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
case 'add_claim':
	unset($get_vars['claim_id']);
	header('Location: insurance_add_claim.php?'.http_build_query($get_vars));
	break;
case 'edit_claim':
	header('Location: insurance_edit_claim.php?'.http_build_query($get_vars));
	break;
case 'claim_comments':
	header('Location: insurance_claim_comments.php?'.http_build_query($get_vars));
	break;
case 'summary':
	header('Location: insurance_summaries.php?'.http_build_query($get_vars));
	break;
case 'claims':
	header('Location: insurance_list_claims.php?'.http_build_query($get_vars));
	break;
case 'add':
	unset($get_vars['insurance_id']);
	header('Location: insurance_add.php?'.http_build_query($get_vars));
	break;
case 'edit':
default:
	$insurance_lock=insuranceGetLock($insurance_id);
	if($insurance_lock===false || $insurance_lock['staff_id']==$staff_id)
		insuranceSetLock($insurance_id,$staff_id);
	header('Location: insurance_edit.php?'.http_build_query($get_vars));
	break;
case 'list':
	unset($get_vars['insurance_id']);
	header('Location: insurance_list.php?'.http_build_query($get_vars));
}

die();
?>