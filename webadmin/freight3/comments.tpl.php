<!-- comments.tpl -->

<?php
$comments_url = "index.php?action=comments&id=" . $id;
?>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionComments\');return false">Comments</a>') ?>

<div id="sectionComments">

<table class="table_fields">
	<tr>
		<th style="width:25%;">New Comment :</th>
		<td><?php echo formatHTMLTextarea($section_handler->section_name . "[comment]", "", true) ?></td>
	</tr>
</table>

<?php
if (count($item_comments))
{
?>
<br />

<table class="table_grid">
	<tr><th colspan="3">Previous Comments</th></tr>
	<tr class="alt">
		<th>Date</th>
		<th>Staff</th>
		<th style="width:100%;">Comments</th>
	</tr>
<?php
	$staff_list = $section_handler->getOptionList("staff_id");
	$temp = reset(array_chunk($item_comments, 5));
	$alt = true;
	foreach($temp as $comment)
	{
?>
	<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td style="white-space:nowrap;"><?php echo date("j M, Y", $comment["comment_time"]) ?></td>
		<td style="white-space:nowrap;"><?php hecho($staff_list[$comment["staff_id"]]) ?></td>
		<td><?php echo nl2br(hecho($comment["comment_text"], 1)) ?></td>
	</tr>
<?php
	}
	if (count($item_comments) > 5)
	{
?>
	<tr class="alt"><td colspan="3"><input type="button" onclick="<?php hecho("window.open(" . jecho($comments_url, 1) . ")") ?>" value="View All (<?php echo count($item_comments) ?>)" /></td></tr>
<?php
	}
?>
</table>
<?php
}
?>

<br />
<br />

</div>

<!-- /comments.tpl -->
