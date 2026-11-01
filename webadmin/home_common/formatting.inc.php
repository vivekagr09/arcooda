<?php
/*
if (!function_exists("benchmark_request"))
{
global $benchmark_request_start;
$benchmark_request_start = explode(" ", microtime());
function benchmark_request()
{
global $benchmark_request_start;
$time = explode(" ", microtime());
$time = bcsub(bcadd($time[0], $time[1], 6), bcadd($benchmark_request_start[0], $benchmark_request_start[1], 6), 6);
if (filesize("/tmp/request_times.txt") > 1048576)
{
file_put_contents("/tmp/request_times2.txt", "");
rename("/tmp/request_times2.txt", "/tmp/request_times3.txt");
rename("/tmp/request_times.txt", "/tmp/request_times2.txt");
rename("/tmp/request_times3.txt", "/tmp/request_times.txt");
}
file_put_contents("/tmp/request_times.txt", $time . "\thttps://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "\n", FILE_APPEND);
}
register_shutdown_function("benchmark_request");
}
 */

if (!function_exists('compareArrays')) {
    function compareArrays($array1, $array2)
    {
        $ret = array();
        foreach ($array1 as $key => $value) {
            if (stripslashes($value) != stripslashes($array2[$key])) {
                $ret[$key] = $value;
            }
        }

        return $ret;
    }
}
if (!function_exists('compare_arrays')) {
    function compare_arrays($array1, $array2)
    {
        $diff = false;
        foreach ($array1 as $key => $value) {
            if (hecho(formatSQLString(stripslashes($value)), 1) != hecho(formatSQLString(stripslashes($array2[$key])), 1)) {
                $diff = true;
                break;
            }
        }
        return $diff;
    }
}
if (!function_exists('custom_setcookie')) {
    function custom_setcookie($name, $value = '', $expires = 0, $path = '', $domain = '', $secure = false, $http_only = false)
    {
        if (!headers_sent()) {
            header('Set-Cookie: ' . rawurlencode($name) . '=' . rawurlencode($value) . (empty($expires) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expires)) . (empty($path) ? '' : '; path=' . $path) . (empty($domain) ? '' : '; domain=' . $domain) . (!$secure ? '' : '; secure') . (!$http_only ? '' : '; HttpOnly'), false);
            return true;
        }
        return false;
    }
}
if (!function_exists('draw_roundrectangle')) {
    function draw_roundrectangle($img, $x, $y, $cx, $cy, $rad, $col)
    {
        imagefilledrectangle($img, $x, $y + $rad, $cx, $cy - $rad, $col);
        imagefilledrectangle($img, $x + $rad, $y, $cx - $rad, $cy, $col);
        $dia = $rad * 2;
        imagefilledellipse($img, $x + $rad, $y + $rad, $rad * 2, $dia, $col);
        imagefilledellipse($img, $x + $rad, $cy - $rad, $rad * 2, $dia, $col);
        imagefilledellipse($img, $cx - $rad, $cy - $rad, $rad * 2, $dia, $col);
        imagefilledellipse($img, $cx - $rad, $y + $rad, $rad * 2, $dia, $col);
    }
}
if (!function_exists("highlight_words")) {
    function highlight_words($string, $words, $before = '[HIGHLIGHT]', $after = '[/HIGHLIGHT]', $nested = false)
    {
        /**
         * Words is expected to be an array, convert if string
         */
        if (!is_array($words)) {
            $words = explode(' ', $words);
        }

        $words = array_unique($words);
        foreach ($words as $k => $v) {
            if (trim($v) == "") {
                unset($words[$k]);
            }
        }

        if (count($words) == 0) {
            return $string;
        }

        /**
         * Find the positions of each word in the string
         */
        $ranges = array();
        for ($pos = 0; $pos < strlen($string); $pos++) {
            foreach ($words as $v) {
                if (strcasecmp(substr($string, $pos, strlen($v)), $v) == 0) {
                    if (!isset($ranges[$pos])) {
                        $ranges[$pos] = 0;
                    }

                    $ranges[$pos] = max($ranges[$pos], $pos + strlen($v));
                }
            }
        }

        /**
         * If we allow nesting, just insert the before/after, otherwise we need to flatten the results first
         */
        if ($nested) {
            $replace_list = array();
            foreach ($ranges as $k => $v) {
                $replace_list[$k] = 1;
                $replace_list[$v] = 2;
            }
            ksort($replace_list);
            foreach (array_reverse($replace_list, true) as $k => $v) {
                $string = substr($string, 0, $k) . ((1 == $v) ? $before : $after) . substr($string, $k);
            }

        } else {
            foreach ($ranges as $k2 => $v2) {
                foreach ($ranges as $k1 => $v1) {
                    if ($k2 > $k1 && $v1 > $k2) {
                        $v1          = max($v1, $v2);
                        $ranges[$k1] = $v1;
                        unset($ranges[$k2]);
                    }
                }
            }

            foreach (array_reverse($ranges, true) as $k => $v) {
                $string = substr($string, 0, $k) . $before . substr($string, $k, $v - $k) . $after . substr($string, $v);
            }

        }

        /**
         * Return the result
         */
        return $string;
    }
}
if (!function_exists('imageflip')) {
    function imageflip($image)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        for ($y = intdiv($h, 2) + ($h & 1); $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $color1 = imagecolorat($image, $x, $y);
                $color2 = imagecolorat($image, $x, $h - $y - 1);
                imagesetpixel($image, $x, $y, $color2);
                imagesetpixel($image, $x, $h - $y - 1, $color1);
            }
        }

    }
}
if (!function_exists('imagemirror')) {
    function imagemirror($image)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        for ($x = intdiv($w, 2) + ($w & 1); $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $color1 = imagecolorat($image, $x, $y);
                $color2 = imagecolorat($image, $w - $x - 1, $y);
                imagesetpixel($image, $x, $y, $color2);
                imagesetpixel($image, $w - $x - 1, $y, $color1);
            }
        }

    }
}
if (!function_exists('is_included')) {
    function is_included($filename)
    {
        $test_file = realpath($filename);
        if (false === $test_file) {
            $test_file = $filename;
        }

        foreach (get_included_files() as $included) {
            if ($test_file === $included) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('is_assoc')) {
    function is_assoc($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $i = 0;
        foreach ($array as $k => $v) {
            if ($i !== $k) {
                return true;
            }

            $i++;
        }
        return false;
    }
}
if (!function_exists('my_trim')) {
    function my_trim($array)
    {
        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = trim($array[$i]);
        }

        return $array;
    }
}
if (!function_exists('oldformatHTMLDescription')) {
    function oldformatHTMLDescription($list, $id = 0)
    {
        $lines         = explode("\n", $list);
        $inlist        = false;
        $closed_inlist = true;
        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ('' == $line) {
                continue;
            }

            if (substr($line, 0, 1) == "*" || substr($line, 0, 1) == "-") {
                if (false == $inlist) {
                    echo "<ul>\n";
                    $closed_inlist = false;
                    $inlist        = true;
                }
                $line = substr($line, 1);
                echo "<li style=\"color:black;\">$line</li>\n";
            } else {
                if (true == $inlist) {
                    echo "</ul>\n<br />\n";
                    $closed_inlist = true;
                    $inlist        = false;
                }
                echo '<font color="orange"><b>' . ucwords($line) . '</b></font><br/>' . "\n";
            }
        }
        if (false == $closed_inlist) {
            echo "</ul>\n<br />\n";
        }

    }
}
if (!function_exists('print_array')) {
    function print_array($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}
if (!function_exists('print_table')) {
    function print_table($inArray, $capture = false)
    {
        if ($capture) {
            ob_start();
        }

        if (is_array($inArray) || is_object($inArray)) {
            echo '<table style="width:auto;" border="1">' . "\n";
            foreach ($inArray as $key => $value) {
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td style="vertical-align:top;">' . hecho($key, 1) . '</td>' . "\n";
                echo "\t\t" . '<td style="text-align:left;">';
                if (is_array($value)) {
                    print_table($value);
                } else {
                    hecho($value);
                }

                echo '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
            }
            echo '</table>' . "\n";
        } else {
            hecho($inArray);
        }
        if ($capture) {
            return ob_get_clean();
        }

    }
}
if (!function_exists('str_replace_special')) {
    function str_replace_special($search, $replace_before, $replace_after, $subject)
    {
        $output = '';
        if (!is_array($search)) {
            $search = array($search);
        }

        while (strlen($subject) > 0) {
            $findpos = array();
            foreach ($search as $key => $findme) {
                $pos = stripos($subject, $findme);
                if (false !== $pos) {
                    $findpos[$key] = $pos;
                }

            }
            if (count($findpos) > 0) {
                asort($findpos);
                $output .= substr($subject, 0, reset($findpos));
                $subject = substr($subject, reset($findpos));
                $key     = key($findpos);
                $findme  = substr($subject, 0, strlen($search[$key]));
                if (is_array($replace_before)) {
                    $output .= $replace_before[$key] . $findme . $replace_after[$key];
                } else {
                    $output .= $replace_before . $findme . $replace_after;
                }

                $subject = substr($subject, strlen($findme));
            } else {
                $output .= $subject;
                $subject = '';
            }
        }
        return $output;
    }
}
if (!function_exists('formatCleanString')) {
    function formatCleanString($inString)
    {
        return trim(preg_replace("/[\\0- ]+/", " ", $inString));
        /*
    $result = trim($inString);
    for ($i = 0;$i < 32;$i++) $result = str_replace(chr($i), '', $result);
    return $result;
     */
    }
}
if (!function_exists('formatCompareArrays')) {
    function formatCompareArrays($array1, $array2, $capture = false)
    {
        if ($capture) {
            ob_start();
        }

        echo '<table style="width:100%" align="center">' . "\n";
        echo "\t" . '<tr><th>Key</th><th>Value (Array 1)</th><th>Value (Array 2)</th><th>Same</th></tr>' . "\n";
        foreach ($array1 as $key => $value) {
            $val   = hecho(formatSQLString(stripslashes($value)), 1);
            $val_b = hecho(formatSQLString(stripslashes($array2[$key])), 1);
            echo "\t" . '<tr><td>' . hecho($key, 1) . '</td><td>' . hecho(formatSQLString($array1[$key]), 1) . '</td><td>' . hecho(formatSQLString($array2[$key]), 1) . '</td><td>' . (($array1[$key] == $array2[$key]) ? 'same' : 'diff') . '</td></tr>' . "\n";
        }
        echo '</table>' . "\n";
        if ($capture) {
            return ob_get_clean();
        }

    }
}
if (!function_exists('formatDateTime')) {
    function formatDateTime($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return date('Y-m-d H:i:s', $time ?? time());
    }
}
if (!function_exists('formatDate')) {
    function formatDate($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return date('Y-m-d', $time ?? time());
    }
}
if (!function_exists('formatTime')) {
    function formatTime($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return date('H:i:s', $time ?? time());
    }
}
if (!function_exists('formatDaysOpen')) {
    function formatDaysOpen($startDate = null, $endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = time();
        }
        if (is_null($startDate)) {
            $startDate = time();
        }
        $days  = round((($endDate - $startDate) / 86400), 0);
        return $days;
    }
}
if (!function_exists('formatFileSafe')) {
    function formatFileSafe($intext, $preserve_case = false)
    {
        $replaceDOS   = array('/', "\\", ':', '*', '?', '"', '<', '>', '|', "\0");
        $replaceLinux = array('/', "\0");
        $replaceURI   = array(' ', '?', '#', '&', '%');
        $replaceExtra = array(',', '(', ')', '[', ']', "'");
        $replaceAll   = array_unique(array_merge($replaceDOS, $replaceLinux, $replaceURI, $replaceExtra));
        if ($preserve_case) {
            $outtext = trim($intext);
        } else {
            $outtext = strtolower(trim($intext));
        }

        foreach ($replaceAll as $get) {
            $outtext = str_replace($get, '_', $outtext);
        }

        $temptext = '';
        while ($outtext != $temptext) {
            $temptext = $outtext;
            $outtext  = str_replace('__', '_', $outtext);
        }
        while (substr($outtext, 0, 1) == '_' || substr($outtext, 0, 1) == '.') {
            $outtext = substr($outtext, 1);
        }

        while (substr($outtext, -1, 1) == '_') {
            $outtext = substr($outtext, 0, -1);
        }

        return $outtext;
    }
}
if (!function_exists('formatFileSize')) {
    function formatFileSize($size)
    {
        if (0 == $size) {
            return '0 KB';
        }

        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
    }
}
if (!function_exists('formatHTMLBox')) {
    function formatHTMLBox($titleHTML = '', $contentHTML = '', $width = '', $height = '', $className = 'table_white', $borders = 255)
    {
        $outtext  = '';
        $div_list = array();
        if ('' != $className) {
            $div_list[] = array('class' => $className);
        }

        foreach (array(1, 2, 4, 8, 5, 6, 9, 10) as $key => $border) {
            if (($borders & pow(2, $key)) != 0) {
                $div_list[] = array('class' => 'b' . $border);
            }
        }

        if ('' != $width || '' != $height) {
            if (count($div_list) == 0) {
                $div_list[] = array();
            }

            $first_index = reset(array_keys($div_list));
            $last_index  = end(array_keys($div_list));
            $div_list[$first_index]['style'] .= 'float:left;';
            if ('' != $width) {
                $div_list[$last_index]['style'] .= 'width:' . $width . ';';
            }

            if ('' != $height) {
                $div_list[$last_index]['style'] .= 'height:' . $height . ';';
            }

        }
        foreach ($div_list as $atts) {
            $outtext .= '<div';
            foreach ($atts as $key => $value) {
                $outtext .= ' ' . $key . '="' . $value . '"';
            }

            $outtext .= '>';
        }
        $outtext .= "\n";
        if ('' != $titleHTML) {
            $outtext .= '	<div class="head">' . $titleHTML . "</div>\n";
        }

        if ('' != $contentHTML) {
            $outtext .= '	<div class="body">' . $contentHTML . "</div>\n";
        }

        foreach ($div_list as $atts) {
            $outtext .= '</div>';
        }

        $outtext .= "\n";
        return $outtext;
    }
}
if (!function_exists('formatHTMLChunkLinks')) {
    function formatHTMLChunkLinks($chunk_url, $current_chunk, $chunked_list, $legend = '')
    {
        if ('' == $legend) {
            $legend = array();
            foreach ($chunked_list as $key => $value) {
                $legend[$key] = $key;
            }

        }
        $result = '';
        foreach ($legend as $key => $label) {
            if ($key == $current_chunk) {
                $result .= '<span style="background-color:#7FFFD4;">' . hecho($label, 1) . '</span>&nbsp;';
            } elseif (count($chunked_list[$key]) > 0) {
                $result .= '<a href="' . hecho($chunk_url . $key, 1) . '">' . hecho($label, 1) . '</a>&nbsp;';
            } else {
                $result .= '<span style="color:#999999;">' . hecho($label, 1) . '</span>&nbsp;';
            }
        }

        return $result;
    }
}
if (!function_exists('formatHTMLDescription')) {
    function formatHTMLDescription($text)
    {
        return ParsedownExtra::instance()->setBreaksEnabled(true)->text($text);
    }
}
if (!function_exists('formatHTMLInputs')) {
    function formatHTMLInputs($inarray, $child = '')
    {
        $outtext = '';
        if (is_array($inarray)) {
            foreach ($inarray as $key => $item) {
                if (is_array($item)) {
                    if ('' == $child) {
                        $outtext .= formatHTMLInputs($item, $key);
                    } else {
                        $outtext .= formatHTMLInputs($item, $child . '[' . $key . ']');
                    }

                } elseif ('' == $child) {
                    $outtext .= '<input type="hidden" name="' . hecho($key, 1) . '" value="' . hecho($item, 1) . "\" />\n";
                } else {
                    $outtext .= '<input type="hidden" name="' . hecho($child . '[' . $key . ']', 1) . '" value="' . hecho($item, 1) . "\" />\n";
                }

            }
        }

        return $outtext;
    }
}
if (!function_exists('formatHTMLOptions')) {
    function formatHTMLOptions($inarray, $sel = '', $line = '')
    {
        if (!is_array($sel)) {
            $sel = array($sel);
        }

        $outtext = '';
        if (is_array($inarray)) {
            foreach ($inarray as $key => $item) {
                if (is_array($item)) {
                    $outtext .= '<optgroup label="' . hecho($key, 1) . '">' . $line . formatHTMLOptions($item, $sel, $line) . '</optgroup>' . $line;
                } elseif (substr($key, 0, 2) == '--') {
                    $outtext .= '<option disabled="disabled">' . htmlspecialchars($item) . "</option>" . $line;
                } else {
                     if (strpos($item, '<span') !== false) {
                           $item = preg_replace('/<span.*?>/i', '', $item);   // Remove opening <span ...>
                            $item = str_replace('</span>', '', $item);         // Remove closing </span>
                            $item = trim($item);
                        $outtext .= '<option value="' . htmlspecialchars($key) . '"' . ((in_array($key, $sel)) ? ' selected="selected"' : '') . ' class="highlight-option">' . htmlspecialchars($item) . "</option>" . $line;
                    }else{
                        $outtext .= '<option value="' . htmlspecialchars($key) . '"' . ((in_array($key, $sel)) ? ' selected="selected"' : '') . '>' . htmlspecialchars($item) . "</option>" . $line;
                    }
                    
                }

            }
        }

        return $outtext;
    }
}
if (!function_exists('formatHTMLPageLinks')) {
    function formatHTMLPageLinks($page_url, $current_page, $displayed_items, $total_items, $items_per_page, $style = '')
    {
        if (0 == $items_per_page) {
            $items_per_page = 1;
        }

        $total_pages = ceil($total_items / ($items_per_page + 0.0));
        $first_page  = max(0, $current_page - 2);
        $last_page   = min($total_pages - 1, $first_page + 4);
        $result      = '<b>Showing : ' . ($current_page * $items_per_page + 1) . ' - ' . ($current_page * $items_per_page + $displayed_items) . ' of ' . $total_items . '</b>';
        if ($total_pages > 1) {
            $result .= '<b> | </b>';
        }

        if ($first_page > 0) {
            $result .= '<a ' . $style . ' href="' . hecho($page_url . max(0, $current_page - 5), 1) . '">&lt;&lt;</a> ';
        }

        if ($total_pages > 1) {
            for ($i = $first_page; $i <= $last_page; $i++) {
                if ($i != $current_page) {
                    $result .= '<a ' . $style . (($i == $current_page - 1) ? ' rel="prev"' : '') . (($i == $current_page + 1) ? ' rel="next"' : '') . ' href="' . hecho($page_url . $i, 1) . '">';
                }

                $result .= ($i * $items_per_page + 1) . ' - ' . min($total_items, ($i + 1) * $items_per_page);
                if ($i != $current_page) {
                    $result .= '</a>';
                }

                if ($i < $last_page) {
                    $result .= ' | ';
                }

            }
            if ($last_page < ($total_pages - 1)) {
                $result .= ' <a ' . $style . ' href="' . hecho($page_url . min($total_pages - 1, $current_page + 5), 1) . '">&gt;&gt;</a>';
            }

        }
        if ("?page_id=" != $page_url && "?page=" != $page_url) {
            return str_replace('href=""', 'href="?"', strtr($result, array("&amp;page_id=0&amp;" => "&amp;", "&amp;page=0&amp;" => "&amp;", "?page_id=0&amp;" => "?", "&amp;page_id=0" => "", "?page=0&amp;" => "?", "&amp;page=0" => "", "?page_id=0" => "", "?page=0" => "")));
        }

        return $result;
    }
}
if (!function_exists("formatHTMLPublicPageLinks")) {
    function formatHTMLPublicPageLinks($page_url, $total_pages, $current_page = 0)
    {
        if ($total_pages < 2) {
            return '';
        }

        $page_list = array();

        /**
         * Nearby Pages
         */
        $first_page = max(0, $current_page - 4);
        $last_page  = min($total_pages - 1, $first_page + 9);
        $first_page = max(0, $last_page - 9);
        foreach (range($first_page, $last_page) as $i) {
            $page_list[$i] = $i;
        }

        /**
         * Add the First and Last Pages
         */
        foreach (range(0, min(0, $total_pages - 1)) as $i) {
            $page_list[$i] = $i;
        }

        foreach (range(max(0, $total_pages - 1), $total_pages - 1) as $i) {
            $page_list[$i] = $i;
        }

        /**
         * Build the final links
         */
        sort($page_list);
        $result = array();
        if ($current_page > 0) {
            $result[] = '<li><a rel="prev" href="' . hecho($page_url . ($current_page - 1), 1) . '">Previous</a></li>';
        } else {
            $result[] = '<li><span>Previous</span></li>';
        }

        $prev = -1;
        foreach ($page_list as $page_id) {
            if ($page_id != $prev + 1) {
                $result[] = '<li><span>...</span></li>';
            }

            $prev = $page_id;
            if ($current_page != $page_id) {
                $result[] = '<li><a' . (($current_page == $page_id - 1) ? ' rel="next"' : '') . (($current_page == $page_id + 1) ? ' rel="prev"' : '') . ' href="' . hecho($page_url . $page_id, 1) . '">' . ($page_id + 1) . '</a></li>';
            } else {
                $result[] = '<li><span>' . ($page_id + 1) . '</span></li>';
            }

        }
        if ($current_page < ($total_pages - 1)) {
            $result[] = '<li><a rel="next" href="' . hecho($page_url . ($current_page + 1), 1) . '">Next</a></li>';
        } else {
            $result[] = '<li><span>Next</span></li>';
        }

        if ("?page_id=" != $page_url && "?page=" != $page_url) {
            return str_replace('href=""', 'href="?"', strtr('<ul class="page_links">' . implode(' ', $result) . '</ul>', array("&amp;page_id=0&amp;" => "&amp;", "&amp;page=0&amp;" => "&amp;", "?page_id=0&amp;" => "?", "&amp;page_id=0" => "", "?page=0&amp;" => "?", "&amp;page=0" => "", "?page_id=0" => "", "?page=0" => "")));
        }

        return '<ul class="page_links">' . implode(' ', $result) . '</ul>';
    }
}
/*
if (!function_exists('formatHTMLPublicTitle'))
{
function formatHTMLPublicTitle($title, $extra = '', $unfinished = false, $type = 'flag')
{
return '<h2>' . hecho($title, 1) . '</h2>' . "\n";
}
function formatHTMLPublicTitle($title, $extra = '', $unfinished = false, $type = 'flag')
{
return '<div class="'.$type.'Header"><img src="/images/title_arrow.png" style="vertical-align:bottom;" alt="&gt;" />'.$title.(($extra!='')?(' - '.$extra):'').'</div><br />'."\n";
}
function formatHTMLPublicTitle($title, $extra = '', $unfinished = false, $type = 'flag')
{
return '<table width="95%" align="center"><tr><td width="0"><img src="/common/images/title/'.formatFileSafe('title_'.$title.'.gif').'" /></td><td width="100%"><hr /></td></tr></table><br /><br />'."\n";
}
function formatHTMLPublicTitle($title, $extra = '', $unfinished = false, $type = 'flag')
{
return '<div class="'.$type.'Header">'.$title.(($extra!='')?(' - '.$extra):'').'</div><br />'."\n";
}
}
 */
if (!function_exists('formatHTMLTabs')) {
    function formatHTMLTabs($tab_array = array(), $active = '', $jscript_array)
    {
        if (!is_array($tab_array) || !count($tab_array)) {
            return;
        }

        if ('' == $jscript_array) {
            return;
        }

        $str  = '<div align="center"><div class="tabHeader">';
        $tabs = array();
        foreach ($tab_array as $name => $dis_name) {
            $span   = '<span id="span' . $name . '"' . (($name != $active) ? 'class="tabHeaderInactive"' : '') . '>';
            $href   = '<a href="javascript:toggleTabs(\'' . $name . '\', ' . $jscript_array . ');">' . $dis_name . '</a>';
            $tabs[] = $span . $href . '</span>';
        }
        $str .= implode("\n", $tabs) . '</div></div>';
        return $str;
    }
}
if (!function_exists('formatHTMLTextarea')) {
    function formatHTMLTextarea($name, $value, $js_resize = false, $extra = '', $rows = '')
    {
        $cols = 75;
        if ('' == $rows) {
            $rows = max(2, substr_count(wordwrap($value, 75, "\n", true), "\n"));
        }

        $result = '<textarea';
        $result .= ' name="' . hecho($name, 1) . '"';
        $result .= ' id="' . hecho($name, 1) . '"';
        $result .= ' cols="' . $cols . '"';
        $result .= ' rows="' . intval($rows) . '"';
        if ($js_resize) {
            $result .= ' oninput="adjustTextareaHeight(this);"';
        }

        if ('' != $extra) {
            $result .= ' ' . $extra;
        }

        $result .= '>';
        $result .= hecho($value, 1);
        $result .= '</textarea>';
        return $result;
    }
}
if (!function_exists('formatHTMLTitle')) {
    function formatHTMLTitle($title, $extra = '', $unfinished = false)
    {
        return "<br />\n<div class=\"flagHeader\"><span" . (($unfinished) ? ' style="background-color:yellow;"' : '') . ">" . $title . "</span> " . $extra . "</div>\n<br />\n";
    }
}
if (!function_exists('formatHTMLUBB')) {
    function formatHTMLUBB($inText)
    {
        $pos1    = 0;
        $pos2    = 0;
        $pos3    = 0;
        $outText = '';
        while ('' != $inText) {
            $pos1 = strpos($inText, '[');
            $pos2 = strpos($inText, ']', $pos1 + 1);
            if (false === $pos1 || false === $pos2) {
                $outText .= hecho($inText, 1);
                return $outText;
            }
            if ($pos1 > 0) {
                $outText .= hecho(substr($inText, 0, $pos1), 1);
                $inText = substr($inText, $pos1);
                $pos2 -= $pos1;
            }
            $pos1    = $pos2;
            $ubbcode = substr($inText, 1, $pos1 - 1);
            $urlcode = '';
            $pos2    = strpos($ubbcode, '=');
            if (!(false === $pos2)) {
                $urlcode = substr($ubbcode, $pos2 + 1);
                $ubbcode = substr($ubbcode, 0, $pos2);
            }
            $ubbcode = strtolower($ubbcode);
            @$pos2   = strpos(strtolower($inText), '[/' . $ubbcode . ']', $pos1 + 1);
            if (false === $pos2) {
                $outText .= '[';
                $inText = substr($inText, 1);
            } else {
                $pos3    = strlen('[/' . $ubbcode . ']') + $pos2;
                $altcode = substr($inText, $pos1 + 1, $pos2 - $pos1 - 1);
                if ('' == $urlcode) {
                    $urlcode = $altcode;
                }

                switch ($ubbcode) {
                    case 'color':
                        $outText .= '<span style="color:' . hecho($urlcode, 1) . ';">' . formatHTMLUBB($altcode) . '</span>';
                        $inText = substr($inText, $pos3);
                        break;
                    case 'b':
                    case 'i':
                    case 's':
                    case 'u':
                        $outText .= '<' . $ubbcode . '>' . formatHTMLUBB($altcode) . '</' . $ubbcode . '>';
                        $inText = substr($inText, $pos3);
                        break;
                    case 'code':
                    case 'sql':
                        $outText .= '<code>' . hecho($altcode, 1) . '</code>';
                        $inText = substr($inText, $pos3);
                        break;
                    case 'img':
                        $outText .= '<img src="' . hecho($urlcode, 1) . '" alt="' . hecho($altcode, 1) . '" />';
                        $inText = substr($inText, $pos3);
                        break;
                    case 'url':
                        $outText .= '<a href="' . hecho($urlcode, 1) . '" target="_blank">' . formatHTMLUBB($altcode) . '</a>';
                        $inText = substr($inText, $pos3);
                        break;
                    default:
                        $outText .= '[';
                        $inText = substr($inText, 1);
                }
            }
        }
        return $outText;
    }
}
if (!function_exists('formatJSArray')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $inarray
     *
     * @return string
     */
    function formatJSArray($inarray)
    {
        $safe_vals = array();
        foreach ($inarray as $key => $value) {
            $safe_vals[] = formatJSVariant($value);
        }

        return '[' . implode(',', $safe_vals) . ']';
    }
}
if (!function_exists('formatJSAssoc')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $inarray
     *
     * @return string
     */
    function formatJSAssoc($inarray)
    {
        $safe_vals = array();
        foreach ($inarray as $key => $value) {
            $safe_vals[] = formatJSVariant($key) . ':' . formatJSVariant($value);
        }

        return '{' . implode(',', $safe_vals) . '}';
    }
}
if (!function_exists('formatJSString')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $instring
     *
     * @return string
     */
    function formatJSString($instring)
    {
        $result = trim($instring);
        $result = addslashes($result);
        $result = str_replace("\n", '\n', $result);
        $result = str_replace("\r", '\n', $result);
        $result = str_replace('\\"', '\\042', $result);
        $result = str_replace("\\'", '\\047', $result);
        return "'" . $result . "'";
    }
}
if (!function_exists('formatJSVariant')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $value
     *
     * @return string
     */
    function formatJSVariant($value)
    {
        if (is_object($value) || function_exists('is_assoc') && is_assoc($value)) {
            return formatJSAssoc($value);
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        } elseif (is_array($value)) {
            return formatJSArray($value);
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return formatJSString($value);
        }

    }
}
if (!function_exists('formatJSONString')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $obj
     *
     * @return string
     */
    function formatJSONString($obj)
    {
        return '"' . strtr($obj, array("\"" => "\\\"", "\\" => "\\\\", "/" => "\\/", "\010" => "\\b", "\014" => "\\f", "\012" => "\\n", "\015" => "\\r", "\011" => "\\t")) . '"';
    }
}
if (!function_exists('formatJSONObject')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $obj
     *
     * @return string
     */
    function formatJSONObject($obj)
    {
        $escaped = array();
        foreach ($obj as $k => $v) {
            $escaped[] = formatJSONString($k) . ':' . formatJSON($v);
        }

        return "{" . implode(",", $escaped) . "}";
    }
}
if (!function_exists('formatJSONArray')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $obj
     *
     * @return string
     */
    function formatJSONArray($obj)
    {
        $escaped = array();
        foreach ($obj as $k => $v) {
            $escaped[] = formatJSON($v);
        }

        return "[" . implode(",", $escaped) . "]";
    }
}
if (!function_exists('formatJSON')) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $obj
     *
     * @return string
     */
    function formatJSON($value)
    {
        if (is_object($value) || (function_exists('is_assoc') && is_assoc($value))) {
            return formatJSONObject($value);
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        } elseif (is_array($value)) {
            return formatJSONArray($value);
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return ($value) ? 'true' : 'false';
        } else {
            return formatJSONString($value);
        }

    }
}
if (!function_exists('formatMETA')) {
    function formatMETA($theString, $type = '')
    {
        $theString = strip_tags($theString);
        $theString = str_replace('"', "'", $theString);
        $theString = str_replace(';', "", $theString);
        if (strcasecmp($type, "keyword") == 0) {
            $theString = str_replace(",", " ", $theString);
            $theString = str_replace("-", " ", $theString);
            $words     = explode(' ', $theString);
            $words     = my_trim($words);
            $words     = array_unique($words);
            $keywords  = "";
            for ($i = 0; $i < 50 && $i < count($words); $i++) {
                $key = key($words);
                if (trim($words[$key]) != '') {
                    $keywords .= ucwords($words[$key]) . ", ";
                }

                next($words);
            }
            return $keywords;
        }
        return $theString;
    }
}
if (!function_exists('formatMemberAddress')) {
    function formatMemberAddress($inArray)
    {
        $addr = '';
        $line = array();
        if (isset($inArray['company'])) {
            $line[0] = $inArray['company'];
        }

        if (isset($inArray['address_1'])) {
            $line[1] = $inArray['address_1'];
        }

        if (isset($inArray['address_2'])) {
            $line[2] = $inArray['address_2'];
        }

        $line[3] = '';
        if (isset($inArray['city'])) {
            $line[3] .= $inArray['city'] . ', ';
        }

        if (isset($inArray['state'])) {
            $line[3] .= $inArray['state'] . ' ';
        }

        if (isset($inArray['zip'])) {
            $line[3] .= ('NA' != $inArray['zip']) ? $inArray['zip'] : '';
        }

        if (isset($inArray['country_full'])) {
            $line[4] = $inArray['country_full'];
        }

        $line[5] = ' ';
        $line[6] = '';
        if (isset($inArray['phone_number']) && '' != $inArray['phone_number']) {
            $line[6] = 'Ph: ' . $inArray['phone_number'];
        }

        $line[7] = '';
        for ($i = 0; $i < 8; $i++) {
            if (isset($line[$i]) && '' != $line[$i]) {
                $addr .= $line[$i] . "\n";
            }
        }

        return trim($addr);
    }
}
/*
if (!function_exists('formatPHPLiveHTML'))
{
function formatPHPLiveHTML($member_id, $capture = false, $btn = '', $product_id = 0, $deptid = 0, $text = '', $checkout = 0, $signup = 0, $name = '', $lname = '', $email = '')
function formatPHPLiveHTML($member_id, $capture = false, $btn = '', $product_id = 0, $sub_id = 0, $deptid = 0, $text = '', $checkout = 0, $signup = 0, $name = '', $lname = '', $email = '', $bulk_id = 0, $wanted_id = 0)
{
if ($btn == '') $btn = formatPHPLiveRandom();
$url_vars=array();
$url_vars['base_url']='/phplive';
$url_vars['btn']=$btn;
$url_vars['bulk_id']=intval($bulk_id);
$url_vars['checkout']=$checkout;
$url_vars['deptid']=intval($deptid);
$url_vars['email']=$email;
$url_vars['l']='highway';
$url_vars['lname']=$lname;
$url_vars['member_id']=intval($member_id);
$url_vars['name']=$name;
$url_vars['product_id']=intval($product_id);
$url_vars['signup']=$signup;
$url_vars['site_id']=$_GLOBALS['site_settings']['site_id'];
$url_vars['sub_id']=intval($sub_id);
$url_vars['text']=$text;
$url_vars['wanted_id']=intval($wanted_id);
$url_vars['x']='1';
foreach($url_vars as $key=>$value) if($value='') unset($url_vars[$key]);
$result = '<script type="text/javascript" src="'.hecho('/phplive/js/status_image.php?'.http_build_query($url_vars), 1).'"></script>';
if ($capture) return $result; else echo $result;
}
}
 */
if (!function_exists('formatPHPLiveRandom')) {
    function formatPHPLiveRandom()
    {
        static $btn = 0;
        if (0 == $btn) {
            $btn = mt_rand(100, 10000000);
        }

        return $btn;
    }
}
if (!function_exists('formatPortOptions')) {
    function formatPortOptions($inarray, $type, $sel = '')
    {
        $outtext = '';
        if (count($inarray) == 0) {
            $outtext .= '<option value="" selected="selected">No ' . ucfirst($type) . 'ports found for the selected Country</option>';
        } else {
            foreach ($inarray as $key => $item) {
                $outtext .= '<option value="' . hecho($key, 1) . '"' . (($key == $sel) ? ' selected="selected"' : '') . '>' . hecho($item, 1) . "</option>\n";
            }
        }

        return $outtext;
    }
}
if (!function_exists('formatRTFString')) {
    include_once __DIR__ . '/utf8.inc.php';
    mb_internal_encoding("UTF-8");
    function formatRTFString($instring)
    {
        $instring = mb_convert_encoding($instring, 'Windows-1252', 'UTF-8');

        $instring = str_replace("\\", "\\\\", $instring);
        $instring = str_replace("{", "\\{", $instring);
        $instring = str_replace("}", "\\}", $instring);
        $instring = str_replace("\n", "\par ", $instring);
        return $instring;
    }
}
if (!function_exists('formatSQL')) {
    function formatSQL($value)
    {
        if (is_null($value)) {
            return "NULL";
        }

        if (is_array($value)) {
            return formatSQLSet($value);
        }

        if (is_int($value)) {
            return strval($value);
        }

        return formatSQLString($value);
    }
}
if (!function_exists('formatSQLDate')) {
    function formatSQLDate($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return "'" . date('Y-m-d', $time ?? time()) . "'";
    }
}
if (!function_exists('formatSQLDateTime')) {
    function formatSQLDateTime($time = null)
    {
        // @note date(,) in php<8 will treat null as 0
        return "'" . date('Y-m-d H:i:s', $time ?? time()) . "'";
    }
}
if (!function_exists('formatSQLLikeSafe')) {
    function formatSQLLikeSafe($text)
    {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $text);
    }
}
if (!function_exists('formatSQLLikeString')) {
    function formatSQLLikeString($text)
    {
        return formatSQLString('%' . formatSQLLikeSafe($text) . '%');
    }
}
if (!function_exists('formatSQLSet')) {
    function formatSQLSet($inarray)
    {
        if (!is_array($inarray)) {
            $inarray = explode(",", $inarray);
        }

        $inarray = array_unique($inarray);
        foreach ($inarray as $k => $v) {
            if (!is_string($v) || !is_numeric($v)) {
                $inarray[$k] = formatSQL($v);
            }
        }

        sort($inarray);
        return formatSQLString(implode(",", $inarray));
    }
}
if (!function_exists('formatSQLString')) {
    function formatSQLString($instring)
    {
        $outstring = "'" . addslashes($instring) . "'";
        $outstring = str_replace(chr(13) . chr(10), '\n', $outstring);
        $outstring = str_replace(chr(13), '\n', $outstring);
        $outstring = str_replace(chr(10), '\n', $outstring);
        return $outstring;
    }
}
if (!function_exists('formatSecondsToMinutes')) {
    function formatSecondsToMinutes($seconds)
    {
        $mins = floor($seconds / 60);
        $secs = $seconds % 60;
        return $mins . ' Minutes, ' . $secs . ' Seconds';
    }
}
if (!function_exists('formatSecondsToText')) {
    function formatSecondsToText($seconds)
    {
        if ($seconds > 31557600) {
            return ceil($seconds / 31557600.0) . ' Years';
        }

        if ($seconds > 2629800) {
            return ceil($seconds / 2629800.0) . ' Months';
        }

        if ($seconds > 604800) {
            return ceil($seconds / 604800.0) . ' Weeks';
        }

        if ($seconds > 86400) {
            return ceil($seconds / 86400.0) . ' Days';
        }

        if ($seconds > 3600) {
            return ceil($seconds / 3600.0) . ' Hours';
        }

        return round($seconds / 60.0) . ' Minutes';
    }
}
if (!function_exists('formatSecondsToTime')) {
    function formatSecondsToTime($seconds, $sects = 3)
    {
        $result = array();
        for ($i = 0; $i < 2; $i++) {
            array_unshift($result, substr('00' . ($seconds % 60), -2));
            $seconds = intval($seconds / 60);
        }
        array_unshift($result, $seconds);
        if ($sects >= 0) {
            while (count($result) > $sects) {
                array_pop($result);
            }
        }

        return implode(':', $result);
    }
}
if (!function_exists('formatTabsToSpaces')) {
    function formatTabsToSpaces($inString, $tabsize = 4)
    {
        return str_replace("\t", str_repeat(' ', $tabsize), $inString);
    }
}
if (!function_exists('formatTimeToSeconds')) {
    function formatTimeToSeconds($inString)
    {
        $result     = 0;
        $components = explode(':', $inString);
        for ($i = 0; $i < 3; $i++) {
            $result *= 60;
            $result += $components[$i];
        }
        return $result;
    }
}
if (!function_exists("formatTitle")) {
    function formatTitle($title, $maxlen = 100)
    {
        if (strlen($title) > $maxlen) {
            $title = str_replace(" by Highway Entertainment ", " ", $title);
        }

        if (strlen($title) > $maxlen) {
            $title = preg_replace("/(- Page [0-9]+ )of [0-9]+ /", "\\1", $title);
        }

        if (strlen($title) > $maxlen) {
            $title = str_replace("- Page 1 ", "", $title);
        }

        if (strlen($title) > $maxlen) {
            $title = str_replace(" in Australia ", " ", $title);
        }

        if (strlen($title) > $maxlen) {
            $title = str_replace(" for Sale ", " ", $title);
        }

        if (strlen($title) > $maxlen) {
            $len    = strlen($title);
            $split1 = array_reverse(explode(" | ", $title));
            foreach ($split1 as $k => $v) {
                $save   = null;
                $split2 = explode(" - ", $v);
                if (preg_match("/^Page [0-9]+$/", end($split2))) {
                    $save = array_pop($split2);
                }

                while ($len > $maxlen && count($split2) > 1) {
                    $len -= 3 + strlen(array_pop($split2));
                }

                if (!is_null($save)) {
                    $split2[] = $save;
                }

                $split1[$k] = implode(" - ", $split2);
            }
            $title = implode(" | ", array_reverse($split1));
        }
        if (strlen($title) > $maxlen) {
            $len    = strlen($title);
            $split1 = explode(" | ", $title);
            $save1  = array_shift($split1);
            $save2  = array_pop($split1);
            while ($len > $maxlen && count($split1) > 0) {
                $len -= 3 + strlen(array_pop($split1));
            }

            array_unshift($split1, $save1);
            array_push($split1, $save2);
            $title = implode(" | ", $split1);
        }
        if (strlen($title) > $maxlen) {
            $title = reset(explode(" | ", $title));
        }

        return $title;
    }
}
if (!function_exists('formatURLSafe')) {
    function formatURLSafe($intext, $preserve_case = false)
    {
        return str_replace('_', '-', formatFileSafe($intext, $preserve_case));
    }
}
if (!function_exists('formatWebsite')) {
    function formatWebsite($website)
    {
        $ret = '';
        if (strstr($website, 'www') == '' && strstr($website, 'http') == '') {
            $ret = "https://www." . $website;
        } else if (strstr($website, 'http') == '') {
            $ret = "https://" . $website;
        }

        if ('' != $ret) {
            return $ret;
        }

        return $website;
    }
}
if (!function_exists('formatYouJoomlaPlayerVars')) {
    function formatYouJoomlaPlayerVars($var)
    {
        if (is_array($var)) {
            $results = array();
            foreach ($var as $key => $value) {
                if (is_array($value)) {
                    $results[] = $key . '*[' . formatYouJoomlaPlayerVars($value) . ']*a';
                } else {
                    $results[] = $key . '*' . formatYouJoomlaPlayerVars($value);
                }
            }

            return implode('||', $results);
        }
        if (is_bool($var)) {
            return ($var) ? 'true*b' : 'false*b';
        }

        if (is_float($var)) {
            return floatval($var) . '*f';
        }

        if (is_int($var)) {
            return intval($var) . '*i';
        }

        if (is_string($var)) {
            return strval($var) . '*s';
        }

        return '';
    }
}

if (!function_exists("hecho")) {
    include_once __DIR__ . '/utf8.inc.php';
    mb_internal_encoding("UTF-8");
    function hecho($text, $return = false)
    {
        //if (utf8ToUnicode($text) === false) $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        if (!isUtf8($text)) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }

        if ($return) {
            return preg_replace('/&amp;([A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/', '&$1', htmlspecialchars($text, ENT_QUOTES, "UTF-8"));
        } else {
            echo preg_replace('/&amp;([A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/', '&$1', htmlspecialchars($text, ENT_QUOTES, "UTF-8"));
        }

    }
}

if (!function_exists("jecho")) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $text
     * @param bool  $return
     *
     * @return string|null
     */
    function jecho($text, $return = false)
    {
        if ($return) {
            return formatJSON($text);
        }

        echo formatJSON($text);
    }
}

if (!function_exists("secho")) {
    function secho($text, $return = false)
    {
        if ($return) {
            return formatSQL($text);
        }

        echo formatSQL($text);
    }
}

if (!function_exists("base64_url_encode")) {
    function base64_url_encode($input)
    {
        return strtr(rtrim(base64_encode($input), "="), '+/=', '-_,');
    }
}

if (!function_exists("base64_url_decode")) {
    function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_,', '+/='));
    }
}

if (!function_exists("relative_url")) {
    function relative_url($location = "", $to = null)
    {
        $split = explode("?", $_SERVER["REQUEST_URI"], 2);
        $url   = array(
            "scheme"   => ((array_key_exists("HTTPS", $_SERVER) && "on" == $_SERVER["HTTPS"]) ? "https" : "http"),
            "host"     => $_SERVER["HTTP_HOST"],
            "path"     => array_shift($split),
            "query"    => implode("?", $split),
            "fragment" => "",
        );
        if (!is_null($to)) {
            $url = parse_url($to);
        }

        if (preg_match("@^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?@", $location, $matches)) {
            if (array_key_exists(2, $matches) && "" != $matches[2]) {
                $url["host"]     = "";
                $url["path"]     = "/";
                $url["query"]    = "";
                $url["fragment"] = "";
            }
            if (array_key_exists(4, $matches) && "" != $matches[4]) {
                $url["path"]     = "/";
                $url["query"]    = "";
                $url["fragment"] = "";
            }
            if (array_key_exists(5, $matches) && "" != $matches[5]) {
                $url["query"]    = "";
                $url["fragment"] = "";
            }
            if (array_key_exists(7, $matches) && "" != $matches[7]) {
                $url["fragment"] = "";
            }
            if (array_key_exists(5, $matches) && "" != $matches[5] && substr($matches[5], 0, 1) != "/") {
                $temp = $url["path"];
                if (substr($temp, -1) == "/") {
                    $temp .= ".";
                }

                $matches[5] = dirname($temp) . "/" . $matches[5];
            }
            foreach (array(
                "scheme"   => (array_key_exists(2, $matches)) ? $matches[2] : "",
                "host"     => (array_key_exists(4, $matches)) ? $matches[4] : "",
                "path"     => (array_key_exists(5, $matches)) ? $matches[5] : "",
                "query"    => (array_key_exists(7, $matches)) ? $matches[7] : "",
                "fragment" => (array_key_exists(9, $matches)) ? $matches[9] : "",
            ) as $k => $v) {
                if ("" != $v) {
                    $url[$k] = $v;
                }
            }

        }
        $url = ((array_key_exists("host", $url) && "" != $url["host"]) ? ((("" != $url["scheme"]) ? ($url["scheme"] . ":") : "") . "//" . $url["host"]) : "") . $url["path"] . rtrim("?" . ((array_key_exists('query', $url)) ? $url["query"] : ''), "?") . rtrim("#" . ((array_key_exists('fragment', $url)) ? $url["fragment"] : ''), "#");
        return $url;
    }
}

if (!function_exists("redirect")) {
    function redirect($location, $http_code = null)
    {
        $code_names = array(
            300 => "Multiple Choices",
            301 => "Moved Permanently",
            302 => "Found",
            303 => "See Other",
            304 => "Not Modified",
            305 => "Use Proxy",
            306 => "Switch Proxy",
            307 => "Temporary Redirect",
            308 => "Resume Incomplete",
        );
        if (is_null($http_code)) {
            $http_code = 302; //Found
            if ("HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"]) {
                $http_code = 303;
            }
            //See Other
        }
        header('Location: ' . relative_url($location), true, $http_code);
        if (array_key_exists($http_code, $code_names)) {
            header($_SERVER["SERVER_PROTOCOL"] . " " . $http_code . " " . $code_names[$http_code], true);
            header("Status: " . $http_code . " " . $code_names[$http_code], true);
        }
    }
}

if (!function_exists("null_coalesce")) {
    function null_coalesce($args)
    {
        if (func_num_args() == 1 && is_array($args)) {
            return call_user_func_array('null_coalesce', $args);
        }

        foreach (func_get_args() as $arg) {
            if (!is_null($arg)) {
                return $arg;
            }
        }

        return null;
    }
}
if (!function_exists("false_coalesce")) {
    function false_coalesce($args)
    {
        if (func_num_args() == 1 && is_array($args)) {
            return call_user_func_array('false_coalesce', $args);
        }

        foreach (func_get_args() as $arg) {
            if ($arg) {
                return $arg;
            }
        }

        return null;
    }
}
if (!function_exists("empty_coalesce")) {
    function empty_coalesce($args)
    {
        if (func_num_args() == 1 && is_array($args)) {
            return call_user_func_array('empty_coalesce', $args);
        }

        foreach (func_get_args() as $arg) {
            if (!empty($arg)) {
                return $arg;
            }
        }

        return null;
    }
}
if (!function_exists("value_coalesce")) {
    function value_coalesce($args)
    {
        if (func_num_args() == 1 && is_array($args)) {
            return call_user_func_array('value_coalesce', $args);
        }

        foreach (func_get_args() as $arg) {
            if (!is_null($arg) && "" !== $arg) {
                return $arg;
            }
        }

        return null;
    }
}
