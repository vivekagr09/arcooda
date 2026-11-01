<?php
/**
 * @var object      $DB
 * @var mixed[]     $_GLOBALS
 *
 * @var mixed[]     $invoice
 * @var mixed[][]   $invoice_products
 * @var mixed[][]   $invoice_charges
 *
 * @var string|null $force_signature
 */

use Fpdf\Fpdf;

moduleLoad('fpdf');
moduleLoad('invoice');

global $DB, $_GLOBALS;

$staff        = $_GLOBALS['staff'];
$payment_data = $DB->query_first(sprintf(
    "SELECT * FROM `payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
    $invoice['site_id'],
    formatSQLString($invoice['paymenttype_id'])
), MYSQLI_ASSOC);
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';

$force_images                   = [];
$force_images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$force_images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$force_images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$force_images['signature']      = 'images/sigs/' . $_GLOBALS['staff']['signature'];

$boundary = md5(strval(mt_rand(0, 65535)));

foreach ($force_images as $key => $value) {
    if (!is_file($website_path . $value)) {
        $force_images[$key] = '';
    }

}

//-----------------------------------------------------------------------------
//This part should be the same as jpeg_invoice2.tpl.php

$charges_list = [];
$query        = $DB->query(sprintf(
    "SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name` ASC"
));
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}
$DB->free_result($query);

$terms_list          = getTermsList();
$warranty_terms_list = getWarrantyTermsList();
$staff               = $_GLOBALS['staff'];
$payment_data        = $DB->query_first(sprintf(
    "SELECT * FROM `payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
    $invoice['site_id'],
    formatSQLString($invoice['paymenttype_id'])
), MYSQLI_ASSOC);
$invoice_labels      = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
$website_info        = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`websites` WHERE `id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);

//$website_url = $website_info['url'];
//$website_path = $website_info['root_dir'];
$website_url  = 'https://' . $_SERVER['SERVER_NAME'] . '/';
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';
if (substr($website_url, -1) != '/') {
    $website_url .= '/';
}

if (substr($website_path, -1) != '/') {
    $website_path .= '/';
}
$images                   = [];
$images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$images['signature']      = 'images/sigs/' . $_GLOBALS['staff']['signature'];
if (in_array($invoice['invoice_status'], ['enquiry', 'inquiry', 'closed'])) {
    $images['signature'] = '';
}

if (isset($force_signature) && '' != $force_signature) {
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

$invoice_type = array_search($invoice['invoice_status'], [
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
]);

$invoice_reference = strtoupper(purchaseOrderFormatNumber($invoice['order_id'], (3 == $invoice_type || 4 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id'],1));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['supplier_id'], 0), 5));
}

$invoice_type_text = 'QUOTATION';
if ($invoice_type > 2) {
    $invoice_type_text = strtoupper($invoice['invoice_status']) . ' INVOICE';
}

if (in_array($invoice['invoice_status'], ['sales', 'processed'])) {
    $invoice_type_text = 'SALES INVOICE';
}

if (9 == $invoice_type) {
    $invoice_type_text = 'PURCHASE SUMMARY';
}
 $invoice_type_text = (((strpos($invoice_reference, 'PECO#') !== false))?'PURCHASE INQUIRY':'PURCHASE ORDER')."                                                                       ".$invoice_reference;
 ///////$invoice_type_value=$invoice_reference;
$time_to_use = $invoice['open_time'];
if ($invoice_type < 3) {
    $time_to_use = time();
} elseif ($invoice['proforma_time'] > 0) {
    $time_to_use = $invoice['proforma_time'];
}

if (isset($invoice_time) && 0 != $invoice_time) {
    $time_to_use = $invoice_time;
}

//-----------------------------------------------------------------------------

class FPDF_Invoice extends Fpdf
{
    /**
     * @var string $textAlign
     */
    public $textAlign = 'L';

    /**
     * @var float $fontSize
     */
    public $fontSize = 12.0;

    /**
     * @var float $padding
     */
    public $padding = 0.0;

    /**
     * @var int $border
     */
    public $border = 0;

    /**
     * @var float[] $headcol
     */
    public $headcol = [];

    /**
     * @var string $fontWeight
     */
    public $fontWeight = 'normal';

    /**
     * @var string $fontStyle
     */
    public $fontStyle = 'normal';

    /**
     * @var string $fontString
     */
    public $fontString = '';

    /**
     * @var string $verticalAlign
     */
    public $verticalAlign = 'T';

    /**
     * @var string[] $class_buffer
     */
    public $class_buffer = [];

    /**
     * @param string $orientation
     * @param string $unit
     * @param string $format
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        $this->SetFont('times', '', $this->fontSize = 8);
        $this->SetMargins(12.7, 12.7);
        $this->SetAutoPageBreak(true);
        $this->AddPage();
    }

    /**
     * @return string
     */
    public function border_string()
    {
        $result = '';
        if ($this->border & 1) {
            $result .= 'T';
        }

        if ($this->border & 2) {
            $result .= 'B';
        }

        if ($this->border & 4) {
            $result .= 'L';
        }

        if ($this->border & 8) {
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

    /**
     * @param  string $class
     * @return void
     */
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
                $this->border |= 15;
                global $invoice;
                switch ($invoice['site_id']) {
                    case 1:$this->SetFillColor(0x00, 0x23, 0xF4);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 2:$this->SetFillColor(0x00, 0xC9, 0x00);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 3:$this->SetFillColor(0x99, 0xCC, 0xFF);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        $this->border = 0;
                        break;
                    case 9:$this->SetFillColor(0x45, 0x4B, 0x84);
                        $this->SetTextColor(0xFF, 0xFF, 0xFF);
                        break;
                    case 16:$this->SetFillColor(0x29, 0x3B, 0x4E);
                        $this->SetTextColor(0xE2, 0xEE, 0xFA);
                        break;
                    default:$this->SetFillColor(0x77, 0x77, 0x77);
                        $this->SetTextColor(0x00, 0x00, 0x00);
                        break;
                }
                $this->border    = $this->border | 15;
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
                $this->SetMargins(12.7, 12.7);
                $this->border    = $this->border | 15;
                break;
             case 'inner_cell_class':
                $this->padding   = 1.0;
                $this->SetMargins(12.7, 12.7);
                $this->border    = 0;
                $this->fontStyle  = 'italic';
                $this->SetFontSize(8);
                break;
             case 'box_addrress':
                $this->padding   = 2.0;
                $this->textAlign = 'L';
                $this->border    = $this->border | 15;
                break;
            case 'box_total':
                $this->fontWeight    = 'bold';
                $this->padding       = 1.0;
                $this->textAlign     = 'C';
                $this->verticalAlign = 'M';
                $this->border        = $this->border | 15;
                break;
             case 'center_align_text':
                $this->padding       = 1.0;
                $this->textAlign     = 'C';
                $this->verticalAlign = 'M';
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

    }

    public function WasHeader()
    {
        $this->headcol = [
            0 => 0.0,
            1 => 19.92,
            2 => 54.17,
            3 => 100.80,
            4 => 123.63,
            5 => 149.38,
            6 => 177.8,
            7 => 24.92,
        ];
        foreach ($this->headcol as $key => $value) {
            $this->headcol[$key] = $value + 12.7;
        }

        $this->layoutrow = [
            0 => 12.7,
        ];

        //invoice stuff
        global $invoice, $invoice_products, $invoice_charges;
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
                $temp = [];
            }

        } else {
            $this->SetXY($this->headcol[1], $ypos1);
            $this->class_push('company_logo');
            $this->MultiCell($this->headcol[5] - $this->headcol[1], $this->fontSize / 2.5, utf8_decode(array_shift($temp)), $this->border_string(), $this->textAlign, 1); // TODO: Remove utf8_decode once pdf is utf-8 compatible
            $this->class_pop();
        }
        $this->MultiCell($this->headcol[5] - $this->headcol[1], $this->fontSize / 2.5, utf8_decode(implode("\n", $temp)), $this->border_string(), $this->textAlign, 1); // TODO: Remove utf8_decode once pdf is utf-8 compatible
        $ypos1 = $ypos2 = $this->GetY();

        //Top Right
        $this->class_push('box_product');
        $this->class_push('invoice_type');
        $this->textAlign = 'L';
        $this->SetXY($this->headcol[0], $ypos2);
        $this->DoTableRow();
        $this->DoTableCell($this->headcol[6] - $this->headcol[0], 0, $invoice_type_text);
        $this->DoEndTableRow();
        $this->class_pop();
        $this->class_pop();
        $ypos2 = $this->GetY();

        $ypos1 = $ypos2 = max($ypos1, $ypos2);
        $this->SetY($ypos1);
        $this->layoutrow[] = $ypos1;

        $contacts_list = [];
        if (9 == $invoice_type) {
            if ('' != $invoice['bill_address_full']) {
                $contacts_list['Supplier'] = $invoice['bill_address_full'];
            }

        } else {
            if ('' != $invoice['bill_address_full']) {
                $contacts_list['Supplier'] = $invoice['bill_address_full'];
            }
            if ('' != $invoice['ship_to']) {
                $contacts_list['Delivery to'] = ($invoice['ship_to'])?getOrderAddressByid($invoice['ship_to']):'';
            }
            /*
            if ('' != $invoice['notify_address_full']) {
                $contacts_list['NOTIFY PARTY'] = $invoice['notify_address_full'];
            }
            */

            /*
            if ('' != $invoice['ship_address_full'])
            {
                $contacts_list['SHIP TO'] = $invoice['ship_address_full'];
            }
            if ('' != $invoice['notify_address_full'] && 'shipview' == $action)
            {
                $contacts_list['NOTIFY PARTY'] = $invoice['notify_address_full']; //. "\n" . $invoice['notify_email'];
            }
            */
        }
        $contacts_layout = array_chunk(array_keys($contacts_list), 2);

        //Contacts
        $this->class_push('box_header');
        $this->textAlign = 'C';
        $this->SetX($this->headcol[0]);
         $yset=$this->GetY();
         $yset=$yset+5;
        $this->SetY($yset);
        $this->DoTableRow();
        {
            $this->DoTableCell(89, 0, 'SUPPLIER');
            $this->DoTableCell(89, 0, 'DELIVERY TO');
        }
        $this->DoEndTableRow();
        $this->class_pop();
        $this->SetX($this->headcol[0]);
        $this->DoTableRow();
        foreach ($contacts_layout as $row) {
            //$this->class_push('box_normal');
            foreach ($row as $v) {
                $this->class_push('box_addrress');
                $this->DoTableCell(89, 0, $contacts_list[$v]);
                //$this->DoTableCell($this->headcol[$key] - $this->headcol[$key-3], 0, $v . ":\n" . $contacts_list[$v]);
                
                $this->class_pop();
            }
        }
        $this->DoEndTableRow();
            //$this->class_pop();
            $this->Ln();
            $this->layoutrow[] = $this->GetY();
        $temp         = [];
       // $temp['NO.']  = $invoice_reference;
        $temp['DATE'] = date('jS F, Y', $time_to_use);
        if (9 == $invoice_type) {
            if ('' != $terms_list[$invoice['terms_id']]) {
                $temp['SALES TERMS'] = $terms_list[$invoice['terms_id']];
            }

            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TO'] = $payment_data['payment_method_name'];
            }

            if ('' != $invoice['proforma_tracknumber']) {
                $temp['TRACKING #'] = $invoice['proforma_tracknumber'];
            }

        } else {
            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TO'] = $payment_data['payment_method_name'];
            }

            if ('' != $invoice['proforma_purchaseorder']) {
                $temp['CLIENT P.O.#'] = $invoice['proforma_purchaseorder'];
            }

            /*
            if ('' != $terms_list[$invoice['terms_id']])
            {
                $temp['SHIPPING TERMS'] = $terms_list[$invoice['terms_id']];
            }
            if ('' != $invoice['ship_account_num'])
            {
                $temp['SHIP ACCOUNT'] = $invoice['ship_account_num'];
            }
            */
            if ('' != $invoice['proforma_tracknumber']) {
                $temp['TRACKING #'] = $invoice['proforma_tracknumber'];
            }
            /*
            if ('' != $all_staff_list[$invoice['handler_id']])
            {
                $temp['SALES REP'] = $all_staff_list[$invoice['handler_id']];
            }
            */
        }
        $temp['MANAGER'] = $all_staff_list[$invoice['staff_id']];
        $temp['OUR REF'] = $invoice['order_refrence'];
        if (count($temp) > 0) {
            $widths = [];

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
        global $invoice, $invoice_products, $invoice_charges;
        //jpeg invoice shared stuff
        global $charges_list, $terms_list, $warranty_terms_list, $invoice_labels, $website_info, $website_url, $images, $invoice_reference, $invoice_type, $invoice_type_text, $time_to_use, $all_staff_list, $payment_data;
    }

    public function Middle()
    {
        $this->WasHeader();

        //invoice stuff
        global $invoice, $invoice_products, $invoice_charges;
        //jpeg invoice shared stuff
        global $charges_list, $terms_list, $warranty_terms_list, $invoice_labels, $website_info, $website_path, $website_url, $images, $invoice_reference, $invoice_type, $invoice_type_text, $time_to_use, $all_staff_list, $payment_data;

        $ypos1 = $ypos2 = $this->GetY();

        $this->class_push('box_header');
        $this->textAlign = 'C';
        $this->SetX($this->headcol[0]);
        $this->DoTableRow();
        {
            $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, 'ITEM#');
            $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, 'DESCRIPTION');
            $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, 'QTY');
            $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, 'UNIT PRICE');
            $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, 'TOTAL');
        }
        $this->DoEndTableRow();
        $this->class_pop();
        $ypos1 = $ypos2 = $this->GetY();
        $warranty_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('warranty_terms_id');
        $plugs_list      = array('-1' => 'Not Applicable') + invoiceGetOptionList('plug_id');
        $packing_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('packing_requirement');
        $voltage_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('voltage_id2');
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                $product_name=($product['generic']=='y')?$product['supplier_productname']:$product['product_name'];
                $product_warranty=(($product['product_warranty'])?("\n".'-Warranty :'.$warranty_terms[$product['product_warranty']]):'').(($product['product_plug'])?("\n".'-Plug Type :'.$plugs_list[$product['product_plug']]):'').(($product['product_packing'])?("\n".'-Packing :'.$packing_terms[$product['product_packing']]):'').(($product['product_voltage'])?("\n".'-Voltage :'.$voltage_terms[$product['product_voltage']]):'');
                $product_id=($product['generic']=='y')?$product['product_id']:$product['sup_code'];
                $product_qty=($product['product_qty'])?$product['product_qty']:0;
                if ($ypos2 > 227.0) {
                    $this->AddPage();
                    $this->class_push('box_header');
                    $this->textAlign = 'C';
                    $this->SetX($this->headcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, 'ITEM#');
                    $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, 'DESCRIPTION');
                    $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, 'QUANTITY');
                    $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, 'UNIT PRICE');
                    $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, 'AMOUNT');
                    $this->DoEndTableRow();
                    $this->class_pop();
                    $ypos1 = $ypos2 = $this->GetY();
                }

                if (9 == $invoice_type) {
                    $product['product_offer'] = convertCurrency($product['product_cost'], $invoice['invoice_currency']);
                } else {
                    $product['product_offer'] = convertCurrency($product['product_offer'], $invoice['invoice_currency']);
                }

                $product['product_total'] = convertCurrency($product['product_qty'] * $product['product_offer'], $invoice['invoice_currency']);

                $this->class_push('box_product');
                if ('' != $product['product_notes']) {
                    $this->border = ($this->border & ~2);
                }

                $this->SetX($this->headcol[0]);
                $this->DoTableRow();
                    $proimages=purchaseOrderimagesFromDatabase($product['order_id'],1,$product['id']);
                        if(count($proimages)){
                            $image= 'webadmin/assets/suppliers/' . $proimages[0]['filename'] . '';
                            $dims   = getimagesize($website_path . $image);
                            $height = 34.0;
                            $yposn = $this->GetY();
                             $xpos   = $this->headcol[3] - $this->headcol[2];
                            $width  = $dims[0] * $height / $dims[1];
                    //$this->SetXY($this->headcol[1], $ypos1 + $height);
                    //$this->Image($website_path . $image, $xpos);
                            
                            
                        }
                    $this->class_push('center_align_text');
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0,$product_id);
                    $this->class_pop();
                    $this->textAlign = 'L';
                    $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0,$product_name,true);
                    $this->class_push('inner_cell_class');
                    $this->DoInnerTableCell($this->headcol[3] - $this->headcol[1], 0,$product_warranty,false);
                   $this->class_pop();
                    $this->class_push('center_align_text');
                    $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, $product_qty);
                    $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, (($product['product_cost'] > 0) ?number_format($product['product_cost'], 2, '.', ',') . ' ' . $invoice['invoice_currency'] : 'TBA'));
                   
                $this->textAlign = 'C';
                $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, (( ($product_qty*$product['product_cost']) > 0) ? number_format(($product_qty*$product['product_cost']), 2, '.', ',') . ' ' . $invoice['invoice_currency'] : 'TBA'));
                 $this->class_pop();
                $this->DoEndTableRow();
                if ('' != $product['product_notes']) {
                    $this->border    = ($this->border ^ 3);
                    $this->textAlign = 'L';
                    $this->SetFont('times', 'I');
                    $this->SetX($this->headcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, '');
                    /*
                    if (9 == $invoice_type)
                    {
                        $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, '');
                        $this->DoTableCell($this->headcol[4] - $this->headcol[2], 0, $product['product_notes']);
                        $this->DoTableCell($this->headcol[5] - $this->headcol[3], 0, '');
                    }
                    else
                    */
                    {
                        $this->DoTableCell($this->headcol[3] - $this->headcol[1], 0, $product['product_notes']);
                        $this->DoTableCell($this->headcol[4] - $this->headcol[3], 0, '');
                        $this->DoTableCell($this->headcol[5] - $this->headcol[4], 0, '');
                        $this->DoTableCell($this->headcol[6] - $this->headcol[5], 0, '');
                    }
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
                $this->border = ($this->border & ~2);
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

        $sub_total = $invoice['total_invoice_cost'];
        $gst_offer = 0;

        if (is_array($invoice_charges) && 9 != $invoice_type) {
            foreach ($invoice_charges as $charge) {

                // It was 1 before. But 1 is paypal in Coinop. There is no GST charges registered.
                // Charg ID 0 does not exists. So, charges will be display in invoice.
                if (0 != $charge['charge_id']) {
                    //Do not display GST here
                    $this->class_push('box_product');
                    if ('' != $invoice['ship_product_notes']) {
                        $this->border = ($this->border & ~2);
                    }

                    $this->SetX($this->headcol[0]);
                    $this->DoTableRow();
                    $this->DoTableCell($this->headcol[1] - $this->headcol[0], 0, sprintf('C%02d', $charge['charge_id']));
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
            /*
            if (9 == $invoice_type)
            {
                $this->DoTableCell($this->headcol[2] - $this->headcol[1], $height, '');
                $this->DoTableCell($this->headcol[3] - $this->headcol[2], $height, '');
                $this->DoTableCell($this->headcol[4] - $this->headcol[3], $height, '');
            }
            else
            */
            {
                $this->DoTableCell($this->headcol[3] - $this->headcol[1], $height, '');
                $this->DoTableCell($this->headcol[4] - $this->headcol[3], $height, '');
                $this->DoTableCell($this->headcol[5] - $this->headcol[4], $height, '');
                $this->DoTableCell($this->headcol[6] - $this->headcol[5], $height, '');
            }
            $this->DoEndTableRow();
            $this->class_pop();
            $ypos1 = $ypos2 = $this->GetY();
        }

        $temp = [];
        /*
        if (9 == $invoice_type)
        {
            $temp['Total'] = convertCurrency($invoice['products_cost'], $invoice['invoice_currency']);
        }
        else
        */
        {
            $temp['Sub Total'] = ($sub_total > 0) ? number_format(convertCurrency($sub_total, $invoice['invoice_currency']), 2, '.', ',') : 'TBA';
            if (0 != $gst_offer) {

                // Hack
                $label = 'GST 10%';

                $temp[$label] = convertCurrency($gst_offer, $invoice['invoice_currency']);
            }

            //$temp['Shipping Charges'] = ($invoice['ship_offer'] > 0) ? convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']) : 'TBA';
            $temp['Invoice Amount'] = ($invoice['total_invoice_cost'] > 0) ? number_format(convertCurrency($invoice['total_invoice_cost'], $invoice['invoice_currency']), 2, '.', ',') . ' ' : 'TBA';
            $temp['Invoice Currency'] = $invoice['invoice_currency'];
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

        $proforma_comments = $invoice['proforma_comments'];
        if (!is_array($proforma_comments)) {
            $proforma_comments = explode(',', $proforma_comments);
        }

        $the_text = [];
       /* if (0 == $invoice['handler_id']) {
            $the_text[] = 'SALESMAN: ONLINE';
        } else {
            $the_text[] = 'SALESMAN: ' . $all_staff_list[$invoice['handler_id']];
        }*/
       
        $the_text[] = "\n".'Goods Needed BY : '.(($invoice['needby_date'])?date('d-m-Y',strtotime($invoice['needby_date'])):'N/A')."\n";
         $the_text[] = 'Special Request : '.$invoice['special_request']."\n";
        $the_text[] = '';


        $proforma_points = [];


        $n    = 0;
        $prev = ':';
      

        /*
        $the_text[] = '1. Delivery Date: ' . (($invoice['delivery_date'] != 0) ? date('jS F, Y', strtotime($invoice['delivery_date'])) : '');
        $the_text[] = '2. Warranty Period: ' . $warranty_terms_list[$invoice['waranty_terms_id']];
        $the_text[] = '3. Remarks: ' . $invoice['invoice_remarks'];
        $the_text[] = '4. No Return of Goods Accepted without Written Authorization.';
        $the_text[] = '5. All business transactions subject to our standard trading terms and conditions.';
        */
    
        $the_text = implode("\n", $the_text);

        $this->SetXY($this->headcol[0], $ypos1+20);
        $this->MultiCell(0, $this->fontSize / 2.5, utf8_decode($the_text), 0, 'L', 0); // TODO: Remove utf8_decode once pdf is utf-8 compatible
        $this->Ln();
        $ypos1 = $ypos2 = max($ypos2, $this->GetY());

        $this->layoutrow[] = $ypos1;
        $this->SetY($ypos1);

        if (9 != $invoice_type) {
            $this->textAlign = 'R';
            /*
            if ($ypos1 >= 275) {
                $this->AddPage();
                $ypos1 = $ypos = $this->GetY();
            }

            $this->SetXY($this->headcol[4], $ypos1);
            //$this->BackgroundColor = 'none';
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[7] - $this->headcol[4], 0, 'FOR ' . strtoupper($invoice_labels['company_name']) . ((20 != $invoice["site_id"]) ? ' PTY' : '') . ' LTD');
            $this->DoEndTableRow();
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Line($this->headcol[4], $this->GetY(), $this->headcol[7], $this->GetY());
            $this->textAlign = 'R';
            $this->SetX($this->headcol[4]);
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[7] - $this->headcol[4], 0, 'Authorised Signatory');
            $this->DoEndTableRow();
            */

            $this->SetFont('times', 'I');
            $this->SetX($this->headcol[3]);
            $this->border |= 1;
            $this->DoTableRow();
            $this->DoTableCell($this->headcol[6] - $this->headcol[3], 0, strtoupper($invoice_labels['company_name']) . ' Company Stamp and Signature');
            $this->DoEndTableRow();
            $this->SetFont('times', '');

            if ('' != $images['stamp'] && is_file($website_path . $images['stamp'])) {
                //$this->SetAlpha(0.5);
                $this->Image($website_path . $images['stamp'], ($this->headcol[6] + $this->headcol[4]) / 2.0 - 12.7, $ypos1 - 19.55, 0, 33.4);
                //$this->SetAlpha(1.0);
            }

            if ('' != $images['signature'] && is_file($website_path . $images["signature"])) {
                $this->Image($website_path . $images['signature'], ($this->headcol[6] + $this->headcol[4]) / 2.0 - 12.7, $ypos1 - 12.7, 0, 20.7);
            }

?>
            <img src="<?=$images['signature']?>" alt="" style="height:0.5in;" /><br />
<?php
}
    }
    public function DoTableCell($width, $height = 0, $text = '',$test=false)
    {
        $xpos = $this->GetX();
        $ypos = $this->GetY();
        if (0 == $height) {
            $height = $this->fontSize / 2.5;
        }

        $height = max($height, $this->NbLines($width - 2 * $this->padding, $text) * $this->fontSize / 2.5);
        $height += 2 * $this->padding;
        $this->table_row_content[] = [
            $xpos, $ypos, $width, $height, $text, $this->class_buffer,
            $this->textAlign, $this->fontSize, $this->padding, $this->border,
        ];
        $this->table_row_height = max($this->table_row_height, $height);
        //$this->Cell($width, $height, $text, $this->border_string(), 0, $this->textAlign, 1);
        if($test==false){
            $this->SetX($xpos + $width);
        }
    }
    public function DoInnerTableCell($width, $height = 0, $text = '',$test=false)
    {
        $xpos = $this->GetX();
        $ypos = $this->GetY()+1;
        if (0 == $height) {
            $height = $this->fontSize / 2.5;
        }

        $height = max($height, $this->NbLines($width - 2 * $this->padding, $text) * $this->fontSize / 2.5);
        $height += 2 * $this->padding;
        $this->table_row_content[] = [
            $xpos, $ypos, $width, $height, $text, $this->class_buffer,
            $this->textAlign, $this->fontSize, $this->padding, $this->border,
        ];
        $this->table_row_height = max($this->table_row_height, $height);
        //$this->Cell($width, $height, $text, $this->border_string(), 0, $this->textAlign, 1);
        $this->SetX($xpos + $width);
    }
    public function DoTableRow()
    {
        $this->table_row_height  = $this->fontSize / 2.5;
        $this->table_row_content = [];
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

            $this->MultiCell($cell[2] - 2 * $cell[8], $this->fontSize / 2.5, utf8_decode($cell[4]), 0, $cell[6]); // TODO: Remove utf8_decode once pdf is utf-8 compatible

            $this->class_pop();
        }
        $this->Ln();
        $this->SetY($ypos1 + $this->table_row_height);
        $this->table_row_content = [];
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
$pdf = new FPDF_Invoice();
$pdf->Middle();
ob_end_clean();
$invoice_buffer = $pdf->Output('invoice.pdf', 'S');
