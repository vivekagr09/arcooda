<!-- product_supplier2.tpl -->

<?php
moduleLoad('invoice');
moduleLoad('product'); //productFromDatabase

$secondary_currencies = array(
    "au" => "AUD",
    "us" => "USD",
);
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='invoice[ship_product_name]']").change(function() {
		shipChange('pname');
	});
	$("[name='invoice[ship_cost]']").change(function() {
		shipChange('pcost');
	});
	$("[name='invoice[ship_online]']").change(function() {
		shipChange('ponline');
	});
	$("[name='invoice[ship_offer]']").change(function() {
		shipChange('poffer');
	});
});
//]]>
</script>

<?php
/**
 * Add all missing fields
 */
foreach (array(
    "ship_id",
    "voucher_used",
    "ship_supplier_id",
    "ship_insurance",

    "products_cost",
    "products_offer",
    "products_online",
    //"ship_cost",
    //"ship_offer",
    //"ship_online",
    "charges_cost",
    "charges_offer",
    "charges_online",
    //"invoice_cost",
    //"invoice_offer",
    //"invoice_online",
    "invoice_profit",
) as $k) {
?>
<input type="hidden" name="invoice[<?php hecho($k)?>]" value="<?php hecho($invoice[$k])?>" />
<?php
}

/**
 * Add all alternate currency fields
 */
foreach (array(
    "products_cost",
    "products_offer",
    "products_online",
    //"ship_cost",
    //"ship_offer",
    "ship_online",
    "charges_cost",
    "charges_offer",
    "charges_online",
    //"invoice_cost",
    //"invoice_offer",
    "invoice_online",
    "invoice_profit",
) as $k) {
    foreach ($secondary_currencies as $k2 => $v2) {
?>
<input type="hidden" name="invoice[<?php hecho($k . $k2)?>]" value="<?php hecho($invoice[$k . $k2])?>" />
<?php
}
}

?>

<?= formatHTMLTitle("Products Requested") ?>

<div id="sectionProductsRequested">
	<table class="table_grid wide">
		<tr>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_id&quot;);return false">PID</a></th>
			<th style="width:100%;"><a href="#" onclick="product_supplier_sort(&quot;product_name&quot;);return false">Product Name</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_qty&quot;);return false">Qty.</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_cost&quot;);return false">Cost Price (<span name="invoice[invoice_currency]"><?php hecho($invoice["invoice_currency"])?></span>)</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_online&quot;);return false">Online Price (<span name="invoice[invoice_currency]"><?php hecho($invoice["invoice_currency"])?></span>)</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_offer&quot;);return false">Offer Price (<span name="invoice[invoice_currency]"><?php hecho($invoice["invoice_currency"])?></span>)</a></th>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<th><a href="#" onclick="product_supplierSort(&quot;product_offer<?php hecho($k)?>&quot;);return false">Offer <?php hecho($v)?></a></th>
<?php
}
?>
			<th></th>
		</tr>
<?php
$count = 1;
if (isset($invoice_products)) {
    foreach ($invoice_products as $v) {
        echo product_row($count++, $v);
    }
}

?>
		<tr id="product_supplier_ship_product">
			<td><input type="text" name="invoice[ship_product_str]" size="4" value="<?php hecho($invoice["ship_product_str"])?>" disabled="disabled" class="number" /></td>
			<td>
				<input type="text" name="invoice[ship_product_name]" size="64" value="<?php hecho($invoice["ship_product_name"])?>" maxlength="64" class="wide" />
				<div id="product_supplier_ship_product_extra"><textarea name="invoice[ship_product_notes]" rows="<?= max(3, count(explode("\n", $invoice["ship_product_notes"]))) ?>" cols="40" class="wide"><?php hecho($invoice["ship_product_notes"])?></textarea></div>
			</td>
			<td><input type="text" size="3" value="1" disabled="disabled" class="number" /></td>
			<td><input type="text" name="invoice[ship_cost]" size="6" value="<?php hecho(convertCurrency($invoice["ship_cost"], $invoice["invoice_currency"]))?>" maxlength="10" class="wide number" /></td>
			<td><input type="text" name="invoice[ship_online]" size="6" value="<?php hecho(convertCurrency($invoice["ship_online"], $invoice["invoice_currency"]))?>" maxlength="10" class="wide number" /></td>
			<td><input type="text" name="invoice[ship_offer]" size="6" value="<?php hecho(convertCurrency($invoice["ship_offer"], $invoice["invoice_currency"]))?>" maxlength="10" class="wide number" /></td>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<td><input type="text" name="invoice[ship_offer<?php hecho($k)?>]" size="6" value="<?php hecho(convertCurrency($invoice["ship_offer" . $k], $v))?>" maxlength="10" class="wide number" /></td>
<?php
}
?>
			<td>
				<input type="button" value="+/-" onclick="<?php hecho('$("#product_supplier_ship_product_extra").slideToggle()')?>" />
				<a href="#" onclick="product_supplier_ship_delete();return false"><img src="/webadmin/images/icon_remove.gif" /></a>
			</td>
		</tr>
<?php
$count = 1;
if (isset($invoice_charges)) {
    foreach ($invoice_charges as $v) {
        echo charge_row($count++, $v);
    }
}

?>
		<tr class="alt">
			<td colspan="3" style="text-align:right;"><b>TOTAL</b></td>
			<td><input type="text" name="invoice[invoice_cost]" size="6" value="<?php hecho(convertCurrency($invoice["invoice_cost"], $invoice["invoice_currency"]))?>" readonly="readonly" maxlength="10" class="wide number" /></td>
			<td><input type="text" name="invoice[invoice_online]" size="6" value="<?php hecho(convertCurrency($invoice["invoice_online"], $invoice["invoice_currency"]))?>" readonly="readonly" maxlength="10" class="wide number" /></td>
			<td><input type="text" name="invoice[invoice_offer]" size="6" value="<?php hecho(convertCurrency($invoice["invoice_offer"], $invoice["invoice_currency"]))?>" readonly="readonly" maxlength="10" class="wide number" /></td>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<td><input type="text" name="invoice[invoice_offer<?php hecho($k)?>]" size="6" value="<?php hecho(convertCurrency($invoice["invoice_offer" . $k], $v))?>" readonly="readonly" maxlength="10" class="wide number" /></td>
<?php
}
?>
			<td><b><?php hecho($invoice["invoice_currency"])?></b></td>
		</tr>
	</table>
	<br />
	<div style="text-align:center;">
		<input onclick="product_supplier_add_product()" type="button" value="Add Product" />
		<input onclick="product_supplier_add_generic()" type="button" value="Add Generic" />
		<input onclick="product_supplier_add_charge()" type="button" value="Add Charge" />
		<input onclick="product_supplier_add_voucher()" type="button" value="Add Voucher" />
	</div>
	<br />
	<br />
</div>

<div style="display:none;" id="sectionProductsRequested_hideNote">
	All Products requested can be viewed by Clicking "Products Requested" above<br />
</div>

<?php
//-----------------------------------------------------------------------------

$secondary_currencies = array(
    "au" => "AUD",
    "us" => "USD",
);
$supplier_list       = invoiceGetOptionList("default_supplier");
$products_staff_list = invoiceGetOptionList("products_staff_id");
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>

<?= formatHTMLTitle("Product Suppliers", '<a href="#" onclick="toggleSection(\'sectionProductSuppliersDefaults\');return false">Default Supplier Values</a>') ?>

<div id="sectionProductSuppliersDefaults" style="display:none;">
	<table class="table_fields wide">
		<tr>
			<th style="width:25%;">Supplier :</th>
			<td colspan="3">
				<input type="text" name="invoice[default_supplier]" size="4" value="<?php hecho($invoice["default_supplier"])?>" class="number" />
				<input type="button" value="S" onclick="product_supplier_search_supplier()" />
				<input type="text" name="invoice[def_sup_name]" size="64" value="<?php hecho($supplier_list[$invoice["default_supplier"]])?>" class="wide" />
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Warranty :</td>
			<td style="width:25%;"><select name="invoice[def_warranty]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_warranty"), $invoice["def_warranty"]) ?>
			</select></td>
			<th style="width:25%;">Voltage :</td>
			<td style="width:25%;"><select name="invoice[def_voltage]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_voltage"), $invoice["def_voltage"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Plug Type :</td>
			<td><select name="invoice[def_plug]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_plug"), $invoice["def_plug"]) ?>
			</select></td>
			<th>Condition :</td>
			<td><select name="invoice[def_condition]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_condition"), $invoice["def_condition"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Packing :</td>
			<td><select name="invoice[def_packing]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_packing"), $invoice["def_packing"]) ?>
			</select></td>
			<th>Condition :</td>
			<td><select name="invoice[def_terms]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_terms"), $invoice["def_terms"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Currency :</td>
			<td><select name="invoice[def_currency]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_currency"), $invoice["def_currency"]) ?>
			</select></td>
			<th>Country :</td>
			<td><select name="invoice[def_country]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("def_country"), $invoice["def_country"]) ?>
			</select></td>
		</tr>
	</table>
	<br />
	<div style="text-align:center;">
		<input type="button" value="Update Products With Default Values" />
	</div>
	<br />
	<br />
</div>

<div id="sectionProductSuppliers">
	<table class="table_grid wide">
		<tr>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_id&quot;);return false">PID</a></th>
			<th style="width:50%;"><a href="#" onclick="product_supplier_sort(&quot;product_name&quot;);return false">Product Name</a></th>
			<th style="width:50%;"><a href="#" onclick="product_supplier_sort(&quot;supplier_name&quot;);return false">Supplier</a></th>
			<th style="width:50%;"><a href="#" onclick="product_supplier_sort(&quot;supplier_found&quot;);return false">Found</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;product_qty&quot;);return false">Qty</a></th>
			<th><a href="#" onclick="product_supplier_sort(&quot;supplier_costau&quot;);return false">Cost</a></th>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<th><a href="#" onclick="product_supplierSort(&quot;supplier_cost<?php hecho($k)?>&quot;);return false">Cost <?php hecho($v)?></a></th>
<?php
}
?>
		</tr>
<?php
$count = 1;
if (isset($invoice_products)) {
    foreach ($invoice_products as $v) {
        echo supplier_row($count++, $v);
    }
}

?>
		<tr id="product_supplier_ship_supplier">
			<td><input type="text" name="invoice[ship_product_str]" size="4" value="<?php hecho($invoice["ship_product_str"])?>" disabled="disabled" class="number" /></td>
			<td><input type="text" name="invoice[ship_product_name]" size="64" value="<?php hecho($invoice["ship_product_name"])?>" maxlength="64" class="wide" /></td>
			<td><input type="text" name="invoice[ship_supplier_name]" size="64" value="<?php hecho($invoice["ship_supplier_name"])?>" maxlength="64" class="wide" /></td>
			<td><select>
			</select></td>
			<td><input type="text" size="3" value="1" disabled="disabled" class="number" /></td>
			<td><input type="text" name="invoice[ship_cost]" size="6" value="<?php hecho(convertCurrency($invoice["ship_cost"], $invoice["invoice_currency"]))?>" maxlength="10" class="wide number" /></td>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<td><input type="text" name="invoice[ship_cost<?php hecho($k)?>]" size="6" value="<?php hecho(convertCurrency($invoice["ship_cost" . $k], $v))?>" maxlength="10" class="wide number" /></td>
<?php
}
?>
		</tr>
		<tr class="alt">
			<td colspan="5" style="text-align:right;"><b>TOTAL</b></td>
<?php
foreach ($secondary_currencies as $k => $v) {
?>
			<td>
				<input type="text" name="invoice[invoice_cost<?php hecho($k)?>]" size="6" value="<?php hecho(convertCurrency($invoice["invoice_cost" . $k], $v))?>" readonly="readonly" maxlength="10" class="wide number" />
				<b><?php hecho($v)?></b>
			</td>
<?php
}
?>
		</tr>
	</table>
	<br />
	<table class="table_fields wide">
		<tr>
			<th style="width:25%;">Notes :</th>
			<td>
				Products highlighted in <span style="color:#fa9;">red</span> have no suppliers in the database<br />
				Products highlighted in <span style="color:#68b;">blue</span> are available from two or more suppliers<br />
				Products highlighted in <span style="color:#80f;">violet</span> have unknown weights<br />
			</td>
		</tr>
		<tr>
			<th>Stock Received :</th>
			<td><select name="invoice[stock_received]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("stock_received"), $invoice["stock_received"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Packing Terms (<a href="invoice_options.php?section=pack_requirement" target="_blank">Add / Edit</a>) :</th>
			<td><select name="invoice[pack_requirement_id]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("pack_requirement_id"), $invoice["pack_requirement_id"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Shipping Terms (<a href="invoice_options.php?section=ship_terms" target="_blank">Add / Edit</a>) :</th>
			<td><select name="invoice[terms_id]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("terms_id"), $invoice["terms_id"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Ships From :</th>
			<td><select name="invoice[supply_country]">
				<option value="0"></option>
				<?= formatHTMLOptions(invoiceGetOptionList("supply_country"), $invoice["supply_country"]) ?>
			</select></td>
		</tr>
		<tr>
			<th>Products Located :</th>
			<td>
				<select name="invoice[products_located]">
					<option value="0"></option>
					<?= formatHTMLOptions(invoiceGetOptionList("products_located"), $invoice["products_located"]) ?>
				</select><br />
				<b style="color:#f00;"><i><?php hecho($products_staff_list[$invoice["products_staff_id"]])?> - <?php hecho(date("d/m/y h:i", $invoice["products_locate_time"]))?></i></b>
			</td>
		</tr>
	</table>
	<br />
	<br />
</div>

<div style="display:none;" id="sectionProductSuppliers_hideNote">
	All Product Suppliers can be viewed by Clicking "Products Suppliers" above<br />
</div>


<?php
//-----------------------------------------------------------------------------

$secondary_currencies = array(
    "au" => "AUD",
    "us" => "USD",
);
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>

<?= formatHTMLTitle("Purchase Order Summary", '<a href="#" onclick="toggleSection(\'sectionPurchaseOrderSummarySummary\');return false">Toggle</a>') ?>

<div id="sectionPurchaseOrderSummary">
	<div id="sectionPurchaseOrderSummarySummary">

	</div>
	<div style="text-align:center;">
	</div>
</div>




<div id="sectionOSummary">
<table class="wide" id="orderTable" name="orderTable" style="display:none">
	<tr>
		<td style="text-align:center" colspan="2" class="left_title">Customer Needs</td>
		<td style="text-align:center" colspan="2" class="left_title">Current Supply</td>
		<td class="left_title">&nbsp;</td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Warranty :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $owarranty) ?></select></td>
		<td style="width:25%" class="left_title">Warranty :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $invoice['def_warranty']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_warranty'] == $owarranty && $owarranty > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Condition :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $ocondition) ?></select></td>
		<td style="width:25%" class="left_title">Condition :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $invoice['def_condition']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_condition'] == $ocondition && $ocondition > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
<!--
	<tr>
		<td style="width:25%" class="left_title">Packing :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $opacking) ?></select></td>
		<td style="width:25%" class="left_title">Packing :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $invoice['def_warranty']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_packing'] == $opacking && $opacking > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
-->
	<tr>
		<td style="width:25%" class="left_title">Voltage :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $ovoltage) ?></select></td>
		<td style="width:25%" class="left_title">Voltage :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $invoice['def_voltage']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_voltage'] == $ovoltage && $ovoltage > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td style="width:25%" class="left_title">Plug Type :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $oplug) ?></select></td>
		<td style="width:25%" class="left_title">Plug Type :</td>
		<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $invoice['def_plug']) ?></select></td>
		<td class="left_title"><img src="images/<?= ($invoice['def_plug'] == $oplug && $oplug > 0) ? 'tick' : 'cross' ?>.gif"></td>
	</tr>
	<tr>
		<td class="left_title">General Supply Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Not Displayed To Customer</span></td>
		<td colspan="4"><textarea name="supply_notes" rows="<?= max(5, count(explode("\n", $invoice['supply_notes']))) ?>" onkeyup="textareaAutoSize(this,1,5);"><?php hecho($invoice['supply_notes'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Customer Request Notes</td>
		<td colspan="4"><textarea name="customer_requests" rows="<?= max(5, count(explode("\n", $invoice['customer_requests']))) ?>" onkeyup="textareaAutoSize(this,1,5);"><?php hecho($invoice['customer_requests'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Products Purchased :</td>
		<td colspan="4"><select name="supply_located"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['supply_located']) ?></select></td>
	</tr>
<?php
if ('y' == $invoice['supply_located']) {
    $staff = 'Customer';
    if ($invoice['supply_staff_id'] > 0) {
        $staff = $all_staff_list[$invoice['supply_staff_id']];
    }

?>
	<tr style="color: red;font-weight:bold">
		<td class="left_title">Authorised By :</td>
		<td colspan="4"><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['supply_locate_time']) ?></i></td>
	</tr>
<?php
}
?>
</table>
<table class="wide">
	<tr>
		<td style="width:25%" class="left_title"></td>
		<td>
			<input type="button" value="Preview Summary
Purchase Order" onclick="actionPurchasePreview(this.form);" />
<?php
if (count($invoice_porders) == 0) {
?>
			<input type="button" value="Make Purchase
Order(s)" onclick="actionPurchaseOrder(this.form);" />
<?php
}
?>
		</td>
	</tr>
</table>
<span name="orderTable_hideNote"></span>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOSummary_hideNote">
	The Order Summary can be viewed by Clicking "Purchase Order Summary" above<br />
</div>
























<?php
$apply_rounding = !in_array($_GLOBALS['site_settings']['site_id'], array(20, 26));
$show_usd       = in_array($_GLOBALS['site_settings']['site_id'], array(20, 26));
$owarranty      = $ocondition      = $opacking      = $ovoltage      = $oshipping      = $oplug      = 0;
$defSup         = $invoice['default_supplier'];
foreach ($invoice_products as $product) {
    if ($product['product_warranty'] > 0) {
        if (0 == $owarranty) {
            $owarranty = $product['product_warranty'];
        } elseif ($owarranty != $product['product_warranty']) {
            $owarranty = 0;
        }
    }

    if ($product['product_condition'] > 0) {
        if (0 == $ocondition) {
            $ocondition = $product['product_condition'];
        } elseif ($ocondition != $product['product_condition']) {
            $ocondition = 0;
        }
    }

    if ($product['product_packing'] > 0) {
        if (0 == $opacking) {
            $opacking = $product['product_packing'];
        } elseif ($opacking != $product['product_packing']) {
            $opacking = 0;
        }
    }

    if ($product['product_voltage'] > 0) {
        if (0 == $ovoltage) {
            $ovoltage = $product['product_voltage'];
        } elseif ($ovoltage != $product['product_voltage']) {
            $ovoltage = 0;
        }
    }

    if ($product['product_terms'] > 0) {
        if (0 == $oshipping) {
            $oshipping = $product['product_terms'];
        } elseif ($oshipping != $product['product_terms']) {
            $oshipping = 0;
        }
    }

    if ($product['product_plug'] > 0) {
        if (0 == $oplug) {
            $oplug = $product['product_plug'];
        } elseif ($oplug != $product['product_plug']) {
            $oplug = 0;
        }
    }

    if (0 == $defSup && $product['supplier_id'] > 0) {
        $defSup = $product['supplier_id'];
    }

}
$supValues       = getSupplierValues($defSup);
$warranty_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('warranty_terms_id');
$condition_terms = array('-1' => 'Not Applicable') + invoiceGetOptionList('condition');
$packing_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('packing_requirement');
$voltage_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('voltage_id2');
$shipping_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('terms_id');
$plugs_list      = array('-1' => 'Not Applicable') + getInvoicePlugList();

function product_row($pcount = '[PRODUCT_COUNT]', $product = array())
{
    global $invoice, $_GLOBALS, $show_usd;

    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'AUD';
    }

    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }

    $hidden = array(
        'decimal[' . $pcount . ']'               => $product['decimal'],
        'generic[' . $pcount . ']'               => $product['generic'],
        'generic_code[' . $pcount . ']'          => $product['generic_code'],
        'prod_count[' . $pcount . ']'            => $product['prod_count'],
        'product_price_changed[' . $pcount . ']' => ('' != $product['product_price_changed']) ? $product['product_price_changed'] : 'n',
        'product_rank[' . $pcount . ']'          => $product['product_rank'],
        'product_section[' . $pcount . ']'       => $product['product_section'],
        'product_updated[' . $pcount . ']'       => $product['product_updated'],
        'product_weight[' . $pcount . ']'        => $product['product_weight'],
        'product_weight_inc[' . $pcount . ']'    => $product['product_weight_inc'],
    );

    ob_start();
?>
<tr id="product_rowa[<?= $pcount ?>]">
	<td>
		<?= formatHTMLInputs($hidden) ?>
		<input name="product_id[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'pid')" type="text" value="<?php hecho($product['product_id'])?>" style="text-align:right;" size="4" />
		<input name="sub_id[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'pid')" type="text" value="<?php hecho($product['sub_id'])?>" style="text-align:right;" size="4" />
	</td>
	<td>
<?php
if ('y' != $product['generic']) {
?>
		<input onclick="searchProduct(<?= $pcount ?>, getValue('product_id[<?= $pcount ?>]'))" type="button" value="S" />
<?php
}
?>
	</td>
	<td><input name="product_name[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'pname')" type="text" value="<?php hecho($product['product_name'])?>" style="width:100%;" /></td>
	<td>
<?php
if ('y' != $product['generic']) {
?>
		<input onclick="searchProduct(<?= $pcount ?>, getValue('product_name[<?= $pcount ?>]'))"	type="button" value="S" />
<?php
}
?>
	</td>
	<td><input name="product_qty[<?= $pcount ?>]" onchange="updateProductPrice(<?= $pcount ?>)" type="text" value="<?php hecho($product['product_qty'])?>" style="text-align:right;" size="3" onfocus="this.select()" /></td>
	<td><input name="product_cost[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'pcost')" type="text" value="<?php hecho(convertCurrency($product['product_cost'], $inv, '', false, $product['decimal']))?>" style="text-align:right;" size="6" onfocus="this.select()" /></td>
	<td><input name="product_online[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'ponline')" type="text" value="<?php hecho(convertCurrency($product['product_online'], $inv, '', false, $product['decimal']))?>" style="text-align:right;" size="6" onfocus="this.select()" /></td>
	<td><input name="product_offer[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'poffer')" type="text" value="<?php hecho(convertCurrency($product['product_offer'], $inv, '', false, $product['decimal']))?>" style="text-align:right;" size="6" onfocus="this.select()" /></td>
	<td><input name="product_offerau[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>, 'pofferau')" type="text" value="<?php hecho(convertCurrency($product['product_offerau'], 'AUD', '', false, $product['decimal']))?>" style="text-align:right;<?= ($show_usd) ? 'display:none;' : '' ?>" size="6" readonly="readonly" /><input name="product_offerus[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>,'pofferus')" type="text" value="<?php hecho(convertCurrency($product['product_offerus'], 'USD', '', false, $product['decimal']))?>" style="text-align:right;<?= ($show_usd) ? '' : 'display:none;' ?>" size="6"	readonly="readonly" /></td>
	<td><input onclick="deleteProduct(<?= $pcount ?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_rowb[<?= $pcount ?>]">
	<td><a href="javascript:void(rankProduct(<?= $pcount ?>,-1))"><img src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankProduct(<?= $pcount ?>,1))"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
	<td colspan="9"><textarea name="product_notes[<?= $pcount ?>]" rows="<?= max(3, count(explode("\n", trim($product['product_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3)" onchange="setValue('product_updated[<?= $pcount ?>]','y')" style="width:100%;"><?php hecho(trim($product['product_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
    $output = array();
    $string = '';
    foreach (explode("\n", ob_get_clean()) as $value) {
        $value = trim($value);
        if (in_array(substr($value, 0, 3), array('<td', '<tr')) || '</tr>' == $value) {
            $output[] = $string;
            $string   = '';
        }
        $string .= $value;
    }
    return implode("\n", $output) . $string;
}
function charge_row($ccount = '[CHARGE_COUNT]', $charge = array(), $site_id = 0)
{
    global $DB, $_GLOBALS, $show_usd;
    $hidden = array('charge_updated[' . $ccount . ']' => $charge['charge_updated'], 'charge_rank[' . $ccount . ']' => $charge['charge_rank'], 'charge_id[' . $ccount . ']' => $charge['charge_id']);
    ob_start();
?>
<tr id="product_charge_rowa[<?= $ccount ?>]">
	<td><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?><input type="text" name="charge_product_id[<?= $ccount ?>]" style="text-align:right;" value="<?= sprintf('C%02d', $charge['charge_id']) ?>" readonly="readonly" size="4" /></td>
	<td></td>
	<td><input name="charge_name[<?= $ccount ?>]" type="text" value="<?php hecho($charge['charge_name'])?>" style="width:100%;" /></td>
	<td></td>
	<td><input name="charge_qty[<?= $ccount ?>]" readonly="readonly" onchange="chargeChange(<?= $ccount ?>,'pqty')" size="3" style="text-align:right;" type="text" value="<?= ($charge['charge_qty'] > 0) ? $charge['charge_qty'] : 1 ?>" onfocus="this.select()"/></td>
	<td><input name="charge_cost[<?= $ccount ?>]" onchange="chargeChange(<?= $ccount ?>,'pcost')" size="6" style="text-align:right;" type="text" value="<?= $charge['charge_cost'] ?>" onfocus="this.select()"/></td>
	<td><input name="charge_online[<?= $ccount ?>]" onchange="chargeChange(<?= $ccount ?>,'ponline')" size="6" style="text-align:right;" type="text" value="<?= $charge['charge_online'] ?>" onfocus="this.select()"/></td>
	<td><input name="charge_offer[<?= $ccount ?>]" onchange="chargeChange(<?= $ccount ?>,'poffer')" size="6" style="text-align:right;" type="text" value="<?= $charge['charge_offer'] ?>" onfocus="this.select()"/></td>
	<td><input name="charge_offerau[<?= $ccount ?>]" onchange="chargeChange(<?= $ccount ?>,'pofferau')" readonly="readonly" size="6" style="text-align:right;<?= ($show_usd) ? 'display:none;' : '' ?>" type="text" value="<?= $charge['charge_offerau'] ?>" /><input name="charge_offerus[<?= $ccount ?>]" onchange="chargeChange(<?= $ccount ?>,'pofferus')" readonly="readonly" size="6" style="text-align:right;<?= ($show_usd) ? '' : 'display:none;' ?>" type="text" value="<?= $charge['charge_offerus'] ?>" /></td>
	<td><input type="hidden" name="charge_count[<?= $ccount ?>]" value="<?= $charge['charge_count'] ?>" /><input onclick="deleteCharge(<?= $ccount ?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_charge_rowb[<?= $ccount ?>]">
	<td><a href="javascript:void(rankCharge(<?= $ccount ?>,-1))"><img src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankCharge(<?= $ccount ?>,1))"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
	<td colspan="9"><textarea name="charge_notes[<?= $ccount ?>]" rows="<?= max(3, count(explode("\n", trim($charge['charge_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3)" onchange="setValue('charge_updated[<?= $ccount ?>]','y')" style="width:100%;"><?php hecho(trim($charge['charge_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function supplier_row($pcount = '[PRODUCT_COUNT]', $product = array())
{
    global $invoice, $warranty_terms, $condition_terms, $packing_terms, $voltage_terms, $shipping_terms, $plugs_list, $products_need_update, $site_id, $_GLOBALS, $show_usd;
    $invoice_copied = (is_array($products_need_update)) ? true : false;
    $bgcolor        = '';
    if ($product['supplier_count'] < 1) {
        $bgcolor = '#FFAA99';
    } elseif ($product['supplier_count'] > 1) {
        $bgcolor = '#6688BB';
    } elseif ($product['product_weight'] <= 1 && 'y' != $product['generic']) {
        $bgcolor = '#8B00FF';
    }

    $sup_name = $product['supplier_name'];
    $prodDB   = (9 == $invoice['site_id'] && $product['product_id'] > 0) ? productFromDatabase($product['product_id']) : array();
    $sup_id   = (0 != $product['supplier_id']) ? $product['supplier_id'] : $prodDB['supplier'];
    if ('' != $product['supplier']) {
        $sup_id = $product['supplier'];
    }

    if ('' == $sup_name) {
        if (9 == $invoice['site_id'] || 16 == $invoice['site_id']) {
            $sup_name = getSupplierName($sup_id);
        }
    }

    $supCountry = ('' != $product['supplier_country']) ? $product['supplier_country'] : 'AU';
    $yesno      = array('n' => 'No', 'y' => 'Yes');
    $stock      = $yesno + array('s' => 'Stock');
    $disNotes   = $product['supplier_notes'];
    if ($product['product_id'] > 0 && 'y' != $product['generic'] && 9 == $invoice['site_id']) {
        if (9 == $invoice['site_id']) {
            $notes        = '';
            $inStock      = $prodDB['qty'] - $prodDB['reserved'] - $product['product_qty'];
            $availability = getAvailabilityName($prodDB['availability']);
            if (count($product) > 0) {
                if (('y' == $product['supplier_found'] || 's' == $product['supplier_found']) && $inStock > 0) {
                    $notes = 'Stock: Ready To Send (' . $inStock . ' remaining)';
                } else {
                    if (1 == $prodDB['availability']) {
                        $availability = '2-4 Weeks';
                    }

                    $notes = 'Out of Stock: ' . $availability;
                }
            }
            $disNotes = ('' != $notes) ? $notes : trim('' != $product['supplier_notes']);
            if ($inStock <= 0) {
                $product['supplier_found'] = 'n';
            }

        }
        $weight_message = "\nProduct weight is not defined";
        if (strpos($disNotes, $weight_message) === false && $product['product_weight'] <= 0) {
            $disNotes .= $weight_message;
        }

        if (strpos($disNotes, $weight_message) !== false && $product['product_weight'] > 0) {
            $disNotes = str_replace($weight_message, '', $disNotes);
        }

    }
    $update_button = (9 == $invoice['site_id'] && 'y' != $product['generic']);
    $hidden        = array('supplier_id[' . $pcount . ']' => $sup_id, 'supplier_product_cost[' . $pcount . ']' => $product['product_cost']);
    ob_start();
?>
<tr id="supplier_rowa[<?= $pcount ?>]">
	<th><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>PID</th>
	<th>Product Name</th>
	<th>Supplier</th>
	<th>&nbsp;</th>
	<th>Found</th>
	<th>Qty</th>
	<th>Cost <span name="span_supplier_currency[<?= $pcount ?>]"><?php hecho($product['supplier_currency'])?></span></th>
	<th>Cost <?= ($show_usd) ? 'USD' : 'AUD' ?></th>
</tr>
<tr id="supplier_rowb[<?= $pcount ?>]"<?= ('' != $bgcolor) ? (' style="background-color:' . $bgcolor . ';"') : '' ?>>
	<td><input disabled="disabled" name="supplier_product_id[<?= $pcount ?>]" size="4" style="text-align:right;" type="text" value="<?= $product['product_id'] ?>" onchange="productChange(<?= $pcount ?>,'sid');" /><input disabled="disabled" name="supplier_sub_id[<?= $pcount ?>]" size="4" style="text-align:right;" type="text" value="<?= $product['sub_id'] ?>" onchange="productChange(<?= $pcount ?>,'sid');" /></td>
	<td><input name="supplier_product_name[<?= $pcount ?>]" disabled="disabled" size="12" style="width:100%;overflow:hidden;" type="text" value="<?php hecho($product['product_name'])?>" /></td>
	<td><input name="supplier_name[<?= $pcount ?>]" size="12" style="width:100%;" type="text" value="<?php hecho($sup_name)?>" /></td>
	<td><input onclick="searchSupplier(<?= $pcount ?>,getValue('supplier_name[<?= $pcount ?>]'));" type="button" value="S" /></td>
	<td><select name="supplier_found[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>,'sfound');"><?= str_replace("\n", "", formatHTMLOptions($stock, $product['supplier_found'])) ?></select></td>
	<td><input name="supplier_product_qty[<?= $pcount ?>]" disabled="disabled" size="3" style="text-align:right;" type="text" value="<?= $product['product_qty'] ?>" onfocus="this.select();"/></td>
	<td><input name="supplier_cost[<?= $pcount ?>]" size="6" style="text-align:right;" type="text" value="<?= $product['supplier_cost'] ?>" onfocus="this.select();" onchange="productChange(<?= $pcount ?>,'ocost');" /></td>
	<td><input name="product_costau[<?= $pcount ?>]" readonly="readonly" size="6" style="text-align:right;<?= ($show_usd) ? 'display:none;' : '' ?>" type="text" value="<?= $product['product_costau'] ?>" /><input name="product_costus[<?= $pcount ?>]" readonly="readonly" size="6" style="text-align:right;<?= ($show_usd) ? '' : 'display:none;' ?>" type="text" value="<?= $product['product_costus'] ?>" /></td>
</tr>
<tr class="alt" id="supplier_rowc[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Warranty :</td>
	<td colspan="2"><select name="product_warranty[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>');productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($warranty_terms, $product['product_warranty'])) ?></select></td>
	<td colspan="1" class="left_title">Voltage :</td>
	<td colspan="3"><select name="product_voltage[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>'); productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($voltage_terms, $product['product_voltage'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowd[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Plug Type :</td>
	<td colspan="2"><select name="product_plug[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>'); productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($plugs_list, $product['product_plug'])) ?></select></td>
	<td colspan="1" class="left_title" style="white-space:nowrap;">Condition :</td>
	<td colspan="3"><select name="product_condition[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>'); productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($condition_terms, $product['product_condition'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowe[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Packing :</td>
	<td colspan="2"><select name="product_packing[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($packing_terms, $product['product_packing'])) ?></select></td>
	<td colspan="1" class="left_title">Terms :</td>
	<td colspan="3"><select name="product_terms[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>);"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($shipping_terms, $product['product_terms'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowf[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Currency :</td>
	<td colspan="2"><select name="supplier_currency[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>,'ocost');"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCurrencyList(), $product['supplier_currency'])) ?></select></td>
	<td colspan="1" class="left_title">Country :</td>
	<td colspan="3"><select name="supplier_country[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>);"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCountryList(), $supCountry)) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowg[<?= $pcount ?>]" style="<?= ('y' == $product['generic']) ? 'display:none' : '' ?>">
	<td colspan="8" style="text-align:center"><?php
if ($update_button) {?><input onclick="updateCostPrice(<?= $product['product_id'] ?>,<?= $invoice['site_id'] ?>,<?= $pcount ?>);" type="button" value=" Update Product Cost Price " style="background-color:#CDEFEA" /><?php
}?></td>
</tr>
<tr class="alt" id="supplier_rowh[<?= $pcount ?>]" style="<?= ('y' != $product['generic'] && count($product) && 2 == $site_id && 2 == $invoice['site_id'] && 0 == $product['sub_id']) ? '' : 'display:none;' ?>">
	<td colspan="8" style="text-align:center;"><?php
if ('y' != $product['generic'] && count($product) && 2 == $site_id && 2 == $invoice['site_id'] && 0 == $product['sub_id']) {?><input type="button" value="Make Individual Product" onclick="makeProductIndividual('<?= $pcount ?>')" /><?php
}?></td>
</tr>
<tr class="alt" id="supplier_rowi[<?= $pcount ?>]">
	<td>Notes<br /><br /><input type="button" onclick="splitProduct(<?= $pcount ?>);" value="Split" /></td>
	<td colspan="7"><textarea style="width:100%;" name="supplier_notes[<?= $pcount ?>]" rows="<?= max(3, count(explode("\n", trim($disNotes)))) ?>" onkeyup="textareaAutoSize(this,1,3);"><?php hecho(trim($disNotes))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProducts\');">Products Requested</a>') ?>
<div id="sectionProducts">

<script type="text/javascript">
//<![CDATA[
var product_count = <?= max(100, count($invoice_products)) ?>;
var charge_count = <?= max(100, count($invoice_charges)) ?>;
var charge_names = <?php jecho(invoiceGetOptionList('charge_id'))?>;

function setChargeData(data, return_message)
{
	if (return_message == 'add')
	{
		var target_id = String(charge_count);
		var site_id = getValue('site_id');
		var mappings = {
			1:{1:13, 2:14, 9:7, 16:16, 11:15},
			2:{11:20},
			3:{1:9, 2:10, 9:2, 16:12, 11:11},
			4:{11:8},
			5:{11:1},
			6:{11:5},
			7:{11:21},
			10:{11:data.charge_id}
		};
		var charge_id=0;
		if(mappings[data.class_id]!=null)
		{
			if(mappings[data.class_id][site_id]!=null)
				charge_id=mappings[data.class_id][site_id];
			else
				charge_id=mappings[data.class_id][11];
		}
		var add_charge=true;
		if(charge_id==1 && gstAdded) add_charge=false;
		if (charge_id == 1 && site_id == 20) add_charge = false;
		if(add_charge)
		{
			addCharge();
			if(charge_id==1) gstAdded=true;
			setValue('charge_id['+target_id+']',charge_id);
			setValue('charge_product_id['+target_id+']','C'+charge_id);
			setValue('charge_name['+target_id+']',charge_names[charge_id]);
			chargeChange(charge_count);
		}
	}
}

function addCharge()
{
	//global charge_count
	var currency = getValue('invoice_currency');
	if (currency == null || currency == '')
	{
		alert('You are unable to add charges until a currency is selected');
		var currencyElem = getElement('invoice_currency');
		if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
		return false;
	}
	var ccount = String(charge_count);

	$("#charges_table").append(<?= str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(charge_row('[CHARGE_COUNT]', array('charge_rank' => '[CHARGE_COUNT]'), $invoice['site_id']), 1)) ?>);

	charge_count++;
	//return charge_count - 1;
	return true;
}

function searchVoucher()
{
	var member_id=getValue('member_id');
	url='/webadmin/gift_voucher/?return_message=invoice&member_id='+escape(member_id);
	if(member_id > 0) openWindow(url,'700','600',true,'scrollbars=1');
	else
	{
		alert('Please select a Member before adding a Gift Voucher');
		var elem=getElement('bill_name');
		if(elem.select) elem.select();
		if(elem.focus) elem.focus();
	}
}

function setVoucherData(data)
{
	if(data!=null && data['return_message']=='invoice')
	{
		var target_id=String(charge_count);
		var site_id=getValue('site_id');
		addCharge();
		var charge_id=22;
		setValue('charge_id['+target_id+']',charge_id);
		setValue('charge_product_id['+target_id+']','C'+charge_id);
		setValue('charge_name['+target_id+']','Gift Voucher ('+data['formatted_id']+')');
		setValue('charge_cost['+target_id+']','0.00');
		//setValue('charge_costau['+target_id+']','0.00');
		// charge offer
		var amount=convertCurrency(0-data['amount'],data['currency'],getValue('invoice_currency'));
		var amountau=convertCurrency(0-data['amount'],data['currency'],'AUD');
		var amountus=convertCurrency(0-data['amount'],data['currency'],'USD');
		setValue('charge_offer['+target_id+']',amount);
		setValue('charge_online['+target_id+']',amount);
		setValue('charge_offerau['+target_id+']',amountau);
		setValue('charge_offerus['+target_id+']',amountus);

		// now have to update the voucher_user field....
		setValue('voucher_used', data['id']);

		productUpdateTotals();
	}
}

function addProduct()
{
	var currency = getValue('invoice_currency');
	if (currency == null || currency == '')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem = getElement('invoice_currency');
		if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
		return false;
	}
	var pcount = String(product_count);

	$("#product_table").append(<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'generic' => 'n')), 1)) ?>);
	$("#supplier_table").append(<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1)) ?>);

	product_count++;
	return product_count-1;
}

function addGeneric()
{
	//global product_count;
	var currency = getValue('invoice_currency');
	if (currency == null || currency == '')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem = getElement('invoice_currency');
		if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
		return false;
	}
	var pcount = String(product_count);

	$("#product_table").append(<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y')), 1)) ?>);
	$("#supplier_table").append(<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1)) ?>);

	product_count++;
	return product_count-1;
}

function addBulk()
{
	var result=addGeneric();
	if(result!=false) setValue('generic['+result+']','b');
	return result;
}

function deleteCharge(row)
{
	var charge_id=getValue('charge_id['+row+']');
	if(charge_id==1) gstAdded=false;
	var theRow=getElement("product_charge_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("product_charge_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	chargeChange();
}

function deleteFreight()
{
	setValue('ship_id','');
	setValue('ship_product_id','');
	setValue('ship_product_name','');
	setValue('ship_courier_id','');
	setValue('ship_notes','');
	setValue('supplier_ship_notes','');
	setValue('ship_cost','');
	setValue('ship_offer','');
	setValue('ship_online','');
	setValue('ship_costau','');
	setValue('ship_costus','');
	setValue('ship_offerau','');
	setValue('ship_offerus','');
	var theRow=getElement("product_ship_rowa");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("product_ship_rowb");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("supplier_ship_rowa");
	if(theRow) theRow.style.display='none';
	shipChange();
}

function deleteProduct(row)
{
	var theRow=getElement("product_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("product_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowc["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowd["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowe["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowf["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowg["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowh["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowi["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);

	productChange();
}

function chargeChange(row,change_type)
{
	var do_totals=false;

	do_totals=true;

	switch(change_type)
	{
	case 'pofferus':
		setValue("charge_offerau["+row+"]", convertCurrency(getValue("charge_offerus["+row+"]"), "USD", "AUD"));
		setValue("charge_offer["+row+"]", convertCurrency(getValue("charge_offerus["+row+"]"), "USD", getValue("invoice_currency")));
		break;
	case 'pofferau':
		setValue("charge_offerus["+row+"]", convertCurrency(getValue("charge_offerau["+row+"]"), "AUD", "USD"));
		setValue("charge_offer["+row+"]", convertCurrency(getValue("charge_offerau["+row+"]"), "AUD", getValue("invoice_currency")));
		break;
	}

	if(do_totals) productUpdateTotals();
}

function productChange(row,change_type)
{
	var do_totals=false;
	var do_located=false;

	switch(change_type)
	{
	case 'pid':
		setValue('supplier_product_id['+row+']',getValue('product_id['+row+']'));
		setValue('supplier_sub_id['+row+']',getValue('sub_id['+row+']'));
		break;
	case 'pname':
		if(getValue("product_id["+row+"]")<1 || getValue("supplier_id["+row+"]")<1)
			setValue("supplier_product_name["+row+"]",getValue("product_name["+row+"]"));
		break;
	case 'pqty':
		setValue("supplier_product_qty["+row+"]",getValue("product_qty["+row+"]"));
		productUpdateWeight();
		do_totals=true;
		break;
	case 'pcost':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var product_cost=getValue("product_cost["+row+"]");
		setValue("supplier_cost["+row+"]",convertCurrency(product_cost,invoice_currency,supplier_currency));
		setValue("supplier_product_cost["+row+"]",product_cost);
		setValue("product_costau["+row+"]",convertCurrency(product_cost,invoice_currency,"AUD"));
		setValue("product_costus["+row+"]",convertCurrency(product_cost,invoice_currency,"USD"));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("product_offerau["+row+"]", convertCurrency(getValue("product_offer["+row+"]"), getValue("invoice_currency"), "AUD"));
		setValue("product_offerus["+row+"]", convertCurrency(getValue("product_offer["+row+"]"), getValue("invoice_currency"), "USD"));
		setValue('product_price_changed['+row+']','y');
		do_totals=true;
		break;
	case 'pofferus':
		setValue("product_offerau["+row+"]", convertCurrency(getValue("product_offerus["+row+"]"), "USD", "AUD"));
		setValue("product_offer["+row+"]", convertCurrency(getValue("product_offerus["+row+"]"), "USD", getValue("invoice_currency")));
		do_totals=true;
		break;
	case 'pofferau':
		setValue("product_offerus["+row+"]", convertCurrency(getValue("product_offerau["+row+"]"), "AUD", "USD"));
		setValue("product_offer["+row+"]", convertCurrency(getValue("product_offerau["+row+"]"), "AUD", getValue("invoice_currency")));
		do_totals=true;
		break;
	case 'sid':
		setValue('product_id['+row+']',getValue('supplier_product_id['+row+']'));
		setValue('sub_id['+row+']',getValue('supplier_sub_id['+row+']'));
		break;
	case 'sname':
		setValue("product_name["+row+"]",getValue("supplier_product_name["+row+"]"));
		break;
	case 'sfound':
		do_located=true;
		break;
	case 'sqty':
		setValue("product_qty["+row+"]",getValue("supplier_product_qty["+row+"]"));
		productUpdateWeight();
		do_totals=true;
		break;
	case 'scost':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var supplier_product_cost=getValue("supplier_product_cost["+row+"]");
		setValue("supplier_cost["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,supplier_currency));
		setValue("product_costau["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,"AUD"));
		setValue("product_costus["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,"USD"));
		setValue("product_cost["+row+"]",supplier_product_cost);
		do_totals=true;
		break;
	case 'ocost':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var supplier_cost=getValue("supplier_cost["+row+"]");
		setValue("supplier_product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency));
		setValue("product_costau["+row+"]", convertCurrency(supplier_cost,supplier_currency,"AUD"));
		setValue("product_costus["+row+"]", convertCurrency(supplier_cost,supplier_currency,"USD"));
		setValue("product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency));
		do_totals=true;
		break;
	case 'scostus':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var product_costus=Number(getValue("product_costus["+row+"]"));
		setValue("product_costau["+row+"]",convertCurrency(product_costus,"USD","AUD"));
		setValue("supplier_cost["+row+"]",convertCurrency(product_costus,"USD",supplier_currency));
		setValue("supplier_product_cost["+row+"]",convertCurrency(product_costus,"USD",invoice_currency));
		setValue("product_cost["+row+"]",convertCurrency(product_costus,"USD",invoice_currency));
		do_totals=true;
		break;
	case 'scostau':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var product_costau=Number(getValue("product_costau["+row+"]"));
		setValue("product_costus["+row+"]",convertCurrency(product_costau,"AUD","USD"));
		setValue("supplier_cost["+row+"]",convertCurrency(product_costau,"AUD",supplier_currency));
		setValue("supplier_product_cost["+row+"]",convertCurrency(product_costau,"AUD",invoice_currency));
		setValue("product_cost["+row+"]",convertCurrency(product_costau,"AUD",invoice_currency));
		do_totals=true;
		break;
	default:
		do_totals=true;
		break;
	}

	setExtraSupplierData();
	if(do_totals) productUpdateTotals();
	if(do_located) productUpdateLocated();
}

function getProductRows()
{
	var product_table=getElement("product_table");
	var product_rows=[];
	if(product_table!=null)
	{
		var theRows=product_table.rows;
		for(var i=0;i<theRows.length;i++)
			if(theRows[i].id.substr(0,13)=='product_rowa[')
				if(searchArray(theRows[i].id.substr(12),product_rows)==null)
					product_rows[product_rows.length]=theRows[i].id.substr(12);
	}
	return product_rows;
}

function getChargeRows()
{
	var charges_table=getElement("charges_table");
	var charge_rows=[];
	if(charges_table!=null)
	{
		var theRows=charges_table.rows;
		for(var i=0;i<theRows.length;i++)
			if(theRows[i].id.substr(0,20)=='product_charge_rowa[')
				if(searchArray(theRows[i].id.substr(19),charge_rows)==null)
					charge_rows[charge_rows.length]=theRows[i].id.substr(19);
	}
	return charge_rows;
}

function rankProductsAlphabetically()
{
	getElement('prodLink').innerHTML='Please Wait...';
	setTimeout("sortAlphabetically()",500);
}

function sortAlphabetically()
{
	// function to rank the products alphabetically....
	var product_rows=getProductRows();
	var ar=[];
	var first_prow=getElement('product_rowa'+product_rows[0]);
	var first_srow=getElement('supplier_rowa'+product_rows[0]);
	for(var i=0;i<product_rows.length;i++) ar[i]=getValue("product_name"+product_rows[i])+'^'+product_rows[i];
	ar.sort(); ar.reverse();

	// now have swap the rows around....
	for(i=0;i<ar.length;i++)
	{
		var tmp=explode('^',ar[i]);
		//print_array(tmp);
		var source_row;
		var from_row=tmp[1];
		var target_table=getElement('product_table');//target_row.parentNode;
		setValue('product_rank'+from_row,ar.length-i);
		if(source_row=getElement("product_rowa"+from_row)) target_table.insertBefore(source_row,first_prow);
		var tmpp=source_row;
		if(source_row=getElement("product_rowb"+from_row)) target_table.insertBefore(source_row,first_prow);
		first_prow=tmpp;
		var target_table=getElement('supplier_table');//target_row.parentNode;
		if(source_row=getElement("supplier_rowa"+from_row)) target_table.insertBefore(source_row,first_srow);
		var tmps=source_row;
		if(source_row=getElement("supplier_rowb"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowc"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowd"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowe"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowf"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowg"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowh"+from_row)) target_table.insertBefore(source_row,first_srow);
		if(source_row=getElement("supplier_rowi"+from_row)) target_table.insertBefore(source_row,first_srow);
		first_srow=tmps;
	}
	getElement('prodLink').innerHTML='Product Name';
}

function rankProduct(row,direction)
{

	var from_row='['+row+']';
	var from_rank=Number(getValue("product_rank"+from_row));
	var swap_row=null;
	var swap_rank=null;
	var product_rows=getProductRows();
	for(var i=0;i<product_rows.length;i++)
	{
		var check_row=product_rows[i];
		var check_rank=Number(getValue("product_rank"+check_row));
		if(check_row!=from_row && ((direction<0 && check_rank<from_rank && (swap_rank==null || check_rank>swap_rank)) || (direction>0 && check_rank>from_rank && (swap_rank==null || check_rank<swap_rank))))
		{
			swap_row=check_row;
			swap_rank=check_rank;
		}
	}
	if(swap_row!=null)
	{
		setValue("product_rank"+from_row,swap_rank);
		setValue("product_rank"+swap_row,from_rank);
		if(direction>0)
		{
			var temp=from_row;
			from_row=swap_row;
			swap_row=temp;
		}
		var source_row;
		var target_row=getElement("product_rowa"+swap_row);
		var target_table=target_row.parentNode;
		if(source_row=getElement("product_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("product_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
		var target_row=getElement("supplier_rowa"+swap_row);
		var target_table=target_row.parentNode;
		if(source_row=getElement("supplier_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowc"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowd"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowe"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowf"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowg"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowh"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("supplier_rowi"+from_row)) target_table.insertBefore(source_row,target_row);
	}
}

function rankCharge(row,direction)
{

	var from_row='['+row+']';
	var from_rank=Number(getValue("charge_rank"+from_row));
	var swap_row=null;
	var swap_rank=null;
	var charge_rows=getChargeRows();
	for(var i=0;i<charge_rows.length;i++)
	{
		var check_row=charge_rows[i];
		var check_rank=Number(getValue("charge_rank"+check_row));
		if(check_row!=from_row && ((direction<0 && check_rank<from_rank && (swap_rank==null || check_rank>swap_rank)) || (direction>0 && check_rank>from_rank && (swap_rank==null || check_rank<swap_rank))))
		{
			swap_row=check_row;
			swap_rank=check_rank;
		}
	}
	if(swap_row!=null)
	{
		setValue("charge_rank"+from_row,swap_rank);
		setValue("charge_rank"+swap_row,from_rank);
		if(direction>0)
		{
			var temp=from_row;
			from_row=swap_row;
			swap_row=temp;
		}
		var source_row;
		var target_row=getElement("product_charge_rowa"+swap_row);
		var target_table=target_row.parentNode;
		if(source_row=getElement("product_charge_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
		if(source_row=getElement("product_charge_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
	}
}

function productUpdateLocated()
{
	var all_found=true;

	var supplier_rows=[];

	var theRows=getElement("supplier_table").rows;
	for(var i=0;i<theRows.length;i++)
		if(theRows[i].id.substr(0,14)=='supplier_rowa[')
			if(searchArray(theRows[i].id.substr(13),supplier_rows)==null)
				supplier_rows[supplier_rows.length]=theRows[i].id.substr(13);

	for(var i=0;i<supplier_rows.length;i++)
		if(getValue("supplier_found"+supplier_rows[i])=='n')
			all_found=false;

	setValue('products_located',(all_found)?'y':'n');
}

function shipChange(change_type)
{
	var do_totals=false;

	switch(change_type)
	{
	case 'pname':
		setValue("supplier_ship_product_name",getValue("ship_product_name"));
		break;
	case 'pqty':
		setValue("supplier_ship_qty",getValue("ship_qty"));
		do_totals=true;
		break;
	case 'pcost':
		setValue("ship_costau",convertCurrency(getValue("ship_cost"),getValue("invoice_currency"),"AUD"));
		setValue("ship_costus",convertCurrency(getValue("ship_cost"),getValue("invoice_currency"),"USD"));
		setValue("supplier_ship_cost",getValue("ship_cost"));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("ship_offerau", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "AUD"));
		setValue("ship_offerus", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "USD"));
		do_totals=true;
		break;
	case 'pofferus':
		setValue("ship_offer", convertCurrency(getValue("ship_offerus"), "USD", getValue("invoice_currency")));
		setValue("ship_offerau", convertCurrency(getValue("ship_offerus"), "USD", "AUD"));
		do_totals=true;
		break;
	case 'pofferau':
		setValue("ship_offer", convertCurrency(getValue("ship_offerau"), "AUD", getValue("invoice_currency")));
		setValue("ship_offerus", convertCurrency(getValue("ship_offerau"), "AUD", "USD"));
		do_totals=true;
		break;
	case 'sname':
		setValue("ship_product_name",getValue("supplier_ship_product_name"));
		break;
	case 'sqty':
		setValue("ship_qty",getValue("supplier_ship_qty"));
		do_totals=true;
		break;
	case 'scost':
		setValue("ship_costau", convertCurrency(getValue("supplier_ship_cost"), getValue("invoice_currency"), "AUD"));
		setValue("ship_costus", convertCurrency(getValue("supplier_ship_cost"), getValue("invoice_currency"), "USD"));
		setValue("ship_cost",getValue("supplier_ship_cost"));
		do_totals=true;
		break;
	case 'scostus':
		setValue("supplier_ship_cost", convertCurrency(getValue("ship_costus["+row+"]"), "USD", getValue("invoice_currency")));
		setValue("ship_costau", convertCurrency(getValue("ship_costus["+row+"]"), "USD", "AUD"));
		setValue("ship_cost",getValue("supplier_ship_cost"));
		do_totals=true;
		break;
	case 'scostau':
		setValue("supplier_ship_cost", convertCurrency(getValue("ship_costau["+row+"]"), "AUD", getValue("invoice_currency")));
		setValue("ship_costus", convertCurrency(getValue("ship_costau["+row+"]"), "AUD", "USD"));
		setValue("ship_cost",getValue("supplier_ship_cost"));
		do_totals=true;
		break;
	default:
		do_totals=true;
		break;
	}

	if(do_totals) productUpdateTotals();
}

function productUpdateWeight()
{
	var total_weight=0.0;

	var product_rows=[];
	var theRows=getElement("product_table").rows;
	for(var i=0;i<theRows.length;i++)
		if(theRows[i].id.substr(0,13)=='product_rowa[')
			if(searchArray(theRows[i].id.substr(12),product_rows)==null)
				product_rows[product_rows.length]=theRows[i].id.substr(12);

	for(var i=0;i<product_rows.length;i++)
	{
		var rowNum=product_rows[i];
		var addMe=0;
		var product_qty=Number(getValue("product_qty"+rowNum));
		var product_weight=Number(getValue("product_weight"+rowNum));
		var product_weight_inc=Number(getValue("product_weight_inc"+rowNum));
		if(product_qty>0)
		{
			addMe=product_weight;
			if(!isNaN(addMe)) total_weight+=addMe;
			if(product_qty>1)
			{
				addMe=(product_qty-1)*product_weight_inc;
				if(!isNaN(addMe)) total_weight+=addMe;
			}
		}
	}

	setAllValues('ship_weight',Number(total_weight).toFixed(2));
}

function productUpdateTotals()
{
	var total_cost=0.0;
	var total_costau=0.0;
	var total_costus=0.0;
	var total_offer=0.0;
	var total_offerau=0.0;
	var total_offerus=0.0;
	var total_online=0.0;

	var product_rows=[];
	var theRows=getElement("product_table").rows;
	for(var i=0;i<theRows.length;i++) if(theRows[i].id.substr(0,13)=='product_rowa[' && searchArray(theRows[i].id.substr(12),product_rows)==null) product_rows.push(theRows[i].id.substr(12));

	var charge_rows=[];
	var theRows1=getElement("charges_table").rows;
	for(var i=0;i<theRows1.length;i++) if(theRows1[i].id.substr(0,20)=='product_charge_rowa[' && searchArray(theRows1[i].id.substr(19),charge_rows)==null) charge_rows.push(theRows1[i].id.substr(19));

	var in_cur=getValue('invoice_currency');

	for(var i=0;i<product_rows.length;i++)
	{
		var rowNum=product_rows[i];
		var addMe=0;
		var product_qty=Number(getValue("product_qty"+rowNum));
		var product_cost=Number(getValue("product_cost"+rowNum));
		var product_costau=Number(getValue("product_costau"+rowNum));
		var product_costus=Number(getValue("product_costus"+rowNum));
		var product_offer=Number(getValue("product_offer"+rowNum));
		var product_offerau=Number(getValue("product_offerau"+rowNum));
		var product_offerus=Number(getValue("product_offerus"+rowNum));
		var product_online=Number(getValue("product_online"+rowNum));
		addMe=product_qty*product_cost;
		if(!isNaN(addMe)) total_cost+=addMe;
		addMe=product_qty*product_costau;
		if(!isNaN(addMe)) total_costau+=addMe;
		addMe=product_qty*product_costus;
		if(!isNaN(addMe)) total_costus+=addMe;
		addMe=product_qty*product_offer;
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=product_qty*product_offerau;
		if(!isNaN(addMe)) total_offerau+=addMe;
		addMe=product_qty*product_offerus;
		if(!isNaN(addMe)) total_offerus+=addMe;
		addMe=product_qty*product_online;
		if(!isNaN(addMe)) total_online+=addMe;
	}

	var products_cost=total_cost;
	var products_costau=total_costau;
	var products_costus=total_costus;
	var products_online=total_online;
	var products_offer=total_offer;
	var products_offerau=total_offerau;
	var products_offerus=total_offerus;
	setAllValues('products_cost',convertCurrency(products_cost.toFixed(2),in_cur));
	setAllValues('products_costau',convertCurrency(products_costau.toFixed(2),'AUD'));
	setAllValues('products_costus',convertCurrency(products_costus.toFixed(2),'USD'));
	setAllValues('products_offer',convertCurrency(products_offer.toFixed(2),in_cur));
	setAllValues('products_online',convertCurrency(products_online.toFixed(2),in_cur));
	setAllValues('products_offerau',convertCurrency(products_offerau.toFixed(2),'AUD'));
	setAllValues('products_offerus',convertCurrency(products_offerus.toFixed(2),'USD'));

<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$charges_priority = array();
$query            = $DB->query("SELECT * FROM `highwaygroup`.`charges` ORDER BY `charge_priority` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_amount[$row['charge_id']]   = $row['charge_amount'];
    $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
    $charges_percent[$row['charge_id']]  = $row['charge_percent'];
    $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
    $charges_priority[$row['charge_id']] = intval($row['charge_priority']);
}
?>
	var charges_ids=<?php jecho(array_keys($charges_amount))?>;
	var charges_amount=<?php jecho(array_values($charges_amount))?>;
	var charges_currency=<?php jecho(array_values($charges_currency))?>;
	var charges_percent=<?php jecho(array_values($charges_percent))?>;
	var charges_rounding=<?php jecho(array_values($charges_rounding))?>;
	var charges_priority=<?php jecho(array_values($charges_priority))?>;

	for(var j = 0;j<charges_ids.length;j++) if (charges_priority[j] <= 7)
	{
		for(var i = 0;i<charge_rows.length;i++)
		{
			var rowNum = charge_rows[i];
			var charge_id = Number(getValue('charge_id' + rowNum));
			if (charge_id == charges_ids[j])
			{
				var addMe = 0;
				var amount = charges_amount[j];
				var currency = charges_currency[j];
				var percent = Number(charges_percent[j]) / 100.0;
				var rounding = Number(charges_rounding[j]);

				var charge_amount = charge_cost = '0.00';
				var charge_amount_au = '0.00';
				var charge_amount_us = '0.00';

				if (rounding == 0.0 && percent == 0.0 && amount == '0.00')
				{
<?php
if (moduleLoad("insurance") || function_exists("insuranceFromDatabase")) {
    $insurance = insuranceFromDatabase(1);
?>
					if (charge_id == 21)
					{
						var result_aud = "0.00";
						var cover_amount = products_offerau;
						//if (in_cur == "AUD") cover_amount = bcmul(cover_amount, "1.1", 2);
						if (getValue("supply_country") == "AU" && getValue("bill_country") == "AU" && getValue("shipto_country[0]") == "AU") cover_amount = bcmul(cover_amount, "1.1", 2);

<?php
switch ($insurance["pricing_type"]) {
        case 'brackets':
            foreach (insuranceRatesFromDatabase(1) as $rate) {
?>
						if (bccomp(cover_amount, <?php jecho($rate["rate_start"])?>, 2) > 0) result_aud = bcadd(result_aud, bcmul(bcsub((bccomp(cover_amount, <?php jecho($rate["rate_finish"])?>, 2) < 0) ? cover_amount : <?php jecho($rate["rate_finish"])?>, <?php jecho($rate["rate_start"])?>, 2), <?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>, 2), 2);
<?php
}
            break;
        default:
        case 'fixed':
?>
						result_aud = bcmul(cover_amount, <?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>, 2);
<?php
break;
    }
?>
						setValue('charge_cost' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
						setValue('charge_offer' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
					}
<?php
}
?>

					charge_cost=getValue('charge_cost'+rowNum);
					charge_amount=getValue('charge_offer'+rowNum);
					charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD');
					charge_amount_us=convertCurrency(charge_amount,in_cur,'USD');
					setValue('charge_online'+rowNum,charge_amount);
					setValue('charge_offerau'+rowNum,charge_amount_au);
					setValue('charge_offerus'+rowNum,charge_amount_us);
				}
				else
				{
					charge_amount=Number(convertCurrency(amount,currency,in_cur));

					var temp = total_offer;
					for(var k = 0;k < product_rows.length;k++) if (getValue("generic" + product_rows[k]) == "y" && getValue("generic_code" + product_rows[k]) == "GFR") temp -= Number(getValue("product_qty" + product_rows[k])) * Number(getValue("product_offer" + product_rows[k]));

					charge_amount += Number(temp)*percent;

					if(rounding != 0.0) charge_amount = (Math.ceil((temp + charge_amount) / rounding) * rounding) - temp;

					charge_cost=charge_amount=convertCurrency(Number(charge_amount).toFixed(2),in_cur);
					charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD');
					charge_amount_us=convertCurrency(charge_amount,in_cur,'USD');

					if (charge_id == 18 || charge_id == 19 || charge_id == 34 || charge_id == 35 || charge_id == 36 || charge_id == 17)
					{
						charge_cost = "0.00";
					}

					setValue('charge_cost'+rowNum,convertCurrency(charge_cost,in_cur));
					setValue('charge_offer'+rowNum,convertCurrency(charge_amount,in_cur));
					setValue('charge_offerau'+rowNum,convertCurrency(charge_amount_au,'AUD'));
					setValue('charge_offerus'+rowNum,convertCurrency(charge_amount_us,'USD'));
					setValue('charge_online'+rowNum,convertCurrency(charge_amount,in_cur));
				}

				total_cost+=Number(charge_cost);
				total_costau+=Number(convertCurrency(charge_cost,in_cur,'AUD'));
				total_costus+=Number(convertCurrency(charge_cost,in_cur,'USD'));
				total_offer+=Number(charge_amount);
				total_offerau+=Number(charge_amount_au);
				total_offerus+=Number(charge_amount_us);
				total_online+=Number(charge_amount);
			}
		}
	}

	var charges_offer=total_offer-products_offer;
	var charges_offerau=total_offerau-products_offerau;
	var charges_offerus=total_offerus-products_offerus;
	var charges_cost=total_cost-products_cost;
	var charges_costau=total_costau-products_costau;
	var charges_costus=total_costus-products_costus;

	if(getValue('ship_id')>0)
	{
		addMe=Number(getValue('ship_cost'));
		if(!isNaN(addMe)) total_cost+=addMe;
		addMe=Number(getValue('ship_costau'));
		if(!isNaN(addMe)) total_costau+=addMe;
		addMe=Number(getValue('ship_costus'));
		if(!isNaN(addMe)) total_costus+=addMe;
		addMe=Number(getValue('ship_offer'));
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=Number(getValue('ship_offerau'));
		if(!isNaN(addMe)) total_offerau+=addMe;
		addMe=Number(getValue('ship_offerus'));
		if(!isNaN(addMe)) total_offerus+=addMe;
		addMe=Number(getValue('ship_online'));
		if(!isNaN(addMe)) total_online+=addMe;
	}

	var ship_cost=total_cost-(products_cost+charges_cost);
	var ship_costau=total_costau-(products_costau+charges_costau);
	var ship_costus=total_costus-(products_costus+charges_costus);
	var ship_offer=total_offer-(products_offer+charges_offer);
	var ship_offerau=total_offerau-(products_offerau+charges_offerau);
	var ship_offerus=total_offerus-(products_offerus+charges_offerus);

	//Hack
	total_offerau=Number(total_offerau);
	total_offerus=Number(total_offerus);

	for(var j=0;j<charges_ids.length;j++) if (charges_priority[j] > 7)
	{
		for(var i=0;i<charge_rows.length;i++)
		{
			var rowNum=charge_rows[i];
			var charge_id=Number(getValue('charge_id'+rowNum));
			if(charge_id==charges_ids[j])
			{
				var addMe=0;
				var amount=charges_amount[j];
				var currency=charges_currency[j];
				var percent=Number(charges_percent[j])/100.0;
				var rounding=Number(charges_rounding[j]);

				var charge_amount=charge_cost='0.00';
				var charge_amount_au='0.00';
				var charge_amount_us='0.00';

				if(rounding==0.0 && percent==0.0 && amount=='0.00')
				{
<?php
if (moduleLoad("insurance") || function_exists("insuranceFromDatabase")) {
    $insurance = insuranceFromDatabase(1);
?>
					if (charge_id == 21)
					{
						var result_aud = "0.00";
						var cover_amount = products_offerau;
						//if (in_cur == "AUD") cover_amount = bcmul(cover_amount, "1.1", 2);
						if (getValue("supply_country") == "AU" && getValue("bill_country") == "AU" && getValue("shipto_country[0]") == "AU") cover_amount = bcmul(cover_amount, "1.1", 2);

<?php
switch ($insurance["pricing_type"]) {
        case 'brackets':
            foreach (insuranceRatesFromDatabase(1) as $rate) {
?>
						if (bccomp(cover_amount, <?php jecho($rate["rate_start"])?>, 2) > 0) result_aud = bcadd(result_aud, bcmul(bcsub((bccomp(cover_amount, <?php jecho($rate["rate_finish"])?>, 2) < 0) ? cover_amount : <?php jecho($rate["rate_finish"])?>, <?php jecho($rate["rate_start"])?>, 2), <?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>, 2), 2);
<?php
}
            break;
        default:
        case 'fixed':
?>
						result_aud = bcmul(cover_amount, <?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>, 2);
<?php
break;
    }
?>
						setValue('charge_cost' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
						setValue('charge_offer' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
					}
<?php
}
?>

					charge_cost=getValue('charge_cost'+rowNum);
					charge_amount=getValue('charge_offer'+rowNum);
					charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD');
					charge_amount_us=convertCurrency(charge_amount,in_cur,'USD');
					setValue('charge_online'+rowNum,charge_amount);
					setValue('charge_offerau'+rowNum,charge_amount_au);
					setValue('charge_offerus'+rowNum,charge_amount_us);
				}
				else
				{
					charge_amount=Number(convertCurrency(amount,currency,in_cur));
					charge_amount+=Number(total_offer)*percent;

					if(rounding!=0.0) charge_amount=(Math.ceil((total_offer+charge_amount)/rounding)*rounding)-total_offer;

					charge_cost=charge_amount=convertCurrency(Number(charge_amount).toFixed(2),in_cur);
					charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD');
					charge_amount_us=convertCurrency(charge_amount,in_cur,'USD');

					setValue('charge_cost'+rowNum,convertCurrency(charge_cost,in_cur));
					setValue('charge_offer'+rowNum,convertCurrency(charge_amount,in_cur));
					setValue('charge_offerau'+rowNum,convertCurrency(charge_amount_au,'AUD'));
					setValue('charge_offerus'+rowNum,convertCurrency(charge_amount_us,'USD'));
					setValue('charge_online'+rowNum,convertCurrency(charge_amount,in_cur));
				}

				total_cost+=Number(charge_cost);
				total_costau+=Number(convertCurrency(charge_cost,in_cur,'AUD'));
				total_costus+=Number(convertCurrency(charge_cost,in_cur,'USD'));
				total_offer+=Number(charge_amount);
				total_offerau+=Number(charge_amount_au);
				total_offerus+=Number(charge_amount_us);
				total_online+=Number(charge_amount);
			}
		}
	}

	var charges_offer=total_offer-(products_offer+ship_offer);
	var charges_offerau=total_offerau-(products_offerau+ship_offerau);
	var charges_offerus=total_offerus-(products_offerus+ship_offerus);
	var charges_cost=total_cost-(products_cost+ship_cost);
	var charges_costau=total_costau-(products_costau+ship_costau);
	var charges_costus=total_costus-(products_costus+ship_costus);
	setAllValues('charges_offer',convertCurrency(Number(charges_offer).toFixed(2),in_cur));
	setAllValues('charges_offerau',convertCurrency(Number(charges_offerau).toFixed(2),'AUD'));
	setAllValues('charges_offerus',convertCurrency(Number(charges_offerus).toFixed(2),'USD'));
	setAllValues('charges_cost',convertCurrency(Number(charges_cost).toFixed(2),in_cur));
	setAllValues('charges_costau',convertCurrency(Number(charges_costau).toFixed(2),'AUD'));
	setAllValues('charges_costus',convertCurrency(Number(charges_costus).toFixed(2),'USD'));

	setAllValues('invoice_cost',convertCurrency(Number(total_cost).toFixed(2),in_cur));
	setAllValues('invoice_online',convertCurrency(Number(total_online).toFixed(2),in_cur));
	setAllValues('invoice_offer',convertCurrency(Number(total_offer).toFixed(2),in_cur));
	setAllValues('invoice_offerau',convertCurrency(Number(total_offerau).toFixed(2),'AUD'));
	setAllValues('invoice_offerus',convertCurrency(Number(total_offerus).toFixed(2),'USD'));
	setAllValues('invoice_costau',convertCurrency(Number(total_costau).toFixed(2),'AUD'));
	setAllValues('invoice_costus',convertCurrency(Number(total_costus).toFixed(2),'USD'));

	setAllValues('invoice_profit',convertCurrency(Number(total_offer-total_cost).toFixed(2),in_cur));
	setAllValues('invoice_profitau',convertCurrency(Number(total_offerau-total_costau).toFixed(2),'AUD'));
	setAllValues('invoice_profitus',convertCurrency(Number(total_offerus-total_costus).toFixed(2),'USD'));
}

function productShowOutlet()
{
	var plug_id=Number(getValue('plug_id'));
	if(plug_id==null || plug_id<1) return;
	var url='/webadmin/world/plugs/show_image.php?id='+plug_id;
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'powerplug_popup',features);
}

function searchProduct(row,searchFor)
{
	var currency=getValue('invoice_currency');
	var site_id=getValue('site_id');
	var member_id=getValue('member_id');
	if(site_id=='1' || site_id=='2') site_id='1|2';
	//var url='/webadmin/products/products_search.php?action=do_search';
	//url+='&query='+searchFor;
	var url='/webadmin/products/?action=list&show_sites=n';
	url+='&return_message=select_'+row;
	url+='&search_for='+escape(searchFor);
	if(currency!=null) url+='&currency='+currency;
	if(site_id!=null) url+='&site_id='+site_id;
	if(member_id!=null) url+='&member_id='+member_id;
	var features='scrollbars, resizable';//height=700, width=800
	window.open(url,'product_popup',features);
}

function searchSupplier(row,searchFor)
{
	var generic=getValue('generic['+row+']');
	var generic_code=getValue('generic_code['+row+']');
	var url='/webadmin/suppliers/index.php?action=list';
	//if(generic=='y' && generic_code=='GFR') url='/webadmin/freight_forwarders/?action=list';
	if(generic=='y' && generic_code=='GFR') url='/webadmin/forwarders/?action=list';
	url+='&return_message=select_'+row;
	if(searchFor!='None') url+='&search_for='+escape(searchFor);
	var features='scrollbars, resizable';//height=700, width=800
	window.open(url,'Search',features);
}

function selectSupplier(row,supplier_id)
{
	var url='/webadmin/suppliers/index.php?action=select';
	url+='&return_message=select_'+row;
	url+='&supplier_id='+supplier_id;
	var features='scrollbars, resizable';
	window.open(url,'Search',features);
}

function searchDefaultSupplier(searchFor)
{
	var url='/webadmin/suppliers/index.php?action=list';
	url+='&return_message=default';
	if(searchFor!='None') url+='&search_for='+escape(searchFor);
	var features='scrollbars, resizable';//height=700, width=800
	window.open(url,'Search',features);
}

function splitProduct(row)
{
	var rowNum='['+row+']';

	var product_qty=getValue('product_qty'+rowNum);
	if(product_qty==null || product_qty<2) return;
	product_qty=Number(product_qty);

	var split_qty=0;
	if(product_qty<4)
	{
		split_qty=confirm('Are you sure you would like to split this product?');
		if(!split_qty) return;
		split_qty=1;
	}
	else
	{
		split_qty=prompt('How many of this product would you like to split? (1 - '+(product_qty-1)+')','1');
		if(split_qty==null || split_qty<1 || split_qty>=product_qty) return;
	}
	var new_product=product_count;
	addProduct();

	var fields=Array('product_cost', 'product_id', 'sub_id', 'product_name', 'product_notes', 'product_offer', 'product_offerau', 'product_offerus', 'product_online', 'product_weight', 'product_weight_inc', 'supplier_product_cost', 'supplier_cost_onload', 'supplier_cost_orig', 'supplier_cost_orig_curr', 'product_costau', 'product_costus', 'supplier_country', 'supplier_found', 'supplier_id', 'supplier_id_orig', 'supplier_name', 'supplier_notes', 'supplier_product_id', 'supplier_sub_id', 'supplier_product_name', 'supplier_product_qty');
	for(var i=0;i<fields.length;i++)
	{
		var field=fields[i];
		setValue(field+'['+new_product+']',getValue(field+rowNum));
	}

	setValue('product_qty['+new_product+']',split_qty);
	setValue('supplier_product_qty['+new_product+']',split_qty);
	setValue('product_qty'+rowNum,product_qty-split_qty);
	setValue('supplier_product_qty'+rowNum,product_qty-split_qty);
}

function setBulkData(data)
{
	if(data==null) return;

	if(data['return_message']=='add_bulk')
	{
		var row=addBulk();
		var currency=getValue('invoice_currency');

		var bulk_number='000000'+data['bulk_id'];
		bulk_number='BO-'+bulk_number.substring(bulk_number.length-4);

		setValue('product_id['+row+']',				bulk_number);
		setValue('sub_id['+row+']',					'0');
		setValue('product_name['+row+']',			data['title']);
		setValue('product_qty['+row+']',			'1');
		setValue('product_cost['+row+']',			convertCurrency(data['bulk_cost'],'AUD',currency));
		setValue('supplier_product_cost['+row+']',	convertCurrency(data['bulk_cost'],'AUD',currency));
		setValue('product_costau['+row+']',			data['bulk_cost']);
		setValue('product_costus['+row+']',			convertCurrency(data['bulk_cost'],'AUD','USD'));
		setValue('product_online['+row+']',			convertCurrency(data['bulk_online']/1.1,'AUD',currency));
		setValue('product_offer['+row+']',			convertCurrency(data['bulk_offer']/1.1,'AUD',currency));
		setValue('product_offerau['+row+']',		convertCurrency(data['bulk_offer']/1.1,'AUD'));
		setValue('product_offerus['+row+']',		convertCurrency(data['bulk_offer']/1.1,'USD'));
		setValue('product_weight['+row+']',			data['bulk_weight']);
		setValue('product_weight_inc['+row+']',		data['bulk_weight']);
		setValue('product_warranty['+row+']',		data['bulk_warranty']);
		setValue('product_condition['+row+']',		data['bulk_condition']);
		setValue('product_terms['+row+']',			data['bulk_terms']);
		setValue('product_voltage['+row+']',		data['bulk_voltage']);
		setValue('generic_code['+row+']',			bulk_number);
		setValue('product_price_changed['+row+']',	'n');
		if(data['bulk_notes']!=null) setValue('product_notes['+row+']', data['bulk_notes']);
		if(data['bulk_weight']<=0.0 && addMissingWeight!=null) addMissingWeight(bulk_number,data['title'],data['bulk_url']);

		var data2=data;
		data2['return_message']='bulk_select_'+row;
		setSupplierData(data2);



		productUpdateWeight();
		productChange(row);
	}
}

function setProductData(data)
{
	if(data==null) return;

	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);
		/*
		generic[100]
		generic_code[100]
		prod_count[100]
		product_cost[100]
		product_id[100]
		product_name[100]
		product_notes[100]
		product_offer[100]
		product_offerau[100]
		product_offerus[100]
		product_online[100]
		product_price_changed[100]
		product_qty[100]
		product_rank[100]
		product_updated[100]
		product_weight[100]
		product_weight_inc[100]
		sub_id[100]
		*/
		if(data['product_id']==null)			data['product_id']=getValue('product_id['+row+']');
		if(data['sub_id']==null)				data['sub_id']=0;
		if(data['min_order']==null)				data['min_order']=1;
		if(data['product_cost']==null)			data['product_cost']='0.00';
		if(data['product_online']==null)		data['product_online']='0.00';
		if(data['product_offer']==null)			data['product_offer']='0.00';
		if(data['product_weight']==null)		data['product_weight']=0.0;
		if(data['product_weight_inc']==null)	data['product_weight_inc']=0.0;
		if(data['product_weight_inc']==0.0)		data['product_weight_inc']=data['product_weight'];
		if(data['product_notes']==null)			data['product_notes']='';
		if(data['product_condition']==null)		data['product_condition']=0;
		if(data['stock']==null)					data['stock']='c';
		if(data['dPoints']==null)				data['dPoints']=2;
		if(data['section']==null)				data['section']=0;

		setValue('product_id['+row+']',				data['product_id']);
		setValue('sub_id['+row+']',					data['sub_id']);
		setValue('product_name['+row+']',			data['product_name']);
		setValue('product_qty['+row+']',			data['min_order']);
		setValue('product_condition['+row+']',		data['product_condition']);
		setValue('product_cost['+row+']',			data['product_cost']);
		setValue('product_costau['+row+']',			data['product_cost_au']);
		setValue('product_costus['+row+']',			convertCurrency(data['product_cost_au'],'AUD','USD'));
		setValue('product_notes['+row+']',			data['product_notes']);
		setValue('product_offer['+row+']',			data['product_offer']);
		setValue('product_offerau['+row+']',		data['product_offerau']);
		setValue('product_offerus['+row+']',		convertCurrency(data['product_offerau'],'AUD','USD'));
		setValue('product_online['+row+']',			data['product_online']);
		setValue('product_section['+row+']',		data['section']);
		setValue('decimal['+row+']',				data['dPoints']);
		setValue('product_price_changed['+row+']',	'n');
		setValue('product_warranty['+row+']',		data['warranty']);
		setValue('product_weight['+row+']',			data['product_weight']);
		setValue('product_weight_inc['+row+']',		data['product_weight_inc']);
		setValue('supplier_product_cost['+row+']',	data['product_cost']);
		setValue('supplier_country['+row+']',		data['location']);

		if(data['supplier']==0 && (data['stock']=='s' || data['stock']=='ss'))
		{
			data['supplier_id']='660';
			data['supplier_name']='Highway Entertainment Pty Ltd';
			//data['country']='AU';
			if(data['voltage']!=null)		setValue('product_voltage['+row+']',	data['voltage']);
			if(data['plug_type']!=null)		setValue('product_plug['+row+']',		data['plug_type']);
			if(data['condition']!=null)		setValue('product_condition['+row+']',	data['condition']);
			if(data['packing_terms']!=null)	setValue('product_packing['+row+']',	data['packing_terms']);
			if(data['ship_terms']!=null)	setValue('product_terms['+row+']',		data['ship_terms']);
		}
		setValue('supplier_product_qty['+row+']',	data['min_order']);

		if(data['producttype_id']!=null) setValue('producttype_id',data['producttype_id']);

		if(data['product_weight']<=0.0 && addMissingWeight!=null) addMissingWeight(data['product_id'],data['product_name'],data['product_url']);
	}

	setSupplierData(data);
	if(data['supplier']!=null)
	{
		if(typeof(data['supplier'])=="object")
			selectSupplier(row,data['supplier'][0]);
		else
			selectSupplier(row,data['supplier']);
	}

	productUpdateWeight();
	productChange(row);
}

function setForwardersData(data)
{
	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);
		setValue('supplier_currency['+row+']',data['currency']);
		//setValue('supplier_cost['+row+']',data['product_cost']);
		setValue('supplier_id['+row+']',data['id']);
		//setValue('supplier_product_id['+row+']',data['product_id']);
		//setValue('supplier_sub_id['+row+']',data['sub_id']);
		//setValue('supplier_product_name['+row+']',data['supplier_product_name']);
		setValue('supplier_name['+row+']',data['name']);
		setValue('supplier_country['+row+']',data['country']);
		setValue('supplier_found['+row+']','y');
		productChange(row,'sfound');
	}
}

function setForwarderData(data)
{
	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);
		//setValue('supplier_currency['+row+']',data['cost_currency']);
		//setValue('supplier_cost['+row+']',data['product_cost']);
		setValue('supplier_id['+row+']',data['id']);
		//setValue('supplier_product_id['+row+']',data['product_id']);
		//setValue('supplier_sub_id['+row+']',data['sub_id']);
		//setValue('supplier_product_name['+row+']',data['supplier_product_name']);
		setValue('supplier_name['+row+']',data['name']);
		setValue('supplier_country['+row+']',data['country']);
		setValue('supplier_found['+row+']','y');
		productChange(row,'sfound');
	}
}

function setSupplierData(data)
{
	if(data == null) return;

	if(data['return_message']=='default')
	{
		setValue('def_sup_name',data['supplier_name']);
		setValue('default_supplier',data['id']);
		if(data['warranty']!= null) setValue('def_warranty',data['warranty']);
		if(data['condition']!= null) setValue('def_condition',data['condition']);
		if(data['packing']!= null) setValue('def_packing',data['packing']);
		if(data['voltage']!= null) setValue('def_voltage',data['voltage']);
		if(data['plug']!= null) setValue('def_plug',data['plug']);
		if(data['terms']!= null) setValue('def_terms',data['terms']);
		if(data['currency']!=null) setValue('def_currency',data['currency']);
		if(data['country']!=null)
		{
			setValue('def_country',data['country']);
			setValue('supply_country',data['country']);
		}
	}

	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);
		/*
		product_condition[100]
		product_costau[100]
		product_costus[100]
		product_packing[100]
		product_plug[100]
		product_terms[100]
		product_voltage[100]
		product_warranty[100]
		span_supplier_currency[100]
		supplier_cost[100]
		supplier_country[100]
		supplier_currency[100]
		supplier_found[100]
		supplier_id[100]
		supplier_name[100]
		supplier_notes[100]
		supplier_product_cost[100]
		supplier_product_id[100]
		supplier_product_name[100]
		supplier_product_qty[100]
		supplier_sub_id[100]
		*/
		if(data['supplier']!='' && data['supplier']!=null) data['supplier_id']=data['supplier'];
		if(data['supplier_id']=='' || data['supplier_id']==null) data['supplier_id']=data['id'];
		if(data['supplier_product_name']=='' || data['supplier_product_name']==null) data['supplier_product_name']=data['product_name'];

		if(data['cost_currency']!=null)			setValue('supplier_currency['+row+']',		data['cost_currency']);
		if(data['cost_price']!=null)			setValue('supplier_cost['+row+']',			data['product_cost']);
		if(data['supplier_id']!=null)			setValue('supplier_id['+row+']',			data['supplier_id']);
		if(data['product_id']!=null)			setValue('supplier_product_id['+row+']',	data['product_id']);
		if(data['sub_id']!=null)				setValue('supplier_sub_id['+row+']',		data['sub_id']);
		if(data['supplier_product_name']!=null)	setValue('supplier_product_name['+row+']',	data['supplier_product_name']);
		if(data['supplier_name']!=null)			setValue('supplier_name['+row+']',			data['supplier_name']);
		if(data['currency']!=null)
		{
			setValue('supplier_currency['+row+']',	data['currency']);
			productChange(row,'scost');
		}
		var supC=getValue('supplier_country['+row+']');
		if(data['country']!=null && (supC=='' || supC==0)) setValue('supplier_country['+row+']',data['country']);
		// now update the default values if the section & the condition match
		var entered=false;
		var site_id=getValue('site_id');
		var section=getValue('product_section['+row+']');
		var condition=getValue('product_condition['+row+']');
		if(data['values']!='' && data['values']!=null)
		{
			for(key in data['values'])
			{
				if(entered) break;
				var tmp=data['values'][key];
				if(site_id==tmp['company'] && section==tmp['section'] && condition==tmp['condition'])
				{
					if(tmp['warranty']!=null)	setValue('product_warranty['+row+']',	tmp['warranty']);
					if(tmp['voltage']!=null)	setValue('product_voltage['+row+']',	tmp['voltage']);
					if(tmp['plug']!=null)		setValue('product_plug['+row+']',		tmp['plug']);
					if(tmp['condition']!=null)	setValue('product_condition['+row+']',	tmp['condition']);
					if(tmp['packing']!=null)	setValue('product_packing['+row+']',	tmp['packing']);
					if(data['terms']!=null)		setValue('product_terms['+row+']',		tmp['terms']);
					entered=true;
				}
			}
		}

		//now have to update the overall Packing/Shipping Terms


		if(data['qty']!=null && data['reserved']!=null)
			setValue('supplier_found['+row+']',((data['qty']-data['reserved']>0)?((data['stock']=='s' || data['stock']=='ss')?'s':'y'):'n'));
		productChange(row,'sfound');
	}

	if(data['return_message'].substring(0,12)=='bulk_select_')
	{
		var row=data['return_message'].substring(12);

		var bulk_number='000000'+data['bulk_id'];
		bulk_number='BO-'+bulk_number.substring(bulk_number.length-4);

		if(data['supplier_id']=='' || data['supplier_id']==null)
			data['supplier_id']=data['id'];
		if(data['supplier_product_name']=='' || data['supplier_product_name']==null)
			data['supplier_product_name']=data['product_name'];

		setValue('supplier_country['+row+']',		data['bulk_country']);
		setValue('supplier_id['+row+']',			data['bulk_supplier']);
		setValue('supplier_name['+row+']',			data['bulk_supplier_name']);
		setValue('supplier_product_qty['+row+']','1');
		setValue('supplier_currency['+row+']','AUD');
		setValue('supplier_cost['+row+']',data['bulk_cost']);
		setValue('supplier_product_id['+row+']',bulk_number);
		setValue('supplier_sub_id['+row+']','');
		setValue('supplier_product_name['+row+']',data['title']);

		setValue('supplier_found['+row+']','y');
		productChange(row,'sfound');
	}
}

function setExtraSupplierData()
{
	var ids=getProductRows();
	var def_country='';
	var pack='';
	var terms='';
	for(key in ids)
	{
		var tmp=getValue('supplier_country'+ids[key]);
		if(def_country=='' || tmp==def_country) def_country=tmp;
		else def_country='';
		tmp=getValue('product_packing'+ids[key]);
		if(pack=='' || tmp==pack) pack=tmp;
		else pack='';
		tmp=getValue('product_terms'+ids[key]);
		if(terms=='' || tmp==terms) terms=tmp;
		else terms='';
	}
	var tm=getElement('terms_id');
	tm.value=terms; tm.style.backgroundColor='#'+((terms==0)?'EE90A0':'FFFFFF');
	var dc=getElement('supply_country');
	dc.value=def_country; dc.style.backgroundColor='#'+((def_country=='')?'EE90A0':'FFFFFF');
	var pr=getElement('pack_requirement_id');
	pr.value=pack; pr.style.backgroundColor='#'+((pack==0)?'EE90A0':'FFFFFF');
}

function updateAllSuppliers(supplier_name, obj)
{
	var old_val=obj.value;
	obj.value='Please Wait...Updating';
	var supplier_id=getValue('default_supplier');
	var rows=getProductRows();
	for(key in rows)
	{
		var tmp=rows[key];
		setValue('supplier_name'+tmp,supplier_name);
		setValue('supplier_id'+tmp,supplier_id);
		var updateNotes=false;
		if(getValue('def_warranty')!=0)	{setValue('product_warranty'+tmp,getValue('def_warranty')); updateNotes=true;}
		if(getValue('def_condition')!=0){setValue('product_condition'+tmp,getValue('def_condition')); updateNotes=true;}
		if(getValue('def_voltage')!=0)	{setValue('product_voltage'+tmp,getValue('def_voltage')); updateNotes=true;}
		if(getValue('def_plug')!=0)		{setValue('product_plug'+tmp,getValue('def_plug')); updateNotes=true;}
		if(getValue('def_currency')!="")
		{
			setValue('supplier_currency'+tmp,getValue('def_currency'));
			productChange(tmp.substr(1,tmp.length-2),'scostau');
			updateNotes=true;
		}
		if(getValue('def_country')!="")
		{
			setValue('supplier_country'+tmp,getValue('def_country'));
			setValue('supply_country',getValue('def_country'));
		}
		if(getValue('def_packing')!=0)	setValue('product_packing'+tmp,getValue('def_packing'));
		if(getValue('def_terms')!=0)	setValue('product_terms'+tmp,getValue('def_terms'));
		if(updateNotes) updateProductNotes(tmp);
	}
	obj.value=old_val;
}

function updateProductNotes(prod_count)
{
	var warranty = <?php jecho($warranty_terms)?>;
	var voltage = <?php jecho($voltage_terms)?>;
	var notes = [];
	var val1=getValue('product_voltage'+prod_count);
	var val2=getValue('product_plug'+prod_count);
	var val3=getValue('product_warranty'+prod_count);
	var val4=getValue('product_condition'+prod_count);
	var count=0;
	if(val1!=0) { notes[count]='Power Supply Voltage: '+voltage[val1]; count++ }
	if(val2!=0) { notes[count]='Power Adapter ID: '+val2; count++;  }
	if(val3!=0) { notes[count]='Warranty: '+warranty[val3]; count++; }
	if(val4==2) { notes[count]='Condition: Used'; count++; }
	if(getValue('product_updated'+prod_count) == 'n') setValue('product_notes'+prod_count,notes.join("\n"));
}

function actionPurchasePreview(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="invoice_purchase_preview.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}

function actionPurchaseOrder(theFrm)
{
	//Do a check first
	var rows=getElement('product_table').rows;
	var ids=[];
	var count=0;
	for(var i=0;i<rows.length;i++)
	{
		var n=rows[i].id;
		if(n!=null)
		{
			var t1=n.split("[");
			var t2=t1[1].split("]");
			if(!array_search(t2[0],ids))
				ids[count++]=t2[0];
		}
	}
	for(var i=0;i<ids.length;i++)
		if(getValue('supplier_id['+ids[i]+']')==0)
		{
			alert("One or more products do not have a supplier\n\nPlease correct this before trying again");
			return false;
		}

	var oldact=theFrm.action;
	theFrm.action=".?action=do_purchase";
	theFrm.submit();
	theFrm.action=oldact;
}

function updateCostPrice(id, site_id, row_id)
{
	var url='update_cost_price.php?product_id='+id+'&site_id='+site_id+'&return_message='+row_id+'&currency='+getValue('invoice_currency');
	if(confirm('This will update the cost price for ID#'+id+'. Continue??')) window.open(url,'','width=10,height=10');
}

function setCostPrice(data)
{
	row_id=data['return_message'];
	setValue('supplier_product_cost['+row_id+']',data['product_cost']);
	setValue('product_costau['+row_id+']',data['product_cost_au']);
	setValue('product_costus['+row_id+']',convertCurrency(data['product_cost_au'],'AUD','USD'));
	productChange(row_id,'scost');
	productChange(row_id,'scostau');
}

function updateProductPrice(row)
{
	var product_id=getValue('product_id['+row+']');
	var sub_id=getValue('sub_id['+row+']');
	var product_qty=getValue('product_qty['+row+']');
	var generic=getValue('generic['+row+']');
	var site_id=getValue('site_id');
	var member_id=getValue('member_id');
	var currency=getValue('invoice_currency');
	var url = 'invoice_update_price.php?product_id='+product_id;
	if(sub_id!=null) url+='&sub_id='+sub_id;
	if(site_id!=null) url += '&site_id='+site_id;
	if(member_id!=null) url += '&member_id='+member_id;
	if(currency!=null) url+= '&currency='+currency;
	if(product_qty!=null) url += '&product_qty='+product_qty;
	productChange(row,'pqty');
<?php
if (9 != $_GLOBALS['site_settings']['site_id']) {
?>
	if(generic=='n')
	{
		//now start of the AJAX script.
		if (window.XMLHttpRequest)
		{ // Mozilla, Safari, ...
			http_request = new XMLHttpRequest();
			//if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
			if (http_request.overrideMimeType) http_request.overrideMimeType('text/plain');
		}
		else if (window.ActiveXObject)
		{ //IE
			try
			{
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				try
				{
					http_request = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e) {}
			}
		}
		if (!http_request) {
			alert('Giving up :( Cannot create an XMLHTTP instance');
			return false;
		}

		http_request.onreadystatechange = function() { updatePrices(http_request,row); };
		http_request.open('GET', url, true);
		http_request.send(null);
	}
<?php
}
?>
}

function updatePrices(http_request,row)
{
	if (http_request.readyState == 4)
	{
		if (http_request.status == 200)
		{
			var prices=explode(', ',http_request.responseText);
			var price_changed=getValue('product_price_changed['+row+']');
			if(price_changed=='n')
			{
				setValue('product_online['+row+']',prices[0]);
				setValue('product_offer['+row+']',prices[0]);
				setValue('product_offerau['+row+']',prices[1]);
				setValue('product_offerus['+row+']',convertCurrency(prices[1],'AUD','USD'));
			}
			productUpdateTotals();
		}
		else alert('There was a problem with the request.');
	}

}

function makeProductIndividual(pcount)
{
	var product_id=getValue('product_id['+pcount+']');
	var sub=getValue('sub_id['+pcount+']');
	var war=getValue('product_warranty['+pcount+']');
	var con=getValue('product_condition['+pcount+']');
	var vlt=getValue('product_voltage['+pcount+']');
	var pck=getValue('product_packing['+pcount+']');
	var trm=getValue('product_terms['+pcount+']');
	var cnt=getValue('supplier_country['+pcount+']');
	//var plg=getValue('product_plug['+pcount+']');
	var url='/webadmin/products/products_add.php?link_id='+product_id;
	if(sub!='') url+='&sub_id='+sub;
	if(war!=0) url+='&warranty='+war;
	if(vlt!=0) url+='&voltage='+vlt;
	if(con!=0) url+='&condition='+con;
	if(pck!=0) url+='&packing_terms='+pck;
	if(trm!=0) url+='&ship_terms='+trm;
	if(cnt!='') url+='&location='+cnt;
	window.open(url);
}
//]]>
</script>

<?php
$product_affected = array();
foreach ($invoice_products as $product) {
    $product_affected[] = $product['prod_count'];
}

?>
<input type="hidden" name="product_affected" value="<?= implode(',', $product_affected) ?>" />

<?php
$charge_affected = array();
foreach ($invoice_charges as $charge) {
    $charge_affected[] = $charge['charge_count'];
}

?>
<input type="hidden" name="charge_affected" value="<?= implode(',', $charge_affected) ?>" />

<!-- /product_supplier2.tpl.php -->
