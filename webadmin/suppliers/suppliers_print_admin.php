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

$text        = $_REQUEST['show'];
$sub         = $_REQUEST['sub'];
$alpha_value = $_REQUEST['alpha_value'];

$extra = ('' != $sub) ? "s.$text " . (('site' == $text) ? "LIKE '%" . $sub . "%' " : '=' . $DB->escape($sub)) : '';
$extra .= ('' != $alpha_value) ? (('' != $extra) ? " AND " : "") . "s.manufacturer LIKE " . $DB->escape($alpha_value . '%') : '';
/*
if($sub != '')
{
$extra = "s.$text='$sub'";
if($text == 'site')
$extra = "s.$text LIKE '%$sub%'";
}
if($alpha_value != '')
{
if($extra != '')
$extra .= " AND ";
$extra .= "s.manufacturer LIKE '$alpha_value%'";
}
 */

$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'supplier_id';
$order      = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

?>
<script language="javascript">
var windowObj = this;
function printpage()
{
	if (window.print) window.print();
	else alert('To print this page, select Print Frame from the file menu.');
}

function doPrint()
{
	if (confirm("Click OK to print this document")) printpage();
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
			Suppliers Summary - Admin Copy<br />
			Printed By <?=$_GLOBALS['staff']['name']?><br />
			Print Date: <?=date("d/m/y")?>
		</td>
	</tr>
</table>
</div>
<?php
$supplier_list = supplierSearch($search_for, $extra, $site_id, $type);
$supplier_list = supplierSort($supplier_list, $order_by);
$seaports      = getSeaPortsList();
$airports      = getAirPortsList();
$company       = array(0 => 'None', 1 => 'HG', 2 => 'HE', 9 => 'CNV', 11 => 'HGR', 16 => 'CAR');
$sup_type      = array('supp' => 'Supplier', 'man' => 'Manufacturer', 'both' => 'Supplier & Manufacturer');

if ('desc' == $order) {
    $supplier_list = array_reverse($supplier_list);
}

if (count($supplier_list)) {
?>
<br />
<table bordercolor="#CCCCCC" border=1 cellpadding="3" style="font-size:xx-small;border:1px solid #CCCCCC;border-collapse:collapse; width:100%;">
	<tr>
		<th style="width:5%" rowspan="2">Name</th><th style="width:10%" rowspan="2">Address</th>
		<th style="width:5%" rowspan="2">Phone</th><th style="width:5%" rowspan="2">Fax</th>
		<th style="width:5%" rowspan="2">Mobile</th></th><th style="width:5%" rowspan="2">Email</th>
		<th style="width:5%" rowspan="2">Website</th><th style="width:5%" rowspan="2">Currency</th>
		<th style="width:5%" rowspan="2">Warehouses</th><th style="width:10%" colspan="2">Shipping Locations</th></th>
		<th style="width:15%" colspan="3">Contacts</th><th style="width:5%" rowspan="2">Bank Details</th>
		<th style="width:5%" rowspan="2">Default Values</th><th style="width:5%" rowspan="2">Supplier For</th>
		<th style="width:5%" rowspan="2">Type</th><th style="width:5%" rowspan="2">Active</th>
	</tr>
	<tr>
		<th style="width:5%">Seaport</th><th style="width:5%">Airport</th>
		<th style="width:5%">Name</th><th style="width:5%">Position</th><th style="width:5%">Email</th>
	</tr>
<?php
foreach ($supplier_list as $supplier_id) {
        $supplier   = supplierFromDatabase($supplier_id);
        $warehouses = supplierWarehousesFromDatabase($supplier_id, true);
        $contacts   = supplierContactsFromDatabase($supplier_id);
        //print_array($contacts);
        $banks = supplierBanksFromDatabase($supplier_id, true);
        $sites = explode(',', $supplier['site']);
        foreach ($sites as $key => $site) {
            $sites[$key] = $company[$site];
        }

        $address = array();
        if ('' != $supplier['address']) {
            $address[] = $supplier['address'];
            $temp      = array();
            if ('' != $supplier['city']) {
                $temp[] = $supplier['city'];
            }

            if ('' != $supplier['state']) {
                $temp[] = $supplier['state'];
            }

            if ('' != $supplier['post_code']) {
                $temp[] = $supplier['post_code'];
            }

            $address[] = implode(", ", $temp);
            if ('' != $supplier['country']) {
                $address[] = getCountryName($supplier['country']);
            }

        }
        $address = implode("\n", $address);
        if ('' != $supplier['country'] && '' != $supplier['seaport']) {
            $seaport = $seaports[$supplier['country']][$supplier['seaport']];
        }

        if ('' != $supplier['country'] && '' != $supplier['airport']) {
            $airport = $airports[$supplier['country']][$supplier['airport']];
        }

        $rowspan = max(1, count($contacts));
        $contact = $contacts[0];
        unset($contacts[0]);
        $default = false;
        if ($supplier['waranty'] > 0) {
            $default = true;
        }

        if ($supplier['voltage'] > 0) {
            $default = true;
        }

        if ($supplier['condition'] > 0) {
            $default = true;
        }

        if ($supplier['plug'] > 0) {
            $default = true;
        }

        if ($supplier['packing'] > 0) {
            $default = true;
        }

        if (in_array('HE', $sites) || in_array('HG', $sites)) {
            if ($supplier['terms'] > 0) {
                $default = true;
            }

            if ($supplier['coin'] > 0) {
                $default = true;
            }

            if ($supplier['version'] > 0) {
                $default = true;
            }

        }
?>
	<tr style="text-align:center;">
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['supplier_name']?></td>
		<td style="width:10%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=nl2br($address)?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['phone']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['fax']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['mobile']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['email']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['website']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$supplier['currency']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$warehouses > 0 ? 'Y' : 'N'?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$seaport?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$airport?></td>
		<td style="width:5%"><?=$contact['name']?></td><td style="width:5%"><?=$contact['title']?></td>
		<td style="width:5%"><?=$contact['email']?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$banks > 0 ? 'Y' : 'N'?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$default ? 'Y' : 'N'?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=implode(", ", $sites)?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=$sup_type[$supplier['type']]?></td>
		<td style="width:5%" <?=($rowspan > 1) ? 'rowspan="' . $rowspan . '"' : ''?>><?=(('n' == $supplier['active']) ? 'Not ' : '') . 'Active'?></td>
	</tr>
<?php
foreach ($contacts as $contact) {
?>
	<tr style="text-align:center;">
		<td style="width:5%"><?=$contact['name']?></td><td style="width:5%"><?=$contact['title']?></td>
		<td style="width:5%"><?=$contact['email']?></td>
	</tr>
<?php
}
    }
?>
</table>
<?php
}
