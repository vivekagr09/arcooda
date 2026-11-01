<?php

namespace SEO\Services;

use Exception;
use \SEO\Common as Common;
use SEO\Parsers\BingSearchParser;
use Studio\Util\Http\WebRequest;

/**
 * Bing Scraper
 * Developed by Bailey Herbert
 * https://baileyherbert.com/
 *
 * This class was created for SEO Studio and can be used in compliance with the license you purchased for that product.
 * View CodeCanyon license specifications here: http://codecanyon.net/licenses ("Standard")
 */

class Bing
{
    private $url;
    protected $countryCode;
    protected $latitude;
    protected $longitude;

    public function __construct($url, $countryCode = null, $latitude = null, $longitude = null) {
        $this->url = $url;
        $this->countryCode = $countryCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Performs a Bing query and returns an \SEO\Services\BingResult object
     * @param string $query The search query to perform
     * @param int    $start  The first row of results to retrieve (default 0)
     * @throws \SEO\Common\SEOException when blocked (code 1) or connect error (code 2)
     * @return BingSearchParser object containing results
     */
    public function query($query, $start = 0, $num = 10, $html = null) {
        global $studio;

        if ($html === null) {
            $query = urlencode($query);

            if ($start > 0) $start = "&first=$start";
            else $start = "";

            if ($this->countryCode) $cc = "&cc=" . $this->countryCode;
            else $cc = "";

            $web = new WebRequest("https://www.bing.com/search?q={$query}{$start}{$cc}");
            $web->setHeader('DNT', '1');
            $web->setReferer('https://www.bing.com/');
            $web->setOption(CURLOPT_FOLLOWLOCATION, false);
            $web->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36');
            $web->setHeader('accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7');
            $web->setHeader('Upgrade-Insecure-Requests', '1');
            $web->setHeader('TE', 'trailers');
            $web->setHeader('Sec-Fetch-Dest', 'document');
            $web->setHeader('Sec-Fetch-Mode', 'navigate');
            $web->setHeader('Sec-Fetch-Site', 'same-origin');
            $web->setHeader('Sec-Fetch-User', '?1');

            $params = [];

            if ($num !== 10) {
                $params['NRSLT'] = strval($num);
            }

            $web->setCookie('SRCHHPGUSR', http_build_query($params));

            $ipv4 = (int)$studio->getopt('bing-ipv4-enforced', '0');

            if ($ipv4 > 0 && defined('CURL_IPRESOLVE_V4')) {
                $web->setOption(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }

            $response = $web->get();
            $code = $response->getStatusCode();
            $data = $response->getBody();

            if ($code === 301 || $code === 302) {
                $location = $response->getHeader('location');
                $location = is_array($location) ? $location[0] : $location;

                if (!is_string($location)) {
                    throw new Exception('Illegal redirect');
                }

                if (stripos($location, 'cn.bing.com') !== false) {
                    if ($ipv4 === 0) {
                        $studio->setopt('bing-ipv4-enforced', '1');
                        return $this->query($query, $start, $num, $html);
                    }

                    throw new Common\SEOException("Bing rejected our query because origin country is China", 1);
                }
            }

            if ($code !== 200) {
                throw new Common\SEOException("Unexpected response from Bing ($code)", 1);
            }
        }
        else {
            $data = $html;
        }

        return new BingSearchParser($data);
    }

    /**
    * Gets the indexed pages for the website
    * @param int    $page  The results page to retrieve (default 1)
    * @throws \SEO\Common\SEOException when blocked (code 1) or connect error (code 2)
    * @return BingSearchParser object containing results
    */
    public function getIndexedPages($page = 0, $num = 10) {
        return $this->query("site:{$this->url->domain}", $page, $num);
    }
}
