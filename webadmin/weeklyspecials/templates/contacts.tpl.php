<!-- contacts.tpl.php -->

<?php
if(is_array($weeklyspecial_contacts))
{
?>

<script type="text/javascript">
//<![CDATA[
var contacts=100;
function weeklyspecialContactAdd()
{
	var con_table = getElement('weeklyspecial_contacts_table');
	if(con_table == null) return;
	var newRow = addTableRow(con_table,'weeklyspecial_contacts_rowa['+contacts+']');
	addTableCell(newRow,'<table class="wide" align="center" style="width:100%;" border="0"><tr><td rowspan="4" class="left_title" style="width:10%;text-align:center;vertical-align:middle;"><input type="hidden" name="weeklyspecial_contacts['+contacts+'][id]" value="" /><input type="hidden" name="weeklyspecial_contacts['+contacts+'][id]" value="<?= $id ?>" /><input type="checkbox" name="weeklyspecial_contacts['+contacts+'][main]" value="y" onclick="weeklyspecialContactSelect('+contacts+')" /><br /><span id="weeklyspecial_contacts_default['+contacts+']" style="display:none;"><small>Default</small></span></td><td class="left_title" style="width:25%;">Contact Name :</td><td style="width:55%;"><input class="intext" type="text" name="weeklyspecial_contacts['+contacts+'][name]" value="" /></td><td rowspan="4" class="left_title" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="weeklyspecialContactDelete('+contacts+');return false;">Delete</a></td></tr><tr><td class="left_title">Title :</td><td><input class="intext" type="text" name="weeklyspecial_contacts['+contacts+'][title]" value="" /></td></tr><tr><td class="left_title">Email Address :</td><td><input class="intext" type="text" name="weeklyspecial_contacts['+contacts+'][email]" value="" /></td></tr><tr><td class="left_title">Phone :</td><td><input class="intext" type="text" name="weeklyspecial_contacts['+contacts+'][phone]" value="" /></td></tr><tr><td colspan="2" class="left_title">Emails sent to this address</td><td><select name="weeklyspecial_contacts['+contacts+'][send_email_cc]"><option value="y">Yes</option><option value="n" selected="selected">No</option></select></td></tr></table>');
	var con_rows=weeklyspecialContactGetRows();
	if(con_rows.length==1) weeklyspecialContactSelect(con_rows[0]);
	contacts++;
}
function weeklyspecialContactDelete(row)
{
	if(confirm('Are You Sure you want to delete this contact??'))
	{
		var theRow=getElement('weeklyspecial_contacts_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		var con_rows=weeklyspecialContactGetRows();
		if(con_rows.length==1) weeklyspecialContactSelect(con_rows[0]);
	}
}
function weeklyspecialContactGetRows()
{
	var con_table=getElement("weeklyspecial_contacts_table");
	var con_rows=[];
	var con_rows2={};
	if(con_table!=null)
	{
		var theRows=con_table.rows;
		for(var i=0;i<theRows.length;i++) if(theRows[i].id.substr(0,21)=='weeklyspecial_contacts_rowa[')
		{
			var key=theRows[i].id.substr(21);
			key=key.substr(0,key.length-1);
			if(con_rows2[key]==null)
			{
				con_rows2[key]=true;
				con_rows[con_rows.length]=key;
			}
		}
	}
	return con_rows;
}
function weeklyspecialContactSelect(row)
{
	var con_rows=weeklyspecialContactGetRows();
	for(var i=0;i<con_rows.length;i++) if(con_rows[i]==row)
	{
		getElement('weeklyspecial_contacts['+con_rows[i]+'][main]').checked=true;
		getElement('weeklyspecial_contacts_default['+con_rows[i]+']').style.display='';
	}
	else
	{
		getElement('weeklyspecial_contacts['+con_rows[i]+'][main]').checked=false;
		getElement('weeklyspecial_contacts_default['+con_rows[i]+']').style.display='none';
	}
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialContacts\');">Company Contacts</a>','<a style="color:black;" href="#" onclick="weeklyspecialContactAdd();return false;">Add New Contact</a>') ?>

<div id="sectionWeeklySpecialContacts">
	<table class="wide" id="weeklyspecial_contacts_table">
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_contacts as $contact)
	{
		if($contact['email']==$weeklyspecial['email_address']) $contact['send_email_cc']='y';
		$count++;
		$affected[]=$contact['id'];
?>
		<tr id="weeklyspecial_contacts_rowa[<?= $count ?>]">
			<td><table class="wide" align="center" style="width:100%;" border="0">
				<tr>
					<td rowspan="4" class="left_title" style="width:10%;text-align:center;vertical-align:middle;">
						<input type="hidden" name="weeklyspecial_contacts[<?= $count ?>][id]" value="<?= $contact['id'] ?>" />
						<input type="hidden" name="weeklyspecial_contacts[<?= $count ?>][id]" value="<?= $contact['id'] ?>" />
						<input type="checkbox" name="weeklyspecial_contacts[<?= $count ?>][main]" value="y"<?= ($contact['main']=='y'?' checked="checked"':'') ?> onclick="weeklyspecialContactSelect(<?= $count ?>)" /><br />
						<span id="weeklyspecial_contacts_default[<?= $count ?>]"<?= ($contact['main']=='y')?'':' style="display:none;"' ?>><small>Default</small></span>
					</td>
					<td class="left_title" style="width:25%;">Contact Name :</td>
					<td style="width:55%;"><input class="intext" type="text" name="weeklyspecial_contacts[<?= $count ?>][name]" value="<?= htmlspecialchars($contact['name']) ?>" /></td>
					<td rowspan="4" class="left_title" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="weeklyspecialContactDelete(<?= $count ?>);return false;">Delete</a></td>
				</tr>
				<tr>
					<td class="left_title">Title :</td>
					<td><input class="intext" type="text" name="weeklyspecial_contacts[<?= $count ?>][title]" value="<?= htmlspecialchars($contact['title']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title">Email Address :</td>
					<td><input class="intext" type="text" name="weeklyspecial_contacts[<?= $count ?>][email]" value="<?= htmlspecialchars($contact['email']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title">Phone :</td>
					<td><input class="intext" type="text" name="weeklyspecial_contacts[<?= $count ?>][phone]" value="<?= htmlspecialchars($contact['phone']) ?>" /></td>
				</tr>
				<tr>
					<td colspan="2" class="left_title">Emails sent to this address</td>
					<td><select name="weeklyspecial_contacts[<?= $count ?>][send_email_cc]">
						<option value="y">Yes</option>
						<option value="n"<?= ($contact['email']!=$weeklyspecial['email_address'])?' selected="selected"':'' ?>>No</option>
					</select></td>
				</tr>
			</table></td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" name="weeklyspecial_contacts_affected" value="<?= implode(',',$affected) ?>" />
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialContacts_hideNote">Company Contacts can be viewed by Clicking "Company Contacts" above<br /></div>

<?php
}
?>

<!-- /contacts.tpl.php -->
