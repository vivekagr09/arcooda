<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "edit";

$id            = intval($posted['id']);
$item          = $section_handler->fromDatabase($id);
$item_zones    = $section_handler->zonesFromDatabase($id);
$item_comments = $section_handler->commentsFromDatabase($id);

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url           = 'index.php?' . http_build_query($doedit_url);
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$("#editForm").submit(function() {
		return true;
	});
});
//]]>
</script>

<form accept-charset="utf-8" autocomplete="off" method="post" id="editForm" action="<?php hecho($doedit_url)?>">
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[id]") ?>" value="<?php hecho($item["id"]) ?>" />
<?php

include __DIR__ . '/addedit.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
?>