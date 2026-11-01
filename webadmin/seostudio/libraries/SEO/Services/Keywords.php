<?php

namespace SEO\Services;

use Exception;
use Studio\Util\Http\WebRequest;

class Keywords {

	/**
	 * Returns an array of keyword suggestions for the given input.
	 *
	 * @param string $query
	 * @param string $language The two-character language code (e.g. `en`)
	 * @param string $country The two-character country code (e.g. `US`)
	 * @return string[]
	 */
	public static function getKeywordSuggestions($query, $language, $country) {
        global $studio, $api;

		$lastFailedAt = intval($studio->getopt('kwsuggest-last-failure', '0'));
		$forceRemote = $studio->getopt('google-force-remote', false);
		$googleEnabled = $studio->getopt('google-enabled') == 'On';

		if (($lastFailedAt < time() - 3600 && !$forceRemote) || !$googleEnabled) {
			$query = str_replace('-', ' ', $query);

			$req = new WebRequest(sprintf('https://suggestqueries.google.com/complete/search?client=chrome&hl=%s&gl=%s&callback=?&q=%s', $language, $country, urlencode($query)));
			$res = $req->get();

			try {
				$data = $res->getJson();

				$keywords = $data[1];
				array_unshift($keywords, $query);

				return $keywords;
			}
			catch (Exception $e) {
				$studio->setopt('kwsuggest-last-failure', strval(time()));
			}
		}

        if ($googleEnabled && isset($api)) {
			$data = $api->getGoogleSuggestions($query, $language, $country);

			if (is_array($data)) {
				$keywords = $data[1];
				array_unshift($keywords, $query);

				return $keywords;
			}
		}

		throw new Exception("Could not generate suggestions at this time");
	}

	/**
	 * Fetches and returns keyword data.
	 *
	 * @param string[] $keywords
	 * @param string $country The two-character country code (e.g. `US`)
	 * @return array
	 */
	public static function getKeywordData($keywords, $country) {
		$keywordsEncoded = json_encode($keywords);
		$req = new WebRequest(sprintf('https://db2.keywordsur.fr/keyword_surfer_keywords?country=%s&keywords=%s', $country, urlencode($keywordsEncoded)));
		$res = $req->get();
		return $res->getJson();
	}

	/**
	 * Returns an array of country codes that can retrieve surfer data.
	 *
	 * @return string[]
	 */
	public static function getSurferCountries() {
		return [
			'DZ', 'AO', 'AR', 'AM', 'AU', 'AT', 'BH', 'BD', 'BE',
			'BO', 'BR', 'BG', 'CA', 'CL', 'CO', 'CR', 'HR', 'CY',
			'CZ', 'DK', 'EC', 'EG', 'SV', 'EE', 'FI', 'FR', 'DE',
			'GR', 'GT', 'HK', 'HU', 'IN', 'ID', 'IE', 'IL', 'IT',
			'JP', 'JO', 'KE', 'LV', 'LT', 'MY', 'MT', 'MX', 'MA',
			'NL', 'NZ', 'NI', 'NG', 'NO', 'PK', 'PY', 'PE', 'PL',
			'PT', 'RO', 'RU', 'SA', 'RS', 'SG', 'SK', 'SI', 'ZA',
			'KR', 'ES', 'LK', 'SE', 'CH', 'TW', 'TH', 'TN', 'TR',
			'UA', 'AE', 'GB', 'US', 'UY', 'VE', 'VN'
		];
	}

	/**
	 * Returns an array of country codes that can retrieve fallback data.
	 *
	 * @return string[]
	 */
	public static function getFallbackCountries() {
		return ['AU', 'CA', 'IN', 'NZ', 'ZA', 'UK', 'GB', 'US'];
	}

	/**
	 * Fetches and returns fallback keyword data.
	 *
	 * @param string[] $keywords
	 * @param string $country The two-character country code (e.g. `US`)
	 * @return array
	 */
	public static function getFallbackData($keywords, $country) {
		return [];
	}

}
