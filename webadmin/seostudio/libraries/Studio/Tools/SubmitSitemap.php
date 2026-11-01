<?php

namespace Studio\Tools;

use Exception;
use SEO\Helper\Url;
use Studio\Util\Http\UserWebRequest;
use Studio\Util\Http\WebRequest;

class SubmitSitemap extends Tool
{
    var $name = "Submit Sitemaps";
    var $id = "submit-sitemap";
    var $icon = "submit-sitemaps";
    var $template = "submit-sitemap.html";

    public function run() {
        global $studio;

        $this->data = array(
            'sitemaps' => array(),
            'code' => 0
        );

        $total = 0;

        if (isset($_GET['sitemap'])) {
            $sitemap = $_GET['sitemap'];
            $sitemap = unserialize(base64_decode($sitemap));
            $checkid = strrev(md5(strrev(urlencode($_GET['sitemap']))));
            $sec = $_GET['sec'];

            if ($checkid != $sec) {
                $studio->showError(rt("Invalid security token"));
                $this->stop();
            }

            $url = $sitemap;
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                $studio->showError(rt("Invalid sitemap"));
                $this->stop();
            }

            // Submit the sitemap to google.com
            $web = new WebRequest('https://www.google.com/ping?sitemap=' . urlencode($url));
            $web->get();

            // Submit the sitemap to bing.com
            $web = new WebRequest('https://www.bing.com/ping?sitemap=' . urlencode($url));
            $web->get();

            $this->stop();
        }

        # First, check robots.txt for a sitemap file.

        $web = new UserWebRequest($this->url->getPath('/robots.txt'), 500 * 1024);
        $response = null;

        try {
            $response = $web->get();
            $code = $response->getStatusCode();
        }
        catch (\Exception $e) {
            $code = 404;
        }

        if ($code == 200) {
            # We have a working sitemap file. Let's see if it has any sitemaps.

            $s = new \SEO\Parsers\Robots($response->getBody());
            $sitemaps = $s->getSitemaps();

            if (count($sitemaps) > 0) {
                foreach ($sitemaps as $arr) {
                    $contents = "";
                    $sitemapLink = null;

                    try {
                        $link = new Url($arr['href']);
                        $sitemapLink = $link->getAbsoluteURL();
                    }
                    catch (Exception $ex) {
                        continue;
                    }

                    $sitemapRequest = new UserWebRequest($sitemapLink);

                    try {
                        $sitemapResponse = $sitemapRequest->get();
                        $code = $sitemapResponse->getStatusCode();
                        $sitemapLink = $sitemapResponse->getUrl();
                    }
                    catch (\Exception $e) {
                        $code = 404;
                    }

                    $contents = "";
                    $d = 0;
                    if ($code == 200 && substr(strtolower($sitemapLink), -4) == ".xml") $contents = $sitemapResponse->getBody();
                    if ($code == 200) $d = 50;

                    $h = Url::normalize($sitemapLink);
                    $contentType = '?';

                    if ($contents) {
                        $contentType = $sitemapResponse->getHeader('Content-Type');
                        if (strpos($contentType, ';') !== false) {
                            $contentType = substr($contentType, 0, strpos($contentType, ';'));
                        }
                    }

                    $this->data['sitemaps'][$h] = array(
                        'code' => $code,
                        'd' => $d,
                        'url' => $sitemapLink,
                        'type' => $contentType,
                    );

                    if ($total++ > 50) {
                        break;
                    }
                }
            }
        }

        # Then, check for the actual sitemap.xml file if it isn't already in the array.

        $sitemapLink = $this->url->getPath('/sitemap.xml');
        $request = new UserWebRequest($sitemapLink);

        try {
            $response = $request->get();
            $code = $response->getStatusCode();
            $sitemapLink = $response->getUrl();
        }
        catch (\Exception $e) {
            $code = 404;
        }

        $contents = "";
        $d = 0;

        if ($code == 200) {
            $contents = $response->getBody();
            $d = 100;
        }

        $h = Url::normalize($sitemapLink);
        $contentType = '?';

        if ($contents) {
            $contentType = $response->getHeader('Content-Type');
            if (strpos($contentType, ';') !== false) {
                $contentType = substr($contentType, 0, strpos($contentType, ';'));
            }
        }

        if (isset($this->data['sitemaps'][$h])) {
            $this->data['sitemaps'][$h]['d'] = $d;
        }
        else {
            $this->data['sitemaps'][$h] = array(
                'code' => $code,
                'd' => $d,
                'url' => $sitemapLink,
                'type' => $contentType
            );
        }
    }

    public function output() {
        global $page;

        $sitemapsHTML = "";
        $i = 0;

        foreach ($this->data['sitemaps'] as $url => $params) {
            if ($params['code'] != 200) continue;
            $i++;

            if (stripos($params['type'], 'xml') === false) {
                $params['d'] = ceil($params['d'] * .67);
            }

            $codeimg = "<img src=\"" . $page->getPath() . "resources/images/x32.png\" width=\"16px\" style=\"margin: -2px 3px 0 0;\">";
            if ($params['code'] == 200) $codeimg = "<img src=\"" . $page->getPath() . "resources/images/check32.png\" width=\"16px\" style=\"margin: -2px 3px 0 0;\">";

            $odd = (($i % 2 == 0) ? "odd" : "");
            $id = urlencode(base64_encode(serialize($params['url'])));
            $sec = strrev(md5(strrev($params['url'])));

            $sitemapsHTML .= "<tr class=\"$odd\">
                <td>{$params['url']}</td>
                <td class=\"center\">{$codeimg} {$params['code']}</td>
                <td class=\"center\">{$params['type']}</td>
                <td class=\"center\"><a class=\"btn btn-green submit\" data-id=\"$id\" data-sec=\"$sec\">" . rt("Submit") . "</a></td>
            </tr>";
        }

        if ($i == 0) {
            throw new \Exception(rt("No sitemaps found to submit"));
        }

        $html = $this->getTemplate();
        $html = str_replace("[[SITEMAPS]]", $sitemapsHTML, $html);

        echo sanitize_trusted($html);
    }

    protected function getCacheKey() {
        return "";
    }
}
