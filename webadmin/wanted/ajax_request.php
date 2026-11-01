<?php
include_once 'wanted_common.php';

$result=$posted;

switch($posted['action'])
{
case 'wantedSearch':
	$criteria = array();
	foreach($posted as $k=>$v) if($k!='action') $criteria[$k]=$v;
	$result['result'] = wantedSearch($criteria);

	if($posted['return_sort']!='') $result['result']=wantedSort($result['result']);
	if($posted['return_desc']!='') $result['result']=array_reverse($result['result']);
	if($posted['return_limit']>0) $result['result']=reset(array_chunk($result['result'],$posted['return_limit']));

	break;

case 'wantedFromDatabase':
	$result['result'] = wantedFromDatabase($posted['id']);
	break;

case 'wantedGetValue':
	$result['result'] = wantedGetValue($posted['id'],$posted['field']);
	break;
}

header('Content-Type: text/javascript');
echo json_encode($result);
