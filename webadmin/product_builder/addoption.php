<?php 
/**
 *	Module : Product Builder
 *
 *	addoption.php
 *
 *	======================================
 *	TODO: Need to work on history record
 *	======================================
 *
 *	This page is to manage the options. The option has different items.
 *	And, each item can be rearranged in the position, so that the items will
 *  be shown in order of position.
 *
 *	The UI is controlled by options.js
 *
 *	The structure of data collection
 *	----------------------------------------
 *	[pb_options] => array(
 *		[name] => "",
 *		[title] => "",
 *		[template] => "",
 *		[options] => array(
 *			[0] => array(
 *				[] => array(
 *					[product_id] => "",
 *					[site_id] => "",
 *					[price] => "",
 *					[currency] => "",
 *					[position] => ""
 *				)
 *			),
 *			[1] => array(
 *				[] => array(
 *					[header] => "",
 *					[text] => "",
 *					[price] => "",
 *					[currency] => "",
 *					[position] => ""
 *				)
 *			),
 *			[2] => array(
 *				[] => array(
 *					[header] => "",
 *					[img] => "",
 *					[ext] => "",
 *					[description] => "",
 *					[price] => "",
 *					[currency] => "",
 *					[position] => ""
 *				)
 *			),
 *			[3] => array(
 *				[] => array(
 *					[header] => "",
 *					[youtube] => "",
 *					[description] => "",
 *					[price] => "",
 *					[currency] => "",
 *					[position] => ""
 *				)
 *			)
 *		)
 *	)
 *
 *	In the options, the array keys indicate the option key
 *	0 - Link Product : The item is selected from the product list from database
 *	1 - Link Text    : The item is text based.
 *  2 - Link Photo   : The item is custom photo based. The photo need to be uploaded.
 *  3 - Video        : The item is video based. The video is from youtube.
 *
 *  The option key 0, 1, 2 are for standard template.
 *	The option key 3 is only available for video feature template. 
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  09 JAN 2018
 */

include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);
$product_rules=$pb->getOptions(null, null,null, null,'rule');

// Options from database
$options = [];

// Action to be performed
$action = "do_add_option";

// To edit option
if ($posted['action'] == "edit_option") {
	$option_id = $posted['option_id'];
	$options = $pb->getOptionById($option_id);
	$action = "do_edit_option";
}

// Option name
$option_name = (!array_key_exists('name', $options)) ? "" : $options['name'];

// Option title
$option_title =(!array_key_exists('title', $options)) ? "" : $options['title'];

// Title
$title = (count($options)==0) ? "Add New Option" : "Edit Option - " . ucwords($option_name);

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

<form action="index.php?action=<?= $action ?>" method="post" style="width: 96%;" onsubmit="if (document.getElementById('option-name-input').value.length > 0) { return true; } else { alert('Name is missing.'); return false;};" enctype='multipart/form-data'>
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
					<td class="left-title">Option Name : </td>
					<td><input type="text" name="pb_options[name]" class="text-input" value="<?= $option_name ?>" id="option-name-input"/></td>
				</tr>

				<tr>
					<td class="left-title">Title : </td>
					<td><input type="text" name="pb_options[title]" class="text-input" value="<?= $option_title ?>" id="option-title-input"/></td>
				</tr>
				<tr>
		            <td class="left-title">Option Picture : </td>
		            <td>
		               
		               	
		                <div id="sectionItemImage" style="padding:10px 0px;">
		                    <a href="<?php hecho(array_key_exists('option_image', $options) ? $options['option_image'] : '#')?>" target="_blank" id="item_image_a"><img id="item_image_img" src="/images/builder/<?=$options['option_image'] ?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
							
		                </div>
		                 <div id="productPictureRemote">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
		                    <input style="width:272px;" name="option_image" type="file" accept="image/*" />
		                    <input type="hidden" name="pb_options[option_image]" value="<?= $option_image ?>">
		                </div>
		                <?php
							if (array_key_exists('option_image', $options)) {
							?>
						                    
						                    <small style="width: 98%; resize: vertical; height: 70px; margin-top: 10px;"><i>NB: Uploading new image will overwrite existing image, Center image will display actual size</i><br/>
						                    </small>
						                    <br />
						<?php
						}
						?>
		            </td>
		        </tr>
		        <tr>
					<td class="left-title">Mouse Over text : </td>
					<td><textarea  name="pb_options[mouse_over_text]" style="width: 98%;" value="<?= $options['mouse_over_text'] ?>" id="mouse_over_text" cols="75" rows="7"><?= $options['mouse_over_text'] ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 13px;"></td>
				</tr>
				<tr>
					<td class="left-title">Template : </td>
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
					$sortedOptions = $pb->sortOptionsByPosition($options['options']);
					foreach($sortedOptions as $option_val) {
						// Get options key
						$option_list_key = $option_val['option_key'];
						$rulesinput=json_encode($option_val['rules']);
						switch($option_list_key) {
							case 0 :	// Product
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								$productimage= sprintf('/p/%s-%d.jpg??width=250&height=250&crop=true', urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $option_val['product_name']), '-')), $option_val['product_id'] );
								echo "<div class='option-init' data-option-key='0'>";
								echo "<input type='hidden' class='product-id-input' value='{$option_val['product_id']}' />";
								echo "<input type='hidden' class='product-name-input' value='{$option_val['product_name']}' />";
								echo "<input type='hidden' class='product-short-desc-input' value='{$option_val['short_desc']}' />";
								echo "<input type='hidden' class='site-id-input' value='{$option_val['site_id']}' />";
								echo "<input type='hidden' class='sub-id-input' value='0' />";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "<input type='hidden' class='option-input-default' value='{$option_val['default']}' />";
								echo "<input type='hidden' class='option-input-qty' value='{$option_val['qty']}' />";
								echo "<input type='hidden' class='option-youtube-input' value='{$option_val['youtube']}' />";
								echo "<input type='hidden' class='color-html-input' value='".json_decode($option_val['color_html'])."' />";
								echo "<input type='hidden' class='option-rules-input' value='".$rulesinput."' />";
								echo "<input type='hidden' class='option-image-input' value='".$productimage."' />";
								echo "</div>";
								break;

							case 1 :	// Text
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								
								echo "<div class='option-init' data-option-key='1'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<textarea class='text-input'>{$option_val['text']}</textarea>";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='option-input-default' value='{$option_val['default']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "<input type='hidden' class='option-rules-input' value='".$rulesinput."' />";
								echo "<input type='hidden' class='option-input-qty' value='{$option_val['qty']}' />";
								echo "</div>";
								break;

							case 2 :	// Photo
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								
								echo "<div class='option-init' data-option-key='2'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<input type='hidden' class='src-input' value='{$option_val['img']}.{$option_val['ext']}' />";
								echo "<textarea class='description-input'>{$option_val['description']}</textarea>";
								echo "<input type='hidden' class='option-input-default' value='{$option_val['default']}' />";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "<input type='hidden' class='option-rules-input' value='".$rulesinput."' />";
								echo "<input type='hidden' class='option-input-qty' value='{$option_val['qty']}' />";
								echo "</div>";
								break;

							case 3 :	// Video
								$currency = $option_val['currency'];
								$price = $option_val['price'];
								$usd = convertCurrency($price, $currency, "USD");
								$aud = convertCurrency($price, $currency, "AUD");
								
								echo "<div class='option-init' data-option-key='3'>";
								echo "<input type='hidden' class='header-input' value='{$option_val['header']}' />";
								echo "<input type='hidden' class='youtube-input' value='{$option_val['youtube']}' />";
								echo "<textarea class='description-input'>{$option_val['description']}</textarea>";
								echo "<input type='hidden' class='option-input-default' value='{$option_val['default']}' />";
								echo "<input type='hidden' class='info-link-input' value='{$option_val['infolink']}' />";
								echo "<input type='hidden' class='price-input' value='{$option_val['price']}' />";
								echo "<input type='hidden' class='currency-input' value='{$option_val['currency']}' />";
								echo "<input type='hidden' class='usd-input' value='{$usd}' />";
								echo "<input type='hidden' class='aud-input' value='{$aud}' />";
								echo "<input type='hidden' class='option-rules-input' value='".$rulesinput."' />";
								echo "<input type='hidden' class='option-input-qty' value='{$option_val['qty']}' />";
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
			<div class="flagHeader"><a href='#' style='color:#fff;' onclick="toggleSection('historySection'); return false;"><span>Last 5 Changes</span></a>&nbsp;<a href="index.php?action=view_history&id=<?= $option_id ?>&section=options&return_message" target="_blank">View All</a></div>
			<br>
		</li>

		<li id='historySection' style='display: none;'>
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

				<?php if ($lock !== false && $lock['staff_id'] != $_GLOBALS['staff']['id'] && $action == "do_edit_option" && 0) { ?>
				<input type="button" value="Locked" disabled style="width: 100px"/>
				<?php } else { ?>
				<input type="submit" value="Save" style="width: 100px" onclick="return validatedefault();"/>
				<?php } ?>
			</div>
		</li>
	</ul>
</form>
<script type="text/javascript">
var product_rules=<?php echo  json_encode($product_rules) ?> ;
</script>
<?php include "templates/option_footer.tpl.php"; ?>
<script type="text/javascript">
	$("[name='option_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_image_img").attr("src", e.target.result);
            $("#item_image_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>
</body>
</html>