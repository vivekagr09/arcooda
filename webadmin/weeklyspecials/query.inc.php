<?php
if (!class_exists('WeeklySpecial_Handler')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';
    class WeeklySpecial_Handler extends GenericSection_Handler
    {
        public $section_name = 'weeklyspecial';
        public $table_name   = "weeklyspecials";
        public $search_bits  = array('a.id', 'a.description');
        public function formatNumber($id)
        {
            return sprintf('WS#%06d', $id);
        }
        public function getOptionList($table_name)
        {
            global $DB;
            $table       = '';
            $option_list = array();
            switch ($table_name) {
                case 'id':
                    $table = 'weeklyspecials';
                    $key   = 'id';
                    $value = "modified";
                    break;
                case 'ext_fields':
                    return array(
                        'modified_time' => 'UNIX_TIMESTAMP(a.modified)',
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

if (!defined('QUERY_WEEKLYSPECIAL')) {
    define('QUERY_WEEKLYSPECIAL', 90209);
}
