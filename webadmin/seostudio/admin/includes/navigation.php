<?php

use SEO\Services\KeywordsEverywhere;

$isCronOutdated = $this->studio->getopt('cron-last-run') < (time() - 43200);
$isSitemapReviewNeeded = $this->studio->getopt('sitemap-needs-review') === '1';

return [
	'customize' => [
		'title' => "Customize",
		'description' => "Change the appearance of the website",
		'links' => [
			[
				'title' => 'Branding',
				'url' => 'customize/branding.php'
			],
			[
				'title' => 'Navigation',
				'url' => 'customize/navigation.php'
			],
			[
				'title' => 'Colors',
				'url' => 'customize/colors.php'
			]
		]
	],
	'content' => [
		'title' => 'Content',
		'description' => 'Manage content throughout the website',
		'links' => [
			[
				'title' => 'Languages',
				'url' => 'content/languages/index.php'
			],
			[
				'title' => 'Pages',
				'url' => 'content/pages/index.php'
			],
			[
				'title' => 'Tools',
				'url' => 'content/tools/index.php'
			],
			[
				'title' => 'Advertising',
				'url' => 'content/advertising.php'
			],
			[
				'title' => 'Files',
				'url' => 'content/files/index.php',
				'notifications' => $isSitemapReviewNeeded ? 1 : 0
			]
		]
	],
	'users' => [
		'title' => 'Users',
		'description' => 'Manage users and groups',
		'links' => [
			[
				'title' => 'Users',
				'url' => 'users/index.php'
			],
			[
				'title' => 'Groups',
				'url' => 'users/groups/index.php'
			],
			[
				'title' => 'Tool usage',
				'url' => 'users/stats/usage.php'
			]
		]
	],
	'services' => [
		'title' => 'Services',
		'description' => 'Manage services and integrations',
		'links' => [
			[
				'title' => 'Cron',
				'url' => 'services/cron.php',
				'issues' => $isCronOutdated ? 1 : 0
			],
			[
				'title' => 'Search',
				'url' => 'services/search.php'
			],
			[
				'title' => 'Data sources',
				'url' => 'services/sources/index.php',
				'issues' => KeywordsEverywhere::isLowBalance() ? 1 : 0
			]
		]
	],
];
