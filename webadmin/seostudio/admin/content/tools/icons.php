<?php

use Studio\Content\Icons\IconManager;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11)->header('content');

$iconSets = IconManager::getIconSets();
$currentSet = $studio->getopt('icon-theme', 'classic-blue');

if (isset($_POST['icon-set']) && !DEMO) {
	$setName = $_POST['icon-set'];

	foreach ($iconSets as $set) {
		if ($set->dirName === $setName && $set->isInstalled()) {
			$tools = $studio->getTools();
			$icons = $set->getIcons();

			foreach ($tools as $tool) {
				$page = $tool->getPage('');
				$iconId = $page->getIconFromSet()->id;

				if ($iconId !== '' && array_key_exists($iconId, $icons)) {
					$page->setIcon($set->dirName, $iconId);
				}
			}

			$studio->setopt('icon-theme', $set->dirName);
			break;
		}
	}

	header("Location: icons.php?success=1");
	die;
}

?>
<section>
	<div class="header with-border">
		<h1>
			<a class="back" href="index.php">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
				</svg>
			</a>
			Change icon theme
		</h1>
		<p>
			Set the theme for all icons at once.
		</p>
	</div>

	<form action="" method="post">
		<input type="hidden" name="isPostback" value="1">

		<div class="save-container post fixed">
			<div class="saveable">
				<div class="card mb-4 tab-pane" id="tab-icon">
					<div class="card-body">
						<input type="hidden" name="icon-set" value="<?php echo $currentSet; ?>">

						<ul class="nav nav-pills mb-3">
							<?php
								foreach ($iconSets as $index => $set) {
									$active = $currentSet === $set->dirName ? 'active' : '';
							?>
							<li class="nav-item">
								<button class="icon-drawer-tab nav-link <?php echo $active; ?>" data-set="<?php echo $set->dirName; ?>" data-bs-toggle="tab" data-bs-target="#icons-<?php echo $set->dirName; ?>" type="button">
									<?php echo $set->name; ?>
								</button>
							</li>
							<?php
								}
							?>
						</ul>

						<div class="tab-content">
							<?php
								foreach ($iconSets as $set) {
									$active = $currentSet === $set->dirName ? 'active' : '';
							?>
							<div class="tab-pane <?php echo $active; ?>" id="icons-<?php echo $set->dirName; ?>">
								<div class="icon-drawer preview" data-set="<?php echo $set->dirName; ?>">
									<?php foreach ($set->getIcons() as $id) { ?>
										<div class="icon" data-set="<?php echo $set->dirName; ?>" data-id="<?php echo $id; ?>">
											<img src="../../../resources/iconsets/<?php echo $set->dirName; ?>/<?php echo $id; ?>.png" alt="<?php echo $id; ?>">
										</div>
									<?php } ?>
									<?php if (!$set->isInstalled()) { ?>
										<div class="not-installed">
											<p>This icon set is not installed.</p>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php
								}
							?>
						</div>
					</div>
				</div>

				<div class="alert alert-primary d-flex align-items-center">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
						<circle cx="12" cy="12" r="9" />
						<line x1="12" y1="8" x2="12.01" y2="8" />
						<polyline points="11 12 12 12 12 16 13 16" />
					</svg>
					<div>
						You can also change the icon theme for each tool individually. Just head over to a tool's
						settings page and look for the "icon" tab.
					</div>
				</div>
			</div>
			<div class="save">
				<div class="save-box">
					<button type="submit">
						<span>Set icon theme</span>
						<span>Saved</span>
						<img src="../../../resources/images/load32.gif" width="16px" height="16px">
					</button>
				</div>
			</div>
		</div>
	</form>
</section>


<script>
	var drawer = $('.icon-drawer');
	var drawerTabs = $('.icon-drawer-tab');
	var icons = drawer.find('.icon');

	var iconSetInput = $('input[name=icon-set]');

	var activeIconElement = null;

	// Changing tabs
	drawerTabs.on('shown.bs.tab', function(event) {
		var tab = drawerTabs.filter(event.target);
		var setName = tab.data('set');

		iconSetInput.val(setName).trigger('change');
	});
</script>

<?php
$page->footer();
?>
