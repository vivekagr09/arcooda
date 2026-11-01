<?php
namespace app\controllers;

use app\models\TwigInvoice;

/**
 * @var string             $action
 * @var \app\models\MyTwig $twig
 */
?><?php

global $_GLOBALS;

switch ($action) {
    case 'index':
        break;

    case 'invoice':
        global $_GLOBALS;

        $invoices = TwigInvoice::search([
            'invoice_id' => $_GET['id'] ?? 0,
            'rand_num'   => max($_GET['crc'] ?? 0, 1),
        ]);
        if (empty($invoices)) {
            redirect('/account/orders');
            die();
        }

        $twig_invoice = reset($invoices);
        $invoice_id   = $twig_invoice->id;

        $invoice = invoiceFromDatabase($invoice_id);
        if (!in_array($invoice['invoice_status'], array('proforma', 'sales'))) {
            redirect('/account/orders');
            die();
        }
        
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        $amount_due       = $invoice['invoice_offer'];
        foreach ($invoice_payments as $row) {
            $amount_due = bcsub($amount_due, $row['payment_amount'], 2);
        }
        if (bccomp($amount_due, '0.00', 2) <= 0) {
            redirect('success?invoice_id=' . $invoice_id);
            die();
        }

        switch ($invoice['paymenttype_id']) {
            case "GOOGLE":        // Google Pay
                //no break
            case "APPLE":         // Apple Pay
                //no break
            case "AMEX":          // Credit Card - American Express
                //no break
            case "MASTERCARD":    // Credit Card - Mastercard
                //no break
            case "VISA":          // Credit Card - Visa
                //no break
            case "CREDIT":        // Credit Card - Visa/Mastercard
                //no break
            case "CREDIT_SECURE": // Secure Credit Card
                //no break
            /*
            case "":
                //no break
            */
            case "PAYPAL":        // PayPal
                //no break
            case "BANK":          // AUD Bank Deposit
                //no break
            case "EUR_BANK":      // EUR Bank Deposit
                //no break
            case "EUR_BANK2":     // EUR Bank Account - HSBC
                //no break
            case "GBP_BANK":      // GBP Bank Deposit
                //no break
            case "GBP_BANK2":     // GBP Bank Account - HSBC
                //no break
            case "HKD_BANK":      // HKD Bank Deposit
                //no break
            case "JPY_BANK":      // JPY Bank Deposit
                //no break
            case "USD_BANK3":     // USD Bank Account - Commonwealth Bank
                //no break
            case "USD_BANK":      // USD Bank Deposit - HSBC
                //no break
            case "USD_BANK2":     // Wise - International Customer
                //no break
            case "USD_BANK4":     // Wise - USA Customer
                redirect('/airwallex/invoice?id=' . $invoice_id . '&crc=' . $invoice['rand_num']);
                die();
        }

        die('Payment method not yet implemented');

    case 'success':
        //TODO
        die($twig->load('airwallex-result.twig')->render(['inputs' => [
            'invoice'     => '',
            'title_front' => '',
            'status'      => 'success',
        ]]));

    case 'failed':
        //TODO
        die($twig->load('airwallex-result.twig')->render(['inputs' => [
            'invoice'     => '',
            'title_front' => '',
            'status'      => 'failed',
        ]]));
    case "airwallex-checkout":
        global $_GLOBALS;

        moduleLoad('payment');
        moduleLoad('invoice');

        $invoices = TwigInvoice::search([
            'invoice_id' => $_GET['id'] ?? 0,
            'rand_num'   => max($_GET['crc'] ?? 0, 1),
        ]);
        if (empty($invoices)) {
            redirect('/account/orders');
            die();
        }

        $twig_invoice = reset($invoices);
        $invoice_id   = $twig_invoice->id;

        $invoice = invoiceFromDatabase($invoice_id);
        // $invoice_products = invoiceProductsFromDatabase($invoice_id);
        // $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
        // $invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice_id);    
        if (!in_array($invoice['invoice_status'], array('proforma', 'sales'))) {
            redirect('/account/orders');
            die();
        }
        
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        $amount_due       = $invoice['invoice_offer'];
        foreach ($invoice_payments as $row) {
            $amount_due = bcsub($amount_due, $row['payment_amount'], 2);
        }
        if (bccomp($amount_due, '0.00', 2) <= 0) {
            redirect('success?invoice_id=' . $invoice_id);
            die();
        }

        $payment_methods = [];
        if (function_existS('paymentMethodsFromDatabase') && function_exists('paymentMethodQuery')) {       
            foreach (paymentMethodsFromDatabase(paymentMethodQuery([
                'site_id'               => $_GLOBALS['site_settings']['site_id'],
                'payment_method_public' => 'y',
            ])) as $row) {
                $payment_methods[$row['payment_method_id']] = $row;
            }
        } else {
            // @todo phase this out, use the module functions
            if ($query = $DB->query(sprintf(
                "SELECT *, `payment_method_id` AS `id` FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC",
                $_GLOBALS['site_settings']['site_id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $payment_methods[$row['id']] = $row;
                }
                $DB->free_result($query);
            }
        }

        $payment_due = $invoice['invoice_offer'];
        foreach ($invoice_payments as $payment) {
            $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
        }

        die($twig->load('airwallex-checkout.twig')->render(['inputs' => [
            'invoice'                => $invoice,
            'payment_methods'        => $payment_methods,
            'invoice_payments'       => $invoice_payments,
            'payment_due'            => $payment_due,
            'titletop'               => 'Payment',
        ]]));

    case "payment-checkout":
        global $_GLOBALS;
        moduleLoad('payment');
        moduleLoad('invoice');

        $invoices = TwigInvoice::search([
            'invoice_id' => $_GET['id'] ?? 0,
            'rand_num'   => $_GET['crc'] ?? 0,
        ]);
        if (empty($invoices)) {
            redirect('/account/orders');
            die();
        }

        $twig_invoice = reset($invoices);
        $invoice_id   = $twig_invoice->id;

        $invoice = invoiceFromDatabase($invoice_id);
        // $invoice_products = invoiceProductsFromDatabase($invoice_id);
        // $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
        // $invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice_id);    
       /* if (!in_array($invoice['invoice_status'], array('proforma', 'sales'))) {
            redirect('/account/orders');
            die();
        }*/
        
        $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
        $amount_due       = $invoice['invoice_offer'];
        foreach ($invoice_payments as $row) {
            $amount_due = bcsub($amount_due, $row['payment_amount'], 2);
        }
        if (bccomp($amount_due, '0.00', 2) <= 0) {
            redirect('success?invoice_id=' . $invoice_id);
            die();
        }

        $payment_methods = [];
        if (function_existS('paymentMethodsFromDatabase') && function_exists('paymentMethodQuery')) {       
            foreach (paymentMethodsFromDatabase(paymentMethodQuery([
                'site_id'               => $_GLOBALS['site_settings']['site_id'],
                'payment_method_public' => 'y',
            ])) as $row) {
                $payment_methods[$row['payment_method_id']] = $row;
            }
        } else {
            // @todo phase this out, use the module functions
            if ($query = $DB->query(sprintf(
                "SELECT *, `payment_method_id` AS `id` FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC",
                $_GLOBALS['site_settings']['site_id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $payment_methods[$row['id']] = $row;
                }
                $DB->free_result($query);
            }
        }

        $payment_due = $invoice['invoice_offer'];
        foreach ($invoice_payments as $payment) {
            $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
        }
        die($twig->load('payment-checkout.twig')->render(['inputs' => [
            'invoice'                => $invoice,
            'payment_methods'        => $payment_methods,
            'invoice_payments'       => $invoice_payments,
            'payment_due'            => $payment_due,
            'titletop'               => 'Payment',
        ]]));

    case "manual-payment-checkout":
        global $_GLOBALS;

        moduleLoad('payment');

        $payment_methods = [];
        if (function_existS('paymentMethodsFromDatabase') && function_exists('paymentMethodQuery')) {       
            foreach (paymentMethodsFromDatabase(paymentMethodQuery([
                'site_id'               => $_GLOBALS['site_settings']['site_id'],
                'payment_method_public' => 'y',
            ])) as $row) {
                $payment_methods[$row['payment_method_id']] = $row;
            }
        } else {
            // @todo phase this out, use the module functions
            if ($query = $DB->query(sprintf(
                "SELECT *, `payment_method_id` AS `id` FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC",
                $_GLOBALS['site_settings']['site_id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $payment_methods[$row['id']] = $row;
                }
                $DB->free_result($query);
            }
        }

        $issues = [];

        foreach ([
            'name',
            'reference',
            'email',
            'amount'
        ] as $k) {
            if ('' === trim($_POST[$k] ?? '')) {
                $issues[$k] = 'This field is required';
            }
        }

        die($twig->load('manual-payment-checkout.twig')->render(['inputs' => [
            'payment_methods'        => $payment_methods,
            'titletop'               => 'Payment',
            'issues'                 => $issues,
        ]]));
    case "manual-payment" :
    if (count($_POST) && $_POST['action']=='payment') {
        global $_GLOBALS;
        global $DB;
        moduleLoad('notify');
        moduleLoad('payment');
        moduleLoad('member');
        $credit_id = 0;
        if ($DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`credit_payments` (`credit_name`, `credit_amount`, `credit_email`, `credit_ip`, `credit_ref`, `credit_date`, `credit_currency`, `credit_currency_amount`, `credit_company`,`credit_notes`,`paymenttype_id`) VALUES (%s, %s, %s, %s, %s, NOW(), %s, %s, %s,%s,%s)",
            $DB->escape($_POST['cc_name']),
            $DB->escape($_POST['cc_amount']),
            $DB->escape($_POST['cc_email']),
            $DB->escape($_SERVER['REMOTE_ADDR']),
            $DB->escape($_POST['cc_reference']),
            $DB->escape('USD'),
            $DB->escape($_POST['cc_amount']),
            $DB->escape($_POST['cc_name']),
            $DB->escape($_POST['cc_notes']),
            $DB->escape($_POST['paymenttype_id'])
        ))) {
            $credit_id = $DB->insert_id();
        }
        $member_id = 0;
        if ($row = $DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members` WHERE `email_address` = %s LIMIT 1",
            $DB->escape($_POST['cc_email'])
        ), MYSQLI_ASSOC)) {
            $member_id = $row['member_id'];
        } elseif ($row = $DB->query_first(sprintf(
            "SELECT `id` FROM `highwaygroup`.`new_members_logins` WHERE `email_address` = %s LIMIT 1",
            $DB->escape($_POST['cc_email'])
        ), MYSQLI_ASSOC)) {
            $member_id = $row['id'];
        } elseif ($row = $DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_contacts` WHERE `email` = %s LIMIT 1",
            $DB->escape($_POST['cc_email'])
        ), MYSQLI_ASSOC)) {
            $member_id = $row['member_id'];
        } elseif ($row = $DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_notify` WHERE `email` = %s LIMIT 1",
            $DB->escape($_POST['cc_email'])
        ), MYSQLI_ASSOC)) {
            $member_id = $row['member_id'];
        } elseif ($row = $DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_shipping` WHERE `email` = %s LIMIT 1",
            $DB->escape($_POST['cc_email'])
        ), MYSQLI_ASSOC)) {
            $member_id = $row['member_id'];
        }
        if($member_id == 0 ){
            $ip      = $_SERVER['REMOTE_ADDR'];
            $country = getCountryInfo($ip);
             $member_id = memberToDatabase([
            'email_address' => $_POST['cc_email'],
            'first_name'    => $_POST['cc_name'],
            'country'       =>  $country['a2'] ?? '',
            'account_type'  => 'lite',
            ]);
            memberLoginToDatabase([
                'member_id' => $member_id,
                'active'    => 'n',
            ]);
        }
       
        $payment_id = paymentToDatabase();
        redirect('/airwallex/manual-payment?payment_id=' . $payment_id .'&credit_id=' .$credit_id. '&member_id=' .$member_id);
        die();
    }
}
