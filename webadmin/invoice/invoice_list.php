<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order    = (array_key_exists('order', $posted) && 'asc' == $posted['order']) ? 'asc' : 'desc';
$order_by = (array_key_exists('order_by', $posted)) ? strtolower($posted['order_by']) : 'invoice_id';
$page_id  = (array_key_exists('page_id', $posted)) ? max(0, intval($posted['page_id'])) : 0;

//Build a search criteria array
$criteria = array();
foreach (array('site_id', 'newsletter_id', 'visitor_id', 'invoice_action', 'invoice_status', 'paymenttype_id', 'payments_located', 'member_id', 'staff_id', "tax_month", "tax_year", 'ship_country','ship_id') as $key) {
    if (array_key_exists($key, $posted) && !empty($posted[$key])) {
        $criteria[$key] = (is_array($posted[$key])) ? $posted[$key] : preg_split("/[^a-zA-Z0-9]+/", $posted[$key]);
    }
}

if (9 == $_GLOBALS["site_settings"]["site_id"]) {
    $allowed = array(9, 16, 25);
    if (is_array($criteria["site_id"])) {
        foreach ($criteria["site_id"] as $k => $v) {
            if (!in_array($v, $allowed)) {
                unset($criteria["site_id"][$k]);
            }
        }

        if (count($criteria["site_id"]) == 0) {
            $criteria["site_id"] = 9;
        }
    } elseif (!in_array($criteria["site_id"], $allowed)) {
        $criteria["site_id"] = 9;
    }
} elseif (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $criteria["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

if (array_key_exists('search_for', $posted) && !empty($posted['search_for'])) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}

if (array_key_exists('status', $posted) && !empty($posted['status'])) {
    $criteria['invoice_status'] = explode('|', $posted['status']);
}

if (array_key_exists('bill_country', $posted) && !empty($posted['bill_country'])) {
    $criteria['bill_country'] = explode('|', $posted['bill_country']);
}

if (array_key_exists('handler', $posted) && !empty($posted['handler'])) {
    $criteria['handler_id'] = $posted['handler'];
}

if (array_key_exists('open_year', $posted) && !empty($posted['open_year'])) {
    $criteria['open_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['open_year']));
}

if (array_key_exists('open_quarter', $posted) && !empty($posted['open_quarter'])) {
    $criteria['open_quarter'] = mktime(0, 0, 0, date('m', $posted['open_quarter']), 1, date('Y', $posted['open_quarter']));
}

if (array_key_exists('open_month', $posted) && !empty($posted['open_month'])) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if (array_key_exists('open_week', $posted) && !empty($posted['open_week'])) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

if (array_key_exists('invoice_list', $posted) && !empty($posted['invoice_list'])) {
    $criteria['invoice_id'] = preg_split('/[^0-9]+/', $posted['invoice_list']);
}
if (array_key_exists('invoice_action', $posted) && !empty($posted['invoice_action'])) {
    $criteria['invoice_action'] = $posted['invoice_action'];
}

//Find and pagenate the invoices
$invoice_list = invoiceSearch($criteria);

if (array_key_exists('prod_string', $posted)) {
    $temp = preg_split("/[^a-z0-9]+/i", $posted['prod_string']);
    foreach ($temp as $k => $v) {
        if ("" == $v) {
            unset($temp[$k]);
        }
    }

    if (count($temp) > 0) {
        $where = array();
        foreach ($temp as $findme) {
            foreach (array('`ip`.`product_id`', '`ip`.`product_name`', '`ip`.`product_notes`') as $v) {
                $where[] = $v . " LIKE " . formatSQLLikeString($findme);
            }
        }

        $temp  = array();
        $sql   = sprintf("SELECT `ip`.`invoice_id` FROM `highwaygroup`.`invoice_products` AS `ip` WHERE %s GROUP BY `ip`.`invoice_id` ASC", implode(" OR ", $where));
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $temp[] = $row['invoice_id'];
        }

        $DB->free_result($query);

        $invoice_list = array_intersect($invoice_list, $temp);
    }
}
if (array_key_exists('generic_code', $posted)) {
    $where[] = "`ip`.`generic_code` IN ('GPA','GMA','GPC','GME')";
    $temp  = array();
    $sql   = sprintf("SELECT `ip`.`invoice_id` FROM `highwaygroup`.`invoice_products` AS `ip` WHERE %s GROUP BY `ip`.`invoice_id` ASC", implode(" OR ", $where));
    $query = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $temp[] = $row['invoice_id'];
    }

    $DB->free_result($query);

    $invoice_list = array_intersect($invoice_list, $temp);
}
if (array_key_exists('payments_made', $posted) && 'y' == $posted['payments_made']) {
    $temp  = array();
    if ($query = $DB->query(sprintf(
        "SELECT `ip`.`invoice_id` FROM `highwaygroup`.`invoice_payments` AS `ip` GROUP BY `ip`.`invoice_id`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $temp[] = $row['invoice_id'];
        }
        $DB->free_result($query);
    }

    $invoice_list = array_intersect($invoice_list, $temp);
}

$site_id   = intval($posted['site_id']);
$status_id = (array_key_exists('status', $posted)) ? $posted['status'] : '';
$search    = (array_key_exists('search_for', $posted)) ? $posted['search_for'] : '';

?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionSearchInvoices');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search Invoices</b></a>
	<br /><br />
	<div id="sectionSearchInvoices" class="child"<?=("" != $search) ? '' : ' style="display:none"'?>>
<?php
include __DIR__ . '/templates/search_invoices.tpl.php';
?>
	</div>
	<div id="sectionSearchInvoices_hideNote" class="child"<?=("" != $search) ? ' style="display:none"' : ''?>></div>
</div>
<?php

if (array_key_exists("status", $posted) && ((is_array($posted["status"]) && (in_array("sales", $posted["status"]) || in_array("proforma", $posted["status"]))) || in_array("sales", preg_split("/[^a-z0-9]+/", $posted["status"])) || in_array("proforma", preg_split("/[^a-z0-9]+/", $posted["status"]))) && (!isset($posted["referer"]))) {
    $sub_criteria                   = $criteria;
    $sub_criteria["invoice_status"] = "processed";
    $sub_criteria["invoice_action"] = array('working', 'feedback', 'nointerest', 'payment', 'approval', 'shipping', 'shipped', 'cancelled', 'nohelp', 'returned', 'sent_factory', 'prepare_factory', 'prepare_customer', 'sent_customer', 'return_completed', 'return_cancelled');
    $invoice_list                   = array_unique(array_merge($invoice_list, invoiceSearch($sub_criteria)));
    sort($invoice_list);
}

if (array_key_exists("supplier_id", $posted)) {
    $supplier_id = (is_array($posted["supplier_id"])) ? $posted["supplier_id"] : preg_split("/[^0-9]+/", $posted["supplier_id"]);
    foreach ($supplier_id as $k => $v) {
        $supplier_id[$k] = intval($v);
    }

    if (count($supplier_id) == 0 || count($invoice_list) == 0) {
        $invoice_list = array();
    } else {
        $query        = $DB->query("SELECT DISTINCT `invoice_id` FROM `highwaygroup`.`invoice_products` WHERE `supplier_id` IN(" . implode(", ", $supplier_id) . ") AND `invoice_id` IN(" . implode(", ", $invoice_list) . ")");
        $invoice_list = array();
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_list[] = $row["invoice_id"];
        }

        $DB->free_result($query);
    }
}

$invoice_list = invoiceSort($invoice_list, $order_by);
if ('desc' == $order) {
    $invoice_list = array_reverse($invoice_list);
}

$items_per_page = 100;
if ($_GLOBALS['staff']['invoices_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['invoices_per_page'];
}

$count_total = count($invoice_list);

if ($count_total > 0) {
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewBySiteHandler');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler/Website</b></a>
	<br /><br />
	<div id="sectionViewBySiteHandler" class="child" style="display:none">
<?php
include __DIR__ . '/templates/navigate_invoices.tpl.php';
?>
	</div>
	<div id="sectionViewBySiteHandler_hideNote" class="child"></div>
</div>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewStatus');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Invoice Status</b></a>
	<br /><br />
	<div id="sectionViewStatus" class="child" style="display:none">
<?php
include __DIR__ . '/templates/navigate_invoices2.tpl.php';
?>
	</div>
	<div id="sectionViewStatus_hideNote" class="child"></div>
</div>
<?php
}

$extra   = ($popup_window) ? '<i><a href=".?action=add&status=sales&site_id=' . $site_id . '" target="_blank">Add New Invoice</a> | <a href=".?action=add&status=inquiry&site_id=' . $site_id . '" target="_blank">Add New Inquiry</a></i>' : '';
$display = ($display_both) ? 'Invoices/Inquiries' : (($display_inquiry) ? 'Inquiries' : 'Invoices');
?>
<?=formatHTMLTitle($display, $extra)?>
<?php

$count_total = count($invoice_list);

$invoice_list  = array_chunk($invoice_list, $items_per_page);
$count_pages   = count($invoice_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
if (array_key_exists($page_id, $invoice_list)) {
    $invoice_list = $invoice_list[$page_id];
} else {
    $invoice_list = array();
}
$count_display = count($invoice_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'invoice_select.php?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
include __DIR__ . '/templates/list_invoices.tpl.php';
?>
</form>
<br />
<?=$page_links?><br />
<br />
<?php
if (!$popup_window) {
?>
<?php
if($_GET['referer'] === 'product_ordered'){ ?>
<input type="button" onclick="window.open('invoice_download.xlx.php?<?=http_build_query($get_vars)?>');" value="Export Excel" />
<?php } ?>
<input type="button" onclick="window.open('invoice_list_printable.php?<?=http_build_query($get_vars)?>');" value="Print <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?> List" />
<input type="button" onclick="window.open('invoice_wanted_printable.php?<?=http_build_query($get_vars)?>');" value="Print Wanted List" />
<?php
} else {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
