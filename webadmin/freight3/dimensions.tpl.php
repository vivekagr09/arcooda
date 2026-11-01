<!-- dimensions.tpl -->

<?php

$help_url = "/webadmin/help.php?";

$item["max_width"] = bcadd($item["max_width"], "0.000", 3);
$item["max_depth"] = bcadd($item["max_depth"], "0.000", 3);
$item["max_height"] = bcadd($item["max_height"], "0.000", 3);
$item["max_weight"] = bcadd($item["max_weight"], "0.000", 3);

$max_width_ins = bcdiv($item["max_width"], "2.54", 3);
$max_depth_ins = bcdiv($item["max_depth"], "2.54", 3);
$max_height_ins = bcdiv($item["max_height"], "2.54", 3);
$max_weight_lbs = bcdiv($item["max_weight"], "2.20462262", 3);
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='<?php echo $section_handler->section_name ?>[max_rule]']").change(function() {
		var val = $(this).val();
		$("option", this).each(function() {
			if ($(this).val() != val) $(".max_rule_" + $(this).val()).hide();
		});
		$(".max_rule_" + val).show();
	}).change();
	$(["max_width", "max_depth", "max_height"]).each(function() {
		$("[name='<?php echo $section_handler->section_name ?>[" + this + "]']").change(function() {
			var type = this.name.split("[")[1].split("]")[0];
			var val = bcadd($(this).val(), "0.000", 3);
			$(this).val(val);
			$("[name='" + type + "_ins']").val(bcdiv(val, "2.54", 3));
		});
		$("[name='" + this + "_ins']").change(function() {
			var type = this.name.substr(0, this.name.length - 4);
			var val = bcadd($(this).val(), "0.000", 3);
			$(this).val(val);
			$("[name='<?php echo $section_handler->section_name ?>[" + type + "]']").val(bcmul(val, "2.54", 3));
		});
	});
	$(["max_weight"]).each(function() {
		$("[name='<?php echo $section_handler->section_name ?>[" + this + "]']").change(function() {
			var type = this.name.split("[")[1].split("]")[0];
			var val = bcadd($(this).val(), "0.000", 3);
			$(this).val(val);
			$("[name='" + type + "_lbs']").val(bcdiv(val, "2.54", 3));
		});
		$("[name='" + this + "_lbs']").change(function() {
			var type = this.name.substr(0, this.name.length - 4);
			var val = bcadd($(this).val(), "0.000", 3);
			$(this).val(val);
			$("[name='<?php echo $section_handler->section_name ?>[" + type + "]']").val(bcmul(val, "2.20462262", 3));
		});
	});
});
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDimensions\');return false">Dimensions</a>') ?>

<div id="sectionDimensions">

<table class="table_fields">
	<tr>
		<th style="width:25%;">Dimension Rules (<a href="<?php hecho($help_url . "max_rule") ?>" target="_blank">help</a>) :</th>
		<td><select name="<?php hecho($section_handler->section_name . "[max_rule]") ?>" class="wide">
			<option value=""></option>
			<?php echo formatHTMLOptions($section_handler->getOptionList("max_rule"), $item["max_rule"]) ?>
		</select></td>
	</tr>
	<tr class="max_rule_satchel max_rule_crate max_rule_cylinder max_rule_girth">
		<th>Max Dimensions (cms) :</th>
		<td>
			<span class="max_rule_satchel max_rule_crate">
				<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[max_width]") ?>" value="<?php hecho($item["max_width"]) ?>" size="7" />
			</span>
			<b class="max_rule_satchel max_rule_crate">Width</b>
			<span class="max_rule_crate">x</span>
			<span class="max_rule_crate max_rule_cylinder max_rule_girth">
				<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[max_depth]") ?>" value="<?php hecho($item["max_depth"]) ?>" size="7" />
			</span>
			<b class="max_rule_crate">Depth</b>
			<b class="max_rule_cylinder">Girth</b>
			<b class="max_rule_girth">Total</b>
			x
			<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[max_height]") ?>" value="<?php hecho($item["max_height"]) ?>" size="7" />
			<b class="max_rule_satchel max_rule_crate">Height</b>
			<b class="max_rule_cylinder max_rule_girth">Length</b>
		</td>
	</tr>
	<tr class="max_rule_satchel max_rule_crate max_rule_cylinder max_rule_girth">
		<th>Max Dimensions (inches) :</th>
		<td>
			<span class="max_rule_satchel max_rule_crate">
				<input type="text" class="number" name="max_width_ins" value="<?php hecho($max_width_ins) ?>" size="7" />
			</span>
			<b class="max_rule_satchel max_rule_crate">Width</b>
			<span class="max_rule_crate">x</span>
			<span class="max_rule_crate max_rule_cylinder max_rule_girth">
				<input type="text" class="number" name="max_depth_ins" value="<?php hecho($max_depth_ins) ?>" size="7" />
			</span>
			<b class="max_rule_crate">Depth</b>
			<b class="max_rule_cylinder">Girth</b>
			<b class="max_rule_girth">Total</b>
			x
			<input type="text" class="number" name="max_height_ins" value="<?php hecho($max_height_ins) ?>" size="7" />
			<b class="max_rule_satchel max_rule_crate">Height</b>
			<b class="max_rule_cylinder max_rule_girth">Length</b>
		</td>
	</tr>
	

	<tr class="max_rule_satchel max_rule_crate max_rule_cylinder max_rule_girth">
		<th>Volumn Size Restrictions(cms) :</th>
		<td>
			<span class="">
				<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[vol_size_width]") ?>" value="<?php hecho($item["vol_size_width"]) ?>" size="7" />
			</span>
			<b class="">Width</b>
			<span class="">x</span>
			<span class="">
				<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[vol_size_depth]") ?>" value="<?php hecho($item["vol_size_depth"]) ?>" size="7" />
			</span>
			<b class="">Depth</b>
			x
			<input type="text" class="number" name="<?php hecho($section_handler->section_name . "[vol_size_height]") ?>" value="<?php hecho($item["vol_size_height"]) ?>" size="7" />
			<b class="">Height</b>
		</td>
	</tr>

	<tr>
		<th>Cubic Divisor</th>
		<td><input type="text" class="number" name="<?php hecho($section_handler->section_name . "[cubic_divisor]") ?>" value="<?php hecho($item["cubic_divisor"]) ?>" size="4" /> <b>cm&sup3;/kg</b><br />
		(Common values: 4000 for air, 5000 for land, 6000 for sea)</td>
	</tr>
</table>

<br />
<br />

</div>

<!-- /dimensions.tpl -->
