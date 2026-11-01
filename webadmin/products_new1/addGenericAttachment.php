<?php
include_once __DIR__ . '/common.inc.php';

$posted    = array_merge($_GET, $_POST, $_FILES);
$action    = strtolower($posted['action']);
$id        = intval($posted['id']);
$product_id = intval($posted['product_id']);
$colour_id = intval($posted['colour_id']);
$thumb     = ('' != $posted['thumb']) ? true : false;
 function supplierInvoiceAttachmentsFromDatabase($id)
{
    global $DB;
    $row = $DB->query_first(sprintf(
        "SELECT * FROM `products_images` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC);
    return $row;
}
function showAddAttachment($colour_id,$product_id)
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
			<input type="hidden" name="colour_id" value="<?=$colour_id?>" />
            <input type="hidden" name="product_id" value="<?=$product_id?>" />
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
function addAttachment($posted)
{
    global $DB, $_GLOBALS;
    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $colour_id     = $posted['colour_id'];
    $product_id= $posted['product_id'];
    $size      = intval($posted['image_file']['size']);
    $caption   = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
    $filename=explode('.', $file_orig)[0];
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }
    if ($size && !$error) {
        $query     =  $DB->query(sprintf(
            "INSERT INTO `products_images` (`product_id`, `color_id`, `user_id`) VALUES (%d, %d, %d)",
            $product_id,
            $colour_id,
            $_GLOBALS['staff']['id'],
        ));
        $ss_id     = $DB->insert_id($query);
        $file_new  = "product_color_" . time() . '_' . $ss_id . '.' . $file_ext;
        $insert_filename='/webadmin/uploads/screenshot/image/'.$file_new;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE products_images SET image=" . formatSQLString($insert_filename) . ",caption=" . formatSQLString($caption). ",file_orig=" . formatSQLString($filename) . " WHERE id={$ss_id}");
            $color_detail=getProductColourById($colour_id);
            $data = array('id' => $ss_id, 'caption' => $caption, 'user_id' => $_GLOBALS['staff']['id'],'colour_id'=>$colour_id,'product_id'=>$product_id,'filetype' => $file_ext, 'size' => formatFileSize($size),'colour'=>$color_detail["colour"],'file_orig'=>$filename);
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
			if (window.opener != null && window.opener.setproductcolourimage != null){
				window.close();
			    window.opener.setproductcolourimage(data);
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
    $attach    = supplierInvoiceAttachmentsFromDatabase($id);
    $filename  = basename($attach['image']);
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image/';
    $file      = $dir . $filename;
    $mime_type = mime_content_type($file);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . $self_gmdate);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $attach['file_orig'] . '"');
    echo file_get_contents($file);
}
function viewThumbnail($id)
{ 
    $id        = intval($id);
    $attach    = supplierInvoiceAttachmentsFromDatabase($id);
    $filename  = basename($attach['image']);
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image/';
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
        showAddAttachment($colour_id,$product_id);
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
