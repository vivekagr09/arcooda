<!-- products_bulkweights.tpl -->

<?php
function product_bulkweights_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $hidden = array(
        'product_bulkweights[' . $count . '][id]' => $row_data['id'],
    );
    ob_start();
?>
<tr id="product_bulkweights_rowa[<?= $count ?>]">
	<td><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?><input type="number" name="product_bulkweights[<?= $count ?>][qty]" min="0" max="9999999999" step="1" size="6" value="<?php hecho($row_data['qty']) ?>" onchange="productUpdateBulkWeights(<?= $count ?>, 'qty')" style="width:96px" /></td>
	<td><input type="number" name="product_bulkweights[<?= $count ?>][width]" min="0.0" max="999999999.9" step="0.1" size="6" value="<?php hecho($row_data['width']) ?>" onchange="productUpdateBulkWeights(<?= $count ?>, 'width')" style="width:96px" /></td>
	<td><input type="number" name="product_bulkweights[<?= $count ?>][depth]" min="0.0" max="999999999.9" step="0.1" size="6" value="<?php hecho($row_data['depth']) ?>" onchange="productUpdateBulkWeights(<?= $count ?>, 'width')" style="width:96px" /></td>
	<td><input type="number" name="product_bulkweights[<?= $count ?>][height]" min="0.0" max="999999999.9" step="0.1" size="6" value="<?php hecho($row_data['height']) ?>" onchange="productUpdateBulkWeights(<?= $count ?>, 'height')" style="width:96px" /></td>
	<td><input type="number" name="product_bulkweights[<?= $count ?>][weight]" min="0.000" max="9999999.999" step="0.001" size="6" value="<?php hecho($row_data['weight']) ?>" onchange="productUpdateBulkWeights(<?= $count ?>, 'weight')" style="width:96px" /></td>
	<td><input type="number" name="product_bulkweights[<?= $count ?>][online_weight]" min="0.000" max="9999999.999" step="0.001" size="6" value="<?php hecho($row_data['online_weight']) ?>" readonly="readonly" style="width:96px" /></td>
	<td><input onclick="productDeleteBulkWeights(<?= $count ?>)" type="button" value="Delete" /></td>
</tr>
<?php
    return ob_get_clean();
}

if (is_array($product_bulkweights)) {
?>

<script type="text/javascript">
//<![CDATA[
var product_bulkweights_count = <?= max(100, count($product_bulkweights)) ?>;
function productUpdateBulkWeights(row, field) {
	var substr = "product_bulkweights[" + row + "]";

    var cubic_divisor = "5000";
    setValue(substr + "[cubic_divisor]", cubic_divisor);

    var width  = getValue(substr + "[width]");
	var depth  = getValue(substr + "[depth]");
	var height = getValue(substr + "[height]");
    var cubic  = bcdiv(bcmul(bcmul(width, depth, 3), height, 3), cubic_divisor, 3);
    setValue(substr + "[cubic]", cubic);

    var weight = bcadd(getValue(substr + "[weight]"), "0.000", 3);

    setValue(substr + "[online_weight]", (bccomp(cubic, weight, 3) > 0) ? cubic : weight);
}
function productAddBulkWeights()
{
	var success      = false;
	var count        = String(product_bulkweights_count);
	var target_table = getElement("product_bulkweights_table");

	if (target_table != null)
	{
		success = true;
		var newRow1 = addTableRow(target_table, "product_bulkweights_rowa[" + count + "]");
		if (count & 1) newRow1.className = "alt";
<?php
    $count = 0;
    foreach (explode("\n", product_bulkweights_row("[ROW_COUNT]", array(
    ))) as $row) {
        $row = trim($row);
        if (substr($row, 0, 3) == "<tr") {
            $count++;
        } elseif (substr($row, 0, 3) == "<td") {
?>
		addTableCell(newRow<?= $count ?>, <?= strtr(jecho(substr($row, strpos($row, '>') + 1, -5), 1), array(
            "[ROW_COUNT]" => '" + count +"',
        )) ?>).style.textAlign = "center";
<?php
        }
    }
?>
	}
	if (success) product_bulkweights_count++;
	return success;
}
function productDeleteBulkWeights(row)
{
	var theRow = getElement("product_bulkweights_rowa[" + row + "]");
	if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}
//]]>
</script>

<!-- <?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionBulkWeights\');return false">Bulk Weights</a>', '<a href="#" onclick="productAddBulkWeights();return false">Add Bulk Weights</a>') ?> -->

<!-- <div id="sectionBulkWeights"> -->
<tr>
	<td class="left">Bulk Weights (<a href="#" onclick="productAddBulkWeights();return false">Add Bulk Weight</a>)</td>
	<td class="right">

	<table class="wide" name="product_bulkweights_table">
		<tr>
			<th style="width:12%">Qty</th>
			<th style="width:12%">Width (cm)</th>
			<th style="width:12%">Depth (cm)</th>
			<th style="width:12%">Height (cm)</th>
			<th style="width:12%">Weight (kg)</th>
			<th style="width:12%">Online Weight (kg)</th>
			<th style="width:16%">&nbsp;</th>
		</tr>
<?php
    $affected = array();
    $count    = 1;
    foreach ($product_bulkweights as $row_data) {
        echo product_bulkweights_row($count++, $row_data);
        $affected[] = $row_data['id'];
    }
?>
	</table>
	<input type="hidden" name="product_bulkweights_affected" value="<?= implode("|", $affected) ?>" />

	</td>
</tr>
	<!-- <br /><br /> -->
<!-- </div> -->

<!-- <div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionBulkWeights_hideNote"> -->
	<!-- Bulk Weights can be viewed by Clicking "Bulk Weights" above<br /> -->
<!-- </div> -->
<?php
}
?>

<!-- /products_bulkweights.tpl -->
