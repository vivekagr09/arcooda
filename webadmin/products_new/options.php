<?php
include_once __DIR__ . '/common.inc.php';

$option_list = array(
    'style'           => array(
        'description' => 'Product Styles',
        'table'       => 'product_style',
        'key1_name'   => 'id',
        'value1_name' => 'style'
    ),
    'pcb_type'        =>array(
        'description' => 'Product PCBs & Kits Types',
        'table'       => '`products_pcb_types`',
        'key1_name'   => 'id',
        'value1_name' => 'pcb_type',
    ),
    'storage'         => array(
        'description' => 'Storage Locations',
        'table'       => '`product_storage_location`',
        'key1_name'   => 'id',
        'value1_name' => 'location',
    ),
    'available'       => array(
        'description' => 'Product Availabilities',
        'table'       => '`product_availability`',
        'key1_name'   => 'id',
        'value1_name' => 'availability',
    ),
    'condition'       => array(
        'description' => 'Product Conditions',
        'table'       => '`product_conditions`',
        'key1_name'   => 'id',
        'value1_name' => 'condition',
    ),
    'product_material'       => array(
        'description' => 'Product Material',
        'table'       => '`product_material`',
        'key1_name'   => 'id',
        'value1_name' => 'material',
    ),
    'product_file_type'       => array(
        'description' => 'Product File Type',
        'table'       => '`product_art_file_type`',
        'key1_name'   => 'id',
        'value1_name' => 'file_type',
    ),
    'product_series'       => array(
        'description' => 'Product Series',
        'table'       => '`product_series`',
        'key1_name'   => 'id',
        'value1_name' => 'series',
    ),
    'cabinet_button_brand'       => array(
        'description' => 'Button Brand',
        'table'       => '`product_cabinet_button_brand`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'cabinet_joystick_brand'       => array(
        'description' => 'Joystick Brand',
        'table'       => '`product_cabinet_joystick_brand`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'cabinet_lighting'       => array(
        'description' => 'LED Cabinet Lighting',
        'table'       => '`product_cabinet_lighting`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'cabinet_panel_material'       => array(
        'description' => 'Cabinet Panel Material',
        'table'       => '`product_cabinet_panel_material`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'cabinet_material'       => array(
        'description' => 'Cabinet Material',
        'table'       => '`product_cabinet_material`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'monitor_part_number'       => array(
        'description' => 'Monitor Part Number',
        'table'       => '`product_monitor_part_number`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'monitor_other_options'       => array(
        'description' => 'Monitor Other Options',
        'table'       => '`product_monitor_other_options`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'monitor_touchscreen'       => array(
        'description' => 'Monitor Touch Screen',
        'table'       => '`product_monitor_touchscreen`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'monitor_specifications'       => array(
        'description' => 'Monitor Specification',
        'table'       => '`product_monitor_specifications`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'monitor_size'       => array(
        'description' => 'Monitor Size',
        'table'       => '`product_monitor_size`',
        'key1_name'   => 'id',
        'value1_name' => 'data_value',
    ),
    'warranty'        => array(
        'description' => 'Product Warranty Terms',
        'table'       => "`highwaygroup`.`waranty_terms`",
        'key1_name'   => 'id',
        'value1_name' => 'waranty',
    ),
    'colour'          => array(
        'description' => 'Product Colours',
        'table'       => '`product_colour`',
        'key1_name'   => 'id',
        'value1_name' => 'colour',
        'value2_name' => 'code2',
        'value3_name' => 'code3',
    ),
    'monitor_type'        => array(
        'description' => 'Monitor Type',
        'table'       => "`monitor_type`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_resolution'        => array(
        'description' => 'Monitor Resolution',
        'table'       => "`monitor_resolution`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_ratio'        => array(
        'description' => 'Monitor Ratio',
        'table'       => "`monitor_ratio`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
     'monitor_brightness'        => array(
        'description' => 'Monitor Brightness',
        'table'       => "`monitor_brightness`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_contrast_ratio'        => array(
        'description' => 'Contrast Ratio',
        'table'       => "`monitor_contrast_ratio`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_viewing_angle'        => array(
        'description' => 'Viewing Angle',
        'table'       => "`monitor_viewing_angle`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_display_color'        => array(
        'description' => 'Display Color',
        'table'       => "`monitor_display_color`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_refresh_rate'        => array(
        'description' => 'Refresh Rate',
        'table'       => "`monitor_refresh_rate`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_panel_type'        => array(
        'description' => 'Panel Type',
        'table'       => "`monitor_panel_type`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_display_surface'        => array(
        'description' => 'Display Surface',
        'table'       => "`monitor_display_surface`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_power'        => array(
        'description' => 'Power',
        'table'       => "`monitor_power`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_low_resolution_support'        => array(
        'description' => 'Low Resolution Support',
        'table'       => "`monitor_low_resolution_support`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
     'monitor_pc_resolution'        => array(
        'description' => 'PC Monitor Resolution',
        'table'       => "`monitor_pc_resolution`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'monitor_vesa_mount'        => array(
        'description' => 'VESA Mount',
        'table'       => "`monitor_vesa_mount`",
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),

);

$option_name = $_REQUEST['option_name'];
$action      = $_REQUEST['action'];
if (!array_key_exists($option_name, $option_list)) {
    $action = 'options';
}

switch ($action) {
    case 'do_add':
    case 'do_edit':
        optionsDoEdit($option_name);
        if (isset($_REQUEST['return_message'])) {
            $action = 'do_select';
        } else {
            $action = 'do_list';
        }

        break;
}

$get_vars  = $_REQUEST;
$keep_only = array('option_name', 'page', 'order', 'order_by', 'option_id', 'return_message');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

switch ($action) {
    case 'do_select':
        echo 'Not yet done';
        die();
    case 'do_list':
        $get_vars['action'] = 'list';
        header('Location: ?' . http_build_query($get_vars));
        die();
}

// ------------------------------- Headers Sent -------------------------------

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
optionsHeader($option_name);
switch ($action) {
    case 'options':
        optionsDisplayAll();
        break;
    default:
    case 'list':
        optionsDisplay($option_name);
        break;
    case 'add':
        $_REQUEST['option_id'] = '';
    case 'edit':
        optionsAddEdit($option_name);
        break;
}
optionsFooter($option_name);
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
die();

// --------------------------------- Finished ---------------------------------

function optionsDisplayAll()
{
    global $option_list, $get_vars;

    $list_url           = $get_vars;
    $list_url['action'] = 'list';
    unset($list_url['option_name']);
    $list_url = '?' . http_build_query($list_url) . '&option_name=';
?>
<table class="table_grid">
    <tr><th>Select an Option</th></tr>
<?php
$alt = false;
    foreach ($option_list as $option_name => $option_info) {
        if ('' == $option_info['description']) {
            $option_info['description'] = ucwords(str_replace('_', ' ', $option_name));
        }

?>
    <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>><td><a target="_blank" href="<?=$list_url . $option_name?>"><?php hecho($option_info['description'])?></a></td></tr>
<?php
$count++;
    }
?>
</table>
<?php
}

function optionsDisplay($option_name)
{
    global $DB, $option_list, $get_vars;

    $page     = intval($_REQUEST['page']);
    $order_by = (isset($_REQUEST['order_by'])) ? $_REQUEST['order_by'] : 'id';
    $order    = $_REQUEST['order'];
    if ('desc' != $order) {
        $order = 'asc';
    }

    $sort_url           = $get_vars;
    $sort_url['action'] = 'list';
    unset($sort_url['order_by']);
    unset($sort_url['order']);
    $sort_url = '?' . http_build_query($sort_url) . '&order_by=';

    $add_url           = $get_vars;
    $add_url['action'] = 'add';
    unset($add_url['option_id']);
    $add_url = '?' . http_build_query($add_url);

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['option_id']);
    $edit_url = '?' . http_build_query($edit_url) . '&option_id=';

    $select_url           = $get_vars;
    $select_url['action'] = 'do_select';
    unset($select_url['option_id']);
    $select_url = '?' . http_build_query($select_url) . '&option_id=';

    $delete_url           = $get_vars;
    $delete_url['action'] = 'delete';
    unset($delete_url['option_id']);
    $delete_url = '?' . http_build_query($delete_url) . '&option_id=';

    $option_info = $option_list[$option_name];
    if ('' == $option_info['description']) {
        $option_info['description'] = ucwords(str_replace('_', ' ', $option_name));
    }

    $key_list   = optionsGetFieldList($option_info, 'key');
    $value_list = optionsGetFieldList($option_info, 'value');
    $field_list = $key_list + $value_list;

    $result_list = array();
    $query       = $DB->query(sprintf(
        "SELECT %s FROM %s%s",
        implode(', ', $field_list),
        $option_info['table'],
        (array_key_exists($order_by, $field_list)) ? sprintf(
            " ORDER BY `%s`",
            $order_by
        ) : ''
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $result_list[] = $row;
    }
    $DB->free_result($query);

    if ('desc' == $order) {
        $result_list = array_reverse($result_list);
    }
    $items_per_page = 50;
    $count_total    = count($result_list);
    $result_list    = array_chunk($result_list, $items_per_page);
    $count_pages    = count($result_list);
    $page           = max(0, min($count_pages - 1, $page));
    if(count($result_list))
    $result_list    = $result_list[$page];
    $count_display  = count($result_list);

    if (!is_array($result_list)) {
        $result_list = array();
    }

    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url) . '&page=';

    $page_links = formatHTMLPageLinks($page_url, $page, $count_display, $count_total, $items_per_page);
?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionOptions&quot;);return false">' . hecho($option_info['description'], 1) . '</a>', '<a href="' . $add_url . '">Add New</a>')?>

<div name="sectionOptions">
<table class="table_grid">
    <tr>
<?php
foreach ($key_list as $field_name => $field_expr) {
        echo '<th><a href="' . $sort_url . $field_name . (($order_by == $field_name) ? '&order=desc' : '') . '">' . hecho(ucfirst($field_name), 1) . '</a></th>' . "\n";
    }

    foreach ($value_list as $field_name => $field_expr) {
        echo '<th style="width:' . floor(100.0 / count($value_list)) . '%;"><a href="' . $sort_url . $field_name . (($order_by == $field_name) ? '&order=desc' : '') . '">' . hecho(ucfirst($field_name), 1) . '</a></th>' . "\n";
    }

?>
        <th></th>
    </tr>
<?php
$alt = false;
    foreach ($result_list as $row) {
        $option_id = array();
        foreach ($key_list as $field_name => $field_expr) {
            $option_id[] = $row[$field_name];
        }

        $option_id = implode('|', $option_id);
?>
    <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
<?php
foreach ($key_list as $field_name => $field_expr) {
            echo '<td>' . hecho($row[$field_name], 1) . '</td>' . "\n";
        }

        foreach ($value_list as $field_name => $field_expr) {
            echo '<td>' . hecho($row[$field_name], 1) . '</td>' . "\n";
        }

?>
        <td style="white-space:nowrap;">
            <a href="<?=$edit_url . $option_id?>">Edit</a>
<?php
if ('' != $_REQUEST['return_message']) {
            echo '<a href="' . $select_url . $option_id . '">Delete</a>' . "\n";
        }

        if (false) {
            echo '<a href="' . $delete_url . $option_id . '">Delete</a>' . "\n";
        }

?>
        </td>
    </tr>
<?php
}
?>
</table>
<br />
<?=$page_links?>
<br /><br />
</div>
<?php
}

function optionsAddEdit($option_name)
{
    global $DB, $option_list, $get_vars;

    $add_url           = $get_vars;
    $add_url['action'] = 'do_add';
    unset($add_url['option_id']);
    $add_url = '?' . http_build_query($add_url);

    $edit_url           = $get_vars;
    $edit_url['action'] = 'do_edit';
    $edit_url           = '?' . http_build_query($edit_url);

    $option_info = $option_list[$option_name];
    if ('' == $option_info['description']) {
        $option_info['description'] = ucwords(str_replace('_', ' ', $option_name));
    }

    $key_list   = optionsGetFieldList($option_info, 'key');
    $value_list = optionsGetFieldList($option_info, 'value');
    $field_list = $key_list + $value_list;

    $row = $DB->query_first(sprintf(
        "SELECT %s FROM %s WHERE CONCAT_WS('|', %s) = %s%s LIMIT 1",
        implode(', ', $field_list),
        $option_info['table'],
        implode(',', $key_list),
        $DB->escape($_REQUEST['option_id']),
        (array_key_exists($order_by, $field_list)) ? sprintf(
            " ORDER BY `%s`",
            $order_by
        ) : ''
    ), MYSQLI_ASSOC);

?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionOption&quot;);return false">' . ((false !== $row) ? 'Update ' : 'Add To ') . hecho($option_info['description'], 1) . '</a>')?>

<div name="sectionOption">
    <form action="<?=(false !== $row) ? $edit_url : $add_url?>" method="post">
    <table class="table_fields">
<?php
if (false !== $row) {
        foreach ($key_list as $key_name => $key_expr) {
            echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($key_name, 1))) . ' :</th><td>' . hecho($row[$key_name], 1) . '</td></tr>' . "\n";
        }
    }

    foreach ($value_list as $value_name => $value_expr) {
        echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($value_name, 1))) . ' :</th><td><input style="width:98%;" type="text" name="option[' . hecho($value_name, 1) . ']" value="' . hecho($row[$value_name], 1) . '" /></td></tr>' . "\n";
    }

?>
    </table>
    <br />
    <div style="text-align:center;"><input type="submit" value="<?=(false !== $row) ? 'Update' : 'Add'?>" /></div>
    </form>
    <br /><br />
</div>
<?php
}

function optionsGetFieldList($option_info, $type = array('key', 'value'))
{
    if (is_string($type)) {
        $type = array($type);
    }

    $field_list = array();
    foreach ($type as $field_type) {
        $i = 1;
        while ('' != $option_info[$field_type . $i . '_name']) {
            if ('' != $option_info[$field_type . $i . '_expr']) {
                $field_list[$option_info[$field_type . $i . '_name']] = $option_info[$field_type . $i . '_expr'] . ' AS `' . $option_info[$field_type . $i . '_name'] . '`';
            } else {
                $field_list[$option_info[$field_type . $i . '_name']] = '`' . $option_info[$field_type . $i . '_name'] . '`';
            }

            $i++;
        }
    }
    return $field_list;
}

function optionsHeader($option_name)
{
    global $DB, $option_list, $get_vars;

    if (array_key_exists($option_name, $option_list)) {
        $option_info = $option_list[$option_name];
        if ('' == $option_info['description']) {
            $option_info['description'] = ucwords(str_replace('_', ' ', $option_name));
        }

        $add_url           = $get_vars;
        $add_url['action'] = 'add';
        unset($add_url['option_id']);
        $add_url = '?' . http_build_query($add_url);

        $list_url           = $get_vars;
        $list_url['action'] = 'list';
        $list_url           = '?' . http_build_query($list_url);

        /*$row = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    $option_info['table']
    ), MYSQLI_ASSOC);*/
    }
?>
<!-- optionsHeader() -->

<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td style="text-align:center;width:20%;background:#EEEEEE;"><b>Options</b></td>
            <td style="text-align:right;width:80%;background:#EEEEEE;">
<?php
if (array_key_exists($option_name, $option_list)) {
?>
            <a href="<?=$add_url?>">Add New <?php hecho($option_info['description'])?></a> |
            <a href="<?=$list_url?>">List All <?php hecho($option_info['description'])?> (<?=intval($row[0])?>)</a>
<?php
}
?>
            </td>
        </tr>
    </table>
    <div>
        <br />

<!-- /optionsHeader() -->
<?php
}

function optionsFooter($option_name)
{
?>
    </div>
</div>
<?php
}

function optionsDoEdit($option_name)
{
    global $DB, $option_list, $get_vars;

    $option_id   = $_REQUEST['option_id'];
    $option_info = $option_list[$option_name];
    $key_list    = optionsGetFieldList($option_info, 'key');
    $value_list  = optionsGetFieldList($option_info, 'value');
    $field_list  = $key_list + $value_list;

    $row = $DB->query_first(sprintf(
        "SELECT %s FROM %s WHERE CONCAT_WS('|', %s) = %s%s LIMIT 1",
        implode(', ', $field_list),
        $option_info['table'],
        implode(',', $key_list),
        $DB->escape($option_id),
        (array_key_exists($order_by, $field_list)) ? sprintf(
            " ORDER BY `%s`",
            $order_by
        ) : ''
    ), MYSQLI_ASSOC);
    if ($row) {
        $update_vals = array();
        foreach ($value_list as $value_name => $value_expr) {
            $update_vals[] = "`" . $value_name . "`=" . $DB->escape($_REQUEST['option'][$value_name]);
        }

        $query = $DB->query(sprintf(
            "UPDATE %s SET %s WHERE CONCAT_WS('|', %s) = %s LIMIT 1",
            $option_info['table'],
            implode(", ", $update_vals),
            implode(',', $key_list),
            $DB->escape($option_id)
        ));
    } else {
        $insert_vals = array();
        foreach ($value_list as $value_name => $value_expr) {
            $insert_vals["`" . $value_name . "`"] = $DB->escape($_REQUEST['option'][$value_name]);
        }
        $query = $DB->query(sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $option_info['table'],
            implode(", ", array_keys($insert_vals)),
            implode(", ", array_values($insert_vals))
        ));
        $option_id = $DB->insert_id;
    }
    return $option_id;
}
