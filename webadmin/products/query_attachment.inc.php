<?php
function productAttachmentExists($attachment_id,$product_id=false)
{
	global $DB;

	$attachment_id=intval($attachment_id);
	$sql="SELECT attachment_id FROM product_attachments WHERE attachment_id=".$attachment_id;
	if($product_id!==false) $sql.=" AND product_id=".intval($product_id);
	$row=$DB->query_first($sql,MYSQLI_ASSOC);

	if($row) return true;
	return false;
}

function productAttachmentsFTPToDatabase($product_id)
{
	global $DB;

	$product_id=intval($product_id);
	$ftp_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/manuals_ftp/product_'.$product_id;
	$attachment_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/attachments';

	if(file_exists($ftp_dir) && is_dir($ftp_dir))
	{
		$hdir=opendir($ftp_dir);
		while(false!==($row=readdir($hdir)))
			if(substr($row,0,1)!='.')
			{
				$extension=substr($row,strrpos($row,'.')+1);
				$before=$DB->query_first("SELECT COUNT(*) as total FROM product_attachments WHERE product_id='".$product_id."'");
				$product=$DB->query_first("SELECT * FROM products p, product_types pt WHERE p.id='".$product_id."' AND pt.id=p.type");
				$letter=chr(ord('a')+$before['total']);
				$new_file=$product_id. $letter. '_'. str_replace(" ","_",$product['product_name']). '_'. $product['name']. '.'. $extension;
				$size=filesize($ftp_dir.'/'.$row);

				if(rename($ftp_dir.'/'.$row,$attachment_dir.'/'.$new_file))
					$DB->query("INSERT INTO product_attachments(user, attach_date, file_name, file_original, description, filetype, manual_type, size, product_id) VALUES('FTP', NOW(), '".addslashes($new_file)."', '".addslashes($row)."', 'FTP Uploaded Manual', '".$extension."', 'coe', '".$size."',".$product_id.")");
			}
		closedir($hdir);
	}

}

function productAttachmentsFromFTP($product_id)
{
	$results=array();
	$product_id=intval($product_id);
	$ftp_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/manuals_ftp/product_'.$product_id;
	if(file_exists($ftp_dir) && is_dir($ftp_dir))
	{
		$hdir=opendir($ftp_dir);
		while(false!==($row=readdir($hdir)))
			if(substr($row,0,1)!='.')
				$results[]=$row;
		closedir($hdir);
	}
	return $results;
}

function productAttachmentsFromDatabase($product_id)
{
	global $DB;

	$product_id=intval($product_id);
	$sql="SELECT attachment_id FROM product_attachments WHERE product_id=".$product_id;
	$results=array();
	$query=$DB->query($sql);
	while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
		$results[]=$row['attachment_id'];

	return $results;
}

function productAttachmentFromFTP($file_name,$product_id=0)
{
	$product_id=intval($product_id);
	$ftp_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/manuals_ftp/product_'.$product_id;
	if(!is_file($ftp_dir.'/'.$file_name)) return;

	$result=array();
	$result['attachment_id']=$file_name;
	$result['product_id']=$product_id;
	$result['attach_date']=filemtime($ftp_dir.'/'.$file_name);
	$result['file_name']=$file_name;
	$result['file_original']=$file_name;
	$result['description']=$file_name;
	$result['filetype']=substr($file_name,strrpos($file_name,'.')+1);

	return $result;
}

function productAttachmentFromDatabase($attachment_id,$product_id=false)
{
	global $DB;
	$attachment_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/attachments';
	if(!productAttachmentExists($attachment_id,$product_id)) return false;

	$attachment_id=intval($attachment_id);
	$sql="SELECT * FROM product_attachments WHERE attachment_id=".$attachment_id." LIMIT 1";
	$row = $DB->query_first($sql,MYSQLI_ASSOC);
	if($row['size'] == 0){
		$filename=$attachment_dir.'/'.$row['file_name'];
		$new_size = filesize($filename);
		$DB->query("UPDATE product_attachments SET size='$new_size' WHERE attachment_id = $attachment_id");
	}

	$sql="SELECT * FROM product_attachments WHERE attachment_id=".$attachment_id." LIMIT 1";


	return $DB->query_first($sql,MYSQLI_ASSOC);
}

function productAttachmentDelete($attachment_id,$product_id=false)
{
	global $DB;
	if(!productAttachmentExists($attachment_id,$product_id)) return false;

	$attachment_id=intval($attachment_id);
	$sql="DELETE FROM product_attachments WHERE attachment_id=".$attachment_id;
	$DB->query($sql);

	return true;
}

function productAttachmentGetFTPURI($product_id=0)
{
	$product_id=intval($product_id);
	$manuals_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/manuals_ftp';
	$product_dir='product_'.$product_id;
	if($product_id>0)
	{
		if(!is_dir($manuals_dir.'/'.$product_dir))
		{
			mkdir($manuals_dir.'/'.$product_dir);
//			chmod($manuals_dir.'/'.$product_dir,0777);
		}
		return 'ftp://'. urlencode('manuals@coinopexpress.com'). ':'. urlencode('manuals'). '&#64;ftp.coinopexpress.com/'. $product_dir. '/';
	}
	return 'ftp://'.urlencode('manuals@coinopexpress.com').':'.urlencode('manuals').'&#64;ftp.coinopexpress.com/';
}

function updateFileSize()
{
	global $DB;
	$attachment_dir=$_SERVER['DOCUMENT_ROOT'].'/webadmin/products/attachments';
	$sql="SELECT * FROM product_attachments";
	$res = $DB->query($sql,MYSQLI_ASSOC);
	while($row = $DB->fetch_array($res)){
		if($row['size'] == 0){
			$attachment_id=intval($row['attachment_id']);
			$filename=$attachment_dir.'/'.$row['file_name'];
			//echo 'File Name = '.$filename.'<br />';
			$new_size = filesize($filename);
			$DB->query("UPDATE product_attachments SET size='$new_size' WHERE attachment_id = $attachment_id");
		}
	}
}
?>