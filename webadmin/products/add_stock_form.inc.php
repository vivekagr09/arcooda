<?php
global $_GLOBALS;

?>

<script type="text/javascript">
<!--

var intAdminNoteIndex=17;
var intAdminNoteCount=1;

function do_copy_short()
{
	document.admin_form.long_desc.value=document.admin_form.short_desc.value;
}

function refreshCosts()
{
    convert(document.admin_form.cost_price, document.admin_form.cost_currency, document.admin_form.approx_cost_price, document.admin_form.approx_cost_currency);
    //doMarkup();
    setRate(document.admin_form.approx_cost_exch_rate, document.admin_form.cost_currency);
}

function formatDate(inDate)
{
    inDate = new Date(inDate);
    var outDate = inDate.getDate() + "-" + (inDate.getMonth() + 1) + "-" + inDate.getFullYear();
    document.getElementsByName("data3[date_received]").value = outDate;
    //document.write("The date is: " + outDate);
}

function doMarkup()
{
	var approx_cost=document.admin_form.approx_cost_price.value;
	var sales=document.admin_form.sales_price.value;
	var approx_currency=document.admin_form.approx_cost_currency.value;
	var sales_currency=document.admin_form.sales_currency.value;
	var markup_value=parseInt((sales-approx_cost)/approx_cost*10000);
	markup_value=markup_value/100;
	if(approx_currency==sales_currency&&isNaN(markup_value)==false)
		document.admin_form.markup.value=markup_value;
}

function UpdateValues()
{
  	//newWin = window.open('Update.php?id=' + document.getElementsByName("data3[coe_id]")[0].value,'ChildWindow', "fullscreen=no", "directories=no", "location=no", "menubar=no", "resizable=no", "scrollbars=no", "status=no", "titlebar=no", "toolbar=no");
	var url='Update.php?id='+document.getElementsByName("data3[coe_id]")[0].value;
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'ChildWindow',features);
}

function UpdateInitialValue(coeID)
{
    //newWin = window.open('display_stock.php?id=' + coeID,'ChildWindow');
	var url='display_stock.php?id=' + coeID;
	var features='scrollbars, resizable, height=400, width=600';
//	window.open(url,'ChildWindow',features);
}

function reverseMarkup()
{
	var approx_cost=document.admin_form.approx_cost_price.value;
	var sales=document.admin_form.sales_price.value;
	var approx_currency=document.admin_form.approx_cost_currency.value;
	var sales_currency=document.admin_form.sales_currency.value;
	var markup=document.admin_form.markup.value;
	markup = parseFloat(markup/100);
	if(approx_currency==sales_currency)
	{
		var newSales=approx_cost*(1+markup)*100;
		newSales=parseInt(newSales)/100;
		document.admin_form.sales_price.value=newSales;
	}
}

function removeOldNotes()
{
	var theTable=document.getElementById('admin_notes_table');
	if(!theTable) return;
	while(theTable.rows.length>2)
	{
		theTable.deleteRow(theTable.rows.length-1);
		intAdminNoteCount--;
	}
	return;
	while(intAdminNoteCount>1)
	{
		formTable.deleteRow(intAdminNoteIndex+intAdminNoteCount-2);
		intAdminNoteCount--;
	}
	document.getElementsByName("noteCount")[0].value=intAdminNoteCount;
}

function addTableCell(tableRow,innerStuff)
{
	var newCol=document.createElement('td');
	newCol.innerHTML=innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}

function childrenAlert(target)
{
	var theString='';
	var colCount=0;
	for(child in target)
	{
		colCount++;
		if(colCount>6) {theString+="\n";colCount=0;}
		theString+=String(child)+" \t";
	}
	alert(theString);
}

function addColourQty(colourName,colourQty)
{
	var theTable=document.getElementById('colour_qty_table');
	if(!theTable) return;
	var theRow=theTable.insertRow(theTable.rows.length);
	addTableCell(theRow,'Colour <input type="text" name="category_colour[]" value="'+colourName+'" />');
	addTableCell(theRow,'Qty <input type="text" name="category_qty[]" size = "6" value="'+colourQty+'" />');
	addTableCell(theRow,'<input type="button" value="Delete" onclick="javascript:deleteColourQty(this.parentNode.parentNode.rowIndex);" />');
	return;
}

function deleteColourQty(theIndex)
{
	var theTable=document.getElementById('colour_qty_table');
	if(!theTable) return;
	theTable.deleteRow(theIndex);
}

function deleteAllColourQty()
{
	var theTable=document.getElementById('colour_qty_table');
	if(!theTable) return;
	while(theTable.rows.length>0)
		theTable.deleteRow(theTable.rows.length-1);
}

function addProductSize(dimensions, dimensionsQty)
{
	var theTable=document.getElementById('product_size_table');
	if(!theTable) return;
	var theRow=theTable.insertRow(theTable.rows.length);
	addTableCell(theRow,'Size <input type="text" name="category_dimensions[]" value="'+dimensions+'" />');
	addTableCell(theRow,'Qty <input type="text" name="category_dimensions_qty[]" size = "6" value="'+dimensionsQty+'" />');
	addTableCell(theRow,'<input type="button" value="Delete" onclick="javascript:deleteProductSize(this.parentNode.parentNode.rowIndex);" />');
	return;
}

function deleteProductSize(theIndex)
{
	var theTable=document.getElementById('product_size_table');
	if(!theTable) return;
	theTable.deleteRow(theIndex);
}

function deleteAllProductSize()
{
	var theTable=document.getElementById('product_size_table');
	if(!theTable) return;
	while(theTable.rows.length>0)
		theTable.deleteRow(theTable.rows.length-1);
}

//This function adds a new Admin note entry to the form with the products old
//admin notes
function addOldAdminNote(userName,date,note)
{
	var theTable=document.getElementById('admin_notes_table');
	if(!theTable) return;

	var theRow=theTable.insertRow(theTable.rows.length);
	if(intAdminNoteCount&1)
		theRow.style.backgroundColor="#FFFFFF";
	else
		theRow.style.backgroundColor="#EEEEEE";

	var theCell=addTableCell(theRow,date);
	theCell.className="bottomLeft";
	theCell.style.verticalAlign="top";
	theCell.nowrap='true';
	theCell.align="center";

	var theCell=addTableCell(theRow,userName);
	theCell.className="bottomLeft";
	theCell.style.verticalAlign="top";
	theCell.nowrap='true';
	theCell.align="center";

	var theCell=addTableCell(theRow,note);
	theCell.className="bottomLeftRight";

	intAdminNoteCount++;
	return;

	//Create the new row
    var newRow=formTable.insertRow(intAdminNoteIndex);
    //Insert a new cell for info about the note
    var infoCell = newRow.insertCell(0);
    //Add the userName for the admin note & set the name cells properties
    infoCell.nowrap = 'true';
    infoCell.bgColor = '#CCD0F2';
    infoCell.align = 'right';
    infoCell.innerHTML = '<b>Old Admin Note:<br><br>User: </b>' + '<input type ="text" readOnly name = "admin_name['+ intAdminNoteCount + ']" value = "' + userName + '"><br><b>Date: </b><input type = "text" readOnly name = admin_date[' + intAdminNoteCount + ']" value = "' + date + '">';
    //Insert a new cell for the admin note
    var noteCell = newRow.insertCell(1);
    //Add the text for the admin note text
    noteCell.bgColor = '#dfe1f7';
    noteCell.align = 'left';
    noteCell.innerHTML = '<textarea name="admin_note[' + intAdminNoteCount + ']" cols="30" rows="8" style="width:350px"></textarea>';
    //update the admin notes text
    var txt = 'admin_note[' + intAdminNoteCount +']';
    document.getElementsByName(txt)[0].value=note;
    document.getElementsByName(txt)[0].readOnly='true';
    //Increment the counter
    intAdminNoteCount++;
    document.getElementsByName("noteCount")[0].value = intAdminNoteCount;
}

// This function is to reset the admin note table
function resetAdminNotesTable() {
  var theTable=document.getElementById('admin_notes_table');
	if(!theTable) return;

  var defaultCell = '<tr>';
  defaultCell += '<td colspan="3" align="center" class="box"><strong>Admin Notes</strong></td>';
  defaultCell += '</tr>';
  defaultCell += '<tr bgcolor="#E3EBF7">';
  defaultCell += '<td width="10%" align="center" nowrap class="bottomLeft"><strong>Date Added</strong></td>';
  defaultCell += '<td width="5%" align="center" nowrap class="bottomLeft"><strong>User</strong></td>';
  defaultCell += '<td width="85%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>';
  defaultCell += '</tr>';

  theTable.innerHTML = defaultCell;
}

function info()
{
    document.write('The number of rows is: ' + formTable.rows.length + '<BR>');
}
//-->

</script>
<?php addCalendarHeader();?>

<input type="hidden" name="noteCount" value="1" />
<table border="0" align="center" cellpadding="4" cellspacing="0" id="formTable">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  <!--Add in Purchase order no -->
  <tr>
    <td nowrap class="left"><b>Purchase Order No:</b></td>
    <td class="right"> <input type="text" name="data3[purchase_no]" size="40" style="width:300px" value="<?=$product_name;?>">
    </td>
  </tr>
  <!--Add in Stock COD -->
  <tr>
    <td nowrap class="left"><b>Stock COD:</b></td>
    <td class="right"> <input type="text" name="data3[stock_cod]" size="40" style="width:300px" value="<?=$product_name?>">
    </td>
  </tr>
  <!--COE ID -->
  <tr bgcolor="#EEEEE0">
    <td nowrap class="left"><b>COE ID:</b></td>
    <td class="right"><input type="text" name="data3[coe_id]" size="40" style="width:300px">
	<!--Refresh Button -->
	<input type="button" value="Find" onClick="javascript: UpdateValues()" />
    </td>
  </tr>
  <!-- Company Stock Dropdown -->
  <tr>
    <td nowrap class="left"><b>Company Stock :</b></td>
    <td class="right"> <select name="data3[company_stock]" style="width:300px">
<?php
$result = $DB->query("SELECT id, name FROM stock_company_stock");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo '>' . ($row['name']) . '</option>';
}

?>
      </select> <a href="/webadmin/generic_admin.php?table=stock_company_stock&action=add" target="_blank">Add</a>
      | <a href="/webadmin/generic_admin.php?table=stock_company_stock&query=" target="_blank">Edit</a></td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Product Name :</b></td>
    <td class="right"> <input type="text" name="data3[product_name]" size="40" style="width:300px" value="<?=$product_name?>">
    </td>
  </tr>
  <!--Add Product Catagory Dropdown here -->
  <tr>
    <td nowrap class="left"><b>Product Category :</b></td>
    <td class="right"> <select name="data3[product_catagory]" style="width:300px">
<?php
$result = $DB->query("SELECT id, name FROM stock_product_catagories ORDER BY id");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo '>' . ($row['name']) . '</option>';
}

?>
      </select> <a href="/webadmin/generic_admin.php?table=stock_product_catagories&action=add" target="_blank">Add</a>
      | <a href="/webadmin/generic_admin.php?table=stock_product_catagories&query=" target="_blank">Edit</a></td>
  </tr>
  <!--Product Type -->
  <tr>
    <td nowrap class="left"><b>Product Type :</b></td>
    <td class="right"> <select name="data3[product_type]" style="width:300px">
<?php
$result = $DB->query("SELECT id, name FROM product_types ORDER BY name ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $_GLOBALS['site_options']['default_product_type'] == $row['type_name'] ? ' selected ' : '';
    echo '>' . ($row['name']) . '</option>';
}

?>
      </select> <a href="sections_admin.php?table=product_types&action=add" target="_blank">Add</a>
      | <a href="sections_admin.php?table=product_types&query=" target="_blank">Edit</a></td>
  </tr>
  <!-- location -->
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr bgcolor="#EEEEE0">
    <td nowrap class="left"><b>Storage Location :</b></td>
    <td class="right"> <select name="data3[storage]" style="width:300px">
        <option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_storage_location ORDER BY location ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $_GLOBALS['site_options']['default_storage_location'] == $row['location'] ? ' selected ' : '';
    echo '>' . ($row['location']) . '</option>';
}

?>
      </select> <a href="/webadmin/generic_admin.php?table=product_storage_location&action=add" target="_blank">Add</a>
      | <a href="/webadmin/generic_admin.php?table=product_storage_location&query=" target="_blank">Edit</a></td>
  </tr>
  <!--//location -->
  <!--Floor location-->
  <tr>
    <td nowrap class="left"><b>Floor Location :</b></td>
    <td class="right"> <input type="text" name="data3[floor_location]" size="40" style="width:300px" value="<?=$product_name?>">
    </td>
  </tr>
  <!-- country-->
  <tr>
    <td nowrap class="left"><b>Country/Location :</b></td>
    <td class="right"> <select name="data3[country_location]" style="width:300px">
        <option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_country_location ORDER BY name ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $_GLOBALS['site_options']['default_country_location'] == $row['name'] ? ' selected ' : '';
    //put code in around here to check if the value pulled from the database is right, then assign it as selected
    echo '>' . ($row['name']) . '</option>';
}

?>
      </select> <a href="/webadmin/generic_admin.php?table=product_country_location&action=add" target="_blank">Add</a>
      | <a href="/webadmin/generic_admin.php?table=product_country_location&query=" target="_blank">Edit</a></td>
  </tr>
  <!--//country-->
  <tr>
    <td nowrap class="left"><strong>Date Received :</strong></td>
    <td class="right"><input name="data3[date_received]" type="text" id="date_available" style="width:300px" value="<?=date("d-m-Y", time())?>" size="40">
      <?php addCalendarBtn("date_available")?>
    </td>
  </tr>
  <!--//received -->
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <!--Product Details -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Product Details :</b><br><br>
		<!--<font color="red" size="-1">Admin notes are never<br>displayed on the main site</font>--></td>
    <td bgcolor="#EEEEE0" class="right"> <textarea name="data3[product_details]" cols="30" rows="8" style="width:350px"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <!--Admin notes -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Admin Notes :</b><br><br>
		<font color="red" size="-1">Admin notes are never<br>displayed on the main site</font></td>
    <td bgcolor="#EEEEE0" class="right"> <textarea name="admin_notes" cols="30" rows="8" style="width:350px"></textarea>
    </td>
  </tr>
	<tr>
		<td colspan="2" nowrap class="centre">
			<br />
			<table width="500" border="0" cellspacing="0" cellpadding="2" align="center" id="admin_notes_table">
				<tr>
					<td colspan="3" align="center" class="box"><strong>Admin Notes</strong></td>
				</tr>
				<tr bgcolor="#E3EBF7">
					<td width="10%" align="center" nowrap class="bottomLeft"><strong>Date Added</strong></td>
					<td width="5%" align="center" nowrap class="bottomLeft"><strong>User</strong></td>
					<td width="85%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>
				</tr>
			</table>
			<br /><br />
		</td>
	</tr>
  <!--Condition -->
  <tr>
    <td nowrap class="left"><b>Condition :</b></td>
    <td class="right"> <select name="data3[condition]" style="width:300px">
        <option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_conditions");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $_GLOBALS['site_options']['default_condition'] == $row['condition'] ? ' selected ' : '';
    echo '>' . ($row['condition']) . '</option>';
}

?>
      </select> </td>
  </tr>
  <!--Weight (kg) -->
  <tr>
    <td nowrap class="left"><b>Actual Product Weight (kgs):</b></td>
    <td class="right"> <input type="text" name="data3[weight]" size="40" style="width:300px" value="<?=$weight?>">
    </td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Total Weight (kgs):</b></td>
    <td class="right"> <input type="text" name="data3[total_weight]" size="40" style="width:300px" value="<?=$total_weight?>">
    </td>
  </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <!--Supplier -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Supplier :</b></td>
    <td bgcolor="#EEEEE0" class="right"> <select name="data3[supplier]" style="width:300px">
        <option value=""></option>
        <!-- supplier -->
<?php
$result = $DB->query("SELECT id, supplier FROM suppliers ORDER BY supplier ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $row['supplier'] == $_GLOBALS['site_options']['default_supplier'] ? ' selected ' : '';
    echo '>';
    echo stripslashes($row['supplier']) . '</option>';
}
?>
      </select>
      <a href="/webadmin/suppliers_admin.php?table=suppliers&action=add" target="_blank">Add</a>
      <a href="/webadmin/suppliers_admin.php?table=suppliers&query=" target="_blank">Edit</a></td>
      <!-- //supplier -->
    </td>
  </tr>
  <!--Cost Price --><!--Renamed to Unit Price -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Unit Price :</b></td>
    <td bgcolor="#EEEEE0" class="right"> <input type="text" name="data3[unit_price]" id="cost_price" size="10" style="width:70px" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);">
      <!-- cost price input currency -->
      <select name="data3[cost_currency]" style="width:230px" id="cost_currency" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['cost_currency'] ? " selected" : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
      </select>
      <!-- //cost price input currency -->
    </td>
  </tr>
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Approx Cost Price :</b></td>
    <td bgcolor="#EEEEE0" class="right">
	  <input type="hidden" name="data3[approx_cost_price]" id="approx_cost_exch_rate" value="">
      <input type="text" name="approx_cost_price" size="10" id="approx_cost_price" style="width:70px" disabled="disabled" onChange="doMarkup();">
      <!-- sales price input currency -->
      <select name="data3[approx_cost_currency]" id="approx_cost_currency" style="width:230px" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['approx_cost_currency'] ? " selected" : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}

?>
      </select>
      <!-- //sales price input currency -->
    </td>
  </tr>
  <!--
  <tr>
    <td nowrap class="left"><b>Sales Price :</b></td>
    <td class="right"> <input type="text" name="data3[sales_price]" id="sales_price" size="10" style="width:70px" onChange="doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);">
      <!-- sales price input currency
      <select name="data3[sales_currency]" id="sales_currency" style="width:230px" onChange="doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['sales_currency'] ? " selected" : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}

?>
      </select>
      <!-- //sales price input currency
    </td>
  </tr>
  -->
  <!--
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Markup % :</b></td>
    <td bgcolor="#EEEEE0" class="right"> <input type="text" name="data3[markup]" id="markup" size="10" style="width:70px" onChange="reverseMarkup()">
      % </td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Price Flux :</b></td>
    <td class="right">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="20%">
				<select name="data3[price_flux]" style="width:70px">
				<option value="y">Yes</option>
				<option value="n" selected>No</option>
			  </select>
		    </td>
		<td>
		&nbsp;&nbsp;Allow automatic price fluctuation <br>&nbsp;&nbsp;according to curreny exchanges
		</td>
		</tr>
	</table>
	 </td>
  </tr>
  -->
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <!--Make this a Sample check box -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Sample:</b></td>
    <td bgcolor="#EEEEE0" class="right"> <input type="text" name="data3[sample_qty]" size="10" style="width:70px"></td>
  </tr>
  <!--Make this a stock checkbox -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Stock:</b></td>
    <td bgcolor="#EEEEE0" class="right"> <input type="text" name="data3[stock_qty]" size="10" style="width:70px"></td>
  </tr>
  <!--Make this an order checkbox -->
  <tr>
    <td nowrap bgcolor="#EEEEE0" class="left"><b>Order:</b></td>
    <td bgcolor="#EEEEE0" class="right"> <input type="text" name="data3[order_qty]" size="10" style="width:70px"></td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Customer Name:</b></td>
    <td class="right"> <input type="text" name="data3[customer_name]" size="40" style="width:300px">
    </td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Invoice Reference:</b></td>
    <td class="right"> <input type="text" name="data3[invoice_ref]" size="40" style="width:300px">
    </td>
  </tr>
	<tr>
		<td nowrap class="left" valign="top"><b>Colour Quantities </b>(<a href="javascript:addColourQty('','');">Add</a>):</td>
		<td class="right"><table id="colour_qty_table"></table></td>
	</tr>
	<tr>
		<td nowrap class="left" valign="top"><b>Product Size </b>(<a href="javascript:addProductSize('','');">Add</a>):</td>
		<td class="right"><table id="product_size_table"></table></td>
	</tr>
<?php
if (false) {
?>
  <tr>
    <td nowrap class="left"><b>Total Qty:</b></td>
    <td class="right"> <input type="text" name="data3[qty]" size="40" style="width:60px">
    </td>
  </tr>
<?php
}
?>
  <!--Not sure if this is needed or not -->
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left"><b>Active :</b></td>
    <td class="right"> <select name="data3[active]" style="width:70px">
        <option value="y" selected>Yes</option>
        <option value="n">No</option>
      </select> </td>
  </tr>
  <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><input type="radio" name="go_next" value="true" <?php if ('true' == $go_next) {
    echo 'checked';
}
?>></td>
    <td>Add more products</td>
  </tr>
  <tr>
    <td align="right"><input type="radio" name="go_next" value="false" <?php if ('true' != $go_next) {
    echo 'checked';
}
?>></td>
    <td>Go back to previous page</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
        <input type="hidden" name="action" value="do_add">
        <input type="submit" name="do_action" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
</table>
