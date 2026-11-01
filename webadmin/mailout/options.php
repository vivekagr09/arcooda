<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

$results = $DB->query("SELECT * FROM mailout_options");
while ($item = $DB->fetch_array($results)) {
    $MAILOUT_OPTIONS[$item['options']] = $item['value'];
}
$DB->free_result($results);

//===========================================================================
// get the global options, if it doesn't exists return null
//
// PARAMS: the option value wanted
// RETURN: null if not found, else returnt the value
//===========================================================================
function mailout_options($option)
{
    global $MAILOUT_OPTIONS;
    if (isset($MAILOUT_OPTIONS[$option])) {
        return $MAILOUT_OPTIONS[$option];
    } else {
        return null;
    }
}
