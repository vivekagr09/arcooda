<?php

namespace Studio\Tools;

use \Studio\Util\CURL;
use \SEO\Helper\DOM;
use Exception;
use SEO\Helper\Url;
use Studio\Util\Http\UserWebRequest;

class ExtractMetaTags extends Tool
{
    var $name = "Extract Meta Tags";
    var $id = "extract-meta-tags";
    var $icon = "extract-meta-tags";
    var $template = "meta-tags.html";

    var $path = "/";
    var $domain;

    public function prerun($url) {
        $this->path = $url ? (new Url($url))->path : '/';

        if (isset($_POST['path'])) {
            $this->path = $_POST['path'];
        }
    }

    public function run() {
        $web = new UserWebRequest($this->url->getPath($this->path));
        $response = $web->get();
        $html = $response->getBody();

        new DOM;

        $tags = array();
        $dom = \SEO\Helper\str_get_html($html);
        $googleUses = array("viewport", "description", "robots", "googlebot");
        $bingUses = array("viewport", "description", "robots", "keywords", "bingbot");

        if (!$dom) {
            throw new Exception(rt('Failed to download webpage: {$1}', 'Got invalid response!'));
        }

        if ($dom->find("title", 0)) {
            $tags[] = array('name' => "title", 'content' => $dom->find("title", 0)->plaintext, 'google' => true, 'bing' => true);
        }

        foreach ($dom->find("meta") as $meta) {
            if (!isset($meta->content)) $meta->content = "";
            if (!isset($meta->name)) continue;

            $g = in_array(mb_strtolower($meta->name), $googleUses);
            $b = in_array(mb_strtolower($meta->name), $bingUses);

            $tags[] = array('name' => $meta->name, 'content' => $meta->content, 'google' => $g, 'bing' => $b);
        }

        $p = parse_url($response->getUrl());

        $this->data = array(
            'tags' => $tags,
            'domain' => strtolower($p['scheme'] . "://" . $p['host'])
        );
    }

    public function output() {
        global $page;
        $path = $page->getPath();

        $html = $this->getTemplate();
        $html = str_replace("[[SITE]]", $this->data['domain'], $html);
        $html = str_replace("[[PATH]]", $this->path, $html);

        $itemsHTML = "";
        foreach ($this->data['tags'] as $i => $row) {
            $google = "<img src=\"{$path}resources/images/check32.png\" width=\"16px\" alt=\"Yes\" />";
            $bing = $google;

            if (!$row['google']) $google = "<img src=\"{$path}resources/images/x32.png\" width=\"16px\" alt=\"No\" />";
            if (!$row['bing']) $bing = "<img src=\"{$path}resources/images/x32.png\" width=\"16px\" alt=\"No\" />";

            $odd = (($i % 2 == 0) ? "" : "odd");
            $itemsHTML .= "<tr class=\"$odd\">
                <td>{$row['name']}</td>
                <td>{$row['content']}</td>
                <td class=\"center\">{$google}</td>
                <td class=\"center\">{$bing}</td>
            </tr>";
        }

        $html = str_replace("[[ITEMS]]", $itemsHTML, $html);

        echo sanitize_trusted($html);
    }

    public function record($data = "") {
        parent::record($this->path);
    }

    protected function getCacheKey() {
        return "";
    }
}
