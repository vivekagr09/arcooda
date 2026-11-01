<?php

namespace Studio\Util;

use Throwable;

class TrustedNetwork {

	const REGEX_IPV4 = '/^((?:[0-9]{1,3}\.){3}[0-9]{1,3})$/';
	const REGEX_IPV4_CIDR = '/^((?:[0-9]{1,3}\.){3}[0-9]{1,3})(?:$|\/(\d{1,2}))$/';
	const REGEX_IPV6 = '/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/';
	const REGEX_IPV6_CIDR = '/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))(?:$|\/(\d{1,3}))$/';

	/**
	 * Initializes the trusted network, replacing the `REMOTE_ADDR` server variable as permitted.
	 *
	 * @return void
	 */
	public static function init() {
		try {
			if (!isset($_SERVER['REMOTE_ADDR'])) {
				return;
			}

			$addresses = static::getTrustedAddresses();
			$currentAddress = $_SERVER['REMOTE_ADDR'];

			foreach ($addresses as $address) {
				if ($address->matches($currentAddress)) {
					$forwardedAddresses = static::getForwardedAddresses();

					foreach ($forwardedAddresses as $for) {
						$matched = false;

						foreach ($addresses as $innerAddress) {
							if ($innerAddress->matches($for)) {
								$matched = true;
								break;
							}
						}

						if (!$matched) {
							$_SERVER['REMOTE_ADDR'] = $for;
							break;
						}
					}

					break;
				}
			}

			// Now that trusted proxies have been applied, check for cloudflare
			if (($match = static::getEffectiveCloudflareIP()) !== null) {
				$_SERVER['REMOTE_ADDR'] = $match;
			}
		}
		catch (Throwable $ex) {}
	}

	/**
	 * Returns the value of the `CF-Connecting-IP` header if the current client is detected to be a Cloudflare Edge node.
	 *
	 * @param string|null $connectionIP
	 * @return string|null
	 */
	public static function getEffectiveCloudflareIP($connectionIP = null) {
		global $studio;

		if ($studio->getopt("trusted-network-cloudflare", 'On') !== 'On') {
			return null;
		}

		if (!isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			return null;
		}

		$target = $connectionIP === null ? $_SERVER['REMOTE_ADDR'] : $connectionIP;

		if (static::isCloudflareAddress($target)) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		return null;
	}

	/**
	 * Returns true if the given IP is a Cloudflare edge node.
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isCloudflareAddress($ip) {
		// Source: https://www.cloudflare.com/ips/
		$whitelist = [
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'104.16.0.0/13',
			'104.24.0.0/14',
			'108.162.192.0/18',
			'131.0.72.0/22',
			'141.101.64.0/18',
			'162.158.0.0/15',
			'172.64.0.0/13',
			'173.245.48.0/20',
			'188.114.96.0/20',
			'190.93.240.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17',
			'2400:cb00::/32',
			'2606:4700::/32',
			'2803:f800::/32',
			'2405:b500::/32',
			'2405:8100::/32',
			'2a06:98c0::/29',
			'2c0f:f248::/32',
		];

		foreach ($whitelist as $edgeAddress) {
			$address = new TrustedNetworkAddress($edgeAddress);

			if ($address->matches($ip)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns an array that explains the current client address.
	 *
	 * @return mixed
	 */
	public static function getEffectiveClientIP() {
		$connection = static::getEffectiveConnectionIP();
		$cloudflare = static::getEffectiveCloudflareIP($connection['result']);

		if ($cloudflare !== null) {
			return ['result' => $cloudflare, 'reason' => 'Cloudflare'];
		}

		if ($connection['result'] !== $_SERVER['REMOTE_ADDR']) {
			return ['result' => $connection['result'], 'reason' => 'Trusted proxies'];
		}

		return ['result' => $connection['result'], 'reason' => 'Provided by webserver'];
	}

	/**
	 * Returns an array that explains the current connection address.
	 *
	 * @return mixed
	 */
	public static function getEffectiveConnectionIP() {
		$addresses = static::getTrustedAddresses();
		$currentAddress = $_SERVER['REMOTE_ADDR'];

		foreach ($addresses as $address) {
			if ($address->matches($currentAddress)) {
				$forwardedAddresses = static::getForwardedAddresses();

				foreach ($forwardedAddresses as $for) {
					$matched = false;

					foreach ($addresses as $innerAddress) {
						if ($innerAddress->matches($for)) {
							$matched = true;
							break;
						}
					}

					if (!$matched) {
						return [
							'result' => $for,
							'reason' => 'Trusted proxy: ' . $address->input
						];
					}
				}

				break;
			}
		}

		return [
			'result' => $_SERVER['REMOTE_ADDR'],
			'reason' => 'No matching trusted proxies'
		];
	}

	/**
	 * Returns an array of `X-Forwarded-For` addresses in reverse (operational) order. Note that these are considered
	 * highly untrustworthy and potentially malicious until proven otherwise.
	 *
	 * @return string[]
	 */
	public static function getForwardedAddresses() {
		if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return [];
		}

		$input = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$values = array_reverse(explode(',', $input));
		$forwarded = [];

		foreach ($values as $value) {
			$value = trim($value);

			if (empty($value)) break;
			if (!preg_match(static::REGEX_IPV4, $value) && !preg_match(static::REGEX_IPV6, $value)) break;

			// Check ipv4 validity
			if (strpos($value, ':') === false) {
				foreach (explode('.', $value) as $byte) {
					if (intval($byte) < 0 || intval($byte) > 255) {
						break 2;
					}
				}
			}

			$forwarded[] = $value;
		}

		return $forwarded;
	}

	/**
	 * Returns an array of `TrustedNetworkAddress` objects.
	 *
	 * @return TrustedNetworkAddress[]
	 */
	public static function getTrustedAddresses() {
		global $studio;

		$value = $studio->getopt('trusted-network-proxies', '');
		$lines = preg_split("/(?:\r?\n)+/", $value);
		$addresses = [];

		foreach ($lines as $line) {
			$line = trim($line);

			if (preg_match(static::REGEX_IPV4_CIDR, $line)) {
				$addresses[] = new TrustedNetworkAddress($line);
			}
			else if (preg_match(static::REGEX_IPV6_CIDR, $line)) {
				$addresses[] = new TrustedNetworkAddress($line);
			}
		}

		return $addresses;
	}

}

class TrustedNetworkAddress {

	/**
	 * The original address as it was input.
	 *
	 * @var string
	 */
	public $input;

	/**
	 * @param string $input
	 */
	public function __construct($input) {
		$this->input = $input;
	}

	/**
	 * Returns the subnet portion of the address.
	 *
	 * @return string
	 */
	public function getSubnet() {
		if (strpos($this->input, '/') === false) {
			return $this->input;
		}
		else {
			list($subnet, $bits) = explode('/', $this->input);
			return $subnet;
		}
	}

	/**
	 * Returns the bits portion of the address.
	 *
	 * @return int
	 */
	public function getBits() {
		if (strpos($this->input, '/') === false) {
			return 32;
		}
		else {
			list($subnet, $bits) = explode('/', $this->input);
			return intval($bits);
		}
	}

	/**
	 * Returns true if the given IP matches this address or range.
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function matches($ip) {
		if (strpos($this->input, ':') !== false && strpos($ip, ':') === false) {
			return false;
		}

		if (strpos($this->input, ':') === false && strpos($ip, ':') !== false) {
			return false;
		}

		if (strpos($this->input, '/') === false) {
			return inet_pton($ip) == inet_pton($this->input);
		}
		else if (strpos($this->input, ':') !== false) {
			list($subnet, $bits) = explode('/', $this->input);

			$allowed = static::getNormalizedRange($subnet, intval($bits));
			$input = static::getNormalizedRange($ip, intval($bits));

			return $allowed === $input;
		}
		else {
			list($subnet, $bits) = explode('/', $this->input);

			// Check subnet for validity
			foreach (explode('.', $subnet) as $byte) {
				if (intval($byte) < 0 || intval($byte) > 255) {
					return false;
				}
			}

			// Check input for validity
			foreach (explode('.', $ip) as $byte) {
				if (intval($byte) < 0 || intval($byte) > 255) {
					return false;
				}
			}

			$subnet = unpack('H*', inet_pton($subnet));

			foreach ($subnet as $i => $h) {
				$subnet[$i] = base_convert($h, 16, 2);
			}

			$subnet = substr(implode('', $subnet), 0, $bits);
			$ip = unpack('H*', inet_pton($ip));

			foreach ($ip as $i => $h) {
				$ip[$i] = base_convert($h, 16, 2);
			}

			$ip = substr(implode('', $ip), 0, $bits);

			return $subnet == $ip;
		}
	}

	/**
	 * Normalizes the IPv6 address to preserve the first given bits. Returns the exact input if not IPv6.
	 *
	 * @param string $ip
	 * @param int $bits
	 * @return string|false
	 */
	private static function getNormalizedRange($ip, $bits = 64) {
		$origBits = $bits;

		if (strpos($ip, ':') !== false) {
			$ipAsBinary = inet_pton($ip);
			$ipAsHex = bin2hex($ipAsBinary);

			$ip = inet_ntop($ipAsBinary);
			$flexbits = 128 - $bits;
			$addr_hex_first = $ipAsHex;
			$pos = 31;

			while ($flexbits > 0) {
				$orig_first = substr($addr_hex_first, $pos, 1);
				$origval_first = hexdec($orig_first);
				$mask = 0xf << (min(4, $flexbits));
				$new_val_first = $origval_first & $mask;
				$new_first = dechex($new_val_first);
				$addr_hex_first = substr_replace($addr_hex_first, $new_first, $pos, 1);
				$flexbits -= 4;
				$pos -= 1;
			}

			$outputString = implode(':', str_split($addr_hex_first, 4));
			return $outputString . '/' . $origBits;
		}

		return $ip;
	}

}
