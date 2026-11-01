<?php
/**
 * 	Project: Product Builder
 *
 *	summary.tpl.php
 *
 *	The summary of all selected values.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  15/02/2018
 */

moduleLoad('product'); //productFromDatabase

$machine = $machines[$machineKey];

$productData = productFromDatabase($machine['product_id'], $machine['sub_id']);

$productImage = "";

if (isset($productData['image_url'])) {
	$productImage = relative_url($productData['image_url']);
} else if (isset($productData['thumb_url'])) {
	$productImage = str_replace('thumb', 'large', $productData['thumb_url']);
}

$img = $productImage;

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

?>

<div>
	<div style="padding: 25px 0;">
		<div class="page-title"><h2 class="clr" style="text-align: center;">My Custom Cabinet</h2></div>

		<div style="padding: 10px 0;">
			<img src="<?= $img ?>" width="100%"/>
		</div>

		<div id="summary-option-list-wrapper" class="align-middle">
			<ul class="clr" id="summary-option-list">
				<li><?=$voltage?>V</li>
				
				<?php if ($shipping != "") { ?>
				<li>Shipping Method: <?php echo $shipping . " ($". $shipping_price .")"; ?></li>
				<?php } ?>
				
				<?php 
				foreach($machine['options'] as $k => $option) {
					$items = $pb->getOptionById($option['id']);
					if ($option['template'] == 'videofeat') {
						$items = $pb->sortOptionsByPrice($items['options']);
					} else {
						$items = $pb->sortOptionsByPosition($items['options']);
					}

					$item = $items[$summary[$k]];

					$name = "";

					if ($item['option_key'] == 0) {
						$itemProductData = productFromDatabase($item['product_id'], $item['site_id']);
						$name = $itemProductData['product_name'];
					} else {
						$name = $item['header'];
					}

					echo "<li style='margin: 5px 0;'>". $name ." ($". $item['price'] ." x {$option['qty']})</li>";
				}
				?>
			</ul>
		</div>
	</div>

	<div class="summary-sep"></div>

	<div style="padding: 25px 10px;" id="inquiry_form">
		<div class="sent-notification" style="display: none;">
			<div style="padding: 25px 10px; border: 1px solid #ffb500; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; margin-bottom: 10px;">
				<h3 class="clr">Thank you for your inquiry</h3>

				<p>
					Your inquiry has been sent. We will contact you as soon as possible.
				</p>
			</div>

			<button type="button" class="blue-btn y-btn" style="width: 100%; margin-bottom: 10px;" onclick="window.location.href='<?= $website ?>'">BUILD ANOTHER CABINET</button>

			<button type="button" class="blue-btn y-btn" style="width: 100%;" onclick="window.location.href='https://<?= $continueShopping ?>'">CONTINUE SHOPPING</button>
		</div>

		<form action="/builder/ajax/send_inquiry.html" method="post" onsubmit="return sendInquiry(this);">
			<ul class="clr non-liststyle form-list">
				<li>
					<input type="text" name="fname" placeholder="First Name" class="text-input"/>
				</li>

				<li>
					<input type="text" name="lname" placeholder="Last Name" class="text-input"/>
				</li>

				<li>
					<input type="text" name="email" placeholder="Email" class="text-input"/>
				</li>

				<li>
					<select name="country" class="select-input">
                        <?php
                        $details = json_decode(file_get_contents("http://ipinfo.io/" . get_client_ip()));
                        $country_code = $details->country;
                        
                        foreach($countries as $k => $v) {
                            if ($k != "NV") {
                                $selected = ($country_code == $k) ? "selected" : "";
                                echo "<option value='". $k ."' $selected>". $v ."</option>";
                            }
                        }
                        ?>
                    </select>
				</li>

				<li>
					<div class="text-area-wrapper">
						<textarea class="textarea-input placeholder" name="comments">Comments</textarea>
					</div>
				</li>

<?php
/*
				<li>
					<input type="checkbox" name="join_mailing_list" checked value="<?= $_GLOBALS['site_settings']['site_id'] ?>"/> <span class="font-14">Join Mailing List</span>
				</li>
*/
?>

				<li>
					<?php
					echo "<input type='hidden' name='items' value=\"". $productData['product_name'] ." ($". $machine['price'] .")\" class='summary-items-input' />"; 
					echo "<input type='hidden' name='items' value='{$voltage}V' class='summary-items-input' />";

					if ($shipping != "") {
						echo "<input type='hidden' name='items' value='Shipping Method: {$shipping} ($". $shipping_price .")' class='summary-items-input' />";
					}
					
					foreach($machine['options'] as $k => $option) {
						$items = $pb->getOptionById($option['id']);

						if ($option['template'] == 'videofeat') {
							$items = $pb->sortOptionsByPrice($items['options']);
						} else {
							$items = $pb->sortOptionsByPosition($items['options']);
						}

						$item = $items[$summary[$k]];

						$name = "";

						if ($item['option_key'] == 0) {
							$itemProductData = productFromDatabase($item['product_id'], $item['site_id']);
							$name = $itemProductData['product_name'];
						} else {
							$name = $item['header'];
						}

						echo "<input type='hidden' name='items' value='{$name} ($". $item['price'] ." x {$option['qty']})' class='summary-items-input'/>";
					}

					echo "<input type='hidden' name='machine_name' value=\"{$productData['product_name']}\" />";
					echo "<input type='hidden' name='machine_id' value='{$productData['product_id']}' />";
					echo "<input type='hidden' name='currency' value='{$machine['currency']}' />";
					echo "<input type='hidden' name='price' value='{$total_price}' id='total-price-input'/>";
					echo "<input type='hidden' name='sub_id' value='{$productData['sub_id']}' />";
			
					?>
					<input type="submit" value="SEND REQUEST" class="blue-btn" id="send-btn" style="width: 50%;"/>
				</li>
			</ul>
		</form>
	</div>
</div>