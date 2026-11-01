<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('survey');

//Only show history if there is some to show
if (!isset($invoice_survey) || count($invoice_survey) <= 0) {
    return;
}
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSurvey\');">Survey</a>')?>

<div id="sectionSurvey">
	<table>
		<tr>
			<td class="left_title">Survey Name</td>
			<td><input type="hidden" id="survey-id-input" /><input type="text" id="survey-name-input" /> <button type="button" onclick="openSearch();">Search</button> <button type="button" onclick="addSurvey('<?=$invoice['member_id']?>', this);">Add</button></td>
		</tr>

		<tr id="survey-token-link-display" style="display: none;">
			<td class="left_title">Token Link</td>
			<td><input id="survey-token-link" type="text" name="survey" value="" readonly style="width: 50%; text-indent: 5px;" required> <button type="button" onclick="copyToken(this);">Copy</button></td>
		</tr>
	</table><br>

<?php
if (class_exists('Survey') && count($invoice_survey) > 0) {
?>

	<table>
		<tr>
			<th>Added Date</th>
			<th>Link</th>
			<th>Complete</th>
			<th>Complete Date</th>
			<th>Status</th>
			<th></th>
		</tr>

<?php
    foreach ($invoice_survey as $is) {
        $survey      = new Survey($DB);
        $surveyToken = $survey->getTokenByLink($is['link']);
?>

		<tr>
			<td style="text-align: center;"><?=date("d/m/Y", strtotime($surveyToken['added_date']))?></td>
			<td style="text-align: center;"><?=$surveyToken['link']?></td>
			<td style="text-align: center;"><?=(intval($surveyToken['complete']) == 0) ? "No" : "Yes"?></td>
			<td style="text-align: center;"><?=(intval($surveyToken['complete']) == 0) ? "-" : date("d/m/Y", strtotime($surveyToken['complete_date']))?></td>
			<td style="text-align: center;"><?=(intval($surveyToken['status']) == 0) ? "Inactive" : "Active"?></td>
			<td style="text-align: center;">
				<a href="/webadmin/survey/editToken.php?link=<?=$surveyToken['link']?>" target="_blank">Edit</a> |
				<a href="/webadmin/survey/editSurvey.php?id=<?=$surveyToken['survey_id']?>" target="_blank">View Survey</a>
			</td>
		</tr>

<?php
    }
?>

	</table>

<?php
}
?>

</div>

<script type="text/javascript">
//<![CDATA[
function copyToken(btn) {
	var copyText = document.getElementById("survey-token-link");

	copyText.select();
	document.execCommand("copy");

	btn.innerText = "Copied";
}

function addSurvey(member_id, btn) {
	var survey_id = $("#survey-id-input").val();

	if (survey_id.length > 0) {
		$.ajax({
			url : "/webadmin/survey/phpajax/add_token.php",
			type: "post",
			data: {survey_id : survey_id, member_id: member_id},
			beforeSend: function() {
				$(btn).text("Loading...");
				$(btn).attr("disabled", "disabled");
			},

			success: function(r) {
				if (r != "error") {
					$("#survey-token-link-display").show();
					$("#survey-token-link").val(r);
					$(btn).text("Add");
					$(btn).removeAttr("disabled");
				}
			}
		});
	} else {
		alert("Choose survey ID.");
	}
}

function setSurvey(name, id) {
	document.getElementById("survey-name-input").value = name;
	document.getElementById("survey-id-input").value = id;
}

function openSearch() {
	var search_for = "";

	if (document.getElementById("survey-name-input").value.length > 0) {
		search_for = "&search_for=" + document.getElementById("survey-name-input").value;
	}

	window.open('/webadmin/survey/viewSurvey.php?return_message=add' + search_for);
}
//]]>
</script>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
