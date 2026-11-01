<?php
include __DIR__ . '/setup_invoice.inc.php';

moduleLoad('tcpdf');

set_time_limit(100);

define('FONT_HEIGHT_CONSTANT', 0.41);
define('LINE_BREAK_HEIGHT', 4.0);
define('FONT_BASELINE_CONSTANT', 4.9);

class TCPDFInvoice extends TCPDF
{
    public $style = array();

    public $textAlign;
    public $fontSize;
    public $padding;
    public $border;
    public $headcol;
    public $bodycol;
    public $contactcol;
    public $productcol;
    public $layoutrow;
    public $fill;
    public $class_buffer = array();
    public function __construct()
    {
        $args = func_get_args();
        if (count($args)) {
            call_user_func_array(array('parent', '__construct'), $args);
        } else {
            $this->__construct();
        }

        $this->SetFont('helvetica', '', $this->fontSize = 8);
        $this->SetMargins(12.7, 12.7);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
    }
    public function border_string()
    {
        $result = '';
        if ($this->border&1) {
            $result .= 'T';
        }

        if ($this->border&2) {
            $result .= 'B';
        }

        if ($this->border&4) {
            $result .= 'L';
        }

        if ($this->border&8) {
            $result .= 'R';
        }

        return $result;
    }
    public function class_pop()
    {
        array_pop($this->class_buffer);
        $this->class_apply('base');
        foreach ($this->class_buffer as $class) {
            $this->class_apply($class);
        }
    }
    public function class_push($class)
    {
        $this->class_buffer[] = $class;
        $this->class_apply($class);
    }
    public function styles_apply($styles)
    {
        static $sides = array('top', 'right', 'bottom', 'left');
        $styles       = preg_replace(array('/\s\s+/', '/;$/'), array(' ', ''), $styles);
        foreach (explode(';', $styles) as $group) {
            $split               = explode(':', $group);
            $style               = trim(array_shift($split));
            $values              = explode(' ', trim(implode(':', $split)));
            $this->style[$style] = implode(' ', $values);
            $nested_call         = array();

            switch ($style) {
                case 'border':
                case 'margin':
                case 'padding':
                    foreach ($sides as $side) {
                        $nested_call[] = $style . '-' . $side . ':' . implode(' ', $values);
                    }

                    break;
                case 'border-color':
                case 'border-style':
                case 'border-width':
                    $temp       = explode('-', $style);
                    $main_style = array_shift($temp);
                    foreach ($sides as $side) {
                        $nested_call[] = $main_style . '-' . $side . '-' . implode('-', $temp) . ':' . implode($values);
                    }

                    break;
                case 'background':
                    foreach (array('color', 'image', 'repeat', 'attachment', 'position') as $sub_style) {
                        if (count($values)) {
                            $nested_call[] = $style . '-' . $sub_style . ':' . array_shift($values);
                        }
                    }

                    break;
                case 'border-top':
                case 'border-right':
                case 'border-bottom':
                case 'border-left':
                    foreach (array('width', 'style', 'color') as $sub_style) {
                        if (count($values)) {
                            $nested_call[] = $style . '-' . $sub_style . ':' . array_shift($values);
                        }
                    }

                    break;
                case 'font':
                    foreach (array('style', 'variant', 'weight', 'size', 'family') as $sub_style) {
                        if (count($values)) {
                            $nested_call[] = $style . '-' . $sub_style . ':' . array_shift($values);
                        }
                    }

                    break;
                case 'outline':
                    foreach (array('color', 'style', 'width') as $sub_style) {
                        if (count($values)) {
                            $nested_call[] = $style . '-' . $sub_style . ':' . array_shift($values);
                        }
                    }

                    break;
            }

            if (count($nested_call)) {
                $this->styles_apply($nested_call);
            }
        }
    }
    public function class_apply($class)
    {
        switch ($class) {
            case 'base':
                $this->styles_apply('background-color:transparent;border:1px none black;color:black;font:normal normal normal medium helvetica;padding:0px;text-align:left;vertical-align:top;');

                $this->SetFontSize($this->fontSize = 8);
                $this->SetTextColor(0x00, 0x00, 0x00);
                $this->SetDrawColor(0x00, 0x00, 0x00);
                $this->textAlign = 'L';
                $this->padding   = 0;
                $this->border    = 0;
                $this->fill      = 0;
                break;
            case 'box_header':
                global $invoice;
                $this->border |= 15;
                switch ($invoice['site_id']) {
                    case 1:
                        $this->styles_apply('background-color:#0023f4;color:white;');
                        $this->SetFillColor(0x00, 0x23, 0xF4);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 2:
                        $this->styles_apply('background-color:#1E901D;color:white;');
                        $this->setFillColor(30, 144, 29);
                        //$this->SetFillColor(0x1E, 0x90, 0x1D);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 3:
                        $this->styles_apply('background-color:#99CCFF;color:black;');
                        $this->setFillColor(0x99, 0xCC, 0xFF);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        $this->border = 0;
                        break;
                    case 9:
                        $this->styles_apply('background-color:#454b84;color:white;');
                        $this->SetFillColor(0x45, 0x4B, 0x84);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 16:
                        $this->styles_apply('background-color:#293b4e;color:#e2eefa;');
                        $this->SetFillColor(0x29, 0x3B, 0x4E);
                        $this->SetTextColor(0xE2, 0xEE, 0xFA);
                        break;
                    case 18:
                        $this->styles_apply('background-color:#0c9544;color:white;');
                        $this->SetFillColor(0x0C, 0x95, 0x44);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 19:
                        $this->styles_apply('background-color:#3cb54c;color:white;');
                        $this->SetFillColor(0x3C, 0xB5, 0x4C);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 20:
                        $this->styles_apply('background-color:#029e4e;color:white;');
                        $this->SetFillColor(0x02, 0x9E, 0x4E);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 22:
                        $this->styles_apply('background-color:#B8317D;color:white;');
                        $this->SetFillColor(0xB8, 0x31, 0x7D);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 24:
                        $this->styles_apply('background-color:#8FBE3C;color:black;');
                        $this->SetFillColor(0x8F, 0xBE, 0x3C);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                    case 30:
                        $this->styles_apply('background-color:#1B75BB;color:white;');
                        $this->SetFillColor(0x1b, 0x75, 0xbb);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 39:
                        $this->styles_apply('background-color:#1b75bb;color:white;');
                        $this->SetFillColor(0x1b, 0x75, 0xbb);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    default:
                        $this->styles_apply('background-color:#777777;color:black;');
                        $this->SetFillColor(0x77, 0x77, 0x77);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                }
                $this->styles_apply('border-style:solid;padding:1px;');
                $this->fill    = 1;
                $this->padding = 1;
                break;
            case 'box_shaded':
                $this->styles_apply('background-color:#777777;color:white;border-style:solid;padding:1px;');
                $this->fill = 1;
                $this->SetFillColor(0x77, 0x77, 0x77);
                $this->SetTextColor(0xFF, 0xFF, 0xFF);
                $this->padding = 1;
                $this->border |= 15;
                break;
            case 'box_normal':
                $this->styles_apply('border-style:solid;padding:1px;');
                $this->border |= 15;
                $this->padding = 1;
                break;
            case 'box_product':
                $this->styles_apply('padding:1px;text-align:center;border-style:solid;');
                $this->padding   = 1;
                $this->textAlign = 'C';
                $this->border |= 15;
                break;
            case 'company_logo':
                $this->styles_apply('font-size:x-large;');
                $this->SetFontSize($this->fontSize = 24);
                break;
            case 'invoice_type':
                $this->styles_apply('font-size:large;');
                $this->SetFontSize($this->fontSize = 12);
                break;
            case 'bpay_reference':
                $this->styles_apply('padding:5px;');
                $this->padding = 5.0;
                break;
            case 'bpay_logo':
                $this->styles_apply('font-size:small;background-color:#ffffff;');
                $this->SetFontSize($this->fontSize = 7.9);
                $this->SetFillColor(0xFF, 0xFF, 0xFF);
                $this->fill = 1;
                break;
            case 'preview_invoice':
                $this->styles_apply('background-color:#ffffff;');
                $this->SetFontSize($this->fontSize = 36);
                $this->SetFillColor(0xFF, 0xFF, 0xFF);
                $this->fill = 1;
        }
    }
    public function Header()
    {
        global $invoice_id, $invoice, $invoice_products, $invoice_charges;
        //From setup_invoice.inc.php
        global $charges_list, $country_list, $images, $invoice_inforow, $invoice_labels, $invoice_reference, $invoice_type, $invoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url, $invoice_diagonal, $action;

        $this->headcol    = array(0 => 12.7, 1 => 121.92, 2 => 156.21, 3 => 190.5);
        $this->contactcol = array(0 => 12.7, 1 => 101.6, 2 => 190.5);
        $this->productcol = array(0 => 12.7, 1 => 33.02, 2 => 53.34, 3 => 109.22, 4 => 129.54, 5 => 149.86, 6 => 171.18, 7 => 190.5);
        $this->layoutrow  = array(0 => 12.7);

        $ypos1 = $ypos2 = 12.7;
        $temp  = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader']));
        if ('AU' == $invoice['bill_country'] && 'AU' == $invoice['ship_country']) {
            $temp = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader_au']));
        }

        $this->class_push('base');
        if ('' != $images['company_logo']) {
            $image_size = getimagesize($website_path . $images['company_logo']);
            $x_space    = min($this->headcol[1] - $this->headcol[0], $image_size[0] * 25.4 / 96.0);
            $y_space    = min(25.4, $image_size[1] * 25.4 / 96.0);
            if ($image_size[0] * $y_space > $image_size[1] * $x_space) {
                $this->Image($website_path . $images['company_logo'], $this->headcol[0], $ypos1, $x_space, 0);
            } else {
                $this->Image($website_path . $images['company_logo'], $this->headcol[0], $ypos1, 0, $y_space);
            }

            $this->SetXY($this->headcol[0], $ypos1 + $y_space + LINE_BREAK_HEIGHT);
            array_shift($temp);
        } else {
            $this->SetXY($this->headcol[0], $ypos1);
            $this->class_push('company_logo');
            $this->MultiCell($this->headcol[1] - $this->headcol[0], $this->fontSize * FONT_HEIGHT_CONSTANT, array_shift($temp), $this->border_string(), $this->textAlign, $this->fill);
            $this->class_pop();
        }
        $this->MultiCell($this->headcol[1] - $this->headcol[0], $this->fontSize * FONT_HEIGHT_CONSTANT, implode("\n", $temp), $this->border_string(), $this->textAlign, $this->fill);
        $this->Ln(LINE_BREAK_HEIGHT);
        $ypos1 = $this->GetY();
        $this->class_push('box_shaded');
        $this->class_push('invoice_type');
        $this->textAlign = 'C';
        $this->SetXY($this->headcol[1], $ypos2);
        $this->DoTableRow();
        $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $invoice_type_text);
        $this->DoEndTableRow();
        $this->class_pop();
        $this->class_pop();
        $this->Ln(LINE_BREAK_HEIGHT);
        $this->class_push('box_header');
        $this->textAlign = 'C';
        $this->SetX($this->headcol[1]);
        $this->DoTableRow();
        $this->DoTableCell($this->headcol[2] - $this->headcol[1], 0, ($invoice_type < 3) ? 'QUOTATION #' : 'INVOICE #');
        $this->DoTableCell($this->headcol[3] - $this->headcol[2], 0, 'DATE');
        $this->DoEndTableRow();
        $this->class_pop();
        $this->class_push('box_normal');
        $this->textAlign = 'C';
        $this->SetX($this->headcol[1]);
        $this->DoTableRow();
        $this->DoTableCell($this->headcol[2] - $this->headcol[1], 0, invoiceFormatNumber($invoice_id, ((3 == $invoice_type || 4 == $invoice_type || 9 == $invoice_type) ? $invoice['revision_id'] : null), $invoice['site_id']));
        $this->DoTableCell($this->headcol[3] - $this->headcol[2], 0, date('jS F, Y', $time_to_use));
        $this->DoEndTableRow();
        $this->class_pop();
        $this->Ln(LINE_BREAK_HEIGHT);
        $ypos2 = $this->GetY();
        $ypos1 = $ypos2 = max($ypos1, $ypos2);

        // now set the preview text if set...
        if ('' != $invoice_diagonal) {
            $this->class_push('preview_invoice');
            $this->textAlign = 'C';
            $this->SetXY($this->headcol[0], 12.7);
            $this->MultiCell(178, 36, "\n" . $invoice_diagonal . "\n\n", 0, 'C', 1);
            $this->class_pop();
        }

        $this->SetY($ypos1);
        $this->layoutrow[] = $ypos1;
        $contacts_list     = array();
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
        }
        $contacts_layout = array_chunk(array_keys($contacts_list), 2);
        foreach ($contacts_layout as $row) {
            $this->class_push('box_header');
            $this->SetX($this->contactcol[0]);
            $this->DoTableRow();
            if ('' != $row[0]) {
                $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $row[0]);
            }

            if ('' != $row[1]) {
                $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0, $row[1]);
            }

            $this->DoEndTableRow();
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
            $this->class_push('box_normal');
            $this->SetX($this->contactcol[0]);
            $this->DoTableRow();
            if ('' != $row[0]) {
                $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $contacts_list[$row[0]]);
            }

            if ('' != $row[1]) {
                $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0, $contacts_list[$row[1]]);
            }

            $this->DoEndTableRow();
            $this->class_pop();
            $this->Ln(LINE_BREAK_HEIGHT);
            $ypos1 = $ypos2 = $this->GetY();
        }
        $this->layoutrow[] = $ypos1;
        if (count($invoice_inforow) > 0) {
            $total_width = 0;
            foreach ($invoice_inforow as $key => $value) {
                $total_width += max($this->GetStringWidth($key), $this->GetStringWidth($value));
            }

            $max_width = $this->contactcol[2] - $this->contactcol[0];
            $this->class_push('box_header');
            $this->textAlign = 'C';
            $this->SetX($this->contactcol[0]);
            $this->DoTableRow();
            foreach ($invoice_inforow as $key => $value) {
                $percent = max($this->GetStringWidth($key), $this->GetStringWidth($value)) / $total_width;
                $this->DoTableCell($max_width * $percent, 0, $key);
            }
            $this->DoEndTableRow();
            $this->class_pop();
            $this->class_push('box_normal');
            $this->textAlign = 'C';
            $this->SetX($this->contactcol[0]);
            $this->DoTableRow();
            foreach ($invoice_inforow as $key => $value) {
                $percent = max($this->GetStringWidth($key), $this->GetStringWidth($value)) / $total_width;
                $this->DoTableCell($max_width * $percent, 0, $value);
            }
            $this->DoEndTableRow();
            $this->class_pop();
            $this->Ln(LINE_BREAK_HEIGHT);
        }
        $ypos1             = $ypos2             = $this->GetY();
        $this->layoutrow[] = $ypos1;
    }
    public function Footer()
    {
        global $invoice_id, $invoice, $invoice_products, $invoice_charges;
        //From setup_invoice.inc.php
        global $charges_list, $country_list, $images, $invoice_inforow, $invoice_labels, $invoice_reference, $invoice_type, $invoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url, $invoice_diagonal;

        /*if($invoice_diagonal!='')
        {
        $angle_radian=atan($this->h/$this->w);
        $angle_degree=$angle_radian*180.0/M_PI;
        $this->SetFontSize($this->fontSize = 105);
        $width=$this->GetStringWidth($invoice_diagonal);
        $height=$this->fontSize*FONT_HEIGHT_CONSTANT;
        $baseline=$height/FONT_BASELINE_CONSTANT;
        $matrix=array(cos($angle_radian),sin($angle_radian),0-sin($angle_radian),cos($angle_radian));
        $box1=array(0,$baseline,$width,$baseline,$width,$baseline-$height,0,$baseline-$height);
        $box=array();
        for($i=0;$i<4;$i++)
        {
        $box[$i<<1]=$box1[$i<<1]*$matrix[0]+$box1[($i<<1)|1]*$matrix[1];
        $box[($i<<1)|1]=$box1[$i<<1]*$matrix[2]+$box1[($i<<1)|1]*$matrix[3];
        }
        $offset_x=($this->w-max(max($box[0],$box[2]),max($box[4],$box[6]))-min(min($box[0],$box[2]),min($box[4],$box[6])))/2;
        $offset_y=($this->h-max(max($box[1],$box[3]),max($box[5],$box[7]))-min(min($box[1],$box[3]),min($box[5],$box[7])))/2;
        /*
        $box2=array();
        for($i=0;$i<4;$i++)
        {
        $box2[$i*2]=$box[$i*2]+$offset_x;
        $box2[$i*2+1]=$box[$i*2+1]+$offset_y;
        }
        $this->Polygon($box2);
         *//*
    $x=$this->GetX();
    $y=$this->GetY();
    $this->SetXY(0,0);
    $this->StartTransform();
    $this->Translate($offset_x,$offset_y);
    $this->Rotate($angle_degree,0,0);
    $this->Text(0,0,$invoice_diagonal);
    $this->StopTransform();
    $this->SetXY($x,$y);
    }*/
    }
    public function Middle()
    {
        global $invoice_id, $invoice, $invoice_products, $invoice_charges, $DB;
        //From setup_invoice.inc.php
        global $charges_list, $country_list, $images, $invoice_inforow, $invoice_labels, $invoice_reference, $invoice_type, $invoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url;

        $ypos1 = $ypos2 = $this->GetY();
        $this->class_push('box_header');
        $this->textAlign = 'C';
        $this->SetX($this->productcol[0]);
        $this->DoTableRow();
        if (9 == $invoice_type) {
            $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, 'SUPPLIER');
            $this->DoTableCell($this->productcol[2] - $this->productcol[1], 0, 'ITEM ID');
            $this->DoTableCell($this->productcol[3] - $this->productcol[2], 0, 'ITEM NAME');
            $this->DoTableCell($this->productcol[4] - $this->productcol[3], 0, 'QTY');
            $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, 'PRICE EACH');
            $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, 'PRICE EACH (AUD)');
            $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, 'AMOUNT');
        } else {
            $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, 'ITEM ID');
            $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, 'ITEM NAME');
            $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, 'QTY');
            $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, 'PRICE EACH');
            $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, 'AMOUNT');
        }
        $this->DoEndTableRow();
        $this->class_pop();
        $ypos1 = $ypos2 = $this->GetY();
        if (is_array($invoice_products)) {
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
                    $this->class_push('box_product');
                    if ('' != $product['product_notes']) {
                        $this->border = ($this->border&~2);
                    }

                    $this->SetX($this->productcol[0]);
                    $this->DoTableRow();
                    if (9 == $invoice_type) {
                        $this->textAlign = 'L';
                        $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, $product['supplier_name']);
                        $this->textAlign = 'C';
                        $this->DoTableCell($this->productcol[2] - $this->productcol[1], 0, $product['product_id']);
                        $this->textAlign = 'L';
                        $this->DoTableCell($this->productcol[3] - $this->productcol[2], 0, $product['product_name']);
                        $this->textAlign = 'C';
                        $this->DoTableCell($this->productcol[4] - $this->productcol[3], 0, $product['product_qty']);
                        $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, $product['product_offer']);
                        $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, convertCurrency($product['product_costau'], 'AUD'));
                    } else {
                        $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, $product['product_id']);
                        $this->textAlign = 'L';
                        $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $product['product_name']);
                        $this->textAlign = 'C';
                        $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, $product['product_qty']);
                        $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, $product['product_offer']);
                    }
                    $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, $product['product_total']);
                    $this->DoEndTableRow();
                    if ('' != $product['product_notes']) {
                        $this->border    = ($this->border ^ 3);
                        $this->textAlign = 'L';
                        $this->SetFont('helvetica', 'I');
                        $this->SetX($this->productcol[0]);
                        $this->DoTableRow();
                        $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                        if (9 == $invoice_type) {
                            $this->DoTableCell($this->productcol[2] - $this->productcol[1], 0, '');
                            $this->DoTableCell($this->productcol[3] - $this->productcol[2], 0, $product['product_notes']);
                            $this->DoTableCell($this->productcol[4] - $this->productcol[3], 0, '');
                        } else {
                            $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $product['product_notes']);
                        }
                        $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                        $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, '');
                        $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, '');
                        $this->DoEndTableRow();
                        $this->SetFont('helvetica', '');
                    }
                    $this->class_pop();
                    $ypos1 = $ypos2 = $this->GetY();
                    if ($ypos1 >= 275) {
                        $this->AddPage();
                        $ypos1 = $ypos = $this->GetY();
                    }
                }
            }
        }

        if (0 != $invoice['ship_id'] && 9 != $invoice_type) {
            $this->class_push('box_product');
            if ('' != $invoice['ship_product_notes']) {
                $this->border = ($this->border&~2);
            }

            $this->SetX($this->productcol[0]);
            $this->DoTableRow();

            if (20 == $invoice["site_id"]) {
                $row = $DB->query_first("SELECT `type` FROM `gdelta_gdelta`.`freight_services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            } elseif (39 == $invoice["site_id"]) {
                $row = $DB->query_first("SELECT `product_type` AS `type` FROM `freight3`.`services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            } else {
                $row = $DB->query_first("SELECT `type` FROM `highwaygroup`.`freight_services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            }
            $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : ""));

            $this->textAlign = 'L';
            $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $invoice['ship_product_name']);
            $this->textAlign = 'C';
            $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
            $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']));
            $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']));
            $this->DoEndTableRow();
            if ('' != $invoice['ship_product_notes']) {
                $this->border    = ($this->border ^ 3);
                $this->textAlign = 'L';
                $this->SetFont('helvetica', 'I');
                $this->SetX($this->productcol[0]);
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $invoice['ship_product_notes']);
                $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, '');
                $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, '');
                $this->DoEndTableRow();
                $this->SetFont('helvetica', '');
            }
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
            if ($ypos1 >= 275) {
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }
        }

        $sub_total = $invoice['invoice_offer'];
        $gst_offer = 0;
        if (is_array($invoice_charges) && 9 != $invoice_type) {
            foreach ($invoice_charges as $charge) {
                if (1 != $charge['charge_id']) {
                    $this->class_push('box_product');
                    if ('' != $invoice['ship_product_notes']) {
                        $this->border = ($this->border&~2);
                    }

                    $this->SetX($this->productcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, sprintf('C%02d', $charge['charge_id']));
                    $this->textAlign = 'L';
                    $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $charges_list[$charge['charge_id']]);
                    $this->textAlign = 'C';
                    $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                    $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, convertCurrency($charge['charge_offer'], $invoice['invoice_currency']));
                    $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, convertCurrency($charge['charge_offer'], $invoice['invoice_currency']));
                    $this->DoEndTableRow();
                    $this->class_pop();
                    $ypos1 = $ypos2 = $this->GetY();
                    if ($ypos1 >= 275) {
                        $this->AddPage();
                        $ypos1 = $ypos = $this->GetY();
                    }
                } else {
                    $gst_offer += convertCurrency($charge['charge_offer'], $invoice['invoice_currency']);
                }
            }
        }
        $sub_total -= $gst_offer;
        if ($this->GetY() < 227.0) {
            $height = 227.0 - $this->GetY();
            $this->class_push('box_product');
            $this->SetX($this->productcol[0]);
            $this->DoTableRow();
            $this->DoTableCell($this->productcol[1] - $this->productcol[0], $height, '');
            if (9 == $invoice_type) {
                $this->DoTableCell($this->productcol[2] - $this->productcol[1], $height, '');
                $this->DoTableCell($this->productcol[3] - $this->productcol[2], $height, '');
                $this->DoTableCell($this->productcol[4] - $this->productcol[3], $height, '');
            } else {
                $this->DoTableCell($this->productcol[4] - $this->productcol[1], $height, '');
            }
            $this->DoTableCell($this->productcol[5] - $this->productcol[4], $height, '');
            $this->DoTableCell($this->productcol[6] - $this->productcol[5], $height, '');
            $this->DoTableCell($this->productcol[7] - $this->productcol[6], $height, '');
            $this->DoEndTableRow();
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
        }
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
        foreach ($temp as $key => $value) {
            $this->SetX($this->productcol[5]);
            $this->DoTableRow();
            $this->class_push('box_header');
            $this->textAlign = 'C';
            $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, $key);
            $this->class_pop();
            $this->class_push('box_product');
            $this->textAlign = 'C';
            $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, $value);
            $this->class_pop();
            $this->DoEndTableRow();
        }
        $ypos2    = $this->GetY();
        $the_text = array();
        if ('' != $invoice['supply_notes'] && 9 == $invoice_type) {
            $the_text[] = "REMARKS:\n" . $invoice['supply_notes'];
        }

        if ('' != $invoice['invoice_comments'] && 9 != $invoice_type) {
            $the_text[] = "COMMENTS/NOTES:\n" . $invoice['invoice_comments'];
        }

        if (0 != $invoice['waranty_terms_id'] && 9 != $invoice_type) {
            $the_text[] = "WARRANTY TERMS:\n" . $warranty_terms_list[$invoice['waranty_terms_id']];
        }

        $the_text = implode("", $the_text);
        $this->class_push('box_product');
        $this->class_push('box_notes');
        $this->SetXY($this->productcol[0], $ypos1);
        $this->DoTableRow();
        $tmp             = $this->textAlign;
        $this->textAlign = 'L';
        $this->DoTableCell($this->productcol[5] - $this->productcol[0], $ypos2 - $ypos1 - 2.0, $the_text);
        $this->textAlign = $tmp;
        $this->DoEndTableRow();
        $this->class_pop();
        $this->class_pop();
        $this->SetY(max($this->GetY(), $ypos2));
        $this->Ln(LINE_BREAK_HEIGHT);
        $ypos1 = $ypos2 = $this->GetY();
        if ($ypos1 >= 275) {
            $this->AddPage();
            $ypos1 = $ypos = $this->GetY();
        }
        $this->layoutrow[] = $ypos1;

        $the_text     = array();
        $payment_info = false;
        switch ($invoice['paymenttype_id']) {
            case 'BPAY':
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[4] - $this->productcol[0], 0, 'Payment Information:');
                $this->DoEndTableRow();
                if ('' != $images['payment_method']) {
                    $this->Image($website_path . $images['payment_method'], $this->headcol[0], $this->GetY(), 50.8, 0);
                }

                $this->class_push('bpay_reference');
                $this->DoTableRow();
                $this->DoTableCell(10, 17.3, '');
                $this->DoTableCell(40.8, 17.3, "Biller Code: 424606\nRef: " . $invoice_reference);
                $this->DoEndTableRow();
                $this->class_pop();
/*
$this->class_push('bpay_logo');
$this->DoTableRow();
$this->DoTableCell(50.8, 18, "Telephone & Internet Banking - BPAY\nCall your bank, credit union or building society to make this payment from your cheque, savings or credit card account.\nMore info: www.bpay.com.au");
$this->DoEndTableRow();
$this->class_pop();
 */
                $this->SetY($ypos1);
                break;
            default:
                if ('' != $payment_data['payment_method_details']) {
                    $the_text[] = 'Payment Information:';
                    $the_text[] = trim($payment_data['payment_method_details']);
                    $this->SetX($this->productcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->productcol[4] - $this->productcol[0], 0, implode("\n", $the_text));
                    $this->DoEndTableRow();
                }
                break;
        }

        if (9 != $invoice_type) {
            if ($ypos1 >= 275) {
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }
            if ('' != $images['stamp']) {
                $this->Image($website_path . $images['stamp'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 3.0, $ypos1 - 3.0, 0, 25.4);
            }

            if ('' != $images['signature']) {
                $this->Image($website_path . $images['signature'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 5.0, $ypos1 + 2.0, 0, 12.7);
            }

            $this->textAlign = 'R';
            $this->SetXY($this->productcol[4], $ypos1);
            //$this->BackgroundColor='none';
            $this->DoTableRow();
            $this->DoTableCell($this->productcol[7] - $this->productcol[4], 0, 'FOR ' . strtoupper($invoice_labels['company_name']) . ((20 != $invoice["site_id"]) ? ' PTY' : '') . ' LTD');
            $this->DoEndTableRow();
            $this->Ln(LINE_BREAK_HEIGHT);
            $this->textAlign = 'R';
            $this->SetX($this->productcol[4]);
            $this->DoTableRow();
            $this->DoTableCell($this->productcol[7] - $this->productcol[4], 0, 'Authorised Signatory');
            $this->DoEndTableRow();
        }
    }

    private $block_level   = 0;
    private $content_stack = array();

    public function DoStartBlock($type, $class = '', $style = '')
    {
        $this->block_level++;
        $this->class_apply($class);
        $this->styles_apply($style);
        $this->content_stack[$this->block_level]['style']    = $this->style;
        $this->content_stack[$this->block_level]['type']     = $type;
        $this->content_stack[$this->block_level]['children'] = array();
    }
    public function DoEndBlock()
    {
        $this->content_stack[$this->block_level - 1]['children'][] = $this->content_stack[$this->block_level];
        unset($this->content_stack[$this->block_level]);
        $this->block_level--;
        $this->style = $this->content_stack[$this->block_level];
    }
    public function DoTextNode($text)
    {
        $this->content_stack[$this->block_level]['children'][] = $text;
    }
    public function DoCalcSize($node, $xpos, $ypos, $width = '', $height = '')
    {
        $left = $right = $xpos;
        $top  = $bottom  = $ypos;
        foreach ($node['children'] as $child) {
            if (is_string($child)) {
                $right = $width + $left;
            } elseif (is_array($child)) {
                $result = $this->DoCalcSize($child, $xpos, $ypos, $width, $height);
                $right  = max($xpos + $result[2], $right);
                $bottom = max($ypos + $result[3], $right);
                $xpos   = $result[0];
                $ypos   = $result[1];
            }
        }
        switch ($node['style']['display']) {
            case 'block':
                return array($left, $ypos, $right - $left, $bottom - $top);
            default:
            case 'inline':
                return array($right, $top, $right - $left, $bottom - $top);
        }
    }

    private $table_row_content = array();
    private $table_row_height  = 0;

    public function DoTableCell($width, $height = 0, $text = '')
    {
        $temp_style             = $this->style;
        $temp_style['abs_left'] = $this->GetX();
        $temp_style['abs_top']  = $this->GetY();
        if ('' != $width) {
            $temp_style['width'] = $width;
        }

        if ('' != $height) {
            $temp_style['height'] = $height;
        }

        //$current_table_row[] = array($temp_style

        $xpos = $this->GetX();
        $ypos = $this->GetY();
        if (0 == $height) {
            $height = $this->fontSize * FONT_HEIGHT_CONSTANT;
        }

        //$width-=$this->padding;
        $height = max($height, $this->NbLines($width - 2.0, $text) * $this->fontSize * FONT_HEIGHT_CONSTANT);
        $height += 2 * $this->padding;
        $this->table_row_content[] = array($xpos, $ypos, $width, $height, $text, $this->class_buffer, $this->textAlign, $this->fontSize, $this->padding, $this->border, $this->fill);
        $this->table_row_height    = max($this->table_row_height, $height);
        $this->SetX($xpos + $width);
    }
    public function DoTableRow()
    {
        $this->table_row_height  = $this->fontSize * FONT_HEIGHT_CONSTANT;
        $this->table_row_content = array();
    }
    public function DoEndTableRow()
    {
        $xpos1 = $xpos2 = $this->GetX();
        $ypos1 = $ypos2 = $this->GetY();
        foreach ($this->table_row_content as $cell) {
            $this->class_push('base');
            foreach ($cell[5] as $class) {
                $this->class_apply($class);
            }

            $this->border = $cell[9];
            $this->fill   = $cell[10];
            $this->SetXY($cell[0], $cell[1]);
            $this->Cell($cell[2], $this->table_row_height, '', $this->border_string(), 0, '', $this->fill);
            $this->class_pop();
        }
        foreach ($this->table_row_content as $cell) {
            $this->class_push('base');
            foreach ($cell[5] as $class) {
                $this->class_apply($class);
            }

            $this->fontSize = $cell[7];
            $this->SetXY($cell[0] + $cell[8], $cell[1] + $cell[8]);
            $this->MultiCell($cell[2] - 2 * $cell[8], $this->fontSize * FONT_HEIGHT_CONSTANT, $cell[4], 0, $cell[6]);
            $this->class_pop();
        }
        $this->Ln(LINE_BREAK_HEIGHT);
        $this->SetY($ypos1 + $this->table_row_height);
        $this->table_row_content = array();
    }
    public function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if (0 == $w) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s    = str_replace("\r", '', $txt);
        $nb   = strlen($s);
        if ($nb > 0 && "\n" == $s[$nb - 1]) {
            $nb--;
        }

        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ("\n" == $c) {
                $i++;
                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;
                continue;
            }
            if (' ' == $c) {
                $sep = $i;
            }

            $l += $cw[$c];
            if ($l > $wmax) {
                if (-1 == $sep) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j   = $i;
                $l   = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}
ob_start();

$country_list = getCountryList();

$pdf = new TCPDFInvoice('P', 'mm', 'A4');
$pdf->Middle();
ob_end_clean();
$invoice_buffer = $pdf->Output('invoice.pdf', 'S');
