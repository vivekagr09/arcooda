<?php
include_once __DIR__ . '/common.inc.php';

$action = $posted['action'];
if (!isset($inc)) {
	include_once __DIR__ . '/header.inc.php';
}

switch ($action) {
    case 'do_search':
        $query    = $posted['query'];
        $order_by = ($posted['order_by']) ? $posted['order_by'] : 'contractor_id';
        $order    = ($posted['order']) ? $posted['order'] : 'desc';
        $site_id  = $posted['site_id'];
        do_search($query, $order_by, $order, $site_id);
        //$action = 'search';
        break;
    case 'search':
    default:
        display_search();
}

function do_search($query, $order_by = 'contractor_id', $order = 'desc', $site_id = 'all')
{
    display_search();
    global $posted, $DB;

    $page = $posted['page'];
    if ('' == $page) {
        $page = 0;
    }

    $limit = 100;
    $start = $page * $limit;

    $use_convert_quotes = true;
    $product_id         = intval($query);
    $set                = "`ip`.`product_name` LIKE " . formatSQLLikeString($query);
    if ($product_id > 0) {
        $set = "`ip`.`product_id` = $product_id";
    }

    $contractor_list  = array();
    $contractor_dates = array();

    // @todo

    $o_contractor_list = array_unique($contractor_list);

    $new_contractor_list = array();
    if (is_array($site_id) && !in_array('all', $site_id) && ('all' != $site_id || '' != $site_id)) {
        foreach ($o_contractor_list as $contractor_id) {
            if ('' != $site_id || 'all' != $site_id) {
                $contractor_type = contractorGetValue($contractor_id, 'contractor_type');
                if ('' == $contractor_type) {
                    $new_contractor_list[] = $contractor_id;
                } else {
                    $contractor_type = explode(',', $contractor_type);
                    if (is_array($contractor_type)) {
                        foreach ($contractor_type as $st) {
                            if (in_array($st, $site_id)) {
                                $new_contractor_list[] = $contractor_id;
                            }
                        }
                    }

                }
            }
        }
    } else {
        $new_contractor_list = $o_contractor_list;
    }

    echo formatHTMLTitle('Results');

    $sort_url = 'search2.php?action=do_search&query=' . $posted['query'];
    if (is_array($site_id)) {
        $temp = "&";
        foreach ($site_id as $key => $value) {
            $temp .= "site_id[$key]=$value&";
        }

        $sort_url .= $temp;
    }

    $contractor_list = array_chunk($new_contractor_list, $limit);
    $contractor_list = $contractor_list[$page];

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['member_id']);
    $edit_url = '.?' . http_build_query($edit_url);

    if (count($contractor_list) > 0) {
?>
<table class="table_grid">
	<tr>
<?php
        $array = array(
            'date' => 'Date Of Order',
            'contractor_id' => 'ID',
            'company' => 'Company',
            'last_name' => 'Contact Name',
            'contractor_type' => 'Contractor Type',
            'orders' => 'Orders',
            'modified' => 'Last Contact',
        );
        foreach ($array as $key => $value) {
            $new_order = ($order_by == $key && 'asc' == $order) ? 'desc' : 'asc';
?>
		<th>
			<a href="<?=$sort_url?>&page=<?=$page?>&order_by=<?=$key?>&order=<?=$new_order?>"><?=$value?></a>
			<?=($order_by == $key) ? '<img src="../' . $order . '.gif">' : ''?>
		</th>
<?php
}
?>
		<th></th>
	</tr>
<?php
foreach ($contractor_list as $contractor_id) {
            $contractor = contractorFromDatabase($contractor_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="height:32px;">
		<td style="text-align:center;"><?=date('d/m/Y', $contractor_dates[$contractor_id]);?></td>
		<td style="text-align:center;"><?=substr('000000' . $contractor_id, -6)?></td>
		<td style="text-align:center;"><?=$contractor['company']?></td>
		<td style="text-align:center;"><?=$contractor['name'] . ' ' . $contractor['last_name']?></td>
		<td style="text-align:center;">
			<!--<table cellpadding=2><tr>-->
<?php
$mem_type = explode(",", $contractor['contractor_type']);
            foreach ($mem_type as $mem) {
                if ('' != $mem) {
?>
			<img src="images/<?=$mem?>.png">&nbsp;&nbsp;&nbsp;
<?php
}
            }
?>
			<!--</tr></table>-->
		</td>
		<td style="text-align:center;"><?=$contractor['total_orders']?></td>
		<td style="text-align:center;"><?=('0000-00-00' == $contractor['modified']) ? 'never' : date("d/m/y", strtotime($contractor['modified']))?></td>
		<td style="text-align:center;">
<?php
if (isset($posted['return_message'])) {
?>
			<a href=".?action=select&contractor_id=<?=$contractor_id?>&return_message=<?=urlencode($posted['return_message'])?>">Select</a>
			<a href="<?=$edit_url?>&contractor_id=<?=$contractor_id?>&return_message=<?=urlencode($posted['return_message'])?>">Edit</a>
<?php
} else {
?>
			<a href="<?=$edit_url?>&contractor_id=<?=$contractor_id?>" target="_blank">Edit</a>
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
$items = count($contractor_list);
        $total = count($o_contractor_list);
        if ('alpha' == $by) {
            $limit = $total;
        }

        $url = "?action=do_search&query=$query&order_by=$order_by&order=$order&p&by=$by&alpha_value=$alpha_value&page=";
        echo '<br /><br /><center>' . formatHTMLPageLinks($url, $page, $items, $total, $limit, 'style="color:blue"') . '</center>';
    } else {
        echo "No Results were found.";
    }

}

function display_search()
{
    global $posted, $websites_list;
?>
<br />
<link type="text/css" rel="stylesheet" href="style.css" />
<form accept-charset="utf-8" autocomplete="off" method="get" action="search2.php"><table class="table_search">
	<tr><td>
<?php
if (isset($posted['return_message'])) {
?>
        <input type="hidden" name="return_message" value="<?php hecho($posted['return_message']) ?>" />
<?php
}
?>
        <input type="hidden" name="action" value="do_search" />
        <div align="center"><strong><font color="#FF6600" size="4">Sales Search</font></strong></div>
    </td></tr>
	<tr><td><div align="center">Enter a <b>Product ID</b> or <b>Product Name</b></div></td></tr>
	<tr><td><div align="center">
        <input type="text" name="query" size="30" value="<?php hecho($posted['query']) ?>" />
        <input type="submit" value="Search" />
    </div></td></tr>
    <tr><td><div align="center">
        <input type="checkbox" name="site_id[0]" size="30" value="all" <?=(in_array('all', $selected)) ? 'checked' : ''?>>All&nbsp;&nbsp;
<?php
    $selected = $posted['site_id'];
    if (!is_array($selected)) {
        $selected = array();
    }
    foreach ($websites_list as $site_id => $site_name) {
?>
        <input type="checkbox" name="site_id[]" value="<?=$site_id?>"<?=(in_array($site_id, $selected)) ? ' checked="checked"' : ''?> /><?php hecho($site_name) ?>&nbsp;
<?php
}
?>
    </div></td></tr>
</table></form>
<?php
}

if (!isset($inc)) {
    include_once __DIR__ . '/footer.inc.php';
}
