<?php
if($_GLOBALS['site_settings']['site_id']!=3) $posted['site_id']=$_GLOBALS['site_settings']['site_id'];

$all_staff_list=getStaffList(false);
$active_staff_list=getStaffList();
$inactive_staff_list=array_diff_assoc($all_staff_list, $active_staff_list);
?>