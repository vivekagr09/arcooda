<?php
/**
 * @var int $staff_id
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWebsitePreferences\');">Website Preferences</a>')?>

<div id="sectionWebsitePreferences">

<table class="wide">
<?php
$match_array=array('hgr' => 11, 'car'=>16, 'cnv'=>9, 'he'=>2, 'hg'=>1, 'xbox'=>18, 'asp'=>20, 'gg'=>22, 'rda'=>23);
$alt=array('hgr' => "Highway Group", 'car'=>'Car Converters', 'cnv'=>'Converters.tv', 'he'=>'Highway Entertainment', 'hg'=>'Highway Games', 'xbox'=>'Xbox Arcade Machine', 'asp'=>'Arcade Spare Parts', 'gg'=>'Game Gate Asia', 'rda'=>'Radikal Darts');
$match_array=array_chunk($match_array,4,true);
foreach ($match_array as $array)
{
	echo '<tr>';
	$count=0;
	foreach ($array as $key=>$site_id)
	{
		$site_name=$alt[$key];
?>
		<td valign="middle">
			<input disabled="disabled" type="checkbox" onclick="javascript:updateSite('<?=$site_id ?>',this);" name="staff_sites[]" value="<?=$site_id ?>"<?=($checked)?' checked="checked"':''?> />
		</td>
		<td valign="middle">
			<img src="/webadmin/images/sites/site_<?=$site_id ?>/label.gif" alt="<?=$site_name ?>" title="<?=$site_name?>" /><br />
			<span style="font-size:10px;<?=($checked)?'':'display:none;' ?>" id="<?=$site_id ?>_span">Site Email: <input style="font-size:11px;" type="text" name="staff_emails[<?=$site_id?>]" value="<?=htmlspecialchars(staffEmailGetValue($staff_id, $site_id) ?? '')?>" /></span>
		</td>

<?php
		$count++;
	}
	if ($count<4) while ($count<4) { echo '<td colsan="2">&nbsp;</td>'; $count++; }
	echo '</tr>';
}
?>
</table>

<br /><br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
