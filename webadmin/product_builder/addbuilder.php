<style type="text/css">
	.moucehovershow p{
		padding: 10px;
		font-size: 20px;
	}

</style>
<?php 
/**
 *	Module: Product Builder
 *
 *	addbuilder.php
 *
 *	============================================
 *	TODO: Need to work on history record
 *	============================================
 *
 *	This page is to manage the machines with associated options. This page allows
 *	multiple machiens with multiple options to be added. The options associated with
 *	the machine can be set a different quantity needed.
 *
 *	The UI is controlled by builders.js
 *
 *	The structure of data collection
 *	-----------------------------------------
 *	[pb_builder] => array(
 *		[name] => "",
 *		[websites] => array(),
 *		[machines] => array(
 *			[] => array(
 *				[name] => "",
 *				[product_id] => "",
 *				[site_id] => "",
 *				[price] => "",
 *				[currency] => "",
 *				[description] => "",
 *				[options] => array(
 *					[name] => "",
 *					[id] => "",
 *					[qty] => "",
 *					[position] => ""
 *				)
 *			)
 *		)
 *	);
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

// Common
include_once "common.inc.php";

moduleLoad('product'); //productFromDatabase

// Builder Header
include_once "templates/builder_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);

// Options from database
// null if add new builder
$builders = null;

// Action to be performed
$action = "do_add_builder";

// Get builder from database if the builder is to be edited
if ($posted['action'] == "edit_builder") {
	$builder_id = $posted['builder_id'];
	$builders = $pb->getBuilderById($builder_id);
	$action = "do_edit_builder";
}

// Option name
$builder_name = ($builders == null) ? "" : $builders['name'];

// Status of option
$status = ($builders == null) ? "" : $builders['status'];

// Get categories list
$categories = $pb->getAllCategoryNames();

// Get websites from database
$ig_websites = array(	// Websites to be ignored
	3,	// Coinopexpress
	5,	// Channel Beat
	10,	// Josifovski Admin
	12,	// Freight Calculator
	15,	// Fortunewave
	21,	// Great Delta
	23,	// Radikal Darts Australia
	27,	// SVOS
	28,	// Destiny Bridal
	29,	// Images By Angelo
	32,	// FotoInc
	33	// Highway Media
);

$ig_websites_str = implode(",", $ig_websites);	// Convert array to string

$websites = array();
if ($websitesQuery = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`websites` WHERE NOT `id` IN(%s)",
    $ig_websites_str
))) {
    while ($row = $DB->fetch_assoc($websitesQuery)) {
        $websites[] = $row;
    }
    $DB->free_result($websitesQuery);
}

// Manual added websites
$websites[]['url'] =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'/';

// Check the update message
if (isset($_SESSION['updated_token']) && !empty($_SESSION['updated_token']) &&
	isset($_GET['updated']) && !empty($_GET['updated']) && $_GET['updated'] == $_SESSION['updated_token']) {
	
	echo "<p style='color: red;'>Option Data has been updated.</p>";

	unset($_SESSION['updated_token']);
}

// Check the error
if (isset($_SESSION['website_err_token']) && !empty($_SESSION['website_err_token']) &&
	isset($_GET['website_err']) && !empty($_GET['website_err']) && $_GET['website_err'] == $_SESSION['website_err_token']) {
	
	echo "<p style='color: red;'>Error: Duplicated website was found. One website can only be used for one builder.</p>";

	unset($_SESSION['website_err_token']);
}

// Title
$title = ($action == "do_add_builder") ? "Add Builder" : "Edit Builder - " . ucwords($builder_name);

// Get locks
$lock = $pb->builderGetLock($posted['gid']);

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

<form action="index.php?action=<?= $action ?>" method="post" style="width: 96%;" onsubmit = "if(document.getElementById('builder-name-input').value.length > 0) { return true; } else { alert('Name is missing.'); return false;}">
	<ul class="clr">
		<li>
			<h1 style="text-align: center;"><?= $title ?></h1>
		</li>
		<li>
			<br>
			<div class="flagHeader"><span>Builder Information</span></div>
			<br>
		</li>

		<li>
			<table class="form-table">
				<tr>
					<td class="left-title">Name of Builder</td>
					<td><input type="text" name="pb_builder[name]" class="text-input" value="<?= ucwords($builder_name) ?>" id="builder-name-input"/></td>
				</tr>

				<tr>
					<td class="left-title">Websites</td>
					<td>
						<select onchange="addWebsite(this);">
							<option disabled selected>Select Website</option>
							<?php foreach($websites as $website) { ?>
							<option value="<?= $website['url'] ?>"><?= $website['url'] ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>

				<tr>
					<td class="left-title"></td>
					<td>
						<table id="websites-display" style="width: 100%;">
							<?php  
							if ($builders != null) {
								foreach($builders['websites'] as $website) {
									echo "<tr>";
									echo "<td>";
									echo "<input type='text' name='pb_builder[websites][]' class='text-input' value='". $website['link'] ."'/>";
									echo "</td>";
									echo "<td><button type='button' class='pb-website-remove-btn' onclick='removeWebsite(this);'>Remove</button></td>";
									echo "</tr>";
								}
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td class="left-title">
						Categories<br />
						<a href="/webadmin/product_builder/?action=view_category" target="_blank">ADD</a>
					</td>
					<td>
						<table class="clr builder-options-table-cat" id="categoy-shortable-table">
<?php 
							$posNumNew = 0;
							foreach($categories as $cat) {
								$checked = "";
								$posNumNew++;
								if ($builders != null) {
									if (in_array($cat['id'], $builders['category'])) {
										$checked = "checked";
									}
								}

								echo "<tr>";
								echo "<td>";
								echo "<span class='position-number-cat'></span>";
								echo "<input type='hidden' name='pb_builder[category][".$cat['id']."][position]' value='". $posNumNew ."'/>";
								echo "</td>";
								echo "<td width='5px;'><div class='drag-icon'></div></td>";
								echo "<td  width='19px;'><input type='checkbox' name='pb_builder[category][".$cat['id']."][value]' value='". $cat['id'] ."' class='cat-checkbox' {$checked}/><td>";
								echo "<td>{$cat['name']}</td>";
								echo "</tr>";
							}
?>
						</table>
					</td>
				</tr>
				
			</table>
			<br>
		</li>

		<li>

		</li>
	</ul>

	<ul class="clr">
		<li>
			<br>
			<div class="flagHeader">
				<span>Machines</span>
				<a href="#" style="margin: 0 5px; color: #000;" onclick="window.open('/webadmin/products_new/master_products.php?return_message=add&multiple=y&search_for='); return false;"><i>Add machine</i></a>
				<!--<a href="#" style="margin: 0 5px; color: #000;" onclick="window.open('/webadmin/products/products_list.php?return_message=add'); return false;"><i>Add machine</i></a>-->
			</div>
			<br>
		</li>

		<li id="machines-loader">
			<?php if ($builders != null && !empty($builders['machines'])) { ?>
			<?php $machinesUID = 0; // Machine DOM Unique ID?>
			<?php foreach($builders['machines'] as $machine) { ?>
			<?php $productData = productFromDatabase($machine['product_id'], $machine['sub_id']); ?>

			<table class="form-table machines-table" id="machines-table-<?= $machinesUID ?>" style="margin-bottom: 50px;" data-machinesUID="<?= $machinesUID ?>">
				<tr>
					<td class="left-title">Machine Name</td>
					<td>
						<table style="width: 100%;">
							<tr>
								<td>
									<input type="text" name="pb_builder[machines][<?= $machinesUID ?>][name]" class="text-input" value="<?= $machine['name'] ?>"/>
									<input type="hidden" name="pb_builder[machines][<?= $machinesUID ?>][product_id]" value="<?= $machine['product_id'] ?>" />
									<input type="hidden" name="pb_builder[machines][<?= $machinesUID ?>][site_id]" value="<?= $machine['site_id'] ?>"/>
									<input type="hidden" name="pb_builder[machines][<?= $machinesUID ?>][id]" value="<?= $machine['id'] ?>" />
									<input type="hidden" name="pb_builder[machines][<?= $machinesUID ?>][sub_id]" value="<?= $machine['sub_id'] ?>" />
								</td>
								<td><button type="button" onclick="return deleteMachine(<?= $machinesUID ?>);">Delete</button></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="left-title">Machine Id</td>
					<td>
						<table style="width: 100%;">
							<tr>
								<td>
									<a href="/webadmin/products_new/edit.php?action=edit&id=<?= $machine['product_id']?>" target="_blank"><?= $machine['product_id'] ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="left-title">Category</td>
					<td>
						<select name="pb_builder[machines][<?= $machinesUID ?>][category]">
							<option value="" disabled selected>-- Select Category --</option>
							<?php
							foreach($categories as $cat) {
								if ($cat['id'] != 1) {
									$selected = ($cat['id'] == $machine['category']) ? "selected" : "";
									echo "<option value='{$cat['id']}' {$selected}>{$cat['name']}</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>

				<tr>
					<td class="left-title">Position to be displayed</td>
					<td>
						<input type="number" name="pb_builder[machines][<?= $machinesUID ?>][position]" class="text-input" style="width: 70px; margin-left: 2px;" value="<?= $machine['position'] ?>"/>
					</td>
				</tr>

				<tr>
					<td class="left-title">Image</td>
					<td>
						<img src="<?= $productData['thumb_url'] ?>" width="96" height="96" style="margin-left:2px;"/>
					</td>
				</tr>

				<tr>
					<td class="left-title">Price</td>
					<td>
						<?php
						// Variables to be passed to javascript
						$priceUSD = 0;
						$priceAUD = 0;
						$priceValue = 0;
						$selectUSD = "";
						$selectAUD = "";

						if ($machine['currency'] == "USD") {	// The current currency is USD
							$priceUSD = $machine['price'];
							$priceAUD = convertCurrency($priceUSD, "USD", "AUD");
							$priceValue = $priceUSD;
							$selectUSD = "selected";
						} else {								// The current currency is AUD
							$priceAUD = $machine['price'];
							$priceUSD = convertCurrency($priceAUD, "AUD", "USD");
							$priceValue = $priceAUD;
							$selectAUD = "selected";
						}
						?>
						<input type="text" name="pb_builder[machines][<?= $machinesUID ?>][price]" class="text-input" value="<?= $priceValue ?>" style="width:100px; margin-left: 2px;" data-usd="<?= $priceUSD ?>" data-aud="<?= $priceAUD ?>"/>
						<select name="pb_builder[machines][<?= $machinesUID ?>][currency]" style="height: 21px; margin-left: 2px;" onchange="machinesChangeCurrency(this);">
							<option value="USD" <?= $selectUSD ?>>USD</option>
							<option value="AUD" <?= $selectAUD ?>>AUD</option>
						</select>
					</td>
				</tr>

				<tr>
					<td class="left-title">Delivery Method</td>
					<td>
						<table class="clr">
							<tr>
								<td>
									<input type="checkbox" name="pb_builder[machines][<?= $machinesUID ?>][delivery_pickup]" <?php if ($machine['delivery_pickup'] == 1) {echo "checked";} ?>/>
									&nbsp;
									<span>Pickup</span>
								</td>
								<td>
									<input type="number" name="pb_builder[machines][<?= $machinesUID?>][delivery_pickup_price]" class="text-input" value="<?= $machine['delivery_pickup_price'] ?>" style="width:100px; margin-left: 2px;" placeholder="Price"/>
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" name="pb_builder[machines][<?= $machinesUID ?>][delivery_sea]" <?php if ($machine['delivery_sea'] == 1) {echo "checked";} ?> />
									&nbsp;
									<span>Sea Freight</span>
								</td>
								<td>
									<input type="number" name="pb_builder[machines][<?= $machinesUID?>][delivery_sea_price]" class="text-input" value="<?= $machine['delivery_sea_price'] ?>" style="width:100px; margin-left: 2px;" placeholder="price"/>
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" name="pb_builder[machines][<?= $machinesUID ?>][delivery_air]" <?php if ($machine['delivery_air'] == 1) {echo "checked";} ?> />
									&nbsp;
									<span>Air Freight</span>
								</td>
								<td>
									<input type="number" name="pb_builder[machines][<?= $machinesUID?>][delivery_air_price]" class="text-input" value="<?= $machine['delivery_air_price'] ?>" style="width:100px; margin-left: 2px;" placeholder="price"/>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td class="left-title">Description</td>
					<td>
						<textarea name="pb_builder[machines][<?= $machinesUID ?>][description]" class="textarea-input description" style="resize: vertical; width: 100%; height: 150px; font-family: Arial;"><?= $machine['description'] ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="left-title">Special Notes</td>
					<td>
						<textarea name="pb_builder[machines][<?= $machinesUID ?>][special_notes]" class="textarea-input" style="resize: vertical; width: 100%; height: 150px; font-family: Arial;"><?= $machine['special_notes'] ?></textarea>
					</td>
				</tr>
				<tr>
					<td Class="left-title">Options</td>
					<td><a href="#" style="margin: 0 5px; color: #000;" onclick="return chooseOptions(<?= $machinesUID ?>);"><i>Add options</i></a></td>
				</tr>

				<tr>
					<td colspan="2">
						<table class="view-table builder-options-table" id="builder-options-table-<?= $machinesUID ?>" style="width: 100%;" data-machinesUID="<?= $machinesUID ?>">
							<thead>
								<tr>
									<th style="width: 100px;">Position</th>
									<th style="width: 20px;"></th>
									<th style="width: 100px;"></th>
									<th>Option Title</th>
									<th>Qty</th>
									<th style="width: 100px;"></th>
								</tr>
							</thead>

							<?php 
							if (!empty($machine['options'])) {
								$posNum = 0;
								foreach($machine['options'] as $option) {
									$optionrules = $pb->getOptionRulesById($option['id']);
									$posNum++;	// Position number
									echo "<tr>";
									echo "<td>";
									echo "<span class='position-number'>". $posNum ."</span>";
									echo "<input type='hidden' name='pb_builder[machines][". $machinesUID ."][options][". ($posNum - 1) ."][position]' value='". $posNum ."'/>";
									echo "<input type='hidden' name='pb_builder[machines][". $machinesUID ."][options][". ($posNum - 1) ."][id]' value='". $option['id'] ."'/>";
									echo "<input type='hidden' name='pb_builder[machines][". $machinesUID ."][options][". ($posNum - 1) ."][name]' value='". $option['name'] ."'/>";
									echo "</td>";
									echo "<td><div class='drag-icon'></div></td>";
									echo "<td>";
									if($optionrules['non_display'] || $optionrules['display'])
									echo "<div class='moucehovershow'><a href='javascript:void(0);'style='font-size:20px;float: left;' onmouseover=\"tt_in('".$optionrules['display']."')\" onmouseout='tt_out();' >Rules</a><a  href='javascript:void(0);' onmouseover=\"tt_in('".$optionrules['non_display']."')\" onmouseout='tt_out();' > <img style='width:20px;' src='/webadmin/images/cross.gif' /></a></div>";
									echo "</td>";
									echo "<td>". ucwords($option['name']) ."</td>";
									echo "<td><input type='number' name='pb_builder[machines][". $machinesUID ."][options][". ($posNum - 1) ."][qty]' class='text-inpue' style='width:50px;' value='". $option['qty'] ."'></td>";
									echo "<td><a style='pading:5px;' class='builde-options-remove-btn sort-link' href='#' onclick='removeOption(this); return false;'>Remove</a>
										<a style='pading:5px;'  class='builde-options-remove-btn sort-link' href='/webadmin/product_builder/addoption.php?action=edit_option&option_id=".$option['id']."' target='_blank'>Edit</a></td>";
									echo "</tr>";
								}
							} else {
								echo "<tr class='no-option-text'>";
								echo "<td colspan='5'><i>No Options Added</i></td>";
								echo "</tr>";
							}
							?>
						</table>
					</td>
				</tr>
			</table>

			<?php $machinesUID++; ?>
			<?php } // END FOREACH ?>
			<?php } // END IF?>
		</li>
	</ul>

	<?php if ($action == "do_edit_builder") { ?>
	<ul class="clr">
		<li>
			<br>
			<br>
			<div class="flagHeader"><a href='#' style='color:#fff;' onclick="toggleSection('historySection'); return false;"><span>Last 5 Changes</span></a>&nbsp;<a href="index.php?action=view_history&id=<?= $builder_id ?>&section=builders&return_message" target="_blank">View All</a></div>
			<br>
		</li>

		<li id="historySection" style='display: none;'>
			<table class="view-table">
				<tr>
					<th>Date</th>
					<th>User</th>
					<th>History</th>
				</tr>
				<?php
				$history = $pb->getHistory($builder_id, 'builders', 5);

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
	</ul>
	<?php } ?>

	<ul class="clr">
		<li>
			<br>
			<div class="flagHeader"><span>Status</span></div>
			<br>
		</li>

		<li>
			<div class="grid10" style="text-align: center;">
				<select name="pb_builder[status]" style="width: 100px;">
					<option value="0" <?php if ($status == "" || $status == 0) echo "selected"; ?>>Inactive</option>
					<option value="1" <?php if ($status == 1) echo "selected"; ?>>Active</option>
				</select>
			</div>
		</li>
	</ul>

	<ul class="clr">
		<li>
			<div class="grid10" style="text-align:center">
				<?php if ($builders != null) { ?>
				<input type="hidden" name="pb_builder[id]" value="<?= $builder_id ?>" />
				<?php } ?>
				<?php if ($lock !== false && $lock['staff_id'] != $_GLOBALS['staff']['id'] && $action == "do_edit_builder" && 0) { ?>
				<input type="button" value="Locked" disabled style="width: 100px"/>
				<?php } else { ?>
				<input type="submit" value="Save" style="width: 100px"/>
				<?php } ?>
			</div>
		</li>
	</ul>

</form>

<?php include "templates/builder_footer.tpl.php"; ?>