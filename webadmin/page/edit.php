<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id            = intval($_REQUEST['id']);
$item          = pageFromDatabase($id);
$item_images   = pageImageSearch(array('page_id' => $id));
//$item_tags     = pageTagsFromDatabase($id);
$item_lock     = pageGetLock($id);
$item_comments = array_reverse(pageCommentSearch(array('page_id' => $id, 'comment_type' => 'admin')));

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url           = 'index.php?' . http_build_query($doedit_url);

$action = 'edit';

?>
<form action="<?php hecho($doedit_url) ?>" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . '/addedit.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
