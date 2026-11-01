<!-- invoice_suppliers.tpl -->

<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/classes/class.html_to_js.php';
$html_to_js = new html_to_js();

$owarranty = $ocondition = $opacking = $ovoltage = $oshipping = $oplug = 0;
$defSup    = $invoice['default_supplier'];
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
$supValues = getSupplierValues($defSup);

$warranty_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('warranty_terms_id');
$condition_terms = array('-1' => 'Not Applicable') + invoiceGetOptionList('condition');
$packing_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('packing_requirement');
$voltage_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('voltage_id2');
$shipping_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('terms_id');
$plugs_list      = array('-1' => 'Not Applicable') + invoiceGetOptionList('plug_id');

function supplier_row($count = '[PRODUCT_COUNT]', $data = array())
{
    $post_key = 'invoice_suppliers[' . $count . ']';
    $hidden   = array(
    );

    ob_start();
?>
<tr id="supplier_rowa[<?= $count ?>]">
	<th><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>PID</th>
	<th>Product Name</th>
	<th>Supplier</th>
	<th>&nbsp;</th>
	<th>Found</th>
	<th>Qty</th>
	<th>Cost <span name="span_supplier_currency[<?= $pcount ?>]"><?php hecho($product['supplier_currency'])?></span></th>
	<th>Cost USD</th>
</tr>
<tr id="supplier_rowb[<?= $pcount ?>]"<?= ('' != $bgcolor) ? (' style="background-color:' . $bgcolor . ';"') : '' ?>>
	<td><input disabled="disabled" name="supplier_product_id[<?= $pcount ?>]" size="4" style="text-align:right;" type="text" value="<?= $product['product_id'] ?>" onchange="productChange(<?= $pcount ?>,'sid');" /></td>
	<td><input name="supplier_product_name[<?= $pcount ?>]" disabled="disabled" size="12" style="width:100%;overflow:hidden;" type="text" value="<?php hecho($product['product_name'])?>" /></td>
	<td><input name="supplier_name[<?= $pcount ?>]" size="12" style="width:100%;" type="text" value="<?php hecho($sup_name)?>" /></td>
	<td><input onclick="searchSupplier(<?= $pcount ?>,getValue('product_id[<?= $pcount ?>]'));" type="button" value="S" /></td>
	<td><select name="supplier_found[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>,'sfound');"><?= str_replace("\n", "", formatHTMLOptions($stock, $product['supplier_found'])) ?></select></td>
	<td><input name="supplier_product_qty[<?= $pcount ?>]" disabled="disabled" size="3" style="text-align:right;" type="text" value="<?= $product['product_qty'] ?>" onfocus="this.select();"/></td>
	<td><input name="supplier_cost[<?= $pcount ?>]" size="6" style="text-align:right;" type="text" value="<?= $product['supplier_cost'] ?>" onfocus="this.select();" onchange="productChange(<?= $pcount ?>,'ocost');" /></td>
	<td><input name="product_costus[<?= $pcount ?>]" readonly="readonly" size="6" style="text-align:right;" type="text" value="<?= $product['product_costus'] ?>" /></td>
</tr>
<tr class="alt" id="supplier_rowc[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Warranty :</td>
	<td colspan="2"><select name="product_warranty[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($warranty_terms, $product['product_warranty'])) ?></select></td>
	<td colspan="1" class="left_title">Voltage :</td>
	<td colspan="3"><select name="product_voltage[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($voltage_terms, $product['product_voltage'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowd[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Plug Type :</td>
	<td colspan="2"><select name="product_plug[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($plugs_list, $product['product_plug'])) ?></select></td>
	<td colspan="1" class="left_title" style="white-space:nowrap;">Condition :</td>
	<td colspan="3"><select name="product_condition[<?= $pcount ?>]" onchange="updateProductNotes('<?= $pcount ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($condition_terms, $product['product_condition'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowe[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Packing :</td>
	<td colspan="2"><select name="product_packing[<?= $pcount ?>]"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($packing_terms, $product['product_packing'])) ?></select></td>
	<td colspan="1" class="left_title">Terms :</td>
	<td colspan="3"><select name="product_terms[<?= $pcount ?>]"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($shipping_terms, $product['product_terms'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowf[<?= $pcount ?>]">
	<td colspan="2" class="left_title">Currency :</td>
	<td colspan="2"><select name="supplier_currency[<?= $pcount ?>]" onchange="productChange(<?= $pcount ?>,'ocost');"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCurrencyList(), $product['supplier_currency'])) ?></select></td>
	<td colspan="1" class="left_title">Country :</td>
	<td colspan="3"><select name="supplier_country[<?= $pcount ?>]"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCountryList(), $supCountry)) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowg[<?= $pcount ?>]" style="<?= ($generic) ? 'display:none' : '' ?>">
	<td colspan="8" style="text-align:center"><?php if ($update_button) {?><input onclick="updateCostPrice(<?= $product['product_id'] ?>,<?= $invoice['site_id'] ?>,<?= $pcount ?>);" type="button" value=" Update Product Cost Price " style="background-color:#CDEFEA" /><?php }?></td>
</tr>
<tr class="alt" id="supplier_rowh[<?= $pcount ?>]">
	<td>Notes<br /><br /><input type="button" onclick="splitProduct(<?= $pcount ?>);" value="Split" /></td>
	<td colspan="7"><textarea cols="80" style="width:100%;" name="supplier_notes[<?= $pcount ?>]" rows="<?= max(3, count(explode("\n", trim($disNotes)))) ?>" onkeyup="textareaAutoSize(this,1,3);"><?php hecho(trim($disNotes))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var supplier_count=<?= max(100, count($invoice_products)) ?>;

function invoiceSupplierAdd()
{
	var count=String(supplier_count);
	var table_elem=getElement("invoice_supplier_table");
	if(table_elem!=null)
	{
		<?= str_replace("[SUPPLIER_COUNT]", "'+count+'", implode("\n\t\t", $html_to_js->parse_html(supplier_row('[SUPPLIER_COUNT]')))) ?>

		supplier_count++;
		return true;
	}
	return false;
}

function invoiceSupplierGetRows()
{
	var table_elem=getElement("invoice_supplier_table");
	var table_rows=[];
	var table_rows2={};
	if(table_elem!=null)
	{
		var theRows=table_elem.rows;
		for(var i=0;i<theRows.length;i++) if(theRows[i].id.substr(0,21)=='invoice_supplier_rowa[')
		{
			var key=theRows[i].id.substr(21);
			key=key.substr(0,key.length-1);
			if(table_rows2[key]==null)
			{
				table_rows2[key]=true;
				table_rows[table_rows.length]=key;
			}
		}
	}
	return table_rows;
}







function addCharge()
{
	//global charge_count
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add charges until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var site_id=getValue('site_id');
	var ccount=String(charge_count);
	var prod_table=getElement("charges_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'product_charge_rowa['+ccount+']');//,-4);
<?php
$count = 0;
foreach (explode("\n", charge_row('[CHARGE_COUNT]', array(), $invoice['site_id'])) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	if(success) charge_count++;
	return success;
}

function addProduct()
{
	//global product_count;
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var pcount=String(product_count);
	var prod_table=getElement("product_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'product_rowa['+pcount+']');//,-4);
		var newRow2=addTableRow(prod_table,'product_rowb['+pcount+']');//,-4);
		newRow2.className='alt';
<?php
$count = 0;
foreach (explode("\n", product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	var supp_table=getElement("supplier_table");
	if(supp_table!=null)
	{
		success=true;
		var newRow1=addTableRow(supp_table,'supplier_rowa['+pcount+']');
		var newRow2=addTableRow(supp_table,'supplier_rowb['+pcount+']');
		var newRow3=addTableRow(supp_table,'supplier_rowc['+pcount+']');
		var newRow4=addTableRow(supp_table,'supplier_rowd['+pcount+']');
		var newRow5=addTableRow(supp_table,'supplier_rowe['+pcount+']');
		var newRow6=addTableRow(supp_table,'supplier_rowf['+pcount+']');
		var newRow7=addTableRow(supp_table,'supplier_rowg['+pcount+']');
		var newRow8=addTableRow(supp_table,'supplier_rowh['+pcount+']');
		newRow3.className='alt'; newRow4.className='alt'; newRow5.className='alt'; newRow6.className='alt'; newRow6.style.textAlign='center'; newRow8.className='alt';
<?php
$count = 0;
foreach (explode("\n", supplier_row('[PRODUCT_COUNT]', array(), true)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<th>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ").className = 'th';\n";
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    } elseif (substr($row, 0, 13) == '<td colspan="') {
        $cspan = substr($row, 13, 1);
        echo "var theCell = addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ");\n";
        echo "theCell.colSpan = " . $cspan . ";\n";
        if (9 == $cspan) {
            echo "theCell.style.textAlign = 'center';\n";
        }

        echo "theCell.className = 'left_title';\n";
    }
}
?>
	}
	if(success) product_count++;
}

function addGeneric()
{
	//global product_count;
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var pcount=String(product_count);
	var prod_table=getElement("product_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'product_rowa['+pcount+']');//,-4);
		var newRow2=addTableRow(prod_table,'product_rowb['+pcount+']');//,-4);
		newRow2.className='alt';
<?php
$count = 0;
foreach (explode("\n", product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]'), true)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	var supp_table=getElement("supplier_table");
	if(supp_table!=null)
	{
		success=true;
		var newRow1=addTableRow(supp_table,'supplier_rowa['+pcount+']');
		var newRow2=addTableRow(supp_table,'supplier_rowb['+pcount+']');
		var newRow3=addTableRow(supp_table,'supplier_rowc['+pcount+']');
		var newRow4=addTableRow(supp_table,'supplier_rowd['+pcount+']');
		var newRow5=addTableRow(supp_table,'supplier_rowe['+pcount+']');
		var newRow6=addTableRow(supp_table,'supplier_rowf['+pcount+']');
		var newRow7=addTableRow(supp_table,'supplier_rowg['+pcount+']');
		var newRow8=addTableRow(supp_table,'supplier_rowh['+pcount+']');
		newRow3.className='alt'; newRow4.className='alt'; newRow5.className='alt'; newRow6.className='alt'; newRow8.className='alt';
<?php
$count = 0;
foreach (explode("\n", supplier_row('[PRODUCT_COUNT]', array(), true)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<th>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    } elseif (substr($row, 0, 13) == '<td colspan="') {
        $cspan = substr($row, 13, 1);
        echo "var theCell = addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ");\n";
        if (9 == $cspan) {
            echo "theCell.style.display='none';\n";
        }

        echo "theCell.colSpan=" . $cspan . ";\n";
        echo "theCell.className='left_title';\n";
    }
}
?>
	}
	if(success) product_count++;
}

function deleteCharge(row)
{
	var theRow=getElement("product_charge_rowa["+row+"]");
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
	setValue('ship_costus','');
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
	var theRow=getElement("product_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowc["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowd["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowe["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowf["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowg["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("supplier_rowh["+row+"]");
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
		setValue("charge_offer["+row+"]", convertCurrency(getValue("charge_offerus["+row+"]"), "USD", getValue("invoice_currency"),true));
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
		setValue("supplier_cost["+row+"]",convertCurrency(product_cost,invoice_currency,supplier_currency,true));
		setValue("supplier_product_cost["+row+"]",product_cost);
		setValue("product_costus["+row+"]",convertCurrency(product_cost,invoice_currency,"USD",true));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("product_offerus["+row+"]", convertCurrency(getValue("product_offer["+row+"]"), getValue("invoice_currency"), "USD",true));
		setValue('product_price_changed['+row+']','y');
		do_totals=true;
		break;
	case 'pofferus':
		setValue("product_offer["+row+"]", convertCurrency(getValue("product_offerus["+row+"]"), "USD", getValue("invoice_currency"),true));
		do_totals=true;
		break;
	case 'sid':
		setValue('product_id['+row+']',getValue('supplier_product_id['+row+']'));
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
		setValue("supplier_cost["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,supplier_currency,true));
		setValue("product_costus["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,"USD",true));
		setValue("product_cost["+row+"]",supplier_product_cost);
		do_totals=true;
		break;
	case 'ocost':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var supplier_cost=getValue("supplier_cost["+row+"]");
		setValue("supplier_product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency,true));
		setValue("product_costus["+row+"]", convertCurrency(supplier_cost,supplier_currency,"USD",true));
		setValue("product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency,true));
		do_totals=true;
		break;
	case 'scostus':
		var supplier_currency=getValue("supplier_currency["+row+"]");
		var invoice_currency=getValue("invoice_currency");
		var product_costus=Number(getValue("product_costus["+row+"]"));
		setValue("supplier_cost["+row+"]",convertCurrency(product_costus,"USD",supplier_currency,true));
		setValue("supplier_product_cost["+row+"]",convertCurrency(product_costus,"USD",invoice_currency,true));
		setValue("product_cost["+row+"]",convertCurrency(product_costus,"USD",invoice_currency,true));
		do_totals=true;
		break;
	default:
		do_totals=true;
		break;
	}

	if(do_totals) productUpdateTotals();
	if(do_located) productUpdateLocated();
}

function getProductRows()
{
	var product_table = getElement("product_table");
	var product_rows = [];
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
function memberShippingGetRows()
{
	var ship_table=getElement("member_shipping_table");
	var ship_rows=[];
	var ship_rows2={};
	if(ship_table!=null)
	{
		var theRows=ship_table.rows;
		for(var i=0;i<theRows.length;i++) if(theRows[i].id.substr(0,21)=='member_shipping_rowa[')
		{
			var key=theRows[i].id.substr(21);
			key=key.substr(0,key.length-1);
			if(ship_rows2[key]==null)
			{
				ship_rows2[key]=true;
				ship_rows[ship_rows.length]=key;
			}
		}
	}
	return ship_rows;
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
	}
}

function productUpdateLocated()
{
	var all_found=true;

	var supplier_rows = [];

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
		setValue("ship_costus",convertCurrency(getValue("ship_cost"),getValue("invoice_currency"),"USD",true));
		setValue("supplier_ship_cost",getValue("ship_cost"));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("ship_offerus", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "USD", true));
		do_totals=true;
		break;
	case 'pofferus':
		setValue("ship_offer", convertCurrency(getValue("ship_offerus"), "USD", getValue("invoice_currency"),true));
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
		setValue("ship_costus", convertCurrency(getValue("supplier_ship_cost"), getValue("invoice_currency"), "USD",true));
		setValue("ship_cost",getValue("supplier_ship_cost"));
		do_totals=true;
		break;
	case 'scostus':
		setValue("supplier_ship_cost", convertCurrency(getValue("ship_costus["+row+"]"), "USD", getValue("invoice_currency"),true));
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

	var product_rows = [];
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
	var total_costus=0.0;
	var total_offer=0.0;
	var total_offerus=0.0;
	var total_online=0.0;

	var product_rows = [];
	var charge_rows = [];

	var theRows=getElement("product_table").rows;
	var theRows1=getElement("charges_table").rows;
	for(var i=0;i<theRows.length;i++)
	{
		if(theRows[i].id.substr(0,13)=='product_rowa[')
			if(searchArray(theRows[i].id.substr(12),product_rows)==null)
				product_rows[product_rows.length]=theRows[i].id.substr(12);
	}
	for(var i=0;i<theRows1.length;i++)
	{
		if(theRows1[i].id.substr(0,20)=='product_charge_rowa[')
			if(searchArray(theRows1[i].id.substr(19),charge_rows)==null)
				charge_rows[charge_rows.length]=theRows1[i].id.substr(19);
	}

	var in_cur=getValue('invoice_currency');

	for(var i=0;i<product_rows.length;i++)
	{
		var rowNum=product_rows[i];
		var addMe=0;
		var product_qty=Number(getValue("product_qty"+rowNum));
		var product_cost=Number(getValue("product_cost"+rowNum));
		var product_costus=Number(getValue("product_costus"+rowNum));
		var product_offer=Number(getValue("product_offer"+rowNum));
		var product_offerus=Number(getValue("product_offerus"+rowNum));
		var product_online=Number(getValue("product_online"+rowNum));
		addMe=product_qty*product_cost;
		if(!isNaN(addMe)) total_cost+=addMe;
		addMe=product_qty*product_costus;
		if(!isNaN(addMe)) total_costus+=addMe;
		addMe=product_qty*product_offer;
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=product_qty*product_offerus;
		if(!isNaN(addMe)) total_offerus+=addMe;
		addMe=product_qty*product_online;
		if(!isNaN(addMe)) total_online+=addMe;
	}

	var products_cost=total_cost;
	var products_costus=total_costus;
	var products_offer=total_offer;
	var products_offerus=total_offerus;
	setAllValues('products_cost',convertCurrency(products_cost.toFixed(2),in_cur,'',true));
	setAllValues('products_costus',convertCurrency(products_costus.toFixed(2),'USD','',true));
	setAllValues('products_offer',convertCurrency(products_offer.toFixed(2),in_cur,'',true));
	setAllValues('products_offerus',convertCurrency(products_offerus.toFixed(2),'USD','',true));

	if(getValue('ship_id')>0)
	{
		addMe=Number(getValue('ship_cost'));
		if(!isNaN(addMe)) total_cost+=addMe;
		addMe=Number(getValue('ship_costus'));
		if(!isNaN(addMe)) total_costus+=addMe;
		addMe=Number(getValue('ship_offer'));
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=Number(getValue('ship_offerus'));
		if(!isNaN(addMe)) total_offerus+=addMe;
		addMe=Number(getValue('ship_online'));
		if(!isNaN(addMe)) total_online+=addMe;
	}

	var ship_cost=total_cost-products_cost;
	var ship_costus=total_costus-products_costus;
	var ship_offer=total_offer-products_offer;
	var ship_offerus=total_offerus-products_offerus;

	//Hack
	total_offerus=Number(total_offerus);

<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$query            = $DB->query("SELECT * FROM `charges` ORDER BY `charge_priority` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_amount[$row['charge_id']]   = $row['charge_amount'];
    $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
    $charges_percent[$row['charge_id']]  = $row['charge_percent'];
    $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
}
?>
	var charges_ids = <?php jecho(array_keys($charges_amount))?>;
	var charges_amount = <?php jecho(array_values($charges_amount))?>;
	var charges_currency = <?php jecho(array_values($charges_currency))?>;
	var charges_percent = <?php jecho(array_values($charges_percent))?>;
	var charges_rounding = <?php jecho(array_values($charges_rounding))?>;

	for(var j=0;j<charges_ids.length;j++)
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

				var charge_amount='0.00';
				var charge_amount_us='0.00';

				if(rounding==0.0 && percent==0.0 && amount=='0.00')
				{
					charge_amount=getValue('charge_cost'+rowNum);
					charge_amount_us=convertCurrency(getValue('charge_cost'+rowNum),getValue('invoice_currency'),'USD',true);
				}
				else
				{
					charge_amount=Number(convertCurrency(amount,currency,in_cur,true));
					charge_amount+=Number(total_offer)*percent;

					if(rounding!=0.0)
						charge_amount=(Math.ceil((total_offer+charge_amount)/rounding)*rounding)-total_offer;

					charge_amount=convertCurrency(Number(charge_amount).toFixed(2),in_cur,'',true);
					charge_amount_us=convertCurrency(charge_amount,in_cur,'USD',true);

					setValue('charge_cost'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
//					setValue('charge_cost'+rowNum,charge_amount_us);
					setValue('charge_offer'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
					setValue('charge_offerus'+rowNum,convertCurrency(charge_amount_us,'USD','',true));
					setValue('charge_online'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
				}

				total_cost+=Number(charge_amount);
				total_costus+=Number(charge_amount_us);
				total_offer+=Number(charge_amount);
				total_offerus+=Number(charge_amount_us);
				total_online+=Number(charge_amount);

				/*
				total_cost+=convertCurrency(charge_amount,in_cur,'',true);
				total_costus+=convertCurrency(charge_amount_us,'USD','',true);
				total_offer+=convertCurrency(charge_amount,in_cur,'',true);
				total_offerus+=convertCurrency(charge_amount_us,'USD','',true);
				total_online+=convertCurrency(charge_amount,in_cur,'',true);
				*/
			}
		}
	}

	var charges_offer=total_offer-(products_offer+ship_offer);
	var charges_offerus=total_offerus-(products_offerus+ship_offerus);
	setAllValues('charges_offer',convertCurrency(Number(charges_offer).toFixed(2),in_cur,'',true));
	setAllValues('charges_offerus',convertCurrency(Number(charges_offerus).toFixed(2),'USD','',true));

	setAllValues('invoice_cost',convertCurrency(Number(total_cost).toFixed(2),in_cur,'',true));
	setAllValues('invoice_online',convertCurrency(Number(total_online).toFixed(2),in_cur,'',true));
	setAllValues('invoice_offer',convertCurrency(Number(total_offer).toFixed(2),in_cur,'',true));
	setAllValues('invoice_offerus',convertCurrency(Number(total_offerus).toFixed(2),'USD','',true));
	setAllValues('invoice_costus',convertCurrency(Number(total_costus).toFixed(2),'USD','',true));

	setAllValues('invoice_profit',convertCurrency(Number(total_offer-total_cost).toFixed(2),in_cur,'',true));
	setAllValues('invoice_profitus',convertCurrency(Number(total_offerus-total_costus).toFixed(2),'USD','',true));
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
	var producttype=getValue('producttype_id');
	if(site_id=='1') site_id='1|2';
//	var url='/webadmin/products/index.php?action=list';
	//var url='productsearch.php?type='+producttype;
	var url='productsearch.php?';
	url+='&return_message=select_'+row;
	url+='&for='+escape(searchFor);
	if(currency!=null) url+='&currency='+currency;
//	if(site_id!=null) url+='&site_id='+site_id;
//	if(member_id!=null) url+='&member_id='+member_id;
	var features='';//'scrollbars, resizable';//height=700, width=800
	window.open(url,'product_popup',features);
}

function searchSupplier(row,searchFor)
{
	//var url='/webadmin/suppliers/index.php?action=list';
	var url='suppliersearch.php?action=list';
	url+='&return_message=select_'+row;
	if(searchFor!='None') url+='&product_id='+escape(searchFor);
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

	var fields=Array('product_cost', 'product_id', 'product_name', 'product_notes', 'product_offer', 'product_offerus', 'product_online', 'product_weight', 'product_weight_inc', 'supplier_product_cost', 'supplier_cost_onload', 'supplier_cost_orig', 'supplier_cost_orig_curr', 'product_costus', 'supplier_country', 'supplier_found', 'supplier_id', 'supplier_id_orig', 'supplier_name', 'supplier_notes', 'supplier_product_id', 'supplier_product_name', 'supplier_product_qty');
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

function setProductData(data)
{
	if(data==null) return;

	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);

		if(data['product_id']==null)			data['product_id']=getValue('product_id['+row+']');
		if(data['min_order']==null)				data['min_order']=1;
		if(data['product_cost']==null)			data['product_cost']='0.00';
		if(data['product_online']==null)		data['product_online']='0.00';
		if(data['product_offer']==null)			data['product_offer']='0.00';
		if(data['product_weight']==null)		data['product_weight']=0.0;
		if(data['product_weight_inc']==null)	data['product_weight_inc']=0.0;
		if(data['product_weight_inc']==0.0)		data['product_weight_inc']=data['product_weight'];
		if(data['product_notes']==null)			data['product_notes']='';
		if(data['product_condition']==null)		data['product_condition']=0;

		setValue('product_id['+row+']',				data['product_id']);
		setValue('product_name['+row+']',			data['product_name']);
		setValue('product_qty['+row+']',			data['min_order']);
		setValue('product_cost['+row+']',			data['product_cost']);
		setValue('supplier_product_cost['+row+']',	data['product_cost']);
		setValue('product_costus['+row+']',			data['product_costus']);
		setValue('product_online['+row+']',			data['product_online']);
		setValue('product_offer['+row+']',			data['product_offer']);
		setValue('product_offerus['+row+']',		data['product_offerus']);
		setValue('product_weight['+row+']',			data['product_weight']);
		setValue('product_weight_inc['+row+']',		data['product_weight_inc']);
		setValue('product_notes['+row+']',			data['product_notes']);
		setValue('product_warranty['+row+']',		data['warranty']);
		setValue('product_condition['+row+']',		data['product_condition']);
		setValue('product_price_changed['+row+']',	'n');

		setValue('supplier_product_qty['+row+']',	data['min_order']);

		if(data['producttype_id']!=null)
			setValue('producttype_id',data['producttype_id']);
		category=data['producttype_id'];

		//if(data['product_weight']<=0.0 && addMissingWeight!=null)
		//	addMissingWeight(data['product_id'],data['product_name'],data['product_url']);
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
		if(data['supplier_country']!=null)
		{
			setValue('def_country',data['country']);
			setValue('supply_country',data['country']);
		}
	}
	if(data['return_message'].substring(0,7)=='select_')
	{
		var row=data['return_message'].substring(7);

		var keys={
			'supplier_country':'country',
			'supplier_id':'id',
			'supplier_product_name':'product_name'
		};
		for(var i in keys) if(data[i]=='' || data[i]==null) data[i]=data[keys[i]];
		var keys={
			'supplier_cost':'product_cost',
			'supplier_country':'supplier_country',
			'supplier_currency':'cost_currency',
			'supplier_id':'supplier_id',
			'supplier_name':'supplier_name',
			'supplier_product_id':'product_id',
			'supplier_product_name':'supplier_product_name'
		};
		for(var i in keys) if(data[keys[i]]!=null) setValue(i+"["+row+"]",data[keys[i]]);

		setValue('supplier_found['+row+']','y');
		productChange(row,'scost');
		productChange(row,'sfound');
	}
}

function updateAllSuppliers(supplier_name, obj)
{
	var old_val=obj.value;
	obj.value='Please Wait...Updating';
	var supplier_id=getValue('default_supplier');
	var rows=getElement('supplier_table').rows;
	var str='';
	var ids=Array();
	var count=0;
	for(key in rows)
	{
		var n=rows[key].id;
		if(n && n!=null)
		{
			var t1=n.split("[");
			var t2=t1[1].split("]");
			if(!searchArray(t2[0],ids))
			{
				ids[count]=t2[0];
				count++;
			}
		}
	}
	for(key in ids)
	{
		setValue('supplier_name['+ids[key]+']',supplier_name);
		setValue('supplier_id['+ids[key]+']',supplier_id);
		var updateNotes=false;
		if(getValue('def_warranty')!=0)	{setValue('product_warranty['+ids[key]+']',getValue('def_warranty')); updateNotes=true;}
		if(getValue('def_condition')!=0){setValue('product_condition['+ids[key]+']',getValue('def_condition')); updateNotes=true;}
		if(getValue('def_voltage')!=0)	{setValue('product_voltage['+ids[key]+']',getValue('def_voltage')); updateNotes=true;}
		if(getValue('def_plug')!=0)		{setValue('product_plug['+ids[key]+']',getValue('def_plug')); updateNotes=true;}
		if(getValue('def_currency')!="")
		{
			setValue('supplier_currency['+ids[key]+']',getValue('def_currency'));
			productChange(ids[key],'scostus');
			updateNotes=true;
		}
		if(getValue('def_country')!="")
		{
			setValue('supplier_country['+ids[key]+']',getValue('def_country'));
			setValue('supply_country',getValue('def_country'));
		}
		if(getValue('def_packing')!=0)	setValue('product_packing['+ids[key]+']',getValue('def_packing'));
		if(getValue('def_terms')!=0)	setValue('product_terms['+ids[key]+']',getValue('def_terms'));
		if(updateNotes) updateProductNotes(ids[key]);
	}
	obj.value=old_val;
}

function updateProductNotes(prod_count)
{
	var warranty = <?php jecho($warranty_terms)?>;
	var voltage = <?php jecho($voltage_terms)?>;
	var notes = [];
	var val1=getValue('product_voltage['+prod_count+']');
	var val2=getValue('product_plug['+prod_count+']');
	var val3=getValue('product_warranty['+prod_count+']');
	var val4=getValue('product_condition['+prod_count+']');
	var count=0;
	if(val1!=0) { notes[count]='Power Supply Voltage: '+voltage[val1]; count++ }
	if(val2!=0) { notes[count]='Power Adapter ID: '+val2; count++;  }
	if(val3!=0) { notes[count]='Warranty: '+warranty[val3]; count++; }
	if(val4==2) { notes[count]='Condition: Used'; count++; }
	if(getValue('product_updated['+prod_count+']') == 'n') setValue('product_notes['+prod_count+']',notes.join("\n"));
}

function actionPurchasePreview(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="/webadmin/invoice/invoice_purchase_preview.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}

function actionPurchaseOrder(theFrm)
{
	//Do a check first
	var rows=getElement('product_table').rows;
	var ids=Array();
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
	theFrm.action="/webadmin/invoice/?action=do_purchase";
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
	setValue('product_costus['+row_id+']',data['product_cost_us']);
	productChange(row_id,'scost');
	productChange(row_id,'scostus');
}

function updateProductPrice(row)
{
	var product_id=getValue('product_id['+row+']');
	var product_qty=getValue('product_qty['+row+']');
	var generic=getValue('generic['+row+']');
	var site_id=getValue('site_id');
	var member_id=getValue('member_id');
	var currency=getValue('invoice_currency');
	var url = 'invoice_update_price.php?product_id='+product_id;
	if(site_id!=null) url += '&site_id='+site_id;
	if(member_id!=null) url += '&member_id='+member_id;
	if(currency!=null) url+= '&currency='+currency;
	if(product_qty!=null) url += '&product_qty='+product_qty;

	if(generic=='n')
	{
		//now start of the AJAX script.
		if (window.XMLHttpRequest)
		{ // Mozilla, Safari, ...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
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
}

function updatePrices(http_request,row)
{
	//alert(http_request.readyState);
	if (http_request.readyState == 4) {
		if (http_request.status == 200)
		{
			//alert(http_request.responseText);
			var prices=explode(', ',http_request.responseText);
			var price_changed=getValue('product_price_changed['+row+']');
			if(price_changed=='n')
			{
				setValue('product_online['+row+']',prices[0]);
				setValue('product_offer['+row+']',prices[0]);
				setValue('product_offerus['+row+']',prices[1]);
			}
			productChange(row,'pqty');
		}
		else alert('There was a problem with the request.');
	}

}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionInvoiceSuppliers\');">Product Suppliers</a>', '<a href="javascript:toggleSection(\'sectionDefSup\');">Default Supplier Values</a>') ?>

<div id="sectionInvoiceSuppliers">

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

<div id="sectionDefSup" style="display:none;">
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%">Supplier Search :</td>
		<td colspan="2" style="width:50%"><input type="hidden" name="default_supplier" value="<?= $invoice['default_supplier'] ?>" /><input style="width:100%" type="text" name="def_sup_name" value="<?php hecho(getSupplierName($invoice['default_supplier']))?>" /></td>
		<td style="width:25%"><input type="button" value=" Search " onclick="searchDefaultSupplier(getValue('def_sup_name'))"/></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%">Warranty :</td>
		<td style="width:25%">
			<select name="def_warranty"><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $invoice['def_warranty']) ?></select>
		</td>
		<td class="left_title" style="width:25%">Voltage :</td>
		<td style="width:25%">
			<select name="def_voltage"><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $invoice['def_voltage']) ?></select>
		</td>
	</tr>
	<tr>
		<td class="left_title">Plug Type :</td>
		<td style="width:25%"><select name="def_plug"><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $invoice['def_plug']) ?></select></td>
		<td class="left_title">Condition :</td>
		<td><select name="def_condition"><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $invoice['def_condition']) ?></select></td>
	</tr>
	<tr>
		<td class="left_title">Packing :</td>
		<td><select name="def_packing"><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $invoice['def_packing']) ?></select></td>
		<td class="left_title">Terms :</td>
		<td><select name="def_terms"><option value="0">Not Selected</option><?= formatHTMLOptions($shipping_terms, $invoice['def_terms']) ?></select></td>
	</tr>
	<tr>
		<td class="left_title">Currency :</td>
		<td><select name="def_currency"><option value="">Not Selected</option><?= formatHTMLOptions(getCurrencyList(), $invoice['def_currency']) ?></select></td>
		<td class="left_title">Country :</td>
		<td><select name="def_country"><option value="">Not Selected</option><?= formatHTMLOptions(getCountryList(), $invoice['def_country']) ?></select></td>
	</tr>
	<tr><td colspan="4" class="left_title" style="text-align:center"><input type="button" value=" Update Products with Default Values " onclick="updateAllSuppliers(getValue('def_sup_name'),this)" /></td></tr>
</table>
<br />
</div>
<div id="sectionDefSup_hideNote"></div>

<div id="sectionSuppliers">
<table class="wide">
	<tbody id="supplier_table">
<?php
$pcount = 1;
$alt    = true;
foreach ($invoice_products as $product) {
    echo supplier_row($pcount++, $product);
}

?>
	</tbody>
	<tr>
		<th style="width:32px;">PID</th>
		<th style="width:50%;">Product Name</th>
		<th style="width:50%;">Supplier</th>
		<th>&nbsp;</th>
		<th style="width:256px;">Found</th>
		<!--<th style="width:128px;">Used</th>-->
		<th style="width:32px;">Qty</th>
		<th style="width:64px;">Cost</th>
		<th style="width:64px;">Cost USD</th>
	</tr>
	<tr id="supplier_ship_rowa"<?= (0 == $invoice['ship_id']) ? ' style="display:none;"' : '' ?>>
		<td><input type="text" name="supplier_ship_product_id" size="4" value="F<?= str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT) ?><?= ('y' == $invoice['ship_insurance']) ? 'I' : 'N' ?>" disabled="disabled" style="text-align:right;" onchange="shipChange('sid');" /><input type="hidden" name="ship_insurance" value="<?= $invoice['ship_insurance'] ?>" /></td>
		<td><input type="text" name="supplier_ship_product_name" style="width:100%;" value="<?php hecho($invoice['ship_product_name'])?>" onchange="shipChange('sname');" /></td>
		<td colspan="2"><input type="hidden" name="ship_supplier_id" value="<?= intval($invoice['ship_supplier_id']) ?>" /><input type="text" name="ship_supplier_name" value="<?php hecho($invoice['ship_supplier_name'])?>" size="12" style="width:100%;" onchange="shipChange('ssupplier');" /></td>
		<td></td>
		<td><input type="text" name="supplier_ship_qty" size="3" style="text-align:right;" value="1" onchange="shipChange('sqty');" onfocus="this.select();"/></td>
		<td><input type="text" size="6" style="text-align:right;" name="supplier_ship_cost" value="<?= sprintf('%.2f', $invoice['ship_cost']) ?>" onchange="shipChange('scost');" onfocus="this.select();"/></td>
		<td><input type="text" size="6" style="text-align:right;" readonly="readonly" name="ship_costus" value="<?= sprintf('%.2f', $invoice['ship_costus']) ?>" onchange="shipChange('scostus');" /></td>
	</tr>
	<tr class="alt">
		<td colspan="7" style="font-weight:bold;text-align:right;">TOTAL
			<input type="text" name="invoice_cost" readonly="readonly" size="10" value="<?= sprintf('%.2f', $invoice['invoice_cost']) ?>" style="text-align:right;" />
			<input type="hidden" name="invoice_costus" value="<?= sprintf('%.2f', $invoice['invoice_costus']) ?>" style="text-align:right;" />
		</td>
		<td style="font-weight:bold;" name="product_currency"><?= $invoice['invoice_currency'] ?></td>
	</tr>
	<tr class="alt"><td colspan="8"><table style="width:100%;height:100%;border-style:none;">
		<tr><td class="left_title"></td><td>&nbsp;</td></tr>
		<tr>
			<td class="left_title" style="width:25%;vertical-align:top;">Notes</td>
			<td>
				Products highlighted in <span style="color:#FFAA99;">red</span> have no suppliers in the database<br />
				Products highlighted in <span style="color:#6688BB;">blue</span> are available from two or more suppliers<br />
				Products highlighted in <span style="color:#8B00FF;">violet</span> have unknown weights<br />
			</td>
		</tr>
		<tr><td class="left_title"></td><td>&nbsp;</td></tr>
<?php
if ('y' == $invoice['backorder_flag']) {
?>
		<tr><td class="left_title" colspan="2" style="text-align:center;color:red;padding:6px;">Invoice Created as a Backorder, use box below to indicate when new stock arrives.<br />(Once new stock is received, the customer can use the re-checkout feature online)</td></tr>
		<tr>
			<td class="left_title">New Stock Received :</td>
			<td><select name="stock_received" style="width:100%"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['stock_received']) ?></select></td>
		</tr>
<?php
}
?>

		<tr>
			<td class="left_title">Ships From</td>
			<td><select name="supply_country">
				<?= formatHTMLOptions(array('' => '') + invoiceGetOptionList('supply_country'), $invoice['supply_country']) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Products Located</td>
			<?php $colour = 'y' == $invoice['products_located'] ? '#00FF00' : '#FF0000'?>
			<td><select name="products_located" style="background-color:<?= $colour ?>;" onchange="updateSelect(this);"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['products_located']) ?></select></td>
		</tr>
<?php
if ('y' == $invoice['products_located']) {
    $staff = 'Customer';
    if ($invoice['products_staff_id'] > 0) {
        $staff = $all_staff_list[$invoice['products_staff_id']];
    }

?>
		<tr style="color: red;font-weight:bold">
			<td class="left_title">Authorised</td>
			<td><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['products_locate_time']) ?></i></td>
		</tr>
<?php
}
?>
		</table></td></tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionSuppliers_hideNote">
	All Product Suppliers can be viewed by Clicking "Products Suppliers" above<br />
</div>

<!-- /product_supplier.tpl -->
