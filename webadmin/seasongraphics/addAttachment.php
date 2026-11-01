<?php
include_once __DIR__ . '/common.inc.php';

$posted    = array_merge($_GET, $_POST, $_FILES);
$action    = strtolower($posted['action']);
$name        = $posted['name'];
$class     = $posted['class'];
$thumb     = ('' != $posted['thumb']) ? true : false;
$id        = $posted['id'];

function showAddAttachment($class)
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
			<input type="hidden" name="class" value="<?=$class?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File :</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                    </td>
				</tr>
                <tr><td></td><td></td></tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
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
    $size      = intval($posted['image_file']['size']);
    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
    $class     = $posted['class'];
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($size && !$error) {
        $dyname=time() . '_' . rand(10,1000);
        $file_new  = $class."_season_" . $dyname . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/season/image/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $data = array('id'=>$dyname,'name' => $file_new,'class'=>$class, 'staff' => $_GLOBALS['staff']['id'], 'filetype' => $file_ext, 'size' => formatFileSize($size));
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Attachment</title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($data) ?>;
function setimageinvoice(){
    if (window.opener != null && window.opener.setImagedata != null){
        window.close();
        window.opener.setImagedata(data);
    }else{
        alert('Unable to send image back to form');
        window.close();
    }
}
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="setimageinvoice();" value="Close Window" /></td></tr>
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
        $dir              = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/season/image/';
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
function viewAttachment($name)
{
    $filename  = $name;
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/season/image/';
    $file      = $dir . $filename;
    $mime_type = mime_content_type($name);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($file)));
    header("Content-Type: " . $mime_type);
    readfile($file);
}
function viewThumbnail($name)
{ 
    $filename  = $name;
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/season/image/';
    $file      = $dir . $filename;
    $mime_type = mime_content_type($file);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($file)));
    header("Content-Type: " . $mime_type);
    readfile($file);
}
function deleteAttachment($id)
{
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Delete Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
<script type="text/javascript">
    function deleteAttachment(){
        if (window.opener != null && window.opener.deleteAttachment != null){
            window.close();
            window.opener.deleteAttachment("<?=$id?>");
        }else{
            alert('Unable to send image back to form');
            window.close();
        }
    }

</script>
<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment  Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick='deleteAttachment()' /></td></tr>
</table>
<br />

	</body>
</html>
<?php
}
switch ($action) {
    case "add":
        showAddAttachment($class);
        break;

    case "do_add":
        addAttachment($posted);
        break;

    case "delete":
        deleteAttachment($id);
        break;

    case "view":
        if ($thumb) {
            viewThumbnail($name);
        } else {
            viewAttachment($name);
        }

        break;

    case "":
    default:
        die("Invalid Selection!!");
}
