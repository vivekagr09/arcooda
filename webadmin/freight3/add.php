<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "add";

$id            = 0;
$item          = array(
	'cubic_divisor' => '5000',
);
$item_zones    = array();
$item_comments = array();

$doadd_url           = $get_vars;
$doadd_url["action"] = "do_add";
$doadd_url           = "index.php?" . http_build_query($doadd_url);
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$("#addForm").submit(function() {
		return true;
	});
});
//]]>
</script>

<form accept-charset="utf-8" autocomplete="off" method="post" id="addForm" action="<?php hecho($doadd_url)?>">
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[id]") ?>" value="" />
<?php

include __DIR__ . '/addedit.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
?>