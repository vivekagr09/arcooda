<!-- pricing.tpl -->

<?php
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPricingStructure\');return false">Pricing Structure</a>') ?>

<div id="sectionPricingStructure">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Fuel Surcharge (%) :</th>
		<td style="width:75%;"><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[fuel_charge]") ?>" value="<?php hecho($item['fuel_charge']) ?>" /></td>
	</tr>
	<tr>
		<th>Our Handling Charge (<?= getCurrencySymbol($item['currency']) ?>) :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[handling_fee]") ?>" value="<?php hecho($item['handling_fee']) ?>" /></td>
	</tr>
	<tr>
		<th>Our Handling Percent (%) :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[handling_percent]") ?>" value="<?php hecho($item['handling_percent']) ?>" /></td>
	</tr>
	<tr>
		<th>Our Retail Pricing Discount (%) :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[retail_discount]") ?>" value="<?php hecho($item['retail_discount']) ?>" /></td>
	</tr>
</table>
</div><br />

<!-- /pricing.tpl -->
