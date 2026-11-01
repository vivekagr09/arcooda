<?php
/**
 * @var int    $asset_id
 * @var string $asset_type
 */
include_once __DIR__ . '/common.inc.php';
global $DB;
$get_vars = $_REQUEST;
$keep_only = array('asset_id', 'owner_id', 'asset_type', 'return_message');
foreach ($get_vars as $key => $value) {
    if (!in_array($key, $keep_only)) {
        unset($get_vars[$key]);
    }
}
if (false == $table_info) {
    $get_vars['action'] = $_REQUEST['action'];
    redirect('types.php?' . http_build_query($get_vars));
    die();
}
//Update Actions
switch ($_REQUEST['action']) {
    case 'do_add':
        unset($posted['asset_id']);
    case 'do_edit':
        $asset                = assetFromPost($asset_type, $_REQUEST);
        $asset_id             = intval(assetToDatabase($asset_type, $asset));
        $posted['asset_id']   = $asset_id;
        $get_vars['asset_id'] = $asset_id;
        if (0 == $_FILES['asset_file']['error'] && $_FILES['asset_file']['size'] > 0) {
            $asset['asset_id'] = $asset_id;
            $asset['fileorig'] = $_FILES['asset_file']['name'];
            $asset['filetype'] = $_FILES['asset_file']['type'];
            $asset['filesize'] = $_FILES['asset_file']['size'];
            $asset['filename'] = assetGenerateUniqueName($asset_type, $asset);
            if ('' == $asset['asset_caption']) {
                $asset['asset_caption'] = $asset['fileorig'];
            }

            move_uploaded_file($_FILES['asset_file']['tmp_name'], $table_info['directory'] . $asset['filename']);
            assetToDatabase($asset_type, $asset);
        }

        if ('workmanual_pages' == $asset_type) {
            $DB->query(sprintf(
				"UPDATE `highwaygroup`.`workmanual_pages` SET `page_rank` = `page_id` WHERE `page_id` = %d LIMIT 1",
				$asset_id
			));
        }

        $action = 'list';
        if (isset($_REQUEST['return_message'])) {
            $_REQUEST['action'] = 'select';
        }
        break;
    case 'do_delete':
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM " . $table_info['table']);
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }
        if (array_key_exists("active", $fields)) {
            $DB->query(sprintf(
				"UPDATE %s SET `active` = 'n' WHERE %s = %d LIMIT 1",
				$table_info['table'],
				$table_info['asset_id'],
				$asset_id
			));
        }else{
            $DB->query(sprintf(
				"DELETE FROM %s WHERE %s = %d LIMIT 1",
				$table_info['table'],
				$table_info['asset_id'],
				$asset_id
			));
        }

        $action = 'list';
        if (isset($_REQUEST['return_message'])) {
            $_REQUEST['action'] = 'select';
        }

        break;
}

//Redirect Actions
switch ($_REQUEST['action']) {
    case 'add':
        unset($get_vars['asset_id']);
        redirect('add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        redirect('edit.php?' . http_build_query($get_vars));
        break;
    case 'select':
        redirect('select.php?' . http_build_query($get_vars));
        break;
    case 'thumb':
        redirect('thumb.php?' . http_build_query($get_vars));
        break;
    case 'view':
        redirect('view.php?' . http_build_query($get_vars));
        break;
    case 'delete':
        redirect('delete.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        unset($get_vars['asset_id']);
        redirect('list.php?' . http_build_query($get_vars));
}

die();
