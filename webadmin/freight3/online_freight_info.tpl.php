<!-- online_freight_info.tpl -->

<?php
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'onlinefreightinfo\');return false">Online Freight Information</a>') ?>

<div id="onlinefreightinfo">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Registered :</th>
		<td>
			<select class="wide" id="active" name="<?php hecho($section_handler->section_name . "[registered]") ?>">
			<option value=""></option>
			<?= formatHTMLOptions($section_handler->getOptionList("freight_info"), $item["registered"]) ?>
			</select>
		</td>
		<!-- <td style="width:75%;"><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[freight_registered]") ?>" value="" /></td> -->
	</tr>
	<tr>
		<th>Signature on Delivery :</th>
		<td><select class="wide" id="active" name="<?php hecho($section_handler->section_name . "[sign_on_delivery]") ?>">
		<option value=""></option>
		<?= formatHTMLOptions($section_handler->getOptionList("freight_info"), $item["sign_on_delivery"]) ?>
        </select>
		</td>
		<!-- <td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[sign_on_delivery]") ?>" value="" /></td> -->
	</tr>
	<tr>
		<th>Tracking :</th>
		<td><select class="wide" name="<?php hecho($section_handler->section_name . "[tracking]") ?>" id="<?php hecho($section_handler->section_name . "[tracking]") ?>">
			<?php echo formatHTMLOptions($section_handler->getOptionList("tracking"), $item['tracking']) ?>
		</select></td>
	</tr>
	<tr>
		<th>Express :</th>
		<td><select class="wide" id="active" name="<?php hecho($section_handler->section_name . "[express]") ?>">
		<option value=""></option>
		<?= formatHTMLOptions($section_handler->getOptionList("freight_info"), $item["express"]) ?>
        </select>
		</td>
		<!-- <td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[freight_express]") ?>" value="" /></td> -->
	</tr>
	<tr>
		<th>Insurance :</th>
		<td style="width:75%;"><select class="wide" name="<?php hecho($section_handler->section_name . "[insurance]") ?>" id="<?php hecho($section_handler->section_name . "[insurance]") ?>">
			<?php echo formatHTMLOptions($section_handler->getOptionList("insurance"), $item['insurance']) ?>
		</select></td>
		<!-- <td><input class="wide" type="text" name="<?php hecho($section_handler->section_name . "[freight_insurance_info]") ?>" value="" /></td> -->
	</tr>
	<tr>
		<th>Notes :</th>
		<td><textarea class="wide" type="text" name="<?php hecho($section_handler->section_name . "[freight_notes]") ?>"><?=$item['freight_notes'];?></textarea></td>
	</tr>
</table>
<br />
<br />

</div>

<!-- /additional.tpl -->