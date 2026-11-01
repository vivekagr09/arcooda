<?php

namespace Studio\Tools;

use Studio\Util\Http\UserResponseTooBigException;
use Studio\Util\Http\UserWebRequest;
use Studio\Util\Http\WebRequestException;

class Robots extends Tool
{
    var $name = "Robots.txt";
    var $id = "robots-txt";
    var $icon = "robots";
    var $template = "robots.html";

    public function run() {
        $href = $this->url->getPath('/robots.txt');

        try {
            $web = new UserWebRequest($href, 1024 * 1024);
            $response = $web->get();

            $code = $response->getStatusCode();
            if ($code >= 400 && $code < 500) throw new \Exception(rt('Robots.txt file was not found (error {$1}) at {$2}', $code, $response->getUrl()));
            if ($code >= 500 && $code < 600) throw new \Exception(rt('Robots.txt is temporarily unavailable (error {$1}) at {$2}', $code, $response->getUrl()));

        }
        catch (UserResponseTooBigException $ex) {
            throw new \Exception(rt("Robots.txt file exceeds the Google size limit of 500KB. Please reduce the file size."));
        }
        catch (WebRequestException $ex) {
            throw new \Exception(rt('Failed to connect to {$1}: {$2}', $href, $ex->getMessage()));
        }

        $this->data = array(
            'd' => new \SEO\Parsers\Robots($response->getBody()),
            'raw' => $response->getBody()
        );
    }

    public function output() {
        $data = $this->data['d'];

        $sitemapsHTML = "";
        foreach ($data->getSitemaps() as $d) {
            $priority = $d['priority'];
            $href = $d['href'];
            $sitemapsHTML .= "<tr><td class=\"center\">$priority</td><td>$href</td></tr>";
        }

        $rulesHTML = "";
        $i = 0;
        foreach ($data->getAllRules()['agents'] as $crawler => $rules) {
            $i++;
            if ($crawler == "*") $crawler = "All";
            if ($crawler == "baiduspider") $crawler = "Baidu";
            if ($crawler == "bingbot") $crawler = "Bing";
            if ($crawler == "googlebot") $crawler = "Google";
            if ($crawler == "googlebot-image") $crawler = "Google Images";
            if ($crawler == "msnbot") $crawler = "MSN";
            if ($crawler == "msrbot") $crawler = "Microsoft";
            if (stripos($crawler, "yahoo") !== false) $crawler = "Yahoo!";
            if ($crawler == "yandexbot") $crawler = "Yandex";
            if ($crawler == "yandeximages") $crawler = "Yandex Images";
            if ($crawler == "googlebot-mobile") $crawler = "Google Mobile";
            if ($crawler == "googlebot-news") $crawler = "Google News";
            if ($crawler == "googlebot-video") $crawler = "Google Videos";
            if ($crawler == "slurp") $crawler = "Yahoo!";

            $list = "";
            $allRules = array();

            foreach ($rules['allow'] as $ix=>$rule) $allRules[$ix] = "<strong>" . rt("Allow") . "</strong>: {$rule}";
            foreach ($rules['disallow'] as $ix=>$rule) $allRules[$ix] = "<strong>" . rt("Disallow") . "</strong>: {$rule}";

            foreach ($allRules as $r) {
                $list .= "<li>$r</li>";
            }

            if (rt($crawler) != "?") $crawler = rt($crawler);

            $odd = (($i % 2 == 0) ? "odd" : "");
            $html = "<tr class=\"$odd\">
                <td class=\"center\">$crawler</td>
                <td>
                <ul>
                    $list
                </ul>
                </td>
            </tr>";

            $rulesHTML .= $html;
        }

        if (empty($sitemapsHTML)) {
            $sitemapsHTML .= "<tr><td class=\"center\" colspan=\"2\">" . rt("Nothing to show.") . "</td></tr>";
        }

        $html = $this->getTemplate();
        $html = str_replace("[[SITEMAPS]]", $sitemapsHTML, $html);
        $html = str_replace("[[RULES]]", $rulesHTML, $html);
        $html = str_replace("[[CODE]]", $this->data['raw'], $html);

        echo sanitize_trusted($html);
    }

    protected function getCacheKey() {
        return "";
    }
}
