<?php
// inputs:
// - $ss_id

$ss = $DB->query_first(sprintf(
  "SELECT * FROM `product_screenshots` WHERE `ss_id` = %d",
  $ss_id
), MYSQLI_ASSOC);

$ss_type_list = array();
$query = $DB->query("SELECT `id`, `type` FROM `screenshot_type` ORDER BY `type` ASC");
while ($row = $DB->fetch_assoc($query)) {
  $ss_type_list[$row['id']] = $row['type'];
}
$DB->free_result($query);
?>
<table class="table_fields">
  <tr>
    <th>Current Picture :</th>
    <td>
      <input type="text" name="old_pic" size="40" value="<?php hecho($ss['filename']) ?>" style="width:80%;" disabled="disabled" />
      <a href="<?php hecho('/webadmin/gogetmemyimage.php?i=' . urlencode($ss['filename'])) ?>">Link</a>
    </td>
  </tr>
  <tr>
    <th>Screenshot :</th>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="part_ss" class="wide" />
    </td>
  </tr>
  <tr>
    <th>Caption :</th>
    <td><input type="text" name="part_ss_cap" size="40" class="wide" value="<?php hecho($ss['caption']) ?>" /></td>
  </tr>
  <tr>
    <th>Screenshot Type :</th>
    <td><select name="ss_type" class="wide"><?= formatHTMLOptions($ss_type_list, $ss['ss_type']) ?></select></td>
  </tr>
  <tr>
    <th>Rank :</th>
    <td><input type="text" name="rank" class="wide" value="<?php hecho($ss['rank']) ?>" /></td>
  </tr>
  <tr>
    <th></th>
    <td><a href="<?php hecho('/' . $ss['filename']) ?>" target="_blank"><img src="<?php hecho("/" . $ss['filename']) ?>" style="max-width:470px;max-height:114px;" /></a></td>
  </tr>
</table>

<br />

<div style="text-align:center;">
  <input type="hidden" name="ss_id" value="<?php hecho($ss_id) ?>" />
  <input type="hidden" name="action" value="do_edit" />
  <input type="submit" name="add" value="Update" />
  <input type="reset" name="clear" value="Clear" />
</div>
