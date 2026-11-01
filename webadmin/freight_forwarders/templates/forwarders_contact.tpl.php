<!-- forwarders_contact.tpl.php -->

<?php
function contact_row($ccount='[CONTACT_COUNT]',$contact=array())
{
	ob_start();
?>
<table style="width:100%;">
	<tr>
		<td class="left_title" style="width:10%;text-align:center" rowspan="3">
			<input type="checkbox" name="contact[<?php echo $ccount?>][main]" value="y" onclick="mainSelected(<?php echo $ccount?>)" <?php echo ($contact['main']=='y')?'checked="checked"':''?> />
			<br /><span id="space_<?php echo $ccount?>"><?php echo ($contact['main']=='y') ? 'Default' : '' ?></span>
		</td>
		<td class="left_title" style="width:20%;">Contact Name</td>
		<td><input type="hidden" name="contact[<?php echo $ccount ?>][id]" value="<?php hecho($contact['id']) ?>" /><input name="contact[<?php echo $ccount ?>][name]" type="text" style="width:98%;" value="<?php hecho($contact['name']) ?>" /></td>
		<td rowspan="3" class="left_title" style="width:25%;"><a href="javascript:deleteContact(<?php echo $ccount ?>);">Delete</a></td>
	</tr>
	<tr>
		<td class="left_title">Position</td>
		<td><input name="contact[<?php echo $ccount ?>][title]" type="text" style="width:98%;" value="<?php hecho($contact['title']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Email Address</td>
		<td><input name="contact[<?php echo $ccount ?>][email]" type="text" style="width:98%;" value="<?php hecho($contact['email']) ?>" /></td>
	</tr>
</table>
<?php
	return ob_get_clean();
}
?>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionContacts\');">Contact People for this Forwarder</a>','<a href="javascript:addContact();">Add New Contact</a>') ?>
<div id="sectionContacts">

<script type="text/javascript">
var firstContact=1;
var lastContact=<?php echo max(0,count($forwarder_contacts)) ?>;

function addContact()
{
	lastContact++;
	var ccount=String(lastContact);
	var cont_table=getElement('contact_table');
	if(cont_table==null) return;

	var newRow1=addTableRow(cont_table,'contact_row['+ccount+']');
	addTableCell(newRow1,<?php echo str_replace('[ccount]','" + ccount + "', jecho(contact_row('[ccount]'), 1)) ?>);
}

function deleteContact(contact_id)
{
	var theRow=getElement('contact_row['+String(contact_id)+']');
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}

function mainSelected(count)
{
	getElement('space_'+count).innerHTML='Default';
	for(i=firstContact;i<=lastContact;i++)
	{
		var elem=getElement('contact['+i+'][main]');
		if(elem!= null && count!=i)
		{
			elem.checked=false;
			getElement('space_'+i).innerHTML='';
		}
	}
}
</script>

<table class="wide" id="contact_table">
<?php
$ccount=1;
$contacts_affected=array();
if(count($forwarder_contacts)>0)
	foreach($forwarder_contacts as $contact)
	{
		echo '<tr id="contact_row['.$ccount.']"><td>'.contact_row($ccount++,$contact).'</td></tr>'."\n";
		$contacts_affected[]=$contact['id'];
	}
else
{
	$temp=$forwarder;
	unset($temp['id']);
	echo '<tr id="contact_row[0]"><td>'.contact_row(0,$temp).'</td></tr>'."\n";
}
?>
</table>

<input type="hidden" name="contacts_affected" value="<?php echo implode(',',$contacts_affected) ?>" />

<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionContacts_hideNote">
Contact People for this Forwarder can be viewed by Clicking "Contact People for this Forwarder" above
<br />
</div>

<!-- /forwarders_contact.tpl.php -->
