<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id      = '';
$item    = array(
	'parent_id' => 0,

	'content_type' => "html",
	'name' => '',
	'content' => '',

	'title' => '',
	'keywords' => '',
	'description' => '',
	'slug' => '',
	'modified_unix' => 0,

	'active' => '',
);
$item_images   = array();
$item_tags     = array();
$item_lock     = false;
$item_comments = array();

$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_add';
$doadd_url           = 'index.php?' . http_build_query($doadd_url);

$action = 'add';
?>
<form action="<?= $doadd_url ?>" method="post">
<?php
include __DIR__ . '/addedit.tpl.php';
?>
</form>
<?php
include_once __DIR__ . '/footer.inc.php';
