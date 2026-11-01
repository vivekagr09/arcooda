<?php
/**
 * @var int $id
 */
?><?php

global $DB;

moduleLoad('staff');

$item = staffFromDatabase($id);
?>
<p>Are you sure you wish to delete <b><?=htmlspecialchars($item['first_name'])?> <?=htmlspecialchars($item['last_name'])?></b>?</p>

<div class="text-center">
	<input type="hidden" name="username" value="<?=htmlspecialchars($item['user'])?>" />
	<input type="hidden" name="action" value="do_delete" />
	<input type="submit" name="add" value="Yes" />
	<input type="reset" name="clear" value="No" onClick="history.back()" />
</div>
