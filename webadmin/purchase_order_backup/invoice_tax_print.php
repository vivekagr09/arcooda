<?php
include_once __DIR__ . "/common.inc.php";

moduleLoad('invoice');
moduleLoad('tcpdf');

//Build a search criteria array
$criteria = $_GLOBALS["criteria_options"]["invoice"];
//$criteria["invoice_status"] = array("proforma", "sales", "processed", "cancelled");

if (0 != $posted['tax_year']) {
    $criteria['tax_year'] = mktime(0, 0, 0, 1, 1, date('Y', intval($posted['tax_year'])));
}

if (0 != $posted['tax_fyear']) {
    $criteria['tax_fyear'] = mktime(0, 0, 0, 7, 1, date('Y', intval($posted['tax_fyear'])) - ((date("m", intval($posted["tax_fyear"])) > 6) ? 0 : 1));
}

if (0 != $posted['tax_month']) {
    $criteria['tax_month'] = mktime(0, 0, 0, date('m', intval($posted['tax_month'])), 1, date('Y', intval($posted['tax_month'])));
}

if (0 != $posted['tax_week']) {
    $criteria['tax_week'] = mktime(0, 0, 0, date('m', intval($posted['tax_week'])), date('d', intval($posted['tax_week'])) + 1 - date('w', intval($posted['tax_week'])), date('Y', intval($posted['tax_week'])));
}

$invoice_list = array();
foreach (invoiceChunk(invoiceSearch($criteria), "tax_id") as $tax_id => $sub_list) {
    if (0 != $tax_id && count($sub_list) == 1) {
        $invoice_list = array_merge($invoice_list, $sub_list);
    }
}

$invoice_list = invoiceSort($invoice_list, "tax_id");
$count_total  = count($invoice_list);

$scale_x = 50.0 / 178.0;

$first_time = time();
$last_time  = 0;
foreach (invoiceChunk($invoice_list, "tax_time") as $k => $v) {
    $first_time = min($first_time, $k);
    $last_time  = max($last_time, $k);
}

$header1 = "Sales Register";
if (0 != $posted["tax_fyear"]) {
    $row  = explode("-", date("m-Y", intval($posted["tax_fyear"])));
    $year = $row[1];
    if ($row[0] < 6) {
        $year--;
    }

    $first_time = mktime(0, 0, 0, 7, 1, $year);
    $last_time  = mktime(0, 0, 0, 7, 1, $year + 1) - 1;
    $header1    = sprintf("Sales for financial year %d-%d", $year, $year + 1);
}
if (0 != $posted["tax_year"]) {
    $year       = date("Y", intval($posted["tax_year"]));
    $first_time = mktime(0, 0, 0, 1, 1, $year);
    $last_time  = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
    $header1    = sprintf("Sales for year %d", $year);
}
if (0 != $posted["tax_month"]) {
    $row        = explode("-", date("m-Y", intval($posted["tax_month"])));
    $first_time = mktime(0, 0, 0, $row[0], 1, $row[1]);
    $last_time  = mktime(0, 0, 0, $row[0] + 1, 1, $row[1]) - 1;
    $header1    = sprintf("Sales for %s", date("F Y", intval($posted["tax_month"])));
}

$header2 = sprintf("%s through %s", date("d/m/Y", $first_time), date("d/m/Y", $last_time));

class MYPDF extends TCPDF
{
    public function Header()
    {
        global $_GLOBALS, $scale_x, $DB, $header1, $header2;

        static $invoice_header = null;
        if (is_null($invoice_header)) {
            $row            = $DB->query_first(sprintf(
                "SELECT `company_invoiceheader` FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
                $_GLOBALS["site_settings"]["site_id"]
            ), MYSQLI_ASSOC);
            $invoice_header = explode("\n", $row["company_invoiceheader"]);
            unset($invoice_header[4]);
        }

        static $page_count = 0;
        $page_count++;

        $this->SetTextColor(0x00, 0x00, 0x80);
        $this->SetFont("helvetica", "B", 16);
        $this->Cell(0, 0, $invoice_header[0], 0, 1, "C");
        $this->SetFont("helvetica", "I", 10);
        for ($i = 1; $i < 5; $i++) {
            $this->Cell(0, 0, $invoice_header[$i], 0, 1, "C");
        }

        $this->SetTextColor(0x80, 0x00, 0x00);
        $this->SetFont("helvetica", "B", 16);
        $this->Cell(0, 0, $header1, 0, 1, "C");

        $this->SetFont("helvetica", "I", 10);
        $this->Cell(0, 0, "", 0, 1, "C");

        $this->SetFont("helvetica", "B", 16);
        $this->Cell(0, 0, $header2, 0, 1, "C");

        $this->SetTextColor(0x00, 0x00, 0x80);
        $this->SetDrawColor(0x80, 0x00, 0x00);
        $this->SetFont("helvetica", "B", 9);
        $this->Cell($scale_x * 5.0, 0, "");
        $this->Cell($scale_x * 66.0, 0, date("j/m/Y"));
        $this->Cell($scale_x * 491.0, 0, "");
        $this->Cell($scale_x * 51.0, 0, "Page " . $page_count);
        $this->Ln();
        $this->Cell($scale_x * 5.0, 0, "");
        $this->Cell($scale_x * 66.0, 0, date("g:i:s A"));
        $this->Ln();
        $this->Cell($scale_x * 5.0, 0, "", "B");
        $this->Cell($scale_x * 66.0, 0, "Date", "B");
        $this->Cell($scale_x * 66.0, 0, "Invoice #", "B");
        $this->Cell($scale_x * 86.0, 0, "Our Ref", "B");
        $this->Cell($scale_x * 311.0, 0, "Customer Name", "B");
        //$this->Cell($scale_x * 178.0, 0, "Customer Name", "B");
        $this->Cell($scale_x * 65.0, 0, "Amount USD", "B");
        $this->Cell($scale_x * 14.0, 0, "", "B");
        /*
        $this->Cell($scale_x *  76.0, 0, "Amount Due", "B");
        $this->Cell($scale_x *   6.0, 0, "", "B");
        $this->Cell($scale_x *  51.0, 0, "Status", "B");
         */
        $this->Ln();
    }
    public function Footer()
    {
    }
}
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(trim($_GLOBALS["staff"]["first_name"] . " " . $_GLOBALS["staff"]["last_name"]));
//$pdf->SetTitle('Sales Register');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetMargins(17.8, 4.5 + 7.1 + 16.9 + 7.1 + 4.2 + 7.1 + 4 + 4 + 4.2 + 2.6);
$pdf->SetHeaderMargin(4.5);
$pdf->SetFooterMargin(0.0);
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage();

$total_invoices = 0;
$total_sales    = "0.00";
foreach ($invoice_list as $invoice_id) {
    $invoice = invoiceFromDatabase($invoice_id);
    $pdf->Cell($scale_x * 2.0, 0, "");
    $pdf->Cell($scale_x * 66.0, 0, (0 != $invoice["tax_time"]) ? date("d/m/Y", $invoice["tax_time"]) : "");
    $pdf->Cell($scale_x * 66.0, 0, sprintf("%08d", $invoice["tax_id"]));
    $pdf->Cell($scale_x * 86.0, 0, end(explode("#", invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']))));
    $pdf->Cell($scale_x * 311.0, 0, ('' != $invoice['bill_company']) ? $invoice['bill_company'] : $invoice['bill_name']);
    //$pdf->Cell($scale_x * 178.0, 0, ($invoice['bill_company'] != '') ? $invoice['bill_company'] : $invoice['bill_name']);
    $pdf->Cell($scale_x * 65.0, 0, sprintf('$%0.2f', $invoice['invoice_offerus']), 0, 0, "R");
    $pdf->Cell($scale_x * 14.0, 0, "");
    /*
    $pdf->Cell($scale_x *  76.0, 0, '$0.00', 0, 0, "R");
    $pdf->Cell($scale_x *   6.0, 0, "");
    $pdf->Cell($scale_x *  51.0, 0, $invoice['invoice_action']);
     */
    $pdf->Ln();

    $total_invoices++;
    $total_sales = bcadd($total_sales, $invoice["invoice_offerus"], 2);
}

$pdf->SetTextColor(0x80, 0x00, 0x00);
$pdf->SetFont("helvetica", "I", 10);
$pdf->Cell(0, 0, "", 0, 1, "C");
$pdf->SetFont("helvetica", "B", 16);
$pdf->Cell(0, 0, "Total Invoices = " . $total_invoices, 0, 1, "C");
$pdf->SetFont("helvetica", "I", 10);
$pdf->Cell(0, 0, "", 0, 1, "C");
$pdf->SetFont("helvetica", "B", 16);
$pdf->Cell(0, 0, 'Total Sales = $' . $total_sales, 0, 1, "C");

$pdf->Output('example_003.pdf', 'I');
die();
