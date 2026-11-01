<?php
/**
 * Common setup for template 7
 *
 * Expects:
 *  $item_settings - settings for the template, to override if necessary
 * Returns:
 *  $sections - A list of sections
 *  $ranks - A list of sections by rank
 */

$sections = array(
	'paragraph' => array(
		'title' => 'Welcome',
		'options' => '',
		'defaults' => array('enable'=>'y','value'=>"Dear [FIRST_NAME],\n\nBest Regards,\n[STAFF_NAME]"),
	),
);
?>