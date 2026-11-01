<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$quote_id = $posted['quote_id'];
if (!$quote_id) {
    die("No Quote ID supplied!!!");
}

$posted = array_merge($_GET, $_POST, $_FILES);

$action = $posted['action'];
$method = $posted['method'];

/**
 * @param int    $quote_id
 * @param string $method
 *
 * @return void
 */
function showAddAttachment($quote_id, $method)
{
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="quote_id" value="<?=$quote_id?>" />
			<input type="hidden" name="method" value="<?=$method?>" />
			<table style="width:30%;border:1px solid blue;background-color:#99CCFF;margin:auto">
				<!--<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;"><input style="width:200px;font: 13px verdana;" type="text" name="image_desc"/></td>
				</tr>-->
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">File:</td>
					<td style="text-align:left;">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
						<input style="width:285px;font: 13px verdana;" type="file" name="image_file" accept="image"/>
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Upload File"/></td></tr>
			</table>
		</form>
	</body>
</html>
<?php
}

/**
 * @param mixed[] $posted
 *
 * @return void
 */
function doAdd($posted)
{
    global $DB;

    $quote_id  = intval($posted['quote_id']);
    $method    = $posted['method'];
    $file_ext  = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);

    if (array_key_exists('image_file', $_FILES) && UPLOAD_ERR_OK === $_FILES['image_file']['error']) {
        $dir    = $_SERVER["DOCUMENT_ROOT"] . "/webadmin/invoice/quote_attachments/";
        $prefix = 'tracking';
        if ('sea' == $method) {
            $prefix = 'lading';
        } elseif ('sea2' == $method) {
            $prefix = 'lading2';
        } elseif ('air' == $method) {
            $prefix = 'master_airway';
        } elseif ('air2' == $method) {
            $prefix = 'house_airway';
        }

        $fileNew  = $prefix . "_" . $quote_id . '.' . $file_ext;
        $set      = array();
        $tab      = '`highwaygroup`.`freight_services_quotes`';
        $filename = 'tracking';
        if ('sea' == $method) {
            $filename = 'lading';
        } elseif ('sea2' == $method) {
            $filename = 'lading2';
        } elseif ('air' == $method) {
            $filename = 'master_airway';
        } elseif ('air2' == $method) {
            $filename = 'house_airway';
        }

        $filename2 = $filename . '_file_orig';
        $filename .= '_file';

        $set[] = $filename . ' = ' . formatSQLString($fileNew);
        $set[] = $filename2 . ' = ' . formatSQLString($_FILES['image_file']['name']);
        $set[] = '`filetype` = ' . formatSQLString($file_ext);
        $set[] = '`filesize` = ' . intval($_FILES['image_file']['size']);
        if ('courier' == $method) {
            $tab = '`highwaygroup`.`invoice_courier_quotes`';
        }

        $sql = "UPDATE " . $tab . " SET " . implode(', ', $set) . " WHERE id=" . intval($quote_id);
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dir . $fileNew)) {
            $DB->query($sql);
        }

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<table style="width:30%;border:1px solid blue;background-color:#99CCFF;margin:auto">
			<tr><td style="text-align:center;"><b>Attachment for Quote #<?=$quote_id?> successfully added</b></td></tr>
			<tr><td style="text-align:center"><input type="button" onclick="window.close()" value="Close Window" /></td></tr>
		</table>
	</body>
</html>
<?php
    }
}

/**
 * @param int    $quote_id
 * @param string $method
 *
 * @return void
 */
function viewQuoteAttachment($quote_id, $method)
{
    global $DB;
    $quote_id = intval($quote_id);
    if (!$quote_id) {
        return;
    }

    $self_time   = filemtime($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
    if (false === $self_time) {
        $self_time = time();
    }
    $self_gmdate = gmdate(DATE_RFC1123, $self_time);

    $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if ($if_modified_since == $self_gmdate) {
        header("HTTP/1.0 304 Not Modified");
        die();
    }

    $quote = $DB->query_first(sprintf(
        "SELECT * FROM `highwaygroup`.`freight_services_quotes` WHERE `id` = %d LIMIT 1",
        $quote_id
    ), MYSQLI_ASSOC);
    $fieldname     = (('sea' == $method) ? 'lading' : (('sea2' == $method) ? 'lading2' : ((('air' == $method) ? 'master' : 'house') . '_airway'))) . '_file';
    $origFieldname = $fieldname . '_orig';
    $filename      = $quote[$fieldname];
    $file_orig     = $quote[$origFieldname];
    $mime_type     = mime_content_type($filename);

    $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/quote_attachments/';
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header('Last-Modified: ' . $self_gmdate);
    header('Content-Length: ' . filesize($dir . $filename));
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . urlencode($file_orig) . '"');
    header("Content-Transfer-Encoding: binary");
    readfile($dir . $filename);
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
