<!-- requests.tpl.php -->

<?php
if(is_array($weeklyspecial_requests))
{
	$count_r=count($weeklyspecial_requests);
	if($count_r > 5)
	{
		$weeklyspecial_requests=array_chunk($weeklyspecial_requests, 5);
		$weeklyspecial_requests=$weeklyspecial_requests[0];
	}
?>

<script type="text/javascript">
//<![CDATA[
function removeSpecialRequest(id)
{
	if(confirm('Are you sure you want to delete this request??'))
		window.open('weeklyspecial_delete_request.php?id='+id,'','width=10,height=10');
}
function removeRequestRow(id)
{
	var table=getElement('requests');
	if(table==null) return;
	var theRow=getElement("request_"+id);
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialRequests\');">WeeklySpecial Warning</a>') ?>

<div id="sectionWeeklySpecialRequests">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:18%">New Warning :</td>
			<td style="width:82%"><?= formatHTMLTextarea('new_request','') ?></td>
		</tr>
	</table>
	<br /><br />
	<table class="wide" id="requests">
		<tr><th colspan="4" class="box">WeeklySpecial Warnings</th></tr>
		<tr style="background-color:#eef;">
			<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
			<td width="20%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
			<td width="60%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Comments</b></td>
			<td width="60%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Remove</b></td>
		</tr>
<?php
	foreach($weeklyspecial_requests as $request)
	{
?>
	<tr style="text-align:center" <?=($count++%2==1)?'class="alt"':''?> id="request_<?=$request['id']?>">
		<td nowrap="nowrap" class="bottomLeft"><?=date("d M, Y",strtotime($request['date']))?></td>
		<td nowrap="nowrap" class="bottomLeft"><?=getStaffName($request['staff'])?></td>
		<td class="bottomLeft"><?=nl2br($request['comments'])?></td>
		<td class="bottomLeft"><input type="button" value=" Remove " onclick="removeSpecialRequest('<?=$request['id']?>');"/></td>

	</tr>
<?php
	}
	if($count_r > 5)
	{
?>
	<tr><td colspan=4 style="text-align:center"><input type="button" value="View All (<?=$count_r?>)" onclick="view_requests()"></td></tr>
<?php
	}
?>
	</table>
	<br /><br />
</div>

<?php
}
?>

<!-- /requests.tpl.php -->
