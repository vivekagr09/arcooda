<?php
/**
 * @var string $action
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $DB;
?>

<script type="text/javascript">
//<![CDATA[
function updateSite(site_id, elem)
{
	var display=(elem.checked) ? '' : 'none';
<?php
if ($action == 'add') {
?>
	display='none';
<?php
}
?>
	getElement(site_id+'_a').style.display=display;
	getElement(site_id+'_span').style.display=display;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdministration\');">Administration</a>')?>

<div id="sectionAdministration">

<table style="width:75%;">
	<tr>
		<td class="left_title" style="width:25%;">Date Started (yyyy-mm-dd)</td>
		<td><input type="date" name="start_date" value="<?=($staff['start_time'] != 0) ? date('Y-m-d', $staff['start_time']) : ''?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Date Finished (yyyy-mm-dd)</td>
		<td><input type="date" name="end_date" value="<?=($staff['end_time'] != 0) ? date('Y-m-d', $staff['end_time']) : ''?>" /></td>
	</tr>
<?php
if ($staff['start_time']>0)
{
	$temp=(($staff['end_time']>0)?$staff['end_time']:time())-$staff['start_time'];
	$here_years=floor($temp/31557600);
	$temp%=31557600;
	$here_months=floor($temp/2629800);
	$temp%=2629800;
	$here_days=floor($temp/86400);
?>
	<tr>
		<td class="left_title">Time at Highway</td>
		<td><?=$here_years?> years, <?=$here_months?> months, <?=$here_days?> days</td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title">Comments</td>
		<td><textarea name="admin_notes" rows="8" onkeyup="javascript:textareaAutoSize(this,1,8);"></textarea></td>
	</tr>
</table>

</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdministration_hideNote">
Administration can be viewed by Clicking "Administration" above
</div>

<br /><br />

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSites\');">Website Preferences</a>')?>

<div id="sectionSites">

<table style="width:95%;">
<?php
$match_array=array();
$alt=array();
$query=$DB->query("SELECT company_id,company_invoicesuffix,company_name FROM `highwaygroup`.`company_info` ORDER BY company_name");
while ($row=$DB->fetch_assoc($query))
{
	$match_array[$row['company_invoicesuffix']]=$row['company_id'];
	$alt[$row['company_invoicesuffix']]=$row['company_name'];
}
$DB->free_result($query);

$match_array=array_chunk($match_array,4,true);
foreach ($match_array as $row=>$array)
{
	echo '<tr>';
	$count=0;
	foreach ($array as $key=>$site_id)
	{
		$checked=(in_array($site_id,$staff_sites)) ? true : false;
		$site_name=$alt[$key];
?>
		<td valign="middle"><input type="checkbox" onclick="updateSite('<?=$site_id?>', this)" name="staff_sites[]" value="<?=$site_id?>"<?=($checked) ? ' checked="checked"' : ''?> /></td>
		<td valign="middle">
			<img src="/webadmin/images/sites/site_<?=$site_id?>/label.gif" alt="<?=$site_name?>" title="<?=$site_name?>" /><br />
			<small><i><a<?=($checked) ? '' : ' style="display:none"'?> id="<?=$site_id?>_a" href="/webadmin/permissions/permissions_options.php?site_id=<?=$site_id?>&username=<?=$staff['user']?>" target="_blank"> Website Settings</a></small></i>
			<br />
			<span style="font-size:10px;<?=($checked) ? '' : 'display:none'?>" id="<?=$site_id?>_span">Site Email: <input style="font-size:11px;" type="text" name="staff_emails[<?=$site_id?>]" value="<?=htmlspecialchars(staffEmailGetValue($staff_id, $site_id) ?? '')?>" /></span>
		</td>

<?php
		$count++;
	}
	if ($count<4) while ($count<4) { echo '<td colspan="2">&nbsp;</td>'; $count++; }
	echo '</tr>';
}
?>
</table>

<br /><br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
