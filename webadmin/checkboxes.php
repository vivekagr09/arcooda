<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........: Xiang Wei ZHUO <c980722@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

$CB_PAD['front'] = 'd';
$CB_PAD['back']  = 'b';

function parseCheckboxes($checkbox, $table)
{
    global $DB, $CB_PAD;

    if ('' == $table) {
        return '';
    }

    $results = $DB->query("SELECT * FROM $table");

    while ($row = $DB->fetch_array($results)) {
        $my_string .= $CB_PAD[front] . (1 == $checkbox[$row[id]] ? $row[id] : '') . $CB_PAD[back];
    }

    return $my_string;
}

function getSearchQuery($checkbox)
{
    global $DB, $CB_PAD;

    if ('' == $table) {
        return '';
    }

}
