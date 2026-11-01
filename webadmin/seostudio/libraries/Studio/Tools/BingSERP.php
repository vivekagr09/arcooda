<?php

namespace Studio\Tools;

use Exception;
use SEO\Helper\Url;
use SEO\Parsers\GoogleSearchResult;
use SEO\Services\LinkAssistant;

class BingSERP extends Tool
{
    var $name = "Bing SERP";
    var $id = "bing-serp";
    var $template = "serp.html";
    var $icon = "bing-serp";
    var $usesSearchNetwork = true;

    var $keyword;
    protected $chosenCountry = null;

    public function prerun($url) {
        $this->keyword = "";

        if (isset($_POST['keyword'])) {
            $this->keyword = trim(mb_strtolower($_POST['keyword']));
            $this->keyword = preg_replace('/\s+/', ' ', trim($this->keyword));
        }

        if (isset($_POST['country'])) $this->chosenCountry = trim(mb_strtoupper($_POST['country']));
    }

    public function run() {
        global $studio;

        if (!file_exists($countriesFile = $studio->bindir . '/geotargets/countries.json')) {
            throw new Exception('Missing geotargets');
        }

        if ($this->keyword !== "") {
            $countryCode = $this->getSelectedCountry();
            $countryData = json_decode(file_get_contents($countriesFile), true);
            $latitude = null;
            $longitude = null;

            if (is_string($countryCode) && array_key_exists($countryCode, $countryData)) {
                $data = $countryData[$countryCode];
                $latitude = $data['lat'];
                $longitude = $data['lng'];
            }

            $lookingFor = str_replace('www.', '', $this->url->getHostname());
            $lookingForExp = '/^https?:\/\/(?:[a-z0-9_-]+\.)*' . preg_quote($lookingFor, '/') . '[^a-z.]/im';

            $targetRank = null;
            $currentIndex = 0;
            $results = [];

            /**
             * This variable adjusts the number of pages to fetch from search results.
             *
             * PLEASE READ CAREFULLY: If you are using the free included Bing proxying service, changing this can
             * quickly deplete your rate limit and drastically increase the time this tool takes to load.
             *
             * This will be increased to 2-3 in a future update once the service is capable of delivering multiple
             * pages quickly. Right now, it's simply too slow.
             */
            $maxPages = 2;

            for ($page = 1; $page <= $maxPages; $page++) {
                $bing = new \Studio\Ports\Bing($this->url, $countryCode, $latitude, $longitude);
                $parser = $bing->query($this->keyword, $currentIndex, 50);

                @$studio->sql->ping();

                foreach ($parser->getResults() as $result) {
                    $index = ++$currentIndex;
                    $hostname = $this->getHostName($result);
                    $arr = $result->detach();

                    if ($hostname === null) {
                        continue;
                    }

                    $correctedHref = $this->getHref($result);
                    $isMatch = preg_match($lookingForExp, $correctedHref);

                    if ($targetRank === null && $isMatch) {
                        $targetRank = $index;
                    }

                    try {
                        $arr['rank'] = $index;
                        $arr['hostname'] = $hostname;
                        $arr['is_match'] = $isMatch;
                        $arr['href_corrected'] = $correctedHref;

                        $results[] = $arr;
                    }
                    catch (\Exception $ex) {
                        continue;
                    }
                }

                if ($targetRank !== null && $currentIndex >= 20) {
                    break;
                }

                if ($parser->getNumTotalResults() < $parser->getNumResults() || $parser->getNumTotalResults() === 0) {
                    break;
                }
            }

            $chunks = array_chunk($results, 100, true);
            $links = new LinkAssistant();

            foreach ($chunks as $chunk) {
                $urls = [];
                $indices = [];

                @$studio->sql->ping();

                foreach ($chunk as $index => $result) {
                    $urls[] = $result['href'];
                    $indices[] = $index;
                }

                foreach ($links->getAuthorityScores($urls) as $index => $scores) {
                    $index = $indices[$index];
                    $results[$index]['inlink_rank'] = $scores->inlink_rank;
                    $results[$index]['domain_inlink_rank'] = $scores->domain_inlink_rank;
                }
            }

            $this->data = [
                'results' => $results,
                'rank' => $targetRank,
                'numRows' => count($results)
            ];
        }
    }

    /**
     * Gets the hostname of the result.
     *
     * @param GoogleSearchResult $result
     * @return string|null
     */
    protected function getHostName($result) {
        try {
            if (substr($result->href, 0, 24) === 'https://www.bing.com/ck/') {
                if (substr($result->citation, 0, 4) === 'http') {
                    $slashIndex = strpos($result->citation, '/', 8);

                    if ($slashIndex !== false) {
                        $url = substr($result->citation, 0, $slashIndex + 1);
                        return (new Url($url))->getHostname();
                    }

                    return (new Url($result->citation))->getHostname();
                }

                return;
            }

            return (new Url($result->href))->getHostname();
        }
        catch (Exception $ex) {
            return;
        }
    }

    /**
     * Gets the best possible href of the result.
     *
     * @param GoogleSearchResult $result
     * @return string|null
     */
    protected function getHref($result) {
        if (substr($result->href, 0, 24) === 'https://www.bing.com/ck/') {
            if (substr($result->citation, 0, 4) === 'http') {
                $href = $result->citation;

                // If the href ends with '...', return everything before it
                if (substr($href, -3) === '...') {
                    return substr($href, 0, -3);
                }

                // Otherwise, try to parse the result
                try {
                    return (new Url($href))->getAbsoluteURL();
                }
                catch (Exception $ex) {
                    $httpColonIndex = strpos($href, '://');
                    $httpScheme = substr($href, 0, $httpColonIndex + 3);

                    return $httpScheme . $this->getHostName($result);
                }

                return (new Url($result->citation))->getHostname();
            }
        }

        return $result->href;
    }

    public function output() {
        global $studio;

        if ($this->data) {
            $rank = $this->data['rank'];
            $count = $this->data['numRows'];

            if ($rank === null) {
                $rank = rt('{$1} did not appear in the first {$2} results.', $this->url->domain, $count);
            }
            else {
                $rank = rt('{$1} was #{$2} in the search results.', $this->url->domain, $rank);
            }
        }
        else {
            $rank = rt("Enter a keyword to find your rank.");
        }

        echo $this->renderTemplate([
            'title' => rt("Bing Results"),
            'keyword' => $this->keyword,
            'rank' => $rank,
            'countries' => $this->getCountries(),
            'activeCountry' => $this->getSelectedCountry(),
            'favicons' => $studio->getopt('disable-result-favicons') !== 'On'
        ]);

        return;
    }

    public function record($data = "") {
        if ($this->keyword != "") parent::record("Keyword: " . $this->keyword);
    }

    protected function getCacheKey() {
        return $this->id . ":" . $this->keyword . ":v3@" . $this->getSelectedCountry();
    }

    private function getCountries() {
        global $language;

        $countries = $language->getCountries();
        $unsupported = [];
        $results = [];

        foreach ($countries as $code => $name) {
            if (is_array($name)) $name = $name[0];
            if (in_array(strtolower($code), $unsupported)) continue;

            $results[$code] = $name;
        }

        asort($results);
        return $results;
    }

    /**
     * Returns the country to select in the dropdown.
     */
    private function getSelectedCountry() {
        global $studio;

        if (!is_null($this->chosenCountry)) {
            if ($this->chosenCountry === '') {
                return null;
            }

            return $this->chosenCountry;
        }

        if (isset($_SESSION['kwr_country'])) {
            return $_SESSION['kwr_country'];
        }

        return $studio->getopt('tools-default-country', 'US');
    }

}
