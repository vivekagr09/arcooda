<?php

use Studio\Util\Sitemap\Sitemap;
use Studio\Util\Sitemap\SitemapParameters;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11)->header('content');

$tools = $studio->getTools();
$toolsDisabled = json_decode($studio->getopt('tools.disabled', '[]'), true);

/**
 * Returns `true` if the specified tool ID is enabled.
 *
 * @param string $toolId
 * @return bool
 */
function isToolEnabled($toolId) {
    global $toolsDisabled;
    return !in_array($toolId, $toolsDisabled);
}

/**
 * Sets the enabled state of the specified tool ID.
 *
 * @param string $toolId
 * @param bool $enabled
 * @return void
 */
function setToolEnabled($toolId, $enabled = true) {
    global $studio, $toolsDisabled;

    // Add the ID to the array if we need to disable it
    if (!$enabled) {
        if (!in_array($toolId, $toolsDisabled)) {
            $toolsDisabled[] = $toolId;
        }
    }

    // Remove it from the array if we're enabling it
    else {
        if (in_array($toolId, $toolsDisabled)) {
            $toolsDisabled = array_diff($toolsDisabled, array($toolId));
        }
    }

    $studio->setopt('tools.disabled', json_encode($toolsDisabled));
}

if (isset($_GET['toggle']) && !DEMO) {
	$toolId = $_GET['toggle'];

	foreach ($tools as $tool) {
		if ($tool->id === $toolId) {
			$enabled = !isToolEnabled($tool->id);
			setToolEnabled($tool->id, $enabled);

			try {
				if (Sitemap::enabled()) {
					if ($enabled) {
						Sitemap::set('/tool.php?id=' . $toolId, new SitemapParameters(1));
					}
					else {
						Sitemap::remove('/tool.php?id=' . $toolId);
					}

					if (Sitemap::changed()) {
						$studio->setopt('sitemap-needs-review', '1');
					}
				}
			}
			catch (Exception $ex) {}

			header('Location: index.php');
			die;
		}
	}

	die("Tool not found!");
}
?>

<section>
    <div class="table-heading">
        <div class="title">
            <h1>Tools</h1>
        </div>
		<div class="actions">
			<a class="btn" href="icons.php">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
					<path d="M12 21a9 9 0 1 1 0 -18a9 8 0 0 1 9 8a4.5 4 0 0 1 -4.5 4h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" />
					<circle cx="7.5" cy="10.5" r=".5" fill="currentColor" />
					<circle cx="12" cy="7.5" r=".5" fill="currentColor" />
					<circle cx="16.5" cy="10.5" r=".5" fill="currentColor" />
				</svg>
				Change icon theme
			</a>
			<a class="btn btn-primary" href="embed.php">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-code" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
					<polyline points="7 8 3 12 7 16" />
					<polyline points="17 8 21 12 17 16" />
					<line x1="14" y1="4" x2="10" y2="20" />
				</svg>
				Embed tools
			</a>
		</div>
    </div>

    <div class="table-responsive-md">
        <table class="table table-hover">
            <thead>
                <tr>
					<th width="250px">Name</th>
					<th width="250px">Id</th>
					<th>Status</th>
                    <th width="90px"></th>
                </tr>
            </thead>
            <tbody>
				<?php
				foreach ($tools as $tool) {
					$toolPage = $tool->getPage('en-us');
				?>
				<tr>
					<td class="text-nowrap">
						<a class="fill" href="edit.php?id=<?php echo $tool->id; ?>">
							<img src="<?php echo $page->getPath() . sanitize_attribute($toolPage->getIcon()); ?>" height="24px" class="me-2">
							<?php echo $tool->name; ?>
						</a>
					</td>
					<td class="text-nowrap">
						<a class="fill" href="edit.php?id=<?php echo $tool->id; ?>">
							<?php echo $tool->id; ?>
						</a>
					</td>
					<td class="text-nowrap"><?php
						if (isToolEnabled($tool->id)) {
							echo '<span class="badge bg-success">Enabled</span> ';
						}
						else {
							echo '<span class="badge bg-danger">Disabled</span> ';
						}

						if ($toolPage->hasCustomizations()) {
							echo '<span class="badge bg-light text-dark">Customized</span> ';
						}
						else {
							echo '<span class="badge bg-light text-dark">Not customized</span> ';
						}
					?></td>
                    <td class="right">
                        <div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" data-bs-boundary="window">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="edit.php?id=<?php echo $tool->id; ?>">Edit</a></li>
                                <li><a class="dropdown-item" href="embed.php?id=<?php echo $tool->id; ?>">Generate embed</a></li>
                                <li><a class="dropdown-item" href="?toggle=<?php echo $tool->id; ?>"><?php
									echo (isToolEnabled($tool->id)) ? 'Disable' : 'Enable';
								?></a></li>
                            </ul>
                        </div>
                    </td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</section>

<?php
$page->footer();
?>
