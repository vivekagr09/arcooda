<script type="text/javascript">
//<![CDATA[
function deleteRequest(id)
{
	if(confirm('Are you sure you want to delete this Request ??'))
	{
		$.ajax({
			type: "GET",
			url: "members_delete_request.php",
			data: "id="+id,
			async: false,
			success: function(msg) {
				if(msg=='success') {
					var theRow=getElement("request_"+id);
					if(theRow) {
						theRow.parentNode.deleteRow(theRow.sectionRowIndex);
						alert("Deleted Successfully");
					}
				}
				else alert('There was an error with your request, please try again...');
			}
		});
	}
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionRequests\'); return false;">Member Requests</a>')?>
<div id="sectionRequests"><table>
	<tr><td class="left_title" style="width:25%;">Member Request :</td><td><textarea name="member_request" rows="3" onkeyup="javascript:textareaAutoSize(this,1,3,25);"></textarea></td></tr>
</table><br />
<?php
if (count($member_requests) > 0) {
?>
<table class="wide" border="1" cellpadding="4">
	<tr><th colspan="4" style="background-color:#EEEEEE">Member Requests</td></tr>
	<tr><th>Date</th><th>User</th><th>Comment</th><th></th></tr>
<?php
$count = 0;
    foreach ($member_requests as $request) {
?>
	<tr <?=($count++ % 2 == 1) ? 'class="alt"' : ''?> style="text-align:center;" id="request_<?=$request['id']?>">
		<td style="padding:4px;" nowrap><?=date(_('d/m/Y'), $request['comment_time'])?></td>
		<td style="padding:4px;" nowrap><?=$request['staff']?></td>
		<td style="padding:4px;width:100%;text-align:left;"><?=nl2br(htmlspecialchars($request['comments']))?></td>
		<td><input type="button" value="Delete" onclick="deleteRequest('<?=$request['id']?>')" /></td>
	</tr>
<?php
}
?>
</table><br />
<?php
}
?>
</div>