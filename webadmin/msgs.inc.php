<?php

function striReplace($find, $replace, $string)
{
    // Case Independent str_replace
    if (!is_array($find)) {
        $find = array($find);
    }

    if (!is_array($replace)) {
        if (!is_array($find)) {
            $replace = array($replace);
        } else {
            // this will duplicate the string into an array the size of $find
            $c       = count($find);
            $rString = $replace;
            unset($replace);
            for ($i = 0; $i < $c; $i++) {
                $replace[$i] = $rString;
            }
        }
    }
    foreach ($find as $fKey => $fItem) {
        $between = explode(strtolower($fItem), strtolower($string));
        $pos     = 0;
        foreach ($between as $bKey => $bItem) {
            $between[$bKey] = substr($string, $pos, strlen($bItem));
            $pos += strlen($bItem) + strlen($fItem);
        }
        $string = implode($replace[$fKey], $between);
    }
    return ($string);
}

function getMsg($msg_id)
{
    global $DB;
    $retarr = $DB->query_first("SELECT string FROM user_msg_strings WHERE id='$msg_id'");
    return $retarr['string'];
}
