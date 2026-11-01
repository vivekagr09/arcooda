<?php
include_once dirname(__FILE__) . "/common.inc.php";

$result=$posted;

switch($posted['action'])
{
case 'newsSearch':
	$criteria = array();
	foreach($posted as $k=>$v) if($k!='action') $criteria[$k]=$v;
	$result['result'] = $NH->search($criteria,'date_added');
	if($posted['return_sort']!='') $result['result'] = $NH->sort($result['result']);
	//if($posted['return_desc']) $result['result'] = array_reverse($result['result']);
	if($posted['return_limit']>0) $result['result'] = reset(array_chunk($result['result'], $posted['return_limit']));

	break;

case 'newsFromDatabase':
	$result['result'] = $NH->fromDatabase($posted['id']);
	break;

case 'newsGetValue':
	$result['result'] = $NH->getValue($posted['id'], $posted['field']);
	break;
}

header('Content-Type: text/javascript');
echo json_encode($result);
