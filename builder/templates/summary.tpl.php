<?php
/**
 * 	Project: Product Builder
 *
 *	summary.tpl.php
 *
 *	The summary of all selected values.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  05/02/2018
 */

moduleLoad('product'); //productFromDatabase

$machine = $machines[$machineKey];
$productData = productFromDatabase($machine['product_id'], $machine['sub_id']);

$productImage = "";

if (!empty($productData['image_url'] ?? '')) {
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

<div style="margin: 25px auto; width:"<?= (isset($_GET['type']))?'100%;':'70%;'?>" border: 3px solid #1a79ff; border-radius: 10px; -webkit-border-radius: 10px; -moz-border-radius: 10px;">
	<div style="padding: 25px 0;">
		<div class="page-title"><h2 class="clr">My Custom Cabinet</h2></div>
		<table class="clr" style="margin: auto;">
			<tr>
				<td style="width: 30%; text-align: center;">
					<img src="<?= $img ?>" width="250" height="250" />
				</td>

				<td style="padding-left: 20px;">
					<div id="summary-option-list-wrapper">
						<ul class="clr" id="summary-option-list">
							<li><?=$voltage?>V</li>
							
							<?php if ($shipping != "") { ?>
							<li>Shipping Method: <?php echo $shipping . " ($". $shipping_price .")"; ?></li>
							<?php } ?>
							
							<?php
							//echo '<pre>'; print_r($machine['options']); die; 
							foreach($machine['options'] as $k => $option) {
								$items = $pb->getOptionById($option['id']);
								if ($option['template'] == 'videofeat') {
									$items = $pb->sortOptionsByPrice($items['options']);
								} else {
									$items = $pb->sortOptionsByPosition($items['options']);
								}

								$item = $items[$summary[$k]];

								$name = "";
								$color_name='';
								if ($item['option_key'] == 0) {
									$itemProductData = productFromDatabase($item['product_id'], $item['site_id']);
									$name = $itemProductData['product_name'];
									foreach ($_GET['option_color'] ?? [] as $keyval => $data) {
										$keynew=$k.'_'.$summary[$k]; 
										if($keyval==$keynew){
											 $color_detail=getProductColourById($data['value']);
											$color_name=($color_name=='')?'( '.$color_detail['colour'].'':($color_name.','.$color_detail['colour']);
										}
									}
									$color_name=($color_name=='')?'':($color_name.')');
								} else {
									$name = $item['header'];
								}

								echo "<li>". $name ." ($". $item['price'] ." x {$option['qty']})".$color_name."</li>";
							}
							?>
						</ul>
					</div>
				</td>

				<td style="padding: 10px; border-left: 1px solid #f0f0f0; text-align: center; width: 30%;">
					<h2><span style="color: #5D7C89; font-weight: lighter;">Total</span> $<span id="summary-price-display"><?= $total_price ?></span><span style="font-size: 12px; font-weight: normal;"><?= $machine['currency'] ?></span></h2>
				</td>
			</tr>
		</table>
	</div>

	<div class="summary-sep"></div>
	<div style="margin-left: 3%; margin-right: 3%; border-radius: 10px; height: 100px; background-color: hsl(136deg 71% 96%); border: 1px solid black;">
		<div style="padding: 10px; display: under; text-decoration: underline; ">
			<b>Special Notes</b>
		</div>
		<div style="padding-left: 10px;"><?= $machine['special_notes']?></div>
	</div>
	<div style="padding: 25px 0;" id="inquiry_form">
		<div class="sent-notification" style="display: none;">
			<h3 class="clr">Thank you for your inquiry</h3>

			<p>
				Your inquiry has been sent. We will contact you as soon as possible.
			</p>

			<div class="align-middle" id="finish-footer" style="padding: 25px;">
				<div class="grid-inline-mid">
					<button type="button" class="blue-btn y-btn" style="width: 250px;" onclick="window.location.href='<?= $website ?>'">BUILD ANOTHER CABINET</button>
				</div>

				<div class="grid-inline-mid">
					<button type="button" class="blue-btn y-btn" style="width: 250px;" onclick="window.location.href='https://<?= $continueShopping ?>'">CONTINUE SHOPPING</button>
				</div>
			</div>
		</div>
		<form action="/builder/ajax/send_inquiry.php" method="post" onsubmit="return sendInquiry(this);">
			<table class="clr" style="margin: auto;">
				<tr>
					<td colspan="2"><div id="err"></div></td>
				</tr>
				<?php  if (!isset($_GLOBALS['member']['member_id'])) { ?>
				<tr>
					<td style="vertical-align: top;">
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
		                            
		                            foreach(getCountryList() as $k => $v) {
		                                if ($k != "NV") {
		                                    $selected = ($country_code == $k) ? "selected" : "";
		                                    echo "<option value='". $k ."' $selected>". $v ."</option>";
		                                }
		                            }
		                            ?>
	                            </select>
							</li>

<?php
/*
							<li>
								<input type="checkbox" name="join_mailing_list" checked value="<?= $_GLOBALS['site_settings']['site_id'] ?>"/> <span class="font-14">Join Mailing List</span>
							</li>
*/
?>

							<li>
								<!--input type="checkbox" name="send_copy" checked/> <span class="font-14">Send copy to my email</span-->
							</li>
						</ul>
					</td>

					<td style="vertical-align: top;">
						<ul class="clr non-liststyle form-list">
							<li>
								<div class="text-area-wrapper">
									<textarea class="textarea-input placeholder" name="comments">Comments</textarea>
								</div>
							</li>

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
									$color_name="";
									if ($item['option_key'] == 0) {
										$itemProductData = productFromDatabase($item['product_id'], $item['site_id']);
										$name = $itemProductData['product_name'];
										foreach ($_GET['option_color'] ?? [] as $keyval => $data) {
											$keynew=$k.'_'.$summary[$k]; 
											if($keyval==$keynew){
												 $color_detail=getProductColourById($data['value']);
												$color_name=($color_name=='')?'( '.$color_detail['colour'].'':($color_name.','.$color_detail['colour']);
											}
										}
									$color_name=($color_name=='')?'':($color_name.')');
									} else {
										$name = $item['header'];
									}

									echo "<input type='hidden' name='items' value='{$name} ($". $item['price'] ." x {$option['qty']}) ".$color_name."' class='summary-items-input'/>";
								}

								echo "<input type='hidden' name='machine_name' value=\"{$productData['product_name']}\" />";
								echo "<input type='hidden' name='machine_id' value='{$productData['id']}' />";
								echo "<input type='hidden' name='currency' value='{$machine['currency']}' />";
								echo "<input type='hidden' name='price' value='{$total_price}' id='total-price-input'/>";
								echo "<input type='hidden' name='sub_id' value='{$productData['sub_id']}' />";
						
								?>
								<input type="submit" value="SEND REQUEST" class="blue-btn" id="send-btn" style="width: 252px;"/>
							</li>
						</ul>
					</td>
				</tr>
			<?php }else{ ?>
				<tr>
					<input type="hidden" name="fname" value ="<?=htmlspecialchars($_REQUEST['fname'] ?? $_GLOBALS['member']['first_name'] ?? $_GLOBALS['member']['name'] ?? '')?>" placeholder="First Name" class="text-input"/>
					<input type="hidden" name="lname" placeholder="Last Name" class="text-input" value ="<?=htmlspecialchars($_REQUEST['lname'] ?? $_GLOBALS['member']['surname'] ?? $_GLOBALS['member']['last_name'] ?? '')?>"/>
					<input type="hidden" name="email" placeholder="Email" class="text-input" value ="<?= $_GLOBALS['member']['email_address']?>"/>
					<input type="hidden" name="country" placeholder="Email" class="text-input" value ="<?= $_GLOBALS['member']['country']?>"/>
					<input type="checkbox" name="join_mailing_list" style="display:none;" value="<?= $_GLOBALS['site_settings']['site_id'] ?>"/>
					
					<td style="vertical-align: top;">
						<ul class="clr non-liststyle form-list">
							<li>
								<div class="text-area-wrapper">
									<textarea class="textarea-input placeholder" name="comments">Comments</textarea>
								</div>
							</li>

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

									$color_name="";
									if ($item['option_key'] == 0) {
										$itemProductData = productFromDatabase($item['product_id'], $item['site_id']);
										$name = $itemProductData['product_name'];
										foreach ($_GET['option_color'] as $keyval => $data) {
											$keynew=$k.'_'.$summary[$k]; 
											if($keyval==$keynew){
												 $color_detail=getProductColourById($data['value']);
												$color_name=($color_name=='')?'( '.$color_detail['colour'].'':($color_name.','.$color_detail['colour']);
											}
										}
									$color_name=($color_name=='')?'':($color_name.')');
									} else {
										$name = $item['header'];
									}
									
									echo "<input type='hidden' name='items' value='{$name} ($". $item['price'] ." x {$option['qty']}) ".$color_name."' class='summary-items-input'/>";
								}

								echo "<input type='hidden' name='machine_name' value=\"{$productData['product_name']}\" />";
								echo "<input type='hidden' name='machine_id' value='{$productData['id']}' />";
								echo "<input type='hidden' name='currency' value='{$machine['currency']}' />";
								echo "<input type='hidden' name='price' value='{$total_price}' id='total-price-input'/>";
								echo "<input type='hidden' name='sub_id' value='{$productData['sub_id']}' />";
						
								?>
								<input type="submit" value="SEND REQUEST" class="blue-btn" id="send-btn" style="width: 252px;"/>
							</li>
						</ul>
					</td>
				</tr>

			<?php } ?>
			</table>
		</form>
	</div>
</div>