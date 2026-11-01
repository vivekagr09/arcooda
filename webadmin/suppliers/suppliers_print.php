<?php
return;

include_once __DIR__ . '/suppliers_common.php';

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$text        = $posted['show'];
$sub         = $posted['sub'];
$alpha_value = $posted['alpha_value'];

if ('' != $sub) {
    $extra = "s.$text='$sub'";
    if ('site' == $text) {
        $extra = "s.$text LIKE '%$sub%'";
    }

}
if ('' != $alpha_value) {
    if ('' != $extra) {
        $extra .= " AND ";
    }

    $extra .= "s.manufacturer LIKE '$alpha_value%'";
}

$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page       = intval($posted['page']);
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'supplier_id';
$order      = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$site_id = $posted['site_id'];
$type    = $posted['type'];

$search_url           = $get_vars;
$search_url['action'] = 'list';
unset($search_url['search_for']);
$search_url = 'index.php?' . http_build_query($search_url);
?>
<script language="javascript">
var windowObj = this;
function printpage() {
        if (window.print)
        {
				window.print();
        }
        else
        {
                alert('To print this page, select Print Frame from the file menu.');
        }
}

function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
</script>
<body style="margin:0px 0px 0px 0px;font-size: xx-small;font-family: Verdana, Arial, Helvetica, Sans-serif;" onLoad="doPrint()">
<div style="text-align:center;">
<table style="width:100%;border: 1px solid #FFFFFF">
	<tr>
		<td style="text-align:left;vertical-align:top;width:auto;font-size:8pt;">
			<div style="font-size:20pt;text-align:left;">COINOP EXPRESS</div>
		</td>
		<td style="width:200px;font-size:8pt;vertical-align:top;">
			Suppliers Summary<br />
			Printed By <?=$_GLOBALS['staff']['name']?><br />
			Print Date: <?=date("d/m/y")?>
		</td>
	</tr>
</table>
</div>
<?php
$supplier_list = supplierSearch($search_for, $extra, $site_id, $type);
$supplier_list = supplierSort($supplier_list, $order_by);

if ('desc' == $order) {
    $supplier_list = array_reverse($supplier_list);
}

if (count($supplier_list)) {
    $company = array(0 => 'None', 1 => 'HG', 2 => 'HE', 9 => 'CNV', 11 => 'HGR', 16 => 'CAR');

    $status = array('approval' => 'Awaiting', 'payment' => 'Awaiting', 'goods' => 'Awaiting', 'checking' => 'Awaiting', 'cancelled' => '', 'processed' => '');
    foreach ($status as $key => $value) {
        $status[$key] = (('Awaiting' == $value) ? $value . ' ' : '') . ucfirst($key);
    }

    $pay = array('1' => 'No', '2' => 'Yes', '3' => 'Deposit');
?>
<br />
<table bordercolor="#CCCCCC" border=1 cellpadding="3" style="font-size:xx-small;border:1px solid #CCCCCC;border-collapse:collapse; width:100%;">
	<tr>
		<th style="width:5%">ID</th><th style="width:40%">Company</th><th style="width:10%">Country</th>
		<th style="width:4%">Open Orders</th><th style="width:4%">Total Orders</th><th style="width:12%">Last Order</th>
		<th style="width:10%">Value (USD)</th><th style="width:10%">Supplier For</th><th style="width:5%">Status</th>
	</tr>
<?php
$alt = false;
    foreach ($supplier_list as $supplier_id) {
        $supplier = supplierFromDatabase($supplier_id);
        $sites    = explode(',', $supplier['site']);
        foreach ($sites as $key => $site) {
            $sites[$key] = $company[$site];
        }

        $active_status = (('n' == $supplier['active']) ? 'Not ' : '') . 'Active';
?>
		<tr style="text-align:center;white-space:nowrap;">
			<td><?=$supplier_id?></td><td><?=$supplier['supplier_name']?></td><td><?=$supplier['country_full']?></td>
			<td><?=$supplier['open_orders']?></td><td><?=$supplier['total_orders']?></td><td><?=$supplier['last_order']?></td>
			<td>$<?=$supplier['order_value']?></td><td><?=implode(", ", $sites)?></td><td><?=$active_status?></td>
		</tr>
<?php
}
?>
	</table>
	<br />
	<br />
<?php
} else {
?>
	No results were found
<?php
}

include_once 'templates/suppliers_footer.tpl.php';?>