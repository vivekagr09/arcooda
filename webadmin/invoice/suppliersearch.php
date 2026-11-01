<?php
/*

Edit this file: to have better structure, but file might not be needed as supplier information is already passed with the product information....

Last Edit: Jwalit Shah, 10/5/06

 */

include_once __DIR__ . '/common.inc.php';

moduleLoad('supplier');

$myurl = "suppliersearch.php";

switch ($_POST['action']) {
    case 'select':
        showSelectSupplier();
        break;
    case 'special':
        showSelectSpecial();
        break;
    default:
        showSearchSupplier();
        break;
}
return;

function searchProductSuppliers($product_id, $detailed = false)
{
    global $DB;

    $sql = "SELECT " .
    "s.id," .
    "s.manufacturer as supplier_name," .
    "p.product_name," .
    "pp.cost_price as buy_price," .
    "pp.sales_price as sell_price" .
    " FROM `highwaygroup`.`suppliers` s " .
    " LEFT JOIN `convert_convert`.`products` p ON p.supplier = s.id" .
    " LEFT JOIN `convert_convert`.`products_pricing` pp ON pp.pid=p.id" .
    " WHERE p.id = " . intval($product_id);
    if ($detailed) {
        $sql = "SELECT " .
            "s.id," .
            "s.manufacturer as supplier_name," .
            "p.product_name," .
            "pp.cost_price as buy_price," .
            "pp.sales_price as sell_price" .
            " FROM `highwaygroup`.`suppliers` s " .
            " LEFT JOIN `convert_convert`.`products` p ON p.supplier = s.id" .
            " LEFT JOIN `convert_convert`.`products_pricing` pp ON pp.pid=p.id";
        " WHERE s.id = " . intval($product_id);
    }

    $res = $DB->query($sql);
    $ret = array();
    while ($row = $DB->fetch_assoc($res)) {
        $ret[] = $row;
    }

    return $ret;
}

function showSearchSupplier()
{
    moduleLoad('product'); //productFromDatabase

    global $myurl, $posted;

    $target_row = $posted['row'];
    $currency   = $posted['currency'];
    $product_id = $posted['for'];

    $product_info  = productFromDatabase($product_id);
    $country_list  = getCountryList();
    $supplier_list = searchProductSuppliers($product_id);
    $special_list  = supplierSearch('', '');

    $addsupplier_url = '/webadmin/suppliers/?action=add';

    $supplier_count = 0;
    if (is_array($supplier_list)) {
        $supplier_count = count($supplier_list);
    }

    $special_count = count($special_list);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript">
//<![CDATA[
function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}
function setValue(name,value)
{
	var elem=getElement(name);
	if(elem==null) return;
	elem.value=value;
}
function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	if(theSect==null) return;
	if(theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
function addSupplier()
{
	window.open(<?=json_encode($addsupplier_url)?>);
}
//]]>
		</script>
	</head>
	<body style="text-align:center">
		<div class="invoice_content" style="text-align:center">

<form method="post" action="<?=$myurl?>?row=<?=$target_row?>&amp;currency=<?=$currency?>&amp;for=<?=$product_id?>">
<input type="hidden" name="supplier_id" value="0" />
<input type="hidden" name="action" value="select" />

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShipSuppliers\');">Suppliers That Ship Product</a>')?>

<div id="sectionShipSuppliers">
	<table class="wide spacetable">
		<tr>
			<th>Product Name</th>
			<th>Supplier Name</th>
			<th>Buy Price USD</th>
			<th>Sell Price USD</th>
			<th>Location</th>
			<th>&nbsp;</th>
		</tr>
<?php
    if ($supplier_count > 0) {
        foreach ($supplier_list as $item) {
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="width:50%;white-space:nowrap;text-align:left;"><?=$product_info['product_name']?></td>
			<td style="width:50%;white-space:nowrap;text-align:center;"><?=$item['supplier_name']?> <?=$item['supplier_name_ch']?></td>
			<td style="text-align:right;white-space:nowrap;"><?=$item['buy_price']?></td>
			<td style="text-align:right;white-space:nowrap;"><?=$item['sell_price']?></td>
			<td style="text-align:center;white-space:nowrap;"><?=$country_list[$item['country']]?></td>
			<td style="text-align:center"><input type="button" value="Select" onclick="javascript:setValue('supplier_id','<?=$item['id']?>');form.submit();" /></td>
		</tr>
<?php
        }
    }
?>
		<tr><th colspan="8" style="text-align:center;">Found <?=$supplier_count?> result<?=(1 != $supplier_count) ? 's' : ''?></th></tr>
	</table>
</div>
</form>

<form method="post" action="<?=$myurl?>?row=<?=$target_row?>&amp;currency=<?=$currency?>&amp;for=<?=$product_id?>">
<input type="hidden" name="supplier_id" value="0" />
<input type="hidden" name="action" value="special" />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAllSuppliers\');">All Other Suppliers</a>')?>

<div id="sectionAllSuppliers">
	<table class="wide spacetable">
		<tr>
			<th>Supplier Name</th>
			<th>Location</th>
			<th>&nbsp;</th>
		</tr>
<?php
    if (count($special_list) > 0) {
        foreach ($special_list as $supplier_id) {
            $item = supplierFromDatabase($supplier_id);
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="width:100%;white-space:nowrap;text-align:left;"><?=$item['supplier_name']?></td>
			<td style="text-align:center;white-space:nowrap;"><?=$country_list[$item['country']]?></td>
			<td style="text-align:center"><input type="button" value="Select" onclick="javascript:setValue('supplier_id','<?=$supplier_id?>');form.submit();" /></td>
		</tr>
<?php
        }
    }
?>
		<tr><th colspan="3" style="text-align:center;">Found <?=$special_count?> result<?=(1 != $special_count) ? 's' : ''?></th></tr>
	</table>

<?php
//if(has_permission('PRD_CFG', $allowed_sections) || has_permission('NEW_SUPP', $allowed_sections))
    //{
?>
	<!--<input onclick="javascript:addSupplier();" type="button" value="Add New Supplier" />-->
<?php
//}
?>

</div>
</form>

<table class="wide spacetable" style="width:60%;margin:auto">
	<tr><td style="text-align:center;padding:8px 8px 8px 8px;">All suppliers can be shown by clicking on "All Other Suppliers" above</td></tr>
</table>
</div>

<script type="text/javascript">
//<![CDATA[
if (toggleSection) {
    toggleSection('sectionAllSuppliers');
}
//]]>
</script>
</div>

	</body>
</html>
<?php
}

function showSelectSupplier()
{
    moduleLoad('product'); //productFromDatabase

    global $DB, $posted;
    $target_row  = $posted['row'];
    $currency    = $posted['currency'];
    $product_id  = $posted['for'];
    $supplier_id = $posted['supplier_id'];

    $product_info  = productFromDatabase($product_id);
    $country_list  = getCountryList();
    $supplier_list = searchProductSuppliers($product_id, true);

    $supplier_count = 0;
    if (is_array($supplier_list)) {
        $supplier_count = count($supplier_list);
    }

    $key = '';
    foreach ($supplier_list as $tkey => $item) {
        if ($item['id'] == $supplier_id) {
            $key = $tkey;
        }
    }

    $supplier = $supplier_list[$key];

    $supplier['product_id']           = $product_id;
    $supplier['supplier_qty']         = 1;
    $supplier['supplier_cost']        = $supplier['buy_price'];
    $supplier['supplier_productname'] = $product_info['product_name'];
    $supplier['supplier_count']       = $supplier_count;
    $supplier['supplier_notes']       = $supplier['notes'];
    if (trim($supplier['supplier_name']) == '') {
        $supplier['supplier_name'] = $supplier['supplier_name_ch'];
    }

    $supplier['supplier_country']     = $supplier['location'];
    $supplier['supplier_productname'] = $product_info['product_name'];

    $supplier = array_merge($supplier, supplierFromDatabase($supplier_id));
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
	</head>
	<body>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($supplier)?>;
if (window.opener.setSupplierData) {
	window.opener.setSupplierData('<?=$target_row?>',data);
	//window.close();
} else {
	alert('Unable to send supplier back to form');
}
//]]>
        </script>
    </body>
</html>
<?php
}

function showSelectSpecial()
{
    moduleLoad('product'); //productFromDatabase

    global $DB, $posted;
    $target_row  = $posted['row'];
    $currency    = $posted['currency'];
    $product_id  = $posted['for'];
    $supplier_id = $posted['supplier_id'];

    $product_info  = productFromDatabase($product_id);
    $country_list  = getCountryList();
    $supplier_list = searchProductSuppliers($supplier_id, true);
    //print_array($supp_list);
    //$supplier_list = array();
    //foreach($supp_list as $key=>$id)
    //    $supplier_list[$id] = supplierFromDatabase($id);

    //print_array($supplier_list);
    $supplier_count = 0;
    if (is_array($supplier_list)) {
        $supplier_count = count($supplier_list);
    }

    $key = '';
    foreach ($supplier_list as $tkey => $item) {
        if ($item['id'] == $supplier_id) {
            $key = $tkey;
        }
    }

    $supplier = $supplier_list[$key];

    $supplier['product_id']           = $product_id;
    $supplier['supplier_qty']         = 1;
    $supplier['supplier_cost']        = $supplier['buy_price'];
    $supplier['supplier_productname'] = $product_info['product_name'];
    $supplier['supplier_count']       = $supplier_count;
    $supplier['supplier_notes']       = $supplier['notes'];
    if (trim($supplier['supplier_name']) == '') {
        $supplier['supplier_name'] = $supplier['supplier_name_ch'];
    }

    $supplier['supplier_country']        = $supplier['location'];
    $supplier['supplier_productname']    = $product_info['product_name'];
    $supplier['supplier_cost_orig']      = $supplier['buy_price'];
    $supplier['supplier_cost_orig_curr'] = $supplier['currency'];

    $supplier = array_merge($supplier, supplierFromDatabase($supplier_id));
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <title></title>
        <script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($supplier)?>;
if (window.opener.setSupplierData) {
    window.opener.setSupplierData('<?=$target_row?>', data);
} else {
    alert('Unable to send supplier back to form');
}
//]]>
        </script>
    </head>
    <body>
    </body>
</html>
<?php
}
