<?php

namespace Studio\Util\Http;

use Exception;

class DNS {

	private static $cache = [];

	/**
	 * Gets the IP address for the given hostname, with internal caching.
	 *
	 * @param string $hostname
	 * @return string
	 */
	public static function get($hostname) {
		$hostname = strtolower($hostname);

		if (array_key_exists($hostname, static::$cache)) {
			return static::$cache[$hostname];
		}

		return static::resolve($hostname);
	}

	/**
	 * Resolves an IP address and caches it.
	 *
	 * @param string $hostname
	 * @return string
	 * @throws Exception
	 */
	private static function resolve($hostname) {
		$addr = @gethostbyname($hostname);

		if ($addr === $hostname) {
			throw new Exception(rt('Couldn\'t connect to {$1}', $hostname));
		}

		static::$cache[$hostname] = $addr;
		return $addr;
	}

	/**
	 * Returns an array of resolver addresses in the format `hostname:ip` for use with cURL.
	 *
	 * @return string[]
	 */
	public static function getResolveArray() {
		$addresses = [];

		foreach (static::$cache as $hostname => $ip) {
			$addresses[] = $hostname . ':80:' . $ip;
			$addresses[] = $hostname . ':443:' . $ip;
		}

		return $addresses;
	}

}
