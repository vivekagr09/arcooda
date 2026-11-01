<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<?php
$id = intval($posted['id']);
$data = customTemplateFromDatabase($id);
?>
<form action=".?id=<?= $id ?>&action=do_edit" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . "/addedit.tpl.php";
?>
<center>
	<input type="hidden" name="id" value="<?= $data['id']?>">
	<input type="hidden" name="action" value="do_edit">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>
</form>
<?php

include_once __DIR__ . "/footer.inc.php";
?>