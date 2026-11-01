<?php
/**
 * @param mixed[]   $invoice
 * @param mixed[][] $invoice_charges
 * @param mixed[][] $invoice_products
 */
?><?php

/**
 * @var string    $formatted_invoice_number
 * @var string[]  $images
 * @var string[]  $invoice_comments
 * @var string[]  $invoice_contacts
 * @var string    $invoice_diagonal
 * @var string[]  $invoice_inforow
 * @var string[]  $invoice_labels
 * @var string    $invoice_reference
 * @var int       $invoice_type
 * @var string    $invoice_type_text
 * @var string[]  $payment_data
 * @var int       $time_to_use
 * @var string    $website_path
 */
include __DIR__ . '/setup_invoice.inc.php';

/*use Fpdf\Fpdf;

moduleLoad('fpdf');*/
require_once 'tfpdf/tfpdf.php';
set_time_limit(100);

define('FONT_HEIGHT_CONSTANT', 0.41);
define('LINE_BREAK_HEIGHT', 4.0);
define('FONT_BASELINE_CONSTANT', 4.9);
if (!class_exists("FPDFInvoice")) {
    class FPDFInvoice extends tFPDF
    {
        /** @var string[] */
        private $style = array();

        /** @var string */
        public $textAlign;

        /** @var int|float */
        public $fontSize;

        /** @var int */
        public $padding;

        /** @var int */
        public $border;

        /** @var float[] */
        public $headcol;

        /** @var float[] */
        public $contactcol;

        /** @var float[] */
        public $productcol;

        /** @var float[] */
        public $layoutrow;

        /** @var bool */
        public $fill;

        /** @var string[] */
        public $class_buffer = array();

        public function __construct()
        {
            parent::__construct('P', 'mm', 'A4');
            $this->SetFont('helvetica', '', $this->fontSize = 8);
            $this->SetMargins(12.7, 12.7);
            $this->SetAutoPageBreak(false);
            $this->AddPage();
        }

        /**
         * @return string
         */
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

        /**
         * @return void
         */
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
             return mb_convert_encoding($word, 'UTF-8', 'UTF-8');
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
        /**
         * @param string $class
         *
         * @return void
         */
        public function class_push($class)
        {
            $this->class_buffer[] = $class;
            $this->class_apply($class);
        }

        /**
         * @param string $styles
         *
         * @return void
         */
        public function styles_apply($styles)
        {
            static $sides = array('top', 'right', 'bottom', 'left');
            $styles       = preg_replace(array('/\s\s+/', '/;$/'), array(' ', ''), $styles) ?? '';
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

        /**
         * @param string $class
         *
         * @return void
         */
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
                    $this->fill      = false;
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
                            $this->styles_apply('background-color:#eeeeee;color:black;');
                            $this->SetFillColor(0xee, 0xee, 0xee);
                            $this->SetTextColor(0x00, 0x00, 0x00);
                            break;
                    }
                    $this->styles_apply('border-style:solid;padding:1px;');
                    $this->fill    = true;
                    $this->padding = 1;
                    break;
                case 'box_shaded':
                    $this->styles_apply('background-color:#777777;color:white;border-style:solid;padding:1px;');
                    $this->fill = true;
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
                case 'box_gift_notes':
                    $this->styles_apply('text-decoration:italic');
                    $this->SetFont('helvetica', 'I');
                    //$this->SetTextColor(0xFF, 0x00, 0x00);
                    break;
                 case 'box_gift_notes_row':
                    //$this->SetTextColor(0xFF, 0x00, 0x00);
                    break;
                case 'box_product':
                    $this->styles_apply('padding:1px;text-align:center;border-style:solid;');
                    $this->padding   = 1;
                    $this->textAlign = 'C';
                    $this->border |= 15;
                    break;
                case 'mark_paid_on_invoice':
                    $this->SetFont('helvetica');
                     $this->padding   = 1;
                    $this->SetFontSize(18);
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
                    $this->padding = 5;
                    break;
                case 'bpay_logo':
                    $this->styles_apply('font-size:small;background-color:#ffffff;');
                    $this->SetFontSize($this->fontSize = 7.9);
                    $this->SetFillColor(0xFF, 0xFF, 0xFF);
                    $this->fill = true;
                    break;
                case 'preview_invoice':
                    $this->styles_apply('background-color:#ffffff;');
                    $this->SetFontSize($this->fontSize = 36);
                    $this->SetFillColor(0xFF, 0xFF, 0xFF);
                    $this->fill = true;
                    break;
            }
        }

        /**
         * @return void
         */
        public function Header()
        {
            global $invoice, $formatted_invoice_number;
            //From setup_invoice.inc.php
            global $images, $invoice_contacts, $invoice_inforow, $invoice_labels, $invoice_type, $invoice_type_text, $time_to_use, $website_path, $invoice_diagonal;

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
            if ('' != $images['company_logo'] && $image_size = getimagesize($website_path . $images['company_logo'])) {
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
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $invoice_type_text);
            $this->DoEndTableRow();
            $this->class_pop();
            $this->class_pop();
            $this->Ln();
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
            $this->DoTableCell($this->headcol[2] - $this->headcol[1], 0, $formatted_invoice_number);
            $this->DoTableCell($this->headcol[3] - $this->headcol[2], 0, date('jS F, Y', $time_to_use));
            $this->DoEndTableRow();
            $this->class_pop();
            $this->Ln();
            $ypos2 = $this->GetY();
            $ypos1 = $ypos2 = max($ypos1, $ypos2);

            $this->SetY($ypos1);
            $this->layoutrow[] = $ypos1;
            $contacts_layout   = array();
            if (is_array($invoice_contacts)) {
                $contacts_layout = array_chunk(array_keys($invoice_contacts), 2);
            }

            foreach ($contacts_layout as $row) {
                $this->class_push('box_header');
                $this->AddFont('NotoSansSC','','NotoSansSC-Regular.ttf',true); 
                $this->SetFont('NotoSansSC','',8);
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                if ('' != $row[0]) {
                    $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $this->normalize($row[0]));
                }

                if ('' != $row[1]) {
                    $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0,  $this->normalize($row[1]));
                }

                $this->DoEndTableRow();
                $this->class_pop();
                $ypos1 = $ypos2 = $this->GetY();
                $this->class_push('box_normal');
                $this->AddFont('NotoSansSC','','NotoSansSC-Regular.ttf',true); 
                $this->SetFont('NotoSansSC','',8);
                $this->SetX($this->contactcol[0]);
                $this->DoTableRow();
                if ('' != $row[0]) {
                    $this->DoTableCell($this->contactcol[1] - $this->contactcol[0], 0, $this->normalize($invoice_contacts[$row[0]]));
                }

                if ('' != $row[1]) {
                    $this->DoTableCell($this->contactcol[2] - $this->contactcol[1], 0, $this->normalize($invoice_contacts[$row[1]]));
                }

                $this->DoEndTableRow();
                $this->class_pop();
                $this->Ln();
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
                $this->Ln();
            }
            $ypos1             = $ypos2             = $this->GetY();
            $this->layoutrow[] = $ypos1;
        }

        /**
         * @return void
         */
        public function Footer()
        {
            //From setup_invoice.inc.php
            global $invoice_diagonal;

            if ('' != $invoice_diagonal) {
                $angle_radian = atan($this->h / $this->w);
                $angle_degree = $angle_radian * 180.0 / M_PI;
                $this->SetFontSize($this->fontSize = 105);
                $width    = $this->GetStringWidth($invoice_diagonal);
                $height   = $this->fontSize * FONT_HEIGHT_CONSTANT;
                $baseline = $height / FONT_BASELINE_CONSTANT;
                $matrix   = array(cos($angle_radian), sin($angle_radian), 0 - sin($angle_radian), cos($angle_radian));
                $box1     = array(0, $baseline, $width, $baseline, $width, $baseline - $height, 0, $baseline - $height);
                $box      = array();
                for ($i = 0; $i < 4; $i++) {
                    $box[$i << 1]       = $box1[$i << 1] * $matrix[0] + $box1[($i << 1) | 1] * $matrix[1];
                    $box[($i << 1) | 1] = $box1[$i << 1] * $matrix[2] + $box1[($i << 1) | 1] * $matrix[3];
                }
                $offset_x = ($this->w - max(max($box[0], $box[2]), max($box[4], $box[6])) - min(min($box[0], $box[2]), min($box[4], $box[6]))) / 2;
                $offset_y = ($this->h - max(max($box[1], $box[3]), max($box[5], $box[7])) - min(min($box[1], $box[3]), min($box[5], $box[7]))) / 2;
                /*
                $box2=array();
                for($i=0;$i<4;$i++)
                {
                $box2[$i*2]=$box[$i*2]+$offset_x;
                $box2[$i*2+1]=$box[$i*2+1]+$offset_y;
                }
                $this->Polygon($box2);
                 */
                // @fixme This is needed for preview invoices
                //$this->TextWithRotation($offset_x, $offset_y, $invoice_diagonal, $angle_degree, 0);
            }
        }

        /**
         * @return void
         */
        public function Middle()
        {
            global $invoice, $invoice_products, $invoice_charges,$action,$invoice_gifts;
            //From setup_invoice.inc.php
            global $images, $invoice_comments, $invoice_labels, $invoice_reference, $invoice_type, $payment_data, $website_path,$invoice_row_comment;

            global $DB;

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
               /* usort($invoice_products, function ($a, $b) {
                    if (is_numeric($a['product_number']) && is_string($b['product_number'])) return -1; // Numbers first
                    if (is_string($a['product_number']) && is_numeric($b['product_number'])) return 1;  // Strings last
                    if (is_numeric($a['product_number']) && is_numeric($b['product_number'])) return $a['product_number'] <=> $b['product_number']; // Sort numbers normally
                    return strcmp($a['product_number'], $b['product_number']); // Sort strings alphabetically
                });*/
                if($invoice['manual_odering'] == 'n'){
                    usort($invoice_products, function($a, $b) {
                        return $b['product_offer'] <=> $a['product_offer'];
                    });
                }
                foreach ($invoice_products as $arraykey => $product) {
                    if ('c' != $product['generic']) {
                        if ($product['product_id'] > 0 && $product['sub_id'] > 0) {
                            $product['product_id'] = str_pad($product['product_id'], 5, '0', STR_PAD_LEFT) . '-' . str_pad($product['sub_id'], 4, '0', STR_PAD_LEFT);
                        }

                        if ('' != $product['generic_code'] && '0' == $product['product_id']) {
                            $product['product_id'] = $product['generic_code'];
                        }
                        if($action == 'shipview'){
                            $product_notesdata = '';
                            if($invoice['is_product_harmonised_code'] == 'y' && $product['is_harmonised_code'] == 'y'){
                                 $product_notesdata = $product_notesdata . "Harmonised Code : ".$product['harmonised_code']."\n";
                            }
                            if($invoice['is_product_origin_country'] == 'y' && $product['is_origin_country'] == 'y'){
                                 $product_notesdata = $product_notesdata . "Country Of Origin : ". $product['origin_country']."\n";
                            }
                            if($invoice['is_product_usage'] == 'y' && $product['is_product_usage'] == 'y'){
                                 $product_notesdata = $product_notesdata . "Usage : ". $product['product_usage']."\n";
                            }
                            if($invoice['is_product_materials'] == 'y' && $product['is_product_materials'] == 'y'){
                                 $product_notesdata = $product_notesdata . "Materials : ".$product['product_materials']."\n";
                            }
                            $product['product_notes'] = $product['product_notes'] . $product_notesdata;
                         
                        }
                        if($product['is_free'] == 'y'){
                             $product['product_notes'] = 'Save $'.$product['product_offer'].' USD'."\n";;
                        }
                        $product['product_notes'] = rtrim($product['product_notes'], "\n");
                        //if ($product['product_condition'] == 2) $product['product_id'].= 'U';
                        if (9 == $invoice_type) {
                            $product['product_offer'] = convertCurrency($product['product_cost'], $invoice['invoice_currency']);
                        } else {
                            $product['product_offer'] = convertCurrency($product['product_offer'], $invoice['invoice_currency']);
                        }
                        $productQuantity = $product['product_qty'];
                        if($product['is_free'] == 'y'){
                            $productQuantity = $productQuantity -1;
                        }
                        $product['product_total'] = convertCurrency($productQuantity * $product['product_offer'], $invoice['invoice_currency']);
                        $this->class_push('box_product');
                        if ('' != $product['product_notes']) {
                            $this->border = ($this->border&~2);
                        }
                        if($product['is_free'] == 'y' &&  $product['product_qty'] == 1){
                            $product['product_offer'] = '0.00';
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
                            $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $product['product_name'].( $product['is_free'] == 'y' ? ' (FREE)': '') );
                            $this->textAlign = 'C';
                            $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, $product['product_qty']);
                            $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, $product['product_offer']);
                        }
                        $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, $product['product_total']);
                        $this->DoEndTableRow();
                        if ('' != $invoice['customer_requests'] && 9 != $invoice_type && $arraykey == 0) {
                               //$product['product_notes'] =  $product['product_notes']."\n\n".$invoice['customer_requests'];
                        }
                        if ('' != $product['product_notes']) {
                            $product_notes=explode("\n",$product['product_notes']);
                            $product_notes=array_map('trim', $product_notes);
                            $product['product_notes']= "  ".implode("\n  ", $product_notes);
                            $this->border    = ($this->border ^ 3);
                            $this->textAlign = 'L';
                            $this->SetX($this->productcol[0]);
                            $this->class_push('box_notes_product');
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
            if (isset($invoice_gifts['gift']) && count($invoice_gifts['gift']) > 0) {
                foreach ($invoice_gifts['gift'] as $data) {
                    $this->class_push('box_product');
                     if ('' != $data['gift_detail']) {
                        $this->border = ($this->border & ~2);
                    }    
                    // Start creating the table row for the gift listing
                    $this->border = ($this->border&~2);
                    $this->SetX($this->productcol[0]); 
                    $this->DoTableRow(); 
                    $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, $data['product_id']);
                    $this->textAlign = 'L';
                    $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $data['gift_name'].' (FREE - Gift)' . "\n\n" . $data['gift_detail']);
                    $this->textAlign = 'C';
                    $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, $data['gift_qty']);
                    $this->class_push('box_gift_notes_row');
                    $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, '0.00');

                    $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, '0.00');
                    $this->class_pop();
                    // End the table row
                    $this->DoEndTableRow();
                    $this->border    = ($this->border ^ 3);
                    $this->textAlign = 'L';
                    $this->SetX($this->productcol[0]);
                    $this->class_push('box_gift_notes');
                    $this->DoTableRow();
                    $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                    if (9 == $invoice_type) {
                        $this->DoTableCell($this->productcol[2] - $this->productcol[1], 0, '');
                        $this->DoTableCell($this->productcol[3] - $this->productcol[2], 0, 'Save $'.$data['total'].' USD');
                        $this->DoTableCell($this->productcol[4] - $this->productcol[3], 0, '');
                    } else {
                        $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, 'Save $'.$data['total'].' USD');
                    }
                    $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                    $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, '');
                    $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, '');
                    $this->DoEndTableRow();
                    $this->class_pop();
                   /* if ('' != $data['gift_detail']) {
                        $this->border    = ($this->border ^ 3);
                        $this->textAlign = 'L';
                        $this->SetX($this->productcol[0]);
                        $this->class_push('box_notes');
                        $this->DoTableRow();
                        $this->DoTableCell($this->productcol[1] - $this->productcol[0], 0, '');
                        if (7 == $invoice_type) {
                            $this->DoTableCell($this->productcol[2] - $this->productcol[1], 0, '');
                            $this->DoTableCell($this->productcol[3] - $this->productcol[2], 0, $data['gift_detail']);
                            $this->DoTableCell($this->productcol[4] - $this->productcol[3], 0, '');
                        } else {
                            $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $data['gift_detail']);
                        }
                        $this->DoTableCell($this->productcol[5] - $this->productcol[4], 0, '');
                        $this->DoTableCell($this->productcol[6] - $this->productcol[5], 0, '');
                        $this->DoTableCell($this->productcol[7] - $this->productcol[6], 0, '');
                        $this->DoEndTableRow();
                        $this->class_pop();
                    }*/

                    $this->class_pop();
                    $ypos1 = $ypos2 = $this->GetY();
                        if ($ypos1 >= 275) {
                            $this->AddPage();
                            $ypos1 = $ypos = $this->GetY();}
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
                        $this->DoTableCell($this->productcol[4] - $this->productcol[1], 0, $charge['charge_name']);
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

            if ($this->GetY() > 227.0) {
                $height = 275.0 - $this->GetY();
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
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }

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

                    $temp['GST 10%']    = explode('.', $temp['GST 10%']);
                    $temp['GST 10%'][0] = number_format($temp['GST 10%'][0]);
                    $temp['GST 10%']    = implode('.', $temp['GST 10%']);
                }
                $temp['Total'] = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']);

                $temp['Sale Amount']    = explode('.', $temp['Sale Amount']);
                $temp['Sale Amount'][0] = number_format($temp['Sale Amount'][0]);
                $temp['Sale Amount']    = implode('.', $temp['Sale Amount']);
            }

            $temp['Total']    = explode('.', $temp['Total']);
            $temp['Total'][0] = number_format($temp['Total'][0]);
            $temp['Total']    = implode('.', $temp['Total']);

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
            $row_text = array();
            $paid_text = '';
            if (is_array($invoice_comments)) {
                foreach ($invoice_comments as $key => $field) {
                    // $the_text[] = trim($key) . ":\n" . trim($field);
                    $the_text[] = $key . (!empty($field) ? ":\n" . trim($field) : '');
                }
            }

            
            if (is_array($invoice_row_comment)) {
                foreach ($invoice_row_comment as $key => $field) {
                    // $the_text[] = trim($key) . ":\n" . trim($field);
                    $row_text[] = trim($field);
                }
            }
            //$row_text = trim(implode("\n", $row_text));
            $normalText  = trim(implode("\n", $the_text));
            $cellX = $this->productcol[0];
            $cellW = $this->productcol[5] - $this->productcol[0];
            $normalHeight = $normalText !== ''
                ? $this->NbLines($cellW - 2, $normalText) * $this->fontSize * FONT_HEIGHT_CONSTANT
                : 0;

            $warningHeight = 0;
            foreach ($row_text as $t) {
                $warningHeight +=
                    $this->NbLines($cellW - 4, $t) * $this->fontSize * FONT_HEIGHT_CONSTANT + 2;
            }

            $totalHeight = $normalHeight + $warningHeight + 4;
            $this->class_push('box_product');
            $this->class_push('box_notes');
            $this->SetXY($this->productcol[0], $ypos1);
            $this->DoTableRow();
            $tmp = $this->textAlign;
            $this->textAlign = 'L';
            if(count($row_text)){
                $this->DoTableCell($cellW, $totalHeight, $normalText);
             
            }else{
                $this->DoTableCell($this->productcol[5] - $this->productcol[0], $ypos2 - $ypos1 - 2.0, $normalText);
            }
            
            $this->textAlign = $tmp;
            $this->DoEndTableRow();
            $y = $ypos1 + $normalHeight + 2;

            foreach ($row_text as $text) {

                if ($text === '') continue;

                $h =
                    $this->NbLines($cellW - 4, $text)
                    * $this->fontSize * FONT_HEIGHT_CONSTANT + 2;

                // set text color to red
                $this->SetTextColor(255, 0, 0);

                $this->SetXY($cellX + 2, $y + 1);
                $this->MultiCell(
                    $cellW - 4,
                    $this->fontSize * FONT_HEIGHT_CONSTANT,
                    $text,
                    0,
                    'L'
                );

                // reset text color back to black
                $this->SetTextColor(0, 0, 0);

                $y += $h + 1;
            }


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
            $PamentInvoiceYpos = $this->GetY() + 10;
            $this->SetY($PamentInvoiceYpos);

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
                    $this->SetY($ypos1);
                    break;
                default:
                    if ('' != $payment_data['payment_method_details']) {

                        if($invoice['mark_paid_on_invoice'] == 1) {
                              $the_text[] = 'PAID';
                        }else{
                            $the_text[] = 'Payment Information:';
                            $the_text[] = trim($payment_data['payment_method_details']);    
                        }
                        if($invoice['mark_paid_on_invoice'] == 1) {
                              $this->class_push('mark_paid_on_invoice');
                        }
                        $this->SetX($this->productcol[0]);
                        $this->DoTableRow();
                        $this->DoTableCell($this->productcol[4] - $this->productcol[0], 0, implode("\n", $the_text));
                        $this->DoEndTableRow();
                        if($invoice['mark_paid_on_invoice'] == 1) {
                            $this->class_pop();
                        }
                    }else{
                        if($invoice['mark_paid_on_invoice'] == 1) {
                             $this->class_push('mark_paid_on_invoice');
                            $this->SetX($this->productcol[0]);
                            $this->DoTableRow();
                            $this->DoTableCell($this->productcol[4] - $this->productcol[0], 0, 'PAID');
                            $this->DoEndTableRow();
                             $this->class_pop();
                        }
                    }
                    break;
            }
            if (9 != $invoice_type) {
                if ($ypos1 >= 275) {
                    $this->AddPage();
                    $ypos1 = $ypos = $this->GetY();
                }
                if ('' != $images['stamp'] && is_file($website_path . $images['stamp'])) {
                    $ypos1 = $ypos1+10;
                    $this->Image($website_path . $images['stamp'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 14.0, $ypos1 - 3.0, 0, 25.4);
                }

                if ('' != $images['signature'] && is_file($website_path . $images["signature"])) {
                    $ypos1 = $ypos1+10;
                    $this->Image($website_path . $images['signature'], ($this->productcol[7] + $this->productcol[4]) / 2.0 - 8.0, $ypos1 + 2.0, 0, 12.7);
                }

                $this->textAlign = 'R';
                $this->SetXY($this->productcol[4],$PamentInvoiceYpos);
                //$this->BackgroundColor='none';
                $this->DoTableRow();
                $this->DoTableCell($this->productcol[7] - $this->productcol[4], 0, 'FOR ' . strtoupper($invoice_labels['company_name']) . ((20 != $invoice["site_id"]) ? ' PTY' : '') . ' LTD');
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

        /** @var mixed[][] */
        private $table_row_content = array();

        /** @var float */
        private $table_row_height = 0.0;

        /**
         * @param float  $width
         * @param float  $height
         * @param string $text
         *
         * @return void
         */
        public function DoTableCell($width, $height = 0.0, $text = '')
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

        /**
         * @return void
         */
        public function DoTableRow()
        {
            $this->table_row_height  = $this->fontSize * FONT_HEIGHT_CONSTANT;
            $this->table_row_content = array();
        }

        /**
         * @return void
         */
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

        /**
         * @param int|float $w
         * @param string    $txt
         *
         * @return int
         */
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
        protected function safeText($text)
        {
            if ($text === null) {
                return '';
            }

            // remove invisible junk characters
            $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);

            // ensure UTF-8
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
            }

            return $text;
        }

    }
}
ob_start();
$invoiceTitle = $formatted_invoice_number. ' - '.$invoice['bill_name'];
$pdf = new FPDFInvoice();
$pdf->SetTitle($invoiceTitle);
$pdf->Middle();
ob_end_clean();
if(isset($posted['process_job']) || ($invoice['do_revision'] || $invoice_products['do_revision'] || $invoice_charges['do_revision'])) {
    $invoice_buffer = $pdf->Output($invoiceTitle.'.pdf', 'S');
} else {
    $invoice_buffer = $pdf->Output('I',$invoiceTitle.'.pdf', 'S');
}

