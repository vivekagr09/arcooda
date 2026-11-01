<?php
moduleLoad('invoice');

$page_info = array( //Page size in inches
    'width'        => 7.26,
    'height'       => 10.69,
    'margin'       => 0.010000001,
/*
'width'=>8.26,
'height'=>11.69,
'margin'=>0.5,
 */
    'cell_padding' => 0.083333,
    'row_spacing'  => 0.25,
    'col_spacing'  => 0.25,
    'head_height'  => 0.25,
);
$font_size1 = 9; //Font size in points
$font_size2 = 24; //Font size in points
$font_name  = '/home/common/fonts/Arial.ttf'; //Font filename

$dpi = 89; //Dots per inch
//$dpi=72;                //Dots per inch

$invoice_labels = $DB->query_first(sprintf(
    "SELECT * FROM %s WHERE `company_id` = %d LIMIT 1",
    "`highwaygroup`.`company_info`",
    $invoice['site_id']
), MYSQLI_ASSOC);

$buffer = imagecreate($dpi * $page_info['width'], $dpi * $page_info['height']);
imageantialias($buffer, false);

$white = imagecolorallocate($buffer, 255, 255, 255);
$black = imagecolorallocate($buffer, 0, 0, 0);
//if($show_grey)
$grey = imagecolorallocate($buffer, 221, 221, 221);
//else
//    $grey=$white;

$ypos1 = $dpi * $page_info['margin'];
$ypos2 = $dpi * $page_info['margin'];
$xpos1 = $dpi * $page_info['margin'];
$xpos2 = $dpi * ($page_info['width'] - $page_info['margin']);

$invoice_type = array_search($invoice['invoice_status'], array(
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

//-----------------------------------------------------------------------------
//Start invoice title/date

$widths      = $dpi * 1.5;
$heights     = $dpi * $page_info['head_height'];
$xpos1       = $xpos2 - $widths * 2;
$cells       = array();
$cells[0][0] = ($invoice_type < 3) ? 'QUOTATION' : strtoupper($invoice['invoice_status']) . ' INVOICE';
$cells[0][1] = 'DATE';
$cells[1][0] = invoiceFormatNumber($invoice_id, (9 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']);
$cells[1][1] = date('d - F, Y', ($invoice_type < 3) ? time() : $invoice['proforma_time']);
imagefilledrectangle($buffer, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights * 2, $grey);
foreach ($cells as $key1 => $col) {
    $ypos1 = $ypos2;
    foreach ($col as $key2 => $cell) {
        imagerectangle($buffer, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights, $black);
        $pos = alignText($font_size1, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights, $cell, $font_name);
        imagefttext(
            $buffer,
            $font_size1,
            0.0,
            $pos[0],
            $pos[1],
            $black,
            $font_name,
            $cell,
            array('linespacing' => 1.4)
        );
        $ypos1 += $heights;
    }
    $xpos1 += $widths;
}
unset($cells);

$temp  = $ypos1;
$ypos1 = $ypos2;
$ypos2 = $temp; //Swap $ypos1 and $ypos2
$xpos2 = $xpos1 - $dpi * $page_info['col_spacing'];
$xpos1 = $dpi * $page_info['margin'];

$temp = explode("\n", $invoice_labels['company_invoiceheader']);
$text = array_shift($temp);
$pos  = alignText($font_size2, $xpos1, $ypos1, $xpos1, $ypos1, $text, $font_name, -1, -1);
imagefttext(
    $buffer,
    $font_size2,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$bbox = imageftbbox(
    $font_size2,
    0.0,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$ypos1 += $dpi * $page_info['cell_padding'] + $bbox[3] - $bbox[7];
$text = implode("\n", $temp);
$pos  = alignText($font_size1, $xpos1, $ypos1, $xpos1, $ypos1, $text, $font_name, -1, -1);
imagefttext(
    $buffer,
    $font_size1,
    0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$bbox = imageftbbox(
    $font_size1,
    0.0,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$ypos1 += $dpi * $page_info['cell_padding'] + $bbox[3] - $bbox[7];

$ypos2 = $ypos1 = max($ypos1, $ypos2) + $dpi * $page_info['row_spacing'];
$xpos1 = $dpi * $page_info['margin'];
$xpos2 = $dpi * ($page_info['width'] - $page_info['margin']);

//End invoice title/date
//-----------------------------------------------------------------------------
//Start Invoice addresses

$contacts_list = array();
if ('' != $invoice['bill_address']) {
    $contacts_list['SOLD TO'] = $invoice['bill_name'] . "\n" . $invoice['bill_address'] . "\n" . $invoice['bill_email'];
}

if ('' != $invoice['ship_address']) {
    $contacts_list['SHIP TO'] = $invoice['ship_address'];
}

if ('' != $invoice['notify_address']) {
    $contacts_list['NOTIFY PARTY'] = $invoice['notify_address'] . "\n" . $invoice['notify_email'];
}

$contacts_layout = array_chunk(array_keys($contacts_list), 2);
$padding         = $dpi * $page_info['cell_padding'];

foreach ($contacts_layout as $row) {
    $col1   = reset($row);
    $col2   = next($row);
    $widths = (($xpos2 - $xpos1) - $dpi * $page_info['col_spacing']) / 2;

    $heights = $dpi * $page_info['head_height'];

    imagefilledrectangle($buffer, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights, $grey);
    imagerectangle($buffer, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights, $black);
    $pos = alignText($font_size1, $xpos1 + $padding, $ypos1, $xpos1 + $widths, $ypos2 + $heights, $col1, $font_name, -1);
    imagefttext(
        $buffer,
        $font_size1,
        0.0,
        $pos[0],
        $pos[1],
        $black,
        $font_name,
        $col1,
        array('linespacing' => 1.4)
    );
    if ($col2) {
        imagefilledrectangle($buffer, $xpos2 - $widths, $ypos1, $xpos2, $ypos1 + $heights, $grey);
        imagerectangle($buffer, $xpos2 - $widths, $ypos1, $xpos2, $ypos1 + $heights, $black);
        $pos = alignText($font_size1, $xpos2 + $padding - $widths, $ypos1, $xpos2, $ypos1 + $heights, $col2, $font_name, -1);
        imagefttext(
            $buffer,
            $font_size1,
            0.0,
            $pos[0],
            $pos[1],
            $black,
            $font_name,
            $col2,
            array('linespacing' => 1.4)
        );
    }
    $ypos1 += $heights;

    $bbox1 = imageftbbox(
        $font_size1,
        0.0,
        $font_name,
        $contacts_list[$col1],
        array('linespacing' => 1.4)
    );
    if ($col2) {
        $bbox2 = imageftbbox(
            $font_size1,
            0.0,
            $font_name,
            $contacts_list[$col2],
            array('linespacing' => 1.4)
        );
    } else {
        $bbox2 = array(0, 0, 0, 0, 0, 0, 0, 0);
    }

    $heights = max($bbox1[3] - $bbox1[7], $bbox2[3] - $bbox2[7]) + $dpi * $page_info['cell_padding'] * 2;

    imagerectangle($buffer, $xpos1, $ypos1, $xpos1 + $widths, $ypos1 + $heights, $black);
    $pos = alignText($font_size1, $xpos1 + $padding, $ypos1 + $padding, $xpos1 + $widths, $ypos1 + $heights, $contacts_list[$col1], $font_name, -1, -1);
    imagefttext(
        $buffer,
        $font_size1,
        0.0,
        $pos[0],
        $pos[1],
        $black,
        $font_name,
        $contacts_list[$col1],
        array('linespacing' => 1.4)
    );
    if ($col2) {
        imagerectangle($buffer, $xpos2 - $widths, $ypos1, $xpos2, $ypos1 + $heights, $black);
        $pos = alignText($font_size1, $xpos2 + $padding - $widths, $ypos1 + $padding, $xpos2, $ypos1 + $heights, $contacts_list[$col2], $font_name, -1, -1);
        imagefttext(
            $buffer,
            $font_size1,
            0.0,
            $pos[0],
            $pos[1],
            $black,
            $font_name,
            $contacts_list[$col2],
            array('linespacing' => 1.4)
        );
    }

    $ypos1 += $heights + $dpi * $page_info['row_spacing'];
    $ypos2 = $ypos1;
}

//End Invoice addresses
//-----------------------------------------------------------------------------
//Start Invoice Options

$options             = array();
$options['SALESMAN'] = $all_staff_list[$invoice['handler_id']];

if ($invoice_type > 2) {
    $options['SALE TERM']    = ('' != $invoice['proforma_terms']) ? $invoice['proforma_terms'] : 'TBA';
    $options['PAYMENT TYPE'] = ('' != $invoice['proforma_payterms']) ? $invoice['proforma_payterms'] : 'TBA';
    $options['SHIPMENT VIA'] = ('' != $invoice['proforma_shipvia']) ? $invoice['proforma_shipvia'] : 'TBA';
    $options['TRACKING NO.'] = ('' != $invoice['proforma_tracknumber']) ? $invoice['proforma_tracknumber'] : 'TBA';
} else {
    $options['SALE TERM']    = ('' != $terms_list[$invoice['terms_id']]) ? $terms_list[$invoice['terms_id']] : "TBA";
    $options['PAYMENT TYPE'] = 'TBA';
    $options['SHIPMENT VIA'] = 'TBA';
    $options['TRACKING NO.'] = 'TBA';
}

$widths = array();
foreach ($options as $key => $content) {
    $bbox         = imageftbbox(
        $font_size1,
        0.0,
        $font_name,
        $key,
        array('linespacing' => 1.4)
    );
    $widths[$key] = max($widths[$key], $dpi * $page_info['cell_padding'] * 2 + $bbox[2] - $bbox[0]);
    $bbox         = imageftbbox(
        $font_size1,
        0.0,
        $font_name,
        $content,
        array('linespacing' => 1.4)
    );
    $widths[$key] = max($widths[$key], $dpi * $page_info['cell_padding'] * 2 + $bbox[2] - $bbox[0]);
}

$total = 0;
foreach ($widths as $value) {
    $total += $value;
}

$dpv = ($xpos2 - $xpos1) / $total;

$row_height = $dpi * $page_info['head_height'];
$ypos2 += $row_height;
imagefilledrectangle($buffer, $xpos1, $ypos1, $xpos2, $ypos2, $grey);

foreach ($options as $key => $content) {
    $wide = $dpv * $widths[$key];
    imagerectangle($buffer, $xpos1, $ypos1, $xpos1 + $wide, $ypos2, $black);
    $pos = alignText($font_size1, $xpos1, $ypos1, $xpos1 + $wide, $ypos2, $key, $font_name);
    imagefttext(
        $buffer,
        $font_size1,
        0.0,
        $pos[0],
        $pos[1],
        $black,
        $font_name,
        $key,
        array('linespacing' => 1.4)
    );

    imagerectangle($buffer, $xpos1, $ypos2, $xpos1 + $wide, $ypos2 + $row_height, $black);
    $pos = alignText($font_size1, $xpos1, $ypos2, $xpos1 + $wide, $ypos2 + $row_height, $content, $font_name);
    imagefttext(
        $buffer,
        $font_size1,
        0.0,
        $pos[0],
        $pos[1],
        $black,
        $font_name,
        $content,
        array('linespacing' => 1.4)
    );

    $xpos1 += $wide;
}

$ypos2 += $row_height + $dpi * $page_info['row_spacing'];
$ypos1 = $ypos2;
$xpos1 = $dpi * $page_info['margin'];

//End Invoice Options
//-----------------------------------------------------------------------------
//Start Remarks and Total

$store_ypos = $ypos1;
$ypos1      = $dpi * ($page_info['height'] - $page_info['margin'] - 2.25);
$ypos2      = $dpi * ($page_info['height'] - $page_info['margin']);
$row_height = $dpi * $page_info['head_height'];

$widths = array(0.78, $page_info['width'] - 0.78 * 4 - $page_info['margin'] * 2, 0.78, 0.78, 0.78);

$text = "REMARKS:\n" . $invoice['invoice_remarks'];
$pos  = alignText($font_size1, $xpos1, $ypos1 + $padding * 2, $xpos1, $ypos1 + $padding * 2, $text, $font_name, -1);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);

imagefilledrectangle($buffer, $xpos2 - $dpi * ($widths[4] + $widths[3]), $ypos1, $xpos2 - $dpi * $widths[4], $ypos1 + $row_height * 3, $grey);

$x1   = $xpos2 - $dpi * ($widths[4] + $widths[3]);
$x2   = $xpos2 - $dpi * $widths[4];
$text = 'Sub-Total';
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$x1   = $x2;
$x2   = $xpos2;
$text = $invoice['invoice_offer'];
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$ypos1 += $row_height;

$x1   = $xpos2 - $dpi * ($widths[4] + $widths[3]);
$x2   = $xpos2 - $dpi * $widths[4];
$text = 'Total';
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$x1   = $x2;
$x2   = $xpos2;
$text = $invoice['invoice_offer'];
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$ypos1 += $row_height;

$x1   = $xpos2 - $dpi * ($widths[4] + $widths[3]);
$x2   = $xpos2 - $dpi * $widths[4];
$text = 'Currency';
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$x1   = $x2;
$x2   = $xpos2;
$text = $invoice['invoice_currency'];
$pos  = alignText($font_size1, $x1, $ypos1, $x2, $ypos1 + $row_height, $text, $font_name);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
imagerectangle($buffer, $x1, $ypos1, $x2, $ypos1 + $row_height, $black);

$ypos2 = $ypos1 = $store_ypos;

//End Remarks and Total
//-----------------------------------------------------------------------------
//Start Invoice Products

$widths     = array(0.78, $page_info['width'] - 0.78 * 4 - $page_info['margin'] * 2, 0.78, 0.78, 0.78);
$cells      = array('ITEM NO.', 'ITEM NAME', 'QTY.', 'UNIT PRICE', 'AMOUNT');
$row_height = $dpi * $page_info['head_height'];
$padding    = $dpi * $page_info['cell_padding'];

$ypos2 += $row_height;
imagefilledrectangle($buffer, $xpos1, $ypos1, $xpos2, $ypos2, $grey);
foreach ($widths as $key => $width) {
    $wide = $dpi * $width;
    imagerectangle($buffer, $xpos1, $ypos1, $xpos1 + $wide, $ypos2, $black);
    $pos = alignText($font_size1, $xpos1, $ypos1, $xpos1 + $wide, $ypos2, $cells[$key], $font_name);
    imagefttext(
        $buffer,
        $font_size1,
        0.0,
        $pos[0],
        $pos[1],
        $black,
        $font_name,
        $cells[$key],
        array('linespacing' => 1.4)
    );
    imagerectangle($buffer, $xpos1, $ypos2, $xpos1 + $wide, $dpi * ($page_info['height'] - $page_info['margin'] - 2.25), $black);

    $xpos1 += $wide;
}

foreach ($invoice_products as $key => $product) {
    $xpos1    = $dpi * $page_info['margin'];
    $ypos1    = $ypos2;
    $row_info = array($product['product_id'], $product['product_name'], $product['product_qty'], sprintf('%.2f', $product['product_offer']), sprintf('%.2f', $product['product_offer'] * $product['product_qty']));
    $ypos2 += $row_height;

    if ($grey == $white) {
        imagesetstyle($buffer, array($black, $white));
        imageline($buffer, $xpos1, $ypos2, $xpos2, $ypos2, IMG_COLOR_STYLED);
        imagesetstyle($buffer, array($black));
    } else {
        imageline($buffer, $xpos1, $ypos2, $xpos2, $ypos2, $grey);
    }

    foreach ($widths as $key => $width) {
        $wide = $dpi * $width;
        $pos  = alignText($font_size1, $xpos1 + $padding, $ypos1 + $padding, $xpos1 + $wide - $padding, $ypos2, $row_info[$key], $font_name, (1 == $key) ? -1 : 0, -1);
        imagefttext(
            $buffer,
            $font_size1,
            0.0,
            $pos[0],
            $pos[1],
            $black,
            $font_name,
            $row_info[$key],
            array('linespacing' => 1.4)
        );
        $xpos1 += $wide;
    }
}

$xpos1 = $dpi * $page_info['margin'];
$ypos2 += $dpi * $page_info['row_spacing'];
$ypos1 = $ypos2;

//End Invoice Products
//-----------------------------------------------------------------------------
//Start Invoice Signature

$ypos2 = $dpi * ($page_info['height'] - $page_info['margin']);
$xpos1 = $dpi * ($page_info['width'] - $page_info['margin'] - 0.78 * 3);

$text       = 'Authorised Signature';
$bbox       = imageftbbox(
    $font_size1,
    0.0,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$row_height = $bbox[3] - $bbox[7];
$ypos1      = $ypos2 - $row_height;

$pos = alignText($font_size1, $xpos1, $ypos1, $xpos2, $ypos2, $text, $font_name, 1, 1);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);

if ($grey == $white) {
    imagesetstyle($buffer, array($black, $white));
    imageline($buffer, $xpos1, $ypos1, $xpos2, $ypos1, IMG_COLOR_STYLED);
    imagesetstyle($buffer, array($black));
} else {
    imageline($buffer, $xpos1, $ypos1, $xpos2, $ypos1, $grey);
}

$ypos2 = $ypos1;
$ypos1 -= $dpi * 1;

//Do Signature Here

$ypos2 = $ypos1;

$text       = strtoupper($invoice_labels['company_name']);
$bbox       = imageftbbox(
    $font_size1,
    0.0,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$row_height = $bbox[3] - $bbox[7];
$ypos1      = $ypos2 - $row_height - 2;
$pos        = alignText($font_size1, $xpos1, $ypos1, $xpos2, $ypos2, $text, $font_name, 1, 1);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);

$ypos2 = $ypos1;

$text       = 'FOR AND ON BEHALF OF';
$bbox       = imageftbbox(
    $font_size1,
    0.0,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);
$row_height = $bbox[3] - $bbox[7];
$ypos1      = $ypos2 - $row_height - 2;
$pos        = alignText($font_size1, $xpos1, $ypos1, $xpos2, $ypos2, $text, $font_name, 1, 1);
imagefttext(
    $buffer,
    $font_size1,
    0.0,
    $pos[0],
    $pos[1],
    $black,
    $font_name,
    $text,
    array('linespacing' => 1.4)
);

//End Invoice Signature
//-----------------------------------------------------------------------------

header('Content-Type: image/jpeg');
//imagerectangle($buffer,0,0,imagesx($buffer)-1,imagesy($buffer)-1,$black);
imagejpeg($buffer);
die();

function alignText($font_size, $x1, $y1, $x2, $y2, $text, $font_name, $horiz = 0, $vert = 0)
{
    $bbox   = imageftbbox(
        $font_size,
        0.0,
        $font_name,
        $text,
        array('linespacing' => 1.4)
    );
    $width  = $bbox[2] - $bbox[0];
    $height = $bbox[3] - $bbox[7];
    $xpos   = 0;
    $ypos   = 0;
    if ($vert > 0) {
        $ypos = $y2 - $height;
    } elseif ($vert < 0) {
        $ypos = $y1;
    } else {
        $ypos = ($y2 + $y1 - $height) / 2;
    }

    if ($horiz > 0) {
        $xpos = $x2 - $width;
    } elseif ($horiz < 0) {
        $xpos = $x1;
    } else {
        $xpos = ($x2 + $x1 - $width) / 2;
    }

    return array($xpos - $bbox[0], $ypos - $bbox[7]);
}
