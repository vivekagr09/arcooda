<!-- additional.tpl -->

<?php
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionAdditionalDetails\');return false">Additional Details</a>') ?>

<div id="sectionAdditionalDetails">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Booking Cut-off Time :</th>
		<td style="width:75%;"><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[cut_off]") ?>" value="<?php hecho($item["cut_off"]) ?>" /></td>
	</tr>
	<tr>
		<th>Booking Phone Number :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[book_phone]") ?>" value="<?php hecho($item["book_phone"]) ?>" /></td>
	</tr>
	<tr>
		<th>Latest Pickup Time :</th>
		<td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[pickup]") ?>" value="<?php hecho($item["pickup"]) ?>" /></td>
	</tr>
</table>
<br />
<br />

</div>

<!-- /additional.tpl -->
