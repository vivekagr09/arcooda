<?php
use PHPMailer\PHPMailer\PHPMailer;

// Constant Variables
define("NOTIFY_COMMENT_CREATED",           11);
define("NOTIFY_DISTRIBUTOR_APPLICATION",   14);
define("NOTIFY_GENERIC",                    6);
define("NOTIFY_INQUIRY_CREATED",           10);
define("NOTIFY_INQUIRY_MODIFIED",           7);
define("NOTIFY_INVOICE_AWAITING_SHIPPING",  4);
define("NOTIFY_INVOICE_CREATED",            1);
define("NOTIFY_INVOICE_MODIFIED",           7);
define("NOTIFY_INVOICE_SHIPPED",            5);
define("NOTIFY_MANUAL_PAYMENT_ERROR",      12);
define("NOTIFY_MANUAL_PAYMENT_RECEIVED",   13);
define("NOTIFY_PAYMENT_ERROR",              3);
define("NOTIFY_PAYMENT_RECEIVED",           2);
define("NOTIFY_PINBALL_JOINUS",            16);
define("NOTIFY_PINBALL_START",             17);
define("NOTIFY_PRODUCT_BUILDER_INQUIRY",    0);
define("NOTIFY_PRODUCT_DETAILS_MISSING",    9);
define("NOTIFY_PRODUCT_INVENTORY_LOW",      8);
define("NOTIFY_SERVICE_REQUEST_CREATED",   15);
define("NOTIFY_PRODUCT_INQUIRY_MEMBER",    18);
define("NOTIFY_PAYMENT_SUCCESS",           19);
define("NOTIFY_PAYMENT_FAILURE",           20);
define("CONTACTUS_INQUIRY_CREATED",        21);
define("NOTIFY_QUICK_INQUIRY_CREATED",     22);
define("NOTIFY_FREIGHT_TAX_INQUIRY_CREATED",23);
/**
 * Get notify options from database
 *
 * @return Object[] Returns the query result
 */
if (!function_exists("notifyOptionsFromDatabase")) {
    function notifyOptionsFromDatabase()
    {
        global $DB;
        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT * FROM `notify_options`"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        $DB->free_result($query);
        return $result;
    }
}

/**
 * Get notify options from post
 *
 * @param Object $posted Post to get notify options
 *
 * @return Object Returns the result
 */
if (!function_exists("notifyOptionsFromPost")) {
    function notifyOptionsFromPost($posted)
    {
        $result = notifyOptionsFromDatabase();
        foreach (@$posted['notify_affected'] as $k1 => $v1) {
            foreach (@$v1 as $k2 => $v2) {
                foreach (@$v2 as $k3 => $v3) {
                    if ('y' == $v3) {
                        foreach ($result as $k4 => $v4) {
                            if ($v4['action_id'] == $k3 && $v4['staff_id'] == $k2 && $v4['site_id'] == $k1) {
                                unset($result[$k4]);
                            }
                        }
                    }
                }
            }
        }
        foreach (@$posted['notify_options'] as $k1 => $v1) {
            foreach (@$v1 as $k2 => $v2) {
                foreach (@$v2 as $k3 => $v3) {
                    if ('on' == $v3) {
                        $result[] = array('staff_id' => $k2, 'action_id' => $k3, 'site_id' => $k1);
                    }
                }
            }
        }
        return $result;
    }
}

/**
 * Insert notify options to database
 *
 * @param Object $notify_options Notify options to be inserted into database
 */
if (!function_exists("notifyOptionsToDatabase")) {
    function notifyOptionsToDatabase($notify_options)
    {
        global $DB;
        $insert = array();
        foreach ($notify_options as $row) {
            $insert[] = sprintf(
                "(%d, %d, %d)",
                $row["site_id"],
                $row["staff_id"],
                $row["action_id"]
            );
        }
        $DB->query(sprintf(
            "DELETE FROM `notify_options`"
        ));
        if (count($insert)) {
            $DB->query(sprintf(
                "INSERT IGNORE INTO `notify_options` (`site_id`, `staff_id`, `action_id`) VALUES %s",
                implode(", ", $insert)
            ));
        }
    }
}
if (!function_exists('notifyActionMade')) {
    function notifyActionMade($action_id, $site_id, $template_vars)
    {
        global $DB,$_GLOBALS;

        moduleLoad('template');

        $staff_list = array();
        $sites = explode('|', $site_id);
        foreach (notifyOptionsFromDatabase() as $row) {
            if (in_array($row['site_id'], $sites) && $row['action_id'] == $action_id) {
                $staff_list[] = $row['staff_id'];
            }
        }

        //if($row['site_id']==$site_id && $row['action_id']==$action_id)

        $email_templates = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`email_templates`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $email_templates[$row['template_id']] = $row;
            }
            $DB->free_result($query);
        }

        $email_template = new Template();
        $email_template->set_vars($template_vars);

        $subject = '(' . $action_id . ') Website Notification';
        $content = '(' . $action_id . ') A general notification from Coinopexpress server' . "\n\n";
        $content .= 'The following values are set for the notification:' . "\n";
        foreach ($template_vars as $key => $value) {
            $content .= strtolower(substr($key, 1, -1)) . ' = ' . $value . "\n";
        }

        moduleLoad('phpmailer');

        $email = new PHPMailer();
        $email->From = 'sales@' . str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $email->Sender = $email->From;
        $email->FromName = $template_vars['[COMPANY_NAME]'] . ' Alert';
        $email->Mailer = "sendmail";
        //$email->isHTML(false);
        $email->IsHTML(true);
        if ($action_id == 6) {
            $email->isHTML(false);
        }
        $appy_email_templ= true;
        $from_name = '';
        switch ($action_id) {
            case 1:
                $from_name = 'Alert - New Order Placed';
                if ($email_templates[6]['template_subject'] != '') {
                    $subject = $email_templates[6]['template_subject'];
                }else{
                    $subject = 'New Order Placed - Invoice [INVOICE_NUMBER] - [BILL_NAME]';
                }

                if ($email_templates[6]['template_text'] != '') {
                    $content = $email_templates[6]['template_text'];
                }

                break;
            case 2:
                $from_name = 'Alert - Payment Received';
                if ($email_templates[7]['template_subject'] != '') {
                    $subject = $email_templates[7]['template_subject'];
                }

                if ($email_templates[7]['template_text'] != '') {
                    $content = $email_templates[7]['template_text'];
                }

                break;
            case 3:
                $from_name = 'Alert - Payment Error';
                if ($email_templates[8]['template_subject'] != '') {
                    $subject = $email_templates[8]['template_subject'];
                }

                if ($email_templates[8]['template_text'] != '') {
                    $content = $email_templates[8]['template_text'];
                }

                break;
            case 4:
                $from_name = 'Alert - Goods Awaiting Shipping';
                if ($email_templates[9]['template_subject'] != '') {
                    $subject = $email_templates[9]['template_subject'];
                }

                if ($email_templates[9]['template_text'] != '') {
                    $content = $email_templates[9]['template_text'];
                }

                break;
            case 5:
                $from_name = 'Alert - Payment Received';
                if ($email_templates[10]['template_subject'] != '') {
                    $subject = $email_templates[10]['template_subject'];
                }
                if ($email_templates[10]['template_text'] != '') {
                    $content = $email_templates[10]['template_text'];
                }

                break;
            case 6:
                $subject = '[EMAIL_SUBJECT]';
                $content = '[EMAIL_BODY]';
                if (is_array($template_vars['[ATTACHMENTS]'])) {
                    foreach ($template_vars['[ATTACHMENTS]'] as $attach) {
                        $email->AddStringAttachment($attach['contents'], $attach['file_name'], 'base64', $attach['content_type']);
                    }
                }

                break;
            case 7:
                //order modified action
                $from_name = 'Alert - Order Modified';
                if ($email_templates[11]['template_subject'] != '') {
                    $subject = $email_templates[11]['template_subject'];
                }

                if ($email_templates[11]['template_text'] != '') {
                    $content = $email_templates[11]['template_text'];
                }

                break;
            case 8:
                //order modified action
                $from_name = 'Alert - Inventory Low';
                if ($email_templates[12]['template_subject'] != '') {
                    $subject = $email_templates[12]['template_subject'];
                }

                if ($email_templates[12]['template_text'] != '') {
                    $content = $email_templates[12]['template_text'];
                }
            case 12 :
                $from_name = "Alert - Manual Payment Error -".$template_vars["[USER_NAME]"];
                $subject   = $from_name;
                ob_start();  
                include __DIR__ . '/manual_payment_error.tpl.php'; 
                $content   = ob_get_clean();
                $appy_email_templ= false;
                //if (preg_match("/<title>(.+)<\\/title>/", $content, $match)) $subject = $match[1];
                $email->isHTML(true);
                break;
            case 13 :
                $from_name = "Alert - Manual Payment Received -".$template_vars["[USER_NAME]"];
                $subject   = $from_name;
                ob_start();  
                include __DIR__ . '/manual_payment_received.tpl.php'; 
                $content   = ob_get_clean();
                $appy_email_templ= false;
                //if (preg_match("/<title>(.+)<\\/title>/", $content, $match)) $subject = $match[1];
                $email->isHTML(true);
                break;
            case 18:
                $from_name = 'Inquiry';
                $subject = 'Inquiry for the Invoice #QAR'.$template_vars['[INVOICE_ID]'];

                if ($email_templates[16]['template_text'] != '') {
                    $content = nl2br($email_templates[16]['template_text']);
                }
                break;
            case 19:
                $from_name = "Payment Status";
                $subject = 'Payment Approval - Payment received for #IAR-'.$template_vars['[INVOICE_ID]'];
                break;
            case 20:
                $from_name = "Payment Status";
                $subject = 'Payment Approval - Payment failed for #IAR-'.$template_vars['[INVOICE_ID]'];
                break;
             case 10:
                $from_name = 'Alert – New Quote Placed';
                $subject = 'New Quote Placed - Inquiry – #QAR-' .$template_vars['[INVOICE_ID]'].' – '.$_GLOBALS['member']['full_name'];

                if ($email_templates[16]['template_text'] != '') {
                    $content = nl2br($email_templates[16]['template_text']);
                }
                break;
            case 21:
                $from_name = 'Alert – Contact Page Inquiry';
                $subject = 'New Contact Page Inquiry – #QAR-' .$template_vars['[INVOICE_ID]'].'-'.$template_vars['[CONTACTUS_SUBJECT]'];

                if ($email_templates[16]['template_text'] != '') {
                    $content = nl2br($email_templates[16]['template_text']);
                }
                break;
             case 22:
                $from_name = 'Alert – Quick  Inquiry';
                $subject = 'New Quick Inquiry  – #QAR-' .$template_vars['[INVOICE_ID]'].' - '.$template_vars['[INQUIRY_PRODUCT_NAME]'];

                if ($email_templates[16]['template_text'] != '') {
                    $content = nl2br($email_templates[16]['template_text']);
                }
                break;
            case 23:
                $from_name = 'Alert – Quick  Inquiry';
                $subject = 'New Freight+Tax Inquiry  – #QAR-' .$template_vars['[INVOICE_ID]'].' - '.$template_vars['[INQUIRY_PRODUCT_NAME]'];

                if ($email_templates[16]['template_text'] != '') {
                    $content = nl2br($email_templates[16]['template_text']);
                }
                break;
        }

        if ($from_name != '') {
            $email->FromName = $from_name;
        }

        if ($action_id != 6 && $action_id != 8 && $action_id != 18 &&  $action_id != 12 &&  $action_id != 13) {
            $content = getHTMLTemplate($action_id);
        }

        $email_template->set_contents($subject);
        $subject = $email_template->apply();
        if($appy_email_templ){
            $email_template->set_contents($content);
            $content = $email_template->apply();
        }
        $email->CharSet = "utf-8";
        $email->Subject = $subject;
        $email->Subject = $subject;
        $email->Body = $content;
        if (count($staff_list) > 0) {
            foreach ($staff_list as $staff_id) {
                $row = $DB->query_first(sprintf(
                    "SELECT `email`, `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `id` = %d LIMIT 1",
                    $staff_id
                ), MYSQLI_ASSOC);
                $email->AddAddress($row['email'], trim($row['first_name'] . ' ' . $row['last_name']));
            }
            $result = $email->Send();
        } else if($action_id == 18) {
            $email->AddAddress($template_vars['[BILL_EMAIL]']);
            $email->Send();
        } else {
            $email->AddAddress('backup@highwaygroup.net', 'Unassigned Notification');
            $result = $email->Send();
        }
        if ($site_id == 39) {
            $email->ClearAddresses();
            $email->AddAddress('sales@arcooda.com');
            $email->Send();
        }
    }
}
if (!function_exists("notifyActionRecipients")) {
    function notifyActionRecipients($action_id, $site_id = null) {
        global $DB, $_GLOBALS;

        if (is_null($site_id)) {
            $site_id = $_GLOBALS['site_settings']['site_id'];
        }

        $result = array(
        );

        if ($query  = $DB->query(sprintf(
            "SELECT `u`.`first_name`, `u`.`last_name`, `u`.`email` FROM `highwaygroup`.`notify_options` AS `no` INNER JOIN `highwaygroup`.`users` AS `u` ON `u`.`id` = `no`.`staff_id` WHERE `no`.`action_id` = %d AND `no`.`site_id` = %d AND `u`.`active` = 'y'",
            $action_id,
            $site_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[strtolower($row['email'])] = trim($row['first_name'] . ' ' . $row['last_name']);
            }
            $DB->free_result($query);
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `website`, `sales_email` FROM `highwaygroup`.`websites` WHERE `id` = %d AND `sales_email` <> '' LIMIT 1",
            $site_id
        ), MYSQLI_ASSOC)) {
            $result[strtolower($row['sales_email'])] = trim($row['website'] . ' Sales');
        }

        return $result;
    }
}
function getHTMLTemplate($action_id)
{
    $tdStyle = "background-color:#EBEBEB;color:#333333;";

    $text = array(1 => 'A new order has been created online.', 2 => 'Payment for this invoice has been received.', 3 => 'There was an error with the payment.', 4 => 'This Order is awaiting to be shipped.', 5 => 'This order has been shipped.', 6 => '', 7 => 'This order has been modified.',10 => 'A new inquiry has been created.',);
    $status = array(1 => 'Order Created', 2 => 'Payment Received', 3 => 'Payment Error', 4 => 'Awaiting Shipping', 5 => 'Order Shipped.', 6 => '', 7 => 'Order Modified',10=>'Inquiry Created',21=>'Inquiry Created',22=>'Inquiry Created');

    $template = '<div style="font-family:Verdana;font-size:13px;color:#717171;"><b>Order Status: <span style="color:red">' . $status[$action_id] . '</span></b></div><br /><br />';
    $template .= '<div style="font-family:Verdana;font-size:12px;color:#717171;">' . $text[$action_id] . ' The details are as follows :</div><br />';
    $template .= '<div style="width:85%;padding-left:20px;">';
    $template .= '<table style="border-collapse:collapse;width: 85%;border: 1px solid #CCCCCC;font-family:Verdana;color:#717171;font-size:11px;" cellspacing=0 cellpadding=2 bordercolor="#CCCCCC" border=1>';
    $template .= '<tr><td style="font-size:12px;' . $tdStyle . '"><b>Bill To</b></td>';
    $template .= '<td style="font-size:12px;' . $tdStyle . '"><b>Ship To</b></td></tr>';
    $template .= '<tr><td style="color:#717171;">[BILL_ADDRESS_FULL_HTML]</td><td style="color:#717171;">[SHIP_ADDRESS_FULL_HTML]</td></tr></table><br />';
    $template .= '<table style="border-collapse:collapse;width: 85%;border: 1px solid #CCCCCC;font-family:Verdana;color:#717171;font-size:11px;" cellspacing=0 cellpadding=4 bordercolor="#CCCCCC" border=1>';
    $template .= '<tr style="text-align:center"><td style="' . $tdStyle . '"><b>Item ID</b></td><td style="' . $tdStyle . ';text-align:left"><b>Product Name</b></td><td style="' . $tdStyle . '"><b>Qty</b></td><td style="' . $tdStyle . '"><b>Price Each</b></td><td style="' . $tdStyle . '"><b>Amount</b></td></tr>';
    $template .= '[INVOICE_HTML_PRODUCTS][INVOICE_HTML_SHIPPING][INVOICE_HTML_CHARGES][INVOICE_HTML_TOTALS]</table><br />';
    $template .= '</div><br /><br />';
    $template .= '[PAYMENT_METHOD_HTML]';
    $template .= '<div style="width:85%;padding-left:20px;color:#717171;">Links :<a href="' . htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=[INVOICE_ID]')) . '" style="color:#717171;">View Inquiry</a>, <a href="' . htmlspecialchars(relative_url('/webadmin/invoice?status=inquiry%7Cproforma%7Csales%7Creturn&site_id=39&action=edit&invoice_id=[INVOICE_ID]')) . '" style="color:#717171;">Edit Inquiry</a></div>';

    return $template;
}
