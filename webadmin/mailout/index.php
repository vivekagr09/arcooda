<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

$section_code = "EMAILS";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once 'email_functions.php';

echo "<br>";
include 'header.inc.php';

if ('reset' == $action) {

    clean_up();
    $statement = "DELETE FROM mailout_tmp_list";

    $DB->query($statement);

    echo "Temporary List Cleared.<br>";

}

$statement = 'SELECT COUNT(*) AS total FROM mailout_tmp_list WHERE response != "Sent"';
$emails    = $DB->query_first($statement);

if ($emails['total'] > 0) {
    include 'list_not_empty.inc.php';
} else {
    include 'list_generator.php';
}
