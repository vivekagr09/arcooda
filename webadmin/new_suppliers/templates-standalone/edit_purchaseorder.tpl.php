<?php
if(!(isset($mode) || $mode == "edit" || $mode == "add"))
{
	$mode = "edit";
}
?>
<script language="javascript">
<!-- //Search Window Rubbish
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

	// Function to automagically update account numbers when the sv_id select box changes
	function updateAccount()
	{
		var accounts = new Object;
<?php
foreach($couriers as $c): ?>
		accounts["<?= $c['id'] ?>"] = "<?= $c['account_no'] ?>";
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
foreach($suppliers as $s):
?>
		contacts[<?= $s['supplier_id'] ?>] = new Array();
<?php
$i = 0;
	foreach($contacts as $c):
		if($c['supplier_id'] == $s['supplier_id']): ?>
			contacts[<?= $s['supplier_id'] ?>][<?= $i++ ?>] = "<?= $c['contact_name'] ?>";
			contacts[<?= $s['supplier_id'] ?>][<?= $i++ ?>] = "<?= $c['contact_id'] ?>";
<?php
endif;
	endforeach; ?>
<?php
endforeach; ?>

		//if (!optionTest) return;
		var box = document.add_edit_purchaseorder.supplier_id;
		var number = box.options[box.selectedIndex].value;
		if (!number) return;
		var list = contacts[number];
		var box2 = document.add_edit_purchaseorder.contact_id;
		box2.options.length = 0;
		for(i=0;i<list.length;i+=2)
		{
			box2.options[i/2] = new Option(list[i],list[i+1]);
		}
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
-->
</script>
<form accept-charset="utf-8" action="<?= $form_dest ?>" method="post" enctype="multipart/form-data" name="add_edit_purchaseorder">
	<!-- General Details -->
	<table>
		<tr>
			<td class="left_title">Buyer:</td>
			<td>
<?php
if($mode == "add"): ?>
				<select name="from_company">
					<option value="0"<?= ($porder['from_company']) ? "" : " selected=\"selected\"" ?>>Coinopexpress</option>
					<option value="1"<?= ($porder['from_company']) ? " selected=\"selected\"" : "" ?>>Fortunewave</option>
				</select>
<?php
else: ?>
				<?= ($porder['from_company']) ? "Fortunewave" : "Coinopexpress" ?>
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
				<select name="supplier_id" onchange="changeContactList();">
<?php
foreach($suppliers as $s) : ?>
					<option value="<?= $s['supplier_id'] ?>"<?= ($porder['supplier_id'] == $s['supplier_id']) ? " selected=\"selected\"" : "" ?>><?= $s['supplier_name'] ?></option>
<?php
endforeach; ?>
				</select>
<?php
else: ?>
				<?= $supplier_name ?>
<?php
endif; ?>
			</td>
			<td class="collapse">
<?php
if($mode == "add"): ?>
				<a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a>
<?php
endif; ?>
			</td>
		</tr>
		<tr>
			<td class="left_title">Supplier Contact Name:</td>
			<td>
				<select name="contact_id">
<?php
foreach($contacts as $c) : ?>
<?= $contact ?>
					<option value="<?= $c['contact_id'] ?>"<?= ($porder['supplier_contact'] == $c['contact_id']) ? " selected=\"selected\"" : "" ?>><?= $c['contact_name'] ?></option>
<?php
endforeach; ?>
				</select>
<?php
if($mode == "add"): ?>
				<script type="text/javascript">changeContactList();</script>
<?php
endif; ?>
			</td>
			<td class="collapse"><a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Send Goods To:</td>
			<td>
				<select name="send_to">
<?php
foreach($dests as $d) : ?>
					<option value="<?= $d['dest_id'] ?>"<?= ($porder['destination_id'] == $d['dest_id']) ? " selected=\"selected\"" : "" ?>><?= $d['name'] ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a></td>
		</tr>
	</table>
	<!-- End General Details -->
	<br />
	<!-- Order List -->
	<table class="wide">
		<tr>
			<th>Our ID</td>
			<th>Qty</td>
			<th>Name of Product</td>
			<th>Price</td>
			<th>Currency</td>
			<th class="collapse">&nbsp;</td>
		</tr>
<?php
$shaded = false;
$i = 0;
if($mode == "add"):
//=====================\\
// Product ADD Section \\
//=====================\\
	foreach($order as $oitem) : ?>
		<tr<?= $shaded ? " class=\"alt\"" : "" ?>>
			<td style="width:60px">
				<input name="order[<?= $i ?>][id]" type="text" class="intext" style="width:60px" value="<?= $oitem['product_id'] ?>" />
			</td>
			<td style="width:60px">
				<input name="order[<?= $i ?>][qty]" type="text" class="intext" style="width:60px" value="<?= $oitem['qty'] ?>" />
			</td>
			<td>
				<input name="order[<?= $i ?>][name]" type="text" class="intext" style="width:99%" value="<?= $oitem['name'] ?>" />
			</td>
			<td style="width:60px">
				<input name="order[<?= $i ?>][price]" type="text" class="intext" style="width:60px" value="<?= $oitem['price'] ?>" />
			</td>
			<td style="width:60px">
				<select name="order[<?= $i ?>][curr]" style="width:60px">
<?php
foreach($currency as $curr) : ?>
					<option value="<?= $curr['code'] ?>"<?= $curr['code'] == "USD" ? " selected" : ""  ?>><?= $curr['code'] . ": " . $curr['name'] ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse">
				<input type="button" name="search" value="Search" onclick="openSearchWindow('order[<?= $i ?>]', <?= $i ?>)">
			</td>
		</tr>
<?php
		$shaded = !$shaded;
		$i++;
	endforeach;
else:
//======================\\
// Product EDIT Section \\
//======================\\
	foreach($order as $oitem) : ?>
		<tr<?= $shaded ? " class=\"alt\"" : "" ?>>
			<input name="oldorder[<?= $i ?>][id]" type="hidden" value="<?= $oitem['product_id'] ?>" />
			<input name="oldorder[<?= $i ?>][updated]" type="hidden" value="1" /> <!-- JS Here! -->
			<td style="width:60px">
				<?= ($oitem['product_id'] == 0) ? "N/A" : $oitem['product_id'] ?>
			</td>
			<td style="width:60px">
				<input name="oldorder[<?= $i ?>][qty]" type="text" class="intext" style="width:60px" value="<?= $oitem['qty'] ?>" />
			</td>
			<td>
				<?= $oitem['name'] ?>
			</td>
			<td style="width:60px">
				<?= $oitem['price'] ?>
			</td>
			<td style="width:60px">
<?php
foreach($currency as $curr):
			if($curr['code'] == $oitem['currency']): ?>
				<?= $curr['code'] ?>
<?php
endif;
		endforeach; ?>
			</td>
			<td class="collapse">
				<a href="?action=do_rmi&amp;item_id=<?= $oitem['product_id'] ?>&amp;po_id=<?= $_REQUEST['id'] ?>">Delete</a>
			</td>
		</tr>
<?php
		$shaded = !$shaded;
		$i++;
	endforeach;
endif;
if($mode == "edit") :
?>
		<tr<?= $shaded ? " class=\"alt\"" : "" ?>>
			<td style="width:60px">
				<input name="order[<?= $i ?>][id]" type="text" class="intext" style="width:60px" value="" />
			</td>
			<td style="width:60px">
				<input name="order[<?= $i ?>][qty]" type="text" class="intext" style="width:60px" value="" />
			</td>
			<td>
				<input name="order[<?= $i ?>][name]" type="text" class="intext" style="width:99%" value="Add New Item..." />
			</td>
			<td style="width:60px">
				<input name="order[<?= $i ?>][price]" type="text" class="intext" style="width:60px" value="" />
			</td>
			<td style="width:60px">
				<select name="order[<?= $i ?>][curr]" style="width:60px">
<?php
foreach($currency as $curr) : ?>
					<option value="<?= $curr['code'] ?>"<?= $curr['code'] == "USD" ? " selected" : ""  ?>><?= $curr['code'] . ": " . $curr['name'] ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><input type="button" name="search" value="Search" onclick="openSearchWindow('order[<?= $i ?>]', <?= $i ?>)">
</td>
		</tr>
<?php
endif; ?>
	</table>
	<!-- End Order List -->
	<br />
	<!-- General -->
	<table>
		<tr>
			<td class="left_title">Ship Via:</td>
			<td>
				<select name="sv_id" onchange="updateAccount();">
<?php
foreach($couriers as $c): ?>
					<option value="<?= $c['id'] ?>"<?= ($c['id'] == $porder['shipvia_id']) ? " selected" : "" ?>><?= $c['name'] ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Shipping Charge (USD):</td>
			<td><input name="ship_charge" type="text" class="intext" value="<?= $porder['shipping_charge'] ?>"/></td>
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
					<option value="<?= $t['term_id'] ?>"<?= ($t['term_id'] == $porder['terms_id']) ? " selected" : "" ?>><?= $t['term'] ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse"><a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Notes on P/O:</td>
			<td>
				<textarea name="po_notes" rows="5" cols="40"><?= $porder['po_notes'] ?></textarea>
			</td>
			<td class="collapse">&nbsp;</td>
		</tr>
	</table>
	<!-- End General -->
	<br />
	<!-- Authorisedness -->
	<table>
		<tr>
			<td class="left_title">Order / Payment Authorised:</td>
			<td>
				<select name="authed">
					<option value="0">No</option>
					<option value="1"<?= $porder['authorised'] ? " selected=\"selected\"" : "" ?>>Yes</option>
				</select>
			</td>
		</tr>
<?php
if($porder['authorised']): ?>
		<tr>
			<td class="left_title">Authorised by:</td>
			<td><?= $porder['authorised_by']['first_name'] . " " . $porder['authorised_by']['last_name'] ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?= date("d/m/Y, H:i", $porder['authorised_date']) ?></td>
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
				<select name="paid">
					<option value="0">No</option>
					<option value="1"<?= $porder['paid'] ? " selected=\"selected\"" : "" ?>>Yes</option>
				</select>
			</td>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Payment Method:</td>
			<td>
				<select name="pay_method">
					<option></option>
<?php
foreach($paymethods as $p): ?>
					<option value="<?= $p['id'] ?>"<?= ($p['id'] == $porder['payment_method']) ? " selected" : "" ?>><?= $p['text'] ?></option>
<?php
endforeach; ?>				</select>
			</td>
			<td class="collapse"><a href="#">Add</a>&nbsp;|&nbsp;<a href="#">Edit</a></td>
		</tr>
		<tr>
			<td class="left_title">Receipt:</td>
			<td>
<?php
if($porder['receipt_fname']): ?>
				<a href="receipt_files/<?= $porder['porder_id'] ?>-<?= $porder['receipt_fname'] ?>">Click Here</a>
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
			<td><?= $porder['paid_by']['first_name'] . " " . $porder['paid_by']['last_name'] ?></td>
			<td class="collapse">&nbsp;</td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?= date("d/m/Y, H:i", $porder['paid_date']) ?></td>
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
				<select name="received">
					<option value="0">No</option>
					<option value="1"<?= $porder['received'] ? " selected=\"selected\"" : "" ?>>Yes</option>
				</select>
			</td>
		</tr>
<?php
if($porder['received']): ?>
		<tr>
			<td class="left_title">Received by:</td>
			<td><?= $porder['received_by']['first_name'] . " " . $porder['received_by']['last_name'] ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?= date("d/m/Y, H:i", $porder['received_date']) ?></td>
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
				<select name="checked">
					<option value="0">No</option>
					<option value="1"<?= $porder['checked'] ? " selected=\"selected\"" : "" ?>>Yes</option>
				</select>
			</td>
		</tr>
<?php
if($porder['checked']): ?>
		<tr>
			<td class="left_title">Received by:</td>
			<td><?= $porder['checked_by']['first_name'] . " " . $porder['checked_by']['last_name'] ?></td>
		</tr>
		<tr>
			<td class="left_title">Date:</td>
			<td><?= date("d/m/Y, H:i", $porder['checked_date']) ?></td>
		</tr>
<?php
endif; // received ?>
	</table>
	<!-- End Checkedness -->
	<br />
	<!-- Open / Closed -->
	<table>
		<tr>
			<td class="left_title">Status</td>
			<td>
				<select name="status">
					<option value="1"<?= $porder['status'] ? " selected=\"selected\"" : "" ?>>Open</option>
					<option value="0">Closed</option>
				</select>
			</td>
		</tr>
	</table>
	<!-- End Open / Closed -->
	<br />
	<!-- Admin Notes -->
	<table>
		<tr>
			<td class="left_title" colspan="2">Admin Notes</td>
		</tr>
		<tr>
			<td class="left_title">Admin Notes:</td>
			<td>
				<textarea name="admin_notes" rows="5" cols="40"></textarea>
			</td>
		</tr>
	</table>
	<br />
<?php
if(count($admin_notes)) : ?>
	<table class="wide">
		<th>User</th>
		<th>Date</th>
		<th>Note</th>
<?php
$shaded = true;
foreach($admin_notes as $n) : ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td class="collapse"><?= str_replace(" ", "&nbsp;", $n['user']['first_name'] . " " . $n['user']['last_name']); ?></td>
			<td class="collapse"><?= date("d/m/Y,&\\nb\sp;H:i", $n['date']); ?></td>
			<td><?= $n['note'] ?></td>
		</tr>
<?php
endforeach; ?>
	</table>
<?php
endif; ?>
	<!-- End Admin Notes -->
	<br />
	<!-- Buttons -->
	<div style="text-align: center">
		<script language="javascript">
		<!--
			function SetPreviewActions()
			{
				document.forms[0].action = 'view_purchaseorder.php<?=$form_dest?>';
				document.forms[0].target = '_blank';
			}

			function SetUpdateActions()
			{
				document.forms[0].action = '<?=$form_dest?>';
				document.forms[0].target = '';
			}
		//-->
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
	<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
		<td class="collapse"><?= str_replace(" ", "&nbsp;", $h['user']['first_name'] . " " . $h['user']['last_name']); ?></td>
		<td class="collapse"><?= date("d/m/Y,&\\nb\sp;H:i", $h['date']); ?></td>
		<td><?= $h['action'] ?><?= $h['object_id'] ? ": ID " . $h['object_id'] : "" ?></td>
	</tr>
<?php
endforeach; ?>
</table>
<!-- End Order History -->
<br />
<?php
endif; ?>
