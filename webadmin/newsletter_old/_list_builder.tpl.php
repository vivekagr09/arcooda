<!-- list_builder.tpl.php -->

<?php
$site_list = $section_handler->getOptionList('site_id');
if (11 != $_GLOBALS['site_settings']['site_id']) {
    $site_list = array($_GLOBALS['site_settings']['site_id'] => $site_list[$_GLOBALS['site_settings']['site_id']]);
}

$interests_list = array(
    1   => 'Dedicated Machines',
    2   => 'PCBs and Kits',
    3   => 'Pinballs',
    4   => 'Redemption Machines',
    5   => 'Token Machines',
    6   => 'Gambling Machines',
    7   => 'Touchscreen Games',
    8   => 'Novelty Machines',
    9   => 'Carnival Machines',
    10  => 'Simulators',
    11  => 'Photo Machines',
    12  => 'Jukeboxes',
    13  => 'Spare Parts',
    14  => 'Vending Machines',
    100 => 'None',
    101 => 'Home Market Products',
    102 => 'Kiddy Rides',
);
$business_list = array(
    1   => 'Amusement Centre Operator',
    2   => 'Site Operator / Street Run',
    3   => 'Trader / Reseller',
    4   => 'Private Collector',
    5   => 'Casino Operator',
    6   => 'Manufacturer',
    7   => 'Distributor',
    8   => 'Amusement Park',
    9   => 'Carnival Rides',
    10  => 'Other',
    100 => 'None',
);
$disable_checkboxes = true;
if ('add' == $action) {
    $disable_checkboxes = false;
}

$disable_string = (($disable_checkboxes) ? ' disabled="disabled"' : '');
?>

<script type="text/javascript">
//<![CDATA[
function sectionListBuilderPreview()
{
	$('form#<?=$section_name?>_form').eq(0).each(function() {
		var action=this.action;			var target=this.target;
		this.action='preview_list.php';	this.target='_blank';
		this.submit();
		this.action=action;				this.target=target;
	});
	return false;
}
function sectionListBuilderInvertSelection(name)
{
	$('[name="<?=$section_name?>['+name+'][]"]').each(function() {this.checked=!this.checked;});
	return false;
}
function sectionListBuilderSelectAll(name)
{
	$('[name="<?=$section_name?>['+name+'][]"]').each(function() {this.checked=true;});
	return false;
}
function sectionListBuilderToggleEnable(toggle)
{
	$('#sectionListBuilder :checkbox[name!="<?=$section_name?>_list_rebuild"]').each(function() {this.disabled=!toggle;}).filter(':not(:checked)').parent().css('display',(toggle)?'':'none');
	$('#sectionListBuilder span.note').toggle(toggle);
	return true;
}
function sectionListBuilderSelectSite(site_id)
{
	var theElems=$('[name="<?=$section_name?>[active_id][]"]').add('[name="<?=$section_name?>[mailinglist_id][]"]').add('[name="<?=$section_name?>[blocklist_id][]"]');
	theElems.each(function() {this.checked=false;});
	if(site_id!=null && site_id!="") theElems.filter('[value="'+site_id+'"]').each(function() {this.checked=true;});
}
//]]>
</script>

<style type="text/css">
div.wrappedOption {display:inline-block;vertical-align:top;width:232px;}
.wide td.left_title .note {font-size:66%;<?=($disable_checkboxes) ? 'display:none;' : ''?>}
.wide td.left_title .note a {color:blue;}
</style>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionListBuilder\');">List Builder</a>', '<a href="#" onclick="return sectionListBuilderPreview()">Preview List</a>')?>

<div id="sectionListBuilder">
<?php
if ('add' == $action) {
?>
<input type="hidden" name="<?=$section_name?>_list_rebuild" value="on" />
<?php
}
?>
	<table class="wide">
<?php
if ('add' != $action) {
?>
		<tr style="border-bottom:1px dashed #e9a;">
			<td class="left_title">Rebuild Options</td>
			<td><input type="checkbox" name="<?=$section_name?>_list_rebuild" onclick="return sectionListBuilderToggleEnable(this.checked)"<?=($disable_checkboxes) ? '' : ' checked="checked"'?> /> Rebuild On Submit Changes</td>
		</tr>
<?php
}
?>
		<tr style="border-bottom:1px dashed #e9a;">
			<td class="left_title" style="width:25%;">
				Include Active Members<br />
				<span class="note">(<a href="#" onclick="return sectionListBuilderSelectAll('active_id')">Select All</a> / <a href="#" onclick="return sectionListBuilderInvertSelection('active_id')">Invert Selection</a>)</span>
			</td>
			<td style="width:75%;">
<?php
$selected = explode(',', $item['active_id']);
foreach ($site_list as $key => $value) {
?>
				<div class="wrappedOption"<?=('add' != $action && !in_array($key, $selected)) ? ' style="display:none;"' : ''?>><input type="checkbox"<?=$disable_string?> name="<?=$section_name?>[active_id][]" value="<?=$key?>"<?=(in_array($key, $selected)) ? ' checked="checked"' : ''?> /> <?php hecho($value)?></div>
<?php
}
?>
			</td>
		</tr>
		<tr style="border-bottom:1px dashed #e9a;">
			<td class="left_title">
				Include Newsletter Members<br />
				<span class="note">(<a href="#" onclick="return sectionListBuilderSelectAll('mailinglist_id')">Select All</a> / <a href="#" onclick="return sectionListBuilderInvertSelection('mailinglist_id')">Invert Selection</a>)</span>
			</td>
			<td>
<?php
$selected = explode(',', $item['mailinglist_id']);
foreach ($site_list as $key => $value) {
?>
				<div class="wrappedOption"<?=('add' != $action && !in_array($key, $selected)) ? ' style="display:none;"' : ''?>><input type="checkbox"<?=$disable_string?> name="<?=$section_name?>[mailinglist_id][]" value="<?=$key?>"<?=(in_array($key, $selected)) ? ' checked="checked"' : ''?> /> <?php hecho($value)?></div>
<?php
}
?>
			</td>
		</tr>
		<tr style="border-bottom:1px dashed #e9a;">
			<td class="left_title">
				Skip Cancelled Emails<br />
				<span class="note">(<a href="#" onclick="return sectionListBuilderSelectAll('blocklist_id')">Select All</a> / <a href="#" onclick="return sectionListBuilderInvertSelection('blocklist_id')">Invert Selection</a>)</span>
			</td>
			<td>
<?php
$selected = explode(',', $item['blocklist_id']);
foreach ($site_list as $key => $value) {
?>
				<div class="wrappedOption"<?=('add' != $action && !in_array($key, $selected)) ? ' style="display:none;"' : ''?>><input type="checkbox"<?=$disable_string?> name="<?=$section_name?>[blocklist_id][]" value="<?=$key?>"<?=(in_array($key, $selected)) ? ' checked="checked"' : ''?> /> <?php hecho($value)?> Blocklist</div>
<?php
}
?>
			</td>
		</tr>
		<tr style="border-bottom:1px dashed #e9a;">
			<td class="left_title">
				Filter members by interest<br />
				<span class="note">(<a href="#" onclick="return sectionListBuilderSelectAll('interest_id')">Select All</a> / <a href="#" onclick="return sectionListBuilderInvertSelection('interest_id')">Invert Selection</a>)</span>
			</td>
			<td>
<?php
$selected = explode(',', $item['interest_id']);
foreach ($interests_list as $key => $value) {
?>
				<div class="wrappedOption"<?=('add' != $action && !in_array($key, $selected)) ? ' style="display:none;"' : ''?>><input type="checkbox"<?=$disable_string?> name="<?=$section_name?>[interest_id][]" value="<?=$key?>"<?=(in_array($key, $selected)) ? ' checked="checked"' : ''?> /> <?php hecho($value)?></div>
<?php
}
?>
			</td>
		</tr>
		<tr>
			<td class="left_title">
				Filter members by business type<br />
				<span class="note">(<a href="#" onclick="return sectionListBuilderSelectAll('business_id')">Select All</a> / <a href="#" onclick="return sectionListBuilderInvertSelection('business_id')">Invert Selection</a>)</span>
			</td>
			<td>
<?php
$selected = explode(',', $item['business_id']);
foreach ($business_list as $key => $value) {
?>
				<div class="wrappedOption"<?=('add' != $action && !in_array($key, $selected)) ? ' style="display:none;"' : ''?>><input type="checkbox"<?=$disable_string?> name="<?=$section_name?>[business_id][]" value="<?=$key?>"<?=(in_array($key, $selected)) ? ' checked="checked"' : ''?> /> <?php hecho($value)?></div>
<?php
}
?>
			</td>
		</tr>
	</table>
	<br />
	<br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionListBuilder_hideNote">List Builder can be viewed by Clicking "List Builder" above<br /></div>

<!-- /list_builder.tpl.php -->
