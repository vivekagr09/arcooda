<?php
include_once __DIR__ . '/common.inc.php';
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
$posted = array_merge($_GET,$_POST,$_FILES);
$action = strtolower($posted['action']);
$name = '';
$news_id = 0;
if (array_key_exists("name", $posted)) $name = $posted['name'];
if (array_key_exists("news_id", $posted)) $news_id = intval($posted['news_id']);

function showAddScreenshot($news_id)
{
?>
<html>
	<head><title>Add Image</title></head>
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
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><textarea style="width:365px;font: 13px verdana;" type="text" name="image_desc" rows="5"></textarea></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}


function addScreenshot($posted)
{
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
				$dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/extra/' ;
				$thumb_dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/thumbnail/' ;
			    $name = time().'_'.rand();
				$file_new  = $name . "." . $file_ext;

				if(move_uploaded_file($file_temp,$dir.$file_new))
				{
					if (in_array(strtolower($file_ext), array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tga', 'tif', 'tiff'))) {
                        exec('/usr/bin/convert -resize 96x96! ' . $dir . $file_new . ' ' . $thumb_dir . $file_new);
                    }
					$data[] = array('image'=>$file_new, 'caption'=>$caption, 'staff' => $_SERVER['REMOTE_USER'], 'filetype'=>$file_ext, 'size'=>formatFileSize($size));
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
			<tr><td style="text-align:center;"><b>Slider images successfully added</b></td></tr>
			<tr><td align="center"><input type="button" onclick="window.opener.addScreenShot(data);window.close();" value="Close Window" /></td></tr>
			</table>
			</body>
		</html>
<?php	}
}



function viewScreenshot($name, $thumb)
{
	$dir=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/extra/';
	$thumb_dir = $thumb_dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/thumbnail/';
	$file = (($thumb) ? (((file_exists($thumb_dir.$name))  ? $thumb_dir : $dir)) : $dir);
	$file=$file.$name;
	$type=substr($name,strrpos($name,'.'));
	if(substr($type,0,1)=='.') 
	$type=substr($type,1);
		switch(strtolower($type))
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
	case "":
	default:
		die("Invalid Selection!!");
}
