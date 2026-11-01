<?php
include_once __DIR__ . "/common.inc.php";

$list_currency = $posted['currency'];
if ('' == $list_currency) {
    $list_currency = $current_staff['currency'];
}

if ("" == $list_currency) {
    $list_currency = "USD";
}

$list = true;
include_once "header.inc.php";

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = (isset($posted['order_by'])) ? $posted['order_by'] : 'date_added';
$criteria = $_GLOBALS['criteria_options']['products'];
unset($criteria['active']);
unset($criteria['active_id']);
foreach (productGetOptionList('all_fields') as $k => $v) {
    if ('' != $posted[$k]) {
        $criteria[$k] = explode('|', $posted[$k]);
    } elseif (array_key_exists($key, $posted)) {
        $criteria[$key] = $posted[$key];
    }
}

unset($criteria['id']);
if ('' != $posted['category_id']) {
    $criteria['category_id'] = explode('|', $posted['category_id']);
}

if ('' != $posted['supplier_id']) {
    $criteria['supplier_id'] = explode('|', $posted['supplier_id']);
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['subset_list']) {
    $criteria['product_str'] = explode('|', $posted['subset_list']);
}
if ('' == $posted['return_message']) {
    $sub_criteria = $criteria;
    unset($sub_criteria['type']);
    $product_list = productSearch($sub_criteria);
    if (count($product_list) > 0) {
        $list_by1 = 'type';
        $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByType');return false"><img name="imEx" src="/webadmin/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Item Types</b></a>
	<br /><br />
	<div id="sectionViewByType" class="child" style="display:none;">
<?php
include 'templates/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

    $sub_criteria = $criteria;
    unset($sub_criteria['preferred_supplier']);
    $product_list = productSearch($sub_criteria);
    if (count($product_list) > 0) {
        $list_by1 = 'preferred_supplier';
        $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewBySupplier');return false"><img name="imEx" src="/webadmin/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Suppliers</b></a>
	<br /><br />
	<div id="sectionViewBySupplier" class="child" style="display:none;">
<?php
include 'templates/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

    $sub_criteria = $criteria;
    unset($sub_criteria['storage']);
    $product_list = productSearch($sub_criteria);
    if (count($product_list) > 0) {
        $list_by1 = 'storage';
        $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByStorage');return false"><img name="imEx" src="/webadmin/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Location</b></a>
	<br /><br />
	<div id="sectionViewByStorage" class="child" style="display:none;">
<?php
include 'templates/navigate.tpl.php';
?>
	</div>
</div>
<?php
}
}
$search_for = $posted['search_for'];
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionSearch'))"><img name="imEx" src="/webadmin/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?php echo (isset($posted['search_for'])) ? '' : ' style="display:none;"' ?>>
<?php
include 'templates/search.tpl.php';
?>
	</div>
</div>
<?php

/*
$storage_list = productGetOptionList('storage');
$stock_list = productGetOptionList('stock');
if ($posted['storage'] != '') echo '<p><b>This Page shows all the products in the Location: ' . htmlspecialchars($storage_list[$posted['storage']]) . '</b></p>';
if ($posted['stock'] != '') echo '<p><b>This Page shows all the products of Stock Type :' . htmlspecialchars($stock_list[$posted['stock']]) . '</b></p>';
//if ($posted['category_id'] != '') echo '<p><b>This page shows all the products in the Category: ' . $full_category_list[$posted['category_id']] . '</b></p>';
 */

if ('m' == $posted['product_type']) {
    unset($criteria['site_id']);
}

$site_list = array(
    39 => 'ARC - Arcooda',
);
if (isset($posted['site_id']) && isset($site_list[$posted['site_id']])) {
    $site_list = array($posted['site_id'] => $site_list[$posted['site_id']]);
}

$text_criteria = array("Showing");

$temp = 0;
if (is_array($criteria["active"])) {
    $temp = in_array("y", $criteria["active"]) - in_array("n", $criteria["active"]);
} elseif ("y" == $criteria["active"]) {
    $temp = 1;
} elseif ("n" == $criteria["active"]) {
    $temp = -1;
}

if (0 == $temp) {
    $text_criteria[] = "all";
} elseif (1 == $temp) {
    $text_criteria[] = "active";
} elseif (-1 == $temp) {
    $text_criteria[] = "inactive";
}

$temp = 0;
if (is_array($criteria["product_type"])) {
    $temp = in_array("m", $criteria["product_type"]) - in_array("ind", $criteria["product_type"]);
} elseif ("m" == $criteria["product_type"]) {
    $temp = 1;
} elseif ("ind" == $criteria["product_type"]) {
    $temp = -1;
}

if (1 == $temp) {
    $text_criteria[] = "master";
} elseif (-1 == $temp) {
    $text_criteria[] = "individual";
}

$section_list = productGetOptionList("section");
$temp         = array();
if (is_array($criteria["section"])) {
    foreach ($criteria["section"] as $v) {
        $temp[] = $section_list[$v];
    }
} elseif (0 != $criteria["section"]) {
    $temp[] = $section_list[$criteria["section"]];
}

if (count($temp) > 0 && count($temp) < count($section_list)) {
    $text_criteria[] = strtolower(implode("+", $temp));
} else {
    $text_criteria[] = "products";
}

$also = array();

$temp = 0;
if (is_array($criteria["on_special"])) {
    $temp = in_array("y", $criteria["on_special"]) - in_array("n", $criteria["on_special"]);
} elseif ("y" == $criteria["on_special"]) {
    $temp = 1;
} elseif ("n" == $criteria["on_special"]) {
    $temp = -1;
}

if (1 == $temp) {
    $also[] = "on special";
} elseif (-1 == $temp) {
    $also[] = "not on special";
}

if (count($also)) {
    $text_criteria[] = "that are " . implode(", ", $also);
}

$text_criteria = implode(" ", $text_criteria);

if (is_array($criteria["any_text"])) {
    $text_criteria .= ', containing text: "' . implode('", "', $criteria["any_text"]) . '"';
} elseif ("" != $criteria["any_text"]) {
    $text_criteria .= ', containing text: "' . $criteria["any_text"] . '"';
}

?>
<p style="text-align:center;"><?php hecho($text_criteria)?></p>
<?php

$select_url = $get_vars;
unset($select_url['action']);
$select_url = 'products_select.php?' . http_build_query($select_url);
?>
<form action="<?php echo htmlspecialchars($select_url) ?>" method="post">
<?php
foreach ($site_list as $site_id => $site_name) {
    $criteria['site_id'] = $site_id;
    $product_list        = productSearch($criteria);
    $product_list        = productSort($criteria, $order_by, false);
?>
<center><?php echo formatHTMLTitle($site_name) ?></center>
<?php
foreach (explode('|', $posted['hide_list']) as $k) {
        $key = array_search($k, $product_list);
        if (false !== $key) {
            unset($product_list[$key]);
        }

    }
    if ('desc' == $order) {
        $product_list = array_reverse($product_list);
    }

    $items_per_page = 50;
    if ($_GLOBALS['staff']['products_per_page'] > 0) {
        $items_per_page = $_GLOBALS['staff']['products_per_page'];
    }

    $count_total   = count($product_list);
    $product_list  = array_chunk($product_list, $items_per_page);
    $count_pages   = count($product_list);
    $page_id       = max(0, min($count_pages - 1, $page_id));
    $product_list  = $product_list[$page_id] ?? [];
    $count_display = count($product_list);
    if ($count_total > 0) {
        $ignore_ids = explode('|', $posted['ignore_list']);
        if ($linked_id) {
            $ignore_ids[] = $linked_id;
            // ie ignore that product + all the products that are related..
            $related = productRelatedFromDatabase($linked_id);
            foreach ($related as $tmp) {
                $ignore_ids[] = $tmp;
            }

            $ignore_ids = array_unique($ignore_ids);
        }
        $page_url            = $get_vars;
        $page_url['site_id'] = $site_id;
        unset($page_url['page_id']);
        $page_url   = '?' . http_build_query($page_url) . '&page_id=';
        $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
        include 'templates/list.tpl.php';
?>
	<?php echo $page_links ?>
<?php
} else {
?>
No results were found
<?php
}
}
?>
</form>
<br /><br />
<?php
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
include_once "footer.inc.php";
?>