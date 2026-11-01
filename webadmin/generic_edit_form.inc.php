<?php
show_locked_msg($table, $id);

$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = ".$_REQUEST['id']);
?>
<center>
    <table border="0" cellpadding="2">
        <tr><td colspan="2"><?= $error_msg ?>&nbsp;</td></tr>
<?php
for ($i = 0; $i < count($fields); $i++) {
    $key        = key($fields);
    $field_name = ucwords(str_replace('_', ' ', $key));
    if ($key != $id_field) {
/*
        if ($key == 'colour_img') {
?>
        <tr>
            <td><b>Colour Image:</b></td>
            <td style="padding: 10px 0;">
<?php
            if ($item['colour_img'] !== '') {
?>
                <img src="/<?= $item['colour_img'] ?>" width="100" />
                <br />
<?php
            }
?>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                <input type="file" name="manufacturers_colour_image" size="20" style="width: 400px;" />
            </td>
        </tr>
<?php
        } elseif ($key == 'bw_img') {
?>
        <tr>
            <td><b>Black & White Image:</b></td>
            <td style="padding: 10px 0;">
<?php
            if ($item['bw_img'] !== '') {
?>
                <img src="/<?= $item['bw_img'] ?>" width="100" />
                <br />
<?php
            }
?>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                <input type="file" name="manufacturers_bw_image" size="20" style="width: 400px;" />
            </td>
        </tr>
<?php
        }
*/
        if ('img' == $key) {
?>
        <tr>
            <td><b>Image:</b></td>
            <td style="padding: 10px 0;">
<?php
            if ('' !== $item['img']) {
?>
                <img src="<?= $item['img'] ?>" width="100" />
                <br />
<?php
            }
?>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                <input type="file" name="manufacturers_colour_image" size="20" style="width: 400px;" />
            </td>
        </tr>
<?php
        } elseif ('show_front' == $key) {
?>
        <tr>
            <td><b>Show Online:</b></td>
            <td style="padding: 10px 0;">
                <select name="field_data[show_front]">
                    <option value="y" <?= ('y' == $item['show_front']) ? 'selected' : '' ?>>Yes</option>
                    <option value="n" <?= ('n' == $item['show_front']) ? 'selected' : '' ?>>No</option>
                </select>
            </td>
        </tr>
<?php
        } else {
?>
        <tr>
            <td><b><?= $field_name ?>:</b></td>
            <td><input type="text" name="field_data[<?= $key ?>]" size="20" value="<?=$item[$key]?>" style="width:400px" /></td>
        </tr>
<?php
        }
    }
    next($fields);
}

reset($fields);
?>
        <tr>
            <td colspan="2">
                <center>
                    <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
                    <input type="hidden" name="action" value="do_edit">
                    <input type="submit" name="add" value="Update">
                    <input type="reset" name="clear" value="Clear">
                </center>
            </td>
        </tr>
    </table>
</center>
<p>
