<?php

namespace Studio\Tools;

use SEO\Services\SpyFu;

class TopSearchQueries extends Tool
{
    var $name = "Top Search Queries";
    var $id = "top-search-queries";
    var $icon = "top-search-queries";
    var $template = "search-queries.html";

    protected $chosenCountry = null;

    public function prerun($url) {
        if (isset($_POST['country'])) {
            $country = trim(strtolower($_POST['country']));

            if (in_array($country, SpyFu::getCountryCodes())) {
                $this->chosenCountry = $country;
            }
        }
    }

    public function run() {
        if ($this->chosenCountry) {
            $sf = new SpyFu();
            $keywords = [];

            foreach ($sf->getMostValuableKeywords($this->url->getHostname(), $this->chosenCountry) as $keyword) {
                $keywords[] = $keyword;
            }

            $this->data = [
                'keywords' => $keywords
            ];
        }
    }

    public function record($data = "") {
        if ($this->chosenCountry) parent::record("Country: " . strtoupper($this->chosenCountry));
    }

    public function output() {
        echo $this->renderTemplate([
            'hasData' => isset($this->data['keywords']),
            'countries' => $this->getCountries(),
            'activeCountry' => $this->getSelectedCountry()
        ]);
    }

    protected function getCacheKey() {
        return $this->id . ":" . $this->getSelectedCountry();
    }

    private function getCountries() {
        global $language;

        $countries = $language->getCountries();
        $supported = SpyFu::getCountryCodes();
        $results = [];

        foreach ($countries as $code => $name) {
            if ($code === 'GB') $code = 'UK';
            if (is_array($name)) $name = $name[0];
            if (!in_array(strtolower($code), $supported)) continue;

            $results[strtolower($code)] = $name;
        }

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

        if (isset($_SESSION['kwr_country']) && in_array($_SESSION['kwr_country'], SpyFu::getCountryCodes())) {
            return $_SESSION['kwr_country'];
        }

        $default = strtolower($studio->getopt('tools-default-country', 'US'));

        if (in_array($default, SpyFu::getCountryCodes())) {
            return $default;
        }

        return 'us';
    }

}
