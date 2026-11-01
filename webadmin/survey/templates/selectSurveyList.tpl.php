<?php

/**
 *	Template: selectSurveyList
 *	Page: selectSurvey
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
 *	Last Modified: 11 July 2018
 */

?>

<!-- View Survey Title -->
<br><div class="flagHeader"><span>Survey List</span></div><br>

<!-- List of survey -->
<table class="view-table">
	<thead>
		<tr>
			<th><a class="sort-link" href="?by=id&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">ID</a></th>

			<th><a class="sort-link" href="?by=name&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Name</a></th>

			<th><a class="sort-link" href="?by=status&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Status</a></th>

			<th><a class="sort-link" href="?by=available&order=<?= $nextOrder[$order] ?>&offset=<?= $offset ?>">Available</a></th>

			<th></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach($surveyData as $v) { ?>

		<tr>
			<td><?= $v['id'] ?></td>
			<td><?= $v['name'] ?></td>
			<td><?php echo (intval($v['status']) == 1) ? "Active" : "Inactive"; ?></td>
			<td>
				<?php
					if (intval($v['available']) == Survey::SURVEY_MEMBER_ONLY) {
						echo "Member";
					} else if (intval($v['available']) == Survey::SURVEY_TOKEN_ONLY) {
						echo "Token";
					} else {
						echo "Anonymous";
					}
				?>
			</td>
			<td>
				<button onclick="selectSurvey(<?= $v['id'] ?>);">Select</button>
			</td>
		</tr>

		<?php } ?>

	</tbody>
</table>

<script>

function selectSurvey(id) {
	
	if(window.opener!=null && window.opener.setSurveyId!=null)
		window.opener.setSurveyId(id);
	else if(window.parent!=null && window.parent.setSurveyId!=null)
		window.parent.setSurveyId(id);
	else
		alert('Unable to send survey back to form');
	window.close();
}

</script>