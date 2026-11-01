<?php
/**
 * @var string[] $all_staff_list
 * @var int      $asset_id
 * @var string   $asset_type
 * @var string   $section_singular
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'delete';

$asset = assetFromDatabase($asset_type, $asset_id);

?>
<?=formatHTMLTitle('Delete ' . $section_singular)?>

Are you sure you want to delete this <?=strtolower($section_singular)?>?<br />
<br />
<a href=".?<?=htmlspecialchars(http_build_query(['action' => 'view'] + $_GET))?>" target="_blank"><img src=".?<?=htmlspecialchars(http_build_query(['action' => 'thumb'] + $_GET))?>" /></a><br />
<br />
<table class="table_fields">
	<tr>
		<th>Caption</th>
		<td><?php hecho($asset['asset_caption'])?></td>
	</tr>
	<tr>
		<th>Filename</th>
		<td><?php hecho($asset['fileorig'])?></td>
	</tr>
	<tr>
		<th>Added By</th>
		<td><?php hecho($all_staff_list[$asset['staff_id']])?></td>
	</tr>
	<tr>
		<th>Date Added</th>
		<td><?=date('d/m/Y', $asset['asset_time'])?></td>
	</tr>
</table>

<br />

<form accept-charset="utf-8" autocomplete="off" method="post" action=" index.php?action=do_delete&<?= http_build_query($_GET) ?>" >
	<input type="submit" value="Delete" />
	<input type="button" value="Cancel" onclick="<?=htmlspecialchars((array_key_exists('return_message', $_GET)) ? 'window.close()' : ('document.location = ' . jecho('.?' . http_build_query(['action' => 'list'] + $_GET), 1)))?>" />
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
