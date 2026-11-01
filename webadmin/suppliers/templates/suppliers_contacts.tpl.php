<div align="center">
<?php
$extra = '<small><i><a style="color:blue" href="#" onclick="javascript: addContact(); return false;">Add New Contact</a></i></small>';
echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionContacts\');">'.$dis_name.' Contacts</a>',$extra);
?>
</div>
<div id="sectionContacts">
<table class="wide" align="center" style="width:98%;" name="contact" id="contact">
<?php
$contacts=$supplier_contacts;
$count = 0;
if(count($contacts) > 0)
{
	foreach($contacts as $contact)
	{
?>
	<tr id="contact_row[<?=$contact['id']?>]" <?=($count%2==1)?'class="alt"':''?> conid="<?=$contact['id']?>">
		<td>
			<table class="wide" align="center" style="width:100%" border=0>
				<tr>
					<td class="left_title" valign="bottom" style="width:10%">&nbsp;</td>
					<td class="left_title" style="width: 15%">Contact Name :</td>
					<td>
						<input type="hidden" name="contacts[<?=$contact['id']?>][id]" value="<?=$contact['id']?>" />
						<input type="text" style="width:100%" name="contacts[<?=$contact['id']?>][name]" value="<?=$contact['name']?>" />
					</td>
					<td class="left_title">&nbsp;</td>
				</tr>
				<tr>
					<td class="left_title" style="width:1%;text-align: center">
						<input type="checkbox" name="contacts[<?=$contact['id']?>][main]" value="y" onclick="mainContactSelected(<?=$contact['id']?>)" <?=($contact['main']=='y') ? 'checked="checked"':''?>>
					</td>
					<td class="left_title" style="width: 15%">Position :</td>
					<td><input type="text" style="width:100%" name="contacts[<?=$contact['id']?>][title]" value="<?=$contact['title']?>" /></td>
					<td valign="bottom" class="left_title" style="text-align:center;width:5%">
						<a href = "#" onclick="javascript: delContact('<?=$contact['id']?>'); return false;">Delete</a>
					</td>
				</tr>
				<tr>
					<td class="left_title" style="text-align:center;width:10%" valign="top"><span id="main_txt[<?=$contact['id']?>]"><?=($contact['main']=='y') ? 'Preferred' : ''?></span></td>
					<td class="left_title" style="width: 15%">Email Address :</td>
					<td><input type="text" style="width:100%" name="contacts[<?=$contact['id']?>][email]" value="<?=$contact['email']?>" /></td>
					<td class="left_title">&nbsp;</td>
				</tr>
				<tr>
					<td class="left_title" style="width: 10%;">&nbsp;</td>
					<td class="left_title" style="width: 15%">Phone :</td>
					<td><input type="text" style="width:100%" name="contacts[<?=$contact['id']?>][phone]" value="<?=$contact['phone']?>" /></td>
					<td class="left_title">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
		$count++;
	}
}
?>
</table>
</div>
<br />