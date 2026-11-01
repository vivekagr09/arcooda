<?php

namespace Studio\Tools;

use Exception;
use SEO\Common\SEOException;
use SEO\Services\LinkAssistant;
use Studio\Util\Http\WebRequestException;

class TopReferrers extends Tool
{
    var $name = "Top Referrers";
    var $id = "top-referrers";
    var $icon = "top-referrers";
    var $template = "top-referrers.html";

    public function run() {
        global $studio;

        try {
            $links = new LinkAssistant();
            $referrers = $links->getReferringDomains(
                $this->url->getAbsoluteURL(),
                250,
                'host',
                'domain_inlink_rank'
            );

            usort($referrers, function($a, $b) {
                return $b->backlinks - $a->backlinks;
            });

            $this->data = [
                'referrers' => array_slice($referrers, 0, 200)
            ];
        }
        catch (SEOException $ex) {
            throw new Exception(rt($ex->getMessage()), $ex->getCode());
        }
        catch (WebRequestException $ex) {
            throw new Exception(rt("We're having some technical difficulties. Please try again later."));
        }
        catch (Exception $ex) {
            $studio->errors->handleException($ex);
            throw new Exception(rt("Unknown error."));
        }
    }

    public function output() {
        global $studio;

        echo $this->renderTemplate([
            'favicons' => $studio->getopt('disable-result-favicons') !== 'On'
        ]);
    }
}

?>
