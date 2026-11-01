<?php
include_once __DIR__ . '/common.inc.php';
global $DB;

include_once 'PEAR.php';
include_once 'Spreadsheet/Excel/Writer.php';

$fname    = tempnam("/tmp", "invoice_list.xls");
$workbook = new Spreadsheet_Excel_Writer($fname);

/* ---------- FORMATS ---------- */

// header
$fmt_header = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'Weight'     => 1,
    'HAlign'     => 'center',
    'VAlign'     => 'vcenter',
    'Border'     => 1,
]);

// center aligned (default for all except product name)
$fmt_center = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'HAlign'     => 'center',
    'VAlign'     => 'vcenter',
    'Border'     => 1,
]);

// product name (left aligned + wrapped)
$fmt_product = $workbook->addFormat([
    'FontFamily' => 'Calibri',
    'Size'       => 11,
    'HAlign'     => 'left',
    'VAlign'     => 'vcenter',
    'TextWrap'   => 1,
    'Border'     => 1,
]);

$worksheet = $workbook->addWorksheet($label);

/* ---------- COLUMN WIDTHS ---------- */
$worksheet->setColumn(0, 0, 12); // Product Id
$worksheet->setColumn(1, 1, 55); // Product Name (important)
$worksheet->setColumn(2, 5, 14); // Other numeric columns

/* ---------- HEADER ---------- */
$row_id = 0;
$worksheet->writeRow($row_id++, 0, [
    'Product Id',
    'Product Name',
    'Stock',
    'Inquiry',
    'Invoice',
    'Total Units',
], $fmt_header);

/* ---------- DATA ---------- */
$query = $DB->query("
    SELECT 
        p.id,
        p.product_name,
        p.qty,
        p.stock_type,
        SUM(CASE WHEN inv.invoice_status = 'inquiry' THEN ip.product_qty ELSE 0 END) AS inquiry_total,
        SUM(CASE WHEN inv.invoice_status IN ('proforma', 'sales', 'return') THEN ip.product_qty ELSE 0 END) AS invoice_total,
        SUM(ip.product_qty) AS grand_total
    FROM arcooda_www.products AS p
    INNER JOIN highwaygroup.invoice_products AS ip ON ip.product_id = p.id
    INNER JOIN highwaygroup.invoices AS inv ON inv.invoice_id = ip.invoice_id
    WHERE inv.invoice_status IN ('inquiry', 'proforma', 'sales', 'return')
    AND inv.site_id = 39
    GROUP BY p.id, p.product_name, p.qty, p.stock_type
");

while ($order = $DB->fetch_assoc($query)) {

    $worksheet->write($row_id, 0, $order['id'], $fmt_center);
    $worksheet->write($row_id, 1, $order['product_name'], $fmt_product);
    $worksheet->write($row_id, 2,
        ($order['stock_type'] != 'consignment') ? $order['qty'] : '',
        $fmt_center
    );
    $worksheet->write($row_id, 3, $order['inquiry_total'], $fmt_center);
    $worksheet->write($row_id, 4, $order['invoice_total'], $fmt_center);
    $worksheet->write($row_id, 5, $order['grand_total'], $fmt_center);

    $row_id++;
}

$DB->free_result($query);

/* ---------- OUTPUT ---------- */
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"product_ordered.xls\"");

$workbook->close();
header("Content-Length: " . filesize($fname));
readfile($fname);
unlink($fname);
?>
