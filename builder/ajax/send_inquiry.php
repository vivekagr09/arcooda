<?php
/**
 *     Project: Product Builder
 *
 *    send_inquiry.php
 *
 *    This is called from ajax to send the inquiry.
 *
 *    @author Kelvin (khtat@highwaygroup.net)
 *    @since  09 JAN 2018
 */
use app\models\PHPMailerEmail;

include __DIR__ . '/../inc/common.inc.php';

moduleLoad('invoice'); //invoiceChargesToDatabase, invoiceCourierQuotesToDatabase, invoiceProductsToDatabase, invoiceEmailTemplatesList, invoiceToDatabase, invoiceTemplateVars
moduleLoad('member'); //memberMailingListSubscribe, memberIdFromEmail, memberToDatabase, memberFromDatabase
moduleLoad('notify'); //notifyActionMade
moduleLoad('template');

$required = array("fname", "lname", "email", "country", "mail_list", "send_copy", "comments", "machine_name", "machine_id", "items", "price", "currency", "sub_id");

function validate($post)
{
    global $required;
    foreach ($required as $k => $v) {
        if (isset($_POST[$v]) === false) {
            return false;
        }
    }

    return true;
}

if (validate($required)) {
    $fname        = $_POST['fname'];
    $lname        = $_POST['lname'];
    $email        = $_POST['email'];
    $country      = $_POST['country'];
    $mail_list    = $_POST['mail_list'];
    $send_copy    = $_POST['send_copy'];
    $comments     = $_POST['comments'];
    $machine_name = $_POST['machine_name'];
    $machine_id   = intval($_POST['machine_id']);
    $items        = $_POST['items'];
    $price        = floatval($_POST['price']);
    $currency     = $_POST['currency'];
    $sub_id       = intval($_POST['sub_id']);

    $posted['inquiry'] = array(
        "name"       => $fname . " " . $lname,
        "email"      => $email,
        "country"    => $country,
        "product_id" => $machine_id,
        "price"      => $price,
        "currency"   => $currency,
        "subject"    => $machine_name,
        "sub_id"     => $sub_id,
    );

    $posted['inquiry']['message'] = "";

    foreach ($items as $item) {
        $posted['inquiry']['message'] .= $item . "\r\n";
    }

    $posted['inquiry']['message'] .= '"' . $comments . '"';

    /**
     * Create or load a matching member
     */
    $member_id = $_GLOBALS["member"]["id"];

    if (moduleLoad("member")) {
        if (0 == $member_id) {
            $member_id = memberIdFromEmail($email);
        }
        if (0 == $member_id) {
            include "../inc/build_member.inc.php";
            //$member["id"] = $member["member_id"] = $member_id = memberToDatabase($member);
            $insert = array();

            //@todo memberToDatabase($member);
            $member["id"] = $member["member_id"] = $member_id = $DB->modelSave($member, MEMBER_TABLENAME, MEMBER_PRIMARYKEY);
        }
        $_GLOBALS["member"] = memberFromDatabase($member_id);
    }
    /**
     *    Subscribe member to selected newsletter
     */
    /*if ("checked" == $mail_list) {
        memberMailingListSubscribe($_GLOBALS['member']['id'], $_GLOBALS['site_settings']['style_id']);
    }*/
    /**
     * Create a matching invoice
     */
    include "../inc/build_inquiry.inc.php";

    /**
     * Save invoice to database and fetch invoice_id
     */
    /*
    $invoice["invoice_offer"]    = $invoice_products[0]["product_offer"];
    $invoice["invoice_cost"]     = $invoice_products[0]["product_cost"];
    $invoice["invoice_online"]   = $invoice_products[0]["product_online"];
    $invoice["invoice_offerau"]  = $invoice_products[0]["product_offerau"];
    $invoice["invoice_costau"]   = $invoice_products[0]["product_costau"];
    $invoice["invoice_onlineau"] = $invoice_products[0]["product_onlineau"];

    $invoice_products[0]["generic"]      = "y";
    $invoice_products[0]["generic_code"] = "GMA";
    $invoice_products[0]['product_qty'] = 1;
    */
    $invoice['invoice_id'] = $invoice_id = invoiceToDatabase($invoice);

    foreach ($invoice_charges as $k => $v) {
        $invoice_charges[$k]["invoice_id"] = $invoice_id;
    }
    invoiceChargesToDatabase($invoice_id, $invoice_charges);
    
    foreach ($invoice_courier_quotes as $k => $v) {
        $invoice_courier_quotes[$k]["invoice_id"] = $invoice_id;
    }
    invoiceCourierQuotesToDatabase($invoice_id, $invoice_courier_quotes);

    foreach ($invoice_products as $k => $v) {
        $invoice_products[$k]["invoice_id"] = $invoice_id;
    }
    invoiceProductsToDatabase($invoice_id, $invoice_products);

    $invoice_template_vars = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);

    $template_engine = new Template();
    $template_engine->set_vars($invoice_template_vars);
    $email_template_list = invoiceEmailTemplatesList();

    $mailer           = new PHPMailerEmail();
    $mailer->From     = $_GLOBALS["site_settings"]["email_sales"];
    $mailer->FromName = $_GLOBALS["site_settings"]["site_name"];
    $mailer->Sender   = 'no-reply@highwaygroup.net';

    /**
     * Send staff notification for new invoice
     */
    if (function_exists("notifyActionMade")) {
        notifyActionMade(
            NOTIFY_PRODUCT_BUILDER_INQUIRY,
            $_GLOBALS["site_settings"]["site_id"],
            $invoice_template_vars
        );
    }
    $successhtml='<div class="order-success__body">
               <div class="order-success__header" style="padding: 0px 0 52px;">
                  <div class="order-success__icon" style="text-align: center;display: table;">
                     <div class="row">
                     <svg width="100" height="100">
                        <path d="M50,100C22.4,100,0,77.6,0,50S22.4,0,50,0s50,22.4,50,50S77.6,100,50,100z M50,2C23.5,2,2,23.5,2,50
                           s21.5,48,48,48s48-21.5,48-48S76.5,2,50,2z M44.2,71L22.3,49.1l1.4-1.4l21.2,21.2l34.4-34.4l1.4,1.4L45.6,71
                           C45.2,71.4,44.6,71.4,44.2,71z"></path>
                     </svg>
                     <div style="text-align: left; margin-left: 20px; margin-top: -17px;">
                        <h1 class="order-success__title">Thank You</h1>
                     </div>
                  </div>
                  </div>
                  <div class="order-success__icon" style="text-align: center;display: table;margin-top: 10px;">
                     <p><span style="font-size: 22px;"><b>Reference </b>: </span><span style="color: #5ac144; font-weight: 600; font-size: 22px;">' . htmlspecialchars(invoiceGetValue($invoice_id, 'invoice_number')) . '</span></p>
                  </div>
                  <div class="order-success__icon row" style="text-align: center;display: table;margin-top: 10px;">
                     <p>Your custom product request has been received.We will contact you as soon as possible. Your request can also be followed in your account <a href="/account/dashboard">My Account</a>.</p>
                  </div>
                  <div class="order-success__actions"><a href="/" class="btn btn-sm btn-secondary">Go To Homepage</a> <a href="/product-builder.html" class="btn btn-primary">Build Another Product</a></div>
               </div>
            </div>';
    echo $successhtml;
} else {
    echo "error";
}
