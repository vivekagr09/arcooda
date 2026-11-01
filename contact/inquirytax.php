<style>
    
    .color-red {
    color: #ff0000;
}
.inquiry_form{
    background: #fafafa;
    padding: 15px;
    font-size: 15px;
}
.inquiry_form .form-control{
    font-size: 15px;
    font-weight: 400;
    letter-spacing: .5px;
    border: 1px solid #a7a7a7;
    border-radius: 5px;
}
.inquiry_form b{
    font-weight: 500;
    font-size: 15px;
    margin-bottom: 0px;
}
</style>
<?php
/**
 * Generate an inquiry email from form, and send to sales email address.
 * @Author: Mingze Xi <mxi@highwaygroup.net>
 * Added Date: 26/10/2015
 * Last Update: 26/10/2015
 */

use app\models\PHPMailerEmail;

include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceHistoryToDatabase, invoiceProductsToDatabase, invoiceProductsFromDatabase, invoiceToDatabase, invoiceFromDatabase, invoiceGetOptionList
moduleLoad('member'); //memberIdFromEmail, memberFromDatabase, memberGetOptionList
moduleLoad('product'); //productFromDatabase
moduleLoad('notify'); //notifyActionMade
// Function to get the client IP address
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}
if (isset($_REQUEST["id"]) && function_exists("productFromDatabase")) {
    $product   = productFromDatabase($_REQUEST['id']);
    if(count($product)==0 && $_REQUEST['action']!='complete'){
        echo '<h1>Not a valid request</h1>';
        die;
    }
}else{
    echo '<h1>Not a valid request</h1>';
    die;
}
$invoice_currency  = 'USD';
$_GLOBALS["site_options"]["DEFAULT_CURRENCY"] = 'USD';

//@fixme This script is screaming at me, using autoglobals, build member too early, overwriting _GLOBALS
$defaultAddress = [];
foreach ($_GLOBALS['member']['ship_addr'] ?? [] as $member_shippingaddress) {
    foreach ([
        'ship_name'     => 'contact',
        'ship_address'  => 'address',
        'ship_city'     => 'city',
        'ship_company'  => 'company_name',
        'ship_country'  => 'country',
        'ship_email'    => 'email',
        'ship_needs'    => 'instructions',
        'ship_phone'    => 'phone_no',
        'ship_postcode' => 'post_code',
        'ship_state'    => 'state',
        'address_type'  => 'address_type'
    ] as $k => $v) {
        if (array_key_exists($v, $member_shippingaddress)) {
            $defaultAddress[$v] = $member_shippingaddress[$v];
        }
    }
    if ('y' == $member_shippingaddress['default']) {
        break;
    }
}
include __DIR__ . '/build_member.inc.php';

// Process inquiry
if (array_key_exists('action', $_REQUEST) && 'send' == $_REQUEST['action']) {
    $recaptcha = $_REQUEST['g-recaptcha-response'];
    $secret_key = '6Ld2WYEnAAAAAL-bqCPD653IS2Exp7V_qEieGwAx';
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
          . $secret_key . '&response=' . $recaptcha;
    $response = file_get_contents($url);
    $response = json_decode($response);
    $bHasLink = strpos($_POST['customer_requests'], 'http') !== false || strpos($_POST['customer_requests'], 'www.') !== false;
    if ($response->success !=1 || $bHasLink) {
        $rdirect['value']   = 'n';
        $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
        $data               = json_encode($rdirect);
        setcookie('redirect', $data, strtotime('+1 year'), '/');
        ?>
        <script type="text/javascript">
            var data = <?=json_encode(['invoice_id'=>0])?>;
            if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                window.opener.redirectToInvoiceSuccess(data);
                window.close();
            }else{
                window.close();
            }
        </script>
    <?php
        die();  
    }

    if (!array_key_exists('member', $_GLOBALS)) {
        $member_email=$_REQUEST['member_email'];
        $member_id = memberIdFromEmail($member_email);
        if ($member_id) {
            $_GLOBALS['member'] = memberFromDatabase($member_id);
              foreach ([
                'member_location'=> 'country',
                'member_phone'   => 'phone_no',
                'member_phone'   => 'mobile_no',
                'member_phone'   => 'mobile_no',
                'member_email'   => 'email',
                'default'        => 'default',
                'given-name'     => 'contact',
                'family-name'    => 'contact_last',
                'postal-code'    => 'post_code',
                'addresstype'   => 'address_type',
                'street-address' => 'address',
                'address-level2' => 'city',
                'address-level1' => 'state',
                'postal-code'    => 'post_code',
            ] as $k => $v) {
                if (array_key_exists($k, $_POST)) {
                    $address[$v] = trim($_POST[$k]);
                }
            }
             $_GLOBALS['member']['ship_addr']=$address;
        } else {
            if(empty($member['first_name']) || empty($member['email_address'])){
                $rdirect['value']   = 'n';
                    $rdirect['message'] = 'Required parameter not available to complete this request.';
                    $data               = json_encode($rdirect);
                    setcookie('redirect', $data, strtotime('+1 year'), '/');
                    ?>
                    <script type="text/javascript">
                        var data = <?=json_encode(['invoice_id'=>0])?>;
                        if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                            window.opener.redirectToInvoiceSuccess(data);
                            window.close();
                        }else{
                            window.close();
                        }
                    </script>
                <?php
                    die();
                }   
            $member_id = memberToDatabase($member);
            $member = memberFromDatabase($member_id);
            $_GLOBALS['member'] = $member;

            // Add member details to shipping address

            $address = [
                'member_id'  => $_GLOBALS['member']['member_id'],
                'updated_by' => 0,
                'default'    => 'y',
            ];

            foreach ([
                'member_location'=> 'country',
                'member_phone'   => 'phone_no',
                'member_phone'   => 'mobile_no',
                'member_phone'   => 'mobile_no',
                'member_email'   => 'email',
                'default'        => 'default',
                'given-name'     => 'contact',
                'family-name'    => 'contact_last',
                'postal-code'    => 'post_code',
                'addresstype'   => 'address_type',
                'street-address' => 'address',
                'address-level2' => 'city',
                'address-level1' => 'state',
                'postal-code'    => 'post_code',
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
        }
    }
    if($_GLOBALS['member']['spammer']=='y'){
        $rdirect['value']   = 'n';
        $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
        $data               = json_encode($rdirect);
        setcookie('redirect', $data, strtotime('+1 year'), '/');
        ?>
        <script type="text/javascript">
            var data = <?=json_encode(['invoice_id'=>0])?>;
            if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                window.opener.redirectToInvoiceSuccess(data);
                window.close();
            }else{
                window.close();
            }
        </script>
    <?php
        die();
    }
    include __DIR__ . '/build_invoice.inc.php';

    $invoice_id = invoiceToDatabase($invoice);
    invoiceProductsToDatabase($invoice_id, $invoice_products);
    invoiceGiftsToDatabase($invoice_id, $invoice_products);
    $invoice = invoiceFromDatabase($invoice_id);
    $invoice_products = invoiceProductsFromDatabase($invoice_id);

    /**
     * Send notification for member
     * Updated By Swapnil 21st July
     */
    $invoice_template_vars = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);
    $invoice_template_vars['[INQUIRY_PRODUCT_NAME]'] = $product['product_name'];
    if (function_exists("notifyActionMade")) {
        notifyActionMade(NOTIFY_FREIGHT_TAX_INQUIRY_CREATED, $_GLOBALS["site_settings"]["site_id"], $invoice_template_vars);
    }
  /*  if (function_exists("notifyActionMade")) {
        notifyActionMade(NOTIFY_PRODUCT_INQUIRY_MEMBER, $_GLOBALS["site_settings"]["site_id"], $invoice_template_vars);
    }*/
    /** end */

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

  /*  $email           = new PHPMailerEmail();
    $email->From     = $_GLOBALS['site_settings']['email_sales'];
    $email->FromName = 'Alert – Quick  Inquiry';
    //$email->Subject  = $product['product_name'] . " (ID = " . $product_id . ") - New Inquiry Q" . $inquiry_id;
    $email->Subject  = "New Quick Inquiry - " . $invoice['invoice_number'].' - '.$product['product_name'];
    $email->Body     = $message;
    $email->IsHtml(true);
    $email->AddAddress('sales@arcooda.com', $_GLOBALS['site_settings']['site_name']);
    $result = $email->Send();*/

    // send email to "sales@arcooda.com"

    redirect('?action=complete&id=' . $invoice_id . '&rand_num=' . $invoice['rand_num']);
    return;
}

if (array_key_exists('action', $_REQUEST) && 'complete' == $_REQUEST['action']) { 
    ?>
        <script>
            var data = <?=json_encode(['invoice_id'=>$_REQUEST['id']])?>;
            if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                window.opener.redirectToInvoiceSuccess(data);
                window.close();
            }else{
                window.close();
            }
        </script>
<?php /*include_once __DIR__ . '/header.inc.php';
    echo '<div style = "margin: 20px 10px; border-radius: 25px; background: #52D017; color: #fff; padding: 20px 10px; text-align: center; ">';
    echo '<h1 style="color: #ffffff; font-size: 26px;">Thanks for your inquiry</h1>';
    echo '<div style="padding: 50px 0; font-size: 25px; ">ECO#' . $invoice_id . '</div>';
    echo '<p>Our staff will contact you soon!</p>';
    echo '</div>';
    include_once __DIR__ . '/footer.inc.php';
    return;*/
}


include_once __DIR__ . '/header.inc.php';
//$popup_class ???
?>
<br />
<div class="container"><div class="row"><div class="col-sm-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5" style="margin:auto;">
    <h2 class="btn-warning" style="text-align: center; padding: 5px;">Freight & Tax Quotation Form</h2>
    
   
    <p>Using this service will add 10-20 days extra in transit time; however it is a door to door service with all taxes paid. Generally, the price will be the same/close to the Express Courier non tax service. If you decide to order/pay via express courier, we will still check with you if you would like the tax paid service during the shipping arrangements.</p>
    <p>The quotation will be emailed to you and also display in your MyDashboard (if logged in).</p>
    <form class="<?= $popup_class ?> inquiry_form" id="inquiry_form" method="post" action="">
        <input type="hidden" name="id" value="<?php hecho($_REQUEST['id']) ?>" />
        <div class="form-group">
            <label for="_product_name"><b>Product Name</b> <span class="color-red">*</span></label>
            <input type="text" class="form-control form-control-lg bg-primary text-white" id="_product_name" name="product_name" value="<?php hecho($_REQUEST['product_name']) ?>" readonly="readonly" />
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="product_qty"><b>Qty</b> <span class="color-red">*</span></label>
                    <input type="number" class="form-control form-control-lg" id="product_qty" name="product_qty" value="<?php hecho($_REQUEST['product_qty'] ?? 1) ?>" min="1" max="10000" step="1" placeholder="Numbers only" required />
                </div>
            </div>
            <div class="col-12">
                 <div class="form-group">
                    <label for="given-name"><b>First Name</b> <span class="color-red">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="given_name" name="given-name" value="<?php hecho($_REQUEST['given-name'] ?? $_GLOBALS['member']['first_name'] ?? $_GLOBALS['visitor']['first_name'] ?? '') ?>" placeholder="" required />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="family-name"><b>Last Name</b> <span class="color-red">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="family-name" name="family-name" value="<?php hecho($_REQUEST['family-name'] ?? $_GLOBALS['member']['last_name'] ?? $_GLOBALS['visitor']['last_name'] ?? '') ?>" placeholder="" required />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="member_email"><b>Email</b> <span class="color-red">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="member_email" name="member_email" value="<?php hecho($_REQUEST['member_email'] ?? $_GLOBALS['member']['email_address'] ?? $_GLOBALS['visitor']['email_address'] ?? '') ?>" placeholder="" required/>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="member_phone"><b>Contact Phone Numbers</b> </label>
                    <input type="text" class="form-control form-control-lg" id="member_phone" name="member_phone" value="<?php hecho($_REQUEST['member_phone'] ?? $_GLOBALS['member']['phone_no'] ?? $_GLOBALS['visitor']['phone_no'] ?? '') ?>" placeholder=""/>
                </div>
            </div>
             <div class="col-12">
                <div class="form-group">
                    <label for="address_type"><b>Address Type</b><span class="color-red">*</span> </label>
                    <select name="addresstype" required="required" class="form-control form-control-lg" id="address_type">
                        <option value=""></option>
                    <option value="commercial" <?=  $defaultAddress['address_type'] == 'commercial' ? ' selected="selected"' : '' ?> >Commercial Property</option>
                    <option value="residential" <?= $defaultAddress['address_type'] == 'residential' ? ' selected="selected"' : '' ?> >Residential Property</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="street-address"><b>Street Address</b><span class="color-red">*</span> </label>
                     <textarea name="street-address" required="required" class="form-control form-control-lg" id="street-address" autocomplete="street-address" ><?php hecho($_REQUEST['street-address'] ??$defaultAddress['address'] ?? $_GLOBALS['visitor']['address'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="address-level2"><b>City/Suburb</b><span class="color-red">*</span> </label>
                    <input type="text" class="form-control form-control-lg" id="address-level2" name="address-level2" required value="<?php hecho($_REQUEST['city'] ?? $defaultAddress['city'] ?? $_GLOBALS['visitor']['city'] ?? '') ?>" placeholder="City"/>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="address-level2"><b>State</b><span class="color-red">*</span> </label>
                    <input type="text" class="form-control form-control-lg" id="address-level1" name="address-level1" required value="<?php hecho($_REQUEST['city'] ?? $defaultAddress['state'] ?? $_GLOBALS['visitor']['state'] ?? '') ?>" placeholder="state"/>
                </div>
            </div>
             <div class="col-12">
                <div class="form-group">
                    <label for="address-level2"><b>Zip / Postal Code</b><span class="color-red">*</span> </label>
                    <input type="text" class="form-control form-control-lg" id="postal-code" name="postal-code" required value="<?php hecho($_REQUEST['post_code'] ?? $defaultAddress['post_code'] ?? $_GLOBALS['visitor']['post_code'] ?? '') ?>" placeholder="Zip / Postal Code"/>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="member_location"><b>Country</b> <span class="color-red">*</span></label>
                    <select class="form-control form-control-lg" id="member_location" name="member_location" style="width:100%;" required>
                        <option value=""></option>
                        <?= formatHTMLOptions(memberGetOptionList('country'), ($defaultAddress['country'])?$defaultAddress['country']:$member['country']) ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="comments"><b>Comments</b> <span class="color-red">*</span></label>
           <!--  If you are looking to order multiple items, please list the product name, ID and quantity. -->
            <textarea class="form-control <?= $popup_class ?>" id="comments" name="customer_requests" rows="6" placeholder=" If you are looking to order multiple items, please list the product name, ID and quantity. 

Please also leave any comments or special requirements. 
" required><?php hecho($_REQUEST['customer_requests']) ?></textarea>
        </div>
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6Ld2WYEnAAAAAJL089eYW4Yl-JLJ35x7M3kbTNb6"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block" id="send-btn" name="action" value="send" onclick="return validateForm();">Send us your inquiry</button>
    </form>
</div></div></div>
<br />
    <script type="text/javascript">
    if ($(window).width() >= 600){  
        $('input[type=text]').removeAttr('placeholder');
        $('input[type=email]').removeAttr('placeholder');
        $('input[type=number]').removeAttr('placeholder');
    }   

</script>
    <script type="text/javascript">

        $(document).ready(function(){
            $('input, textarea').on('focus', function(){
                $(this).css("border-color", "#52D017");
            });

            $('input, textarea').on('blur', function(){
                $(this).css("border-color", "rgb(169, 169, 169)");
            })
        });

        function validateForm() {
            var form =document.getElementById("inquiry_form");
            var qty = form.product_qty;
            var name = form.given_name;
	        var email = form.member_email;
            var phone = form.member_phone;
            var error = false;
            var msg = "";

            /*
            if (!phonenumber(phone)) {
                phone.style.borderColor = "#ff0000";
                msg += "Phone number is invalid. Please use valid phone number \r\n";
                error = true;
            } else {
                phone.removeAttribute('style');
            } */

            if (!ValidateEmail(email.value)) {
                email.style.borderColor = "#ff0000";
                msg += "Email is invalid. Please use valid email.\r\n";
                error = true;
            } else {
                email.removeAttribute('style');
            }

	    if (name.value.length == 0) {
		name.style.borderColor = "#ff0000";
		error = true;
		msg += "Name/Company field is empty. \r\n";
	    } else {
		name.removeAttribute('style');
	    }

            if (isNaN(qty.value)) {
                qty.style.borderColor = "#ff0000";
                error = true;
            } else {
                qty.removeAttribute('style');
            }

            if (error) {
                alert(msg);
            }

            // ReCaptcha Verification
            var response = grecaptcha.getResponse();
            if(response.length == 0)
            {
                alert("Please verify you are not a robot");
                error = true;
            }
            //end

            return (!error);
        }

    function phonenumber(inputtxt) {
        var phoneno = /^\d{10}$/;
        if(inputtxt.value.match(phoneno)) {
            return true;
        } else {
            return false;
        }
    }

    function ValidateEmail(mail) {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
            return true;
        }
            return false;
    }
    </script>

<?php
include_once __DIR__ . '/footer.inc.php';
