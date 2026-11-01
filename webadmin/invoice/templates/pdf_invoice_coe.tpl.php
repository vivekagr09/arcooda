<?php
global $_GLOBALS;

moduleLoad('tcpdf');

$country_list = getCountryList();

$staff        = (array_key_exists('staff', $_GLOBALS)) ? $_GLOBALS['staff'] : array();
$payment_data = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($invoice['site_id']) . " AND payment_method_id=" . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_path = '/home/coinopex/public_html/';

$force_images                   = array();
$force_images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$force_images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$force_images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$force_images['signature']      = 'images/sigs/' . $staff['signature'];

$boundary = md5(mt_rand(0, 65535));

foreach ($force_images as $key => $value) {
    if (!is_file($website_path . $value)) {
        $force_images[$key] = '';
    }
}

//-----------------------------------------------------------------------------
//This part should be the same as jpeg_invoice2.tpl.php

$charges_list = array();
$query        = $DB->query("SELECT charge_id,charge_name FROM `highwaygroup`.`charges` ORDER BY charge_name");
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}

$terms_list          = getTermsList();
$warranty_terms_list = invoiceGetOptionList('warranty_terms_id');
$staff               = (array_key_exists('staff', $_GLOBALS)) ? $_GLOBALS['staff'] : array();
$payment_data        = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($invoice['site_id']) . " AND payment_method_id=" . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$invoice_labels      = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE company_id=" . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_info        = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE id=" . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

//$website_url=$website_info['url'];
//$website_path=$website_info['root_dir'];
$website_url  = 'http://www.coinopexpress.com/';
$website_path = '/home/coinopex/public_html/';
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
$images['signature']      = 'images/sigs/' . $staff['signature'];
if (in_array($invoice['invoice_status'], array('enquiry', 'closed'))) {
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

$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type || 9 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']))));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], 0), 5));
}

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

class TCPDFInvoice extends TCPDF
{
    public $textAlign;
    public $fontSize;
    public $padding;
    public $border;
    public $headcol;
    public $bodycol;
    public $contactcol;
    public $fontWeight;
    public $fontStyle;
    public $fontString;
    public $verticalAlign;
    public $class_buffer = array();

    public $layoutrow = array();

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        $this->SetFont('times', '', $this->fontSize = 8);
        $this->SetMargins(12.7, 12.7);
        $this->SetAutoPageBreak(true);
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
    public function class_apply($class)
    {
        switch ($class) {
            case 'base':
                $this->fontWeight = 'normal';
                $this->fontStyle  = 'normal';
                $this->SetFontSize($this->fontSize = 10);
                $this->SetTextColor(0x00, 0x00, 0x00);
                $this->SetFillColor(0xFF, 0xFF, 0xFF);
                $this->SetDrawColor(0x00, 0x00, 0x00);
                $this->textAlign     = 'L';
                $this->verticalAlign = 'T';
                $this->padding       = 0;
                $this->border        = 0;
                break;
            case 'box_header':
                global $invoice;
                $this->border |= 15;
                switch ($invoice['site_id']) {
                    case 1:
                        $this->SetFillColor(0x00, 0x23, 0xF4);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 2:
                        $this->SetFillColor(0x00, 0xC9, 0x00);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 3:
                        $this->SetFillColor(0x99, 0xCC, 0xFF);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        $this->border = 0;
                        break;
                    case 9:
                        $this->SetFillColor(0x45, 0x4B, 0x84);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 16:
                        $this->SetFillColor(0x29, 0x3B, 0x4E);
                        $this->SetTextColor(0xE2, 0xEE, 0xFA);
                        break;
                    case 18:
                        $this->SetFillColor(0x0C, 0x95, 0x44);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 19:
                        $this->SetFillColor(0x3C, 0xB5, 0x4C);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 20:
                        $this->SetFillColor(0x02, 0x9E, 0x4E);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 22:
                        $this->SetFillColor(0xB8, 0x31, 0x7D);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 24:
                        $this->SetFillColor(0x8F, 0xBE, 0x3C);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                    case 30:
                        $this->SetFillColor(0x1b, 0x75, 0xbb);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 39:
                        $this->SetFillColor(0x1b, 0x75, 0xbb);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    default:
                        $this->SetFillColor(0x77, 0x77, 0x77);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                }
                $this->fontWeight = 'bold';
                $this->SetDrawColor(0x00, 0x00, 0x00);
                $this->padding = 1.0;
                break;
            case 'box_shaded':
                global $invoice;
                switch ($invoice['site_id']) {
                    case 3:
                        $this->SetFillColor(0xFF, 0xFF, 0x00);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                    default:
                        $this->SetFillColor(0x77, 0x77, 0x77);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        $this->border |= 15;
                        break;
                }
                $this->fontWeight = 'bold';
                $this->SetDrawColor(0x00, 0x00, 0x00);
                $this->padding = 0.5;
                break;
            case 'box_normal':
                $this->SetDrawColor(0x00, 0x00, 0x00);
                $this->border |= 15;
                break;
            case 'box_product':
                $this->padding   = 1.0;
                $this->textAlign = 'C';
                $this->border    = $this->border | 15;
                break;
            case 'box_total':
                $this->fontWeight    = 'bold';
                $this->padding       = 1.0;
                $this->textAlign     = 'C';
                $this->verticalAlign = 'M';
                $this->border        = $this->border | 15;
                break;
            case 'company_logo':
                $this->fontWeight = 'bold';
                $this->SetFontSize($this->fontSize = 24);
                break;
            case 'invoice_type':
                $this->fontWeight = 'bold';
                $this->SetFontSize($this->fontSize = 14);
                break;
        }
        $style = '';
        if ('bold' == $this->fontWeight) {
            $style .= 'B';
        }

        if ('italic' == $this->fontStyle) {
            $style .= 'I';
        }

        if ($this->fontString != $style) {
            $this->SetFont('times', $style);
            $this->fontString = $style;
        }
    }

    public function Header()
    {
        //Linear alg: x*0.2874172137205856116627162273851+19.92=
        $this->SetDrawColor(0x23, 0x1F, 0x20);
        $this->SetFillColor(0x00, 0x00, 0xFF);
        $this->Rect(20.024, 89.425, 177.616 - 19.92, 23.123 - 19.92, 'DF');
        $this->Rect(20.024, 78.112, 177.616 - 19.92, 23.358 - 19.92, 'DF');
        $this->SetFillColor(0x23, 0x1F, 0x20);
        $this->Rect(22.132, 53.980, 94.457 - 19.92, 40.997 - 19.92, 'DF');
        $this->Rect(99.852, 53.980, 97.611 - 19.92, 40.997 - 19.92, 'DF');
        $this->Circle(21.557, 200.888, 0.539, 'DF');
        $this->Circle(21.557, 206.825, 0.539, 'DF');
        $this->Circle(21.557, 212.480, 0.539, 'DF');
        $this->Circle(21.557, 215.724, 0.539, 'DF');
        $this->Circle(21.557, 221.678, 0.539, 'DF');
        $this->Circle(21.557, 225.127, 0.539, 'DF');
        $this->SetFillColor(0xAE, 0xDE, 0xE4);
        $this->Rect(125.071, 217.583, 72.649 - 19.92, 29.692 - 19.92, 'F');
        $this->SetFillColor(0xF9, 0xEC, 0x31);
        $this->Rect(135.180, 46.938, 62.252 - 19.92, 24.866 - 19.92, 'F');
        $this->SetFillColor(0xFF, 0xFF, 0xFF);
        $this->Rect(52.988, 39.283, 108.302 - 19.92, 26.900 - 19.92, 'F');
        $this->Rect(20.161, 53.405, 95.853 - 19.92, 40.997 - 19.92, 'DF');
        $this->Rect(99.256, 53.405, 97.611 - 19.92, 40.997 - 19.92, 'DF');
        $this->Rect(20.024, 81.422, 177.616 - 19.92, 25.598 - 19.92, 'D');
        $this->Rect(20.024, 92.739, 177.616 - 19.92, 154.545 - 19.92, 'D');
        $this->Line(34.114, 89.377, 34.114, 195.259);
        $this->Line(41.485, 78.092, 41.485, 87.205);
        $this->Line(63.520, 78.092, 63.520, 87.205);
        $this->Line(79.615, 78.092, 79.615, 87.205);
        $this->Line(104.333, 78.092, 104.333, 87.205);
        $this->Line(112.573, 244.085, 177.720, 244.085);
        $this->Line(119.805, 78.092, 119.805, 87.205);
        $this->Line(124.989, 89.377, 124.989, 227.077);
        $this->Line(125.023, 217.608, 177.530, 217.608);
        $this->Line(137.491, 89.468, 137.491, 195.273);
        $this->Line(138.057, 78.092, 138.057, 87.205);
        $this->Line(156.452, 78.092, 156.452, 87.205);
        $this->Line(156.557, 89.377, 156.557, 227.077);
        $this->SetDrawColor(0x80, 0x80, 0x80);
        $this->Line(19.92, 195.190, 177.442, 195.190);
        $this->Line(20.225, 99.525, 177.155, 99.525);
        $this->Line(20.225, 106.136, 177.155, 106.136);
        $this->Line(20.225, 110.830, 177.155, 110.830);
        $this->Line(20.225, 117.536, 177.155, 117.536);
        $this->Line(124.932, 206.830, 177.446, 206.830);

        $this->headcol = array(
            0 => 0.0,
            1 => 19.92,
            2 => 54.17,
            3 => 100.80,
            4 => 123.63,
            5 => 149.38,
            6 => 177.8,
        );
        foreach ($this->headcol as $key => $value) {
            $this->headcol[$key] = $value + 12.7;
        }

        $this->layoutrow = array(
            0 => 12.7,
        );

        return;

        //invoice stuff
        global $invoice_id, $invoice, $invoice_products, $invoice_charges;
        //jpeg invoice shared stuff
        global $website_path, $charges_list, $terms_list, $warranty_terms_list, $invoice_labels, $website_info, $website_url, $images, $invoice_reference, $invoice_type, $invoice_type_text, $time_to_use, $all_staff_list, $payment_data;

        $ypos1 = $ypos2 = 12.7;

        //Top Logo
        $temp = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader']));
        $this->class_push('base');
        if ('' != $images['company_logo']) {
            $dims   = getimagesize($website_path . $images['company_logo']);
            $height = 34.0;
            $width  = $dims[0] * $height / $dims[1];
            $xpos   = (($this->headcol[6] - $this->headcol[0]) - $width) / 2.0 + $this->headcol[0];
            $this->Image($website_path . $images['company_logo'], $xpos, $ypos1, $width, $height);
            $this->SetXY($this->headcol[1], $ypos1 + $height);
            array_shift($temp);
            if (3 == $invoice['site_id']) {
                $temp = array();
            }
        } else {
            $this->SetXY($this->headcol[1], $ypos1);
            $this->class_push('company_logo');
            $this->MultiCell($this->headcol[5] - $this->headcol[1], $this->fontSize / 2.5, array_shift($temp), $this->border_string(), $this->textAlign, 1);
            $this->class_pop();
        }
        $this->MultiCell($this->headcol[5] - $this->headcol[1], $this->fontSize / 2.5, implode("\n", $temp), $this->border_string(), $this->textAlign, 1);
        $ypos1 = $ypos2 = $this->GetY();

        //Top Right
        $this->class_push('box_shaded');
        $this->class_push('invoice_type');
        $this->textAlign = 'C';
        $this->SetXY($this->headcol[3], $ypos2);
        $this->DoTableRow();
        $this->DoTableCell($this->headcol[6] - $this->headcol[3], 0, $invoice_type_text);
        $this->DoEndTableRow();
        $this->class_pop();
        $this->class_pop();
        $ypos2 = $this->GetY();

        $ypos1 = $ypos2 = max($ypos1, $ypos2);
        $this->SetY($ypos1);
        $this->layoutrow[] = $ypos1;

        $contacts_list = array();
        if (9 == $invoice_type) {
            if ('' != $invoice['bill_address_full']) {
                $contacts_list['Consignee'] = $invoice['bill_address_full'];
            }
        } else {
            if ('' != $invoice['bill_address_full']) {
                $contacts_list['Consignee'] = $invoice['bill_address_full'];
            }

//            if($invoice['ship_address_full']!='')
//                $contacts_list['SHIP TO']=$invoice['ship_address_full'];
//            if($invoice['notify_address_full']!='' && $action=='shipview')
//                $contacts_list['NOTIFY PARTY']=$invoice['notify_address_full'];//."\n".$invoice['notify_email'];
        }
        $contacts_layout = array_chunk(array_keys($contacts_list), 2);

        //Contacts
        foreach ($contacts_list as $key => $value) {
            //$this->class_push('box_normal');
            $this->SetX($this->headcol[1]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $key . ":\n" . $value);
            $this->DoEndTableRow();
            //$this->class_pop();

            $this->Ln();
            $ypos1 = $ypos2 = $this->GetY();
        }
        $this->layoutrow[] = $ypos1;

        $temp         = array();
        $temp['NO.']  = $invoice_reference;
        $temp['DATE'] = date('jS F, Y', $time_to_use);
        if (9 == $invoice_type) {
            //    if($all_staff_list[$invoice['handler_id']]!='')    $temp['SALES REP']=$all_staff_list[$invoice['handler_id']];
            //    $temp['DISCHARGE PORT']=($supplier_country!='')?$country_list[$supplier_country]:'-';
            if ('' != $terms_list[$invoice['terms_id']]) {
                $temp['SALES TERMS'] = $terms_list[$invoice['terms_id']];
            }

            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }
            if ('' != $invoice['ship_product_name']) {
                $temp['VIA.'] = $invoice['ship_product_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TERMS'] = $payment_data['payment_method_name'];
            }

            //    if($invoice['proforma_tracknumber']!='')        $temp['TRACKING #']=$invoice['proforma_tracknumber'];
        } else {
            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }
            if ('' != $invoice['ship_product_name']) {
                $temp['VIA.'] = $invoice['ship_product_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TERMS'] = $payment_data['payment_method_name'];
            }

            if ('' != $invoice['proforma_purchaseorder']) {
                $temp['CLIENT P.O.#'] = $invoice['proforma_purchaseorder'];
            }

            //    if($terms_list[$invoice['terms_id']]!='')        $temp['SHIPPING TERMS']=$terms_list[$invoice['terms_id']];
            //    if($invoice['ship_account_num']!='')            $temp['SHIP ACCOUNT']=$invoice['ship_account_num'];
            //    if($invoice['proforma_tracknumber']!='')        $temp['TRACKING #']=$invoice['proforma_tracknumber'];
            //    if($all_staff_list[$invoice['handler_id']]!='')    $temp['SALES REP']=$all_staff_list[$invoice['handler_id']];
        }

        if (count($temp) > 0) {
            $widths = array();

            $this->class_push('box_header');
            foreach ($temp as $key => $value) {
                $widths[$key] = $this->GetStringWidth($key) + 2 * $this->padding;
            }

            $this->class_pop();
            $this->class_push('box_product');
            foreach ($temp as $key => $value) {
                $widths[$key] = max($widths[$key], $this->GetStringWidth($value) + 2 * $this->padding);
            }

            $this->class_pop();

            $total_width = array_sum($widths);
            $max_width   = $this->headcol[6] - $this->headcol[0];
            $free_space  = ($max_width - $total_width) / count($temp);

            $this->class_push('box_header');
            $this->textAlign = 'C';
            $this->SetX($this->headcol[0]);
            $this->DoTableRow();
            foreach ($temp as $key => $value) {
                $this->DoTableCell($widths[$key] + $free_space, 0, $key);
            }

            $this->DoEndTableRow();
            $this->class_pop();

            $this->class_push('box_product');
            $this->textAlign = 'C';
            $this->SetX($this->headcol[0]);
            $this->DoTableRow();
            foreach ($temp as $key => $value) {
                $this->DoTableCell($widths[$key] + $free_space, 0, $value);
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
        //invoice stuff
        global $invoice_id, $invoice, $invoice_products, $invoice_charges;
        //jpeg invoice shared stuff
        global $charges_list, $terms_list, $warranty_terms_list, $invoice_labels, $website_info, $website_url, $images, $invoice_reference, $invoice_type, $invoice_type_text, $time_to_use, $all_staff_list, $payment_data;
    }

    public function Middle()
    {
        return;

        //invoice stuff
        global $invoice_id, $invoice, $invoice_products, $invoice_charges;
        //jpeg invoice shared stuff
        global $charges_list, $terms_list, $warranty_terms_list, $invoice_labels, $website_info, $website_url, $images, $invoice_reference, $invoice_type, $invoice_type_text, $time_to_use, $all_staff_list, $payment_data;

        $ypos1 = $ypos2 = $this->GetY();

        $this->class_push('box_header');
        $this->textAlign = 'C';
        $this->SetX($this->headcol[0]);
        $this->DoTableRow();
/*        if($invoice_type==9)
{
$this->DoTableCell($this->headcol[1]-$this->headcol[0], 0, 'SUPPLIER');
$this->DoTableCell($this->headcol[2]-$this->headcol[1], 0, 'ITEM#');
$this->DoTableCell($this->headcol[3]-$this->headcol[2], 0, 'DESCRIPTION');
$this->DoTableCell($this->headcol[4]-$this->headcol[3], 0, 'QUANTITY');
$this->DoTableCell($this->headcol[5]-$this->headcol[4], 0, 'UNIT PRICE');
$this->DoTableCell($this->headcol[6]-$this->headcol[5], 0, 'UNIT PRICE (USD)');
$this->DoTableCell($this->headcol[7]-$this->headcol[6], 0, 'AMOUNT (USD)');
}
else
 */{
            $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, 'ITEM#');
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, 'DESCRIPTION');
            $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, 'QUANTITY');
            $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, 'UNIT PRICE');
            $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, 'AMOUNT');
        }
        $this->DoEndTableRow();
        $this->class_pop();
        $ypos1 = $ypos2 = $this->GetY();

        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
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

                $this->SetX($this->headcol[0]);
                $this->DoTableRow();
/*                if($invoice_type==9)
{
$this->textAlign='L';
$this->DoTableCell($this->headcol[1]-$this->headcol[0], 0, $product['supplier_name']);
$this->textAlign='C';
$this->DoTableCell($this->headcol[2]-$this->headcol[1], 0, $product['product_id']);
$this->textAlign='L';
$this->DoTableCell($this->headcol[3]-$this->headcol[2], 0, $product['product_name']);
$this->textAlign='C';
$this->DoTableCell($this->headcol[4]-$this->headcol[3], 0, $product['product_qty']);
$this->DoTableCell($this->headcol[5]-$this->headcol[4], 0, $product['product_offer']);
$this->DoTableCell($this->headcol[6]-$this->headcol[5], 0, convertCurrency($product['product_costus'],'USD'));
}
else
 */{
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, $product['product_id']);
                    $this->textAlign = 'L';
                    $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $product['product_name']);
                    $this->textAlign = 'C';
                    $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, $product['product_qty']);
                    $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, (($product['product_offer'] > 0) ? $product['product_offer'] . ' ' . $invoice['invoice_currency'] : 'TBA'));
                }
                $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, (($product['product_total'] > 0) ? $product['product_total'] . ' ' . $invoice['invoice_currency'] : 'TBA'));
                $this->DoEndTableRow();
                if ('' != $product['product_notes']) {
                    $this->border    = ($this->border ^ 3);
                    $this->textAlign = 'L';
                    $this->SetFont('times', 'I');
                    $this->SetX($this->headcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, '');
/*                    if($invoice_type==9)
{
$this->DoTableCell($this->headcol[3]-$this->headcol[1], 0, '');
$this->DoTableCell($this->headcol[4]-$this->headcol[2], 0, $product['product_notes']);
$this->DoTableCell($this->headcol[5]-$this->headcol[3], 0, '');
}
else
 */$this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $product['product_notes']);
                    $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, '');
                    $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, '');
                    $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, '');
                    $this->DoEndTableRow();
                    $this->SetFont('times', '');
                }
                $this->class_pop();
                $ypos1 = $ypos2 = $this->GetY();
            }
        }

        if (0 != $invoice['ship_id'] && 9 != $invoice_type) {
            $this->class_push('box_product');
            if ('' != $invoice['ship_product_notes']) {
                $this->border = ($this->border&~2);
            }

            $this->SetX($this->headcol[0]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, sprintf('F%02d', $invoice['ship_id']) . (('y' == $invoice['ship_insurance']) ? 'I' : 'N'));
            $this->textAlign = 'L';
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $invoice['ship_product_name']);
            $this->textAlign = 'C';
            $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, '');
            $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency']);
            $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency']);
            $this->DoEndTableRow();
            if ('' != $invoice['ship_product_notes']) {
                $this->border    = ($this->border | 2);
                $this->textAlign = 'L';
                $this->SetX($this->headcol[0]);
                $this->DoTableRow();
                $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, '');
                $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $invoice['ship_product_notes']);
                $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, '');
                $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, '');
                $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, '');
                $this->DoEndTableRow();
            }
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
        }

        $sub_total = $invoice['invoice_offer'];
        $gst_offer = 0;

        if (is_array($invoice_charges) && 9 != $invoice_type) {
            foreach ($invoice_charges as $charge) {
                if (1 != $charge['charge_id']) {
                    /* Do not display GST here */
                    $this->class_push('box_product');
                    if ('' != $invoice['ship_product_notes']) {
                        $this->border = ($this->border&~2);
                    }

                    $this->SetX($this->headcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, sprintf('C%02d', $invoice['charge_id']));
                    $this->textAlign = 'L';
                    $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $charges_list[$charge['charge_id']]);
                    $this->textAlign = 'C';
                    $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, '');
                    $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, convertCurrency($charge['charge_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency']);
                    $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, convertCurrency($charge['charge_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency']);
                    $this->DoEndTableRow();
                    $this->class_pop();
                    $ypos1 = $ypos2 = $this->GetY();
                } else {
                    $gst_offer += convertCurrency($charge['charge_offer'], $invoice['invoice_currency']);
                }
            }
        }
        $sub_total -= $gst_offer;

        if ($this->GetY() < 227.0) {
            $height = 227.0 - $this->GetY();
            $this->class_push('box_product');
            $this->SetX($this->headcol[0]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[1] - $this->headcol[0], $height, '');
/*            if($invoice_type==9)
{
$this->DoTableCell($this->headcol[2]-$this->headcol[1], $height, '');
$this->DoTableCell($this->headcol[3]-$this->headcol[2], $height, '');
$this->DoTableCell($this->headcol[4]-$this->headcol[3], $height, '');
}
else
 */$this->DoTableCell($this->headcol[3] - $this->headcol[1], $height, '');
            $this->DoTableCell($this->headcol[4] - $this->headcol[3], $height, '');
            $this->DoTableCell($this->headcol[5] - $this->headcol[4], $height, '');
            $this->DoTableCell($this->headcol[6] - $this->headcol[5], $height, '');
            $this->DoEndTableRow();
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
        }

        $temp = array();
/*        if($invoice_type==9)
$temp['Total']=convertCurrency($invoice['products_cost'],$invoice['invoice_currency']);
else
 */{
            $temp['Sub Total'] = ($sub_total > 0) ? convertCurrency($sub_total, $invoice['invoice_currency']) : 'TBA';
            if (0 != $gst_offer) {
                $temp['GST 10%'] = convertCurrency($gst_offer, $invoice['invoice_currency']);
            }

            $temp['Shipping Charges'] = ($invoice['ship_offer'] > 0) ? convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) : 'TBA';
            $temp['Invoice Amount']   = ($invoice['invoice_offer'] > 0) ? convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'] : 'TBA';
        }
        reset($temp);

        foreach ($temp as $key => $value) {
            $this->SetX($this->headcol[4]);
            $this->DoTableRow();

            if ('Invoice Amount' == $key) {
                $this->class_push('box_total');
            } else {
                $this->class_push('box_product');
            }

            $this->textAlign = 'C';
            $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, $key);
            $this->class_pop();

            $this->class_push('box_total');
            $this->textAlign = 'C';
            $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, $value);
            $this->class_pop();

            $this->DoEndTableRow();
        }
        $ypos2 = $this->GetY() + 29.0;

        $the_text   = array();
        $the_text[] = 'SALESMAN: ' . $all_staff_list[$invoice['handler_id']];
        $the_text[] = '';
        $the_text[] = '1. Delivery Date: ' . ((0 != $invoice['delivery_time']) ? date('jS F, Y', $invoice['delivery_time']) : '');
        $the_text[] = '2. Warranty Period: ' . $warranty_terms_list[$invoice['waranty_terms_id']];
        $the_text[] = '3. Remarks: ' . $invoice['invoice_comments'];
        $the_text[] = '4. No Return of Goods Accepted without Written Authorization.';
        $the_text[] = '5. All business transactions subject to our standard trading terms and conditions.';
        if ('' != $payment_data['payment_method_details']) {
            $the_text[] = '6. Bank Details:';
            $the_text[] = trim($payment_data['payment_method_details']);
        }
        $the_text = implode("\n", $the_text);

        $this->SetXY($this->headcol[0], $ypos1);
        $this->MultiCell($this->headcol[4] - $this->headcol[0], $this->fontSize / 2.5, $the_text, 0, 'L', 0);
        $this->Ln();
        $ypos1 = $ypos2 = max($ypos2, $this->GetY());

        $this->layoutrow[] = $ypos1;
        $this->SetY($ypos1);

        if (9 != $invoice_type) {
            $this->textAlign = 'R';
            $this->SetFont('times', 'I');
            $this->SetX($this->headcol[3]);
            $this->border |= 1;
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[6] - $this->headcol[3], 0, strtoupper($invoice_labels['company_name']) . ' Company Stamp and Signature');
            $this->DoEndTableRow();
            $this->SetFont('times', '');
?>
			<img src="<?=$images['signature']?>" alt="" style="height:0.5in;" /><br />
<?php
        }
    }

    private $table_row_content = array();
    private $table_row_height  = 0;

    public function DoTableCell($width, $height = 0, $text = '')
    {
        $xpos = $this->GetX();
        $ypos = $this->GetY();
        if (0 == $height) {
            $height = $this->fontSize / 2.5;
        }

        $height = max($height, $this->NbLines($width - 2 * $this->padding, $text) * $this->fontSize / 2.5);
        $height += 2 * $this->padding;
        $this->table_row_content[] = array(
            $xpos, $ypos, $width, $height, $text, $this->class_buffer,
            $this->textAlign, $this->fontSize, $this->padding, $this->border,
        );
        $this->table_row_height = max($this->table_row_height, $height);
//        $this->Cell($width, $height, $text, $this->border_string(), 0, $this->textAlign, 1);
        $this->SetX($xpos + $width);
    }
    public function DoTableRow()
    {
        $this->table_row_height  = $this->fontSize / 2.5;
        $this->table_row_content = array();
        //Do Nothing
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
            $this->SetXY($cell[0], $cell[1]);
            $this->Cell($cell[2], $this->table_row_height, '', $this->border_string(), 0, '', 1);
            $this->class_pop();
        }
        foreach ($this->table_row_content as $cell) {
            $this->class_push('base');
            foreach ($cell[5] as $class) {
                $this->class_apply($class);
            }

            $this->fontSize = $cell[7];
            $this->SetXY($cell[0] + $cell[8], $cell[1] + $cell[8]);
            if ('M' == $this->verticalAlign) {
                $this->SetXY($cell[0] + $cell[8], $cell[1] + $cell[8] + ($this->table_row_height - $cell[3]) / 2);
            }

            $this->MultiCell($cell[2] - 2 * $cell[8], $this->fontSize / 2.5, $cell[4], 0, $cell[6]);
            $this->class_pop();
        }
        $this->Ln();
        $this->SetY($ypos1 + $this->table_row_height);
        $this->table_row_content = array();
    }
    public function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if (0 == $w) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s    = str_replace("\r", '', $txt);
        $nb   = strlen($s);
        if ($nb > 0 and "\n" == $s[$nb - 1]) {
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
$pdf = new TCPDFInvoice();
$pdf->Middle();
ob_end_clean();
$invoice_buffer = $pdf->Output('invoice.pdf', 'S');
