<?php

/**
 *	Template: tokenList
 *	Page: viewToken
 *
 *	The template displays the list of survey.
 *
 *  Precondition: The survey object has been instantiated and
 *				  the survey data has been retrieved before including this template.
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 6th July 2018
 */

?>

<!-- View Survey Title -->
<br><div class="flagHeader"><span>View Token</span> <a class="sort-link" href="index.php?action=add_token">Add New Token</a></div><br>

<!-- List of survey -->
<table class="view-table">
	<thead>
		<tr>
			<th><a class="sort-link" href="?by=added_date&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Added Date</a></th>

			<th>Token</th>

			<th>Link</th>

			<th>Member ID</th>

			<th>Staff</th>

			<th><a class="sort-link" href="?by=status&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Status</a></th>

			<th></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach($tokenData as $v) { ?>

		<tr>
			<td><?= date("d/m/Y", strtotime($v['added_date'])) ?></td>
			<td><?= $v['token'] ?></td>
			<td><?= $v['link'] ?></td>
			<td><?= $v['member_id'] ?></td>
			<td><?= $all_staff_list[$v['staff']] ?></td>
			<td><?php echo (intval($v['status']) == 1) ? "Active" : "Inactive"; ?></td>
			<td>
				<a class="sort-link" href="index.php?action=edit_token&id=<?= $v['token'] ?>">Edit</a>
			</td>
		</tr>

		<?php } ?>

	</tbody>
</table>