<input type="hidden" name="project[project_id]" value="<?php echo $project_id ?>" />

<table>
	<tr>
		<td class="left_title" style="width:25%;">
			Project Title
		</td>
		<td>
			<input type="text" class="intext" name="project[project_title]" value="<?php echo htmlspecialchars($project['project_title']) ?>" />
		</td>
	</tr>
	<tr>
		<td class="left_title">
			Project Website
		</td>
		<td>
<?php
$site_list = projectGetOptionList("site_id");
if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $site_list = array(
	$project['site_id'] => $site_list[$project['site_id']],
);
?>
			<select name="project[site_id]"><?php echo formatHTMLOptions($site_list, $project["site_id"]) ?></select>
		</td>
	</tr>
</table>

<br />
<br />

<input type="submit" value="Update Changes" />
