<?php
include_once __DIR__ . '/common.inc.php';

$posted    = undo_magicquotes(array_merge($_GET, $_POST, $_FILES));
$action    = strtolower($posted['action']);
$id        = intval($posted['id']);
$rowid = intval($posted['rowid']);
$type = intval($posted['type']);
$thumb     = ('' != $posted['thumb']) ? true : false;
 function supplierInvoiceAttachmentsFromDatabase($id)
{
    global $DB;
    $row = $DB->query_first(sprintf(
        "SELECT *, UNIX_TIMESTAMP(`ma`.`date`) AS `time_added` FROM `suppliers_invoice_attachment` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC);
    if ($row) {
        $ext = end(explode('.', $row['filename']));
        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/assets/supplier' . '/' . $row['filename'])) {
            $row['thumb_url'] = sprintf(
                '/webadmin/supplier_new/images/%d_%d_sml.%s',
                $id, $row['supplier_id'], $ext
            );
            $row['image_url'] = sprintf(
                '/webadmin/supplier_new/images/%d_%d.%s',
                $id, $row['supplier_id'], $ext
            );
        }
        $row['date_str'] = date('d/m/Y', $row['time_added']);
    }
    return $row;
}
function showAddAttachment($rowid,$type)
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
			<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
			<input type="hidden" name="row_id" value="<?=$rowid?>" />
            <input type="hidden" name="type" value="<?=$type?>" />
			<input type="hidden" name="invoice_product_id" value="0" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File :</td>
					<td style="text-align:left;"><input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image"/></td>
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
function addAttachment($posted)
{
    global $DB, $_GLOBALS;
    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $row_id     = $posted['row_id'];
    $type= $posted['type'];
    $caption   = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $size      = intval($posted['image_file']['size']);
    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($size && !$error) {
        $sql       = "INSERT INTO suppliers_invoice_attachment (date, caption, file_orig, staff_id, filetype, size,image_type) VALUES ( NOW(), " . formatSQLString($caption) . ", " . formatSQLString($file_orig) . ", " . intval($_GLOBALS['staff']['id']) . ", '$file_ext', $size,$type)";
        $query     = $DB->query($sql);
        $ss_id     = $DB->insert_id($query);
        $file_new  = "supplier_" . time() . '_' . $ss_id . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
        $thumb_dir = $dir . 'thumbnails/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE suppliers_invoice_attachment SET filename=" . formatSQLString($file_new) . " WHERE id={$ss_id}");
            if (in_array(strtolower($file_ext), array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'tif', 'tiff'))) {
                exec('/usr/bin/convert -resize 96x96! ' . $dir . $file_new . ' ' . $thumb_dir . $file_new);
            }

            $data = array('id' => $ss_id, 'caption' => $caption, 'staff' => $_GLOBALS['staff']['id'],'row_id'=>$row_id,'type'=>$type,'filetype' => $file_ext, 'size' => formatFileSize($size));
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Attachment</title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($data) ?>;
//]]>
		function setimageinvoice(){
			if (window.opener != null && window.opener.setInvoiceImagedata != null){
				window.close();
			    window.opener.setInvoiceImagedata(data);
			}else{
				alert('Unable to send image back to form');
				window.close();
			}
		}
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Generic Product  successfully added</b></td></tr>
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

function viewAttachment($id)
{
    $id        = intval($id);
    $attach    = supplierInvoiceAttachmentsFromDatabase($id);
    $filename  = $attach['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $attach['size'];
    $file      = $dir . $filename;
    $mime_type = mime_content_type($filename);
    header('Date: ' . gmdate(DATE_RFC1123, $_SERVER['REQUEST_TIME']));
    header('Last-Modified: ' . $self_gmdate);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $attach['file_orig'] . '"');
    echo file_get_contents($file);
}
function viewThumbnail($id)
{ 
    $id        = intval($id);
    $attach    = supplierInvoiceAttachmentsFromDatabase($id);
    $filename  = $attach['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $attach['size'];
    $file      = $dir . $filename;
    $thumb_file = $thumb_dir . $filename;
    $mime_type = mime_content_type($thumb_file);
    header('Date: ' . gmdate(DATE_RFC1123, $_SERVER['REQUEST_TIME']));
    header('Last-Modified: ' . $self_gmdate);
    header('Content-Length: ' . $size);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $attach['file_orig'] . '"');
    echo file_get_contents($thumb_file);
}
function deleteAttachment($id)
{
    if (supplierInvoiceAttachmentsFromDatabase($id)) {
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Delete Attachment</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<script type="text/javascript">
//<![CDATA[
var id = <?php jecho($id) ?>;
//]]>
		function deleteAttachment(){
			if (window.opener != null && window.opener.deleteAttachmentdata != null){
				window.close();
			    window.opener.deleteAttachmentdata(id);
			}else{
				alert('Unable to send image back to form');
				window.close();
			}
		}
		</script>
<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for product  Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="deleteAttachment(<?=$id?>);" /></td></tr>
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
        showAddAttachment($rowid,$type);
        break;

    case "do_add":
        addAttachment($posted);
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
