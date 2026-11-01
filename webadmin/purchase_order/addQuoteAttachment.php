<?php
include_once __DIR__ . '/common.inc.php';

$quote_id = $posted['quote_id'];
if (!$quote_id) {
    die("No Quote ID supplied!!!");
}

$posted = undo_magicquotes(array_merge($_GET, $_POST, $_REQUEST, $_FILES));

$action = $posted['action'];
$method = $posted['method'];

function showAddAttachment($quote_id, $method)
{
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">

<form accept-charset="utf-8" enctype="multipart/form-data" action="" method="post">
    <input type="hidden" name="action" value="do_add" />
    <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
    <input type="hidden" name="quote_id" value="<?= $quote_id ?>" />
    <input type="hidden" name="method" value="<?= $method ?>" />
    <table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
        <!--<tr>
            <td style="text-align:right;font: bold 14px verdana;">Description:</td>
            <td style="text-align:left;"><input style="width:200px;font: 13px verdana;" type="text" name="image_desc"/></td>
        </tr>-->
        <tr>
            <td style="text-align:right;font: bold 14px verdana;">File:</td>
            <td style="text-align:left;"><input style="width:285px;font: 13px verdana;" type="file" name="image_file" accept="image"/></td>
        </tr>
        <tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Upload File"/></td></tr>
    </table>
</form>

    </body>
</html>
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
        $fileOrig = $posted['image_file']['name'];
        $set      = array();
        $tab      = 'freight_services_quotes';
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

        $set[] = $filename . '=' . formatSQLString($fileNew);
        $set[] = $filename2 . '=' . formatSQLString($posted['image_file']['name']);
        $set[] = 'filetype=' . formatSQLString($file_ext);
        $set[] = 'filesize=' . intval($posted['image_file']['size']);
        if ('courier' == $method) {
            $tab = 'invoice_courier_quotes';
        }

        $sql = "UPDATE " . $tab . " SET " . implode(', ', $set) . " WHERE id=" . intval($quote_id);
        if (move_uploaded_file($posted['image_file']['tmp_name'], $dir . $fileNew)) {
            $DB->query($sql);
        }

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Attachment for Quote #<?= $quote_id ?> successfully added</b></td></tr>
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

    $sql           = "SELECT * FROM `freight_services_quotes` WHERE `id` = " . $quote_id;
    $quote         = $DB->query_first($sql, MYSQLI_ASSOC);
    $fieldname     = (('sea' == $method) ? 'lading' : (('sea2' == $method) ? 'lading2' : ((('air' == $method) ? 'master' : 'house') . '_airway'))) . '_file';
    $origFieldname = $fieldname . '_orig';
    $filename      = $quote[$fieldname];
    $file_orig     = $quote[$origFieldname];
    $mime_type     = mime_extension_type($filename);
    if ('text/plain' == $mime_type) {
        $mime_type = mime_content_type($mime_type);
    }

    $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/quote_attachments/';
    header('Date: ' . gmdate(DATE_RFC1123, $_SERVER['REQUEST_TIME']));
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
