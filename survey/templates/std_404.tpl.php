<nav class="navbar navbar-expand-lg navbar-light">
	<a class="navbar-brand" href="<?= $domain ?>">
		<img id="logo" src="<?= $logo ?>" height="50" alt="<?= $domain ?>">
	</a>

	<div class="collapse navbar-collapse justify-content-center">
		<h4 id="survey-title">Survey - <?= $surveyName ?></h4>
	</div>
</nav>

<div class='container survey-question-container text-center'>
	<h4>404 Page Not Found</h4>

  <p class="pt-5 pb-5 text-danger">Sorry, the survey does not exists or is already expired.</p>
  
  <button type="button" class="btn btn-primary" onclick="window.location.href='<?= $domain ?>'">Go to main site</button>
</div>