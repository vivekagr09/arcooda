<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionComments\'); return false;">Comments</a>')?>
<div id="sectionComments"><table>
	<tr><td class="left_title" style="width:25%;">Admin Notes :</td><td><textarea name="admin_comment" rows="3" onkeyup="javascript:textareaAutoSize(this,1,3,25);"></textarea></td></tr>
</table><br />
<?php
if (count($member_comments) > 0) {
    $totalC          = count($member_comments);
    $member_comments = array_chunk($member_comments, 5);
    $member_comments = $member_comments[0];
    include __DIR__ . '/list_comments.tpl.php';

    if ($totalC > 5) {
?>
    <div style="text-align:center"><input type="button" value="View All (<?=$totalC?>)" onclick="<?=htmlspecialchars('window.open(' . json_encode('.?action=comments&member_id=' . $member_id) . ')')?>" /></div>
<?php
    }
}
?>
</div>