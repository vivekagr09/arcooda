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
moduleLoad('member'); //memberCommentAdd, memberFromDatabase, memberIdFromEmail, memberGetOptionList
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
$product_database=productFromDatabase($_REQUEST['id']);
$invoice_currency  = 'USD';
$_GLOBALS["site_options"]["DEFAULT_CURRENCY"] = 'USD';
if (array_key_exists('member', $_GLOBALS)) {
    $member = memberFromDatabase($_GLOBALS['member']['member_id']);
    $member['member_email']=$member['email_address'];

}
// Process inquiry
if (array_key_exists('action', $_REQUEST) && 'send' == $_REQUEST['action']) {

    if (!array_key_exists('member', $_GLOBALS)) {
        $member_email=$_REQUEST['member_email'];
        // Extend the Member Record
        $member_id = memberIdFromEmail($member_email);
        if ($member_id) {
            $_GLOBALS['member'] = memberFromDatabase($member_id);
        } else {
            $member_id = memberFromDatabase($member);

            $member = memberFromDatabase($member_id);
            $_GLOBALS['member'] = $member;
        }
    }
    // @fixme This needs some form of sanitizing before it gets to the webadmin
    if(isset($_FILES['serial_image']['name'])){
        $file_orig = $_FILES['serial_image']['name'];
        $file_temp = $_FILES['serial_image']['tmp_name'];
        $error     = $_FILES['serial_image']['error'];
        $caption   = ('' != $posted['subject']) ? $posted['subject'] : $_FILES['serial_image']['name'];
        $size      = intval($_FILES['serial_image']['size']);
        $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }
        if ($size && !$error) {
            if ($DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`new_members_attachments` (`member_id`, `date`, `caption`, `file_orig`, `filetype`, `size`) VALUES (%d, NOW(), %s, %s, %s, %d)",
                $member_id,
                formatSQLString($caption),
                formatSQLString($file_orig),
                formatSQLString($file_ext),
                $size
            ))) {
                $ss_id     = $DB->insert_id();
                $file_new  = "member_" . $member_id . '_' . $ss_id . '.' . $file_ext;
                $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/member/attachment/';
                if (move_uploaded_file($file_temp, $dir . $file_new)) {
                    $DB->query("UPDATE `highwaygroup`.`new_members_attachments` SET `filename` = " . formatSQLString($file_new) . " WHERE `id` = {$ss_id}");
                }
            }
        }
    }
    if(!empty($_REQUEST['comments'])){
        memberCommentAdd($member_id, $_REQUEST['comments']);
    }
    include __DIR__ . '/build_invoice.inc.php';
    $invoice_id = invoiceToDatabase($invoice);
    invoiceProductsToDatabase($invoice_id, $invoice_products);

    $invoice = invoiceFromDatabase($invoice_id);
    $invoice_products = invoiceProductsFromDatabase($invoice_id);

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
            $comments
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
            $comments
        )),
    ));

    $subject = $_REQUEST['subject'];
    $country_list = invoiceGetOptionList('bill_country');
    ob_start();
?>
<p>Order Status: <span style="color:#f00;">Repair Created</span></p>
<p>A new Repair invoice  has been created . The details are as follow:</p>
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

<p><b>Link: </b> <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=view&invoice_id=' . $invoice_id))?>">View Inquiry</a> . <a href="<?=htmlspecialchars(relative_url('/webadmin/invoice/?action=edit&invoice_id=' . $invoice_id))?>">Edit Inquiry</a></p>
<?php

   $message = ob_get_clean();
     /*mail("sales@arcooda.com", $subject, $message);

    $email           = new PHPMailerEmail();
    $email->Subject  = 'Product Registration – Inquiry '.$invoice['invoice_number'].' '.$product_database['product_name'] . " (ID = " . $product_database['product_id'] . ")";
    $email->Body     = $message;
    $email->IsHtml(true);
    $email->AddAddress("sales@arcooda.com");*/
    //$result = $email->Send();

    // send email to "sales@arcooda.com"

    redirect('?action=complete&id=' . $invoice_id . '&rand_num=' . $invoice['rand_num']);
    return;
}

if (array_key_exists('action', $_REQUEST) && 'complete' == $_REQUEST['action']) {
    include_once __DIR__ . '/header.inc.php';
    echo '<div style = "margin: 20px 10px; border-radius: 25px; background: #52D017; color: #fff; padding: 20px 10px; text-align: center; ">';
    echo '<h1 style="color: #ffffff; font-size: 26px;">REFERENCE = RAR#' . $_REQUEST['id'] . '</h1>';
    echo '<div style="padding: 50px 0; font-size: 25px; ">Thank you for registering your product. Our team will contact you soon with all details about gaining access to the Owner Section.</div>';
    echo '</div>';
    include_once __DIR__ . '/footer.inc.php';
    return;
}


include_once __DIR__ . '/header.inc.php';
//$popup_class ???

?>

<style>
label {
    margin-bottom: 10px !important;
    margin-top: 10px;
}

</style>
<br />
<div class="container"><div class="row"><div class="col-sm-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5" style="margin:auto;">
    <h1>Product Registration</h1>
    <i>Please use the below form to register your machine and game software. Your purchase will be linked to your account allowing you access to product news, updates and exclusive member specials.</i>
    <hr />
   
    <form class="<?= $popup_class ?> inquiry_form" id="inquiry_form" method="post" action="" onsubmit="return validateForm(this);" enctype="multipart/form-data">
        <?php 
        if($product_database['order_rule']=='invoice_only_for_restricted_owners'){
            echo '<label for="product_id"><b>Select Register Product</b></label>';
            echo '<select class="form-control form-control-lg" id="product_id" name="id" style="width:100%;background: #9bddd8;">'; 
             if($product_database['product_availabe_for']){
                    foreach (explode(',', $product_database['product_availabe_for']) as $proid) {
                        $prod =productFromDatabase($proid);
                        echo '<option value="'.$prod['id'].'">'.$prod['product_name'].'</option>';
                    }
                }
            echo '</select>';
        ?>
        <?php
        }else{
            $criteria=[];
            if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
                $criteria = array_merge(
                    $_GLOBALS['criteria_options']['product'],
                    $criteria
                );
            }
            $criteria['section']=['2','3'];
            $product_list = productSearch($criteria);
            $product_list = productSort($product_list, 'product_name');
            echo '<label for="product_id"><b>Select Register Product</b></label>';
            echo '<select class="form-control form-control-lg" id="product_id" name="id" style="width:100%;background: '.((isset($_REQUEST["id"]))?"#9bddd8;":"rgb(221 155 217)").';">'; 
                    echo '<option value="">Please Select Product to Register</option>';
                    foreach ($product_list as $proid) {
                        $prod =productFromDatabase($proid);
                        echo '<option value="'.$prod['id'].'" '.(($prod['id']==$_REQUEST['id'])?'selected':"").'>'.ucwords($prod['product_name']).'</option>';
                    }
            echo '</select>';
        }
        ?>

        <div class="form-group">
            <label for="first_name"><b>First Name</b></label>
            <input type="hidden" class="form-control form-control-lg" id="product_qty" name="product_qty" value="<?php hecho($_REQUEST['product_qty']) ?>" min="1" max="10000" step="1" value="1" placeholder="Numbers only" />
            <input type="text" class="form-control form-control-lg" id="first_name" name="name" value="<?php hecho(trim($member['name'])) ?>" placeholder="First name" />
        </div>
        <div class="form-group">
            <label for="member_name"><b>Last Name</b></label>
            <input type="text" class="form-control form-control-lg" id="member_name" name="surname" value="<?php hecho(trim($member['surname'])) ?>" placeholder="Last name" />
        </div>
        <div class="form-group">
            <label for="member_email"><b>Email</b></label>
            <input type="text" class="form-control form-control-lg" id="member_email" name="member_email" value="<?php hecho($member['member_email']) ?>" placeholder="Your email address" />
        </div>
        <div class="form-group">
            <label for="member_location"><b>Country</b></label>
            <select class="form-control form-control-lg" id="member_location" name="member_location" style="width:100%;">
                <option value=""></option>
                <?= formatHTMLOptions(memberGetOptionList('country'), $member['country']) ?>
            </select>
        </div>
        <div class="form-group">
            <label for="purchased_from"><b>Where Purchased</b></label>
            <input type="text" class="form-control form-control-lg" id="purchased_from" name="purchased_from" value="" placeholder="Where Purchased" />
        </div>
        <div class="form-group">
            <label for="serial_number"><b>Machine Serial # <span data-toggle="tooltip" data-placement="top" data-html="true" title="<div><b>MACHINES</b></div><p>On the back of the machine there will be a metal plate with either Arcooda, Highway Entertainment or Highway Games plate. Please check the serial number and send to us</p><div><b>SOFTWARE</b></div><p>On the game board or USB dongle, there will be a serial number. If not available, please share your invoice number</p>">(?)</span></b></label>
            <input type="text" class="form-control form-control-lg" id="serial_number" name="serial_number" value="" placeholder="Machine Serial" />
        </div>
        <div class="form-group">
            <label for="serial_number"><b>Photo of Serial #  <span data-toggle="tooltip" data-placement="top" data-html="true" title="<div><b>MACHINES</b></div><p>On the back of the machine there will be a metal plate with either Arcooda, Highway Entertainment or Highway Games plate. Please take a photo and send to us.</p><div><b>SOFTWARE</b></div><p>On the game board or USB dongle, there will be a serial number. Please take photo and send to us. If not available, please share your invoice numbe</p>">(?)</span></b></label>
                <div style="display: flex;">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input class="form-control form-control-lg" id="serial_image" name="serial_image" type="file" accept="image/png,image/jpeg" style="width: 50%">
                    <span id="image_div" style="width: 50%;margin-left: 10px;"></span>
                </div>
        </div>
         <div class="form-group">
            <label for="subject"><b>Subject</b></label>
            <input type="text" class="form-control form-control-lg" id="subject" name="subject"  placeholder="Subject" value="Please Register My product"/>
        </div>
        <div class="form-group">
            <label for="comments"><b>Comment</b></label>
            <textarea class="form-control <?= $popup_class ?>" id="comments" name="comments" rows="6" placeholder="Please type your message here."><?php hecho($_REQUEST['comments']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block" id="send-btn" name="action" value="send">Register My Product</button>
    </form>
</div></div></div>
<br />



<?php
include_once __DIR__ . '/footer.inc.php';
?>
    <script type="text/javascript">
        $("[name='serial_image']").change(function() {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#image_div").html('<img src="'+e.target.result+'" width="100">');
            };
            reader.readAsDataURL(this.files[0]);
        });
        $("body").on('change','#product_id',function(){
            if($(this).val()!=""){
                $(this).css('background','#9bddd8');
            }else{
                $(this).css('background','rgb(221 155 217)');
            }
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
            var first_name = form.first_name;
            var purchased_from = form.purchased_from;
            var email = form.member_email;
            var phone = form.member_phone;
            var serial_number = form.serial_number;
            var serial_image = form.serial_image; 
            var subject = form.subject;
            var product=form.id;
            var member_location=form.member_location;
            var error = false;
            var msg = "";

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
            msg += "last name is empty. \r\n";
            } else {
            name.removeAttribute('style');
            }

            if (product.value.length == 0) {
            product.style.borderColor = "#ff0000";
            error = true;
            msg += "Please select register product. \r\n";
            } else {
            product.removeAttribute('style');
            }
            if (first_name.value.length == 0) {
            first_name.style.borderColor = "#ff0000";
            error = true;
            msg += "First name is empty. \r\n";
            } else {
            first_name.removeAttribute('style');
            }
            if (member_location.value.length == 0) {
            member_location.style.borderColor = "#ff0000";
            error = true;
            msg += "Country is empty. \r\n";
            } else {
            member_location.removeAttribute('style');
            }
            if (purchased_from.value.length == 0) {
                purchased_from.style.borderColor = "#ff0000";
                error = true;
                msg += "Where purchased is empty. \r\n";
            } else {
                purchased_from.removeAttribute('style');
            }
            if (serial_number.value.length == 0) {
                serial_number.style.borderColor = "#ff0000";
                error = true;
                msg += "Machine Serial is empty. \r\n";
            } else {
                serial_number.removeAttribute('style');
            }
            if (serial_image.value.length == 0) {
                serial_image.style.borderColor = "#ff0000";
                error = true;
                msg += "Serial image is empty. \r\n";
            } else {
                serial_image.removeAttribute('style');
            }
            if (subject.value.length == 0) {
                subject.style.borderColor = "#ff0000";
                error = true;
                msg += "Subject is empty. \r\n";
            } else {
                subject.removeAttribute('style');
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
