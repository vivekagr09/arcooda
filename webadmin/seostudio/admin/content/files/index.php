<?php

use Studio\Util\Sitemap\Sitemap;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11)->header('content');

$pages = array(
	'robots.txt' => ['robots.php', 'Tells robots and crawlers how to crawl your website.'],
	'sitemap.xml' => ['sitemap.php', 'Helps crawlers find all of your available pages.']
);

$activePages = [];
$notificationPages = [];

if (Sitemap::enabled()) {
	$activePages[] = 'sitemap.xml';
}

if (file_exists($studio->basedir . '/robots.txt')) {
	$activePages[] = 'robots.txt';
}

if ($studio->getopt('sitemap-needs-review') === '1') {
	$notificationPages[] = 'sitemap.xml';
}

?>

<section>
    <div class="table-heading">
        <div class="title">
            <h1>Files</h1>
        </div>
    </div>

    <div class="table-responsive-md">
        <table class="table table-hover">
            <thead>
                <tr>
					<th width="250px">Name</th>
					<th>Description</th>
					<th>Status</th>
                    <th width="90px"></th>
                </tr>
            </thead>
			<tbody>
				<?php foreach ($pages as $name => $config) {
					$link = $config[0];
					$desc = $config[1];
				?>
				<tr>
					<td class="text-nowrap">
						<a class="fill" href="<?php echo $link; ?>">
							<?php echo $name; ?>
							<?php if (in_array($name, $notificationPages)) { ?>
							<span class="badge bg-primary ms-1">1</span>
							<?php } ?>
						</a>
					</td>
					<td class="text-nowrap"><a class="fill" href="<?php echo $link; ?>"><?php echo $desc; ?></a></td>
					<td class="text-nowrap">
						<?php if (in_array($name, $activePages)) { ?>
						<span class="badge bg-success">Active</span>
						<?php } else { ?>
						<span class="badge bg-light text-dark">Not found</span>
						<?php } ?>
					</td>
					<td class="right">
						<div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" data-bs-boundary="window">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../../../<?php echo $name; ?>">Open file</a></li>
                                <li><a class="dropdown-item" href="<?php echo $link; ?>">Edit</a></li>
                            </ul>
                        </div>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</section>

<?php
$page->footer();
?>
