<?php
include __DIR__ . '/setup_shipinvoice.inc.php';
if (!class_exists('MyShape')) {
    include_once __DIR__ . '/../classes/MyShape.php';
}
if (!class_exists('TextBox')) {
    include_once __DIR__ . '/../classes/TextBox.php';
}

$page_info = array('width' => 7.26, 'height' => 10.69, 'margin' => 0.0, 'row_spacing' => 0.25, 'col_spacing' => 0.25);
$dpi       = 89;
foreach ($page_info as $key => $value) {
    $page_info[$key] = $value * $dpi;
}

$buffer = imagecreate($page_info['width'], $page_info['height']);
imageantialias($buffer, false);

$shades = array();
for ($i = 15; $i >= 0; $i--) {
    $shades[$i] = imagecolorallocate($buffer, $i * 0x11, $i * 0x11, $i * 0x11);
}

$white             = $shades[15];
$black             = $shades[0];
$grey              = $shades[14];
$shades['stamp'][] = imagecolorallocate($buffer, 131, 186, 222);
$shades['stamp'][] = imagecolorallocate($buffer, 112, 175, 217);
$shades['stamp'][] = imagecolorallocate($buffer, 198, 223, 240);

switch ($sinvoice['site_id']) {
    case 1:
        $head_back = imagecolorallocate($buffer, 0, 35, 244);
        $head_text = $white;
        break;
    case 2:
        $head_back = imagecolorallocate($buffer, 30, 144, 29);
        $head_text = $white;
        break;
    case 9:
        $head_back = imagecolorallocate($buffer, 69, 75, 132);
        $head_text = $white;
        break;
    case 16:
        $head_back = imagecolorallocate($buffer, 41, 59, 78);
        $head_text = imagecolorallocate($buffer, 226, 238, 250);
        break;
    case 18:
        $head_back = imagecolorallocate($buffer, 12, 149, 68);
        $head_text = $white;
        break;
    default:
        $head_back = $grey;
        $head_text = $black;
}
//imagefilledrectangle($buffer, 0, 0, $page_info['width'], $page_info['height'], $white);

$boxes = array();

//-----------------------------------------------------------------------------
//Start invoice title/date
$xpos1 = $page_info['margin'];
$ypos1 = $page_info['margin'];
$xpos2 = $page_info['width'] - $page_info['margin'];
$ypos2 = $page_info['margin'];
//$time_to_use = $sinvoice['open_time'];
//if ($sinvoice_type < 3) $time_to_use = time();
//elseif ($sinvoice['proforma_time'] > 0) $time_to_use = $sinvoice['proforma_time'];
//if ($sinvoice_time > 0) $time_to_use = $sinvoice_time;
$time_to_use = ($sinvoice['proforma_time'] > 0) ? $sinvoice['proforma_time'] : $sinvoice['open_time'];
if (0 != $sinvoice_time) {
    $time_to_use = $sinvoice_time;
}

$temp            = array();
$temp[]          = new TextBox('DATE', $head_back, $head_text, $black);
$temp[]          = new TextBox(date('jS F, Y', $time_to_use), $white, $black, $black);
$temp[]          = new TextBox('INVOICE #', $head_back, $head_text, $black);
$sinvoice_number = shipinvoiceFormatNumber($sinvoice_id, null, $sinvoice['site_id']);
$temp[]          = new TextBox($sinvoice_number, $white, $black, $black);
$min_width       = 0;
$min_height      = 0;
foreach ($temp as $box) {
    $min_width  = max($min_width, $box->width);
    $min_height = max($min_height, $box->height);
}
$count = 0;
$box   = new TextBox('SALES INVOICE', $shades[8], $white, $black);
if ('AU' == $sinvoice['ship_country'] && 'AU' == $sinvoice['bill_country']) {
    $box->setText('TAX INVOICE');
}

if (12322 == $sinvoice_id) {
    $box->setText('PROFORMA INVOICE');
}

$box->setMinSize(2 * $min_width + 1, 0);
$box->setMaxSize(2 * $min_width + 1, 0);
$box->text_font = __DIR__ . '/../fonts/Verdana Bold.ttf';
$box->setFontSize(12);
$box->setPosition($xpos2 - $box->width, $ypos2);
$box->text_y += 4;
$box->height = max(32, $box->height);
$box->y2     = $box->y1 + $box->height - 1;
$xpos1       = $box->x1;
$ypos2 += $box->height + $page_info['row_spacing'];
$boxes[] = $box;
foreach ($temp as $key => $box) {
    $temp[$key]->setMinSize($min_width + 1, $min_height + 1);
    $temp[$key]->setPosition($xpos2 - (intval($count / 2) + 1) * $min_width - 1, $ypos2 + ($count&1) * $min_height - 1);
    $count++;
}
foreach ($temp as $key => $box) {
    $boxes[] = $temp[$key];
    $xpos1   = min($xpos1, $temp[$key]->x1);
    $ypos2   = max($ypos2, $temp[$key]->y2);
}
$xpos2 = $xpos1 - $page_info['col_spacing'];
$xpos1 = $page_info['margin'];
$temp  = explode("\n", $sinvoice_labels['company_invoiceheader']);
if ('AU' == $sinvoice['bill_country'] && 'AU' == $sinvoice['ship_country']) {
    $temp = explode("\n", $sinvoice_labels['company_invoiceheader_au']);
}

if ('' != $images['company_logo']) {
    $logo_image  = imagecreatefromstring(file_get_contents($website_path . $images['company_logo']));
    $from_width  = imagesx($logo_image);
    $from_height = imagesy($logo_image);
    imagecopy($buffer, $logo_image, $xpos1, $ypos1, 0, 0, $from_width, $from_height);
    if (function_exists('imagedestroy')) {
        imagedestroy($logo_image);
    }
    $logo_image = false;
    $ypos1 += $from_height + $page_info['row_spacing'];
    array_shift($temp);
} else {
    $box = new TextBox(array_shift($temp), '', $black, '', $xpos2 - $xpos1, -1, 0);
    $box->setPadding(0);
    $box->setFontSize(24);
    $box->setPosition($xpos1, $ypos1);
    $ypos1 += $box->height + 4;
    $boxes[] = $box;
}

$box = new TextBox(implode("\n", $temp), '', $black, '', $xpos2 - $xpos1, -1, -1);
$box->setPadding(0);
$box->setPosition($xpos1, $ypos1);
// this will wrap address into multiple lines as it is in database
$box->text_wrapped = $box->text_original;

$ypos1 += $box->height;
$boxes[] = $box;
$ypos1   = $ypos2   = max($ypos1, $ypos2) + $page_info['row_spacing'];
$xpos1   = $page_info['margin'];
$xpos2   = $page_info['width'] - $page_info['margin'];
//End invoice title/date
//-----------------------------------------------------------------------------
//Start Invoice addresses
$contacts_list = array();
if ('' != $sinvoice['ship_address_full']) {
    $contacts_list['SHIP TO'] = $sinvoice['ship_address_full'];
}

// Hack to remove NOTIFY PARTY for this invoice id as requested by customer (Weird but I don't know why)
if (57033 == $invoice_id) {
    if ('' != $sinvoice['notify_address_full']) {
        $contacts_list[' '] = $sinvoice['notify_address_full'];
    }
} else {
    if ('' != $sinvoice['notify_address_full']) {
        $contacts_list['NOTIFY PARTY'] = $sinvoice['notify_address_full'];
    }
}

$contacts_layout = array_chunk(array_keys($contacts_list), 2);
$min_width       = $max_width       = ($page_info['width'] - ($page_info['margin'] * 2) + 1) / 2;
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
    $ypos1   = $ypos2 += $box1->height - 1;
    $box1    = new TextBox($contacts_list[$row[0]], $white, $black, $black, $max_width, -1, -1);
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
    $ypos1   = $ypos2 += $box1->height + $page_info['row_spacing'];
}
//End Invoice addresses
//-----------------------------------------------------------------------------
//Start Invoice Options
$temp = array();
foreach ($sinvoice_inforow as $key => $value) {
    $temp[] = array(new TextBox($key, $head_back, $head_text, $black), new TextBox($value, $white, $black, $black));
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
$count       = 0;
while ($total_width > ($xpos2 - $xpos1) && $total_width != $old_total && $count < 10) {
    $count++;
    $old_total = $total_width;
    $scale     = ($xpos2 - $xpos1 - $fixed_width) / ((0 == $loose_width) ? 1 : $loose_width);
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
$box                        = new TextBox('AMOUNT', $head_back, $head_text, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 - $dpi, $ypos1);
$table_fields['product_total'] = $box;
$xpos2                         = $box->x1;
$box                           = new TextBox('PRICE EACH', $head_back, $head_text, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 + 1 - $dpi, $ypos1);
$table_fields['product_offer'] = $box;
$xpos2                         = $box->x1;
$column_width                  = 1.0 * $dpi;
$box                           = new TextBox('QTY', $head_back, $head_text, $black, $column_width);
$box->setMinSize($column_width, 0);
$box->setPosition($xpos2 + 1 - $column_width, $ypos1);
$table_fields['product_qty'] = $box;
$xpos2                       = $box->x1;
$box                         = new TextBox('ITEM NAME', $head_back, $head_text, $black, $table_fields['product_qty']->x1 + 1 - $table_fields['product_id']->x2);
$box->setMinSize($table_fields['product_qty']->x1 + 1 - $table_fields['product_id']->x2, 0);
$box->setPosition($xpos1 + 1, $ypos1);
$table_fields['product_name'] = $box;
$min_height                   = 0;
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
$ypos1 = $ypos2 = $page_info['height'] - $page_info['margin'];
$xpos1 = ($table_fields['product_qty']->x2 + $table_fields['product_qty']->x1) / 2;
$ypos2 -= $page_info['row_spacing'] / 2;
$box = new TextBox('Authorised Signatory', '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = $box;
$ypos2 -= $box->height;
imageline($buffer, $xpos1, $ypos2, $xpos2, $ypos2, $black);
if ('' != $images['stamp']) {
    $stamp_image = imagecreatefromstring(file_get_contents($website_path . $images['stamp']));
    $from_width  = imagesx($stamp_image);
    $from_height = imagesy($stamp_image);
    $to_width    = $from_width;
    $to_height   = $from_height;
    $to_x        = ($page_info['width'] - $page_info['margin']) - $from_width - 65.0;
    $to_y        = ($page_info['height'] - $page_info['margin']) - $from_height;
    imagecopy($buffer, $stamp_image, $to_x, $to_y, 0, 0, $from_width, $from_height);
    if (function_exists('imagedestroy')) {
        imagedestroy($stamp_image);
    }
    $stamp_image = false;
}
if ('' != $images['signature']) {
    $sig_image = imagecreatefromstring(file_get_contents($website_path . $images['signature']));
    //$sig_white = imagecolorclosest($sig_image, 255, 255, 255);
    //imagecolortransparent($sig_image, $sig_white);
    $from_width  = imagesx($sig_image);
    $from_height = imagesy($sig_image);
    $scale1      = 2 * $dpi / ($from_width);
    $scale2      = 0.5 * $dpi / ($from_height);
    $scale       = min(1, min($scale1, $scale2));
    imagecopyresampled($buffer, $sig_image, $xpos2 - ($from_width * $scale), $ypos2 - ($from_height * $scale), 0, 0, $from_width * $scale, $from_height * $scale, $from_width, $from_height);
    if (function_exists('imagedestroy')) {
        imagedestroy($sig_image);
    }
    $sig_image = false;
}
$ypos2 -= 0.5 * $dpi;
$box = new TextBox('FOR ' . strtoupper($sinvoice_labels['company_name']), '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = $box;
$ypos2 -= $box->height;
$xpos2        = $xpos1;
$xpos1        = $page_info['margin'];
$payment_info = false;
if ("y" == $sinvoice["show_payment_info"]) {
    switch ($sinvoice['paymenttype_id']) {
        case 'BPAY':
            if ('' != $images['payment_method']) {
                $bpay_logo = imagecreatefrompng($website_path . $images['payment_method']);
                imagecopy($buffer, $bpay_logo, $xpos1, $ypos1 - imagesy($bpay_logo), 0, 0, imagesx($bpay_logo), imagesy($bpay_logo));
                $ypos1 -= imagesy($bpay_logo);
                if (function_exists('imagedestroy')) {
                    imagedestroy($bpay_logo);
                }
                $bpay_logo = false;
            } else {
                $ypos1 -= 68;
            }

            $box = new TextBox("Biller Code: 424606\nRef: " . $sinvoice_reference, $white, $black, $black, 156);
            $box->setMinSize(156, 64);
            $box->setPosition($xpos1 + 46, $ypos1 + 4);
            $boxes[]      = $box;
            $payment_info = true;
            break;
        default:
            if ('' != $images['payment_method']) {
                $payment_logo = imagecreatefrompng($website_path . $images['payment_method']);
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
                $payment_info = true;
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
}
//Pause Products Listing
//-----------------------------------------------------------------------------
//Start Signature and remarks and total
$xpos1     = $table_fields['product_id']->x1;
$xpos2     = $table_fields['product_total']->x2;
$ypos1     = $table_fields['product_id']->y2;
$sub_total = $sinvoice['invoice_offer'];
$gst_offer = 0;
if (count($sinvoice_charges) > 0) {
    foreach ($sinvoice_charges as $charge) {
        if (1 == $charge['charge_id']) {
            $gst_offer += $charge['charge_offer'];
        }
    }
}

$sub_total -= $gst_offer;
$sinvoice_comments = $sinvoice['invoice_comments'] . "\n";
if ($sinvoice['waranty_terms_id'] > 0) {
    $sinvoice_comments .= 'Warranty Terms: ' . $warranty_terms_list[$sinvoice['waranty_terms_id']] . "\n";
}
$origin_country = ('' != $sinvoice['origin_country'] && $sinvoice['is_origin_country'] == 'y') ? $country_list[$sinvoice['origin_country']] : '';
$harmonised_code = ('' != $sinvoice['harmonised_code'] && $sinvoice['is_harmonised_code'] == 'y') ? $sinvoice['harmonised_code'] : '';
$invoice_materials = ('' != $sinvoice['invoice_materials'] && $sinvoice['is_invoice_materials'] == 'y') ? $sinvoice['invoice_materials'] : '';
$invoice_usage = ('' != $sinvoice['invoice_usage'] && $sinvoice['is_invoice_usage'] == 'y') ? $sinvoice['invoice_usage'] : '';

// Add origin country and harmonized code if available
if ('' != $origin_country || '' != $harmonised_code) {
    $sinvoice_comments .= 'HARMONIZED CODE: ' . $harmonised_code;
    $sinvoice_comments .= '               COUNTRY OF ORIGIN: ' . $origin_country . "\n";
}

// Add invoice materials and usage if available
if ('' != $invoice_materials || '' != $invoice_usage) {
    $sinvoice_comments .= 'MATERIALS: ' . $invoice_materials;
    $sinvoice_comments .= '               USAGE: ' . $invoice_usage . "\n";
}

$sinvoice_comments = trim($sinvoice_comments);
$new_boxes         = array();
$new_boxes[0]      = new TextBox($sinvoice['invoice_currency'], $white, $black, $black, $dpi);
$new_boxes[1]      = new TextBox(sprintf('%.2f', $sinvoice['invoice_offer']), $white, $black, $black, $dpi);
$new_boxes[2]      = new TextBox(sprintf('%.2f', $gst_offer), $white, $black, $black, $dpi);
$new_boxes[3]      = new TextBox(sprintf('%.2f', $sub_total), $white, $black, $black, $dpi);
$new_boxes[4]      = new TextBox("Currency", $head_back, $head_text, $black, $dpi);
$new_boxes[5]      = new TextBox("Total", $head_back, $head_text, $black, $dpi);
$new_boxes[6]      = new TextBox("GST 10%", $head_back, $head_text, $black, $dpi);
$new_boxes[7]      = new TextBox("Sale Amount", $head_back, $head_text, $black, $dpi);
$new_boxes[8]      = new TextBox(('' != $sinvoice_comments) ? ("COMMENTS/NOTES\n" . $sinvoice_comments) : '', $white, $black, $black, $table_fields['product_qty']->x2 + 1 - $xpos1, -1, -1);
$div               = 4;
$eight             = 8;
if (0.0 == $gst_offer) {
    $eight = 6;
    $div   = 3;
    unset($new_boxes[2]);
    unset($new_boxes[6]);
    $new_boxes = array_reverse(array_reverse($new_boxes));
}
for ($i = 0; $i < $eight; $i++) {
    $new_boxes[$i]->setMinSize($dpi, 0);
    $new_boxes[$i]->setMaxSize($dpi, 0);
}
$new_boxes[$eight]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, 0);
$new_boxes[$eight]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1);
$min_height = $new_boxes[0]->height;
for ($i = 0; $i < $eight; $i++) {
    $min_height = max($min_height, $new_boxes[$i]->height);
}

if ($new_boxes[$eight]->height > $min_height * $div) {
    $min_height = $new_boxes[$eight]->height / $div;
}

for ($i = 0; $i < $eight; $i++) {
    $new_boxes[$i]->setMinSize($dpi, $min_height + 1);
    $new_boxes[$i]->setMaxSize($dpi, $min_height + 1);
}
$new_boxes[$eight]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * $div + 1);
$new_boxes[$eight]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * $div + 1);
$ypos2 -= $min_height;
for ($i = 0; $i < $eight; $i++) {
    $x = $table_fields['product_offer']->x1;
    if (($i >= $div) == 0) {
        $x = $table_fields['product_offer']->x2;
    }

    $y = $ypos2 - ($i % $div) * $min_height;
    $new_boxes[$i]->setPosition($x, $y);
    $boxes[] = $new_boxes[$i];
}
$new_boxes[$eight]->setPosition($xpos1, $ypos2 - ($div - 1) * $min_height);
$boxes[] = $new_boxes[$eight];
$ypos2 -= ($div - 1) * $min_height;
foreach ($table_fields as $key => $box) {
    $boxes[] = new MyShape($box->x1, $ypos1, $box->x2, $ypos2, 'rect', $black, $white);
}

//-----------------------------------------------------------------------------
if (count($sinvoice_products) > 0) {
    foreach ($sinvoice_products as $product) {
        if ('c' != $product['generic']) {
            if (2 == $product['product_condition']) {
                $product['product_id'] .= 'U';
            }

            if ('' != $product['generic_code'] && '0' == $product['product_id']) {
                $product['product_id'] = $product['generic_code'];
            }

            $product['product_offer'] = convertCurrency($product['product_offer'], $sinvoice['invoice_currency'], '', false, 2);
            $product['product_total'] = sprintf('%.2f', $product['product_qty'] * sprintf('%.2f', $product['product_offer']));
            if (3428 == $sinvoice_id && 0 == $product['product_count']) {
                $product['product_total'] = sprintf('%.2f', '1980');
                $product['product_offer'] = '0.066';
            } elseif (3992 == $sinvoice_id && 0 == $product['product_count']) {
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
                $new_box->text_font = __DIR__ . '/../fonts/Arial Italic.ttf';
                $new_box->setMinSize($new_box->width, $new_box->height);
                $new_box->setMaxSize($new_box->width, $new_box->height);
                $boxes[] = $new_box;
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
                $boxes[] = $new_boxes[$field];
            }

            $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
        }
    }
}
if (0 != $sinvoice['ship_id']) {
    $product               = array();
    $product['product_id'] = 'F' . str_pad($sinvoice['ship_id'], 2, '0', STR_PAD_LEFT) . (('y' == $sinvoice['ship_insurance']) ? 'I' : 'N');

    if (20 == $invoice["site_id"]) {
        $row = $DB->query_first("SELECT `type` FROM `gdelta_gdelta`.`freight_services` WHERE `id` = " . intval($sinvoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
    } elseif (39 == $invoice["site_id"]) {
        $row = $DB->query_first("SELECT `product_type` AS `type` FROM `freight3`.`services` WHERE `id` = " . intval($sinvoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
    } else {
        $row = $DB->query_first("SELECT `type` FROM `highwaygroup`.`freight_services` WHERE `id` = " . intval($sinvoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
    }

    if ('truck' == $row['type']) {
        $product['product_id'] .= 'T';
    }

    $product['product_name']  = $sinvoice['ship_product_name'];
    $product['product_qty']   = '';
    $product['product_offer'] = sprintf('%.2f', $sinvoice['ship_offer']);
    $product['product_total'] = sprintf('%.2f', $sinvoice['ship_offer']);
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
    }
    $ypos1 += $min_height;
    if ('' != $sinvoice['ship_product_notes']) {
        $ypos1 -= 4;
        $new_box = new TextBox($sinvoice['ship_product_notes'], '', $black, '', '', -1);
        $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
        $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
        $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1);
        $new_box->setPadding(0);
        $new_box->text_font = __DIR__ . '/../fonts/Arial Italic.ttf';
        $min_height += $new_box->height;
        foreach ($table_fields as $field => $box) {
            if ('product_name' != $field) {
                $new_boxes[$field]->setMinSize($box->width, $min_height);
                $new_boxes[$field]->setMaxSize($box->width, $min_height);
            }
        }

        $boxes[] = $new_box;
        $ypos1 += $new_box->height + 4;
    }
    foreach ($table_fields as $field => $box) {
        $boxes[] = $new_boxes[$field];
    }

    $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
}
if (count($sinvoice_charges) > 0) {
    $temp_list = array();
    $query     = $DB->query("SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name` ASC");
    while ($row = $DB->fetch_assoc($query)) {
        $temp_list[$row['charge_id']] = $row['charge_name'];
    }

    foreach ($sinvoice_charges as $charge) {
        if (1 != $charge['charge_id']) {
            $product                 = array();
            $product['product_id']   = 'C' . str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT);
            $product['product_name'] = $temp_list[$charge['charge_id']];
            //$product['product_notes'] = $charge["charge_notes"];
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
                $boxes[] = $new_boxes[$field];
            }
            $ypos1 += $min_height;
            if ('' != $product['product_notes']) {
                $ypos1 -= 4;
                $new_box = new TextBox($product['product_notes'], '', $black, '', '', -1);
                $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
                $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
                $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1);
                $new_box->setPadding(0);
                $new_box->text_font = __DIR__ . '/../fonts/Arial Italic.ttf';
                $boxes[]            = $new_box;
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

$size_failure = false;
if ($ypos1 > $ypos2) {
    $size_failure = true;
}

unset($boxes);
$sinvoice_buffer = $buffer;
return;
