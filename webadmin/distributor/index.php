<?php
include_once __DIR__ . '/common.inc.php';

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function distributorFromPost($posted)
{
    $result = distributorFromDatabase($posted['id']) ?? distributorDefault();

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }

    $result['category'] = json_encode(explode(',', $result['category']));

    return distributorVirtual($result);
}

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function distributorContactFromPost($posted)
{
    $result = distributorContactFromDatabase($posted['id']) ?? distributorContactDefault();

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }

    return distributorContactVirtual($result);
}

/**
 * @param mixed[][] $posted
 *
 * @return mixed[][]
 */
function distributorContactsFromPost($posted)
{
    $result = [];
    foreach ($posted ?? [] as $v) {
        $result[] = distributorContactFromPost($v);
    }

    return $result;
}

switch ($_REQUEST['action'] ?? '') {
    case 'do_toggle':
        $id = $_REQUEST['id'];
        distributorSetValue($id, 'active', (distributorHasValue($id, 'active', 'y')) ? 'n' : 'y');

        $_GET['action'] = 'list';
        break;
    
    case 'do_add':
        //no break
    case 'do_edit':
        $posted = $_POST;
        $item   = distributorFromPost($posted['item']);
        $id     = distributorToDatabase($item);

        if (array_key_exists('item_image', $_FILES)) {
            $upload = $_FILES['item_image'];
            if (UPLOAD_ERR_OK === $upload['error']) {
                $filename = sprintf(
                    'distributor_%d_%d.%s',
                    $id,
                    time(),
                    pathinfo($upload['name'], PATHINFO_EXTENSION)
                );
                if (move_uploaded_file($upload['tmp_name'], sprintf(
                    '%s/%s',
                    distributorGetOptionList('item_image_path'),
                    $filename
                ))) {
                    distributorSetValue($id, 'item_image', $filename);
                }
            }
        }
         if (array_key_exists('map_image', $_FILES)) {
            $upload = $_FILES['map_image'];
            if (UPLOAD_ERR_OK === $upload['error']) {
                $filename = sprintf(
                    'distributor_map_image_%d_%d.%s',
                    $id,
                    time(),
                    pathinfo($upload['name'], PATHINFO_EXTENSION)
                );
                if (move_uploaded_file($upload['tmp_name'], sprintf(
                    '%s/%s',
                    distributorGetOptionList('item_image_path'),
                    $filename
                ))) {
                    distributorSetValue($id, 'map_image', $filename);
                }
            }
        }
        if (!empty($posted['special_notes'] ?? '')) {
            distributorAddComment($id, $posted['special_notes']);
        }


        $item_contacts = distributorContactsFromPost($posted['item_contacts'] ?? []);
        distributorContactsToDatabase($id, $item_contacts);
        foreach ($posted['item_contacts_deleted'] ?? [] as $contact_id) {
            if (!empty($contact_id)) {
                distributorContactDelete($contact_id);
            }
        }

        $_GET['action'] = 'edit';
        $_GET['id']     = $id;
        break;
    case 'delete':
        distributorDelete($_GET['id']);
        $action = 'list';
        break;
}

switch ($_GET['action'] ?? '') {
    default:
        //no break
    case 'list':
        redirect('list.php?' . http_build_query($_GET));
        break;

    case 'add':
        redirect('add.php?' . http_build_query($_GET));
        break;

    case 'edit':
        redirect('edit.php?' . http_build_query($_GET));
        break;

    case 'item_image':
        redirect('item_image.php?' . http_build_query($_GET));
        break;
    case 'map_image':
        redirect('item_image.php?' . http_build_query($_GET));
        break;
}
