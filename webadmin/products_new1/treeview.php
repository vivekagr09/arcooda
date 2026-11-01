<?php
include_once __DIR__ . '/common.inc.php';

$_GLOBALS['popup_window'] = true;

include_once __DIR__ . '/header.inc.php';

$id      = 0;
$recurse = array();

if (array_key_exists('id', $_REQUEST)) {
    $id = intval($_REQUEST['id']);
}
if (array_key_exists('recurse', $_REQUEST)) {
    $recurse = explode('.', $_REQUEST['recurse']);
}

$type_tree    = productGetOptionList('type_tree');
$type_list    = productGetOptionList('type');
$type_list[0] = 'Product Categories';

function treeview_recurse($id = 0, $recurse = array())
{
    global $type_tree, $type_list;
    $view_url           = $_GET;
    $view_url['action'] = 'list';
    $view_url['type']   = intval($id);
    $view_url           = '.?' . http_build_query($view_url);
?>
<li>
<?php
    if (array_key_exists($id, $type_tree)) {
        if (in_array($id, $recurse)) {
            $delrecurse_url            = $_GET;
            $delrecurse_url['recurse'] = trim(str_replace('.' . $id . '.', '.', '.' . implode('.', $recurse) . '.'), '.');
            $delrecurse_url            = '?' . http_build_query($delrecurse_url);
?>
    <a name="type_<?= $id ?>" href="<?php hecho($delrecurse_url) ?>#type_<?= $id ?>"><i class="fas fa-minus-square"></i></a>
    <a href="<?php hecho($view_url) ?>" target="_top"><?php hecho($type_list[$id]) ?></a>
    <ul class="type_tree">
<?php
            foreach ($type_tree[$id] as $sub_id) {
                treeview_recurse($sub_id, $recurse);
            }
?>
    </ul>
<?php
        } else {
            $addrecurse_url            = $_GET;
            $addrecurse_url['recurse'] = implode('.', array_merge($recurse, array($id)));
            $addrecurse_url            = '?' . http_build_query($addrecurse_url);
?>
    <a name="type_<?= $id ?>" href="<?php hecho($addrecurse_url) ?>#type_<?= $id ?>"><i class="fas fa-plus-square"></i></a>
    <a href="<?php hecho($view_url) ?>" target="_top"><?php hecho($type_list[$id]) ?></a>
<?php
        }
    } else {
?>
    <a href="<?php hecho($view_url) ?>" target="_top"><?php hecho($type_list[$id]) ?></a>
<?php
    }
?>
</li>
<?php
}
?>

<style>
ul.type_tree,ul.type_tree>li {list-style:none inside none;padding:0 0 0 1em;margin:0}
ul.type_tree>li>a {color:inherit;text-decoration:none}
</style>

<ul class="type_tree">
<?php
if (0 == $id) {
    foreach ($type_tree[0] as $v) {
        treeview_recurse($v, $recurse);
    }
} else {
    treeview_recurse($id, $recurse);
}
?>
</ul>
<?php

include_once __DIR__ . '/footer.inc.php';
