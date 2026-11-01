<?php
moduleLoad('invoice');

//include_once($_SERVER["DOCUMENT_ROOT"] . "/webadmin/functions/matrix_funcs.inc.php");

include_once __DIR__ . '/../classes/MyShape.php';
include_once __DIR__ . '/../classes/MyFrame.php';
include_once __DIR__ . '/../classes/TextBox.php';

global $_GLOBALS;

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

$invoice_labels = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
$invoice_type   = array_search($invoice['invoice_status'], array(
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

$i            = 1;
$color_back   = $i++;
$color_text   = $i++;
$color_shade  = $i++;
$color_title  = $i++;
$color_border = $i++;
$color_debug  = $i++;
$color_grey   = $i++;
$color_white  = $i++;

$font_normal       = '/home/common/fonts/Verdana.ttf';
$font_bold         = '/home/common/fonts/Verdana Bold.ttf';
$classes           = array();
$classes['header'] = array('font_size' => 8, 'font_color' => $color_title, 'pen' => $color_border, 'paper' => $color_shade, 'height' => 20);
$classes['normal'] = array('font_size' => 8, 'font_color' => $color_text, 'pen' => $color_border, 'paper' => $color_back);

function frame_params($vars)
{
    global $font_normal, $font_bold, $classes;

    $frame = new MyFrame();

    $do_text = false;
    $do_rect = false;

    if (is_array($vars['class'])) {
        foreach ($vars['class'] as $classname) {
            if ('' != $classname && isset($classes[$classname])) {
                $vars = array_merge($classes[$classname], $vars);
            }
        }

    } elseif ('' != $vars['class'] && isset($classes[$vars['class']])) {
        $vars = array_merge($classes[$vars['class']], $vars);
    }

    foreach ($vars as $key => $value) {
        if (is_null($value)) {
            unset($vars[$key]);
        }
    }

    $text_box            = new TextBox();
    $text_box->text_font = $font_normal;
    foreach ($vars as $key => $value) {
        $handled = true;
        switch ($key) {
            case 'text':$text_box->text_original = $value;
                break;
            case 'font_size':$text_box->text_size = $value;
                break;
            case 'font_bold':if ($value) {
                    $text_box->text_font = $font_bold;
                }

                break;
            case 'font_color':$text_box->pen_color = $value;
                break;
            case 'padding':$text_box->text_padding = $value;
                break;
            case 'width':$text_box->min_width = $text_box->max_width = $text_box->width = $value;
                break;
            case 'height':$text_box->min_height = $text_box->max_height = $text_box->height = $value;
                break;
            case 'align_x':$text_box->text_horiz = $value;
                break;
            case 'align_y':$text_box->text_vert = $value;
                break;
            case 'min_width':
            case 'min_height':
            case 'max_width':
            case 'max_height':$text_box->$key = $value;
                break;
            default:$handled = false;
                break;
        }
        $do_text = ($do_text || $handled);
    }

    $old_text_box = $text_box;
    if ($do_text) {
        $old_text_box->calculateDimensions();
        $text_box            = new MyFrame('text', $old_text_box->text_x, $old_text_box->text_y);
        $text_box->text      = $old_text_box->text_wrapped;
        $text_box->text_font = $old_text_box->text_font;
        $text_box->text_size = $old_text_box->text_size;
        $text_box->pen_color = $old_text_box->pen_color;
    }

    $rect_box = new MyFrame('rect', 0, 0, $old_text_box->x2, $old_text_box->y2);
    foreach ($vars as $key => $value) {
        $handled = true;
        switch ($key) {
            case 'width':
                $rect_box->vertices[2] = $value;
                $rect_box->vertices[4] = $value;
                break;
            case 'height':
                $rect_box->vertices[5] = $value;
                $rect_box->vertices[7] = $value;
                break;
            case 'pen':$rect_box->set_pen($value);
                break;
            case 'paper':$rect_box->set_paper($value);
                break;
            default:$handled = false;
                break;
        }
        $do_rect = ($do_rect || $handled);
    }

    $x = $y = 0;
    foreach ($vars as $key => $value) {
        switch ($key) {
            case 'x':$x = $value;
                break;
            case 'y':$y = $value;
                break;
        }
    }
    $frame->position_frame(array($x, $y));

    if ($do_rect) {
        $frame->children[] = $rect_box;
    }

    if ($do_text) {
        $frame->children[] = $text_box;
    }

    return $frame;
}

//-----------------------------------------------------------------------------
//HEADER
//-----------------------------------------------------------------------------

$header_frame = new MyFrame();
$header_frame->set_paper($color_debug);

$col_width = floor(1.45 * $dpi);
$xpos      = $page_info['width'] - 2 * $col_width - 1;
$ypos1     = $ypos2     = 0;

$out_text = 'QUOTATION';
if ($invoice_type > 2) {
    $out_text = strtoupper($invoice['invoice_status']) . ' INVOICE';
}

if (in_array($invoice['invoice_status'], array('sales', 'processed'))) {
    $out_text = 'SALES INVOICE';
}

//"TAX INVOICE" etc.
$header_frame->children[] = $frame = frame_params(array('text' => $out_text, 'font_size' => 12, 'font_bold' => true, 'font_color' => $color_white, 'width' => 2 * $col_width, 'pen' => $color_border, 'paper' => $color_grey, 'x' => $xpos, 'padding' => 10));
$ypos2 += 31 + $page_info['row_spacing'];

//"INVOICE #" / "DATE"
$header_frame->children[] = frame_params(array('text' => 'DATE', 'class' => 'header', 'width' => $col_width, 'x' => $xpos + $col_width, 'y' => $ypos2));
$header_frame->children[] = frame_params(array('text' => ($invoice_type < 3) ? 'QUOTATION #' : 'INVOICE #', 'class' => 'header', 'width' => $col_width, 'x' => $xpos, 'y' => $ypos2));
$bounds                   = $header_frame->get_boundaries();
$ypos2                    = $bounds['max'][1];

//invoice # / date
$row_height               = 20;
$header_frame->children[] = frame_params(array('text' => date('jS F, Y', ($invoice_type < 3) ? time() : $invoice['open_time']), 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos + $col_width, 'y' => $ypos2));
$header_frame->children[] = frame_params(array('text' => invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']), 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos, 'y' => $ypos2));
$ypos2 += $row_height;

$temp      = explode("\n", $invoice_labels['company_invoiceheader']);
$logo_dir  = $_SERVER["DOCUMENT_ROOT"] . "/webadmin/images/sites/site_" . $invoice['site_id'] . '/';
$logo_file = $logo_dir . 'logo_invoice.png';

//Company Logo + Address
$invoice_title = array_shift($temp);
if (is_file($logo_file)) {
    $frame = new MyFrame('image', 0, 0, $logo_file);
} else {
    $frame = frame_params(array('text' => $invoice_title, 'width' => $xpos, 'font_size' => 24, 'font_color' => $color_text, 'align_x' => -1));
}

$bounds = $frame->get_boundaries();
$ypos1 += $bounds['max'][1] - $bounds['min'][1] + $page_info['row_spacing'];
$header_frame->children[] = $frame;
$header_frame->children[] = frame_params(array('text' => implode("\n", $temp), 'width' => $xpos, 'font_color' => $color_text, 'align_x' => -1, 'y' => $ypos1, 'padding' => 0));
$bounds                   = $header_frame->get_boundaries();
$ypos                     = $page_info['row_spacing'] + $bounds['max'][1] - $bounds['min'][1];

$contacts_list = array();
if ('' != $invoice['bill_address_full']) {
    $contacts_list['BILL TO'] = $invoice['bill_address_full'];
}

if ('' != $invoice['ship_address_full']) {
    $contacts_list['SHIP TO'] = $invoice['ship_address_full'];
}

if ('' != $invoice['notify_address']) {
    $contacts_list['NOTIFY PARTY'] = $invoice['notify_address'] . "\n" . $invoice['notify_email'];
}

$contacts_layout = array_chunk(array_keys($contacts_list), 2);
$col_width       = ($page_info['width'] - ($page_info['margin'] * 2) + 1) / 2 - 1;

//Contacts
foreach ($contacts_layout as $row) {
    $row_height               = 20;
    $header_frame->children[] = frame_params(array(
        'text' => $row[0], 'class' => 'header', 'width' => $col_width, 'y' => $ypos, 'align_x' => -1,
    ));
    if ('' != $row[1]) {
        $header_frame->children[] = frame_params(array(
            'text' => $row[1], 'class' => 'header', 'width' => $col_width, 'x' => $col_width, 'y' => $ypos, 'align_x' => -1,
        ));
    }

    $bounds = $header_frame->get_boundaries();
    $ypos   = $bounds['max'][1];

    $frame1 = frame_params(array(
        'text' => $contacts_list[$row[0]], 'class' => 'normal', 'width' => $col_width, 'align_x' => -1, 'align_y' => -1,
    ));
    if ('' != $row[1]) {
        $bounds = $frame1->get_boundaries();
        $frame2 = frame_params(array(
            'text' => $contacts_list[$row[1]], 'class' => 'normal', 'min_height' => $bounds['max'][1], 'width' => $col_width, 'align_x' => -1, 'align_y' => -1,
        ));
        $bounds                           = $frame2->get_boundaries();
        $frame1->children[0]->vertices[5] = $frame1->children[0]->vertices[7] = $bounds['max'][1];
        $frame2->position_frame(array($col_width, $ypos));
        $header_frame->children[] = $frame2;
    }
    $frame1->position_frame(array(0, $ypos));
    $header_frame->children[] = $frame1;
    $bounds                   = $header_frame->get_boundaries();
    $ypos += $bounds['max'][1] + $page_info['row_spacing'];
}

$bounds = $header_frame->get_boundaries();
$ypos   = $bounds['max'][1] + $page_info['row_spacing'];

//Invoice Options
$boxes     = array();
$xpos1     = 0;
$xpos2     = $page_info['width'] - 1;
$ypos2     = $ypos1     = 0;
$black     = $color_border;
$white     = $color_back;
$head_back = $color_shade;
$head_text = $color_title;

$temp = array();
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

if ('' != $terms_list[$invoice['terms_id']]) {
    $terms_list = getTermsList();
    $temp[]     = array(
        new TextBox('SHIPPING TERMS', $head_back, $head_text, $black),
        new TextBox($terms_list[$invoice['terms_id']], $white, $black, $black),
    );
}
if ('' != $all_staff_list[$invoice['handler_id']]) {
    $temp[] = array(
        new TextBox('SALES REP', $head_back, $head_text, $black),
        new TextBox($all_staff_list[$invoice['handler_id']], $white, $black, $black),
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

foreach ($boxes as $box) {
    $header_frame->children[] = frame_params(array(
        'text' => $box->text_wrapped, 'font_size' => $box->text_size, 'font_color' => $box->text_color, 'width' => $box->width, 'height' => $box->height, 'pen' => $box->border_color, 'paper' => $box->back_color, 'x' => $box->x1, 'y' => $ypos + $box->y1,
    ));
}

$xpos1 = $page_info['margin'];
$ypos1 = $ypos2 += $page_info['row_spacing'] + $min_height;

//-----------------------------------------------------------------------------

$footer_frame = new MyFrame();
$footer_frame->set_paper($color_debug);

$col_width = $dpi;
$xpos      = $page_info['width'] - 2 * $col_width;
$ypos      = 0;

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

//Comments/Notes
$frame1 = frame_params(array(
    'text' => ('' != $invoice_comments) ? ("COMMENTS/NOTES\n" . $invoice_comments) : '', 'class' => 'normal', 'width' => $xpos, 'align_x' => -1, 'align_y' => -1,
));
$bounds     = $frame1->get_boundaries();
$row_height = max(20, $bounds['max'][1] / ((0 != $gst_offer) ? 4 : 3));

//Sale Amount / $amount
$footer_frame->children[] = frame_params(array(
    'text' => 'Sale Amount', 'class' => 'header', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos, 'y' => $ypos,
));
$footer_frame->children[] = frame_params(array(
    'text' => convertCurrency($sub_total, $invoice['invoice_currency']), 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos + $col_width - 1, 'y' => $ypos,
));
$ypos += $row_height;

//GST 10% / $amount
if (0 != $gst_offer) {
    $footer_frame->children[] = frame_params(array(
        'text' => 'GST 10%', 'class' => 'header', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos, 'y' => $ypos,
    ));
    $footer_frame->children[] = frame_params(array(
        'text' => convertCurrency($gst_offer, $invoice['invoice_currency']), 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos + $col_width - 1, 'y' => $ypos,
    ));
    $ypos += $row_height;
}

//Total / $amount
$footer_frame->children[] = frame_params(array(
    'text' => 'Total', 'class' => 'header', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos, 'y' => $ypos,
));
$footer_frame->children[] = frame_params(array(
    'text' => convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']), 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos + $col_width - 1, 'y' => $ypos,
));
$ypos += $row_height;

//Currency / invoice_currency
$footer_frame->children[] = frame_params(array(
    'text' => 'Currency', 'class' => 'header', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos, 'y' => $ypos,
));
$footer_frame->children[] = frame_params(array(
    'text' => $invoice['invoice_currency'], 'class' => 'normal', 'width' => $col_width, 'height' => $row_height, 'x' => $xpos + $col_width - 1, 'y' => $ypos,
));
$ypos += $row_height;

$bounds                           = $footer_frame->get_boundaries();
$frame1->children[0]->vertices[5] = $frame1->children[0]->vertices[7] = $bounds['max'][1];
$footer_frame->children[]         = $frame1;
$ypos                             = $bounds['max'][1] + $page_info['row_spacing'];

$staff = array();
if (is_null($force_signature)) {
    $staff = $_GLOBALS['staff'];
}

if (in_array($invoice['invoice_status'], array('enquiry', 'inquiry', 'closed'))) {
    $staff['signature'] = '';
}

if (!is_null($force_signature)) {
    $staff['signature'] = $force_signature;
}

$sig_file = $_SERVER["DOCUMENT_ROOT"] . "/images/sigs/" . $staff['signature'];

$ypos2       = 0;
$right_frame = new MyFrame();
$right_frame->position_frame(array($page_info['width'] / 2));
$right_frame->append_transform(matrixRotate(0.1));

$frame = frame_params(array(
    'text' => 'FOR [WEBSITE] PTY LTD', 'font_size' => 8, 'font_color' => $color_text,
));
$bounds = $frame->get_boundaries();
$frame->position_frame(array(0 - $bounds['max'][0], $ypos2, 0, 1));
$right_frame->children[] = $frame;
$ypos2 += $bounds['max'][1] - $bounds['min'][1];

$frame      = new MyFrame('image', 0, 0, $sig_file);
$bounds     = $frame->get_boundaries();
$row_height = $bounds['max'][1] - $bounds['min'][1];
$frame->append_transform(matrixScale(array(0.5 * $dpi / $row_height, 0.5 * $dpi / $row_height)));
$bounds = $frame->get_boundaries();
$frame->position_frame(array(0 - $bounds['max'][0], $ypos2));
$right_frame->children[] = $frame;
$ypos2 += $bounds['max'][1] - $bounds['min'][1];

$frame = new MyFrame('line', 0 - $dpi * 2, 0, 0, 0);
$frame->set_pen($color_border);
$right_frame->children[] = $frame;
$ypos2 += 1;

$frame = frame_params(array(
    'text' => 'Authorised Signatory', 'font_size' => 8, 'font_color' => $color_text,
));
$bounds = $frame->get_boundaries();
$frame->position_frame(array(0 - $bounds['max'][0], $ypos2));
$right_frame->children[] = $frame;
$ypos2 += $bounds['max'][1] - $bounds['min'][1];

$footer_frame->children[] = $right_frame;
/*
if(is_file($sig_file))
{
$sig_image=imagecreatefromstring(file_get_contents($sig_file));

$from_width=imagesx($sig_image);
$from_height=imagesy($sig_image);

//    $scale1=($xpos2-$xpos1)/(3*$from_height);
//    $scale2=($xpos2-$xpos1)/($from_width);
$scale1=2*$dpi/($from_width);
$scale2=0.5*$dpi/($from_height);
$scale=min(1,min($scale1,$scale2));

imagecopyresampled($buffer, $sig_image, $xpos2-($from_width*$scale), $ypos2-($from_height*$scale), 0, 0, $from_width*$scale, $from_height*$scale, $from_width, $from_height);
	if (function_exists('imagedestroy')) {
        imagedestroy($sig_image);
    }
    $sig_image = false;
}

$ypos2-=0.5*$dpi;
//$ypos2-=($xpos2-$xpos1)/3;

$box=new TextBox('FOR '.strtoupper($invoice_labels['company_name']),'',$black,'',$xpos2-$xpos1,1,1);
$box->setPadding(0);
$box->setPosition($xpos2-$box->width,$ypos2-$box->height);
$boxes[]=$box;
$ypos2-=$box->height;

$xpos2=$xpos1;
$xpos1=$page_info['margin'];

$logo_dir = $_SERVER["DOCUMENT_ROOT"] . "/webadmin/images/payment/";
$payment_data = $DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);

$payment_info=false;
switch($invoice['paymenttype_id'])
{
case 'BPAY':
$bpay_logo=imagecreatefrompng($logo_dir.$payment_data['payment_method_image']);
imagecopy($buffer,$bpay_logo,$xpos1,$ypos1-imagesy($bpay_logo),0,0,imagesx($bpay_logo),imagesy($bpay_logo));

//$brn=str_replace("\r\n"," ",chunk_split(bpayGenerateNumber($invoice['member_id'],$invoice['invoice_id']),5));
$brn=str_replace("\r\n"," ",chunk_split(bpayGenerateNumber($invoice['member_id'],0),5));

$box=new TextBox("Biller Code: 424606\nRef: ".$brn,$white,$black,$black,156);
$box->setMinSize(156,64);
$box->setPosition($xpos1+46,$ypos1+4-imagesy($bpay_logo));
$boxes[]=$box;

$ypos1-=imagesy($bpay_logo);
$payment_info=true;
	if (function_exists('imagedestroy')) {
        imagedestroy($bpay_logo);
    }
    $bpay_logo = false;
break;

default:
if($payment_data['payment_method_image']!='' && file_exists($logo_dir.$payment_data['payment_method_image']))
{
$payment_info=true;
$payment_logo=imagecreatefrompng($logo_dir.$payment_data['payment_method_image']);
imagecopy($buffer,$payment_logo,$xpos1,$ypos1-imagesy($payment_logo),0,0,imagesx($payment_logo),imagesy($payment_logo));
if(imagesx($payment_logo)>imagesy($payment_logo))
$ypos1-=imagesy($payment_logo);
else
$xpos1+=imagesx($payment_logo);
	if (function_exists('imagedestroy')) {
        imagedestroy($payment_logo);
    }
    $payment_logo = false;
}

if($payment_data['payment_method_details']!='')
{
$payment_info=true;
$box=new TextBox(strip_tags($payment_data['payment_method_details']),'',$black,'',$xpos2-$xpos1,-1,-1);
$box->setPadding(0);
$box->setPosition($xpos1,$ypos1-$box->height);
$boxes[]=$box;
$ypos1-=$box->height;
}

$xpos1=$page_info['margin'];
break;
}

if($payment_info)
{
$box=new TextBox('Payment Info:','',$black,'',$xpos2-$xpos1,-1,-1);
$box->setPadding(0);
$box->setPosition($xpos1,$ypos1-$box->height);
$boxes[]=$box;
$ypos1-=$box->height;
}

$ypos1=$ypos2=min($ypos1,$ypos2)-$page_info['row_spacing'];
$xpos1=$page_info['margin'];
$xpos2=$page_info['width']-$page_info['margin'];
 */

//-----------------------------------------------------------------------------

$buffer = imagecreate($page_info['width'], $page_info['height']);
imageantialias($buffer, false);
$palette[$color_debug] = imagecolorallocate($buffer, 6 * 17, 8 * 17, 11 * 17);
$shades                = array();
for ($i = 0; $i < 16; $i++) {
    $shades[$i] = imagecolorallocate($buffer, $i * 17, $i * 17, $i * 17);
}

$palette                = array();
$palette[$color_back]   = $shades[15];
$palette[$color_text]   = $shades[0];
$palette[$color_border] = $shades[0];
$palette[$color_grey]   = $shades[8];
$palette[$color_white]  = $shades[15];
switch ($invoice['site_id']) {
    case 1:
        $palette[$color_shade] = imagecolorallocate($buffer, 0, 35, 244);
        $palette[$color_title] = $shades[15];
        break;
    case 2:
        $palette[$color_shade] = imagecolorallocate($buffer, 0, 201, 0);
        $palette[$color_title] = $shades[15];
        break;
    case 9:
        $palette[$color_shade] = imagecolorallocate($buffer, 69, 75, 132);
        $palette[$color_title] = $shades[15];
        break;
    case 16:
        $palette[$color_shade] = imagecolorallocate($buffer, 41, 59, 78);
        $palette[$color_title] = imagecolorallocate($buffer, 226, 238, 250);
        break;
    default:
        $palette[$color_shade] = $shades[7];
        $palette[$color_title] = $shades[0];
        break;
}
//imagefilledrectangle($buffer,0,0,$page_info['width'],$page_info['height'],$palette[$color_back]);

//$header_frame->set_palette($palette);
//$header_frame->draw($buffer);
//$header_frame->draw_bounds($buffer,$palette[$color_border],false);

//$footer_frame->set_palette($palette);
//$footer_frame->draw($buffer);
//$footer_frame->draw_bounds($buffer,$palette[$color_border],2);
$right_frame->set_palette($palette);
$right_frame->draw($buffer);
$right_frame->draw_bounds($buffer, $palette[$color_border], 1);

//$middle_frame->set_palette($palette);
//$middle_frame->draw($buffer);
//$middle_frame->draw_bounds($buffer,$palette[$color_border],false);

header('Content-Type: image/png');
imagepng($buffer);
die();

//End invoice title/date
//-----------------------------------------------------------------------------
//Start Invoice Options

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

$ypos1 = $ypos2 = $page_info['height'] - $page_info['margin'];

//Signature is about the size of the last 3 columns
$xpos1 = ($table_fields['product_qty']->x2 + $table_fields['product_qty']->x1) / 2;

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

if (in_array($invoice['invoice_status'], array('enquiry', 'inquiry', 'closed'))) {
    $staff['signature'] = '';
}

if (!is_null($force_signature)) {
    $staff['signature'] = $force_signature;
}

$sig_dir  = $_SERVER["DOCUMENT_ROOT"] . "/images/sigs/";
$sig_file = $sig_dir . $staff['signature'];

if (is_file($sig_file)) {
    $sig_image = imagecreatefromstring(file_get_contents($sig_file));

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

$logo_dir     = $_SERVER["DOCUMENT_ROOT"] . '/webadmin/images/payment/';
$payment_data = $DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);

$payment_info = false;
switch ($invoice['paymenttype_id']) {
    case 'BPAY':
        $bpay_logo = imagecreatefrompng($logo_dir . $payment_data['payment_method_image']);
        imagecopy($buffer, $bpay_logo, $xpos1, $ypos1 - imagesy($bpay_logo), 0, 0, imagesx($bpay_logo), imagesy($bpay_logo));

        //$brn=str_replace("\r\n"," ",chunk_split(bpayGenerateNumber($invoice['member_id'],$invoice['invoice_id']),5));
        $brn = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], 0), 5));

        $box = new TextBox("Biller Code: 424606\nRef: " . $brn, $white, $black, $black, 156);
        $box->setMinSize(156, 64);
        $box->setPosition($xpos1 + 46, $ypos1 + 4 - imagesy($bpay_logo));
        $boxes[] = $box;

        $ypos1 -= imagesy($bpay_logo);
        $payment_info = true;
        if (function_exists('imagedestroy')) {
            imagedestroy($bpay_logo);
        }
        $bpay_logo = false;
        break;

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
            $box          = new TextBox(strip_tags($payment_data['payment_method_details']), '', $black, '', $xpos2 - $xpos1, -1, -1);
            $box->setPadding(0);
            $box->setPosition($xpos1, $ypos1 - $box->height);
            $boxes[] = $box;
            $ypos1 -= $box->height;
        }

        $xpos1 = $page_info['margin'];
        break;
}

if ($payment_info) {
    $box = new TextBox('Payment Info:', '', $black, '', $xpos2 - $xpos1, -1, -1);
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
    $boxes[] = new MyFrame('rect', $box->x1, $ypos1, $box->x2, $ypos2, $black, $white);
}

//-----------------------------------------------------------------------------

if (count($invoice_products) > 0) {
    foreach ($invoice_products as $product) {
        $product['product_total'] = sprintf('%.2f', $product['product_qty'] * sprintf('%.2f', $product['product_offer']));
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

        $boxes[] = new MyFrame('line', $xpos1, $ypos1, $xpos2, $ypos1, IMG_COLOR_STYLED);
    }
}

if (0 != $invoice['ship_id']) {
    $product                  = array();
    $product['product_id']    = (('y' == $invoice['ship_insurance']) ? 'N' : 'F') . str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT);
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

    $boxes[] = new MyFrame('line', $xpos1, $ypos1, $xpos2, $ypos1, IMG_COLOR_STYLED);
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

$main_frame        = new MyFrame(64, 64, 256, 256, -1, -1);
$main_frame->boxes = $boxes;
$main_frame->draw($buffer);

//foreach($boxes as $box)
//    $box->draw($buffer);

$stamp_file = $_SERVER["DOCUMENT_ROOT"] . '/webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.gif';
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
