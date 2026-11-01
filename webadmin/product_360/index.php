<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('category'); //categoryHasValue, categorySetValue

$action = 'list';
if (array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
}
$id     = $_REQUEST['id'];
$vars   = $_GET;
unset($vars['action']);

switch ($action) {
    case 'do_add':
        // no break
    case 'do_edit':
        $posted['item']['id']=$posted['id'];
        $view = view360FromPost($posted['item']);
        $id =view360ToDatabase($view);
        if(count($posted['product_image'])){
            foreach ($posted['product_image'] as $key => $image) {
                $image['view_id']=$id;
                $image['file_orig']=$image['name'];
                $images = view360ImagesFromPost($image);
                view360ImagesToDatabase($images);
            }
        }
        if (array_key_exists('item_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_images_deleted_val']) as $v) {
                if ($v) {
                    view360GenericsDelete(
                        $v,
                        "`360_images`",
                        "id"
                    );
                }
            }
        }
        if(count($posted['marker'])){
            foreach ($posted['marker'] as $key => $marker) {
                $marker['view_id']=$id;
                if(isset($marker['id']) && in_array($marker['id'],$posted['activemarker'])){
                    $marker['status']=1;
                }else{
                    $marker['status']=0;
                }
                if($marker['name']){
                    $marker = view360MarkerFromPost($marker,$_FILES,$key);
                    view360MarkerToDatabase($marker);
                }
                
            }
        }

        if (array_key_exists('item_marker_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_marker_deleted_val']) as $v) {
                if ($v) {
                          view360MarkerDelete($v);
                }
            }
        }
        foreach ($posted['marker_location'] as $marker_id => $images) {
            foreach ($images as $image_id => $position) {
                if($position['left']>0 && $position['top']>0){
                    $location=[];
                    $location['marker_id']=$marker_id;
                    $location['image_id']=$image_id;
                    $location['active']='y';
                    $location['left']=$position['left'];
                    $location['top']=$position['top'];
                    $location['id']=(isset($position['id']))?$position['id']:0;
                    $post_location=view360GenericsFromPost(
                        $location,
                        "`360_view_marker_location`"
                    );
                    view360GenericToDatabase(
                        $post_location,
                        "`360_view_marker_location`"
                    );
                }else{
                    view360GenericsDelete(
                        $position['id'],
                        "`360_view_marker_location`",
                        "id"
                    );
                }
            }
        }
        $vars['id']= $id;
       $action = 'edit';
       break;

    case 'update_setting' :
        $setting = view360GenericsFromPost(
            $posted['item'],
            "`360_view_setting`"
        );
        $setting['id'] = 1;
        view360GenericToDatabase(
            $setting,
            "`360_view_setting`"
        );
        $action = 'setting';
        break;
}

function deleteProductType($type_id)
{
    // @fixme This doesn't seem like the right place for this function, so it's probably not correct
    trigger_error('Protection Error');
    die('Protection error: ' . __FILE__ . ':' . __LINE__);

    moduleLoad('category'); //categoryDelete

    $type_id = intval($type_id);
    if (!$type_id) {
        return;
    }

    if (categoryDelete($type_id)) {
        echo 'Type ID: ' . $type_id . ' successfully deleted';
    }
}

//procesing switch-case:
switch ($action) {
    case 'do_delete':
        deleteProductType($posted['id']);
        $action = 'list';
        break;
    case 'do_toggle':
        categorySetValue($_REQUEST['id'], 'active', (categoryHasValue($_REQUEST['id'], 'active', 'y')) ? 'n' : 'y');
        $action = 'list';
        break;
    case 'do_toggle_featured':
        categorySetValue($_REQUEST['id'], 'featured', (categoryHasValue($_REQUEST['id'], 'featured', 'y')) ? 'n' : 'y');
        $action = 'list';
        break;
}
switch ($action) {
    case 'add':
    case 'delete':
    case 'edit':
    case 'list':
    case 'preview':
    case 'setting':
        redirect(sprintf(
            '%s.php?%s',
            $action,
            http_build_query($vars)
        ));
        die();
}

include_once __DIR__ . '/header.inc.php';

trigger_error('Invalid action');

include_once __DIR__ . '/footer.inc.php';
