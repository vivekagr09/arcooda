<?php
return;

include_once('suppliers_common.php');

$supplier_id=intval($posted['supplier_id']);
$thumb=$posted['thumb'];
if(!$supplier_id) die('Invalid ID passed');
$dir=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/';
$thumb_dir=$dir.'thumbnails/';
//$row=$DB->query_first("SELECT image,filetype FROM suppliers WHERE id=$supplier_id LIMIT 1", MYSQLI_ASSOC);
if($thumb=='true' && is_file($thumb_dir.$row['image'])) $dir=$thumb_dir;
if($row['image']!='')
{
	$ext=$row['filetype'];
	switch(strtolower($ext))
	{
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
	echo file_get_contents($dir.$row['image']);
	die();
}
echo 'No Image Found...';
die();
