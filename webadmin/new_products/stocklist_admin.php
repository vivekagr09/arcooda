<?php
$section_code = "STOCK";
include_once __DIR__ . '/common.inc.php';

if (!function_exists('http_build_query')) {
    function http_build_query($formdata)
    {
        $arr = array();
        if (is_array($formdata) && count($formdata) > 0) {
            foreach ($formdata as $key => $val) {
                if ('' != $val) {
                    $arr[] = urlencode($key) . "=" . urlencode($val);
                }
            }

            return implode("&", $arr);
        }
        return '';
    }
}

if (!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
}

//Strip the goddamn slashes before it touches anything in the system (Politically correct time to do it)
$posted        = array_merge($_GET, $_POST);
$public_posted = array_merge($_GET, $_POST);
foreach ($public_posted as $key => $value) {
    if (is_array($value)) {
        unset($public_posted[$key]);
    }
}

$get_vals = $public_posted;
unset($get_vals['action']);
$safety_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($get_vals);

switch ($posted['action']) {
    case 'update_qty':
        if (count($posted['check_qty']) > 0) {
            foreach ($posted['check_qty'] as $key => $value) {
                $sql = "UPDATE stock SET sample_qty='" . $posted['sample_qty'][$key] . "', stock_qty='" . $posted['stock_qty'][$key] . "', order_qty='" . $posted['order_qty'][$key] . "' WHERE id='" . $key . "' LIMIT 1";
                $DB->query($sql);
            }
        }

        header("location: " . $safety_url);
        break;
    case 'do_add':
    case 'do_edit':
        $create_new = false;
        if (intval($data1['id']) < 1) {
            $create_new            = true;
            $stock_id              = stocklistAddItem();
            $_POST['stock_id']     = $stock_id;
            $_POST['action']       = 'do_edit';
            $_POST['data1']['id']  = $stock_id;
            $posted['stock_id']    = $stock_id;
            $posted['action']      = 'do_edit';
            $posted['data1']['id'] = $stock_id;
        }
        stocklistEditItem();
        $posted['action'] = 'list';
        if ('true' == $posted['go_next']) {
            if (false == $create_new) {
                $sql = "SELECT id FROM stock WHERE id>" . intval($posted['data1']['id']) . " ORDER BY id LIMIT 1";
                $row = $DB->query_first($sql);
                if ($row['id'] > 0) {
                    $posted['id']     = $row['id'];
                    $posted['action'] = 'edit';
                    header("location: ?action=edit&id=" . $row['id']);
                    die();
                }
            } else {
                $posted['action'] = 'add';
                header("location: ?action=add");
                die();
            }
        }
        header("location: " . $safety_url);
        die();
        break;
    case 'delete':
        $posted['action'] = 'list';
        $sql              = "DELETE FROM stock WHERE id=" . intval($posted['id']) . " LIMIT 1";
        $DB->query($sql);
        $sql = "DELETE FROM stock_admin_notes WHERE stock_id=" . intval($posted['id']) . " LIMIT 1";
        $DB->query($sql);
        $sql = "DELETE FROM stock_colour_qty WHERE stock_id=" . intval($posted['id']) . " LIMIT 1";
        $DB->query($sql);
        $sql = "DELETE FROM stock_dimensions_qty WHERE stock_id=" . intval($posted['id']) . " LIMIT 1";
        $DB->query($sql);
        header("location: " . $safety_url);
        break;
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

$sql = "SELECT COUNT(id) AS total FROM stock";
$row = $DB->query_first($sql);

?>
<br />

<table width="100%" cellspacing="2" cellpadding="2"><tr bgcolor="#EEEEEE">
	<td width="20%" align="center"><b>Stocklist</b></td>
	<td width="80%" align="right" style="white-space:nowrap;">
		<a href="javascript:MM_openBrWindow('/webadmin/products/admin_help.html','help','scrollbars=yes,width=500,height=400')">HELP</a> |
		<a href="?action=add">Add Stocklist</a> |
		<a href="?action=list">List Stocklist (<?=intval($row['total'])?>)</a>
	</td>
</tr></table>

<script type="text/javascript" src="/webadmin/expandToggle.js"></script>

<?php
stocklistCategories();
?>

<table width="95%" align="center">
<form accept-charset="utf-8" method="post" action="<?=$safety_url?>">
	<tr><td style="text-align:right;">
		<input name="search_for" value="<?=str_replace('"', '&quot;', $posted['search_for'])?>" />
		<input type="submit" value="Search" />
	</td></tr>
</form>
</table>
<?php

switch ($posted['action']) {
    case 'add':
        stocklistEditForm(0);
        break;
    case 'edit':
        stocklistEditForm($posted['id']);
        break;
    default:
    case 'list':
        stocklistListForm($posted);
        break;
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
return;

function stocklistEditForm($stock_id)
{
    global $DB, $_GLOBALS, $allowed_sections;

    $data1 = array(); //Data
    $data2 = array(); //Admin Notes
    $data3 = array(); //Colours
    $data4 = array(); //Dimensions

    $data1['id']                   = $stock_id;
    $data1['cost_currency']        = $_GLOBALS['site_options']['cost_currency'];
    $data1['approx_cost_currency'] = $_GLOBALS['site_options']['approx_cost_currency'];
    $data1['product_type']         = $_GLOBALS['site_options']['default_product_type'];
    $data1['storage']              = $_GLOBALS['site_options']['default_storage_location'];
    $data1['country_location']     = $_GLOBALS['site_options']['default_country_location'];
    $data1['condition']            = $_GLOBALS['site_options']['default_condition'];
    $data1['supplier']             = $_GLOBALS['site_options']['default_supplier'];
    $data1['date_received']        = date('Y-m-d');

    if ($stock_id > 0) {
        $data1 = $DB->query_first(
            " SELECT" .
            "	s.*," .
            "	UNIX_TIMESTAMP(s.date_received) AS time_received" .
            " FROM stock s" .
            " WHERE s.id='" . $stock_id . "'" .
            " LIMIT 1"
        );
        $data1['date_received'] = date('Y-m-d', $data1['time_received']);

        $data2 = array();
        $query = $DB->query("SELECT * FROM stock_admin_notes WHERE stock_id='" . $stock_id . "'");
        while ($row = $DB->fetch_array($query)) {
            $data2[] = $row;
        }

        $data3 = array();
        $query = $DB->query("SELECT * FROM stock_colour_qty WHERE stock_id='" . $stock_id . "'");
        while ($row = $DB->fetch_array($query)) {
            $data3[] = $row;
        }

        $data4 = array();
        $query = $DB->query("SELECT * FROM stock_dimensions_qty WHERE stock_id='" . $stock_id . "'");
        while ($row = $DB->fetch_array($query)) {
            $data4[] = $row;
        }

    }

    echo "<br />\n";
    include 'stocklist_form.inc.php';
}

function stocklistListForm($posted)
{
    global $DB, $allowed_sections;

    //Build a list of fields, their names, labels and sort keys
    $narrow_list = array(
        'company'  => 's.company_stock',
        'catagory' => 's.product_catagory',
        'type'     => 's.product_type',
        'supplier' => 's.supplier',
        'location' => 's.storage',
    );
    $field_list = array(
        'id'               => array('ID', 's.id', 'center'),
        'product_name'     => array('Product Name', 's.product_name', 'left'),
        'product_catagory' => array('Category', 'spc.name', 'center'),
        'country_location' => array('Country Location', 'pcl.name', 'left'),
        'unit_price'       => array('Unit Price', 's.unit_price*c.usfrom', 'right'),
        'sample_qty'       => array('Sample', 's.sample_qty', 'right'),
        'stock_qty'        => array('Stock', 's.stock_qty', 'right'),
        'order_qty'        => array('Order', 's.order_qty', 'right'),
    );
    $search_fields = array('s.product_name', 's.id', 's.coe_id');
    $select_from   = " FROM stock s" .
        " LEFT JOIN product_country_location pcl ON pcl.id=s.country_location" .
        " LEFT JOIN stock_product_catagories spc ON spc.id=s.product_catagory" .
        " LEFT JOIN `highwaygroup`.`currency` AS `c` ON `c`.`code` = `s`.`cost_currency`";

    //Build the search query
    $whereAND = array();
    foreach ($narrow_list as $key => $field) {
        if (isset($posted[$key])) {
            $whereAND[] = $field . '=' . intval($posted[$key]);
        }
    }

    if ('' != $posted['search_for']) {
        foreach (explode(' ', $posted['search_for']) as $search) {
            if ('' != $search) {
                $whereOR = array();
                foreach ($search_fields as $field) {
                    $whereOR[] = $field . " LIKE '%" . addslashes($search) . "%'";
                }

                $whereAND[] = '(' . implode(' OR ', $whereOR) . ')';
            }
        }
    }

    $order_by = $field_list['product_name'][1];
    if (isset($field_list[$posted['order_by']])) {
        $order_by = $field_list[$posted['order_by']][1];
    }

    //Do the search
    $list_sql = "SELECT s.id " . $select_from;
    if (count($whereAND) > 0) {
        $list_sql .= " WHERE " . implode(' AND ', $whereAND);
    }

    if ('' != $order_by) {
        $list_sql .= " ORDER BY " . $order_by;
        if ('desc' == $posted['desc']) {
            $list_sql .= " DESC";
        }

    }
    $query = $DB->query($list_sql);

    //Build the list
    $data1 = array();
    while ($row = $DB->fetch_array($query)) {
        $data1[] = $row;
    }

    $safe_posted = array_merge($_POST, $_GET);

    foreach ($safe_posted as $key => $value) {
        if (is_array($value)) {
            unset($safe_posted[$key]);
        }
    }

    //Cut the list to current page
    $total_count = count($data1);
    $data1       = array_chunk($data1, 50);
    $total_pages = count($data1);
    $data1       = $data1[intval($posted['page'])];
    $total_shown = count($data1);

    $view_url = '/products/stocklist/plush/[product_name]_[id].html';
    $edit_url = '?action=edit&id=';

    $get_vals           = $safe_posted;
    $get_vals['action'] = 'delete';
    $delete_url         = 'stocklist_delete.php?' . http_build_query($get_vals) . '&id=';

    $get_vals           = $safe_posted;
    $get_vals['action'] = 'list';
    unset($get_vals['page']);
    $page_url = '?' . http_build_query($get_vals) . '&page=';
    unset($get_vals['order_by']);
    $get_vals['desc'] = ('desc' == $get_vals['desc']) ? 'asc' : 'desc';
    $order_url        = '?' . http_build_query($get_vals) . '&order_by=';

    page_links($posted['page'], 50, $total_count, $page_url);
?>
<table width="95%" align="center">
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
	<form accept-charset="utf-8" action="" method="post">
	<tr><td colspan="<?=count($field_list) + 1?>" style="text-align:right;">
		<input type="hidden" name="action" value="update_qty" />
		<input type="submit" value="Update Quantities" />
	</td></tr>
<?php
}
?>
	<tr bgcolor="#000066">
<?php
foreach ($field_list as $key => $field) {
?>
		<th style="white-space:nowrap;"<?=($key == $posted['order_by'] || $order_by == $field[1]) ? ' bgcolor="#336699"' : ''?>>
<?php
if (isset($field[1])) {
            echo '<a class="td_options" href="' . $order_url . $key . '">';
        }

        echo $field[0];
        if (isset($field[1])) {
            echo '</a>';
        }

        if ($key == $posted['order_by'] || $order_by == $field[1]) {
            echo '<img src="/webadmin/products/' . ('desc' == $posted['desc'] ? 'desc' : 'asc') . '.gif" />';
        }

?>
		</th>
<?php
}
?>
		<th>&nbsp;</th>
	</tr>
<?php
$alt = true;
    foreach ($data1 as $row) {
        $sql           = "SELECT ";
        $select_fields = array();
        foreach ($field_list as $key => $field) {
            if (isset($field[1])) {
                $select_fields[] = $field[1] . ' AS ' . $key;
            }
        }

        $sql .= implode(', ', $select_fields);
        $sql .= $select_from;
        $sql .= " WHERE s.id='" . $row['id'] . "'";
        $item = $DB->query_first($sql);

        //Stupid hack for slashes (Some idiot doesn't know how to strip them before saving to database)
        $item['product_name'] = stripslashes($item['product_name']);
?>
	<tr<?=($alt = !$alt) ? '' : ' bgcolor="#EEEEEE"'?> height="32px">
<?php
foreach ($field_list as $key => $field) {
?>
		<td style="<?=isset($field[2]) ? ('text-align:' . $field[2] . ';') : ''?><?=('product_name' != $key) ? 'white-space:nowrap;width:1px;' : ''?>">
<?php
switch ($key) {
                case 'unit_price':
                    echo sprintf('%.2f', $item['unit_price']) . ' USD';
                    break;
                case 'product_name':
                    $url = $view_url;
                    foreach ($item as $find => $replace) {
                        $url = str_replace('[' . $find . ']', urlencode($replace), $url);
                    }

?>
			<a href="<?=$url?>" target="_blank"><?=str_replace('<', '&lt;', $item[$key])?></a>
<?php
break;
                case 'sample_qty':
                case 'stock_qty':
                case 'order_qty':
                    if (has_permission('STOCK', $allowed_sections)) {
?>
			<input type="text" style="width:32px;text-align:right;" name="<?=$key?>[<?=$item['id']?>]" value="<?=$item[$key]?>" onchange="javascript:document.getElementsByName('check_qty[<?=$item['id']?>]')[0].checked=true;" />
<?php
} else {
                        echo $item[$key];
                    }

                    break;
                default:
                    echo $item[$key];
            }
?>
		</td>
<?php
}
?>
		<td style="text-align:center;white-space:nowrap;">
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
			<input type="checkbox" name="check_qty[<?=$item['id']?>]" style="width:auto;height:auto;" /> |
<?php
}
?>
			<a href="<?=$edit_url . $item['id']?>">Edit</a> |
			<a href="<?=$delete_url . $item['id']?>">Delete</a>
		</td>
	</tr>
<?php
}
    if (has_permission('STOCK', $allowed_sections)) {
?>
	</form>
<?php
}
?>
</table>
<?php
page_links($posted['page'], 50, $total_count, $page_url);
}

function page_links($current_page, $items_per_page, $total_items, $page_url)
{
    $total_pages = ceil($total_items / $items_per_page);

    $first_item = $current_page * $items_per_page + 1;
    $last_item  = min($total_items, $first_item + $items_per_page - 1);

    $prev_page  = 0;
    $next_page  = $total_pages - 1;
    $first_page = max(0, $current_page - 2);
    $last_page  = min($first_page + 4, $total_pages - 1);
    $first_page = max(0, $last_page - 4);

    $links_array = array();
    if ($current_page > $prev_page) {
        $links_array[] = '<a href="' . $page_url . $prev_page . '">&lt;&lt;</a>';
    }

    for ($i = $first_page; $i <= $last_page; $i++) {
        if ($i == $current_page) {
            $links_array[] = ($i * $items_per_page + 1) . ' - ' . min($total_items, ($i + 1) * $items_per_page);
        } else {
            $links_array[] = '<a href="' . $page_url . $i . '">' . ($i * $items_per_page + 1) . ' - ' . min($total_items, ($i + 1) * $items_per_page) . '</a>';
        }

    }
    if ($current_page < $next_page) {
        $links_array[] = '<a href="' . $page_url . $next_page . '">&gt;&gt;</a>';
    }

?>
<p align="center">
	<b>Showing : <?=$first_item?> - <?=$last_item?> of <?=$total_items?> |</b>
	<?=implode(' | ', $links_array)?>
</p>
<?php
}

//-----------------------------------------------------------------------------

function stocklistCategories()
{
    global $DB;

    $viewby_name   = array('company' => 'Company', 'catagory' => 'Category', 'type' => 'Item Types', 'supplier' => 'Suppliers', 'location' => 'Location');
    $complete_list = array();

    $sql = " SELECT scs.name, scs.id, COUNT(s.id) AS total" .
        " FROM stock s" .
        " LEFT JOIN stock_company_stock scs ON s.company_stock=scs.id" .
        " GROUP BY s.company_stock" .
        " ORDER BY scs.name";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_bycompany', 'List by Company');
    viewby_links('?company=', $list_items);
    collapseGroupEnd();
    $complete_list['company'] = $list_items;

    $sql = " SELECT spc.name, spc.id, COUNT(s.id) AS total" .
        " FROM stock s" .
        " LEFT JOIN stock_product_catagories spc ON s.product_catagory=spc.id" .
        " GROUP BY s.product_catagory" .
        " ORDER BY spc.name";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_bycategory', 'List by Category');
    viewby_links('?catagory=', $list_items);
    collapseGroupEnd();
    $complete_list['catagory'] = $list_items;

    $sql = " SELECT pt.name, pt.id, COUNT(s.id) AS total" .
        " FROM stock s" .
        " LEFT JOIN product_types pt ON s.product_type=pt.id" .
        " GROUP BY s.product_type" .
        " ORDER BY pt.name";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_bytype', 'List by Item Types');
    viewby_links('?type=', $list_items);
    collapseGroupEnd();
    $complete_list['type'] = $list_items;

    $sql = " SELECT su.id, su.supplier AS name, COUNT(s.id) AS total" .
        " FROM stock s" .
        " LEFT JOIN suppliers su ON s.supplier=su.id" .
        " GROUP BY s.supplier" .
        " ORDER BY su.supplier";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_bysupplier', 'List by Suppliers');
    viewby_links('?supplier=', $list_items);
    collapseGroupEnd();
    $complete_list['supplier'] = $list_items;

    $sql = " SELECT psl.location AS name, psl.id, COUNT(s.id) AS total" .
        " FROM stock s" .
        " LEFT JOIN product_storage_location psl ON s.storage=psl.id" .
        " GROUP BY s.storage" .
        " ORDER BY psl.location";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_bylocation', 'List by Location');
    viewby_links('?location=', $list_items);
    collapseGroupEnd();
    $complete_list['location'] = $list_items;

    $sql = " SELECT" .
        "	pt.name," .
        "	SUM(s.unit_price * (s.stock_qty + s.sample_qty + s.order_qty) * c.usfrom) AS total" .
        " FROM stock s" .
        " LEFT JOIN product_types pt ON s.product_type=pt.id" .
        " INNER JOIN `highwaygroup`.`currency` AS `c` ON `s`.`cost_currency` = `c`.`code`" .
        " GROUP BY s.product_type" .
        " ORDER BY pt.name";
    $list_items = array();
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query)) {
        $list_items[] = $row;
    }

    collapseGroupStart('div_byvalue', 'Stock Values');
    viewby_total('', $list_items);
    collapseGroupEnd();

    collapseGroupStart('div_printlist', 'View/Print List');
    echo '<table width="95%" align="center"><tr><td><a href="/webadmin/products/list_stock_details.php?parent=496" target="_blank">View All</a>' . "</td></tr></table>\n";
    foreach ($complete_list as $key => $list) {
        echo '<table width="95%" align="center"><tr><td><i>View by ' . $viewby_name[$key] . "</i></td></tr></table>\n";
        viewby_links('/webadmin/products/list_stock_details.php?parent=496&' . $key . '=', $list, true);
    }
    collapseGroupEnd();
}

function viewby_total($view_url, $list_items)
{
?>
<table width="0" align="center">
<?php
$total = 0;
    $alt   = true;
    foreach ($list_items as $col) {
        $total += $col['total'];
?>
	<tr<?=($alt = !$alt) ? '' : ' style="background-color:#EEEEEE;"'?>>
		<td style="font-weight:bold;white-space:nowrap;">
<?php
if ('' != $col['id']) {
            echo '<a href="' . $view_url . $col['id'] . '">';
        }

        if ('' != $col['name']) {
            echo $col['name'];
        } else {
            echo 'Other';
        }

        if ('' != $col['id']) {
            echo '</a>';
        }

?>
		</td>
		<td style="text-align:right;white-space:nowrap;"><?=sprintf('%.2f', $col['total'])?></td>
		<td>&nbsp;</td>
	</tr>
<?php
}
?>
	<tr>
		<td style="font-weight:bold;text-align:right;">Total :</td>
		<td style="text-align:right;"><?=sprintf('%.2f', $total)?></td>
		<td>USD</td>
	</tr>
</table>
<?php
}

function viewby_links($view_url, $list_items, $new_window = false)
{
    $list_items = array_chunk($list_items, 4);
?>
<table width="95%" align="center">
<?php
foreach ($list_items as $row) {
        echo "	<tr>\n";
        foreach ($row as $col) {
            echo '		<td width="25%">';
            if ('' != $col['id']) {
                echo '<a href="' . $view_url . $col['id'] . '"' . ($new_window ? ' target="_blank"' : '') . '>';
            }

            if ('' != $col['name']) {
                echo $col['name'];
            } else {
                echo 'Other';
            }

            echo ' (' . $col['total'] . ')';
            if ('' != $col['id']) {
                echo '</a>';
            }

            echo "</td>\n";
        }
        echo "	</tr>\n";
    }
?>
</table>
<?php
}

function collapseGroupStart($sectionID, $sectionHeader)
{
?>
<a href="javascript:void(0);" onclick="javascript:expandIt('<?=$sectionID?>');"><img name="imEx" src="/webadmin/products/triDown.gif" border="0" title="Expand/Collapse All" /> <b><?=$sectionHeader?></b></a>
<br />
<div id="<?=$sectionID?>Child" class="child">
<?php
}
function collapseGroupEnd()
{
?>
</div>
<br />
<?php
}

function stocklistAddItem()
{
    global $DB;
    $query    = $DB->query("INSERT INTO stock(date_added) VALUES (NOW())");
    $insertid = $DB->insert_id();
    return $insertid;
}

function stocklistEditItem()
{
    global $DB, $posted;

    $data1 = $posted['data1'];
    $data2 = $posted['data2'];
    $data3 = $posted['data3'];
    $data4 = $posted['data4'];

/*    foreach($posted['data3_colour'] as $key=>$value)
$data3[]=array('colour'=>$value,'quantity'=>$posted['data3_quantity'][$key]);
foreach($posted['data4_size'] as $key=>$value)
$data4[]=array('size'=>$value,'qty'=>$posted['data4_qty'][$key]);
 */
    $data1['last_user']     = get_user();
    $stock_id               = intval($data1['id']);
    $data1['qty']           = $data1['sample_qty'] + $data1['stock_qty'] + $data1['order_qty'];
    $data1['date_received'] = date('Y-m-d', strtotime($data1['date_received']));

    $old_stock = $DB->query_first("SELECT * FROM stock WHERE id=" . $stock_id);

    $keys1 = array('coe_id', 'company_stock', 'product_name', 'product_catagory', 'product_type', 'date_received', 'condition', 'weight', 'total_weight', 'supplier', 'unit_price', 'qty', 'samplestock', 'date_added', 'last_modified', 'storage', 'country_location', 'cost_currency', 'active', 'approx_cost_price', 'approx_cost_currency', 'product_details', 'stock_cod', 'purchase_no', 'floor_location', 'last_modification', 'last_user', 'customer_name', 'invoice_ref', 'sample_qty', 'stock_qty', 'order_qty');

    $update1 = array();
    foreach ($keys1 as $key) {
        if (isset($data1[$key]) && $old_stock[$key] != $data1[$key]) {
            $update1[] = "`" . $key . "`='" . addslashes(trim($data1[$key])) . "'";
        }
    }

    if (count($update1) > 0) {
        $query = "UPDATE stock SET last_modified=NOW(), last_modification='" . addslashes(implode(', ', $update1)) . "', " . implode(', ', $update1) . " WHERE id=" . $stock_id . " LIMIT 1";
        $DB->query($query);
    }

    if (trim($data2['admin_note']) != '') {
        $sql = "INSERT INTO stock_admin_notes(stock_id,user,date_added,note) VALUES (" . $stock_id . ",'" . addslashes($data1['last_user']) . "',NOW(),'" . addslashes($data2['admin_note']) . "')";
        $DB->query($sql);
    }

    //add the colour quantities
    $DB->query("DELETE FROM stock_colour_qty WHERE stock_id=" . $stock_id);
    if (is_array($data3) && count($data3) > 0) {
        foreach ($data3['colour'] as $key => $colour) {
            $statement = "INSERT INTO stock_colour_qty (stock_id, colour, quantity) VALUES (" . $stock_id . ",'" . addslashes($colour) . "','" . addslashes($data3['quantity'][$key]) . "')";
            $DB->query($statement);
        }
    }

    //add the dimension quantities
    $DB->query("DELETE FROM stock_dimensions_qty WHERE stock_id=" . $stock_id);
    if (is_array($data4) && count($data4) > 0) {
        foreach ($data4['dimensions'] as $key => $dimensions) {
            $statement = "INSERT INTO stock_dimensions_qty (stock_id, dimensions, qty) VALUES (" . $stock_id . ",'" . addslashes($dimensions) . "','" . addslashes($data4['qty'][$key]) . "')";
            $DB->query($statement);
        }
    }

    //need to check the suppliers, we remembered the old supplier ID from the
    //edit form, when the supplier ID changes, we update the supplier_product record
    $supplier_id     = intval($data2['supplier']);
    $old_supplier_id = intval($data2['old_supplier']);

    if ($old_supplier_id != $supplier_id) {
//        $DB->query("DELETE FROM supplier_product WHERE product_id = '$id' AND supplier_id = '$old_supplier_id'");
        if ($supplier_id > 0) {
            $DB->query("INSERT IGNORE INTO supplier_product (supplier_id, product_id) VALUES ('$supplier_id', '$id')");
        }

    }

/*
//need to see if the unit price differs from that of the product in the product section
//then update it (I've been told to do this)
//Don't blame me should the building collapse, I was over here in this clearing...
//Chopping trees or something
$coe_id=intval($_POST['data3']['coe_id']);
$sql="SELECT p.weight, sp.supplier_id, p.cost_price, p.cost_currency FROM products p LEFT JOIN supplier_product sp ON sp.product_id=p.id WHERE p.id=".$coe_id;
$old_prod=$DB->query_first($sql);
if($old_prod)
{
$new_weight=floatval($_POST['data3']['weight']);
$new_supplier=intval($_POST['data3']['supplier']);
$new_price=floatval($_POST['data3']['unit_price']);
$new_currency=$_POST['data3']['cost_currency'];
if($old_prod['weight']!=$new_weight || $old_prod['supplier_id']!=$new_supplier || $old_prod['cost_price']!=$new_price || $old_prod['cost_currency']!=$new_currency)
{
$sql=    " UPDATE products".
" SET".
"    weight=".$new_weight.",".
"    cost_price=".$new_price.",".
"    cost_currency='".$new_currency."'".
" WHERE id = '".$coe_id."'";
$DB->query($sql);
if($new_supplier>0)
$DB->query("INSERT IGNORE INTO supplier_product(product_id,supplier_id) VALUES(".$coe_id.",".$new_supplier.")");
//For some reason the query isn't logging
$DB->query("INSERT INTO query_log(log_date,user_id,query) VALUES(NOW(),'".$user."',\"".$sql."\")");
}
}
 */
}
