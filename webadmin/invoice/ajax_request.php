<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$result = $posted;

switch ($posted['action']) {
    case 'invoiceSearch':
        $criteria = $_GLOBALS['criteria_options']['invoice'];
        foreach ($posted as $k => $v) {
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
        $result['result'] = invoiceSearch($criteria);

        if ('' != $posted['return_sort']) {
            $result['result'] = invoiceSort($result['result']);
        }

        if ('' != $posted['return_desc']) {
            $result['result'] = array_reverse($result['result']);
        }

        if ($posted['return_limit'] > 0) {
            $results_by_page = array_chunk(
                $result['result'],
                $posted['return_limit']
            );
            $result['result'] = reset($results_by_page);
        }

        break;

    case 'invoiceFromDatabase':
        $result['result'] = invoiceFromDatabase($posted['id']);
        break;

    case 'invoiceGetValue':
        $result['result'] = invoiceGetValue($posted['id'], $posted['field']);
        break;
}

header('Content-Type: text/javascript');
jecho($result);
