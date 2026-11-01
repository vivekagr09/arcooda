<?php

namespace Studio\Util;

use Exception;
use Studio\Util\Http\WebRequest;
use Studio\Util\Http\WebRequestException;

/**
 * This class manages captchas and rate limits for users.
 *
 * Each request is recorded in the database under a key, such as `tools` or `login`. If the number of requests reaches
 * a configured rate limit, a captcha "hit" is recorded, after which the user must complete a captcha to send another
 * request with the same key, or must wait until the hit expires.
 *
 * When a captcha is completed successfully, the hit and all matching requests are deleted, and rate limiting is
 * effectively reset.
 */
class Ratelimit {

	private static $permanentHits = [];
	private static $cachedHits = [];
	private static $cachedIntervals = [];
	private static $inserts = [];

	/**
	 * Returns `true` if the current user is rate limited for the given key.
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function checkRateLimited($key) {
		global $studio;

		if (!static::getEnabled()) {
			return false;
		}

		// Check for an existing captcha hit
		if (!array_key_exists($key, static::$cachedHits)) {
			if ($p = $studio->sql->prepare("SELECT 1 FROM captcha_hits WHERE `remote_address` = ? AND `name` = ? AND `timestamp` >= ? LIMIT 1")) {
				$params = new BindParam();
				$params->add('s', static::ip());
				$params->add('s', $key);
				$params->add('i', time());
				$params->apply($p);

				$p->execute();
				$p->store_result();

				$rows = $p->num_rows;
				$p->close();

				// If there's a match, we're rate limited
				if ($rows > 0) {
					static::$cachedHits[$key] = true;
					return true;
				}

				static::$cachedHits[$key] = false;
			}
		}
		else if (static::$cachedHits[$key]) {
			return true;
		}

		// Check for rate limits in individual intervals
		$checked = 0;
		foreach (static::intervals() as $interval) {
			$limit = static::getRateLimit($key, $interval);

			if ($limit > 0) {
				$count = static::getRequestCount($key, $interval);
				$checked++;

				// If there's a hit, record it and return true
				if ($count >= $limit) {
					static::recordHit($key);
					return true;
				}
			}
		}

		// When all intervals are set to 0, we should always captcha
		if ($checked === 0) {
			static::$permanentHits[$key] = true;
			return true;
		}

		return false;
	}

	/**
	 * Returns the number of requests received from the user for the given key and within the given interval.
	 *
	 * @param string $key
	 * @param string $interval
	 * @return int
	 */
	protected static function getRequestCount($key, $interval) {
		global $studio;

		$cacheKey = strtolower("$key:$interval");

		if (array_key_exists($cacheKey, static::$cachedIntervals)) {
			return static::$cachedIntervals[$cacheKey];
		}

		if ($p = $studio->sql->prepare("SELECT 1 FROM captcha_requests WHERE `remote_address` = ? AND `name` = ? AND `timestamp` >= ?")) {
			$params = new BindParam();
			$params->add('s', static::ip());
			$params->add('s', $key);
			$params->add('i', time() - static::getInterval($interval));
			$params->apply($p);

			$p->execute();
			$p->store_result();

			$rows = $p->num_rows;
			return static::$cachedIntervals[$cacheKey] = (int) $rows;
		}

		return static::$cachedIntervals[$cacheKey] = 0;
 	}

	/**
	 * Records a request from the current user for the given key.
	 *
	 * @param mixed $key
	 * @return void
	 */
	public static function recordRequest($key) {
		global $studio;

		if (!static::getEnabled()) {
			return;
		}

		if ($p = $studio->sql->prepare('INSERT INTO captcha_requests (`remote_address`, `name`, `timestamp`) VALUES (?, ?, ?)')) {
			$ip = static::ip();
			$timestamp = time();

			if (!array_key_exists($key, static::$inserts)) {
				static::$inserts[$key] = [];
			}

			$p->bind_param('ssi', $ip, $key, $timestamp);
			$p->execute();

			static::$inserts[$key][] = $studio->sql->insert_id;

			$p->close();

			foreach (static::$cachedIntervals as $cacheKey => $value) {
				if (substr($cacheKey, 0, strlen($key) + 1) === $key . ':') {
					static::$cachedIntervals[$cacheKey] = $value + 1;
				}
			}
		}
	}

	/**
	 * Checks for rate limits and returns `false` if currently limited. Otherwise, records the request for the current
	 * user and given key, and then returns `true`.
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function guardRequest($key) {
		if (static::checkRateLimited($key)) {
			return false;
		}

		static::recordRequest($key);
		return true;
	}

	/**
	 * Refunds any requests recorded for the given key during this pageload.
	 *
	 * @param string $key
	 * @return void
	 */
	public static function refundRequest($key) {
		global $studio;

		try {
			if (array_key_exists($key, static::$inserts) && count(static::$inserts[$key]) > 0) {
				$ids = implode(' OR id = ', static::$inserts[$key]);
				$studio->sql->query('DELETE FROM captcha_requests WHERE id = ' . $ids);
				unset(static::$inserts[$key]);

				foreach (static::$cachedIntervals as $cacheKey => $value) {
					if (substr($cacheKey, 0, strlen($key) + 1) === $key . ':') {
						static::$cachedIntervals[$cacheKey] = $value - 1;
					}
				}
			}
		}
		catch (Exception $ex) {}
	}

	/**
	 * Clears all rate limit state for the current user.
	 *
	 * @return void
	 */
	public static function clearRateLimit() {
		global $studio;

		$ip = static::ip();

		if ($p = $studio->sql->prepare('DELETE FROM `captcha_requests` WHERE `remote_address` = ?')) {
			$p->bind_param('s', $ip);
			$p->execute();
			$p->close();
		}

		if ($p = $studio->sql->prepare('DELETE FROM `captcha_hits` WHERE `remote_address` = ?')) {
			$p->bind_param('s', $ip);
			$p->execute();
			$p->close();
		}

		static::$cachedHits = [];
		static::$cachedIntervals = [];
		static::$inserts = [];
	}

	/**
	 * Records a captcha hit from the current user for the given key.
	 *
	 * @param mixed $key
	 * @return void
	 */
	protected static function recordHit($key) {
		global $studio;

		if (array_key_exists($key, static::$cachedHits) && static::$cachedHits[$key]) {
			return;
		}

		if ($p = $studio->sql->prepare('INSERT INTO captcha_hits (`remote_address`, `name`, `timestamp`) VALUES (?, ?, ?)')) {
			$ip = static::ip();
			$timestamp = time() + (static::getDuration($key) * 60);

			$p->bind_param('ssi', $ip, $key, $timestamp);
			$p->execute();

			$p->close();
			static::$cachedHits[$key] = true;
		}
	}

	/**
	 * Gets the configured rate limit for a key and interval.
	 *
	 * @param string $key
	 * @param string $interval `1m`, `5m`, `15m`, `30m`, and `1h`.
	 * @return int
	 */
	public static function getRateLimit($key, $interval) {
		global $studio;

		$defaults = [
			'login' => ['1m' => 0, '5m' => 0, '15m' => 0, '30m' => 0, '1h' => 30],
			'register' => ['1m' => 0, '5m' => 0, '15m' => 0, '30m' => 0, '1h' => 5],
			'tools' => ['1m' => 15, '5m' => 40, '15m' => 75, '30m' => 100, '1h' => 125],
			'search' => ['1m' => 10, '5m' => 30, '15m' => 60, '30m' => 70, '1h' => 80],
			'keywords' => ['1m' => 10, '5m' => 30, '15m' => 60, '30m' => 70, '1h' => 80],
			'contact' => ['1m' => 0, '5m' => 0, '15m' => 0, '30m' => 0, '1h' => 2]
		];

		$map = json_decode($studio->getopt(sprintf('ratelimits-%s', $key), '{}'), true);

		if (array_key_exists($interval, $map)) {
			return $map[$interval];
		}

		return $defaults[$key][$interval];
	}

	/**
	 * Sets the configured rate limit for a key and interval.
	 *
	 * @param string $key
	 * @param string $interval `1m`, `5m`, `15m`, `30m`, and `1h`.
	 * @param int $limit
	 * @return void
	 */
	public static function setRateLimit($key, $interval, $limit) {
		global $studio;

		$map = json_decode($studio->getopt(sprintf('ratelimits-%s', $key), '{}'), true);
		$map[$interval] = $limit;

		$studio->setopt(sprintf('ratelimits-%s', $key), json_encode($map));
	}

	/**
	 * Returns the IP address of the user.
	 *
	 * @return string
	 */
	protected static function ip() {
		$ip = $_SERVER['REMOTE_ADDR'];

		return static::getNormalizedRange($ip, 64);
	}

	/**
	 * Returns the supported intervals.
	 *
	 * @return string[]
	 */
	public static function intervals() {
		return ['1m', '5m', '15m', '30m', '1h'];
	}

	/**
	 * Returns the supported keys.
	 *
	 * @return array[]
	 */
	public static function keys() {
		return [
			['key' => 'login', 'description' => 'Limits login & password reset'],
			['key' => 'register', 'description' => 'Limits new account registration'],
			['key' => 'tools', 'description' => 'Limits usage of all tools'],
			['key' => 'search', 'description' => 'Limits search network requests, this will be applied alongside the tools rate limit'],
			['key' => 'keywords', 'description' => 'Limits keyword research requests, this will be applied alongside the tools rate limit'],
			['key' => 'contact', 'description' => 'Limits contact form submissions'],
		];
	}

	/**
	 * Returns the number of seconds in the given interval string.
	 *
	 * @param string $interval
	 * @return int
	 */
	protected static function getInterval($interval) {
		switch ($interval) {
			case '1m': return 60;
			case '5m': return 300;
			case '15m': return 900;
			case '30m': return 1800;
			case '1h': return 3600;
		}

		throw new Exception('Unknown interval "' . $interval . '"');
	}

	/**
	 * Returns the number of minutes to wait for a rate limit hit to expire on the given key.
	 *
	 * @param string $key
	 * @return int
	 */
	public static function getDuration($key) {
		global $studio;

		$default = 120;

		if ($key === 'login' || $key === 'register') {
			$default = 1440;
		}

		if ($key === 'contact') {
			$default = 1440;
		}

		$map = json_decode($studio->getopt('ratelimit-durations', '{}'), true);

		if (array_key_exists($key, $map)) {
			return $map[$key];
		}

		return $default;
	}

	/**
	 * Sets the number of minutes to wait for a rate limit hit to expire on the given key.
	 *
	 * @param string $key
	 * @param int $minutes
	 * @return void
	 */
	public static function setDuration($key, $minutes) {
		global $studio;

		$map = json_decode($studio->getopt('ratelimit-durations', '{}'), true);
		$map[$key] = $minutes;

		return $studio->setopt('ratelimit-durations', json_encode($map));
	}

	/**
	 * Returns `true` if rate limiting is enabled.
	 *
	 * @return bool
	 */
	public static function getEnabled() {
		global $studio;
		return $studio->getopt('ratelimit-enabled') === 'On';
	}

	/**
	 * Returns `true` if captchas are enabled.
	 *
	 * @return bool
	 */
	public static function getCaptchaEnabled() {
		global $studio;
		return static::getEnabled() && $studio->getopt('ratelimit-action', '') !== '';
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

	public static function getCaptchaDetails() {
		global $studio;

		if (static::getCaptchaEnabled()) {
			$provider = $studio->getopt('ratelimit-action');
			$siteKey = $studio->getopt('ratelimit-sitekey', '');

			if ($provider && $siteKey) {
				return array(
					'provider' => $provider,
					'sitekey' => $siteKey
				);
			}
		}

		return false;
	}

	public static function getCaptchaHeaderCode() {
		if ($captcha = static::getCaptchaDetails()) {
			switch ($captcha['provider']) {
				case 'hcaptcha': return '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
				case 'recaptcha': return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
				case 'turnstile': return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
			}
		}

		return '';
	}

	protected static function getCaptchaUrl() {
		if ($captcha = static::getCaptchaDetails()) {
			switch ($captcha['provider']) {
				case 'hcaptcha': return 'https://hcaptcha.com';
				case 'recaptcha': return 'https://www.google.com/recaptcha/api';
				case 'turnstile': return 'https://challenges.cloudflare.com/turnstile/v0';
			}
		}

		throw new Exception('Invalid captcha service');
	}

	protected static function getCaptchaFormKey() {
		if ($captcha = static::getCaptchaDetails()) {
			switch ($captcha['provider']) {
				case 'hcaptcha': return 'h-captcha-response';
				case 'recaptcha': return 'g-recaptcha-response';
				case 'turnstile': return 'cf-turnstile-response';
			}
		}

		return '__none__';
	}

	/**
	 * Returns the submitted captcha token, or null if none was submitted.
	 *
	 * @return string|null
	 */
	public static function getCaptchaSubmittedToken() {
		$key = static::getCaptchaFormKey();

		if ($key !== '__none__') {
			return isset($_POST[$key]) ? $_POST[$key] : null;
		}

		return null;
	}

	public static function getCaptchaKeys() {
		return ['h-captcha-response', 'g-recaptcha-response', 'cf-turnstile-response'];
	}

	public static function getSiteKey() {
		global $studio;
		return $studio->getopt('ratelimit-sitekey', '');
	}

	/**
	 * Checks the given captcha response input for validity, clearing all ratelimit state for the current user if
	 * successful. Returns `true` for success or `false` for failure.
	 *
	 * @param string $token
	 * @return bool
	 * @throws Exception
	 * @throws WebRequestException
	 */
	public static function checkCaptchaInput($token) {
		global $studio;

		$web = new WebRequest(static::getCaptchaUrl() . '/siteverify');
		$web->setUserAgent('Mozilla/5.0 (compatible; WebRequest/1.0)');
		$response = $web->post([
			'secret' => $studio->getopt('ratelimit-secretkey', ''),
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
			'sitekey' => static::getSiteKey()
		]);

		$body = $response->getJson();

		if ($response->getStatusCode() !== 200) {
			$errors = implode('; ', $body['error-codes']);

			if (in_array('invalid-input-secret', $body['error-codes'])) {
				throw new Exception('Got errors from captcha service: ' . $errors);
			}

			return false;
		}

		if ($body['success']) {
			static::clearRateLimit();
			return true;
		}

		return false;
	}

	/**
	 * Returns the highest duration in minutes.
	 *
	 * @return int
	 */
	protected static function getHighestDuration() {
		$highest = 0;

		foreach (static::keys() as $row) {
			$duration = static::getDuration($row['key']);

			if ($duration > $highest) {
				$highest = $duration;
			}
		}

		return $highest;
	}

	/**
	 * Cleans up in cron context.
	 *
	 * @return void
	 */
	public static function cron() {
		global $studio;

		$requests = time() - 3600;
		$hits = time() - (static::getHighestDuration() * 60);

		try {
			$studio->sql->query("DELETE FROM `captcha_requests` WHERE `timestamp` <= $requests;");
			$studio->sql->query("DELETE FROM `captcha_hits` WHERE `timestamp` <= $hits;");
		}
		catch (Exception $ex) {
			// Silence errors because this will often run in fragile contexts
		}
	}

}
