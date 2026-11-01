<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice');

$invoice_list = invoiceChunk(invoiceSearch(), 'open_week');

foreach ($invoice_list as $key => $row) {
    echo formatDateTime($key);
    echo "<br \>\n";
}
