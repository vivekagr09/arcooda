<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id           = abs(intval($_REQUEST['id']));
if (!$id) {
    die('Invalid Id passed...');
}
$chapter      = pageChapterFromDatabase($id);
$page_id      = $chapter['page_id'];
$page         = pageFromDatabase($page_id);
$chapter_lock = pageChapterGetLock($id);

$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_edit_chapter';
$doadd_url           = 'index.php?' . http_build_query($doadd_url);

$action = 'chapter_edit';
?>
<center><h1><?= $page['name'] ?></h1></center><br />
<form action="<?= $doadd_url ?>" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . '/chapter_addedit.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
