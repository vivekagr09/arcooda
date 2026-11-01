<?php
return;

include_once __DIR__ . '/common.inc.php';

$quote_id = $posted['quote_id'];
if (!$quote_id) {
    die("No Quote ID supplied!!!");
}

$posted = array_merge($_GET, $_POST);

$action = $posted['action'];
$method = $posted['method'];

function showAddAttachment($quote_id, $method)
{
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="quote_id" value="<?=$quote_id?>" />
			<input type="hidden" name="method" value="<?=$method?>" />
			<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
				<!--<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><input style="width:200px;font: 13px verdana;" type="text" name="image_desc"/></td>
				</tr>-->
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File:</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:285px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                    </td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Upload File"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

function doAdd($posted)
{
    global $DB;

    $file_orig = $posted['image_file']['name'];
    $quote_id  = intval($posted['quote_id']);
    $method    = $posted['method'];
    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    if ($posted['image_file']['size'] && !$posted['image_file']['error']) {
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Quote #<?=$quote_id?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.close()" value="Close Window" /></td></tr>
	</table>
	</body>
</html>
<?php
}
}

function viewQuoteAttachment($quote_id, $method)
{
    global $DB;
    $quote_id = intval($quote_id);
    if (!$quote_id) {
        return;
    }

    $self_time   = filemtime($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
    $self_gmdate = gmdate(DATE_RFC1123, $self_time);
    $want_time   = $self_time;

    $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if ($if_modified_since == $self_gmdate) {
        header("HTTP/1.0 304 Not Modified");
        die();
    }

    $fieldname     = (('sea' == $method) ? 'lading' : 'master_airway') . '_file';
    $origFieldname = $fieldname . '_orig';
    $filename      = $quote[$fieldname];
    $file_orig     = $quote[$origFieldname];
    $mime_type     = mime_content_type($filename);

    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . $self_gmdate);
    header('Content-Length: ' . $quote['filesize']);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $file_orig . '"');

    $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/supplier/attachment/';
    echo file_get_contents($dir . $filename);
}

switch ($action) {
    case 'do_add':
        doAdd($posted);
        break;
    case 'view':
        viewQuoteAttachment($quote_id, $method);
        break;
    case 'add':
    default:
        showAddAttachment($quote_id, $method);
        break;
}
