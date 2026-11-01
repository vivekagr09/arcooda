<?php
moduleLoad('invoice');

include_once __DIR__ . '/../classes/MyShape.php';
include_once __DIR__ . '/../classes/TextBox.php';

$allow_shades = true;
$page_info    = array( //Page size in inches
    'width'       => 7.26,
    'height'      => 10.69,
    'margin'      => 0.0,
    'row_spacing' => 0.25,
    'col_spacing' => 0.25,
);
$dpi = 89; //Dots per inch
//$dpi=96;

//Convert dimensions to pixels
foreach ($page_info as $key => $value) {
    $page_info[$key] = $value * $dpi;
}

//-----------------------------------------------------------------------------
//This part should be the same as html_invoice.tpl.php

global $_GLOBALS;

$charges_list = array();
$query        = $DB->query("SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name`");
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}

$terms_list          = getTermsList();
$warranty_terms_list = getWarrantyTermsList();
$payment_data        = $DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$invoice_labels      = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
$website_info        = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

//$website_url=$website_info['url'];
//$website_path=$website_info['root_dir'];
$website_url  = 'https://' . $_SERVER['SERVER_NAME'] . '/';
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';
if (substr($website_url, -1) != '/') {
    $website_url .= '/';
}

if (substr($website_path, -1) != '/') {
    $website_path .= '/';
}

$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$images['signature']      = 'sigs/' . $_GLOBALS['staff']['signature'];
if (in_array($invoice['invoice_status'], array('enquiry', 'inquiry', 'closed'))) {
    $images['signature'] = '';
}

if (!is_null($force_signature)) {
    $images['signature'] = 'sigs/' . $force_signature;
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

$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']))));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], 0), 5));
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
    $invoice_type_text = 'SALES INVOICE';
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

//-----------------------------------------------------------------------------

$buffer = imagecreate($page_info['width'], $page_info['height']);
imageantialias($buffer, false);

if ($allow_shades) {
    imagecolorallocate($buffer, 6 * 17, 8 * 17, 11 * 17);
}

$white  = imagecolorallocate($buffer, 255, 255, 255);
$black  = imagecolorallocate($buffer, 0, 0, 0);
$yellow = imagecolorallocate($buffer, 255, 255, 0);

$shades    = array();
$shades[0] = $black;
for ($i = 1; $i < 15; $i++) {
    if ($allow_shades) {
        $shades[$i] = imagecolorallocate($buffer, $i * 17, $i * 17, $i * 17);
    } else {
        $shades[$i] = ($i < 13) ? $black : $white;
    }
}

$shades[15] = $white;
$grey       = $shades[14];

$head_back = $grey;
$head_text = $black;

imagefilledrectangle($buffer, 0, 0, $page_info['width'], $page_info['height'], $white);

if (1 == $invoice['site_id']) {
    $head_back = imagecolorallocate($buffer, 0, 35, 244);
    $head_text = $white;
}

if (2 == $invoice['site_id']) {
    $head_back = imagecolorallocate($buffer, 0, 201, 0);
    $head_text = $white;
}

if (3 == $invoice['site_id']) {
    $head_back = imagecolorallocate($buffer, 153, 205, 255);
}

if (9 == $invoice['site_id']) {
//    $head_back=imagecolorallocate($buffer,0,109,255);
    $head_back = imagecolorallocate($buffer, 69, 75, 132);
    $head_text = $white;
}

if (16 == $invoice['site_id']) {
    $head_back = imagecolorallocate($buffer, 41, 59, 78);
    $head_text = imagecolorallocate($buffer, 226, 238, 250);
}

if ($allow_shades) {
    $unfinished = imagecolorallocate($buffer, 255, 255, 0);
} else {
    $unfinished = $black;
}

$boxes = array();

//-----------------------------------------------------------------------------
//Start invoice title/date

$xpos1 = $page_info['margin'];
$ypos1 = $page_info['margin']; //Position of left stuff
$xpos2 = $page_info['width'] - $page_info['margin'];
$ypos2 = $page_info['margin']; //Position of right stuff

$time_to_use = $invoice['open_time'];
if ($invoice_type < 3) {
    $time_to_use = time();
} elseif ($invoice['proforma_time'] > 0) {
    $time_to_use = $invoice['proforma_time'];
}

if ($invoice_time > 0) {
    $time_to_use = $invoice_time;
}

$temp           = array();
$temp[]         = new TextBox('DATE', $head_back, $head_text, $black);
$temp[]         = new TextBox(date('jS F, Y', $time_to_use), $white, $black, $black);
$temp[]         = new TextBox(($invoice_type < 3) ? 'QUOTATION #' : 'INVOICE #', $head_back, $head_text, $black);
$invoice_number = invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']);
if ('shipview' == $action) {
    $invoice_number = 'S' . substr($invoice_number, 1);
}

$temp[]     = new TextBox($invoice_number, $white, $black, $black);
$min_width  = 0;
$min_height = 0;
foreach ($temp as $box) {
    $min_width  = max($min_width, $box->width);
    $min_height = max($min_height, $box->height);
}
$count = 0;

$box = new TextBox(($invoice_type > 2) ? (strtoupper($invoice['invoice_status']) . ' INVOICE') : 'QUOTATION', $yellow, $black, $black);
if (in_array($invoice['invoice_status'], array('sales', 'processed'))) {
    /*if($invoice_id==3637)
    $box->setText('PROFORMA INVOICE');
     */
    $box->setText('SALES INVOICE');
}
$box->setMinSize(2 * $min_width + 1, 0);
$box->setMaxSize(2 * $min_width + 1, 0);
$box->text_font = '/home/common/fonts/Verdana Bold.ttf';
$box->setFontSize(12);
$box->setPosition($xpos2 - $box->width, $ypos2);

//Size tweek
$box->text_y += 4;
$box->height = max(32, $box->height);
$box->y2     = $box->y1 + $box->height - 1;

$xpos1 = $box->x1;
$ypos2 += $box->height + $page_info['row_spacing'];
$boxes[] = $box;

foreach ($temp as $key => $box) {
    $temp[$key]->setMinSize($min_width + 1, $min_height + 1);
    $temp[$key]->setPosition($xpos2 - (intval($count / 2) + 1) * $min_width - 1, $ypos2 + ($count & 1) * $min_height - 1);
    $count++;
}
foreach ($temp as $key => $box) {
    $boxes[] = $temp[$key];
    $xpos1   = min($xpos1, $temp[$key]->x1);
    $ypos2   = max($ypos2, $temp[$key]->y2);
}

$xpos2 = $xpos1 - $page_info['col_spacing'];
$xpos1 = $page_info['margin'];

$temp      = explode("\n", $invoice_labels['company_invoiceheader']);
$logo_dir  = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/sites/site_' . $invoice['site_id'] . '/';
$logo_file = $logo_dir . 'logo_invoice.png';

if (is_file($logo_file)) {
    $logo_image  = imagecreatefromstring(file_get_contents($logo_file));
    $from_width  = imagesx($logo_image);
    $from_height = imagesy($logo_image);
    imagecopy($buffer, $logo_image, $xpos1, $ypos1, 0, 0, $from_width, $from_height);
	if (function_exists('imagedestroy')) {
        imagedestroy($logo_image);
    }
    $logo_image = false;
    $ypos1 += $from_height + $page_info['row_spacing'];
/*
$scale1=($xpos2-$xpos1)/(3*$from_height);
$scale2=($xpos2-$xpos1)/($from_width);
$scale=min(1,min($scale1,$scale2));
imagecopyresampled($buffer, $logo_image, $xpos1, $ypos1, 0, 0, $from_width*$scale, $from_height*$scale, $from_width, $from_height);
$ypos1+=$from_height*$scale;
 */
    //array_shift($temp);
} else {
    $box = new TextBox(array_shift($temp), '', $black, '', $xpos2 - $xpos1, -1, 0);
    $box->setPadding(0);
    $box->setFontSize(24);
    $box->setPosition($xpos1, $ypos1);
    $ypos1 += $box->height + 4;
    $boxes[] = $box;
}

//$box=new TextBox(implode("\n",$temp),'',$black,'',$xpos2-$xpos1,-1,-1);
//$box->setPadding(0);
//$box->setPosition($xpos1,$ypos1);
//$ypos1+=$box->height;
//$boxes[]=$box;

$ypos1 = $ypos2 = max($ypos1, $ypos2) + $page_info['row_spacing'];
$xpos1 = $page_info['margin'];
$xpos2 = $page_info['width'] - $page_info['margin'];

//End invoice title/date
//-----------------------------------------------------------------------------
//Start Invoice addresses

$contacts_list = array();
if (9 == $invoice_type) {
    if ('' != $invoice['bill_address_full']) {
        $contacts_list['SOLD TO'] = $invoice['bill_address_full'];
    }

} else {
    if ('' != $invoice['bill_address_full']) {
        $contacts_list['BILL TO'] = $invoice['bill_address_full'];
    }

    if ('' != $invoice['ship_address_full']) {
        $contacts_list['SHIP TO'] = $invoice['ship_address_full'];
    }

    if ('' != $invoice['notify_address_full'] && 'shipview' == $action) {
        $contacts_list['NOTIFY PARTY'] = $invoice['notify_address_full'];
    }
//."\n".$invoice['notify_email'];
}
$contacts_layout = array_chunk(array_keys($contacts_list), 2);

$min_width = $max_width = ($page_info['width'] - ($page_info['margin'] * 2) + 1) / 2;
foreach ($contacts_layout as $row) {
    $box1 = new TextBox($row[0], $head_back, $head_text, $black, $max_width, -1, 0);
    $box1->setMinSize($min_width, 0);
    if ('' != $row[1]) {
        $box2 = new TextBox($row[1], $head_back, $head_text, $black, $max_width, -1, 0);
        $box2->setMinSize($min_width, 0);
        $row_height = max($box1->height, $box2->height);
        $box1->setMinSize($min_width, $row_height);
        $box2->setMinSize($min_width, $row_height);
        $box2->setPosition($xpos2 - $box2->width, $ypos2);
        $boxes[] = $box2;
    }
    $box1->setPosition($xpos1, $ypos1);
    $boxes[] = $box1;

    $ypos1 = $ypos2 += $box1->height - 1;

    $box1 = new TextBox($contacts_list[$row[0]], $white, $black, $black, $max_width, -1, -1);
    $box1->setMinSize($min_width, 0);
    if ('' != $row[1]) {
        $box2 = new TextBox($contacts_list[$row[1]], $white, $black, $black, $max_width, -1, -1);
        $box2->setMinSize($min_width, 0);
        $row_height = max($box1->height, $box2->height);
        $box1->setMinSize($min_width, $row_height);
        $box2->setMinSize($min_width, $row_height);
        $box2->setPosition($xpos2 - $box2->width, $ypos2);
        $boxes[] = $box2;
    }
    $box1->setPosition($xpos1, $ypos1);
    $boxes[] = $box1;

    $ypos1 = $ypos2 += $box1->height + $page_info['row_spacing'];
}

//End Invoice addresses
//-----------------------------------------------------------------------------
//Start Invoice Options

$temp = array();
$change_array = array('3693', '3929', '3861');
if (in_array($invoice_id, $change_array)) {
    $invoice['site_id'] = 1;
}

if ('' != $invoice['paymenttype_id']) {
    $row = $DB->query_first("SELECT `payment_method_name` FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
    if ('' != $row['payment_method_name']) {
        $temp[] = array(
            new TextBox('PAYMENT TYPE', $head_back, $head_text, $black),
            new TextBox($row['payment_method_name'], $white, $black, $black),
        );
    }

}
if ('' != $invoice['proforma_purchaseorder']) {
    $temp[] = array(
        new TextBox('PURCHASE ORDER', $head_back, $head_text, $black),
        new TextBox($invoice['proforma_purchaseorder'], $white, $black, $black),
    );
}

$terms_list = getTermsList();
if ('' != $terms_list[$invoice['terms_id']]) {
    $temp[] = array(
        new TextBox('SHIPPING TERMS', $head_back, $head_text, $black),
        new TextBox($terms_list[$invoice['terms_id']], $white, $black, $black),
    );
} elseif ('' != $terms_list[$invoice['def_terms']]) {
    $temp[] = array(
        new TextBox('SHIPPING TERMS', $head_back, $head_text, $black),
        new TextBox($terms_list[$invoice['def_terms']], $white, $black, $black),
    );
}

$supply_country_list = invoiceGetOptionList('supply_country');
if ('' != $invoice['supply_country']) {
    $temp[] = array(
        new TextBox('SHIPS FROM', $head_back, $head_text, $black),
        new TextBox($supply_country_list[$invoice['supply_country']], $white, $black, $black),
    );
}

if ('' != $invoice['ship_supplier_name']) {
    $temp[] = array(
        new TextBox('SHIP VIA', $head_back, $head_text, $black),
        new TextBox($invoice['ship_supplier_name'], $white, $black, $black),
    );
}

if ('' != $invoice['ship_account_num']) {
    $temp[] = array(
        new TextBox('SHIP ACCOUNT', $head_back, $head_text, $black),
        new TextBox($invoice['ship_account_num'], $white, $black, $black),
    );
}

if ('' != $invoice['proforma_tracknumber']) {
    $temp[] = array(
        new TextBox('TRACKING #', $head_back, $head_text, $black),
        new TextBox($invoice['proforma_tracknumber'], $white, $black, $black),
    );
}

if ('' != $all_staff_list[$invoice['handler_id']]) {
    $temp[] = array(
        new TextBox('SALES REP', $head_back, $head_text, $black),
        new TextBox($all_staff_list[$invoice['handler_id']], $white, $black, $black),
    );
}

$min_height = 0;
foreach ($temp as $col) {
    $min_height = max($min_height, $col[0]->height);
}

foreach ($temp as $key => $col) {
    $temp[$key][0]->setMinSize($col[0]->width, $min_height);
    $temp[$key][1]->setMinSize($col[0]->width, $min_height);
}
$ypos2 += $min_height - 1;

$old_total   = 0;
$total_width = 0 - count($temp);
$loose_width = 0 - count($temp);
foreach ($temp as $col) {
    $total_width += max($col[0]->width, $col[1]->width);
    $loose_width += max($col[0]->width - $col[0]->min_width, $col[1]->width - $col[1]->min_width);
}
$fixed_width = $total_width - $loose_width;

$count = 0;
while ($total_width > ($xpos2 - $xpos1) && $total_width != $old_total && $count < 10) {
    $count++;
    $old_total = $total_width;

    $scale = ($xpos2 - $xpos1 - $fixed_width) / ((0 == $loose_width) ? 1 : $loose_width);
    foreach ($temp as $key => $col) {
        $width = (max($col[0]->width, $col[1]->width) - $col[0]->min_width) * $scale + $col[0]->min_width;
        $temp[$key][0]->setMaxSize($width);
        $temp[$key][1]->setMaxSize($width);
    }

    $total_width = 0 - count($temp);
    $loose_width = 0 - count($temp);
    foreach ($temp as $col) {
        $total_width += max($col[0]->width, $col[1]->width);
        $loose_width += max($col[0]->width - $col[0]->min_width, $col[1]->width - $col[1]->min_width);
    }
    $fixed_width = $total_width - $loose_width;
}

$add_width = (($xpos2 - $xpos1) - $total_width - 1) / max(1, count($temp));
foreach ($temp as $key => $col) {
    $width = max($col[0]->width, $col[1]->width) + $add_width;
    $temp[$key][0]->setMinSize($width, $col[0]->min_height);
    $temp[$key][1]->setMinSize($width, $col[1]->min_height);
    $temp[$key][0]->setMaxSize($width);
    $temp[$key][1]->setMaxSize($width);
}

$min_height = 0;
foreach ($temp as $col) {
    $min_height = max($min_height, $col[1]->height);
}

foreach ($temp as $key => $col) {
    $temp[$key][1]->setMinSize($col[1]->min_width, $min_height);
    $temp[$key][0]->setPosition($xpos1, $ypos1);
    $temp[$key][1]->setPosition($xpos1, $ypos2);
    $xpos1 += $col[0]->width - 1;
    $boxes[] = $temp[$key][0];
    $boxes[] = $temp[$key][1];
}
unset($temp);

$xpos1 = $page_info['margin'];
$ypos1 = $ypos2 += $page_info['row_spacing'] + $min_height;

//End Invoice Options
//-----------------------------------------------------------------------------
//Start Products Listing

$table_fields = array();

$column_width = 1.0 * $dpi;
$box          = new TextBox('ITEM ID', $head_back, $head_text, $black, $column_width);
$box->setMinSize($column_width, 0);
$box->setPosition($xpos1, $ypos1);
$table_fields['product_id'] = $box;
$xpos1                      = $box->x2 - 1;

$box = new TextBox('AMOUNT', $head_back, $head_text, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 - $dpi, $ypos1);
$table_fields['product_total'] = $box;
$xpos2                         = $box->x1;

$box = new TextBox('PRICE EACH', $head_back, $head_text, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 + 1 - $dpi, $ypos1);
$table_fields['product_offer'] = $box;
$xpos2                         = $box->x1;

$column_width = 1.0 * $dpi;
$box          = new TextBox('QTY', $head_back, $head_text, $black, $column_width);
$box->setMinSize($column_width, 0);
$box->setPosition($xpos2 + 1 - $column_width, $ypos1);
$table_fields['product_qty'] = $box;
$xpos2                       = $box->x1;

$box = new TextBox('ITEM NAME', $head_back, $head_text, $black, $table_fields['product_qty']->x1 + 1 - $table_fields['product_id']->x2);
$box->setMinSize($table_fields['product_qty']->x1 + 1 - $table_fields['product_id']->x2, 0);
$box->setPosition($xpos1 + 1, $ypos1);
$table_fields['product_name'] = $box;

$min_height = 0;
foreach ($table_fields as $box) {
    $min_height = max($box->height, $min_height);
}

foreach ($table_fields as $key => $box) {
    $table_fields[$key]->setMinSize($box->min_width, $min_height);
}

$xpos1 = $page_info['margin'];
$xpos2 = $page_info['width'] - $page_info['margin'];
$ypos1 = $ypos2 += $min_height;

foreach ($table_fields as $key => $box) {
    $boxes[] = $box;
}

//Pause Products Listing
//-----------------------------------------------------------------------------
//Start Signature and remarks and total

global $_GLOBALS;

$ypos1 = $ypos2 = $page_info['height'] - $page_info['margin'];

//Signature is about the size of the last 3 columns
$xpos1 = ($table_fields['product_qty']->x2 + $table_fields['product_qty']->x1) / 2;

$ypos2 -= $page_info['row_spacing'] / 2;

$box = new TextBox('Authorised Signatory', '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = &$box;
$ypos2 -= $box->height;

imageline($buffer, $xpos1, $ypos2, $xpos2, $ypos2, $black);

//Leave space
$staff = array();
if (is_null($force_signature)) {
    $staff = $_GLOBALS['staff'];
}

if (in_array($invoice['invoice_status'], array('closed'))) {
    $staff['signature'] = '';
}

if (!is_null($force_signature)) {
    $staff['signature'] = $force_signature;
}

$stamp_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
if (is_file($stamp_file) && 9 == $invoice['site_id']) {
    $stamp_image = imagecreatefromstring(file_get_contents($stamp_file));
    $from_width  = imagesx($stamp_image);
    $from_height = imagesy($stamp_image);
    $to_width    = $from_width;
    $to_height   = $from_height;
    mt_srand($invoice_id);
    $to_x = ($page_info['width'] - $page_info['margin']) - $from_width;
    $to_y = ($page_info['height'] - $page_info['margin']) + 4 - $from_height;

    imagecopy($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height);
//    imagecopymerge($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height, 100);
    //    imagecopymergegray($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height, 100);
    //    imagecopyresampled($buffer, $stamp_image, $to_x, $to_y, 0, 0, $to_width, $to_height, $from_width, $from_height);
    //    imagecopyresized($buffer, $stamp_image, $to_x, $to_y, 0, 0, $to_width, $to_height, $from_width, $from_height);
	if (function_exists('imagedestroy')) {
        imagedestroy($stamp_image);
    }
    $stamp_image = false;
}

$sig_dir  = $_SERVER['DOCUMENT_ROOT'] . '/sigs/';
$sig_file = $sig_dir . $staff['signature'];

if (is_file($sig_file)) {
    $sig_image = imagecreatefromstring(file_get_contents($sig_file));
    $sig_white = imagecolorclosest($sig_image, 255, 255, 255);
    imagecolortransparent($sig_image, $sig_white);

    $from_width  = imagesx($sig_image);
    $from_height = imagesy($sig_image);

//    $scale1=($xpos2-$xpos1)/(3*$from_height);
    //    $scale2=($xpos2-$xpos1)/($from_width);
    $scale1 = 2 * $dpi / ($from_width);
    $scale2 = 0.5 * $dpi / ($from_height);
    $scale  = min(1, min($scale1, $scale2));

    imagecopyresampled($buffer, $sig_image, $xpos2 - ($from_width * $scale), $ypos2 - ($from_height * $scale), 0, 0, $from_width * $scale, $from_height * $scale, $from_width, $from_height);
	if (function_exists('imagedestroy')) {
        imagedestroy($sig_image);
    }
    $sig_image = false;
}

$ypos2 -= 0.5 * $dpi;
//$ypos2-=($xpos2-$xpos1)/3;

$box = new TextBox('FOR ' . strtoupper($invoice_labels['company_name']), '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = $box;
$ypos2 -= $box->height;

$xpos2 = $xpos1;
$xpos1 = $page_info['margin'];

$logo_dir     = ''; //$_SERVER["DOCUMENT_ROOT"] . '/webadmin/images/payment/';
$payment_data = array(); //$DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
if (in_array($invoice_id, $change_array)) {
    $invoice['site_id'] = 9;
}

$payment_info = false;
switch ($invoice['paymenttype_id']) {
    default:
        if ('' != $payment_data['payment_method_image'] && file_exists($logo_dir . $payment_data['payment_method_image'])) {
            $payment_info = true;
            $payment_logo = imagecreatefrompng($logo_dir . $payment_data['payment_method_image']);
            imagecopy($buffer, $payment_logo, $xpos1, $ypos1 - imagesy($payment_logo), 0, 0, imagesx($payment_logo), imagesy($payment_logo));
            if (imagesx($payment_logo) > imagesy($payment_logo)) {
                $ypos1 -= imagesy($payment_logo);
            } else {
                $xpos1 += imagesx($payment_logo);
            }

            if (function_exists('imagedestroy')) {
                imagedestroy($payment_logo);
            }
            $payment_logo = false;
        }

        if ('' != $payment_data['payment_method_details']) {
            $payment_info = true;
            $temp         = array();
            foreach (explode("\n", trim($payment_data['payment_method_details'])) as $row) {
                $row      = explode(":", $row);
                $temp_row = array();
                if (count($row) > 1) {
                    $output     = array_shift($row);
                    $temp_row[] = new TextBox($output . (('' == $output) ? '' : ':') . ' ', '', $black, '', $xpos2 - $xpos1, -1, -1);
                }
                $temp_row[] = new TextBox(trim(implode(':', $row)) . ' ', '', $black, '', $xpos2 - $xpos1, -1, -1);
                foreach ($temp_row as $key => $cell) {
                    $temp_row[$key]->setPadding(1);
                }

                $temp[] = $temp_row;
            }
            $max_width = 0;
            foreach ($temp as $key => $row) {
                if (count($row) > 1) {
                    $max_width = max($max_width, $row[0]->width);
                }
            }

            foreach ($temp as $key => $row) {
                if (count($row) > 1) {
                    $row[0]->setMinSize($max_width, 0);
                    $row[0]->setMaxSize($max_width, 0);
                    $temp[$key] = $row;
                }
            }

            foreach (array_reverse($temp) as $row) {
                if (count($row) > 1) {
                    $max_height = max($row[0]->height, $row[1]->height);
                    $row[0]->setMinSize($row[0]->width, $max_height);
                    $row[0]->setMaxSize($row[0]->width, $max_height);
                    $row[1]->setMinSize(0, $max_height);
                    $row[1]->setMaxSize(0, $max_height);
                    $row[1]->setPosition($xpos1 + $row[0]->width, $ypos1 - $row[1]->height);
                    $boxes[] = $row[1];
                }
                $row[0]->setPosition($xpos1, $ypos1 - $row[0]->height);
                $boxes[] = $row[0];
                $ypos1 -= $row[0]->height;
            }
/*
if(false)
{
$box=new TextBox(strip_tags($payment_data['payment_method_details']),'',$black,'',$xpos2-$xpos1,-1,-1);
$box->setPadding(0);
$box->setPosition($xpos1,$ypos1-$box->height);
$boxes[]=$box;
$ypos1-=$box->height;
}
 */
        }

        $xpos1 = $page_info['margin'];
        break;
}

if ($payment_info) {
    $box = new TextBox('Payment Information:', '', $black, '', $xpos2 - $xpos1, -1, -1);
    $box->setPadding(0);
    $box->setPosition($xpos1, $ypos1 - $box->height);
    $boxes[] = $box;
    $ypos1 -= $box->height;
}

$ypos1 = $ypos2 = min($ypos1, $ypos2) - $page_info['row_spacing'];
$xpos1 = $page_info['margin'];
$xpos2 = $page_info['width'] - $page_info['margin'];

//Pause Products Listing
//-----------------------------------------------------------------------------
//Start Signature and remarks and total

$xpos1 = $table_fields['product_id']->x1;
$xpos2 = $table_fields['product_total']->x2;
$ypos1 = $table_fields['product_id']->y2;

$sub_total = $invoice['invoice_offer'];
$gst_offer = 0;

if (count($invoice_charges) > 0) {
    foreach ($invoice_charges as $charge) {
        if (1 == $charge['charge_id']) {
            $gst_offer += $charge['charge_offer'];
        }
    }
}

$sub_total -= $gst_offer;

$invoice_comments = $invoice['invoice_comments'] . "\n";
if ($invoice['waranty_terms_id'] > 0) {
    $warranty_terms_list = getWarrantyTermsList();
    $invoice_comments .= 'Warranty Terms: ' . $warranty_terms_list[$invoice['waranty_terms_id']] . "\n";
}
$invoice_comments = trim($invoice_comments);

$new_boxes    = array();
$new_boxes[0] = new TextBox($invoice['invoice_currency'], $white, $black, $black, $dpi);
$new_boxes[1] = new TextBox(sprintf('%.2f', $invoice['invoice_offer']), $white, $black, $black, $dpi);
$new_boxes[2] = new TextBox(sprintf('%.2f', $gst_offer), $white, $black, $black, $dpi);
$new_boxes[3] = new TextBox(sprintf('%.2f', $sub_total), $white, $black, $black, $dpi);
$new_boxes[4] = new TextBox("Currency", $head_back, $head_text, $black, $dpi);
$new_boxes[5] = new TextBox("Total", $head_back, $head_text, $black, $dpi);
$new_boxes[6] = new TextBox("GST 10%", $head_back, $head_text, $black, $dpi);
$new_boxes[7] = new TextBox("Sale Amount", $head_back, $head_text, $black, $dpi);
$new_boxes[8] = new TextBox(
    ('' != $invoice_comments) ? ("COMMENTS/NOTES\n" . $invoice_comments) : '',
    $white, $black, $black, $table_fields['product_qty']->x2 + 1 - $xpos1, -1, -1
);

$div   = 4;
$eight = 8;
if (0.0 == $gst_offer) {
    $eight = 6;
    $div   = 3;
    unset($new_boxes[2]);
    unset($new_boxes[6]);
    $new_boxes = array_reverse(array_reverse($new_boxes));
}

//Bound sizes
for ($i = 0; $i < $eight; $i++) {
    $new_boxes[$i]->setMinSize($dpi, 0);
    $new_boxes[$i]->setMaxSize($dpi, 0);
}
$new_boxes[$eight]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, 0);
$new_boxes[$eight]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1);

//Fetch calced sizes
$min_height = $new_boxes[0]->height;
for ($i = 0; $i < $eight; $i++) {
    $min_height = max($min_height, $new_boxes[$i]->height);
}

if ($new_boxes[$eight]->height > $min_height * $div) {
    $min_height = $new_boxes[$eight]->height / $div;
}

//Fix sizes
for ($i = 0; $i < $eight; $i++) {
    $new_boxes[$i]->setMinSize($dpi, $min_height + 1);
    $new_boxes[$i]->setMaxSize($dpi, $min_height + 1);
}
$new_boxes[$eight]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * $div + 1);
$new_boxes[$eight]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * $div + 1);

//Position boxes
$ypos2 -= $min_height;
for ($i = 0; $i < $eight; $i++) {
    $x = $table_fields['product_offer']->x1;
    if (($i >= $div) == 0) {
        $x = $table_fields['product_offer']->x2;
    }

    $y = $ypos2 - ($i % $div) * $min_height;
    $new_boxes[$i]->setPosition($x, $y);
    $boxes[] = &$new_boxes[$i];
}
$new_boxes[$eight]->setPosition($xpos1, $ypos2 - ($div - 1) * $min_height);
$boxes[] = &$new_boxes[$eight];

$ypos2 -= ($div - 1) * $min_height;

foreach ($table_fields as $key => $box) {
    $boxes[] = new MyShape($box->x1, $ypos1, $box->x2, $ypos2, 'rect', $black, $white);
}

//-----------------------------------------------------------------------------

if (count($invoice_products) > 0) {
    foreach ($invoice_products as $product) {
        if (2 == $product['product_condition']) {
            $product['product_id'] .= 'U';
        }

        $product['product_total'] = sprintf('%.2f', $product['product_qty'] * sprintf('%.2f', $product['product_offer']));
        if (3428 == $invoice_id && 0 == $product['product_count']) {
            $product['product_total'] = sprintf('%.2f', '1980');
            $product['product_offer'] = '0.066';
        } elseif (3992 == $invoice_id && 0 == $product['product_count']) {
            $product['product_offer'] = '63.65';
            $product['product_total'] = '63.65';
        }

        $new_boxes = array();
        foreach ($table_fields as $field => $box) {
            $new_box = new TextBox($product[$field], '', $black, '', $box->width, ('product_name' == $field) ? -1 : 0);
            $new_box->setMinSize($box->width, 0);
            $new_box->setMaxSize($box->width, 0);
            $new_box->setPosition($box->x1, $ypos1);
            $new_boxes[$field] = $new_box;
        }
        $min_height = 0;
        foreach ($new_boxes as $box) {
            $min_height = max($box->height, $min_height);
        }

        foreach ($table_fields as $field => $box) {
            $new_boxes[$field]->setMinSize($box->width, $min_height);
            $new_boxes[$field]->setMaxSize($box->width, $min_height);
        }
        $ypos1 += $min_height;

        if ('' != $product['product_notes']) {
            $new_box = new TextBox($product['product_notes'], '', $black, '', '', -1);
            $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
            $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
            $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1 - 2);
            $new_box->setPadding(0);
            $new_box->text_font = '/home/common/fonts/Arial Italic.ttf';
            $new_box->setMinSize($new_box->width, $new_box->hegith);
            $new_box->setMaxSize($new_box->width, $new_box->hegith);
            $boxes[] = &$new_box;

            $min_height += $new_box->height;
            foreach ($table_fields as $field => $box) {
                if ('product_name' != $field) {
                    $new_boxes[$field]->setMinSize($box->width, $min_height);
                    $new_boxes[$field]->setMaxSize($box->width, $min_height);
                }
            }

            $ypos1 += $new_box->height;
        }

        foreach ($table_fields as $field => $box) {
            $boxes[] = &$new_boxes[$field];
        }

        $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
    }
}

if (0 != $invoice['ship_id']) {
    $product                  = array();
    $product['product_id']    = 'F' . str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT) . (('y' == $invoice['ship_insurance']) ? 'I' : 'N');
    $product['product_name']  = $invoice['ship_product_name'];
    $product['product_qty']   = '';
    $product['product_offer'] = sprintf('%.2f', $invoice['ship_offer']);
    $product['product_total'] = sprintf('%.2f', $invoice['ship_offer']);
    $new_boxes                = array();
    foreach ($table_fields as $field => $box) {
        $new_box = new TextBox($product[$field], '', $black, '', $box->width, ('product_name' == $field) ? -1 : 0);
        $new_box->setMinSize($box->width, 0);
        $new_box->setMaxSize($box->width, 0);
        $new_box->setPosition($box->x1, $ypos1);
        $new_boxes[$field] = $new_box;
    }
    $min_height = 0;
    foreach ($new_boxes as $box) {
        $min_height = max($box->height, $min_height);
    }

    foreach ($table_fields as $field => $box) {
        $new_boxes[$field]->setMinSize($box->width, $min_height);
        $new_boxes[$field]->setMaxSize($box->width, $min_height);
//        $boxes[]=&$new_boxes[$field];
    }
    $ypos1 += $min_height;

    if ('' != $invoice['ship_product_notes']) {
        $ypos1 -= 4;
        $new_box = new TextBox($invoice['ship_product_notes'], '', $black, '', '', -1);
        $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
        $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
        $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1);
        $new_box->setPadding(0);
        $new_box->text_font = '/home/common/fonts/Arial Italic.ttf';

        $min_height += $new_box->height;
        foreach ($table_fields as $field => $box) {
            if ('product_name' != $field) {
                $new_boxes[$field]->setMinSize($box->width, $min_height);
                $new_boxes[$field]->setMaxSize($box->width, $min_height);
            }
        }

        $boxes[] = &$new_box;
        $ypos1 += $new_box->height + 4;
    }

    foreach ($table_fields as $field => $box) {
        $boxes[] = &$new_boxes[$field];
    }

    $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
}

if (count($invoice_charges) > 0) {
    $temps_list = array();
    $query      = $DB->query("SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name`");
    while ($row = $DB->fetch_assoc($query)) {
        $temp_list[$row['charge_id']] = $row['charge_name'];
    }

    foreach ($invoice_charges as $charge) {
        if (1 != $charge['charge_id']) {
            $product                  = array();
            $product['product_id']    = 'C' . str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT);
            $product['product_name']  = $temp_list[$charge['charge_id']];
            $product['product_qty']   = '';
            $product['product_offer'] = sprintf('%.2f', $charge['charge_offer']);
            $product['product_total'] = sprintf('%.2f', $charge['charge_offer']);
            $new_boxes                = array();
            foreach ($table_fields as $field => $box) {
                $new_box = new TextBox($product[$field], '', $black, '', $box->width, ('product_name' == $field) ? -1 : 0);
                $new_box->setMinSize($box->width, 0);
                $new_box->setMaxSize($box->width, 0);
                $new_box->setPosition($box->x1, $ypos1);
                $new_boxes[$field] = $new_box;
            }
            $min_height = 0;
            foreach ($new_boxes as $box) {
                $min_height = max($box->height, $min_height);
            }

            foreach ($table_fields as $field => $box) {
                $new_boxes[$field]->setMinSize($box->width, $min_height);
                $new_boxes[$field]->setMaxSize($box->width, $min_height);
                $boxes[] = &$new_boxes[$field];
            }
            $ypos1 += $min_height;

            if ('' != $product['product_notes']) {
                $ypos1 -= 4;
                $new_box = new TextBox($product['product_notes'], '', $black, '', '', -1);
                $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
                $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
                $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1);
                $new_box->setPadding(0);
                $new_box->text_font = '/home/common/fonts/Arial Italic.ttf';
                $boxes[]            = &$new_box;
                $ypos1 += $new_box->height + 4;
            }

            $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
        }
    }
}

imagesetstyle($buffer, array($black, $white));
foreach ($boxes as $box) {
    $box->draw($buffer);
}

$stamp_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.gif';
if (is_file($stamp_file) && is_file($sig_file)) {
    $stamp_image = imagecreatefromstring(file_get_contents($stamp_file));
    $from_width  = imagesx($stamp_image);
    $from_height = imagesy($stamp_image);
    $to_width    = $dpi;
    $to_height   = $dpi;
    mt_srand($invoice_id);
    $to_x = ($page_info['width'] - $page_info['margin']) - (mt_rand(200, 300) / 100.0) * $dpi;
    $to_y = ($page_info['height'] - $page_info['margin']) - (mt_rand(100, 150) / 100.0) * $dpi;

    imagecopy($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height);
//    imagecopymerge($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height, 100);
    //    imagecopymergegray($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height, 100);
    //    imagecopyresampled($buffer, $stamp_image, $to_x, $to_y, 0, 0, $to_width, $to_height, $from_width, $from_height);
    //    imagecopyresized($buffer, $stamp_image, $to_x, $to_y, 0, 0, $to_width, $to_height, $from_width, $from_height);
	if (function_exists('imagedestroy')) {
        imagedestroy($stamp_image);
    }
    $stamp_image = false;
}

unset($boxes);

$invoice_buffer = $buffer;
return;
