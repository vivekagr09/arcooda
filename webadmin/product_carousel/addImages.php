<?php
include_once __DIR__ . '/common.inc.php';

$posted = array_merge($_GET,$_POST,$_FILES);
$action = strtolower($posted['action']);
$name = '';
$product_id = 0;
if (array_key_exists("name", $posted)) $name = $posted['name'];
if (array_key_exists("product_id", $posted)) $product_id = intval($posted['product_id']);
if (array_key_exists("id", $posted)) $id = intval($posted['id']);

function showAddScreenshot($product_id)
{
?>
<html>
	<head><title>Add Screenshot</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File:</td>
					<td style="text-align:left;">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
						<input style="width:450px;font: 13px verdana;" type="file" name="image_file[]" accept="image" multiple="true" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Title:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="title"/></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><textarea style="width:365px;font: 13px verdana;" type="text" name="image_desc" rows="5"></textarea></td>
				</tr>
					<tr>
					<td style="text-align:right;font: bold 14px verdana;">Link Url:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="url" name="link"/></td>
				</tr>
				</tr>
					<tr>
					<td style="text-align:right;font: bold 14px verdana;"></td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="hidden" name="button_text"/></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}
function showEditScreenshot($id)
{
	global $DB;
    $shot = $DB->query_first("SELECT * from `product_page_carousel_images` WHERE id=".$id,MYSQL_ASSOC);
?>
<html>
	<head><title>Edit Image</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_edit" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="hidden" name="id" value="<?=$id?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File:</td>
					<td style="text-align:left;"><input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image" /></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Title:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="title" value="<?=htmlspecialchars($shot['title'])?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><textarea style="width:365px;font: 13px verdana;" type="text" name="image_desc" rows="5"><?=htmlspecialchars($shot['caption'])?></textarea></td>
				</tr>
					<tr>
					<td style="text-align:right;font: bold 14px verdana;">Link Url:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="url" name="link" value="<?=$shot['link']?>"/></td>
				</tr>
				<tr>
					<td align="center" colspan=2>
						<a href="addImages.php?name=<?= $shot['name']?>&action=view&thumb=true" target="_blank">
							<img src="addImages.php?name=<?= $shot['name']?>&action=view&thumb=true" width="96" height="96">
						</a>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Update Image" /></td></tr>
			</table>
		</form>
</body></html>
<?php
}

function editScreenshot($posted)
{
    global $DB;

    $config['live'] = true;

    $id         = intval($posted['id']);

    $file_orig = $posted['image_file']['name'];
    $file_temp = $posted['image_file']['tmp_name'];
    $error     = $posted['image_file']['error'];
    $size      = intval($posted['image_file']['size']);
    $caption   = $posted['image_desc'];
    $title     = $posted['title'];
    $link      = $posted['link'];

    //update the fields first, before updating the image...
    $DB->query("UPDATE product_page_carousel_images SET caption=" . formatSQLString($caption) . ", title=" . formatSQLString($title) . ", link='" . $posted['link'] . "' WHERE id=$id");

    if ($size && !$error) {
        $file_ext = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' ;
	    $name = time().'_'.rand();
		$file_name  = $name . "." . $file_ext;

        if(move_uploaded_file($file_temp,$dir.$file_name)){
            $DB->query("UPDATE product_page_carousel_images SET name =" . formatSQLString($file_name) . ", file_orig=" . formatSQLString($file_orig) . ", size=$size, filetype='$file_ext' WHERE id=$id");
        }
    }

    // now format the JS Array to pass to other page
    $data = array();
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
<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?=$product_id?> successfully updated</b></td></tr>
<tr><td align="center"><input type="button" onclick="window.opener.location=window.opener.location;window.close();" value="Close Window" /></td></tr>
</table>
</body>
</html>
<?php
}

function addScreenshot($posted)
{
	global $_GLOBALS;
	global $DB;

	if(isset($posted['image_file']['name'])){
		$data=[];
		foreach ($posted['image_file']['name'] as $key => $file) {
			$file_orig = $file;
			$file_temp = $posted['image_file']['tmp_name'][$key];
			$error = $posted['image_file']['error'][$key];
			$caption = ($posted['image_desc'] != '') ? $posted['image_desc'] : $posted['image_file']['name'][$key];
			$link = $posted['link'];
			$button_text = $posted['button_text'];
			$size = intval($posted['image_file']['size'][$key]);
			$file_ext=substr($file_orig,strrpos($file_orig,'.'));
			if(substr($file_ext,0,1)=='.') 
			$file_ext=substr($file_ext,1);
			if($size && !$error)
			{
				$dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' ;
			    $name = time().'_'.rand();
				$file_new  = $name . "." . $file_ext;
				if(move_uploaded_file($file_temp,$dir.$file_new))
				{
					/*if (in_array(strtolower($file_ext), array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'tif', 'tiff'))) {
                        exec('/usr/bin/convert -resize 1150x580! ' . $dir . $file_new . ' ' . $dir . $file_new);
                    }*/
					$data[] = array(
						'image'       => $file_new,
						'title'       => $_POST['title'], 
						'caption'     => $caption,
						'button_text' => $button_text,
						'link'        => $link, 
						'staff'       => $_GLOBALS['staff']['user'],
						'filetype'    => $file_ext,
						'size'        => formatFileSize($size),
					);
				}
			}
		} 
		?>
		<html>
			<head>
				<script type="text/javascript">
		//<![CDATA[
		var data = <?= json_encode($data) ?>;
		//]]>
				</script>
			</head>
			<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
			<tr><td style="text-align:center;"><b>Crousel images successfully added</b></td></tr>
			<tr><td align="center"><input type="button" onclick="window.opener.addScreenShot(data);window.close();" value="Close Window" /></td></tr>
			</table>
			</body>
		</html>
<?php	}
}



function viewScreenshot($name, $thumb)
{
	$dir=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/';
	$thumb_dir = $dir;
	$file = (($thumb) ? (((file_exists($thumb_dir.$name))  ? $thumb_dir : $dir)) : $dir);
	$file=$file.$name;
	$type=substr($name,strrpos($name,'.'));
	if(substr($type,0,1)=='.') 
	$type=substr($type,1);
	if($name==''){
		 $file = $_SERVER['DOCUMENT_ROOT'].'/svg/uploadimage.png';
		 $type='png';
	}
	

		switch($type)
		{
			case "jpg":
				header("Content-type: image/jpeg");
				break;
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




/**********************************************************Functions Definitions: End ********************************************************************/


// Main Switch-Case...
switch($action)
{
	case "add":
		showAddScreenshot($id);
		break;
	case "do_add":
		addScreenshot($posted);
		break;
	case "view":
		viewScreenshot($posted['name'], $posted['thumb']);
		break;
	case "edit":
		showEditScreenshot($posted['id']);
		break;
	case 'do_edit':
        editScreenshot($posted);
        break;
	case "":
	default:
		die("Invalid Selection!!");
}
