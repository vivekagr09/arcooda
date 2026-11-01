<?php
include_once __DIR__ . '/common.inc.php';

$posted    = array_merge($_GET, $_POST, $_FILES);
$action    = strtolower($posted['action']);
$id        = intval($posted['id']);
$supplier_id = intval($posted['supplier_id']);
$thumb     = ('' != $posted['thumb']) ? true : false;
function showAddAttachment($supplier_id)
{
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action="">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="supplier_id" value="<?=$supplier_id?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File :</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                    </td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description :</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="image_desc"/></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
	</body>
</html>
<?php
}
function showEditAttachment($id, $supplier_id)
{
    $shot = supplierAttachmentFromDatabase($id);
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Edit Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action="">
			<input type="hidden" name="action" value="do_edit" />
			<input type="hidden" name="id" value="<?=$id?>" />
			<input type="hidden" name="supplier_id" value="<?=$supplier_id?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File:</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                    </td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Caption:</td>
					<td style="text-align:left;">
						<input style="width:365px;font: 13px verdana;" type="text" name="image_desc" value="<?php hecho($shot['caption'])?>"/>
					</td>
				</tr>
				<tr>
					<td align="center" colspan=2>
						<a href="addAttachment.php?ss_id=<?=$shot['id']?>&action=view" target="_blank">
							<img src="addAttachment.php?id=<?=$shot['id']?>&action=view&thumb=true" width="96" height="96">
						</a>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Update Image"/></td></tr>
			</table>
		</form>
	</body>
</html>
<?php
}
function addAttachment($posted)
{
    global $DB, $_GLOBALS;
    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $caption   = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $size      = intval($posted['image_file']['size']);
    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($size && !$error) {
        $sql       = "INSERT INTO new_suppliers_attachments (supplier_id, date, caption, file_orig, staff_id, filetype, size) VALUES (" . intval($posted['supplier_id']) . ", NOW(), " . formatSQLString($caption) . ", " . formatSQLString($file_orig) . ", " . intval($_GLOBALS['staff']['id']) . ", '$file_ext', $size)";
        $query     = $DB->query($sql);
        $ss_id     = $DB->insert_id($query);
        $file_new  = "member_" . $posted['supplier_id'] . '_' . $ss_id . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE new_suppliers_attachments SET filename=" . formatSQLString($file_new) . " WHERE id={$ss_id}");
            $data = array('id' => $ss_id, 'caption' => $caption, 'staff' => $_GLOBALS['staff']['id'], 'filetype' => $file_ext, 'size' => formatFileSize($size));
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Attachment</title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($data) ?>;
//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Supplier ID #<?=$posted['supplier_id']?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="location.reload();" value="Close Window" /></td></tr>
</table>

	</body>
</html>
<?php
return $ss_id;
        }
    }
    return 0;
}
function editAttachment($posted)
{
    global $DB;
    $id        = intval($posted['id']);
    $supplier_id = intval($posted['supplier_id']);
    $o_shot    = supplierAttachmentFromDatabase($id);
    $first     = (intval($o_shot['rank']) === 1) ? true : false;
    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $size      = intval($posted['image_file']['size']);
    $caption   = $posted['image_desc'];
    $DB->query("UPDATE new_suppliers_attachments SET caption=" . formatSQLString($caption) . " WHERE id=$id");
    if ($size && !$error) {
        $file_ext = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }

        $file_name        = $o_shot['filename'];
        list($name, $ext) = explode('.', $file_name);
        $file_name        = $name . '.' . $file_ext;
        $dir              = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
        if (move_uploaded_file($file_temp, $dir . $file_name)) {
            $img_size = getimagesize($dir . $file_name);
            $width    = $img_size[0];
            $height   = $img_size[1];
            if ($width > 750 || $height > 750) {
                $width  = 750;
                $height = 750;
            }
            $DB->query("UPDATE new_suppliers_attachments SET filename=" . formatSQLString($file_name) . ", file_orig=" . formatSQLString($file_orig) . ", size=$size, filetype='$file_ext' WHERE id=$id");
        }
    }
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Edit Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Supplier ID #<?=$supplier_id?> successfully updated<br />Changes on the other page will appear once the page is refreshed.</b></td></tr>
	<tr><td align="center"><input type="button" onclick="location.reload();" value="Close Window" /></td></tr>
</table>

	</body>
</html>
<?php
}
function viewAttachment($id)
{
    $id        = intval($id);
    $attach    = supplierAttachmentFromDatabase($id);
    $filename  = $attach['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $attach['size'];
    $file      = $dir . $filename;
    $mime_type = mime_content_type($filename);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . $self_gmdate);
    header('Content-Length: ' . $size);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $attach['file_orig'] . '"');
    echo file_get_contents($file);
}
function viewThumbnail($id)
{ 
    $id        = intval($id);
    $attach    = supplierAttachmentFromDatabase($id);
    $filename  = $attach['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $attach['size'];
    $file      = $dir . $filename;
    $thumb_file = $thumb_dir . $filename;
    $mime_type = mime_content_type($thumb_file);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . $self_gmdate);
    header('Content-Length: ' . $size);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $attach['file_orig'] . '"');
    echo file_get_contents($thumb_file);
}
function deleteAttachment($id)
{
    if (supplierAttachmentDelete($id)) {
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Delete Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Supplier ID #<?=$supplier_id?> (#<?=$id?>) Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="window.opener.deleteAttachment(<?=$id?>);window.close()" /></td></tr>
</table>
<br />

	</body>
</html>
<?php
return true;
    }
}
switch ($action) {
    case "add":
        showAddAttachment($supplier_id);
        break;

    case "do_add":
        addAttachment($posted);
        break;

    case 'edit':
        showEditAttachment($id, $supplier_id);
        break;

    case 'do_edit':
        editAttachment($posted);
        break;

    case "delete":
        deleteAttachment($id);
        break;

    case "view":
        if ($thumb) {
            viewThumbnail($id);
        } else {
            viewAttachment($id);
        }

        break;

    case "":
    default:
        die("Invalid Selection!!");
}
