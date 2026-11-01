<?php
// inputs:
// - $ss_id

$ss = $DB->query_first(sprintf(
  "SELECT * FROM `product_screenshots` WHERE `ss_id` = %d",
  $ss_id
), MYSQLI_ASSOC);

/*
$ss_type_list = array();
$query = $DB->query("SELECT `id`, `type` FROM `screenshot_type` ORDER BY `type` ASC");
while ($row = $DB->fetch_assoc($query)) {
  $ss_type_list[$row['id']] = $row['type'];
}
$DB->free_result($query);
*/

/*
$img_file = $_SERVER['DOCUMENT_ROOT'] . $ss['filename'];
if (is_file($img_file) && file_exists($img_file)) {
	$img_info = getImageSize($img_file);
	$img = '<img src="' . $ss['filename'] . '" ' . $img_info[3] . ' alt="' . $ss['caption'] . ' border="1" />';
}
*/
?>
<table class="table_fields">
  <tr>
    <th></th>
    <td><a href="<?php hecho('/' . $ss['filename']) ?>" target="_blank"><img src="<?php hecho('/' . $ss['filename']) ?>" alt="<?php hecho($ss['caption']) ?>" border="1" style="max-width:470px;max-height:124px;" /></a></td>
  </tr>
  <tr>
    <th>SS_ID :</th>
    <td><?php hecho($ss['ss_id']) ?></td>
  </tr>
  <tr>
    <th>Current Picture:</th>
    <td><?php hecho($ss['filename']) ?></td>
  </tr>
  <tr>
    <th>Caption :</th>
    <td><?php hecho($ss['caption']) ?></td>
  </tr>
  <tr>
    <th>Rank :</th>
    <td><?php hecho($ss['rank']) ?></td>
  </tr>
  <tr>
    <th>Date Added :</th>
    <td><?php hecho($ss['date_added']) ?></td>
  </tr>
</table>

<br />

<div style="text-align:center;">
  <b>Do you want to delete this screenshot?</b>
  <br />
  <br />
  <input type="hidden" name="ss_id" value="<?php hecho($ss_id) ?>" />
	<input type="hidden" name="action" value="do_delete" />
  <input type="submit" name="add" value="Yes" />
  <input type="reset" name="clear" value="No" onClick="javascript:history.back()" />
</div>
