<?php

namespace Studio\Tools;

use Exception;
use SEO\Helper\Url;
use SEO\Services\LinkAssistant;

class Competition extends Tool
{
    var $name = "Competition";
    var $id = "competition";
    var $icon = "competition";
    var $template = "competition.html";
    var $usesSearchNetwork = true;

    var $keyword = "";
    var $you;

    protected $chosenCountry = null;

    public function prerun($url) {
        if (isset($_POST['keyword'])) $this->keyword = trim(mb_strtolower($_POST['keyword']));
        if (isset($_POST['country'])) $this->chosenCountry = trim(mb_strtoupper($_POST['country']));
    }

    public function run() {
        global $studio;

        if (!file_exists($countriesFile = $studio->bindir . '/geotargets/countries.json')) {
            throw new Exception('Missing geotargets');
        }

        @ini_set('max_execution_time', 120);

        if ($this->keyword != "") {
            $countryCode = $this->getSelectedCountry();
            $countryData = json_decode(file_get_contents($countriesFile), true);
            $latitude = null;
            $longitude = null;

            if (is_string($countryCode) && array_key_exists($countryCode, $countryData)) {
                $data = $countryData[$countryCode];
                $latitude = $data['lat'];
                $longitude = $data['lng'];
            }

            $links = new LinkAssistant();
            $google = new \Studio\Ports\Google($this->url, $countryCode);
            $query = $google->query($_POST['keyword'], 1, 100);

            $results = array();
            $resultLinks = array();
            $targetRank = -1;

            foreach ($query->getResults() as $result) {
                $url = @parse_url($result->href);
                if (!is_array($url) || !isset($url['host'])) continue;
                $host = mb_strtolower($url['host']);
                $stats = array();
                $isTarget = false;

                if ($host === mb_strtolower($this->url->getHostname())) {
                    $isTarget = true;
                }

                else if (str_replace('www.', '', $host) === str_replace('www.', '', mb_strtolower($this->url->getHostname()))) {
                    $isTarget = true;
                }

                if ($targetRank < 0 && $isTarget) {
                    $targetRank = count($results) + 1;
                }

                try {
                    $resultLinks[] = (new Url($result->href))->getAbsoluteURL();
                    $results[] = [
                        'rank' => count($results) + 1,
                        'title' => $result->title,
                        'host' => $host,
                        'url' => $result->href,
                        'is_target' => $isTarget
                    ];
                }
                catch (\Exception $ex) {}
            }

            // If the target wasn't found, show only the first 20 results
            if ($targetRank < 0) {
                $resultLinks = array_slice($resultLinks, 0, 20);
                $results = array_slice($results, 0, 20);
            }

            // Otherwise, show the target with the 15 links before
            else {
                $results = array_slice($results, max($targetRank - 6, 0), 11);
                $resultLinks = array_slice($resultLinks, max($targetRank - 6, 0), 11);
            }

            // Fix loss of database connection when the above code is slow
            @$studio->sql->ping();

            // Look up website statistics
            $resultLinks[] = $this->url->getAbsoluteURL();
            $stats = $links->getMetrics($resultLinks);
            $statsForTarget = array_pop($stats);

            if (!$statsForTarget) {
                $this->data = [];
                return;
            }

            foreach ($results as $i => &$result) {
                $result['metrics'] = $stats[$i];
            }

            $targetResult = [
                'rank' => $targetRank,
                'host' => $this->url->getHostname(),
                'url' => $this->url->getAbsoluteURL(),
                'metrics' => $statsForTarget
            ];

            // Look up authorities
            $scores = $links->getAuthorityScores($resultLinks);
            $scoresForTarget = array_pop($scores);

            foreach ($results as $i => &$result) {
                $result['inlink_rank'] = $scores[$i]->inlink_rank;
                $result['domain_inlink_rank'] = $scores[$i]->domain_inlink_rank;
            }

            $targetResult['inlink_rank'] = $scoresForTarget->inlink_rank;
            $targetResult['domain_inlink_rank'] = $scoresForTarget->domain_inlink_rank;

            $this->data = [
                'results' => $results,
                'target' => $targetResult,
                'showTarget' => $targetRank < 0
            ];
        }
    }

    public function output() {
        global $studio;

        echo $this->renderTemplate([
            'keyword' => $this->keyword,
            'countries' => $this->getCountries(),
            'activeCountry' => $this->getSelectedCountry(),
            'favicons' => $studio->getopt('disable-result-favicons') !== 'On'
        ]);
    }

    public function record($data = "") {
        if ($this->keyword != "") parent::record("Keyword: " . $this->keyword);
    }

    protected function getCacheKey() {
        return $this->id . ":" . $this->keyword . ":v2@" . $this->getSelectedCountry();
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
