<?php

namespace SEO\Parsers;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;

class GoogleSearchParserNative {

	/**
	 * The parsed document instance.
	 *
	 * @var DOMDocument
	 */
	protected $document;

	/**
	 * The xpath instance for the document.
	 *
	 * @var DOMXPath
	 */
	protected $xpath;

	/**
	 * The detected search results container.
	 *
	 * @var DOMNode
	 */
	protected $container;

	/**
	 * The detected search result row elements.
	 *
	 * @var DOMNode[]
	 */
	protected $rowElements = [];

	/**
	 * The extracted search results.
	 *
	 * @var GoogleSearchResult[]
	 */
	protected $results = [];

	/**
	 * The total number of results or `null` if not known.
	 *
	 * @var int|null
	 */
	protected $numResults;

	/**
	 * The number of milliseconds it took for the parser to read this page.
	 *
	 * @var float
	 */
	public $took;

	/**
	 * Creates a new `GoogleSearchParser` instance.
	 *
	 * @param string $html
	 */
	public function __construct($html) {
		$start = microtime(true);
		$html = $this->optimizeInput($html);

		libxml_use_internal_errors(true);
		$this->document = new DOMDocument('1.1');
		$this->document->loadHTML($html);
		$this->xpath = new DOMXPath($this->document);

		if (($this->container = $this->findMainContainer()) === null) {
			return;
		}

		if ($this->hasNoResults()) {
			return;
		}

		$this->rowElements = $this->findRowElements();
		$this->results = $this->parseResults();
		$this->numResults = $this->extractResultCount();

		$this->took = (microtime(true) - $start) * 1000;
	}

	/**
	 * Extracts the total number of results as reported by Google. Returns `null` if it cannot be determined.
	 *
	 * @return int|null
	 */
	protected function extractResultCount() {
		$containerPath = $this->container->getNodePath();

		// Match exact identifier
		if (($element = $this->findFirst($this->document, './/*[@id="result-stats"]')) !== null) {
			if (substr($element->getNodePath(), strlen($containerPath)) !== $containerPath) {
				if (preg_match('/\b([\d,.]+)\b/', $element->textContent, $matches)) {
					return intval(preg_replace('/[^\d]/', '', $matches[1]));
				}
			}
		}

		// Match parent
		if (($element = $this->findFirst($this->document, './/*[@id="appbar"]')) !== null) {
			if (substr($element->getNodePath(), strlen($containerPath)) !== $containerPath) {
				if (preg_match('/\b([\d,.]+)\b/', $element->textContent, $matches)) {
					return intval(preg_replace('/[^\d]/', '', $matches[1]));
				}
			}
		}

		// Match by nobr
		if (($element = $this->findFirst($this->document, './/*/nobr')) !== null) {
			if (substr($element->getNodePath(), strlen($containerPath)) !== $containerPath) {
				if (preg_match('/(^| )(\d[\d,.]*)\b/', $element->parentNode->textContent, $matches)) {
					return intval(preg_replace('/[^\d]/', '', $matches[2]));
				}
			}
		}
	}

	/**
	 * Parses the row elements into search results.
	 *
	 * @return GoogleSearchResult[]
	 */
	protected function parseResults() {
		$results = [];
		$index = 0;

		foreach ($this->rowElements as $rowElement) {
			$link = $this->extractResultLink($rowElement);
			$headingElement = $this->extractResultHeading($rowElement);
			$citationElement = $this->extractResultCitation($rowElement, $link);

			if ($headingElement && $citationElement) {
				$description = $this->extractResultDescription($rowElement, $headingElement, $citationElement);

				$result = $results[] = new GoogleSearchResult();
				$result->index = $index++;
				$result->title = trim($headingElement->textContent);
				$result->citation = trim($citationElement->textContent);
				$result->description = $description;
				$result->href = $link;
				$result->node = $rowElement;
			}
		}

		return $results;
	}

	/**
	 * Extracts the link for the given row element or `null` if it's not for an external website.
	 *
	 * @param DOMNode $node
	 * @return string|null
	 */
	protected function extractResultLink(DOMNode $node) {
		// Match link with a heading inside
		$link = $this->findFirst($node, './/a[count(.//h2|.//h3) > 0]');

		// Fallback to standalone link if needed & extract href
		if (($link = $link ?: $this->findFirst($node, './/a')) !== null) {
			return $this->extractResultHref($link);
		}
	}

	/**
	 * Extracts the link for the given anchor element or `null` if it's not for an external website.
	 *
	 * @param DOMNode $node
	 * @return string|null
	 */
	protected function extractResultHref(DOMNode $node) {
		if ($node->attributes) {
			foreach ($node->attributes as $attribute) {
				if (preg_match('/^https?:\/\//i', $attribute->value)) {
					return $attribute->value;
				}

				if (substr($attribute->value, 0, 5) === '/url?') {
					if (preg_match('/(&|&amp;)url=https?:\/\/translate\.google\.com\//m', $attribute->value)) continue;
					if (preg_match('/(&|&amp;)url=https?:\/\/\w+cache\.googleusercontent\.com\//m', $attribute->value)) continue;

					if (!preg_match('/[\?&]([^=]+)=((?:https:\/\/|http:\/\/|\/\/)[^&]+)/', $attribute->value, $matches)) {
						return null;
					}

					return html_entity_decode(urldecode($matches[2]), ENT_QUOTES);
				}
			}
		}
	}

	/**
	 * Extracts the heading for the given row element and returns its text or `null` if not found.
	 *
	 * @param DOMNode $node
	 * @return DOMNode|null
	 */
	protected function extractResultHeading(DOMNode $node) {
		$headings = $this->find($node, './/a//h2|.//a//h3');

		// Sort headings by xpath length
		usort($headings, function(DOMNode $a, DOMNode $b) use ($headings) {
			$a = $a->getNodePath();
			$b = $b->getNodePath();

			$diff = strlen($a) - strlen($b);

			if ($diff === 0) {
				return array_search($a, $headings) - array_search($b, $headings);
			}

			return $diff;
		});

		if (count($headings) > 0) {
			return $headings[0];
		}
	}

	/**
	 * Extracts the citation for the given row element and returns its text or `null` if not found.
	 *
	 * @param DOMNode $node
	 * @param string $link
	 * @return DOMNode|null
	 */
	protected function extractResultCitation(DOMNode $node, $link) {
		// Look for exact <cite> elements
		if (($cite = $this->findFirst($node, './/cite')) !== null) {
			return $cite;
		}

		// Look for the first element whose role is set to "text"
		if (($cite = $this->findFirst($node, './/*[@role="text"]')) !== null) {
			return $cite;
		}

		if (($parsed = @parse_url($link)) !== false && isset($parsed['host'])) {
			$matches = $this->find($node, './/a//*[contains(normalize-space(),"' . $parsed['host'] . '")]');

			if (count($matches) > 0) {
				usort($matches, function($a, $b) use ($matches) {
					return strlen($a->textContent) - strlen($b->textContent);
				});

				return $matches[0];
			}
		}
	}

	/**
	 * Extracts the text description for the given row element or returns `null` if it could not be determined.
	 *
	 * @param DOMNode $node
	 * @param DOMNode $heading
	 * @param DOMNode $citation
	 * @return string|null
	 */
	protected function extractResultDescription(DOMNode $node, DOMNode $heading, DOMNode $citation) {
		$hasFoundCitation = false;
		$hasFoundHeading = false;
		$ignoredElements = ['em', 'i', 'strong', 'b', 'cite', 'h2', 'h3'];
		$scannedPaths = [];

		$headingPath = $heading->getNodePath();
		$citationPath = $citation->getNodePath();

		$candidates = [];

		foreach ($this->find($node, './/*[normalize-space(text())]') as $element) {
			$elementPath = $element->getNodePath();

			if (substr($elementPath, 0, strlen($citationPath)) === $citationPath) {
				$hasFoundCitation = true;
				continue;
			}

			if (substr($elementPath, 0, strlen($headingPath)) === $headingPath) {
				$hasFoundHeading = true;
				continue;
			}

			if (strpos($elementPath, '/a/') !== false) {
				$scannedPaths[] = $elementPath;
				continue;
			}

			if (in_array($element->nodeName, $ignoredElements)) {
				$scannedPaths[] = $elementPath;
				continue;
			}

			if (in_array($element->nodeName, ['th', 'td'])) {
				return '';
			}

			if ($hasFoundCitation && $hasFoundHeading) {
				$skip = false;

				foreach ($scannedPaths as $path) {
					if (substr($elementPath, 0, strlen($path)) === $path) {
						$skip = true;
						break;
					}
				}

				if ($skip) {
					continue;
				}

				$content = trim($element->textContent);

				if (mb_substr($content, -1) === '—') {
					$scannedPaths[] = $elementPath;
					continue;
				}

				if ($this->findFirst($element, './/em[1]')) {
					return $content;
				}
				else {
					$candidates[] = $content;
				}
			}
		}

		if (count($candidates) > 0) {
			usort($candidates, function($a, $b) use ($candidates) {
				$diff = strlen($b) - strlen($a);

				if ($diff === 0) {
					return array_search($b, $candidates) - array_search($a, $candidates);
				}

				return $diff;
			});

			return $candidates[0];
		}
	}

	/**
	 * Returns an array of nodes that are each responsible for a distinct search result row.
	 *
	 * @return DOMNode[]
	 * @throws Exception
	 */
	protected function findRowElements() {
		// Get all links in the search result container
		// This will also include links for rich elements like places and images, as well as sitelinks
		$links = $this->find($this->container, './/a[@ping][count(.//h2|.//h3) > 0]');

		// Filter out links to Google
		// $links = array_filter($links, function(DOMNode $link) {
		// 	if (isset($link->attributes) && ($href = $link->attributes->getNamedItem('href'))) {
		// 		if (preg_match('/^https?:\/\/(?:[a-z0-9_]+\.)*google\.com(\/.+)/i', $href->nodeValue, $matches)) {
		// 			$path = $matches[1];

		// 			if (substr($path, 0, 5) !== '/url?') {
		// 				var_dump($path);
		// 				return false;
		// 			}
		// 		}
		// 	}

		// 	return true;
		// });

		// Reseat array
		$links = array_values($links);
		$rows = array();

		// Find the common ancestor between all links
		// The search result rows will be directly underneath this element
		$commonAncestors = $this->getCommonAncestors($links);

		if (empty($commonAncestors)) {
			return array();
		}

		// Get ancestor chains for each link
		$ancestorChains = array();
		$ancestorsToSkip = count($commonAncestors);

		foreach ($links as $link) {
			$hash = spl_object_hash($link);
			$chain = $this->getAncestorChain($link);
			$ancestorChains[$hash] = array_slice($chain, $ancestorsToSkip);
		}

		// Iterate over all links
		foreach ($links as $link) {
			$linkHash = spl_object_hash($link);
			$ancestors = array_values($ancestorChains[$linkHash]);
			$ancestorHashes = array();

			foreach ($ancestors as $index => $ancestor) {
				$ancestorHashes[spl_object_hash($ancestor)] = $index;
			}

			// Iterate over ancestor chains for other hashes
			// Disqualify ancestors that are shared between the current link and any others
			// Then, find the first ancestor for our link that is not disqualified
			foreach ($ancestorChains as $otherHash => $otherAncestors) {
				if ($otherHash === $linkHash) {
					continue;
				}

				foreach ($otherAncestors as $otherAncestor) {
					$hash = spl_object_hash($otherAncestor);

					if (isset($ancestorHashes[$hash])) {
						$index = $ancestorHashes[$hash];
						$ancestors[$index] = null;
						$ancestorHashes[$hash] = null;
					}
				}
			}

			// Find the first non-null ancestor
			foreach ($ancestors as $ancestor) {
				if (!is_null($ancestor)) {
					$rows[] = $ancestor;
					break;
				}
			}
		}

		// Compute nodes using the classic method
		$ancestor = $commonAncestors[count($commonAncestors) - 1];
		$alternativeRows = iterator_to_array($ancestor->childNodes);

		if (count($alternativeRows) > count($rows)) {
			$rows = $alternativeRows;
		}

		// Return the child nodes as an array
		// return
		return $rows;
	}

	/**
	 * Returns true if there are no available search results.
	 *
	 * @return bool
	 */
	protected function hasNoResults() {
		return ($this->findFirst($this->container, './/a[1]') === null);
	}

	/**
	 * Executes the xpath expression on the document.
	 *
	 * @param DOMNode $element
	 * @param string $expression
	 * @return DOMNode[]
	 */
	protected function find(DOMNode $element, $expression) {
		$list = $this->xpath->query($expression, $element);
		return iterator_to_array($list);
	}

	/**
	 * Executes the xpath expression on the document and returns the first match or `null`.
	 *
	 * @param DOMNode $element
	 * @param string $expression
	 * @return DOMNode|null
	 */
	protected function findFirst(DOMNode $element, $expression) {
		$list = $this->xpath->query($expression, $element);
		return $list->item(0);
	}

	/**
	 * Returns an array of common ancestors between all of the given elements, or an empty array if there are no
	 * common ancestors between them. The last element is the deepest node in common.
	 *
	 * @param DOMNode[] $nodes
	 * @return DOMNode[]
	 */
	protected function getCommonAncestors($nodes) {
		if (empty($nodes)) {
			return;
		}

		$chains = $this->getAncestorChain($nodes[0]);
		$chainsLength = count($chains);

		foreach ($nodes as $node) {
			$ancestors = $this->getAncestorChain($node);
			$indices = [];

			foreach ($ancestors as $index => $nodeAncestor) {
				if ($index < $chainsLength) {
					$chainAncestor = $chains[$index];

					if ($chainAncestor !== $nodeAncestor) {
						$indices[] = $index;
					}
				}
			}

			if (count($indices) > 0) {
				foreach (array_reverse($indices) as $index) {
					unset($chains[$index]);
				}

				$chainsLength = count($chains);
				$chains = array_values($chains);
			}
		}

		// if (count($chains) > 0) {
		// 	return $chains[count($chains) - 1];
		// }

		return $chains;
	}

	/**
	 * Tries to find the main container on the page.
	 *
	 * @return DOMNode|null
	 */
	protected function findMainContainer() {
		if (($element = $this->findFirst($this->document, './/*[@role="main"]')) !== null) {
			return $element;
		}

		if (($element = $this->findFirst($this->document, './/*[@id="main"]')) !== null) {
			return $element;
		}
	}

	/**
	 * Returns an array of all ancestors for a node, where the last element in the array is the direct parent.
	 *
	 * @param DOMNode $node
	 * @return array
	 */
	protected function getAncestorChain(DOMNode $node) {
		$parents = [];
		$parentTarget = $node;

		while (!is_null($parent = $parentTarget->parentNode)) {
			array_unshift($parents, $parent);
			$parentTarget = $parent;
		}

		return $parents;
	}

	/**
	 * Reduces the size of the given HTML string by stripping out scripts and styles.
	 *
	 * @param string $html
	 * @return string
	 */
	protected function optimizeInput(&$html) {
		$html = preg_replace('/(<(?:script|style|svg)\b[^<]*(?:(?!<\/(?:script|style|svg)>)<[^<]*)*<\/(?:script|style|svg)>| (aria-[\w-]+|style|eid|tabindex|data-[\w-]+|class|js\w+)="((?!https?:|\/)[^"]*)?")/i', '', $html);
		return $html;
	}

	/**
	 * Returns an array of results.
	 *
	 * @return GoogleSearchResult[]
	 */
	public function getResults() {
		return $this->results;
	}

	/**
	 * Returns the number of results available for the current data set.
	 *
	 * @return int
	 */
	public function getNumResults() {
		return count($this->results);
	}

	/**
	 * Returns the estimated total number of results available for this query.
	 *
	 * @return int
	 */
	public function getNumTotalResults() {
		return $this->numResults;
	}

	/**
	 * Returns the DOM element that is believed to contain the search results for this page. Returns `null` if a
	 * container element could not be reliably determined.
	 *
	 * @return HTMLDocumentNode|null
	 */
	public function getContainer() {
		return $this->container;
	}

}
