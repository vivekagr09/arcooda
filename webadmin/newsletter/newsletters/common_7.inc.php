<?php
/**
 * Common setup for template
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
		'defaults' => array('enable'=>'y','value'=>"Dear [FIRST_NAME],\n\n\n\nBest Regards,\n[STAFF_NAME]"),
	),
	'large_banner' => array(
		'title' => 'Large Banner',
		'options' => 'wide_thin',
		'defaults' => array('enable'=>'n','option'=>'brief','qty'=>'1'),
	),
	'news' => array(
		'title' => 'News',
		'options' => 'brief_full',
		'defaults' => array('enable'=>'n','option'=>'brief','qty'=>'2'),
	),
	'packages' => array(
		'title' => 'Packages',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'full','qty'=>'1'),
	),
	'arcade_parts' => array(
		'title' => 'Arcade Parts',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'brief','qty'=>'10'),
	),
	'arcade_boards' => array(
		'title' => 'Arcade Boards',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'brief','qty'=>'10'),
	),
	'specials' => array(
		'title' => 'Specials',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'summary','qty'=>'4'),
	),
	'latest_offers' => array(
		'title' => 'Latest Offers',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'brief','qty'=>'5'),
	),
	'featured_products' => array(
		'title' => 'Featured Products',
		'options' => 'summary_brief_full',
		'defaults' => array('enable'=>'n','option'=>'summary','qty'=>'2'),
	),
	'bottom_banner' => array(
		'title' => 'Bottom Banner',
		'options' => 'wide_thin',
		'defaults' => array('enable'=>'n','option'=>'full','qty'=>'1'),
	),
);
?>