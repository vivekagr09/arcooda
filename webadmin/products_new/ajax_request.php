<?php
include_once __DIR__ . '/common.inc.php';

$result = $_REQUEST;

switch ($_REQUEST['action']) {
    case 'productSearch':
        $criteria = $_GLOBALS['criteria_options']['products'];
        foreach ($_REQUEST as $k => $v) {
            if ('ignore' == $k) {
                if (is_array($v)) {
                    foreach ($v as $field) {
                        unset($criteria[$field]);
                    }
                }

            } elseif ('action' != $k) {
                $criteria[$k] = $v;
            }

        }

        $item_query = productQuery($criteria);
        if ('' != $_REQUEST['return_sort']) {
            $result['result'] = productSort($item_query);
        } else {
            $result['result'] = productSearch($item_query);
        }

        if ('' != $_REQUEST['return_desc']) {
            $result['result'] = array_reverse($result['result']);
        }

        if ($_REQUEST['return_limit'] > 0) {
            $result['result'] = reset(array_chunk($result['result'], $_REQUEST['return_limit']));
        }

        break;

    case 'productFromDatabase':
        $result['result'] = productFromDatabase($_REQUEST['id']);
        break;

    case 'productDimensionFromDatabase':
        $result['result'] = productDimensionFromDatabase($_REQUEST['id'], 0, true);
        break;

    case 'productsFromDatabase':
        $result['result'] = productsFromDatabase($_REQUEST['id']);
        break;

    case 'productGetValue':
        $result['result'] = productGetValue($_REQUEST['id'], $_REQUEST['field']);
        break;
}

header('Content-Type: text/javascript');
jecho($result);
