<?php
namespace app\models;

include_once '/home/arcooda/public_html/webadmin/vendor/tecnickcom/tcpdf/tcpdf.php';

use TCPDF;


class CustomPDF extends TCPDF
{
    public $productNames = [];

    // ---- HEADER ----
public function Header()
{
    // --- LOGO ---
    $this->Image('/home/arcooda/public_html/brand.png', 15, 8, 35, 15);

    // --- TITLE CENTERED ---
    $this->SetFont('helvetica', 'B', 14);

    // Set Y to same vertical height as image
    $this->SetY(12);  // adjust slightly to align vertically

    // A full-width cell so text stays centered
    $this->Cell(0, 10, 'Monitor Comparison Table', 0, 0, 'C');

    // HEADER BLOCK START POSITION
    $startY = 25;
    $headerHeight = 50;

    $this->SetXY(15, $startY);

    // --- First column "Product" ---
    $this->SetFont('helvetica', 'B', 9);
    $this->SetFillColor(230, 230, 230);

    $this->MultiCell(40, $headerHeight, "Product", 1, 'C', true, 0, '', '', true, 0, false, true, $headerHeight, 'M');


    // --- PRODUCT COLUMNS ---
    $usableWidth = 297 - 15 - 15; // A4 landscape minus margins
    $prodCount = count($this->productNames);
    $prodWidth = ($usableWidth - 40) / $prodCount;

    foreach ($this->productNames as $prod) {

        $x = $this->GetX();
        $y = $this->GetY();

        // Draw outer cell
        $this->Rect($x, $y, $prodWidth, $headerHeight);

        // --- IMAGE (centered) ---
        $imgW = 20;
        $imgH = 20;

        $imgX = $x + ($prodWidth - $imgW) / 2;
        $imgY = $y + 3;

        $this->Image($prod[1], $imgX, $imgY, $imgW, $imgH);

        // --- PRODUCT NAME (reduced space) ---
        // Image height = 20 → name should start around 27px below top
        $this->SetXY($x, $y + 27);
        $this->MultiCell($prodWidth, 33, $prod[0], 0, 'C', false, 0);

        // Move to next product column
        $this->SetXY($x + $prodWidth, $y);
    }

    // move cursor AFTER header
    $this->SetY($startY + $headerHeight);
}





    // ---- FOOTER ----
    public function Footer()
    {
        // Move 20 mm from bottom
        $this->SetY(-20);

        // Page Number (Bottom Right)
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'R');

        // Website (center)
        $this->SetY(-20);
        $pageWidth = $this->getPageWidth();

        // Calculate X position to center the cell
        // Cell width = 50 (same as your code)
        $x = ($pageWidth - 50) / 2;

        $this->SetX($x);
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(50, 10, 'www.arcooda.com', 0, 0, 'C');
        $this->SetFont('helvetica', '', 9);
        $this->SetX($x-100);
        $this->Cell(100, 10, date('jS F Y'), 0, 0, 'C');
        // QR Code (bottom-left)
        $style = ['border' => 0];
        $this->write2DBarcode('https://www.arcooda.com', 'QRCODE,H', 15, $this->GetY() - 4, 12, 12, $style);
    }

}
