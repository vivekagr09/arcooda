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
	'top_banner' => array(
		'title' => 'Top Banner',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'1'),
	),
	'updates' => array(
		'title' => 'Trading Company Updates',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'2'),
	),
	'classified' => array(
		'title' => 'Classified Items Closing Soon',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'2'),
	),
	'wanted' => array(
		'title' => 'Wanted Items Closing Soon',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'2'),
	),
	'news' => array(
		'title' => 'Latest News Stories',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'2'),
	),
	'featured_products' => array(
		'title' => 'Latest Products',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'10'),
	),
	'forum' => array(
		'title' => 'Latest Forum Discussion Topics',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'10'),
	),
	'tradeshow' => array(
		'title' => 'Upcoming Trade Shows',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'4'),
	),
	'bottom_banner' => array(
		'title' => 'Bottom Banner',
		'options' => '',
		'defaults' => array('enable'=>'n','option'=>'','qty'=>'1'),
	),
);
?>