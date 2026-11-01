<!-- status.tpl -->

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='<?php hecho($section_handler->section_name . "[active_id]") ?>']").change(function() {
		var status = $("[name='<?php hecho($section_handler->section_name . "[active_id]") ?>'] option:selected").text();
		if(status == 'Inactive')
		{
			$("[name='<?php hecho($section_handler->section_name . "[active_id]") ?>']").css("background-color",'#f00');
		} else {
			$("[name='<?php hecho($section_handler->section_name . "[active_id]") ?>']").css("background-color",'#0f0');
		}
		console.log(status);
	}).change();
});
//]]>
</script>

<?php
$active_id = $item["active_id"];
if (!is_array($active_id)) $active_id = preg_split("/[^0-9]+/", $active_id);
$temp = $active_id;

$deactivate_key = $activate_key = implode(",", $temp);
if (($key = array_search($_GLOBALS["site_settings"]["site_id"], $temp)) !== false)
{
	unset($temp[$key]);
	$deactivate_key = implode(",", $temp);
}
else
{
	$temp[] = $_GLOBALS["site_settings"]["site_id"];
	$activate_key = implode(",", $temp);
}
?>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Status</a>') ?>

<div id="sectionStatus">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Status :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[active_id]") ?>">
			<option value="<?php hecho($activate_key) ?>"<?php echo (in_array($_GLOBALS["site_settings"]["site_id"], $active_id)) ? ' selected="selected"' : '' ?> style="background-color:#0f0;">Active</option>
			<option value="<?php hecho($deactivate_key) ?>"<?php echo (!in_array($_GLOBALS["site_settings"]["site_id"], $active_id)) ? ' selected="selected"' : '' ?>  style="background-color:#f00;">Inactive</option>
		</select></td>
	</tr>
</table>

<br />
<br />

</div>

<!-- /status.tpl -->
