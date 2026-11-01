<?php

namespace Studio\Util\Sitemap;

use DOMDocument;
use Exception;
use SimpleXMLElement;

class Sitemap {

	/**
	 * @var SimpleXMLElement|null
	 */
	protected static $xml;

	/**
	 * @var string
	 */
	protected static $hash;

	/**
	 * A map between paths and their XML elements.
	 * @var SimpleXMLElement[]
	 */
	protected static $links = [];

	/**
	 * Whether or not to automatically save changes.
	 * @var boolean
	 */
	protected static $autoSave = true;

	/**
	 * @var bool
	 */
	protected static $modified = false;

	/**
	 * Returns true if the sitemap is enabled (i.e. the file exists and has data).
	 *
	 * @return bool
	 */
	public static function enabled() {
		$path = static::getSitemapPath();

		if (!function_exists('simplexml_load_string')) {
			return false;
		}

		if (file_exists($path) && is_readable($path)) {
			$content = file_get_contents($path);

			return !empty($content);
		}

		return false;
	}

	/**
	 * Resets the sitemap to its default state, or creates a starter sitemap if not currently enabled.
	 *
	 * @return void
	 */
	public static function reset() {
		global $studio;

		// Load from the base template instead of disk
		static::load(true);
		static::setAutoSave(false);

		// Add built-in pages
		static::set('/', new SitemapParameters(1));

		if ($studio->getopt('disable-home-page') !== 'On') {
			static::set('/tools.php', new SitemapParameters(0.9));
		}

		static::set('/account/login.php', new SitemapParameters(0.5));
		static::set('/account/register.php', new SitemapParameters(0.5));

		// Add tools
		$toolsData = unserialize($studio->getopt("tools"));
		$toolsDisabled = json_decode($studio->getopt('tools.disabled', '[]'), true);
		$toolsAuthenticated = json_decode($studio->getopt('tools.authenticated', '[]'), true);
		$toolsRequireLogin = $studio->getopt('login-tools') == 'On';

		if (!$toolsRequireLogin) {
			foreach ($toolsData as $tools) {
				foreach ($tools as $id) {
					if (!in_array($id, $toolsDisabled) && !in_array($id, $toolsAuthenticated)) {
						static::set('/tool.php?id=' . $id, new SitemapParameters(1));
					}
				}
			}
		}

		// Add pricing plans page
		if ($studio->sql->query('SELECT * FROM `plugins` WHERE `name` LIKE "%subscriptions%" AND `enabled` = 1;')->num_rows > 0) {
			static::set('/pricing.php', new SitemapParameters(0.8));
		}

		// Add contact page
		if ($studio->sql->query('SELECT * FROM `plugins` WHERE `name` LIKE "%contact%" AND `enabled` = 1;')->num_rows > 0) {
			static::set('/contact.php', new SitemapParameters(0.5));
		}

		static::save();
		static::setAutoSave(true);
	}

	/**
	 * Updates sitemap entries for tools after changing their authentication settings. This is because we want to avoid
	 * listing tools in the sitemap if they require login.
	 *
	 * @return void
	 */
	public static function reapplyTools() {
		global $studio;

		// Skip when not enabled
		if (!static::enabled()) {
			return;
		}

		// Load and disable autosave
		static::load();
		static::setAutoSave(false);

		// Add tools
		$toolsData = unserialize($studio->getopt("tools"));
		$toolsDisabled = json_decode($studio->getopt('tools.disabled', '[]'), true);
		$toolsAuthenticated = json_decode($studio->getopt('tools.authenticated', '[]'), true);
		$toolsRequireLogin = $studio->getopt('login-tools') == 'On' || $studio->getopt('show-tools') === 'On';

		foreach ($toolsData as $tools) {
			foreach ($tools as $id) {
				$link = '/tool.php?id=' . $id;
				$allowed = !$toolsRequireLogin && !in_array($id, $toolsDisabled) && !in_array($id, $toolsAuthenticated);
				$exists = static::has($link);

				// Add the link if needed
				if (!$exists && $allowed) {
					static::set($link, new SitemapParameters(1));
				}

				// Remove the link if needed
				else if ($exists && !$allowed) {
					static::remove($link);
				}
			}
		}

		// Save and reenable autosave
		static::save();
		static::setAutoSave(true);

		// Mark for review
		if (Sitemap::changed()) {
			$studio->setopt('sitemap-needs-review', '1');
		}
	}

	/**
	 * Adds a new page to the sitemap, or updates the page if it already exists.
	 *
	 * @param string $path The path to the file, relative to the script's root directory
	 * @param SitemapParameters|null $params Additional parameters for the sitemap entry (optional)
	 * @return void
	 */
	public static function set($path, SitemapParameters $params = null) {
		static::load();
		$node = static::getNode($path);

		if ($params instanceof SitemapParameters) {
			static::updateChild($node, 'lastmod', $params->lastmod);
			static::updateChild($node, 'changefreq', $params->changefreq);
			static::updateChild($node, 'priority', $params->priority !== null ? number_format($params->priority, 1, '.', '') : null);
		}

		static::write();
	}

	/**
	 * Checks if the sitemap has an entry for the given page.
	 *
	 * @param string $path The path to the file, relative to the script's root directory
	 * @return bool
	 */
	public static function has($path) {
		static::load();
		return static::getNode($path, false) !== null;
	}

	/**
	 * Removes an entry for the given page from the sitemap if it exists.
	 *
	 * @param string $path The path to the file, relative to the script's root directory
	 * @return void
	 */
	public static function remove($path) {
		static::load();
		$node = static::getNode($path, false);

		if ($node !== null) {
			unset($node[0]);
		}

		static::write();
	}

	/**
	 * Updates the base URL for the script's sitemap entries.
	 *
	 * @param string $base
	 * @return void
	 */
	public static function setBaseUrl($base) {
		global $studio;
		static::load();

		$existing = static::getBaseUrl();
		$base = rtrim($base, '/');

		foreach (static::$links as $path => $link) {
			if (substr($link->loc, 0, strlen($existing)) === $existing) {
				$link->loc = $base . '/' . ltrim($path, '/');
			}
		}

		$studio->setopt('sitemap-base-url', $base);
		static::write();
	}

	/**
	 * Gets the base URL for the script's sitemap entries.
	 *
	 * @return string
	 */
	protected static function getBaseUrl() {
		global $studio;

		if (($value = $studio->getopt('sitemap-base-url')) !== null) {
			return $value;
		}

		$url = $studio->getopt('public-url');
		$studio->setopt('sitemap-base-url', rtrim($url, '/'));

		return $url;
	}

	/**
	 * Loads the sitemap and returns the object.
	 *
	 * @param bool $reset Set to `true` to load the base template instead of the file from disk.
	 * @return SimpleXMLElement
	 */
	protected static function load($reset = false) {
		global $studio;

		if (static::$xml === null || $reset) {
			$path = static::getSitemapPath();
			$content = static::getBaseTemplate();
			$links = [];

			if (!$reset && file_exists($path) && is_readable($path)) {
				$content = file_get_contents($path);
			}

			$xml = simplexml_load_string($content);

			if ($xml === false) {
				throw new Exception('Failed to parse sitemap.xml');
			}

			$base = static::getBaseUrl();

			foreach ($xml as $child) {
				if ($child->getName() === 'url') {
					$path = null;

					foreach ($child as $nestedChild) {
						if ($nestedChild->getName() === 'loc') {
							$url = $nestedChild->__toString();

							if (substr($url, 0, strlen($base)) === $base) {
								$path = substr($url, strlen($base));
								break;
							}
						}
					}

					if ($path) {
						$path = $studio->permalinks->getReversePermalink($path);
						$links[$path] = $child;
					}
				}
			}

			static::$hash = md5($content);
			static::$xml = $xml;
			static::$links = $links;

			return $xml;
		}

		return static::$xml;
	}

	/**
	 * Writes the internal XML object to the disk.
	 *
	 * @param bool $isAutoSave
	 * @return void
	 * @throws Exception
	 */
	protected static function write($isAutoSave = true) {
		if (!static::$autoSave && $isAutoSave) {
			return;
		}

		if (static::$xml !== null) {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML(static::$xml->asXML());

			$content = $dom->saveXML();

			static::$modified = static::$hash !== md5($content);

			if (file_put_contents(static::getSitemapPath(), $content) === false) {
				throw new Exception('Failed to write sitemap: ' . static::getSitemapPath());
			}
		}
	}

	/**
	 * Saves changes to the sitemap. This only needs to be called when you've used `setAutoSave(false)`.
	 *
	 * @return void
	 * @throws Exception
	 */
	public static function save() {
		static::write(false);
	}

	/**
	 * Sets whether the sitemap manager will automatically apply changes to the disk.
	 *
	 * @param bool $enabled
	 * @return void
	 */
	public static function setAutoSave($enabled) {
		static::$autoSave = $enabled;
	}

	/**
	 * Returns the absolute path to the sitemap file.
	 *
	 * @return string
	 */
	public static function getSitemapPath() {
		global $studio;
		return $studio->basedir . DIRECTORY_SEPARATOR . 'sitemap.xml';
	}

	/**
	 * Returns the base template for an empty sitemap.
	 *
	 * @return string
	 */
	protected static function getBaseTemplate() {
		return (
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
			'</urlset>'
		);
	}

	/**
	 * Gets the permalink for the given path. Preserves query arguments and transforms them as needed. The resulting
	 * path is guaranteed to start with a `/`.
	 *
	 * @param string $path
	 * @return string
	 */
	protected static function getPermalink($path) {
		global $studio;

		if ($studio->usePermalinks()) {
			$link = $studio->permalinks->getLinkWithVariations($path);

			if ($link !== null) {
				$path = $link->getUri($path);

			}
		}

		if (substr($path, 0, 1) !== '/') {
			$path = '/' . $path;
		}

		return $path;
	}

	/**
	 * Fetches the node for the given path, or creates it if not found.
	 *
	 * @param string $path
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	protected static function getNode($path, $autoCreate = true) {
		global $studio;

		$xml = static::load();
		$base = rtrim(static::getBaseUrl(), '/');
		$path = ltrim($path, '/');

		if (array_key_exists($path, static::$links)) {
			return static::$links[$path];
		}

		if ($autoCreate) {
			$node = $xml->addChild('url');
			$node->addChild('loc', $base . static::getPermalink($path));

			return $node;
		}
	}

	/**
	 * Updates a child for the given node, creating and removing it as needed.
	 *
	 * @param SimpleXMLElement $node The parent node for the child
	 * @param string $name The name of the child
	 * @param mixed $value The value of the child - `null` will be ignored and `''` (blank) will remove
	 * @return SimpleXMLElement|null
	 */
	protected static function updateChild(SimpleXMLElement $node, $name, $value) {
		// Find an existing match
		$match = null;
		foreach ($node as $child) {
			if ($child->getName() === $name) {
				$match = $child;
				break;
			}
		}

		// Ignore if the value is null
		if (is_null($value)) {
			return;
		}

		// Delete if the value is an empty string
		if ($value === '') {
			if ($match !== null) {
				unset($match->{0});
			}

			return;
		}

		// Update existing values
		if ($match !== null) {
			$match->{0} = $value;
			return $node;
		}

		// Add new child
		return $node->addChild($name, $value);
	}

	/**
	 * Updates the sitemap to reflect the given permalink status.
	 *
	 * @param bool|null $enabled When not specified, will be inferred automatically from current settings
	 * @return void
	 */
	public static function setPermalinksEnabled($enabled = null) {
		global $studio;

		if ($enabled === null) {
			$enabled = $studio->usePermalinks();
		}

		static::load();
		$base = static::getBaseUrl();

		foreach (static::$links as $path => $link) {
			if (substr($link->loc, 0, strlen($base)) === $base) {
				$match = $studio->permalinks->getLinkWithVariations($path);

				if ($enabled && $match) {
					$path = $match->getUri($path);
				}

				$link->loc = $base . '/' . ltrim($path, '/');
			}
		}

		static::write();
	}

	/**
	 * Returns `true` if the sitemap file has been modified since it was initially loaded.
	 *
	 * @return bool
	 */
	public static function changed() {
		return static::$modified;
	}

}
