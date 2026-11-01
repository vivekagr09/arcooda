<script type="text/javascript">
function updatefreightname(data){
      document.getElementById('freight_name').value=data.options[data.selectedIndex].text;
}
</script>
<?php
include_once __DIR__ . '/common.inc.php';
$fields_address['address']=[
    'company_name'=>'Company name',
    'company_name_chinese'=>'公司名',
    'address'=>'Address',
    'address_chinese'=>'地址',
    'city'=>'City',
    'city_chinese'=>'城市',
    'state'=>'State',
    'state_chinese'=>'状态',
    'post_code'=>'Post Code',
    'post_code_chinese'=>'邮政编码',
    'country'=>'Country / 国家',
    'first_name'=>'First Name',
    'first_name_chinese'=>'名',
    'last_name'=>'Last Name',
    'last_name_chinese'=>'姓',
    'email'=>'Email / 电子邮件',
    'phone'=>'Phone / 电话',
    'phone2'=>'2nd Contact Number /第二联络号码',
    'fax'=>'Fax / 传真',
    'mobile'=>'Mobile /移动的',
    'wechat'=>'Wechat / 微信',
];
$fields_address['freight']=[
    'freight_id'=>'Freight',
    'account_number'=>'Account Number',
    'freight_name'=>'Freight Name',
    'notes'=>'Notes'

];
$option_list = array(
    'freight'           => array(
        'description' => 'Order Freight',
        'table'       => 'purchase_order_freight',
        'key1_name'   => 'id',
        'value1_name' => 'freight_name'
    ),
    'address'        =>array(
        'description' => 'Order Address',
        'table'       => '`purchase_order_address`',
        'key1_name'   => 'id',
        'value1_name' => 'address',
    ),
    
    /*
    'version'         => array(
        'description' => 'Version Type',
        'table'       => 'products_versions',
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'features'        => array(
        'description' => 'Product Additional Features',
        'table'       => 'products_features',
        'key1_name'   => 'id',
        'value1_name' => 'feature',
    ),
    'genre'            => array(
        'description' => 'Game Genre / Style',
        'table'       => 'products_genres',
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'country_location' => array(
        'description' => 'Country Locations',
        'table'       => 'product_country_location',
        'key1_name'   => 'id',
        'value1_name' => 'name',
    ),
    'packing'          => array(
        'description' => 'Packing Style',
        'table'       => 'invoice_packing',
        'key1_name'   => 'id',
        'value1_name' => 'packing',
    ),
    'pedal_configuration'=>array(
        'description'=>'Product Pedal Configurations',
        'table'=>'products_pedal_configs',
        'key1_name'=>'id',
        'value1_name'=>'pedal_configuration',
    ),
    'plug_type'=>array(
        'description'=>'Product Plug Types',
        'table'=>'products_plug_types',
        'key1_name'=>'id',
        'value1_name'=>'name',
    ),
    'screen_resolution'=>array(
        'description'=>'Product Screen Resolutions',
        'table'=>'products_screen_resolutions',
        'key1_name'=>'id',
        'value1_name'=>'screen_resolution',
    ),
    'screen_size'=>array(
        'description'=>'Product Screen Sizes',
        'table'=>'products_screen_sizes',
        'key1_name'=>'id',
        'value1_name'=>'screen_size',
    ),
    'section'=>array(
        'description'=>'Product Sections',
        'table'=>'products_section',
        'key1_name'=>'id',
        'value1_name'=>'name',
    ),
    'shifter'=>array(
        'description'=>'Product Shifter Types',
        'table'=>'products_shifter_types',
        'key1_name'=>'id',
        'value1_name'=>'shifter_type',
    ),
    'ship_terms'    => array(
        'description' => 'Product Shipping Terms',
        'table'       => 'purchase_orders_terms',
        'key1_name'   => 'id',
        'value1_name' => 'term',
    ),
    'steering'=>array(
        'description'=>'Product Steering Types',
        'table'=>'products_steering_types',
        'key1_name'=>'id',
        'value1_name'=>'steering_type',
    ),
    'voltages'=>array(
        'description' => 'Power Voltages',
        'table'       => "`highwaygroup`.`invoice_voltages`",
        'key1_name'   => "id",
        'value1_name' => "voltage",
    ),
    */
);

$option_name = $_REQUEST['option_name'];
$action      = $_REQUEST['action'];
if (!array_key_exists($option_name, $option_list)) {
    $action = 'options';
}

switch ($action) {
    case 'do_add':
    case 'do_edit':
        optionsDoEdit($option_name,$fields_address[''.$option_name.'']);
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
        optionsAddEdit($option_name,$fields_address[''.$option_name.'']);
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
    $result_list    = $result_list[$page];
    if($result_list)
    $count_display  = count($result_list);
    else
     $count_display  = 0;   
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

function optionsAddEdit($option_name,$fields_data)
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
        '*',
        $option_info['table'],
        implode(',', $key_list),
        $DB->escape($_REQUEST['option_id']),
        (array_key_exists($order_by, $field_list)) ? sprintf(
            " ORDER BY `%s`",
            $order_by
        ) : ''
    ), MYSQLI_ASSOC);

?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionOption&quot;);return false">' . (('add' !== $_REQUEST['action']) ? 'Update ' : 'Add To ') . hecho($option_info['description'], 1) . '</a>')?>

<div name="sectionOption">
    <form action="<?=(false !== $row) ? $edit_url : $add_url?>" method="post">
    <table class="table_fields">
<?php
    if (false !== $row) {
        foreach ($key_list as $key_name => $key_expr) {
            echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($key_name, 1))) . ' :</th><td>' . hecho($row[$key_name], 1) . '</td></tr>' . "\n";
        }
    }
    foreach ($fields_data as $value_expr => $value_name) {
        if($value_expr=='country'){
          
             echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($value_name, 1))) . ' :</th><td><select style="width:98%;"  name="option[' . hecho($value_expr, 1) . ']" ><option value="">Not Selected</option>'.formatHTMLOptions(supplierGetOptionList('country'), $row[$value_expr]).'</select></td></tr>' . "\n";
        }else if($value_expr=='freight_id'){
            $courier_list    = getCourierproviderList();
             echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($value_name, 1))) . ' :</th><td><select style="width:98%;"  name="option[' . hecho($value_expr, 1) . ']" id="freight_id" onchange=updatefreightname(this)><option value="">Not Selected</option>'.formatHTMLOptions( $courier_list, $row[$value_expr]).'</select></td></tr>' . "\n";

        }else if($value_expr=='freight_name'){
            echo '<input style="width:98%;" type="hidden" id="freight_name" name="option[' . hecho($value_expr, 1) . ']" value="' . hecho($row[$value_expr], 1) . '" />';

        }else{
            echo '<tr><th>' . ucwords(str_replace('_', ' ', hecho($value_name, 1))) . ' :</th><td><input style="width:98%;" type="text" name="option[' . hecho($value_expr, 1) . ']" value="' . hecho($row[$value_expr], 1) . '" /></td></tr>' . "\n";
        }
        
    }

?>
    </table>
    <br />
    <div style="text-align:center;"><input type="submit" value="<?=('add' !== $_REQUEST['action']) ? 'Update' : 'Add'?>" /></div>
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

        $row = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    $option_info['table']
    ), MYSQLI_ASSOC);
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
            <a href="<?=$list_url?>">List All <?php hecho($option_info['description'])?> (<?=intval($row['total'])?>)</a>
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

function optionsDoEdit($option_name,$data_fields)
{
    global $DB, $option_list, $get_vars,$_GLOBALS;

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
        foreach ($data_fields as $value_name => $value_expr) {
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
        foreach ($data_fields as $value_name => $value_expr) {
            $insert_vals["`" . $value_name . "`"] = $DB->escape($_REQUEST['option'][$value_name]);
        }
        $insert_vals['created_by'] = $_GLOBALS['staff']['id'];
        $insert_vals['created_at'] = "NOW()";
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
