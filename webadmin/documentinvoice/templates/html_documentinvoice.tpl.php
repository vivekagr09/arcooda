<?php
global $_GLOBALS;

ob_start();

$charges_list = array();
$query        = $DB->query("SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}

$terms_list          = getTermsList();
$warranty_terms_list = documentinvoiceGetOptionList('warranty_terms_id');
$staff               = $_GLOBALS['staff'];
$payment_data        = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($sinvoice['site_id']) . " AND payment_method_id=" . formatSQLString($sinvoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$sinvoice_labels     = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE company_id=" . intval($sinvoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_info        = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE id=" . intval($sinvoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

if ('' == $sinvoice_labels['company_invoiceheader']) {
    $sinvoice_labels['company_invoiceheader'] = $sinvoice['company_name'] . "\n" . $sinvoice['company_address'];
}

if ('' == $sinvoice_labels['company_invoiceheader_au']) {
    $sinvoice_labels['company_invoiceheader_au'] = $sinvoice['company_name'] . "\n" . $sinvoice['company_address'];
}

if ('' == $sinvoice_labels['company_name']) {
    $sinvoice_labels['company_name'] = $sinvoice['company_name'];
}

//$website_url=$website_info['url'];
//$website_path=$website_info['root_dir'];
$website_url  = 'http://www.highwaygroup.net/';
$website_path = '/home/hwgroup/public_html/';
if (substr($website_url, -1) != '/') {
    $website_url .= '/';
}

if (substr($website_path, -1) != '/') {
    $website_path .= '/';
}

$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/stamp.png';
$images['signature']      = 'images/sigs/' . $staff['signature'];
if ($sinvoice['site_id'] <= 0) {
    $images['signature'] = '';
}

if (in_array($sinvoice['invoice_status'], array('inquiry', 'closed'))) {
    $images['signature'] = '';
}

if (!is_null($force_signature)) {
    $images['signature'] = 'images/sigs/' . $force_signature;
}

foreach ($images as $key => $value) {
    if (is_file($website_path . $value)) {
        $images[$key] = $website_url . $value;
    } else {
        $images[$key] = '';
    }
}

if (is_array($force_images)) {
    foreach ($force_images as $key => $value) {
        $images[$key] = $value;
    }
}

$sinvoice_reference = strtoupper(str_replace('_', '', formatFileSafe(documentinvoiceFormatNumber($dinvoice_id, $sinvoice['revision_id'], $sinvoice['site_id']))));
if ('BPAY' == $sinvoice['paymenttype_id']) {
    $sinvoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($sinvoice['member_id'], 0), 5));
}

$sinvoice_type     = array_search($sinvoice['invoice_status'], array(
    'inquiry',
    'enquiry',
    'closed',
    'proforma',
    'sales',
    'processed',
    'cancelled',
    'return',
    'completed',
    'purchase',
));
$sinvoice_type_text = 'INVOICE';
/*
if($sinvoice['ship_country']=='AU' && $sinvoice['bill_country']=='AU')
$sinvoice_type_text='TAX INVOICE';
else
$sinvoice_type_text='SALES INVOICE';
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<style type="text/css">
.invoice *
{
	font:8pt verdana;
	border-collapse:collapse;
}
.no_break, .no_break *
{
	page-break-inside:avoid;
}
.invoice .box_header
{
<?php
switch ($sinvoice['site_id']) {
    case 1:
        echo "	background-color:#0023F4;\n	color:#FFFFFF;\n";
        break;
    case 2:
        echo "	background-color:#00C900;\n	color:#FFFFFF;\n";
        break;
    case 9:
        echo "	background-color:#454B84;\n	color:#FFFFFF;\n";
        break;
    case 16:
        echo "	background-color:#293B4E;\n	color:#E2EEFA;\n";
        break;
    default:
        echo "	background-color:#777777;\n	color:#000000;\n";
        break;
}
?>
}
.invoice .box_shaded
{
	background-color:#777777;
	color:#FFFFFF;
}
.invoice .box_normal, .invoice .box_header, .invoice .box_shaded
{
	padding:4px;
	border:1px solid #000000;
}
.invoice .box_product
{
	padding:4px;
	text-align:center;
	vertical-align:middle;
	border-left:1px solid #000000;
	border-right:1px solid #000000;
	border-bottom:1px dashed #000000;
}
</style>
<title>Invoice <?=documentinvoiceFormatNumber($dinvoice_id, (3 == $sinvoice_type || 4 == $sinvoice_type) ? $sinvoice['revision_id'] : null, $sinvoice['site_id'])?></title>
</head>
<body style="margin:0px;"><table align="center" class="invoice" style="border-collapse:collapse;width:98%;">
<thead style="display:table-header-group;">
	<tr><td colspan="5" style="padding:0px;">
<table style="width:100%;"><tr>
<?php
$temp = explode("\n", str_replace("\r", "", $sinvoice_labels['company_invoiceheader']));
if ('AU' == $sinvoice['bill_country'] && 'AU' == $sinvoice['ship_country']) {
    $temp = explode("\n", str_replace("\r", "", $sinvoice_labels['company_invoiceheader_au']));
}

?>
	<td style="text-align:left;width:100%;">
<?php
if ('' != $images['company_logo']) {
?>
		<img style="font-size:24pt;" src="<?=$images['company_logo']?>" alt="<?=htmlspecialchars(array_shift($temp))?>" /><br />
		<br />
<?php
} else {
?>
		<span style="font-size:24pt;"><?=htmlspecialchars(array_shift($temp))?></span><br />
<?php
}
?>
		<?=implode("<br />\n		", $temp)?>
	</td>
	<td style="text-align:right;vertical-align:top;">
		<table style="width:2.7in;"><tr><td class="box_shaded" style="font-size:12pt;font-weight:bold;text-align:center;">
			<?=$sinvoice_type_text?>
		</td></tr></table>
		<br /><br />
		<table style="width:2.7in;">
			<tr>
				<td class="box_header" style="width:50%;text-align:center;">INVOICE #</td>
				<td class="box_header" style="width:50%;text-align:center;">DATE</td>
			</tr>
			<tr>
				<td class="box_normal" style="text-align:center;"><?=documentinvoiceFormatNumber($dinvoice_id, (3 == $sinvoice_type || 4 == $sinvoice_type) ? $sinvoice['revision_id'] : null, $sinvoice['site_id'])?></td>
				<td class="box_normal" style="text-align:center;"><?=date('jS F, Y', ($sinvoice_type < 3) ? time() : $sinvoice['open_time'])?></td>
			</tr>
		</table>
	</td>
</tr></table>
<br /><br />
<?php
$contacts_list = array();
//if($sinvoice['bill_address_full']!='')
//    $contacts_list['BILL TO']=$sinvoice['bill_address_full'];
if ('' != $sinvoice['ship_address_full']) {
    $contacts_list['SHIP TO'] = $sinvoice['ship_address_full'];
}

if ('' != $sinvoice['notify_address']) {
    $contacts_list['NOTIFY PARTY'] = $sinvoice['notify_address_full'];
}
//."\n".$sinvoice['notify_email'];
$contacts_layout = array_chunk(array_keys($contacts_list), 2);
$col_width       = ($page_info['width'] - ($page_info['margin'] * 2) + 1) / 2 - 1;

//Contacts
foreach ($contacts_layout as $row) {
?>
<table style="width:100%;">
	<tr>
		<td class="box_header" style="width:50%;"><?=htmlspecialchars($row[0])?></td>
		<td<?=('' != $row[1]) ? ' class="box_header"' : ''?> style="width:50%;"><?=htmlspecialchars($row[1])?></td>
	</tr>
	<tr>
		<td class="box_normal"><?=nl2br(htmlspecialchars($contacts_list[$row[0]]))?></td>
		<td<?=('' != $row[1]) ? ' class="box_normal"' : ''?>><?=nl2br(htmlspecialchars($contacts_list[$row[1]]))?></td>
	</tr>
</table>
<br /><br />
<?php
}

$temp = array();
if ('' != $payment_data['payment_method_name']) {
    $temp['PAYMENT TYPE'] = $payment_data['payment_method_name'];
}

if ('' != $sinvoice['proforma_purchaseorder']) {
    $temp['PURCHASE ORDER'] = $sinvoice['proforma_purchaseorder'];
}

if ('' != $terms_list[$sinvoice['terms_id']]) {
    $temp['SHIPPING TERMS'] = $terms_list[$sinvoice['terms_id']];
}

if ('' != $sinvoice['ship_supplier_name']) {
    $temp['SHIP VIA'] = $sinvoice['ship_supplier_name'];
}

if ('' != $sinvoice['ship_account_num']) {
    $temp['SHIP ACCOUNT'] = $sinvoice['ship_account_num'];
}

if ('' != $sinvoice['proforma_tracknumber']) {
    $temp['TRACKING #'] = $sinvoice['proforma_tracknumber'];
}

if ('' != $all_staff_list[$sinvoice['handler_id']]) {
    $temp['SALES REP'] = $all_staff_list[$sinvoice['handler_id']];
}

if (count($temp) > 0) {
?>
<table style="width:100%;">
	<tr>
<?php
    foreach ($temp as $key => $value) {
        echo '		<td class="box_header" style="text-align:center;">' . htmlspecialchars($key) . '</td>' . "\n";
    }

?>
	</tr>
	<tr>
<?php
    foreach ($temp as $key => $value) {
        echo '		<td class="box_normal" style="text-align:center;">' . nl2br(htmlspecialchars($value)) . '</td>' . "\n";
    }

?>
	</tr>
</table>
<br /><br />
<?php
}

?>
</thead>
<tfoot style="display:table-footer-group;">
</tfoot>
<tbody>
	<tr>
		<td class="box_header" style="text-align:center;width:0.8in;">ITEM ID</td>
		<td class="box_header" style="text-align:center;">ITEM NAME</td>
		<td class="box_header" style="text-align:center;width:0.8in;">QTY</td>
		<td class="box_header" style="text-align:center;width:0.8in;">PRICE EACH</td>
		<td class="box_header" style="text-align:center;width:0.8in;">AMOUNT</td>
	</tr>
<?php
if (is_array($sinvoice_products)) {
    foreach ($sinvoice_products as $product) {
        if ('c' != $product['generic']) {
            $product['product_offer'] = convertCurrency($product['product_offer'], $sinvoice['invoice_currency']);
            $product['product_total'] = convertCurrency($product['product_qty'] * $product['product_offer'], $sinvoice['invoice_currency']);
?>
	<tr>
		<td class="box_product"><?=$product['product_id']?></td>
		<td class="box_product" style="text-align:left;">
			<?=htmlspecialchars($product['product_name'])?>
			<?=('' != $product['product_notes']) ? ('<div style="font-style:italic;padding-left:12px;">' . nl2br(htmlspecialchars($product['product_notes'])) . '</div>') : ''?>
		</td>
		<td class="box_product"><?=$product['product_qty']?></td>
		<td class="box_product"><?=$product['product_offer']?></td>
		<td class="box_product"><?=$product['product_total']?></td>
	</tr>
<?php
        }
    }
}

if (0 != $sinvoice['ship_id']) {
?>
	<tr>
		<td class="box_product"><?=(('y' == $sinvoice['ship_insurance']) ? 'N' : 'F') . str_pad($sinvoice['ship_id'], 2, '0', STR_PAD_LEFT)?></td>
		<td class="box_product" style="text-align:left;">
			<?=htmlspecialchars($sinvoice['ship_product_name'])?>
			<?=('' != $sinvoice['ship_product_notes']) ? ('<div style="font-style:italic;padding-left:12px;">' . nl2br(htmlspecialchars($sinvoice['ship_product_notes'])) . '</div>') : ''?>
		</td>
		<td class="box_product"></td>
		<td class="box_product"><?=convertCurrency($sinvoice['ship_offer'], $sinvoice['invoice_currency'])?></td>
		<td class="box_product"><?=convertCurrency($sinvoice['ship_offer'], $sinvoice['invoice_currency'])?></td>
	</tr>
<?php
}

$sub_total = $sinvoice['invoice_offer'];
$gst_offer = 0;

if (is_array($sinvoice_charges)) {
    foreach ($sinvoice_charges as $charge) {
        if (1 != $charge['charge_id']) {
            ; /* Do not display GST here */
?>
	<tr>
		<td class="box_product">C<?=str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT)?></td>
		<td class="box_product" style="text-align:left;">
			<?=htmlspecialchars($charges_list[$charge['charge_id']])?>
		</td>
		<td class="box_product"></td>
		<td class="box_product"><?=convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency'])?></td>
		<td class="box_product"><?=convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency'])?></td>
	</tr>
<?php
        } else {
            $gst_offer += convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency']);
        }
    }
}
$sub_total -= $gst_offer;
?>
<!--	<tr><td class="box_product" style="height:100%;"></td><td class="box_product"></td><td class="box_product"></td><td class="box_product"></td><td class="box_product"></td></tr> -->
<?php
$temp                = array();
$temp['Sale Amount'] = convertCurrency($sub_total, $sinvoice['invoice_currency']);
if (0 != $gst_offer) {
    $temp['GST 10%'] = convertCurrency($gst_offer, $sinvoice['invoice_currency']);
}

$temp['Total']    = convertCurrency($sinvoice['invoice_offer'], $sinvoice['invoice_currency']);
$temp['Currency'] = $sinvoice['invoice_currency'];
reset($temp);
?>
	<tr>
		<td class="box_normal" colspan="3" rowspan="<?=count($temp)?>">
			<?=('' != $sinvoice['invoice_comments']) ? nl2br(htmlspecialchars($sinvoice['invoice_comments'])) : ''?>
			<?=(0 != $sinvoice['waranty_terms_id']) ? ("<br />\n" . 'Warranty Terms: ' . nl2br(htmlspecialchars($warranty_terms_list[$sinvoice['waranty_terms_id']]))) : ''?>
		</td>
		<td class="box_header" style="text-align:center;"><?=htmlspecialchars(key($temp))?></td>
		<td class="box_normal" style="text-align:center;"><?=htmlspecialchars(current($temp))?></td>
	</tr>
<?php
array_shift($temp);
foreach ($temp as $key => $value) {
?>
	<tr>
		<td class="box_header" style="text-align:center;"><?=htmlspecialchars($key)?></td>
		<td class="box_normal" style="text-align:center;"><?=htmlspecialchars($value)?></td>
	</tr>
<?php
}
?>
	<tr><td colspan="5" class="no_break" style="padding:0px;">
<br /><br />

<table style="width:100%;"><tr>
	<td style="text-align:left;vertical-align:bottom;width:100%;">
<?php

if ('' != $sinvoice['paymenttype_id']) {
?>
		Payment Information:<br />
<?php
    switch ($sinvoice['paymenttype_id']) {
        case 'BPAY':
?>
		<div style="position:relative;left:47px;top:5px;z-index:5;">
			<table style="position:absolute;width:154px;height:62px;"><tr><td style="vertical-align:middle;padding:12px;">
				Biller Code: 424606<br />
				Ref: <?=$sinvoice_reference?>
			</td></tr></table>
		</div>
		<?=('' != $images['payment_method']) ? ('<img src="' . $images['payment_method'] . '" alt="" /><br />') : ''?>
<?php
            break;
        default:
?>
		<?=('' != $images['payment_method']) ? ('<img src="' . $images['payment_method'] . '" alt="" /><br />') : ''?>
<?php
            echo "<table>\n";
            foreach (explode("\n", trim($payment_data['payment_method_details'])) as $row) {
                $row = explode(":", $row);
                echo "<tr>\n";
                if (count($row) > 1) {
                    $output = array_shift($row);
                    echo "<td>" . htmlspecialchars($output) . (('' == $output) ? '' : ':') . "</td><td>\n";
                } else {
                    echo '<td colspan="2">' . "\n";
                }

                echo htmlspecialchars(implode(':', $row)) . "</td>\n</tr>\n";
            }
            echo "</table>\n";
            break;
    }
}
?>
	</td>
	<td style="text-align:right;vertical-align:bottom;">
		<table style="width:2in;height:1in;"><tr><td style="text-align:right;vertical-align:middle;<?=('' != $images['stamp']) ? ("background:#FFFFFF url('" . $images['stamp'] . "') no-repeat scroll bottom right;") : ''?>">
			FOR <?=htmlspecialchars(strtoupper($sinvoice_labels['company_name']))?> PTY LTD<br />
			<img src="<?=$images['signature']?>" alt="" style="height:0.5in;" /><br />
			Authorised Signatory
		</td></tr></table>
	</td>
</tr></table>

	</td></tr>
</tbody>

</table></body>
</html>

<?php
$sinvoice_buffer = ob_get_clean();
