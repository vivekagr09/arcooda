<!-- members_mailing.tpl.php -->

<?php
/* TODO: put this bit inside a function somewhere on the Mailing List side... */
$templates = array();
if ($query = $DB->query(sprintf(
    "SELECT `style_id`, `site_id`, `style_description` FROM `highwaygroup`.`newsletter_styles`%s ORDER BY `style_description` ASC",
    ('' != $posted['site_id'] && '11' != $posted['site_id']) ? sprintf(
        " WHERE `site_id` = %d",
        $posted['site_id']
    ) : ""
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $templates[$row['style_id']] = $row;
    }
    $DB->free_result($query);
}

?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMailing\');return false">Mailing Lists</a>')?>

<div id="sectionMailing">
	<table class="wide" border="1" border="#CCC">
		<tr>
			<th>Mailing List Template</th>
			<th>Active</th>
			<th>Joined/Cancelled</th>
			<th>Comments</th>
		</tr>
<?php
$website_ids = memberGetOptionList("site_id");
foreach ($templates as $template_id => $template) {
    if (array_key_exists($template['site_id'], $website_ids)) {
        $mailing        = $member_mailinglists[$template_id];
        $joined_time    = $mailing['time_joined'];
        $cancelled_time = $mailing['time_cancelled'];
        $name_key       = 'members_mailing[' . $template_id . ']';
?>
		<tr style="background-color:#<?=('y' == $mailing['active']) ? 'EF' : 'FE'?>E;text-align:center">
			<td style="width:20%"><input type="hidden" name="<?=$name_key?>[style_id]" value="<?=$template_id?>" /><?=htmlspecialchars($template['style_description'])?></td>
			<td style="width:20%"><select name="<?=$name_key?>[active]"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $mailing['active'])?></select></td>
			<td style="width:20%"><?=($joined_time) ? 'Date Joined: ' . date(_('d/m/Y'), $joined_time) . '<br />' : ''?><?=($cancelled_time && date(_('d/m/Y'), $cancelled_time) != '30/11/-0001') ? 'Date Cancelled: ' . date(_('d/m/Y'), $cancelled_time) : ''?></td>
			<td style="width:40%"><textarea name="<?=$name_key?>[comments]" rows="3" cols="" style="width:98%;"><?=htmlspecialchars($mailing['comments'])?></textarea></td>
		</tr>
<?php
}
}
?>
	</table>
</div>

<!-- /members_mailing.tpl.php -->
