<?php

namespace Studio\Util\Sitemap;

class SitemapParameters {

	/**
	 * The priority for the page.
	 *
	 * @var float|null
	 */
	public $priority;

	/**
	 * The frequency of changes for the page (`daily`, `weekly`, etc).
	 *
	 * @var string|null
	 */
	public $changefreq;

	/**
	 * The date (YYYY-MM-DD) that the page was last modified.
	 *
	 * @var string|null
	 */
	public $lastmod;

	/**
	 * Constructs a new `SitemapParameters` instance.
	 *
	 * @param float|null $priority
	 * @param string|null $changefreq
	 * @param string|null $lastmod
	 */
	public function __construct($priority = null, $changefreq = null, $lastmod = null) {
		$this->priority = $priority;
		$this->changefreq = $changefreq;
		$this->lastmod = $lastmod;
	}

}
