<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/header.inc.php';

include_once "options_functions.inc.php";
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">Site Options</div>
		</td>
		<td class="pageMenu" width="80%"><div align="right"><a href="/webadmin/options">List All Options</a></div></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>

<div id="optcontent">
<div><h2 style="text-align:center;">Add/Edit Options</h2></div>
<div align="center" width="95%">
<?php

$user_details = $_GLOBALS['staff'];
$messages     = array();
$time_now     = time();
$posted       = array_merge($_GET, $_POST);

// Handle action request
if ('do_edit' == $posted['action'] && file_exists($posted['opt_type'] . '_options.inc.php')) {
    $action = 'do_edit';
    include $posted['opt_type'] . '_options.inc.php';
    $posted['action'] = 'list';
} elseif ('do_add' == $posted['action'] && file_exists($posted['opt_type'] . '_options.inc.php')) {
    $action = 'do_add';
    include $posted['opt_type'] . '_options.inc.php';
    $posted['action'] = 'list';
}

if (count($messages) > 0) {
    foreach ($messages as $msg) {
        echo $msg . "<br />\n";
    }
}

if ('edit' == $posted['action'] && file_exists($posted['opt_type'] . '_options.inc.php')) {
    $action = 'edit';
    include $posted['opt_type'] . '_options.inc.php';
} elseif ('add' == $posted['action'] && file_exists($posted['opt_type'] . '_options.inc.php')) {
    $action = 'add';
    include $posted['opt_type'] . '_options.inc.php';
} else {
    $action = 'list';
    include 'site_options.inc.php';
    include 'company_options.inc.php';
}
?>
</div>
<!-- footer.inc.php -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>