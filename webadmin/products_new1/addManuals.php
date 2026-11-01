<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
include_once __DIR__ . '/common.inc.php';

$posted = array_merge($_GET, $_POST, $_FILES);
$action = strtolower($posted['action']);
$id     = intval($posted['id']);

/**********************************************************Functions Definitions ************************************************************************/
function showAddManual($product_id)
{
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="id" value="<?=$product_id?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Document File:</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf" />
                    </td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="image_desc" /></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Active:</td>
					<td style="text-align:left;">
						<?php $options = array('y' => 'Yes', 'n' => 'No');?>
						<select style="width:365px;font: 13px verdana;" name="active">
							<?=formatHTMLOptions($options)?>
						</select>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Upload Document"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

function getManualLetter($id)
{
    global $DB;
    $id    = intval($id);
    $array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $res   = $DB->query("SELECT `id` FROM `product_safety_approvels` WHERE `product_id` = $id");
    $count = $DB->num_rows($res);
    return $array[$count];
}

function addManual($posted)
{
    global $_GLOBALS;
    global $DB;

    $file_orig   = $posted['image_file']['name'];
    $file_temp   = $posted['image_file']['tmp_name'];
    $error       = $posted['image_file']['error'];
    $description = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $size        = intval($posted['image_file']['size']);
    $active      = $posted['active'];
    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($size && !$error) {
        $sql = "INSERT INTO `product_safety_approvels` (`product_id`, `attach_date`, `file_original`, `description`, `filetype`, `manual_type`, `size`, `active`,`updated_by`) VALUES (" . intval($posted['id']) . ", NOW(), " . $DB->escape($file_orig) . ", " . $DB->escape($description) . ", '$file_ext', 'cnv', $size, '$active', " . $DB->escape($_GLOBALS['staff']['user']) . ")";

        $query  = $DB->query($sql);
        $man_id = $DB->insert_id;
        $file_new = "safety_" . $posted['id'] . getManualLetter($posted['id']) . '.' . $file_ext;
        $dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/safety_approval/';
        //Now need to add the manual to the package contents...
        //productPackageContentsAdd(intval($posted['id']), 'manual', $description);

        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE `product_safety_approvels` SET `file_name` = " . $DB->escape($file_new) . " WHERE `id` = {$man_id}");

            // now format the JS Array to pass to the products other page
            $data = array(
                'id'          => $man_id,
                'description' => $description,
                'filename'    => $file_new,
                'staff'       => $_GLOBALS['staff']['user'],
                'filetype'    => $file_ext,
                'size'        => formatFileSize($size),
                'active'      => $active,
                'file_orig'   => $file_orig,
            );
?>
<html>
	<head>
		<script type="text/javascript">
//<![CDATA[
var data =  <?php jecho($data) ?>;
//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Manual for Product ID #<?=$posted['id']?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.opener.manualAdd(data);window.close();" value="Close Window" /></td></tr>
	</table>
	</body>
</html>
<?php
            return $man_id;
        }
    }
    return 0;
}

function viewManual($id)
{
    global $DB;

    $id       = intval($id);
    $res      = $DB->query_first("SELECT `file_name`, `filetype`, `size`, `file_original` FROM `product_safety_approvels` WHERE `id` = $id LIMIT 1");
    $filename = $res['file_name'];
    $dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/safety_approval/';
    $size     = $res['size'];
    $type     = $res['filetype'];

    $file = $dir . $filename;

    $mime_type = mime_content_type($file);

    header('Content-Length: ' . $size);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: attachment; filename="' . $res['file_original'] . '"');

    echo file_get_contents($file);
}

function deleteManual($id)
{
    global $DB;
    $id = intval($id);
    //echo "ID to be deleted is $id";
    $row        = $DB->query_first("SELECT `file_name`, `product_id` FROM `product_safety_approvels` WHERE `id` = $id");
    $product_id = intval($row['product_id']);
    if ($row) {
        $dir   = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/safety_approval/';
        $fname = $dir . $row['file_name'];
        //unlink($fname);
        $DB->query("DELETE FROM `product_safety_approvels` WHERE `id` = $id");
    }
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Manual for Product ID #<?=$product_id?> (#<?=$id?>) Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.opener.delManual(<?=$id?>);window.close();" /></td></tr>
	</table>
	</body>
		<br />
	</body>
</html>
<?php
    return true;

}

/**********************************************************Functions Definitions: End ********************************************************************/

// Main Switch-Case...
switch ($action) {
    case "add":
        showAddManual($id);
        break;
    case "do_add":
        addManual($posted);
        break;
    case "delete":
        deleteManual($posted['man_id']);
        break;
    case "view":
        viewManual($posted['man_id']);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
