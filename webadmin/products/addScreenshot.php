<?php
include_once dirname(__FILE__) . "/products_common.php";

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

function getScreenshotRank($product_id, $sub_id)
{
    global $DB;
    $product_id = intval($product_id);
    $sub_id     = intval($sub_id);
    $res        = $DB->query("SELECT `id` FROM `hg_products`.`products_screenshots` WHERE `product_id` = $product_id AND `sub_id` = $sub_id");
    $rank       = $DB->num_rows($res);
    return $rank + 1;
}

function addScreenshot($posted)
{
    global $_GLOBALS;
    global $DB;

    $config['live'] = true;

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
        $sql      = "INSERT INTO `hg_products`.`products_screenshots` (`product_id`, `sub_id`, `date`, `caption`, `ss_type`, `file_orig`, `staff`, `filetype`, `size`, `rank`, `active`) VALUES (" . intval($posted['product_id']) . ", " . intval($posted['sub_id']) . ", NOW(), " . formatSQLString($caption) . ", " . intval($posted['ss_type']) . ", " . formatSQLString($file_orig) . ", " . $DB->escape($_GLOBALS['staff']['user']) . ", '$file_ext', $size, " . $new_rank . ", '" . $posted['active'] . "')";

        $query = $DB->query($sql);

        $ss_id = $DB->insert_id($query);

        $file_new  = "ss_" . $posted['product_id'] . '_' . $ss_id . '.' . $file_ext; // new format to avoid any errors with overwriting of filenames..
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/images/screenshots/';

        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            // max allowed size: 750x750. If image bigger than that it will be sized down, otherwise it will remain the same
            $DB->query("UPDATE `hg_products`.`products_screenshots` SET `filename` = " . formatSQLString($file_new) . " WHERE `id` = {$ss_id}");

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
        }
    }
    return 0;
}

function editScreenshot($posted)
{
    global $_GLOBALS;
    global $DB;

    $config['live'] = true;

    $id         = intval($posted['id']);
    $product_id = intval($posted['product_id']);
    $sub_id     = intval($posted['sub_id']);

    $o_shot = productScreenshotFromDatabase($id);

    $first = (intval($o_shot['rank']) === 1) ? true : false; // if its the 1st screenshot then need to update all the screenshots with the new image if its a power supply.

    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $size      = intval($posted['image_file']['size']);
    $caption   = $posted['image_desc'];
    $ss_type   = $posted['ss_type'];

    //update the fields first, before updating the image...
    $DB->query("UPDATE `hg_products`.`products_screenshots` SET `caption` = " . formatSQLString($caption) . ", `ss_type` = " . intval($ss_type) . ", `active` = '" . $posted['active'] . "' WHERE `id` = $id");

    if ($size && !$error) {
        $file_ext = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }

        $file_name        = $o_shot['filename'];
        list($name, $ext) = explode('.', $file_name);
        //$file_name = $name.'.'.$file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/images/screenshots/';
        $thumb_dir = $dir . 'thumbnails/';

        if (move_uploaded_file($file_temp, $dir . $file_name)) {
            /*
            $img_size = getimagesize($dir.$file_name);
            $width = $img_size[0]; $height = $img_size[1];
            if($width>750 || $height>750) {    $width=750; $height=750; }
             */

            $DB->query("UPDATE `hg_products`.`products_screenshots` SET `filename` = " . formatSQLString($file_name) . ", `file_orig` = " . formatSQLString($file_orig) . ", `size` = $size, `filetype` = '$file_ext' WHERE `id` = $id");
        }
    }

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

function viewScreenshot($id, $thumb)
{
    global $DB;
    if ($thumb) {
        viewThumbnail($id);
    }

    $id        = intval($id);
    $res       = $DB->query_first("SELECT `product_id`, `filename`, `filetype`, `size`, `caption` FROM `hg_products`.`products_screenshots` WHERE `id` = $id LIMIT 1");
    $filename  = $res['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/images/screenshots/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $res['size'];
    $file      = (($thumb) ? (((file_exists($thumb_dir . $filename) && filesize($thumb_dir . $filename) > $size) ? $dir : $thumb_dir) . $filename) : $dir . $filename);
    $type      = $res['filetype'];

    if ('Full Kit' == $res['caption'] && false) {
        $product  = productFromDatabase($res['product_id']);
        $name     = $product['product_name'];
        $contents = productPackageContents($res['product_id']);
        if (count($contents)) {
            // need to seperate out the power supplies...
            $power = array();
            $ar    = array('power_supply_110V', 'power_supply_240V');
            foreach ($contents as $key => $array) {
                if (in_array($array['field'], $ar)) {
                    $power[] = $array;
                    unset($contents[$key]);
                } elseif ('product' == $array['field']) {
                    $prod[] = $array;
                    unset($contents[$key]);
                }
            }
            if (count($power) == 2 && $power[0]['value'] == $power[1]['value']) {
                unset($power[1]);
            }

        }
?>
<html>
	<head><title>Full Kit Screenshot - <?php hecho($name)?></title></head>
	<body>
		<table style="font-family:Verdana;font-weight:bold;border:3px solid #000000;" border="0" cellpadding="6" cellspacing="0">
			<tr style="font-size:25px;">
				<td>
					<b>ID #<?php echo $res['product_id'] ?><br />
					<?php hecho($name)?>
<?php
if ('' != $product['sup_code']) {
?>
					(<?php hecho($product['sup_code'])?>)</b>
<?php
}
?>
				</td>
			</tr>
			<tr><td style="height:550px;width:550px;">
				<img height="550px" width="550px" src="https://www.converters.tv/products/images/<?php echo formatFileSafe($name) . '_' . $id . '_' . $res['product_id'] ?>.jpg" border="0"/>
			</td></tr>
			<tbody style="font-size:15px;">
			<tr><td><b>Package Contents :</b></td></tr>
			<tr><td style="padding-left:20px;">1 x <?php hecho($prod[0]['value'])?></td></tr>
<?php
if (count($contents)) {
            foreach ($contents as $key => $array) {
                if ('manual' == $array['field']) {
                    $array['value'] = 'Product Manual';
                }

?>
			<tr><td style="padding-left:20px;"><?php echo $array['qty'] ?> x <?php hecho($array['value'])?></td></tr>
<?php
}
        }
        if (count($power)) {
?>
			<tr><td style="padding-left:20px;"><?php echo $power[0]['qty'] ?> x <?php hecho($power[0]['value'])?></td></tr>
<?php
if (count($power) > 1) {
?>
			<tr><td style="padding-left:20px;"><?php echo $power[1]['qty'] ?> x <?php hecho($power[1]['value'])?></td></tr>
<?php
}
        }
?>
			</tbody>
		</table>
	</body>
</html>
<?php
} elseif (file_exists($file)) {
        switch (strtolower($type)) {
            case "jpg":
            case "jpeg":
                header("Content-type: image/jpeg");
                break;
            case "gif":
                header("Content-type: image/gif");
                break;
            case "png":
                header("Content-type: image/png");
                break;
            default:
                echo "Image not recognized.";
                return 1;
        }
        echo file_get_contents($file);
    }
}

function viewThumbnail($id)
{
    global $DB;
    $id = intval($id);
    if (!$id) {
        die();
    }

    $res       = $DB->query_first("SELECT `product_id`, `filename`, `filetype`, `size`, `caption` FROM `hg_products`.`products_screenshots` WHERE `id` = $id LIMIT 1");
    $filename  = $res['filename'];
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/images/screenshots/';
    $thumb_dir = $dir . 'thumbnails/';
    $size      = $res['size'];
    //$file = ((file_exists($thumb_dir.$filename) && filesize($thumb_dir.$filename) > $size)  ? $dir : $thumb_dir).$filename;
    $file = ($thumb_dir . $filename);
    $type = $res['filetype'];
    if (file_exists($file)) {
        switch (strtolower($type)) {
            case "jpeg":
            case "jpg":
                header("Content-type: image/jpeg");
                break;
            case "gif":
                header("Content-type: image/gif");
                break;
            case "png":
                header("Content-type: image/png");
                break;
            default:
                echo "Image not recognized.";
                return 1;
        }
        echo file_get_contents($file);
    }
    die();
}

function viewProductImage($product_id, $sub_id, $thumb)
{
    global $DB;

    $product_id = intval($product_id);
    $res        = $DB->query_first("SELECT `image`, `filetype`, `size` FROM `hg_products`.`products` WHERE `id` = $product_id AND `sub_id` = $sub_id LIMIT 1", MYSQLI_ASSOC);
    $filename   = $res['image'];
    $dir        = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/images/products/';
    $thumb_dir  = $dir . 'thumbnails/';
    $size       = $res['size'];
    $file       = (($thumb) ? $thumb_dir : $dir) . $filename;
    //$file = (($thumb) ? (((filesize($thumb_dir.$filename) > $size)  ? $dir : $thumb_dir).$filename) : $dir.$filename);
    $type = strtolower($res['filetype']);
    if ('' == $type) {
        list($pic, $type) = explode('.', $res['image']);
    }

    if (is_file($file)) {
        switch ($type) {
            case "jpeg":
            case "jpg":
                header("Content-type: image/jpeg");
                break;
            case "gif":
                header("Content-type: image/gif");
                break;
            case "png":
                header("Content-type: image/png");
                break;
            default:
                echo "Image not recognized.";
                return 1;
        }
        readfile($file);
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        return;
    }
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
        viewScreenshot($posted['ss_id'], $posted['thumb']);
        break;
    case "view_product":
        viewProductImage($product_id, $sub_id, $posted['thumb']);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
?>