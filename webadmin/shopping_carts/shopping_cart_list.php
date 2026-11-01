<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

moduleLoad('member'); //memberGetValue
moduleLoad('visitor');

include_once __DIR__ . '/templates/shopping_cart_header.tpl.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

if ('' == $order_by) {
    $order_by = 'date_added';
}

//Build a search criteria array
$criteria             = array();
$criteria['staff_id'] = $_GLOBALS['staff']['id'];
if ('' != $posted['author']) {
    $criteria['author'] = explode('|', $posted['author']);
}

if ('' != $posted['changed_by']) {
    $criteria['changed_by'] = explode('|', $posted['changed_by']);
}

if ('' != $posted['active']) {
    $criteria['active'] = $posted['active'];
}

if ('' != $posted['archived']) {
    $criteria['archived'] = $posted['archived'];
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

//Find and pagenate the invoices
$shopping_cart_list = shopping_cartSearch($criteria);
$shopping_cart_list = shopping_cartSort($shopping_cart_list, $order_by);
if ('desc' == $order) {
    $shopping_cart_list = array_reverse($shopping_cart_list);
}

$items_per_page = 50;
$count_total    = count($shopping_cart_list);

?>

<?=formatHTMLTitle('Shopping Carts')?>

<?php

$shopping_cart_list = array_chunk($shopping_cart_list, $items_per_page);
$count_pages        = count($shopping_cart_list);
$page_id            = max(0, min($count_pages - 1, $page_id));
$shopping_cart_list = $shopping_cart_list[$page_id];
$count_display      = count($shopping_cart_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />

<table class="wide" style="width: 98%;border-color:#FFFFFF" align="center" cellspacing="0" cellpadding="2" border="1">
	<tr>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=id&order=<?=('id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">ID</a></th>
		<th style="width:25%;"><a href="<?=$page_url?>&order_by=member_id&order=<?=('member_id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Member</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=visitor_id&order=<?=('visitor_id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Visitor ID</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=status&order=<?=('status' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Shopping Cart Status</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=stage&order=<?=('stage' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Checkout Stage</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=date_added&order=<?=('date_added' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date Added</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=last_updated&order=<?=('last_updated' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Last Updated</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=date_ordered&order=<?=('date_ordered' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date Ordered</a></th>
		<th style="width:10%;"><a href="<?=$page_url?>&order_by=date_added&order=<?=('date_added' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Days Open</a></th>
		<th>&nbsp;</th>
	</tr>
<?php
    $alt    = true;
    $status = array('open' => 'Open', 'ordered' => 'Order Completed', 'saved' => 'Saved', 'closed' => 'Cancelled/Deleted');
    $stage  = array(1 => 'Open', 2 => 'Delivery Address', 3 => 'Shipping Details', 4 => 'Payment Method', 5 => 'Review Order', 6 => 'Confirmation', 7 => 'Order Completed');
    foreach ($shopping_cart_list as $shopping_cart_id) {
        $class = "";
        if ($alt = !$alt) {
            $class = ' style="background-color:#F3F3F3;"';
        }

        $cart        = shopping_cartFromDatabase($shopping_cart_id);
        $daysOpen    = abs(formatDaysOpen($cart['time_added'], (('ordered' == $cart['status']) ? $cart['time_ordered'] : null)));
        $member_id   = $cart['member_id'];
        $visitor_id  = $cart["visitor_id"];
        $member_name = '';
        $member_url  = '';
        if ($visitor_id > 0) {
            $visitor = visitorFromDatabase($visitor_id);
            if ("" != $visitor["user_agent"]) {
                $browser     = @get_browser($visitor['user_agent'], true) ?: ['parent' => null, 'browser' => null];
                $member_name = array();
                if ($browser["crawler"]) {
                    if ("" != $browser["parent"]) {
                        $member_name[] = $browser["parent"];
                    }

                    if ($browser["browser"] != $browser["parent"]) {
                        $member_name[] = $browser["browser"];
                    }

                    if (0 != $browser["version"]) {
                        $member_name[] = $browser["version"];
                    }

                    $member_name[] = "Crawler";
                } else {
                    if ("" != $browser["platform"]) {
                        $member_name[] = $browser["platform"];
                    }

                    if ("" != $browser["parent"]) {
                        $member_name[] = $browser["parent"];
                    }

                    $member_name[] = "User";
                }
                $member_name = implode(" ", $member_name);
            }
        }
        if ($member_id > 0 && function_exists('memberGetValue')) {
            $member_name = memberGetValue($member_id, 'first_name') . ' ' . memberGetValue($member_id, 'last_name') . ' (' . $member_id . ')';
            $member_url  = '/webadmin/member/?action=edit&member_id=' . $member_id;
        }
        if (7 == $cart['stage']) {
            $class = ' style="background-color:#CFCFCF;"';
            if ('y' == $cart['payment_rejected'] && $cart['rejected_time']) {
                $class = ' style="background-color:#FFCCCC;"';
            } elseif ('y' == $cart['payment_made'] && $cart['payment_time']) {
                $class = ' style="background-color:#CCFFCC;"';
            }
        }
        $checkout = $stage[$cart['stage']];
        if ('closed' == $cart['status']) {
            $checkout = 'Order Cancelled';
        }

?>
	<tr<?=$class?>>
		<td style="text-align:center;width:36pt;">SC<?=str_pad($shopping_cart_id, 3, '0', STR_PAD_LEFT)?></a></td>
		<td style="text-align:center;width:172pt;">
<?php
if ('' != $member_url) {
?>
			<a href="<?=$member_url?>" target="_blank">
<?php
}
?>
			<?=htmlspecialchars($member_name)?>
<?php
if ('' != $member_url) {
?>
			</a>
<?php
}
?>
		</td>
		<td><a href="visitor_history.php?style=txt&amp;visitor_id=<?=$cart["visitor_id"]?>" target="_blank"><?=$cart['visitor_id']?></a></td>
		<td style="text-align:center;width:36pt;"><?=$status[$cart['status']]?></a></td>
		<td style="text-align:center;width:36pt;"><?=$checkout?></a></td>
		<td style="text-align:center;width:36pt;"><?=(0 != $cart['time_added']) ? date(_('jS F, Y'), $cart['time_added']) : ''?></a></td>
		<td style="text-align:center;width:36pt;"><?=(0 != $cart['time_updated']) ? date(_('jS F, Y'), $cart['time_updated']) : ''?></a></td>
		<td style="text-align:center;width:36pt;"><?=(0 != $cart['time_ordered']) ? date(_('jS F, Y'), $cart['time_ordered']) : ''?></a></td>
		<td style="text-align:center;width:36pt;"><?=$daysOpen?></a></td>
		<td style="white-space:nowrap;text-align:center;">
			<a href=".?action=view&shopping_cart_id=<?=$shopping_cart_id?>" target="_blank">View Cart</a><br />
			<a href="visitor_history.php?visitor_id=<?=$cart["visitor_id"]?>" target="_blank">Map Visitor</a>
		</td>
	</tr>
<?php
}
?>
</table>
<br />
<?=$page_links?><br /><br />
<span style="font-style:italic;">Notes:</span><br />
Rows highlighted in <span style="background-color:#CFCFCF;">grey</span> are shopping carts that have been completed.<br />
Rows highlighted in <span style="background-color:#FFCCCC;">pink</span> are orders, where payment has been rejected.<br />
Rows highlighted in <span style="background-color:#CCFFCC;">green</span> are orders, where payment has been accepted.<br /><br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/templates/shopping_cart_footer.tpl.php';
