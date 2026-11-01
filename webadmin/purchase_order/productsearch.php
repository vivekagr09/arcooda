<?php
include_once __DIR__ . '/common.inc.php';

$myurl = "productsearch.php";

switch ($_REQUEST['action']) {
    case 'select':
        showSelectProduct();
        break;
    case 'select_supplier':
        showSelectSupplier();
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

function searchProductSuppliers($supplier_product_id, $detailed = false)
{
    global $DB;

    $result = array();
    $sql    = " SELECT";
    if ($detailed) {
        $sql .= "   sp.*,   s.*,";
    }

    $sql .= "   sp.supplier_id," .
        "   p.cost_price as buy_price," .
        "   p.cost_currency as currency," .
        "   CONCAT(s.name, ' ' , s.surname) as supplier_name," .
        "   '' as supplier_name_ch," .
        "   s.country as location" .
        " FROM supplier_product sp" .
        " INNER JOIN new_suppliers s ON s.supplier_id=sp.supplier_id" .
        " INNER JOIN products p ON p.id=sp.product_id" .
        " WHERE sp.id=" . $supplier_product_id . " limit 1";
    $q = $DB->query($sql);
    while ($row = $DB->fetch_array($q, MYSQLI_ASSOC)) {
        $result[] = $row;
    }
    return $result;
}

function showSearchProduct()
{
    global $DB, $myurl;
    $itemsperpage   = 50;
    $posted         = undo_magicquotes(array_merge($_GET, $_POST));
    $page           = $posted['page'];
    $return_message = $posted['return_message'];
    $prod_type      = $posted['type'];
    $currency       = $posted['currency'];
    $search_id      = $posted['search_id'];
    $search_for     = $posted['for'];
    $sort_by        = $posted['sort_by'];
    $sort_ord       = $posted['sort_ord'];
    if ('' == $sort_by) {
        $sort_by = 'p.product_name';
    }

    $base_types = array();
    $sql        = "SELECT pt.id, pt.name FROM product_types pt WHERE pt.parent=0";
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $base_types[$row['id']] = $row['name'];
    }

    $sql   = array();
    $sql[] = "SELECT SQL_CALC_FOUND_ROWS";
    $sql[] = "	p.product_name," .
        "	p.*," .
        "	p.id as product_id," .
        "	IF(p.price_flux='y',(p.markup+100.0)*p.cost_price/100.0,IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)) AS offer_price," .
        "	IF(p.price_flux='y',p.cost_currency,IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_currency`, `p`.`sales_currency`)) AS offer_currency," .
        "	IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) As cost_price," .
        "	IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_currency`, `p`.`cost_currency`) As cost_currency," .
        "	pt.name as product_subtype," .
        "	pt.parent as product_roottype";
    $sql[] = "FROM products p";
    $sql[] = "INNER JOIN product_types pt ON p.type=pt.id";

    $where = array();
    if ('' != $search_for) {
        $where[] = "p.product_name LIKE " . formatSQLString('%' . $search_for . '%');
    }

//    if($search_for!='')    $where[]=    "(p.id LIKE '%".implode("%' OR p.id LIKE '%",explode(' ',$search_for))."%' OR p.product_name LIKE '%".implode("%' OR p.product_name LIKE '%",explode(' ',$search_for))."%')";
    if ('' != $search_id) {
        $where[] = "p.id=" . intval($search_id);
    }

    if ($prod_type > 0) {
        $where[] = "pt.parent='{$prod_type}'";
    }

    if (count($where) > 0) {
        $sql[] = "WHERE " . implode(' AND ', $where);
    }

    if ('' != $sort_by) {
        $sql[] = "ORDER BY {$sort_by}";
    }
    //.($sort_ord!='')?' DESC':'';
    $sql[] = "LIMIT " . $page * $itemsperpage . ',' . $itemsperpage;

    $product_list = array();
    $query        = $DB->query(implode(' ', $sql));
    $shown_rows   = mysqli_num_rows($query);
    $total_rows   = $DB->query_first("SELECT FOUND_ROWS()");
    $total_rows   = $total_rows[0];
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $row['product_online'] = convertCurrency($row['offer_price'], $row['offer_currency'], $currency);
        $row['product_cost']   = convertCurrency($row['cost_price'], $row['cost_currency'], $currency);
        $product_list[]        = $row;
    }

    $total_pages = floor($total_rows / $itemsperpage) + 1;

    $the_links               = array();
    $the_links['start_from'] = $page * $itemsperpage + 1;
    $the_links['ends_at']    = min($total_rows, ($page + 1) * $itemsperpage);
    $the_links['max']        = $total_rows;
    $the_links['links']      = array();
    $max_page                = ceil($total_rows / $itemsperpage) - 1;
    $min_page                = 0;
    for ($i = $min_page; $i <= $max_page; $i++) {
        $a_link          = array();
        $a_link['start'] = $i * $itemsperpage + 1;
        $a_link['end']   = min($a_link['start'] + $itemsperpage - 1, $total_rows);
        if ($page != $i) {
            $a_link['url'] = $myurl . '?page=' . $i . '&return_message=' . $return_message . '&type=' . $prod_type . '&currency=' . $currency . '&for=' . $search_for . '&sort_by=' . $sort_by . '&sort_ord=' . $sort_ord;
        }

        if (0 == $i || $i == $max_page || $i == $page || $i == $page - 1 || $i == $page + 1 || ($i > $page && $i < 4)) {
            $the_links['links'][$i] = $a_link;
        }

    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link type="text/css" rel="stylesheet" href="/webadmin/admin.css" />
    	<link type="text/css" rel="stylesheet" href="style.css" />
	    <script type="text/javascript">
//<![CDATA[
function getElement(name) {
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}

function setValue(name,value) {
	var elem=getElement(name);
	if(elem==null) return;
	elem.value=value;
}
//]]>
    	</script>
    </head>
    <body style="text-align:center;">

<form accept-charset="utf-8" method="post" action="<?= $myurl ?>?page=<?= $page ?>&amp;return_message=<?= $return_message ?>&amp;type=<?= $prod_type ?>&amp;currency=<?= $currency ?>&amp;for=<?= urlencode($search_for) ?>&amp;search_id=<?= $search_id ?>&amp;sort_by=<?= $sort_by ?>&amp;sort_ord=<?= $sort_ord ?>">
<input type="hidden" name="product_id" value="0" />
<input type="hidden" name="action" value="select_supplier" />

<div class="invoice_content">
<table class="wide table_grid">
	<tr><th>ID</th><th>Product Name</th><th>Cost Price</th><th>Sales Price</th><th>&nbsp;</th></tr>
<?php
if (count($product_list) > 0) {
        foreach ($product_list as $item) {
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?> style="font-size:12px;">
		<td style="text-align:center"><?= $item['product_id'] ?></td>
		<td style="width:70%;text-align:center;"><a target="_blank" href="/products/<?= urlencode(str_replace(' ', '_', stripslashes($base_types[$item['product_roottype']]))) ?>/<?= urlencode(str_replace(' ', '_', stripslashes($item['product_subtype']))) ?>/<?= urlencode(str_replace(' ', '_', stripslashes($item['product_name']))) ?>_<?= $item['product_id'] ?>.html?preview=true"><?= stripslashes($item['product_name']) ?></a></td>
		<td style="text-align:center"><?= $item['product_cost'] ?></td>
		<td style="text-align:center"><?= $item['product_online'] ?></td>
		<td style="text-align:center"><input type="button" value="Select" onclick="setValue('product_id','<?= $item['product_id'] ?>');form.submit();" /></td>
	</tr>
<?php
}
    } else {
?>
	<tr><td colspan="5" style="text-align:center;font-size:12px;"><br />No products found on this page<br /><br /></td></tr>
<?php
}
?>
	<tr><th colspan="5" style="text-align:center;"><?= show_links($the_links) ?></th></tr>
</table>
<br />
<table style="width:0px;background-color:transparent;border:0px none;" align="center">
<?php
if ($prod_type > 0) {
?>
	<tr style="background-color:transparent;font-size:12px;"><td style="white-space:nowrap;"><b><a style="color:blue;" target="_blank" href="/webadmin/products/admin.php?table=products&parent=<?= $prod_type ?>&action=add">Add New <?= str_replace('<', '&lt;', ucfirst($base_types[$prod_type])) ?></a></b></td></tr>
<?php
} else {
        foreach ($base_types as $key => $value) {
?>
	<tr style="background-color:transparent;font-size:12px;"><td style="white-space:nowrap;"><b><a style="color:blue;" target="_blank" href="/webadmin/products/admin.php?table=products&parent=<?= $key ?>&action=add">Add New <?= str_replace('<', '&lt;', ucfirst($value)) ?></a></b></td></tr>
<?php
}
    }
?>
</table>
</div>
</form>

    </body>
</html>
<?php
}
function showSelectSupplier(){
    global $DB, $myurl;
    $itemsperpage   = 50;
    $posted         = undo_magicquotes(array_merge($_GET, $_POST));
    $page           = $posted['page'];
    $return_message = $posted['return_message'];
    $prod_type      = $posted['type'];
    $currency       = $posted['currency'];
    $search_id      = $posted['product_id'];
    $search_for     = $posted['for'];
    $sort_by        = $posted['sort_by'];
    $sort_ord       = $posted['sort_ord'];
    if ('' == $sort_by) {
        $sort_by = 'p.product_name';
    }

    $base_types = array();
    $sql        = "SELECT pt.id, pt.name FROM product_types pt WHERE pt.parent=0";
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $base_types[$row['id']] = $row['name'];
    }

    $sql   = array();
    $sql[] = "SELECT SQL_CALC_FOUND_ROWS";
    $sql[] = "  p.product_name," .
        "   p.*,sp.id supplier_product_id,CONCAT(s.name, ' ' , s.surname) as supplier_name,s.company,sp.sup_name as supplier_product_name," .
        "   sp.sup_code as sup_code,sp.sup_currency as currency,p.id as product_id," .
        "   IF(p.price_flux='y',(p.markup+100.0)*p.cost_price/100.0,IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_price`, `p`.`sales_price`)) AS offer_price," .
        "   IF(p.price_flux='y',p.cost_currency,IF(`p`.`stock_type` = 'consignment', `p`.`consignment_sales_currency`, `p`.`sales_currency`)) AS offer_currency," .
        "   IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_price`, `p`.`cost_price`) As cost_price," .
        "  IF(`p`.`stock_type` = 'consignment', `p`.`consignment_cost_currency`, `p`.`cost_currency`) As cost_currency," .
        "   pt.name as product_subtype," .
        "   pt.parent as product_roottype";
    $sql[] = "FROM products p";
    $sql[] = "INNER JOIN product_types pt ON p.type=pt.id";
    $sql[] = "INNER JOIN supplier_product sp ON sp.product_id=p.id";
    $sql[] = "INNER JOIN new_suppliers s ON s.supplier_id=sp.supplier_id";
    $where = array();
    if ('' != $search_for) {
        $where[] = "p.product_name LIKE " . formatSQLString('%' . $search_for . '%');
    }

//    if($search_for!='')    $where[]=    "(p.id LIKE '%".implode("%' OR p.id LIKE '%",explode(' ',$search_for))."%' OR p.product_name LIKE '%".implode("%' OR p.product_name LIKE '%",explode(' ',$search_for))."%')";
    if ('' != $search_id) {
        //$search_id= substr($search_id, 7); 
        $where[] = "sp.product_id='".$search_id."'";
    }

    if ($prod_type > 0) {
        $where[] = "pt.parent='{$prod_type}'";
    }

    if (count($where) > 0) {
        $sql[] = "WHERE " . implode(' AND ', $where);
    }

    if ('' != $sort_by) {
        $sql[] = "ORDER BY {$sort_by}";
    }
    //.($sort_ord!='')?' DESC':'';
    $sql[] = "LIMIT " . $page * $itemsperpage . ',' . $itemsperpage;
    $product_list = array();
    $query        = $DB->query(implode(' ', $sql));
    $shown_rows   = mysqli_num_rows($query);
    $total_rows   = $DB->query_first("SELECT FOUND_ROWS()");
    $total_rows   = $total_rows[0];
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $row['product_online'] = convertCurrency($row['offer_price'], $row['offer_currency'], $currency);
        $row['product_cost']   = convertCurrency($row['cost_price'], $row['cost_currency'], $currency);
        $product_list[]        = $row;
    }

    $total_pages = floor($total_rows / $itemsperpage) + 1;

    $the_links               = array();
    $the_links['start_from'] = $page * $itemsperpage + 1;
    $the_links['ends_at']    = min($total_rows, ($page + 1) * $itemsperpage);
    $the_links['max']        = $total_rows;
    $the_links['links']      = array();
    $max_page                = ceil($total_rows / $itemsperpage) - 1;
    $min_page                = 0;
    for ($i = $min_page; $i <= $max_page; $i++) {
        $a_link          = array();
        $a_link['start'] = $i * $itemsperpage + 1;
        $a_link['end']   = min($a_link['start'] + $itemsperpage - 1, $total_rows);
        if ($page != $i) {
            $a_link['url'] = $myurl . '?page=' . $i . '&return_message=' . $return_message . '&type=' . $prod_type . '&currency=' . $currency . '&for=' . $search_for . '&sort_by=' . $sort_by . '&sort_ord=' . $sort_ord;
        }

        if (0 == $i || $i == $max_page || $i == $page || $i == $page - 1 || $i == $page + 1 || ($i > $page && $i < 4)) {
            $the_links['links'][$i] = $a_link;
        }

    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <link type="text/css" rel="stylesheet" href="/webadmin/admin.css" />
        <link type="text/css" rel="stylesheet" href="style.css" />
        <script type="text/javascript">
//<![CDATA[
function getElement(name) {
    var elem=document.getElementById(name);
    if(elem) return elem;
    elem=document.getElementsByName(name);
    if(elem.length==0) return null;
    return elem[0];
}

function setValue(name,value,supid=false) {
    var elem=getElement(name);
    if(supid){
        var sup=getElement('supplier_product_id');
        sup.value=supid;
    }
    
    if(elem==null) return;
    elem.value=value;
}
//]]>
        </script>
    </head>
    <body style="text-align:center;">

<form accept-charset="utf-8" method="post" action="<?= $myurl ?>?page=<?= $page ?>&amp;return_message=<?= $return_message ?>&amp;type=<?= $prod_type ?>&amp;currency=<?= $currency ?>&amp;for=<?= urlencode($search_for) ?>&amp;search_id=<?= $search_id ?>&amp;sort_by=<?= $sort_by ?>&amp;sort_ord=<?= $sort_ord ?>">
<input type="hidden" name="product_id" value="0" />
<input type="hidden" name="supplier_product_id" value="0" />
<input type="hidden" name="action" value="select" />

<div class="invoice_content">
<?php
if (count($product_list) > 0) {
    echo '<h3 style="text-align: left;padding-left: 3%;"> '.$product_list[0]["company"].'<h3>';
}
?>
<table class="wide table_grid">
    <tr><th>ID</th><th>Product Name</th><th>Supplier Product Name</th><th>Supplier  Code</th><th>Supplier  Name</th><th>Cost Price</th><th>Sales Price</th><th>Currency</th><th>&nbsp;</th></tr>
<?php
if (count($product_list) > 0) {
        foreach ($product_list as $item) {
?>
    <tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?> style="font-size:12px;">
        <td style="text-align:center"><?= $item['product_id'] ?></td>
        <td style="width:40%;"><a target="_blank" href="/products/<?= urlencode(str_replace(' ', '_', stripslashes($base_types[$item['product_roottype']]))) ?>/<?= urlencode(str_replace(' ', '_', stripslashes($item['product_subtype']))) ?>/<?= urlencode(str_replace(' ', '_', stripslashes($item['product_name']))) ?>_<?= $item['product_id'] ?>.html?preview=true"><?= stripslashes($item['product_name']) ?></a></td>
        <td style="text-align:center;"><?= $item['sup_code'] ?></td>
        <td style="text-align:center;"><?= $item['supplier_product_name'] ?></td>
        <td style="text-align:center"><?= $item['supplier_name'] ?></td>
        <td style="text-align:center"><?= $item['product_cost'] ?></td>
        <td style="text-align:center"><?= $item['product_online'] ?></td>
        <td style="text-align:center"><?= $_GET['currency'] ?></td>
        <td style="text-align:center"><input type="button" value="Select" onclick="setValue('product_id','<?= $item['product_id'] ?>','<?= $item['supplier_product_id'] ?>');form.submit();" /></td>
    </tr>
<?php
}
    } else {
?>
    <tr><td colspan="9" style="text-align:center;font-size:12px;"><br />No Supplier found for this product<br /><br /></td></tr>
<?php
}
?>
    <tr><th colspan="9" style="text-align:center;"><?= show_links($the_links) ?></th></tr>
</table>


</div>
</form>

    </body>
</html>
<?php
}
function showSelectProduct()
{
    global $DB;
    $posted     = undo_magicquotes(array_merge($_GET, $_POST));
    $product_id = $posted['product_id'];
    $currency   = $posted['currency'];
    $supplier_product_id= $posted['supplier_product_id'];
    $product = $DB->query_first(
        " SELECT" .
        "   p.*," .
        "   pt.parent AS producttype_id" .
        " FROM products p" .
        " INNER JOIN product_types pt ON p.type=pt.id" .
        " WHERE p.id='{$product_id}'"
    );

    if ('y' == $product['price_flux']) {
        $product['sales_price']    = ($product['markup'] + 100.0) * $product['cost_price'] / 100.0;
        $product['sales_currency'] = $product['cost_currency'];
    }
    if($product['stock_type'] == 'consignment'){
        $product['cost_price'] = $product['consignment_sales_currency'];
    }
    $return_data['image'] = $product['image'];
    $return_data['image_url'] = '';
    $return_data['image_path'] = '';
    if ('' != $product['image'] && is_file($_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/image/' . $product['image'])) {
        $return_data['image_url'] = sprintf(
            '/images/%s-%d.jpg',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $product['product_name']), '-')),
            $product['id']
        );
        $return_data['image_path'] = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/image/' . $product['image'];
    }
    $return_data['producttype_id'] = $product['producttype_id'];

    $return_data['product_id']         = $product['id'];
    $return_data['product_name']       = stripslashes($product['product_name']);
    $return_data['product_qty']        = 1;
    $cost_price = 0.00;
    if($product['stock_type'] == 'consignment'){
        $product['cost_price'] = $product['consignment_cost_price'];
    }
    if($product['stock_type'] == 'consignment'){
        $product['cost_currency'] = $product['consignment_cost_currency'];
    }
    if($product['stock_type'] == 'consignment'){
        $product['sales_price'] = $product['consignment_sales_price'];
    }
    $return_data['product_cost']       = convertCurrency($product['cost_price'], $product['cost_currency'], $currency);
    $return_data['product_costus']     = convertCurrency($product['cost_price'], $product['cost_currency'], 'USD');
    $return_data['product_online']     = convertCurrency($product['sales_price'], $product['sales_currency'], $currency);
    $return_data['product_offerus']    = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD');
    $return_data['product_offer']      = $return_data['product_online'];
    $return_data['product_weight']     = convertWeight($product['weight']);
    $return_data['product_weight_inc'] = $product['weight_inc'];
    $return_data['product_notes']      = trim($product['notes']);
    $return_data['min_order']          = $product['min_order'];
    if ($product['min_order'] > 1) {
        $return_data['product_notes'] = trim($return_data['product_notes'] . "\n- unit price in $" . convertCurrency($product['sales_price'], $product['sales_currency'], $currency) . strtolower($currency) . ' for MOQ in ' . $product['min_order'] . ' pieces');
    } else {
        $return_data['min_order'] = 1;
    }

    $return_data['supplier_productid']   = $product['id'];
    $return_data['supplier_qty']         = 1;
    $return_data['supplier_cost']        = $return_data['product_cost'];
    $return_data['supplier_productname'] = $return_data['product_name'];

    $supplier_count = 0;

    $supplier_list  = searchProductSuppliers($supplier_product_id, true);
    $supplier_count = count($supplier_list);
    $supplier       = $supplier_list[0];

    if (1 == $supplier_count) {
        $return_data['supplier_id']             = $supplier['supplier_id'];
        $return_data['supplier_cost']           = convertCurrency($supplier['buy_price'], $supplier['currency'], $currency);
        $return_data['supplier_costus']         = convertCurrency($supplier['buy_price'], $supplier['currency'], 'USD');
        $return_data['supplier_name']           = $supplier['supplier_name'] . ' ' . $supplier['supplier_name_ch'];
        $return_data['supplier_country']        = $supplier['location'];
        $return_data['supplier_notes']          = '';
        $return_data['supplier_cost_orig']      = $supplier['buy_price'];
        $return_data['sup_code']      = $supplier['sup_code'];
        $return_data['product_name']      = $supplier['sup_name'];
        $return_data['supplier_cost_orig_curr'] = $supplier['currency'];
    }

    $return_data['supplier_count'] = $supplier_count;
    $return_data['return_message'] = $posted['return_message'];
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script type="text/javascript">
//<![CDATA[
var data = <?php jecho($return_data)?>;
if(window.opener.setProductData){
    window.close();
    window.opener.setProductData(data);
}else{
    alert('Unable to send product back to form');
    window.close();
}
//]]>
        </script>
    </head>
    <body>
    </body>
</html>
<?php
}
?>
?>
