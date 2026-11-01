<!-- insurance.tpl -->

<?php
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='<?php hecho($section_handler->section_name . "[insurance]") ?>']").change(function() {
		var insurance = $(this).val();
		$("[name='<?php hecho($section_handler->section_name . "[insurance]") ?>']").val(insurance);
		$("option", this).each(function() {
			if ($(this).val() != insurance) $(".insurance_" + $(this).val()).hide();
		}).each(function() {
			if ($(this).val() == insurance) $(".insurance_" + $(this).val()).show();
		});
	}).change();
});
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionInsurance\');return false">Insurance</a>') ?>

<div id="sectionInsurance">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Insurance :</th>
		<td style="width:75%;"><select class="wide" name="<?php hecho($section_handler->section_name . "[insurance]") ?>">
			<?php echo formatHTMLOptions($section_handler->getOptionList("insurance"), $item['insurance']) ?>
		</select></td>
	</tr>
	<tr class="insurance_y">
		<th>Free Value Provided :</th>
		<td class="unfinished">$<input style="width:90%;" type="text" name="<?php hecho($section_handler->section_name . "[insurance_value]") ?>" value="<?php hecho($item["insurance_value"]) ?>"></td>
	</tr>
	<tr class="insurance_o">
		<th>% Value of Goods :</th>
		<td class="unfinished"><input style="width:80%;" type="text" name="<?php hecho($section_handler->section_name . "[insurance_percent]") ?>" value="<?php hecho($item["insurance_percent"]) ?>" />% - normally as % of value of goods</td>
	</tr>
	<tr>
		<th>Highway Group Insurance :</th>
		<td class="unfinished"><input style="width:55%;" type="text" name="<?php hecho($section_handler->section_name . "[insurance_highway]") ?>" value="<?php hecho($item["insurance_highway"]) ?>">% Value of goods</td>
	</tr>
	<tr>
		<th></th>
		<td class="unfinished">
			<input type="checkbox" name=""> Use Highway Insurance<br />
			<input type="checkbox" name=""> Use Freight Company Insurance
		</td>
	</tr>
</table>
<br />
<br />

</div>

<!-- /insurance.tpl -->
