<?php
include_once('permissions_common.inc.php');
include_once('templates/permissions_header.tpl.php');

$site_id=intval($posted['site_id']);

if($posted['action']=='do_edit_permission') updateDefaultSections($posted);
elseif($posted['action']=='do_edit_handler')
{
	if(is_array($posted['sites'])) foreach($sites as $s_id=>$handler)
		$DB->query("UPDATE highwaygroup.site_default_handler SET staff_id=".intval($handler)." WHERE site_id=".intval($s_id));
}

$sections=permissionSections($site_id);
$admins=permissionSections($site_id,true);
?>
<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDefault\');">Default Access Sections</a>') ?>
<form action="" method="post"><div id="sectionDefault" id="sectionDefault">
	<input type="hidden" name="action" value="do_edit_permission" />
	<table style="width:35%;">
		<tr><th>Section</th><th>Default</th></tr>
<?php
foreach($sections as $section_id=>$section)
{
?>
		<tr><td class="left_title" style="text-align:center;width:65%;"><?php echo $section?></td><td style="width:35%;text-align:center;"><input type="checkbox" name="default[]" value="<?php echo $section_id?>" <?php echo isSectionDefault($section_id) ? 'checked' : ''?>/></tr>
<?php
}
foreach($admins as $section_id=>$section)
{
?>
		<tr style="background-color:#DFDFDF;text-align:center;">
			<td style="width:65%;"><b><?php echo $section?></b></td><td style="width:35%;"><input type="checkbox" name="default[]" value="<?php echo $section_id?>" />
		</tr>
<?php
}
?>
	</table>
	<center><input type="submit" value=" Update " /><center>
</div></form>
