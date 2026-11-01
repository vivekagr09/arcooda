<?php
if (!class_exists("World_Handler")) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';
    class World_Handler extends GenericSection_Handler
    {
        public $section_name    = "world";
        public $table_name      = "country";
        public $database_prefix = "`world`.";
        public $search_bits     = array("`a`.`code`", "`a`.`name`", "`a`.`capital_city`", "`a`.`language`");
        public $primary_key     = "code";

        public function formatNumber($id)
        {
            return sprintf("WO#%06d", $id);
        }
        public function getOptionList($table_name)
        {
            global $DB;
            global $_GLOBALS;
            $table       = "";
            $option_list = array();
            switch ($table_name) {
                case "id":
                case "country":
                    $table = "`world`.`country`";
                    $key   = "`code`";
                    $value = "`name`";
                    break;

                case "language":
                case "languages":
                    $table = "`world`.`language`";
                    //$key = "`id`";
                    $key   = "`name`";
                    $value = "`name`";
                    break;

                case "video_signal":
                    $table = "`world`.`video_signal`";
                    //$key = "`id`";
                    $key   = "`value`";
                    $value = "`value`";
                    break;

                case "voltage":
                    $table = "`world`.`voltage`";
                    //$key = "`id`";
                    $key   = "`value`";
                    $value = "`value`";
                    break;

                case "report_type":
                    return array(
                        "population"     => "Population",
                        "internet_users" => "Internet Users",
                        "dial_code"      => "Country Codes",
                        "internet_code"  => "Internet Codes",
                        "world_times"    => "World Times",
                    );

                case "currency":
                case "list_currency":
                    return getCurrencyList();

                case "all_fields_types":
                    $result                     = parent::getOptionList($table_name);
                    $result["report_type"][1]   = "set('population,internet_users,dial_code,internet_code,world_times')";
                    $result["languages"][1]     = "set(...)";
                    $result["video_signal"][1]  = "set(...)";
                    $result["voltage"][1]       = "set(...)";
                    $result["plugs"][1]         = "set(...)";
                    $result["internet_code"][1] = "set(...)";
                    return $result;

                case "extra_fields":
                case "ext_fields":
                    return array(
                        "first_letter" => "IF(LCASE(LEFT(`a`.`name`, 1)) IN('" . implode("', '", range("a", "z")) . "'), LCASE(LEFT(`a`.`name`, 1)), '_')",
                        //"created_time" => "UNIX_TIMESTAMP(`a`.`created`)",
                        //"modified_time" => "UNIX_TIMESTAMP(`a`.`modified`)",
                        "report_type"  => "'population,internet_users,dial_code,internet_code,world_times'",
                    ); //to do
                    break;

                default:
                    return parent::getOptionList($table_name);
            }
            if ("" != $table) {
                $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $option_list[$row[0]] = $row[1];
                }

            }
            return $option_list;
        }
    }
}
if (!defined("QUERY_WORLD")) {
    define("QUERY_WORLD", 150730);
}
