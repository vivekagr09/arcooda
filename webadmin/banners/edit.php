<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$add    = false;
$edit   = true;
$id     = intval($_REQUEST['id']);
$item = bannerFromDatabase($id);

if (!$item) {
    die("Banner with that ID doesn't exist");
}

if ('' != $_REQUEST['changes']) {
?>
<span style="color:red;text-align:center;">
  Following Changes Made :<br />
  <br />
  <?= nl2br($_REQUEST['changes']) ?>
</span>
<?php
}

?>
<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action=".?action=do_edit">
<?php
include __DIR__ . '/addedit.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
