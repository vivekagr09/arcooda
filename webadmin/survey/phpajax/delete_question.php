<?php
include_once("../common.inc.php");

$survey = new Survey($DB);

if (isset($posted['question_id']) === true && empty($posted['question_id']) === false) {
	
	$based = (isset($posted['based']) === true && empty($posted['based']) === false && $posted['based'] == true) ? true : false;

 	if ($survey->deleteQuestion(intval($posted['question_id']), $based)) {
 		echo "success";
 	} else {
 		echo "error";
 	}
} else {
	echo "error";
}
?>