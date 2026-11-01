<?php
include_once __DIR__ . '/common.inc.php';

$posted    = array_merge($_GET, $_POST, $_FILES);
$action    = strtolower($posted['action']);
$id        = intval($posted['id']);
$contractor_id = intval($posted['contractor_id']);
$thumb     = ('' != $posted['thumb']) ? true : false;
function showAddAttachment($contractor_id)
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
			<input type="hidden" name="contractor_id" value="<?=$contractor_id?>" />
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
function showEditAttachment($id, $contractor_id)
{
    $shot = contractorAttachmentFromDatabase($id);
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
			<input type="hidden" name="contractor_id" value="<?=$contractor_id?>" />
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
        $sql       = "INSERT INTO contractors_attachments (contractor_id, date, caption, file_orig, staff_id, filetype, size) VALUES (" . intval($posted['contractor_id']) . ", NOW(), " . formatSQLString($caption) . ", " . formatSQLString($file_orig) . ", " . intval($_GLOBALS['staff']['id']) . ", '$file_ext', $size)";
        $query     = $DB->query($sql);
        $ss_id     = $DB->insert_id($query);
        $file_new  = "contractor_" . $posted['contractor_id'] . '_' . $ss_id . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/contractor/attachment/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE contractors_attachments SET filename=" . formatSQLString($file_new) . " WHERE id={$ss_id}");

            $data = array('id' => $ss_id,'image_url' =>'/webadmin/uploads/contractor/attachment/'.$file_new,'thumb_url'=>'/webadmin/uploads/contractor/attachment/'.$file_new,'caption' => $caption, 'staff' => $_GLOBALS['staff']['id'], 'filetype' => $file_ext, 'size' => formatFileSize($size));
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
	<tr><td style="text-align:center;"><b>Attachment for contractor ID #<?=$posted['contractor_id']?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.opener.addAttachment(data);window.close();" value="Close Window" /></td></tr>
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
    $contractor_id = intval($posted['contractor_id']);
    $o_shot    = contractorAttachmentFromDatabase($id);
    $first     = (intval($o_shot['rank']) === 1) ? true : false;
    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $size      = intval($posted['image_file']['size']);
    $caption   = $posted['image_desc'];
    $DB->query("UPDATE contractors_attachments SET caption=" . formatSQLString($caption) . " WHERE id=$id");
    if ($size && !$error) {
        $file_ext = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }

        $file_name        = $o_shot['filename'];
        list($name, $ext) = explode('.', $file_name);
        $file_name        = $name . '.' . $file_ext;
        $dir              = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/contractor/attachment/';
        if (move_uploaded_file($file_temp, $dir . $file_name)) {
            $img_size = getimagesize($dir . $file_name);
            $width    = $img_size[0];
            $height   = $img_size[1];
            if ($width > 750 || $height > 750) {
                $width  = 750;
                $height = 750;
            }
            $DB->query("UPDATE contractors_attachments SET filename=" . formatSQLString($file_name) . ", file_orig=" . formatSQLString($file_orig) . ", size=$size, filetype='$file_ext' WHERE id=$id");
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
	<tr><td style="text-align:center;"><b>Attachment for contractor ID #<?=$contractor_id?> successfully updated<br />Changes on the other page will appear once the page is refreshed.</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.close();" value="Close Window" /></td></tr>
</table>

	</body>
</html>
<?php
}
function viewAttachment($id)
{
    $id        = intval($id);
    $attach    = contractorAttachmentFromDatabase($id);
    $filename  = $attach['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/contractor/attachment/';
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
    $id             = intval($id);
    $attach         = contractorAttachmentFromDatabase($id);
    $dir            = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/contractor/attachment/';
    $from_file      = $dir . $attach['filename'];
    $content_type   = 'image/png';
    $to_file        = $attach['file_orig'];
    @$last_modified = filemtime($from_file);
    $thumb_content  = '';
    $mime           = reset(explode('/', mime_content_type($from_file)));
    $mime_thumb     = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/filetypes/' . $mime . '.png';
    if (!file_exists($mime_thumb)) {
        $mime       = reset(explode('/', $attach['filetype']));
        $mime_thumb = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/filetypes/' . $mime . '.png';
        if (!file_exists($mime_thumb)) {
            $mime       = 'unknown';
            $mime_thumb = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/filetypes/unknown.png';
        }
    }
    if ('image' != $mime || !file_exists($from_file)) {
        $to_file        = $mime . '.png';
        $last_modified  = filemtime($mime_thumb);
        $thumb_content  = implode('', file($mime_thumb));
        $content_length = strlen($thumb_content);
    } else {
        ob_start();
        passthru('/usr/bin/convert ' . $from_file . ' -resize 96x96! png:-');
        $thumb_content  = ob_get_clean();
        $content_length = strlen($thumb_content);
    }
    if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && (strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $last_modified)) {
        header("HTTP/1.1 304 Not Modified");
        die();
    }
    header("Date: " . date('r'));
    header("Last-Modified: " . date('r', $last_modified));
    header("HTTP/1.1 200 OK");
    header("Content-Length: " . $content_length);
    header("Content-Type: " . $content_type);
    header('Content-Disposition: inline; filename="' . urlencode($to_file) . '"');
    echo $thumb_content;
    die();
}
function deleteAttachment($id)
{
    if (contractorAttachmentDelete($id)) {
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Delete Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for contractor ID #<?=$contractor_id?> (#<?=$id?>) Successfully Removed.</b></td></tr>
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
        showAddAttachment($contractor_id);
        break;

    case "do_add":
        addAttachment($posted);
        break;

    case 'edit':
        showEditAttachment($id, $contractor_id);
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
