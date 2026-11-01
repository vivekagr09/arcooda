<?php
include __DIR__ . '/setup_shipinvoice.inc.php';

use Fpdf\Fpdf;

moduleLoad('fpdf');

set_time_limit(100);

define('FONT_HEIGHT_CONSTANT', 0.41);
define('LINE_BREAK_HEIGHT', 4.0);
define('FONT_BASELINE_CONSTANT', 4.9);

if (!class_exists("FPDFInvoice")) {
    class FPDFInvoice extends Fpdf
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
        public $decoration;
        public $class_buffer = array();
        public function __construct()
        {
            parent::__construct('P', 'mm', 'A4');
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
        protected function normalize($word)
        {
            // Thanks to: http://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
            
            $word = str_replace("@","%40",$word);
            $word = str_replace("`","%60",$word);
            $word = str_replace("¢","%A2",$word);
            $word = str_replace("£","%A3",$word);
            $word = str_replace("¥","%A5",$word);
            $word = str_replace("|","%A6",$word);
            $word = str_replace("«","%AB",$word);
            $word = str_replace("¬","%AC",$word);
            $word = str_replace("¯","%AD",$word);
            $word = str_replace("º","%B0",$word);
            $word = str_replace("±","%B1",$word);
            $word = str_replace("ª","%B2",$word);
            $word = str_replace("µ","%B5",$word);
            $word = str_replace("»","%BB",$word);
            $word = str_replace("¼","%BC",$word);
            $word = str_replace("½","%BD",$word);
            $word = str_replace("¿","%BF",$word);
            $word = str_replace("À","%C0",$word);
            $word = str_replace("Á","%C1",$word);
            $word = str_replace("Â","%C2",$word);
            $word = str_replace("Ã","%C3",$word);
            $word = str_replace("Ä","%C4",$word);
            $word = str_replace("Å","%C5",$word);
            $word = str_replace("Æ","%C6",$word);
            $word = str_replace("Ç","%C7",$word);
            $word = str_replace("È","%C8",$word);
            $word = str_replace("É","%C9",$word);
            $word = str_replace("Ê","%CA",$word);
            $word = str_replace("Ë","%CB",$word);
            $word = str_replace("Ì","%CC",$word);
            $word = str_replace("Í","%CD",$word);
            $word = str_replace("Î","%CE",$word);
            $word = str_replace("Ï","%CF",$word);
            $word = str_replace("Ð","%D0",$word);
            $word = str_replace("Ñ","%D1",$word);
            $word = str_replace("Ò","%D2",$word);
            $word = str_replace("Ó","%D3",$word);
            $word = str_replace("Ô","%D4",$word);
            $word = str_replace("Õ","%D5",$word);
            $word = str_replace("Ö","%D6",$word);
            $word = str_replace("Ø","%D8",$word);
            $word = str_replace("Ù","%D9",$word);
            $word = str_replace("Ú","%DA",$word);
            $word = str_replace("Û","%DB",$word);
            $word = str_replace("Ü","%DC",$word);
            $word = str_replace("Ý","%DD",$word);
            $word = str_replace("Þ","%DE",$word);
            $word = str_replace("ß","%DF",$word);
            $word = str_replace("à","%E0",$word);
            $word = str_replace("á","%E1",$word);
            $word = str_replace("â","%E2",$word);
            $word = str_replace("ã","%E3",$word);
            $word = str_replace("ä","%E4",$word);
            $word = str_replace("å","%E5",$word);
            $word = str_replace("æ","%E6",$word);
            $word = str_replace("ç","%E7",$word);
            $word = str_replace("è","%E8",$word);
            $word = str_replace("é","%E9",$word);
            $word = str_replace("ê","%EA",$word);
            $word = str_replace("ë","%EB",$word);
            $word = str_replace("ì","%EC",$word);
            $word = str_replace("í","%ED",$word);
            $word = str_replace("î","%EE",$word);
            $word = str_replace("ï","%EF",$word);
            $word = str_replace("ð","%F0",$word);
            $word = str_replace("ñ","%F1",$word);
            $word = str_replace("ò","%F2",$word);
            $word = str_replace("ó","%F3",$word);
            $word = str_replace("ô","%F4",$word);
            $word = str_replace("õ","%F5",$word);
            $word = str_replace("ö","%F6",$word);
            $word = str_replace("÷","%F7",$word);
            $word = str_replace("ø","%F8",$word);
            $word = str_replace("ù","%F9",$word);
            $word = str_replace("ú","%FA",$word);
            $word = str_replace("û","%FB",$word);
            $word = str_replace("ü","%FC",$word);
            $word = str_replace("ý","%FD",$word);
            $word = str_replace("þ","%FE",$word);
            $word = str_replace("ÿ","%FF",$word);

            return urldecode($word);
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
                        foreach (array('color', 'image', 'repeat', 'attachment', 'xposition', 'yposition') as $sub_style) {
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
                    $this->styles_apply(implode(';', $nested_call));
                }
            }
        }
        public function class_apply($class)
        {
            switch ($class) {
                case 'base':
                    $this->styles_apply('background-color:transparent;border:1px none black;color:black;font:normal normal normal medium helvetica;padding:0px;text-align:left;vertical-align:top;text-decoration:none;');

                    $this->SetFontSize($this->fontSize = 8);
                    $this->SetFont('', '');
                    $this->SetTextColor(0x00, 0x00, 0x00);
                    $this->SetDrawColor(0x00, 0x00, 0x00);
                    $this->textAlign = 'L';
                    $this->padding   = 0;
                    $this->border    = 0;
                    $this->fill      = 0;
                    break;
                case 'box_header':
                    global $sinvoice;
                    $this->border |= 15;
                    switch ($sinvoice['site_id']) {
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
                            $this->styles_apply('background-color:#eeeeee;color:black;');
                            $this->SetFillColor(0xee, 0xee, 0xee);
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
                case 'box_notes':
                    $this->styles_apply('font-size:small;text-decoration:italic');
                    $this->SetFont('', 'I');
                    $this->SetFontSize($this->fontSize = 7);
                    break;
                case 'box_notes_product':
                    $this->styles_apply('text-decoration:italic');
                    $this->SetFont('helvetica', 'I');
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
                    break;
            }
        }
        public function Header()
        {
            global $sinvoice_id, $sinvoice, $sinvoice_products, $sinvoice_charges, $formatted_sinvoice_number, $action;
            //From setup_invoice.inc.php
            global $charges_list, $country_list, $images, $sinvoice_comments, $sinvoice_contacts, $sinvoice_inforow, $sinvoice_labels, $sinvoice_reference, $sinvoice_type, $sinvoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url, $sinvoice_diagonal;

            $this->headcol    = array(0 => 12.7, 1 => 121.92, 2 => 156.21, 3 => 190.5);
            $this->contactcol = array(0 => 12.7, 1 => 101.6, 2 => 190.5);
            $this->productcol = array(0 => 12.7, 1 => 33.02, 2 => 53.34, 3 => 109.22, 4 => 129.54, 5 => 149.86, 6 => 171.18, 7 => 190.5);
            $this->layoutrow  = array(0 => 12.7);

            $ypos1 = $ypos2 = 12.7;
            $temp  = explode("\n", str_replace("\r", "", $sinvoice_labels['company_invoiceheader']));
            if ('AU' == $sinvoice['bill_country'] && 'AU' == $sinvoice['ship_country']) {
                $temp = explode("\n", str_replace("\r", "", $sinvoice_labels['company_invoiceheader_au']));
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
            $this->ln();
            $ypos1 = $this->GetY();
            $this->class_push('box_shaded');
            $this->class_push('invoice_type');
            $this->textAlign = 'C';
            $this->SetXY($this->headcol[1], $ypos2);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $sinvoice_type_text);
            $this->DoEndTableRow();
            $this->class_pop();
            $this->class_pop();
            $this->Ln();
            $this->class_push('box_header');
            $this->textAlign = 'C';
            $this->SetX($this->headcol[1]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[2] - $this->headcol[1], 0, ($sinvoice_type < 3) ? 'INVOICE #' : 'INVOICE #');
            $this->DoTableCell($this->headcol[3] - $this->headcol[2], 0, 'DATE');
            $this->DoEndTableRow();
            $this->class_pop();
            $this->class_push('box_normal');
            $this->textAlign = 'C';
            $this->SetX($this->headcol[1]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[2] - $this->headcol[1], 0, $formatted_sinvoice_number);
            $this->DoTableCell($this->headcol[3] - $this->headcol[2], 0, date('jS F, Y', $time_to_use));
            $this->DoEndTableRow();
            $this->class_pop();
            $this->Ln();
            $ypos2 = $this->GetY();
            $ypos1 = $ypos2 = max($ypos1, $ypos2);
            $this->SetY($ypos1);
            $this->layoutrow[] = $ypos1;
            $contacts_layout   = array_chunk(array_keys($sinvoice_contacts), 2);
            foreach ($contacts_layout as $row) {
                $this->class_push('box_header');
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                if ('' != $row[0]) {
                    $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $this->normalize($row[0]));
                }

                if ('' != $row[1]) {
                    $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0, $this->normalize($row[1]));
                }

                $this->DoEndTableRow();
                $this->class_pop();
                $ypos1 = $ypos2 = $this->GetY();
                $this->class_push('box_normal');
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                if ('' != $row[0]) {
                    $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $this->normalize($sinvoice_contacts[$row[0]]));
                }

                if ('' != $row[1]) {
                    $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0, $this->normalize($sinvoice_contacts[$row[1]]));
                }

                $this->DoEndTableRow();
                $this->class_pop();
                $this->Ln();
                $ypos1 = $ypos2 = $this->GetY();
            }
            $this->layoutrow[] = $ypos1;
            if (count($sinvoice_inforow) > 0) {
                $total_width = 0;
                foreach ($sinvoice_inforow as $key => $value) {
                    $total_width += max($this->GetStringWidth($key), $this->GetStringWidth($value));
                }

                $max_width = $this->contactcol[2] - $this->contactcol[0];
                $this->class_push('box_header');
                $this->textAlign = 'C';
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                foreach ($sinvoice_inforow as $key => $value) {
                    $percent = max($this->GetStringWidth($key), $this->GetStringWidth($value)) / $total_width;
                    $this->DoTableCell($max_width * $percent, 0, $key);
                }
                $this->DoEndTableRow();
                $this->class_pop();
                $this->class_push('box_normal');
                $this->textAlign = 'C';
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                foreach ($sinvoice_inforow as $key => $value) {
                    $percent = max($this->GetStringWidth($key), $this->GetStringWidth($value)) / $total_width;
                    $this->DoTableCell($max_width * $percent, 0, $value);
                }
                $this->DoEndTableRow();
                $this->class_pop();
                $this->Ln();
            }
            $ypos1             = $ypos2             = $this->GetY();
            $this->layoutrow[] = $ypos1;
        }
        public function Footer()
        {
            global $sinvoice_id, $sinvoice, $sinvoice_products, $sinvoice_charges;
            //From setup_invoice.inc.php
            global $charges_list, $country_list, $images, $sinvoice_comments, $sinvoice_contacts, $sinvoice_inforow, $sinvoice_labels, $sinvoice_reference, $sinvoice_type, $sinvoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url;
        }
        public function Middle()
        {
            global $sinvoice_id, $sinvoice, $sinvoice_products, $sinvoice_charges;
            //From setup_invoice.inc.php
            global $charges_list, $country_list, $images, $sinvoice_comments, $sinvoice_contacts, $sinvoice_inforow, $sinvoice_labels, $sinvoice_reference, $sinvoice_type, $sinvoice_type_text, $payment_data, $staff, $terms_list, $time_to_use, $warranty_terms_list, $website_info, $website_path, $website_url;

            $ypos1 = $ypos2 = $this->GetY();
            $this->class_push('box_header');
            $this->textAlign = 'C';
            $this->SetX($this->productcol[0]);
            $this->DoTableRow();
            if (9 == $sinvoice_type) {
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
            if (is_array($sinvoice_products)) {
                foreach ($sinvoice_products as $product) {
                    $product_notesdata = '';
                    if($sinvoice['is_product_harmonised_code'] == 'y' && $product['is_harmonised_code'] == 'y'){
                         $product_notesdata = $product_notesdata . "Harmonised Code : ".$product['harmonised_code']."\n";
                    }
                    if($sinvoice['is_product_origin_country'] == 'y' && $product['is_origin_country'] == 'y'){
                         $product_notesdata = $product_notesdata . "Country Of Origin : ". $product['origin_country']."\n";
                    }
                    if($sinvoice['is_product_usage'] == 'y' && $product['is_product_usage'] == 'y'){
                         $product_notesdata = $product_notesdata . "Usage : ". $product['product_usage']."\n";
                    }
                    if($sinvoice['is_product_materials'] == 'y' && $product['is_product_materials'] == 'y'){
                         $product_notesdata = $product_notesdata . "Materials : ".$product['product_materials']."\n";
                    }
                    $product['product_notes'] = $product['product_notes'] . $product_notesdata;
                    $product['product_notes'] = rtrim($product['product_notes'], "\n");
                    if ('c' != $product['generic']) {
                        if ('' != $product['generic_code'] && '0' == $product['product_id']) {
                            $product['product_id'] = $product['generic_code'];
                        }

                        if (9 == $sinvoice_type) {
                            $product['product_offer'] = convertCurrency($product['product_cost'], $sinvoice['invoice_currency']);
                        } else {
                            $product['product_offer'] = convertCurrency($product['product_offer'], $sinvoice['invoice_currency']);
                        }

                        $product['product_total'] = convertCurrency($product['product_qty'] * $product['product_offer'], $sinvoice['invoice_currency']);
                        $this->class_push('box_product');
                        if ('' != $product['product_notes']) {
                            $this->border = ($this->border&~2);
                        }

                        $this->SetX($this->productcol[0]);
                        $this->DoTableRow();
                        if (9 == $sinvoice_type) {
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
                            $product_notes=explode("\n",$product['product_notes']);
                            $product_notes=array_map('trim', $product_notes);
                            $product['product_notes']= "  ".implode("\n  ", $product_notes);
                            $this->border    = ($this->border ^ 3);
                            $this->textAlign = 'L';
                            $this->class_push('box_notes_product');
                            $this->SetX($this->productcol[0]);
                            $this->DoTableRow();
                            $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                            if (9 == $sinvoice_type) {
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
                             $this->class_pop();
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

            if (0 != $sinvoice['ship_id'] && 9 != $sinvoice_type) {
                $this->class_push('box_product');
                if ('' != $sinvoice['ship_product_notes']) {
                    $this->border = ($this->border&~2);
                }

                $this->SetX($this->productcol[0]);
                $this->DoTableRow();

                $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, sprintf('F%02d', $sinvoice['ship_id']) . (('y' == $sinvoice['ship_insurance']) ? 'I' : 'N'));
                $this->textAlign = 'L';
                $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $sinvoice['ship_product_name']);
                $this->textAlign = 'C';
                $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, convertCurrency($sinvoice['ship_offer'], $sinvoice['invoice_currency']));
                $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, convertCurrency($sinvoice['ship_offer'], $sinvoice['invoice_currency']));
                $this->DoEndTableRow();
                if ('' != $sinvoice['ship_product_notes']) {
                    $this->border    = ($this->border ^ 3);
                    $this->textAlign = 'L';
                    $this->SetFont('helvetica', 'I');
                    $this->SetX($this->productcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                    $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $sinvoice['ship_product_notes']);
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

            $sub_total = $sinvoice['invoice_offer'];
            $gst_offer = 0;
            if (is_array($sinvoice_charges) && 9 != $sinvoice_type) {
                foreach ($sinvoice_charges as $charge) {
                    if (1 != $charge['charge_id']) {
                        $this->class_push('box_product');
                        if ('' != $sinvoice['ship_product_notes']) {
                            $this->border = ($this->border&~2);
                        }

                        $this->SetX($this->productcol[0]);
                        $this->DoTableRow();
                        $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, sprintf('C%02d', $charge['charge_id']));
                        $this->textAlign = 'L';
                        $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $charges_list[$charge['charge_id']]);
                        $this->textAlign = 'C';
                        $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                        $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency']));
                        $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency']));
                        $this->DoEndTableRow();
                        $this->class_pop();
                        $ypos1 = $ypos2 = $this->GetY();
                        if ($ypos1 >= 275) {
                            $this->AddPage();
                            $ypos1 = $ypos = $this->GetY();
                        }
                    } else {
                        $gst_offer += convertCurrency($charge['charge_offer'], $sinvoice['invoice_currency']);
                    }
                }
            }
            $sub_total -= $gst_offer;

            if ($this->GetY() > 227.0) {
                $height = 275.0 - $this->GetY();
                $this->class_push('box_product');
                $this->SetX($this->productcol[0]);
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[1] - $this->productcol[0], $height, '');
                if (9 == $sinvoice_type) {
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
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }

            if ($this->GetY() < 227.0) {
                $height = 227.0 - $this->GetY();
                $this->class_push('box_product');
                $this->SetX($this->productcol[0]);
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[1] - $this->productcol[0], $height, '');
                if (9 == $sinvoice_type) {
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
            if (9 == $sinvoice_type) {
                $temp['Total'] = convertCurrency($sinvoice['products_cost'], $sinvoice['invoice_currency']);
            } else {
                $temp['Sale Amount'] = convertCurrency($sub_total, $sinvoice['invoice_currency']);
                if (0 != $gst_offer) {
                    $temp['GST 10%'] = convertCurrency($gst_offer, $sinvoice['invoice_currency']);

                    $temp['GST 10%']    = explode('.', $temp['GST 10%']);
                    $temp['GST 10%'][0] = number_format($temp['GST 10%'][0]);
                    $temp['GST 10%']    = implode('.', $temp['GST 10%']);
                }
                $temp['Total'] = convertCurrency($sinvoice['invoice_offer'], $sinvoice['invoice_currency']);

                $temp['Sale Amount']    = explode('.', $temp['Sale Amount']);
                $temp['Sale Amount'][0] = number_format($temp['Sale Amount'][0]);
                $temp['Sale Amount']    = implode('.', $temp['Sale Amount']);
            }

            $temp['Total']    = explode('.', $temp['Total']);
            $temp['Total'][0] = number_format($temp['Total'][0]);
            $temp['Total']    = implode('.', $temp['Total']);

            $temp['Currency'] = $sinvoice['invoice_currency'];
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
            foreach ($sinvoice_comments as $key => $field) {
                // $the_text[] = trim($key) . ":\n" . trim($field);
                $the_text[] = $key . (!empty($field) ? ":\n" . trim($field) : '');
            }

            $the_text = trim(implode("\n", $the_text));
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
            $this->Ln();
            $ypos1 = $ypos2 = $this->GetY();
            if ($ypos1 >= 275) {
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }
            $this->layoutrow[] = $ypos1;

            $the_text     = array();
            $payment_info = false;
            switch ($sinvoice['paymenttype_id']) {
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
                    $this->DoTableCell(40.8, 17.3, "Biller Code: 424606\nRef: " . $sinvoice_reference);
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

            if (9 != $sinvoice_type) {
                if ($ypos1 >= 275) {
                    $this->AddPage();
                    $ypos1 = $ypos = $this->GetY();
                }
                if ('' != $images['stamp']) {
                    $this->Image($website_path . $images['stamp'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 14.0, $ypos1 - 3.0, 0, 25.4);
                }

                if ('' != $images['signature']) {
                    $this->Image($website_path . $images['signature'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 8.0, $ypos1 + 2.0, 0, 12.7);
                }

                $this->textAlign = 'R';
                $this->SetXY($this->productcol[4], $ypos1);
                //$this->BackgroundColor='none';
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[7] - $this->productcol[4], 0, 'FOR ' . strtoupper($sinvoice_labels['company_name']) . ((20 != $sinvoice['site_id']) ? ' PTY' : '') . ' LTD');
                $this->DoEndTableRow();
                $this->Ln();
                $this->Ln();
                $this->Ln();
                $this->Line($this->productcol[4], $this->GetY(), $this->productcol[7], $this->GetY());
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
            $this->Ln();
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
}
ob_start();
$pdf = new FPDFInvoice();
$pdf->Middle();
ob_end_clean();
$sinvoice_buffer = $pdf->Output("I",$formatted_sinvoice_number.'.pdf', 'S');
