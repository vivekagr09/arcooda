<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id           = '';



$chapter      = array();
$page_id      = $_REQUEST['page_id'];
$page         = pageFromDatabase($page_id);


$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_add_chapter';
$doadd_url           = 'index.php?' . http_build_query($doadd_url);

$action = 'chapter_add';
?>
<center><h1><?= $page['name'] ?></h1></center><br />
<form action="<?= $doadd_url ?>" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . '/chapter_addedit.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
