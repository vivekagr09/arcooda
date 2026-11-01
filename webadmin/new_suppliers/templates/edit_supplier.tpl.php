<?php
if(!(isset($mode) || $mode == "edit" || $mode == "add"))
{
	$mode = "edit";
}
?>
<script language="JavaScript" type="text/javascript">
<!--
var edit_contact_id = <?= ($supplier['contacts'][0]['contact_id']) ? $supplier['contacts'][0]['contact_id'] : 0;?>;

// Simple script for changing values of a text box based on a select box.
// 20040921: Expanded to include 2 variables so we can set ID's in links too =[
function change_email()
{
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

function popup(url)
{
	window.open(url + "&popup=1", "popup", "menubar=no,toolbar=no,status=no,height=200,width=400");
	return false;
}

function update_contacts(update_data)
{
	var sel = document.getElementById("edit_contact")
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
		sel.options[j] = new Option(name, email+":"+id);
		sel.options[j++].innerHTML = name;
	}

	sel.selectedIndex = at;
	change_email();
}

// -->
</script>

<form accept-charset="utf-8" name="add_edit_supplier" method="post" action="<?= $form_dest; ?>">
	<input name="id" type="hidden" value="<?= $supplier['supplier_id']; ?>">
	<input name="supplier_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="contacts_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="types_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<div class="flagHeader">
		<span>
			English Details
		</span>
	</div>
	<br />
	<table>
		<tr>
          <td class="left_title">Supplier Name:</td>
          <td><input name="supplier_name" type="text" class="intext" value="<?= $supplier['supplier_name']; ?>" /></td>
	  </tr>
		<tr>
          <td class="left_title">Head Office Address:</td>
          <td><textarea name="address" cols="40" rows="5"><?= $supplier['address']; ?></textarea>
          </td>
	  </tr>
		<tr>
		  <td class="left_title">Factory Name:</td>
		  <td><input name="factory_name" type="text" class="intext" id="facory_name" value="<?= $supplier['factory_name']; ?>"></td>
	  </tr>
		<tr>
		  <td class="left_title">Factory Address: </td>
		  <td><textarea name="factory_address" cols="40" rows="5" id="factory_address"><?= $supplier['factory_address']; ?></textarea>
          </td>
	  </tr>
		<tr>
		  <td class="left_title">District Office: </td>
		  <td><input name="doffice_name" type="text" class="intext" id="doffice_name" value="<?= $supplier['doffice_name']; ?>"></td>
	  </tr>
		<tr>
		  <td class="left_title">District Office Address: </td>
	      <td><textarea name="doffice_address" cols="40" rows="5" id="doffice_address"><?= $supplier['doffice_address']; ?></textarea>
          </td>
	  </tr>
  </table>
	  <br />
	  <br />
	<div class="flagHeader">
		<span>
			Chinese Details
		</span>
	</div>
	<br />
	  <table>
		<tr>
          <td class="left_title">Supplier Name:</td>
          <td><input name="supplier_name_ch" type="text" class="intext" id="supplier_name_ch" value="<?= $supplier['supplier_name_ch']; ?>" /></td>
	  </tr>
		<tr>
          <td class="left_title">Head Office Address:</td>
          <td><textarea name="address_ch" cols="40" rows="5" id="address_ch"><?= $supplier['address_ch']; ?></textarea>
          </td>
	  </tr>
		<tr>
          <td class="left_title">Factory Name:</td>
          <td><input name="factory_name_ch" type="text" class="intext" id="facory_name_ch" value="<?= $supplier['factory_name_ch']; ?>"></td>
	  </tr>
		<tr>
          <td class="left_title">Factory Address: </td>
          <td><textarea name="factory_address_ch" cols="40" rows="5" id="factory_address_ch"><?= $supplier['factory_address_ch']; ?></textarea>
          </td>
	  </tr>
		<tr>
          <td class="left_title">District Office: </td>
          <td><input name="doffice_name_ch" type="text" class="intext" id="doffice_name_ch" value="<?= $supplier['doffice_name_ch']; ?>"></td>
	  </tr>
		<tr>
          <td class="left_title">District Office Address: </td>
          <td><textarea name="doffice_address_ch" cols="40" rows="5" id="doffice_address_ch"><?= $supplier['doffice_address_ch']; ?></textarea>
          </td>
	  </tr>
	  </table>
	  <br />
	  <br />
	<div class="flagHeader">
		<span>
			Common Details
		</span>
	</div>
	<br />
	  <table>
		<tr>
			<td class="left_title">Country:</td>
			<td>
				<select name="country">
					<option></option>
<?php
//Patch: 2018/05/08 - Daniel Gault
//Note: (Warning) Invalid argument supplied for foreach() @160
include_once __DIR__ . '/../functions/country.inc.php';
$countries = makeCountryArray();
?>
<?php
foreach($countries as $c): ?>
					<option value="<?= $c['code']; ?>"<?= ($c['code'] == $supplier['country_code']) ? " selected" : "";?>><?= trim($c['name']); ?></option>
<?php
endforeach; ?>
				</select>
			</td>
		</tr>
		<tr class="alt">
			<td class="left_title">Head Office Phone:</td>
			<td>
				<table class="phonenums">
					<tr class="alt">
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="phone_cc" type="text" class="intext" value="<?= $supplier['phone_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="phone_ac" type="text" class="intext" value="<?= $supplier['phone_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="phone" type="text" class="intext" value="<?= $supplier['phone']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr>
          <td class="left_title">Head Office Fax:</td>
			<td>
				<table class="phonenums">
					<tr>
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="fax_cc" type="text" class="intext" value="<?= $supplier['fax_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="fax_ac" type="text" class="intext" value="<?= $supplier['fax_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="fax" type="text" class="intext" value="<?= $supplier['fax']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr class="alt">
          <td class="left_title">Factory Phone:</td>
			<td>
				<table class="phonenums">
					<tr class="alt">
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="factory_phone_cc" type="text" class="intext" value="<?= $supplier['factory_phone_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="factory_phone_ac" type="text" class="intext" value="<?= $supplier['factory_phone_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="factory_phone" type="text" class="intext" value="<?= $supplier['factory_phone']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr>
          <td class="left_title">Factory Fax:</td>
			<td>
				<table class="phonenums">
					<tr>
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="factory_fax_cc" type="text" class="intext" value="<?= $supplier['factory_fax_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="factory_fax_ac" type="text" class="intext" value="<?= $supplier['factory_fax_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="factory_fax" type="text" class="intext" value="<?= $supplier['factory_fax']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr class="alt">
          <td class="left_title">District Office Phone:</td>
			<td>
				<table class="phonenums">
					<tr class="alt">
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="doffice_phone_cc" type="text" class="intext" value="<?= $supplier['doffice_phone_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="doffice_phone_ac" type="text" class="intext" value="<?= $supplier['doffice_phone_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="doffice_phone" type="text" class="intext" value="<?= $supplier['doffice_phone']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr>
          <td class="left_title">District Office Fax:</td>
			<td>
				<table class="phonenums">
					<tr>
						<td class="collapse">Country Code:</td>
						<td class="ctrycode"><input name="doffice_fax_cc" type="text" class="intext" value="<?= $supplier['doffice_fax_cc']; ?>" /></td>
						<td class="collapse">Area Code:</td>
						<td class="areacode"><input name="doffice_fax_ac" type="text" class="intext" value="<?= $supplier['doffice_fax_ac']; ?>" /></td>
						<td class="collapse">Phone Number:</td>
						<td><input name="doffice_fax" type="text" class="intext" value="<?= $supplier['doffice_fax']; ?>" /></td>
					</tr>
				</table>
			</td>
	    </tr>
		<tr>
          <td class="left_title">Website:</td>
          <td><input name="website" type="text" class="intext" value="<?= $supplier['website']?$supplier['website']:"http://"; ?>" /></td>
	    </tr>
		<tr>
          <td class="left_title">Website 2:</td>
          <td><input name="website2" type="text" class="intext" value="<?= $supplier['website2']?$supplier['website2']:"http://"; ?>" /></td>
	    </tr>
		<tr>
          <td class="left_title">Head Office Email:</td>
          <td><input name="email" type="text" class="intext" id="email" value="<?= $supplier['email']; ?>"></td>
	    </tr>
		<tr>
          <td class="left_title">Business Nature:</td>
          <td><input name="business_nature" type="text" class="intext" id="business_nature" value="<?= $supplier['business_nature']; ?>"></td>
	    </tr>
		<tr>
          <td class="left_title">Bank Details: </td>
          <td><textarea name="bank_detail" cols="40" rows="5" id="bank_detail"><?= $supplier['bank_detail']; ?></textarea></td>
	    </tr>
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
          <td class="left_title">Where to Find: <br />(File Location)</td>
          <td><input name="where_to_find" type="text" class="intext" id="where_to_find" value="<?= $supplier['where_to_find']; ?>"></td>
	    </tr>
		<tr>
          <td class="left_title">Active:</td>
          <td><select name="active">
              <option value="1"<?= $supplier['active'] ? " selected=\"selected\"" : ""; ?>>Yes</option>
              <option value="0"<?= !$supplier['active'] ? " selected=\"selected\"" : ""; ?>>No</option>
            </select>
          </td>
	  </tr>
	  </table>
	  <br />
	  <br />
	<div class="flagHeader">
		<span>
			Contacts
		</span>
	</div>
	  <br />
	  <table>
<?php
if($mode == 'edit') : ?>
		<tr>
          <td class="left_title" style="width:30%">Contact People:</td>
	  <td>
		<select name="edit_contact" id="edit_contact" onchange="change_email()">
<?php
if (array_key_exists('contacts', $supplier) && is_array($supplier['contacts'])) foreach($supplier['contacts'] as $c) : ?>
	              <option value="<?= $c['contact_email']; ?>:<?=$c['contact_id'];?>">
              <?= $c['contact_name']; ?>
              </option>
<?php
endforeach; ?>
		</select>
	</td>
	<td style="width: 5%"><!-- class="collapse"-->&nbsp;<a href="contacts_admin.php?action=add&amp;supplier_id=<?=$supplier['supplier_id']?>" onclick="javascript:return popup('contacts_admin.php?action=add&amp;supplier_id=<?=$supplier['supplier_id']?>');">Add</a>&nbsp;|&nbsp;<a href="contacts_admin.php?action=edit" onclick="javascript:return popup('contacts_admin.php?action=edit&contact_id='+edit_contact_id);">Edit</a></td>
	</tr>
		<tr>
          <td class="left_title">Contact email:</td>
          <td colspan="2"><input name="contact_email" type="text" class="intext" value="<?= $supplier['contacts'][0]['contact_email']; ?>" /></td>
	  </tr>
<?php
else: ?>
		<tr>
          <td class="left_title">Main Contact Person:</td>
          <td><input name="contact_name" type="text" class="intext" value="" /></td>
	  </tr>
		<tr>
          <td class="left_title">Main Contact email:</td>
          <td><input name="contact_email" type="text" class="intext" value="" /></td>
	  </tr>
		<tr>
          <td class="left_title">Main Contact Position:</td>
          <td><input name="contact_position" type="text" class="intext" value="" /></td>
	  </tr>
	<tr><td class="left_title" colspan="2">&nbsp;</td></tr>
	<tr>
          <td class="left_title">Second Contact Person:</td>
          <td><input name="contact2_name" type="text" class="intext" value="" /></td>
	  </tr>
		<tr>
          <td class="left_title">Second Contact email:</td>
          <td><input name="contact2_email" type="text" class="intext" value="" /></td>
	  </tr>
		<tr>
          <td class="left_title">Second Contact Position:</td>
          <td><input name="contact2_position" type="text" class="intext" value="" /></td>
	  </tr>
<?php
endif; ?>

	</table>
	<br />
	<br />
	<div class="flagHeader">
		<span>
			Admin Notes
		</span>
	</div>
	<br />
<?php
if($admin_notes && count($admin_notes)) : ?>
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
	<div style="text-align: center; width: 100%">
		<input name="submit" type="submit" value="Update" />
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
