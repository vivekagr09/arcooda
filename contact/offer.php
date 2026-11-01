<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<style type="text/css">
select option {
    color: #000;
}
.error{
    color: red;
    padding-left: 25%;
}
</style>
<script type="text/javascript">
    function closewanted(key){
        window.opener.closewanted(key);
        window.close();
    }
</script>

<?php
/**
 * Generate an inquiry email from form, and send to sales email address.
 * @Author: Mingze Xi <mxi@highwaygroup.net>
 * Added Date: 26/10/2015
 * Last Update: 26/10/2015
 */

use app\models\PHPMailerEmail;

include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberIdFromEmail, memberFromDatabase, memberToDatabase
moduleLoad('product'); //productFromDatabase

// Function to get the client IP address
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
if (array_key_exists('member', $_GLOBALS)) {
    $member = $_GLOBALS['member'];
    //echo '<pre>'; print_r($member); die;
}

$invoice_currency  = 'USD';
$_GLOBALS["site_options"]["DEFAULT_CURRENCY"] = 'USD';

// Process inquiry
$issues = [];
$insert_id=0;
if (array_key_exists('action', $_REQUEST) && 'send' == $_REQUEST['action']) {
     foreach ([
        'wanted_id',
        'product_name',
        'country_location',
        'price',
        'currency',
        'qty',
        'first_name',
        'last_name',
        'email_address',
        'location',
    ] as $k) {
        if ('' === trim($_POST['item'][$k] ?? '')) {
            $issues[$k] = '' . str_replace('_',' ', $k) . ' field is required';
        }
    }
    if(empty($_POST['g-recaptcha-response'])){
        $issues['captcha']='Invalid Captcha';
    }
    if(count($issues)==0){
        $member_email = $_REQUEST['item']['email_address'] ?? '';
        if (!array_key_exists('member', $_GLOBALS)) {

            // Extend the Member Record
            $member_id = memberIdFromEmail($member_email);
            if ($member_id) {
                //$_GLOBALS['member'] = memberFromDatabase($member_id);
            } else {
                $member_data=[];
                $member_data['name']          = $member_data['first_name'] = $_REQUEST['item']['first_name'];
                $member_data['surname']       = $member_data['last_name']  = $_REQUEST['item']['last_name'];
                $member_data['email_address'] = $_REQUEST['item']['email_address'];
                $member_data['company']       = $_REQUEST['item']['company'];
                $member_data['country']       = $_REQUEST['item']['location'];
                $member_id = memberToDatabase($member_data);
                $member = memberFromDatabase($member_id);

                //$_GLOBALS['member'] = $member;
            }
        }
        $insert_wantedoffer  = array();
        $insert_wantedoffer['status'] = formatSQLString('under consideration');
        $insert_wantedoffer['date_added'] = formatSQLDateTime();
        foreach ([
            'wanted_id' => 'wanted_id',
            'first_name' => 'name',
            'description'  => 'description',
            'country_location' => 'product_location',
            'price'      => 'price',
            'currency'     => 'currency',
            'qty'    => 'quantity_offered',
            'email_address'   => 'email',
            'location'   => 'country',
            'company'	=>'company'
        ] as $k => $v) {
            if (array_key_exists($k, $_POST['item'])) {
            	if($k=='first_name'){
            		$name=$_POST['item']['first_name'].' '.$_POST['item']['last_name'];
            		$insert_wantedoffer[$v] = formatSQLString(trim($name));
            	}else{
            		$insert_wantedoffer[$v] = formatSQLString(trim($_POST['item'][$k]));
            	}
                
            }
        }
         if (count($insert_wantedoffer)) {
                $sql = "INSERT INTO wanted_offers(`" . implode('`, `', array_keys($insert_wantedoffer)) . "`) VALUES(" . implode(", ", $insert_wantedoffer) . ")";
                $DB->query($sql);
                $insert_id= $DB->insert_id();
            }else{
                $issues['invalid']='There are some issue try again later';
            }
    ob_start();
    ?>
    <?php
        ob_start();
        // send email to "sales@arcooda.com"
    ?>
        
    <p style="width:100%"> ----------------------------------------------------------</p>
         <div> OFFER REQUEST</div>
    <p style="width:100%">----------------------------------------------------------</p>

<p>A request for a wanted item was submitted, details are listed below:</p>
        <table class="table table-bordered">
        <tr><th>Customer Name     : </th> <td> <?php echo $_REQUEST['item']['first_name'].' '.$_REQUEST['item']['last_name'];?> </td></tr>
        <tr><th>Customer Email    :</th> <td> <?php echo $_REQUEST['item']['email_address'];?> </td></tr>

        <tr><th>Wanted Item       :</th> <td><?php echo $_REQUEST['item']['product_name'];?> </td></tr>
        </table>
    <p>To view & review this offer request in the admin, please follow the link below.</p>
    <p><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'///webadmin/wanted/wanted_edit.php?approved=n&wanted_id='.$_REQUEST['item']['wanted_id'];?> </p>

    <?php

        $message = ob_get_clean();
       /* $email           = new PHPMailerEmail();
        $email->Subject  =  $_REQUEST['item']['product_name'] . " (ID = " . $_REQUEST['item']['id'] . ") - New wanted request " . $insert_id;
        $email->Body     = $message;
        $email->IsHtml(true);
        $email->AddAddress("sales@arcooda.com");
        $result = $email->Send();*/
        /*redirect('?action=complete&id=' . $insert_id . '');
        return;*/
    }
}
if ($insert_id) {
    include_once __DIR__ . '/header.inc.php';
    echo '<div class="logo__image"><img src="/brand.png" alt="logo" /></div>';
    echo '<div style = "margin: 20px 10px; border-radius: 25px; background: #52D017; color: #fff; padding: 20px 10px; ">';
    echo '<h1 style="color: #ffffff; font-size: 50px;font-weight: 700;">Wanted - Your Offer to Us </h1>';
    echo '<hr style=" border-top: 3px solid #fff;">';
    echo '<h1 style="color: #ffffff; font-size: 26px;">Thank You</h1>';
    echo '<h1 style="color: #ffffff; font-size: 17px;">Your sales offer has been sent to our team and we will share with our customer immediately. We will be in touch as soon as we have further updates.</h1>';
    echo '</div>';
    include_once __DIR__ . '/footer.inc.php';
    return;
}


include_once __DIR__ . '/header.inc.php';
//$popup_class ???

$product=productFromDatabase($_REQUEST['id']);
//echo '<pre>'; print_r($product); die;
?>
<br />
<div class="container"><div class="row"><div class="col-sm-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5" style="margin:auto;">
    <?php
        if(count($issues)>0){
            echo '<ul style="color:red;">';
            foreach ($issues as $key => $issue) {
                echo '<li>'.$issue.'</li>';
            }
            echo '</ul>';
        }
    ?>
    <h1 style="margin-bottom: 25px;">Send your Offer</h1>
    <form class="<?= $popup_class ?> inquiry_form" id="inquiry_form" method="post" action="" onsubmit="return validateForm(this);">
        <input type="hidden" name="item[wanted_id]" value="<?php hecho($_REQUEST['wanted_id']) ?>" />
        <div class="btn btn-warning btn-lg btn-block" style="background:#e885a7;margin-bottom: 16px;width: 40%;">About My Offer</div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Product Name</b></label>
            <input type="text" class="form-control col-sm-9" id="_product_name" name="item[product_name]" value="<?php hecho($_REQUEST['product_name']) ?>" readonly="readonly" required />
        </div>

        <div class="form-group row">
            <label for="_product_type" class="col-sm-3 col-form-label"><b>I can offer</b></label>
            <input type="number" class="form-control col-sm-6" name="item[qty]" placeholder="Quantity" min="1" required data-msg-required="Please enter Quantity" value="1">
            <label for="_product_name" class="col-sm-3 col-form-label">Units/Qty</label>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Product  Location</b></label>
            <select name="item[country_location]" class="form-control col-sm-9" style="color:red;" required data-msg-required="Please select product location">
                <option value="">Country Location</option>
            <?=formatHTMLOptions(getCountryList())?>
            </select>
        </div>
          <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label" ><b>My Sell Price</b></label>
            <input type="text" class="form-control col-sm-4" name="item[price]" placeholder="Price" required  data-msg-required="Please enter sell price">
            <select name="item[currency]" class="form-control col-sm-4" style="margin-left: 3px;" required="required" >
            <?=formatHTMLOptions(productGetOptionList('sales_currency'),'USD')?>
            </select>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Offer Description</b></label>
            <textarea name="item[description]"class="form-control col-sm-9" rows="8"></textarea> 
        </div>
       
        <div class="btn btn-warning btn-lg btn-block" style="background:#e885a7;margin-bottom: 30px;margin-top: 20px;width: 40%;">About Me</div>
        <div class="form-group row">
            <label for="member_name" class="col-sm-3 col-form-label"><b>First Name</b></label>
            <input type="text" class="form-control col-sm-9" id="member_name" name="item[first_name]"  placeholder="First name" required value="<?php hecho($_REQUEST['item']['first_name'] ?? $_GLOBALS['member']['first_name'] ?? $_GLOBALS['member']['name'] ?? '') ?>" data-msg-required="Please enter first name"/>
        </div>
        <div class="form-group row">
            <label for="member_name" class="col-sm-3 col-form-label"><b>Last Name</b></label>
            <input type="text" class="form-control col-sm-9" id="member_name" name="item[last_name]"  placeholder="Last name" value="<?php hecho($_REQUEST['item']['last_name'] ?? $_GLOBALS['member']['last_name'] ?? $_GLOBALS['member']['surname'] ?? '') ?>"  required="required" data-msg-required="Please enter last name"/>
        </div>
        <div class="form-group row">
            <label for="member_name" class="col-sm-3 col-form-label"><b>Company Name</b></label>
            <input type="text" class="form-control col-sm-9" id="company" name="item[company]"  placeholder="Company Name" value="<?php hecho(trim($member['company'])) ?>"/>
        </div>
        <div class="form-group row">
            <label for="member_email" class="col-sm-3 col-form-label"><b>Email</b></label>
            <input type="email" class="form-control col-sm-9" id="member_email" name="item[email_address]"  placeholder="Your email address" required value="<?php hecho(trim($member['email_address'])) ?>"  data-msg-required="Please enter email"/>
        </div>
        <div class="form-group row">
            <label for="member_location" class="col-sm-3 col-form-label"><b>Your Location</b></label>
            <select class="form-control col-sm-9" id="location" name="item[location]" style="width:100%;" required  data-msg-required="Please select your location">
                <option value=""></option>
                <?= formatHTMLOptions(getCountryList(),$member['country']) ?>
            </select>
        </div>
        <div class="form-group row">
            <label for="member_location" class="col-sm-3 col-form-label"></label>
            <div class="g-recaptcha " data-sitekey="6LcaM-0UAAAAAOOjr8A4FNnYnzLPOo0uMn3fBvM2"></div>
        </div>
        <button type="submit" class="btn btn-warning btn-lg btn-block" id="send-btn" name="action" value="send" style="background:#e885a7;">Send us your offer</button>
    </form>
</div></div></div>
<br />

    <script type="text/javascript">
        $(".inquiry_form").each(function() {
          $(this).validate({
            rules: {
              
            },
            // Specify validation error messages
            messages: {
            
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
            submitHandler: function(form) {
              form.submit();
            }
          });
        });
        $(document).ready(function(){
            $('input, textarea').on('focus', function(){
                $(this).css("border-color", "#52D017");
            });

            $('input, textarea').on('blur', function(){
                $(this).css("border-color", "rgb(169, 169, 169)");
            })
        });

        function validateForm(form) {
            var qty = form.product_qty;
            var name = form.member_name;
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
