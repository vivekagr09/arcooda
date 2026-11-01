<?php
moduleLoad('invoice');
moduleLoad('tcpdf');

$handler_list = invoiceGetOptionList('handler_id');

$subject_list = array(
    'enquiry'   => 'Quotation',
    'inquiry'   => 'Quotation',
    'closed'    => 'Closed Invoice',
    'proforma'  => 'Proforma Invoice',
    'sales'     => 'Sales Invoice',
    'processed' => 'Sales Invoice',
    'cancelled' => 'Cancelled Invoice',
    'purchase'  => 'Purchase Summary',
);

$company_info = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);

class TCPDF_Invoice extends TCPDF
{
    public $home_url      = null;
    public $logo_alt      = null;
    public $logo_invoice  = null;
    public $logo_address1 = null;
    public $logo_address2 = null;

    public function __construct()
    {
        call_user_func_array([parent, '__construct'], func_get_args());
        $this->home_url      = 'https://' . $_SERVER['SERVER_NAME'] . '/';
        $this->logo_alt      = 'Arcooda';
        $this->logo_invoice  = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/sites/site_39/logo_invoice.png';
        $this->logo_address1 = '44 Maitland Rd (Downstairs), Mayfield East, NSW 2304, Australia';
        $this->logo_address2 = 'Phone: +61 02 4968 9313   Fax: +61 02 4968 9314   Email: sales@arcooda.com';
    }

    public function top_section()
    {
        $this->startTemplate();

        $y = 0;
        if (is_file($this->logo_invoice)) {
            $dim   = getimagesize($this->logo_invoice);
            $scale = 96.0 / $dim[1];
            if ($dim[0] * $scale > $this->pageWidth) {
                $scale = (0.0 + $this->pageWidth) / $dim[0];
            }

            if (!$get_height) {
                $this->image($this->logo_invoice, ($this->pageWidth - $dim[0] * $scale) / 2, 0, $dim[0] * $scale, $dim[1] * $scale, '', $home_url, 'N');
            }
            $y += $dim[1] * $scale;
        } else {
            if (!$get_height) {
                $this->cell(0, 0, $this->logo_alt, 0, 1, 'C', false, $this->home_url);
            }
            $y += $this->lineHeight;
        }
        if (!$get_height) {
            $this->cell(0, 0, $this->logo_address1, 0, 1, 'C');
            $this->cell(0, 0, $this->logo_address1, 0, 1, 'C');
        }
        $y += 2 * $this->lineHeight;

        return $this->endTemplate();

?>
    <g transform="matrix(1 0 0 1 0 0)">
        <image overflow="visible" x="86.8062" y="0" width="378" height="94" xlink:href="#logo_header"></image>
        <text x="161.1304" y="78.9048" font-size="8">44 Maitland Rd (Downstairs), Mayfield East, NSW 2304, Australia</text>
        <text x="126.1964" y="88.9048" font-size="8">Phone: +61 02 4968 9313   Fax: +61 02 4968 9314   Email: sales@arcooda.com</text>
    </g>
    <g transform="matrix(1 0 0 1 401.02 94.004)">
        <rect x="0" y="0" fill="#F9EC31" width="147.283" height="17.209"/>
        <text x="7.313" y="14.0536" fill="#231F20" font-size="13">PROFORMA INVOICE</text>
    </g>
    <g transform="matrix(1 0 0 1 0 116.503)">
        <g transform="matrix(1 0 0 1 0 0)">
            <rect x="7.696" y="2" fill="#231F20" stroke="#231F20" stroke-width="0.25" width="259.334" height="73.333"/>
            <rect x="0.839" y="0" fill="#FFFFFF" stroke="#231F20" stroke-width="0.25" width="264.191" height="73.333"/>
            <text transform="matrix(0.9753 0 0 1 4.3921 9.3354)" fill="#231F20" font-size="8">Invoice to</text>
            <text transform="matrix(0.9753 0 0 1 61.6963 15.5004)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">Company name</tspan>
                <tspan x="0" y="14" fill="#231F20" font-size="8">address line 1</tspan>
                <tspan x="0" y="28" fill="#231F20" font-size="8">address line 2</tspan>
            </text>
            <text transform="matrix(0.9753 0 0 1 62.6963 55.0004)" font-size="8">Name</text>
            <text transform="matrix(0.9753 0 0 1 63.1963 68.0004)" font-size="8">000-000-000</text>
        </g>
        <g transform="matrix(1 0 0 1 270.408 0)">
            <rect x="8.104" y="2" fill="#231F20" stroke="#231F20" stroke-width="0.25" width="270.308" height="73.333"/>
            <rect x="6.03" y="0" fill="#FFFFFF" stroke="#231F20" stroke-width="0.25" width="270.308" height="73.333"/>
            <text transform="matrix(1 0 0 1 14.1662 9.8354)" fill="#231F20" font-size="8">Deliver to</text>
            <text transform="matrix(0.9753 0 0 1 73.2883 15.0004)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">Company name</tspan>
                <tspan x="0" y="14" fill="#231F20" font-size="8">address line 1</tspan>
                <tspan x="0" y="28" fill="#231F20" font-size="8">address line 2</tspan>
            </text>
            <text transform="matrix(0.9753 0 0 1 73.7883 56.5004)" font-size="8">Name</text>
            <text transform="matrix(1 0 0 1 73.219 69.0004)" font-size="9">xxxxxx@xxxxx.com</text>
        </g>
    </g>

    <g>
        <rect x="0.363" y="202.467" opacity="0.7" fill="#0000FF" width="548.666" height="11.962"/>

        <rect x="0.363" y="213.983" fill="none" stroke="#231F20" stroke-width="0.25" width="548.667" height="19.754"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="151.695" y1="202.396" x2="151.695" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="207.695" y1="202.396" x2="207.695" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="293.696" y1="202.396" x2="293.696" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="347.528" y1="202.396" x2="347.528" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="411.03" y1="202.396" x2="411.03" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="475.03" y1="202.396" x2="475.03" y2="234.101"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="75.029" y1="202.396" x2="75.029" y2="234.101"/>
        <g>
            <text transform="matrix(1 0 0 1 13.7036 210.5083)" fill="#FFFFFF" font-size="7">SALES PERSON</text>
            <text transform="matrix(1 0 0 1 108.8599 211.0005)" fill="#FFFFFF" font-size="7">NO.</text>
            <text transform="matrix(1 0 0 1 170.0889 211.5093)" fill="#FFFFFF" font-size="7">DATE</text>
            <text transform="matrix(1 0 0 1 244.7739 211.3521)" fill="#FFFFFF" font-size="7">TERMS</text>
            <text transform="matrix(1 0 0 1 303.6035 211.1484)" fill="#FFFFFF" font-size="7">SHIP VIA</text>
            <text transform="matrix(1 0 0 1 357.4941 211.1484)" fill="#FFFFFF" font-size="7">PAYMENT VIA</text>
            <text transform="matrix(1 0 0 1 419.5078 211.1484)" fill="#FFFFFF" font-size="7">VENDOR REF. #</text>
            <text transform="matrix(1 0 0 1 499.708 210.3154)" fill="#FFFFFF" font-size="7">CURRENCY</text>
        </g>
        <g>
            <text transform="matrix(1 0 0 1 14.2686 227.48)" fill="#231F20" font-size="9">Joe Blogs</text>
            <text transform="matrix(1.204 0 0 1 82.9102 227.4224)" fill="#231F20" font-size="9">ECO#00019</text>
            <text transform="matrix(1 0 0 1 156.6816 226.98)" fill="#231F20" font-size="9">26-Aug-08</text>
            <text transform="matrix(1 0 0 1 225.6094 227.9805)" fill="#231F20" font-size="9">EX WORK HK</text>
            <text transform="matrix(1 0 0 1 307.9473 228.4971)" fill="#231F20" font-size="10">EMS</text>
            <text transform="matrix(1 0 0 1 374.9482 227.646)" fill="#231F20" font-size="9">T/T</text>
            <text transform="matrix(1 0 0 1 423.5029 227.6655)" fill="#231F20" font-size="9">1180269</text>
            <text transform="matrix(1 0 0 1 508.5586 226.832)" fill="#231F20" font-size="9">USD</text>
        </g>
    </g>
<?php
    }
    public function bottom_section()
    {
?>
    <g>
        <line fill="#231F20" stroke="#808080" stroke-width="0.25" x1="0" y1="609.809" x2="548.062" y2="609.809"/>
        <g>
            <text transform="matrix(1 0 0 1 3.7285 621.145)" fill="#231F20" font-size="8">Notes:</text>
            <circle fill="#231F20" cx="5.696" cy="629.636" r="1.875"/>
            <text transform="matrix(1 0 0 1 11.8765 631.7075)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">Payment to be settled by XX% deposit to confirm the order via bank T/T, XX% ballance before shipment </tspan>
                <tspan x="0" y="8" fill="#231F20" font-size="8">released. Any bank charges outside Hong Kong are borne by sender.</tspan>
            </text>
            <circle fill="#231F20" cx="5.696" cy="650.292" r="1.875"/>
            <text transform="matrix(1 0 0 1 10.7686 652.3901)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">All invoices are warrented for a period of XX days unless otherwise noted, warranty only covers spare</tspan>
                <tspan x="0" y="8" fill="#231F20" font-size="8">part replacement. does not include any shipping or labor cost.</tspan>
            </text>
            <circle fill="#231F20" cx="5.696" cy="669.968" r="1.875"/>
            <text transform="matrix(1 0 0 1 10.877 672.354)" fill="#231F20" font-size="8">Delivery date to be XX-XX days upon receipt of customer deposit payment.</text>
            <circle fill="#231F20" cx="5.696" cy="681.255" r="1.875"/>
            <text transform="matrix(1 0 0 1 12.4473 684.5679)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">Goods sold do not cover shipping insurance, shipping cost stated is just an estemated quote by forwarder,</tspan>
                <tspan x="0" y="8" fill="#231F20" font-size="8">real shipping charges will be adjusted and updated with balance payment invoice.</tspan>
            </text>
            <circle fill="#231F20" cx="5.696" cy="701.968" r="1.875"/>
            <text transform="matrix(1 0 0 1 11.9849 705.1382)" fill="#231F20" font-size="8">No return of goods without written authorization.</text>
            <circle fill="#231F20" cx="5.696" cy="713.968" r="1.875"/>
            <text transform="matrix(1 0 0 1 10.9824 715.9673)" fill="#231F20" font-size="8">All business transactions subject to our company standard trading terms and conditions.</text>
        </g>
        <g>
            <line fill="#231F20" stroke="#808080" stroke-width="0.25" x1="365.366" y1="650.309" x2="548.076" y2="650.309"/>
            <line fill="#231F20" stroke="#231F20" stroke-width="0.25" x1="365.681" y1="687.81" x2="548.366" y2="687.81"/>
            <g>
                <text transform="matrix(1 0 0 1 425.7295 621.644)" fill="#231F20" font-size="10">Sub Total</text>
                <text transform="matrix(1 0 0 1 493.3955 621.978)" fill="#231F20" font-size="9">13320.63</text>
            </g>
            <g>
                <text transform="matrix(1 0 0 1 399.9023 633.3032)" font-size="10">Discount (XX%)</text>
                <text transform="matrix(1 0 0 1 491.8652 633.3022)" fill="#231F20" font-size="9">(1332.06)</text>
            </g>
            <g>
                <text transform="matrix(1 0 0 1 370.7275 644.9751)" fill="#231F20" font-size="10">Shipping and Handling</text>
                <text transform="matrix(1 0 0 1 496.3662 645.6353)" fill="#231F20" font-size="9">2332.96</text>
            </g>
            <g>
                <text transform="matrix(1 0 0 1 445.0615 661.644)" fill="#231F20" font-size="10">Total</text>
                <text transform="matrix(1 0 0 1 493.7295 661.9771)" fill="#231F20" font-size="9">13320.63</text>
            </g>
            <g>
                <text transform="matrix(1 0 0 1 411.7295 680.6567)" fill="#231F20" font-size="10">Amount Paid</text>
                <text transform="matrix(1 0 0 1 494.0322 680.7651)" fill="#231F20" font-size="9">(2332.96)</text>
            </g>
            <g>
                <rect x="365.847" y="687.723" fill="#AEDEE4" width="183.459" height="33.998"/>
                <text transform="matrix(1 0 0 1 423.3623 711.2769)" fill="#231F20" font-size="10">Total Due</text>
                <text transform="matrix(1 0 0 1 484.7295 711.1187)" fill="#231F20" font-size="12">13187.57</text>
            </g>
        </g>
    </g>
    <g>
        <g>
            <text transform="matrix(1 0 0 1 5.7305 739.814)">
                <tspan x="0" y="0" fill="#231F20" font-size="8">Bank Details:</tspan>
                <tspan x="0" y="9.6" fill="#231F20" font-size="8">Account Name: </tspan>
                <tspan x="52.426" y="9.6" fill="#231F20" font-size="8" letter-spacing="17"></tspan>
                <tspan x="72" y="9.6" fill="#231F20" font-size="8">coinopexpress.com LTD.</tspan>
                <tspan x="0" y="19.2" fill="#231F20" font-size="8">Account No: </tspan>
                <tspan x="53.102" y="19.2" fill="#231F20" font-size="8" letter-spacing="16"></tspan>
                <tspan x="72" y="19.2" fill="#231F20" font-size="8">400-070397-274</tspan>
                <tspan x="0" y="28.8" fill="#231F20" font-size="8">Bank Name: </tspan>
                <tspan x="54.211" y="28.8" fill="#231F20" font-size="8" letter-spacing="15"></tspan>
                <tspan x="72" y="28.8" fill="#231F20" font-size="8">The Hongkong and Shanghai </tspan>
                <tspan x="0" y="38.4" fill="#231F20" font-size="8" letter-spacing="34"></tspan>
                <tspan x="72" y="38.4" fill="#231F20" font-size="8">Banking Corporation</tspan>
                <tspan x="0" y="48" fill="#231F20" font-size="8">Address: </tspan>
                <tspan x="54.441" y="48" fill="#231F20" font-size="8" letter-spacing="15"></tspan>
                <tspan x="72" y="48" fill="#231F20" font-size="8">1 Queen&apos;s Road, Central, </tspan>
                <tspan x="0" y="57.6" fill="#231F20" font-size="8" letter-spacing="34"></tspan>
                <tspan x="72" y="57.6" fill="#231F20" font-size="8">Hong Kong</tspan>
                <tspan x="0" y="67.2" fill="#231F20" font-size="8">Swift Code: </tspan>
                <tspan x="54.445" y="67.2" fill="#231F20" font-size="8" letter-spacing="15"></tspan>
                <tspan x="72" y="67.2" fill="#231F20" font-size="8">HSBC HKHH HKH</tspan>
            </text>
        </g>
        <g>
            <line fill="none" stroke="#231F20" stroke-width="0.25" stroke-dasharray="1" x1="322.363" y1="779.93" x2="549.029" y2="779.93"/>
            <text transform="matrix(1 0 0 1 324.0303 792.2632)" fill="#231F20" font-size="11">COINOP EXPRESS Company Stamp and Signature</text>
            <image overflow="visible" width="219" height="106" xlink:href="stamp" transform="matrix(0.5624 0 0 0.5624 409.7314 740.6646)"></image>
            <image overflow="visible" width="160" height="115" xlink:href="signature" transform="matrix(0.5535 0 0 0.5535 381.7119 740.9458)"></image>
        </g>
    </g>
    <text transform="matrix(1 0 0 1 182.9897 827.3218)" fill="#AEDEE4" font-size="11">Thankyou for your Business!</text>
<?php
    }
    public function body_section()
    {
?>
<svg>
    <text transform="matrix(1 0 0 1 33.5088 172.6733)">
        <tspan x="0" y="0" fill="#231F20" font-size="8">Attn: </tspan>
        <tspan x="-18.223" y="13" fill="#231F20" font-size="8">Tel/Email: </tspan>
    </text>

<!-- top -->
<!-- /top -->
    <g>
        <rect x="0.363" y="241.825" opacity="0.7" fill="#0000FF" width="548.666" height="11.143"/>
        <rect x="0.363" y="253.358" fill="none" stroke="#231F20" stroke-width="0.25" width="548.667" height="468.396"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="49.384" y1="241.658" x2="49.384" y2="610.051"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="365.563" y1="241.658" x2="365.563" y2="720.753"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="475.396" y1="241.658" x2="475.396" y2="720.753"/>
        <line fill="none" stroke="#231F20" stroke-width="0.25" x1="409.062" y1="241.975" x2="409.062" y2="610.099"/>
        <g>
            <text transform="matrix(1 0 0 1 6.2817 249.8462)" fill="#FFFFFF" font-size="7">PRODUCT ID</text>
            <text transform="matrix(1 0 0 1 173.4053 249.7505)" fill="#FFFFFF" font-size="7">DESCRIPTION OF GOODS</text>
            <text transform="matrix(1 0 0 1 372.4971 249.7505)" fill="#FFFFFF" font-size="7">QUANTITY</text>
            <text transform="matrix(1 0 0 1 426.5391 249.7505)" fill="#FFFFFF" font-size="7">UNIT PRICE</text>
            <text transform="matrix(1 0 0 1 499.7783 249.0688)" fill="#FFFFFF" font-size="7">AMOUNT</text>
        </g>
        <g>
            <text transform="matrix(1 0 0 1 15.729 264.9678)" fill="#231F20" font-size="9">6874</text>
            <text transform="matrix(1 0 0 1 61.3364 263.9683)" fill="#231F20" font-size="9">I-Bowl (Ten Pin Bowling Machine)</text>
            <text transform="matrix(1 0 0 1 384.7295 264.9673)" fill="#231F20" font-size="9">1</text>
            <text transform="matrix(1 0 0 1 424.3975 264.4673)" fill="#231F20" font-size="9">9029.93</text>
            <text transform="matrix(1 0 0 1 495.3975 263.9673)" fill="#231F20" font-size="9">9029.93</text>
            <text transform="matrix(1 0 0 1 59.0039 272.9663)" fill="#231F20" font-size="8">Please note the price is for single Lane only</text>
            <line fill="none" stroke="#808080" stroke-width="0.5" x1="1.062" y1="276.967" x2="547.062" y2="276.967"/>
        </g>
        <g>
            <text transform="matrix(1 0 0 1 20.729 287.9678)" fill="#231F20" font-size="9">818</text>
            <text transform="matrix(1 0 0 1 61.3364 286.9683)" fill="#231F20" font-size="9">Sanwa Joystick</text>
            <text transform="matrix(1 0 0 1 384.7295 287.9673)" fill="#231F20" font-size="9">1</text>
            <text transform="matrix(1 0 0 1 429.3975 286.9673)" fill="#231F20" font-size="9">TBA</text>
            <text transform="matrix(1 0 0 1 501.5635 287.6343)" fill="#231F20" font-size="9">TBA</text>
            <text transform="matrix(1 0 0 1 59.0039 295.9683)" fill="#231F20" font-size="8">Please advice your needed quantity to our salesman for best price to you.</text>
            <line fill="none" stroke="#808080" stroke-width="0.5" x1="1.062" y1="299.967" x2="547.062" y2="299.967"/>
        </g>
        <g>
            <text transform="matrix(1 0 0 1 14.729 310.9678)" fill="#231F20" font-size="9">7266</text>
            <text transform="matrix(1 0 0 1 61.3364 311.3003)" fill="#231F20" font-size="9">Sticker with Attitude photo machine</text>
            <text transform="matrix(1 0 0 1 384.7295 310.6343)" fill="#231F20" font-size="9">1</text>
            <text transform="matrix(1 0 0 1 424.3975 311.6353)" fill="#231F20" font-size="9">2094.89</text>
            <text transform="matrix(1 0 0 1 493.5654 311.6353)" fill="#231F20" font-size="9">2094.89</text>
            <line fill="none" stroke="#808080" stroke-width="0.5" x1="1.062" y1="316.299" x2="547.062" y2="316.299"/>
        </g>
        <g>
            <text transform="matrix(1 0 0 1 14.729 326.3008)" fill="#231F20" font-size="9">6874</text>
            <text transform="matrix(1 0 0 1 61.3364 326.3022)" fill="#231F20" font-size="9">I-Bowl (Ten Pin Bowling Machine)</text>
            <text transform="matrix(1 0 0 1 59.0039 334.6343)" fill="#231F20" font-size="8">Please note the price is for single Lane only</text>
            <text transform="matrix(1 0 0 1 384.7295 326.3003)" fill="#231F20" font-size="9">1</text>
            <text transform="matrix(1 0 0 1 425.3975 326.3003)" fill="#231F20" font-size="9">9029.93</text>
            <text transform="matrix(1 0 0 1 493.3975 326.3003)" fill="#231F20" font-size="9">9029.93</text>
            <line fill="none" stroke="#808080" stroke-width="0.5" x1="1.062" y1="339.633" x2="547.062" y2="339.633"/>
        </g>
    </g>

<!-- bottom -->
<!-- /bottom -->

</svg>
<?php
    }
}

ob_start();

// create new PDF document
$pdf = new TCPDF_Invoice('P', 'mm', 'A4');

// set document information
$pdf->SetCreator($company_info['Company Name']);
$pdf->SetAuthor($handler_list[$invoice['handler_id']]);
$pdf->SetTitle($invoice['invoice_number']);
$pdf->SetSubject($subject_list[$invoice['invoice_status']]);

$keywords = array();
foreach ($invoice_products as $k => $v) {
    $keywords[] = $v['product_name'];
}

foreach ($invoice_charges as $k => $v) {
    $keywords[] = $v['charge_name'];
}

$pdf->SetKeywords(implode(', ', array_unique($keywords)));

// set default header data
$pdf->setHeaderData(false, 0, $subject_list[$invoice['invoice_status']], $invoice['invoice_number'], array(0x7f, 0x7f, 0x7f), array(0x7f, 0x7f, 0x7f));
$pdf->setFooterData(array(0x7f, 0x7f, 0x7f), array(0x7f, 0x7f, 0x7f));

// set header and footer fonts
$pdf->setHeaderFont(array('helvetica', '', 8));
$pdf->setFooterFont(array('helvetica', '', 8));

// set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// set margins
$pdf->SetMargins(12.7, 12.7, 12.7);
$pdf->SetHeaderMargin(12.7);
$pdf->SetFooterMargin(12.7);

// set auto page breaks
$pdf->SetAutoPageBreak(false);

// set image scale factor
//$pdf->setImageScale(72.0/25.4);

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->setFont('dejavusans', '', 10, '', true);
$pdf->setCellPadding(1);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->addPage();
$y  = $pdf->getY();
$x1 = 0;
$x2 = $pdf->getPageWidth() - 88.9;

$pdf->setXY($x1, $y);
$pdf->image(
    $_SERVER['DOCUMENT_ROOT'] . '/webadmin/images/sites/site_3/logo_invoice.png',
    '', '', 101.6, 25.4, '', '', 'B', true, 300, 'L', false, false, 0, 'LT'
);
$y1 = $pdf->getY();

$pdf->setXY($x2, $y);
$pdf->cell(38.1, 0, strtoupper($subject_list[$invoice['invoice_status']]), 1, 0, 'C', false);
$pdf->cell(0, 0, 'ICO#040218B', 1, 2, 'C', false);
$pdf->setX($x2);
$pdf->cell(38.1, 0, strtoupper('DATE'), 1, 0, 'C', false);
$pdf->cell(0, 0, date(_('Y-m-d'), $invoice['proforma_time']), 1, 2, 'C', false);
$y2 = $pdf->getY();

$pdf->setXY(0, max($y1, $y2));

ob_end_clean();
$invoice_buffer = $pdf->Output('invoice.pdf', 'S');
return;

$staff = $_GLOBALS['staff'];

$payment_data = $DB->query_first(sprintf(
    "SELECT * FROM `payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
    $invoice['site_id'],
    formatSQLString($invoice['paymenttype_id'])
), MYSQLI_ASSOC);
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';

$force_images                   = array();
$force_images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$force_images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$force_images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$force_images['signature']      = 'images/sigs/' . $staff['signature'];

$boundary = md5(mt_rand(0, 65535));

foreach ($force_images as $key => $value) {
    if (!is_file($_SERVER['DOCUMENT_ROOT'] . $value)) {
        $force_images[$key] = '';
    }
}

//-----------------------------------------------------------------------------
//This part should be the same as jpeg_invoice2.tpl.php

$charges_list        = invoiceGetOptionList('charge_id');
$terms_list          = invoiceGetOptionList('terms_id');
$warranty_terms_list = invoiceGetOptionList('warranty_terms_id');
$staff               = $_GLOBALS['staff'];
$payment_data        = $DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$invoice_labels      = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
$website_info        = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

$website_url  = 'https://www.coinopexpress.com/';
$website_path = $_SERVER['DOCUMENT_ROOT'];
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
if (in_array($invoice['invoice_status'], array('enquiry', 'inquiry', 'closed'))) {
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

$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice_id, (3 == $invoice_type || 4 == $invoice_type) ? $invoice['revision_id'] : null, $invoice['site_id']))));
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

class TCPDF_Invoice2 extends TCPDF
{
    public $textAlign, $fontSize, $padding, $border, $headcol;
    public $bodycol, $contactcol, $fontWeight, $fontStyle, $fontString;
    public $verticalAlign;
    public $class_buffer = array();

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

    }

    public function WasHeader()
    {
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
            if ('' != $terms_list[$invoice['terms_id']]) {
                $temp['SALES TERMS'] = $terms_list[$invoice['terms_id']];
            }

            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TERMS'] = $payment_data['payment_method_name'];
            }

            if ('' != $invoice['proforma_tracknumber']) {
                $temp['TRACKING #'] = $invoice['proforma_tracknumber'];
            }

        } else {
            if ('' != $invoice['ship_supplier_name']) {
                $temp['VIA.'] = $invoice['ship_supplier_name'];
            }

            if ('' != $payment_data['payment_method_name']) {
                $temp['PAYMENT TERMS'] = $payment_data['payment_method_name'];
            }

            if ('' != $invoice['proforma_purchaseorder']) {
                $temp['CLIENT P.O.#'] = $invoice['proforma_purchaseorder'];
            }

            //    if($terms_list[$invoice['terms_id']]!='')        $temp['SHIPPING TERMS']=$terms_list[$invoice['terms_id']];
            //    if($invoice['ship_account_num']!='')            $temp['SHIP ACCOUNT']=$invoice['ship_account_num'];
            if ('' != $invoice['proforma_tracknumber']) {
                $temp['TRACKING #'] = $invoice['proforma_tracknumber'];
            }

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
        $this->SetFont('times', '', $this->font_size = 8);
        $this->SetMargins(12.7, 12.7);
        $this->SetAutoPageBreak(true);
        $this->AddPage();

        $this->WasHeader();

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

        $sub_total = $invoice['invoice_offer'];
        $gst_offer = 0;

        if (is_array($invoice_charges) && 9 != $invoice_type) {
            foreach ($invoice_charges as $charge) {
                //Do not display GST here
                if (1 != $charge['charge_id']) {
                    $this->class_push('box_product');
                    if ('' != $invoice['ship_product_notes']) {
                        $this->border = ($this->border & ~2);
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

            //$temp['Shipping Charges']=($invoice['ship_offer']>0)?convertCurrency($invoice['ship_offer'],$invoice['invoice_currency']):'TBA';
            $temp['Invoice Amount'] = ($invoice['invoice_offer'] > 0) ? convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'] : 'TBA';
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

        $the_text   = array();
        $the_text[] = 'SALESMAN: ' . $all_staff_list[$invoice['handler_id']];
        $the_text[] = '';

        $n = 0;

        foreach ($proforma_comments as $k => $v) {
            switch ($v) {
                case "terms":
                    $n++;
                    $the_text[] = $n . '. Terms: ' . $invoice['proforma_terms'];
                    break;

                case "shipment":
                    $the_text[] = $n . '. Shipment: ' . $invoice['proforma_shipvia'];
                    break;

                case "delivery_date":
                    $n++;
                    $the_text[] = $n . '. Delivery Date: ' . ((0 != $invoice['delivery_date']) ? date('jS F, Y', strtotime($invoice['delivery_date'])) : '');
                    break;

                case "warranty":
                    $n++;
                    $the_text[] = $n . '. Warranty Period: ' . $warranty_terms_list[$invoice['waranty_terms_id']];
                    break;

                case "return_goods":
                    $n++;
                    $the_text[] = $n . '. Remarks: No Return of Goods Accepted without Written Authorization.';
                    break;

                case "terms_conditions":
                    $n++;
                    $the_text[] = $n . '. Remarks: All business transactions subject to our standard trading terms and conditions.';
                    break;

                case "remarks":
                    $n++;
                    $the_text[] = $n . '. Remarks: ' . $invoice['invoice_remarks'];
                    break;
            }
        }

        /*$the_text[]='1. Delivery Date: '.(($invoice['delivery_date']!=0)?date('jS F, Y',strtotime($invoice['delivery_date'])):'');
        $the_text[]='2. Warranty Period: '.$warranty_terms_list[$invoice['waranty_terms_id']];
        $the_text[]='3. Remarks: '.$invoice['invoice_remarks'];
        $the_text[]='4. No Return of Goods Accepted without Written Authorization.';
        $the_text[]='5. All business transactions subject to our standard trading terms and conditions.';*/
        if ('' != $payment_data['payment_method_details']) {
            $the_text[] = ($n + 1) . '. Bank Details:';
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
$pdf = new TCPDF_Invoice('P', 'mm', 'A4');
$pdf->Middle();
ob_end_clean();
$invoice_buffer = $pdf->Output('invoice.pdf', 'S');
