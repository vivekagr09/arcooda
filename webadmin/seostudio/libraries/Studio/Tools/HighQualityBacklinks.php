<?php

namespace Studio\Tools;

use Exception;
use SEO\Common\SEOException;
use SEO\Services\LinkAssistant;
use SEO\Services\Structures\LinkAssistantBacklinkCounts;
use SEO\Services\Structures\LinkAssistantBacklinkSearch;
use Studio\Util\Http\WebRequestException;

class HighQualityBacklinks extends Tool
{
    var $name = "High Quality Backlinks";
    var $id = "high-quality-backlinks";
    var $icon = "high-quality-backlinks";
    var $template = "backlinks.html";

    public function run() {
        global $studio;

        try {
            $links = new LinkAssistant();

            $search = new LinkAssistantBacklinkSearch();
            $search->target = $this->url->getAbsoluteURL();
            $search->limit = 350;
            $search->orderBy = 'inlink_rank';
            $search->perDomain = 5;
            $search->mode = 'host';

            $backlinks = $links->getBacklinks($search);
            $counts = new LinkAssistantBacklinkCounts();
            $countsError = false;

            try {
                $counts = $links->getBacklinkCounts($this->url->getAbsoluteURL());
            }
            catch (Exception $innerEx) {
                $countsError = true;
            }

            $this->data = [
                'counts' => $counts,
                'backlinks' => $backlinks
            ];

            if ($countsError) {
                $this->data['counts_failed'] = true;
            }
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
        echo $this->renderTemplate([
            'title' => rt('Top Backlinks')
        ]);
    }
}
