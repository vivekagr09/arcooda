<?php

use SEO\Services\KeywordsEverywhere;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(22)->header('services');

?>

<section>
	<form action="" method="post">
		<div class="save-container">
			<div class="saveable">
				<h1 class="mb-2">Extra data sources</h1>
				<p class="mb-3">
					These data sources are completely optional, but can supercharge your script's tools when enabled.
					For high traffic and commercial websites, enabling these sources is recommended to ensure uptime and
					data quality.
				</p>

				<div class="accordion toggleable" id="accordion">
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed disabled" type="button">
								<div class="indicator <?php echo ($studio->getopt('kwe-enabled') === 'On') ? (
									KeywordsEverywhere::isLowBalance() ? 'warn' : 'enabled'
								) : 'disabled'; ?>"><div></div></div>
								<div class="title">
									Keywords Everywhere
								</div>
								<div class="description">Adds competition & trend data to the keyword research tool</div>
								<div class="actions">
									<a class="btn btn-xs" href="keywords-everywhere.php">Manage</a>
								</div>
							</button>
						</h2>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>

<?php
$page->footer();
?>
