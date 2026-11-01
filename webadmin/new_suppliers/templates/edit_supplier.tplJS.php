<?php
/*********************************************************
File created by Jwalit Shah - 22/09/05
New Supplier add page...
*********************************************************/
?>
<?php
if(!(isset($mode) || $mode == "edit" || $mode == "add"))
{
	$mode = "edit";
}
if($mode == 'add'){
	$supplier['contacts'] = array();
	$supplier['warehouse'] = array();
	$supplier['payments'] = array();
}
$next_row_id=1;
?>
<script language="Javascript" src="../expandToggle.js" type="text/Javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
var edit_contact_id = <?= ($supplier['contacts'][0]['contact_id']) ? $supplier['contacts'][0]['contact_id'] : 0;?>;
var next_row_id = <?=$next_row_id?>;

// Simple script for changing values of a text box based on a select box.
// 20040921: Expanded to include 2 variables so we can set ID's in links too =[
function change_email(){
	// Get Email form select box
	var box = document.forms[0].edit_contact;
	if(!box.options.length) return;
	var text = box.options[box.selectedIndex].value;
	var email = text.substring(0, text.indexOf(":"));
	var id = text.substring(text.indexOf(":") + 1, text.length);
	if (!email) return;
	// Set Text Box
	document.forms[0].contact_email.value = email;
	edit_contact_id = id;
}

function valid(form){
	supp_name = form.supplier_name.value;
	if(supp_name == ""){
		alert("Please enter a Supplier Name");
		form.supplier_name.select();
		form.supplier_name.focus();
		return false;
	}
	else
		return true;
}


function popup(url){
	window.open(url + "&popup=1", "popup", "menubar=no,toolbar=no,status=no,height=150,width=400");
	return false;
}

function update_contacts(update_data){
	var sel = document.getElementById("edit_contact")
	var at = sel.selectedIndex
	sel.selectedIndex = 0;
	var j = 0;

	// Remove Current Contacts
	for(var i = 0; i < sel.options.length; i++){
		sel.remove(i);
	}

	// Stick in new ones
	for(var i = 0; i < update_data.length; i++){
		var name = update_data[i++];
		var email = update_data[i++];
		var id = update_data[i];
		sel.options[j] = new Option(name, email+":"+id);
		sel.options[j++].innerHTML = name;
	}

	sel.selectedIndex = at;
	change_email();
}
function copy_address(){
	document.getElementById('warehouse[0][company]').value = document.getElementById('company').value;
	document.getElementById('warehouse[0][phone]').value = document.getElementById('phone').value;
	document.getElementById('warehouse[0][address]').value = document.getElementById('address').value;
	document.getElementById('warehouse[0][address2]').value = document.getElementById('address2').value;
	document.getElementById('warehouse[0][fax]').value = document.getElementById('fax').value;
	document.getElementById('warehouse[0][city]').value = document.getElementById('city').value;
	document.getElementById('warehouse[0][website]').value = document.getElementById('website').value;
	document.getElementById('warehouse[0][country]').value = document.getElementById('country').value;
	document.getElementById('warehouse[0][zip]').value = document.getElementById('zip').value;
	document.getElementById('warehouse[0][email]').value = document.getElementById('sup_email').value;
}

function delContact(c_row_id,type){
	var orow = type;
	var question = "Are you sure you wish to delete this value?";
	var row_id = orow + "_" + c_row_id;
	//alert("Row_id = "+row_id);
	if(confirm(question)){
		var row = document.getElementById(row_id);
		if(!row) row = document.getElementById(orow);
		var tbody = row.parentNode;
		if(tbody.rows.length > 3){
			// load a page to actually delete the id from the database....
			var con = "";
			var src = "";
			if(type == "contact_row"){
				con = 'contact['+c_row_id+'][id]';
				src = "?action=delete_contact&type=contacts&id=";
			}
			if(type == "payment_row"){
				con = 'pay['+c_row_id+'][id]';
				src = "?action=delete_contact&type=payment&id=";
			}
			var doc = document.getElementById(con);
			if(doc){
				src += doc.value
				var dif = popup(src);
			}
			tbody.removeChild(row);
			// now have to update the hidden fields
			if(type == 'payment_row')
				h_field = document.getElementById('pay_method');
			else
				h_field = document.getElementById('contacts');

			var old = h_field.value;
			old--;
			h_field.value = old;
		}
		else
			alert("At least one set of contact details is required.");// Only one contact left.
	}
}

function add_contact(){
	next_row_id = document.getElementById('contacts').value;
	//next_row_id++;
	var theTable=document.getElementById('contact');
	var theRow=theTable.insertRow(theTable.rows.length);
	theRow.id="contact_row_"+next_row_id;
	var theCol1=document.createElement('TD');
	theCol1.innerHTML='<input type="text" class="intext" name="contact['+next_row_id+'][name]" value="" /><input type="hidden" name="contact['+next_row_id+'][contact_id]" value="" />';
	theRow.appendChild(theCol1);
	var theCol3=document.createElement('TD');
	theCol3.innerHTML='<input type="text" class="intext" name="contact['+next_row_id+'][title]" value="" />';
	theRow.appendChild(theCol3);
	var theCol4=document.createElement('TD');
	theCol4.innerHTML='<input type="text" class="intext" name="contact['+next_row_id+'][email]" value="" />';
	theRow.appendChild(theCol4);
	var theCol5=document.createElement('TD');
	theCol5.innerHTML='<input type="text" class="intext" name="contact['+next_row_id+'][phone]" value="" />';
	theRow.appendChild(theCol5);
	var theCol6=document.createElement('TD');
	theCol6.style.width="2%";
	theCol6.innerHTML='<a href = "#" onclick="javascript: delContact('+next_row_id+',\'contact_row\'); return false;">Delete</a>';
	theRow.appendChild(theCol6);
	increment('contacts');
	//next_row_id++;
}

//var w_address=1;
function cloneRow(type,table){
	w_address = document.getElementById('w_address').value;
	var theTable=document.getElementById(table);

	var theRow=addTableRow(theTable,'clone_me_'+w_address);
	var theCell=addTableCell(theRow,'');
	var theInnerTable=addTable(theCell);

	theRow.style.display='block';
	theInnerTable.style.width="100%";
	theInnerTable.className="invisible";

	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'Company: ').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][company]" style="width:300px" value="">').style.width='300px';
	addTableCell(theRow,'Phone: ').className='left_title';
	addTableCell(theRow,'<input style="width:200px" name="warehouse['+w_address+'][phone]" type="text" class="intext" value="">');

	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'Office Address: ').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][address]" style="width:300px" value="">');
	addTableCell(theRow,'Fax: ').className='left_title';
	addTableCell(theRow,'<input style="width:200px" name="warehouse['+w_address+'][fax]" type="text" class="intext" value="">');

	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'&nbsp;').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][address2]" style="width: 300px" value="">');
	addTableCell(theRow,'Website: ').className='left_title';
	addTableCell(theRow,'<input name="warehouse['+w_address+'][website]" type="text" class="intext" value="http://"/>');

	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'City: ').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][city]" style="width: 300px" value="">');
	addTableCell(theRow,'&nbsp;').className='left_title';
	addTableCell(theRow,'&nbsp;');

	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'Post Code:').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][zip]" style="width: 300px" value="">');
	addTableCell(theRow,'&nbsp;').className='left_title';
	addTableCell(theRow,'&nbsp;');
	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'Country:').className='left_title';
	addTableCell(theRow,'<select name="warehouse['+w_address+'][country]"><option value="">[SELECT ONE]</option><?php
foreach($countries as $c): ?><option value="<?= $c['code']; ?>"><?= addslashes(trim($c['name'])); ?></option><?php
endforeach; ?></select>');
	addTableCell(theRow,'Email: ').className='left_title';
	addTableCell(theRow,'<input type="text" name="warehouse['+w_address+'][email]" size=40 style="width: 320px" value="">');
	theRow=addTableRow(theInnerTable,'');
	addTableCell(theRow,'Comments:').className='left_title';
	addTableCell(theRow,'<textarea name="warehouse['+w_address+'][comments]" rows=6></textarea>').colSpan=3;

	theCell.appendChild(theInnerTable);
	theRow.setAttribute("style","display:inline-table");

	increment('w_address');
}

function addTable(theParent){
	var newTable=document.createElement('table');
	theParent.appendChild(newTable);
	return newTable;
}
function addTableRow(theTable,newName){
	var newRow=theTable.insertRow(theTable.rows.length);
	newRow.name=newName;
	newRow.id=newName;
	return newRow;
}

function addTableCell(tableRow,innerStuff){
	var newCol=document.createElement('td');
	//newCol.setAttribute(att,val);
	newCol.innerHTML=innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}

// Copied from Nathan's processCellDOM function in trcloner.js file...
function empty(newRow){
	for(var i = 0; i < newRow.childNodes.length; i++)
	{
		var child = newRow.childNodes[i];
		if(child.childNodes.length) empty(child);
		if(child.value && (child.type == "text")) child.value = "";
		if(child.value && (child.type == "hidden")) child.value = "";
		if(child.checked && (child.type == "checkbox")) child.checked = "";
	}
}
function addPayMethod(){
	pay = document.getElementById('pay_method').value;
	var theTable=document.getElementById('payment');
	var theRow=theTable.insertRow(theTable.rows.length);
	theRow.id="payment_row_"+pay;
	var theCol1=document.createElement('TD');
	theCol1.innerHTML = '<select onChange="javascript:changePay(this,'+pay+')" name="pay['+pay+'][type]" id="pay['+pay+'][type]"><option value="cc">Credit Card</option><option value="bnk" >Bank Deposit</option><option value="jyp">JYP - Japanese Yen</option><option value="rmb">RMB - Chinese Renminbi</option><option value="gbp">GBP - Great Britan Pound</option><option value="eur">EUR - European Euros</option><option value="usd">USD - US Dollar</option><option value="aud">AUD - Australian Dollar</option></select>';
	theRow.appendChild(theCol1);
	var theCol2=document.createElement('TD');
	theCol2.innerHTML = '<textarea onfocus="if(this.value == \'Please Enter Bank Details:\') this.value=\'\';" name="pay['+pay+'][details]" id="pay['+pay+'][details]" rows="10" class="intext" ></textarea>';
	theCol2.width="40%";
	theRow.appendChild(theCol2);
	var theCol3=document.createElement('TD');
	theCol3.innerHTML = 'Notes: <input id="pay['+pay+'][notes]" name="pay['+pay+'][notes]" type="text" class="intext" value="" />';
	theCol3.width="40%";
	theRow.appendChild(theCol3);
	var theCol4=document.createElement('TD');
	theCol4.innerHTML = '<a href = "#" onclick="javascript: delContact('+pay+',\'payment_row\'); return false;">Delete</a>';
	theCol4.width="1%";
	theRow.appendChild(theCol4);
	increment('pay_method');
}

function changePay(select,num){
	var type = 'pay['+num+'][details]';
	details = document.getElementById(type);
	if(details.value == ""){
		if(select.value == "bnk")
			details.value = "Please Enter Bank Details:";
	}
}

function increment(type){
	h_field = document.getElementById(type);
	var old = h_field.value;
	old++;
	h_field.value = old;
}

// Test function to view values in the hidden fields...
function viewHiddenThings(){
	h_field1 = document.getElementById('pay_method');
	h_field2 = document.getElementById('w_address');
	h_field3 = document.getElementById('contacts');

	//view warehouse addresses...
	/*var str = "";
	for(i = 0; i <= w_address;i++){
		var w = 'warehouse['+i+'][company]';
		var w1 = 'warehouse['+i+'][address]';
		var w2 = 'warehouse['+i+'][address2]';
		var w3 = 'warehouse['+i+'][phone]';
		var w4 = 'warehouse['+i+'][fax]';
		var w5 = 'warehouse['+i+'][city]';
		var w6 = 'warehouse['+i+'][country]';
		var w7 = 'warehouse['+i+'][zip]';
		var w8 = 'warehouse['+i+'][email]';
		var w9 = 'warehouse['+i+'][comments]';
		var w10 = 'warehouse['+i+'][website]';
		//alert("W = "+w+", "+w1+", "+w2+", "+w3+", "+w4+", "+w5+", "+w6+", "+w7+", "+w8+", "+w9+", "+w10);
		var wa = document.getElementById(w);
		var wb = document.getElementById(w1);
		var wc = document.getElementById(w2);
		var wd = document.getElementById(w3);
		var we = document.getElementById(w4);
		var wf = document.getElementById(w5);
		var wg = document.getElementById(w6);
		var wh = document.getElementById(w7);
		var wi = document.getElementById(w8);
		var wj = document.getElementById(w9);
		var wk = document.getElementById(w10);
		str = "Company"+i+" = "+wa.value+", "+wb.value+", "+wc.value+", "+wd.value+", "+we.value+", "+wf.value+", "+wg.value+", "+wh.value+", "+wi.value+", "+wj.value+", "+wk.value+"\n";
		alert(str);
	}
	//alert(str);*/

	alert("Contacts = "+h_field3.value+", Warehouse addresses = "+h_field2.value+" & Pay Methods = "+h_field1.value);
}
// -->
</script>
<form accept-charset="utf-8" name="add_edit_supplier" method="post" action="<?= $form_dest; ?>" onSubmit="return valid(this)">
	<input name="id" type="hidden" value="<?= $supplier['supplier_id']; ?>">
	<input name="supplier_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="contacts_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="types_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input id="contacts" name="contacts" type="hidden" value="0"><input id="w_address" name="w_address" type="hidden" value="0"><input name="pay_method" id="pay_method" type="hidden" value="0">
	<div class="flagHeader">
		<span>
			English Details
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span><a href="#" onclick="expandIt('chinese'); return false" style="color:white">Chinese Details</a></span>
	</div>
	<br />
	<table width="100%">
		<tr>
          <td class="left_title">Supplier Name:</td>
          <td><input name="supplier_name" type="text" class="intext" value="<?= $supplier['supplier_name']; ?>" /></td>
	  </tr>
	</table><br>
	<table>
		<tr>
			<th class="left_title" style="text-align:left">Head Office Details</th>
		</tr>
	</table>
	<table>
		<tr>
			<td class="left_title">Company: </td>
			<td>
				<input type="text" name="company" style="width:300px" value="<?= $supplier['factory_name']; ?>">
			</td>
			<td class="left_title">Phone:</td>
			<td>
				<input style="width:200px" name="phone" type="text" class="intext" value="<?= $supplier['phone']; ?>">
			</td>
		</tr>
		<tr>
			<td class="left_title">Office Address:</td>
			<td>
				<input type="text" name="address" style="width:300px" value="<?= $supplier['address']; ?>">
			</td>
			<td class="left_title">Fax:</td>
			<td>
				<input style="width:200px" name="fax" type="text" class="intext" value="<?= $supplier['fax']; ?>">
			</td>
		</tr>
		<tr>
			<td class="left_title">&nbsp;</td>
			<td><input type="text" name="address2" style="width: 300px" value="<?= $supplier['address2']; ?>"></td>
			<td class="left_title">Website:</td>
			<td><input name="website" type="text" class="intext" value="<?= $supplier['website']?$supplier['website']:"http://"; ?>" /></td>
		</tr>
		<tr>
			<td class="left_title">City: </td>
			<td><input type="text" name="city" style="width: 300px" value="<?= $supplier['city']; ?>"></td>
			<td class="left_title">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Post Code: </td>
			<td><input type="text" name="zip" style="width: 300px" value="<?= $supplier['pcode']; ?>"></td>
			<td class="left_title">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Country:</td>
			<td>
				<select name="country" style="width:300px">
					<option value="">[SELECT ONE]</option>
<?php
foreach($countries as $c): ?>
					<option value="<?= $c['code']; ?>"<?= ($c['code'] == $supplier['country_code']) ? " selected" : "";?>><?= trim($c['name']); ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="left_title">Email</td>
			<td><input type="text" name="sup_email" style="width: 320px" value="<?=$supplier['email']?>"></td>
		</tr>
	</table>
	  <br />
	<br />
	<div id="chineseChild" style="display:none">
		<div class="flagHeader">
			<span><a href="#" onclick="expandIt('chinese'); return false" style="color:white">Chinese Details</a></span>
		</div>
		<br>
	  <table>
		<tr>
          <td class="left_title" style="font-weight: normal;">&#20379;&#25033;&#21830;&#21517;&#31281;:</td><!--Supplier Name **-->
          <td><input name="supplier_name_ch" type="text" class="intext" value="<?= $supplier['supplier_name_ch']; ?>" /></td>
	  </tr>
	</table><br>
	<table>
		<tr>
			<th class="left_title" style="font-weight: normal;text-align:left">&#32317;&#20844;&#21496;&#35443;&#32048;&#36039;&#26009;</th><!--Head Office Details **-->
		</tr>
	</table>
	<table>
		<tr>
			<td class="left_title" style="font-weight: normal;">&#20844;&#21496;: </td> <!--Company **-->
			<td>
				<input type="text" name="company_ch" size=40 style="width:300px" value="<?= $supplier['factory_name_ch']; ?>">
			</td>
			<td class="left_title" style="font-weight: normal;">&#38651;&#35441;:</td><!--Phone  **-->
			<td>
				<input style="width:200px" name="phone_ch" type="text" class="intext" value="<?= $supplier['phone']; ?>">
			</td>
		</tr>
		<tr>
			<td class="left_title" style="font-weight: normal;">&#36774;&#20844;&#23460;&#22320;&#22336;:</td> <!-- Office Address **-->
			<td>
				<input type="text" name="address_ch" size=40 style="width:300px" value="<?= $supplier['address_ch']; ?>">
			</td>
			<td class="left_title" style="font-weight: normal;">&#20659;&#30495;</td> <!-- Fax **-->
			<td>
				<input style="width:200px" name="fax_ch" type="text" class="intext" value="<?= $supplier['fax']; ?>">
			</td>
		</tr>
		<tr>
			<td class="left_title" style="font-weight: normal;">&nbsp;</td>
			<td><input type="text" name="address2_ch" size=40 style="width: 300px" value="<?= $supplier['address2_ch']; ?>"></td>
			<td class="left_title" style="font-weight: normal;">&#32178;&#31449;:</td> <!-- Website **-->
			<td><input name="website_ch" type="text" class="intext" value="<?= $supplier['website']?$supplier['website']:"http://"; ?>" /></td>
		</tr>
		<tr>
			<td class="left_title" style="font-weight: normal;">&#22478;&#24066;: </td><!-- City **-->
			<td><input type="text" name="city_ch" size=40 style="width: 300px" value="<?=$supplier['city_ch']?>"></td>
			<td class="left_title">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title" style="font-weight: normal;">&#37109;&#36958;&#21312;&#34399;:</td> <!-- Post Code **-->
			<td><input type="text" name="zip_ch" size=40 style="width: 300px" value="<?=$supplier['pcode']?>"></td>
			<td class="left_title">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title" style="font-weight: normal;">&#22283;&#23478;:</td> <!-- Country **-->
			<td>
				<select name="country_ch" style="width:300px">
					<option value="">[SELECT ONE]</option>
<?php
foreach($countries as $c): ?>
					<option value="<?= $c['code']; ?>"<?= ($c['code'] == $supplier['country_code']) ? " selected" : "";?>><?= trim($c['name']); ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="left_title" style="font-weight: normal;">&#37109;&#31665;:</td><!-- Email **-->
			<td><input type="text" name="email_ch" size=40 style="width: 320px" value="<?=$supplier['email']?>"></td>
		</tr>
	</table><br>
	</div>
		<table id="contact" class="vbox">
		<tr>
			<td colspan=4 class="left_title" style="text-align:left"><a href="#" style="color:black" onclick="javascript:add_contact();return false">Add New Contact</a></td>
		</tr>
		<tr>
			<th width="20%">Name</th>
			<th width="20%">Title/Position</th>
			<th width="35%">email</th>
			<th width="15%">Phone</th>
		</tr>
<?php
$ea = array_values($supplier['contacts']);
		for($i = 0; $i < (count($ea) ? count($ea) : 1); $i++): ?>
			<tr id="contact_row_<?=$i; ?>"<?= $i % 2 ? " class=\"alt\"" : ""; ?>>
				<td>
					<input type="text" class="intext" name="contact[<?=$i?>][name]" value="<?= $ea[$i]['contact_name']; ?>" />
				</td>
				<td><input type="text" class="intext" name="contact[<?= $i?>][title]" value="<?= $ea[$i]['contact_position']; ?>" /></td>
				<td><input type="text" class="intext" name="contact[<?= $i?>][email]" value="<?= $ea[$i]['contact_email']; ?>" /></td>
				<td><input type="text" class="intext" name="contact[<?= $i?>][phone]" value="<?= $ea[$i]['contact_phone']; ?>" /></td>
				<td width="2%"><input name="contact[<?= $i?>][id]" type="hidden" value="<?=$ea[$i]['contact_id']?>"><a href = "#" onclick="javascript: delContact(<?=$i;?>,'contact_row'); return false;">Delete</a></td>
			</tr>
		<?= '<script>next_row_id++;increment(\'contacts\');</script>'; endfor; ?>
	</table>
	  <br />
	  <br />
	  <div class="flagHeader">
		<span>Warehouse Address</span>
		<a href="#" onclick="javascript: copy_address(); return false;">Copy from Above</a> |
		<a href="#" onclick="javascript: cloneRow('clone_me_0','warehouse'); return false;">Add New Address</a>
	</div>
	<br>
	<table class="invisible" id="warehouse">
<?php
$w = array_values($supplier['warehouse']);
				for($i = 0; $i < (count($w) ? count($w) : 1); $i++): ?>
		<tr class="invisible" id="clone_me_<?=$i?>">
			<td>
				<table width="95%" class="invisible" align="left">
					<tr>
						<td class="left_title">Company: </td>
						<td>
							<input type="hidden" name="warehouse[<?=$i?>][id]" value="<?= $w[$i]['id']; ?>" readonly>
							<input type="text" name="warehouse[<?=$i?>][company]" size=40 style="width:300px" value="<?= $w[$i]['company']; ?>">
						</td>
						<td class="left_title">Phone:</td>
						<td>
							<input style="width:200px" name="warehouse[<?=$i?>][phone]" type="text" class="intext" value="<?=$w[$i]['phone']; ?>">
						</td>
					</tr>
					<tr>
						<td class="left_title">Office Address:</td>
						<td>
							<input type="text" name="warehouse[<?=$i?>][address]" size=40 style="width:300px" value="<?= $w[$i]['address']; ?>">
						</td>
						<td class="left_title">Fax:</td>
						<td>
							<input style="width:200px" name="warehouse[<?=$i?>][fax]" type="text" class="intext" value="<?= $w[$i]['fax']; ?>">
						</td>
					</tr>
					<tr>
						<td class="left_title">&nbsp;</td>
						<td><input type="text" name="warehouse[<?=$i?>][address2]" size=40 style="width: 300px" value="<?=$w[$i]['address2']?>"></td>
						<td class="left_title">Website:</td>
						<td><input name="warehouse[<?=$i?>][website]" type="text" class="intext" value="<?= $w[$i]['website']?$w[$i]['website']:"http://"; ?>" /></td>
					</tr>
					<tr>
						<td class="left_title">City: </td>
						<td><input type="text" name="warehouse[<?=$i?>][city]" size=40 style="width: 300px" value="<?=$w[$i]['city']?>"></td>
						<td class="left_title">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="left_title">Post Code: </td>
						<td><input type="text" name="warehouse[<?=$i?>][zip]" size=40 style="width: 300px" value="<?=$w[$i]['zip']?>"></td>
						<td class="left_title">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="left_title">Country:</td>
						<td>
							<select name="warehouse[<?=$i?>][country]" style="width: 300px">
								<option value="">[SELECT ONE]</option>
<?php
foreach($countries as $c): ?>
								<option value="<?= $c['code']; ?>"<?= ($c['code'] == $w[$i]['country']) ? " selected" : "";?>><?= trim($c['name']); ?></option>
<?php
endforeach; ?>
							</select>
						</td>
						<td class="left_title">Email</td>
						<td><input type="text" name="warehouse[<?=$i?>][email]" size=40 style="width: 320px" value="<?=$w[$i]['email']?>"></td>
					</tr>
					<tr>
						<td class="left_title">
							Comments:
						</td>
						<td colspan=3><textarea name="warehouse[<?=$i?>][comments]" rows=6><?=$w[$i]['comments']?></textarea></td>
					</tr>
				</table>
			</td>
		</tr>
		<?= '<script>increment(\'w_address\');</script>';endfor; ?>
	</table>
	<br>
	<br>
	<div class="flagHeader">
		<span>
			Common Details
		</span>
	</div>
	<br />
	  <table>
		<tr>
			<td class="left_title">Supplier for:</td>
			<td>
				  <input name="mach" type="checkbox" value="on"<?= $supplier['mach']; ?>/>
				  Full Machines<br />
				  <input name="kits" type="checkbox" value="on"<?= $supplier['kits']; ?>/>
				  PCB's and Kits<br />
				  <input name="part" type="checkbox" value="on"<?= $supplier['part']; ?>/>
				  Spare Parts<br />
				  <input name="merc" type="checkbox" value="on"<?= $supplier['merc']; ?>/>
			  Merchandise<br>
			  <input name="serv" type="checkbox" id="serv" value="on"<?= $supplier['serv']; ?>>
			  Services
			</td>
		</tr>
		<tr>
          <td class="left_title">Where to Find: <br />( Paper File Location)</td>
          <td><input name="where_to_find" type="text" class="intext" id="where_to_find" value="<?= $supplier['where_to_find']; ?>"></td>
	    </tr>
	  </table>
	  <br />
	  <br />
	<div class="flagHeader">
		<span>
			Payment Methods
		</span>
	</div>
	<br>
	<table id="payment" class="vbox">
		<tr>
			<td colspan=3 class="left_right" style="text-align:left"><a href="#" style="color:black" onclick="javascript:addPayMethod();return false">Add New Payment Method</a></td>
		</tr>
		<tr>
			<th>Payment Method</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
<?php
$p = array_values($supplier['payments']);
		for($i = 0; $i < (count($p) ? count($p) : 1); $i++): ?>
		<tr id="payment_row_<?=$i?>" class="hidden">
			<td class="left_title">
				<input type="hidden" name="pay[<?=$i?>][id]" value="<?=$p[$i]['id']?>">
				<select name="pay[<?=$i?>][type]" id="pay[<?=$i?>][type]" onChange="javascript:changePay(this,<?=$i?>);">
					<option value="cc" <?php
if($p[$i]['type'] == 'cc') echo 'selected';?>>Credit Card</option>
					<option value="bnk" <?php
if($p[$i]['type'] == 'bnk') echo 'selected';?>>Bank Deposit</option>
					<option value="jyp" <?php
if($p[$i]['type'] == 'jyp') echo 'selected';?>>JYP - Japanese Yen</option>
					<option value="rmb" <?php
if($p[$i]['type'] == 'rmb') echo 'selected';?>>RMB - Chinese Renminbi</option>
					<option value="gbp" <?php
if($p[$i]['type'] == 'gbp') echo 'selected';?>>GBP - Great Britan Pound</option>
					<option value="eur" <?php
if($p[$i]['type'] == 'eur') echo 'selected';?>>EUR - European Euros</option>
					<option value="usd" <?php
if($p[$i]['type'] == 'usd') echo 'selected';?>>USD - US Dollar</option>
					<option value="aud" <?php
if($p[$i]['type'] == 'aud') echo 'selected';?>>AUD - Australian Dollar</option>
				</select>
			</td>
			<td width="40%"><textarea onfocus="if(this.value == 'Please Enter Bank Details:') this.value='';" name="pay[<?=$i?>][details]" rows="10" class="intext"><?=$p[$i]['details']?></textarea></td>
			<td width="40%">Notes: <input name="pay[<?=$i?>][notes]" type="text" class="intext" value="<?=$p[$i]['notes']?>" /></td>
			<td width="1%"><a href = "#" onclick="javascript: delContact(<?=$i?>,'payment_row'); return false;">Delete</a></td>
		</tr>
		<?= '<script>increment(\'pay_method\');</script>'; endfor; ?>
	</table>
	<br>
	<br>
	<div class="flagHeader">
		<span>
			Admin Notes
		</span>
	</div>
	<br />
<?php
if(count($admin_notes)) : ?>
	<!-- Admin Notes -->
	<table class="wide vbox">
		<th>User</th>
		<th>Date</th>
		<th>Note</th>
<?php
$shaded = true;
foreach($admin_notes as $n) : ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
			<td class="collapse"><?= str_replace(" ", "&nbsp;", $n['user']['first_name'] . " " . $n['user']['last_name']); ?></td>
			<td class="collapse"><?= date("d/m/Y,&\\nb\sp;H:i", $n['date']); ?></td>
			<td><?= $n['note']; ?></td>
		</tr>
<?php
endforeach; ?>
	</table>
	<!-- End Admin Notes -->
	<br />
<?php
endif; ?>
	<table>
		<tr>
			<td class="left_title">New Admin Note:</td>
			<td>
				<textarea name="new_note" cols="40" rows="5"></textarea>
			</td>
		</tr>
	</table>
	<br />
	<br>
	<div class="flagHeader">
		<span>Activated</span>
	</div><br>
	<table>
		<tr>
          <td class="left_title">Active:</td>
          <td><select name="active">
              <option value="1"<?= $supplier['active'] ? " selected=\"selected\"" : ""; ?>>Yes</option>
              <option value="0"<?= !$supplier['active'] ? " selected=\"selected\"" : ""; ?>>No</option>
            </select>
          </td>
	  </tr>
	</table>
	<div style="text-align: center; width: 100%">
		<input name="submit" type="submit" value="Update" />
		<!--<input name="view" type="button" value="View Hidden Values" onClick="javascript:viewHiddenThings()" /> --><!-- For Test Purposes only-->
<?php
if($mode == 'edit') : ?>
		<input name="mpo_button" type="button" value="Make Purchase Order" onclick="javascript: document.location.href='purchaseorders_admin.php?action=add&supplier_id=<?= $supplier['supplier_id']; ?>'" />
<?php
endif; ?>
	</div>
	<br />
<?php
if($mode == "edit"):
$shaded = false; ?>
	<h3 style="text-align: center">Open Purchase Orders for <?= $supplier['supplier_name']; ?></h3>
	<table class="wide vbox">
		<tr>
			<th>P/O #</th>
			<th>Date</th>
			<th>Ordered By</th>
			<th>Our Contact</th>
			<th class="collapse">&nbsp;</th>
		</tr>
<?php
if(is_array($po_summary) && (count($po_summary) > 0)):
		foreach($po_summary as $o): ?>
		<tr<?= $shaded ? " class=\"alt\"" : ""; ?>>
			<td>#<?= $o['porder_id']; ?></td>
			<td><?= date("d/m/Y", $o['creation_date']); ?></td>
			<td><?= $o['user_name']; ?></td>
			<td><?= $o['contact_name']; ?></td>
			<td class="collapse"><a href="purchaseorders_admin.php?action=edit&amp;id=<?= $o['porder_id']; ?>">Edit</a>&nbsp;|&nbsp;<a href="view_purchaseorder.php?id=<?=$o['porder_id']?>" target="_blank">View</a></td>
		</tr>
<?php
$shaded = !$shaded;
		endforeach;
	else: ?>
		<tr>
			<td colspan=5 style="text-align: center">No open purchase orders listed for this supplier</td>
		</tr>
<?php
endif; ?>
	</table>
<?php
endif; ?>
</form>
