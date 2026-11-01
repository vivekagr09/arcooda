<?php
namespace app\controllers;

use app\forms\AssistedCheckoutForm;
use app\forms\ExpressCheckoutForm;
use app\models\TwigCart;
use app\models\TwigCurrency;
use app\models\TwigInvoice;
use app\models\Airwallex\Client;
use Freight_Service_Handler;
use Stripe\PaymentIntent;
use Stripe\Stripe;
session_start();
/**
 * @var string             $action
 * @var \app\models\MyTwig $twig
 */
?><?php

//Actions:
// /cart/index
// /cart/checkout
// /cart/creditcard
// /cart/payment
// /cart/freight

global $_GLOBALS;
function applycoupon($couponcode,$invoice_products){
    global $_GLOBALS,$DB;
    $coupon=array();
    $coupon['description'] = "Invalid or Expired Coupon!";
    $coupon['name']='';
    $coupon['coupon_avl']=false;
    if ($query = $DB->query(sprintf(
            "SELECT count(*) as `count`, `value`,`coupon_type`,`product_id`,`section`, `active` , `description`, `name` FROM `highwaygroup`.`gift_coupon`  WHERE FIND_IN_SET(`name`, %s) AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `active` = 'y' AND (`site_id` = '' OR `site_id` = %d)",
            formatSQLString($couponcode),
            $_GLOBALS['site_settings']['site_id']
        ))) {
            while ($row = $DB->fetch_assoc($query)) { 
                if($row['count']>0 && $row['active']=='y'){
                    switch($row['coupon_type']){
                        case 2: //Individual Product
                        moduleLoad('product'); //productFromDatabase
                        $invproducts=array_column($invoice_products,'product_id');
                        if(!in_array($row['product_id'], $invproducts)){
                            $product               = productFromDatabase($row['product_id']);
                            $coupon['description'] = 'Please add Product <a href="'.$product['product_url'].'" style="color:red;text-decoration:underline;">'.$product['product_name'].'</a> in your cart to apply this coupon';
                            break;
                        }
                        case 1: //Category
                        $catpro=array_column($invoice_products,'product_section');
                        if(!in_array($row['section'], $catpro)){
                            //break;
                        }
                        case 0: //All
                            $coupon['name']= $row['name'];
                            $coupon['description']=$row['description'];
                            $coupon['coupon_avl']=true;
                            $_SESSION['coupon_applied']=$coupon;
                            if(isset($_GLOBALS['member']['member_id'])){
                                 $member = [
                                    'member_id'         => $_GLOBALS['member']['member_id'],
                                    'updated_by'        => 0,
                                    'discount_code' => $coupon['name'],
                                ];
                                memberToDatabase($member);
                                $_GLOBALS['member']['discount_code']=$couponcode;
                            }else{
                                moduleLoad('visitor'); //applyvisitorcoupon -> webadmin/function/shopping_cart.inc.php
                                applyvisitorcoupon($_GLOBAL['visitor']['visitor_id'],$couponcode);
                                // $_GLOBALS['visitor']['discount_code']=$couponcode;
                            }
                    }
                 }
            }
        }
    return $coupon;
}
function isvalidCoupon($couponcode,$invoice_products){
    global $_GLOBALS,$DB;
    $isValid=false;
    if ($query = $DB->query(sprintf(
            "SELECT count(*) as `count`, `active`,`coupon_type`,`product_id`,`section` FROM `highwaygroup`.`gift_coupon`  WHERE FIND_IN_SET(`name`, %s) AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `active` = 'y' AND (`site_id` = '' OR `site_id` = %d)",
            formatSQLString($couponcode),
            $_GLOBALS['site_settings']['site_id']
        ))) {
            while ($row = $DB->fetch_assoc($query)) { 
                if($row['count']>0 && $row['active']=='y'){
                   switch($row['coupon_type']){
                        case 2: //Individual Product
                        $invproducts=array_column($invoice_products,'product_id');
                        if(!in_array($row['product_id'], $invproducts)){
                            break;
                        }
                        case 1: //Category
                        $catpro=array_column($invoice_products,'product_section');
                        if(!in_array($row['section'], $catpro)){
                            //break;
                        }
                        case 0: //All
                        applycoupon($couponcode,$invoice_products);
                         $isValid=true;   
                    }
                 }
            }
        }
    return $isValid;
}
function removecoupon(){
    global $_GLOBALS;
    $_SESSION['coupon_applied']=NULL;
    if(isset($_GLOBALS['member']['member_id'])){
         $member = [
            'member_id'         => $_GLOBALS['member']['member_id'],
            'updated_by'        => 0,
            'discount_code' => '',
        ];
        memberToDatabase($member);
    } else {
        moduleLoad('visitor'); //applyvisitorcoupon -> webadmin/function/shopping_cart.inc.php
        removevisitorcoupon($_GLOBAL['visitor']['visitor_id'],$couponcode = '');
    }
}
switch ($action) {
    case 'index':
        global $_GLOBALS;
        $type                 = $_POST['type'] ?? $_GET['type'] ?? 'shopping';
        $cart                 = new TwigCart($type);
        $cart->items($type);
        $issues               = [];
        $_REQUEST['redirect'] = null;
       

        /**
         * @var mixed[]   $invoice
         * @var mixed[][] $invoice_products
         * @var mixed[][] $invoice_charges
         * @var mixed[][] $invoice_courier_quotes
         */
        
        if($_POST['action']=='removecoupon'){
            removecoupon();
            redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
            die;
        }
        include __DIR__ . '/../build_invoice.inc.php';
        $coupon = ($_SESSION['coupon_applied'] && isvalidCoupon($_SESSION['coupon_applied']['name'],$invoice_products))?$_SESSION['coupon_applied']:array();
        if(isset($coupon['name'])){
            // to aaply coupon after value set for global variable
            include __DIR__ . '/../build_invoice.inc.php';
        }
        if($_POST['action'] == 'applycoupon'){
            $couponcode =  $_POST['couponcode'];
            $coupon=applycoupon($couponcode,$invoice_products);
            if($coupon['coupon_avl']){
                redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
                die;
            }
           
        }
        $postarray = array();
        $CartLoop  = array_values($cart->Getcart('shopping'));
       

       

        if ('remove' == ($_POST['action'] ?? '')) {


            $FIndProductIdFromCartID=$cart->FindProductID($_POST['id']);
            $FIndLikedIds=cartParentProductCheck($FIndProductIdFromCartID);

            if(count($FIndLikedIds)>0){
                 $cart->removeAllChilds(array_values($FIndLikedIds));
            }
     
            $cart->remove($_POST['id'] ?? 0, $type);
            $cart->save();


            if(count($FIndLikedIds)>0){
                 $cart->removeAllChilds(array_values($FIndLikedIds));
            }
            if(isset($_SESSION['bracket-discount'][$FIndProductIdFromCartID])){
                unset($_SESSION['bracket-discount'][$FIndProductIdFromCartID]);
            }
           // die;
            insertLogDataInShoppingCart();
            redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
            die();
        }

        if ('remove_fright' == ($_POST['action'] ?? '')) {
            $_REQUEST['checkout']['ship_number'] = 'pickup';
        }

        if ('update' == ($_POST['action'] ?? '')) {
            foreach ($_POST['product_qty'] ?? [] as $id => $qty) {
                //print_r($_POST);
                $cart->update($id, $qty);
            }
            $cart->save();
            redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
            die();
        }

        $ship_address_array=[];
        if(isset($_GLOBALS['member']['ship_addr']) && count($_GLOBALS['member']['ship_addr'])){
            $country_list=getCountryList();
            foreach($_GLOBALS['member']['ship_addr'] as $ship_addr){
                if(!in_array($country_list[$ship_addr['country']],$ship_address_array)){
                    $ship_address_array[$ship_addr['id']]=$country_list[$ship_addr['country']];
                }   
            }  
        }
        if('inquiry_quote' == ($_POST['action'] ?? '')) {
            $model = new AssistedCheckoutForm();

            if ($_GLOBALS['member']['id'] ?? 0) {
                $model->loadMember($_GLOBALS['member']);
            }

            $model->load($_POST);
            if ($model->validate() && $id = $model->save()) {
                //Update shopping cart tracking...
                $shopping_cart_id = $_COOKIE['shopping_cart_id'] ?? 0;
                if ($shopping_cart_id) {
                    updateShoppingCartStage($shopping_cart_id, 7); // @todo what is 7?
                    updateShoppingCartStatus($shopping_cart_id, 'ordered');
                    updateShoppingCartInvoice($shopping_cart_id, $id);
                    setcookie('shopping_cart_id', '', strtotime('-1 hour'));
                    $_COOKIE['shopping_cart_id'] = '';
                }

                //Clear the specific cart
                if ('quote' == ($_POST['type'] ?? $_GET['type'] ?? 'shopping')) {
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['shopping_cart']);
                } else {
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['quote_cart']);
                }
                echo "<script>window.close();</script>";
                redirect('/cart/thank-you?invoice_number=' . $id);
                die();
            }
        }
        die($twig->load('cart-index.twig')->render(['inputs' => [
            'cart'                   => $cart,
            'type'                   => $type,
            'title'                  => ucfirst($type . ' Cart'),
            'titletop'               => ('quote' == $type) ? 'Inquiry/Quote Request Cart' : 'Shopping Cart',
            'invoice_courier_quotes' => $invoice_courier_quotes,
            'ship_address_array'     => $ship_address_array,
            'invoice_charges'        => $invoice_charges,
            'invoice_discounts'      => array_values($invoice_discounts),
            'invoice_gifts'          => $invoice_gifts,
            'invoice'                => $invoice,
            'coupon'                 => $coupon,
            'member_couriers'        => $member_couriers,
            'member_id'              => $_GLOBALS['member']['member_id'],
        ]]));

    case 'checkout':
        moduleLoad('invoice'); //invoiceChargesToDatabase, invoiceCourierQuotesToDatabase, invoiceProductsToDatabase, invoiceToDatabase, invoiceTemplateVars
        moduleLoad('member'); //memberContactToDatabase, memberContactSetMain, memberNotifyPartyToDatabase, memberNotifyPartySetDefault, memberShippingAddressToDatabase, memberShippingAddressSetDefault, memberToDatabase
        moduleLoad('notify');
        moduleLoad('product');
        $type                 = $_POST['type'] ?? $_GET['type'] ?? 'shopping';
        $cart                 = new TwigCart($type);
        $issues               = [];
        $_REQUEST['redirect'] = null;
        

        /**
         * @var mixed[]   $invoice
         * @var mixed[][] $invoice_products
         * @var mixed[][] $invoice_charges
         * @var mixed[][] $invoice_courier_quotes
         */
        include __DIR__ . '/../build_invoice.inc.php';
        $coupon = ($_SESSION['coupon_applied'] && isvalidCoupon($_SESSION['coupon_applied']['name'],$invoice_products))?$_SESSION['coupon_applied']:array();
            
        if (!isset($_GLOBALS['member']) || 0 == $_GLOBALS['member']['id']) {
            redirect('/account/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            die();
        }

        $issues = [];

        $type = $_POST['type'] ?? $_GET['type'] ?? 'shopping';

        if ('add_shiping_address' === ($_POST['action'] ?? '')) {
            $address = [
                'member_id'  => $_GLOBALS['member']['id'],
                'updated_by' => 0,
            ];
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

        }

        if ('update_billing' === ($_POST['action'] ?? '')) {
            $member = [
                'member_id'  => $_GLOBALS['member']['id'],
                'updated_by' => 0,
            ];
            $issues = [];
            foreach ([
                'given-name',
                'family-name',
                'street-address',
                'address-level2',
                'address-level1',
                'country',
                'home-tel',
                'account-type',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = 'This field is required';
                }
            }
            foreach ([
                'name'          => 'given-name',
                'first_name'    => 'given-name',
                'surname'       => 'family-name',
                'last_name'     => 'family-name',
                'street-address' => 'address',
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
                memberToDatabase($member);
                redirect('');
                die();
            }
        }

        if ('add-notify-party' === ($_POST['action'] ?? '')) {
            foreach ([
                'company_name',
                'phone_no',
                'city',
                'address',
                'state',
                'email',
                'country',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = '' . $k . ' field is required';
                }
            }
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
                'default'      => 'default',
            ] as $k => $v) {
                if (array_key_exists($v, $_POST)) {
                    $address[$k] = trim($_POST[$v]);
                }
            }
            if (!count($issues)) {
                $member_notifyparty_id = memberNotifyPartyToDatabase($_GLOBALS['member']['id'], $address);
                if ('y' == ($address['default'] ?? '')) {
                    memberNotifyPartySetDefault($_GLOBALS['member']['id'], $member_notifyparty_id);
                }

                redirect('');
                die();
            }
            redirect('');
            die();
        }

        if ('add-contact' === ($_POST['action'] ?? '')) {
            $issues = [];
            foreach ([
                'name',
                'title',
                'email',
                'phone',
                'send_email_cc',
                'main',
            ] as $k) {
                if ('' === trim($_POST[$k] ?? '')) {
                    $issues[$k] = '' . $k . ' field is required';
                }
            }
            foreach ([
                'name'          => 'name',
                'title'         => 'title',
                'email'         => 'email',
                'phone'         => 'phone',
                'send_email_cc' => 'send_email_cc',
                'main'          => 'main',
            ] as $k => $v) {
                if (array_key_exists($v, $_POST)) {
                    $contact[$k] = $_POST[$v];
                }
            }
            if (!count($issues)) {
                $contact_id = memberContactToDatabase($_GLOBALS['member']['id'], $contact);
                if ('y' == ($contact['main'] ?? '')) {
                    memberContactSetMain($_GLOBALS['member']['id'], $contact_id);
                }

                redirect('');
                die();
            }
            redirect('');
            die();
        }

        /**
         * @var mixed[]   $invoice
         * @var mixed[][] $invoice_products
         * @var mixed[][] $invoice_charges
         * @var mixed[][] $invoice_courier_quotes
         * @var string[]  $payment_methods
         */
        include __DIR__ . '/../build_invoice.inc.php';

        $postarray = array();
        $CartLoop  = array_values($cart->Getcart('shopping'));
        if($_POST['couponcode'] != ''){
            $couponcode =  $_POST['couponcode'];
            $coupon=applycoupon($couponcode,$invoice_products);
            if($coupon['coupon_avl']){
                redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
                die;
            }
        }
        
        if($_POST['action']=='removecoupon'){
            removecoupon();
            redirect($_REQUEST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '');
            die;
        }
        if ('submit_order' === ($_REQUEST['action'] ?? '')) {
            // code for air wallex
            $cardNumber = preg_replace('/(?<=\d)\s+(?=\d)/', '', $_POST['cardnumber']);
            $expirationDate = explode('/',$_POST['expirationdate']);

            $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
            $amount_due       = $invoice['invoice_offer'];
            foreach ($invoice_payments as $row) {
                $amount_due = bcsub($amount_due, $row['payment_amount'], 2);
            }
                $remaincart          = [];
                if ('quote' == $type) {
                    $remaincart              = $_GLOBALS['shopping_cart'];
                    $invoice['invoice_type'] = 'quote';
                } else {
                    $remaincart              = $_GLOBALS['quote_cart'];
                    $invoice['invoice_type'] = 'shopping';
                }
                $backorder = false;
                $invoice['coupon_code']=( $_SESSION['coupon_applied'])? $_SESSION['coupon_applied']['name']:'';
                $invoice['discount_ammount']=( $_SESSION['coupon_applied'])? $_SESSION['coupon_applied']['value']:'';
                //Update invoices...
                $invoice_id            = invoiceToDatabase($invoice);
                $invoice['invoice_id'] = $invoice_id;
                invoiceProductsToDatabase($invoice_id, $invoice_products);
                invoiceGiftsToDatabase($invoice_id, $invoice_products);
                invoiceChargesToDatabase($invoice_id, $invoice_charges);
                invoiceCourierQuotesToDatabase($invoice_id, $invoice_courier_quotes);
                foreach ($reserve_product as $data) {
                    $product_id = $data['product_id'];
                    $colour_id = $data['colour_id'];
                    $product_qty = $data['qty'];
                    $sub_id = $data['sub_id'];
                    
                    $query = sprintf(
                        "INSERT INTO `products_reserved` (`invoice_id`, `product_id`,`colour_id`, `sub_id`, `qty`) VALUES (%d, %d, %d, %d,%d)",
                        $invoice_id,
                        $product_id,
                        $colour_id,
                        $sub_id,
                        $product_qty
                    );
                
                    $DB->query($query);
                }
                if(isset($_SESSION['bracket-discount'])){
                     unset($_SESSION['bracket-discount']);  
                }

                $shopping_cart_id = $_COOKIE['shopping_cart_id'] ?? 0;
                if ($shopping_cart_id) {
                    updateShoppingCartStage($shopping_cart_id, 7); // @todo what is 7?
                    updateShoppingCartStatus($shopping_cart_id, 'ordered');
                    updateShoppingCartInvoice($shopping_cart_id, $id);
                    setcookie('shopping_cart_id', '', strtotime('-1 hour'));
                    $_COOKIE['shopping_cart_id'] = '';
                }
                //Update shopping cart...
                foreach ($remaincart as $cart_key => $cart_item) {
                    $min_order = productGetValue($cart_item['product_id'], 'min_order');
                    if ($cart_item['product_qty'] < $min_order) {
                        $cart_item['product_qty']             = $min_order;
                        $remaincart[$cart_key]['product_qty'] = $min_order;
                    }
                    // @fixme $product is undefined
                    $remaincart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                    $remaincart[$cart_key]['product_offer_us'] = $remaincart[$cart_key]['product_offer'];
                    $remaincart[$cart_key]['offer_currency']   = 'USD';
                }
                cartSaveContents($_GLOBALS['member']['id'], $remaincart);
                updateShoppingCartStatus($_COOKIE['shopping_cart_id'], 'ordered');
                setcookie("shopping_cart_id", "", strtotime('-1 hour'));
                $_COOKIE['shopping_cart_id'] = '';
                unset($_SESSION['shipping_method']);
                unset($_SESSION['ship_number']);
                removecoupon();
                //Notify staff...
                if (function_exists('notifyActionMade')) {
                    notifyActionMade(
                        NOTIFY_INVOICE_CREATED,
                        $invoice['site_id'],
                        invoiceTemplateVars($invoice, $invoice_products, $invoice_charges,$invoice_gifts)
                    );
                }
               if(in_array($_POST['checkout']['paymenttype_id'], ['CREDIT_AX','CREDIT_MC', 'CREDIT_VC','VISA','MASTERCARD', 'CREDIT','GOOGLE','APPLE','AMEX']) && $invoice_id){    
                        if($invoice_id) { 
                            $invoice = TwigInvoice::search([
                                'member_id'      => $_GLOBALS['member']['member_id'],
                                'invoice_id'     => $invoice_id,
                            ]);
                            redirect('/payment/invoice?id='.$invoice_id.'&crc='.$invoice[0]->rand_num);
                            die();
                        }

                } 
        
                    //Redirect to orders...
                if ($invoice_id) {
                    redirect('/payment_success?invoice=' . base64_encode((string)$invoice_id));
                    die();
                }
        }
        if($_POST['action'] == 'add'){
            $issues = [];
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
        }
        $courier_listist = getFreightServices('personal', '', true);

        die($twig->load('cart-checkout.twig')->render(['inputs' => [
            'payment_methods'        => $payment_methods,
            'invoice'                => $invoice,
            'invoice_charges'        => $invoice_charges,
            'invoice_courier_quotes' => $invoice_courier_quotes,
            'invoice_products'       => $invoice_products,
            'issues'                 => $issues,
            'invoice_discounts'      => array_values($invoice_discounts),
            'member'                 => $_GLOBALS['member'],
            'coupon'                 => $coupon,
            'member_couriers'        => $member_couriers,
            'courier_list'           => $courier_list,
            'invoice_gifts'          => $invoice_gifts,
        ]]));

    case 'payment_success':
        moduleLoad('invoice'); //invoiceChargesToDatabase, invoiceCourierQuotesToDatabase, invoiceProductsToDatabase, invoiceToDatabase, invoiceTemplateVars
        moduleLoad('notify');
        moduleLoad('product');
        /*$invoice = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_id'     => base64_decode($_REQUEST['invoice'])
        ]); */ 
        $client=new Client($_GLOBALS['site_settings']['airwallex']);
        $data=$client->request('GET','/pa/payment_intents',['id'=>$_GET['id']]);
        $paymentLog = [];
        $paymentLog['invoice_id'] = base64_decode($_REQUEST['invoice']);
        $paymentLog['base_id'] =$_REQUEST['invoice'];
        $paymentLog['logs'] =json_encode($data).$_SERVER['REQUEST_URI'];;
        invoicePaymentLogToDatabase($paymentLog);
        try {
            if(isset($_GET['id']) && isset($_GET['type']) && $_GET['type']=='SUCCESS_URL'){
                if($data[0]==200 && $data[1]->status=='SUCCEEDED' && !invoicePaymentExists($data[1]->id)){
                    $pay_txt='Payment Received via '.$data[1]->merchant_order_id."\n".'id:'.$data[1]->id."\n".'Request Id:'.$data[1]->request_id."\n".'Amount:'.$data[1]->amount."\n".'Currency:'.$data[1]->currency."\n".'merchant order id:'.$data[1]->merchant_order_id."\n".'descriptor:'.$data[1]->descriptor."\n".'status:'.$data[1]->status."\n".'captured amount:'.$data[1]->captured_amount."\n".'-----------------Latest Payment Attempt--------'."\n".'id:'.$data[1]->latest_payment_attempt->id."\n".'amount:'.$data[1]->latest_payment_attempt->amount."\n".'currency:'.$data[1]->latest_payment_attempt->currency."\n".'-----------------Payment Method--------'."\n".'type:'.$data[1]->latest_payment_attempt->payment_method->type."\n".'id:'.$data[1]->latest_payment_attempt->payment_method->id;
                        foreach($data[1]->latest_payment_attempt->payment_method->card as $key=> $card){
                            if(is_array($card)){
                                foreach ($card as $key1 => $val) {
                                   $pay_txt .= "\n".str_replace('_',' ',$key1).':'.$val;
                                }
                            }else{
                                $pay_txt .= "\n".str_replace('_',' ',$key).':'.$card;
                            }
                           
                        }
                        $pay_txt .= "\n".'-----------------device--------'."\n".'ip address:'.$data[1]->latest_payment_attempt->device->ip_address."\n".'http browser type:'.$data[1]->latest_payment_attempt->device->http_browser_type;
                         $pay_txt .= "\n".'-----------------authentication data--------'."\n".' action:'.$data[1]->latest_payment_attempt->authentication_data->fraud_data->action."\n".'score:'.$data[1]->latest_payment_attempt->authentication_data->fraud_data->score;
                         $ip_address='';
                         $payment_country=getCountryInfo($data[1]->latest_payment_attempt->device->ip_address)["a2"];
                         if(strtolower($_GLOBALS['member']['country'])==strtolower($payment_country)){
                            $ip_address='<span style="color:#5fdb69">ip address:'.$data[1]->latest_payment_attempt->device->ip_address.'  <i class="flagicon-' . strtolower($_GLOBALS['member']["country"]) . '"></i> <i class="flagicon-' . strtolower($payment_country).'"></i> </span>';
                         }else{
                            $ip_address='<span style="color:pink">ip address:'.$data[1]->latest_payment_attempt->device->ip_address.' <i class="flagicon-' . strtolower($_GLOBALS['member']["country"]) . '"></i> <i class="flagicon-' . strtolower($payment_country).'"></i>  </span>';
                         }
                         $comment_text='Requested Amount:'.$data[1]->amount.' '.$data[1]->currency."\n".'Response Code:SUCCEEDED'."\n".'Card Brand:'.$data[1]->latest_payment_attempt->payment_method->card->brand."\n".'Card Type:'.$data[1]->latest_payment_attempt->payment_method->card->card_type."\n".'Card Issuer:'.$data[1]->latest_payment_attempt->payment_method->card->issuer_name."\n".$ip_address."\n".'Fraud Score:'.$data[1]->latest_payment_attempt->authentication_data->fraud_data->score."\n".'EDS Enrolled:Yes';
                     $payment = array(
                        'invoice_id'       => base64_decode($_REQUEST['invoice']),
                        'staff_id'         => $_GLOBALS['staff']['id'],
                        'payment_time'     => time(),
                        'payment_text'     => $pay_txt,
                        'TransactionNo'     => $data[1]->id,
                        'payment_amountau' => convertCurrency($data[1]->amount, $data[1]->currency, 'AUD'),
                        'payment_amountus' => convertCurrency($data[1]->amount, $data[1]->currency, 'USD'),
                        'payment_amount'   => $data[1]->amount,
                        'payment_currency' =>$data[1]->currency,
                        'comments'           => $comment_text,
                    );
                    invoicePaymentToDatabase(base64_decode($_REQUEST['invoice']), $payment);
                    invoiceSetValue(base64_decode($_REQUEST['invoice']),'invoice_status','processed');
                    invoiceSetValue(base64_decode($_REQUEST['invoice']),'invoice_action','approval');
                    //Sending mail on payment success.
                    $invoice_id       = base64_decode($_REQUEST['invoice']);
                    $invoice          = invoiceFromDatabase($invoice_id);
                    $invoice_products = invoiceProductsFromDatabase($invoice_id);
                    $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
                    $invoice_gifts    = invoiceGiftsFromDatabaseEmail($invoice_id);
                    if (function_exists('notifyActionMade')) {
                        notifyActionMade(
                            NOTIFY_PAYMENT_SUCCESS,
                            $invoice['site_id'],
                            invoiceTemplateVars($invoice, $invoice_products, $invoice_charges,$invoice_gifts)
                        );
                    }
                }
            }
            $invoice = TwigInvoice::search([
                'invoice_id'     => base64_decode($_REQUEST['invoice'])
            ]); 
        }catch (Exception $e) {
            $paymentLog = [];
            $paymentLog['invoice_id'] = base64_decode($_REQUEST['invoice']);
            $paymentLog['base_id'] =$_REQUEST['invoice'];
            $paymentLog['logs'] =json_encode(['error_message' => $e->getMessage()]);
            invoicePaymentLogToDatabase($paymentLog);
        }
        die($twig->load('order-success.twig')->render(['inputs' => [
            'invoice'     => $invoice,
            'title_front' => ($invoice[0]->invoicestatus() == 'quote') ? 'Inquiry/Quote Request' : 'Checkout',
        ]]));
     case 'payment_fail':
        moduleLoad('invoice'); //invoiceChargesToDatabase, invoiceCourierQuotesToDatabase, invoiceProductsToDatabase, invoiceToDatabase, invoiceTemplateVars
        moduleLoad('notify');
        moduleLoad('product');
       /* $invoice = TwigInvoice::search([
            'member_id'      => $_GLOBALS['member']['member_id'],
            'invoice_id'     => base64_decode($_REQUEST['invoice'])
        ]);*/
       // Code to insert data in invoice_decline table for failed payment
        $client=new Client($_GLOBALS['site_settings']['airwallex']);
        $data=$client->request('GET','/pa/payment_intents',['id'=>$_GET['id']]);
        $paymentLog = [];
        $paymentLog['invoice_id'] = base64_decode($_REQUEST['invoice']);
        $paymentLog['base_id'] =$_REQUEST['invoice'];
        $paymentLog['logs'] =json_encode($data).$_SERVER['REQUEST_URI'];
        invoicePaymentLogToDatabase($paymentLog);
        try {
            if(isset($_GET['id']) && isset($_GET['type']) && $_GET['type']=='FAIL_URL'){
                if($data[1]->status !='SUCCEEDED' && !invoiceDeclineExists($data[1]->id)){
                     $payment_country=getCountryInfo($data[1]->latest_payment_attempt->device->ip_address)["a2"];
                     if(strtolower($_GLOBALS['member']['country'])==strtolower($payment_country)){
                        $ip_address='<span style="color:#5fdb69">ip address:'.$data[1]->latest_payment_attempt->device->ip_address.' <i class="flagicon-' . strtolower($_GLOBALS['member']["country"]) . '"></i>  <i class="flagicon-' . strtolower($payment_country).'"></i> </span>';
                     }else{
                        $ip_address='<span style="color:pink">ip address:'.$data[1]->latest_payment_attempt->device->ip_address.'<i class="flagicon-' . strtolower($_GLOBALS['member']["country"]) . '">  <i class="flagicon-' . strtolower($payment_country).'"></i>  </i></span>';
                     }
                    $comment_text='Requested Amount:'.$data[1]->amount.' '.$data[1]->currency."\n".'Response Code:'.$data[1]->latest_payment_attempt->failure_code."\n".'Card Brand:'.$data[1]->latest_payment_attempt->payment_method->card->brand."\n".'Card Type:'.$data[1]->latest_payment_attempt->payment_method->card->card_type."\n".'Card Issuer:'.$data[1]->latest_payment_attempt->payment_method->card->issuer_name."\n".'ip address:'.$ip_address."\n".'Fraud Score:'.$data[1]->latest_payment_attempt->authentication_data->fraud_data->score."\n".'EDS Enrolled:Yes';
                     $decline = array(
                        'invoice_id'        => base64_decode($_REQUEST['invoice']),
                        'reason'            => 'payment has been failed due to '. str_replace('_',' ',$data[1]->latest_payment_attempt->failure_code),
                        'comments'           => $comment_text,
                        'payment_id'           =>$data[1]->id 
                    );
                    invoiceDeclineToDatabase(base64_decode($_REQUEST['invoice']), $decline);
                    //Sending mail on payment failure.
                    $invoice_id       = base64_decode($_REQUEST['invoice']);
                    $invoice          = invoiceFromDatabase($invoice_id);
                    $invoice_products = invoiceProductsFromDatabase($invoice_id);
                    $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
                    if (function_exists('notifyActionMade')) {
                        notifyActionMade(
                            NOTIFY_PAYMENT_FAILURE,
                            39,
                            invoiceTemplateVars($invoice, $invoice_products, $invoice_charges)
                        );
                    }
                }
            }
            $invoice = TwigInvoice::search([
                'invoice_id'     => base64_decode($_REQUEST['invoice'])
            ]);
        }catch (Exception $e) {
            $paymentLog = [];
            $paymentLog['invoice_id'] = base64_decode($_REQUEST['invoice']);
            $paymentLog['base_id'] =$_REQUEST['invoice'];
            $paymentLog['logs'] =json_encode(['error_message' => $e->getMessage()]);
            invoicePaymentLogToDatabase($paymentLog);
        }
        die($twig->load('airwallex-result.twig')->render(['inputs' => [
            'invoice'     => $invoice,
            'status' => $data[1]->latest_payment_attempt->status,
            'error'          => $data[1]->latest_payment_attempt->failure_code
    ]]));
    case 'payment_manual_success':
        global $_GLOBALS;
          global $DB;
          moduleLoad('notify');
          moduleLoad('payment');
          moduleLoad('member');
          $payment = paymentFromDatabase(base64_decode($_GET['payment_id']));
          if(isset($_GET['id']) && isset($_GET['type']) && $_GET['type']=='SUCCESS_URL'){
              $client=new Client($_GLOBALS['site_settings']['airwallex']);
              $data=$client->request('GET','/pa/payment_intents',['id'=>$_GET['id']]);
              if($data[1]->status =='SUCCEEDED'){
                  if ($row = $DB->query_first(sprintf(
                      "SELECT * FROM `highwaygroup`.`credit_payments` WHERE `credit_id` = %d LIMIT 1",
                      base64_decode($_GET['credit_id'])
                  ), MYSQLI_ASSOC));
                  preg_match('/\((\d+)\)/',$data[1]->merchant_order_id, $matches);
                  $memberid =$matches[1];
                  $payment['id'] = base64_decode($_GET['payment_id']);
                  $payment['Version'] = 1;
                  $payment['Command'] = 'pay';
                  $payment['MerchTxnRef'] =substr(preg_replace('/[\0-\x1f\x80-\xff]+/', '.', sprintf('%x - payment:%s member:%d',base64_decode($_GET['payment_id']), base64_decode($_GET['credit_id']), $memberid)) ?? '', 0, 40);
                  $payment['OrderInfo'] = trim($data[1]->merchant_order_id);
                  $payment['Amount'] = $data[1]->amount;
                  $payment['Locale'] = 'en';
                  $payment['TxnResponseCode'] = $data[1]->latest_payment_attempt->provider_original_response_code;
                  $payment['Message'] =  $data[1]->latest_payment_attempt->status;
                  $payment['TransactionNo'] = $data[1]->latest_payment_attempt->payment_intent_id;
                  $payment['Card'] = $data[1]->latest_payment_attempt->payment_method->card->brand;
                  $payment['VerStatus'] = 'N';
                  $payment['VerSecurityLevel'] = 05;
                  $payment['3DSECI'] = 03;
                  $payment['ReceiptNo'] =$_GET['id'];
                  $payment['transaction_detail'] = json_encode($data);
                  paymentToDatabase($payment);
                  $payment["reference"] =$row['credit_ref'];
                  $payment["payment_id"] =base64_decode($_GET['payment_id']);
                  $payment["user_name"] = $data[1]->customer->first_name;
                  $payment["member_id"] = $memberid;
                  notifyActionMade(NOTIFY_MANUAL_PAYMENT_RECEIVED, $_GLOBALS['site_settings']['site_id'], paymentTemplateVars($payment));
              }
          }
        die($twig->load('order-manual-success.twig')->render(['inputs' => [
          'payment' => $payment,
      ]]));
    case 'payment_manual_fail':
            global $_GLOBALS;
            global $DB;
            moduleLoad('notify');
            moduleLoad('payment');
            moduleLoad('member');
            $payment = paymentFromDatabase(base64_decode($_GET['payment_id']));
            if(isset($_GET['id']) && isset($_GET['type']) && $_GET['type']=='FAIL_URL' && empty($payment['ReceiptNo'])){
                $client=new Client($_GLOBALS['site_settings']['airwallex']);
                $data=$client->request('GET','/pa/payment_intents',['id'=>$_GET['id']]);
                if($data[1]->status !='SUCCEEDED'){
                    if ($row = $DB->query_first(sprintf(
                        "SELECT * FROM `highwaygroup`.`credit_payments` WHERE `credit_id` = %d LIMIT 1",
                        base64_decode($_GET['credit_id'])
                    ), MYSQLI_ASSOC));
                    preg_match('/\((\d+)\)/',$data[1]->merchant_order_id, $matches);
                    $memberid =$matches[1];
                    $payment['id'] = base64_decode($_GET['payment_id']);
                    $payment['Version'] = 1;
                    $payment['Command'] = 'pay';
                    $payment['MerchTxnRef'] =substr(preg_replace('/[\0-\x1f\x80-\xff]+/', '.', sprintf('%x - payment:%s member:%d',base64_decode($_GET['payment_id']), base64_decode($_GET['credit_id']), $memberid)) ?? '', 0, 40);
                    $payment['OrderInfo'] = trim($data[1]->merchant_order_id);
                    $payment['Amount'] = $data[1]->amount;
                    $payment['Locale'] = 'en';
                    $payment['TxnResponseCode'] = $data[1]->latest_payment_attempt->failure_code ;
                    $payment['Message'] =  $data[1]->latest_payment_attempt->failure_code;
                    $payment['TransactionNo'] = $data[1]->latest_payment_attempt->payment_intent_id;
                    $payment['Card'] = $data[1]->latest_payment_attempt->payment_method->card->brand;
                    $payment['VerStatus'] = 'N';
                    $payment['VerSecurityLevel'] = 05;
                    $payment['3DSECI'] = 03;
                    $payment['ReceiptNo'] =$_GET['id'];
                    $payment['transaction_detail'] = json_encode($data);
                    paymentToDatabase($payment);
                    $payment["reference"] =$row['credit_ref'];
                    $payment["payment_id"] =base64_decode($_GET['payment_id']);
                    $payment["user_name"] = $data[1]->customer->first_name;
                    $payment["member_id"] = $memberid;
                    notifyActionMade(NOTIFY_MANUAL_PAYMENT_ERROR, $_GLOBALS['site_settings']['site_id'], paymentTemplateVars($payment));
                }
            }
            die($twig->load('order-manual-fail.twig')->render(['inputs' => [
                'payment'     => $payment
            ]]));

    case 'creditcard':
        die($twig->load('cart-creditcard.twig')->render(['inputs' => [
        ]]));

    case 'payment':
        Stripe::setApiKey('sk_test_16EI0qL0OKM3UT2Rwc5Oovxq00Tq70GT37');
        header('Content-Type: application/json');
        $json_str = file_get_contents('php://input') ?: 'null';
        $json_obj = json_decode($json_str);
        $intent   = null;

        // Get total amount
        $totalAmount = 0;
        foreach ($cart_items as $item) {
            $totalAmount += $item['price'];
        }

        // Get currency
        $currency = (new TwigCurrency())->getCurrency();
        try {
            if (isset($json_obj->payment_method_id)) {
                $intent = PaymentIntent::create([
                    'payment_method'      => $json_obj->payment_method_id,
                    'amount'              => $totalAmount * 100,
                    'currency'            => strtolower($currency),
                    'confirmation_method' => 'manual',
                    'confirm'             => true,
                ]);
            }
            if (isset($json_obj->payment_intent_id)) {
                $intent = PaymentIntent::retrieve(
                    $json_obj->payment_intent_id
                );
                $intent->confirm();
            }

            if ('requires_action' == $intent->status && 'use_stripe_sdk' == $intent->next_action->type) {
                echo json_encode([
                    'requires_action'              => true,
                    'payment_intent_client_secret' => $intent->client_secret,
                ]);
            } else if ('succeeded' == $intent->status) {
                echo json_encode([
                    "success" => true,
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Invalid PaymentIntent status']);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo json_encode([
                'error' => $e->getMessage(),
            ]);
        }
        die();

    case 'freight':
        global $DB;

        moduleLoad('freight');

        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");
        //print_r($_REQUEST); die;
        if (1 == $_REQUEST['type']) {
            $start_location = $_REQUEST['startpoint'] ?? 'CN';
            if (empty($start_location)) {
                $start_location = 'CN';
            }

            $freightService = Freight_Service_Handler::getInstance();
            $hints          = $freightService->hintInternational(
                $start_location,
                $_REQUEST['destination'],
                $_REQUEST['width'],
                $_REQUEST['depth'],
                $_REQUEST['height'],
                $_REQUEST['mass']
            );
            $list           = $freightService->calculateServices(
                $hints,
                $_REQUEST['value'],
                $_REQUEST['currency']
            );
            // echo '<pre>'; print_r($list); die;
            //print_r($freightService );
            usort($list, function ($firstService, $secondService) {
                return bccomp($firstService['offer_price'], $secondService['offer_price'], 2);
            });
            die(json_encode($list));
        } else {
            $response       = [];
            $destination    = $_REQUEST['destination'];
            $start          = (isset($_REQUEST['location'])) ? $_REQUEST['location'] : null;
            $start_location = (isset($_REQUEST['startpoint'])) ? $_REQUEST['startpoint'] : null;
            $shipping       = (isset($_REQUEST['shipping'])) ? $_REQUEST['shipping'] : null;
            $cubic_weight       = (isset($_REQUEST['cubic_weight'])) ? $_REQUEST['cubic_weight'] : 1;
            $shipping_weight       = (isset($_REQUEST['shipping_weight'])) ? $_REQUEST['shipping_weight'] : 1;
            if ($destination && ($start || $start_location)) {
                if ($start) {
                    $result = $DB->query_first(sprintf(
                        "SELECT `code` FROM `product_country_location` WHERE `deleted_at` IS NULL AND id=%d",
                        $start
                    ), MYSQLI_ASSOC);
                    $start_location = $result['code'];
                }
                if ($query = $DB->query(sprintf(
                    "SELECT * FROM `highwaygroup`.`freight_services_quotes` WHERE `country_from` = %s AND `country_to` = %s %s AND `sale_price`>0 AND `port_to` > 0 ORDER BY `id` DESC LIMIT 15",
                    formatSQLString($start_location),
                    formatSQLString($destination),
                    ($shipping) ? ' AND `sea_air` = ' . formatSQLString($shipping) . ' ' : ''
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $temp                    = [];
                        $temp['price_org']= (string) strip_tags((new TwigCurrency($row['sale_price'], $row['quote_currency'])));
                        $row['sale_price']=$row['sale_price']*$cubic_weight;
                        $temp['id']            = $row['id'];
                        $temp['type']            = $row['sea_air'];
                        $temp['container_flags'] = $row['container_flags'];
                        //$temp['shipping_weight']=$row['shipping_weight'];
                        $temp['cubic_weight']    = number_format($row['cubic_weight'], 2);
                        $sale_price              =  number_format(str_replace("$",'',trim(str_replace(" USD",'',$temp['price_org']))),2);
                        $temp['sale_price']      = bcmul($sale_price,$temp['cubic_weight'],2);
                         $temp['port_hg'] = $row['port_to'];
                        $temp['shipping_weight'] = round($row['shipping_weight'], 0);
                        $temp['price']           = (string) strip_tags((new TwigCurrency($row['sale_price'], $row['quote_currency'])));
                        $temp['price_display']           =  (string)(str_replace("USD","",new TwigCurrency($row['sale_price'], $row['quote_currency'])));
                        $temp['port_from']       = getPortName($row['port_to'], $row['country_to'], $row['sea_air']) . '-' . $row['country_to'];
                        if($shipping == 'Air')
                        {
                            $temp['ship_weight']        = $shipping_weight;
                            $ship_weight                =  str_replace("$",'',trim(str_replace(" USD",'',$temp['price'])));
                            $temp['price_per_kg']       = bcdiv($ship_weight,$temp['shipping_weight'],2);
                            $temp['price_display_air']  = bcmul($shipping_weight,$temp['price_per_kg'],2);
                        }
                        $response[]              = $temp;
                    }
                    $DB->free_result($query);
                }
            }

            die(json_encode($response));
        }

    case 'assisted-checkout':
        global $_GLOBALS;

        $model = new AssistedCheckoutForm();

        if ($_GLOBALS['member']['id'] ?? 0) {
            $model->loadMember($_GLOBALS['member']);
        }

        switch ($_POST['action'] ?? '') {
            case 'send':
                //no break
            case 'do_add':
                $model->load($_POST);
                if ($model->validate() && $id = $model->save()) {
                    //Update shopping cart tracking...
                    $shopping_cart_id = $_COOKIE['shopping_cart_id'] ?? 0;
                    if ($shopping_cart_id) {
                        updateShoppingCartStage($shopping_cart_id, 7); // @todo what is 7?
                        updateShoppingCartStatus($shopping_cart_id, 'ordered');
                        updateShoppingCartInvoice($shopping_cart_id, $id);
                        setcookie('shopping_cart_id', '', strtotime('-1 hour'));
                        $_COOKIE['shopping_cart_id'] = '';
                    }

                    //Clear the specific cart
                    if ('quote' == ($_POST['type'] ?? $_GET['type'] ?? 'shopping')) {
                        cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['shopping_cart']);
                    } else {
                        cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['quote_cart']);
                    }
                    ?>
                     <script>
                        var data = <?=json_encode(['invoice_id'=>$id])?>;
                        if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                            window.opener.redirectToInvoiceSuccess(data);
                            window.close();
                        }else{
                            window.close();
                        }
                    </script>
                    <?php
                    //redirect('/cart/thank-you?invoice_number=' . $id);
                    die();
                }
                break;
        }

        die($twig->load('cart/assisted-checkout.twig')->render(['inputs' => [
            'page_title'       => 'Assisted Checkout',
            'page_description' => null,
            'page_keywords'    => null,
            'page_canonical'   => '/cart/assisted-checkout',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Assisted Checkout', 'url' => '/cart/assisted-checkout'],
            ],

            'model'             => $model,
            'comment'           => $_GET['comment'],
        ]]));

    case 'express-checkout':
        global $_GLOBALS;

        $model = new ExpressCheckoutForm();

        if ($_GLOBALS['member']['id'] ?? 0) {
            $model->loadMember($_GLOBALS['member']);
        }

        switch ($_POST['action'] ?? '') {
            case 'do_add':
                $model->load($_POST);
                if ($model->validate() && $id = $model->save()) {
                    //Update shopping cart tracking...
                    $shopping_cart_id = $_COOKIE['shopping_cart_id'] ?? 0;
                    if ($shopping_cart_id) {
                        updateShoppingCartStage($shopping_cart_id, 7); // @todo what is 7?
                        updateShoppingCartStatus($shopping_cart_id, 'ordered');
                        updateShoppingCartInvoice($shopping_cart_id, $id);
                        setcookie('shopping_cart_id', '', strtotime('-1 hour'));
                        $_COOKIE['shopping_cart_id'] = '';
                    }

                    //Clear the specific cart
                    if ('quote' == ($_POST['type'] ?? $_GET['type'] ?? 'shopping')) {
                        cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['shopping_cart']);
                    } else {
                        cartSaveContents($_GLOBALS['member']['id'] ?? 0, $_GLOBALS['quote_cart']);
                    }

                    redirect('/cart/thank-you?invoice_number=' . urlencode(invoiceGetValue($id, 'invoice_number')));
                    die();
                }
                break;
        }

        die($twig->load('cart/express-checkout.twig')->render(['inputs' => [
            'page_title'       => 'Express Checkout',
            'page_description' => null,
            'page_keywords'    => null,
            'page_canonical'   => '/cart/express-checkout',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Express Checkout', 'url' => '/cart/express-checkout'],
            ],

            'model' => $model,
        ]]));

    case 'thank-you':
        die($twig->load('cart/thank-you.twig')->render(['inputs' => [
            'page_title'       => 'Thank You',
            'page_description' => null,
            'page_keywords'    => null,
            'page_canonical'   => '/cart/thank-you',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Thank You', 'url' => '/cart/thank-you'],
            ],

            'invoice_number' => $_GET['invoice_number'] ?? null,
        ]]));
}
