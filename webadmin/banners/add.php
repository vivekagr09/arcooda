<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$add    = true;
$edit   = false;
$id     = 0;
$item = bannerFromDatabase($id);

?>
<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action=".?action=do_add">
<?php
include __DIR__ . '/addedit.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
