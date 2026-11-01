<?php
include_once __DIR__ . '/common.inc.php';
moduleLoad('phpmailer');
moduleLoad('template');
use PHPMailer\PHPMailer\PHPMailer;
/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function wareHouseFromPost($posted)
{
    $result = wareHouseFromDatabase($posted['id']) ?? wareHouseVirtual([]);

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }
    return wareHouseVirtual($result);
}

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function mediaContactFromPost($posted)
{
    $result = socialMediaContactFromDatabase($posted['id']) ?? socialMediaContactDefault();

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }

    return socialMediaContactVirtual($result);
}

/**
 * @param mixed[][] $posted
 *
 * @return mixed[][]
 */
function mediaContactsFromPost($posted)
{
    $result = [];
    foreach ($posted ?? [] as $v) {
        $result[] = mediaContactFromPost($v);
    }

    return $result;
}
$action = $_REQUEST['action'];
switch ($_REQUEST['action'] ?? '') {
    case 'do_toggle':
        $id = $_REQUEST['id'];
        socialMediaPartnerSetValue($id, 'active', (socialMediaPartnerHasValue($id, 'active', 'y')) ? 'n' : 'y');

        $_GET['action'] = 'list';
        break;
    
    case 'do_add':
        //no break
    case 'do_edit':
        $posted = $_POST;
        $item   = wareHouseFromPost($posted['item']);

        $id     = wareHouseToDatabase($item);
        warehouseContactsToDatabase($id,($posted['item_contacts'] ?? []) );
         if (!empty(trim($posted['admin_comment']) ?? '')) {
            warehouseCommentAdd($id, $posted['admin_comment']);
        }
        foreach ($posted['item_contacts_deleted'] ?? [] as $contact_id) {
            if (!empty($contact_id)) {
                warehouseContactDelete($contact_id);
            }
        }

        if(array_key_exists('itemOnlineText',$posted)){
            $dateItemOnlineText = $posted['itemOnlineText'];
            $dateItemOnlineText['show_warehouse'] = (isset( $dateItemOnlineText['show_warehouse']) &&  $dateItemOnlineText['show_warehouse'] == 'y') ? 'y':'n';
            $dateItemOnlineText['show_delivery_time'] = (isset( $dateItemOnlineText['show_delivery_time']) &&  $dateItemOnlineText['show_delivery_time'] == 'y') ? 'y':'n';
            $dateItemOnlineText['show_freight_costs'] = (isset( $dateItemOnlineText['show_freight_costs']) &&  $dateItemOnlineText['show_freight_costs'] == 'y') ? 'y':'n';
            $dateItemOnlineText['show_notes'] = (isset( $dateItemOnlineText['show_notes']) &&  $dateItemOnlineText['show_notes'] == 'y') ? 'y':'n';
            $dateItemOnlineText['country_location'] = $posted['item']['country_location'] ?? '';
            warehouseOnlineDelete( $id);
            warehouseOnlineAdd( $id,$dateItemOnlineText);
        }
        $action = 'edit';
        $_GET['id']     = $id;
        break;
    case 'delete':
        wareHouseDelete($_GET['id']);
        $action = 'list';
        break;
}
switch ($action) {
    case 'result':
     header('Location: ' . $action . '.php?id='.$updateId);
        break;
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
    default:
        //no break
}
