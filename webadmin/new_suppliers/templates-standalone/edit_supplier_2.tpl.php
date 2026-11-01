<?php
if(!(isset($mode) || $mode == "edit" || $mode == "add"))
{
	$mode = "edit";
}
?>
<script language="JavaScript" type="text/javascript">
<!--
// Simple script for changing values of a text box based on a select box.
// 20040921: Expanded to include 2 variables so we can set ID's in links too =[
var edit_contact_id = <?= $supplier['contacts'][0]['contact_id'] ?>;
function change_email()
{
	// Get Email form select box
	var box = document.forms[0].edit_contact;
	var text = box.options[box.selectedIndex].value;
	var email = text.substring(0, text.indexOf(":"));
	var id = text.substring(text.indexOf(":") + 1, text.length);
	if (!email) return;
	// Set Text Box
	document.forms[0].contact_email.value = email;
	edit_contact_id = id;
}
// -->
</script>

<form accept-charset="utf-8" name="add_edit_supplier" method="post" action="<?= $form_dest ?>">
	<input name="id" type="hidden" value="<?= $supplier['supplier_id'] ?>">
	<input name="supplier_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="contacts_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<input name="types_updated" type="hidden" value="1"><?php
// TODO: Update this with JS ?>
	<table class="thin">
      <tr class="nonalt">
          <td colspan="3" nowrap class="left_title">Supplier Details </td>
      </tr>
        <tr class="nonalt">
          <td nowrap>&nbsp;</td>
          <td>English</td>
          <td>Chinese</td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Name:</td>
          <td><input name="supplier_name" type="text" value="<?= $supplier['supplier_name'] ?>" /></td>
          <td><input name="supplier_name_ch" type="text" id="supplier_name_ch" value="<?= $supplier['supplier_name_ch'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Address:</td>
          <td><textarea name="address" cols="40" rows="5"><?= $supplier['address'] ?>
          </textarea></td>
          <td><textarea name="address_ch" cols="40" rows="5" id="address_ch"><?= $supplier['address_ch'] ?>
          </textarea></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Postal Code: </td>
          <td colspan="2"><input name="pcode" type="text" id="pcode" value="<?= $supplier['pcode'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Country:</td>
          <td colspan="2"><select name="country">
            <option></option>
<?php
foreach($countries as $c): ?>
            <option value="<?= $c['code'] ?>"<?= ($c['code'] == $supplier['country_code']) ? " selected" : "" ?>>
            <?= trim($c['name']) ?>
            </option>
<?php
endforeach; ?>
          </select></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title"><p>Phone:</p>
          </td>
          <td colspan="2"><input name="phone" type="text" value="<?= $supplier['phone'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Fax:</td>
          <td colspan="2"><input name="fax" type="text" value="<?= $supplier['fax'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Website:</td>
          <td colspan="2"><input name="website" type="text" value="<?= $supplier['website'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Email:</td>
          <td colspan="2"><input name="email" type="text" id="email" value="<?= $supplier['email'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Supply Type: </td>
          <td colspan="2"><input name="mach" type="checkbox" value="on"<?= $supplier['mach'] ?>/>
Full Machines<br />
<input name="kits" type="checkbox" value="on"<?= $supplier['kits'] ?>/>
PCB's and Kits<br />
<input name="part" type="checkbox" value="on"<?= $supplier['part'] ?>/>
Spare Parts<br />
<input name="merc" type="checkbox" value="on"<?= $supplier['merc'] ?>/>
Merchandise<br>
<input name="serv" type="checkbox" id="serv" value="on"<?= $supplier['serv'] ?>>
Services </td>
      </tr>
      </table>
	  <br />
	  <table class="thin">
        <tr class="nonalt">
          <td colspan="3" nowrap class="left_title">Factory Details </td>
        </tr>
        <tr class="nonalt">
          <td nowrap>&nbsp;</td>
          <td>English</td>
          <td>Chinese</td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Name:</td>
          <td><input name="factory_name" type="text" id="facory_name" value="<?= $supplier['factory_name'] ?>"></td>
          <td><input name="factory_name_ch" type="text" id="facory_name_ch" value="<?= $supplier['factory_name_ch'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Address:</td>
          <td><textarea name="factory_address" cols="40" rows="5" id="factory_address"><?= $supplier['factory_address'] ?>
          </textarea></td>
          <td><textarea name="factory_address_ch" cols="40" rows="5" id="factory_address_ch"><?= $supplier['factory_address_ch'] ?>
          </textarea></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Postal Code: </td>
          <td colspan="2"><input name="factory_pcode" type="text" id="factory_pcode" value="<?= $supplier['factory_pcode'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Phone:</td>
          <td colspan="2"><input name="factory_phone" type="text" id="factory_phone" value="<?= $supplier['factory_phone'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Fax:</td>
          <td colspan="2"><input name="factory_fax" type="text" id="factory_fax" value="<?= $supplier['factory_fax'] ?>" /></td>
        </tr>
      </table>
	  <br />
	  <table class="thin">
        <tr class="nonalt">
          <td colspan="3" nowrap class="left_title">District Office  Details </td>
        </tr>
        <tr class="nonalt">
          <td nowrap>&nbsp;</td>
          <td>English</td>
          <td>Chinese</td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Name:</td>
          <td><input name="doffice_name" type="text" id="doffice_name" value="<?= $supplier['doffice_name'] ?>"></td>
          <td><input name="doffice_name_ch" type="text" id="doffice_name_ch" value="<?= $supplier['doffice_name_ch'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Address:</td>
          <td><textarea name="doffice_address" cols="40" rows="5" id="doffice_address"><?= $supplier['doffice_address'] ?>
          </textarea></td>
          <td><textarea name="doffice_address_ch" cols="40" rows="5" id="doffice_address_ch"><?= $supplier['doffice_address_ch'] ?>
          </textarea></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Postal Code: </td>
          <td colspan="2"><input name="doffice_pcode" type="text" id="doffice_pcode" value="<?= $supplier['doffice_pcode'] ?>"></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Phone:</td>
          <td colspan="2"><input name="doffice_phone" type="text" id="doffice_phone" value="<?= $supplier['doffice_phone'] ?>" /></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Fax:</td>
          <td colspan="2"><input name="doffice_fax" type="text" id="doffice_fax" value="<?= $supplier['doffice_fax'] ?>" /></td>
        </tr>
      </table>
	  <br />
	  <table class="thin">
        <tr class="nonalt">
          <td colspan="2" nowrap class="left_title">Miscellaneous Details </td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Business Nature: </td>
          <td><span class="left_title">
            <input name="business_nature" type="text" id="business_nature" value="<?= $supplier['business_nature'] ?>">
          </span></td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Bank Details: </td>
          <td><textarea name="bank_detail" cols="40" rows="5" id="bank_detail"><?= $supplier['bank_detail'] ?>
            </textarea>
          </td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Where to Find: </td>
          <td>
            <input name="where_to_find" type="text" id="where_to_find" value="<?= $supplier['where_to_find'] ?>">
          </td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Admin Notes:</td>
          <td><textarea name="new_note" cols="40" rows="5"></textarea>
          </td>
        </tr>
        <tr class="nonalt">
          <td nowrap class="left_title">Active:</td>
          <td>
            <select name="active">
              <option value="1"<?= $supplier['active'] ? " selected=\"selected\"" : "" ?>>Yes</option>
              <option value="0"<?= !$supplier['active'] ? " selected=\"selected\"" : "" ?>>No</option>
            </select>
          </td>
        </tr>
      </table>
	  <br />
	  <table class="thin">
		<tr class="nonalt">
          <td colspan="2" class="left_title">Contacts</td>
	  </tr>
<?php
if($mode == 'edit') : ?>
		<tr class="nonalt">
          <td class="left_title" style="width:50%;">Contact People:</td>
          <td><select name="edit_contact" onchange="change_email()">
<?php
foreach($supplier['contacts'] as $c) : ?>
              <option value="<?= $c['contact_email'] ?>:<?= $c['contact_id'] ?>">
              <?= $c['contact_name'] ?>
              </option>
<?php
endforeach; ?>
            </select>
&nbsp;<a href="contacts_admin.php?action=add&amp;supplier_id=<?=$supplier['supplier_id']?>">Add</a>&nbsp;|&nbsp;<a href="javascript:magic" onclick="javascript:this.href='contacts_admin.php?action=edit&amp;id='+edit_contact_id;">Edit</a> </td>
	  </tr>
		<tr class="nonalt">
          <td class="left_title">Contact email:</td>
          <td><input name="contact_email" type="text" value="<?= $supplier['contacts'][0]['contact_email'] ?>" /></td>
	  </tr>
<?php
else: ?>
		<tr class="nonalt">
          <td class="left_title">Contact Person:</td>
          <td><input name="contact_name" type="text" value="" /></td>
	  </tr>
		<tr class="nonalt">
          <td class="left_title">Contact email:</td>
          <td><input name="contact_email" type="text" value="" /></td>
	  </tr>
<?php
endif; ?>
	</table>
	<br />
<?php
if(count($admin_notes)) : ?>
	<!-- Admin Notes -->
	<table class="wide">
		<th>User</th>
		<th>Date</th>
		<th>Note</th>
<?php
$shaded = true;
foreach($admin_notes as $n) : ?>
		<tr class="<?= ($shaded = !$shaded) ? "alt" : "nonalt" ?>">
			<td class="collapse"><?= str_replace(" ", "&nbsp;", $n['user']['first_name'] . " " . $n['user']['last_name']); ?></td>
			<td class="collapse"><?= date("d/m/Y,&\\nb\sp;H:i", $n['date']); ?></td>
			<td><?= $n['note'] ?></td>
		</tr>
<?php
endforeach; ?>
	</table>
	<!-- End Admin Notes -->
	<br />
<?php
endif; ?>
	<div style="text-align: center; width: 100%">
		<input name="submit" type="submit" value="Update" />
		<input name="reset" type="reset" value="Reset" />
	</div>
	<br />
<?php
if($mode == "edit"):
$shaded = false; ?>
	<h3 style="text-align: center">Open Purchase Orders for <?= $supplier['supplier_name'] ?></h3>
	<table class="wide">
		<tr class="nonalt">
			<th>P/O #</th>
			<th>Date</th>
			<th>Ordered By</th>
			<th>Our Contact</th>
			<th class="collapse">&nbsp;</th>
		</tr>
<?php
if(is_array($po_summary) && (count($po_summary) > 0)):
		foreach($po_summary as $o): ?>
		<tr class="<?= $shaded ? "alt" : "nonalt" ?>">
			<td>#<?= $o['porder_id'] ?></td>
			<td><?= date("d/m/Y", $o['creation_date']) ?></td>
			<td><?= $o['user_name'] ?></td>
			<td><?= $o['contact_name'] ?></td>
			<td class="collapse"><a href="purchaseorders_admin.php?action=edit&amp;id=<?= $o['porder_id'] ?>">Edit</a>&nbsp;|&nbsp;<a href="view_purchaseorder.php?id=<?=$o['porder_id']?>" target="_blank">View</a></td>
		</tr>
<?php
endforeach;
	else: ?>
		<tr class="nonalt">
			<td colspan=5 style="text-align: center">No open purchase orders listed for this supplier</td>
		</tr>
<?php
endif; ?>
	</table>
<?php
endif; ?>
</form>
