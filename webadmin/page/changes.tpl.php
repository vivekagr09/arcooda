<!-- changes.tpl -->

<?php
// need to display the changes that were made in the last 5 mins or so.
$temp = array(0 => array_reverse(pageCommentSearch(array('page_id' => $id, 'comment_type' => 'sql'), time() - 255)));
if (count($temp[0])) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
    foreach ($temp[0] as $temp[1]) {
        $temp[2] = pageCommentFromDatabase($temp[1]);
        $temp[3] = substr('00' . dechex(max(0, min(255, (time() - $temp[2]['comment_time'])))), -2);
?>
<div style="color:#FF<?=$temp[3] . $temp[3]?>;text-align:left;width:95%;">
	<li><?=nl2br(hecho(wordwrap($temp[2]['comment'], 80, "\n", true), 1))?></li>
</div>
<br /><br />
<?php
    }
}
?>

<!-- /changes.tpl -->
