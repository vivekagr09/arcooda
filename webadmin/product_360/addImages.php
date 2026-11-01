<?php
include_once __DIR__ . '/common.inc.php';

$posted = array_merge($_GET,$_POST,$_FILES);
$action = strtolower($posted['action']);
$name = '';
$product_id = 0;
if (array_key_exists("name", $posted)) $name = $posted['name'];
if (array_key_exists("product_id", $posted)) $product_id = intval($posted['product_id']);


/**********************************************************Functions Definitions ************************************************************************/
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
					<td style="text-align:right;font: bold 14px verdana;">Caption:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="image_desc"/></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
			<p>Or</p>
			<h3>Search Existing Product Database</h3>
		<form action="" method="post">
			<input type="hidden" name="action" value="search_product" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
			<tr>
				<td style="text-align:right;font: bold 14px verdana;">Product ID/Name:</td>
				<td style="text-align:left;"><input style="width:450px;font: 13px verdana;" type="text" name="searchid" required="true" value="<?= $_REQUEST['searchid']?>"/></td>
			</tr>
			<tr style="margin-top: 10px;"><td style="text-align:right;">&nbsp;</td><td style="text-align:center;"><input type="submit" value="Search" style="padding: 5px; margin: 5px;"/></td></tr>
		</table>
		<form>
			

</body></html>
<?php
}

function ShowsearchProduct($search){
	moduleLoad('product');

	$criteria = array();
	$criteria['any_text'] = preg_split('/[^a-zA-Z0-9]+/',$search);
    $product=productSearch($criteria);
    if(count($product)){
    	$images=productScreenshotsFromDatabase($product[0]);
    	if(count($images)){
    		echo '<form><table class="table_grid" style="width:600px;border:1px solid black;margin-top:10px;" align="center"><input type="hidden" name="action" value="add_product_images" /><tr><th>Select Image</th><th>Image</th><th>Caption</th></tr>';
		 	foreach ($images as $key => $v) { 
		 		if ('' !== $v['image_url'] && file_exists(productGetOptionList('screenshot_path') . $v['filename'])) {
		 		?>
			 	<input type="hidden" name="productimage[<?= $key ?>][filename]" value="<?= $v['filename']?>" >
			 	<tr>
			 		<td width="20%;" align="center"><input type="checkbox" name="productimage[<?= $key ?>][acceptfile]" value="<?= $v['filename']?>"> </td>
			 		<td width="30%"><img src="/webadmin/products_new/addScreenshot.php?ss_id=<?=$v['ss_id'] ?>&action=view" width="150px;"></td>
			 		<td width="50%"><input type="text" name="productimage[<?= $key ?>][caption]" value="<?= $v['caption']?>" style="width:100%;"> </td>
			 	</tr>
		<?php }	} echo '</table><input type="submit" value="Select Images" style="padding: 5px; margin: 5px;"/></form>';
    	}else{
    		echo '<h3>No image found for this product</h3>';
    	}
   
    }else{
    	echo '<h3>No match result found</h3>';
    }
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
			$size = intval($posted['image_file']['size'][$key]);

			$file_ext=substr($file_orig,strrpos($file_orig,'.'));
			if(substr($file_ext,0,1)=='.') 
			$file_ext=substr($file_ext,1);
			if($size && !$error)
			{
				$file_new = "view_".rand().'_'.rand().'.'.$file_ext; // new format to avoid any errors with overwriting of filenames..
				$dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/360view/';
				$thumb_largedir = $dir.'largeimage/';
				$thumb_smalldir = $dir.'smallimage/';
				if(move_uploaded_file($file_temp,$dir.$file_new))
				{
					if (in_array(strtolower($file_ext), array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'tif', 'tiff'))) {
                        exec('/usr/bin/convert -resize 1600x1600! ' . $dir . $file_new . ' ' . $thumb_largedir . $file_new);
                        exec('/usr/bin/convert -resize 800x800! ' . $dir . $file_new . ' ' . $thumb_smalldir . $file_new);
                    }
					// now format the JS Array to pass to other page
					$data[] = array(
						'image'    => $file_new,
						'caption'  => $caption,
						'staff'    => $_GLOBALS['staff']['user'],
						'filetype' => $file_ext,
						'size'     => formatFileSize($size),
						'type'     => 'local',
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
			<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?= $posted['id'] ?> successfully added</b></td></tr>
			<tr><td align="center"><input type="button" onclick="window.opener.addScreenShot(data);window.close();" value="Close Window" /></td></tr>
			</table>
			</body>
		</html>
<?php	}
}

function addProductImages($posted){
	moduleLoad('product');

	global $_GLOBALS;

	$countpro=0;
	$data=[];
	foreach ($posted['productimage'] as $key => $value) {
		$file_ext=substr($value['filename'],strrpos($value['filename'],'.'));
			if(substr($file_ext,0,1)=='.') 
			$file_ext=substr($file_ext,1);
		$size=filesize(productGetOptionList('screenshot_path') . $value['filename']);
		if(isset($value['acceptfile'])){
			$countpro++;
			$data[] = array(
				'image'    => $value['filename'],
				'caption'  => $value['caption'],
				'staff'    => $_GLOBALS['staff']['user'],
				'filetype' => $file_ext,
				'size'     => formatFileSize($size),
				'type'     => 'product'
			);
		}

	} ?>
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
			<tr><td style="text-align:center;"><b><?= $countpro?> product Screenshots  successfully added</b></td></tr>
			<tr><td align="center"><input type="button" onclick="window.opener.addScreenShot(data);window.close();" value="Close Window" /></td></tr>
			</table>
			</body>
		</html>
<?php } 


function viewScreenshot($name, $thumb,$type)
{
	moduleLoad('product');

	if($type=='product'){
		$dir = productGetOptionList('screenshot_path');
		$thumb_dir = $dir;
	}else{
		$dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/360view/';
		$thumb_dir = $dir.'smallimage/';
	}
	
	$file = (($thumb) ? (((file_exists($thumb_dir.$name))  ? $thumb_dir : $dir)) : $dir);
	$file=$file.$name;
	$type=substr($name,strrpos($name,'.'));
	if(substr($type,0,1)=='.') 
	$type=substr($type,1);
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
		viewScreenshot($posted['name'], $posted['thumb'],$posted['type']);
		break;
	case "search_product":
		showAddScreenshot($id);
		ShowsearchProduct($_REQUEST['searchid']);
		break;
	case "add_product_images":
		addProductImages($posted);
		break;
	case "":
	default:
		die("Invalid Selection!!");
}
