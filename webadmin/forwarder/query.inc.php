<?php
if (!class_exists('Forwarder_Handler')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';
    class Forwarder_Handler extends GenericSection_Handler
    {
        public $section_name    = 'forwarder';
        public $table_name      = "freight_forwarders";
        public $database_prefix = '`coinop`.';
        public $search_bits     = array('a.id');
        public function formatNumber($id)
        {
            return sprintf('FF#%06d', $id);
        }
        public function getOptionList($table_name)
        {
            global $DB;
            global $_GLOBALS;
            $table       = '';
            $option_list = array();
            switch ($table_name) {
                case 'id':
                    $table = '`coinop`.`freight_forwarders`';
                    $key   = 'id';
                    $value = "name";
                    break;
                case 'ext_fields':
                    return array(
                    );
                    break;
                default:
                    return parent::getOptionList($table_name);
            }
            if ('' != $table) {
                $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $option_list[$row[0]] = $row[1];
                }

            }
            return $option_list;
        }
    }
}

if (!defined('QUERY_FORWARDER')) {
    define('QUERY_FORWARDER', 191204);
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderDelete')) {
    function forwarderDelete($id)
    {
        forwarderSetValue($id, 'active', 'n');
    }
}
if (!function_exists('forwarderFromDatabase')) {
    function forwarderFromDatabase($id)
    {
        $FH = new Forwarder_Handler();
        return $FH->fromDatabase($id);
    }
}
if (!function_exists('forwarderFromPost')) {
    function forwarderFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderGetLock')) {
    function forwarderGetLock($id)
    {
        $FH = new Forwarder_Handler();
        return $FH->getLock($id);
    }
}
if (!function_exists('forwarderGetOptionList')) {
    function forwarderGetOptionList($option)
    {
        $FH = new Forwarder_Handler();
        return $FH->getOptionList($option);
    }
}
if (!function_exists('forwarderGroupCount')) {
    function forwarderGroupCount($list, $fields)
    {
        $FH = new Forwarder_Handler();
        return $FH->groupCount($option);
    }
}
if (!function_exists('forwarderSearch')) {
    function forwarderSearch($criteria)
    {
        $FH = new Forwarder_Handler();
        return $FH->search($criteria);
    }
}
if (!function_exists('forwarderSetLock')) {
    function forwarderSetLock($id, $staff_id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderSort')) {
    function forwarderSort($list, $field)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderToDatabase')) {
    function forwarderToDatabase($forwarder)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderAccountFromDatabase')) {
    function forwarderAccountFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderAccountFromPost')) {
    function forwarderAccountFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderAccountToDatabase')) {
    function forwarderAccountToDatabase($id, $accounts)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderAccountsFromDatabase')) {
    function forwarderAccountsFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderAccountsFromPost')) {
    function forwarderAccountsFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderAccountsToDatabase')) {
    function forwarderAccountsToDatabase($id, $accounts)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderCommentFromDatabase')) {
    function forwarderCommentFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderCommentToDatabase')) {
    function forwarderCommentToDatabase($id, $comment)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderCommentsFromDatabase')) {
    function forwarderCommentsFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderContactFromDatabase')) {
    function forwarderContactFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderContactFromPost')) {
    function forwarderContactFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderContactToDatabase')) {
    function forwarderContactToDatabase($id, $contact)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderContactsFromDatabase')) {
    function forwarderContactsFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderContactsFromPost')) {
    function forwarderContactsFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderContactsToDatabase')) {
    function forwarderContactsToDatabase($id, $contacts)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderQuoteFromDatabase')) {
    function forwarderQuoteFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderQuoteFromPost')) {
    function forwarderQuoteFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderQuoteToDatabase')) {
    function forwarderQuoteToDatabase($id, $warehouse)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderQuotesFromDatabase')) {
    function forwarderQuotesFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderQuotesFromPost')) {
    function forwarderQuotesFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderQuotesToDatabase')) {
    function forwarderQuotesToDatabase($id, $warehouses)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}

//-----------------------------------------------------------------------------

if (!function_exists('forwarderWarehouseFromDatabase')) {
    function forwarderWarehouseFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderWarehouseFromPost')) {
    function forwarderWarehouseFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderWarehouseToDatabase')) {
    function forwarderWarehouseToDatabase($id, $warehouse)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderWarehousesFromDatabase')) {
    function forwarderWarehousesFromDatabase($id)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderWarehousesFromPost')) {
    function forwarderWarehousesFromPost($posted)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
if (!function_exists('forwarderWarehousesToDatabase')) {
    function forwarderWarehousesToDatabase($id, $warehouses)
    {
        die('TODO: ' . __FILE__ . ':' . __LINE__);
    }
}
