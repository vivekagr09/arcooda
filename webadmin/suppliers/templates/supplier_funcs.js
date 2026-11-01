var contacts = 0;
var warehouse = 0;
var bank = 0;
var forwarders = 0;
var agt=navigator.userAgent.toLowerCase();
<?php $currency_list = getCurrencyList() ?>
<?php $country_list = getCountryList() ?>


function resize(t)
{
	//alert(agt);
	a = t.value.split('\n');
	b=1;
	for (x=0;x < a.length; x++) {
		if (a[x].length >= t.cols) b+= Math.floor(a[x].length/t.cols);
	}
	b+= a.length;
	if (agt.indexOf('opera') == -1)
		t.rows = b;
}

/*
function getElement(name)
{
	var theElem=document.getElementById(name);
	if(theElem!=null) return theElem;
	var theElems=document.getElementsByName(name);
	if(theElems.length>0) return theElems[0];
	return null;
}

function getValue(name)
{
	var theElem=getElement(name);
	if(theElem==null) return null;
	return theElem.value;
}

function setValue(name,value)
{
	var theElem=getElement(name);
	if(theElem!=null)
		if(theElem.value!=null)
			theElem.value=value;
}

function addTableRow(theTable,newName,rownum)
{
	if(rownum==null) rownum=theTable.rows.length;
	var newRow=theTable.insertRow(rownum);
	newRow.name=newName;
	newRow.id=newName;
	return newRow;
}

function addTableCell(tableRow,innerStuff)
{
	var newCol=document.createElement('td');
	newCol.innerHTML=innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}
*/

function addContact()
{
	var con_table = getElement('contact');
	if(con_table == null) return;
	var newRow = addTableRow(con_table,'contact_row['+contacts+']');
	newRow.setAttribute('conid',contacts);
	if(con_table.rows.length % 2 == 1)
		newRow.className = 'alt';
	var newCell = addTableCell(newRow,'<table class="wide" align="center" style="width:100%" border=0><tr><td class="left_title" valign="bottom" style="width:10%">&nbsp;</td><td class="left_title" style="width: 15%">Contact Name :</td><td><input type="hidden" name="contacts['+contacts+'][add]" value="add" /><input type="text" style="width:100%" name="contacts['+contacts+'][name]" value="" /></td><td class="left_title">&nbsp;</td></tr><tr><td class="left_title" style="width:1%;text-align: center"><input type="checkbox" name="contacts['+contacts+'][main]" value="y" onclick="mainContactSelected('+contacts+')"></td><td class="left_title" style="width: 15%">Position :</td><td><input type="text" style="width:100%" name="contacts['+contacts+'][title]" value="" /></td><td valign="bottom" class="left_title" style="text-align:center;width:5%"><a href = "#" onclick="javascript: delContact('+contacts+'); return false;">Delete</a></td></tr><tr><td class="left_title" style="text-align:center;width:10%" valign="top"><span id="main_txt['+contacts+']"></span></td><td class="left_title" style="width: 15%">Email Address :</td><td><input type="text" style="width:100%" name="contacts['+contacts+'][email]" value="" /></td><td class="left_title">&nbsp;</td></tr><tr><td class="left_title" style="width: 10%;">&nbsp;</td><td class="left_title" style="width: 15%">Phone :</td><td><input type="text" style="width:100%" name="contacts['+contacts+'][phone]" value="" /></td><td class="left_title">&nbsp;</td></tr></table>');
	//newCell.style.width = '100%';


	/*
	addTableCell(newRow,'<input type="text" style="width:100%" name="contacts['+contacts+'][name]" value="" /><input type="hidden" name="contacts['+contacts+'][add]" value="add" />');
	addTableCell(newRow,'<input type="text" style="width:100%" name="contacts['+contacts+'][title]" value="" />');
	addTableCell(newRow,'<input type="text" style="width:100%" name="contacts['+contacts+'][email]" value="" />');
	addTableCell(newRow,'<input type="text" style="width:100%" name="contacts['+contacts+'][phone]" value="" />');
	//addTableCell(newRow,'<a href = "#" onclick="javascript: deleteRow(\'contact\','+contacts+'); return false;">Delete</a>');
	*/
	contacts--;
}

function addWarehouse()
{
	var ship_table = getElement('warehouse');
	if(ship_table == null) return;
	var newRow = addTableRow(ship_table,'warehouse_row['+warehouse+']');
	if(ship_table.rows.length % 2 == 0)
		newRow.className = 'alt';
	addTableCell(newRow,'<table class="wide" style="width:100%" cellpadding=0 cellspacing=0><tr><td class="left_title" style="width:18%;">Company Name :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][manufacturer]" value=""></td><td class="left_title" style="width:18%;">Phone :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][phone]" value=""></td></tr><tr><td class="left_title" style="width:18%;">Address :</td><td style="width:32%;" rowspan=2><textarea class="in" style="width:100%;height:100%" name="warehouse['+warehouse+'][address]"></textarea></td><td class="left_title" style="width:18%;">Fax :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][fax]" value=""></td></tr><tr><td class="left_title" style="width:18%;">&nbsp;</td><td class="left_title" style="width:18%;">Mobile :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][mobile]" value=""></td></tr><tr><td class="left_title" style="width:18%;">City :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][city]" value=""></td><td class="left_title" style="width:18%;">Email :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][email]" value=""></td></tr><tr><td class="left_title" style="width:18%;">State :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][state]" value=""></td><td class="left_title" style="width:18%;">Website :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][website]" value=""></td></tr><tr><td class="left_title" style="width:18%;">Post Code :</td><td style="width:32%;"><input type="text" style="width:100%;" name="warehouse['+warehouse+'][post_code]" value=""></td><td class="left_title" style="width:18%;">&nbsp;</td><td style="width:32%;">&nbsp;</td></tr><tr><td class="left_title" style="width:18%;">Country :</td><td style="width:32%;"><select style="width:100%;" name="warehouse['+warehouse+'][country]"><option value="">Not Selected</option><?= formatHTMLOptions($country_list,"","a")?></select></td><td class="left_title" style="width:18%;">&nbsp;</td><td style="width:32%;">&nbsp;</td></tr></table>');
	warehouse--;
}

function formatSupplierAddress()
{
	return (getValue('supplier[address]')+"\n"+getValue('supplier[city]')+', '+getValue('supplier[state]')+', '+getValue('supplier[post_code]'));
}

function chooseBank()
{
	window.open('suppliers_choose.php','','statusbar=1,width=400,height=250');
}

function addBankPayment(table, title)
{
	if(table == null) return;
	var newRow = addTableRow(table,'bank_row['+bank+']');
	if(table.rows.length % 2 == 0)
		newRow.className = 'alt';
	var newCell = addTableCell(newRow,'<table class="wide" align="center" style="width:100%" border=0><tr><th colspan=4>'+title+'<input type="hidden" name="bank['+bank+'][title]" value="'+title+'"><input type="hidden" name="bank['+bank+'][type]" value="b"></th></tr><tr><td class="left_title">Beneficiary :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][beneficiary]" value=""><input type="hidden" name="bank['+bank+'][add]" value="add"></td><td class="left_title">Routing # :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][routing_no]" value=""></td></tr><tr><td class="left_title">Beneficiary Address :</td><td class="in"><textarea name="bank['+bank+'][beneficiary_address]" rows=2 onkeypress="resize(this)"></textarea></td><td class="left_title">SWIFT Code :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][swift_code]" value=""></td></tr><tr><td class="left_title">Account # :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][account_no]" value=""></td><td class="left_title">Currency :</td><td class="in"><select name="bank['+bank+'][currency]" class="in"><?= formatHTMLOptions($currency_list,"","a")?></select></td></tr><tr><td class="left_title">Bank Name :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][bank]" value=""></td><td class="left_title">Bank Branch :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][branch]" value=""></td></tr><tr><td class="left_title">Bank Address :</td><td class="in"><input type="text" class="in" name="bank['+bank+'][address]" value=""></td><td class="left_title">&nbsp;</td><td class="in">&nbsp;</td></tr><tr><td class="left_title">Comments :</td><td colspan=3><textarea name="bank['+bank+'][details]" style="width:100%;height:100%" onkeypress="resize(this)"></textarea></td></tr></table>');

	// now have to auto-fill certain fields..
	setValue('bank['+bank+'][beneficiary]',getValue('supplier[manufacturer]'));
	setValue('bank['+bank+'][beneficiary_address]',formatSupplierAddress());
	//setValue('bank['+bank+'][beneficiary]',getValue('supplier[currency]'));
}

function addBank(type, title)
{
	var for_table = getElement('bank');
	if(for_table == null) return;
	if(type == 'b')
		addBankPayment(for_table, title);
	else{
		var newRow = addTableRow(for_table,'bank_row['+bank+']');
		if(for_table.rows.length % 2 == 0)
			newRow.className = 'alt';
		var newCell = addTableCell(newRow,'<table class="wide" align="center" style="width:100%" border=0><tr><th>'+title+'<input type="hidden" name="bank['+bank+'][title]" value="'+title+'"><input type="hidden" name="bank['+bank+'][type]" value="'+type+'"></th></tr><tr><td><textarea name="bank['+bank+'][details]" style="width:100%" rows=1 onkeypress="resize(this)"></textarea><input type="hidden" name="bank['+bank+'][add]" value="add"></td></tr></table>');
	}
	bank--;
}

/*
function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	if(theSect==null) return;
	if(theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
*/

function togglePic(name)
{
	var elem=getElement(name);
	if(elem.style.display=='none')
		getElement(name+'ImEx').src = 'triDown.gif';
	else
		getElement(name+'ImEx').src = 'triUp.gif';
}

function mainContactSelected(row)
{
	var con_table = getElement('contact');
	//var con_rows = con_table.rows;
	var chk = getElement('contacts['+row+'][main]').value;
	getElement('main_txt['+row+']').innerHTML = 'Preferred';
	for(i=0;i<con_table.rows.length;i++){
		var conid = con_table.rows[i].getAttribute('conid');
		if(conid != row ){
			getElement('contacts['+conid+'][main]').checked=false;
			getElement('main_txt['+conid+']').innerHTML = '';
		}
	}
}

function delContact(row)
{
	if(confirm('Are You sure you want to delete the Contact')){
		if(row > 0){
			// firstly delete the row from the database....
			window.open('index.php?action=delete_contact&contact_id='+row,'','width=10,height=10');
		}
		// now just remove the row..
		var con_table = getElement('contact');
		var rows = con_table.getElementsByTagName("tr");
		var index = 0;
		for(i=0;i<rows.length;i++){
			if(rows[i].getAttribute('conid') == row){
				index = rows[i].rowIndex;
				break;
			}
		}
		con_table.deleteRow(index);
	}
}

function checkSupplier()
{
	var name = getValue('supplier[manufacturer]');
	var email = getValue('supplier[email]');
	window.open('index.php?action=checkSupplier&name='+name+'&email='+email,'','width=15,height=15');
	return false;
}