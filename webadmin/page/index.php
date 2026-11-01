<?php
include_once __DIR__ . '/common.inc.php';

$action = 'list';
$if = 0;
if (array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
}
if (array_key_exists('id', $_REQUEST)) {
	$id = $_REQUEST['id'];
}
$update_status = false;
//Update Actions
switch ($action) {
    case 'do_add':
        unset($_REQUEST['page']['id']);
    case 'do_edit':
        global $_GLOBALS;
        $new_page     = pageFromPost($_REQUEST['item']);
        $id           = pageToDatabase($new_page);
        $_GET['id'] = $id;


        //Handle new uploaded item_images
        if (is_array($_FILES['item_images'])) {
            foreach ($_FILES['item_images']['error'] as $count => $error) {
                if (UPLOAD_ERR_OK == $error['image_file']) {
                    $item_image = array();
                    if (is_array($_REQUEST['item_images'][$count])) {
                        foreach ($_REQUEST['item_images'][$count] as $var => $val) {
                            $item_image[$var] = $val;
                        }
                    }

                    $item_image['page_id']    = $id;
                    $item_image['file_orig'] = $_FILES['item_images']['name'][$count]['image_file'];
                    $image_id                 = pageImageToDatabase($item_image);
                    $item_image['id']         = $image_id;
                    if(isset($_REQUEST['item_images'][$count]['alt_text']) && !empty($_REQUEST['item_images'][$count]['alt_text'])){
                        $altname= trim(preg_replace('/[^A-Za-z0-9]+/', '-', $_REQUEST['item_images'][$count]['alt_text']), '-');
                    }else{
                        $altname='pageimage';
                    }
                    
                    $item_image['image_file'] = $altname .'-' .$image_id . '.' . end(explode('.', $item_image['file_orig']));
                    if (move_uploaded_file($_FILES['item_images']['tmp_name'][$count]['image_file'], pageGetOptionList('page_path') . '/' . $item_image['image_file'])) {
                        $item_image['created_staff_id'] =$_GLOBALS['staff']['id'];
                        pageImageToDatabase($item_image);
                    }
                }
            }
        }

        //Handle field changes for item_images
        if (is_array($_REQUEST['item_images'])) {
            foreach ($_REQUEST['item_images'] as $key => $value) {
                $item_image = array();
                foreach ($_REQUEST['item_images'][$key] as $var => $val) {
                    $item_image[$var] = $val;
                }
                $item_image['modified_staff_id'] =$_GLOBALS['staff']['id'];
                if (0 != $item_image['id']) {
                    pageImageToDatabase($item_image);
                }

            }
        }

        //Handle deleted item_images
        foreach (explode('|', $_REQUEST['item_images_affected']) as $image_id) {
            $found = false;
            if (is_array($_REQUEST['item_images'])) {
                foreach ($_REQUEST['item_images'] as $key => $val) {
                    if ($val['id'] == $image_id) {
                        $found = true;
                    }
                }
            }

            if (!$found) {
                pageImageDelete($image_id);
            }

        }
        if ('' != $_REQUEST['admin_notes']) {
            pageCommentToDatabase($id, $_REQUEST['admin_notes']);
        }
        $update_status = true;
        $action = 'edit';
        break;
    case 'do_add_chapter':
        unset($_REQUEST['id']);
    case 'do_edit_chapter':
        $id                     = $_REQUEST['id'];
        $page_id                = $_REQUEST['page_id'];
        $new_chapter            = pageChapterFromPost($_REQUEST);
        $new_chapter['page_id'] = $page_id;
        $id                     = pageChapterToDatabase($new_chapter);
        $_REQUEST['id']           = $id;

        //Handle new uploaded item_images
        if (is_array($_FILES['item_images'])) {
            foreach ($_FILES['item_images']['error'] as $count => $error) {
                if (UPLOAD_ERR_OK == $error['image_file']) {
                    $item_image = array();
                    if (is_array($_REQUEST['item_images'][$count])) {
                        foreach ($_REQUEST['item_images'][$count] as $var => $val) {
                            $item_image[$var] = $val;
                        }
                    }

                    $item_image['page_id']    = $page_id;
                    $item_image['file_orig'] = $_FILES['item_images']['name'][$count]['image_file'];
                    $image_id                 = pageImageToDatabase($item_image);
                    $item_image['id']         = $image_id;
                    $item_image['image_file'] = 'img_' . $image_id . '.' . end(explode('.', $item_image['file_orig']));
                    if (move_uploaded_file($_FILES['item_images']['tmp_name'][$count]['image_file'], pageGetOptionList('page_path') . '/' . $item_image['image_file'])) {
                        pageImageToDatabase($item_image);
                    }

                }
            }
        }

        //Handle field changes for item_images
        if (is_array($_REQUEST['item_images'])) {
            foreach ($_REQUEST['item_images'] as $key => $value) {
                $item_image = array();
                foreach ($_REQUEST['item_images'][$key] as $var => $val) {
                    $item_image[$var] = $val;
                }

                if (0 != $item_image['id']) {
                    pageImageToDatabase($item_image);
                }

            }
        }

        //Handle deleted item_images
        foreach (explode('|', $_REQUEST['item_images_affected']) as $image_id) {
            $found = false;
            if (is_array($_REQUEST['item_images'])) {
                foreach ($_REQUEST['item_images'] as $key => $val) {
                    if ($val['id'] == $image_id) {
                        $found = true;
                    }
                }
            }

            if (!$found) {
                pageImageDelete($image_id);
            }

        }

        $action = 'chapter_edit';

        break;

    case 'activate':
        $id = $_REQUEST['id'];
        pageSetValue($id, 'active', 'y');
        $action = 'list';
        break;
    case 'deactivate':
        $id = $_REQUEST['id'];
        pageSetValue($id, 'active', 'n');
        $action = 'list';
        break;

    case 'chapter_activate':
        pageChapterSetValue($id, 'active', 'y');
        $action = 'chapter_list';
        break;
    case 'chapter_deactivate':
        pageChapterSetValue($id, 'active', 'n');
        $action = 'chapter_list';
        break;

    case 'move_up':
        $id = $_REQUEST['id'];
        pageMoveRank($id, -1);
        $action = 'list';
        break;
    case 'move_down':
        $id = $_REQUEST['id'];
        pageMoveRank($id, 1);
        $action = 'list';
        break;

    case 'chapter_move_up':
        $id = $_REQUEST['id'];
        pageChapterMoveRank($id, -1);
        $action = 'chapter_list';
        break;
    case 'chapter_move_down':
        $id = $_REQUEST['id'];
        pageChapterMoveRank($id, 1);
        $action = 'chapter_list';
        break;

    case 'unlock':
        pageSetLock($id);
        break;

    case 'chapter_unlock':
        pageChapterSetLock($id);
        $action = 'chapter_list';
        break;

    case 'chapter_delete':
        pageChapterDelete($id);
        $action = 'chapter_list';
        break;

}

$get_vars  = $_GET;
$keep_only = array_keys(pageGetOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'page';
$keep_only[] = 'id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'selected_list';
$keep_only[] = 'return_message';
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        unset($get_vars['id']);
        redirect('add.php?' . http_build_query($get_vars));
        break;
    case 'chapter_add':
        unset($get_vars['id']);
        redirect('chapter_add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        $get_vars['status'] = $update_status;
        pageSetLock($_REQUEST['id'], $_GLOBALS['staff']['staff_id']);
        redirect('edit.php?' . http_build_query($get_vars));
        break;
    case 'chapter_edit':
        pageChapterSetLock($_REQUEST['id'], $_GLOBALS['staff']['staff_id']);
        redirect('chapter_edit.php?' . http_build_query($get_vars));
        break;

    case 'delete':
        redirect('delete.php?' . http_build_query($get_vars));
        break;
    case 'chapter_view':
        die('View under construction...');
        if (pageChapterExists($_REQUEST['id'])) {
            $chapter = pageChapterFromDatabase($_REQUEST['id']);
            redirect('' . $chapter['chapter_url']);
        } else {
            redirect('view.php?' . http_build_query($get_vars));
        }

        break;
    case 'view':
        if (pageExists($_REQUEST['id'])) {
            $item = pageFromDatabase($_REQUEST['id']);
            redirect('' . $item['page_url']);
        } else {
            redirect('view.php?' . http_build_query($get_vars));
        }

        break;
    case 'chapter_list':
        redirect('chapter_list.php?' . http_build_query($get_vars));
        break;
    case 'search':
        $get_vars['search_for'] = '';
    default:
    case 'list':
        unset($get_vars["id"]);
        redirect('list.php?' . http_build_query($get_vars));
        break;
}

die();
