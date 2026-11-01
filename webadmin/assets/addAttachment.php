<?php
include_once __DIR__ . '/common.inc.php';

/**
 * @param int $product_id
 *
 * @return void
 */
function showAddScreenshot($product_id)
{
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action="">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="id" value="<?=$product_id?>" />
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
    $options = array(
        1 => 'Actual',
        2 => 'Reference',
    );
?>
						<select style="width:365px;font: 13px verdana;" name="ss_type">
							<?=formatHTMLOptions($options)?>
						</select>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

/**
 * @param int $id
 *
 * @return array{rank:int, letter:string}
 */
function getScreenshotLetter($id)
{
    global $DB;

    $letters = range('a', 'z');
    if ($row = $DB->query_first(sprintf(
        "SELECT MAX(`rank`) AS `rank` FROM `product_screenshots` WHERE `product_id` = %d",
        $id
    ), MYSQLI_ASSOC)) {
        $num    = $row['rank'] + 1;
        $letter = '';
        while ($num > 0) {
            $letter = $letters[$num % 26] . $letter;
            $num = intdiv($num, 26);
        }
        return [
            'rank'   => $row['rank'] + 1,
            'letter' => $letter,
        ];
    }

    return [
        'rank'   => 1,
        'letter' => 'a',
    ];
}

/**
 * @param mixed[] $posted
 *
 * @return 0
 */
function addScreenshot($posted)
{
    global $_GLOBALS;
    global $DB;

    $file_orig = $_FILES['image_file']['name'];
    $file_temp = $_FILES['image_file']['tmp_name'];
    $error     = $_FILES['image_file']['error'];
    $caption   = ('' != $posted['image_desc']) ? $posted['image_desc'] : $_FILES['image_file']['name'];
    $size      = intval($_FILES['image_file']['size']);

    $file_ext = pathinfo($file_orig, PATHINFO_EXTENSION);

    if ($size && !$error) {
        $details = getScreenshotLetter($posted['id']);
        $sql     = sprintf(
            "INSERT INTO `product_screenshots` (`product_id`, `date`, `caption`, `ss_type`, `file_orig`, `staff`, `filetype`, `size`, `rank`) VALUES (%d, NOW(), %s, %d, %s, %d, %s, %d, %d)",
            $posted['id'],
            $DB->escape($caption),
            $posted['ss_type'],
            $DB->escape($file_orig),
            $_GLOBALS['staff']['id'],
            $DB->escape($file_ext),
            $size,
            $details['rank']
        );

        $query = $DB->query($sql);

        $ss_id = $DB->insert_id($query);

        $file_new  = "ss_" . $posted['id'] . $details['letter'] . '.' . $file_ext;
        $dir       = __DIR__ . '/../uploads/screenshot/image/';
        $thumb_dir = __DIR__ . '/../uploads/screenshot/thumb/';

        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query(sprintf(
                "UPDATE `product_screenshots` SET `filename` = %s WHERE `ss_id` = %d LIMIT 1",
                $DB->escape($file_new),
                $ss_id
            ));

            // now format the JS Array to pass to other page
            $data = array(
                'id'       => $ss_id,
                'caption'  => $caption,
                'staff'    => $_GLOBALS['staff']['id'],
                'filetype' => $file_ext,
                'size'     => formatFileSize($size),
            );
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<table style="width:30%;border:1px solid blue;background-color:#99CCFF;margin:auto">
			<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?=$posted['id']?> successfully added</b></td></tr>
			<tr><td style="text-align:center"><input type="button" onclick="window.opener.addScreenShot(data);window.close();" value="Close Window" /></td></tr>
		</table>
	</body>
</html>
<?php
return $ss_id;
        }
    }
    return 0;
}

/**
 * @param int  $id
 * @param bool $thumb
 *
 * @return bool
 */
function viewScreenshot($id, $thumb)
{
    global $DB;

    if ($row = $DB->query_first(sprintf(
        "SELECT `filename` FROM `product_screenshots` WHERE `ss_id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC)) {
        $row['filename'] = basename($row['filename']); //TODO: Remove once fixed

        $image_path = __DIR__ . '/../uploads/screenshot/image/';
        $thumb_path = __DIR__ . '/../uploads/screenshot/thumb/';

        $file = $image_path . $row['filename'];
        if ($thumb && is_file($thumb_path . $row['filename'])) {
            $file = $thumb_path . $row['filename'];
        }

        if (is_file($file)) {
            header('Content-Type: ' . mime_content_type($file));
            readfile($file);
            return true;
        }
    }

    echo "Image not recognized.";
    return false;
}

/**
 * @param int  $id
 * @param bool $thumb
 *
 * @return bool
 */
function viewProductImage($id, $thumb)
{
    global $DB;

    if ($row = $DB->query_first(sprintf(
        "SELECT `image`, `thumb` FROM `products` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC)) {
        $row['image'] = basename($row['image']); //TODO: Remove once fixed
        $row['thumb'] = basename($row['thumb']); //TODO: Remove once fixed

        $image_path = __DIR__ . '/../uploads/product/image/';
        $thumb_path = __DIR__ . '/../uploads/product/thumb/';

        $file = $image_path . $row['image'];
        if ($thumb && is_file($thumb_path . $row['thumb'])) {
            $file = $thumb_path . $row['thumb'];
        }

        if (is_file($file)) {
            header('Content-Type: ' . mime_content_type($file));
            readfile($file);
            return true;
        }
    }

    echo "Image not recognized.";
    return false;
}

/**
 * @param int  $id
 *
 * @return void
 */
function deleteScreenshot($id)
{
    global $DB;

    $DB->query(sprintf(
        "DELETE FROM `product_screenshots` WHERE `ss_id` = %d LIMIT 1",
        $id
    ));
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
			<tr><td style="text-align:center;"><b>Product Screenshot #<?=$id?> Successfully Removed.</b></td></tr>
			<tr><td align="center"><input type="button" value="Close Window" onclick="window.opener.delScreenshot(<?=$id?>);window.close();" /></td></tr>
		</table>
		<br />
	</body>
</html>
<?php
}

// Main Switch-Case...
switch ($_REQUEST['action']) {
    case "add":
        showAddScreenshot($_REQUEST['id']);
        break;
    case "do_add":
        addScreenshot($_REQUEST);
        break;
    case "delete":
        deleteScreenshot($_REQUEST['id']);
        break;
    case "view":
        viewScreenshot($_REQUEST['ss_id'], $_REQUEST['thumb']);
        break;
    case "view_product":
        viewProductImage($_REQUEST['product_id'], $_REQUEST['thumb']);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
