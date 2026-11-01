<?php
/***********************
 * Menu Functions
 * Filename: menu.inc.php
 ***********************
 * Purpose:
 *    Dump for HTML Generation functions.
 ***********************/

// Grabs the numbers needed to display the menu counters.
if (!function_exists('menuCounters')) {
    function menuCounters($dbp)
    {
        global $DB;
        $allcount = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `all` FROM `%spurchase_order`",
            $dbp
        ), MYSQLI_ASSOC);
        $opencount = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `open` FROM `%spurchase_order` WHERE `status` = 1 AND `checked` = 0",
            $dbp
        ), MYSQLI_ASSOC);
        return $allcount + $opencount;
    }
}

if (!function_exists('outputHTML')) {
    function outputHTML($content_array, $messages, $template_path)
    {
        global $DB;

        moduleLoad('template');

        $html = new Template($template_path);
        $html->set("content", $content_array['content']);
        $html->set("messages", $messages . $content_array['msg']);
        $html->set("title", $content_array['title']);
        $html->set("header", $content_array['header']);
        $html->set("open_po_count", $content_array['counts']['open']);
        $html->set("headerlinks_array", $content_array['header_links']);
        $html->set("linksperline", $content_array['linksperline']);
        $html->set("po_count", $content_array['counts']['all']);
        echo $html->fetch("body.tpl.php");
    }
}

// Makes the "<< | 1-50 | 51-100 | ... | >>" links
if (!function_exists('makePageLimitLinks')) {
    function makePageLimitLinks($base_url, $total_results, $curr_page = 0, $per_page = 50, $num_links = 4)
    {
        $links = "";
        if (stristr($base_url, "?")) {
            $base_url .= "&amp;";
        } else {
            $base_url .= "?";
        }
        $num_pages  = $total_results / $per_page;
        $start_page = floor($curr_page / $num_links) * $num_links;
        $end_page   = $start_page + $num_links;

        if ($start_page) {
            $links .= "<a href=\"{$base_url}page=" . ($start_page - 1) . "&amp;per_page=$per_page\">&lt;&lt;</a> | ";
        } else {
            $links .= "&lt;&lt; | ";
        }

        for ($i = $start_page; $i < $end_page; $i++) {
            $low_num = ($i * $per_page) + 1;
            $hi_num  = ($i + 1) * $per_page;
            $hi_num  = ($hi_num > $total_results) ? $total_results : $hi_num;
            if ($i == $curr_page) {
                $links .= "$low_num-$hi_num | ";
            } else {
                $links .= "<a href=\"{$base_url}page=$i&amp;per_page=$per_page\">$low_num-$hi_num</a> | ";
            }
            if ($hi_num == $total_results) {
                break;
            }

        }

        if ($total_results > ($per_page * $end_page)) {
            $links .= "<a href=\"{$base_url}page=$end_page&amp;per_page=$per_page\">&gt;&gt;</a>";
        } else {
            $links .= " | &gt;&gt;";
        }

        return $links;
    }
}

// String Trimming Function
if (!function_exists('trimString')) {
    function trimString($string, $length = 0, $id = 0)
    {
        $idstring = "";
        if (!$length) {
            $length = 20;
        }

        $pos   = 0;
        $count = 0;
        $ent   = false;
        $s     = trim($string);
        if ($id) {
            $idstring .= "[ID: $id]";
        }
        while ($count < $length) {
            $c = $s{$pos++};
            $trimmed .= $c;
            if ("" == $c) {
                break;
            }

            if ("&" == $c) {
                $ent = true;
            }

            if (";" == $c) {
                $ent = false;
            }

            if (!$ent) {
                $count++;
            }

        }
        echo "<span class=\"titled\" title=\"$s $idstring\">";
        echo $trimmed;
        if ($count >= $length) {
            echo "...";
        }

        echo "</span>";
    }
}
