<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$form_url           = $_GET;
$form_url['action'] = 'edit';
$form_url           = '.?' . http_build_query($action_url);
?>

<form action="<?=htmlspecialchars($form_url)?>" method="post" autocomplete="off" enctype="multipart/form-data">
    <input type="hidden" name="item[id]" value="<?=$id?>" />
<?php
include __DIR__ . '/__details.tpl.php';
include __DIR__ . '/__contacts.tpl.php';
include __DIR__ . '/__distriputor_type.tpl.php';
include __DIR__ . '/__distriputor_map.tpl.php';
include __DIR__ . '/__special_notes.tpl.php';
include __DIR__ . '/__status.tpl.php';
?>
    <br />
    <button type="submit" name="action" value="do_<?=$action?>">Submit Changes</button>
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
