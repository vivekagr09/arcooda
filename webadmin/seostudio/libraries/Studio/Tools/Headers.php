<?php

namespace Studio\Tools;

use Exception;
use SEO\Helper\Url;
use Studio\Util\Http\UserWebRequest;

class Headers extends Tool
{
    var $name = "Headers";
    var $id = "headers";
    var $template = "headers.html";
    var $icon = "headers";

    var $path = "/";

    public function prerun($url) {
        $this->path = $url ? (new Url($url))->path : '/';

        if (isset($_POST['path'])) {
            $p = $_POST['path'];
            if ($p == "" || ($p != "" && substr($p, 0, 1) != "/")) $p = "/$p";

            $this->path = $p;
        }
    }

    public function run() {
        if (stripos($this->path, "://") !== false) throw new Exception(rt("Invalid path"));

        $this->data = array(
            'raw' => null,
            'headers' => array(),
            'redirect' => null,
            'site' => null
        );

        // Check for initial redirections of the target website
        $web = new UserWebRequest($this->url->getPath($this->path));
        $web->setFollowRedirects(true);
        $web->setMaxRedirects(12);
        $web->setOption(CURLOPT_HEADER, true);
        $web->setOption(CURLOPT_NOBODY, true);
        $response = $web->get();

        $effective = $response->getUrl();
        $p = parse_url($effective);
        $this->data['site'] = $p['scheme'] . '://' . $p['host'];

        // Perform the request for the target path
        $web = new UserWebRequest($this->url->getPath($this->path));
        $web->setFollowRedirects(false);
        $web->setOption(CURLOPT_HEADER, true);
        $web->setOption(CURLOPT_NOBODY, true);

        $response = $web->get();
        $status = $response->getStatusCode();

        $this->data['raw'] = $response->getBody();
        $this->data['headers'][] = ['Status', $status, $status >= 200 && $status < 300];

        if ($status === 301) {
            $this->data['redirect'] = rt("This page permanently (301) redirects. See below for the new Location.");
        }

        if ($status === 302) {
            $this->data['redirect'] = rt("This page temporarily (302) redirects. See below for the new Location.");
        }

        foreach ($response->getHeaders() as $key => $values) {
            $values = (array) $values;

            foreach ($values as $value) {
                if (strtolower($key) === 'set-cookie') {
                    $value = preg_replace_callback('/^([^=]+)=([^;]+);/', function($matches) use (&$key) {
                        $target = $matches[1] . '=' . $matches[2] . ';';
                        $redacted = $key . ': ' . $matches[1] . '=[redacted];';

                        $this->data['raw'] = preg_replace('/^set-cookie:\s+' . preg_quote($target, '/') .'/im', $redacted, $this->data['raw']);

                        return $matches[1] . '=[redacted];';
                    }, $value);
                }

                $this->data['headers'][] = [$key, trim($value), true];
            }
        }
    }

    public function output() {
        global $page;

        $path = $page->getPath();
        $html = $this->getTemplate();

        $itemsHTML = "";
        foreach ($this->data['headers'] as $i => $header) {
            $odd = (($i % 2 == 0) ? "" : "odd");
            $check = "<img src=\"{$path}resources/images/check32.png\" width=\"16px\" alt=\"Yes\" style=\"margin: -1px 0 0 0;\" />";
            if (!$header[2]) $check = "<img src=\"{$path}resources/images/x32.png\" width=\"16px\" alt=\"No\" style=\"margin: -1px 0 0 0;\" />";

            if ($header[0] == "Status") $header[0] = "<strong>" . rt("Status") . "</strong>";

            $itemsHTML .= "<tr class=\"$odd\">
                <td>{$header[0]}</td>
                <td>{$header[1]}</td>
                <td class=\"center\">{$check}</td>
            </tr>";
        }

        if (!$this->data['redirect']) $this->data['redirect'] = rt("This page does not redirect.");

        $html = str_replace("[[REDIRECT]]", $this->data['redirect'], $html);
        $html = str_replace("[[ITEMS]]", $itemsHTML, $html);
        $html = str_replace("[[CODE]]", sanitize_html($this->data['raw']), $html);
        $html = str_replace("[[PATH]]", $this->path, $html);
        $html = str_replace("[[SITE]]", $this->data['site'], $html);
        echo sanitize_trusted($html);
    }

    public function record($data = "") {
        parent::record($this->path);
    }

    protected function getCacheKey() {
        return "";
    }
}
