<?php
$pageSetting = array();


$pageSetting['admincontrol_dashboard'] = array(
	'title' => __('admin.menu_admin_panel')
);

$pageSetting['admincontrol_cron'] = array(
	'title' => __('admin.cron_job'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.cron_job'),
			'link' =>  base_url('admincontrol/cron'),
		),
	),
);

$pageSetting['admincontrol_orders_notifications'] = array(
	'title' => __('admin.notifications'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_notification'),
			'link' =>  base_url('admincontrol/notification'),
		),
		array(
			'title' =>  __('admin.page_title_order_notification_details'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['themes_edit_faq'] = array(
	'title' => __('admin.edit_faq'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		array(
			'title' =>  __('admin.page_title_edit_faq'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['themes_add_new_faq'] = array(
	'title' => __('admin.add_new_faq'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		array(
			'title' =>  __('admin.page_title_add_new_faq'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['admincontrol_click_notification'] = array(
	'title' => __('admin.notifications'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_notification'),
			'link' =>  base_url('admincontrol/click_notification'),
		),
		array(
			'title' =>  __('admin.page_title_notification_details'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['admincontrol_tutorial'] = array(
	'title' => __('admin.group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.tutorial'),
			'link' =>  base_url('admincontrol/tutorial'),
		),
	),
);

$pageSetting['admincontrol_manage_tutorial_catgory'] = array(
	'title' => __('admin.group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.tutorial'),
			'link' =>  base_url('admincontrol/tutorial'),
		),
		array(
			'title' =>  __('admin.manage_tutorial_catgory'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['admincontrol_manage_tutorial'] = array(
	'title' => __('admin.group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.tutorial'),
			'link' =>  base_url('admincontrol/tutorial'),
		),
		array(
			'title' =>  __('admin.edit_tutorial'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['admincontrol_troubleshoot'] = array(
	'title' => __('admin.group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.troubleshoot'),
			'link' =>  base_url('admincontrol/troubleshoot'),
		),
	),
);

$pageSetting['admincontrol_usergroup'] = array(
	'title' => __('admin.group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.group'),
			'link' =>  base_url('admincontrol/usergroup'),
		),
	),
);
$pageSetting['admincontrol_todolist'] = array(
	'title' => __('admin.menu_to_do_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_to_do_list'),
			'link' =>  base_url('admincontrol/todolist'),
		),
	),
);

$pageSetting['admincontrol_affiliate_theme'] = array(
	'title' => __('admin.affiliate_theme'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.affiliate_theme'),
			'link' =>  base_url('admincontrol/affiliate_theme'),
		),
	),
);

$pageSetting['admincontrol_manage_review'] = array(
	'title' => __('admin.manage_review'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),

		array(
		'title' =>  __('admin.page_title_store_dashboard'),
		'link' =>  base_url('admincontrol/store_dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.manage_review'),
			'link' =>  base_url('admincontrol/manage_review'),
		),
	),
);




$pageSetting['admincontrol_addons'] = array(
	'title' => __('admin.menu_addons'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		
	),
);

$pageSetting['admincontrol_all_transaction'] = array(
	'title' => __('admin.menu_all_transactions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_all_transactions'),
			'link' =>  base_url('admincontrol/all_transaction'),
		),
		
	),
);

$pageSetting['admincontrol_uncompleted_payments'] = array(
	'title' => __('admin.uncompleted_payments'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.uncompleted_payments'),
			'link' =>  base_url('admincontrol/uncompleted_payments'),
		),
		
	),
);


$pageSetting['admincontrol_payment_gateway'] = array(
	'title' => __('admin.payment_gateways'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.payment_gateways'),
			'link' =>  base_url('admincontrol/payment_gateway'),
		),
		
	),
);

$pageSetting['admincontrol_payment_gateway_documentation'] = array(
	'title' => __('admin.payment_api_documentation'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.payment_api_documentation'),
			'link' =>  base_url('admincontrol/payment_gateway_documentation'),
		),
		
	),
);

$pageSetting['admincontrol_group_form'] = array(
	'title' => __('admin.manage_group'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.group'),
			'link' =>  base_url('admincontrol/usergroup'),
		),
		
		array(
			'title' =>  __('admin.manage_group'),
			'link' =>  base_url('admincontrol/group_form'),
		),
	),
);

$pageSetting['admincontrol_notification'] = array(
	'title' => __('admin.page_title_notification'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_notification'),
			'link' =>  base_url('admincontrol/notification'),
		),
	),
);

$pageSetting['themes_multiple_theme'] = array(
	'title' => __('admin.page_title_theme_home'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
	),
);



$pageSetting['themes_edit_page'] = array(
	'title' => __('admin.page_title_theme_pages'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_page'),
			'link' =>  base_url('themes/edit_page'),
		),
	),
);


$pageSetting['themes_add_new_page'] = array(
	'title' => __('admin.page_title_theme_pages'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_page'),
			'link' =>  base_url('themes/add_new_page'),
		),
	),
);


$pageSetting['themes_add_new_slider'] = array(
	'title' => __('admin.page_title_theme_slider'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_theme_slider'),
			'link' =>  base_url('themes/add_new_slider'),
		),
	),
);

$pageSetting['themes_add_new_recommendation'] = array(
	'title' => __('admin.page_title_theme_recommendation'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_recommendation'),
			'link' =>  base_url('themes/add_new_recommendation'),
		),
	),
);

$pageSetting['themes_add_new_video'] = array(
	'title' => __('admin.page_title_theme_video'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_video'),
			'link' =>  base_url('themes/add_new_video'),
		),
	),
);


$pageSetting['themes_add_new_homecontent'] = array(
	'title' => __('admin.page_title_theme_homecontent'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_homecontent'),
			'link' =>  base_url('themes/add_new_homecontent'),
		),
	),
);

$pageSetting['themes_add_new_section'] = array(
	'title' => __('admin.page_title_theme_section'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_add_new_section'),
			'link' =>  base_url('themes/add_new_section'),
		),
	),
);

$pageSetting['themes_edit_slider'] = array(
	'title' => __('admin.page_title_edit_slider'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_themes_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_slider'),
			'link' =>  base_url('themes/edit_slider'),
		),
	),
);


$pageSetting['themes_edit_section'] = array(
	'title' => __('admin.page_title_edit_section'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_themes_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_section'),
			'link' =>  base_url('themes/edit_section'),
		),
	),
);

$pageSetting['themes_edit_recommendation'] = array(
	'title' => __('admin.page_title_edit_recommendation'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_themes_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_recommendation'),
			'link' =>  base_url('themes/edit_recommendation'),
		),
	),
);

$pageSetting['themes_edit_homecontent'] = array(
	'title' => __('admin.page_title_edit_homecontent'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_themes_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_homecontent'),
			'link' =>  base_url('themes/edit_homecontent'),
		),
	),
);

$pageSetting['themes_edit_video'] = array(
	'title' => __('admin.page_title_edit_video'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_themes_home'),
			'link' =>  base_url('themes/multiple_theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_video'),
			'link' =>  base_url('themes/edit_video'),
		),
	),
);


$pageSetting['admincontrol_theme_section'] = array(
	'title' => __('admin.page_title_theme_section'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_theme'),
			'link' =>  base_url('admincontrol/theme'),
		),
		
		array(
			'title' =>  __('admin.page_title_edit_theme_section'),
			'link' =>  base_url('admincontrol/theme_section'),
		),
	),
);

$pageSetting['membership_membership_orders'] = array(
	'title' => __('admin.page_title_membership_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_membership_orders'),
			'link' =>  base_url('membership/membership_orders'),
		),
	),
);

$pageSetting['membership_membership_purchase_edit'] = array(
	'title' => __('admin.page_title_membership_purchase_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_membership_orders'),
			'link' =>  base_url('membership/membership_orders'),
		),
		array(
			'title' =>  __('admin.page_title_membership_purchase_edit'),
			'link' =>  '',
		),
	),
);

$pageSetting['membership_plan_edit'] = array(
	'title' => __('admin.page_title_membership_plan_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_membership_plans'),
			'link' =>  base_url('membership/plans'),
		),
		array(
			'title' =>  __('admin.page_title_membership_plan_edit'),
			'link' =>  base_url('membership/plan_edit'),
		),
	),
);

$pageSetting['membership_settings'] = array(
	'title' => __('admin.page_title_membership_settings'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_membership_settings'),
			'link' =>  base_url('membership/settings'),
		),
	),
);

$pageSetting['membership_plan_create'] = array(
	'title' => __('admin.page_title_membership_plans'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_membership_plans'),
			'link' =>  base_url('membership/plans'),
		),
		array(
			'title' =>  __('admin.page_title_membership_plan_create'),
			'link' =>  base_url('membership/plans'),
		),
	),
);

$pageSetting['membership_plans'] = array(
	'title' => __('admin.page_title_membership_plans'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_membership_plans'),
			'link' =>  base_url('membership/plans'),
		),
	),
);


$pageSetting['admincontrol_withdrawal_payment_gateways'] = array(
	'title' => __('admin.page_title_withdrawal_payment_gateways'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_wallet'),
			'link' =>  ('#'),
		),
		
		array(
			'title' =>  __('admin.page_title_withdrawal_payment_gateways'),
			'link' =>  base_url('admincontrol/withdrawal_payment_gateways'),
		),
	),
);


$pageSetting['admincontrol_withdrawal_payment_gateways_edit'] = array(
	'title' => __('admin.page_title_withdrawal_payment_gateways_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_wallet'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.page_title_withdrawal_payment_gateways'),
			'link' =>  base_url('admincontrol/withdrawal_payment_gateways'),
		),
		array(
			'title' =>  __('admin.page_title_withdrawal_payment_gateways_edit'),
			'link' =>  base_url('admincontrol/withdrawal_payment_gateways_edit'),
		),
	),
);

$pageSetting['admincontrol_script_details'] = array(
	'title' => __('admin.page_title_script_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_script_details'),
			'link' =>  base_url('admincontrol/script_details'),
		),
	),
);

$pageSetting['admincontrol_store_category'] = array(
	'title' => __('admin.page_category'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_category'),
			'link' =>  base_url('admincontrol/store_category'),
		),
	),
);


$pageSetting['admincontrol_store_category_add'] = array(
	'title' => __('admin.page_title_admincontrol_store_category_add'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_category'),
			'link' =>  base_url('admincontrol/store_category'),
		),
		array(
			'title' =>  __('admin.page_title_store_edit_category'),
			'link' => ('#'),
		),
	),
);



$pageSetting['reportController_admin_transaction'] = array(
	'title' => __('admin.page_title_transaction'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.page_title_transaction'),
			'link' =>  base_url('reportController/admin_transaction'),
		),
	),
);

$pageSetting['admincontrol_userslist'] = array(
	'title' => __('admin.page_title_userslist'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_userslist'),
			'link' =>  base_url('admincontrol/userslist'),
		),
	),
);

$pageSetting['admincontrol_admin_user'] = array(
	'title' => __('admin.page_title_admin_user'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_admin_user'),
			'link' =>  base_url('admincontrol/admin_user'),
		),
	),
);

$pageSetting['admincontrol_admin_user_form'] = array(
	'title' => __('admin.page_title_admin_user'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_admin_user'),
			'link' =>  base_url('admincontrol/admin_user'),
		),
		array(
			'title' =>  __('admin.page_title_edit_admin_user'),
			'link' =>  base_url('admincontrol/admin_user_form'),
		),
	),
);


$pageSetting['admincontrol_addusers'] = array(
	'title' => __('admin.page_title_addusers'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_userslist'),
			'link' =>  base_url('admincontrol/userslist'),
		),
		array(
			'title' =>  __('admin.page_title_addusers'),
			'link' =>  base_url('admincontrol/addusers'),
		),
	),
);


$pageSetting['firstsetting_index'] = array(
	'title' => __('admin.page_title_firstsetting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_firstsetting'),
			'link' =>  base_url('admincontrol/firstsetting'),
		),
	),
);

$pageSetting['admincontrol_userslisttree'] = array(
	'title' => __('admin.page_title_treelist'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_treelist'),
			'link' =>  base_url('admincontrol/userslisttree'),
		),
	),
);


$pageSetting['admincontrol_userslistmail'] = array(
	'title' => __('admin.page_title_userslistmail'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_userslistmail'),
			'link' =>  base_url('admincontrol/userslistmail'),
		),
	),
);

$pageSetting['integration_programs'] = array(
	'title' => __('admin.page_title_programs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_programs'),
			'link' =>  base_url('integration/programs'),
		),
	),
);


$pageSetting['integration_programs_form'] = array(
	'title' => __('admin.page_title_edit_programs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_programs'),
			'link' =>  base_url('integration/programs'),
		),
		array(
			'title' =>  __('admin.page_title_edit_programs'),
			'link' =>  base_url('integration/programs_form'),
		),
	),
);


$pageSetting['admincontrol_downline'] = array(
	'title' => __('admin.page_title_downline'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_downline'),
			'link' =>  base_url('admincontrol/downline'),
		),
	),
);



$pageSetting['integration_integration_tools'] = array(
	'title' => __('admin.page_title_campaigns'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_campaigns'),
			'link' =>  base_url('integration/integration_tools'),
		),
	),
);



$pageSetting['integration_integration_tools_form'] = array(
	'title' => __('admin.page_title_edit_ads'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_campaigns'),
			'link' =>  base_url('integration/integration_tools'),
		),
		array(
			'title' =>  __('admin.page_title_edit_ads'),
			'link' =>  base_url('integration/integration_tools_form/link_ads'),
		),
	),
);


$pageSetting['integration_orders'] = array(
	'title' => __('admin.page_title_integration_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_integration_orders'),
			'link' =>  base_url('integration/orders'),
		),
	),
);

$pageSetting['integration_logs'] = array(
	'title' => __('admin.page_title_integration_logs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_integration_logs'),
			'link' =>  base_url('integration/logs'),
		),
	),
);


$pageSetting['integration_index'] = array(
	'title' => __('admin.page_title_integration_plugins'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_integration_plugins'),
			'link' =>  base_url('integration/index'),
		),
	),
);


$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_postback'),
			'link' =>  base_url('admincontrol/market_tools_setting'),
		),
	),
);

$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_wp_user_register'),
			'link' =>  base_url('integration/instructions/wp_user_register'),
		),
	),
);

$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_wp_forms'),
			'link' =>  base_url('integration/instructions/wp_forms'),
		),
	),
);

$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_show_affiliate_id'),
			'link' =>  base_url('integration/instructions/show_affiliate_id'),
		),
	),
);

$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_wp_show_affiliate_id'),
			'link' =>  base_url('integration/instructions/wp_show_affiliate_id'),
		),
	),
);

$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_affiliate_register_api'),
			'link' =>  base_url('integration/instructions/affiliate_register_api'),
		),
	),
);


$pageSetting['integration_instructions'] = array(
	'title' => __('admin.page_title_integration_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_php_api_library'),
			'link' =>  base_url('integration/instructions/php_api_library'),
		),
	),
);


if($this->uri->segment(3) == 'reviews'){
	$pageSetting['admincontrol_listproduct'] = array(
		'title' => __('admin.page_title_listproduct_review'),
		'breadcrumb' => array(
			array(
				'title' =>  __('admin.page_title_dashboard'),
				'link' =>  base_url('admincontrol/dashboard'),
			),
			array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
			),
			array(
				'title' =>  __('admin.page_title_listproduct'),
				'link' =>  base_url('admincontrol/listproduct'),
			),
			array(
				'title' =>  __('admin.page_title_listproduct_review'),
				'link' =>  base_url('admincontrol/listproduct/reviews'),
			),
		),
	);
} else {
	$pageSetting['admincontrol_listproduct'] = array(
		'title' => __('admin.page_title_listproduct'),
		'breadcrumb' => array(
			array(
				'title' =>  __('admin.page_title_dashboard'),
				'link' =>  base_url('admincontrol/dashboard'),
			),
			array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
			),
			array(
				'title' =>  __('admin.page_title_listproduct'),
				'link' =>  base_url('admincontrol/listproduct'),
			),
		),
	);
}


$pageSetting['admincontrol_addproduct'] = array(
	'title' => __('admin.page_title_addproduct'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_addproduct'),
			'link' =>  base_url('admincontrol/addproduct'),
		),
	),
);



$pageSetting['admincontrol_productupload'] = array(
	'title' => __('admin.page_title_productupload'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_productupload'),
			'link' =>  base_url('admincontrol/productupload'),
		),
	),
);


$pageSetting['admincontrol_store_dashboard'] = array(
	'title' => __('admin.page_title_store_dashboard'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
	),
);


$pageSetting['admincontrol_withdrawal_payment_gateways_doc'] = array(
	'title' => __('admin.page_title_withdrawal_payment_gateways_doc'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_withdrawal_payment_gateways'),
			'link' =>  base_url('admincontrol/withdrawal_payment_gateways'),
		),
		array(
			'title' =>  __('admin.page_title_withdrawal_payment_gateways_doc'),
			'link' =>  base_url('admincontrol/withdrawal_payment_gateways_doc'),
		),
	),
);

$pageSetting['admincontrol_store_setting'] = array(
	'title' => __('admin.page_title_store_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_setting'),
			'link' =>  base_url('admincontrol/store_setting'),
		),
	),
);

$pageSetting['admincontrol_wallet_setting'] = array(
	'title' => __('admin.wallet_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_wallet'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.wallet_setting'),
			'link' =>  base_url('admincontrol/wallet_setting'),
		),
	),
);

$pageSetting['admincontrol_saas_setting'] = array(
	'title' => 'SaaS',
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_saas_settings'),
			'link' =>  base_url('admincontrol/saas_setting'),
		),
	),
);


$pageSetting['admincontrol_updateproduct'] = array(
	'title' => __('admin.page_title_updateproduct'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_updateproduct'),
			'link' =>  base_url('admincontrol/updateproduct'),
		),
	),
);


$pageSetting['admincontrol_videoupload'] = array(
	'title' => __('admin.page_title_videoupload'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_videoupload'),
			'link' =>  base_url('admincontrol/videoupload'),
		),
	),
);


$pageSetting['admincontrol_form'] = array(
	'title' => __('admin.page_title_form'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
	),
);


$pageSetting['admincontrol_form_manage'] = array(
	'title' => __('admin.page_title_form_manage'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_form_manage'),
			'link' =>  base_url('admincontrol/form_manage'),
		),
	),
);


$pageSetting['admincontrol_listorders'] = array(
	'title' => __('admin.page_title_store_listorders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_listorders'),
			'link' =>  base_url('admincontrol/listorders'),
		),
	),
);


$pageSetting['admincontrol_vieworder'] = array(
	'title' => __('admin.page_title_store_order_detail'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_listorders'),
			'link' =>  base_url('admincontrol/listorders'),
		),
		array(
			'title' =>  __('admin.page_title_store_order_detail'),
			'link' =>  '#',
		),
	),
);


$pageSetting['admincontrol_listclients'] = array(
	'title' => __('admin.page_title_listclients'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listclients'),
			'link' =>  base_url('admincontrol/listclients'),
		),
	),
);


$pageSetting['admincontrol_addclients'] = array(
	'title' => __('admin.page_title_addclients'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_listclients'),
			'link' =>  base_url('admincontrol/listclients'),
		),
		array(
			'title' =>  __('admin.page_title_addclients'),
			'link' =>  ('#'),
		),
	),
);



$pageSetting['admincontrol_coupon_manage'] = array(
	'title' => __('admin.page_title_edit_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_listproduct'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_edit_coupon'),
			'link' =>  ('#'),
		),
	),
);


$pageSetting['admincontrol_coupon'] = array(
	'title' => __('admin.page_title_product_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_product_coupon'),
			'link' =>  base_url('admincontrol/coupon'),
		),
	),
);


$pageSetting['admincontrol_form_coupon'] = array(
	'title' => __('admin.page_title_form_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon'),
			'link' =>  base_url('admincontrol/form_coupon'),
		),
	),
);


$pageSetting['admincontrol_form_coupon_manage'] = array(
	'title' => __('admin.page_title_form_coupon_manage'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon'),
			'link' =>  base_url('admincontrol/listproduct'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon_manage'),
			'link' =>  base_url('admincontrol/form_coupon_manage'),
		),
	),
);


$pageSetting['admincontrol_mywallet'] = array(
	'title' => __('admin.page_title_mywallet'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mywallet'),
			'link' =>  base_url('admincontrol/mywallet'),
		),
	),
);


$pageSetting['admincontrol_wallet_withdraw'] = array(
	'title' => __('admin.page_title_wallet_withdraw'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_wallet_withdraw'),
			'link' =>  base_url('admincontrol/wallet_withdraw'),
		),
	),
);


$pageSetting['incomereport_index'] = array(
	'title' => __('admin.page_title_admin_statistics'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.page_title_admin_statistics'),
			'link' =>  base_url('incomereport'),
		),
	),
);



$pageSetting['reportController_admin_statistics'] = array(
	'title' => __('admin.page_title_admin_statistics_graph'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.page_title_admin_statistics_graph'),
			'link' =>  base_url('reportController/admin_statistics'),
		),
	),
);


$pageSetting['admincontrol_registration_builder'] = array(
	'title' => __('admin.page_title_registration_builder'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_registration_builder'),
			'link' =>  base_url('admincontrol/registration_builder'),
		),
	),
);


$pageSetting['pagebuilder_index'] = array(
	'title' => __('admin.page_title_pagebuilder'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_pagebuilder'),
			'link' =>  base_url('pagebuilder'),
		),
	),
);


$pageSetting['pagebuilder_addtheme'] = array(
	'title' => __('admin.page_title_addtheme'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_addtheme'),
			'link' =>  base_url('pagebuilder/addtheme'),
		),
	),
);



$pageSetting['pagebuilder_addpage'] = array(
	'title' => __('admin.page_title_addpage'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_addpage'),
			'link' =>  base_url('pagebuilder/addpage'),
		),
	),
);



$pageSetting['admincontrol_paymentsetting'] = array(
	'title' => __('admin.page_title_configuration'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_configuration'),
			'link' =>  base_url('admincontrol/paymentsetting'),
		),
	),
);

$pageSetting['admincontrol_countries_and_states'] = array(
	'title' => __('admin.countries_and_states'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.useful_links'),
		),
		array(
			'title' =>  __('admin.countries_and_states'),
			'link' =>  base_url('admincontrol/countries_and_states'),
		),
	),
);


$pageSetting['admincontrol_mlm_settings'] = array(
	'title' => __('admin.page_title_mlm_settings'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mlm_settings'),
			'link' =>  base_url('admincontrol/mlm_settings'),
		),
	),
);


$pageSetting['admincontrol_mlm_levels'] = array(
	'title' => __('admin.page_title_mlm_levels'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mlm_levels'),
			'link' =>  base_url('admincontrol/mlm_levels'),
		),
	),
);


$pageSetting['admincontrol_install_new_version'] = array(
	'title' => __('admin.page_title_install_new_version'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_install_new_version'),
			'link' =>  base_url('admincontrol/install_new_version'),
		),
	),
);


$pageSetting['admincontrol_mails'] = array(
	'title' => __('admin.page_title_mails'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_mails'),
			'link' =>  base_url('admincontrol/mails'),
		),
	),
);


$pageSetting['admincontrol_mails_edit'] = array(
	'title' => __('admin.page_title_mails_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mails_edit'),
			'link' =>  base_url('admincontrol/mails'),
		),
	),
);


$pageSetting['admincontrol_backup'] = array(
	'title' => __('admin.page_title_backup'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_backup'),
			'link' =>  base_url('admincontrol/backup'),
		),
	),
);


$pageSetting['admincontrol_language'] = array(
	'title' => __('admin.page_title_language'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_language'),
			'link' =>  base_url('admincontrol/language'),
		),
	),
);


$pageSetting['admincontrol_translation_edit'] = array(
	'title' => __('admin.page_title_translation_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_language'),
			'link' =>  base_url('admincontrol/language'),
		),
		array(
			'title' =>  __('admin.page_title_translation_edit'),
			'link' =>  base_url('admincontrol/translation_edit'),
		),
	),
);



$pageSetting['admincontrol_translation'] = array(
	'title' => __('admin.page_title_edit_translation'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_language'),
			'link' =>  base_url('admincontrol/language'),
		),
		array(
			'title' =>  __('admin.page_title_edit_translation'),
			'link' =>  ('#'),
		),
	),
);



$pageSetting['admincontrol_currency_list'] = array(
	'title' => __('admin.page_title_currency_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_currency_list'),
			'link' =>  base_url('admincontrol/currency_list'),
		),
	),
);



$pageSetting['admincontrol_currency_edit'] = array(
	'title' => __('admin.page_title_currency_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_currency_list'),
			'link' =>  base_url('admincontrol/currency_list'),
		),
		array(
			'title' =>  __('admin.page_title_currency_edit'),
			'link' =>  base_url('admincontrol/currency_edit'),
		),
	),
);


$pageSetting['admincontrol_front_template'] = array(
	'title' => __('admin.page_title_front_template'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_front_template'),
			'link' =>  base_url('admincontrol/front_template'),
		),
	),
);


$pageSetting['admincontrol_editProfile'] = array(
	'title' => __('admin.page_title_editProfile'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_editProfile'),
			'link' =>  base_url('admincontrol/editProfile'),
		),
	),
);


$pageSetting['admincontrol_changePassword'] = array(
	'title' => __('admin.page_title_changePassword'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_changePassword'),
			'link' =>  base_url('admincontrol/changePassword'),
		),
	),
);


$pageSetting['admincontrol_update'] = array(
	'title' => __('admin.page_title_site_update'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_site_update'),
			'link' =>  base_url('admincontrol/update'),
		),
	),
);


$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);



$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);


$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);


$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);



$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);


$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);



$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);


$pageSetting[''] = array(
	'title' => __('admin.page_title_'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_'),
			'link' =>  base_url(''),
		),
	),
);





/* USER CONTROLS */

$pageSetting['usercontrol_dashboard'] = array(
	'title' => __('user.welcome_user') ."<strong>".$userdetails['username']."</strong>",
);

$pageSetting['usercontrol_contact_us'] = array(
	'title' => __('user.contact-us'),
);

$pageSetting['usercontrol_tutorial'] = array(
	'title' => __('user.page_title_tutorial'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_tutorial'),
			'link' =>  base_url('#'),
		),
	),
);

$pageSetting['integration_user_integration_tools'] = array(
	'title' => __('user.page_title_user_integration_tools'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_user_integration_tools'),
			'link' =>  base_url('integration/user_integration_tools'),
		),
	),
);

$pageSetting['integration_user_orders'] = array(
	'title' => __('user.page_title_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_orders'),
			'link' =>  base_url('integration/user_orders'),
		),
	),
);


$pageSetting['integration_click_logs'] = array(
	'title' => __('user.page_title_logs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_logs'),
			'link' =>  base_url('integration/click_logs'),
		),
	),
);


$pageSetting['usercontrol_listproduct'] = array(
	'title' => __('user.page_title_listproduct'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_listproduct'),
			'link' =>  base_url('usercontrol/listproduct'),
		),
	),
);


$pageSetting['usercontrol_purchase_plan'] = array(
	'title' => __('user.page_title_purchase_plan'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_purchase_plan'),
			'link' =>  base_url('usercontrol/purchase_plan'),
		),
	),
);


$pageSetting['membership_buy_membership'] = array(
	'title' => __('user.page_title_buy_membership'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_purchase_plan'),
			'link' =>  base_url('usercontrol/purchase_plan'),
		),
		array(
			'title' =>  __('user.page_title_buy_membership'),
			'link' =>  '',
		),
	),
);

$pageSetting['usercontrol_form'] = array(
	'title' => __('user.page_title_form'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_form'),
			'link' =>  base_url('usercontrol/form'),
		),
	),
);


$pageSetting['usercontrol_listbuyaffiproduct'] = array(
	'title' => __('user.page_title_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_orders'),
			'link' =>  base_url('usercontrol/listbuyaffiproduct'),
		),
	),
);

$pageSetting['usercontrol_all_transaction'] = array(
	'title' => __('user.page_title_all_trans_user'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_all_trans_user'),
			'link' =>  base_url('usercontrol/all_transaction'),
		),
	),
);

$pageSetting['usercontrol_uncompleted_payments'] = array(
	'title' => __('user.uncompleted_payments'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.uncompleted_payments'),
			'link' =>  base_url('admincontrol/uncompleted_payments'),
		),
		
	),
);

$pageSetting['usercontrol_mywallet'] = array(
	'title' => __('user.page_title_mywallet'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_mywallet'),
			'link' =>  base_url('usercontrol/mywallet'),
		),
	),
);


$pageSetting['usercontrol_wallet_withdraw'] = array(
	'title' => __('user.page_title_wallet_withdraw'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_wallet_withdraw'),
			'link' =>  base_url('usercontrol/wallet_withdraw'),
		),
	),
);

$pageSetting['usercontrol_my_deposits'] = array(
	'title' => __('user.my_deposits'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_my_deposits'),
			'link' =>  base_url('usercontrol/my_deposits'),
		),
	),
);

$pageSetting['usercontrol_deposit_details'] = array(
	'title' => __('user.deposit_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_my_deposits'),
			'link' =>  base_url('usercontrol/my_deposits'),
		),
		array(
			'title' =>  __('user.deposit_details'),
			'link' =>  'javascript:void(0)',
		),
	),
);

$pageSetting['usercontrol_addpayment'] = array(
	'title' => __('user.page_title_addpayment'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_addpayment'),
			'link' =>  base_url('usercontrol/addpayment'),
		),
	),
);


$pageSetting['ReportController_user_transaction'] = array(
	'title' => __('user.page_title_user_transaction'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_user_transaction'),
			'link' =>  base_url('reportController/user_transaction'),
		),
	),
);


$pageSetting['ReportController_user_statistics'] = array(
	'title' => __('user.page_title_graph_user_statistics'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_graph_user_statistics'),
			'link' =>  base_url('reportController/user_statistics'),
		),
	),
);

$pageSetting['incomereport_statistics'] = array(
	'title' => __('user.page_title_incomereport_statistics'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_incomereport_statistics'),
			'link' =>  base_url('incomereport/statistics'),
		),
	),
);

$pageSetting['usercontrol_managereferenceusers'] = array(
	'title' => __('user.page_title_managereferenceusers'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_managereferenceusers'),
			'link' =>  base_url('usercontrol/managereferenceusers'),
		),
	),
);

$pageSetting['usercontrol_myreferal'] = array(
	'title' => __('user.page_title_reffer_tree'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_reffer_tree'),
			'link' =>  base_url('usercontrol/myreferal'),
		),
	),
);

$pageSetting['usercontrol_userslisttree'] = array(
	'title' => __('user.page_title_userslisttree'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_userslisttree'),
			'link' =>  base_url('usercontrol/userslisttree'),
		),
	),
);


$pageSetting['usercontrol_editProfile'] = array(
	'title' => __('user.page_title_editProfile'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_editProfile'),
			'link' =>  base_url('usercontrol/editProfile'),
		),
	),
);



$pageSetting['usercontrol_changePassword'] = array(
	'title' => __('user.page_title_changePassword'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_changePassword'),
			'link' =>  base_url('usercontrol/changePassword'),
		),
	),
);

$pageSetting['admincontrol_award_level'] = array(
	'title' => __('admin.page_title_award_level'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_award_level'),
			'link' =>  base_url('admincontrol/award_level'),
		),
	),
);

$pageSetting['admincontrol_create_award_level'] = array(
	'title' => __('admin.page_title_create_award_level'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_award_level'),
			'link' =>  base_url('admincontrol/award_level'),
		),
		array(
			'title' =>  __('admin.page_title_create_award_level'),
			'link' =>  '',
		),
	),
);


$pageSetting['admincontrol_update_award_level'] = array(
	'title' => __('admin.page_title_update_award_level'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_award_level'),
			'link' =>  base_url('admincontrol/award_level'),
		),
		array(
			'title' =>  __('admin.page_title_update_award_level'),
			'link' =>  '',
		),
	),
);


$pageSetting['admincontrol_system_status'] = array(
	'title' => __('admin.page_title_system_status'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_system_status'),
			'link' =>  base_url('admincontrol/system_status'),
		),
	),
);

$pageSetting['usercontrol_notification'] = array(
	'title' => __('user.page_title_notification'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_notification'),
			'link' =>  base_url('usercontrol/notification'),
		),
	),
);



$pageSetting['usercontrol_store_markettools'] = array(
	'title' => __('user.page_title_store_markettools'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_my_share_links'),
			'link' =>  base_url('usercontrol/store_markettools'),
		),
	),
);



$pageSetting['usercontrol_store_orders'] = array(
	'title' => __('user.page_title_store_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('user.page_title_store_orders'),
			'link' =>  base_url('usercontrol/store_orders'),
		),
	),
);



$pageSetting['usercontrol_store_logs'] = array(
	'title' => __('user.page_title_store_logs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_logs'),
			'link' =>  base_url('usercontrol/store_logs'),
		),
	),
);



$pageSetting['usercontrol_my_network'] = array(
	'title' => __('user.page_title_my_network'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_my_network'),
			'link' =>  base_url('usercontrol/my_network'),
		),
	),
);


$pageSetting['ReportController_user_reports'] = array(
	'title' => __('user.page_title_user_reports'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_user_reports'),
			'link' =>  base_url('reportController/user_reports/'),
		),
	),
);

$pageSetting['admincontrol_store_markettools'] = array(
	'title' => __('user.all_markettools'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.all_markettools'),
			'link' =>  base_url('admincontrol/store_markettools/'),
		),
	),
);


$pageSetting['admincontrol_store_orders'] = array(
	'title' => __('user.my_all_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('user.my_all_orders'),
			'link' =>  base_url('admincontrol/store_orders'),
		),
	),
);



$pageSetting['admincontrol_store_logs'] = array(
	'title' => __('admin.my_all_logs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_activity'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.my_all_logs'),
			'link' =>  base_url('admincontrol/store_logs'),
		),
	),
);



$pageSetting['admincontrol_theme_setting'] = array(
	'title' => __('admin.page_title_theme_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_theme_setting'),
			'link' =>  base_url('admincontrol/theme_setting'),
		),
	),
);


$pageSetting['usercontrol_store_products'] = array(
	'title' => __('user.page_title_store_products'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_products'),
			'link' =>  base_url('usercontrol/store_products'),
		),
	),
);

$pageSetting['usercontrol_sales_products'] = array(
	'title' => __('user.page_title_store_sales_products'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_sales_products'),
			'link' =>  base_url('usercontrol/sales_products'),
		),
	),
);


$pageSetting['usercontrol_create'] = array(
	'title' => __('user.page_title_store_sales_products'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_sales_products'),
			'link' =>  base_url('usercontrol/sales_products'),
		),
		array(
			'title' =>  __('user.create_product_campaign'),
			'link' =>  base_url('usercontrol/create'),
		),
	),
);


$pageSetting['usercontrol_update_sale_products'] = array(
	'title' => __('user.page_title_store_sales_products'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_sales_products'),
			'link' =>  base_url('usercontrol/sales_products'),
		),
		array(
			'title' =>  __('user.update_product_campaign'),
			'link' =>  base_url('usercontrol/update_sale_products'),
		),
	),
);


$pageSetting['usercontrol_store_dashboard'] = array(
	'title' => __('user.page_title_store_dashboard'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
	),
);

$pageSetting['usercontrol_store_edit_product'] = array(
	'title' => __('user.page_title_store_edit_product'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_products'),
			'link' =>  base_url('usercontrol/store_products'),
		),
		array(
			'title' =>  __('user.page_title_store_edit_product'),
			'link' =>  base_url('usercontrol/store_edit_product'),
		),
	),
);

$pageSetting['usercontrol_store_setting'] = array(
	'title' => __('user.page_title_store_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_setting'),
			'link' =>  base_url('usercontrol/store_setting'),
		),
	),
);

$pageSetting['usercontrol_store_venodr_orders'] = array(
	'title' => __('user.page_title_store_venodr_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_venodr_orders'),
			'link' =>  base_url('usercontrol/store_venodr_orders'),
		),
	),
);

$pageSetting['usercontrol_listclients'] = array(
	'title' => __('user.page_title_vendor_listclients'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_listclients'),
			'link' =>  base_url('usercontrol/listclients'),
		),
	),
);

$pageSetting['usercontrol_updateproduct'] = array(
	'title' => __('user.page_title_updateproduct'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_listproduct'),
			'link' =>  base_url('usercontrol/listproduct'),
		),
		array(
			'title' =>  __('user.page_title_updateproduct'),
			'link' =>  base_url('usercontrol/updateproduct'),
		),
	),
);


$pageSetting['usercontrol_videoupload'] = array(
	'title' => __('user.page_title_videoupload'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_videoupload'),
			'link' =>  base_url('usercontrol/videoupload'),
		),
	),
);

$pageSetting['usercontrol_productupload'] = array(
	'title' => __('admin.page_title_productupload'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_productupload'),
			'link' =>  base_url('usercontrol/productupload'),
		),
	),
);



$pageSetting['usercontrol_store_coupon_manage'] = array(
	'title' => __('user.page_title_edit_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_coupons'),
			'link' =>  base_url('usercontrol/store_coupon'),
		),
		array(
			'title' =>  __('user.page_title_edit_coupon'),
			'link' =>  ('#'),
		),
	),
);


$pageSetting['usercontrol_store_coupon'] = array(
	'title' => __('user.page_title_product_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
			'title' =>  __('user.page_title_store_coupons'),
			'link' =>  base_url('usercontrol/store_coupon'),
		),
	),
);



$pageSetting['usercontrol_store_form'] = array(
	'title' => __('user.page_title_form'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_form'),
			'link' =>  base_url('usercontrol/store_form'),
		),
	),
);


$pageSetting['usercontrol_store_form_manage'] = array(
	'title' => __('user.page_title_form_manage'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_form'),
			'link' =>  base_url('usercontrol/store_form'),
		),
		array(
			'title' =>  __('user.page_title_form_manage'),
			'link' =>  base_url('usercontrol/store_form_manage'),
		),
	),
);

$pageSetting['usercontrol_store_form_coupon'] = array(
	'title' => __('admin.page_title_form_coupon'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon'),
			'link' =>  base_url('usercontrol/store_form_coupon'),
		),
	),
);


$pageSetting['usercontrol_store_form_coupon_manage'] = array(
	'title' => __('admin.page_title_form_coupon_manage'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon'),
			'link' =>  base_url('usercontrol/store_form_coupon'),
		),
		array(
			'title' =>  __('admin.page_title_form_coupon_manage'),
			'link' =>  base_url('usercontrol/store_form_coupon_manage'),
		),
	),
);

$pageSetting['usercontrol_store_contact'] = array(
	'title' => __('admin.page_title_store_contact'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_contact'),
			'link' =>  base_url('usercontrol/store_contact'),
		),		
	),
);


$pageSetting['integration_integration_category'] = array(
	'title' => __('admin.integration_category'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.integration_category'),
			'link' =>  base_url('integration/integration_category'),
		),
	),
);


$pageSetting['integration_integration_category_add'] = array(
	'title' => __('admin.page_title_integration_category_add'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.integration_category'),
			'link' =>  base_url('integration/integration_category'),
		),
		array(
			'title' =>  __('admin.page_title_integration_category_add'),
			'link' =>  base_url('integration/integration_category_add'),
		),
	),
);

$pageSetting['usercontrol_mlm_levels'] = array(
	'title' => __('user.page_title_vendor_mlm_levels'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_mlm_levels'),
			'link' =>  base_url('usercontrol/mlm_levels'),
		),
	),
);

$pageSetting['usercontrol_external_vendor_orders'] = array(
	'title' => __('user.page_title_vendor_external_vendor_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_external_vendor_orders'),
			'link' =>  base_url('usercontrol/external_vendor_orders'),
		),
	),
);

$pageSetting['usercontrol_mlm_settings'] = array(
	'title' => __('user.page_title_vendor_mlm_settings'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_mlm_settings'),
			'link' =>  base_url('usercontrol/mlm_settings'),
		),
	),
);

$pageSetting['usercontrol_wallet_setting'] = array(
	'title' => __('user.page_title_vendor_wallet_settings'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_markettools'),
			'link' => ('#'),
		),
		array(
			'title' =>  __('user.page_title_vendor_wallet_settings'),
			'link' =>  base_url('usercontrol/wallet_setting'),
		),
	),
);

$pageSetting['usercontrol_share_sales_setting'] = array(
	'title' => __('user.page_title_vendor_share_sales_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_markettools'),
			'link' => ('#'),
		),
		array(
			'title' =>  __('user.page_title_vendor_share_sales_setting'),
			'link' =>  base_url('usercontrol/share_sales_setting'),
		),
	),
);

$pageSetting['usercontrol_programs'] = array(
	'title' => __('user.page_title_programs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_programs'),
			'link' =>  base_url('usercontrol/programs'),
		),
	),
);


$pageSetting['usercontrol_programs_form'] = array(
	'title' => __('user.page_title_edit_programs'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_programs'),
			'link' =>  base_url('usercontrol/programs'),
		),
		array(
			'title' =>  __('user.page_title_edit_programs'),
			'link' =>  base_url('usercontrol/programs_form'),
		),
	),
);

$pageSetting['usercontrol_integration_tools'] = array(
	'title' => __('user.page_title_integration_tools'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_integration_tools'),
			'link' =>  base_url('usercontrol/integration_tools'),
		),
	),
);

$pageSetting['usercontrol_purchase_plan_expire'] = array(
	'title' => __('user.page_title_purchase_plan_expire'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_purchase_plan_expire'),
			'link' =>  base_url('usercontrol/purchase_plan_expire'),
		),
	),
);


$pageSetting['usercontrol_purchase_history'] = array(
	'title' => __('user.page_title_purchase_history'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_purchase_history'),
			'link' =>  base_url('usercontrol/purchase_history'),
		),
	),
);



$pageSetting['usercontrol_integration_tools_form'] = array(
	'title' => __('user.page_title_edit_ads'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_integration_tools'),
			'link' =>  base_url('usercontrol/integration_tools'),
		),
		array(
			'title' =>  __('user.page_title_edit_ads'),
			'link' =>  base_url('usercontrol/integration_tools_form/link_ads'),
		),
	),
);


$pageSetting['usercontrol_integration'] = array(
	'title' => __('user.page_title_usercontrol_plugins'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_usercontrol_plugins'),
			'link' =>  base_url('usercontrol/integration'),
		),
	),
);


$pageSetting['usercontrol_instructions'] = array(
	'title' => __('user.page_title_usercontrol_instructions'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_usercontrol_plugins'),
			'link' =>  base_url('usercontrol/integration'),
		),
		array(
			'title' =>  __('user.page_title_usercontrol_instructions'),
			'link' =>  '',
		),
	),
);

$pageSetting['admincontrol_market_tools_setting'] = array(
	'title' => __('admin.admincontrol_market_tools_setting'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_addons'),
			'link' =>  base_url('admincontrol/addons'),
		),
		array(
			'title' =>  __('admin.page_title_postback'),
			'link' =>  base_url('admincontrol/market_tools_setting'),
		)
	),
);

$pageSetting['usercontrol_integration_mywallet'] = array(
	'title' => __('user.usercontrol_integration_mywallet'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.usercontrol_integration_mywallet'),
			'link' =>  base_url('usercontrol/integration_mywallet'),
		),
	),
);

$pageSetting['usercontrol_integration_orders'] = array(
	'title' => __('user.usercontrol_integration_orders'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.usercontrol_integration_orders'),
			'link' =>  base_url('usercontrol/integration_orders'),
		),
	),
);

$pageSetting['usercontrol_wallet_requests_list'] = array(
	'title' => __('user.usercontrol_wallet_requests_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_mywallet'),
			'link' =>  base_url('usercontrol/mywallet'),
		),
		array(
			'title' =>  __('user.usercontrol_wallet_requests_list'),
			'link' =>  base_url('usercontrol/wallet_requests_list'),
		),
	),
);

$pageSetting['usercontrol_membership_purchase_details'] = array(
	'title' => __('user.page_title_membership_purchase_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_purchase_history'),
			'link' =>  base_url('usercontrol/purchase_history'),
		),
		array(
			'title' =>  __('user.page_title_membership_purchase_details'),
			'link' =>  base_url('usercontrol/membership_purchase_details'),
		),
	),
);

$pageSetting['usercontrol_wallet_requests_details'] = array(
	'title' => __('user.usercontrol_wallet_requests_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.usercontrol_wallet_requests_list'),
			'link' =>  base_url('usercontrol/wallet_requests_list'),
		),
		array(
			'title' =>  __('user.usercontrol_wallet_requests_details'),
			'link' =>  base_url('usercontrol/wallet_requests_list'),
		),
		
	),
);


$pageSetting['admincontrol_wallet_requests_list'] = array(
	'title' => __('admin.admincontrol_wallet_requests_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mywallet'),
			'link' =>  base_url('admincontrol/mywallet'),
		),
		array(
			'title' =>  __('admin.admincontrol_wallet_requests_list'),
			'link' =>  base_url('admincontrol/wallet_requests_list'),
		),
	),
);

$pageSetting['admincontrol_vendor_deposits'] = array(
	'title' => __('admin.admincontrol_admincontrol_deposit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_saas'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.admincontrol_admincontrol_deposit'),
			'link' =>  base_url('admincontrol/vendor_deposits'),
		),
	),
);

$pageSetting['admincontrol_vendor_deposit_details'] = array(
	'title' => __('admin.admincontrol_vendor_deposit_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_mywallet'),
			'link' =>  base_url('admincontrol/mywallet'),
		),
		array(
			'title' =>  __('admin.admincontrol_admincontrol_deposit'),
			'link' =>  base_url('admincontrol/vendor_deposits'),
		),
		array(
			'title' =>  __('admin.admincontrol_vendor_deposit_details'),
			'link' =>  'javascript:void(0)',
		),
	),
);

$pageSetting['admincontrol_payment_gateway_edit'] = array(
	'title' => __('admin.page_title_payment_gateway_edit'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_saas'),
			'link' =>  ('#'),
		),
		array(
			'title' =>  __('admin.payment_gateways'),
			'link' =>  base_url('admincontrol/payment_gateway'),
		),
		array(
			'title' =>  __('admin.page_title_payment_gateway_edit'),
			'link' =>  '#',
		),
	),
);

$pageSetting['admincontrol_wallet_requests_details'] = array(
	'title' => __('admin.admincontrol_wallet_requests_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.admincontrol_wallet_requests_list'),
			'link' =>  base_url('admincontrol/wallet_requests_list'),
		),
		array(
			'title' =>  __('admin.admincontrol_wallet_requests_details'),
			'link' =>  base_url('admincontrol/wallet_requests_list'),
		),
		
	),
);


$pageSetting['admincontrol_dashboard'] = array(
	'title' => __('admin.menu_admin_panel') 

);

$pageSetting['admincontrol_contactus'] = array(
	'title' => __('admin.contactus'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		
		array(
			'title' =>  __('admin.page_title_notification'),
			'link' =>  base_url('admincontrol/notification'),
		),

		array(
			'title' =>  __('admin.contactus'),
			
		),
	),
);


$pageSetting['Productsales_create'] = array(
	'title' => __('admin.create_sale_page_product'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.product_campaign'),
			'link' =>  base_url('Productsales/index'),
		),
		array(
			'title' =>  __('admin.create_sale_page_product'),
			'link' =>  base_url('Productsales/create'),
		),
	),
);

$pageSetting['Productsales_index'] = array(
	'title' => __('admin.product_campaign'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.product_campaign'),
			'link' =>  base_url('Productsales/index'),
		)
	),
);

$pageSetting['Productsales_update'] = array(
	'title' => __('admin.product_campaign'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.page_title_store_dashboard'),
			'link' =>  base_url('admincontrol/store_dashboard'),
		),
		array(
			'title' =>  __('admin.product_campaign'),
			'link' =>  base_url('Productsales/index'),
		),
		array(
			'title' =>  __('admin.create_sale_page_product'),
			'link' =>  base_url('Productsales/update'),
		)
	),
);
$pageSetting['admincontrol_todolist'] = array(
	'title' => __('admin.to_do_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.to_do_list'),
			'link' =>  base_url('admincontrol/todolist'),
		)
	),
);

$pageSetting['admincontrol_ticketssubject'] = array(
	'title' => __('admin.ticket_subject'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_tickets'),
			'link' =>  base_url('admincontrol/tickets'),
		),
		array(
			'title' =>  __('admin.ticket_subject'),
			'link' =>  base_url('admincontrol/ticketssubject'),
		)
	),
);
$pageSetting['admincontrol_tickets'] = array(
	'title' => __('admin.to_do_list'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.useful_links'),
		),
		array(
			'title' =>  __('admin.menu_tickets'),
			'link' =>  base_url('admincontrol/tickets'),
		)
	),
);
$pageSetting['admincontrol_ticketdetails'] = array(
	'title' => __('admin.ticket_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_tickets'),
			'link' =>  base_url('admincontrol/tickets'),
		),
		array(
			'title' =>  __('admin.ticket_details'),
			'link' =>  '',
		)
	),
);
$pageSetting['admincontrol_ticketcreate'] = array(
	'title' => __('admin.create_new_ticket'),
	'breadcrumb' => array(
		array(
			'title' =>  __('admin.page_title_dashboard'),
			'link' =>  base_url('admincontrol/dashboard'),
		),
		array(
			'title' =>  __('admin.menu_tickets'),
			'link' =>  base_url('admincontrol/tickets'),
		),
		array(
			'title' =>  __('admin.create_new_ticket'),
			'link' =>  '',
		)
	),
);


$pageSetting['usercontrol_tickets'] = array(
	'title' => __('user.page_title_tickets'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.title_tickets'),
			'link' =>  base_url('usercontrol/tickets'),
		),
	),
);
$pageSetting['usercontrol_createticket'] = array(
	'title' => __('user.page_title_tickets'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.title_tickets'),
			'link' =>  base_url('usercontrol/tickets'),
		),
		array(
			'title' =>  __('user.create_new_ticket'),
			'link' => '',
		),
	),
);

$pageSetting['usercontrol_ticketdetails'] = array(
	'title' => __('user.page_title_tickets'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.title_tickets'),
			'link' =>  base_url('usercontrol/tickets'),
		),
		array(
			'title' =>  __('user.ticket_details'),
			'link' => '',
		),
	),
);


$pageSetting['usercontrol_payment_details'] = array(
	'title' => __('user.payment_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.payment_details'),
			'link' =>  base_url('usercontrol/payment_details'),
		),
	),
);

$pageSetting['usercontrol_manage_review'] = array(
	'title' => __('user.manage_review'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
		'title' =>  __('user.page_title_store_products'),
		'link' =>  base_url('usercontrol/store_products'),
		),
		array(
			'title' =>  __('user.manage_review'),
			'link' =>  base_url('usercontrol/manage_review'),
		),
	),
);

$pageSetting['usercontrol_vieworder'] = array(
	'title' => __('user.manage_review'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_vendor_store'),
			'link' =>  base_url('usercontrol/store_dashboard'),
		),
		array(
		'title' =>  __('user.page_title_store_orders'),
		'link' =>  base_url('usercontrol/store_venodr_orders'),
		),
		array(
		'title' =>  __('user.page_title_view_order'),
		'link' =>  ('#'),
		),
	),
);


$pageSetting['usercontrol_contactus'] = array(
	'title' => __('user.page_title_contactus_details'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_brad_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_notification'),
			'link' =>  base_url('usercontrol/notification'),
		),
	),
);

$pageSetting['usercontrol_orders_notifications'] = array(
	'title' => __('admin.notifications'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_notification'),
			'link' =>  base_url('usercontrol/notification'),
		),
		array(
			'title' =>  __('user.page_title_order_notification_details'),
			'link' =>  ('#'),
		),
	),
);

$pageSetting['usercontrol_click_notification'] = array(
	'title' => __('admin.notifications'),
	'breadcrumb' => array(
		array(
			'title' =>  __('user.page_title_dashboard'),
			'link' =>  base_url('usercontrol/dashboard'),
		),
		array(
			'title' =>  __('user.page_title_notification'),
			'link' =>  base_url('usercontrol/click_notification'),
		),
		array(
			'title' =>  __('user.page_title_notification_details'),
			'link' =>  ('#'),
		),
	),
);



