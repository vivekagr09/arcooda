<?php
if (!function_exists("formatMemberAddress")) {
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
            $line[3] .= $inArray['zip'];
        }

        if (isset($inArray['country_full'])) {
            $line[4] = $inArray['country_full'];
        }

        $line[5] = '';
        if (isset($inArray['phone_number']) && '' != $inArray['phone_number']) {
            $line[5] = 'Ph: ' . $inArray['phone_number'];
        }

        $line[6] = '';

        for ($i = 0; $i < 7; $i++) {
            if (isset($line[$i]) && '' != $line[$i]) {
                $addr .= $line[$i] . "\n";
            }
        }

        return trim($addr);
    }
}
/*
if (!function_exists('formatHTMLOptions'))
{
function formatHTMLOptions($inarray, $sel = '', $line = '')
{
if (!is_array($inarray) || !count($inarray)) return '';
$outtext = '';
if (is_array($inarray)) foreach($inarray as $key => $item)
{
$out = ($line == '') ? $item : addslashes($item);
$out = htmlspecialchars($out);
$selected = ($key == $sel) ? ' selected="selected"' : '';
$newline = ($line == '') ? "\n" : "";
if (is_array($item))
{
$outtext .= '<optgroup label="' . htmlspecialchars($key) . '">';
$outtext .= formatHTMLOptions($item, $sel, $line);
$outtext .= '</optgroup>';
}
elseif ($out == '' && substr($key, 0, 2) == '--') $outtext .= '<option value="' . htmlspecialchars($key) . '" disabled="disabled">' . $out . "</option>" . $newline;
else $outtext .= '<option value="' . htmlspecialchars($key) . '"' . $selected . '>' . $out . "</option>" . $newline;
}
return $outtext;
}
}
 */
if (!function_exists("formatHTMLPublicTitle")) {
    function formatHTMLPublicTitle($title)
    {
        return '<h2>' . htmlspecialchars($title) . '</h2>' . "\n";
    }
}
if (!function_exists("formatInquiryNumber")) {
    function formatInquiryNumber($id)
    {
        return 'Q#' . substr('000000' . $id, -5);
    }
}
if (!function_exists("formatProformaNumber")) {
    function formatProformaNumber($id)
    {
        return 'I#' . substr('000000' . $id, -5);
    }
}
if (!function_exists("formatJSAssoc")) {
    /**
     * @deprecated use json_encode
     *
     * @param string $varname
     * @param mixed  $inarray
     *
     * @return string
     */
    function formatJSAssoc($varname, $inarray = null)
    {
        if (is_null($inarray)) {
            return formatJSAssoc2($varname);
        }
        //Compatibility with new formatJSAssoc
        return $varname . '=' . formatJSAssoc2($inarray) . ";\n";
    }
}
if (!function_exists("formatJSAssoc2")) {
    /**
     * @deprecated use json_encode
     *
     * @param mixed $inarray
     *
     * @return string
     */
    function formatJSAssoc2($inarray)
    {
        $safe_vals = array();
        if (is_array($inarray)) {
            foreach ($inarray as $key => $value) {
                $safe_vals[] = formatJSString($key) . ':' . formatJSVariant($value);
            }
        }

        return '{' . implode(',', $safe_vals) . '}';
    }
}
if (!function_exists("formatLineWrap")) {
    function formatLineWrap($inString, $width = 70, $newline = "\n")
    {
        $lines     = explode("\n", $inString);
        $outString = '';
        if (count($lines) > 0) {
            $counter = 0;
            foreach ($lines as $chkline) {
                do {
                    $counter++;
                    if ($counter > 0) {
                        $outString .= $newline;
                    }

                    $outString .= substr($chkline, 0, $width);
                    $chkline = substr($chkline, $width);
                } while (strlen($chkline) > 0);
            }

        }
        return $outString;
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
        return '"' . strtr($obj, array("\"" => "\\\"", "\\" => "\\\\", "\/" => "\\\/", "\010" => "\\b", "\014" => "\\f", "\012" => "\\n", "\015" => "\\r", "\011" => "\\t")) . '"';
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
if (!function_exists('formatSQL')) {
    function formatSQL($value)
    {
        if (is_null($value)) {
            return null;
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
        /*
        @todo test that formatSQLString(null) returns "''"

        global $DB;

        return $DB->escape((string)$instring);
        */

        $outstring = "'" . addslashes($instring) . "'";
        $outstring = str_replace(chr(13) . chr(10), '\n', $outstring);
        $outstring = str_replace(chr(13), '\n', $outstring);
        $outstring = str_replace(chr(10), '\n', $outstring);
        return $outstring;
    }
}
if (!function_exists("hecho")) {
    include_once __DIR__ . '/../home_common/utf8.inc.php';
    function hecho($text, $return = false)
    {
        if (utf8ToUnicode($text) === false) {
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
     * @param bool $return
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

if (!function_exists("undo_magicquotes"))
{
    function undo_magicquotes($item)
    {
        if(get_magic_quotes_gpc())
        {
            if(is_array($item))
                if(count($item)>0)
                    foreach($item as $key=>$child)
                        $item[$key]=undo_magicquotes($child);
                else
                    $item=array();
            else
                $item=trim(stripslashes($item));
        }
        return $item;
    }
}

