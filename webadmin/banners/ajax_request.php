<?php
include_once __DIR__ . '/common.inc.php';

$result = $_REQUEST;

switch ($_REQUEST['action']) {
    case 'bannerSearch':
        $criteria = array();
        foreach ($_REQUEST as $k => $v) {
            if ('action' != $k) {
                $criteria[$k] = $v;
            }
        }
        $result['result'] = bannerSearch($criteria);

        if ('' != $_REQUEST['return_sort']) {
            $result['result'] = bannerSort($result['result']);
        }

        // if ('' != $_REQUEST['return_desc']) {
        //     $result['result'] = array_reverse($result['result']);
        // }

        if ($_REQUEST['return_limit'] > 0) {
            $temp = array_chunk($result['result'], $_REQUEST['return_limit']);
            $result['result'] = reset($temp);
        }

        break;

    case 'bannerFromDatabase':
        $result['result'] = bannerFromDatabase($_REQUEST['id']);
        break;

    case 'bannerGetValue':
        $result['result'] = bannerGetValue($_REQUEST['id'], $_REQUEST['field']);
        break;
}

header('Content-Type: text/javascript');
echo json_encode($result);
