<?php 
/**
 *	Prototype for testing
 */

include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/product_builder/common.inc.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/product_builder/templates/option_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);

// Options from database
$options = null;

// Action to be performed
$action = "do_add_option";

// To edit option
if ($posted['action'] == "edit_option") {
	$option_id = $posted['option_id'];
	$options = $pb->getOptionById($option_id);
	$action = "do_edit_option";
}

// Option name
$option_name = ($options == null) ? "" : $options['name'];

// Option title
$option_title = ($options == null) ? "" : $options['title'];

// Title
$title = ($options == null) ? "Add New Option" : "Edit Option - " . ucwords($option_name);

// Update message
if (isset($_SESSION['updated_token']) && !empty($_SESSION['updated_token']) &&
	isset($_GET['updated']) && !empty($_GET['updated']) && $_GET['updated'] == $_SESSION['updated_token']) {
	
	echo "<p style='color: red;'>Option Data has been updated.</p>";

	unset($_SESSION['updated_token']);
}

// Get locks
$lock = $pb->optionGetLock($posted['gid']);

if ($lock !== false && $lock['staff_id'] != $_GLOBALS['staff']['id'] && $action == "do_edit_option") {
	$time_to_live = $lock['expiry_time'] - time();

	echo "<div id='locked-noti-wrapper'>";
	echo "<p id='locked-noti'>";
	echo "This option is not locked to you. You cannot submit any changes.<br>";
	echo "This option is currently locked to " . $all_staff_list[$lock['staff_id']] . ", please wait for ". intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
	echo "</p>";
	echo "</div>";
}
?>

<form action="index.php?action=<?= $action ?>" method="post" style="width: 96%;" onsubmit="if (document.getElementById('option-name-input').value.length > 0) { return true; } else { alert('Name is missing.'); return false;};">
	<ul class="clr">
		<li>
			<h1 style="text-align: center;"><?= $title; ?></h1>
		</li>
		<li>
			<br>
			<div class="flagHeader"><span>Option Information</span></div>
			<br>
		</li>

		<li>
			<table class="form-table">
				<tr>
					<td class="left-title">Option Name</td>
					<td><input type="text" name="pb_options[name]" class="text-input" value="<?= $option_name ?>" id="option-name-input"/></td>
				</tr>

				<tr>
					<td class="left-title">Title</td>
					<td><input type="text" name="pb_options[title]" class="text-input" value="<?= $option_title ?>" id="option-title-input"/></td>
				</tr>

				<tr>
					<td class="left-title">Template</td>
					<td>
						<?php 
						if ($options != null) {
							echo "<span id='option-template'>". $options['template'] ."</span>";
						} else {
						?>

						<select name="pb_options[template]" onchange="changeTemplate(this);">
							<option value='standard'>Standard</option>
							<option value='videofeat'>Video Feature</option>
						</select>

						<?php } ?>
					</td>
				</tr>
			</table>
			<br>
		</li>

		<li>
			<br>
			<div class="flagHeader"><span>Option List</span></div>
			<br>
		</li>

		<li>
			<div id="optionList">
				<?php
				if ($options != null) {
					// Sort options
					$sortedOptions = $pb->sortOptionsByPosition($options['options']);

					foreach($sortedOptions as $option_val) {
						// Get options key
						$option_list_key = $option_val['option_key'];

						switch($option_list_key) {
							case 0 :	// Product
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");

								//echo "<input type='hidden' class='option_list_init' value='". $option_val['product_id'] ."' data-option-key='0' data-option-price='". $option_val['price'] ."' data-option-currency='". $option_val['currency'] ."' data-option-siteid='". $option_val['site_id'] ."' data-option-converted-price='". $convertedPrice ."' />";
								
								echo "<div class='option-init' data-option-key='0'>";
								echo "<input type='hidden' class='product-id-input' value='{$option_val['product_id']}' />";
								echo "<input type='hidden' class='site-id-input' value='{$option_val['site_id']}' />";
								echo "<input type='hidden' class='sub-id-input' value='0' />";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "</div>";

								break;

							case 1 :	// Text
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");

								//echo "<input type='hidden' class='option_list_init' value='". $option_val['text'] ."' data-option-key='1' data-option-price='". $option_val['price'] ."' data-option-currency='". $option_val['currency'] ."' data-option-converted-price='". $convertedPrice ."' data-option-header='". $option_val['header'] ."'/>";
								
								echo "<div class='option-init' data-option-key='1'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<textarea class='text-input'>{$option_val['text']}</textarea>";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "</div>";

								break;

							case 2 :	// Photo
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								
								//echo "<input type='hidden' class='option_list_init' value='". $option_val['img'] .".". $option_val['ext'] ."' data-option-key='2' data-option-price='". $option_val['price'] ."' data-option-currency='". $option_val['currency'] ."' data-option-converted-price='". $convertedPrice ."' data-option-header='". $option_val['header'] ."' data-option-photo-desc='". $option_val['description'] ."'/>";
								
								echo "<div class='option-init' data-option-key='2'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<input type='hidden' class='src-input' value='{$option_val['img']}.{$option_val['ext']}' />";
								echo "<textarea class='description-input'>{$option_val['description']}</textarea>";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "</div>";

								break;

							case 3 :	// Video
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								
								//echo "<input type='hidden' class='option_list_init' value='". $option_val['youtube'] ."' data-option-key='3' data-option-price='". $option_val['price'] ."' data-option-currency='". $option_val['currency'] ."' data-option-converted-price='". $convertedPrice ."' data-option-header='". $option_val['header'] ."' data-option-video-desc='". $option_val['description'] ."' data-option-video-info='". $option_val['infolink'] ."'/>";

								echo "<div class='option-init' data-option-key='3'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<input type='hidden' class='youtube-input' value='{$option_val['youtube']}' />";
								echo "<textarea class='description-input'>{$option_val['description']}</textarea>";
								echo "<input type='hidden' class='info-link-input' value='{$option_val['infolink']}' />";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "</div>";

								break;
						}
					}
				}
				?>
			</div>
		</li>

		<?php if ($action == "do_edit_option") { ?>
		<li>
			<br>
			<br>
			<div class="flagHeader"><span>Last 5 Changes</span>&nbsp;<a href="index.php?action=view_history&id=<?= $option_id ?>&section=options&return_message" target="_blank">View All</a></div>
			<br>
		</li>

		<li>
			<table class="view-table">
				<tr>
					<th>Date</th>
					<th>User</th>
					<th>History</th>
				</tr>

				<?php
				$history = $pb->getHistory($option_id, 'options', 5);

				foreach($history as $h) {
					echo "<tr>";
					echo "<td>". date("d/m/Y", strtotime($h['added_date'])) ."</td>";
					echo "<td>". $all_staff_list[$h['staff']] ."</td>";
					echo "<td style='text-align: left;'>";
					echo $h['history'];
					echo "</td>";
					echo "</tr>";
				}
				?>
			</table>
		</li>
		<?php } ?>

		<li>
			<br><br>
			<div class="grid10" style="text-align:center">
				<?php if ($options != null) { ?>
				<input type="hidden" name="pb_options[id]" value="<?= $option_id ?>" />
				<?php } ?>

				<?php if ($lock !== false && $lock['staff_id'] != $_GLOBALS['staff']['id'] && $action == "do_edit_option") { ?>
				<input type="button" value="Locked" disabled style="width: 100px"/>
				<?php } else { ?>
				<input type="submit" value="Save" style="width: 100px"/>
				<?php } ?>
			</div>
		</li>
	</ul>

</form>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="/webadmin/product_builder/development/pb-ui/pb-option-ui-1.0.0.js" type="text/javascript"></script>
<script type="text/javascript">


$(document).ready(function(){
	var test = new OptionEditor('optionList');
	test.init("standard");
});

</script>