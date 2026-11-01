<?php
include_once dirname(__FILE__) . "/common.inc.php";

include_once dirname(__FILE__) . "/header.inc.php";

// modified by Mingze from /gift_voucher/preset.php

// firstly get the voucher preset...
$site_id   = intval($posted['site_id']);
$preset_id = intval($posted['preset_id']);
//if(!$site_id) $site_id=9;
if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $site_id = $_GLOBALS["site_settings"]["site_id"];
}

$add    = false;
//$preset = $DB->query_first("SELECT * FROM `highwaygroup`.`gift_coupon` WHERE id=" . $preset_id, MYSQL_ASSOC);

//$preset_id=5;
 $sqlU="SELECT * FROM `highwaygroup`.`gift_coupon` WHERE id=" . $preset_id;
$res=$DB->query($sqlU);

$preset=$DB->fetch_assoc($res);

//print_r($preset);

//die;

if (!$preset) {
    $preset = array(
        "site_id" => $_GLOBALS["site_settings"]["site_id"],
    );
    $add = true;}
if (!isset($preset['value'])) {
    $preset['value'] = '0.05';
}

if (!isset($preset['bonus'])) {
    $preset['bonus'] = '0.00';
}

if (!isset($preset['expiry_value'])) {
    $preset['expiry_value'] = 3;
}
$section_list = giftcouponGetOptionList("section");
?>

<script>
var product_search_section = "";
function linkImage(type)
{
	// get value from form
	var search_for=getValue('search_for_'+type);
	if(search_for=='') alert('Please enter search terms');
	else
	{
		// action = images_thumb
		var action='images'+((type=='thumb')?'_thumb':'');
		// http://www.highway.net.au/webadmin/news/news_images.php?action=images_thumb&search_for=ff&return_message=thumb
		var url='http://www.highway.net.au/webadmin/news/?action='+action+'&search_for='+escape(search_for)+'&return_message='+type+'&fromsite=hgroup';
		window.open(url);
	}
}

function setCouponProductData(data)
{
	if(data!=null)
	{
		setValue("search_for_thumb",data['img_url']);
		setValue("thumbnail_url",data['img_url']);
		// console.log(data);
		alert('Image has been linked...Please click "Edit" button to process the changes');
	}
}

function selectCouponType(val) {
	document.getElementById("cat-0-display").setAttribute("style", "display: none;");
	document.getElementById("cat-1-display").setAttribute("style", "display: none;");
	document.getElementById("cat-2-display").setAttribute("style", "display: none;");
	document.getElementById("cat-3-display").setAttribute("style", "display: none;");

	if (val == 0) {
		document.getElementById("section-display").setAttribute("style", "display: none;");
		document.getElementById("product-id-display").setAttribute("style", "display: none;");
		document.getElementById("product-details-display").setAttribute("style", "display: none;");
	} else if (val == 1) {
		document.getElementById("section-display").removeAttribute("style");
		document.getElementById("product-id-display").setAttribute("style", "display: none;");
		document.getElementById("product-details-display").setAttribute("style", "display: none;");
	} else if (val == 2) {
		document.getElementById("section-display").setAttribute("style", "display: none;");
		document.getElementById("product-id-display").removeAttribute("style");
		document.getElementById("product-details-display").removeAttribute("style");
	}
}

function selectSection(val) {
	document.getElementById("cat-0-display").setAttribute("style", "display: none;");
	document.getElementById("cat-1-display").setAttribute("style", "display: none;");
	document.getElementById("cat-2-display").setAttribute("style", "display: none;");
	document.getElementById("cat-3-display").setAttribute("style", "display: none;");

	switch(parseInt(val)) {
		case 1 :
			document.getElementById("cat-1-display").removeAttribute("style");
			break;

		case 2 :
			document.getElementById("cat-2-display").removeAttribute("style");
			break;

		case 3 :
			document.getElementById("cat-3-display").removeAttribute("style");
			break;

		default:
			document.getElementById("cat-0-display").removeAttribute("style");
	}
}

function searchProduct() {
	product_search_section = "main";
	var val = document.getElementById("product-id-input").value;
	window.open('/webadmin/products_new/list.php?return_message=add&search_for=' + val);
}

function searchRestrictionProduct() {
	product_search_section = "restriction";
	var val = document.getElementById("restriction-product-input").value;
	window.open('/webadmin/products_new/list.php?return_message=add&search_for=' + val);
}

function setProductData(data) {
	console.log(data);
	if (product_search_section == "main") {
		var replacedName = data['name'].replace(/ /g, "-");
		var url = '/buy/'+replacedName + "-" + data['id'];

		document.getElementById("product-id-input").value = data['product_id'];
		// document.getElementById("product-url-input").value = data['product_url'];
		document.getElementById("product-url-input").value = url;
		document.getElementById("product-name-input").value = data['product_name'];
		document.getElementById("product-cost-price-input").value = data['product_cost'];
		document.getElementById("product-currency-input").value = data['primary_currency'];

		//var salesPrice = (typeof data['sales_price'] == "undefined") ? data['final_price'] : data['sales_price'];
		document.getElementById("product-sale-price-input").value = data['product_offer'];

		document.getElementById("product-currency-input").value = data['sales_currency'];

		// document.getElementById("product-link").setAttribute("href", data['product_url']);
		document.getElementById("product-link").setAttribute("href", url);
		document.getElementById("product-link").innerHTML = data['product_name'];
		document.getElementById("cost_price").innerHTML = "$" + parseFloat(data['product_cost']).toFixed(2) + " " + data['primary_currency'];
		document.getElementById("sell_price").innerHTML = "$" + parseFloat(data['product_offer']).toFixed(2) + " " + data['primary_currency'];
	} else {
		document.getElementById("restriction-product-input").value = data['product_id'];
		document.getElementById("restriction-product-url-input").value = data['product_url'];
		document.getElementById("restriction-product-name-input").value = data['product_name'];
		document.getElementById("restriction-product-delete-link").removeAttribute("style");

		document.getElementById("restriction-product-link").setAttribute("href", data['product_url']);
		document.getElementById("restriction-product-link").innerHTML = data['product_name'];
	}
}

function changeDiscountType(val) {
	document.getElementById("discount_type_fixed").setAttribute("style", "display: none;");
	document.getElementById("discount_type_percentage_value").setAttribute("style", "display: none;");
	document.getElementById("discount_type_percentage_bonous").setAttribute("style", "display: none;");

	if (val == 0) {
		document.getElementById("discount_type_percentage_value").removeAttribute("style");
		document.getElementById("discount_type_percentage_bonous").removeAttribute("style");
	} else if (val == 1) {
		document.getElementById("discount_type_fixed").removeAttribute("style");
	}
}

function updateProductDetails() {
	var discountType = document.getElementById("discount_type_input");
	var fixedPrice = document.getElementById("fixed_sell_price_input");
	var discount = document.getElementById("discount_input");
	var costPrice = document.getElementById("product-cost-price-input");
	var sellPrice = document.getElementById("product-sale-price-input");
	var currency = document.getElementById("product-currency-input");

	var newPriceDisplay = document.getElementById("discounted_sell_price");
	var profitDisplay = document.getElementById("profit_price");
	var discountDisplay = document.getElementById("discount_price");
	var savingsDisplay = document.getElementById("savings_price");
	var costPriceDisplay = document.getElementById("cost_price");
	var sellPriceDisplay = document.getElementById("sell_price");

	var newPrice = (discountType.value == 1) ? fixedPrice.value : parseFloat(sellPrice.value) - (parseFloat(sellPrice.value) * parseFloat(discount.value));
	var profit = parseFloat(newPrice) - parseFloat(costPrice.value);
	var newDiscount = (discountType.value == 1) ? ((parseFloat(newPrice)/parseFloat(sellPrice.value))) : discount.value;
	var savings = parseFloat(sellPrice.value) - parseFloat(newPrice);

	newPriceDisplay.innerHTML = "$" + parseFloat(newPrice).toFixed(2) + " " + currency.value;
	profitDisplay.innerHTML = "$" + parseFloat(profit).toFixed(2) + " " + currency.value;
	discountDisplay.innerHTML = parseFloat(newDiscount).toFixed(2) * 100 + "%";
	savingsDisplay.innerHTML = "$" + parseFloat(savings).toFixed(2) + " " + currency.value;
	costPriceDisplay.innerHTML = "$" + costPrice.value + " " + currency.value;
	sellPriceDisplay.innerHTML = "$" + sellPrice.value + " " + currency.value;
}

function deleteRestrictionProduct() {
	document.getElementById("restriction-product-input").value = "";
	document.getElementById("restriction-product-url-input").value = "";
	document.getElementById("restriction-product-name-input").value = "";
	document.getElementById("restriction-product-delete-link").style.display = "none";

	document.getElementById("restriction-product-link").setAttribute("href", "#");
	document.getElementById("restriction-product-link").innerHTML = "";
}

$(document).ready(function(){
	updateProductDetails();
});

</script>
<?php if(isset($_GET['update']) && $_GET['update'] == 1) {
?>
<div id="warning" style="text-align: center;color: black;margin: 0px 32px;margin-bottom: 5px;background: green;font-weight: 600;margin-top:20px;">Coupon Updated Successfully.</div>
<?php
}
?>

<form action="index.php?action=do_preset<?=($site_id) ? '&site_id=' . $site_id : ''?>" method="post">
	<?=formatHTMLTitle("Coupon Details");?>
	<table class="wide">
	<input type="hidden" value="<?=$preset['id']?>" name="preset_id" />
	<tr>
		<td class="left_title" style="width:25%;">Title of Discount : </td>
		<td>
			<input style="width:50%" type="text" name="coupon_title" placeholder="Title of the coupon" value="<?=$preset['title']?>" autofocus>
		</td>
	</tr>

	<tr>
		<td class="left_title" style="vertical-align:top">
			Short Description :
		</td>
		<td>
			<textarea name="description" rows="4" cols="50"><?=$preset['description']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="left_title">
			Coupon Code :
		</td>
		<td><input type="text" name="name" value="<?=$preset['name']?>" /></td>
	</tr>
<?php
$site_list = giftcouponGetOptionList("site_id");
$cat_0     = giftcouponGetOptionList("cat_0");
$cat_1     = giftcouponGetOptionList("cat_1");
$cat_2     = giftcouponGetOptionList("cat_2");
$cat_3     = giftcouponGetOptionList("cat_3");

if (!$site_id) {
    ?>
	<tr>
		<td class="left_title" style="max-width:30%;">Company :</td>
		<td>
			<select name="site_id" style="width:50%">
				<option value="">All Sites</option>
				<?=formatHTMLOptions($site_list, $preset['site_id'])?>
			</select>
		</td>
	</tr>
<?php
} else {
    ?>
	<tr>
		<td class="left_title" style="max-width:30%;">Company :</td>
		<td>
			<?php hecho($site_list[$preset["site_id"]])?>
			<input type="hidden" name="site_id" value="<?php hecho($preset["site_id"])?>" />
		</td>
	</tr>
<?php
}
$coupon_type = giftcouponGetOptionList("coupon_type");
?>
	</table>
	<?=formatHTMLTitle("Coupon Choice");?>
	<table class="wide">
 	<tr>
		<td class="left_title">Discount on :</td>
		<td>
			<select name="coupon_type" onchange="selectCouponType(this.value);">
				<?=formatHTMLOptions($coupon_type, $preset['coupon_type'])?>
			</select>
		</td>
	</tr>
	<tr id="section-display" <?=(1 == $preset['coupon_type']) ? "" : 'style="display: none;"';?>>
		<td class="left_title">Section :</td>
		<td><select name="section" onchange="selectSection(this.value);">
			<option value="0">All Sections</option>
			<?=formatHTMLOptions($section_list, $preset['section'])?>
		</select></td>
	</tr>
	<tr id="cat-0-display" class="cat-display" <?=(1 == $preset['coupon_type'] && $preset['section'] > 3) ? '' : 'style="display: none;"';?>>
		<td class="left_title">Category :</td>
		<td><select name="category_0">
			<?=formatHTMLOptions($cat_0, $preset['cat'])?>
		</select></td>
	</tr>
	<tr id="cat-1-display" class="cat-display" <?=(1 == $preset['coupon_type'] && 1 == $preset['section']) ? '' : 'style="display: none;"';?>>
		<td class="left_title">Category :</td>
		<td><select name="category_1">
			<?=formatHTMLOptions($cat_1, $preset['cat'])?>
		</select></td>
	</tr>
	<tr id="cat-2-display" class="cat-display" <?=(1 == $preset['coupon_type'] && 2 == $preset['section']) ? '' : 'style="display: none;"';?>>
		<td class="left_title">Category :</td>
		<td><select name="category_2">
			<?=formatHTMLOptions($cat_2, $preset['cat'])?>
		</select></td>
	</tr>
	<tr id="cat-3-display" class="cat-display" <?=(1 == $preset['coupon_type'] && 3 == $preset['section']) ? '' : 'style="display: none;"';?>>
		<td class="left_title">Category :</td>
		<td><select name="category_3">
			<?=formatHTMLOptions($cat_3, $preset['cat'])?>
		</select></td>
	</tr>
	<!--tr>
		<td class="left_title" style="width:25%;">Product / Website Link : </td>
		<td>
			<input style="width:50%" type="text" name="click_url" placeholder="http://www..." value="<?=$preset['click_url']?>" autofocus>
		</td>
	</tr-->
	<tr id="product-id-display" <?=(2 == $preset['coupon_type']) ? "" : 'style="display: none;"';?>>
		<td class="left_title" style="width:25%;">Product id: </td>
		<td>
			<input style="width:50%" id="product-id-input" type="text" name="product_id" placeholder="Product ID" value="<?=$preset['product_id']?>" autofocus>
			<input type="hidden" name="product_url" id="product-url-input" value="<?=htmlspecialchars($preset['product_url'])?>"/>
			<input type="hidden" name="product_name" id="product-name-input" value="<?=$preset['product_name']?>"/>
			<input type="hidden" name="product_cost_price" id="product-cost-price-input" value="<?=$preset['cost_price']?>" />
			<input type="hidden" name="product_sale_price" id="product-sale-price-input" value="<?=$preset['sell_price']?>" />
			<input type="hidden" name="product_currency" id="product-currency-input" value="<?=$preset['currency']?>" />
			<button type="button" onclick="searchProduct();">Search</button>
			<!--a href="<?=$preset['product_url']?>" id="product-link" style='font-size: 12px;' target="_blank"><?=$preset['product_name']?></a-->
		</td>
	</tr>
	</table>

	<br>

	<table class="wide" id="product-details-display" <?=(2 == $preset['coupon_type']) ? "" : 'style="display: none;"';?>>
		<tr>
			<td colspan="2" style="text-align: center;"><a href="<?=htmlspecialchars($preset['product_url'])?>" id="product-link" style='font-size: 12px;' target="_blank"><?=$preset['product_name']?></a></td>
		</tr>
		<tr>
			<td style="width: 50%">
				<table class="wide">
					<tr>
						<td class="left_title">Cost Price</td>
						<td><span id="cost_price">$0</span></td>
					</tr>
					<tr>
						<td class="left_title">Sell Price</td>
						<td><span id="sell_price">$0</span></td>
					</tr>
					<tr>
						<td class="left_title">New discounted sell price</td>
						<td><span id="discounted_sell_price">$0</span></td>
					</tr>
				</table>
			</td>

			<td>
				<table class="wide">
					<tr>
						<td class="left_title">Profit</td>
						<td><span id="profit_price">$0</span></td>
					</tr>
					<tr>
						<td class="left_title">% Discount</td>
						<td><span id="discount_price">10%</span></td>
					</tr>
					<tr>
						<td class="left_title">Savings</td>
						<td><span id="savings_price">$0</span></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="2" style="text-align: center;">
				<button onclick="updateProductDetails();" type="button">Get Update</button>
			</td>
		</tr>
	</table>

	<?=formatHTMLTitle("Restrictions");?>

	<table class="wide">
		<tr>
			<td class="left_title">Order value must exceed $ </td>
			<td>
				<input type="text" name="restriction_value" id="restriction-value-input" value="<?=$preset['restriction_value'];?>" style="width: 50%"/>
			</td>
		</tr>

		<tr>
			<td class="left_title">Order must include product </td>
			<td>
				<input type="text" name="restriction_product" id="restriction-product-input" value="<?=$preset['restriction_product'];?>" style="width: 50%"/>
				<input type="hidden" name="restriction_product_url" id="restriction-product-url-input" value="<?=$preset['restrict_product_url'];?>"/>
				<input type="hidden" name="restriction_product_name" id="restriction-product-name-input" value="<?=$preset['restriction_product_name'];?>"/>
				<button type="button" onclick="searchRestrictionProduct();">Search</button>
			</td>
		</tr>

		<tr>
			<td class="left_title">Linked: </td>
			<td><a href="<?=$preset['restriction_product_url']?>" id="restriction-product-link" style='font-size: 12px;' target="_blank"><?=$preset['restriction_product_name']?></a> <span id="restriction-product-delete-link" <?=("" == $preset['restriction_product_name']) ? "style='display: none;'" : "";?>>(<a href='#' onclick='deleteRestrictionProduct(); return false;' style='color: #ff0000;'>Delete</a>)</span></td>
		</tr>
	</table>
	<!--tr>
		<td class="left_title" style="width:25%;">Image Link :</td>
		<td>
			<input type="text" name="search_for_thumb" value="">
			<input type="button" value="Link Thumbnail" onclick="linkImage('thumb')">
			<input type="hidden" name="thumbnail_url" value="" />


<?php
if ('' != $preset['thumbnail_url']) {
    $img_baseurl_raw = "https://static.highway.net.au/images/products/large/";
    ?>
			<br />
			<input type="text" readonly="readonly" value="<?=basename($preset['thumbnail_url'])?>" disable/>
			<input type="button" value="View Thumbnail" onclick="javascript:window.open('<?=$preset['thumbnail_url']?>');" />
			<input type="button" value="View Raw Image" onclick="javascript:window.open('<?=$img_baseurl_raw . basename($preset['thumbnail_url'])?>');" />
<?php
}
?>
		</td>
	</tr-->
<?=formatHTMLTitle("Discount Type");?>
<table class="wide">
	<?php $discount_type = giftcouponGetOptionList("discount_type");?>
	<tr>
		<td class="left_title" style="width: 35%;">
			Discount Type :
		</td>
		<td>
			<select name="discount_type" onchange="changeDiscountType(this.value);" id="discount_type_input">
				<?=formatHTMLOptions($discount_type, $preset['discount_type'])?>
			</select>
		</td>
	</tr>

	<tr id="discount_type_fixed" <?=(1 == $preset['discount_type']) ? "" : 'style="display: none;"';?>>
		<td class="left_title">
			Sell Price of Product :
		</td>
		<td>
			<input type="text" name="fixed_sell_price" value="<?=$preset['fixed_price']?>" style="width: 15%;" id="fixed_sell_price_input"/> USD
		</td>
	</tr>

	<?php $value_list = giftcouponGetOptionList("value");?>
	<tr id="discount_type_percentage_value" <?=(0 == $preset['discount_type']) ? "" : 'style="display: none;"';?>>
		<td class="left_title">
			% Discount :
		</td>
		<td>
			<select onchange="toggleSection('amount_extra',(this.value==-123))" name="amount_value" style="width:15%;" id="discount_input">
				<?=formatHTMLOptions($value_list, $preset['value'])?>
			</select>
			<input type="text" name="amount_extra" value="<?=$preset['amount']?>" style="width:15%;<?=(0 != $preset['value']) ? 'display:none;' : ''?>" />
		</td>
	</tr>

<?php $bonus_list = giftcouponGetOptionList("bonus");?>
	<tr id="discount_type_percentage_bonous" <?=(0 == $preset['discount_type']) ? "" : 'style="display: none;"';?>>
		<td class="left_title">
			% Bonus Discount : <br>
			(if purchased in last 12 months)
		</td>
		<td>
			<select name="bonus_value" style="width:15%;">
				<?=formatHTMLOptions($bonus_list, $preset['bonus'])?>
			</select>
		</td>
	</tr>
</table>

<?=formatHTMLTitle("Coupon Settings");?>
<table class="wide">
<?php $expiry_value_list = giftcouponGetOptionList("expiry_value");?>
	<tr>
		<td class="left_title">Expiry Date :</td>
		<td style="width: 50%;">
			<select onchange="toggleSection('expiry_extra',(this.value==0))"  name="expiry_value" style="width:15%;">
				<?=formatHTMLOptions($expiry_value_list, $preset['expiry_value'])?>
			</select>
			<br />
			<span name="expiry_extra" style="<?=(0 != $preset['expiry_value']) ? 'display:none' : ''?>">
			<input type="text" name="expiry_date" value="<?= (!is_null($preset['expiry_date']) && $preset['expiry_date'] !== '0000-00-00 00:00:00') ? date("Y-m-d", strtotime($preset['expiry_date'])) : ''?>">
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('expiry_date'));" />
			<input type="button" value="Clear" onclick="javascript:setValue('expiry_date','');" />
			</span>

			<?php
if (is_null($preset['expiry_date']) || $preset['expiry_date'] == '0000-00-00 00:00:00') {
	$day = '(Never)';
} else {
	$now      = time(); // or your date as well
	$fut      = strtotime($preset['expiry_date']);
	$datediff = $fut - $now;

	$day = round($datediff / (60 * 60 * 24));

	if ($day < 0) {
		$day = "(<i style='color: red;'>already expired</i>)";
	} else {
		$day = $day . " " . (($day > 1) ? "days" : "day");
	}
}
?>
			<div><span>Expire in <?=$day;?></span></div>

		</td>
	</tr>

<?php $show_coupon = giftcouponGetOptionList("show_coupon");?>
	<tr>
		<td class="left_title">
			Show Coupon Code Online :
		</td>
		<td>
			<select name="show_coupon">
				<?=formatHTMLOptions($show_coupon, $preset['show_coupon']);?>
			</select>
		</td>
	</tr>

<?php $active_list = giftcouponGetOptionList("active");
?>

	<tr>
		<td class="left_title">
			Active Status :
		</td>
		<td>
			<select name="active" style="width: 15%;"><?=formatHTMLOptions($active_list, $preset['active'])?></select>
		</td>
	</tr>

</table><center style="margin: 10px 0;"><input type="submit" value="<?=($add) ? 'Add' : 'Edit'?>" /></center></form>
<script>
    // Get a reference to the warning div and close button
    var warningDiv = document.getElementById("warning");
    var closeButton = document.getElementById("closeButton");

    // Function to hide the warning div after 10 seconds
    function hideWarning() {
        warningDiv.style.display = "none";
    }

    // Set a timeout to hide the warning div after 10 seconds
    var timeoutID = setTimeout(hideWarning, 10000);

    // // Add event listener to close button to hide the warning div immediately when clicked
    // closeButton.addEventListener("click", function() {
    //     clearTimeout(timeoutID); // Clear the timeout
    //     hideWarning(); // Hide the warning div immediately
    // });
</script>
<?php
include_once dirname(__FILE__) . "/footer.inc.php";
?>