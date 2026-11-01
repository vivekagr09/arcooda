<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

$email = "No Email";

if (isset($_GET['customer_email']) === true && empty($_GET['customer_email']) === false) {
    $email = $_GET['customer_email'];
}

$customer_details = array();
$query            = $DB->query("SELECT * FROM `highwaygroup`.`new_members` WHERE `email_address` = $DB->escape_string('" . $email . "')");
while ($row = $DB->fetch_assoc($query)) {
    $customer_details = $row;
}

// if customer exists
if (count($customer_details) > 0) {
    echo json_encode($customer_details);
} else {
    echo '"nah"';
}
