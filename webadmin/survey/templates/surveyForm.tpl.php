<link rel="stylesheet" href="/webadmin/products_new/style.css" type="text/css" />
<style type="text/css">
  input[type="file"] {
        display: none;
    }
    /* Custom label styling */
    .custom-file-label {
        padding: 8px;
	    background-color: #f0f0f0;
	    color: #141313;
	    cursor: pointer;
	    margin: 3px;
	    border: 1px solid black;
    }
</style>
<?php

/**
 *	Template: surveyForm
 *	Page: addSurvey / editSurvey
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
 *	Last Modified: 6th July 2018
 */

?>

<?php if (notifySuccess()) { ?>
<br/><p style="text-align: center; color: red;">Recorded</p><br/>
<?php } ?>
<?php
// Start the session
session_start();

if (isset($_SESSION['error_message'])) {
    echo '<div style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
?>


<h1 class="text-align-center"><?= $title ?></h1>

<br><div class="flagHeader"><span>Survey Information</span></div><br>

<form action="index.php?<?= http_build_query($get_vars) ?>" method="post" enctype="multipart/form-data">

     <input type="hidden" name="survey[site_id]" value="39">

	<table class="form-table">
		<tr>
			<td class="left-title">Name of survey </td>
			<td><input type="text" id="survey-name"  name="survey[name]" class="text-input" value="<?= $surveyName ?>" required oninput="generateSurveyURL()"></td>
		</tr>
		<tr>
			<td class="left-title">
				<?php 
					if(!empty($surveyLink)){
						echo '<a href="'.$surveyLink.'" target="_blank">Survey URL</a>';
					}else{
						echo 'Survey URL';
					}
				?>
			 	
			</td>
			<td>
				<!-- <input type="text" name="survey[survey_link][]" id="survey-url"  class="text-input" value="<?= empty($surveyLink)? 'https://www.arcooda.com/survey/' : $surveyLink ?> " required></td> -->

				<div style="display: flex; align-items: center;">
            <span id="base-survey-url" style="white-space: nowrap;">https://www.arcooda.com/survey/</span>
            <input 
                type="text" 
                id="survey-url-part"  
                class="text-input" 
                value="<?= !empty($surveyLink) ? explode('https://www.arcooda.com/survey/', $surveyLink)[1] : '' ?>" 
                oninput="updateFullSurveyURL()" 
                required style="width:30%;">
        </div>
		<input type="hidden" id="survey-url" name="survey[survey_link][]" value="<?= $surveyLink ?>">
		</tr>
		<tr>
			<td class="left-title">Survey Type</td>
			<td>
				<input type="radio" name="survey[survey_type]" value="standalone" checked> Standalone
				<input type="radio" name="survey[survey_type]" value="front" <?= $surveytype == 'front' ? 'checked':'' ?>> Front Page
			</td>
		</tr>


		<tr>
			<td class="left-title">Description </td>
			<td><textarea name="survey[description]" class="textarea-input" required><?= $surveyDescription ?></textarea></td>
		</tr>

		<tr>
			<td class="left-title">Completed Description </td>
			<td><textarea name="survey[completed_description]" class="textarea-input" required><?= $surveyCompletedDescription ?></textarea></td>
		</tr>

		<tr>
			<td class="left-title">Available to </td>
			<td>
				<input type="radio" name="survey[available]" id="survey-availability-anonymous" value="<?= Survey::SURVEY_ANONYMOUS ?>" <?= $surveyAvailabilityChecked[Survey::SURVEY_ANONYMOUS] ?>> <label for="survey-availability-anonymous">Anyone</label>
				<input type="radio" name="survey[available]" id="survey-availability-member" value="<?= Survey::SURVEY_MEMBER_ONLY ?>" <?= $surveyAvailabilityChecked[Survey::SURVEY_MEMBER_ONLY] ?>> <label for="survey-availability-member">Member Only</label>
				<input type="radio" name="survey[available]" id="survey-availability-member" value="<?= Survey::SURVEY_TOKEN_ONLY ?>" <?= $surveyAvailabilityChecked[Survey::SURVEY_TOKEN_ONLY] ?>> <label for="survey-availability-token">Token Only</label>
			</td>
		</tr>

		<tr>
			<td class="left-title">Question Per Page </td>
			<td><input name="survey[qpp]" type="number" value="<?= $qpp ?>" min="1" class="text-input" required></td>
		</tr>
		<tr style="line-height: 42px;">
		    <td class="left-title">Upload Image</td>
		    <td>
		    	<label for="fileInput" class="custom-file-label">SURVEY IMAGE</label>
		        <input type="file" id="fileInput" name="survey[image]" accept="image/*" class="text-input">
		        <a href="<?php hecho(isset($surveyImage) ? '/surveysimage/'.$surveyImage : '/_.gif')?>" target="_blank">
		        <img id="item_thumb_img" src="<?php hecho(isset($surveyImage) ? '/surveysimage/'.$surveyImage : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;padding: 10px; <?php echo isset($surveyImage) ? '':'display:none;'?>"  />
		        </a>
		    </td>
		</tr>
		<tr style="line-height: 30px;">
			<td class="left-title">Status </td>
			<td>
				<select name="survey[status]" onchange="this.style.backgroundColor=((this.value=='0')?'#FF0000':'#00FF00');" style="background:<?=('0' == $surveyStatus["active"]) ? '#f00;' : '#0f0;'?>">
					<option value="1" <?= $surveyStatus["active"] ?>>Active</option>
					<option value="0" <?= $surveyStatus["inactive"] ?>>Inactive</option>
				</select>
			</td>
		</tr>
	</table>

	<br>
	<div class="text-align-center"><input type="submit" value="Save" class="btn"></div>
	<br>
</form>

<script>
	/**
	 * Function to generate a survey URL dynamically based on the entered survey name
	 */
	function updateFullSurveyURL() {
    const baseSurveyURL = "https://www.arcooda.com/survey/";
    const surveyURLPartInput = document.getElementById('survey-url-part');
    const fullSurveyURLInput = document.getElementById('survey-url');

    // Combine base URL with editable part
    fullSurveyURLInput.value = baseSurveyURL + surveyURLPartInput.value.trim().toLowerCase();
}

function generateSurveyURL() {
    const baseSurveyURL = "https://www.arcooda.com/survey/";
    const surveyNameInput = document.getElementById('survey-name');
    const surveyURLPartInput = document.getElementById('survey-url-part');

    const formattedName = surveyNameInput.value
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-'); 

    // Update only the editable part
    surveyURLPartInput.value = formattedName;

    // Call to update the hidden full URL field
    updateFullSurveyURL();
}

	function addLink(website) {
	
		document.getElementById("link-input").value = website + "survey/my_survey";
	}

	function removeLink(btn){
		document.getElementById('survey-links-table').removeChild(btn.parentNode.parentNode);
	}
	$("[name='survey[image]']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_thumb_img").attr("src", e.target.result);
        };
        $("#item_thumb_img").show();
        reader.readAsDataURL(this.files[0]);
    });
</script>