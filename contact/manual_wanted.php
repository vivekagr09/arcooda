<?php
die('This page has been disabled');

/*
I have MAJOR issues with this file, the most serious:
a non-authenticated person can upload a file to the public area of the website.
eg. I upload adminer.php, it will be saved as /images/img_wanted/product_123456789.php

Another issue is the extraction of customer information by supplying their email address
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
<script>
$(document).ready(function(){
   
	$("#search-box").keyup(function(){
		var val = $(this).val();
		if (val.length > 0) {
			$.ajax({
			url: '/s',
			method: 'get',
			data: {s: val,id:$('#product_type').val()},
			beforeSend: function(){
				$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 265px");
			},
			success: function(r){
				if(r.data.length>0){
				var contents = "<p style='font-size: 12px; color: red; font-style: italic;'>Please click any below search results to link the product. If there are no suitable results, please write  the full name of the Wanted Item and fill in the below fields.</p>";
				for(let x in r.data) {
					contents += "<a class='suggestions__item suggestions__product' style='cursor:pointer' onclick='selectproductval(\""+r.data[x].name+"\","+r.data[x].id+",\""+r.data[x].image+"\","+r.data[x].type+")''>";

					contents += "<div class='suggestions__product-image'><img src='"+ r.data[x].image +"' width='96' height='96' /></div>";
					contents += "<div class='suggestions__product-info'><div class='suggestions__product-name'>"+ r.data[x].name +"</div></div>";
					contents += "<div class='suggestions__product-price'>"+ r.data[x].price +"</div>";

					contents += "</a>";
				}
			}else{
				var contents ='';
			}
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(contents);
				$("#search-box").css("background","#FFF");
			}
			});
		}else{
			$("#suggesstion-box").hide();
			$("#suggesstion-box").html('');
		}
	});
});
$(function() {

});
function selectproductval(val,id,image,type) {
	$("#search-box").val(val);
	$("#product_id").val(id);
	$("#product_type_id").val(type);
	$('#add-product-image').hide();
	$('#product_image').show();
	$('#display-image').attr('src',image);
	$(".suggestions__item").hide();
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
}

$invoice_currency  = 'USD';
$_GLOBALS["site_options"]["DEFAULT_CURRENCY"] = 'USD';

// Process inquiry
$issues = [];
$insert_id=0;
if (array_key_exists('action', $_REQUEST) && 'send' == $_REQUEST['action']) {
	 foreach ([
		'product_name',
		'section_id',
		'country_location',
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
		if($_POST['item']['id']==0 && isset($_FILES['product_image'])){
			$file_orig = $_FILES['product_image']['name'];
			$file_temp = $_FILES['product_image']['tmp_name'];
			$error     = $_FILES['product_image']['error'];
			$size      = intval($_FILES['product_image']['size']);
			$file_ext = substr($file_orig, strrpos($file_orig, '.'));
			$file_new  = "product_" . time() . '.' . $file_ext;
			$dir       = $_SERVER['DOCUMENT_ROOT'] . '/images/img_wanted/';
			move_uploaded_file($file_temp, $dir . $file_new);
			$insert_wanted['image'] = formatSQLString(trim($file_new));
		}
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
			'section_id'      =>'section_id'
		] as $k => $v) {
			if (array_key_exists($k, $_POST['item'])) {
				$insert_wanted[$v] = formatSQLString(trim($_POST['item'][$k]));
			}
		}
		 //$insert_wanted['section_id'] =$insert_wanted['type'];
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
	<form class="<?= $popup_class ?> inquiry_form" id="inquiry_form" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="item[id]" value="" id="product_id" value="0"/>
		<input type="hidden" name="item[type]" value="" id="product_type_id" value="0"/>
		<div class="btn btn-warning btn-lg btn-block" style="margin-bottom: 16px;width: 40%;">Item Details</div>
		<div class="form-group row">
			<label for="_product_type" class="col-sm-3 col-form-label"><b>Category/Type</b></label>
			<select name="item[section_id]" class="form-control col-sm-9" readonly="true" required id="product_type">
			<?php
				foreach (productGetOptionList('category_section') as $section_id => $section_name) {
				?>
					<option value="<?=$section_id?>"> <?= $section_name?></option>
				<?php
				}
			   
				?>
			</select>
		</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label"><b>Wanted Item</b></label>
			<input type="text" class="form-control col-sm-9" id="search-box" name="item[product_name]" value="" required="required" data-msg-required="Please enter product name"/>
			
		</div>
		<div class="form-group row">
			 <label for="_product_name" class="col-sm-3 col-form-label"></label>
			  <div id="suggesstion-box" class="col-sm-9"></div>
		</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label"><b>Preferred Location</b></label>
			<select name="item[country_location]" class="form-control col-sm-9" style="color:red;" required="required" data-msg-required="Please select country">
			<option value="" class="redText">Please choose country/Delivery Location</option>
			<?=formatHTMLOptions(memberGetOptionList('country'))?>
			</select>
		</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label"><b>Add your wanted notes</b></label>
			<textarea name="item[wanted_notes]"class="form-control col-sm-9" rows="8"></textarea> 
		</div>
		<div class="form-group row" id="add-product-image">
			<label for="_product_image" class="col-sm-3 col-form-label"><b>Add Images</b></label>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="product_image" class="form-control col-sm-9" accept="image/*"/> 
		</div>
		<div class="form-group row" style="margin-bottom: 28px;display:none;" id="product_image">
			   <img src="" width="100px;" id="display-image">';
		</div>

	   <div class="btn btn-warning btn-lg btn-block" style="margin-bottom: 30px;width: 40%;">Price & Quantity</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label" ><b>Target Price</b></label>
			<input type="text" class="form-control col-sm-3" name="item[price]" placeholder="Price">
			<select name="item[currency]" class="form-control col-sm-5" style="margin-left: 3px;" required>
			<?=formatHTMLOptions(productGetOptionList('sales_currency'),'USD')?>
			</select>
			  <label for="_product_name" class="col-sm3 col-form-label" ></label>
			<label for="_product_name" class="col-sm9 col-form-label" style="margin-left: 25%;font-size: 14px;color:#dc3545;"><i>If no target price please leave price empty</i></label>
		</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label" ><b>Qty Needed</b></label>
		   <input type="number" class="form-control col-sm-3" name="item[qty]" placeholder="Quantity" min="1" value="1" required="required">
		</div>
		 <div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label" ><b>Request Duration</b></label>
		   <select name="item[request_duration]" class="form-control col-sm-3" required="required">
				<option value="2 weeks">2 weeks</option>
				<option value="4 weeks" selected>4 weeks</option>
				<option value="unlimited">Unlimited</option>
			</select>
			<label for="_product_name" class="col-sm5 col-form-label" style="margin-left: 3px;font-size: 14px;color:#dc3545;"><i> How long do you want us to search</i></label>
		</div>
		<div class="form-group row">
			<label for="_product_name" class="col-sm-3 col-form-label" ><b>Order Time</b></label>
		   <select name="item[order_time]" class="form-control col-sm-3" required="required">
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
			<input type="text" class="form-control col-sm-9" id="member_name" name="item[first_name]" value="<?php hecho(trim($_REQUEST['item']['first_name'] ?? $_GLOBALS['member']['first_name'] ?? $_GLOBALS['member']['name'] ?? '')) ?>" placeholder="First name" required="required" data-msg-required="Please enter first name"/>
		</div>
		<div class="form-group row">
			<label for="member_name" class="col-sm-3 col-form-label"><b>Last Name</b></label>
			<input type="text" class="form-control col-sm-9" id="member_namelast" name="item[last_name]" value="<?php hecho($_REQUEST['item']['last_name'] ?? $_GLOBALS['member']['last_name'] ?? $_GLOBALS['member']['surname'] ?? '') ?>" placeholder="Last name" required="required" data-msg-required="Please enter last name"/>
		</div>
		<div class="form-group row">
			<label for="member_email" class="col-sm-3 col-form-label"><b>Email</b></label>
			<input type="email" class="form-control col-sm-9" id="member_email" name="item[email_address]" value="<?php hecho($member['email_address']) ?>" placeholder="Your email address" required="required" data-msg-required="Please enter email"/>
		</div>
		<div class="form-group row">
			<label for="member_phone" class="col-sm-3 col-form-label"><b>Phone Numbers</b></label>
			<input type="text" class="form-control col-sm-9" id="member_phone" name="item[phone_number]" value="<?php hecho($member['phone_number']) ?>" placeholder="Your contact number"/>
		</div>
		<div class="form-group row">
			<label for="member_location" class="col-sm-3 col-form-label"><b>Your Location</b></label>
			<select class="form-control col-sm-9" id="member_location" name="item[member_location]" style="width:100%;" required="required" data-msg-required="Please select your location">
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
