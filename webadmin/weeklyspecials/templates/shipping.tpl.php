<!-- shipping.tpl.php -->

<?php
if(is_array($weeklyspecial_shipping))
{
?>

<script type="text/javascript">
//<![CDATA[
var shipping=100;
function weeklyspecialShippingAdd()
{
	var ship_table = getElement('weeklyspecial_shipping_table');
	if(ship_table == null) return;
	var newRow = addTableRow(ship_table,'weeklyspecial_shipping_rowa['+shipping+']');
	addTableCell(newRow, '<table style="width:100%;"><tr><td class="left_title" style="width:18%;">Company Name:</td><td style="width:32%;"><input type="hidden" name="weeklyspecial_shipping[' + shipping + '][id]" value="" /><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][company]" value="" /></td><td class="left_title" style="width:18%;">Phone :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][phone_number]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Address :</td><td style="width:32%" rowspan="2"><textarea name="weeklyspecial_shipping[' + shipping + '][address_1]" rows="2" cols="40"></textarea></td><td class="left_title" style="width:18%;">2nd Contact Number :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][phone_no2]" value="" /></td></tr><tr><td class="left_title" style="width:18%;"></td><td class="left_title" style="width:18%;">Fax :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][fax_number]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">City :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][city]" value="" /></td><td class="left_title" style="width:18%;">Mobile :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][mobile_number]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">State :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][state]" value="" /></td><td class="left_title" style="width:18%;">Post Code :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][zip]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Country :</td><td style="width:32%"><select name="weeklyspecial_shipping[' + shipping + '][country]"><option value="">Not Selected</option>' + <?=json_encode(formatHTMLOptions($section_handler->getOptionList('country')))?> + '</select></td><td class="left_title" style="width:18%;"></td><td style="width:32%"></td></tr><tr><td class="left_title" style="width:18%;">Main Contact Name :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][name]" value="" /></td><td class="left_title" style="width:18%;">Email :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[' + shipping + '][email]" value="" /></td></tr><tr><td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialShippingDelete(' + shipping + ')" /></td></tr></table>');
	shipping++;
}
function weeklyspecialShippingDelete(row)
{
	if(confirm('Are You Sure you want to delete this shipping address??'))
	{
		var theRow=getElement('weeklyspecial_shipping_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
function weeklyspecialShippingFromDetails()
{
	var key='weeklyspecial_shipping['+shipping+']';
	weeklyspecialShippingAdd();
	setValue(key+'[company]',getValue('weeklyspecial[company]'));
	setValue(key+'[name]',getValue('weeklyspecial[name]')+' '+getValue('weeklyspecial[surname]'));
	setValue(key+'[email]',getValue('weeklyspecial[email_address]'));
	setValue(key+'[address_1]',getValue('weeklyspecial[address_1]'));
	setValue(key+'[city]',getValue('weeklyspecial[city]'));
	setValue(key+'[state]',getValue('weeklyspecial[state]'));
	setValue(key+'[country]',getValue('weeklyspecial[country]'));
	setValue(key+'[zip]',getValue('weeklyspecial[zip]'));
	setValue(key+'[phone_number]',getValue('weeklyspecial[phone_number]'));
	setValue(key+'[fax_number]',getValue('weeklyspecial[fax_number]'));
	setValue(key+'[mobile_number]',getValue('weeklyspecial[mobile_number]'));
	setValue(key+'[company]',getValue('weeklyspecial[company]'));
	setValue(key+'[phone_no2]',getValue('weeklyspecial[phone_no2]'));
}
function weeklyspecialShippingGetRows()
{
	var ship_table=getElement("weeklyspecial_shipping_table");
	var ship_rows=[];
	var ship_rows2={};
	if(ship_table!=null)
	{
		var theRows=ship_table.rows;
		for(var i=0;i<theRows.length;i++) if(theRows[i].id.substr(0,21)=='weeklyspecial_shipping_rowa[')
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
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialShipping\');">Shipping Address</a>', '<a href="#" style="color:#000;" onclick="weeklyspecialShippingFromDetails();return false;">Copy from Above</a> | <a href="#" style="color:#000;" onclick="weeklyspecialShippingAdd();return false;">Add New Address</a>') ?>

<div id="sectionWeeklySpecialShipping">
	<table class="wide" id="weeklyspecial_shipping_table">
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_shipping as $shipping)
	{
		$count++;
		$affected[]=$shipping['id'];
?>
		<tr id="weeklyspecial_shipping_rowa[<?= $count ?>]">
			<td><table style="width:100%;">
				<tr>
					<td class="left_title" style="width:18%;">Company Name:</td>
					<td style="width:32%;">
						<input type="hidden" name="weeklyspecial_shipping[<?= $count ?>][id]" value="<?= $shipping['id'] ?>" />
						<input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][company]" value="<?= htmlspecialchars($shipping['company']) ?>" />
					</td>
					<td class="left_title" style="width:18%;">Phone :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][phone_number]" value="<?= htmlspecialchars($shipping['phone_number']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Address :</td>
					<td style="width:32%" rowspan="2">
						<textarea name="weeklyspecial_shipping[<?= $count ?>][address_1]" rows="2" cols="40"><?= htmlspecialchars(trim($shipping['address_1']."\n".$shipping['address_2'])) ?></textarea>
						<input type="hidden" name="weeklyspecial_shipping[<?= $count ?>][address_2]" value="" />
					</td>
					<td class="left_title" style="width:18%;">2nd Contact Number :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][phone_no2]" value="<?= htmlspecialchars($shipping['phone_no2']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;"></td>
					<td class="left_title" style="width:18%;">Fax :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][fax_number]" value="<?= htmlspecialchars($shipping['fax_number']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">City :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][city]" value="<?= htmlspecialchars($shipping['city']) ?>" /></td>
					<td class="left_title" style="width:18%;">Mobile :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][mobile_number]" value="<?= htmlspecialchars($shipping['mobile_number']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">State :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][state]" value="<?= htmlspecialchars($shipping['state']) ?>" /></td>
					<td class="left_title" style="width:18%;">Post Code :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][zip]" value="<?= htmlspecialchars($shipping['zip']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Country :</td>
					<td style="width:32%"><select name="weeklyspecial_shipping[<?= $count ?>][country]">
						<option value="">Not Selected</option>
						<?= formatHTMLOptions($section_handler->getOptionList('country'),$shipping['country']) ?>
					</select></td>
					<td class="left_title" style="width:18%;"></td>
					<td style="width:32%"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Main Contact Name :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][name]" value="<?= htmlspecialchars(trim($shipping['name'].' '.$shipping['surname'])) ?>" /><input type="hidden" name="weeklyspecial_shipping[<?= $count ?>][surname]" value="" /></td>
					<td class="left_title" style="width:18%;">Email :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_shipping[<?= $count ?>][email]" value="<?= htmlspecialchars($shipping['email']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialShippingDelete(<?= $count ?>)" /></td>
				</tr>
			</table></td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" name="weeklyspecial_shipping_affected" value="<?= implode(',',$affected) ?>" />
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialShipping_hideNote">Shipping addresses can be viewed by Clicking "Shipping Address" above<br /></div>

<?php
}
?>

<!-- /shipping.tpl.php -->
