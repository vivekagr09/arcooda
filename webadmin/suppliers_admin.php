<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........: Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$search_fields[] = '`supplier`';
$search_fields[] = '`s`.`id`';

$gets = get_vars();

function search($querystring)
{
    global $DB, $at, $order, $by;
    global $search_fields;

    $extra = '';
    if (!isset($alpha)) {
        $whereAND = [];
        foreach (preg_split('/\\s+/', $querystring) as $v1) {
            if ('' !== $v1) {
                $whereOR = [];
                foreach ($search_fields as $v2) {
                    $whereOR[] = $v2 . ' LIKE ' . formatSQLLikeString($v1);
                }
                $whereAND[] = '(' . implode(' OR ', $whereOR) . ')';
            }
        }

        if (count($whereAND)) {
            $extra = ' AND ' . implode(' AND ', $whereAND);
        }
    }

    foreach ([
        'id',
        'supplier',
        'notes',
        'contact_person',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'phone',
        'fax',
        'website',
        'email',
        'date_added',
        'active',
        'modified',
        'site_id',
    ] as $k) {
        if (array_key_exists($k, $_GET)) {
            $words = $_GET[$k];
            if (!is_array($words)) {
                $words = preg_split('/\\s+/', $words);
            }
            foreach ($words as $v1) {
                if ('' !== $v1) {
                    $extra .= ' AND `' . $k . '` LIKE ' . formatSQLLikeString($v1);
                }
            }
        }
    }

    //search statement
    $statement = "SELECT `s`.*, COUNT(`sp`.`product_id`) AS `products` FROM `suppliers` AS `s` LEFT JOIN `supplier_product` AS `sp` ON `sp`.`supplier_id` = `s`.`id` WHERE 1 ";

    $group_by = ' GROUP BY `s`.`id` ';

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra . $group_by);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'asc';
    }

    if (!isset($by)) {
        $by = 'supplier';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    //echo $statement.$extra;
    $result = $DB->query($statement . $extra . $group_by . $order_by . $limit);

    $page_url = $_GET;
    unset($page_url['at']);
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);
    show_links(get_links($max, $page_url, 25, 4));

    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    //show more links
    show_links(get_links($max, $page_url, 25, 4));
}

function drawHeaderRow()
{
    global $order, $by, $max, $at, $page;

    $fields['id']       = 'center';
    $fields['supplier'] = 'left';
    $fields['products'] = 'right';
    reset($fields);

    $sort_url = $_GET;
    unset($sort_url['by']);
    unset($sort_url['max']);
    unset($sort_url['at']);
    unset($sort_url['page']);
    $sort_url['order'] = ('desc' == $order) ? 'asc' : 'desc';
    $sort_url['by']    = '';
    $sort_url          = '?' . http_build_query($sort_url);
?>
<br />
<table class="table_grid">
    <tr>
        <th><a href="<?php hecho($sort_url . 'id');?>">Supplier #<?=('id' == $by) ? ('<img src="' . $order . '.gif" border="0" />') : '';?></a></th>
        <th><a href="<?php hecho($sort_url . 'supplier');?>">Supplier Name<?=('supplier' == $by) ? ('<img src="' . $order . '.gif" border="0" />') : '';?></a></th>
        <th><a href="<?php hecho($sort_url . 'country');?>">Country<?=('country' == $by) ? ('<img src="' . $order . '.gif" border="0" />') : '';?></a></th>
        <th><a href="<?php hecho($sort_url . 'products');?>">Products<?=('products' == $by) ? ('<img src="' . $order . '.gif" border="0" />') : '';?></a></th>
        <th></th>
    </tr>
<?php
}

function drawHeaderEnd()
{
?>
</table>
<br />
<?php
}

function showResult($cols, $shaded)
{
?>
<tr<?=($shaded) ? ' class="alt"' : '';?>>
    <td style="text-align:center;"><?php hecho($cols['id']);?></td>
    <td style="text-align:left;"><?php hecho($cols['supplier']);?></td>
    <td style="text-align:center;"><i class="fi fi-<?php hecho(strtolower($cols['country']));?>"></i></td>
    <td style="text-align:right;"><?php hecho($cols['products']);?></td>
    <td style="white-space:nowrap;">
        <a href="?action=edit&id=<?=$cols['id'];?>">Edit</a> |
        <a href="?action=delete&id=<?=$cols['id'];?>">Delete</a> |
        <a href="suppliers_stock.php?action=view&id=<?=$cols['id'];?>&query=">Stocklist</a>
    </td>
</tr>
<?php
}

function doAdd()
{
    global $DB, $data;

    $statement = "INSERT INTO suppliers(date_added, " . get_fields($data) . ")
					VALUES(NOW(), " . get_values($data) . ")";
    $DB->query($statement);

    echo "New Supplier Added<br />";
}

function doEdit()
{
    global $DB, $data, $id;

    $statement = "UPDATE suppliers SET " . get_set_values($data) . " WHERE id = '$id'";
    $DB->query($statement);

    echo "ID: $id Updated<br />";
}

function doDelete()
{
    global $id, $DB;
    $DB->query(sprintf(
        "DELETE FROM `suppliers` WHERE `id` = %d LIMIT 1",
        $id
    ));
    echo "ID:$id Deleted.<br>";
}

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `suppliers`");

$active_count = [];
$result       = $DB->query("SELECT COUNT(*) AS `Rows`, `active` FROM `suppliers` GROUP BY `active`");
while ($row = $DB->fetch_assoc($result)) {
    $active_count[$row['active']] = $row['Rows'];
}
$DB->free_result($result);

?>
<!-- suppliers admin -->

<form action="<?=$gets;?>" method="post">
    <br />
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td style="background:#eee;width:20%;text-align:center"><b>Suppliers</b></td>
            <td style="background:#eee;width:80%;text-align:right">
                <a href="?action=add">Add New Supplier</a> |
                <a href="?query=&amp;active=y">List Active Suppliers (<?=$active_count['y'];?>)</a>
                <a href="?query=">List All Suppliers (<?=array_sum($active_count);?>)</a>
            </td>
        </tr>
    </table>
    <!-- search box -->
    <p style="text-align:right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($_REQUEST["query"]));?>" />
        <input type="submit" name="search" value="Search" />
    </p>
    <!-- //search box -->
<?php

if (trim($_REQUEST["query"]) == '') {

    switch ($action) {
        case "do_add":
            doAdd();
            break;
        case "do_edit":
            doEdit();
            break;
        case "do_delete":
            doDelete();
            break;
        case "add":
            include __DIR__ . '/suppliers_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/suppliers_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/generic_delete_form.inc.php';
            break;

    }
}

if (array_key_exists("query", $_REQUEST)) {
    search($_REQUEST["query"]);
}

?>
</form>

<!--//suppliers admin -->
<?php
include_once __DIR__ . '/footer.inc.php';
