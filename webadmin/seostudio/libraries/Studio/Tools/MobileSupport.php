<?php

namespace Studio\Tools;

use Studio\Util\Http\UserWebRequest;
use Studio\Util\Parsers\HTMLDocument;

class MobileSupport extends Tool
{
    var $name = "Mobile Support Test";
    var $id = "mobile-support";
    var $template = "mobile-support.html";
    var $icon = "mobile-support";

    var $mobile = false;
    var $items = array();

    public function run() {
        $desktop = new UserWebRequest($this->url->getAbsoluteURL());
        $desktop->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.63 Safari/537.36');
        $desktop = $desktop->get();
        $desktopURL = $desktop->getUrl();

        $mobile = new UserWebRequest($this->url->getAbsoluteURL());
        $mobile->setUserAgent('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5 Build/MOB30H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.89 Mobile Safari/537.36');
        $mobile = $mobile->get();
        $mobileURL = $mobile->getUrl();

        if ($mobileURL != $desktopURL) {
            $this->items[] = array(
                'type' => "Redirect",
                'info' => $mobileURL,
                'mobile' => true
            );

            $this->mobile = true;
        }

        # Use the DOM to find stylesheets and meta tags

        $desktopHTML = $desktop->getBody();
        $dom = new HTMLDocument($desktopHTML);

        $viewport = $dom->find("meta[name=viewport]", 0, true);
        if ($viewport && $viewport->hasAttribute('content')) {
            $content = sanitize_attribute($viewport->getAttribute('content'));

            $this->items[] = array(
                'type' => rt("Meta"),
                'info' => "&lt;meta name=\"viewport\" content=\"$content\"&gt;",
                'mobile' => true
            );
            $this->mobile = true;
        }

        if (stripos($desktopHTML, "@media")) {
            $this->items[] = array(
                'type' => rt("Media Query"),
                'info' => $desktopURL,
                'mobile' => true
            );
            $this->mobile = true;
        }

        foreach ($dom->find("link[rel=stylesheet]", null, true) as $link) {
            if (!$link->hasAttribute('href')) continue;
            $href = $link->getAttribute('href');

            if (substr($href, 0, 2) == "//") $href = "https://" . substr($href, 2);
            if (substr($href, 0, 1) == "/") $href = $this->url->getPath($href);
            if (substr($href, 0, 2) == "./") $href = "http://{$this->url->hostName}".substr($href, 2);
            if (stripos($href, "://") === false) $href = "http://{$this->url->hostName}/".$href;

            try {
                $sheetRequest = new UserWebRequest($href);
                $sheetResponse = $sheetRequest->get();

                if (stripos($sheetResponse->getBody(), "@media")) {
                    $this->items[] = array(
                        'type' => rt("Stylesheet"),
                        'info' => $sheetResponse->getUrl(),
                        'mobile' => true
                    );
                    $this->mobile = true;
                }
                else {
                    $this->items[] = array(
                        'type' => rt("Stylesheet"),
                        'info' => $sheetResponse->getUrl(),
                        'mobile' => false
                    );
                }
            } catch(\Exception $e) {
                $this->items[] = array(
                    'type' => rt("Stylesheet"),
                    'info' => $href,
                    'mobile' => false
                );
            }
        }

    }

    public function output() {
        global $page;
        $path = $page->getPath();
        $html = $this->getTemplate();

        if ($this->mobile) {
            $html = str_replace("[[ICON]]", "<img src=\"{$path}resources/images/check128.png\" width=\"64px\" />", $html);
            $html = str_replace("[[LABEL]]", "<p class=\"success-label\">" . rt("This website supports mobile devices.") . "</p>", $html);
        }
        else {
            $html = str_replace("[[ICON]]", "<img src=\"{$path}resources/images/error128.png\" width=\"64px\" />", $html);
            $html = str_replace("[[LABEL]]", "<p class=\"error-label\">" . rt("This website does not support mobile devices.") . "</p>", $html);
        }

        $itemsHTML = "";
        foreach ($this->items as $i => $item) {
            $odd = (($i % 2 == 0) ? "" : "odd");
            $mobile = "<img src=\"{$path}resources/images/check32.png\" width=\"16px\" alt=\"Yes\" />";
            if (!$item['mobile']) $mobile = "<img src=\"{$path}resources/images/x32.png\" width=\"16px\" alt=\"No\" />";

            $itemsHTML .= "<tr class=\"$odd\">
                <td class=\"center\">{$item['type']}</td>
                <td>{$item['info']}</td>
                <td class=\"center\">{$mobile}</td>
            </tr>";
        }

        $html = str_replace("[[ITEMS]]", $itemsHTML, $html);
        echo sanitize_trusted($html);
    }

    protected function getCacheKey() {
        return "";
    }
}
