<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id     = 0;
$action = 'add';
$product_screenshots=[];
$marker=[];
$item = array(
    'is_parent' => 'n',
);

?>
<h1 style="text-align:center;">Add New Product - <?= htmlspecialchars(date(_('d/m/Y'))) ?></h1>

<form action=".?action=do_add" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="" />
<?php

include __DIR__ . '/addedit.tpl.php';

?>
    <div style="text-align:center;">
        <input type="submit" value="Add" /><br />
        <i><span style="color:red"><i><small>*</small></span> - Required Field</i>
    </div>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
