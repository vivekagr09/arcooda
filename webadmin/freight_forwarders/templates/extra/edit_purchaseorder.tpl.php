<?php
if(!(isset($mode) || $mode == "edit" || $mode == "add"))
{
	$mode = "edit";
}
?>
<div id="html_parser" style="display:none;"></div>
<script type="text/javascript" src="functions/trcloner.js"></script>
<script type="text/javascript">
//<![CDATA[
	var edit_contact_id = <?php echo ($porder['supplier_contact']) ? $porder['supplier_contact'] : 0;?>;

	//Search Window Rubbish
	function openSearchWindow(elementName,pos_id)
	{
		var searchStr = document.forms[0].elements[elementName + "[id]"].value;
		if((searchStr == "") || (searchStr == "N/A"))
		{
			document.forms[0].elements[elementName + "[id]"].value = "";
			searchStr = document.forms[0].elements[elementName + "[name]"].value;
		}
		var windowURL = "../productSearch.php?param="+searchStr+"&pos_id="+pos_id;
		searchWnd = window.open(windowURL,name,"titlebar,resizable,scrollbars,width=650,height=400");
		searchWnd.focus();
	}

	function selectProduct(prodID,name,pos_id)
	{
		document.forms[0].elements["order["+pos_id+"][id]"].value = prodID;
		document.forms[0].elements["order["+pos_id+"][name]"].value = name;
	}

	function noResult(pos_id)
	{
		var idbox = document.forms[0].elements["order["+pos_id+"][id]"];
		if(idbox.value == "")
		{
			idbox.value = "N/A";
		}
	}

	function openSupplierSearchWindow()
	{
		var windowURL = "supplier_search.php";
		ssearchWnd = window.open(windowURL,name,"titlebar,resizable,scrollbars,width=650,height=400");
		ssearchWnd.focus();
	}

	function selectSupplier(id,name)
	{
		//supplier_id
		document.forms[0].elements["supplier_id"].value = id;
		changeContactList();
	}

	// Function to automagically update account numbers when the sv_id select box changes
	function updateAccount()
	{
		var accounts = new Object;
<?php
foreach($couriers as $c): ?>
		accounts["<?php echo $c['id']; ?>"] = "<?php echo $c['account_no']; ?>";
<?php
endforeach; ?>
		var box = document.add_edit_purchaseorder.sv_id;
		var id = box.options[box.selectedIndex].value;
		var target = document.add_edit_purchaseorder.ship_acc_num;
		target.value = accounts[id];
	}

	// Functions to automagically re-populate contact list when the supplier changes
	// Is empty in edit mode due to not being needed.
	function changeContactList()
	{
<?php
if($mode == "add"): ?>
		var contacts = new Array();
<?php
foreach($suppliers as $s): ?>
		contacts[<?php echo $s['supplier_id']; ?>] = new Array();
<?php
$i = 0;
	foreach($contacts as $c):
		if($c['supplier_id'] == $s['supplier_id']): ?>
			contacts[<?php echo $s['supplier_id']; ?>][<?php echo $i++; ?>] = "<?php echo $c['contact_name']; ?>";
			contacts[<?php echo $s['supplier_id']; ?>][<?php echo $i++; ?>] = "<?php echo $c['contact_id']; ?>";
<?php
endif;
	endforeach; ?>
<?php
endforeach; ?>

		var box = document.add_edit_purchaseorder.supplier_id;
		var number = box.options[box.selectedIndex].value;
		// Clear & Repopulate selectbox
		if (!number) return;
		var list = contacts[number];
		var box2 = document.add_edit_purchaseorder.contact_id;
		var parser = document.getElementById("html_parser");
		box2.options.length = 0;
		for(i=0;i<list.length;i+=2)
		{
			parser.innerHTML = list[i];
			box2.options[i/2] = new Option(parser.innerHTML,list[i+1]);
		}
		var new_supp_link = "contacts_admin.php?action=add&supplier_id=" + number;
<?php
endif; ?>
	}

	function confirmProductRemoval()
	{
		var i = 0;
		var deletables = "";
		var pform = document.add_edit_purchaseorder;
		while(1)
		{
			if(!pform.elements["oldorder["+i+"][qty]"])
			{	// Element doesn't exist, end of form
				break;
			}
			else if(pform.elements["oldorder["+i+"][qty]"].value < 1)
			{	// Confirm removal
				if(deletables.length)
				{
					deletables += ", "
				}
				deletables += pform.elements["oldorder["+i+"][id]"].value;
			}
			i++;
		}
		if(deletables.length)
		{
			var ans = confirm("You are about to delete item(s) "+deletables+". Do you wish to continue?");
			return ans;
		}
		return true;
	}

	function plz_enable(from, to)
	{	// Stupid function for preventing a user from selecting a non-available item and stuff.
		// By no means optimal, but it'll do.
		var val = document.forms[0].elements[from].value;
		if(val == 1)
		{
			document.forms[0].elements[to].disabled = false;
		}
		else
		{
			if(to == "paid")
			{
				document.forms[0].elements["paid"].disabled = true;
				document.forms[0].elements["paid"].selectedIndex = 0;
				document.forms[0].elements["pay_method"].disabled = true;
				document.forms[0].elements["payment_receipt"].disabled = true;
				document.forms[0].elements["received"].disabled = true;
				document.forms[0].elements["received"].selectedIndex = 0;
				document.forms[0].elements["checked"].disabled = true;
				document.forms[0].elements["checked"].selectedIndex = 0;
			}
			else if(to == "received")
			{
				document.forms[0].elements["received"].disabled = true;
				document.forms[0].elements["received"].selectedIndex = 0;
				document.forms[0].elements["checked"].disabled = true;
				document.forms[0].elements["checked"].selectedIndex = 0;
			}
			else if(to == "checked")
			{
				document.forms[0].elements["checked"].disabled = true;
				document.forms[0].elements["checked"].selectedIndex = 0;
			}
		}
	}

	function fixurl(link, id_from, base_uri)
	{	// Function to dynamically fix URLs to include appropriate IDs
		var obj_from = document.forms[0].elements[id_from];
		var id = obj_from.options[obj_from.selectedIndex].value;
		link.href = base_uri + id;
	}

	function popup(url)
	{
		window.open(url + "&popup=1", "popup", "menubar=no,toolbar=no,status=no,height=200,width=400");
		return false;
	}

	function update_contacts(update_data)
	{
	alert("Pushback!");
		var sel = document.getElementById("contact_id")
		var at = sel.selectedIndex
		sel.selectedIndex = 0;
		var j = 0;

		// Remove Current Contacts
		for(var i = 0; i < sel.options.length; i++)
		{
			sel.remove(i);
		}

		// Stick in new ones
		for(var i = 0; i < update_data.length; i++)
		{
			var name = update_data[i++];
			var email = update_data[i++];
			var id = update_data[i];
			sel.options[j] = new Option(name, id);
			sel.options[j++].innerHTML = name;
		}

		sel.selectedIndex = at;
	}
//]]>
</script>
<form action="<?=$form_dest?>" method="post" enctype="multipart/form-data" name="add_edit_purchaseorder">
	<!-- General Details -->
	<div class="flagHeader"><span>General Details</span></div><br />
	<table>
		<tr>
			<td class="left_title">Buyer:</td>
			<td>
<?php
if($mode == "add"):
	$from_company_list = getWebsitesList();
?>
				<select name="from_company">
					<option value=""></option>
					<?=formatHTMLOptions($from_company_list, $porder['from_company'])?>
				</select>
<?php
else: ?>
				<?=htmlspecialchars($from_company_list[$porder['from_company']])?>
<?php
endif; ?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td class="left_title">Name of Supplier:</td>
			<td>
<?php
if($mode == "add"): ?>
				<select id="supplier_id" name="supplier_id" onchange="changeContactList();">
<?php
foreach($suppliers as $s) : ?>
					<option value="<?php echo $s['supplier_id']; ?>"<?php echo ($porder['supplier_id'] == $s['supplier_id']) ? " selected=\"selected\"" : ""; ?>><?php echo $s['supplier_name']; ?></option>
<?php
endforeach; ?>
				</select>
<?php
else: ?>
				<?=$supplier_name?>
<?php
endif; ?>
			</td>
			<td class="collapse">
<?php
if($mode == "add"): ?>
				<input type="button" value="Search" onclick="javascript: openSupplierSearchWindow();" />
<?php
endif; ?>
			</td>
		</tr>
		<tr>
			<td class="left_title">Supplier Contact Name:</td>
			<td>
				<select id="contact_id" name="contact_id">
<?php
foreach($contacts as $c) : ?>
					<option value="<?php echo $c['contact_id']; ?>"<?php echo ($porder['supplier_contact'] == $c['contact_id']) ? " selected=\"selected\"" : ""; ?>><?php echo $c['contact_name']; ?></option>
<?php
endforeach; ?>
				</select>
<?php
if($mode == "add"): ?>
				<script type="text/javascript">changeContactList();</script>
<?php
endif; ?>
			</td>
<!--			<td class="collapse"><a href="contacts_admin.php?action=add" onclick="javascript:fixurl(this, 'supplier_id', 'contacts_admin.php?action=add&supplier_id=');">Add</a>&nbsp;|&nbsp;<a href="contacts_admin.php?action=edit" onclick="javascript:fixurl(this, 'contact_id', 'contacts_admin.php?action=edit&id=');">Edit</a></td> --!>
			<td class="collapse"><a href="contacts_admin.php?action=add" onclick="javascript:return popup('contacts_admin.php?action=add&amp;supplier_id=<?php echo ($mode == "edit") ? $porder['supplier_id'] : "'+document.getElementById('supplier_id').value+'"?>');">Add</a>&nbsp;|&nbsp;<a href="contacts_admin.php?action=edit" onclick="javascript:return popup('contacts_admin.php?action=edit&contact_id='+document.getElementById('contact_id').value);">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Send Goods To:</td>
			<td>
				<select name="send_to">
<?php
foreach($dests as $d) : ?>
					<option value="<?php echo $d['dest_id']; ?>"<?php echo ($porder['destination_id'] == $d['dest_id']) ? " selected=\"selected\"" : ""; ?>><?php echo $d['name']; ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><a href="stuff_admin.php?section=dest&action=add">Add</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=dest" onclick="javascript:fixurl(this, 'send_to', 'stuff_admin.php?section=dest&action=edit&id=');">Edit</a></td>
		</tr>
	</table>
	<!-- End General Details -->
	<br />
	<!-- Order List -->
	<table class="wide vbox">
		<tr>
			<th>Our ID</th>
			<th>Qty</th>
			<th>Name of Product</th>
			<th>Price</th>
			<th class="collapse">&nbsp;</th>
		</tr>
<?php
$shaded = false;
$i = 0;
if($mode == "add"):
//=====================\\
// Product ADD Section \\
//=====================\\
	for($i = 0; $i < (count($order) ? count($order) : 1); $i++): ?>
		<tr id="order_row<?php echo $i?"_$i":""?>"<?php echo $i % 2 ? " class=\"alt\"" : ""; ?>>
			<td style="width:60px">
				<input name="order[<?=$i?>][id]" type="text" class="intext" style="width:60px" value="<?=$order[$i]['product_id']?>" />
			</td>
			<td style="width:60px">
				<input name="order[<?=$i?>][qty]" type="text" class="intext" style="width:60px" value="<?=$order[$i]['qty']?>" />
			</td>
			<td>
				<input name="order[<?=$i?>][name]" type="text" class="intext" style="width:99%" value="<?=$order[$i]['name']?>" />
			</td>
			<td style="width:60px">
				<input name="order[<?=$i?>][price]" type="text" class="intext" style="width:60px" value="<?=$order[$i]['price']?>" />
			</td>

			<td class="collapse">
				<input type="button" name="search" value="Search" onclick="openSearchWindow('order[<?=$i?>]', <?=$i?>)">
			</td>
		</tr>
<?php
	endfor;
else:
//======================\\
// Product EDIT Section \\
//======================\\
	foreach($order as $oitem) : ?>
		<tr<?php echo $shaded ? " class=\"alt\"" : ""; ?>>
			<input name="oldorder[<?=$i?>][id]" type="hidden" value="<?=$oitem['product_id']?>" />
			<input name="oldorder[<?=$i?>][updated]" type="hidden" value="1" /> <!-- JS Here! -->
			<td style="width:60px">
				<?php echo ($oitem['product_id'] == 0) ? "N/A" : $oitem['product_id']; ?>
			</td>
			<td style="width:60px">
				<input name="oldorder[<?=$i?>][qty]" type="text" class="intext" style="width:60px" value="<?=$oitem['qty']?>" />
			</td>
			<td>
				<?php echo $oitem['name']; ?>
			</td>
			<td style="width:60px">
				<?php echo $oitem['price']; ?>
			</td>
			<td class="collapse">
				<a href="?action=do_rmi&amp;item_id=<?php echo $oitem['product_id'];?>&amp;po_id=<?php echo $_REQUEST['id'];?>">Delete</a>
			</td>
		</tr>
<?php
		$shaded = !$shaded;
		$i++;
	endforeach;
endif;
if($mode == "edit") :
?>
		<tr id="order_row"<?php echo $shaded ? " class=\"alt\"" : ""; ?>>
			<td style="width:60px"><input name="order[0][id]" type="text" class="intext" style="width:60px" value="" /></td>
			<td style="width:60px"><input name="order[0][qty]" type="text" class="intext" style="width:60px" value="" /></td>
			<td><input name="order[0][name]" type="text" class="intext" style="width:99%" value="Add New Item..." /></td>
			<td style="width:60px"><input name="order[0][price]" type="text" class="intext" style="width:60px" value="" /></td>
			<td class="collapse"><input type="button" name="search" value="Search" onclick="openSearchWindow('order[0]', (0))"></td>
		</tr>
<?php
endif; ?>
	</table>
	<input type="button" value="Add Another Product" onclick="javascript: cloneRow('order_row');" />
	<!-- End Order List -->
	<br /><br />
	<!-- General -->
	<table>
		<tr>
			<td class="left_title">Order Currency:</td>
			<td>
				<select name="order_currency">
<?php
foreach($currency as $curr) : ?>
					<option value="<?php echo $curr['code']; ?>"<?php echo ((($curr['code'] == "USD") && ($mode == "add")) || (($curr['code'] == $porder['order_currency']) && ($mode == "edit"))) ? " selected" : "" ;?>><?php echo $curr['code'] . ": " . $curr['name']; ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Ship Via:</td>
			<td>
				<select name="sv_id" onchange="updateAccount();">
<?php
foreach($couriers as $c): ?>
					<option value="<?php echo $c['id']; ?>"<?php echo ($c['id'] == $porder['shipvia_id']) ? " selected" : "";?>><?php echo $c['name']; ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"></td>
		</tr>
		<tr>
			<td class="left_title">Shipping Charge (USD):</td>
			<td><input name="ship_charge" type="text" class="intext" value="<?php echo $porder['shipping_charge']; ?>"/></td>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Our A/C #:</td>
			<td><input name="ship_acc_num" type="text" class="intext" value="I get updated automagically" /></td>
			<script type="text/javascript">updateAccount(); // Put this here to update the "account" text box on load</script>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Shipping Terms:</td>
			<td>
				<select name="ship_terms">
<?php
foreach($terms as $t): ?>
					<option value="<?php echo $t['term_id']; ?>"<?php echo ($t['term_id'] == $porder['terms_id']) ? " selected" : ""; ?>><?php echo $t['term']; ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><a href="stuff_admin.php?section=term&action=add">Add</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=term" onclick="javascript:fixurl(this, 'ship_terms', 'stuff_admin.php?section=term&action=edit&id=');">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Notes on P/O:</td>
			<td>
				<textarea name="po_notes" rows="5" cols="40"><?php echo $porder['po_notes']; ?></textarea>
			</td>
			<td class="collapse">&nbsp;</td>
		</tr>
	</table>
	<!-- End General -->
	<br />
	<br />
	<div class="flagHeader"><span>Order Status</span></div><br />
	<!-- Authorisedness -->
	<table>
		<tr>
			<td class="left_title">Order / Payment Authorised:</td>
			<td>
<?php
if($porder['authorised']): ?>
				Yes
<?php
else: ?>
				<select name="authed" onchange="javascript:plz_enable('authed', 'paid'); plz_enable('authed', 'pay_method'); plz_enable('authed', 'payment_receipt');">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
<?php
endif; ?>
			</td>
		</tr>
<?php
if($porder['authorised']): ?>
		<tr>
			<td class="left_title">Authorised by:</td>
			<td><?php echo $porder['authorised_by']['first_name'] . " " . $porder['authorised_by']['last_name']; ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?php echo date("d/m/Y, H:i", $porder['authorised_date']); ?></td>
		</tr>
<?php
endif; // authorised ?>
	</table>
	<!-- End Authorisedness -->
	<br />
	<!-- Paidness -->
	<table>
		<tr>
			<td class="left_title">Payment Made:</td>
			<td>
<?php
if($porder['paid']): ?>
				Yes
<?php
else: ?>
				<select name="paid"<?php echo $porder['authorised'] ? "" : " disabled=\"disabled\"" ?> onchange="javascript:plz_enable('paid', 'received');">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
<?php
endif; ?>
			</td>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Payment Method:</td>
			<td>
				<select name="pay_method"<?php echo $porder['authorised'] ? "" : " disabled=\"disabled\"" ?>>
<?php
foreach($paymethods as $p): ?>
					<option value="<?php echo $p['id'];?>"<?php echo ($p['id'] == $porder['payment_method']) ? " selected" : "";?>><?php echo $p['text'];?></option>
<?php
endforeach; ?>				</select>
			</td>
			<td class="collapse"><a href="stuff_admin.php?section=paym&action=add">Add</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=paym" onclick="javascript:fixurl(this, 'pay_method', 'stuff_admin.php?section=paym&action=edit&id=');">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Receipt:</td>
			<td>
<?php
if($porder['receipt_fname']): ?>
				<a href="receipt_files/<?php echo $porder['porder_id'];?>-<?php echo $porder['receipt_fname'];?>" target="_blank">Click Here</a>
<?php
else: ?>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				<input name="payment_receipt" type="file" class="infile" />
<?php
endif; ?>
			</td>
			<td class="collapse">&nbsp;</td>
		</tr>
<?php
if($porder['paid']): ?>
		<tr>
			<td class="left_title">Paid by:</td>
			<td><?php echo $porder['paid_by']['first_name'] . " " . $porder['paid_by']['last_name']; ?></td>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?php echo date("d/m/Y, H:i", $porder['paid_date']); ?></td>
			<td class="collapse">&nbsp;</td>
		</tr>
<?php
endif; // paid ?>
	</table>
	<!-- End Paidness -->
	<br />
	<!-- Goodness -->
	<table>
		<tr>
			<td class="left_title">Goods Received:</td>
			<td>
<?php
if($porder['received']): ?>
				Yes
<?php
else: ?>
				<select name="received"<?php echo $porder['paid'] ? "" : " disabled=\"disabled\"" ?> onchange="javascript:plz_enable('received', 'checked');">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
<?php
endif; ?>
			</td>
		</tr>
<?php
if($porder['received']): ?>
		<tr>
			<td class="left_title">Received by:</td>
			<td><?php echo $porder['received_by']['first_name'] . " " . $porder['received_by']['last_name']; ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?php echo date("d/m/Y, H:i", $porder['received_date']); ?></td>
		</tr>
<?php
endif; // received ?>
	</table>
	<!-- End Goodness -->
	<br />
	<!-- Checkedness -->
	<table>
		<tr>
			<td class="left_title">Checked &amp; Approved:</td>
			<td>
<?php
if($porder['checked']): ?>
				Yes
<?php
else: ?>
				<select name="checked"<?php echo $porder['received'] ? "" : " disabled=\"disabled\"" ?>>
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>
<?php
endif; ?>
			</td>
		</tr>
<?php
if($porder['checked']): ?>
		<tr>
			<td class="left_title">Received by:</td>
			<td><?php echo $porder['checked_by']['first_name'] . " " . $porder['checked_by']['last_name']; ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?php echo date("d/m/Y, H:i", $porder['checked_date']); ?></td>
		</tr>
<?php
endif; // checked ?>
	</table>
	<!-- End Checkedness -->
	<br />
	<!-- Open / Closed -->
	<table>
		<tr>
			<td class="left_title">Status</td>
			<td>
				<select name="status">
					<option value="1"<?php echo $porder['status'] ? " selected=\"selected\"" : ""; ?>>Open</option>
					<option value="0">Closed</option>
				</select>
			</td>
		</tr>
	</table>
	<!-- End Open / Closed -->
	<br />
	<br />
	<div class="flagHeader"><span>Admin Notes</span></div><br />
	<!-- Admin Notes -->
<?php
if(count($admin_notes)) : ?>
	<table class="wide vbox">
		<th>User</th>
		<th>Date</th>
		<th>Note</th>
<?php
$shaded = true;
foreach($admin_notes as $n) : ?>
		<tr<?php echo ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
			<td class="collapse"><?php echo str_replace(" ", "&nbsp;", $n['user']['first_name'] . " " . $n['user']['last_name']); ?></td>
			<td class="collapse"><?php echo date("d/m/Y,&\\nb\sp;H:i", $n['date']); ?></td>
			<td><?php echo $n['note']; ?></td>
		</tr>
<?php
endforeach; ?>
	</table>
	<br />
<?php
endif; ?>
	<table>
		<tr>
			<td class="left_title">New Admin Note:</td>
			<td>
				<textarea name="admin_notes" rows="5" cols="40"></textarea>
			</td>
		</tr>
	</table>
	<!-- End Admin Notes -->
	<br />
	<!-- Buttons -->
	<div style="text-align: center">
		<script type="text/javascript">
//<![CDATA[
			function SetPreviewActions()
			{
				document.forms[0].action = 'view_purchaseorder.php<?php echo $form_dest?>';
				document.forms[0].target = '_blank';
			}

			function SetUpdateActions()
			{
				document.forms[0].action = '<?php echo $form_dest?>';
				document.forms[0].target = '';
			}
//]]>
		</script>
		<input name="preview" type="submit" value="Preview P/O" onclick="javascript:SetPreviewActions();" />
		<input name="update" type="submit" value="Update P/O" onclick="javascript:SetUpdateActions(); return confirmProductRemoval();" />
	</div>
	<br />
	<!-- End Buttons -->
</form>
<?php
if(count($order_history)) : ?>
<!-- Order History -->
<h2>Order History</h2></div>
<table class="wide">
	<th>User</th>
	<th>Date</th>
	<th>Action</th>
<?php
$shaded = true;
foreach($order_history as $h) : ?>
	<tr<?php echo ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
		<td class="collapse"><?php echo str_replace(" ", "&nbsp;", $h['user']['first_name'] . " " . $h['user']['last_name']); ?></td>
		<td class="collapse"><?php echo date("d/m/Y,&\\nb\sp;H:i", $h['date']); ?></td>
		<td><?php echo $h['action']; ?><?php echo $h['object_id'] ? ": ID " . $h['object_id'] : "" ?></td>
	</tr>
<?php
endforeach; ?>
</table>
<!-- End Order History -->
<br />
<?php
endif; ?>
