<?php

namespace Studio\Tools;

use Exception;
use SEO\Helper\Url;
use Studio\Util\Http\UserResponseTooBigException;
use Studio\Util\Http\UserWebRequest;
use Studio\Util\Http\WebRequest;
use Studio\Util\Parsers\HTMLDocument;

class Sitemap extends Tool
{
    var $name = "Sitemap";
    var $id = "sitemap";
    var $icon = "sitemap";
    var $template = "sitemap.html";

    public function run() {
        # First, check robots.txt for a sitemap file.

        $this->data = array(
            'sitemaps' => array(),
            'code' => 0
        );

        $total = 0;

        $request = new UserWebRequest('http://' . $this->url->getHostname() . '/robots.txt');

        try {
            $response = $request->get();
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

                    $request = new UserWebRequest($sitemapLink);

                    try {
                        $response = $request->get();
                        $code = $response->getStatusCode();
                        $sitemapLink = $response->getUrl();
                    }
                    catch (UserResponseTooBigException $e) {
                        $code = 200;
                    }
                    catch (\Exception $e) {
                        $code = 404;
                    }

                    $contents = "";
                    $d = 0;

                    if ($code == 200) {
                        $d = 50;

                        if ($response) {
                            $contents = $response->getBody();
                        }
                    }

                    $h = Url::normalize($sitemapLink);
                    $numEntries = 0;
                    $contentType = '?';

                    if ($contents) {
                        $contentType = $response->getHeader('Content-Type');
                        if (strpos($contentType, ';') !== false) {
                            $contentType = substr($contentType, 0, strpos($contentType, ';'));
                        }

                        $selector = '<loc>';
                        if (stripos($contentType, 'rss') !== false) $selector = '<link>';
                        if (stripos($contentType, 'html') !== false) $selector = 'href=';

                        if (stripos($contentType, 'rss') === false && stripos($contentType, 'xml') === false) {
                            $d = ceil($d * .67);
                        }

                        $numEntries = substr_count(strtolower($contents), $selector);
                    }

                    $this->data['sitemaps'][$h] = array(
                        'code' => $code,
                        'd' => $d,
                        'type' => $contentType,
                        'url' => $sitemapLink,
                        'entries' => $numEntries
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
        $response = null;

        try {
            $response = $request->get();
            $code = $response->getStatusCode();
            $sitemapLink = $response->getUrl();
        }
        catch (UserResponseTooBigException $e) {
            $code = 200;
        }
        catch (\Exception $e) {
            $code = 404;
        }

        $contents = "";
        $d = 0;
        $h = Url::normalize($sitemapLink);
        $numEntries = 0;
        $contentType = '?';

        if ($contents) {
            $contentType = $response->getHeader('Content-Type');
            if (strpos($contentType, ';') !== false) {
                $contentType = substr($contentType, 0, strpos($contentType, ';'));
            }

            $selector = '<loc>';
            if (stripos($contentType, 'rss') !== false) $selector = '<link>';
            if (stripos($contentType, 'html') !== false) $selector = 'href=';

            if (stripos($contentType, 'rss') === false && stripos($contentType, 'xml') === false) {
                $d = ceil($d * .67);
            }

            $numEntries = substr_count(strtolower($contents), $selector);
        }

        if ($code == 200) {
            $d = 100;

            if ($response) {
                $contents = $response->getBody();
                $numHops = count($response->getTraces());

                if ($numHops > 1) {
                    $d -= ($numHops - 1) * 2;
                }
            }
        }

        if (isset($this->data['sitemaps'][$h])) {
            $this->data['sitemaps'][$h]['d'] = $d;
        }
        else {
            $this->data['sitemaps'][$h] = array(
                'code' => $code,
                'd' => $d,
                'type' => $contentType,
                'url' => $sitemapLink,
                'entries' => $numEntries
            );
        }

        $dom = new HTMLDocument($contents);
        $extended = $dom->find('sitemap loc');
        $baseD = $d;

        // We'll only do a single layer of this for performance reasons
        foreach ($extended as $element) {
            $sitemapLink = trim($element->getPlainText());

            if (preg_match('/<!\[cdata\[(.+)\]\]/i', $sitemapLink, $matches)) {
                $sitemapLink = trim($matches[1]);
            }

            try {
                $link = new Url($sitemapLink);
                $sitemapLink = $link->getAbsoluteURL();
            }
            catch (Exception $ex) {
                continue;
            }

            $request = new UserWebRequest($sitemapLink);
            $response = null;

            try {
                $response = $request->get();
                $code = $response->getStatusCode();
                $sitemapLink = $response->getUrl();
            }
            catch (UserResponseTooBigException $e) {
                $code = 200;
            }
            catch (\Exception $e) {
                $code = 404;
            }

            $d = floor($baseD * .9);
            $numHops = $response ? count($response->getTraces()) : 0;
            $numEntries = 0;

            if ($numHops > 1) {
                $d -= ($numHops - 1) * 2;
            }

            $contentType = '?';

            if ($contents) {
                $contentType = $response->getHeader('Content-Type');
                if (strpos($contentType, ';') !== false) {
                    $contentType = substr($contentType, 0, strpos($contentType, ';'));
                }

                $selector = '<loc>';
                if (stripos($contentType, 'rss') !== false) $selector = '<link>';
                if (stripos($contentType, 'html') !== false) $selector = 'href=';

                if (stripos($contentType, 'rss') === false && stripos($contentType, 'xml') === false) {
                    $d = ceil($d * .67);
                }

                $numEntries = substr_count(strtolower($contents), $selector);
            }

            $this->data['sitemaps'][Url::normalize($sitemapLink)] = array(
                'code' => $code,
                'd' => $d,
                'type' => $contentType,
                'url' => $sitemapLink,
                'entries' => $numEntries
            );
        }
    }

    public function output() {
        global $page;

        $sitemapsHTML = "";
        $i = 0;

        foreach ($this->data['sitemaps'] as $params) {
            $i++;

            $codeimg = "<img src=\"" . $page->getPath() . "resources/images/x32.png\" width=\"16px\" style=\"margin: -2px 3px 0 0;\">";
            if ($params['code'] == 200) $codeimg = "<img src=\"" . $page->getPath() . "resources/images/check32.png\" width=\"16px\" style=\"margin: -2px 3px 0 0;\">";

            $odd = (($i % 2 == 0) ? "odd" : "");
            $entries = number_format($params['entries']);

            $sitemapsHTML .= "<tr class=\"$odd\">
                <td>{$params['url']}</td>
                <td class=\"center\">{$codeimg} {$params['code']}</td>
                <td class=\"center\">{$params['type']}</td>
                <td class=\"center\">{$entries}</td>
                <td class=\"center\">{$params['d']}%</td>
            </tr>";
        }

        $html = $this->getTemplate();
        $html = str_replace("[[SITEMAPS]]", $sitemapsHTML, $html);

        echo sanitize_trusted($html);
    }

    protected function getCacheKey() {
        return "";
    }
}
