<!--
Created and modified by Mingze Xi@ Feb 2, 2015
Last Update: Feb 16, 2015

Finished: 100%
Unfinished job:
based on products_description.tpl@AGS -->

<script type="text/javascript">
//<![CDATA[
function productDescriptionAdd(title, description)
{
	if (description == null) description = "";
	var theText = getValue("products[product_details]").split("\n");
	if (theText.length == 1 && theText[0] == "") theText = [];
	var added = false;
	var positions = {};
	for (var i = 0; i < theText.length; i++)
	{
		if (theText[i] == title) added = true;
		positions[theText[i]] = i;
	}
	var pos = theText.length;
	//This switch always ensures the order Features, Specifications, Includes
	switch (title)
	{
	case "Features:":
		if (positions["Includes:"] != null) pos = positions["Includes:"];
		if (positions["Specifications:"] != null) pos = positions["Specifications:"];
		break;
	case "Specifications:":
		if (positions["Includes:"] != null) pos = positions["Includes:"];
		break;
	}
	if (positions[title] != null) pos = positions[title] + 1;
	if (!added) theText.splice(pos, 0, title, "-" + description, "");
	else
	{
		for (; pos < theText.length; pos++) if (theText[pos].charAt(0) != '-') break;
		theText.splice(pos, 0, "-" + description);
	}
	setValue("products[product_details]", theText.join("\n"));
}
//]]>
</script>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionDescription\');">Product Description</a>') ?>

<div id="sectionDescription">

	<div id="sectionDesc" class="tabTable" style="width:95%;"><br />

	<table class="table_fields">
		<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">Short Description :</td>
			<td><?php echo formatHTMLTextarea("products[short_desc]", $product["short_desc"], true, "", 7) ?></td>
		</tr>
		<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">Long Description :</td>
			<td>
				<input type="button" value="Copy Description from Above" onclick="setValue('products[long_desc]', getValue('products[short_desc]'))" /><br />
				<?php echo formatHTMLTextarea("products[long_desc]", $product["long_desc"], true, "", 10) ?>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">Product Details :</td>
			<td>
				<?php echo formatHTMLTextarea("products[product_details]", $product["product_details"], true, "", 8) ?><br />
				<input style="width:22%;" type="button" value="Add Feature" onclick="productDescriptionAdd('Features:')" />
				<input style="width:22%;" type="button" value="Add Specification" onclick="productDescriptionAdd('Specifications:')" />
				<input style="width:22%;" type="button" value="Add Include" onclick="productDescriptionAdd('Includes:')" /><br />
			</td>
		</tr>
			<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">Notes :</td>
			<td><?php echo formatHTMLTextarea("products[notes]",$product['notes'], true, "", 5) ?></td>
		</tr>
		<tr>
			<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">&nbsp;</td>
		</tr>
	</table><br /><br /></div>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionDescription_hideNote">
	Product Description can be viewed by Clicking "Product Description" above<br />
</div>

<!-- /products_description.tpl -->
