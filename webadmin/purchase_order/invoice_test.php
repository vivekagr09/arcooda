<?php
include_once __DIR__ . '/common.inc.php';

$invoice_list = invoiceChunk(invoiceSearch(), 'open_week');

foreach ($invoice_list as $key => $row) {
    echo date('Y-m-d H:i:s', $key);
    echo "<br \>\n";
}
