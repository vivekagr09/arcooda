<?php
ob_start();

moduleLoad('invoice');

global $_GLOBALS;

$staff        = $_GLOBALS['staff'];
$payment_data = $DB->query_first("SELECT * FROM payment_methods WHERE site_id=" . intval($invoice['site_id']) . " AND payment_method_id=" . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';

if (!isset($staff_signature)) {
    $staff_signature = true;
}

$force_images                   = array();
$force_images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$force_images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$force_images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
$force_images['signature']      = 'sigs/' . $_GLOBALS['staff']['signature'];

if (!$staff_signature) {
    $staff['signature'] = '';
}

$boundary = md5(mt_rand(0, 65535));

ob_start();
foreach ($force_images as $key => $value) {
    if (is_file($website_path . $value)) {
        $new_filename = md5($value) . '.' . end(explode('.', $value));
//        $force_images[$key]='cid:'.$new_filename;
        $force_images[$key] = $new_filename;
        echo '--' . $boundary . "\r\n";
        echo 'Content-Type: ' . mime_content_type($website_path . $value) . "\r\n";
        echo 'Content-Transfer-Encoding: base64' . "\r\n";
//        echo 'Content-ID: <'.$new_filename.'>'."\r\n";
        echo 'Content-Location: ' . $new_filename . "\r\n";
        echo "\r\n";
        echo chunk_split(base64_encode(file_get_contents($website_path . $value)));
        echo "\r\n";
    } else {
        $force_images[$key] = '';
    }

}
$attach_buffer = ob_get_clean();
include __DIR__ . '/html_invoice.tpl.php';

echo 'From: <Highway Group Pty Ltd>' . "\r\n";
echo 'Subject: Invoice ' . formatFileSafe(invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id'])) . "\r\n";
echo 'Date: ' . date('D, d M Y H:i:s O') . "\r\n";
echo 'MIME-Version: 1.0' . "\r\n";
echo 'Content-Type: multipart/related;' . "\r\n";
echo '	type="text/html";' . "\r\n";
echo '	boundary="' . $boundary . '"' . "\r\n";
echo 'X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2900.2180' . "\r\n";
echo "\r\n";
echo 'This is a multi-part message in MIME format.' . "\r\n";
echo "\r\n";
echo '--' . $boundary . "\r\n";
echo 'Content-Type: text/html;' . "\r\n";
echo '	charset="Windows-1252"' . "\r\n";
echo 'Content-Transfer-Encoding: base64' . "\r\n";
echo "\r\n";
echo chunk_split(base64_encode($invoice_buffer));
echo $attach_buffer;

echo '--' . $boundary . '--' . "\r\n";

$invoice_buffer = ob_get_clean();
