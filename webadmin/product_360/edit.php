<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$id     = intval($posted['id']);
$action = 'edit';
$item = view360GenericFromDatabase(
    $id,
    "`360_view`"
);
$product_screenshots = view360GenericsFromDatabase(
    $id,
    "`360_images`",
    "id",
    "view_id",
    "ORDER BY `rank` ASC"
);
$marker = view360GenericsFromDatabase(
    $id,
    "`360_marker`",
    "id",
    "view_id",
    "ORDER BY `id` ASC"
);
?>
<h1 style="text-align:center;">Edit 360View - <?= htmlspecialchars($item['name']) ?> - <?= htmlspecialchars(date(_('d/m/Y'))) ?></h1>

<form action=".?action=do_edit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>" />
<?php

include __DIR__ . '/addedit.tpl.php';

?>
    <div style="text-align:center;">
        <input type="submit" value="Update" /><br />
        <i><span style="color:red"><i><small>*</small></span> - Required Field</i>
    </div>
</form>
<?php


include_once __DIR__ . '/footer.inc.php';
