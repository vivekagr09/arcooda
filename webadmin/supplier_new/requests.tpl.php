<!-- requests.tpl.php -->

<?php
if (isset($supplier_requests) && is_array($supplier_requests)) {
    $staff_list = supplierGetOptionList('staff_id');
?>

<script type="text/javascript">
//<![CDATA[
function removeSpecialRequest(id) {
    if (confirm('Are you sure you want to delete this request??'))
        window.open('member_delete_request.php?id=' + id, '', 'width=10,height=10');
}
function removeRequestRow(id) {
    var table = getElement('requests');
    if (table == null) return;
    var theRow = getElement("request_" + id);
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberRequests\');">Supplier Warning</a>')?>

<div id="sectionMemberRequests">
	<table class="table_fields">
		<tr>
			<th>New Warning :</th>
			<td><?=formatHTMLTextarea('new_request', '')?></td>
		</tr>
	</table>
	<br /><br />
	<table class="table_grid" id="requests">
		<tr><th colspan="4">Supplier Warnings</th></tr>
		<tr style="background-color:#eef;">
			<th width="10%" nowrap="nowrap"><b>Date</b></th>
			<th width="20%" nowrap="nowrap"><b>User</b></th>
			<th width="60%" nowrap="nowrap"><b>Comments</b></th>
			<th width="60%" nowrap="nowrap"><b>Remove</b></th>
		</tr>
<?php
	if (count($supplier_requests) > 0) {
        foreach ($supplier_requests as $request_id) {
            $request = supplierCommentFromDatabase($request_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=date("d M, Y", ($request['comment_time']))?></td>
			<td><?php hecho($staff_list[$request['staff_id']])?></td>
			<td><?=nl2br(hecho($request['comment_text'], 1))?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialRequest('<?=$request['id']?>');" disabled="disabled" /></td>
		</tr>
<?php
		}
    }
?>
	</table>
	<br /><br />
</div>

<?php
}
?>

<!-- /requests.tpl.php -->
