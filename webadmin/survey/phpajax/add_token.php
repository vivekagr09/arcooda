<?php
include_once("../common.inc.php");

$survey = new Survey($DB);

if (isset($posted['member_id']) === true && empty($posted['member_id']) === false && 
	isset($posted['survey_id']) === true && empty($posted['survey_id']) === false) {

	$surveyData = $survey->getSurveyById(intval($posted['survey_id']));

	$link = rtrim($surveyData['survey_link'][0], "/");

	$link = substr($link, 0, strrpos($link, "/survey/"));
	
	$data = array(
		"staff" => $staff_id,
		"link" => $link,
		"member_id" => intval($posted['member_id']),
		"survey_id" => intval($posted['survey_id']),
		"status" => 1
	);

	$token = $survey->addToken($data);

	$tokenData = $survey->getToken($token);

	echo $tokenData['link'];
}
?>