<!-- notify.tpl.php -->

<?php
if(is_array($weeklyspecial_notify))
{
?>

<script type="text/javascript">
//<![CDATA[
var notify=100;
function weeklyspecialNotifyAdd()
{
	var ship_table = getElement('weeklyspecial_notify_table');
	if(ship_table == null) return;
	var newRow = addTableRow(ship_table,'weeklyspecial_notify_rowa['+notify+']');
	addTableCell(newRow,'<table style="width:100%"><tr><td class="left_title" style="width:18%;">Company Name :</td><td style="width:32%;"><input type="hidden" name="weeklyspecial_notify['+notify+'][id]" value="" /><input type="hidden" name="weeklyspecial_notify['+notify+'][id]" value="<?= $id ?>" /><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][company_name]" value="" /></td><td class="left_title" style="width:18%;">Phone :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][phone_no]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Address :</td><td style="width:32%" rowspan="2"><textarea name="weeklyspecial_notify['+notify+'][address]"></textarea></td><td class="left_title" style="width:18%;">2nd Contact Number :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][phone_no2]" value="" /></td></tr><tr><td class="left_title" style="width:18%;"></td><td class="left_title" style="width:18%;">Fax :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][fax_no]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">City :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][city]" value="" /></td><td class="left_title" style="width:18%;">Mobile :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][mobile_no]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">State :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][state]" value="" /></td><td class="left_title" style="width:18%;">Post Code :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][post_code]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Country :</td><td style="width:32%"><select name="weeklyspecial_notify['+notify+'][country]"><option value="">Not Selected</option>'+<?=json_encode(formatHTMLOptions($section_handler->getOptionList('country')))?>+'</select></td><td class="left_title" style="width:18%;"></td><td style="width:32%"></td></tr><tr><td class="left_title" style="width:18%;">Attention To :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][contact]" value="" /></td><td class="left_title" style="width:18%;">Email :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify['+notify+'][email]" value="" /></td></tr><tr><td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialNotifyDelete('+notify+');" /></td></tr></table>');
	notify++;
}
function weeklyspecialNotifyDelete(row)
{
	if(confirm('Are You Sure you want to delete this notify party??'))
	{
		var theRow=getElement('weeklyspecial_notify_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
function weeklyspecialNotifyFromDetails()
{
	var key='weeklyspecial_notify['+notify+']';
	weeklyspecialNotifyAdd();
	setValue(key+'[company_name]',getValue('weeklyspecial[company]'));
	setValue(key+'[address]',getValue('weeklyspecial[address_1]'));
	setValue(key+'[city]',getValue('weeklyspecial[city]'));
	setValue(key+'[state]',getValue('weeklyspecial[state]'));
	setValue(key+'[post_code]',getValue('weeklyspecial[zip]'));
	setValue(key+'[country]',getValue('weeklyspecial[country]'));
	setValue(key+'[phone_no]',getValue('weeklyspecial[phone_number]'));
	setValue(key+'[phone_no2]',getValue('weeklyspecial[phone_no2]'));
	setValue(key+'[mobile_no]',getValue('weeklyspecial[mobile_number]'));
	setValue(key+'[fax_no]',getValue('weeklyspecial[fax_number]'));
	setValue(key+'[contact]',getValue('weeklyspecial[name]')+' '+getValue('weeklyspecial[surname]'));
	setValue(key+'[email]',getValue('weeklyspecial[email_address]'));
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialNotify\');">Notify Party</a>', '<a href="#" style="color:#000;" onclick="weeklyspecialNotifyFromDetails();return false;">Copy from Above</a> | <a href="#" style="color:#000;" onclick="weeklyspecialNotifyAdd();return false;">Add New Address</a>') ?>

<div id="sectionWeeklySpecialNotify">
	<table class="wide" id="weeklyspecial_notify_table">
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_notify as $notify)
	{
		$count++;
		$affected[]=$notify['id'];
?>
		<tr id="weeklyspecial_notify_rowa[<?= $count ?>]">
			<td><table style="width:100%">
				<tr>
					<td class="left_title" style="width:18%;">Company Name :</td>
					<td style="width:32%;">
						<input type="hidden" name="weeklyspecial_notify[<?= $count ?>][id]" value="<?= $notify['id'] ?>" />
						<input type="hidden" name="weeklyspecial_notify[<?= $count ?>][id]" value="<?= $notify['id'] ?>" />
						<input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][company_name]" value="<?= htmlspecialchars($notify['company_name']) ?>" />
					</td>
					<td class="left_title" style="width:18%;">Phone :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][phone_no]" value="<?= htmlspecialchars($notify['phone_no']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Address :</td>
					<td style="width:32%" rowspan="2"><textarea name="weeklyspecial_notify[<?= $count ?>][address]"><?= htmlspecialchars($notify['address']) ?></textarea></td>
					<td class="left_title" style="width:18%;">2nd Contact Number :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][phone_no2]" value="<?= htmlspecialchars($notify['phone_no2']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;"></td>
					<td class="left_title" style="width:18%;">Fax :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][fax_no]" value="<?= htmlspecialchars($notify['fax_no']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">City :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][city]" value="<?= htmlspecialchars($notify['city']) ?>" /></td>
					<td class="left_title" style="width:18%;">Mobile :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][mobile_no]" value="<?= htmlspecialchars($notify['mobile_no']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">State :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][state]" value="<?= htmlspecialchars($notify['state']) ?>" /></td>
					<td class="left_title" style="width:18%;">Post Code :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][post_code]" value="<?= htmlspecialchars($notify['post_code']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Country :</td>
					<td style="width:32%"><select name="weeklyspecial_notify[<?= $count ?>][country]">
						<option value="">Not Selected</option>
						<?= formatHTMLOptions($section_handler->getOptionList('country'),$notify['country']) ?>
					</select></td>
					<td class="left_title" style="width:18%;"></td>
					<td style="width:32%"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Attention To :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][contact]" value="<?= htmlspecialchars($notify['contact']) ?>" /></td>
					<td class="left_title" style="width:18%;">Email :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_notify[<?= $count ?>][email]" value="<?= htmlspecialchars($notify['email']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialNotifyDelete(<?= $count ?>);" /></td>
				</tr>
			</table></td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" name="weeklyspecial_notify_affected" value="<?= implode(',',$affected) ?>" />
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialNotify_hideNote">Notify parties can be viewed by Clicking "Notify Party" above<br /></div>

<?php
}
?>

<!-- /notify.tpl.php -->
