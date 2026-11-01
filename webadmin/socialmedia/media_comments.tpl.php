<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionComments\'); return false;">Comments</a>')?>
<div id="sectionComments">
<table class="table_fields">
	<tr>
        <th class="left_title">Admin Notes :</th>
        <td>
            <textarea name="admin_comment" rows="3" class="wide" id="myTextarea">
        </textarea></td></tr>
</table><br />
<?php
$member_comments = partnerCommentsFromDatabase($item['id']);
if (count($member_comments) > 0) {
    $totalC          = count($member_comments);
    $member_comments = array_chunk($member_comments, 5);
    $member_comments = $member_comments[0];
    include __DIR__ . '/list_comments.tpl.php';

    if ($totalC > 5) {
?>
    <div style="text-align:center"><input type="button" value="View All (<?=$totalC?>)" onclick="<?=htmlspecialchars('window.open(' . json_encode('.?action=comments&partner_id=' . $item['id']) . ')')?>" /></div>
<?php
    }
}
?>
</div>
<script>
    document.getElementById("myTextarea").addEventListener("click", function() {
        this.setSelectionRange(0, 0); // Move cursor to the start
    });
</script>
</script>