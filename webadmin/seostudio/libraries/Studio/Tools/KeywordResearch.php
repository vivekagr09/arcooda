<?php

namespace Studio\Tools;

use Exception;
use SEO\Common\SEOException;
use SEO\Services\Currencies;
use SEO\Services\Keywords;
use SEO\Services\KeywordsEverywhere;
use Studio\Util\Ratelimit;

class KeywordResearch extends Tool
{
    var $name = "Keyword Research";
    var $id = "keyword-research";
    var $icon = "keyword-research";
    var $template = "keyword-research.html";
    var $requiresWebsite = false;
    var $usesKeywords = true;
    var $usesSearchNetwork = true;

    var $keyword;
    protected $chosenCountry = null;

    public function prerun($url) {
        $this->keyword = "";

        if (isset($_POST['keyword']) && isset($_POST['country'])) {
            $this->keyword = $_POST['keyword'];
            $this->chosenCountry = trim(strtoupper($_POST['country']));
        }
    }

    public function run() {
        global $studio, $language, $api;

        $countryCode = $this->getSelectedCountry();

        if ($this->keyword != "") {
            // Update currency exchange values
            if ($countryCode !== 'US') {
                Currencies::update();
            }

            $locale = $language ? $language->locale : 'en-us';
            $localeParts = explode('-', strtolower($locale), 2);

            // Fall back for invalid locales
            if (count($localeParts) !== 2) $localeParts = array('en', 'us');

            // Form the query parameters
            $query = mb_strtolower(trim($this->keyword));
            $lang = $localeParts[0];
            $country = $countryCode;

            // Perform the request
            $keywords = Keywords::getKeywordSuggestions($query, $lang, $country);
            $items = array();
            $kwData = array();
            $kwDataKeys = array();
            $kwWords = array();

            // Get currency code
            $currency = Currencies::getCurrencyFromCountryCode($countryCode);
            $symbol = Currencies::getSymbol($currency);

            // Keywords Everywhere
            $kweEnabled = KeywordsEverywhere::isEnabled();
            $kweBalance = $kweEnabled ? $studio->getopt('kwe-balance', 0) : 0;
            $kweLimit = $kweEnabled ? $studio->getopt('kwe-max-keywords', 100) : 0;
            $kweActive = false;
            $kweTrendMonths = array();

            // Get keyword data
            try {
                Ratelimit::recordRequest('keywords');
                $returned = Keywords::getKeywordData($keywords, $countryCode);

                foreach($returned as $keyword => $data) {
                    if (array_key_exists(mb_strtolower($keyword), $kwDataKeys)) continue;

                    // Add the primary keyword
                    $kwWords[] = $keyword = mb_strtolower($keyword);
                    $kwDataKeys[$keyword] = array(
                        'text' => $keyword,
                        'cpc_raw' => floatval($data['cpc']),
                        'cpc' => number_format(floatval($data['cpc']), 2),
                        'vol' => floatval($data['search_volume']),
                        'v' => $data['search_volume'],
                        'competition' => 0
                    );
                    $kwData[] = &$kwDataKeys[$keyword];

                    // Add similar keywords
                    foreach ($data['similar_keywords'] as $similar) {
                        if (array_key_exists(mb_strtolower($similar['keyword']), $kwDataKeys)) continue;

                        $kwWords[] = $keyword = mb_strtolower($similar['keyword']);
                        $kwDataKeys[$keyword] = array(
                            'text' => mb_strtolower($keyword),
                            'cpc_raw' => floatval($similar['cpc']),
                            'cpc' => number_format(floatval($similar['cpc']), 2),
                            'vol' => floatval($similar['search_volume']),
                            'v' => $similar['search_volume'],
                            'competition' => 0
                        );
                        $kwData[] = &$kwDataKeys[$keyword];
                    }
                }
            }
            catch (Exception $e) {
                throw new SEOException('Failed to fetch keyword data');
            }

            // Fetch keywords with KWE
            $adjCountryCode = $countryCode === 'GB' ? 'UK' : $countryCode;
            if ($kweEnabled && KeywordsEverywhere::supportsCountry($adjCountryCode)) {
                $slice = array_slice($kwData, 0, $kweLimit, true);
                $words = array_slice($kwWords, 0, $kweLimit, true);

                if ($kweBalance >= count($slice)) {
                    KeywordsEverywhere::subtractBalance(count($slice));

                    try {
                        $data = KeywordsEverywhere::getKeywordData($words, strtolower($adjCountryCode), strtolower($currency));

                        foreach ($data as $row) {
                            $originalRow = &$kwDataKeys[($row['keyword'])];
                            $originalRow['cpc_local'] = $row['cpc']['value'];
                            $originalRow['vol'] = $originalRow['v'] = $row['vol'];
                            $originalRow['competition'] = $row['competition'];

                            $cpcLocal = floatval($row['cpc']['value']);
                            $originalRow['cpc_raw'] = Currencies::getValueUSD($currency, $cpcLocal);
                            $originalRow['cpc'] = number_format($originalRow['cpc_raw'], 2);

                            if (isset($row['trend']) && !empty($row['trend'])) {
                                if (count($kweTrendMonths) === 0) {
                                    $months = [
                                        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                                        'September', 'October', 'November', 'December'
                                    ];

                                    foreach ($row['trend'] as $item) {
                                        $monthNumber = array_search($item['month'], $months) + 1;
                                        $yearNumber = $item['year'];

                                        $kweTrendMonths[] = "$monthNumber/$yearNumber";
                                    }
                                }

                                $trend = [];
                                foreach ($row['trend'] as $item) {
                                    $trend[] = $item['value'];
                                }

                                $originalRow['trend'] = $trend;
                            }
                        }

                        $kweActive = true;
                        $kwData = $slice;
                        $kwWords = $words;
                    }
                    catch (Exception $ex) {
                        KeywordsEverywhere::addBalance(count($slice));
                    }
                }
            }

            // Then, prepare the rows to render
            foreach ($kwData as $value) {
                $keyword = $value['text'];
                $item = $value;

                if (!$kweActive && !is_null($currency) && Currencies::getValue($currency) !== null) {
                    $local = Currencies::getValue($currency, $value['cpc_raw']) ?: 0;
                    $item['cpc_local'] = number_format(floatval($local), 2);
                }

                $items[] = $item;
            }

            // Sort rows by volume
            usort($items, function($a, $b) use ($query) {
                // Always show the original query on top
                if ($a['text'] === $query) return -1;
                if ($b['text'] === $query) return 1;

                // Sort the rest by volume, descending
                if ($a['v'] == $b['v']) return 0;
                return $a['v'] < $b['v'] ? 1 : -1;
            });

            $this->data = [
                'results' => $items,
                'currency_code' => $currency,
                'currency_value' => Currencies::getValue($currency),
                'currency_symbol' => $symbol,
                'trends_available' => count($kweTrendMonths) > 0,
                'trends_months' => $kweTrendMonths
            ];
        }
    }

    public function output() {
        echo $this->renderTemplate([
            'keyword' => $this->keyword,
            'countries' => $this->getCountries(),
            'activeCountry' => $this->getSelectedCountry()
        ]);
    }

    private function getCountries() {
        global $language;

        $countries = $language->getCountries();
        $supported = Keywords::getSurferCountries();
        $results = [];

        foreach ($countries as $code => $name) {
            if (is_array($name)) $name = $name[0];
            if (!in_array($code, $supported)) continue;

            $results[$code] = $name;
        }

        asort($results);
        return $results;
    }

    public function record($data = "") {
        if ($this->keyword != "") parent::record("Keyword: " . $this->keyword);
    }

    protected function getCacheKey() {
        return $this->id . ":" . $this->keyword . ":" . $this->getSelectedCountry();
    }

    /**
     * Returns the country to select in the dropdown.
     */
    private function getSelectedCountry() {
        global $studio;

        if (!is_null($this->chosenCountry)) {
            return $this->chosenCountry;
        }

        if (isset($_SESSION['kwr_country'])) {
            return $_SESSION['kwr_country'];
        }

        return $studio->getopt('tools-default-country', 'US');
    }

}
