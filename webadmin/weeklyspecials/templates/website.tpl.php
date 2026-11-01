<!-- website.tpl.php -->

<?php
$weeklyspecial_type=explode(',',$weeklyspecial['weeklyspecial_type']);
$active_type=array_intersect(explode(',',$weeklyspecial['site_id']),$weeklyspecial_type);
?>

<script type="text/javascript">
//<![CDATA[
function updateSelectColour(elem)
{
	if(elem) elem.style.backgroundColor=(elem.value=='y') ? '#00FF00' : '#FF0000';
}
function updateOverallDiscount(site_id)
{
	var match_array={3:3};
	var new_val='';
	if(site_id==1 || site_id==18) new_val='';
	else new_val=(getElement('weeklyspecial_type['+match_array[site_id]+']').checked) ? getValue('discounts['+site_id+']')+'% discount' : '';
	setAllValues('discount_'+site_id,new_val);
}
function addSpecialPrice(site_id,ret)
{
	var url='/webadmin/weeklyspecials/special_pricing.php?return_message='+ret+'&id=<?= $id ?>&site_id='+site_id;
	var features='scrollbars, resizable';
	window.open(url,'product_popup',features);
}
function setSpecialData(data)
{
	var table = getElement(data['return_message']+'_spec');
	if(table == null) return;
	var newRow = addTableRow(table,'spec_'+data['id']);
	if(table.rows.length % 2 == 1) newRow.className = 'alt';
	addTableCell(newRow,data['product_id']);
	addTableCell(newRow,data['product_name']);
	addTableCell(newRow,data['min_order']);
	addTableCell(newRow,'$'+data['product_onlineau']);
	addTableCell(newRow,'$'+data['product_online']);
	addTableCell(newRow,'$'+data['special_priceau']);
	addTableCell(newRow,'$'+data['special_price']);
	addTableCell(newRow,'<input type="button" value=" Remove " onclick="removeSpecialPrice(\''+data['return_message']+'\','+data['id']+');return false;" />');
}
function removeSpecialPrice(ret,id)
{
	if(confirm("Are you sure you want to delete the Special Pricing??"))
	{
		var url='/webadmin/weeklyspecials/special_pricing.php?action=do_delete&return_message='+ret+'&id='+id;
		var features='scrollbars, resizable, height=20, width=20';
		window.open(url,'product_popup',features);
	}
}
function deleteSpecialPrice(name, row_id)
{
	var table=getElement(name+'_spec');
	if(table==null) return;
	var theRow=getElement('spec_'+row_id);
	if(theRow) table.deleteRow(theRow.sectionRowIndex);
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialWebsite\');">Website Preferences</a>') ?>

<div id="sectionWeeklySpecialWebsite" style="background-color:yellow;">
	<table class="wide">
<?php
foreach(array_chunk($websites_list, 4, true) as $site_list)
{
?>
		<tr>
<?php
	$count = 0;
	foreach($site_list as $site_id=>$site_name)
	{
		$count++;
		$checked=in_array($site_id,$weeklyspecial_type);
?>
			<td><input type="checkbox" onclick="weeklyspecialSiteToggle('<?= $site_id ?>',this.checked)" name="weeklyspecial_type[]" value="<?= $site_id ?>"<?= ($checked)?' checked="checked"':'' ?> /></td>
			<td><img src="/webadmin/images/sites/site_<?= $site_id ?>/label.gif" alt="<?= $site_name ?>" /><br /><small><i><a<?= ($checked)?'':' style="display:none;"' ?> href="javascript:void(toggleSection('site_<?= $site_id ?>_prefs'));">Expand/Hide</a></i></small></td>
<?php
	}
	while ($count < 4)
	{
		$count++;
?>
			<td>&nbsp;</td><td>&nbsp;</td>
<?php
	}
?>
		</tr>
<?php
}
?>
	</table>
	<br /><br />

<?php
foreach($websites_list as $site_id=>$site_name)
{
	$prefs=$weeklyspecial_preferences[$site_id];
	$field='weeklyspecial_preferences['.$site_id.']';
?>
	<table class="wide" id="site_<?= $site_id ?>_prefs"<?= (in_array($site_id,$weeklyspecial_type))?'':' style="display:none;"' ?>>
		<tr><th colspan="2"><?= $site_name ?> Preferences</th></tr>
<?php
	switch($site_id)
	{
	case 3:
		$survey_types=array();
		$survey_answers=array();
		$query=$DB->query("SELECT survey_type,long_desc,parent FROM survey_types");
		while($row=$DB->fetch_assoc($query))
		{
			if($row['parent']==0) $survey_types[$row['survey_type']]=$row['long_desc'];
			else $survey_answers[$row['parent']][$row['survey_type']]=$row['long_desc'];
		}
		$DB->free_result($query);
?>
		<tr>
			<td class="left_title" style="width:18%;">Status :</td>
			<td><select name="weeklyspecial[site_id][]"<?= (in_array($site_id,$active_type))?' style="background-color:#0f0;"':' style="background-color:#f00;"' ?> onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor">
				<option style="background-color:#f00;" value="">Inactive</option>
				<option style="background-color:#0f0;" value="<?= $site_id ?>"<?= (in_array($site_id,$active_type))?' selected="selected"':'' ?>>Active</option>
			</select></td>
		</tr>
<?php
		foreach($survey_types as $type_id=>$type_name)
		{
?>
		<tr>
			<td class="left_title"><?= $type_name ?> :</td>
			<td><select name="<?= $field ?>[survey][<?= $type_id ?>]">
				<option value=""></option>
				<?= formatHTMLOptions($survey_answers[$type_id],$prefs['survey'][$type_id]) ?>
			</select></td>
		</tr>
<?php
		}
?>
		<tr>
			<td class="left_title">Mailing List :</td>
			<td><select name="<?= $field ?>[mailing_list]">
				<option value="n">No</option>
				<option value="y"<?= ($prefs['mailing_list']=='y')?' selected="selected"':'' ?>>Yes</option>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Currency :</td>
			<td><select name="<?= $field ?>[currency]">
				<option value=""></option>
				<?= formatHTMLOptions($section_handler->getOptionList('currency'),$prefs['currency']) ?>
			</select></td>
		</tr>
<?php
		break;
	}
?>
	</table>
	<br /><br />
<?php
}
?>
	<table class="wide" id="site_global_prefs">
		<tr><th colspan="2">Global Preferences</th></tr>
		<tr>
			<td class="left_title" style="width:18%;">Email Login :</td>
			<td><input class="intext" type="text" name="<?= $field ?>[email_address]" value="<?= htmlspecialchars($prefs['email_address']) ?>" /></td>
		</tr>
		<tr>
			<td class="left_title">New Password :</td>
			<td><input class="intext" type="password" name="<?= $field ?>[new_password]" value="" /></td>
		</tr>
		<tr>
			<td class="left_title">Confirm Password :</td>
			<td><input class="intext" type="password" name="<?= $field ?>[confirm_password]" value="" /></td>
		</tr>
	</table>
	<br /><br />
</div>

<!-- /website.tpl.php -->
