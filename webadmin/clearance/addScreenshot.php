<?php
include_once __DIR__ . '/common.inc.php';

$posted     = array_merge($_GET, $_POST, $_FILES);
$action     = strtolower($posted['action']);
$id         = intval($posted['id']);
$product_id = intval($posted['product_id']);
$sub_id     = intval($posted['sub_id']);

/**********************************************************Functions Definitions ************************************************************************/
function showAddScreenshot($product_id, $sub_id)
{
?>
<html>
	<head><title>Add Screenshot</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
			<input type="hidden" name="sub_id" value="<?php echo $sub_id ?>" />
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
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="image_desc"/></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Screenshot Type:</td>
					<td style="text-align:left;">
<?php
$options = array(1 => 'Actual', 2 => 'Reference');
?>
						<select style="width:365px;font: 13px verdana;" name="ss_type">
							<?php echo formatHTMLOptions($options) ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Active :</td>
					<td style="text-align:left;"><select style="width:365px;font: 13px verdana;" name="active">
						<?php echo formatHTMLOptions(array('y' => 'Yes', 'n' => 'No')) ?>
					</select></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

function showEditScreenshot($id, $product_id, $sub_id)
{
    $shot = productScreenshotFromDatabase($id);
?>
<html>
	<head><title>Edit Screenshot</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_edit" />
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
			<input type="hidden" name="sub_id" value="<?php echo $sub_id ?>" />
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
					<td style="text-align:right;font: bold 14px verdana;">Screenshot Type:</td>
					<td style="text-align:left;">
<?php
$options = array(1 => 'Actual', 2 => 'Reference');
?>
						<select style="width:365px;font: 13px verdana;" name="ss_type">
							<?php echo formatHTMLOptions($options, $shot['ss_type']) ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Active :</td>
					<td style="text-align:left;"><select style="width:365px;font: 13px verdana;" name="active">
						<?php echo formatHTMLOptions(array('y' => 'Yes', 'n' => 'No'), $shot['active']) ?>
					</select></td>
				</tr>
				<tr>
					<td align="center" colspan=2>
						<a href="addScreenshot.php?ss_id=<?php echo $shot['id'] ?>&action=view" target="_blank">
							<img src="addScreenshot.php?ss_id=<?php echo $shot['id'] ?>&action=view&thumb=true" width="96" height="96">
						</a>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Update Image"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

/**
 * @param int|string $product_str
 * @param int        $sub_id
 *
 * @return int
 */
function getScreenshotRank($product_str, $sub_id = 0)
{
    global $DB;

    $product_id = intval($product_str);

    //@todo maybe need a productScreenshotNextRank($product_str, $sub_id) function
    if ($row = $DB->query_first(sprintf(
        "SELECT MAX(`rank`) AS `max_rank` FROM %s WHERE `%s` = %d",
        "`arcooda_www`.`products_screenshots`",
        "id",
        $product_id
    ), MYSQLI_ASSOC)) {
        return $row['max_rank'] + 1;
    }

    return 1;
}

function addScreenshot($posted)
{
    global $_GLOBALS;
    global $DB;

    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $caption   = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $size      = intval($posted['image_file']['size']);

    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($size && !$error) {
        $new_rank = getScreenshotRank($posted['product_id'], $posted['sub_id']);

        $screenshot = [
            "id"         => $posted['product_id'],
            'file_orig'  => $file_orig,
            'filename'   => '',
            'caption'    => $caption,
            'ss_type'    => $posted['ss_type'],
            'rank'       => $new_rank,
            'active'     => $posted['active'],
            'date_added' => formatDateTime(),

            'product_id' => $posted['product_id'],
            'sub_id'     => $posted['sub_id'],
            'date'       => formatDateTime(),
            'staff'      => $_GLOBALS['staff']['user'],
            'filetype'   => $file_ext,
            'size'       => $size,
        ];

        //@todo replace with productScreenshotToDatabase($posted['product_id'], $screenshot);
        if ($ss_id = $DB->modelSave($screenshot, "`arcooda_www`.`products_screenshots`", "ss_id")) {
            $file_new  = "ss_" . $posted['product_id'] . '_' . $ss_id . '.' . $file_ext; // new format to avoid any errors with overwriting of filenames..
            $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/';

            if (move_uploaded_file($file_temp, $dir . $file_new)) {
                //@todo replace with productScreenshotSetValue($ss_id, 'filename', $file_new);
                $DB->modelSet($ss_id, 'filename', $file_new, "`arcooda_www`.`products_screenshots`", "ss_id");

                // now format the JS Array to pass to other page
                $data = array(
                    'id'         => $ss_id,
                    'product_id' => intval($posted['product_id']),
                    'sub_id'     => intval($posted['sub_id']),
                    'caption'    => $caption,
                    'staff'      => $_GLOBALS['staff']['user'],
                    'filetype'   => $file_ext,
                    'size'       => formatFileSize($size),
                    'active'     => $posted['active'],
                );
                $tab  = '_' . ((1 == $posted['ss_type']) ? 'act' : 'ref');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
//]]>
	</script>
</head>
<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $posted['product_id'] ?> successfully added</b></td></tr>
<tr><td align="center"><input type="button" onclick="window.opener.addScreenShot(data,'<?php echo $tab ?>');window.close();" value="Close Window" /></td></tr>
</table>
</body>
</html>
<?php
                return $ss_id;
            } else {
                //@todo replace with productScreenshotDelete($ss_id)
                $DB->modelDelete($ss_id, "`arcooda_www`.`products_screenshots`", "ss_id");
            }
        }
    }
    return 0;
}

/**
 * @fixme Don't overwrite files, maybe delete the old screenshot and add a new one if uploading a file
 *
 * @param mixed[] $posted
 *
 * @return void
 */
function editScreenshot($posted)
{
    global $_GLOBALS;
    global $DB;

    $id         = intval($posted['id']);
    $product_id = intval($posted['product_id']);
    $sub_id     = intval($posted['sub_id']);

    if (!$DB->modelExists($id, "`arcooda_www`.`products_screenshots`", "ss_id")) {
        return 0;
    }

    $screenshot = productScreenshotFromDatabase($id);

    $screenshot['caption'] = $posted['image_desc'];
    $screenshot['ss_type'] = $posted['ss_type'];
    $screenshot['active']  = $posted['active'];
    
    if (UPLOAD_ERR_OK === ($posted['image_file']['error'] ?? -1)) {
        $pathinfo = pathinfo($posted['image_file']['name'], PATHINFO_ALL);

        $extension = $pathinfo['extension'];
        $dir       =  $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/';
        $file_name = $screenshot['filename'];

        if (move_uploaded_file($posted['image_file']['tmp_name'], $dir . $file_name)) {
            $screenshot['file_orig'] = $posted['image_file']['name'];
            $screenshot['filename']  = $file_name;
        }
    }

    //@todo replace with productScreenshotToDatabase($product_str, $screenshot, $sub_id);
    $DB->modelSave($screenshot, "`arcooda_www`.`products_screenshots`", "ss_id");

    // now format the JS Array to pass to other page
    $data = array(
        'id'       => $id,
        'caption'  => $caption,
        'staff'    => $_GLOBALS['staff']['user'],
        'filetype' => $file_ext,
        'size'     => formatFileSize($size),
    );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
//]]>
	</script>
</head>
<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
    <table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
        <tr><td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $product_id ?> successfully updated</b></td></tr>
        <tr><td align="center"><input type="button" onclick="window.opener.location=window.opener.location;window.close();" value="Close Window" /></td></tr>
    </table>
</body>
</html>
<?php
}

/**
 * @todo We're in the webadmin, we can direct-link the full-size image, we
 *  should only be concerned with thumbnails here.
 *
 * @param int  $id
 * @param bool $thumb
 *
 * @return void
 */
function viewScreenshot($id, $thumb)
{
    global $DB;

    if ($thumb) {
        viewThumbnail($id);
    }

    if ($row = $DB->query_first(sprintf(
        "SELECT `filename`, `caption` FROM `clearance_screenshots` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC)) {
        $path = sprintf(
            '%s/%s',
             $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/',
            basename($row['filename'])
        );

        if (is_file($path)) {
            header('Date: ' . gmdate(DATE_RFC1123, time()), true);
            header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($path)));
            header('Content-Type: ' . mime_content_type($path));
            readfile($path);
            die();
        }
    }

    header('404 Not Found', true, 404);
}

/**
 * @todo Thumbnails are intended as a quick view of the screenshot, so we
 *  should just return a 96x96 version of the full-size image here, with
 *  acceptable file-system caching and headers
 *
 * @param int $id
 *
 * @return void
 */
function viewThumbnail($id)
{
    global $DB;

    if ($row = $DB->query_first(sprintf(
        "SELECT `filename`, `caption` FROM `product_screenshots` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC)) {
        $path = sprintf(
            '%s/thumb/%s',
            $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/',
            basename($row['filename'])
        );

        if (!is_file($path)) {
            $path = sprintf(
                '%s/%s',
                $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/',
                basename($row['filename'])
            );
        }

        if (is_file($path)) {
            header('Date: ' . gmdate(DATE_RFC1123, time()), true);
            header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($path)));
            header('Content-Type: ' . mime_content_type($path));
            readfile($path);
            die();
        }
    }

    header('404 Not Found', true, 404);
}

function viewProductImage($product_id, $sub_id, $thumb)
{
    $product = productFromDatabase($product_id);

    $image_path = productGetOptionList('image_path') . '/' . $product['image'];
    $thumb_path = productGetOptionList('thumb_path') . '/' . $product['thumb'];
    $file       = ($thumb && is_file($thumb_path)) ? $thumb_path : $image_path;

    if (is_file($file)) {
        header('Content-Type: ' . mime_content_type($file));
        readfile($file);
        die();
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    return;
}

function deleteScreenshot($id)
{
    global $product_id;
    $shot = productScreenshotFromDatabase($id);
    $tab  = '_' . ((1 == $shot['ss_type']) ? 'act' : 'ref');
    if (productScreenshotDelete($id)) {
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $product_id ?> (#<?php echo $id ?>) Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.opener.delScreenshot(<?php echo $id ?>,'<?php echo $tab ?>');window.close();" /></td></tr>
	</table>
	</body>
</html>
<?php
return true;
    }
}

/**********************************************************Functions Definitions: End ********************************************************************/

// Main Switch-Case...
switch ($action) {
    case "add":
        showAddScreenshot($product_id, $sub_id);
        break;
    case "do_add":
        addScreenshot($posted);
        break;
    case 'edit':
        showEditScreenshot($id, $product_id, $sub_id);
        break;
    case 'do_edit':
        editScreenshot($posted);
        break;
    case "delete":
        deleteScreenshot($posted['id']);
        break;
    case "view":
        viewScreenshot($posted['id'], !!$posted['thumb']);
        break;
    case "view_product":
        viewProductImage($product_id, $sub_id, $posted['thumb']);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
?>