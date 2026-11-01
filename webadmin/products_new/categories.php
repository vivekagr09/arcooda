<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('category'); //categoryCount, categoryDelete, categoryFromDatabase, categoryIsParent, categorySetValue

include_once __DIR__ . '/header.inc.php';
$action = $_REQUEST['action'];
$id     = $_REQUEST['id'];

function doEdit($posted)
{
    global $DB;
    $id  = intval($posted['id']);
    $old = array();
    if ($id > 0) {
        $old = categoryFromDatabase($id);
    }

    $categories = $posted['categories'];
    $set        = array();
    $changes    = array();
    if ('' == $categories['name']) {
        echo 'Category Name is needed. Details not updated. Please try again.';
    } else {
        if ('y' == $categories['is_parent']) {
            $categories['parent'] = 0;
        }

        $allowed = array(
            'name',
            'is_parent',
            'parent',
            'active',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'short_desc',
            'long_desc',
            'default_manufacturer',
        );
        $changed_date = false;
        foreach ($allowed as $key) {
            $old_str = formatCleanString($old[$key]);
            $new_str = formatCleanString($categories[$key]);
            if ($old_str != $new_str) {
                if ('meta_title' == $key || 'meta_description' == $key || 'meta_keywords' == $key) {
                    $changed_date = true;
                }

                $changes[] = ucwords(str_replace('_', ' ', $key)) . ' modified. Before->' . $old_str . ', After->' . $new_str;
            }
            $set[$key] = $key . '=' . $DB->escape($categories[$key]);
        }

        $DB->query(sprintf(
            "%s `product_types` SET %s%s",
            ($id) ? "UPDATE" : "INSERT INTO",
            implode(', ', $set),
            ($id) ? sprintf(
                " WHERE `id` = %d LIMIT 1",
                $id
            ) : ''
        ));
        if (!$id) {
            $id = $DB->insert_id();
        }

        // now check if there is a file to be uploaded
        $file      = $_FILES['header_image']['tmp_name'];
        $file_orig = $_FILES['header_image']['name'];
        $ext       = substr(strrchr($file_orig, '.'), 1);
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/images/categories/';
        $thumb_dir = $dir . 'thumbnails/';
        $file_name = 'header_image_' . $id . '.' . $ext;
        $location  = $dir . $file_name;
        $store     = '/images/categories/' . $file_name;
        $filesize  = filesize($file);
        if ($filesize && !$_FILES['header_image']['error']) {
            if (is_uploaded_file($file)) {
                move_uploaded_file($file, $location);
            }

            // now update the database.
            $DB->query(sprintf(
                "UPDATE `product_types` SET `header_image` = %s WHERE `id` = %d LIMIT 1",
                $DB->escape($store),
                $id
            ));
            $changes[] = 'New Header Image Uploaded.';
        }
        $icon      = $_FILES['icon_image']['tmp_name'];
        $icon_orig = $_FILES['icon_image']['name'];
        $iExt      = substr(strrchr($icon_orig, '.'), 1);
        $error     = $_FILES['icon_image']['error'];
        $size      = $_FILES['icon_image']['size'];
        $dir       = '/images/categories/';
        $new_name  = 'icon_image_' . $id . '.' . $iExt;
        if ($size && !$error) {
            if (move_uploaded_file($icon, $_SERVER['DOCUMENT_ROOT'] . $dir . $new_name)) {
                $DB->query(sprintf(
                    "UPDATE `product_types` SET `icon_image` = %s WHERE `id` = %d LIMIT 1",
                    $DB->escape($dir . $new_name),
                    $id
                ));
                $changes[] = 'New Icon image uploaded.';
            }
        }

    }
    if (count($changes)) {
?>
<div style="text-align:left;padding-left:25px;color:#f00;">
    Changes Made:<br />
    <ul>
        <li><?=  implode("</li>\n        <li>", $changes) ?></li>
    </ul>
</div>
<br />
<?php
    }
    return $id;
}

function showEdit($posted)
{
    global $DB;
    $id        = intval($posted['id']);
    $do_action = ($id) ? 'do_edit' : 'do_add';
    $yesno     = array('n' => 'No', 'y' => 'Yes');
    $data      = ($id) ? categoryFromDatabase($id) : array();
    if (!$id) {
        $array = array('name', 'parent', 'is_parent', 'id', 'is_category');
        foreach ($array as $key) {
            if ('' != $posted['categories'][$key]) {
                $data[$key] = $posted['categories'][$key];
            }
        }

    }
    $categories = array('0' => 'None') + productGetOptionList('category_id');
    if ($id > 0) {
        unset($categories[$id]);
    }

?>
<script type="text/javascript">
//<CDATA[
$(function() {
    $("input[name='view_manufacturer']").css("display","").click(function() {
        var id=$("[name='categories[default_manufacturer]']").val();
        if(id!="0" && id!="" && id!=null) window.open("/webadmin/suppliers/?type=man%7Cboth&action=edit&supplier_id="+escape(id));
    });
});
//]]>
</script>
<form action="?" method="post" enctype="multipart/form-data">
<table class="table_fields">
    <input type="hidden" name="action" value="<?=$do_action?>" />
<?php
if (0 != $id) {
?>
    <tr>
        <th>Category ID :</th>
        <td><input style="width:100%" type="text" name="id" value="<?=$id?>" readonly="readonly" /></td>
    </tr>
<?php
}
?>
    <tr>
        <th><span style="color:#f00;"><small>*</small></span>Name :</th>
        <td><input style="width:100%" type="text" name="categories[name]" value="<?=$data['name']?>"></td>
    </tr>
    <tr>
        <th>Is Parent :</th>
        <td><select name="categories[is_parent]" onchange="checkParent(this.value);" style="width:100%;"><?=formatHTMLOptions($yesno, $data['is_parent'])?></select></td>
    </tr>
    <tr id="parent_row" style="<?=('y' == $data['is_parent']) ? 'display:none' : ''?>">
        <th>Choose Parent Category :</th>
        <td>
            <select name="categories[parent]" style="width:100%;">
<?php
foreach ($categories as $category_id => $name) {
        echo '<option style="' . ((categoryIsParent($category_id)) ? 'background:#ee0;' : (categoryIsParent($category_id) ? "background:#ee9;" : '')) . '" value="' . $category_id . '" ' . (($category_id == $data['parent']) ? 'selected' : '') . '>' . $name . '</option>';
    }


?>
            </select>
        </td>
    </tr>
    <tr>
        <th>Short Description :</th>
        <td><textarea name="categories[short_desc]" onkeypress="textareaAutoSize(this,3,3,20);" style="width:100%;"><?php hecho($data['short_desc'])?></textarea></td>
    </tr>
    <tr>
        <th>Lomg Description :</th>
        <td><textarea name="categories[long_desc]" onkeypress="textareaAutoSize(this,3,3,25);" style="width:100%;"><?php hecho($data['long_desc'])?></textarea></td>
    </tr>
    <tr>
        <th>Active :</th>
        <td><select name="categories[active]" style="width:100%;"><?=formatHTMLOptions($yesno, $data['active'])?></select></td>
    </tr>
<?php
$manufacturer_list = productGetOptionList('manufacturer');

?>
    <tr>
        <th>Linked To Manufacturer :</th>
        <td>
            <select name="categories[default_manufacturer]" style="width:auto;">
                <option value=""></option>
                <?=formatHTMLOptions($manufacturer_list, intval($data['default_manufacturer']))?>
            </select>
            <input name="view_manufacturer" type="button" style="display:none;" value="View" />
        </td>
    </tr>
    <tr>
        <th>Header Image :</th>
        <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" name="header_image" />
<?php
if ('' != $data['header_image']) {
?>
            <input type="button" value="View" onclick="window.open('gogetmemyimage.php?i=<?=$data['header_image']?>');return false" />
<?php
}
?>
        </td>
    </tr>
<?php
if ('' != $data['header_image']) {
?>
    <tr>
        <th>&nbsp;</th>
        <td><small style="color:#f00;">NB: Uploading new image will overwrite existing image</small></td>
    </tr>
<?php
}
?>
    <tr>
        <th>Icon Image :</th>
        <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" name="icon_image" />
<?php
if ('' != $data['icon_image']) {
?>
            <input type="button" value="View" onclick="window.open('gogetmemyimage.php?i=<?=$data['icon_image']?>');return false" />
<?php
}
?>
        </td>
    </tr>
<?php
if ('' != $data['icon_image']) {
?>
    <tr>
        <th>&nbsp;</th>
        <td><small style="color:#f00;">NB: Uploading new image will overwrite existing image</small></td>
    </tr>
<?php
}
?>
    <tr><th colspan="2">&nbsp;</th></tr>
</table>
<?php
$total_title = strlen($data['meta_title']);
    $total_desc  = strlen($data['meta_description']);
    $total_keys  = 0;
    $ar          = explode(',', $data['meta_keywords']);
    foreach ($ar as $key => $value) {
        if (trim($value) != '') {
            $total_keys++;
        }
    }

?>
<br />

<script type="text/javascript">
//<![CDATA[
function updateTotals()
{
    var title=getValue('categories[meta_title]');
    setValue('meta_title',title.length);

    var keys=0;
    var keywords=getValue('categories[meta_keywords]').split(',');
    for(i=0;i<keywords.length;i++) if(keywords[i]!='') keys++;
    setValue('meta_keywords',keys);

    var desc=getValue('categories[meta_description]');
    setValue('meta_description',desc.length);
}
function updateTags()
{
    setValue('categories[meta_title]',getValue('categories[name]'));
    setValue('categories[meta_keywords]',formatMETA(getValue('categories[name]')+', '+getValue('categories[short_desc]'),'keyword'));
    setValue('categories[meta_description]',getValue('categories[long_desc]'));
    updateTotals();
}
function clearTags()
{
    setValue('categories[meta_title]','');
    setValue('categories[meta_keywords]','');
    setValue('categories[meta_description]','');
    updateTotals();
}
//]]>
</script>

<div class="flagHeader"><span >META Tags</span></div><br />
<table class="table_fields">
    <tr>
        <th><a href="#" onclick="open_win('help.php?action=title');return false" style="color:#00f;">Title :</a></th>
        <td><input type="text" name="categories[meta_title]" style="width:100%" value="<?=$data['meta_title']?>" onchange="updateTotals()"></td>
        <td id="meta_title" name="meta_title"><?=$total_title?></td>
    </tr>
    <tr>
        <th><a href="#" onclick="open_win('help.php?action=keywords');return false" style="color:#00f;">Keywords :</a></th>
        <td><textarea name="categories[meta_keywords]" style="width:100%" rows=5 onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals()"><?=$data['meta_keywords']?></textarea></td>
        <td id="meta_keywords" name="meta_keywords"><?=$total_keys?></td>
    </tr>
    <tr>
        <th><a href="#" onclick="open_win('help.php?action=desc');return false" style="color:#00f;">Description :</a></th>
        <td><textarea name="categories[meta_description]" style="width:100%" rows=5 onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals()"><?=$data['meta_description']?></textarea></td>
        <td id="meta_description" name="meta_description"><?=$total_desc?></td>
    </tr>
    <tr style="background:#fec;">
        <th>META Tags Last Changed :</th>
        <td style="padding-left:3px;"><?=($data['meta_by']) ? date(_('Y-m-d'), strtotime($data['updated_at'])) . ', By : ' . $meta_by_list[$data['meta_by']] : 'Never'?></td>
        <td>&nbsp;</td>
    </tr>
</table><br />
<div style="text-align:center;">
    <input type="button" value="Auto-Update Tags" onclick="updateTags()" /><input type="submit" value="<?=($id) ? 'Update' : 'Add'?>">
</div>
<br />
<div style="text-align:center;">
    <i><span style="color:#f00;"><i><small>*</small></span> - Required Field</i>
</div>
</form>
<?php
}
function showDelete($category_id)
{
    global $DB;
    $category_id = intval($category_id);
    if (!$category_id) {
        return;
    }

    $data       = categoryFromDatabase($category_id);
    $is_parent  = $data['is_parent'];
    $categories = productGetOptionList('category_id');
    $yesno      = array('n' => 'No', 'y' => 'Yes');
?>
<form action="?" method="post">
<table class="table_fields">
    <input type="hidden" name="action" value="do_delete" />
    <input type="hidden" name="id" value="<?=$category_id?>" />
    <tr><th>ID :</th><td><?=$data['id']?></td></tr>
    <tr><th>Name :</th><td><?=$data['name']?></td></tr>
    <tr><th>Is Parent :</th><td><?=$yesno[$is_parent]?></td></tr>
    <tr><th>Parent Category :</th><td><?=('n' == $is_parent) ? $categories[$data['parent']] : '-'?></td></tr>
    <tr><td colspan="2" style="text-align:center;">Are you sure you want to delete this Product Category??</td></tr>
    <tr>
        <td colspan="2" style="text-align:center;">
            <input type="submit" value="Yes" />&nbsp;
            <input type="button" value="No" onclick="window.location='categories.php';" />
        </td>
    </tr>
</table>
</form>
<?php
}
function buildTree($category_list)
{
    print_array($category_list);
    die();
    $new_list = array();
    foreach ($category_list as $id => $sub_list) {
        if (is_array($sub_list)) {
            foreach ($sub_list as $sub_id) {
                if (is_array($category_list[$sub_id])) {
                    $new_list[$id][$sub_id] = buildTree($category_list[$sub_id]);
                } else {
                    $new_list[$id] = $sub_id;
                }

            }
        } else {
            $new_list[$id] = $sub_list;
        }

    }
    return $new_list;
}
function buildTableView()
{
    global $_GLOBALS;
    $category_list = /*array_reverse(*/
    productGetOptionList('category_id') /*, true)*/;
    $categories_list    = productChunk(productQuery(), 'type');
    $category_subtotals = array();
    foreach ($categories_list as $category_id => $item_list) {
        $category_subtotals[$category_id]['item_count'] = count($item_list);
        $category_subtotals[$category_id]['item_list']  = $item_list;
    }
    $categories_by_category = productGetOptionList('category_id');
    $another                = array(array(0));
    $tree_final             = array();
    $category_totals        = array();
    // create the tree-view, so don't have to use recursive algorithm.
    while (is_array($tree = array_pop($another))) {
        $tree_final[] = $tree;
        $category_id  = end($tree);
        if (is_array($category_subtotals[$category_id])) {
            foreach ($category_subtotals[$category_id] as $key => $value) {
                foreach ($tree as $supracategory_id) {
                    if (!is_array($category_totals[$supracategory_id][$key]) && is_array($value)) {
                        $category_totals[$supracategory_id][$key] = array();
                    }

                    if (is_array($value)) {
                        $category_totals[$supracategory_id][$key] = array_merge($category_totals[$supracategory_id][$key], $value);
                    } else {
                        $category_totals[$supracategory_id][$key] += $value;
                    }

                }
            }
        }
        if (is_array($categories_by_category[$category_id]) && count($categories_by_category[$category_id])) {
            foreach ($categories_by_category[$category_id] as $subcategory_id) {
                $new_tree   = $tree;
                $new_tree[] = $subcategory_id;
                array_push($another, $new_tree);
            }
        }
    }
    $count = 0;
    //$tree_final=array_reverse($tree_final,true);
    foreach ($tree_final as $key => $tree) {
        $total       = intval($category_totals[end($tree)]['item_count']);
        $is_parent   = (count($tree) == 2);
        $is_category = (!$is_parent) ? categoryIsParent(end($tree)) : false;
        $colour      = (count($tree) == 2) ? 'background:#EEEE00;' : (($is_category) ? "background:#EEEE99;" : '');
        $is_active   = isActive(end($tree));
        $action      = ($is_active ? 'de' : '') . 'activate';
        $status      = ($is_active ? 'De-' : '') . 'Activate';
        if (end($tree) > 0) {
?>
            <tr <?=($count++ % 2 == 1) ? 'class="alt"' : ''?> style="text-align:center;<?=$colour?>">
                <td><?=end($tree)?></td>
                <td style="text-align:left;padding-left:<?=10 * count($tree)?>px;"><?=$category_list[end($tree)]?></td>
                <td><?=($is_active) ? 'Yes' : 'No'?></td><td><?=(count($tree) == 2) ? 'Yes' : 'No'?></td>
                <td><?=(count($tree) == 3 || $is_category) ? 'Yes' : ((count($tree) == 2) ? '-' : 'No')?></a></td>
                <td><?=(count($tree) >= 4 && !$is_category) ? 'Yes' : '-'?></td>
                <td>
                    <a href="?action=edit&id=<?=end($tree)?>" target="_blank">Edit</a>
<?php
if (0 == $total && !$is_category) {
?>
                    &nbsp;|&nbsp;<a href="?action=delete&id=<?=end($tree)?>">Delete</a>
<?php
}
?>
                    &nbsp;|&nbsp;<a href="?action=<?=$action?>&id=<?=end($tree)?>"><?=$status?></a>
                </td>
                <td><a href=".?action=category&category=<?=end($tree)?>" target="_blank"><?=$total?></a></td>
            </tr>
<?php
}
    }
}

function isActive($category_id)
{
    global $DB;
    $row = categoryFromDatabase($category_id);
    if ('y' == $row['active']) {
        return true;
    }

    return false;
}
function displayList()
{
?>
<table class="table_grid">
    <tr><th>Category Id</th><th>Category Name</th><th>Active</th><th>Parent</th><th>Category</th><th>Sub Category</th><th>&nbsp;</th><th>Total</th></tr>
<?php
buildTableView();
?>
</table>
<br />
<?php
}
$items = array('total' => categoryCount());
?>
<html>
<head>
    <title>Product Category Types</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript">
//<![CDATA[
function checkParent(value)
{
    getElement('parent_row').style.display=(value=='n') ? '' : 'none';
    if(value=='y') setValue('categories[parent]',0);
}
function categoryDelete(id)
{
    var url='?action=delete&id='+id;
    if(confirm('Are you sure you want to delete this product categoroy??')) window.location=url;
}
function updateTotals()
{
    var title = getValue('categories[meta_title]');
    var meta = getValue('categories[meta_keywords]');
    var desc = getValue('categories[meta_description]');

    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for(i=0;i<keywords.length;i++) {
        if(keywords[i] != '')
            len++;
    }
    getElement('meta_title').innerHTML = title.length;
    getElement('meta_keywords').innerHTML = len;
    getElement('meta_description').innerHTML = desc.length;
}
//]]>
    </script>
</head>
<body>
    <div class="product_content">
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td style="text-align:center;width:20%;background:#EEEEEE;"><b>Categories</b></td>
            <td style="text-align:right;width:80%;background:#EEEEEE;">
                <a href="?action=add">Add New Category</a> |
                <a href="?">List All Categories (<?=$items['total']?>)</a>
            </td>
        </tr>
    </table>
    <br />
<?php
//procesing switch-case:
switch ($action) {
    case 'do_add':
        unset($_REQUEST['id']);
    case 'do_edit':
        $_REQUEST['id'] = $id = doEdit($_REQUEST);
        $action         = 'edit';
        break;

    case 'do_delete':
        categoryDelete($_REQUEST['id']);
        $action = 'list';
        break;

    case 'activate':
        categorySetValue($_REQUEST['id'], 'active', 'y');
        $action = 'list';
        break;

    case 'deactivate':
        categorySetValue($_REQUEST['id'], 'active', 'n');
        $action = 'list';
        break;
}
if ('edit' != $action && 'add' != $action && 'delete' != $action) {
?>
<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="toggleSection(&quot;sectionTreeView&quot;);return false"><i class="fas fa-caret-right"></i> <b>Tree View</b></a>
    <br /><br />
    <div id="sectionTreeView" class="child" style="display:none">
        <iframe src="treeview.php" style="width:95%;height:300px;border-width:0px;"></iframe>
    </div>
</div>
<br />
<?php
}
//now the display switch-case:
switch ($action) {
    case 'add':
        unset($_REQUEST['id']);
    case 'edit':
        showEdit($_REQUEST);
        break;
    case 'delete':
        showDelete($_REQUEST['id']);
        break;
    case 'list':
    default:
        displayList();
}

include_once __DIR__ . '/footer.inc.php';
