<?php
// This is the file that will be seen as the iFrame for the new HTML messages...
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceHistoryFromDatabase

$message_id = intval($posted['message_id']);
if (!$message_id) {
    die('Invalid ID passed...');
}

$message = invoiceHistoryFromDatabase($message_id);

// now just display the message...
echo $message['message_notes'];
