<?php
include_once __DIR__ . '/common.inc.php';

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function seriesFromPost($posted)
{
    return $posted;
}

/**
 * @param mixed[] $posted
 *
 * @return mixed[][]
 */
function seriesImagesFromPost($posted)
{
    return $posted;
}

switch ($_REQUEST['action'] ?? '') {
    case 'do_add':
        //no break
    case 'do_edit':
        $item        = seriesFromPost($_POST['item']);
        $item_images = seriesImagesFromPost($_POST['item_images'] ?? []);

        $id = seriesToDatabase($item);
        seriesImagesToDatabase($id, $item_images);

        foreach ($_POST['item_images_deleted'] ?? [] as $image_id) {
            if (!empty($image_id)) {
                seriesImageDelete($image_id);
            }
        }

        $_GET['action'] = 'edit';
        $_GET['id']     = $id;
        break;

    case 'do_activate':
        $id = $_REQUEST['id'] ?? 0;
        seriesSetValue($id, 'active', ('y' == seriesGetValue($id, 'active')) ? 'n' : 'y');
        $_GET['action'] = 'list';
        unset($_GET['id']);
        break;
}

switch ($_GET['action'] ?? '') {
    default:
        //no break
    case 'list':
        redirect('list.php?' . http_build_query($_GET));
        die();

    case 'view':
        $item = seriesFromDatabase($_GET['id'] ?? 0);
        redirect(sprintf(
            "/series/%s",
            preg_replace('/[^a-zA-Z0-9]+/', '-', $item['slug'])
        ));
        //redirect('view.php?' . http_build_query($_GET));
        die();

    case 'add':
        redirect('add.php?' . http_build_query($_GET));
        die();

    case 'edit':
        redirect('edit.php?' . http_build_query($_GET));
        die();
}
