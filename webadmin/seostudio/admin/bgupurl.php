<?php

use Studio\Util\Sitemap\Sitemap;

require "includes/init.php";
$page->requirePermission('admin-access');

if (isset($_POST['url']) && !DEMO) {
	$url = $_POST['url'];

	$studio->setopt("public-url", $url);

	try {
		if (Sitemap::enabled()) {
			Sitemap::setBaseUrl($url);

			if (Sitemap::changed()) {
				$studio->setopt('sitemap-needs-review', '1');
			}
		}
	}
	catch (Exception $ex) {}

	// v1.84.15 -- Send the new URL to the API if required by an activated service
	if ($studio->getopt("google-enabled") == "On" || $studio->getopt("cron-token") != "") {
		$api->updateURL($url);
	}
}

?>
