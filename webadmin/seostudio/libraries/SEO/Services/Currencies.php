<?php

namespace SEO\Services;

use Exception;

class Currencies {

	private static $countries;
	private static $values;

	/**
	 * Returns the currency code that the given country uses, or `null` if not found.
	 *
	 * @param string $countryCode
	 * @return string|null
	 */
	public static function getCurrencyFromCountryCode($countryCode) {
		if (!is_null($countries = static::getCountries())) {
			$code = strtoupper(trim($countryCode));

			if (array_key_exists($code, $countries)) {
				return $countries[$code];
			}
		}
	}

	/**
	 * Converts the given USD value into the specified currency. Returns `null` if unavailable.
	 *
	 * @param string $currencyCode
	 * @param float $value
	 * @return float|null
	 */
	public static function getValue($currencyCode, $value = 1) {
		if (!is_null($values = static::getCurrencyValues())) {
			$code = strtoupper(trim($currencyCode));

			if (array_key_exists($code, $values)) {
				return $value * $values[$code];
			}
		}
	}

	/**
	 * Converts the given currency into USD. Returns `null` if unavailable.
	 *
	 * @param string $currencyCode
	 * @param float $value
	 * @return float|null
	 */
	public static function getValueUSD($currencyCode, $value) {
		if (!is_null($values = static::getCurrencyValues())) {
			$code = strtoupper(trim($currencyCode));

			if (array_key_exists($code, $values)) {
				return $value / $values[$code];
			}
		}

		return $value;
	}

	/**
	 * Gets the internal currency values array (cached).
	 *
	 * @return array|null
	 */
	public static function getCurrencyValues() {
		global $studio;

		if (is_null(static::$values)) {
			$values = $studio->getopt('currencies-values');
			$expired = $studio->getopt('currencies-values-expiration', 0) <= time();

			if (!is_null($values) && !$expired) {
				static::$values = json_decode($values, true);
			}
			else {
				static::$values = false;
			}
		}

		if (static::$values !== false) {
			return static::$values;
		}
	}

	/**
	 * Gets the internal countries array (cached).
	 *
	 * @return array|null
	 */
	public static function getCountries() {
		global $studio;

		if (is_null(static::$countries)) {
			$countries = $studio->getopt('currencies-countries');
			$expired = $studio->getopt('currencies-countries-expiration', 0) <= time();

			if (!is_null($countries) && !$expired) {
				static::$countries = json_decode($countries, true);
			}
			else {
				static::$countries = false;
			}
		}

		if (static::$countries !== false) {
			return static::$countries;
		}
	}

	/**
	 * Returns the symbol for the given currency code or `null` if not found.
	 *
	 * @param string $currencyCode
	 * @return string|null
	 */
	public static function getSymbol($currencyCode) {
		global $studio;

		if (file_exists($path = $studio->bindir . '/currencies.json')) {
			$content = json_decode(file_get_contents($path), true);
			$currencyCode = strtoupper(trim($currencyCode));

			foreach ($content as $row) {
				if ($row['Code'] === $currencyCode) {
					return $row['Symbol'];
				}
			}
		}
	}

	/**
	 * Updates currency data from the API if necessary.
	 *
	 * @return void
	 */
	public static function update() {
		global $api;

		if ($api->isAuthorized()) {
			if (static::updateCurrencyValues() !== false) {
				static::updateCountries();
			}
		}
	}

	/**
	 * Updates currency values.
	 *
	 * @return void
	 */
	private static function updateCurrencyValues() {
		global $studio, $api;

		if ($studio->getopt('currencies-values-timestamp', 0) <= time()) {
			$studio->setopt('currencies-values-timestamp', time() + 28800);

			try {
				$response = $api->getCurrencyValues();

				if (is_object($response) && isset($response->currencies)) {
					$studio->setopt('currencies-values', json_encode($response->currencies));
					$studio->setopt('currencies-values-timestamp', time() + 259200);
					$studio->setopt('currencies-values-expiration', time() + 7776000);
					$studio->setopt('currencies-values-ok', '1');

					static::$values = $response->currencies;
				}
			}
			catch (Exception $e) {
				$studio->setopt('currencies-values-ok', '0');
				return false;
			}
		}
	}

	/**
	 * Updates the map between countries and currencies.
	 *
	 * @return void
	 */
	private static function updateCountries() {
		global $studio, $api;

		if ($studio->getopt('currencies-countries-timestamp', 0) <= time()) {
			$studio->setopt('currencies-countries-timestamp', time() + 28800);

			try {
				$response = $api->getCurrencyCountries();

				if (is_object($response) && isset($response->countries)) {
					$studio->setopt('currencies-countries', json_encode($response->countries));
					$studio->setopt('currencies-countries-timestamp', time() + 604800);
					$studio->setopt('currencies-countries-expiration', time() + 7776000);
					$studio->setopt('currencies-countries-ok', '1');

					static::$countries = $response->countries;
				}
			}
			catch (Exception $e) {
				$studio->setopt('currencies-countries-ok', '0');
				return false;
			}
		}
	}

}
