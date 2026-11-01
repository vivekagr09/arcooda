<?php
global $_GLOBALS;

ob_start();

$warranty_terms_list = invoiceGetOptionList('warranty_terms_id');
$staff               = (array_key_exists('staff', $_GLOBALS)) ? $_GLOBALS['staff'] : array();
$payment_data        = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($invoice['site_id']) . " AND payment_method_id=" . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$invoice_labels      = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE company_id=" . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_info        = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE id=" . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

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

if (!isset($staff_signature)) {
    $staff_signature = true;
}

$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$images['signature']      = 'images/sigs/' . $staff['signature'];
if (in_array($invoice['invoice_status'], array('inquiry', 'closed'))) {
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

if (!$staff_signature) {
    $images['signature'] = '';
}

$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']))));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], $invoice_id), 5));
}

$invoice_type      = array_search($invoice['invoice_status'], array(
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
$invoice_type_text = 'QUOTATION';
if ($invoice_type > 2) {
    $invoice_type_text = strtoupper($invoice['invoice_status']) . ' INVOICE';
}

if (in_array($invoice['invoice_status'], array('sales', 'processed'))) {
    if (20 != $invoice['site_id'] && 26 != $invoice['site_id'] && 'AU' == $invoice['ship_country'] && 'AU' == $invoice['bill_country']) {
        $invoice_type_text = 'TAX INVOICE';
    } else {
        $invoice_type_text = 'SALES INVOICE';
    }
}
if (9 == $invoice_type) {
    $invoice_type_text = 'PURCHASE SUMMARY';
}

$time_to_use = $invoice['open_time'];
if ($invoice_type < 3) {
    $time_to_use = time();
} elseif ($invoice['proforma_time'] > 0) {
    $time_to_use = $invoice['proforma_time'];
}

if (0 != $invoice_time) {
    $time_to_use = $invoice_time;
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?=(9 == $invoice_type) ? 'Purchase Summary' : (($invoice_type < 3) ? 'Quotation' : 'Invoice')?> <?=invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type || 9 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id'])?></title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
		<style type="text/css">
/*<![CDATA[*/
.table-bordered th{background-color:#448;color:#fff}
/*]]>*/
		</style>
		<style type="text/css">
/*<![CDATA[*/
.invoice *{font:8pt verdana;border-collapse:collapse}
.no_break, .no_break *{page-break-inside:avoid}
.invoice .box_header{
<?php
switch ($invoice['site_id']) {
    case 1:
        echo "	background-color:#0023F4;\n	color:#FFFFFF;\n";
        break;
    case 2:
        echo "	background-color:#1E901D;\n	color:#FFFFFF;\n";
        break;
    case 3:
        echo "	background-color:#99CCFF;\n	color:#000000;\n";
        break;
    case 9:
        echo "	background-color:#454B84;\n	color:#FFFFFF;\n";
        break;
    case 16:
        echo "	background-color:#293B4E;\n	color:#E2EEFA;\n";
        break;
    case 18:
        echo "	background-color:#0C9544;\n	color:#FFFFFF;\n";
        break;
    case 19:
        echo "	background-color:#3cb54c;\n	color:#FFFFFF;\n";
        break;
    case 20:
        echo "	background-color:#029e4e;\n	color:#FFFFFF;\n";
        break;
    case 22:
        echo "	background-color:#B8317D;\n	color:#FFFFFF;\n";
        break;
    case 24:
        echo "	background-color:#8FBE3C;\n	color:#000000;\n";
        break;
    case 30:
        echo "	background-color:#1B75BB;\n	color:#FFFFFF;\n";
        break;
    case 39:
        echo "	background-color:#068DBD;\n	color:#FFFFFF;\n";
        break;
    default:
        echo "	background-color:#777777;\n	color:#000000;\n";
        break;
}
?>
}
.invoice .box_shaded{background-color:#777777;color:#FFFFFF}
.invoice .box_normal,.invoice .box_header,.invoice .box_shaded{padding:4px;border:1px solid #000000}
.invoice .box_product{padding:4px;text-align:center;vertical-align:middle;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px dashed #000000}
/*]]>*/
		</style>
	</head>
	<body><div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-3 pull-right">
				<h1 style="margin:0;"><?=htmlspecialchars(_($invoice_type_text))?></h1>
				<table class="table table-bordered">
					<tr>
						<th><?=htmlspecialchars(($invoice_type < 3) ? _('Quotation #') : _('Invoice #'))?></th>
						<th><?=htmlspecialchars(_("Date"))?></th>
					</tr>
					<tr>
						<td><?=htmlspecialchars($formatted_invoice_number)?></td>
						<td><?=htmlspecialchars(date(_('jS F, Y'), $time_to_use))?></td>
					</tr>
				</table>
			</div>
			<div class="col-xs-12 col-sm-7 col-md-8 col-lg-9">
				<img src="http://www.highwaygroup.net/webadmin/images/sites/site_9/logo_invoice.png" /><br />
				44-48 Maitland Road, Mayfield East 2304, NSW, Australia<br />
				Ph: 02 4968 9313, Fax: 02 4968 9314<br />
				Email: sales@arcooda.com, A.B.N 16 114 939 285<br />
			</div>
		</div>
<?php
if (count($invoice_contacts)) {
?>
		<div class="row">
<?php
    foreach ($invoice_contacts as $k => $v) {
?>
			<div class="col-sm-6 col-md-4"><table class="table table-bordered">
				<tr><th><?=htmlspecialchars(_($k))?></th></tr>
				<tr><td><?=nl2br(htmlspecialchars($v))?></td></tr>
			</table></div>
<?php
    }
?>
		</div>
<?php
}
if (count($invoice_inforow)) {
?>
		<div class="row"><div class="col-md-12"><table class="table table-bordered">
			<tr>
<?php
    foreach ($invoice_inforow as $k => $v) {
?>
				<th><?=htmlspecialchars(_($k))?></th>
<?php
    }
?>
			</tr>
			<tr>
<?php
    foreach ($invoice_inforow as $k => $v) {
?>
				<td><?=htmlspecialchars(_($v))?></td>
<?php
    }
?>
			</tr>
		</table></div></div>
<?php
}
?>
		<div class="row"><div class="col-md-12"><table class="table table-bordered">
			<thead>
				<tr>
					<?=(9 == $invoice_type) ? '<th>SUPPLIER</th>' : ''?>
					<th>ITEM ID</th>
					<th>ITEM NAME</th>
					<th>QTY</th>
					<th>PRICE EACH</th>
					<?=(9 == $invoice_type) ? '<th>PRICE EACH (AUD)</th>' : ''?>
					<th>AMOUNT</th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
<?php
foreach ($invoice_products as $product) {
    if ('c' != $product['generic']) {
        if ('' != $product['generic_code'] && '0' == $product['product_id']) {
            $product['product_id'] = $product['generic_code'];
        }

        if (9 == $invoice_type) {
            $product['product_offer'] = convertCurrency($product['product_cost'], $invoice['invoice_currency']);
        } else {
            $product['product_offer'] = convertCurrency($product['product_offer'], $invoice['invoice_currency']);
        }

        $product['product_total'] = convertCurrency($product['product_qty'] * $product['product_offer'], $invoice['invoice_currency']);
?>
				<tr>
					<?=(9 == $invoice_type) ? ('<td class="box_product">' . htmlspecialchars($product['supplier_name']) . '</td>') : ''?>
					<td class="box_product"><?=$product['product_id']?></td>
					<td class="box_product" style="text-align:left;">
						<?=htmlspecialchars($product['product_name'])?>
						<?=('' != $product['product_notes'] && 9 != $invoice_type) ? ('<div style="font-style:italic;padding-left:12px;">' . nl2br(htmlspecialchars($product['product_notes'])) . '</div>') : ''?>
						<?=('' != $product['supplier_notes'] && 9 == $invoice_type) ? ('<div style="font-style:italic;padding-left:12px;">' . nl2br(htmlspecialchars($product['supplier_notes'])) . '</div>') : ''?>
					</td>
					<td class="box_product"><?=$product['product_qty']?></td>
					<td class="box_product"><?=$product['product_offer']?></td>
					<?=(9 == $invoice_type) ? ('<td class="box_product">' . convertCurrency($product['product_costau'], 'AUD') . '</td>') : ''?>
					<td class="box_product"><?=$product['product_total']?></td>
				</tr>
<?php
    }
}

?>
			</tbody>
		</table></div></div>
	</div>


<div style="position:relative;">
<table align="center" class="invoice" style="border-collapse:collapse;width:98%;">
<thead style="display:table-header-group;">
	<tr><td colspan="<?=(9 == $invoice_type) ? 7 : 5?>" style="padding:0px;">
<table style="width:100%;"><tr>
<?php
$temp = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader']));
if ('AU' == $invoice['bill_country'] && 'AU' == $invoice['ship_country']) {
    $temp = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader_au']));
}

if ('' != $invoice_diagonal) {
?>
	<td colspan="2" style="height:155px;font-size:65px;text-align:center;font-family:Verdana;"><?=$invoice_diagonal?></td>
<?php
} else {
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

		</td></tr></table>
		<br /><br />
	</td>
<?php
}
?>
</tr></table>
</thead>
<tbody>
<?php
if (0 != $invoice['ship_id'] && 9 != $invoice_type) {
    $row = $DB->query_first("SELECT type FROM `highwaygroup`.`freight_services` WHERE id=" . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
?>
	<tr>
		<td class="box_product"><?=sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : "")?></td>
		<td class="box_product" style="text-align:left;">
			<?=htmlspecialchars($invoice['ship_product_name'])?>
			<?=('' != $invoice['ship_product_notes']) ? ('<div style="font-style:italic;padding-left:12px;">' . nl2br(htmlspecialchars($invoice['ship_product_notes'])) . '</div>') : ''?>
		</td>
		<td class="box_product"></td>
		<td class="box_product"><?=convertCurrency($invoice['ship_offer'], $invoice['invoice_currency'])?></td>
		<td class="box_product"><?=convertCurrency($invoice['ship_offer'], $invoice['invoice_currency'])?></td>
	</tr>
<?php
}

$sub_total = $invoice['invoice_offer'];
$gst_offer = 0;

if (is_array($invoice_charges) && 9 != $invoice_type) {
    foreach ($invoice_charges as $charge) {
        /* Do not display GST here */
        if (1 != $charge['charge_id']) {
?>
	<tr>
		<td class="box_product">C<?=str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT)?></td>
		<td class="box_product" style="text-align:left;">
			<?=htmlspecialchars($charge['charge_name2'])?>
		</td>
		<td class="box_product"></td>
		<td class="box_product"><?=convertCurrency($charge['charge_offer'], $invoice['invoice_currency'])?></td>
		<td class="box_product"><?=convertCurrency($charge['charge_offer'], $invoice['invoice_currency'])?></td>
	</tr>
<?php
        } else {
            $gst_offer += convertCurrency($charge['charge_offer'], $invoice['invoice_currency']);
        }
    }
}
$sub_total -= $gst_offer;
?>
<!--	<tr><td class="box_product" style="height:100%;"></td><td class="box_product"></td><td class="box_product"></td><td class="box_product"></td><td class="box_product"></td></tr> -->
<?php
$temp = array();
if (9 == $invoice_type) {
    $temp['Total'] = convertCurrency($invoice['products_cost'], $invoice['invoice_currency']);
} else {
    $temp['Sale Amount'] = convertCurrency($sub_total, $invoice['invoice_currency']);
    if (0 != $gst_offer) {
        $temp['GST 10%'] = convertCurrency($gst_offer, $invoice['invoice_currency']);
    }

    $temp['Total'] = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']);
}
$temp['Currency'] = $invoice['invoice_currency'];
reset($temp);
?>
	<tr>
		<td class="box_normal" colspan="<?=(9 == $invoice_type) ? 5 : 3?>" rowspan="<?=count($temp)?>">
			<?=('' != $invoice['supply_notes'] && 9 == $invoice_type) ? nl2br("Remarks:\n" . htmlspecialchars($invoice['supply_notes'])) : ''?>
			<?=('' != $invoice['invoice_comments'] && 9 != $invoice_type) ? nl2br(htmlspecialchars($invoice['invoice_comments'])) : ''?>
			<?=(0 != $invoice['waranty_terms_id'] && 9 != $invoice_type) ? ("<br />\n" . 'Warranty Terms: ' . nl2br(htmlspecialchars($warranty_terms_list[$invoice['waranty_terms_id']]))) : ''?>
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
	<tr><td colspan="<?=(9 == $invoice_type) ? 7 : 5?>" class="no_break" style="padding:0px;">
<br /><br />

<table style="width:100%;"><tr>
	<td style="text-align:left;vertical-align:bottom;width:100%;">
<?php
if ('' != $invoice['paymenttype_id'] && 9 != $invoice_type) {
?>
		Payment Information:<br />
<?php
    switch ($invoice['paymenttype_id']) {
        case 'BPAY':
?>
		<div style="position:relative;left:47px;top:5px;z-index:5;">
			<table style="position:absolute;width:154px;height:62px;"><tr><td style="vertical-align:middle;padding:12px;">
				Biller Code: 424606<br />
				Ref: <?=$invoice_reference?>
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
<?php
if (9 != $invoice_type) {
?>
		<table style="width:2in;height:1in;"><tr><td style="text-align:right;vertical-align:middle;<?=('' != $images['stamp']) ? ("background:#FFFFFF url('" . $images['stamp'] . "') no-repeat scroll bottom right;") : ''?>">
			FOR <?=htmlspecialchars(strtoupper($invoice_labels['company_name']) . ((20 != $invoice["site_id"]) ? ' PTY' : ''))?> LTD<br />
			<img src="<?=$images['signature']?>" alt="" style="height:0.5in;" /><br />
			Authorised Signatory
		</td></tr></table>
<?php
}
?>
	</td>
</tr></table>

	</td></tr>
</tbody>

</table>
<?php
if ('' != $invoice_diagonal && false) {
    foreach (str_split($invoice_diagonal, 1) as $pos => $letter) {
        if ('' != $letter) {
?>
<div style="position:absolute;font:105pt monospace;left:<?=floor($pos * 95 / strlen($invoice_diagonal))?>%;bottom:<?=floor($pos * 90 / strlen($invoice_diagonal))?>%;"><?=$letter?></div>
<?php
        }
    }
}
?>
</div></body>
</html>

<?php
$invoice_buffer = ob_get_clean();
