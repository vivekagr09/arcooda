<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
include_once __DIR__ . '/common.inc.php';

$posted = array_merge($_GET, $_POST, $_FILES);
$action = strtolower($posted['action']);
$id     = intval($posted['id']);
$ss_id=intval($posted['ss_id']);
/**********************************************************Functions Definitions ************************************************************************/
function showAddManual($product_id)
{
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<input type="hidden" name="id" value="<?=$product_id?>" />
            <input type="hidden" name="active" value="y" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;"> File:</td>
					<td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:450px;font: 13px verdana;" type="file" name="image_file" />
                    </td>
				</tr>
                <tr>
                    <td style="text-align:right;font: bold 14px verdana;"> Type:</td>
                    <td style="text-align:left;">
                        <select style="width:450px;font: 13px verdana;" name ="type">
                            <option value="Artwork">Artwork</option>
                            <option value="Drawings">Drawings</option>
                            <option value="Other">Other</option>
                            <option value="Production Costs">Production Costs</option>
                            <option value="Wiring Harness">Wiring Harness</option>
                        </select>
                       
                    </td>
                </tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Description:</td>
					<td style="text-align:left;">
                        <textarea style="width:365px;font: 13px verdana;" rows="5" name="image_desc" /></textarea>
                    </td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Upload"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}
function showEditAdminfile($id, $product_id)
{
    global $_GLOBALS;
    global $DB;
    $shot      = $DB->query_first("SELECT `id`,`file_name`,`description`,`active`, `filetype`, `size`, `file_original`,`type` FROM `product_admin_files` WHERE `id` = $id LIMIT 1");
?>
<html>
    <head><title>Edit Screenshot</title></head>
    <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
        <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="action" value="do_edit" />
            <input type="hidden" name="id" value="<?php echo $id ?>" />
            <input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
            <table align="center" style="width:100%;border:1px solid blue;background-color:#99CCFF;">
                <tr>
                    <td style="text-align:right;font: bold 14px verdana;">File:</td>
                    <td style="text-align:left;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                        <input style="width:90%;margin:5px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;font: bold 14px verdana;">Caption:</td>
                    <td style="text-align:left;">
                        <textarea style="width:90%;margin:5px;font: 13px verdana;" row="3" name="description" value="<?php hecho($shot['description'])?>"><?php hecho($shot['description'])?></textarea>
                    </td>
                </tr>
                 <tr>
                    <td style="text-align:right;font: bold 14px verdana;">File Name:</td>
                    <td style="text-align:left;">
                        <input style="width:90%;margin:5px;font: 13px verdana;" type="text" name="file_original" value="<?php hecho($shot['file_original'])?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;font: bold 14px verdana;"> Type:</td>
                    <td style="text-align:left;">
                        <select style="width:90%;margin:5px;font: 13px verdana;" name ="type">
                            <option value="Artwork" <?=  $shot['type']  == 'Artwork'?'selected':''?> >Artwork</option>
                            <option value="Drawings" <?=  $shot['type']  == 'Drawings' ?'selected':''?>>Drawings</option>
                            <option value="Other" <?=  $shot['type']  == 'Other' ?'selected':''?> >Other</option>
                            <option value="Production Costs" <?=  $shot['type']  == 'Production Costs' ?'selected':''?>>Production Costs</option>
                            <option value="Wiring Harness" <?=  $shot['type']  == 'Wiring Harness' ?'selected':''?> >Wiring Harness</option>
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
                        <a href="addAdminFiles.php?main_id=<?php echo $shot['id'] ?>&action=view" target="_blank">
                             <?php 
                            if($shot['filetype'] == 'pdf' or $shot['filetype'] == 'pptx'){
                                echo '<img  src="/images/pdf_new.jpg" width="100" style="width:96px; height:96px;background:white;">';
                            }else if($shot['filetype'] == 'xlsx' ){
                                echo '<img  src="/images/xls_new.png" width="100" style="width:96px; height:96px;background:white;">';
                            }else if($shot['filetype'] == 'docx' or $shot['filetype'] == 'doc'){
                                 echo '<img  src="/images/doc_new.png" width="100" style="width:96px; height:96px;background:white;">';
                            }else if($shot['filetype'] == 'bin' or $shot['filetype'] == 'bin'){
                                echo '<img  src="/images/bin-file-icon.png" width="100" style="width:86px; height:86px;background:white;">';
                            }else{
                                 echo '<img src="addAdminFiles.php?action=view&main_id='.$shot['id'].'" width="100" style="width:96px; height:96px;">';
                             }
                            ?>
                        </a>
                    </td>
                </tr>
                <tr><td align="center" colspan="2" style="line-height: 25px;"><input type="submit" value="Update Files" style="margin-top: 20px; margin-bottom: 10px; padding: 10px;"/></td></tr>
                <td align="center" colspan="2" style="line-height: 10px;"><div><i> By pressing Update Files button, it will save the refresh the page.</i></div></td></tr>
            </table>
        </form>
</body></html>
<?php
}
function getManualLetter($id)
{
    global $DB;
    $id    = intval($id);
    $array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $res   = $DB->query("SELECT `id` FROM `product_admin_files` WHERE `product_id` = $id");
    $count = $DB->num_rows($res);
    return $array[$count];
}

function addManual($posted)
{
    global $_GLOBALS;
    global $DB;
    $file_orig   = $posted['image_file']['name'];
    $file_temp   = $posted['image_file']['tmp_name'];
    $error       = $posted['image_file']['error'];
    $description = ('' != $posted['image_desc']) ? $posted['image_desc'] : $posted['image_file']['name'];
    $size        = intval($posted['image_file']['size']);
    $active      = $posted['active'];
    $type      = $posted['type'];
    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }
    if ($size && !$error) {
        $sql = "INSERT INTO `product_admin_files` (`product_id`, `attach_date`, `file_original`, `description`, `filetype`, `manual_type`, `size`, `active`,`updated_by`,`type`) VALUES (" . intval($posted['id']) . ", NOW(), " . $DB->escape($file_orig) . ", " . $DB->escape($description) . ", '$file_ext', 'cnv', $size, '$active', " . $DB->escape($_GLOBALS['staff']['id']) . "," . $DB->escape($type) . ")";

        $query  = $DB->query($sql);
        $main_id = $DB->insert_id;
        $file_new = "safety_" . $posted['id'] . getManualLetter($posted['id']) . '.' . $file_ext;
        $dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/admin_file/';
        //Now need to add the manual to the package contents...
        //productPackageContentsAdd(intval($posted['id']), 'manual', $description);

        if (move_uploaded_file($file_temp, $dir . $file_new)) {
            $DB->query("UPDATE `product_admin_files` SET `file_name` = " . $DB->escape($file_new) . " WHERE `id` = {$main_id}");

            // now format the JS Array to pass to the products other page
            $data = array(
                'id'          => $main_id,
                'edit'          => 0,
                'description' => $description,
                'filename'    => $file_new,
                'staff'       => $_GLOBALS['staff']['user'],
                'filetype'    => $file_ext,
                'size'        => formatFileSize($size),
                'active'      => $active,
                'file_orig'   => $file_orig,
            );
?>
<html>
	<head>
		<script type="text/javascript">
//<![CDATA[
var data =  <?php jecho($data) ?>;
//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Admin File for Product ID #<?=$posted['id']?> successfully added</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.opener.adminFileAdd(data);window.close();" value="Close Window" /></td></tr>
	</table>
	</body>
</html>
<?php
            return $main_id;
        }
    }
    return 0;
}
function editAdminfile($posted)
{
    global $_GLOBALS;
    global $DB;
    $file_orig   = $posted['image_file']['name'];
    $file_temp   = $posted['image_file']['tmp_name'];
    $error       = $posted['image_file']['error'];
    $description = ('' != $posted['description']) ? $posted['description'] : $posted['image_file']['name'];
    $size        = intval($posted['image_file']['size']);
    $active      = $posted['active'];
    $type = $posted['type'];
    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }
    $file_orig      = $posted['file_original'];
    $DB->query("UPDATE `product_admin_files` SET `description` = " . $DB->escape($description) . ",`type` = " . $DB->escape($type) . ",`active` = " . $DB->escape($active) . ",`file_original` = " . $DB->escape($file_orig) . ",`updated_by` = " .$_GLOBALS['staff']['id']. " WHERE `id` = {$posted['id']}");
    if ($size && !$error) {
        $file_new = "safety_" . $posted['id'] . getManualLetter($posted['id']) . '.' . $file_ext;
        $dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/admin_file/';

            if (move_uploaded_file($file_temp, $dir . $file_new)) {
                $DB->query("UPDATE `product_admin_files` SET `file_name` = " . $DB->escape($file_new) . ",`description` = " . $DB->escape($description) . ",`type` = " . $DB->escape($type) . ",`active` = " . $DB->escape($active) . ",`file_original` = " . $DB->escape($file_orig) . ",`updated_by` = " .$_GLOBALS['staff']['id']. " WHERE `id` = {$posted['id']}");
            }
    }   
    // now format the JS Array to pass to the products other page
    $data = array(
        'id'          => $posted['id'],
        'edit'          => 1,
        'description' => $description,
        'filename'    => $file_new,
        'staff'       => $_GLOBALS['staff']['user'],
        'filetype'    => $file_ext,
        'size'        => formatFileSize($size),
        'active'      => $active,
        'file_orig'   => $file_orig,
    );
?>
<html>
    <head>
        <script type="text/javascript">
//<![CDATA[
var data =  <?php jecho($data) ?>;
//]]>
        </script>
    </head>
    <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
    <table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
    <tr><td style="text-align:center;"><b>Admin File for Product ID #<?=$posted['id']?> successfully updated</b></td></tr>
    <tr><td align="center"><input type="button" onclick="window.opener.adminFileAdd(data);window.close();" value="Close Window" /></td></tr>
    </table>
    </body>
</html>
<?php
            return $main_id;
}

function viewManual($id)
{
    global $DB;

    $id       = intval($id);

    $res      = $DB->query_first("SELECT `file_name`, `filetype`, `size`, `file_original` FROM `product_admin_files` WHERE `id` = $id LIMIT 1");
    $filename = $res['file_name'];
    $originalName = $res['file_original'];
    $dir      = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/admin_file/';
    $size     = $res['size'];
    $type     = $res['filetype'];

    $file = $dir . $filename;
    //$mime_type = mime_content_type($file);
     if (is_file($file)) {

        $mimeType = mime_content_type($file) ?: 'application/octet-stream';

        header('Date: ' . gmdate(DATE_RFC1123));
        header('Last-Modified: ' . gmdate(DATE_RFC1123, filemtime($file)));
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($file));

        // Show images in browser, download everything else
        if (strpos($mimeType, 'image/') === 0) {
            header('Content-Disposition: inline; filename="' . $originalName . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $originalName . '"');
        }

        readfile($file);
        exit;
    }
    /*header('Content-Length: ' . $size);
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: attachment; filename="' . $res['file_original'] . '"');

    echo file_get_contents($file);
*/}

function deleteManual($id)
{
    global $DB;
    $id = intval($id);
    //echo "ID to be deleted is $id";
    $row        = $DB->query_first("SELECT `file_name`, `product_id` FROM `product_admin_files` WHERE `id` = $id");
    $product_id = intval($row['product_id']);
    if ($row) {
        $dir   = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/admin_file/';
        $fname = $dir . $row['file_name'];
        //unlink($fname);
        $DB->query("DELETE FROM `product_admin_files` WHERE `id` = $id");
    }
?>
<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Admin File for Product ID #<?=$product_id?> (#<?=$id?>) Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.opener.deleteAdminFiles(<?=$id?>);window.close();" /></td></tr>
	</table>
	</body>
		<br />
	</body>
</html>
<?php
    return true;

}

/**********************************************************Functions Definitions: End ********************************************************************/

// Main Switch-Case...
switch ($action) {
    case "add":
        showAddManual($id);
        break;
    case 'edit':
        showEditAdminfile($ss_id, $id);
        break;
    case "do_add":
        addManual($posted);
        break;
    case 'do_edit':
        editAdminfile($posted);
        break;
    case "delete":
        deleteManual($posted['main_id']);
        break;
    case "view":
        viewManual($posted['main_id']);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
