<?php
$attachment_dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/attachments';
include_once($_SERVER['DOCUMENT_ROOT'] . '/webadmin/connection.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/query_attachment.inc.php');

$product_id = max(0, intval($_REQUEST['product_id']));
$attachment_id = max(0, intval($_REQUEST['attachment_id']));
$thumbnail = isset($_REQUEST['thumb']);
if (!productAttachmentExists($attachment_id, $product_id)) die();
$image = productAttachmentFromDatabase($attachment_id);
$filename = $attachment_dir . '/' . $image['file_name'];
$extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
header('Content-Disposition: inline; filename="' . urlencode($image['file_original']) . '"');
$mimetype = getExtMIMEType($filename);
if (is_file($filename))
{
	if ($thumbnail)
	{
		header('Content-Type: image/png');
		if (substr($mimetype, 0, 6) == 'image/')
		{
			passthru('/usr/bin/convert -resize 96x96! ' . $filename . ' png:-');
		}
		else
		{
			$outfile = "unknown.png";

			//Category thumbs
			if (substr($mimetype, 0, 6) == "audio/") $outfile = "media.png";
			if (substr($mimetype, 0, 6) == "image/") $outfile = "image.png";
			if (substr($mimetype, 0, 8) == "message/") $outfile = "message.png";
			if (substr($mimetype, 0, 5) == "text/") $outfile = "text.png";
			if (substr($mimetype, 0, 6) == "video/") $outfile = "media.png";

			//Extra thumbs
			if (in_array($extension, array('eml', 'msg', ))) $outfile = "message.png";
			if (in_array($extension, array('txt', 'asc', ))) $outfile = "text.png";
			if (in_array($extension, array('7z', 'ace', 'arj', 'bz', 'bz2', 'cab', 'gz', 'iso', 'jar', 'lha', 'lzh', 'rar', 'tar', 'taz', 'tbz', 'tbz2', 'tgz', 'uu', 'uue', 'xxe', 'z', 'zip', ))) $outfile = "archive.png";
			if (in_array($extension, array('docxml', 'wiz', 'dot', 'dothtml', 'dochtml', 'doc', 'wbk', 'rtf', ))) $outfile = "document.png";
			if (in_array($extension, array('mdw', 'mav', 'mat', 'mas', 'mar', 'ldb', 'maq', 'ade', 'adp', 'mad', 'mde', 'mam', 'wizhtml', 'mdbhtml', 'maf', 'mag', 'mdz', 'maw', 'adn', 'mdn', 'mdb', 'mdt', 'mda', ))) $outfile = "database.png";
			if (in_array($extension, array('ppa', 'ppthtml', 'pothtml', 'ppt', 'pps', 'pot', 'pwz', ))) $outfile = "slideshow.png";
			if (in_array($extension, array('xlm', 'xld', 'xlc', 'xlb', 'xls', 'xla', 'xlk', 'csv', 'dif', 'xlshtml', 'xlthtml', 'dqy', 'oqy', 'rqy', 'slk', 'xlt', 'xlv', 'iqy', 'xlw', 'xll', 'xlxml', ))) $outfile = "spreadsheet.png";
			if (in_array($extension, array('html', 'htm', ))) $outfile = "html.png";
			if (in_array($extension, array('pdf', ))) $outfile = "acrobat.png";

			readfile($_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/filetypes/' . $outfile);
		}
	}
	else
	{
		header('Content-Type: ' . $mimetype);
		readfile($filename);
	}
}
die();
?>