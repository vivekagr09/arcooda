<?php
die('This page has been disabled');

/*
It's possible to extract customer information by supplying a valid email address
*/

?>
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

moduleLoad('member'); //memberIdFromEmail, memberFromDatabase, memberToDatabase, memberGetOptionList
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
        'id',
        'product_name',
        'type',
        'country_location',
        'price',
        'currency',
        'qty',
        'request_duration',
        'order_time',
        'first_name',
        'last_name',
        'email_address',
        'member_location',
    ] as $k) {
        if ('' === trim($_POST['item'][$k] ?? '')) {
            $issues[$k] = '' . str_replace('_',' ', $k) . ' field is required';
        }
    }
    if(empty($_POST['g-recaptcha-response'])){
        $issues['captcha']='Invalid Captcha';
    }
    if(count($issues)==0){
        $product = productFromDatabase($_POST['item']['id']);
        if ($product['active'] != 'y') {
            header("Location:invalid_request");
        }
        $member_email = $_REQUEST['item']['email_address'] ?? '';
        if (!array_key_exists('member', $_GLOBALS)) {

            // Extend the Member Record
            $member_id = memberIdFromEmail($member_email);

            if ($member_id) {
                $_GLOBALS['member'] = memberFromDatabase($member_id);
            } else {
                $member_data=[];
                $member_data['name']          = $member_data['first_name'] = $_REQUEST['item']['first_name'];
                $member_data['surname']       = $member_data['last_name']  = $_REQUEST['item']['last_name'];
                $member_data['email_address'] = $_REQUEST['item']['email_address'];
                $member_data['phone_number']  = $_REQUEST['item']['phone_number'];
                $member_data['country']       = $_REQUEST['item']['member_location'];
                $member_id = memberToDatabase($member_data);
                $member = memberFromDatabase($member_id);
                $_GLOBALS['member'] = $member;
            }
        }
        $insert_wanted  = array();
        $insert_wanted['active'] = formatSQLString('n');
        $insert_wanted['member_id'] = $member_id;
        $insert_wanted['date_added'] = formatSQLDateTime();
        $_POST['item']['section_id']=getcategoryParentFromDatabase($_POST['item']["type"]);
        $_POST['item']["type"]=$_POST['item']['section_id'];
        foreach ([
            'id' => 'item_id',
            'product_name' => 'item',
            'type'         => 'type',
            'wanted_notes'  => 'description',
            'country_location' => 'pref_location',
            'price'      => 'target_price',
            'currency'     => 'target_currency',
            'qty'    => 'qty_needed',
            'request_duration' => 'duration_days',
            'order_time'    => 'order_time',
            'first_name'    => 'customer_name',
            'email_address'   => 'customer_email',
            'section_id'     =>'section_id'
        ] as $k => $v) {
            if (array_key_exists($k, $_POST['item'])) {
                $insert_wanted[$v] = formatSQLString(trim($_POST['item'][$k]));
            }
        }
         if (count($insert_wanted)) {
                $sql = "INSERT INTO wanted(`" . implode('`, `', array_keys($insert_wanted)) . "`) VALUES(" . implode(", ", $insert_wanted) . ")";
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
         <div> WANTED REQUEST</div>
    <p style="width:100%">----------------------------------------------------------</p>

<p>A request for a wanted item was submitted, details are listed below:</p>
        <table class="table table-bordered">
        <tr><th>Customer Name     : </th> <td> <?php echo $_REQUEST['item']['first_name'].' '.$_REQUEST['item']['last_name'];?> </td></tr>
        <tr><th>Customer Email    :</th> <td> <?php echo $_REQUEST['item']['email_address'];?> </td></tr>

        <tr><th>Wanted Item       :</th> <td><?php echo $_REQUEST['item']['product_name'];?> </td></tr>

        <tr><th>Wanted ID         : </th><td><?php echo $_REQUEST['item']['id'];?> </td></tr>
        </table>
    <p>To view & activate this wanted request in the admin, please follow the link below.</p>
    <p><?=htmlspecialchars(relative_url('/webadmin/wanted?action=edit&wanted_id=' . $_REQUEST['item']['id']))?></p>

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
    echo '<h1 style="color: #ffffff; font-size: 50px;font-weight: 700;">Wanted - Your Request </h1>';
    echo '<hr style=" border-top: 3px solid #fff;">';
    echo '<h1 style="color: #ffffff; font-size: 26px;">Thank You</h1>';
    echo '<h1 style="color: #ffffff; font-size: 17px;">Your wanted request has been submitted , We will be adding adding this information to the wesite very soon</h1>';
    echo '<p>Would you like to</p>';
    echo '<ul style="color: #fff; font-size: 17px;"><li><a  href="#" style="color: #fff;" onclick="closewanted(0);">Request another wanted item</a></li><li><a href="#" style="color: #fff;" onclick="closewanted(1);">Go to the front page</li></a></ul>';
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
    <h1 style="margin-bottom: 25px;">Add to Wanted</h1>
    <form class="<?= $popup_class ?> inquiry_form" id="inquiry_form" method="post" action="" onsubmit="return validateForm(this);">
        <input type="hidden" name="item[id]" value="<?php hecho($_REQUEST['id']) ?>" />
        <div class="btn btn-warning btn-lg btn-block" style="margin-bottom: 16px;width: 40%;">Item Details</div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Product Name</b></label>
            <input type="text" class="form-control col-sm-9" id="_product_name" name="item[product_name]" value="<?php hecho($_REQUEST['product_name']) ?>" readonly="readonly" required data-msg-required="Please enter product name"/>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Product Id</b></label>
            <span class="form-control col-sm-9" id="_product_id" ><?php hecho($_REQUEST['id']) ?></span>
        </div>

        <div class="form-group row">
            <label for="_product_type" class="col-sm-3 col-form-label"><b>Category/Type</b></label>
            <select name="item[type]" class="form-control col-sm-9" readonly="true" required data-msg-required="Please select category">
            <?php
                $type_tree = productGetOptionList('type_tree');
                foreach (productGetOptionList('category_section') as $section_id => $section_name) {
                ?>
                    <optgroup label="<?php hecho($section_name)?>" class="optgrp_section-<?=$section_id?>">
                <?php
                    foreach ($type_tree['type_tree'][$section_id] as $temp) {
                ?>
                            <option value="<?=$temp?>" <?=($product['type'] == $temp) ? ' selected="selected"' : 'disabled'?>><?=$type_tree['category'][$temp]?></option>
                        <?php
                    }
                ?>
                    </optgroup>
                <?php
                }
               
                ?>
            </select>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Preferred Location</b></label>
            <select name="item[country_location]" class="form-control col-sm-9" style="color:red;" required data-msg-required="Please select  location">
            <option value="" class="redText">Please choose country/Delivery Location</option>
            <?=formatHTMLOptions(memberGetOptionList('country'))?>
            </select>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label"><b>Add your wanted notes</b></label>
            <textarea name="item[wanted_notes]"class="form-control col-sm-9" rows="8"></textarea> 
        </div>
        <div class="form-group row" style="margin-bottom: 28px;">
            <?php 
                if($product['thumb_url']){
                    echo '<img src="'.$product['thumb_url'].'" width="100px;">';
                }
            ?>
        </div>

       <div class="btn btn-warning btn-lg btn-block" style="margin-bottom: 30px;width: 40%;">Price & Quantity</div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label" ><b>Target Price</b></label>
            <input type="text" class="form-control col-sm-3" name="item[price]" placeholder="Price" >
            <select name="item[currency]" class="form-control col-sm-5" style="margin-left: 3px;">
            <?=formatHTMLOptions(productGetOptionList('sales_currency'), $product['sales_currency'])?>
            </select>
              <label for="_product_name" class="col-sm3 col-form-label" ></label>
            <label for="_product_name" class="col-sm9 col-form-label" style="margin-left: 25%;font-size: 14px;color:#dc3545;"><i>If no target price please leave price empty</i></label>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label" ><b>Qty Needed</b></label>
           <input type="number" class="form-control col-sm-3" name="item[qty]" placeholder="Quantity" min="1" required  data-msg-required="Please enter quantity">
        </div>
         <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label" ><b>Request Duration</b></label>
           <select name="item[request_duration]" class="form-control col-sm-3" required>
                <option value="2 weeks">2 weeks</option>
                <option value="4 weeks">4 weeks</option>
                <option value="unlimited">Unlimited</option>
            </select>
            <label for="_product_name" class="col-sm5 col-form-label" style="margin-left: 3px;font-size: 14px;color:#dc3545;"><i> How long do you want us to search</i></label>
        </div>
        <div class="form-group row">
            <label for="_product_name" class="col-sm-3 col-form-label" ><b>Order Time</b></label>
           <select name="item[order_time]" class="form-control col-sm-3" required>
                <option value="immediately">Immediately</option>
                <option value="2 weeks">2 weeks</option>
                <option value="4 weeks">4 weeks</option>
                <option value="unknown">Unknown</option>
            </select>
            <label for="_product_name" class="col-sm5 col-form-label" style="margin-left: 3px;font-size: 14px;color:#dc3545;"><i>If located how soon will you order</i></label>
        </div>
        <div class="btn btn-warning btn-lg btn-block" style="margin-bottom: 30px;margin-top: 20px;width: 40%;">Contact Details</div>
        <div class="form-group row">
            <label for="member_name" class="col-sm-3 col-form-label"><b>First Name</b></label>
            <input type="text" class="form-control col-sm-9" id="member_name" name="item[first_name]" value="<?php hecho($_REQUEST['item']['first_name'] ?? $_GLOBALS['member']['first_name'] ?? $_GLOBALS['member']['name'] ?? '') ?>" placeholder="First name" required  data-msg-required="Please enter first name"/>
        </div>
        <div class="form-group row">
            <label for="member_name" class="col-sm-3 col-form-label"><b>Last Name</b></label>
            <input type="text" class="form-control col-sm-9" id="member_name" name="item[last_name]" value="<?php hecho($_REQUEST['item']['last_name'] ?? $_GLOBALS['member']['last_name'] ?? $_GLOBALS['member']['surname'] ?? '') ?>" placeholder="Last name" required  data-msg-required="Please enter last name"/>
        </div>
        <div class="form-group row">
            <label for="member_email" class="col-sm-3 col-form-label"><b>Email</b></label>
            <input type="email" class="form-control col-sm-9" id="member_email" name="item[email_address]" value="<?php hecho($member['email_address']) ?>" placeholder="Your email address" required data-msg-required="Please enter email"/>
        </div>
        <div class="form-group row">
            <label for="member_phone" class="col-sm-3 col-form-label"><b>Phone Numbers</b></label>
            <input type="text" class="form-control col-sm-9" id="member_phone" name="item[phone_number]" value="<?php hecho($member['phone_number']) ?>" placeholder="Your contact number"/>
        </div>
        <div class="form-group row">
            <label for="member_location" class="col-sm-3 col-form-label"><b>Your Location</b></label>
            <select class="form-control col-sm-9" id="member_location" name="item[member_location]" style="width:100%;" required data-msg-required="Please select your location">
                <option value=""></option>
                <?= formatHTMLOptions(memberGetOptionList('country'), $member['country']) ?>
            </select>
        </div>
        <div class="form-group row">
            <label for="member_location" class="col-sm-3 col-form-label"></label>
            <div class="g-recaptcha " data-sitekey="6LcaM-0UAAAAAOOjr8A4FNnYnzLPOo0uMn3fBvM2"></div>
        </div>
        <button type="submit" class="btn btn-warning btn-lg btn-block" id="send-btn" name="action" value="send">Add Wanted Item</button>
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
