<?php

namespace Studio\Base;

use Studio\Util\ApacheComposer;

class PermalinkHandler {

	/**
	 * The studio instance we're managing permalinks for.
	 *
	 * @var Studio
	 */
	protected $studio;

	/**
	 * @var Permalink[]
	 */
	private $links;

	/**
	 * Whether or not the home page is disabled.
	 *
	 * @var bool
	 */
	private $homeDisabled = false;

	private $curFile;

	/**
	 * Constructs a new `PermalinkHandler` instance.
	 *
	 * @param Studio $studio
	 */
	public function __construct($studio) {
		$this->studio = $studio;
		$stack = debug_backtrace();
		$firstFrame = $stack[count($stack) - 1];
		$this->curFile = $firstFrame['file'];
	}

	/**
	 * Mounts permalinks to the current request and starts output buffer filtering.
	 *
	 * @return void
	 */
	public function mount() {
		global $account;

		// Load relevant state
		$this->homeDisabled = $this->studio->getopt('disable-home-page') === 'On';

		if (isset($_GET['_skipPermalinks'])) {
			$_SESSION['_skipPermalinks'] = 1;
			unset($_GET['_skipPermalinks']);
			return;
		}

		if (isset($_SESSION['_skipPermalinks'])) {
			if (isset($_GET['_enablePermalinks'])) {
				unset($_SESSION['_skipPermalinks']);
				unset($_GET['_enablePermalinks']);
			}
			else {
				return;
			}
		}

		// Get the current link
		$link = $this->getCurrentLink();

		// Redirect to the permalink for this page if necessary
		if ($link !== null && $this->isNative()) {
			// Admin: Allow toggling redirects
			if ($this->studio->getopt('permalinks.redirect', 'On') === 'On') {
				$status = $account->isLoggedIn() ? 302 : 301;
				$uri = $link->getUri($_GET);
				$traversal = $this->getPathTraversal();

				// Admin: Disallow permanent redirection
				if ($this->studio->getopt('permalinks.permanent') === 'Off' && $status === 301) {
					$status = 302;
				}

				if ($uri === '') {
					$uri = './';
				}

				http_response_code($status);
				header("Location: {$traversal}{$uri}");
				echo "<h1>This page has moved to <a href=\"{$traversal}{$uri}\">here</a>.</h1>";

				$this->studio->stop();
			}
		}

		// RC1608728: New redirection rules applied for non-POST requests
		if (!$this->isNative() && !isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
			// RC1608728: Fix double slashes due to relaxed htaccess rules
			if (!empty($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];
				$arguments = '';

				if (strpos($uri, '?') !== false) {
					$arguments = substr($uri, strpos($uri, '?'));
					$uri = substr($uri, 0, strpos($uri, '?'));
				}

				if (strpos($uri, '//') !== false) {
					$uri = preg_replace('/\/+/', '/', $uri) . $arguments;

					http_response_code(301);
					header("Location: {$uri}");
					echo "<h1>This page has moved to <a href=\"{$uri}\">here</a>.</h1>";

					$this->studio->stop();
				}
			}

			// RC1608728: Redirect to argument-specific permalink aliases
			if ($link !== null && !empty($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];

				if (strpos($uri, '?') !== false) {
					$uri = substr($uri, 0, strpos($uri, '?'));
				}

				$permalink = $link->getUri($_GET);
				if (strpos($permalink, '?') !== false) {
					$permalink = substr($permalink, 0, strpos($permalink, '?'));
				}

				// Get the base path (e.g. `/` for root or `/subdir/` if we're in a subdirectory)
				$basePath = $this->getBasePath();

				// Remove the base path from the URI if it matches
				if (substr($uri, 0, strlen($basePath)) === $basePath) {
					$uri = substr($uri, strlen($basePath));
				}

				if ($uri !== $permalink) {
					$status = $account->isLoggedIn() ? 302 : 301;
					$uri = $link->getUri($_GET);
					$traversal = $this->getPathTraversal();

					// Admin: Disallow permanent redirection
					if ($this->studio->getopt('permalinks.permanent') === 'Off' && $status === 301) {
						$status = 302;
					}

					if ($uri === '') {
						$uri = './';
					}

					http_response_code($status);
					header("Location: {$traversal}{$uri}");
					echo "<h1>This page has moved to <a href=\"{$traversal}{$uri}\">here</a>.</h1>";

					$this->studio->stop();
				}
			}
		}

		// Filter the output buffer to change links
        ob_start(array($this, 'doFilterOutput'));
	}

	/**
	 * Returns the base path for the script's root directory. This will always start and end with a slash.
	 *
	 * @return string
	 */
	public function getBasePath() {
		global $studio;

		$url = $studio->getopt('public-url');

		if (is_string($url)) {
			$parsed = @parse_url($url);

			if (is_array($parsed) && isset($parsed['path'])) {
				return preg_replace('/\/+/', '/', '/' . trim($parsed['path'], '/ ') . '/');
			}
		}

		return '/';
	}

	/**
	 * Returns the current path within the script, relative to the root directory of the script. This will always start
	 * with a slash.
	 *
	 * @return string
	 */
	public function getCurrentPath() {
		$uri = $this->getCurrentUri();
		$basePath = $this->getBasePath();

		if (substr($uri, 0, strlen($basePath)) === $basePath) {
			$uri = substr($uri, strlen($basePath));
		}

		return '/' . ltrim($uri, '/');
	}

	/**
	 * Returns the current path relative to the root folder on the server.
	 *
	 * @return string
	 */
	public function getCurrentUri() {
		$uri = $_SERVER['REQUEST_URI'];

		if (strpos($uri, '?') !== false) {
			$uri = substr($uri, 0, strpos($uri, '?'));
		}

		return '/' . ltrim($uri, '/');
	}

	/**
	 * Gets the dirname of a path, but returns a blank string for `.`
	 *
	 * @param string $path
	 * @return string
	 */
	private function dirname($path) {
		$path = dirname($path);

		if ($path === '.') {
			return '';
		}

		return $path;
	}

	/**
	 * Filters the output buffer.
	 *
	 * @param string $buffer
	 * @return string
	 */
	public function doFilterOutput($buffer) {
		$traversal = $this->getPathTraversal();

		// Replace links in the output dynamically
		foreach (array_reverse($this->getLinks()) as $link) {
			$pattern  = '/[\'"](?:\.\/)?' . preg_quote($traversal, '/');
			$pattern .= '(' . preg_quote($link->getPageName(), '/') . ')';
			$pattern .= '(?:\?([^\'"]+))?';
			$pattern .= '[\'"]/i';
			$pattern = str_replace('/index\\.php', '/(?:index\\.php)?', $pattern);

			$buffer = preg_replace_callback($pattern, function($match) use (&$link) {
				$params = array();
				$traverse = $this->getPathTraversal();

				if (isset($match[2])) {
					parse_str($match[2], $params);
				}

				return '"' . $traverse . $link->getUri($params) . '"';
			}, $buffer);

			$dirname = $this->getCurrentDirName();
			if ($dirname === $this->dirname($link->getPageName()) && substr($link->getPageName(), 0, strlen($dirname)) === $dirname) {
				$diff = $dirname !== '' ? 1 : 0;
				$pageName = substr($link->getPageName(), strlen($dirname) + $diff);
				$pattern  = '/[\'"](?:\.\/)?';
				$pattern .= '(' . preg_quote($pageName, '/') . ')';
				$pattern .= '(?:\?([^\'"]+))?';
				$pattern .= '[\'"]/i';

				$buffer = preg_replace_callback($pattern, function($match) use (&$link) {
					$params = array();
					$traverse = $this->getPathTraversal();

					if (isset($match[2])) {
						parse_str($match[2], $params);
					}

					return '"' . $traverse . $link->getUri($params) . '"';
				}, $buffer);
			}
		}

		// Fix broken assets under the resources folder
		$original = $this->getOriginalPathTraversal();
		if ($original !== $traversal) {
			$buffer = preg_replace(
				'/([\'"])' . preg_quote($original, '/') . 'resources\//',
				'$1' . $traversal . 'resources/',
				$buffer
			);
		}

		return $buffer;
	}

	/**
	 * Returns an array of permalink entries.
	 *
	 * @return Permalink[]
	 */
	public function getLinks() {
		if ($this->links === null) {
			$store = json_decode($this->studio->getopt('permalinks.map', '[]'));
			$this->links = array();

			foreach ($store as $entry) {
				if ($entry->page === 'tools.php' && $this->homeDisabled) {
					$entry->permalink = '';
				}

				$this->links[] = new Permalink($entry->page, $entry->permalink, $this);
			}
		}

		return $this->links;
	}

	/**
	 * Returns true if the specified page has a registered permalink.
	 *
	 * @param string $pageName
	 * @return bool
	 */
	public function hasLink($pageName) {
		$links = $this->getLinks();

		foreach ($links as $link) {
			if ($link->getPageName() === $pageName) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the permalink object for the specified page or `null` if not registered.
	 *
	 * @param string $pageName
	 * @return Permalink|null
	 */
	public function getLink($pageName) {
		$links = $this->getLinks();

		foreach ($links as $link) {
			if ($link->getPageName() === $pageName) {
				return $link;
			}
		}

		return null;
	}

	/**
	 * Returns the permalink object for the specified path with additional checks for variations, such as permalinks
	 * bound to (and also without) query parameters.
	 *
	 * @param string $path
	 * @param string[] $query
	 * @return Permalink|null
	 */
	public function getLinkWithVariations($path, $query = null) {
		$path = ltrim($path, '/');

		if (is_null($query)) {
			$query = [];

			if (($index = strpos($path, '?')) !== false) {
				$queryString = substr($path, $index + 1);
				$path = substr($path, 0, $index);

				parse_str($queryString, $query);
			}
		}

		$variations = array(str_replace('\\', '/', $path));
		$params = array_keys($query);

		for ($width = 0; $width < count($params); $width++) {
			$get = array();

			for ($i = 0; $i <= $width; $i++) {
				$key = $params[$i];
				$get[$key] = $query[$key];
			}

			$variations[] = str_replace('\\', '/', $path) . '?' . http_build_query($get);
		}

		foreach (array_reverse($variations) as $page) {
			$link = $this->getLink($page);

			if ($link !== null) {
				return $link;
			}
		}

		return null;
	}

	/**
	 * Reverts a permalink into its `.php` format. Returns the same exact input if no match was found.
	 *
	 * @param string $permalink
	 * @return Permalink|null
	 */
	public function getReversePermalink($permalink) {
		$permalink = ltrim($permalink, '/');
		$links = $this->getLinks();

		// First look for an exact match
		foreach ($links as $link) {
			if ($link->getPermalink() === $permalink) {
				return $link->getPageName();
			}
		}

		// Then try to match with dynamic parameters
		foreach ($links as $link) {
			$params = $link->getParameters();
			$href = $link->getPermalink();

			if (count($params) > 0) {
				$exp = '/^' . preg_quote($href, '/') . '$/';

				foreach ($params as $param) {
					$exp = str_replace("/\\\${$param->name}", '/([^\/]+)', $exp);
				}

				if (preg_match($exp, $permalink, $matches)) {
					$query = [];

					foreach ($params as $index => $param) {
						$value = $matches[$index + 1];
						$query[$param->name] = $value;
					}

					return $link->getPageName() . '?' . http_build_query($query);
				}
			}
			else if ($permalink === $href) {
				return $link->getPageName();
			}
		}

		return $permalink;
	}

	/**
	 * Sets the permalink for the specified page if no existing permalink exists.
	 *
	 * Note: This does not save the new link to the database. You must call the `save()` method.
	 *
	 * @param string $pageName
	 * @param string $permalink
	 * @return void
	 */
	public function setDefaultLink($pageName, $permalink) {
		if (!$this->hasLink($pageName)) {
			$this->links[] = new Permalink($pageName, $permalink, $this);
		}
	}

	/**
	 * Sets the permalink for the specified page. Overwrites existing data.
	 *
	 * Note: This does not save the link to the database. You must call the `save()` method.
	 *
	 * @param string $pageName
	 * @param string $permalink
	 * @return void
	 */
	public function setLink($pageName, $permalink) {
		if ($this->hasLink($pageName)) {
			$existing = $this->getLink($pageName);
			$existing->setPermalink($permalink);
		}
		else {
			$this->links[] = new Permalink($pageName, $permalink, $this);
		}
	}

	/**
	 * Removes the permalink for the specified page if it exists.
	 *
	 * Note: This does not save the changes to the database. You must call the `save()` method.
	 *
	 * @param string $pageName
	 * @return void
	 */
	public function removeLink($pageName) {
		$index = null;

		foreach ($this->getLinks() as $i => $link) {
			if ($link->getPageName() === $pageName) {
				$index = $i;
			}
		}

		if ($index !== null) {
			unset($this->links[$index]);
		}
	}

	/**
	 * Saves permalinks to the database.
	 *
	 * @return void
	 */
	public function save() {
		$links = array();

		foreach ($this->getLinks() as $link) {
			$links[] = array(
				'page' => $link->getPageName(),
				'permalink' => $link->getPermalink()
			);
		}

		$this->studio->setopt('permalinks.map', json_encode($links));
	}

	/**
	 * Writes permalinks to the `.htaccess` file as rewrite rules.
	 *
	 * @return void
	 */
	public function write() {
		$links = $this->getLinks();
		$ordered = [];

		foreach ($links as $link) {
			if (count($link->getParameters()) === 0) {
				// Check for collisions with other ordered links
				$hit = false;
				foreach ($ordered as $index => $otherLink) {
					if ($otherLink->match($link->getPermalink())) {
						array_splice($ordered, $index, 0, [$link]);
						$hit = true;
						break;
					}
				}

				// Skip if hit
				if ($hit) {
					continue;
				}
			}

			$ordered[] = $link;
		}

		$composer = new ApacheComposer($this->studio->basedir . '/.htaccess');
		$composer->import($ordered);
		$composer->save();
	}

	/**
	 * Removes permalinks from the `.htaccess` file.
	 *
	 * @return void
	 */
	public function unwrite() {
		$composer = new ApacheComposer($this->studio->basedir . '/.htaccess');
		$composer->remove();
	}

	/**
	 * Returns the absolute path for the current file.
	 *
	 * @return string
	 */
	public function getCurrentFile() {
		return $this->curFile;
	}

	/**
	 * Returns the current page name, which is useful for finding the corresponding permalink.
	 *
	 * @return string
	 */
	public function getCurrentPageName() {
		$remainder = substr($this->getCurrentFile(), strlen($this->getRootPath()) + 1);
		return str_replace('\\', '/', $remainder);
	}

	/**
	 * Returns the current page name, which is useful for finding the corresponding permalink.
	 *
	 * @return Permalink|null
	 */
	public function getCurrentLink() {
		$remainder = substr($this->getCurrentFile(), strlen($this->getRootPath()) + 1);
		return $this->getLinkWithVariations($remainder, $_GET);
	}

	/**
	 * Returns the current page's directory name.
	 *
	 * @return string
	 */
	public function getCurrentDirName() {
		return $this->dirname($this->getCurrentPageName());
	}

	/**
	 * Returns the absolute path to the script's root directory.
	 *
	 * @return string
	 */
	public function getRootPath() {
		return $this->studio->basedir;
	}

	/**
	 * Returns a sequence of `../` segments to return to the script's root directory from the current page.
	 *
	 * @return string
	 */
	public function getPathTraversal() {
		if ($this->isNative()) {
			return str_repeat('../', substr_count($this->getCurrentPageName(), '/'));
		}

		$currentPath = trim($this->getCurrentPath(), '/');

		return str_repeat('../', substr_count($currentPath, '/'));
	}

	/**
	 * Returns a sequence of `../` segments that the original path for this page would have used.
	 *
	 * @return string
	 */
	public function getOriginalPathTraversal() {
		return str_repeat('../', substr_count($this->getCurrentPageName(), '/'));
	}

	/**
	 * Returns true if the current request is native (not using a permalinked version of the page).
	 *
	 * @return bool
	 */
	public function isNative() {
		$pageName = $this->getCurrentPageName();
		$request = $_SERVER['REQUEST_URI'];

		if (($index = strpos($request, '?')) !== false) {
			$request = substr($request, 0, $index);
		}

		if (substr($request, -strlen($pageName)) === $pageName) {
			return true;
		}

		if (substr($pageName, -10) === '/index.php' && substr($request, -1) === '/') {
			$pageName = str_replace('/index.php', '/', $pageName);

			if (substr($request, -strlen($pageName)) === $pageName) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieves the canonical for the current page if known, or returns `null` otherwise.
	 *
	 * @return string|null
	 */
	public function getCanonical() {
		$currentLink = $this->getCurrentLink();
		$currentPageName = $this->getCurrentPageName();

		if (!is_null($currentLink)) {
			return rtrim($this->studio->getopt('public-url'), '/') . '/' . $currentLink->getUri($_GET, false);
		}
		else if ($currentPageName === 'index.php') {
			return rtrim($this->studio->getopt('public-url'), '/') . '/';
		}
	}

	/**
	 * Retrieves the query arguments for the current page from the real query string (i.e. not including invisible query
	 * arguments from inside permalinks).
	 *
	 * @return array
	 */
	public function getQueryArguments() {
		if ($this->studio->usePermalinks() && !$this->isNative()) {
			if (($link = $this->getCurrentLink()) !== null) {
				$arguments = $_GET;

				foreach ($link->getParameters() as $param) {
					if (array_key_exists($param->name, $arguments)) {
						unset($arguments[$param->name]);
					}
				}

				return $arguments;
			}
		}

		return $_GET;
	}

}

class Permalink {

	private $pageName;
	private $permalink;

	/**
	 * @var PermalinkHandler
	 */
	private $handler;

	public function __construct($page, $permalink, $handler) {
		$this->pageName = $page;
		$this->permalink = $permalink;
		$this->handler = $handler;
	}

	/**
	 * Returns the page that this permalink is for. This will always be a file ending with the `php` extension and it
	 * will be relative to the script's root directory.
	 *
	 * @return string
	 */
	public function getPageName() {
		return $this->pageName;
	}

	/**
	 * Returns the permalink that the target page should use.
	 *
	 * @return string
	 */
	public function getPermalink() {
		return $this->permalink;
	}

	/**
	 * Edits the permalink.
	 *
	 * @param string $permalink
	 * @return void
	 */
	public function setPermalink($permalink) {
		$this->permalink = $permalink;
	}

	/**
	 * Returns the URI for the permalink with the given query arguments applied.
	 *
	 * The returned value will not be prefixed with a traversal string, which is necessary for it to work.
	 *
	 * @param string[]|string $arguments
	 * @param bool $appendUnknownArguments
	 * @return string
	 */
	public function getUri($arguments = array(), $appendUnknownArguments = true) {
		$params = $this->getParameters();
		$link = $this->getPermalink();

		if (is_string($arguments)) {
			if (($index = strpos($arguments, '?')) !== false) {
				$arguments = substr($arguments, $index + 1);
				$newArguments = [];

				parse_str($arguments, $newArguments);
				$arguments = $newArguments;
			}
			else {
				$arguments = [];
			}
		}

		// RC1608728: Check for aliases of the permalink for these specific arguments
		if (count($arguments) > 0) {
			$target = $this->pageName . '?' . http_build_query($arguments, '', '&');

			if (($alias = $this->handler->getLink($target)) !== null && $alias !== $this) {
				return $alias->getUri($arguments);
			}
		}

		foreach ($params as $param) {
			if (isset($arguments[$param->name])) {
				$link = str_replace('$' . $param->name, $arguments[$param->name], $link);
				unset($arguments[$param->name]);
			}
			else {
				$link = str_replace('$' . $param->name, '', $link);
			}
		}

		foreach ($arguments as $key => $value) {
			$str = http_build_query(array($key => $value));

			if (strpos($this->getPageName(), '&' . $str) !== false) {
				unset($arguments[$key]);
			}
			else if (strpos($this->getPageName(), '?' . $str) !== false) {
				unset($arguments[$key]);
			}
		}

		if (count($arguments) > 0 && $appendUnknownArguments) {
			$query = http_build_query($arguments);

			if (strpos($link, '?') === false) {
				$link .= '?';
			}
			else {
				$link .= '&';
			}

			$link .= $query;
		}

		return $link;
	}

	/**
	 * Returns the query parameters for this permalink.
	 *
	 * @return PermalinkParameter[]
	 */
	public function getParameters() {
		$permalink = $this->getPermalink();
		$parameters = array();

		preg_match_all('/\$\w+\b/', $permalink, $matches, PREG_OFFSET_CAPTURE);

		foreach ($matches[0] as $match) {
			$param = new PermalinkParameter();
			$param->name = substr($match[0], 1);
			$param->offset = $match[1];

			$parameters[] = $param;
		}

		return array_reverse($parameters);
	}

	/**
	 * Converts the permalink's page into a regular expression for an apache rewrite rule.
	 *
	 * @return string
	 */
	public function getApacheExp() {
		$rule = $this->getPermalink();

		// Convert query parameters
		foreach ($this->getParameters() as $param) {
			$rule = str_replace('$' . $param->name, '(.+)', $rule);
		}

		// Split the rule by (.+) to get the parts that should be 'quoted'
		$unsafe = explode('(.+)', $rule);
		foreach ($unsafe as $i => $str) {
			$unsafe[$i] = preg_quote($str);
		}

		// Join the strings back together
		$rule = implode('(.+)', $unsafe);

		// Escape spaces
		$rule = str_replace(' ', '\\ ', $rule);

		return '^' . $rule . '$';
	}

	/**
	 * Returns the target path to use for the permalink in a rewrite rule.
	 *
	 * @return string
	 */
	public function getApacheTarget() {
		$path = $this->getPageName();

		// Add query parameters
		if (count($this->getParameters()) > 0) {
			$params = array();
			foreach ($this->getParameters() as $index => $param) {
				$params[$param->name] = '$' . ($index + 1);
			}

			$path .= '?' . str_replace('%24', '$', http_build_query($params));
		}

		return $path;
	}

	/**
	 * Matches this permalink against the given input.
	 *
	 * @param string $input
	 * @return bool
	 */
	public function match($input) {
		$exp = '/^' . preg_quote($this->getPermalink(), '/') . '$/';

		foreach ($this->getParameters() as $param) {
			$exp = str_replace("/\\\${$param->name}", '/([^\/]+)', $exp);
		}

		if (preg_match($exp, $input)) {
			return true;
		}

		return false;
	}

}

class PermalinkParameter {
	public $name;
	public $offset;
}
