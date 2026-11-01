<!--
Created and modified by Mingze Xi@ Feb 9, 2015
Last Update: Feb 23, 2015

Update log: 1. Removed all lbs weight sections

Problem: @AGS, 1kg=2.204 (correct); However, in Coinexpress, it is 1kg=2.222, which should be caused by onchange function onchange="javascript:updateKgs(this,0,'Act')"


Finished: 80%

Unfinished job: 1.convert functions (kg->lbs & lbs->kg)
				2.tab style table
				3.Packing section (including the "add packing dimension" function and "Cubic Weight" etc.)
				4.

The product shipping (W) section starts here, based on products_shipping.tpl.php @AGS/products/template
-->

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShipping\');">Weight/Shipping</a>') ?>
<div id="sectionShipping">

	<div id="sectionUnit" class="tabTable" style="width:95%;">
		<br>
	<table class="table_fields" align="center" style="width:95%;margin:auto;font-size:100%">
		<!--Retrieve dimension data from database-->
<?php
		$statement = "SELECT *, `width` * `depth` * `height` AS `volume` FROM products_dimen WHERE id = '$id'";
		$result = $DB->query($statement);
		$row = $DB->fetch_array($result);
?>
		<caption style="background-color:#5C85FF;font-size:18">UNIT</caption>
		<tr>
			<th nowrap="nowrap" class="left_title" style="width:20%;">Unit Size(cms) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" size="5" name="$row['depth']" value="<?php echo $row['width'] ?>" onchange="javascript:updateIns(this,'width',0,'Dim');onChangeDimensions();" /> <b>Width x</b>
				<input type="text" size="5" name="depth1" value="<?php echo $row['depth'] ?>" onchange="javascript:updateIns(this,'depth',0,'Dim');onChangeDimensions();" /> <b>Depth x</b>
				<input type="text" size="5" name="height1" value="<?php echo $row['height'] ?>" onchange="javascript:updateIns(this,'height',0,'Dim');onChangeDimensions();" /> <b>Height</b>
			</td>
		</tr>
		<tr style="background-color:#DFF7E1;">
			<th nowrap="nowrap" class="left_title" style="width:20%;">Unit Size(inches) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" size="5" name="widthIn" value="<?php echo round($row['width'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'width',0,'Dim');onChangeDimensions();" /> <b>Width x</b>
				<input type="text" size="5" name="depthIn" value="<?php echo round($row['depth'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'depth',0,'Dim');onChangeDimensions();" /> <b>Depth x</b>
				<input type="text" size="5" name="heightIn" value="<?php echo round($row['height'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'height',0,'Dim');onChangeDimensions();" /> <b>Height</b>
			</td>
		</tr>
		<tr>
			<td class="left_title" style="background-color: #EEEEE0;">&nbsp;</td><td style="background-color: #EEEEE0;">&nbsp;</td>
		</tr>
		<tr>
			<th nowrap="nowrap" class="left_title">Actual Product Weight(kg) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" value="<?php echo $row['weight'] ?>" name="actProdWt" size="40" style="width:300px;" onchange="javascript:updateLbs(this,0,'Act');updateWeights(this.value)" />
				<!--
				<b>lbs</b>
				<input type="text" value="<?php echo round($row['weight'] / 0.45, 2) ?>" name="actProdWtL" size="40" style="width:300px;" onchange="javascript:updateKgs(this,0,'Act')" />
				-->
			</td>
		</tr>
		<tr>
			<th nowrap="nowrap" class="left_title">Volume(cm&sup3;) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" value="<?php echo $row['volume'] ?>" name="volWt" size="40" style="width:300px;" />
				<!--
				<b>lbs</b>
				-->
			</td>
		</tr>
		<tr>
			<th nowrap="nowrap" class="left_title">Shipping Weight(kg) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" name="data1[weight]" size="40" style="width:300px;" value="<?php echo htmlspecialchars($product['weight']) ?>" />
				<!--b>lbs</b>
				<input type="text" name="unit_online_weight_lbs" value="<?php echo round($dimension['unit_online_weight']/0.45) ?>" style="width:45%" onchange="updateKGS('unit_online_weight');" /-->
			</td>
		</tr>
		<tr>
			<th nowrap="nowrap" class="left_title">Incremental Weight(kg) :</th>
			<td style="background-color: #EEEEE0;">
				<input type="text" name="data1[weight_inc]" size="40" style="width:300px" value="<?php echo sprintf("%0.3f", $product['weight_inc']) ?>" />

				<!--b>lbs</b>
				<input type="text" name="unit_inc_weight_lbs" value="<?php echo round($dimension['unit_inc_weight']/0.45) ?>" style="width:45%" onchange="updateKGS('unit_inc_weight');" /-->
			</td>
		</tr>
	</table>

	<!--tab for container-->

	<table class="table_fields" align="center" style="width:95%;margin:auto;font-size:100%">
	<caption style="background-color:#5C85FF;font-size:18;">Container</caption>

	<!--Retrieve container data from database-->
<?php
	$str = 'SELECT * FROM container_load WHERE product_id=' . $product['id'];
	$res = $DB->query($str);
	$row = $DB->fetch_array($res);
?>
		<tr>
			<th class="left_title" nowrap="nowrap" style="text-align:right;width:20%;">20ft Container Quantity (w/o Crating) :</th>
			<td style="background-color: #EEEEE0;"><input type="text" name="qty20_unpacked" value="<?php echo $row['freeload_20ft_qty'] ?>" style="width:65%">&nbsp;
			Approx <input type="radio" name="loading20_unpacked" value="approx"<?php echo ($row['freeload_20ft_type'] != 'real') ? ' checked="checked"' : '' ?> />&nbsp;
			Real <input type="radio" name="loading20_unpacked" value="real"<?php echo ($row['freeload_20ft_type'] != 'real') ? '' : ' checked="checked"' ?> />
			</td>
		</tr>
		<tr>
			<th class="left_title" nowrap="nowrap" style="text-align:right;width:20%;">20ft Container Quantity (with Crating) :</th>
			<td style="background-color: #EEEEE0;"><input type="text" name="qty20_packed" value="<?php echo $row['contload_20ft_qty'] ?>" style="width:65%">&nbsp;
			Approx <input type="radio" name="loading20_packed" value="approx"<?php echo ($row['contload_20ft_type'] != 'real') ? ' checked="checked"' : '' ?> />&nbsp;
			Real <input type="radio" name="loading20_packed" value="real"<?php echo ($row['contload_20ft_type'] != 'real') ? '' : ' checked="checked"' ?> />
			</td>
		</tr>
		<tr>
			<th class="left_title" nowrap="nowrap" style="text-align:right;width:20%;">40ft Container Quantity (w/o Crating) :</th>
			<td style="background-color: #EEEEE0;"><input type="text" name="qty40_unpacked" value="<?php echo $row['freeload_40ft_qty'] ?>" style="width:65%">&nbsp;
			Approx <input type="radio" name="loading40_unpacked" value="approx"<?php echo ($row['freeload_40ft_type'] != 'real') ? ' checked="checked"' : '' ?> />&nbsp;
			Real <input type="radio" name="loading40_unpacked" value="real"<?php echo ($row['freeload_40ft_type'] != 'real') ? '' : ' checked="checked"' ?> />
			</td>
		</tr>
		<tr>
			<th class="left_title" nowrap="nowrap" style="text-align:right;width:20%;">40ft Container Quantity (with Crating) :</th>
			<td style="background-color: #EEEEE0;"><input type="text" name="qty40_packed" value="<?php echo $row['contload_40ft_qty'] ?>" style="width:65%">&nbsp;
			Approx <input type="radio" name="loading40_packed" value="approx"<?php echo ($row['contload_40ft_type'] != 'real') ? ' checked="checked"' : '' ?> />&nbsp;
			Real <input type="radio" name="loading40_packed" value="real"<?php echo ($row['contload_40ft_type'] != 'real') ? '' : ' checked="checked"' ?> />
			</td>
		</tr>
		<!--container figure-->
		<tr>
			<td colspan="2" nowrap="nowrap" style="background-color: #EEEEE0;">&nbsp;</td>
		</tr>

		<tr><td colspan="2" style="background-color: #F0F4F9;">
			Without Crating:<br />
			<iframe name="the_iframe1" src="/webadmin/packing/packingsearch.php?product_id=<?php echo $id ?>&amp;product_qty=1&amp;with_crating=0" style="width:100%;" onload="javascript:calcHeight();"></iframe>
		</td></tr>

		<tr><td colspan="2" style="background-color: #F0F4F9;">
			With Crating:
			<iframe name="the_iframe2" src="/webadmin/packing/packingsearch.php?product_id=<?php echo $id ?>&amp;product_qty=1&amp;with_crating=1" style="width:100%;" onload="javascript:calcHeight();"></iframe>
		</td></tr>
	</table>

</div>
