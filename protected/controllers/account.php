<?php
/**
 * @var string $action
 * @var app\models\MyTwig $twig
 */

namespace app\controllers;

use app\models\PHPMailerEmail;
use app\models\TwigCurrency;
use app\models\TwigInvoice;
use app\models\TwigUser;
use app\models\TwigProduct;
use app\models\TwigBloags;

use ParsedownExtra;

global $_GLOBALS;
global $DB;

//Actions:
// /account/dashboard
// /account/logout
// /account/profile
// /account/shipping
// /account/inquiries
// /account/invoice
// /account/orders
// /account/password
// /account/resetpassword
// /account/autologin
// /account/login
// /account/passwordFind
// /account/register

//Actions that require logged-in user
if (in_array($action, ['dashboard', 'profile', 'shipping', 'inquiries', 'orders', 'password', 'resetpassword','invoice'])) {
    if (!($_GLOBALS['member']['member_id'] ?? false)) {
        redirect('/account/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        die();
    }
}

//Actions that require not-logged-in user
if (in_array($action, ['autologin', 'login', 'passwordFind', 'register','checkUsername','captchaimage'])) {
    if ($_GLOBALS['member']['member_id'] ?? false) {

        if(!isset($_GET['crc']) && $action['invoice']){


        }else{
           redirect($_REQUEST['redirect'] ?? '/account/dashboard'); 
           die();
        }
        
        
    }
}
if (!function_exists("GlobalCurrencyList")) {
    /**
     * @deprecated Use getCurrencyList or getExchangeRateList
     *
     * @return mixed[][]
     */
    function GlobalCurrencyList() {
        global $DB;

        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`currency`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

/**
 * @param string $username
 * @param string $password
 *
 * @return mixed[]|false
 */
function login($username, $password)
{
    global $DB;

    moduleLoad('member'); //memberLogin

    if (($valid = $DB->query_first(sprintf(
        "SELECT `m`.`member_id`, `m`.`rand_num` FROM `highwaygroup`.`new_members_logins` AS `ml` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `ml`.`member_id` WHERE `ml`.`active` = 'y' AND `ml`.`password` = %s AND `m`.`email_address` = %s LIMIT 1",
        $DB->escape(md5($password)),
        $DB->escape($username)
    ), MYSQLI_ASSOC)) && $valid['member_id'] > 0) {

        /**
         * Update invalid rand_num
         */
        if (0 == $valid['rand_num']) {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`new_members` SET `rand_num` = DEFAULT(`rand_num`) WHERE `member_id` = %d LIMIT 1",
                $valid["member_id"]
            ));
            if ($row = $DB->query_first(sprintf(
                "SELECT `rand_num` FROM `highwaygroup`.`new_members` WHERE `member_id` = %d LIMIT 1",
                $valid['member_id']
            ), MYSQLI_ASSOC)) {
                $valid['rand_num'] = $row['rand_num'];
            }
        }
        memberLogin($valid['member_id'],$valid['rand_num']);
        //custom_setcookie('member_id', sprintf("%08x%08x", $valid['member_id'], $valid['rand_num']), strtotime('+1 year'), '/');

        //TODO: Merge visitor shopping cart

        return $valid;
    }
    return false;
}

switch ($action) {

    // Logged-in actions
    case 'dashboard':
         $issues               = [];
        if ('cancelorder' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'message',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            if(!count($issues)) {
                $invoice=TwigInvoice::search([
                    'member_id'      => $_GLOBALS['member']['member_id'],
                    'invoice_id' => intval($_POST['id']),
                ]);
                if($invoice){
                    invoiceSetValue($_POST['id'],'invoice_action','cancelled');
                    invoiceSetValue($_POST['id'],'invoice_status','closed');
                    if(isset($_POST['message']) && !empty($_POST['message'])){
                        $history                    = [];
                        $history['message_sender']  = $_GLOBALS['member']['member_id'];
                        $history['message_sentto']  = 'System Admin';
                        $history['message_title']   = $_POST['cancel_order_id'] . ' Order cancelled';
                        $history['message_type_id'] = 3;
                        $history['message_notes']   = $_POST['message'];
                        $history['message_raw']     = $_POST['message'];
                        invoiceHistoryToDatabase($_POST['id'], $history);

                        $invoice_id       = $_POST['id'];
                        $invoice          = invoiceFromDatabase($_POST['id']);
                        $invoice_products = invoiceProductsFromDatabase($_POST['id']);
                        $invoice_charges  = invoiceChargesFromDatabase($_POST['id']);

                        if ('' != $invoice['paymenttype_id']) {
                            $payment_types = getPaymentMethodsList();
                            $payment_data  = $DB->query_first(sprintf(
                                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
                                $invoice['site_id'],
                                formatSQLString($invoice['paymenttype_id'])
                            ), MYSQLI_ASSOC);
                            $payment_method = $payment_types[$invoice['paymenttype_id']];
                            if ('' != $payment_data['payment_method_details']) {
                                // $results['[PAYMENT_DETAILS]']      = $payment_data['payment_method_details'];
                                $payment_details = nl2br(htmlspecialchars($payment_data['payment_method_details']));
                            }
                        }

                        $payment_status      = '<span style="color:red;"><b>Awaiting</b></span>';
                        if ("payment" == $invoice["invoice_action"]) {
                            $payment_status = '<span style="color:red;"><b>Awaiting Payment</b></span>';
                        }
                        if("approval" == $invoice['invoice_action']) {
                            $payment_status = '<span style="color:green;"><b>Awaiting Payment Approval</b></span>';
                        }
                        if ("y" == $invoice["payments_located"]) {
                            $payment_status = '<span style="color:green;"><b>Approved</b></span>';
                        }

                        

                        $payment_method = $payment_method ?? '';

                        $payment_details = $payment_details ?? '';

                        ob_start();
                        ?>
                        <p><?= $_POST['message'] ?></p>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:50%">Bill To Address</th>
                                <th>Ship To Address</th>
                            </tr>
                            <tr>
                                <td><?= nl2br(hecho($invoice['bill_address_full'], 1)) ?></td>
                                <td><?= nl2br(hecho($invoice['ship_address_full'], 1)) ?></td>
                            </tr>
                        </table>
                
                        <table class="table table-bordered">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Price Each</th>
                                <th>Amount</th>
                            </tr>
                        <?php
                        foreach ($invoice_products as $invoice_product) {
                        ?>
                            <tr>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_id']) ?></td>
                                <td>
                                    <?php hecho($invoice_product['product_name']) ?>
                                    <p><i>Prouct ID: <?php hecho($invoice_product['product_id']) ?> - <?php hecho($invoice_product['product_name']) ?><br /><?= nl2br(hecho($invoice_product['product_notes'], 1)) ?></i></p>
                                </td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_qty']) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho(convertCurrency($invoice_product['product_offer'], $invoice['invoice_currency'], '', false, $invoice_product['decimal'])) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?= bcmul($invoice_product['product_offer'], $invoice_product['product_qty'], $invoice_product['decimal']) ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                            <tr>
                                <td colspan="3"></td>
                                <th>Sub-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['products_offer'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <th style="vertical-align:middle;text-align:center;">Grand-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['invoice_offer'] ?> <?= $invoice['invoice_currency'] ?></td>
                            </tr>
                        </table><br/><br/>
                        <table border="1" cellspacing="0" cellpadding="2" style="margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">
                            <tr>
                                <th style="background:#eee;color:#333">Payment Method :</th>
                                <td style="color:#777;"><?= $payment_method ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Payment Approval :</th>
                                <td style="color:#777;"><?= nl2br(htmlspecialchars_decode(htmlspecialchars($payment_status))) ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Additional Payment Details :</th>
                                <td style="color:#777;"><?= $payment_details ?></td>
                            </tr>
                        </table><br/><br/>
                
                        <p><b>Link: </b> <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=' . $invoice_id))?>">View Inquiry</a> . <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>">Edit Inquiry</a><a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>#sectionMemberNotes">Reply to Customer</a></p>
                        <?php
                        $message = ob_get_clean();
                        
                        $email           = new PHPMailerEmail();
                        $email->Subject  = 'Cancellation Request - Invoice '.$_POST['id'] ;
                        $email->From     = $_GLOBALS['site_settings']['email_sales'];
                        $email->FromName = 'Alert - Dashboard';
                        //$email->Body     = sprintf(ParsedownExtra::instance()->setBreaksEnabled(true)->text($message), htmlspecialchars($history['message_raw']));
                        $email->Body     = $message;
                        $email->AltBody  = $message;// sprintf($message, $history['message_raw']);
                        $email->AddAddress($_GLOBALS['site_settings']['email_sales'], $_GLOBALS['site_settings']['site_name']);
                        // $email->AddAddress('vivek@yopmail.com', 'Arcooda');
                        $email->Send();

                        invoiceSetValue($_POST['id'],'handler_note','Customer manually cancelled this order');
                    }
                    redirect($_SERVER['HTTP_REFERER'].'?result=y&msg= Order cancelled successfully');
                }else{
                    redirect($_SERVER['HTTP_REFERER'].'?result=n&msg=No data available for this invoice');
                }
                die();
            }
            
        }
        $seven_days_ago = strtotime('-7 days');
        $msg = ($_GET['msg']) ? $_GET['msg'] : '';
        $currOrders = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_status' => array('proforma', 'sales','inquiry','return'),
        ]);
        $oldOrders = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_status' => array('closed','processed','completed','cancelled','return'),
        ]);
        die($twig->load('account-dashboard.twig')->render(['inputs' => [
            'addresses' => $_GLOBALS['member']['ship_addr'],
            'member'    => $_GLOBALS['member'],
            'currOrders'=> $currOrders,
            'oldOrders' => $oldOrders,
            'result'    => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'     => ['y' => $msg, 'n' => $msg][$_GET['result'] ?? ''] ?? '',
        ]]));

    case 'logout':
        setcookie('member_id', '', strtotime('-1 hour'), '/');
        redirect('/');
        die();

    case 'preferences':
        moduleLoad('member'); //memberToDatabase

        if (isset($_POST['action']) && 'update' == $_POST['action']) {
            //@fixme This code is incorrect, newsletter subscriptions are handled via the newsletter module
            /*
            $member_preferences = memberFromDatabase($_GLOBALS['member']['member_id']);

            $prefs             = [];
            $prefs['customer'] = (isset($_POST['newslater'])) ? 'y' : 'n';
            $prefs['weekly']   = $member_preferences['weekly'];
            $prefs['closeout'] = $member_preferences['closeout'];
            memberPreferencesToDatabase($_GLOBALS['member']['member_id'], $prefs);
            */
          
            $member = [
                'member_id'             => $_GLOBALS['member']['member_id'],
                'special_freight_needs' => (isset($_POST['special_freight']) || '' != $_POST['instruction']) ? 'y' : 'n',
                'special_request'       => $_POST['instruction'],
                'mailing_list'          => (isset($_POST['newslater'])) ? 'y' : 'n',
                'currency'              => $_POST['b_transactionCurrency'],
            ];
            memberToDatabase($member);

            redirect('?result=y&action=update');
            die();
        }

        die($twig->load('account-preferences.twig')->render(['inputs' => [
            'member_preferences' => $member_preferences,
            'member'             => $_GLOBALS['member'],
            'result'             => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'              => [
                'update' => ['y' => _('Site Preferences has been updated'), 'n' => _('Failed to update Site Preferences')],
            ][$_GET['action'] ?? ''][$_GET['result'] ?? ''] ?? '',
            'currency_list'      => GlobalCurrencyList(),
            'membercurrency'     => $_GLOBALS['member']['currency'],
        ]]));
    case 'notify_party':
        moduleLoad('member'); //memberNotifyPartiesFromDatabase, memberNotifyPartyToDatabase, memberNotifyPartyDelete

        $member_notifyparties = memberNotifyPartiesFromDatabase($_GLOBALS['member']['id']);
        $issues               = [];
        if ('add-by-shipping' === ($_POST['action'] ?? '') && !count($issues) && count($_POST['shipping_address'])) {
            foreach ($_POST['shipping_address'] as $address) {
                foreach ($_GLOBALS['member']['ship_addr'] as $shipping_address) {
                    if ($shipping_address['id'] == $address) {
                        $insert_address            = array();

                        $insert_address['default'] = (count($member_notifyparties) > 0) ? 'n' : 'y';
                        foreach ([
                            'company_name' => 'company_name',
                            'address'      => 'address',
                            'city'         => 'city',
                            'state'        => 'state',
                            'country'      => 'country',
                            'contact'      => 'contact',

                            'phone_no'     => 'phone_no',
                            'phone_no2'    => 'phone_no2',
                            'fax_no'       => 'fax_no',
                            'mobile_no'    => 'mobile_no',
                            'post_code'    => 'post_code',
                            'vat_number'   => 'vat_number',
                            'email'        => 'email',
                            'eori_number'    => 'eori_number',
                            'abn_number'     => 'abn_number',
                            'company_number' => 'company_number',
                            'address_type'    => 'address_type',
                            'contact_last'      => 'contact_last',
                        ] as $k => $v) {
                            if (array_key_exists($k, $shipping_address)) {
                                $insert_address[$v] = trim($shipping_address[$k]);
                            }
                        }
                        $member_notifyparty_id = memberNotifyPartyToDatabase($_GLOBALS['member']['id'], $insert_address);
                        if ('y' == ($insert_address['default'] ?? '')) {
                            memberNotifyPartySetDefault($_GLOBALS['member']['id'], $member_notifyparty_id);
                        }
                    }
                }
            }
            redirect('?result=y&action=add');
            die();
        }
        if ('add' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'organization',
                'phone_no',
                'city',
                'address',
                'state',
                'home-email',
                'country',
                'addresstype'
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = '' . $k . ' field is required';
                }
            }
            foreach ([
                'organization' => 'company_name',
                'address'      => 'address',
                'city'         => 'city',
                'state'        => 'state',
                'country'      => 'country',
                'name-first'      => 'contact',
                'phone_no'     => 'phone_no',
                'phone_no2'    => 'phone_no2',
                'fax_no'       => 'fax_no',
                'mobile_no'    => 'mobile_no',
                'post_code'    => 'post_code',
                'vat_number'   => 'vat_number',
                'home-email'        => 'email',
                'default'      => 'default',
                'eori_number'    => 'eori_number',
                'abn_number'     => 'abn_number',
                'company_number' => 'company_number',
                'addresstype'    => 'address_type',
                'name-last'      => 'contact_last',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $address[$v] = trim($_POST[$k]);
                }
            }
            if (!count($issues)) {
                $address['default'] = (count($member_notifyparties) > 0) ? $address['default'] : 'y';

                $member_notifyparty_id = memberNotifyPartyToDatabase($_GLOBALS['member']['id'], $address);
                if ('y' == ($address['default'] ?? '')) {
                    memberNotifyPartySetDefault($_GLOBALS['member']['id'], $member_notifyparty_id);
                }

                redirect('?result=y&action=add');
                die();
            }
            redirect('?result=n&action=add');
            die();
        }
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            $id            = intval($_POST['id'] ?? 0);
            $address['id'] = $id;
            foreach ([
                'organization',
                'phone_no',
                'city',
                'state',
                'home-email',
                'country',
                'addresstype'
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'organization' => 'company_name',
                'street-address'      => 'address',
                'city'         => 'city',
                'state'        => 'state',
                'country'      => 'country',
                'name-first'      => 'contact',
                'phone_no'     => 'phone_no',
                'phone_no2'    => 'phone_no2',
                'fax_no'       => 'fax_no',
                'mobile_no'    => 'mobile_no',
                'post_code'    => 'post_code',
                'vat_number'   => 'vat_number',
                'home-email'        => 'email',
                'default'      => 'default',
                'eori_number'    => 'eori_number',
                'abn_number'     => 'abn_number',
                'company_number' => 'company_number',
                'addresstype'    => 'address_type',
                'name-last'      => 'contact_last',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $address[$v] = $_POST[$k];
                }
            }
            if (!count($issues)) {
                $address['default'] = (count($member_notifyparties) > 0) ? $address['default'] : 'y';

                $member_notifyparty_id = memberNotifyPartyToDatabase($_GLOBALS['member']['id'], $address);
                if ('y' == ($address['default'] ?? '')) {
                    memberNotifyPartySetDefault($_GLOBALS['member']['id'], $member_notifyparty_id);
                }

                redirect('?result=y&action=update');
                die();
            }
            redirect('?result=n&action=update');
            die();
        }
        if ('delete' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['id'] ?? 0);
            foreach ($member_notifyparties as $member_notifyparty) {
                if ($member_notifyparty['id'] == $id) {
                    memberNotifyPartyDelete($id);
                    redirect('?result=y&action=delete');
                    die();
                }
            }
            redirect('?result=n&action=delete');
            die();
        }
        die($twig->load('account-notify-party.twig')->render(['inputs' => [
            'addresses' => $member_notifyparties,
            'ship_addr' => $_GLOBALS['member']['ship_addr'],
            'result'    => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'     => [
                'add'    => ['y' => _('New Notify party has been added'), 'n' => _('Failed to add Notify party')],
                'update' => ['y' => _('Notify party has been updated'), 'n' => _('Failed to update Notify party')],
                'delete' => ['y' => _('Notify party was removed'), 'n' => _('Failed to remove Notify party')],
            ][$_GET['action'] ?? ''][$_GET['result'] ?? ''] ?? '',
            'issues'    => $issues,
        ]]));
    case 'contacts':
        moduleLoad('member'); //memberContactsFromDatabase, memberContactDelete, memberContactToDatabase, memberContactSetMain

        $member_contacts = memberContactsFromDatabase($_GLOBALS['member']['id']);
        $issues          = [];
        if ('add' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'name',
                'title',
                'email',
                'send_email_cc',
                'main',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = '' . $k . ' field is required';
                }
            }
            foreach ([
                'name'          => 'name',
                'last_name'     =>'last_name',
                'title'         => 'title',
                'email'         => 'email',
                'phone'         => 'phone',
                'send_email_cc' => 'send_email_cc',
                'main'          => 'main',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $contact[$v] = $_POST[$k];
                }
            }
            if (!count($issues)) {
                $contact_id = memberContactToDatabase($_GLOBALS['member']['id'], $contact);
                if ('y' == ($contact['main'] ?? '')) {
                    memberContactSetMain($_GLOBALS['member']['id'], $contact_id);
                }

                redirect('?result=y&action=add');
                die();
            }
            redirect('?result=n&action=add');
            die();
        }
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            $id            = intval($_POST['id'] ?? 0);
            $contact['id'] = $id;
            foreach ([
                'name',
                'title',
                'email',
                'send_email_cc',
                'main',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'name'          => 'name',
                'last_name'     =>'last_name',
                'title'         => 'title',
                'email'         => 'email',
                'phone'         => 'phone',
                'send_email_cc' => 'send_email_cc',
                'main'          => 'main',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $contact[$v] = $_POST[$k];
                }
            }

            if (!count($issues)) {
                $contact_id = memberContactToDatabase($_GLOBALS['member']['id'], $contact);
                if ('y' == ($contact['main'] ?? '')) {
                    memberContactSetMain($_GLOBALS['member']['id'], $contact_id);
                }

                redirect('?result=y&action=update');
                die();
            }
            redirect('?result=n&action=update');
            die();
        }
        if ('delete' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['id'] ?? 0);
            foreach ($member_contacts as $contact) {
                if ($contact['id'] == $id) {
                    memberContactDelete($id);
                    $get_contacts = memberContactsFromDatabase($_GLOBALS['member']['id']);
                    if(count($get_contacts) == 1) {
                        $id = '';
                        foreach ($get_contacts as $key => $value) {
                            $id = $value['id'];
                        }
                        if(!empty($id)) {
                            memberContactSetMain($_GLOBALS['member']['id'], $id);
                        }
                    }
                    redirect('?result=y&action=delete');
                    die();
                }
            }
            redirect('?result=n&action=delete');
            die();
        }
        die($twig->load('account-contact.twig')->render(['inputs' => [
            'contacts' => $member_contacts,
            'result'   => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'    => [
                'add'    => ['y' => _('New Contact has been added'), 'n' => _('Failed to add Contact')],
                'update' => ['y' => _('Contact has been updated'), 'n' => _('Failed to update Contact')],
                'delete' => ['y' => _('Contact was removed'), 'n' => _('Failed to remove Contact')],
            ][$_GET['action'] ?? ''][$_GET['result'] ?? ''] ?? '',
            'issues'   => $issues,
        ]]));
    case 'freight':
        moduleLoad('member'); //memberCouriersFromDatabase, memberCourierToDatabase, memberCourierDelete, memberGetOptionList, memberToDatabase

        $member_couriers = memberCouriersFromDatabase($_GLOBALS['member']['member_id']);

        $issues = [];
        if ('add' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'courier_id',
                'account_number',
                'insurance',
                'notes',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = '' . $k . ' field is required';
                }
            }
            foreach ([
                'courier_id'     => 'courier_id',
                'account_number' => 'account_num',
                'courier_name' => 'courier_name',
                'insurance'     => 'insurance',
                'notes'          => 'notes',
                'main'          => 'main',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $freight[$v] = $_POST[$k];
                }
            }
            if (!count($issues)) {
                $courier_id = memberCourierToDatabase($_GLOBALS['member']['id'], $freight);
                if ('y' == ($freight['main'] ?? '')) {
                    memberCourierSetMain($_GLOBALS['member']['id'], $courier_id);
                }

                redirect('?result=y&action=add');
                die();
            }
            redirect('?result=n&action=add');
            die();
        }
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['courier_acc_id'] ?? 0);
            foreach ([
                'courier_id',
                'account_number',
                'notes',
                'insurance'
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'courier_id'     => 'courier_id',
                'account_number' => 'account_num',
                'notes'          => 'notes',
                'insurance'      =>'insurance',
                'courier_name' => 'courier_name',
                'main'           =>'main'
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $freight[$v] = $_POST[$k];
                }
            }

            if (!count($issues)) {
                $freight['id'] = $id;

                $courier_id = memberCourierToDatabase($_GLOBALS['member']['id'], $freight);
                if ('y' == ($freight['main'] ?? '')) {
                    memberCourierSetMain($_GLOBALS['member']['id'], $courier_id);
                }

                redirect('?result=y&action=update');
                die();
            }
            redirect('?result=n&action=update');
            die();
        }
        if ('delete' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['courier_acc_id'] ?? 0);
            foreach ($member_couriers as $courier) {
                if ($courier['id'] == $id) {
                    memberCourierDelete($id);
                    redirect('?result=y&action=delete');
                    die();
                }
            }
            redirect('?result=n&action=delete');
            die();
        }
        $courier_list = getFreightServices('personal', '', true);
        die($twig->load('account-freight.twig')->render(['inputs' => [
            'member_couriers' => $member_couriers,
            'courier_list'    => $courier_list,
            'result'          => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'           => [
                'add'    => ['y' => _('New Freight has been added'), 'n' => _('Failed to add Freight')],
                'update' => ['y' => _('Freight has been updated'), 'n' => _('Failed to update Freight')],
                'delete' => ['y' => _('Freight was removed'), 'n' => _('Failed to remove Freight')],
            ][$_GET['action'] ?? ''][$_GET['result'] ?? ''] ?? '',
            'issues'          => $issues,
        ]]));
    case 'profile':
        moduleLoad('member');

        $member = [
            'member_id'  => $_GLOBALS['member']['member_id'],
            'updated_by' => 0,
        ];
        $issues = [];
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            $validate_array=[
                'given-name',
                'family-name',
                'street-address',
                'address-level2',
                'address-level1',
                'country',
                'home-tel',
                // 'home-tel2',
                'account-type',
                'addresstype',
                'postal-code'
            ];
            if($_POST['account-type']=='company'){
                $validate_array=array_merge($validate_array,['organization']);
            }
            foreach ($validate_array as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'given-name'     => 'first_name',
                'family-name'    => 'last_name',
                'street-address' => 'address',
                // 'street-address' => 'address_1',
                'address-level3' => 'address_2',
                'address-level2' => 'city',
                'address-level1' => 'state',
                'postal-code'    => 'post_code',
                'country'        => 'country',
                'account-type'   => 'account_type',
                'organization'   => 'company_name',
                'home-tel'       => 'phone_no',
                'home-tel2'       => 'phone_no2',
                'fax-tel'        => 'fax_no',
                'vat_number'     => 'vat_number',
                'abn_number'     => 'abn_number',
                'company_number' => 'company_number',
                'eori_number'     => 'eori_number',
                'mobile-tel'     => 'mobile_no',
                'url'            => 'website',
                'organization_title' => 'organization_title',
                'addresstype'   =>'address_type'
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $member[$v] = trim($_POST[$k]);
                }
            }
            if (!count($issues)) {
                /*
                $member_contact = [
                    'id'        => ???,
                    'member_id' => $_GLOBALS['member']['id'],
                    'name'      => $member['first_name'],
                    'last_name' => $member['last_name'],
                    'phone'     => $member['phone_no'],
                    'title'     => $member['organization_title'],
                ];
                memberContactToDatabase($_GLOBALS['member']['id'], $member_contact);
                */
                // @fixme Major security issues
                $DB->query(sprintf(
                    "UPDATE `highwaygroup`.`new_members_contacts` SET `name` = '%s', `last_name` = '%s',`phone` = '%s',`title` = '%s' WHERE `member_id` = %d AND `is_profile` = 1",
                    trim($member['first_name']),
                    trim($member['last_name']),
                    $member['phone_no'],
                    $member['organization_title'],
                    $_GLOBALS['member']['member_id']
                ));
                memberToDatabase($member);
                redirect('?result=y&action=update');
                die();
            }
        }
        die($twig->load('account-profile.twig')->render(['inputs' => [
            'member' => $_GLOBALS['member'],
            'result' => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'  => ['y' => _('Member details have been updated'), 'n' => _('Failed to update member record')][$_GET['result'] ?? ''] ?? '',
            'issues' => $issues,
        ]]));

    case 'shipping':
        moduleLoad('member'); //memberShippingAddressToDatabase, memberShippingAddressSetDefault, memberShippingAddressDelete

        $address = [
            'member_id'  => $_GLOBALS['member']['member_id'],
            'updated_by' => 0,
        ];
        $issues = [];
        if ('add' === ($_POST['action'] ?? '') && !count($issues)) {
            $address['default'] = 'y';
            foreach ([
                'street-address',
                //'country',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'organization'   => 'company_name',
                'street-address' => 'address',
                'address-level3' => 'address_2',
                'address-level2' => 'city',
                'address-level1' => 'state',
                'postal-code'    => 'post_code',
                'country'        => 'country',
                'home-tel'       => 'phone_no',
                'fax-tel'        => 'fax_no',
                'mobile-tel'     => 'mobile_no',
                'home-email'     => 'email',
                'phone_no2'      => 'phone_no2',
                'vat_number'     => 'vat_number',
                'default'        => 'default',
                'eori_number'    => 'eori_number',
                'abn_number'     => 'abn_number',
                'company_number' => 'company_number',
                'addresstype'    => 'address_type',
                'name-first'     => 'contact',
                'name-last'      => 'contact_last',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $address[$v] = trim($_POST[$k]);
                }
            }
            if (!count($issues)) {
                $shipaddr_id = memberShippingAddressToDatabase($_GLOBALS['member']['id'], $address);
                if ('y' == ($address['default'] ?? '')) {
                    memberShippingAddressSetDefault($_GLOBALS['member']['id'], $shipaddr_id);
                }

                redirect('?result=y&action=add');
                die();
            }
            redirect('?result=n&action=add');
            die();
        }
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['id'] ?? 0);
            foreach ($_GLOBALS['member']['ship_addr'] as $shipping_address) {
                if ($shipping_address['id'] == $id) {
                    $address['id'] = $id;
                    $address['default']     = 'y';
                    foreach ([
                        'street-address',
                        //'country',
                    ] as $k) {
                        if ('' === trim($_POST[$k] ?? '')) {
                            $issues[$k] = 'This field is required';
                        }
                    }
                    foreach ([
                        'organization'   => 'company_name',
                        'street-address' => 'address',
                        'address-level3' => 'address_2',
                        'address-level2' => 'city',
                        'address-level1' => 'state',
                        'postal-code'    => 'post_code',
                        'country'        => 'country',
                        'home-tel'       => 'phone_no',
                        'fax-tel'        => 'fax_no',
                        'mobile-tel'     => 'mobile_no',
                        'home-email'     => 'email',
                        'phone_no2'      => 'phone_no2',
                        'vat_number'     => 'vat_number',
                        'default'        => 'default',
                        'eori_number'    => 'eori_number',
                        'abn_number'     => 'abn_number',
                        'company_number' => 'company_number',
                        'addresstype'    => 'address_type',
                        'name-first' => 'contact',
                        'name-last'    => 'contact_last',
                    ] as $k => $v) {
                        if (array_key_exists($k, $_POST)) {
                            $address[$v] = $_POST[$k];
                        }
                    }
                    if (!count($issues)) {
                        $shipaddr_id = memberShippingAddressToDatabase($_GLOBALS['member']['id'], $address);
                        if ('y' == ($address['default'] ?? '')) {
                            memberShippingAddressSetDefault($_GLOBALS['member']['id'], $shipaddr_id);
                        }

                        redirect('?result=y&action=update');
                        die();
                    }
                }
            }
            redirect('?result=n&action=update');
            die();
        }
        if ('delete' === ($_POST['action'] ?? '') && !count($issues)) {
            $id = intval($_POST['id'] ?? 0);
            foreach ($_GLOBALS['member']['ship_addr'] as $shipping_address) {
                if ($shipping_address['id'] == $id) {
                    memberShippingAddressDelete($id);
                    redirect('?result=y&action=delete');
                    die();
                }
            }
            redirect('?result=n&action=delete');
            die();
        }
        die($twig->load('account-shipping.twig')->render(['inputs' => [
            'addresses' => $_GLOBALS['member']['ship_addr'],
            'member' => $_GLOBALS['member'],
            'result'    => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'     => [
                'add'    => ['y' => _('New shipping address has been added'), 'n' => _('Failed to add shipping address')],
                'update' => ['y' => _('Shipping address has been updated'), 'n' => _('Failed to update shipping address')],
                'delete' => ['y' => _('Shipping address was removed'), 'n' => _('Failed to remove shipping address')],
            ][$_GET['action'] ?? ''][$_GET['result'] ?? ''] ?? '',
            'issues'    => $issues,
        ]]));

    case 'inquiries':
        die($twig->load('account-inquiries.twig')->render(['inputs' => [
            'orders' => TwigInvoice::search([
                'member_id'      => $_GLOBALS['member']['member_id'],
                'invoice_status' => ['inquiry', 'closed', 'cancelled', 'return'],
            ]),
            'page'   => intval($_GET['p'] ?? 0),
        ]]));
    case 'invoice':
        moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceProductsFromDatabase, invoiceFromDatabase
        if($_GLOBALS['member']['member_id'] && !isset($_GET['crc'])){
            $invoices = TwigInvoice::search([
                'invoice_id'     => intval($_GET['invoice_id']),
                'member_id'      => $_GLOBALS['member']['member_id']
            ]);
        }else{
            $invoices = TwigInvoice::search([
                'invoice_id'     => intval($_GET['invoice_id']),
                'rand_num'   => $_GET['crc'] ?? 0,
            ]);
        }
        if (count($invoices)) {
            //No browser-side caching
            header("Cache-Control: no-cache, no-store, must-revalidate", true); // HTTP 1.1.
            header("Pragma: no-cache", true); // HTTP 1.0.
            header("Expires: 0", true); // Proxies.
            header('Content-Type: application/pdf');

            $invoice_id       = reset($invoices)->id;
            $invoice          = invoiceFromDatabase($invoice_id);
            $invoice_products = invoiceProductsFromDatabase($invoice_id);
            $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
            $invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
            if ($invoice['site_id'] <= 0) {
                $invoice['site_id'] = 3;
            }

            $invoice_buffer = '';
            include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/templates/pdf_invoice.tpl.php';
            echo $invoice_buffer;
            die();
        }
        break;
     case 'invoicepdf':
        moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceProductsFromDatabase, invoiceFromDatabase
        $invoice_id= openssl_decrypt ($_GET['invoice_id'], "AES-128-CTR",
            "arcoodainc", 0, '1234567891011121');
        $invoices = TwigInvoice::search([
            'invoice_id'     => intval($invoice_id),
        ]);
        if (count($invoices)) {
            //No browser-side caching
            header("Cache-Control: no-cache, no-store, must-revalidate", true); // HTTP 1.1.
            header("Pragma: no-cache", true); // HTTP 1.0.
            header("Expires: 0", true); // Proxies.
            header('Content-Type: application/pdf');

            $invoice_id       = reset($invoices)->id;
            $invoice          = invoiceFromDatabase($invoice_id);
            $invoice_products = invoiceProductsFromDatabase($invoice_id);
            $invoice_charges  = invoiceChargesFromDatabase($invoice_id);

            if ($invoice['site_id'] <= 0) {
                $invoice['site_id'] = 3;
            }

            $invoice_buffer = '';
            include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/templates/pdf_invoice.tpl.php';
            echo $invoice_buffer;
            die();
        }
        break;
    case 'orders':
        moduleLoad('invoice'); //invoiceHistoryToDatabase
        moduleLoad('product'); //productFromDatabase

        $issues = [];
        if ('reorder' === ($_POST['action'] ?? '') && !count($issues)) {
            if (!empty($_POST)) {
                $product_array = [];
                foreach ($_POST['product'] as $key => $productdata) {
                    if ('on' == $productdata['product_checked']) {
                        array_push($product_array, $productdata);
                    }
                }
                if (count($product_array)) {
                    include_once __DIR__ . '/../webadmin/products_new/query.inc.php';
                    foreach ($product_array as $key => $pro) {
                        $product_qty = 0;
                        $product_id  = $pro['product_id'];
                        if (isset($pro['product_qty']) && empty($pro['product_qty']) === false) {
                            $product_qty = intval($pro['product_qty']);
                        }
                        if (productExists($product_id)) {
                            $product               = productFromDatabase($product_id);
                            $product['product_id'] = $product['id'];
                            // Hack:
                            // sub_id, back_qty, power_id, adapter_id not exist in product
                            // But, used in cart. Throwing errors.
                            $product['sub_id']     = 0;
                            $product['back_qty']   = 0;
                            $product['colour_id']  = (isset($pro['color_cart']) && $pro['color_cart'] > 0) ? $pro['color_cart'] : 0;
                            $product['power_id']   = 0;
                            $product['adapter_id'] = 0;
                            $curentprice           = $product['sales_price'];
                            if (array_key_exists('member', $_GLOBALS)) {
                                $user = new TwigUser($_GLOBALS['member']['member_id']);
                                if ($user->wholesale() == 'y') {
                                    $bulkprice = productBulkPricingFromDatabase($product['id']);

                                    if (count($bulkprice)) {
                                        foreach ($bulkprice as $bulkdata) {
                                            if ($product_qty >= $bulkdata['qty'] && $curentprice > $bulkdata['unit_sale_price']) {
                                                $curentprice = number_format((float) $bulkdata['unit_sale_price'], 2, '.', '');
                                            }
                                        }
                                    }
                                }
                            }
                            $product['sales_price'] = $curentprice;
                            if ('n' != $product['active']) {
                                $cart = $_GLOBALS['shopping_cart'];
                                if ($product_qty < 0) {
                                    $cart = cartAddToContents($cart, $product, 0 - $product_qty);
                                }
                                if ($product_qty > 0) {
                                    $cart = cartAddToContents($cart, $product, $product_qty);
                                }
                                if (0 == $product_qty) {
                                    $cart = cartAddToContents($cart, $product, 1);
                                }
                                foreach ($cart as $cart_key => $cart_item) {
                                    $min_order = productGetValue($cart_item['product_id'], 'min_order');
                                    if ($cart_item['product_qty'] < $min_order) {
                                        $cart_item['product_qty']       = $min_order;
                                        $cart[$cart_key]['product_qty'] = $min_order;
                                    }
                                    $cart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                                    $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                                    $cart[$cart_key]['offer_currency']   = 'USD';
                                }
                                $_GLOBALS['shopping_cart'] = $cart;
                                cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
                            }
                        }
                    }
                    redirect('/cart');
                } else {
                    redirect($_SERVER['HTTP_REFERER'].'?result=n&msg=No any product selected');
                    die();
                }
            }
        }
        if ('contact' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'message',
                'subject',
                'id',
                'order_id',
                //'country',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            if (!count($issues)) {
                $history                    = [];
                $history['message_sender']  = $_GLOBALS['member']['member_id'];
                $history['message_sentto']  = 'System Admin';
                $history['message_title']   = $_POST['order_id'] . ' ' . $_POST['subject'];
                $history['message_type_id'] = 3;
                $history['message_notes']   = $_POST['message'];
                $history['message_raw']     = $_POST['message'];
                invoiceHistoryToDatabase($_POST['id'], $history);
                $invoices = TwigInvoice::search([
                    'invoice_id'     => intval($_POST['id']),
                    'member_id'      => $_GLOBALS['member']['member_id']
                ]);
                $invoices=reset($invoices);
                ob_start();
                 include __DIR__ . '/../forms/mail.template.php';
                $message = ob_get_contents();
                $email           = new PHPMailerEmail();
                $email->Subject  = 'New Message - Invoice '.$_POST['order_id'] . ' - ' . $_POST['subject'];
                $email->From     = $_GLOBALS['site_settings']['email_sales'];
                $email->FromName = 'Alert - Dashboard';
                //$email->Body     = sprintf(ParsedownExtra::instance()->setBreaksEnabled(true)->text($message), htmlspecialchars($history['message_raw']));
                $email->Body=$message;
                $email->AltBody  =$message;// sprintf($message, $history['message_raw']);
                $email->AddAddress($_GLOBALS['site_settings']['email_sales'], $_GLOBALS['site_settings']['site_name']);
                // $email->AddAddress('vivek@yopmail.com', 'Arcooda');
                $email->Send();
                redirect($_SERVER['HTTP_REFERER'].'?result=y&msg=Your message was sent successfully');
            } else {
                redirect($_SERVER['HTTP_REFERER'].'?result=n&msg=Failed to send contact request');
            }
            die();
        }
        if ('cancelorder' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'message',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            if(!count($issues)) {
                $invoice=TwigInvoice::search([
                    'member_id'      => $_GLOBALS['member']['member_id'],
                    'invoice_id' => intval($_POST['id']),
                ]);
                if($invoice){
                    invoiceSetValue($_POST['id'],'invoice_action','cancelled');
                    invoiceSetValue($_POST['id'],'invoice_status','closed');
                    if(isset($_POST['message']) && !empty($_POST['message'])){
                        $history                    = [];
                        $history['message_sender']  = $_GLOBALS['member']['member_id'];
                        $history['message_sentto']  = 'System Admin';
                        $history['message_title']   = $_POST['cancel_order_id'] . ' Order cancelled';
                        $history['message_type_id'] = 3;
                        $history['message_notes']   = $_POST['message'];
                        $history['message_raw']     = $_POST['message'];
                        invoiceHistoryToDatabase($_POST['id'], $history);

                        $invoice_id       = $_POST['id'];
                        $invoice          = invoiceFromDatabase($_POST['id']);
                        $invoice_products = invoiceProductsFromDatabase($_POST['id']);
                        $invoice_charges  = invoiceChargesFromDatabase($_POST['id']);

                        if ('' != $invoice['paymenttype_id']) {
                            $payment_types = getPaymentMethodsList();
                            $payment_data  = $DB->query_first(sprintf(
                                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
                                $invoice['site_id'],
                                formatSQLString($invoice['paymenttype_id'])
                            ), MYSQLI_ASSOC);
                            $payment_method = $payment_types[$invoice['paymenttype_id']];
                            if ('' != $payment_data['payment_method_details']) {
                                // $results['[PAYMENT_DETAILS]']      = $payment_data['payment_method_details'];
                                $payment_details = nl2br(htmlspecialchars($payment_data['payment_method_details']));
                            }
                        }

                        $payment_status      = '<span style="color:red;"><b>Awaiting</b></span>';
                        if ("payment" == $invoice["invoice_action"]) {
                            $payment_status = '<span style="color:red;"><b>Awaiting Payment</b></span>';
                        }
                        if("approval" == $invoice['invoice_action']) {
                            $payment_status = '<span style="color:green;"><b>Awaiting Payment Approval</b></span>';
                        }
                        if ("y" == $invoice["payments_located"]) {
                            $payment_status = '<span style="color:green;"><b>Approved</b></span>';
                        }

                        

                        $payment_method = $payment_method ?? '';

                        $payment_details = $payment_details ?? '';

                        ob_start();
                        ?>
                        <p><?= $_POST['message'] ?></p>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:50%">Bill To Address</th>
                                <th>Ship To Address</th>
                            </tr>
                            <tr>
                                <td><?= nl2br(hecho($invoice['bill_address_full'], 1)) ?></td>
                                <td><?= nl2br(hecho($invoice['ship_address_full'], 1)) ?></td>
                            </tr>
                        </table>
                
                        <table class="table table-bordered">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Price Each</th>
                                <th>Amount</th>
                            </tr>
                        <?php
                        foreach ($invoice_products as $invoice_product) {
                        ?>
                            <tr>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_id']) ?></td>
                                <td>
                                    <?php hecho($invoice_product['product_name']) ?>
                                    <p><i>Prouct ID: <?php hecho($invoice_product['product_id']) ?> - <?php hecho($invoice_product['product_name']) ?><br /><?= nl2br(hecho($invoice_product['product_notes'], 1)) ?></i></p>
                                </td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_qty']) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho(convertCurrency($invoice_product['product_offer'], $invoice['invoice_currency'], '', false, $invoice_product['decimal'])) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?= bcmul($invoice_product['product_offer'], $invoice_product['product_qty'], $invoice_product['decimal']) ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                            <tr>
                                <td colspan="3"></td>
                                <th>Sub-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['products_offer'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <th style="vertical-align:middle;text-align:center;">Grand-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['invoice_offer'] ?> <?= $invoice['invoice_currency'] ?></td>
                            </tr>
                        </table><br/><br/>
                        <table border="1" cellspacing="0" cellpadding="2" style="margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">
                            <tr>
                                <th style="background:#eee;color:#333">Payment Method :</th>
                                <td style="color:#777;"><?= $payment_method ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Payment Approval :</th>
                                <td style="color:#777;"><?= nl2br(htmlspecialchars_decode(htmlspecialchars($payment_status))) ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Additional Payment Details :</th>
                                <td style="color:#777;"><?= $payment_details ?></td>
                            </tr>
                        </table><br/><br/>
                
                        <p><b>Link: </b> <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=' . $invoice_id))?>">View Inquiry</a> . <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>">Edit Inquiry</a><a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>#sectionMemberNotes">Reply to Customer</a></p>
                        <?php
                        $message = ob_get_clean();
                        
                        $email           = new PHPMailerEmail();
                        $email->Subject  = 'Cancellation Request - Invoice '.$_POST['id'] ;
                        $email->From     = $_GLOBALS['site_settings']['email_sales'];
                        $email->FromName = 'Alert - Dashboard';
                        //$email->Body     = sprintf(ParsedownExtra::instance()->setBreaksEnabled(true)->text($message), htmlspecialchars($history['message_raw']));
                        $email->Body     = $message;
                        $email->AltBody  = $message;// sprintf($message, $history['message_raw']);
                        $email->AddAddress($_GLOBALS['site_settings']['email_sales'], $_GLOBALS['site_settings']['site_name']);
                        // $email->AddAddress('vivek@yopmail.com', 'Arcooda');
                        $email->Send();

                        invoiceSetValue($_POST['id'],'handler_note','Customer manually cancelled this order');
                    }
                    redirect($_SERVER['HTTP_REFERER'].'?result=y&msg= Order cancelled successfully');
                }else{
                    redirect($_SERVER['HTTP_REFERER'].'?result=n&msg=No data available for this invoice');
                }
                die();
            }
            
        }
        $msg = $_GET['msg'] ?? '';
        die($twig->load('account-orders.twig')->render(['inputs' => [
            'orders'  => TwigInvoice::search([
                'member_id'      => $_GLOBALS['member']['member_id'],
                'invoice_status' => ['proforma', 'sales', 'processed', 'completed','approval'],
            ]),
            'inquiry' => TwigInvoice::search([
                'member_id'      => $_GLOBALS['member']['member_id'],
                'invoice_status' => ['inquiry', 'closed', 'cancelled', 'return','approval'],
            ]),
            'result'  => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'   => ['y' => $msg, 'n' => $msg][$_GET['result'] ?? ''] ?? '',
            'issues'  => $issues,
            'page'    => intval($_GET['p'] ?? 0),
        ]]));

    case 'password':
        $issues = [];

        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'new-password',
                'confirm-password',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }

            if (!count($issues)) {
                if (strlen($_POST['new-password'] ?? '') < 8) {
                    $issues['new-password'] = 'Password must be 8 characters or more';
                } elseif (($_POST['confirm-password'] ?? '') !== $_POST['new-password']) {
                    $issues['new-password'] = 'Passwords do not match';
                }
            }

            /*
            if (!count($issues)) {
                // prevent UTF-8 characters being stripped by escapeshellarg
                setlocale(LC_ALL, 'en_US.utf-8');
                $out = [];
                $ret = null;
                exec(sprintf(
                    "echo %s | /usr/sbin/cracklib-check",
                    escapeshellarg($_POST['new-password'])
                ), $out, $ret);
                if ((0 == $ret) && preg_match("/: ([^:]+)$/", $out[0], $m) && 'OK' !== $m[1]) {
                    $issues['new-password'] = preg_replace('/^it /i', 'Password ', $m[1]);
                }
            }
            */

            if (!count($issues)) {
                //@todo something like memberLoginSetActivePassword($member_id, $_POST['new-password']);
                $DB->query(sprintf(
                    "UPDATE `highwaygroup`.`new_members_logins` SET `password` = %s, `active` = 'y' WHERE `member_id` = %d LIMIT 1",
                    $DB->escape(md5($_POST['new-password'])),
                    $_GLOBALS['member']['id']
                ));
                if ($DB->affected_rows() === 0) {
                    $DB->query(sprintf(
                        "INSERT INTO `highwaygroup`.`new_members_logins` (`member_id`,`email_address`, `password`, `active`)
                         VALUES (%d, %s, %s, 'y')",
                        $_GLOBALS['member']['id'],
                        $DB->escape($_GLOBALS['member']['email_address']),
                        $DB->escape(md5($_POST['new-password']))
                    ));
                }
                redirect('?result=y&action=update');
                die();
            }
        }

        die($twig->load('account-password.twig')->render(['inputs' => [
            'result' => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'  => ['y' => _('Password has been updated'), 'n' => _('Failed to update password')][$_GET['result'] ?? ''] ?? '',
            'issues' => $issues,
        ]]));

    case 'resetpassword':
        $login = [
            'member_id'  => $_GLOBALS['member']['member_id'],
            'updated_by' => 0,
        ];
        $issues = [];
        if ('update' === ($_POST['action'] ?? '') && !count($issues)) {
            if (strlen($_POST['new-password'] ?? '') < 8) {
                $issues['new-password'] = 'Password must be 8 characters or more';
            } elseif (($_POST['confirm-password'] ?? '') !== $_POST['new-password']) {
                $issues['new-password'] = 'Passwords do not match';
            } else {
                $login['password'] = md5($_POST['new-password'] ?? '');
                $login['active']   = 'y';
                foreach ([
                    'new-password',
                    'confirm-password',
                ] as $k) {
                    if ('' === trim($_POST[$k] ?? '')) {
                        $issues[$k] = 'This field is required';
                    }
                }
            }
            if (!count($issues)) {
                // prevent UTF-8 characters being stripped by escapeshellarg
                setlocale(LC_ALL, 'en_US.utf-8');
                $out = [];
                $ret = null;
                exec(sprintf(
                    "echo %s | /usr/sbin/cracklib-check",
                    escapeshellarg($_POST['new-password'])
                ), $out, $ret);
                if ((0 == $ret) && preg_match("/: ([^:]+)$/", $out[0], $m) && 'OK' !== $m[1]) {
                    $issues['new-password'] = preg_replace('/^it /i', 'Password ', $m[1]);
                }
            }
            if (!count($issues)) {
                $DB->query(sprintf(
                    "UPDATE `highwaygroup`.`new_members_logins` SET `password` = %s, `deleted_at` = NULL WHERE `member_id` = %d LIMIT 1",
                    $DB->escape($login['password']),
                    $_GLOBALS['member']['member_id']
                ));
                redirect($_REQUEST['redirect'] ?? '/account/dashboard');
                die();
            }
        }
        die($twig->load('account-resetpassword.twig')->render(['inputs' => [
            'result' => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'  => ['y' => _('Password has been updated'), 'n' => _('Failed to update password')][$_GET['result'] ?? ''] ?? '',
            'issues' => $issues,
        ]]));

    //

    // Not logged-in actions
    case 'autologin':
        moduleLoad('member'); //memberHasValue, memberFromDatabase, memberLogin

        if (isset($_GET['rand_num']) && isset($_GET['member_id'])) {
            $rand_num = intval($_GET['rand_num']);
            if (memberHasValue($_GET['member_id'], 'rand_num', $rand_num)) {
                $member  = memberFromDatabase($_GET['member_id']);
                if($member['link_expire_time']>time()){
                    memberLogin($_GET['member_id'] ?? 0, $rand_num ?? 0);
                    redirect('/account/password');
                    die();
                }else{
                     $rdirect['value']   = 'n';
                    $rdirect['message'] = 'Your reset password link has been expired. Please reset your password again.';
                    $data               = json_encode($rdirect);
                    setcookie('redirect', $data, strtotime('+1 year'), '/');
                    redirect('/');
                       die();
                }
            }
        }
        $rdirect['value']   = 'n';
        $rdirect['message'] = 'Your reset password link has been expired. Please reset your password again.';
        $data               = json_encode($rdirect);
        setcookie('redirect', $data, strtotime('+1 year'), '/');
        redirect('/');
        die();

    case 'login':
        $issues = [];
        if ('login' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'username',
                'current-password',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            if (!count($issues) && !login($_POST['username'] ?? '', $_POST['current-password'] ?? '')) {
                $issues['username'] = 'Wrong username or password';
            }
            if (!count($issues)) {
                redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '/');
                die();
            }
        }
        die($twig->load('account-login.twig')->render(['inputs' => [
            'result' => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'  => ['y' => _('Successfully logged in'), 'n' => _('Unable to login')][$_GET['result'] ?? ''] ?? '',
            'issues' => $issues,
        ]]));

    case 'passwordFind':
        moduleLoad('member'); //memberIdFromEmail, memberSetValue, memberUpdatePassword, memberFromDatabase

        $issues = [];
        if ('recover' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'username',
            ] as $k) {
                 if (!preg_match('/^.+@.+\\..+$/', $_POST[$k])) {
                    $issues['username'] = 'Please enter a valid Email Address';
                }
            }
            $member_id = memberIdFromEmail($_POST['username']) ?? 0;
            if (!$member_id) {
                $issues['username'] = 'The email address you entered is not in our database.<br /> please reconfirm. If you feel this is in error, please <a href="/contact-us/">contact us</a>';
            }
            if (!count($issues)) {
                //Build a new password
                $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@*?!(&.$+-#_';
                $password = '';
                $max      = strlen($keyspace) - 1;
                for ($i = 0; $i < 16; $i++) {
                    $password .= $keyspace[random_int(0, $max)];
                }
                $expire_time = strtotime('+2 hours');
                memberSetValue($member_id, 'rand_num', mt_rand(0, 2147483647));
                memberSetValue($member_id, 'link_expire_time',$expire_time);
                //memberUpdatePassword($password, $member_id);

                $member  = memberFromDatabase($member_id);
                $message = strtr($_GLOBALS['site_options']['RESET_PASS_EMAIL'], [
                    '[cust_name]' => $member['name'],
                    '[password]'  => $password,
                    '[link]'      => relative_url(sprintf(
                        '/account/autologin?member_id=%d&rand_num=%d',
                        $member_id,
                        $member['rand_num']
                    )),
                ] + $member);
                $email          = new PHPMailerEmail();
                $email->Subject = "Password Reset Request";
                $email->Body    = ParsedownExtra::instance()->setBreaksEnabled(true)->text($message);
                $email->AltBody = $message;
                $email->AddAddress($member['email_address'], $member['full_name']);
                $email->Send();

                redirect("?result=y&action=forgotpassword");
                die();
            }
            //redirect("?result=n&action=forgotpassword");
            //die();
        }
        //echo $issues['username']; die;
        die($twig->load('account-passwordFind.twig')->render(['inputs' => [
            'result' => [
                'y' => 'success',
            ][$_GET['result'] ?? ''] ?? '',
            'alert'  => [
                'y' => _("Your reset password request has been sent. Please check your email and follow the link to reset new password.")
            ][$_GET['result'] ?? ''] ?? '',
            'issues' => $issues,
        ]]));

    case 'register':
        moduleLoad('member'); //memberContactsToDatabase, memberNotifyPartiesToDatabase, memberShippingAddressesToDatabase, memberGetOptionList, memberToDatabase, memberLoginsToDatabase, memberCouriersToDatabase, memberTemplateVars

        $issues        = [];

        /**
         * @var mixed[]   $member
         * @var mixed[][] $member_contacts
         * @var mixed[][] $member_logins
         * @var mixed[][] $member_notify
         * @var mixed[]   $issues
         */
        include __DIR__ . '/../build_member.inc.php';

        if ('register' === ($_POST['action'] ?? '')) {
            if (!count($issues)) {
                if (memberIdFromEmail($_POST['username'])) {
                    $issues['username'] = 'Email address already exists. <a href="/account/passwordFind">Recover Account</a>';
                }
            }

            if (!count($issues)) {
                $member_id = memberToDatabase($member);
                if (!$member_id) {
                    $issues['action'] = 'There was an unknown error while processing your registration';
                }
            }

            if (!count($issues)) {
                $member = memberFromDatabase($member_id);
                memberContactsToDatabase($member_id, $member_contacts);
                memberLoginsToDatabase($member_id, $member_logins);
                memberNotifyPartiesToDatabase($member_id, $member_notifyparties);
                memberShippingAddressesToDatabase($member_id, $member_shippingaddresses);
                memberCouriersToDatabase($member_id, $member_couriers);
                $shop_cart = cartLoadContents(0, $_GLOBALS['visitor']['visitor_id'] ?? 0, 'shopping');
                login($_REQUEST['username'], $_REQUEST['newPassword']);
                $message = strtr(
                    $_GLOBALS['site_options']['SIGNUP_SUCCESS_EMAIL'],
                    [
                        '[cust_name]' => $member['full_name'],
                        '[username]'  => $member['email_address'],
                        '[password]'  => $_REQUEST['newPassword'],
                    ] + memberTemplateVars($member)
                );

                $email          = new PHPMailerEmail();
                $email->Subject = "Registration Successful";
                $email->Body    = ParsedownExtra::instance()->setBreaksEnabled(true)->text($message);
                $email->AltBody = $message;
                $email->AddAddress($member['email_address'], $member['full_name']);
                $email->Send();
                unset($_SESSION['rand_code']);
               
                if(count($shop_cart) > 0){
                    redirect('/checkout');
                }else{
                    redirect('/account/dashboard'); 
                }

                die();
            }
        }
        if(count($posted)==0){
            $posted['billing_as_shipping']='y';
            $posted['own_freight']='n';
        }

        $courier_list = getFreightServices('personal', '', true);
        
        die($twig->load('account-register.twig')->render(['inputs' => [
            'issues'        => $issues,
            'model'         => $posted,
            'courier_list'  => memberGetOptionList('courier_id'),
            'currency_list' => GlobalCurrencyList(),
            'courier_list'  => $courier_list,
        ]]));

    case 'news-letter':
        moduleLoad('member'); //memberToDatabase

        if (($_POST['action'] ?? '') === 'newsletter' && !empty($_POST['newsletter-address'])) {
            if ($_GLOBALS['member']['member_id']) {
                $member = [
                    'member_id'         => $_GLOBALS['member']['member_id'],
                    'updated_by'        => 0,
                    'news_letter_email' => $_POST['newsletter-address'],
                ];
                memberToDatabase($member);

                //@fixme This code is incorrect, newsletter subscriptions are handled via the newsletter module
                /*
                $prefs             = [];
                $prefs['customer'] = 'y';
                memberPreferencesToDatabase($_GLOBALS['member']['member_id'], $prefs);
                */

                $rdirect['value']   = 'y';
                $rdirect['message'] = 'Thanks for subscribing';
                $data               = json_encode($rdirect);
                setcookie('redirect', $data, strtotime('+1 year'), '/');
            } else {
                $issues = [];
                if ($row = $DB->query_first(sprintf(
                    "SELECT `member_id` FROM `highwaygroup`.`new_members` WHERE `email_address` = %s LIMIT 1",
                    $DB->escape($_POST['newsletter-address'])
                ), MYSQLI_ASSOC)) {
                    $issues['error'] = 'Email address already exists,Please login for news letter';
                }
                if (!count($issues)) {
                    die($twig->load('account-newsletter.twig')->render(['inputs' => [
                        'email' => $_POST['newsletter-address'],
                    ]]));
                } else {
                    $rdirect['value']   = 'n';
                    $rdirect['message'] = $issues['error'];
                    $data               = json_encode($rdirect);
                    setcookie('redirect', $data, strtotime('+1 year'), '/');
                }
            }
        } else {
            $rdirect['value']   = 'n';
            $rdirect['message'] = 'Not aurthorised url';
            $data               = json_encode($rdirect);
            setcookie('redirect', $data, strtotime('+1 year'), '/');
        }
        redirect('/');
        die();

    case 'newsletter-reg':
        moduleLoad('member'); //memberContactsToDatabase, memberNotifyPartiesToDatabase, memberShippingAddressesToDatabase, memberToDatabase, memberFromDatabase, memberLoginsToDatabase

        $issues = [];

        /**
         * @var mixed[] $member
         * @var mixed[][] $member_contacts
         * @var mixed[][] $member_logins
         * @var mixed[][] $member_notify
         * @var mixed[] $issues
         */
        include __DIR__ . '/../build_member.inc.php';

        if ('newsletter' === ($_POST['action'] ?? '') && !count($issues)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT `member_id` FROM `highwaygroup`.`new_members` WHERE `email_address` = %s LIMIT 1",
                $DB->escape($member['email_address'])
            ), MYSQLI_ASSOC)) {
                $issues['username'] = 'Email address already exists,Please login for news letter';
            }
        }
        if ('newsletter' === ($_POST['action'] ?? '') && !count($issues)) {
            $member_id = memberToDatabase($member);
            if ($member_id) {
                $member = memberFromDatabase($member_id);
                memberContactsToDatabase($member_id, $member_contacts);
                memberLoginsToDatabase($member_id, $member_logins);
                memberNotifyPartiesToDatabase($member_id, $member_notifyparties);

                //@fixme This code is incorrect, newsletter subscriptions are handled via the newsletter module
                /*
                $prefs             = [];
                $prefs['customer'] = 'y';
                memberPreferencesToDatabase($member_id, $prefs);
                */

                //memberShippingAddressesToDatabase($member_id, $member_shippingaddresses);
                //login($_REQUEST['username'], $_REQUEST['new-password']);
                $rdirect['value']   = 'y';
                $rdirect['message'] = 'Thanks for subscribing';
                $data               = json_encode($rdirect);
                setcookie('redirect', $data, strtotime('+1 year'), '/');
                redirect('/');
                die();
            } else {
                $issues['action'] = 'There was an unknown error while processing your newsletter request';
            }
        }
        die($twig->load('account-newsletter.twig')->render(['inputs' => [
            'member' => $member,
            'issues' => $issues,
        ]]));
    case 'checkUsername' :

        global $DB;

        $username = $_POST['username'] ?? '';

        header('Content-Type: application/json');

        if (!preg_match('/^.+@.+\..+$/', $username)) {
            die(json_encode(false));
        }

        if ($DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members` WHERE `email_address` = %s LIMIT 1",
            $DB->escape($username)
        ), MYSQLI_ASSOC)) {
            die(json_encode(false));
        }

        die(json_encode(true));
    
    case 'inquiry' :
        if ('inquiry' === ($_POST['action'] ?? ''))
        {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secret_key = '6Ld2WYEnAAAAAL-bqCPD653IS2Exp7V_qEieGwAx';
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                  . $secret_key . '&response=' . $recaptcha;
            $response = file_get_contents($url);
            $response = json_decode($response);
            $bHasLink = strpos($_POST['comments'], 'http') !== false || strpos($_POST['comments'], 'www.') !== false;
            if ($response->success !=1 || $bHasLink) {
                $rdirect['value']   = 'n';
                $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
                $data               = json_encode($rdirect);
                setcookie('redirect', $data, strtotime('+1 year'), '/');
               $rdirect['value']   = 'n';
                $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
                $data               = json_encode($rdirect);
                setcookie('redirect', $data, strtotime('+1 year'), '/');
                redirect('/');
                die();  
            }
            moduleLoad('invoice'); //invoiceHistoryToDatabase, invoiceProductsToDatabase, invoiceProductsFromDatabase, invoiceToDatabase, invoiceFromDatabase, invoiceGetOptionList
            moduleLoad('member'); //memberIdFromEmail, memberFromDatabase, memberGetOptionList
            moduleLoad('product'); //productFromDatabase
            moduleLoad('notify'); //notifyActionMade
            $invoice_currency  = 'USD';
            $_GLOBALS["site_options"]["DEFAULT_CURRENCY"] = 'USD';
            include __DIR__ . '/../../contact/build_member.inc.php';
            if (!array_key_exists('member', $_GLOBALS)) {
                $member_email=$_REQUEST['member_email'];
                $member_id = memberIdFromEmail($member_email);
                if ($member_id) {
                    $_GLOBALS['member'] = memberFromDatabase($member_id);
                } else {
                    $member_id = memberToDatabase($member);
                    $member = memberFromDatabase($member_id);
                    $_GLOBALS['member'] = $member;

                    // Add member-detail to shipping address on 4th sept by swapnil

                    $address = [
                        'member_id'  => $_GLOBALS['member']['member_id'],
                        'updated_by' => 0,
                        'default'    => 'y',
                    ];

                    foreach ([
                        'member_location'=> 'country',
                        'member_email'   => 'email',
                        'default'        => 'default',
                        'given-name'    => 'contact',
                        'family-name'    => 'contact_last',
                    ] as $k => $v) {
                        if (array_key_exists($k, $_POST)) {
                            $address[$v] = trim($_POST[$k]);
                        }
                    }

                    $shipaddr_id = memberShippingAddressToDatabase($_GLOBALS['member']['id'], $address);
                    if ($shipaddr_id) {
                        $address['id']=$shipaddr_id;
                        $_GLOBALS['member']['ship_addr']=$address;
                    }
                    // end

                }
            }
            if($_GLOBALS['member']['spammer']=='y'){
                $rdirect['value']   = 'n';
                $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
                $data               = json_encode($rdirect);
                setcookie('redirect', $data, strtotime('+1 year'), '/');
                redirect('/');
                die();
            }
            include __DIR__ . '/../../contact/build_invoice.inc.php';
            $invoice_id = invoiceToDatabase($invoice);
            invoiceProductsToDatabase($invoice_id, $invoice_products);

            $invoice = invoiceFromDatabase($invoice_id);
            $invoice_products = invoiceProductsFromDatabase($invoice_id);

            $invoice_template_vars = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);
             $invoice_template_vars['[CONTACTUS_SUBJECT]'] = $posted['product_name'];
            /**
             * Send notification for member
             */
            if (function_exists("notifyActionMade")) {
                notifyActionMade(CONTACTUS_INQUIRY_CREATED, $_GLOBALS["site_settings"]["site_id"], $invoice_template_vars);
            }

            // Check if it's desktop or mobile
            $device = (isset($_GET['popup']) && isset($_GET['mobile']) && 'false' == $_GET['mobile']) ? "Desktop" : "Mobile";

            invoiceHistoryToDatabase($invoice_id, array(
                "message_sender" => $device,
                "message_sentto" => 'Inquiry System',
                "message_title"  => 'Added New Admin Note',
                "message_notes"  => implode("\r\n", array(
                    "Product Inquiry from " . $device,
                    "*******************************",
                    "Customer name : " . ucfirst($member_name),
                    "Customer email: " . $member_email,
                    "Phone Number  : " . $member_phone,
                    "",
                    "MESSAGE",
                    "=================",
                    "",
                    $_REQUEST['customer_requests']
                )),
                "message_raw"    => implode("\r\n", array(
                    "Product Inquiry from " . $device,
                    "*******************************",
                    "Customer name : " . ucfirst($member_name),
                    "Customer email: " . $member_email,
                    "Phone Number  : " . $member_phone,
                    "",
                    "MESSAGE",
                    "=================",
                    "",
                    $_REQUEST['customer_requests']
                )),
            )); 
            if(!empty($invoice['customer_requests'])){
                $history                    = [];
                $history['message_sender']  = $_GLOBALS['member']['member_id'];
                $history['message_sentto']  = 'System Admin';
                $history['message_title']   = $invoice['invoice_number'] . ' Created';
                $history['message_type_id'] = 3;
                $history['message_notes']   = $invoice['customer_requests'];
                $history['message_raw']     = $invoice['customer_requests'];
                invoiceHistoryToDatabase($invoice_id, $history);
            }
            $subject = 'Product Inquiry ID=' . $product_id;
            $country_list = invoiceGetOptionList('bill_country');
            ob_start();
        ?>
        <p>Order Status: <span style="color:#f00;">Inquiry Created</span></p>
        <p>A new inquiry has been created. The details are as follow:</p>
        <table class="table table-bordered">
            <tr>
                <th style="width:50%">Bill To Address</th>
                <th>Ship To Address</th>
            </tr>
            <tr>
                <td><?= nl2br(hecho($invoice['bill_address_full'], 1)) ?></td>
                <td><?= nl2br(hecho($invoice['ship_address_full'], 1)) ?></td>
            </tr>
        </table>

        <table class="table table-bordered">
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Qty</th>
                <th>Price Each</th>
                <th>Amount</th>
            </tr>
        <?php
        foreach ($invoice_products as $invoice_product) {
        ?>
            <tr>
                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_id']) ?></td>
                <td>
                    <?php hecho($invoice_product['product_name']) ?>
                    <p><i>Prouct ID: <?php hecho($invoice_product['product_id']) ?> - <?php hecho($invoice_product['product_name']) ?><br /><?= nl2br(hecho($invoice_product['product_notes'], 1)) ?></i></p>
                </td>
                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_qty']) ?></td>
                <td style="vertical-align:middle;text-align:center;"><?php hecho(convertCurrency($invoice_product['product_offer'], $invoice['invoice_currency'], '', false, $invoice_product['decimal'])) ?></td>
                <td style="vertical-align:middle;text-align:center;"><?= bcmul($invoice_product['product_offer'], $invoice_product['product_qty'], $invoice_product['decimal']) ?></td>
            </tr>
        <?php
        }
        ?>
            <tr>
                <td colspan="3"></td>
                <th>Sub-Total:</th>
                <td style="vertical-align:middle;text-align:center;"><?= $invoice['products_offer'] ?></td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <th style="vertical-align:middle;text-align:center;">Grand-Total:</th>
                <td style="vertical-align:middle;text-align:center;"><?= $invoice['invoice_offer'] ?> <?= $invoice['invoice_currency'] ?></td>
            </tr>
        </table>

        <p><b>Link: </b> <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=' . $invoice_id))?>">View Inquiry</a> . <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>">Edit Inquiry</a><a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>#sectionMemberNotes">Reply to Customer</a></p>
        <?php
            $message = ob_get_clean();
            //mail("sales@arcooda.com", $subject, $message);

           /* $email           = new PHPMailerEmail();
            $email->From     = $_GLOBALS['site_settings']['email_sales'];
            $email->FromName = 'Alert – Contact Page Inquiry';
            //$email->Subject  = $product['product_name'] . " (ID = " . $product_id . ") - New Inquiry Q" . $inquiry_id;
            $email->Subject  = "New Contact Page Inquiry - " . $invoice['invoice_number']; //.' - '.$product['product_name'];
            $email->Body     = $message;
            $email->IsHtml(true);
            $email->AddAddress('sales@arcooda.com', $_GLOBALS['site_settings']['site_name']);
            $result = $email->Send();*/

            header("Location: /cart/thank-you?invoice_number=" . $invoice_id);
        }
        die($twig->load('mobile/mobile-inquiry.twig')->render(['inputs' => [
            'title'            => 'Inquiry',
            'member'           => $_GLOBALS['member'],
        ]]));
     case 'captchaimage' :
        session_start();
        $num1=rand(1,9); //Generate First number between 1 and 9  
        $num2=rand(1,9); //Generate Second number between 1 and 9  
        $captcha_total=$num1+$num2;  

        $math = "$num1"." + "."$num2"." =";  

        $_SESSION['rand_code'] = $captcha_total;

        $height = 25; //CAPTCHA image height
        $width = 80; //CAPTCHA image width

        $image_p = imagecreate($width, $height);
        $black = imagecolorallocate($image_p, 0, 0, 0);
        $white = imagecolorallocate($image_p, 255, 255, 255);
        $font_size = 20; 
        imagestring($image_p, $font_size, 5, 3, $math, $white);
        header("Content-type: image/png");
        imagejpeg($image_p, null, 80);
        die();
    case 'testemailissue':
                $email          = new PHPMailerEmail();
                $email->SMTPDebug = 2; 
                $email->Subject = "Password Reset Request";
                $email->Body    = 'reste password';
                $email->AltBody = 'reste password';
                $email->AddAddress('vivek85agrawal@hotmail.com','vivek85agrawal');
                $result = $email->Send();

                if (!$result) {
                    echo "Mailer Error: " . $email->ErrorInfo;
                } else {
                    echo "Message sent successfully.";
                }
            /*$email = new PHPMailerEmail();

            // Enable SMTP debugging
            $email->SMTPDebug = 2; // 0 = off, 1 = commands, 2 = commands + data

            // Set to use SMTP explicitly
            $email->IsSMTP();
            $email->From     = $_GLOBALS['site_settings']['email_sales'];
            $email->FromName = 'Alert – Contact Page Inquiry';
            $email->Subject  = "test mail";
            $email->Body     = "test mail for arcooda";
            $email->IsHTML(true);
            $email->AddAddress('vivek85agrawal@hotmail.com', 'vivek Agrawal');

            $result = $email->Send();

            if (!$result) {
                echo "Mailer Error: " . $email->ErrorInfo;
            } else {
                echo "Message sent successfully.";
            }*/
         die();
    case 'dashboard-new':
        $issues               = [];
        if ('cancelorder' === ($_POST['action'] ?? '') && !count($issues)) {
            foreach ([
                'message',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            if(!count($issues)) {
                $invoice=TwigInvoice::search([
                    'member_id'      => $_GLOBALS['member']['member_id'],
                    'invoice_id' => intval($_POST['id']),
                ]);
                if($invoice){
                    invoiceSetValue($_POST['id'],'invoice_action','cancelled');
                    invoiceSetValue($_POST['id'],'invoice_status','closed');
                    if(isset($_POST['message']) && !empty($_POST['message'])){
                        $history                    = [];
                        $history['message_sender']  = $_GLOBALS['member']['member_id'];
                        $history['message_sentto']  = 'System Admin';
                        $history['message_title']   = $_POST['cancel_order_id'] . ' Order cancelled';
                        $history['message_type_id'] = 3;
                        $history['message_notes']   = $_POST['message'];
                        $history['message_raw']     = $_POST['message'];
                        invoiceHistoryToDatabase($_POST['id'], $history);

                        $invoice_id       = $_POST['id'];
                        $invoice          = invoiceFromDatabase($_POST['id']);
                        $invoice_products = invoiceProductsFromDatabase($_POST['id']);
                        $invoice_charges  = invoiceChargesFromDatabase($_POST['id']);

                        if ('' != $invoice['paymenttype_id']) {
                            $payment_types = getPaymentMethodsList();
                            $payment_data  = $DB->query_first(sprintf(
                                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
                                $invoice['site_id'],
                                formatSQLString($invoice['paymenttype_id'])
                            ), MYSQLI_ASSOC);
                            $payment_method = $payment_types[$invoice['paymenttype_id']];
                            if ('' != $payment_data['payment_method_details']) {
                                // $results['[PAYMENT_DETAILS]']      = $payment_data['payment_method_details'];
                                $payment_details = nl2br(htmlspecialchars($payment_data['payment_method_details']));
                            }
                        }

                        $payment_status      = '<span style="color:red;"><b>Awaiting</b></span>';
                        if ("payment" == $invoice["invoice_action"]) {
                            $payment_status = '<span style="color:red;"><b>Awaiting Payment</b></span>';
                        }
                        if("approval" == $invoice['invoice_action']) {
                            $payment_status = '<span style="color:green;"><b>Awaiting Payment Approval</b></span>';
                        }
                        if ("y" == $invoice["payments_located"]) {
                            $payment_status = '<span style="color:green;"><b>Approved</b></span>';
                        }

                        

                        $payment_method = $payment_method ?? '';

                        $payment_details = $payment_details ?? '';

                        ob_start();
                        ?>
                        <p><?= $_POST['message'] ?></p>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:50%">Bill To Address</th>
                                <th>Ship To Address</th>
                            </tr>
                            <tr>
                                <td><?= nl2br(hecho($invoice['bill_address_full'], 1)) ?></td>
                                <td><?= nl2br(hecho($invoice['ship_address_full'], 1)) ?></td>
                            </tr>
                        </table>
                
                        <table class="table table-bordered">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Price Each</th>
                                <th>Amount</th>
                            </tr>
                        <?php
                        foreach ($invoice_products as $invoice_product) {
                        ?>
                            <tr>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_id']) ?></td>
                                <td>
                                    <?php hecho($invoice_product['product_name']) ?>
                                    <p><i>Prouct ID: <?php hecho($invoice_product['product_id']) ?> - <?php hecho($invoice_product['product_name']) ?><br /><?= nl2br(hecho($invoice_product['product_notes'], 1)) ?></i></p>
                                </td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho($invoice_product['product_qty']) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?php hecho(convertCurrency($invoice_product['product_offer'], $invoice['invoice_currency'], '', false, $invoice_product['decimal'])) ?></td>
                                <td style="vertical-align:middle;text-align:center;"><?= bcmul($invoice_product['product_offer'], $invoice_product['product_qty'], $invoice_product['decimal']) ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                            <tr>
                                <td colspan="3"></td>
                                <th>Sub-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['products_offer'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <th style="vertical-align:middle;text-align:center;">Grand-Total:</th>
                                <td style="vertical-align:middle;text-align:center;"><?= $invoice['invoice_offer'] ?> <?= $invoice['invoice_currency'] ?></td>
                            </tr>
                        </table><br/><br/>
                        <table border="1" cellspacing="0" cellpadding="2" style="margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">
                            <tr>
                                <th style="background:#eee;color:#333">Payment Method :</th>
                                <td style="color:#777;"><?= $payment_method ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Payment Approval :</th>
                                <td style="color:#777;"><?= nl2br(htmlspecialchars_decode(htmlspecialchars($payment_status))) ?></td>
                            </tr>
                            <tr>
                                <th style="background:#eee;color:#333">Additional Payment Details :</th>
                                <td style="color:#777;"><?= $payment_details ?></td>
                            </tr>
                        </table><br/><br/>
                
                        <p><b>Link: </b> <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=' . $invoice_id))?>">View Inquiry</a> . <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>">Edit Inquiry</a><a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>#sectionMemberNotes">Reply to Customer</a></p>
                        <?php
                        $message = ob_get_clean();
                        
                        $email           = new PHPMailerEmail();
                        $email->Subject  = 'Cancellation Request - Invoice '.$_POST['id'] ;
                        $email->From     = $_GLOBALS['site_settings']['email_sales'];
                        $email->FromName = 'Alert - Dashboard';
                        //$email->Body     = sprintf(ParsedownExtra::instance()->setBreaksEnabled(true)->text($message), htmlspecialchars($history['message_raw']));
                        $email->Body     = $message;
                        $email->AltBody  = $message;// sprintf($message, $history['message_raw']);
                        $email->AddAddress($_GLOBALS['site_settings']['email_sales'], $_GLOBALS['site_settings']['site_name']);
                        // $email->AddAddress('vivek@yopmail.com', 'Arcooda');
                        $email->Send();

                        invoiceSetValue($_POST['id'],'handler_note','Customer manually cancelled this order');
                    }
                    redirect($_SERVER['HTTP_REFERER'].'?result=y&msg= Order cancelled successfully');
                }else{
                    redirect($_SERVER['HTTP_REFERER'].'?result=n&msg=No data available for this invoice');
                }
                die();
            }
            
        }
        $seven_days_ago = strtotime('-7 days');
        $msg = ($_GET['msg']) ? $_GET['msg'] : '';
        $currOrders = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_status' => array('proforma', 'sales','processed','completed','inquiry','cancelled','return'),
            'time_from' => $seven_days_ago,
        ]);
        $oldOrders = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_status' => array('closed', 'proforma', 'sales','processed','completed','inquiry','cancelled','return'),
            'time_till' => $seven_days_ago,
        ]);
        die($twig->load('account-dashboard-new.twig')->render(['inputs' => [
            'addresses' => $_GLOBALS['member']['ship_addr'],
            'member'    => $_GLOBALS['member'],
            'currOrders'=> $currOrders,
            'oldOrders' => $oldOrders,
            'result'    => ['y' => 'success', 'n' => 'danger'][$_GET['result'] ?? ''] ?? '',
            'alert'     => ['y' => $msg, 'n' => $msg][$_GET['result'] ?? ''] ?? '',
        ]]));
    
    case "reviews":
        moduleLoad('review');

        if(($_POST['action'] ==  'add' || $_POST['action'] == 'do_edit') && $_GLOBALS['member']['member_id'])
        {
            $posted['user_id'] = $_GLOBALS['member']['member_id'];
            $posted['user_name'] = strtoupper(substr($_GLOBALS['member']['first_name'], 0, 1)) . strtoupper(substr($_GLOBALS['member']['last_name'], 0, 1));
            $posted['user_country'] = $_GLOBALS['member']['country'];

            $data = '';
            $issues = array();
            foreach ([
                'product_id',
                'reviews',
                'comment',
            ] as $k) {
                if ('' === trim($posted ?? '')) {
                    $issues[$k] = '' . str_replace('_',' ', $k) . ' field is required';
                }
            }

            if(count($issues)==0){
                $posted = reviewFromPost($posted);
                $posted['item'] = $posted;
                $posted['action'] = $_POST['action'] == 'add' ? 'do_add' : 'do_edit';
                // echo"<pre>";print_r($posted); die;
                $reviewId = reviewToDatabase($posted);
                    if (!$reviewId) {
                        $issues['db_error'] = 'Database insertion failed: ' . $DB->error;
                        $message['success']  = 'n';
                    }
                if($posted['action'] == 'do_edit' && isset($_POST['attachmentsremoved']) && count($_POST['attachmentsremoved']) > 0){
                    foreach ($_POST['attachmentsremoved'] as $key => $id) {
                        reviewGenericDelete($id, '`product_reviews_images`','id');
                    }
                }
                $message['success']  = 'y';
            } else {
                $message['success']  = 'n';
            }
        }
        if ('delete' === ($_POST['action'] ?? '')) {
            $id = intval($_POST['id'] ?? 0);
            reviewDeleteFromDatabase($id);
            $message['success']  = 'y';
        }
        $reviews = memberProductReview($_GET['page'] ?? 0);
        // echo '<pre>'; print_r($reviews); die;
        foreach ($reviews['product'] as $key => $v) {
            $product =new TwigProduct($v['pro_id']);
            $reviews['product'][$key]['productObject'] = $product;
                if($v['id']){
                    $screenshot =  reviewGenericsFromDatabase($v['id'],'`product_reviews_images`','id','review_id');
                    if(isset($screenshot) && is_array($screenshot)){
                            foreach ($screenshot as $k => $value) {
                                $screenshot[$k]['url']= sprintf(
                                    '/reviewimage/%s-%s-%s-%d-%d.jpg',
                                    $product->slug,
                                    'review',
                                    $v['user_country'] ? getCountryName($v['user_country']) : 'Australia',
                                    $v['pro_id'],
                                    $value['id']
                                );  
                        }
                    }
                }
                    $reviews['product'][$key]['attachments'] = $screenshot ?? [];
        }
        // echo "<pre>";print_r($reviews); die;
        die($twig->load('account-reviews.twig')->render(['inputs' => [
            'product' => $reviews['product'],
            'pages'   => $reviews['pages'],
            'page'    => $reviews['pages'],
            'result'   => ['y' => 'success', 'n' => 'danger'][$message['success'] ?? ''] ,
            'alert'  => [
                            'y' => ($posted['id']) ?($_POST['action'] == 'delete' ?"Review deleted successfully":"Your review updated successfully"): 'Thank you for your review',
                            'n' => "Failed to submit your review"
                        ][$message['success'] ?? ''],
            'issues' => $issues,
        ]]));
    case "getReview":
        moduleLoad('review');
        $review_list = array();
        if(isset($_GET['id']) && $_GET['id'] != null && $_GLOBALS['member']['member_id']) {
            $reviews = reviewearch(array('product_id' => $_GET['id'],'user_id' => $_GLOBALS['member']['member_id']  ));
            if(count($reviews) > 0) {
                foreach($reviews as $review) {
                    $review_list[] = reviewFromDatabase($review);
                }
            }
            die(json_encode([
                'success' => true,
                'review' => $review_list[0],
            ]));
        } else {
            die(json_encode([
                'success' => false,
                'review' => $review_list,
            ]));
        }
    case "comments":
        moduleLoad('blogcomment');
        $blogs = array();

        if(isset($_POST) && $_POST['action'] == 'do_edit') {
            $posted = $_POST;
            blogCommentSetValue($posted['id'],'comment',$posted['comment']);
            $message['success']  = 'y';
        }

        if(isset($_POST) && $_POST['action'] == 'delete') {
            $posted = $_POST;
            blogCommentDeleteFromDatabase($posted['id']);
            $message['success']  = 'y';
        }

        if($_GLOBALS['member']['member_id']) {
            $blogs = TwigBloags::memberCommentBlogs($_GLOBALS['member']['member_id']);
        }

        die($twig->load('account-comment.twig')->render(['inputs' => [
            'blogs' => $blogs,
            'result'   => ['y' => 'success', 'n' => 'danger'][$message['success'] ?? ''] ,
            'alert'  => [
                            'y' => ($posted['id']) ?($_POST['action'] == 'delete' ?"Comment deleted successfully":"Your comment updated successfully"): 'Thank you for your comment',
                            'n' => "Failed to submit your comment"
                        ][$message['success'] ?? ''],
        ]]));
}
