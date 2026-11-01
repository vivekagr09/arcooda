<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
function product_row($pcount = '[PRODUCT_COUNT]', $product = array())
{
    ob_start();
?>
<tr id="product_rowa[<?=$pcount?>]">
	<td width="5%"><a href="#" onclick="rankProduct(<?=$pcount?>, -1);return false"><img alt="/\" src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="#" onclick="rankProduct(<?=$pcount?>, 1);return false"><img alt="\/" src="/webadmin/images/down_arrow.gif" border="0" /></a></td>
	<td><input type="hidden" name="product_cost[<?=$pcount?>]" value="" /><input type="hidden" name="product_costau[<?=$pcount?>]" value="" /><input name="product_id[<?=$pcount?>]" size="4" style="text-align:right;" type="text" value="<?=$product['product_id']?>" onchange="javascript:productChange(<?=$pcount?>,'pid');" />
	<input type="hidden" name="product_rank[<?=$pcount?>]" value="<?=$pcount?>">
	</td>
	<td><input onclick="javascript:searchProduct(<?=$pcount?>,getValue('product_id[<?=$pcount?>]'));" type="button" value="S" /></td>
	<td><input name="product_name[<?=$pcount?>]" onchange="javascript:productChange(<?=$pcount?>,'pname');" style="width:100%;" type="text" value="<?=htmlspecialchars($product['product_shipping_name'] ?? $product['product_name'])?>" /></td>
	<td><input name="product_qty[<?=$pcount?>]" onchange="javascript:productChange(<?=$pcount?>,'pqty');" size="3" style="text-align:right;" type="text" value="<?=$product['product_qty']?>" /></td>
	<td><input name="product_offer[<?=$pcount?>]" onchange="javascript:productChange(<?=$pcount?>,'poffer');" size="6" style="text-align:right;" type="text" value="<?=$product['product_offer']?>" /></td>
	<td><input name="product_offerau[<?=$pcount?>]" onchange="javascript:productChange(<?=$pcount?>,'pofferau');" readonly="readonly" size="6" style="text-align:right;" type="text" value="<?=$product['product_offerau']?>" /></td>
	<td><input type="hidden" name="prod_count[<?=$pcount?>]" value="<?=$product['prod_count']?>" /><input type="hidden" name="product_weight[<?=$pcount?>]" value="<?=$product['product_weight']?>" /><input type="hidden" name="product_weight_inc[<?=$pcount?>]" value="<?=$product['product_weight_inc']?>" /><input onclick="javascript:deleteProduct(<?=$pcount?>);" type="button" value="Delete" /></td>
</tr>
<?php
return ob_get_clean();
}

function charge_row($ccount = '[CHARGE_COUNT]', $charge = array(), $site_id = 0)
{
    global $DB;
    $options = array();
    $sites   = array(1 => 'HG', 2 => 'HE', 9 => 'CNV', 11 => 'HGR', 16 => 'CARCON');
    if (count($options) == 0) {
        $charges_list = array();
        $query        = $DB->query("SELECT * FROM `highwaygroup`.`charges` " . ((0 != $site_id) ? "WHERE site_id=" . intval($site_id) . " || site_id=0" : "") . " ORDER BY charge_name");
        while ($row = $DB->fetch_assoc($query)) {
            $charges_list[$row['charge_id']] = $row;
        }

        foreach ($charges_list as $key => $row) {
            $options[$row['charge_id']] = 'C0' . $row['charge_id'] . ' - ' . $row['charge_name'] . ((0 == $site_id && 0 != $row['site_id']) ? ' - ' . $sites[$row['site_id']] : '');
        }
    }
    ob_start();
?>
<tr id="product_charge_rowa[<?=$ccount?>]">
	<td></td>
	<td colspan="3"><select name="charge_id[<?=$ccount?>]" onchange="javascript:chargeChange(<?=$ccount?>);"><option value=""></option><?=str_replace("\n", '', formatHTMLOptions($options, $charge['charge_id']))?></select></td>
	<td><input name="charge_qty[<?=$ccount?>]" onchange="javascript:chargeChange(<?=$ccount?>,'pqty');" size="3" style="text-align:right;" type="text" value="1" /></td>
	<td><input name="charge_offer[<?=$ccount?>]" onchange="javascript:chargeChange(<?=$ccount?>,'poffer');" size="6" style="text-align:right;" type="text" value="<?=$charge['charge_offer']?>" /></td>
	<td><input name="charge_offerau[<?=$ccount?>]" onchange="javascript:chargeChange(<?=$ccount?>,'pofferau');" readonly="readonly" size="6" style="text-align:right;" type="text" value="<?=$charge['charge_offerau']?>" /></td>
	<td><input type="hidden" name="charge_count[<?=$ccount?>]" value="<?=$charge['charge_count']?>" /><input onclick="javascript:deleteCharge(<?=$ccount?>);" type="button" value="Delete" /></td>
</tr>
<?php
return ob_get_clean();
}
?>
<script type="text/javascript">
function getProductRows() {
    var product_table = getElement("product_table");
    var product_rows = [];
    if (product_table != null) {
        var theRows = product_table.rows;
        for (var i = 0; i < theRows.length; i++)
            if (theRows[i].id.substr(0, 13) == 'product_rowa[')
                if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                    product_rows[product_rows.length] = theRows[i].id.substr(
                    12);
    }
    return product_rows;
}
function rankProduct(row, direction) {
	console.log(row);
    var from_row = '[' + row + ']';
    var from_rank = Number(getValue("product_rank" + from_row));
    var swap_row = null;
    var swap_rank = null;
    var product_rows = getProductRows();
    for (var i = 0; i < product_rows.length; i++) {
        var check_row = product_rows[i];
        var check_rank = Number(getValue("product_rank" + check_row));
        if (check_row != from_row && ((direction < 0 && check_rank <
                    from_rank && (swap_rank == null || check_rank > swap_rank)
                    ) || (direction > 0 && check_rank > from_rank && (
                    swap_rank == null || check_rank < swap_rank)))) {
            swap_row = check_row;
            swap_rank = check_rank;
        }
    }
    if (swap_row != null) {
        setValue("product_rank" + from_row, swap_rank);
        setValue("product_rank" + swap_row, from_rank);
        if (direction > 0) {
            var temp = from_row;
            from_row = swap_row;
            swap_row = temp;
        }
        var source_row;
        var target_row = getElement("product_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("product_rowa" + from_row)) target_table
            .insertBefore(source_row, target_row);
    }
}
var product_count=<?=max(100, count($sinvoice_products))?>;
var charge_count=<?=max(100, count($sinvoice_charges))?>;

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
foreach (explode("\n", charge_row('[CHARGE_COUNT]', array(), $sinvoice['site_id'])) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . "," . str_replace("[CHARGE_COUNT]", "'+ccount+'", formatJSString(substr($row, 4, -5))) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . "," . str_replace("[CHARGE_COUNT]", "'+ccount+'", formatJSString(substr($row, strpos($row, '>') + 1, -5))) . ").colSpan=" . substr($row, 13, 1) . ";\n";
    }
}
?>
	}
	if(success) charge_count++;
	return success;
}
var product_count = <?=json_encode(max(100, count($invoice_products)))?>;
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
		$("#product_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', json_encode(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'generic' => 'n'))))?>);
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
	setValue('ship_offer','');
	setValue('ship_offerau','');
	var theRow=getElement("product_ship_rowa");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("product_ship_rowb");
	if(theRow) theRow.style.display='none';
	productUpdateTotals();
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
	productChange();
}

function chargeChange(row,change_type)
{
	var do_totals=false;

	do_totals=true;

	switch(change_type)
	{
	case 'pofferau':
		setValue("charge_offer["+row+"]", convertCurrency(getValue("charge_offerau["+row+"]"), "AUD", getValue("invoice_currency"),true));
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
		setValue("product_costau["+row+"]",convertCurrency(getValue("product_cost["+row+"]"),getValue("invoice_currency"),"AUD",true));
		setValue("supplier_product_cost["+row+"]",getValue("product_cost["+row+"]"));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("product_offerau["+row+"]", convertCurrency(getValue("product_offer["+row+"]"), getValue("invoice_currency"), "AUD",true));
		do_totals=true;
		break;
	case 'pofferau':
		setValue("product_offer["+row+"]", convertCurrency(getValue("product_offerau["+row+"]"), "AUD", getValue("invoice_currency"),true));
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
		setValue("product_costau["+row+"]", convertCurrency(getValue("supplier_product_cost["+row+"]"), getValue("invoice_currency"), "AUD",true));
		setValue("product_cost["+row+"]",getValue("supplier_product_cost["+row+"]"));
		do_totals=true;
		break;
	case 'scostau':
		setValue("supplier_product_cost["+row+"]", convertCurrency(getValue("product_costau["+row+"]"), "AUD", getValue("invoice_currency"),true));
		setValue("product_cost["+row+"]",getValue("supplier_product_cost["+row+"]"));
		do_totals=true;
		break;
	default:
		do_totals=true;
		break;
	}

	if(do_totals) productUpdateTotals();
	if(do_located) productUpdateLocated();
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
		setValue("ship_costau",convertCurrency(getValue("ship_cost"),getValue("invoice_currency"),"AUD",true));
		setValue("supplier_ship_cost",getValue("ship_cost"));
		do_totals=true;
		break;
	case 'ponline':
		do_totals=true;
		break;
	case 'poffer':
		setValue("ship_offerau", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "AUD",true));
		do_totals=true;
		break;
	case 'pofferau':
		setValue("ship_offer", convertCurrency(getValue("ship_offerau"), "AUD", getValue("invoice_currency"),true));
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
		setValue("ship_costau", convertCurrency(getValue("supplier_ship_cost"), getValue("invoice_currency"), "AUD",true));
		setValue("ship_cost",getValue("supplier_ship_cost"));
		do_totals=true;
		break;
	case 'scostau':
		setValue("supplier_ship_cost", convertCurrency(getValue("ship_costau["+row+"]"), "AUD", getValue("invoice_currency"),true));
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
	var total_offer=0.0;
	var total_offerau=0.0;
	var total_online=0.0;

	var product_rows=[];
	var charge_rows=[];

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
		var product_offer=Number(getValue("product_offer"+rowNum));
		var product_offerau=Number(getValue("product_offerau"+rowNum));
		addMe=product_qty*product_offer;
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=product_qty*product_offerau;
		if(!isNaN(addMe)) total_offerau+=addMe;
	}

	var products_offer=total_offer;
	var products_offerau=total_offerau;
	setAllValues('products_offer',convertCurrency(products_offer.toFixed(2),in_cur,'',true));
	setAllValues('products_offerau',convertCurrency(products_offerau.toFixed(2),'AUD','',true));

	if(getValue('ship_id')>0)
	{
		addMe=Number(getValue('ship_offer'));
		if(!isNaN(addMe)) total_offer+=addMe;
		addMe=Number(getValue('ship_offerau'));
		if(!isNaN(addMe)) total_offerau+=addMe;
	}

	var ship_offer=total_offer-products_offer;
	var ship_offerau=total_offerau-products_offerau;

	//Hack
	total_offerau=Number(total_offerau);

<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$query            = $DB->query("SELECT * FROM `highwaygroup`.`charges` ORDER BY charge_priority ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_amount[$row['charge_id']]   = $row['charge_amount'];
    $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
    $charges_percent[$row['charge_id']]  = $row['charge_percent'];
    $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
}
?>
	var charges_ids=<?=formatJSArray(array_keys($charges_amount))?>;
	var charges_amount=<?=formatJSArray(array_values($charges_amount))?>;
	var charges_currency=<?=formatJSArray(array_values($charges_currency))?>;
	var charges_percent=<?=formatJSArray(array_values($charges_percent))?>;
	var charges_rounding=<?=formatJSArray(array_values($charges_rounding))?>;

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
				var charge_amount_au='0.00';

				if(rounding==0.0 && percent==0.0 && amount=='0.00')
				{
					charge_amount=getValue('charge_offer'+rowNum);
					charge_amount_au=convertCurrency(getValue('charge_offer'+rowNum),getValue('invoice_currency'),'AUD',true);
				}
				else
				{
					charge_amount=Number(convertCurrency(amount,currency,getValue('invoice_currency'),true));
					charge_amount+=Number(total_offer)*percent;

					if(rounding!=0.0)
						charge_amount=(Math.ceil((total_offer+charge_amount)/rounding)*rounding)-total_offer;

					charge_amount=Number(charge_amount).toFixed(2);
					charge_amount_au=convertCurrency(charge_amount,getValue('invoice_currency'),'AUD',true);

					setValue('charge_offer'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
					setValue('charge_offerau'+rowNum,convertCurrency(charge_amount_au,'AUD','',true));
				}

				total_offer+=Number(charge_amount);
				total_offerau+=Number(charge_amount_au);
			}
		}
	}
	
	var charges_offer=total_offer-(products_offer+ship_offer);
	var charges_offerau=total_offerau-(products_offerau+ship_offerau);
	setAllValues('charges_offer',convertCurrency(Number(charges_offer).toFixed(2),in_cur,'',true));
	setAllValues('charges_offerau',convertCurrency(Number(charges_offerau).toFixed(2),'AUD','',true));

	setAllValues('invoice_offer',convertCurrency(Number(total_offer).toFixed(2),in_cur,'',false));
	setAllValues('invoice_offerau',convertCurrency(Number(total_offerau).toFixed(2),'AUD','',false));
}

function searchProduct(row,searchFor)
{
	var currency=getValue('invoice_currency');
	var site_id=getValue('site_id');
	if(site_id=='1') site_id='1|2';
//	var url='/webadmin/products/products_search.php?action=do_search';
//	url+='&query='+searchFor;
	var url='/webadmin/products/?action=list';
	url+='&return_message=select_'+row;
	url+='&search_for='+escape(searchFor);
	if(currency!=null) url+='&currency='+currency;
	if(site_id!=null) url+='&site_id='+site_id;
	var features='scrollbars, resizable';//height=700, width=800
	window.open(url,'product_popup',features);
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

		setValue('product_id['+row+']',			data['product_id']);
		setValue('product_name['+row+']',		data['product_name']);
		setValue('product_qty['+row+']',		data['min_order']);
		setValue('product_offer['+row+']',		data['product_offer']);
		setValue('product_offerau['+row+']',	convertCurrency(data['product_offer'], data['sales_currency'], 'AUD'));
		setValue('product_weight['+row+']',		data['product_weight']);
		setValue('product_weight_inc['+row+']',	data['product_weight_inc']);
	}

	productUpdateWeight();
	productChange(row);
}
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProducts\');">Product Details</a>')?>
<div id="sectionProducts">

<table class="wide">
	<tr>
		<th>&nbsp;</th>
		<th style="width:32px;">PID</th>
		<th>&nbsp;</th>
		<th style="width:100%;">Product Name</th>
		<th style="width:32px;">Qty.</th>
		<th style="width:64px;">Offer Price (<span name="product_currency"><?=htmlspecialchars($sinvoice['invoice_currency'])?></span>)</th>
		<th style="width:64px;">Offer AUD</th>
		<th style="width:64px;">&nbsp;</th>
	</tr>
	<tbody id="product_table" name="product_table">
	<?php
$pcount = 1;
foreach ($sinvoice_products as $product) {
    echo product_row($pcount++, $product);
}

?>
	</tbody>
	<tr id="product_ship_rowa"<?=(0 == $sinvoice['ship_id'] && 'Pick-Up' != $sinvoice['ship_product_name']) ? ' style="display:none;"' : ''?>>
		<td></td>
		<td><input type="hidden" name="ship_id" value="<?=htmlspecialchars($sinvoice['ship_id'])?>" /><input type="text" name="ship_product_id" size="4" value="F<?=str_pad($sinvoice['ship_id'], 2, '0', STR_PAD_LEFT)?><?=('y' == $sinvoice['ship_insurance']) ? 'I' : 'N'?>" disabled="disabled" style="text-align:right;" /></td>
		<td colspan="2"><input name="ship_product_name" style="width:100%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_product_name'])?>" onchange="javascript:shipChange('pname');" /></td>
		<td><input name="ship_qty" size="3" style="text-align:right;" type="text" value="1" onchange="javascript:shipChange('pqty');" /></td>
		<td><input name="ship_offer" size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $sinvoice['ship_offer'])?>" onchange="javascript:shipChange('poffer');" /></td>
		<td><input name="ship_offerau" size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $sinvoice['ship_offerau'])?>" onchange="javascript:shipChange('pofferau');" readonly="readonly" /></td>
		<td><input onclick="javascript:deleteFreight();" type="button" value="Delete" /></td>
	</tr>
	<tbody id="charges_table" name="charges_table">
	<?php
$ccount = 1;
foreach ($sinvoice_charges as $charge) {
    echo charge_row($ccount++, $charge);
}

?>
	</tbody>
	<tr class="alt">
		<td colspan="4">&nbsp;</td>
		<td style="font-weight:bold;">TOTAL</td>
		<td><input type="text" name="invoice_offer" readonly="readonly" size="6" value="<?=sprintf('%.2f', $sinvoice['invoice_offer'])?>" style="text-align:right;" /></td>
		<td><input type="text" name="invoice_offerau" readonly="readonly" size="6" value="<?=sprintf('%.2f', $sinvoice['invoice_offerau'])?>" style="text-align:right;" /></td>
		<td style="font-weight:bold;" name="product_currency"><?=$sinvoice['invoice_currency']?></td>
	</tr>
	<tr class="alt">
		<td colspan="8" style="text-align:center;">
			<input onclick="javascript:addProduct();" type="button" value="Add Product" />
			<input onclick="javascript:addCharge();" type="button" value="Add Charge" />
			<input type="hidden" name="products_offer" value="<?=sprintf('%.2f', $sinvoice['products_offer'])?>" />
			<input type="hidden" name="products_offerau" value="<?=sprintf('%.2f', $sinvoice['products_offerau'])?>" />
			<input type="hidden" name="charges_offer" value="<?=sprintf('%.2f', $sinvoice['charges_offer'])?>" />
			<input type="hidden" name="charges_offerau" value="<?=sprintf('%.2f', $sinvoice['charges_offerau'])?>" />
		</td>
	</tr>
</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
