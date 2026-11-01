<!-- addedit.tpl -->

<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}
if ('add' == $action) {
    $form_action = 'do_add';
}

?>
<script type="text/javascript">
//<![CDATA[
function pageValidate() {
	return true;
}
//]]>
</script>
<?php
if($_GET['status'] == true) {
    include __DIR__ . "/update_status.tpl.php";
}
include __DIR__ . "/changes.tpl.php";
if ('add' != $action) {
    include __DIR__ . "/lock.tpl.php";
}
include __DIR__ . "/content.tpl.php";
// include __DIR__ . "/images.tpl.php";
include __DIR__ . "/_images.tpl.php";
//include __DIR__ . "/tags.tpl.php";
include __DIR__ . "/metas.tpl.php";
include __DIR__ . "/comments.tpl.php";
include __DIR__ . "/active_status.tpl.php";

?>
<input type="button" value="Submit Changes"<?=($item_lock['staff_id'] == $_GLOBALS['staff']['staff_id'] || 'add' == $action || 1) ? ' onclick="if(pageValidate()) this.form.submit()"' : ' disabled="disabled"'?> />

<!-- /addedit.tpl -->
