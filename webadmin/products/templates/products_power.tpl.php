<!--
Created and modified by Mingze Xi@ Feb 9, 2015
Last Update: Feb 16, 2015

Finished: 100%

Unfinished Job:

based on products_power.tpl.php@AGS/webadmin/products/templates -->

<!--Power Consumption Section-->
<div align="center">
	<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPower\');return false">Power Consumption</a>') ?>
</div>

<div id="sectionPower">

<table class="wide" align="center" style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;">
	<tr>
		<td class="left" style="background-color: #EEEEE0;"><b>Power Consumption</b></td>
		<td class="right" style="background-color: #EEEEE0;"><input type="text" name="data1[power_consumption]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['power_consumption']) ?>" /></td>
	</tr>
</table>
</div>