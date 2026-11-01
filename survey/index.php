<?php
/**
 * Home page
 * 
 * @author Kelvin <khtat@highwaygroup.net>
 */

// include common
include_once(dirname(__FILE__) . "/inc/common.inc.php");

// Include copyright content
include_once(dirname(__FILE__) . "/inc/copyright.inc.php");

// Instantiate Survey Object
$survey = new Survey($DB);

// ---------------------------------------------------

// Get current URI
$uri = $_SERVER['SCRIPT_URI'];

// Root URL
$urlProtocol = (isset($_SERVER['HTTPS']) === true && empty($_SERVER['HTTPS']) === false) ? "https" : "false";
$domain = $urlProtocol . "://" . $_SERVER['HTTP_HOST'];

// Check if preview
$preview = false;

$adminUrl = "https://www.arcooda.com/webadmin/survey/viewSurvey.php";

if (isset($_SERVER['HTTP_REFERER']) === true && empty($_SERVER['HTTP_REFERER']) === false && 
	substr($_SERVER['HTTP_REFERER'], 0, strpos($_SERVER['HTTP_REFERER'], "?")) == $adminUrl) {
	$preview = true;
}

// ---------------------------------------------------

// Token data
$tokenData = null;

// Template file to be loaded
$file = ".tpl.php";

// Get Survey ID
if (strpos($uri, "/t/") === false || $preview === true) {	// Not token
	$surveyId = intval($survey->getSurveyIdByLink($uri));
} else {	// Token Link
	$surveyId = intval($survey->getSurveyIdByTokenLink($uri));

	// Extract token from link
	$stripUri = explode("/", $uri);
	$token = trim(strtolower(end($stripUri)));

	// Get token data
	$tokenData = $survey->getToken($token);

	// Check token status
	if (intval($tokenData['status']) != 1) {
		$surveyId = 0;
	}
}

// Invalid survey
if ($surveyId == 0) {
	$file = "_404.tpl.php";
}

// ---------------------------------------------------

// Get survey data
$surveyData = $survey->getSurveyById($surveyId);

// Check survey status
if (intval($surveyData['status']) == 1 || $preview === true) {
	
	if ($preview === false) {	// Ignore all availability if it's preview
		// Check if survey is only for member
		if ($surveyData['available'] == Survey::SURVEY_MEMBER_ONLY && (isset($_GLOBALS['member']['member_id']) === false || empty($_GLOBALS['member']['member_id']) === true || $_GLOBALS['member']['member_id'] <= 0)) {
			header("Location: /messages/members_only.html?return_url=" . urlencode($uri));
			exit();
		}

		// Check if survey is token based
		if ($surveyData['available'] == Survey::SURVEY_TOKEN_ONLY && $tokenData == null) {
			$file = "_404.tpl.php";
		}
	}

} else {
	$file = "_404.tpl.php";
}

// Get survey questions
$surveyQuestions = $survey->getQuestionBySurveyId($surveyId);

// Template of survey form
$surveyTemplate = "std"; // Standard

// Get Logo
$logoExt = array("jpg", "jpeg", "png", "gif");
$logo = "";
foreach($logoExt as $ext) {
	if (file_exists("img/logo/" . $_SERVER['HTTP_HOST'] . "." . $ext)) {
		$logo = $domain . "/survey/img/logo/" . $_SERVER['HTTP_HOST'] . "." . $ext;
		break;
	}
}

// Some variables to be used in templates
$surveyName = htmlspecialchars($surveyData['name']);
$surveyDescription = nl2br(htmlspecialchars($surveyData['description']));
$surveyCompletedDescription = $surveyData['completed_description'];
$qpp = intval($surveyData['qpp']);

// 404 Page header
if ($file == "_404.tpl.php") {
	header("HTTP/1.0 404 Not Found");
}

// ---------------------------------------------------

// Controller: Save answers
if (isset($posted['survey_answer']) === true && empty($posted['survey_answer']) === false &&
	isset($posted['submit_token']) === true && empty($posted['submit_token']) === false && $posted['submit_token'] == $_SESSION['submit_token']) {
	$answers = array(
		"choice" => array(),
		"open" => array(),
		"survey_id" => $surveyId,
	);

	// Check if it's token
	if ($tokenData != null) {
		$answers['member'] = $tokenData['member_id'];
	} else {
		if (isset($_GLOBALS['member']['member_id']) === true && empty($_GLOBALS['member']['member_id']) === false && $_GLOBALS['member']['member_id'] > 0) {
			$answers['member'] = $_GLOBALS['member']['member_id'];
		}
		if (preg_match('/^([0-9a-f]{1,8})([0-9a-f]{8})$/', $_COOKIE['member_id'] ?? '', $m)) {
			 if (function_exists('memberHasValue') && memberHasValue($member_id = (int)hexdec($m[1]), 'rand_num', (int)hexdec($m[2]))) {
			 	$answers['member'] = $member_id;
			 }
		}
	}
	if (isset($posted['survey_answer']['single']) === true && empty($posted['survey_answer']['single']) === false) {
		foreach($posted['survey_answer']['single'] as $single) {
			if (isset($single['survey_question_choice_id']) === true && empty($single['survey_question_choice_id']) === false) {
				
				if (isset($single['choice_open_value']) === false) {
					$single['choice_open_value'] = "";
				}

				$answers['choice'][] = $single;
			}
		}
	}

	if (isset($posted['survey_answer']['multi']) === true && empty($posted['survey_answer']['multi']) === false) {
		foreach($posted['survey_answer']['multi'] as $multi) {

			$survey_question_id = $multi['survey_question_id'];
			unset($multi['survey_question_id']);

			foreach($multi as $k => $v) {

				if (isset($v['survey_question_choice_id']) === true && empty($v['survey_question_choice_id']) === false) {
					$v['survey_question_id'] = $survey_question_id;
					
					if (isset($v['choice_open_value']) === false) {
						$v['choice_open_value'] = "";
					}

					$answers['choice'][] = $v;
				}
			}

		}
	}

	if (isset($posted['survey_answer']['open']) === true && empty($posted['survey_answer']['open']) === false) {
		foreach($posted['survey_answer']['open'] as $open) {
			$answers['open'][] = $open;
		}
	}
	$pid = $survey->addAnswer($answers);

	if ($pid !== false) {

		if ($tokenData != null) {
			$survey->completeToken($tokenData['token']);
		}

		$file = "_complete.tpl.php";
	}

	// Debug
	$debug = array(
		"survey_class_return_message" => $survey->getErrorMessages(),
		"answers" => $answers,
		"link" => $uri
	);

	$debug_json = json_encode($debug);

	$DB->query(sprintf("INSERT INTO `highwaygroup`.`survey_user_end_debug` (`participant_id`,`answers`) VALUES (%s,%s)", formatSQLString($pid), formatSQLString($debug_json)));

}

// ---------------------------------------------------

// Generate token for form submission
$_SESSION['submit_token'] = md5(uniqid());

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?= $surveyName ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<link rel="stylesheet" href="/survey/css/<?= $_SERVER['HTTP_HOST'] ?>_<?= $surveyTemplate ?>.css">
</head>
<body>
	<?php 
	if(isset($_GET['front'])){
		$surveyTemplate = 'stdfront';
	}
	include "templates/" . $surveyTemplate . $file; 

	?>

	<p class="text-center text-secondary small"><?= $copyright[$_SERVER['HTTP_HOST']] ?></p>

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

	<script src="/survey/js/ui.js"></script>
</body>
</html>
