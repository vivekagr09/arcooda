<?php
/**
 * @var mixed[]  $posted
 * @var string[] $all_staff_list
 * @var string[] $active_staff
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/permissions_header.tpl.php';

$site_id = intval($posted['site_id']);
$action  = $posted['action'];
if ('do_update' == $action) {
    permissionsToDatabase($posted['permissions'], $site_id);
}

$sites = array(1 => 'Highway Games', 2 => 'Highway Entertainment', 9 => 'Converters.tv', 11 => 'Highway Group', 16 => 'Car Converters', 17 => 'Boxer Machine');
if (0 == $site_id) {
    // no site selected so print out the site list..
?>
<table border="1">
	<tr><th>Select A Site to edit permissions for</th></tr>
<?php
foreach ($sites as $site_id => $site) {
?>
	<tr><td class="alt"><a target="_blank" href="?site_id=<?=$site_id?>"><?=htmlspecialchars($site)?></a></td></tr>
<?php
}
?>
</table>
<?php
} else {
?>
	<script>
	function toggleCheckboxes(section_id,check)
	{
		var startName='permissions[';
		var endName=']['+section_id+']';
<?php
foreach ($all_staff_list as $user => $user_name) {
?>
		var theElem=getElement(startName+'<?=$user?>'+endName);
		if (theElem) theElem.checked=check;
<?php
}
?>
	}
	</script>
	<form action="" method="post">

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'site_' . $site_id . '\');">' . $sites[$site_id] . '</a>')?>

	<input type="hidden" name="action" value="do_update" />
	<input type="hidden" name="site_id" value="<?=$site_id?>" />
	<div id="site_<?=$site_id?>" id="site_<?=$site_id?>">
<?php
$sections    = permissionSections($site_id);
    $permissions = permissionsFromDatabase($site_id);
?>
		<table class="wide" border="1">
			<tr>
				<th>&nbsp;</th>
<?php
foreach ($sections as $section) {
?>
				<th><?=$section?></th>
<?php
}
?>
			</tr>
<?php
$count = 0;
    foreach ($active_staff as $user => $staff_name) {
?>
			<tr <?=($count % 2) ? 'style="background-color:#EAEAEA;"' : ''?>><th><?=$staff_name?></th>
<?php
foreach ($sections as $section_id => $section) {
            $checked = '';
            if (is_array($permissions[$user])) {
                $checked = (in_array($section_id, $permissions[$user])) ? ' checked="checked"' : '';
            }

?>
				<td style="text-align:center"><input type="checkbox" name="permissions[<?=$user?>][<?=$section_id?>]"<?=$checked?> /></td>
<?php
}
?>
			</tr>
<?php
$count++;
    }
?>
			<tr>
				<th>&nbsp;</th>
<?php
foreach ($sections as $section_id => $section) {
?>
				<td style="text-align:center;background-color:#AAAAAA"><input type="checkbox" onclick="toggleCheckboxes(<?=$section_id?>,this.checked)" /></td>
<?php
}
?>
			</tr>
		</table>
		<br />
		<div class="text-center"><input type="submit" value=" Update " style="width:150px;"/></div>
	</div>
	</form>
<?php
}
