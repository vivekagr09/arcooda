<?php
include __DIR__ . '/setup_invoice.inc.php';
if (!class_exists('MyShape')) {
    include_once __DIR__ . '/../classes/MyShape.php';
}
if (!class_exists('TextBox')) {
    include_once __DIR__ . '/../classes/TextBox.php';
}

global $_GLOBALS;

$page_info = array('width' => 7.26, 'height' => 10.69, 'margin' => 0.0, 'row_spacing' => 0.25, 'col_spacing' => 0.25);
$dpi       = 89;
foreach ($page_info as $key => $value) {
    $page_info[$key] = $value * $dpi;
}

$buffer = imagecreate($page_info['width'], $page_info['height']);
if (function_exists('imageantialias')) {
    imageantialias($buffer, false);
}

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

switch ($invoice['site_id']) {
    case 1:
        $head_back = imagecolorallocate($buffer, 0, 35, 244);
        $head_text = $white;
        break;
    case 2:
        $head_back = imagecolorallocate($buffer, 30, 144, 29);
        $head_text = $white;
        break;
    case 3:
        $head_back = imagecolorallocate($buffer, 0x99, 0xCC, 0xFF);
        $head_text = $black;
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
    case 19:
        $head_back = imagecolorallocate($buffer, 60, 181, 76);
        $head_text = $white;
        break;
    case 20:
        $head_back = imagecolorallocate($buffer, 2, 158, 78);
        $head_text = $white;
        break;
    case 22:
        $head_back = imagecolorallocate($buffer, 0xB8, 0x31, 0x7D);
        $head_text = $white;
        break;
    case 24:
        $head_back = imagecolorallocate($buffer, 0x8f, 0xbe, 0x3c);
        $head_text = $black;
        break;
    case 30:
        $head_back = imagecolorallocate($buffer, 0x1b, 0x75, 0xbb);
        $head_text = $white;
        break;
    case 39:
        $head_back = imagecolorallocate($buffer, 0x06, 0x8d, 0xbd);
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
$xpos1       = $page_info['margin'];
$ypos1       = $page_info['margin'];
$xpos2       = $page_info['width'] - $page_info['margin'];
$ypos2       = $page_info['margin'];
$time_to_use = $invoice['open_time'];
if ($invoice_type < 3) {
    $time_to_use = time();
} elseif ($invoice['proforma_time'] > 0) {
    $time_to_use = $invoice['proforma_time'];
}

if (isset($invoice_time) && $invoice_time > 0) {
    $time_to_use = $invoice_time;
}

$temp   = array();
$temp[] = new TextBox('DATE', $head_back, $head_text, $black);
$temp[] = new TextBox(date('jS F, Y', $time_to_use), $white, $black, $black);
$temp[] = new TextBox(($invoice_type < 3) ? 'QUOTATION #' : 'INVOICE #', $head_back, $head_text, $black);
/*$invoice_number = invoiceFormatNumber($invoice_id, ($invoice_type == 3 || $invoice_type == 4 || $invoice_type == 9) ? $invoice['revision_id'] : null, $invoice['site_id']);
if ($action == 'shipview') $invoice_number = 'S' . substr($invoice_number, 1);*/
$temp[]     = new TextBox($formatted_invoice_number, $white, $black, $black);
$min_width  = 100;
$min_height = 0;
foreach ($temp as $box) {
    $min_width  = max($min_width, $box->width);
    $min_height = max($min_height, $box->height);
}
$count = 0;
//$box = new TextBox(($invoice_type > 2) ? (strtoupper($invoice['invoice_status']) . ' INVOICE') : 'QUOTATION', $shades[8], $white, $black);
$box = new TextBox($invoice_type_text, $shades[8], $white, $black);
if (in_array($invoice['invoice_status'], array('sales', 'processed'))) {
    if (20 != $invoice['site_id'] && 26 != $invoice['site_id'] && 'AU' == $invoice['ship_country'] && 'AU' == $invoice['bill_country']) {
        $box->setText('TAX INVOICE');
    } else {
        $box->setText('SALES INVOICE');
    }
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
$temp  = explode("\n", $invoice_labels['company_invoiceheader']);
if ('AU' == $invoice['bill_country'] && 'AU' == $invoice['ship_country']) {
    $temp = explode("\n", $invoice_labels['company_invoiceheader_au']);
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
// this will wrap company_info into multiple lines as it is in database
$box->text_wrapped = $box->text_original;
$ypos1 += $box->height;
$boxes[] = $box;
$ypos1   = $ypos2   = max($ypos1, $ypos2) + $page_info['row_spacing'];
$xpos1   = $page_info['margin'];
$xpos2   = $page_info['width'] - $page_info['margin'];
if (false && '' != $invoice_diagonal) {
    $box = new TextBox($invoice_diagonal, $white, $black, '', $xpos2 - $xpos1, 0, 0);
    $box->setMinSize(646, 165);
    $box->setPosition(0, 0);
    $box->setFontSize(48);
    $boxes[] = $box;
}
//End invoice title/date
//-----------------------------------------------------------------------------
//Start Invoice addresses
$contacts_layout = array_chunk(array_keys($invoice_contacts), 2);
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
    $box1    = new TextBox($invoice_contacts[$row[0]], $white, $black, $black, $max_width, -1, -1);
    $box1->setMinSize($min_width, 0);
    if ('' != $row[1]) {
        $box2 = new TextBox($invoice_contacts[$row[1]], $white, $black, $black, $max_width, -1, -1);
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
foreach ($invoice_inforow as $key => $value) {
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
if ('' != $images['stamp'] && is_file($website_path . $images['stamp'])) {
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
if ('' != $images['signature'] && is_file($website_path . $images['signature'])) {
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
if (20 == $invoice["site_id"]) {
    $box = new TextBox('FOR ' . strtoupper($invoice_labels['company_name']) . ' LTD', '', $black, '', $xpos2 - $xpos1, 1, 1);
} else {
    $box = new TextBox('FOR ' . strtoupper($invoice_labels['company_name']) . ' PTY LTD', '', $black, '', $xpos2 - $xpos1, 1, 1);
}

$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = $box;
$ypos2 -= $box->height;
$xpos2        = $xpos1;
$xpos1        = $page_info['margin'];
$payment_info = false;
switch ($invoice['paymenttype_id']) {
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

        $box = new TextBox("Biller Code: 424606\nRef: " . $invoice_reference, $white, $black, $black, 156);
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
//Pause Products Listing
//-----------------------------------------------------------------------------
//Start Signature and remarks and total
$xpos1     = $table_fields['product_id']->x1;
$xpos2     = $table_fields['product_total']->x2;
$ypos1     = $table_fields['product_id']->y2;
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

$the_text = array();
foreach ($invoice_comments as $key => $field) {
    $the_text[] = trim($key) . ":\n" . trim($field);
}

$the_text = trim(implode("\n", $the_text));

$temp                = array();
$temp['Sale Amount'] = "";
$temp['GST 10%']     = "";
if (9 == $invoice_type) {
    $temp['Total'] = convertCurrency($invoice['products_cost'], $invoice['invoice_currency']);
} else {
    $temp['Sale Amount'] = convertCurrency($sub_total, $invoice['invoice_currency']);
    if (0 != $gst_offer) {
        $temp['GST 10%'] = convertCurrency($gst_offer, $invoice['invoice_currency']);

        $temp['GST 10%']    = explode('.', $temp['GST 10%']);
        $temp['GST 10%'][0] = number_format($temp['GST 10%'][0]);
        $temp['GST 10%']    = implode('.', $temp['GST 10%']);
    }
    $temp['Total']          = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']);
    $temp['Sale Amount']    = explode('.', $temp['Sale Amount']);
    $temp['Sale Amount'][0] = number_format($temp['Sale Amount'][0]);
    $temp['Sale Amount']    = implode('.', $temp['Sale Amount']);
}
$temp['Total']    = explode('.', $temp['Total']);
$temp['Total'][0] = number_format($temp['Total'][0]);
$temp['Total']    = implode('.', $temp['Total']);
$temp['Currency'] = $invoice['invoice_currency'];

$new_boxes    = array();
$new_boxes[0] = new TextBox($invoice['invoice_currency'], $white, $black, $black, $dpi);
$new_boxes[1] = new TextBox($temp['Total'], $white, $black, $black, $dpi);
$new_boxes[2] = new TextBox($temp['GST 10%'], $white, $black, $black, $dpi);
$new_boxes[3] = new TextBox($temp['Sale Amount'], $white, $black, $black, $dpi);
$new_boxes[4] = new TextBox("Currency", $head_back, $head_text, $black, $dpi);
$new_boxes[5] = new TextBox("Total", $head_back, $head_text, $black, $dpi);
$new_boxes[6] = new TextBox("GST 10%", $head_back, $head_text, $black, $dpi);
$new_boxes[7] = new TextBox("Sale Amount", $head_back, $head_text, $black, $dpi);
$new_boxes[8] = new TextBox($the_text, $white, $black, $black, $table_fields['product_qty']->x2 + 1 - $xpos1, -1, -1);
$new_boxes[8]->setFontSize(7);
$new_boxes[8]->text_font = __DIR__ . '/../fonts/Arial Italic.ttf';
$div                     = 4;
$eight                   = 8;
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
if (count($invoice_products) > 0) {
    foreach ($invoice_products as $product) {
        if ('c' != $product['generic']) {
            if ($product['product_id'] > 0 && $product['sub_id'] > 0) {
                $product['product_id'] = str_pad($product['product_id'], 5, '0', STR_PAD_LEFT) . '-' . str_pad($product['sub_id'], 4, '0', STR_PAD_LEFT);
            }

            if ('' != $product['generic_code'] && '0' == $product['product_id']) {
                $product['product_id'] = $product['generic_code'];
            }

            //if ($product['product_condition'] == 2) $product['product_id'].= 'U';
            $product['product_offer'] = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
            $product['product_total'] = convertCurrency(intval($product['product_qty']) * $product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
            if (3428 == $invoice['invoice_id'] && 0 == $product['product_count']) {
                $product['product_total'] = sprintf('%.2f', '1980');
                $product['product_offer'] = '0.066';
            } elseif (3992 == $invoice['invoice_id'] && 0 == $product['product_count']) {
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
                $new_box->setFontSize(7);
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
if (isset($invoice_gifts['gift']) && count($invoice_gifts['gift']) > 0) {
    foreach ($invoice_gifts['gift'] as $data) {
        $product                  = array();
            $product['product_id']    = str_pad($data['product_id'], 2, '0', STR_PAD_LEFT);
            $product['product_name']  = $data['gift_name'].' (FREE - Gift)'; 
            $product["product_notes"] = $data['gift_detail'];
            $product['product_qty']   = $data['gift_qty'];
            $product['product_offer'] = sprintf('%.2f', 0.00);
            $product['product_total'] = sprintf('%.2f', 0.00);
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
if (0 != $invoice['ship_id']) {
    $product = array();

    $row                   = $DB->query_first("SELECT type FROM `highwaygroup`.`freight_services` WHERE id=" . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
    $product["product_id"] = sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : "");

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
    }
    $ypos1 += $min_height;
    if ('' != $invoice['ship_product_notes']) {
        $ypos1 -= 4;
        $new_box = new TextBox($invoice['ship_product_notes'], '', $black, '', '', -1);
        $new_box->setMinSize($table_fields['product_name']->width - 32, 0);
        $new_box->setMaxSize($table_fields['product_name']->width - 32, 0);
        $new_box->setPosition($table_fields['product_name']->x1 + 16, $ypos1);
        $new_box->setFontSize(7);
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
if (count($invoice_charges) > 0) {
    foreach ($invoice_charges as $charge) {
        if (1 != $charge['charge_id']) {
            $product                  = array();
            $product['product_id']    = 'C' . str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT);
            $product['product_name']  = $charge['charge_name'];
            $product["product_notes"] = $charge["charge_notes"];
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

if ('' != $invoice_diagonal) {
    $angle_radian = atan($page_info['height'] / $page_info['width']);
    $angle_degree = $angle_radian * 180.0 / M_PI;
    $font_size    = 105;
    $font         = __DIR__ . '/../fonts/Arial.ttf';

    $box1     = imageftbbox(
        $font_size,
        0.0,
        $font,
        $invoice_diagonal,
        array('linespacing' => 1.4)
    );
    $width    = $box1[4] - $box1[0];
    $height   = $font_size;
    $baseline = $height / 4.9;

    $matrix = array(cos($angle_radian), sin($angle_radian), 0 - sin($angle_radian), cos($angle_radian));
    $box1   = array(0, 0, $width, 0, $width, 0 - $height, 0, 0 - $height);
    $box    = array();
    for ($i = 0; $i < 4; $i++) {
        $box[$i << 1]       = $box1[$i << 1] * $matrix[0] + $box1[($i << 1) | 1] * $matrix[1];
        $box[($i << 1) | 1] = $box1[$i << 1] * $matrix[2] + $box1[($i << 1) | 1] * $matrix[3];
    }

    $offset_x = ($page_info['width'] - max(max($box[0], $box[2]), max($box[4], $box[6])) - min(min($box[0], $box[2]), min($box[4], $box[6]))) / 2;
    $offset_y = ($page_info['height'] - max(max($box[1], $box[3]), max($box[5], $box[7])) - min(min($box[1], $box[3]), min($box[5], $box[7]))) / 2;

    /*
    $box2=array();
    for($i=0;$i<4;$i++)
    {
    $box2[$i*2]=$box[$i*2]+$offset_x;
    $box2[$i*2+1]=$box[$i*2+1]+$offset_y;
    }
    imagepolygon($buffer,$box2,4,$black);
     */

    imagefttext(
        $buffer,
        $font_size,
        $angle_degree,
        $offset_x,
        $offset_y,
        $black,
        $font,
        $invoice_diagonal,
        array('linespacing' => 1.4)
    );
}

$size_failure = false;
if ($ypos1 > $ypos2) {
    $size_failure = true;
}

unset($boxes);
$invoice_buffer = $buffer;
return;
