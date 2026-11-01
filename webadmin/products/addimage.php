<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$attachment_dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/attachments';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/connection.inc.php';
include_once 'query_attachment.inc.php';

$posted        = array_merge($_GET, $_POST);
$action        = strtolower($posted['action']);
$product_id    = intval($posted['product_id']);
$attachment_id = intval($posted['attachment_id']);
if ($product_id <= 0) {
    die('Product does not exist');
}

function getLetter($number)
{
    $array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    return $array[$number];
}

switch ($action) {
    case 'do_delete':
        productAttachmentDelete($attachment_id, $product_id);
        $action = 'do_delete';
        break;

    case 'do_add':
        //For adding
        $file_temp = $_FILES['image_file']['tmp_name'];
        $file_orig = strtolower($_FILES['image_file']['name']);
        $file_desc = $posted['image_desc'];
        //if($file_desc=='') $file_desc=$file_orig;
        if ('' != $file_temp) {
            //if($file_desc=='') $file_desc=$file_orig;
            $user_details  = $_GLOBALS['staff'];
            $before        = $DB->query_first("SELECT COUNT(*) as total FROM product_attachments WHERE product_id = '$product_id'");
            $query         = $DB->query("INSERT INTO product_attachments(product_id) VALUES(" . $product_id . ")");
            $attachment_id = $DB->insert_id();

            $file_orig = strtolower($_FILES['image_file']['name']);
            $size      = filesize($_FILES['image_file']['tmp_name']);
            $extension = substr($file_orig, strrpos($file_orig, '.') + 1);
            $product   = $DB->query_first("SELECT * FROM products p,product_types pt WHERE p.id = '$product_id' AND pt.id=p.type");
            $letter    = getLetter($before['total']);
            $new_file  = $product_id . $letter . '_' . str_replace(" ", "_", $product['product_name']) . '_' . $product['name'] . '.' . $extension;
            //$new_file=$product_id.'_'.$product['name'].'_'.$product['name'].'.'.$extension;
            //$new_file='prod_attach'.$attachment_id.'.'.$extension;

            if (is_uploaded_file($_FILES['image_file']['tmp_name']) && move_uploaded_file($_FILES['image_file']['tmp_name'], $attachment_dir . '/' . $new_file)) {
                $DB->query("UPDATE product_attachments SET user='" . $user_details['first_name'] . ' ' . $user_details['last_name'] . "', attach_date=NOW(), file_name=" . formatSQLString($new_file) . ", file_original=" . formatSQLString($_FILES['image_file']['name']) . ", description=" . formatSQLString($file_desc) . ", filetype='" . $extension . "', manual_type='coe', size='" . $size . "' WHERE attachment_id=" . $attachment_id);
            } else {
                productAttachmentDelete($attachment_id);
                $action = 'add';
            }
        }
        break;
}

switch ($action) {
    case 'view':
        header('Location: viewimage.php?product_id=' . $product_id . '&attachment_id=' . $attachment_id);
        die();

    case 'do_delete':
?>
<html><head></head><body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
<table align="center" style="width:100%;height:100%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;" nowrap><b>Manual Successfully Removed</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.opener.delAttachment(<?php echo $attachment_id ?>);window.close();" /></td></tr>
	</table>
</body>
</html>
<?php
        break;

    case 'do_add':
?>
<html><head></head><body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
<table align="center" style="width:100%;height:100%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;" nowrap><b>Manual #<?php echo $attachment_id ?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="<?=htmlspecialchars('window.opener.addAttachment(' . json_encode($attachment_id) . ', ' . json_encode($file_desc) . ', ' . json_encode($file_orig) . ', ' . json_encode($extension) . ');window.close()')?>" value="Close Window" /></td></tr>
	</table>
</body>
</html>
<?php
        break;

    case 'delete':
    case 'remove':
?>
<html><head></head><body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
<table align="center" style="width:100%;height:100%;border:1px solid blue;background-color:#99CCFF;">
	<form action="" method="post">
	<input type="hidden" name="action" value="do_delete" />
	<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
	<input type="hidden" name="attachment_id" value="<?php echo $attachment_id ?>" />
	<tr><td style="text-align:center;"><b>Are you sure you want to delete this manual?</b></td></tr>
	<tr><td align="center">
		<input type="submit" value="Yes" style="width:60px"/>
		<input type="button" onclick="window.close()" value="Cancel" style="width:60px"/>
	</td></tr>
	</form>
</table>
</body></html>
<?php
        break;

    default:
    case 'add':
?>
<html><head></head><body style="text-align:center;vertical-align:middle;width:100%;height:100%;margin:0px 0px 0px 0px;">
<table align="center" style="width:100%;height:100%;border:1px solid blue;background-color:#99CCFF;">
	<form enctype="multipart/form-data" action="" method="post">
	<input type="hidden" name="action" value="do_add" />
	<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
	<tr>
		<td style="text-align:right;font: bold 14px verdana;">Description:</td>
		<td style="text-align:left;"><input style="width:200px;font: 13px verdana;" type="text" name="image_desc" value="<?php echo str_replace('"', '&quot;', $posted['image_desc']) ?>" /></td>
	</tr>
	<tr>
		<td style="text-align:right;font: bold 14px verdana;">File:<br /><span style="font-size:75%;color:red;">Maximum file<br />size 10mb</td>
		<td style="text-align:left;">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input style="width:285px;font: 13px verdana;" type="file" name="image_file" accept="image" />
        </td>
	</tr>
	<tr><td colspan="2" style="text-align:center;"><a href="<?php echo productAttachmentGetFTPURI($product_id) ?>" target="_blank" style="background-color:yellow;">For Larger Files...</a></td></tr>
	<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Attach Manual" /></td></tr>
	</form>
</table>
</body></html>
<?php
        break;
}
