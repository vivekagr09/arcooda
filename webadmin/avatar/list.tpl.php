<table class="table_grid"  style="width:95%;margin:auto;">
	<tr>
		<th></th>
		<th>Image</th>
		<th>Name</th>
		<th>Comments</th>
		<th>Review</th>
		<th>Avatar Date</th>
		<th>Last Interaction</th>
	</tr>
<?php
foreach($item_list as $id)
{
    $member = memberFromDatabase($id);
    if(isset($member['avatar_image'])) {
        $full_name = $member['first_name'] .' '.$member['last_name'];
?>
	<tr<?php echo ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<td></td>
		<td><img src="/avatar/svgavatars/ready-avatars/<?=$member['avatar_image']?>" style="vertical-align:top;" width="90" height="90" title="Make your own Avatar to use for comments, posts and product reviews"></td>
		<td><a href="/webadmin/member/?action=edit&member_id=<?=$member['member_id']?>" target="_blank"><?=$full_name?></a></td>
		<td style="background:yellow;">0</td>
		<td style="background:yellow;">0</td>
		<td><?=$member['avatar_date'] ? date('d/m/Y',strtotime($member['avatar_date'])) : '' ?></td>
        <td style="background:yellow;"></td>
	</tr>
<?php }
}
?>
</table>