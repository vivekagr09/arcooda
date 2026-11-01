<?php
include_once("../common.inc.php");

$survey = new Survey($DB);

if (isset($posted['data']) === true && empty($posted['data']) === false && is_array($posted['data']) === true) {
	$data = $posted['data'];

	foreach($data as $_d) { // 0 -> id, 1 -> position

		$based = (isset($_d[2]) === true && empty($_d[2]) === false && $_d[2] == 1) ? true : false;

		$survey->updateQuestionPosition($_d[0], $_d[1], $based);
	}
}
?>