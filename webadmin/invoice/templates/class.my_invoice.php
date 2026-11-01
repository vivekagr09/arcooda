<?php
include_once __DIR__ . '/../classes/MyShape.php';
include_once __DIR__ . '/../classes/TextBox.php';

class MyInvoice
{
    public $page_info = array(
        'width'        => 7.26,
        'height'       => 10.69,
        'margin'       => 0.0,
        'row_spacing'  => 0.25,
        'cell_padding' => 0.09,
        'font_large'   => 0.27,
        'font_medium'  => 0.18,
        'font_small'   => 0.10,
    );
    public $dpi = 89;
    public $x1  = 0;
    public $y1  = 0;

    public $text_color   = '';
    public $shade_color  = '';
    public $border_color = '';
    public $back_color   = '';

    public $company_name     = '';
    public $company_address  = '';
    public $invoice_title    = 'INVOICE';
    public $invoice_comments = '';

    public $cells_title   = array();
    public $cells_contact = array();
    public $cells_general = array();
    public $cells_total   = array();
    public $products      = array();

    public $file_paylogo   = '';
    public $file_signature = '';

    public function addTitleCell($title, $value)
    {
        $this->cells_title[] = array($title, $value);
    }
    public function addContact($title, $value)
    {
        $this->cells_contact[] = array($title, $value);
    }
    public function addGeneralCell($title, $value)
    {
        $this->cells_general[] = array($title, $value);
    }
    public function addTotalCell($title, $value)
    {
        $this->cells_total[] = array($title, $value);
    }
    public function addProduct($product_id, $product_name, $product_qty, $product_offer, $product_note = '')
    {
        $product = array(
            'product_id'    => $product_id,
            'product_name'  => $product_name,
            'product_qty'   => $product_qty,
            'product_offer' => $product_offer,
            'product_note'  => $product_note,
        );
        if ($product_offer > 0) {
            $product_offer            = sprintf('%.2f', $product_offer);
            $product['product_offer'] = $product_offer;
            $product['product_total'] = sprintf('%.2f', $product_offer * $product_qty);
        } else {
            $product['product_total'] = $product_offer;
        }

        $this->products[] = $product;
    }
    public function getDimensions()
    {
        $dim = $this->page_info;
        foreach ($dim as $key => $value) {
            $dim[$key] *= $this->dpi;
        }

        return $dim;
    }
    public function draw($img)
    {
        $dim = $this->getDimensions();
        $x1  = $this->x1 + $dim['margin'];
        $y1  = $this->y1 + $dim['margin'];
        $x2  = $this->x1 + $dim['width'] - $dim['margin'];
        $y2  = $this->y1 + $dim['height'] - $dim['margin'];

        $templates            = array();
        $templates['default'] = new TextBox();
        $templates['default']->setData(array(
            'text_size'    => $dim['font_small'],
            'text_color'   => $this->text_color,
            'text_padding' => 0,
        ));
        $templates['th'] = $templates['default'];
        $templates['th']->setData(array(
            'back_color'   => $this->shade_color,
            'text_padding' => $dim['cell_padding'],
            'border_color' => $this->border_color,
        ));
        $templates['th-left'] = $templates['default'];
        $templates['th-left']->setData(array(
            'back_color'   => $this->shade_color,
            'text_padding' => $dim['cell_padding'],
            'border_color' => $this->border_color,
            'text_horiz'   => -1,
            'text_vert'    => -1,
        ));
        $templates['td'] = $templates['default'];
        $templates['td']->setData(array(
            'back_color'   => $this->back_color,
            'text_padding' => $dim['cell_padding'],
            'border_color' => $this->border_color,
        ));
        $templates['td-left'] = $templates['default'];
        $templates['td-left']->setData(array(
            'back_color'   => $this->back_color,
            'text_padding' => $dim['cell_padding'],
            'border_color' => $this->border_color,
            'text_horiz'   => -1,
            'text_vert'    => -1,
        ));

        $ybounds = array('top' => $y1, 'bottom' => $y2);

        //Header
        $xbounds = array('left' => $x1, 'right' => $x2);
        $ypos    = $ybounds['top'];
        $box     = $templates['default'];
        $box->setData(array('text_size' => $dim['font_medium'], 'text_original' => $this->invoice_title));
        $box->setPosition($xbounds['right'] - $box->width, $ypos);
        $box->draw($img);
        $ypos = $box->y2 + $dim['cell_padding'];

        $table       = array();
        $row_heights = 0;
        $col_widths  = 0;
        foreach ($this->cells_title as $title_cell) {
            $col = array($templates['th'], $templates['td']);
            for ($i = 0; $i < 2; $i++) {
                $col[$i]->setText($title_cell[$i]);
                $col_widths  = max($col_widths, $col[$i]->width);
                $row_heights = max($row_heights, $col[$i]->height);
            }
            $table[] = $col;
        }
        foreach ($table as $key1 => $col) {
            foreach ($col as $key2 => $box) {
                $table[$key1][$key2]->setData(array('min_width' => $col_widths, 'max_width' => $col_widths, 'min_height' => $row_heights, 'max_height' => $row_heights));
            }
        }

        $xpos = $xbounds['right'] - 1;
        foreach ($table as $key1 => $col) {
            $xpos -= $col_widths - 1;
            $col[0]->setPosition($xpos, $ypos);
            $col[1]->setPosition($xpos, $ypos + $row_heights - 1);
            $col[0]->draw($img);
            $col[1]->draw($img);
        }

        $xbounds['right'] = $xpos - $dim['cell_padding'];
        $temp             = $ypos + 2 * $row_heights + $dim['row_spacing'];
        $ypos             = $ybounds['top'];
        $ybounds['top']   = $temp;

        $box = $templates['default'];
        $box->setData(array('text_original' => $this->company_name, 'text_size' => $dim['font_large'], 'max_width' => $xbounds['right'] - $xbounds['left']));
        $box->setPosition($xbounds['left'], $ypos);
        $box->draw($img);
        $ypos = $box->y2 + $dim['cell_padding'];

        $box = $templates['default'];
        $box->setData(array('text_original' => $this->company_address, 'max_width' => $xbounds['right'] - $xbounds['left']));
        $box->setPosition($xbounds['left'], $ypos);
        $box->draw($img);
        $ypos = $box->y2;

        $ybounds['top'] = max($ybounds['top'], $ypos + $dim['row_spacing']);
        $ypos           = $ybounds['top'];

        //Contacts
        $xbounds       = array('left' => $x1, 'right' => $x2);
        $contact_rows  = array_chunk(array_keys($this->cells_contact), 2);
        $contact_width = ($xbounds['right'] + 1 - $xbounds['left']) / 2;

        if (count($contact_rows) > 0) {
            foreach ($contact_rows as $row) {
                $box1 = $templates['th-left'];
                $box1->setData(array('text_original' => $this->cells_contact[$row[0]][0], 'min_width' => $contact_width, 'max_width' => $contact_width));
                $box1->setPosition($xbounds['left'], $ypos);
                $row_height = $box1->height;
                if ($row[1]) {
                    $box2 = $templates['th-left'];
                    $box2->setData(array('text_original' => $this->cells_contact[$row[1]][0], 'min_width' => $contact_width, 'max_width' => $contact_width));
                    $box2->setPosition($xbounds['right'] - $contact_width, $ypos);
                    $row_height = max($row_height, $box2->height);
                    $box1->setData(array('min_height' => $row_height, 'max_height' => $row_height));
                    $box2->setData(array('min_height' => $row_height, 'max_height' => $row_height));
                    $box2->draw($img);
                }
                $box1->draw($img);
                $ypos += $row_height - 1;

                $box1 = $templates['td-left'];
                $box1->setData(array('text_original' => $this->cells_contact[$row[0]][1], 'min_width' => $contact_width, 'max_width' => $contact_width));
                $box1->setPosition($xbounds['left'], $ypos);
                $row_height = $box1->height;
                if ($row[1]) {
                    $box2 = $templates['td-left'];
                    $box2->setData(array('text_original' => $this->cells_contact[$row[1]][1], 'min_width' => $contact_width, 'max_width' => $contact_width));
                    $box2->setPosition($xbounds['right'] - $contact_width, $ypos);
                    $row_height = max($row_height, $box2->height);
                    $box1->setData(array('min_height' => $row_height, 'max_height' => $row_height));
                    $box2->setData(array('min_height' => $row_height, 'max_height' => $row_height));
                    $box2->draw($img);
                }
                $box1->draw($img);
                $ypos += $row_height + $dim['row_spacing'];
            }
        }
        $ybounds['top'] = $ypos;

        $temp        = array();
        $row_heights = array();
        foreach ($this->cells_general as $key => $value) {
            $col    = array();
            $col[0] = $templates['th'];
            $col[0]->setText($value[0]);
            $row_heights[0] = max($row_heights[0], $col[0]->height);
            $col[1]         = $templates['td'];
            $col[1]->setText($value[1]);
            $row_heights[1] = max($row_heights[1], $col[1]->height);
            $temp[]         = $col;
        }
        $row_widths = array();
        foreach ($temp as $key => $value) {
            $row_widths[$key] = $value[0]->width;
            $temp[$key][0]->setData(array('min_width' => $row_widths[$key], 'min_height' => $row_heights[0], 'max_height' => $row_heights[0]));
            $temp[$key][1]->setData(array('min_width' => $row_widths[$key], 'max_height' => $row_heights[1]));
        }

//-----------------------------------------------------------------------------
//NOTE: I don't remember how this part worked

        $xpos1 = $xbounds['left'];
        $xpos2 = $xbounds['right'];
        $ypos1 = $ypos2 = $ypos;
        $ypos2 += $row_heights[0] - 1;

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
            $scale     = ($xbounds['right'] - $xbounds['left'] - $fixed_width) / $loose_width;
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
        $add_width = (($xpos2 - $xpos1) - $total_width - 1) / count($temp);
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
            $temp[$key][0]->draw($img);
            $temp[$key][1]->draw($img);
        }
        unset($temp);

        $ybounds['top'] = $ypos2 + $dim['row_spacing'] + $min_height;

//NOTE: END NOTE
//-----------------------------------------------------------------------------

        //Signature
        $ypos = $ybounds['bottom'];
        $xpos = $xbounds['right'];
        $box  = $templates['default'];
        $box->setText('Authorised Signatory');
        $box->setPosition($xbounds['right'] - $box->width, $ypos - $box->height);
        $box->draw($img);
        $ypos = $box->y1;
        $xpos = min($xbounds['right'] - $box->width, $xpos);
        $box  = new MyShape($xbounds['right'] - 3 * $this->dpi, $ypos, $xbounds['right'], $ypos, 'line', $this->border_color);
        $box->draw($img);
        $xpos = min($xbounds['right'] - 3 * $this->dpi, $xpos);
        if ('' != $this->file_signature && $sig_img = imagecreatefromstring(file_get_contents($this->file_signature))) {
            imagecopy($img, $sig_img, $xbounds['right'] - imagesx($sig_img), $ypos - imagesy($sig_img), 0, 0, imagesx($sig_img), imagesy($sig_img));
            $ypos -= imagesy($sig_img);
            $xpos = min($xbounds['right'] - imagesx($sig_img), $xpos);
        } else {
            $ypos -= $this->dpi;
        }

        $box = $templates['default'];
        $box->setText('FOR ' . $this->company_name);
        $box->setPosition($xbounds['right'] - $box->width, $ypos - $box->height);
        $box->draw($img);
        $xpos = min($xbounds['right'] - $box->width, $xpos);
        $ypos -= $box->height;
        $xbounds['right'] = $xpos;

        $temp              = $ypos;
        $ypos              = $ybounds['bottom'];
        $ybounds['bottom'] = $temp;

        //Payment Info
        $xpos = $xbounds['left'];
        if ('' != $this->file_paylogo && $pay_img = imagecreatefromstring(file_get_contents($this->file_paylogo))) {
            imagecopy($img, $pay_img, $xpos, $ypos - imagesy($pay_img), 0, 0, imagesx($pay_img), imagesy($pay_img));
            $ypos -= imagesy($pay_img);
        }
        $ybounds['bottom'] = min($ypos, $ybounds['bottom']);
        $ybounds['bottom'] -= $dim['row_spacing'];
        $xbounds = array('left' => $x1, 'right' => $x2);

        $table = $this->cells_total;

        $boxes = array();
        foreach ($table as $row_id => $row) {
            $boxes[0][$row_id] = $templates['th'];
            $boxes[0][$row_id]->setText($row[0]);
            $boxes[1][$row_id] = $templates['td'];
            $boxes[1][$row_id]->setText($row[1]);
        }
        $row_height = 0;
        foreach ($boxes as $col_id => $col) {
            foreach ($col as $row_id => $cell) {
                $boxes[$col_id][$row_id]->setData(array('min_width' => $this->dpi, 'max_width' => $this->dpi));
                $row_height = max($row_height, $cell->height);
            }
        }

        foreach ($boxes as $col_id => $col) {
            foreach ($col as $row_id => $cell) {
                $boxes[$col_id][$row_id]->setData(array('min_height' => $row_height, 'max_height' => $row_height));
            }
        }

        $box = $templates['td-left'];
        $box->setData(array('text_original' => " ", 'min_width' => $xbounds['right'] + 2 - $xbounds['left'] - 2 * $this->dpi, 'max_width' => $xbounds['right'] + 2 - $xbounds['left'] - 2 * $this->dpi, 'min_height' => count($table) * $row_height));
        if ('' != $this->invoice_comments) {
            $box->setText("COMMENTS/NOTES:\n" . $this->invoice_comments);
        }

        if ($box->height > $box->min_height) {
            $addme = $box->height - $box->min_height;
            $div   = $addme / count($table);
            foreach ($boxes as $col_id => $col) {
                foreach ($col as $row_id => $cell) {
                    $boxes[$col_id][$row_id]->setData(array('min_height' => $cell->min_height + $div, 'max_height' => $cell->max_height + $div));
                }
            }
        }

        $ypos = $ybounds['bottom'];
        $ypos -= $box->height;
        $box->setPosition($xbounds['left'], $ypos);
        $box->draw($img);
        $xpos = $xbounds['right'] + 2 - 3 * $this->dpi;
        foreach ($boxes as $col_id => $col) {
            $xpos += $this->dpi - 1;
            $ypos = $ybounds['bottom'] - $box->height;
            foreach ($col as $row_id => $cell) {
                if (0 == $row_id) {
                    $cell->setPosition($xpos, $ypos);
                } else {
                    $cell->setPosition($xpos, $ypos - 1);
                }

                $cell->y2 += 1;
                $cell->draw($img);
                $ypos += $cell->height;
            }
        }
        $ybounds['bottom'] -= $box->height;

        $table      = array('ITEM ID', 'ITEM NAME', 'QTY', 'PRICE EACH', 'AMOUNT');
        $row_height = 0;
        $xpos       = $xbounds['left'];
        $ypos       = $ybounds['top'];
        $boxes      = array();
        foreach ($table as $col_id => $field) {
            $box = $templates['th'];
            if (1 == $col_id) {
                $box->setData(array('min_width' => ($xbounds['right'] - $xbounds['left']) - (count($table) - 1) * $this->dpi, 'max_width' => ($xbounds['right'] - $xbounds['left']) - (count($table) - 1) * $this->dpi));
            } else {
                $box->setData(array('min_width' => $this->dpi + 1, 'max_width' => $this->dpi + 1));
            }

            $box->setText($field);
            $row_height = max($row_height, $box->height);
            $box->setPosition($xpos, $ypos);
            $xpos += $box->width - 1;
            $boxes[] = $box;
        }
        foreach ($boxes as $col_id => $field) {
            $boxes[$col_id]->setData(array('min_height' => $row_height, 'max_height' => $row_height));
            $boxes[$col_id]->draw($img);
        }
        $ypos += $row_height;
        $ybounds['top'] = $ypos - 1;

        $xpos  = $xbounds['left'];
        $ypos  = $ybounds['top'];
        $boxes = array();
        foreach ($table as $col_id => $field) {
            $box = new MyShape($xpos, $ypos, $xpos + $this->dpi, $ybounds['bottom'], 'rect', $this->border_color, $this->back_color);
            if (1 == $col_id) {
                $box->x2 = $box->x1 + ($xbounds['right'] - $xbounds['left']) - (count($table) - 1) * $this->dpi - 1;
            }

            $xpos = $box->x2;
            $box->draw($img);
        }
    }
};

$country_list = getCountryList();

$invoice_labels = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = " . $invoice['site_id'] . " LIMIT 1", MYSQLI_ASSOC);
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

$jpeg_invoice = new MyInvoice();

$temp                          = explode("\n", $invoice_labels['company_invoiceheader']);
$jpeg_invoice->company_name    = array_shift($temp);
$jpeg_invoice->company_address = implode("\n", $temp);
$jpeg_invoice->invoice_title   = ($invoice_type > 2) ? (strtoupper($invoice['invoice_status']) . ' INVOICE') : 'QUOTATION';

$jpeg_invoice->addTitleCell('DATE', date('d - F, Y', ($invoice_type < 3) ? time() : $invoice['proforma_time']));
$jpeg_invoice->addTitleCell(($invoice_type < 3) ? 'QUOTATION #' : 'INVOICE #', invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type || 9 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']));

if ('' != $invoice['bill_address']) {
    $jpeg_invoice->addContact('BILL TO', $invoice['bill_name'] . "\n" . $invoice['bill_address'] . "\n" . $invoice['bill_email']);
}

if ('' != $invoice['ship_address']) {
    $jpeg_invoice->addContact('SHIP TO', $invoice['ship_address']);
}

if ('' != $invoice['notify_address']) {
    $jpeg_invoice->addContact('NOTIFY PARTY', $invoice['notify_address'] . "\n" . $invoice['notify_email']);
}

if ('' != $invoice['bill_name']) {
    $jpeg_invoice->addGeneralCell('CONTACT', $invoice['bill_name']);
}

if ('' != $invoice['paymenttype_id']) {
    $jpeg_invoice->addGeneralCell('PAYMENT TYPE', $invoice['paymenttype_id']);
}

if ('' != $all_staff_list[$invoice['handler_id']]) {
    $jpeg_invoice->addGeneralCell('SALES REP', $all_staff_list[$invoice['handler_id']]);
}

if (false) {
    $jpeg_invoice->addGeneralCell('SHIP VIA', 'unfinished');
}

if ('' != $invoice['proforma_tracknumber']) {
    $jpeg_invoice->addGeneralCell('TRACKING #', $invoice['proforma_tracknumber']);
}

if ('' != $country_list[$invoice['ship_country']]) {
    $jpeg_invoice->addGeneralCell('SHIP TO', $country_list[$invoice['ship_country']]);
}

foreach ($invoice_products as $product) {
    $jpeg_invoice->addProduct(
        $product['product_id'],
        $product['product_name'],
        $product['product_qty'],
        $product['product_offer'],
        $product['product_note']
    );
}

if (0.0 != $invoice['products_offer']) {
    $jpeg_invoice->addTotalCell('Sale Amount', sprintf('%.2f', $invoice['products_offer']));
}

if (0.0 != $invoice['ship_offer']) {
    $jpeg_invoice->addTotalCell('Shipping', sprintf('%.2f', $invoice['ship_offer']));
}

if (0.0 != $invoice['charges_offer']) {
    $jpeg_invoice->addTotalCell('GST 10%', sprintf('%.2f', $invoice['charges_offer']));
}

if (0.0 != $invoice['invoice_offer']) {
    $jpeg_invoice->addTotalCell('Total', sprintf('%.2f', $invoice['invoice_offer']));
}

if ('' != $invoice['invoice_currency']) {
    $jpeg_invoice->addTotalCell('Currency', $invoice['invoice_currency']);
}

if ($invoice['freight_offer'] > 0) {
    $jpeg_invoice->addProduct('F01', 'Freight', '1', $invoice['freight_offer']);
}

$dim    = $jpeg_invoice->getDimensions();
$buffer = imagecreate($dim['width'], $dim['height']);
if (function_exists('imageantialias')) {
    imageantialias($buffer, false);
}

$allow_shades = true;

$white = imagecolorallocate($buffer, 255, 255, 255);
$black = imagecolorallocate($buffer, 0, 0, 0);
$blue  = $white;

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

if ($allow_shades) {
    $blue = imagecolorallocate($buffer, 6 * 17, 8 * 17, 11 * 17);
}

imagefilledrectangle($buffer, 0, 0, imagesx($buffer), imagesy($buffer), $blue);

$jpeg_invoice->text_color   = $black;
$jpeg_invoice->border_color = $black;
$jpeg_invoice->shade_color  = $shades[14];
$jpeg_invoice->back_color   = $white;

//print_r($jpeg_invoice);
//die();

$jpeg_invoice->draw($buffer);

$invoice_buffer = $buffer;
return;

$table_fields = array();

$box = new TextBox('ITEM ID', $grey, $black, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos1, $ypos1);
$table_fields['product_id'] = $box;
$xpos1                      = $box->x2 - 1;

$box = new TextBox('AMOUNT', $grey, $black, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 - $dpi, $ypos1);
$table_fields['product_total'] = $box;
$xpos2                         = $box->x1;

$box = new TextBox('PRICE EACH', $grey, $black, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 + 1 - $dpi, $ypos1);
$table_fields['product_offer'] = $box;
$xpos2                         = $box->x1;

$box = new TextBox('QTY', $grey, $black, $black, $dpi);
$box->setMinSize($dpi, 0);
$box->setPosition($xpos2 + 1 - $dpi, $ypos1);
$table_fields['product_qty'] = $box;
$xpos2                       = $box->x1;

$box = new TextBox('ITEM NAME', $grey, $black, $black, $table_fields['product_qty']->x1 + 1 - $table_fields['product_id']->x2);
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
$xpos1 = $table_fields['product_qty']->x1;

$box = new TextBox('Authorised Signatory', '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = &$box;
$ypos2 -= $box->height;

imageline($buffer, $xpos1, $ypos2, $xpos2, $ypos2, $black);

//Leave space
$ypos2 -= ($xpos2 - $xpos1) / 3;

$box = new TextBox('FOR ' . strtoupper($invoice_labels['company_name']), '', $black, '', $xpos2 - $xpos1, 1, 1);
$box->setPadding(0);
$box->setPosition($xpos2 - $box->width, $ypos2 - $box->height);
$boxes[] = $box;
$ypos2 -= $box->height;

$xpos2 = $xpos1;
$xpos1 = $page_info['margin'];

$logo_dir     = '/home/hwgroup/public_html/webadmin/images/payment/';
$payment_data = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);

$payment_info = false;
switch ($invoice['paymenttype_id']) {
    case 'BPAY':
        $bpay_logo = imagecreatefrompng($logo_dir . $payment_data['payment_method_image']);
        imagecopy($buffer, $bpay_logo, $xpos1, $ypos1 - imagesy($bpay_logo), 0, 0, imagesx($bpay_logo), imagesy($bpay_logo));

        $box = new TextBox("Biller Code:\nRef:", $unfinished, $black, $black, 156);
        $box->setMinSize(156, 64);
        $box->setPosition($xpos1 + 46, $ypos1 + 4 - imagesy($bpay_logo));
        $boxes[] = $box;

        $ypos1 -= imagesy($bpay_logo);
        $payment_info = true;
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

$gst = sprintf('%.2f', $invoice['invoice_offer'] / 11.0);
if ('AU' != $invoice['ship_country']) {
    $gst = 'N/A';
}

$sub_total = $invoice['invoice_offer'] - $gst;

$new_boxes    = array();
$new_boxes[0] = new TextBox($invoice['invoice_currency'], $white, $black, $black, $dpi);
$new_boxes[1] = new TextBox(sprintf('%.2f', $invoice['invoice_offer']), $white, $black, $black, $dpi);
$new_boxes[2] = new TextBox($gst, $white, $black, $black, $dpi);
$new_boxes[3] = new TextBox(sprintf('%.2f', $sub_total), $white, $black, $black, $dpi);
$new_boxes[4] = new TextBox("Currency", $grey, $black, $black, $dpi);
$new_boxes[5] = new TextBox("Total", $grey, $black, $black, $dpi);
$new_boxes[6] = new TextBox("GST 10%", $grey, $black, $black, $dpi);
$new_boxes[7] = new TextBox("Sale Amount", $grey, $black, $black, $dpi);
$new_boxes[8] = new TextBox("COMMENTS/NOTES\n\n\n_", $white, $black, $black, $table_fields['product_qty']->x2 + 1 - $xpos1, -1, -1);

//Bound sizes
for ($i = 0; $i < 8; $i++) {
    $new_boxes[$i]->setMinSize($dpi, 0);
    $new_boxes[$i]->setMaxSize($dpi, 0);
}
$new_boxes[8]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, 0);
$new_boxes[8]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1);

//Fetch calced sizes
$min_height = $new_boxes[0]->height;
for ($i = 0; $i < 8; $i++) {
    $min_height = max($min_height, $new_boxes[$i]->height);
}

if ($new_boxes[8]->height > $min_height * 4) {
    $min_height = $new_boxes[8]->height / 4;
}

//Fix sizes
for ($i = 0; $i < 8; $i++) {
    $new_boxes[$i]->setMinSize($dpi, $min_height + 1);
    $new_boxes[$i]->setMaxSize($dpi, $min_height + 1);
}
$new_boxes[8]->setMinSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * 4 + 1);
$new_boxes[8]->setMaxSize($table_fields['product_qty']->x2 + 1 - $xpos1, $min_height * 4 + 1);

//Position boxes
$ypos2 -= $min_height;
for ($i = 0; $i < 8; $i++) {
    $x = $table_fields['product_offer']->x1;
    if (($i&4) == 0) {
        $x = $table_fields['product_offer']->x2;
    }

    $y = $ypos2 - ($i&3) * $min_height;
    $new_boxes[$i]->setPosition($x, $y);
    $boxes[] = &$new_boxes[$i];
}
$new_boxes[8]->setPosition($xpos1, $ypos2 - 3 * $min_height);
$boxes[] = &$new_boxes[8];

$ypos2 -= 3 * $min_height;

foreach ($table_fields as $key => $box) {
    $boxes[] = new MyShape($box->x1, $ypos1, $box->x2, $ypos2, 'rect', $black, $white);
}

//-----------------------------------------------------------------------------

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
        $new_box->text_font = __DIR__ . '/../fonts/Arial Italic.ttf';
        $boxes[]            = &$new_box;
        $ypos1 += $new_box->height + 4;
    }

    $boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);
}

$new_boxes = array();
$new_box   = new TextBox('F01', '', $black, '', $dpi);
$new_box->setMinSize($dpi, 0);
$new_box->setMaxSize($dpi, 0);
$new_box->setPosition($table_fields['product_id']->x1, $ypos1);
$new_boxes[] = &$new_box;

$new_box = new TextBox('1', '', $black, '', $dpi);
$new_box->setMinSize($dpi, 0);
$new_box->setMaxSize($dpi, 0);
$new_box->setPosition($table_fields['product_qty']->x1, $ypos1);
$new_boxes[] = &$new_box;

$new_box = new TextBox(sprintf('%.2f', $invoice['ship_offer']), '', $black, '', $dpi);
$new_box->setMinSize($dpi, 0);
$new_box->setMaxSize($dpi, 0);
$new_box->setPosition($table_fields['product_offer']->x1, $ypos1);
$new_boxes[] = &$new_box;

$new_box = new TextBox(sprintf('%.2f', $invoice['ship_offer']), '', $black, '', $dpi);
$new_box->setMinSize($dpi, 0);
$new_box->setMaxSize($dpi, 0);
$new_box->setPosition($table_fields['product_total']->x1, $ypos1);
$new_boxes[] = &$new_box;

$min_height = 0;
foreach ($new_boxes as $box) {
    $min_height = max($box->height, $min_height);
}

foreach ($new_boxes as $field => $box) {
    $new_boxes[$field]->setMinSize($dpi, $min_height);
    $new_boxes[$field]->setMaxSize($dpi, $min_height);
    $boxes[] = &$new_boxes[$field];
}
$ypos1 += $min_height;

$boxes[] = new MyShape($xpos1, $ypos1, $xpos2, $ypos1, 'line', IMG_COLOR_STYLED);

imagesetstyle($buffer, array($black, $white));
foreach ($boxes as $box) {
    $box->draw($buffer);
}

unset($boxes);

$invoice_buffer = $buffer;
return;
