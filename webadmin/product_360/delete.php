<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('category'); //categoryFromDatabase
moduleLoad('product');

include_once __DIR__ . '/header.inc.php';

global $DB;
$type_id = intval($type_id);
if (!$type_id) {
    return;
}

$data      = categoryFromDatabase($type_id);
$is_parent = $data['is_parent'];
$types     = productGetOptionList('type');
$yesno     = array('n' => 'No', 'y' => 'Yes');
?>
<form action="products_category_types.php" method="post">
<table class="wide" align="center" style="width:85%">
	<input type="hidden" name="action" value="do_delete" />
	<input type="hidden" name="id" value="<?=$type_id?>" />
	<tr><td class="left_title">ID :</td><td><?=$data['type_id']?></td></tr>
	<tr><td class="left_title">Name :</td><td><?=$data['name']?></td></tr>
	<tr><td class="left_title">Is Parent :</td><td><?=$yesno[$is_parent]?></td></tr>
	<tr><td class="left_title">Parent Category :</td><td><?=('n' == $is_parent) ? $types[$data['parent']] : '-'?></td></tr>
	<tr><td colspan="2" style="text-align:center;">Are you sure you want to delete this Product Category??</td></tr>
	<tr>
		<td colspan="2" style="text-align:center;">
			<input type="submit" value="Yes" />&nbsp;
			<input type="button" value="No" onclick="window.location='products_category_types.php';" />
		</td>
	</tr>
</table>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
