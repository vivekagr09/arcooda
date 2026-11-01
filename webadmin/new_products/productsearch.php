<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/connection.inc.php';

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

switch ($_REQUEST['action']) {
    case 'select':
        showSelectProduct();
        break;
    default:
        showSearchProduct();
        break;
}
return;

function show_links($links)
{
    $result = '';
    if (!count($links)) {
        return;
    }

    $result .= '<b>Showing : ' . $links['start_from'] . ' - ' . $links['ends_at'] . ' of ' . $links['max'] . '</b>';
    if ($links['url_prev_page']) {
        $result .= ' | <a href="' . $links['url_prev_page'] . '">&lt;&lt;</a>';
    }

    foreach ($links['links'] as $link) {
        if ($link['url']) {
            $result .= ' | <a href="' . $link['url'] . '">' . $link['start'] . ' - ' . $link['end'] . '</a>';
        } else {
            $result .= ' | ' . $link['start'] . ' - ' . $link['end'];
        }

    }
    if ($links['url_next_page']) {
        $result .= ' | <a href="' . $links['url_next_page'] . '">&gt;&gt;</a> ';
    }

    return $result;
}

function showSearchProduct()
{
    global $DB;
    $itemsperpage = 50;

    $defaults = array('currency' => 'USD', 'sort_by' => 'name', 'page' => 0);

    $posted = array_merge($defaults, $_GET, $_POST);

    $return_message = $posted['return_message'];
    $currency       = $posted['currency'];
    $sort_by        = $posted['sort_by'];
    $sort_ord       = $posted['sort_ord'];

    $narrow_fields = array('search_id' => 'p.id', 'type' => 'pt.parent');
    $search_fields = array('p.id', 'p.product_name');
    $sort_fields   = array(
        'id'          => 'p.id',
        'name'        => 'p.product_name',
        'cost_price'  => 'c1.usfrom*p.cost_price',
        'sales_price' => "IF(p.price_flux='y',c1.usfrom*p.cost_price*(100+p.markup)/100,c2.usfrom*p.sales_price)",
    );

    //Build an sql query
    $list_sql = " SELECT" .
        "	p.id" .
        " FROM products p" .
        " INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency`" .
        " INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency`";
    $show_sql = " SELECT" .
        "	p.id," .
        "	p.product_name," .
        "	p.cost_price," .
        "	p.cost_currency," .
        "	ROUND(IF(p.price_flux='y',p.cost_price*(100+p.markup)/100,p.sales_price),2) AS sales_price," .
        "	IF(p.price_flux='y',p.cost_currency,p.sales_currency) AS sales_currency" .
        " FROM products p" .
        " INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency`" .
        " INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency`";

    //Build a where expression
    $where    = "";
    $whereAND = array();
    foreach ($narrow_fields as $key => $field) {
        if (trim($posted[$key]) != '') {
            $whereAND[] = $field . "='" . $posted[$key] . "'";
        }
    }

    foreach (explode(' ', $posted['for']) as $find) {
        if (trim($find) != '') {
            $whereOR = array();
            foreach ($search_fields as $field) {
                $whereOR[] = $field . " LIKE '%" . addslashes($find) . "%'";
            }

            if (count($whereOR) > 0) {
                $whereAND[] = '(' . implode(' OR ', $whereOR) . ')';
            }

        }
    }

    if (count($whereAND) > 0) {
        $where = " WHERE " . implode(' AND ', $whereAND);
    }

    //Build a sort expression
    $orderby = "";
    if (isset($sort_fields[$posted['sort_by']])) {
        $orderby = " ORDER BY " . $sort_fields[$posted['sort_by']] . " " . $posted['sort_ord'];
    }

    $product_list = array();
    $query        = $DB->query($list_sql . $where . $orderby);
    while ($row = mysqli_fetch_assoc($query)) {
        $product_list[] = $row['id'];
    }

    $current_page = intval($posted['page']);
    $total_items  = count($product_list);
    $product_list = array_chunk($product_list, $itemsperpage);
    $total_pages  = count($product_list);
    if (!isset($product_list[$current_page])) {
        $current_page = 0;
    }

    $product_list = $product_list[$current_page];
    $total_shown  = count($product_list);

    $get_vals = $posted;
    unset($get_vals['page']);
    $page_url = '?' . http_build_query($get_vals) . '&page=';

    unset($get_vals['sort_by']);
    unset($get_vals['sort_ord']);
    $sort_url = '?' . http_build_query($get_vals) . '&sort_by=';

    $get_vals = $posted;
    unset($get_vals['product_id']);
    $get_vals['action'] = 'select';
    $select_url         = '?' . http_build_query($get_vals) . '&product_id=';
?>
<html>
<head><link rel="stylesheet" href="/webadmin/admin.css" type="text/css" /></head>
<body style="text-align:center;">
<?php
page_links($current_page, $itemsperpage, $total_items, $page_url);

    $header_list = array('id' => 'ID', 'name' => 'Product Name', 'cost_price' => 'Cost Price', 'sales_price' => 'Sales Price');
?>
<table width="95%" align="center">
	<tr class="tableHeader" style="background-color:#000066;">
<?php
foreach ($header_list as $key => $value) {
        if ($sort_by == $key) {
?>
		<th style="white-space:nowrap;" bgcolor="#336699"><a class="td_options" href="<?=$sort_url . $key?>&sort_ord=<?=('DESC' == $sort_ord) ? 'ASC' : 'DESC'?>"><?=$value?> <img border="0" src="/webadmin/products/<?=('DESC' == $sort_ord) ? 'desc' : 'asc'?>.gif" /></a></th>
<?php
} else {
?>
		<th style="white-space:nowrap;"><a class="td_options" href="<?=$sort_url . $key?>"><?=$value?></a></th>
<?php
}
    }
?>
		<th>&nbsp;</th>
	</tr>
<?php
if ($total_items > 0) {
        foreach ($product_list as $product_id) {
            $row = $DB->query_first($show_sql . " WHERE p.id='" . $product_id . "' LIMIT 1");
?>
	<tr class="tableRow" style="background-color:#<?=($alt = !$alt) ? 'FFFFFF' : 'EEEEEE'?>;height:32px;">
		<td style="text-align:right;"><?=$product_id?></td>
		<td style="width:100%;"><?=str_replace('<', '&lt;', stripslashes($row['product_name']))?></td>
		<td style="white-space:nowrap;text-align:right;"><?=$row['cost_price']?> <?=$row['cost_currency']?></td>
		<td style="white-space:nowrap;text-align:right;"><?=$row['sales_price']?> <?=$row['sales_currency']?></td>
		<td style="white-space:nowrap;"><a href="<?=$select_url . $product_id?>">Select</a></td>
	</tr>
<?php
}
    } else {
?>
	<tr class="tableRow" style="height:32px;"><td colspan="5" style="text-align:center;">No products found</td></tr>
<?php
}
?>
</table>
<?php
page_links($current_page, $itemsperpage, $total_items, $page_url);
?>
</div>
</body>
</html>
<?php
}

function showSelectProduct()
{
    global $DB;

    $product_id = $_REQUEST['product_id'];
    $currency   = $_REQUEST['currency'];

    $sql = " SELECT" .
        "	p.id," .
        "	p.product_name," .
        "	p.type," .
        "	p.storage," .
        "	p.country_location," .
        "	p.`condition`," .
        "	p.weight," .
        "	p.preferred_supplier," .
        "	p.cost_price," .
        "	p.cost_currency," .
        "	p.qty," .
        "	p.active," .
        "	pt.parent AS producttype_id," .
        "	ROUND(IF(p.price_flux='y',p.cost_price*(100+p.markup)/100,p.sales_price),2) AS sales_price," .
        "	IF(p.price_flux='y',p.cost_currency,p.sales_currency) AS sales_currency," .
        "	ROUND(c1.usfrom*p.cost_price,2) AS cost_price_us," .
        "	ROUND(IF(p.price_flux='y',c1.usfrom*p.cost_price*(100+p.markup)/100,c2.usfrom*p.sales_price),2) AS sales_price_us," .
        "	ROUND(c3.usto*c1.usfrom*p.cost_price,2) AS cost_price_ask," .
        "	ROUND(c3.usto*IF(p.price_flux='y',c1.usfrom*p.cost_price*(100+p.markup)/100,c2.usfrom*p.sales_price),2) AS sales_price_ask" .
        " FROM products p" .
        " INNER JOIN product_types pt ON p.type=pt.id" .
        " INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency`" .
        " INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency`" .
        " LEFT JOIN `highwaygroup`.`currency` AS `c3` ON `c3`.`code` = '" . $currency . "'";
    $query   = $DB->query($sql . " WHERE p.id='" . $product_id . "'");
    $product = mysqli_fetch_assoc($query);

    $return_data                   = $product;
    $return_data['return_message'] = $posted['return_message'];
    $return_data['product_name']   = stripslashes($product['product_name']);
?>
<html><head></head><body>
<script language="javascript">
var data=Array(<?=count($return_data)?>);
<?php
foreach ($return_data as $key => $item) {
?>
data['<?=$key?>']='<?=addslashes($item)?>';
<?php
}
?>

if(window.opener.setProductData)
	window.opener.setProductData(data);
else
	alert('Unable to send product to parent form');
window.close();
</script>
</body></html>
<?php
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
?>