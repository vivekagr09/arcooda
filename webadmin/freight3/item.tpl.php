<!-- item.tpl -->

<?php
/*
$forwarder_add_url = "/webadmin/forwarders/?return_message=add_forwarder&action=add";
$forwarder_edit_url = "/webadmin/forwarders/?return_message=edit_forwarder&action=edit&forwarder_id=";
*/

$forwarder_add_url = '/webadmin/freight_forwarders_/edit_forwarder.php?action=add';
$forwarder_edit_url = '/webadmin/freight_forwarders_/edit_forwarder.php?action=edit&id=';
?>

<script type="text/javascript">
//<![CDATA[
function forwarderSetData(data)
{
	return false;
}

$(function() {
	
	$("[name='<?php hecho($section_handler->section_name . "[tracking]") ?>']").change(function() {
		var tracking = $(this).val();
		$("[name='<?php hecho($section_handler->section_name . "[tracking]") ?>']").val(tracking);
		$("option", this).each(function() {
			if ($(this).val() != tracking) $(".tracking_" + $(this).val()).hide();
		}).each(function() {
			if ($(this).val() == tracking) $(".tracking_" + $(this).val()).show();
		});
	}).change();
	$("[name='<?php hecho($section_handler->section_name . "[forwarder_id]") ?>']").change(function() {
		var forwarder_id = $(this).val();
		if (parseInt(forwarder_id) > 0) $("[name='forwarder_edit_button']").attr("disabled", "");
		else $("[name='forwarder_edit_button']").attr("disabled", "disabled");
	}).change();
	$("[name='forwarder_add_button']").click(function() {
		window.open(<?php jecho($forwarder_add_url) ?>);
	}).show();
	$("[name='forwarder_edit_button']").click(function() {
		var forwarder_id = $("[name='<?php hecho($section_handler->section_name . "[forwarder_id]") ?>']").val();
		if (parseInt(forwarder_id) > 0) window.open(<?php jecho($forwarder_edit_url) ?> + forwarder_id);
	}).show();
});
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionItem\');return false">Item</a>') ?>

<div id="sectionItem">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Service Name :</th>
		<td style="width:75%;"><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[name]") ?>" value="<?php hecho($item["name"]) ?>" /></td>
	</tr>
	<tr>
		<th style="width:25%;">Online Service Name <span style="height: 15px; width: 15px; display: inline-block; text-align: center; vertical-align: middle; border-radius: 50%; background: #000; color: #FFF;"onmouseover="tt_in(&quot;This text will show in the invoice in the SHIP VIA section \n If you leave this empty, the Supplier’s name will be displayed online.&quot;)" onmouseout="tt_out()">?</span> :</th>
		<td style="width:75%;"><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[online_name]") ?>" value="<?php hecho($item["online_name"]) ?>" /></td>
	</tr>
	<tr>
		<th>Service Type :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[service_type]") ?>">
			<option value=""></option>
			<?= formatHTMLOptions($section_handler->getOptionList("service_type"), $item["service_type"]) ?>
		</select></td>
	</tr>
	<tr>
		<th>Service From :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[service_from]") ?>">
			<option value=""></option>
			<?= formatHTMLOptions($section_handler->getOptionList("service_from"), $item["service_from"]) ?>
		</select></td>
	</tr>
	<tr>
		<th>Product Type :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[product_type]") ?>">
			<option value=""></option>
			<?= formatHTMLOptions($section_handler->getOptionList("product_type"), $item["product_type"]) ?>
		</select></td>
	</tr>
	<tr>
		<th>Supplier :</th>
		<td>
			<select style="width:80%;" name="<?php hecho($section_handler->section_name . "[forwarder_id]") ?>">
				<option value=""></option>
				<?php echo formatHTMLOptions($section_handler->getOptionList("forwarder_id"), $item["forwarder_id"]) ?>
			</select>
			<input name="forwarder_add_button" style="display:none;" type="button" value="Add" />
			<input name="forwarder_edit_button" style="display:none;" disabled="disabled" type="button" value="Edit" />
		</td>
	</tr>
	<tr>
		<th>Supplier Code :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[forwarder_code]") ?>" value="<?php hecho($item["forwarder_code"]) ?>" /></td>
	</tr>
	<tr>
		<th>Currency :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[currency]") ?>">
			<option value=""></option>
			<?= formatHTMLOptions($section_handler->getOptionList("currency"), $item["currency"]) ?>
		</select></td>
	</tr>
	<tr>
		<th>Our Account Number :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[account_no]") ?>" value="<?php hecho($item["account_no"]) ?>" /></td>
	</tr>
	<tr>
		<th>Minimum Order :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[min_order]") ?>" value="<?php hecho($item["min_order"]) ?>" /></td>
	</tr>
	<tr>
		<th>Tracking Service :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[tracking]") ?>">
			<?php echo formatHTMLOptions($section_handler->getOptionList("tracking"), $item['tracking']) ?>
		</select></td>
	</tr>
	<tr class="tracking_y tracking_o">
<?php
if ($item["tracking_url"] != "")
{
?>
		<th><a href="<?php hecho($item["tracking_url"]) ?>" target="_blank">Tracking Website :</a></th>
<?php
}
else
{
?>
		<th>Tracking Website :</th>
<?php
}
?>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[tracking_url]") ?>" value="<?php hecho($item["tracking_url"]) ?>" /></td>
	</tr>
</table>
<br />
<br />

</div>

<!-- /item.tpl -->
