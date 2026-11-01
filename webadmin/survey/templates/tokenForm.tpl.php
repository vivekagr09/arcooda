<?php

/**
 *	Template: tokenForm
 *	Page: addToken / editToken
 *
 *	The template displays survey form
 *
 *  Precondition: The following variables need to be declared for this template
 *				  Variables: - string $title: Title of the page
 *			   				 - string $surveyNameValue: Name of survey.
 *							 - string[] $surveyLink: Link of survey
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 *
 *	Last Modified: 11 July 2018
 */

?>

<h1 class="text-align-center"><?= $title ?></h1>

<br><div class="flagHeader"><span>Token Information</span></div><br>

<form action="index.php?<?= http_build_query($get_vars) ?>" method="post">
	<table class="form-table">

		<?php if ($link == "") { ?>
		<tr>
			<td class="left-title">Website</td>
			<td>
				<select name="token[link]" required>
					<option value="" disabled selected>Select Website </option>
					<?php foreach($websites as $website) { ?>
					<option value="<?= $website['url'] ?>"><?= $website['url'] ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php } else { ?>
		<tr>
			<td class="left-title">Link</td>
			<td>
				<input type="text" value="<?= $link ?>" class="text-input" disabled>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<td class="left-title">Member ID </td>
			<td>
				<?php if ($member_id == 0) { ?>
				<input type="text" name="token[member_id]" id="member-id-input"> <button type="button" onclick="window.open('/webadmin/new_members/members_list.php?return_message=add');">Select Member</button>
				<?php } else { ?>
				<input type="text" value="<?= $member_id ?>" disabled>
				<?php } ?>
			</td>
		</tr>

		<tr>
			<td class="left-title">Survey ID </td>
			<td>
				<?php if ($survey_id == 0) { ?>
				<input type="text" name="token[survey_id]" id="survey-id-input"> <button type="button" onclick="window.open('/webadmin/survey/selectSurvey.php?return_message=add');">Select Survey</button>
				<?php } else { ?>
				<input type="text" value="<?= $survey_id ?>" disabled>
				<?php } ?>
			</td>
		</tr>

		

		<tr>
			<td class="left-title">Status </td>
			<td>
				<select name="token[status]">
					<option value="1" <?php echo ($status == 1) ? "selected" : ""; ?>>Active</option>
					<option value="0" <?php echo ($status == 0) ? "selected" : ""; ?>>Inactive</option>
				</select>
			</td>
		</tr>
	</table>

	<br>
	<input type="hidden" name="token[id]" value="<?= $token ?>" />
	<div class="text-align-center"><input type="submit" value="Save" class="btn"></div>
	<br>
</form>

<script>
	function setMemberData(data) {
		var member_id = data['member_id'];
		document.getElementById("member-id-input").value = member_id;
	}

	function setSurveyId(id) {
		document.getElementById("survey-id-input").value = id;
	}
</script>