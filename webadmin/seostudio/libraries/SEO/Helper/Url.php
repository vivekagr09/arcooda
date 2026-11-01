<?php

namespace SEO\Helper;

use Exception;
use SEO\Common\SEOException;
use Studio\Util\Algorithm\Punycode;

/**
 * Converts a URL into a universal object
 */
class Url
{
    /**
     * The domain as ASCII. This is deprecated, move to `hostName` instead.
     * @var string
     * @deprecated
     */
    public $domain;

    /**
     * The scheme (`http` or `https`).
     * @var string
     */
    public $scheme;

    /**
     * The hostname in ascii format.
     * @var string
     */
    public $hostName;

    /**
     * The hostname in UTF-8 format.
     * @var string
     */
    public $unicodeHostName;

    /**
     * The port number.
     * @var int
     */
    public $port;

    /**
     * The path. Always starts with `/`.
     * @var string
     */
    public $path;

    /**
     * The query string or `null` if not available.
     * @var string|null
     */
    public $query;

    /**
     * Creates a new URL object.
     * @param string|Url $url
     */
    public function __construct($url) {
        global $studio;

        if (!is_string($url)) {
            if ($url instanceof Url) {
                $this->scheme = $url->scheme;
                $this->hostName = $url->hostName;
                $this->unicodeHostName = $url->unicodeHostName;
                $this->port = $url->port;
                $this->path = $url->path;
                $this->query = $url->query;

                return;
            }

            throw new SEOException('Input is not a string');
        }

        $url = trim($url);

        // Fix incomplete or missing schemes
        if (substr($url, 0, 2) === '//') $url = 'http:' . $url;
        else if (!preg_match('/^([a-z]+):\/\//i', $url)) $url = 'http://' . $url;

        // Match against a very-forgiving regular expression
        if (!preg_match('/^([a-z]+):\/\/([^\/:.]+(?:\.?[^.:\/?#]+)+)(?::(\d+))?(\/[^?]*)?(?:\?(.*))?$/i', $url, $matches)) {
            throw new SEOException('Input is invalid or malformed');
        }

        // Get data from the matched expression
        $this->scheme = mb_strtolower($matches[1]);
        $this->hostName = mb_strtolower($matches[2]);
        $this->port = !empty($matches[3]) ? intval($matches[3]) : ($this->scheme == 'https' ? 443 : 80);
        $this->path = !empty($matches[4]) ? $matches[4] : '/';
        $this->query = isset($matches[5]) ? $matches[5] : null;

        // Set fallback data
        $this->unicodeHostName = $this->hostName;
        $this->domain = $this->hostName;

        // Handle errors
        if (!in_array($this->scheme, ['http', 'https'])) throw new SEOException('Invalid scheme');
        if ($this->port <= 0 || $this->port > 65535) throw new SEOException('Invalid port');

        // Handle local hostnames and IP addresses
        if ($studio->getopt('allow-local-hostnames') !== 'On') {
            if (strpos($this->hostName, '.') === false) {
                throw new SEOException('Invalid hostname');
            }

            if (filter_var($this->hostName, FILTER_VALIDATE_IP) !== false) {
                if (filter_var($this->hostName, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                    throw new SEOException('Invalid hostname');
                }
            }
        }

        // Check if the hostname contains unicode
        if (preg_match('/[^\0-\x7E]/', $this->hostName)) {
            $this->hostName = $this->domain = mb_convert_encoding($this->hostName, mb_detect_encoding($this->hostName), 'utf-8');

            if (preg_match('/((?:\.[^.]+)+)$/', $this->hostName, $matches)) {
                $this->unicodeHostName = $this->hostName;
                $this->hostName = $this->domain = Punycode::toAscii($this->hostName);
            }
        }

        // Check if the hostname is punycode
        else if (preg_match('/(^|\.)xn--/', $this->hostName)) {
            try {
                $this->unicodeHostName = Punycode::toUnicode($this->hostName);
            }
            catch (Exception $e) {
                // Do nothing!
                // We now know this isn't punycode, so keep the hostname untouched
            }
        }

        // Build the link
        $link = $this->getLink();

        // Validate the built link
        if (filter_var($link, FILTER_VALIDATE_URL) === false) throw new SEOException('Invalid link');
        if (parse_url($link) === false) throw new SEOException('Invalid link');
    }

    /**
     * Returns the hostname.
     *
     * @return string
     */
    public function getHostname() {
        return $this->hostName;
    }

    /**
     * Returns an absolute URL.
     *
     * @return string
     */
    public function getAbsoluteURL() {
        $path = $this->path ?: '/';
        $scheme = $this->scheme ?: 'https';

        return $scheme . '://' . $this->hostName . $path;
    }

    /**
     * Returns an absolute URL with the specified path.
     *
     * @param string $path
     * @return string
     */
    public function getPath($path) {
        $path = '/' . ltrim($path ?: '/', '/');
        $scheme = $this->scheme ?: 'https';

        return $scheme . '://' . $this->hostName . $path;
    }

    /**
     * Returns `true` if this url has a hostname with unicode.
     *
     * @return bool
     */
    public function hasUnicodeHostName() {
        return $this->unicodeHostName !== $this->hostName;
    }

    /**
     * Returns the full ASCII link.
     *
     * @return string
     */
    public function getLink() {
        return $this->buildLink($this->hostName);
    }

    /**
     * Returns the link with its unicode hostname, if applicable. Otherwise, returns the ASCII version.
     *
     * @return string
     */
    public function getDisplayLink() {
        return urldecode($this->buildLink(
            !is_null($this->unicodeHostName) ? $this->unicodeHostName : $this->hostName
        ));
    }

    /**
     * Builds the link for the given hostname.
     *
     * @param string $hostName
     * @return string
     */
    protected function buildLink($hostName) {
        $link = $this->scheme . '://';
        $link .= $hostName;

        if (!(($this->port == 80 && $this->scheme == 'http') || ($this->port == 443 && $this->scheme == 'https'))) {
            $link .= ':' . $this->port;
        }

        $link .= $this->path;

        if (!is_null($this->query)) {
            $link .= '?' . $this->query;
        }

        return $link;
    }

    /**
     * Normalizes the given link for comparison.
     *
     * @param string $link
     * @return string
     */
    public static function normalize($link) {
        $link = str_ireplace(['http://', 'https://', 'www.'], '', trim($link));

        if (function_exists('mb_strtolower')) {
            $link = mb_strtolower($link);
        }
        else {
            $link = strtolower($link);
        }

        return $link;
    }

}
