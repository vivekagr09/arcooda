<?php
/**
 * @var int $supplier_id
 */

$t_comments = supplierCommentsFromDatabase($supplier_id);
?>
<div align="center">
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionComments\');">Admin Comments</a>')?>
</div>
<div id="sectionComments">
<table class="wide" align="center" style="width:98%">
	<tr>
		<th>New Admin Comment</th>
	</tr>
	<tr>
		<td style="width:82%"><textarea rows=1 style="width:100%;" name="comments" onkeyup="textareaAutoSize(this,3,3,50)"></textarea></td>
	</tr>
</table>
<br/>
<?php
if (count($t_comments) > 0) {
?>
<table class="wide" border="0" cellspacing="0" cellpadding="5" align="center" style="width:98%">
	<tr>
		<th colspan="4" align="center" class="box"><strong>Previous Admin Comments</strong></td>
	</tr>
	<tr style="background-color:#E3EBF7">
		<td width="10%" align="center" nowrap class="bottomLeft"><b>Date</b></td>
		<td width="5%" align="center" nowrap class="bottomLeft"><b>User</b></td>
		<td width="85%" align="center" nowrap class="bottomLeftRight"><b>Comments</b></td>
	</tr>
<?php
	$comments = array_chunk($t_comments, 5);
	$comments = $comments[0];
    $count = 0;
    foreach ($comments as $comment) {
?>
	<tr style="text-align:center" <?=($count % 2 == 1) ? 'class="alt"' : ''?>>
		<td nowrap class="bottomLeft" style="width:10%;"><?=date("d M, Y", strtotime($comment['date']))?></td>
		<td nowrap class="bottomLeft" style="width:5%;"><?=$comment['staff']?></td>
		<td align="left" class="bottomLeftRight" style="width:85%;"><?=nl2br(htmlspecialchars($comment['comments']))?></td>
	</tr>
<?php
$count++;
    }
    if (count($t_comments) > 5) {
?>
	<tr class="alt">
		<td colspan=4 style="text-align:center"><input type="button" value="View All (<?=count($t_comments)?>)" onclick="window.open('index.php?action=comments&supplier_id=<?=$supplier_id?>')"></td>
	</tr>
	<?php }?>
</table>
<?php }?>
</div>
<br />