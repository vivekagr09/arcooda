<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/class.genericsection_handler.php";

class Freight_Service_Handler extends GenericSection_Handler
{
    public $section_name    = "freight_service";
    public $table_name      = "services";
    public $database_prefix = "`freight3`.";
    public $search_bits     = array("a.id");
    public static function getInstance()
    {
        static $_instance = null;
        if (is_null($_instance)) {
            $_instance = new Freight_Service_Handler;
        }

        return $_instance;
    }
    public function formatNumber($id)
    {
        return sprintf("FO%02d", $id);
    }
    public function getOptionList($table_name)
    {
        global $DB;
        global $_GLOBALS;
        $table    = "";
        $key      = "`id`";
        $value    = "`name`";
        $order_by = null;
        switch ($table_name) {
            case "id":
                $table = $this->database_prefix . "`services`";
                break;

            case 'active':
                return array(
                    'y' => 'Active',
                    'n' => 'Deactive',
                );
                break;

            case "max_rule":
                return array(
                    "satchel"  => "Satchel",
                    "crate"    => "Box or Truck",
                    "cylinder" => "Length / Girth",
                    "girth"    => "Length / Length + Girth",
                );

            case 'product_type':
                return array(
                    'per'   => 'Post-paid',
                    'sat'   => 'Prepaid Satchel',
                    'stick' => 'Prepaid Sticker',
                );

            case "staff_id":
                if (moduleLoad("staff")) {
                    if (class_exists('Staff_Handler')) {
                        $handler = Staff_Handler::getInstance();
                        return $handler->getOptionList("id");
                    }
                }
                if (getTableExists("`staff`")) {
                    $table = "`staff`";
                } else {
                    $table = "`highwaygroup`.`users`";
                    $value = "TRIM(CONCAT_WS(' ', `first_name`, `last_name`))";
                }
                break;

            case "delivery_time_id":
                $table    = $this->database_prefix . "`delivery_times`";
                $order_by = "`id`";
                break;

            case "forwarder_id":
                $table = "`coinop`.`freight_forwarders`";
                break;

            case "country":
            case "country_to":
            case "service_to":
                return getCountryList(false);

            case "service_from":
            case "country_from":
                $key      = "DISTINCT `s`.`service_from`";
                $value    = "`c`.`name`";
                $table    = $this->database_prefix . "`services` AS `s` LEFT JOIN `world`.`country` AS `c` ON `c`.`code` = `s`.`service_from`";
                $order_by = "`c`.`name` ASC";
                break;

            case "currency":
                return getCurrencyList();

            case "service_type":
                return array(
                    "int" => "International",
                    "dom" => "Domestic",
                );

            case "location_id":
                $table = $this->database_prefix . "`locations`";
                $value = "CONCAT(IF(`suburb_cn` != '' AND `suburb` != `suburb_cn`, CONCAT(`suburb`, ' (', `suburb_cn`, ')'), `suburb`), ', ', IF(`state_cn` != '' AND `state` != `state_cn`, CONCAT(`state`, ' (', `state_cn`, ')'), `state`), ', ', `post_code`)";
                break;

            case "tracking":
            case "insurance":
            case "tailgate":
            case "registered":
            case "receipted":
            case "express":
                return array(
                    "n" => "No",
                    "o" => "Optional",
                    "y" => "Included",
                );

            case "ext_fields":
            case "extra_fields":
                return array(
                    "active"        => "IF(" . intval($_GLOBALS['site_settings']['site_id']) . " IN(`a`.`active_id`), 'y', 'n')",
                    "created_time"  => "UNIX_TIMESTAMP(`a`.`created`)",
                    "modified_time" => "UNIX_TIMESTAMP(`a`.`modified`)",
                );
                break;
            case "freight_info":
                return [
                    'y' => 'Yes',
                    'n' => 'No',
                ];
                break;
                
            default:
                return parent::getOptionList($table_name);
        }
        if ("" != $table) {
            if (is_null($order_by)) {
                $order_by = $value;
            }

            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table . " ORDER BY " . $order_by);
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $option_list[$row[0]] = $row[1];
            }

        }
        return $option_list;
    }

    /**
     * Return a list of all locations in a certain country
     */
    public function countryLocationsFromDatabase($country)
    {
        global $DB;

        $where = "`country` = " . $DB->escape($country);
        if (in_array($country, array("CN", "HK", "TW"))) {
            $where = "`country` IN('CN', 'HK', 'TW')";
        }

        $results = array();
        $query   = $DB->query("SELECT `id`, CONCAT(IF(`suburb_cn` != '' AND `suburb` != `suburb_cn`, CONCAT(`suburb`, ' (', `suburb_cn`, ')'), `suburb`), ', ', IF(`state_cn` != '' AND `state` != `state_cn`, CONCAT(`state`, ' (', `state_cn`, ')'), `state`), ', ', `post_code`) FROM " . $this->database_prefix . "`locations` WHERE " . $where . " ORDER BY `suburb` ASC, `state` ASC, `post_code` ASC");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $results[$row[0]] = $row[1];
        }

        $DB->free_result($query);
        return $results;
    }

    //-------------------------------------------------------------------------

    /**
     * Delete one or more zones and their locations, countries and prices from the database
     */
    public function zoneDelete($zone_id)
    {
        global $DB;

        /**
         * Ensure an array of id's is specified
         */
        if (!is_array($zone_id)) {
            $zone_id = preg_split("/[^0-9]+/", $zone_id);
        }

        /**
         * Sanitize id list for database
         */
        foreach ($zone_id as $k => $v) {
            if (0 != $v) {
                $zone_id[$k] = intval($v);
            } else {
                unset($zone_id[$k]);
            }
        }

        if (count($zone_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zones` WHERE `id` IN(" . implode(", ", $zone_id) . ") LIMIT " . count($zone_id));
            $this->zoneCountriesDelete($zone_id);
            $this->zoneLocationsDelete($zone_id);
            $this->zoneRatesDelete($zone_id);
        }
    }

    /**
     * Fetch a single zone from the database
     */
    public function zoneFromDatabase($zone_id, $levels = 0)
    {
        global $DB;
        $result = $DB->query_first("SELECT * FROM " . $this->database_prefix . "`zones` WHERE `id` = " . intval($zone_id) . " LIMIT 1", MYSQLI_ASSOC);
        if (!$result) {
            return $result;
        }

        /**
         * Add some virtual fields
         */
        $result["created_time"] = null;
        if (!is_null($result["created"])) {
            $result["created_time"] = strtotime($result["created"] . " GMT");
        }

        $result["modified_time"] = null;
        if (!is_null($result["modified"])) {
            $result["modified_time"] = strtotime($result["modified"] . " GMT");
        }

        /**
         * Recurse into sub objects if required
         */
        if ($levels > 0) {
            $result["countries"] = $this->zoneCountriesFromDatabase($zone_id, $levels - 1);
            $result["locations"] = $this->zoneLocationsFromDatabase($zone_id, $levels - 1);
            $result["rates"]     = $this->zoneRatesFromDatabase($zone_id, $levels - 1);
        }

        return $result;
    }

    /**
     * Apply posted information to a zone
     */
    public function zoneFromPost($zone_id, $posted)
    {
        global $_GLOBALS;
        global $DB;

        $defaults = $DB->table_defaults($this->database_prefix . "`zones`");

        /**
         * Get the "before changes" zone
         */
        $zone = array();
        if (0 != $zone_id) {
            $zone = $this->zoneFromDatabase($zone_id);
        } else {
            $zone = $defaults;
            $zone["staff_id"]      = $_GLOBALS["staff"]["id"];
            $zone["created_time"]  = time();
            $zone["modified_time"] = null;
            $zone["created"]       = formatDateTime($zone["created_time"]);
            $zone["modified"]      = null;
            unset($zone["id"]);
        }

        /**
         * Build the "after changes" zone
         */
        foreach ($defaults as $k => $v) {
            if (array_key_exists($k, $posted) && $posted[$k] !== $zone[$k]) {
                $zone[$k]              = $posted[$k];
                $zone["staff_id"]      = $_GLOBALS["staff"]["id"];
                $zone["modified_time"] = time();
                $zone["modified"]      = formatDateTime($zone["modified_time"]);
            }
        }

        /**
         * Recurse if found
         */
        if (is_array($posted["countries"])) {
            $zone["countries"] = $this->zoneCountriesFromPost($zone_id, $posted["countries"]);
        }

        if (is_array($posted["locations"])) {
            $zone["locations"] = $this->zoneLocationsFromPost($zone_id, $posted["locations"]);
        }

        if (is_array($posted["rates"])) {
            $zone["rates"] = $this->zoneRatesFromPost($zone_id, $posted["rates"]);
        }

        return $zone;
    }

    /**
     * Save a zone to the database
     */
    public function zoneToDatabase($new_zone)
    {
        global $DB;

        $defaults = $DB->table_defaults($this->database_prefix . "`zones`");

        /**
         * Get the "before changes" zone
         */
        $old_zone = array();
        if (0 != $new_zone["id"]) {
            $old_zone = $this->zoneFromDatabase($new_zone["id"]);
        } else {
            $old_zone = $defaults;
            $old_zone["created_time"]  = time();
            $old_zone["modified_time"] = null;
            $old_zone["created"]       = formatDateTime($old_zone["created_time"]);
            $old_zone["modified"]      = null;
            unset($old_zone["id"]);
        }

        /**
         * Build the list of sql updates
         */
        $update_vals              = array();
        $insert_vals              = array();
        $insert_vals["`created`"] = "NOW()";
        foreach ($new_zone as $k => $v) {
            if (array_key_exists($k, $defaults) && $old_zone[$k] !== $new_zone[$k]) {
                $update_vals[$k]             = "`" . $k . "` = " . secho($v, 1);
                $insert_vals["`" . $k . "`"] = secho($v, 1);
            }
        }

        $update_vals["modified"] = "`modified` = NOW()";
        unset($update_vals["id"]);

        /**
         * Apply the changes
         */
        if (0 != $old_zone["id"]) {
            if (count($update_vals) > 1) {
                $update_vals["modified"] = "`modified` = NOW()";
                $DB->query("UPDATE " . $this->database_prefix . "`zones` SET " . implode(", ", $update_vals) . " WHERE `id` = " . intval($old_zone["id"]) . " LIMIT 1");
            }
        } else {
            $DB->query("INSERT INTO " . $this->database_prefix . "`zones` (" . implode(", ", array_keys($insert_vals)) . ") VALUES (" . implode(", ", $insert_vals) . ")");
            $new_zone["id"] = $DB->insert_id();
        }

        /**
         * Apply recursive stuff
         */
        if (is_array($new_zone["countries"]) && array_key_exists("affected", $new_zone["countries"])) {
            $delete_list = $new_zone["countries"]["affected"];

            if (!is_array($delete_list)) {
                $delete_list = preg_split("/[^0-9]+/", $delete_list);
            }

            foreach ($new_zone["countries"] as $k => $v) {
                if (is_numeric($k) && 0 != $v["id"] && ($key = array_search($k, $delete_list)) !== false) {
                    unset($delete_list[$key]);
                }
            }

            foreach ($delete_list as $v) {
                if (0 != $v) {
                    $this->zoneCountryDelete($v);
                }
            }

            foreach ($new_zone["countries"] as $k => $v) {
                if (is_numeric($k)) {
                    $v["zone_id"] = $new_zone["id"];
                    $this->zoneCountryToDatabase($v);
                }
            }

        }
        if (is_array($new_zone["locations"]) && array_key_exists("affected", $new_zone["locations"])) {
            $delete_list = $new_zone["locations"]["affected"];

            if (!is_array($delete_list)) {
                $delete_list = preg_split("/[^0-9]+/", $delete_list);
            }

            foreach ($new_zone["locations"] as $k => $v) {
                if (is_numeric($k) && 0 != $v["id"] && ($key = array_search($k, $delete_list)) !== false) {
                    unset($delete_list[$key]);
                }
            }

            foreach ($delete_list as $v) {
                if (0 != $v) {
                    $this->zoneLocationDelete($v);
                }
            }

            foreach ($new_zone["locations"] as $k => $v) {
                if (is_numeric($k)) {
                    $v["zone_id"] = $new_zone["id"];
                    $this->zoneLocationToDatabase($v);
                }
            }

        }
        if (is_array($new_zone["rates"]) && array_key_exists("affected", $new_zone["rates"])) {
            $delete_list = $new_zone["rates"]["affected"];

            if (!is_array($delete_list)) {
                $delete_list = preg_split("/[^0-9]+/", $delete_list);
            }

            foreach ($new_zone["rates"] as $k => $v) {
                if (is_numeric($k) && 0 != $v["id"] && ($key = array_search($k, $delete_list)) !== false) {
                    unset($delete_list[$key]);
                }
            }

            foreach ($delete_list as $v) {
                if (0 != $v) {
                    $this->zoneLocationDelete($v);
                }
            }

            foreach ($new_zone["rates"] as $k => $v) {
                if (is_numeric($k)) {
                    $v["zone_id"] = $new_zone["id"];
                    $this->zoneLocationToDatabase($v);
                }
            }

        }

        return $new_zone["id"];
    }

    //-------------------------------------------------------------------------

    /**
     * Fetch all zones for a service from the database
     */
    public function zonesFromDatabase($service_id, $levels = 0)
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT * FROM " . $this->database_prefix . "`zones` WHERE `service_id` = " . intval($service_id) . " ORDER BY `name` ASC");
        while ($result = $DB->fetch_assoc($query)) {
            /**
             * Add some virtual fields
             */
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

            /**
             * Recurse into sub objects if required
             */
            if ($levels > 0) {
                $result["countries"] = $this->zoneCountriesFromDatabase($zone_id, $levels - 1);
                $result["locations"] = $this->zoneLocationsFromDatabase($zone_id, $levels - 1);
                $result["rates"]     = $this->zoneRatesFromDatabase($zone_id, $levels - 1);
            }

            $results[] = $result;
        }
        $DB->free_result($query);

        return $results;
    }

//TODO FROM HERE
    public function zonesFromPost($service_id, $posted)
    {
        $result = $this->zonesFromDatabase($service_id);
        if (is_array($posted) && array_key_exists("affected", $posted)) {
            $result["affected"] = $posted["affected"];
            if (!is_array($results["affected"])) {
                $result["affected"] = preg_split("/[^0-9]+/", $result["affected"]);
            }

            $delete_list = $new_zone["rates"]["affected"];
        }

        foreach ($result as $k => $v) {
            if (is_numeric($k) && in_array($v["id"], $affected)) {
                unset($result[$k]);
            }
        }

        foreach ($posted as $k => $v) {
            if (is_numeric($k)) {
                $zone               = $this->zoneFromPost($v["id"], $v);
                $zone["service_id"] = $service_id;
                $result[]           = $zone;
            }
        }

        return $result;
    }

    public function zonesDifference($zones1, $zones2)
    {

    }

    //-------------------------------------------------------------------------

    public function zoneCountryDelete($zone_country_id)
    {
        global $DB;
        if (!is_array($zone_country_id)) {
            $zone_country_id = array(intval($zone_country_id));
        } else {
            foreach ($zone_country_id as $k => $v) {
                $zone_country_id[$k] = intval($v);
            }
        }

        if (count($zone_country_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_countries` WHERE `id` IN(" . implode(", ", $zone_country_id) . ") LIMIT " . count($zone_country_id));
        }

    }
    public function zoneCountryFromDatabase($zone_country_id)
    {
        global $DB;
        $result = $DB->query_first("SELECT * FROM " . $this->database_prefix . "`zone_countries` WHERE `id` = " . intval($zone_country_id), MYSQLI_ASSOC);
        if ($result) {
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

        }
        return $result;
    }
    public function zoneCountryFromPost($zone_country_id, $posted)
    {
        global $_GLOBALS;
        global $DB;

        $defaults     = $DB->table_defaults($this->database_prefix . "`zone_countries`");
        $zone_country = array();
        if (0 != $zone_country_id) {
            $zone_country = $this->zoneCountryFromDatabase($zone_country_id);
        } else {
            $zone_country = $defaults;
            $zone_country["staff_id"]      = $_GLOBALS["staff"]["id"];
            $zone_country["created_time"]  = time();
            $zone_country["modified_time"] = null;
            $zone_country["created"]       = formatDateTime($zone_country["created_time"]);
            unset($zone_country["id"]);
        }
        foreach ($defaults as $k => $v) {
            if (array_key_exists($k, $posted)) {
                $zone_country[$k]              = $posted[$k];
                $zone_country["staff_id"]      = $_GLOBALS["staff"]["id"];
                $zone_country["modified_time"] = time();
                $zone_country["modified"]      = formatDateTime($zone_country["modified_time"]);
            }
        }

        return $zone_country;
    }
    public function zoneCountryToDatabase($zone_country)
    {
        global $DB;
        $fields = getTableFields($this->database_prefix . "`zone_countries`");
        if (0 == $zone_country["id"]) {
            $insert_vals = array();
            foreach ($zone_country as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    $insert_vals[$k] = secho($v, 1);
                }
            }

            $DB->query("INSERT INTO " . $this->database_prefix . "`zone_countries` (`" . implode("`, `", array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
            return $DB->insert_id();
        } else {
            $prev_zone_country = $this->zoneCountryFromDatabase($zone_country["id"]);
            $update_vals       = array();
            foreach ($zone_country as $k => $v) {
                if (array_key_exists($k, $fields) && $prev_zone_country[$k] != $zone_country[$k]) {
                    $update_vals[$k] = "`" . $k . "` = " . secho($v, 1);
                }
            }

            unset($update_vals["id"]);
            if (count($update_vals)) {
                $update_vals["modified"] = "`modified` = NOW()";
                $DB->query("UPDATE " . $this->database_prefix . "`zone_countries` SET " . implode(", ", $update_vals) . " WHERE `id` = " . intval($zone_country["id"]) . " LIMIT 1");
            }
            return $zone_country["id"];
        }
    }

    //-------------------------------------------------------------------------

    public function zoneCountriesDelete($zone_id)
    {
        global $DB;

        /**
         * Ensure an array of id's is specified
         */
        if (!is_array($zone_id)) {
            $zone_id = preg_split("/[^0-9]+/", $zone_id);
        }

        /**
         * Sanitize id list for database
         */
        foreach ($zone_id as $k => $v) {
            if (0 != $v) {
                $zone_id[$k] = intval($v);
            } else {
                unset($zone_id[$k]);
            }
        }

        /**
         * Execute the deletion
         */
        if (count($zone_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_countries` WHERE `zone_id` IN(" . implode(", ", $zone_id) . ") LIMIT " . count($zone_id));
        }

    }
    public function zoneCountriesFromDatabase($zone_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT * FROM " . $this->database_prefix . "`zone_countries` WHERE `zone_id` = " . intval($zone_id));
        while ($result = $DB->fetch_assoc($query)) {
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

            $results[] = $result;
        }
        $DB->free_result($query);
        return $results;
    }
    public function zoneCountriesFromPost($zone_id, $posted)
    {
        $result = $this->zoneCountriesFromDatabase($zone_id);
        if (!is_array($posted)) {
            return $result;
        }

        $result["affected"] = preg_split("/[^0-9]+/", $posted["affected"]);
        foreach ($result as $k => $v) {
            if (is_numeric($k) && in_array($v["id"], $result["affected"])) {
                unset($result[$k]);
            }
        }

        foreach ($posted as $k => $v) {
            if (is_numeric($k)) {
                $zone_country            = $this->zoneCountryFromPost($v["id"], $v);
                $zone_country["zone_id"] = $zone_id;
                $result[]                = $zone_country;
            }
        }

        return $result;
    }

    //-------------------------------------------------------------------------

    public function zoneLocationDelete($zone_location_id)
    {
        global $DB;
        if (!is_array($zone_location_id)) {
            $zone_location_id = array(intval($zone_location_id));
        } else {
            foreach ($zone_location_id as $k => $v) {
                $zone_location_id[$k] = intval($v);
            }
        }

        if (count($zone_location_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_locations` WHERE `id` IN(" . implode(", ", $zone_location_id) . ") LIMIT " . count($zone_location_id));
        }

    }
    public function zoneLocationFromDatabase($zone_location_id)
    {
        global $DB;
        $result = $DB->query_first("SELECT * FROM " . $this->database_prefix . "`zone_locations` WHERE `id` = " . intval($zone_location_id), MYSQLI_ASSOC);
        if ($result) {
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

        }
        return $result;
    }
    public function zoneLocationFromPost($zone_location_id, $posted)
    {
        global $_GLOBALS;
        global $DB;

        $defaults      = $DB->table_defaults($this->database_prefix . "`zone_locations`");
        $zone_location = array();
        if (0 != $zone_location_id) {
            $zone_location = $this->zoneLocationFromDatabase($zone_location_id);
        } else {
            $zone_location = $defaults;
            $zone_location["staff_id"]      = $_GLOBALS["staff"]["id"];
            $zone_location["created_time"]  = time();
            $zone_location["modified_time"] = null;
            $zone_location["created"]       = formatDateTime($zone_location["created_time"]);
            unset($zone_location["id"]);
        }
        foreach ($defaults as $k => $v) {
            if (array_key_exists($k, $posted)) {
                $zone_location[$k]              = $posted[$k];
                $zone_location["staff_id"]      = $_GLOBALS["staff"]["id"];
                $zone_location["modified_time"] = time();
                $zone_location["modified"]      = formatDateTime($zone_location["modified_time"]);
            }
        }

        return $zone_location;
    }
    public function zoneLocationToDatabase($zone_location)
    {
        global $DB;
        $fields = getTableFields($this->database_prefix . "`zone_locations`");
        if (0 == $zone_location["id"]) {
            $insert_vals = array();
            foreach ($zone_location as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    $insert_vals[$k] = secho($v, 1);
                }
            }

            $DB->query("INSERT INTO " . $this->database_prefix . "`zone_locations` (`" . implode("`, `", array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
            return $DB->insert_id();
        } else {
            $prev_zone_location = $this->zoneLocationFromDatabase($zone_location["id"]);
            $update_vals        = array();
            foreach ($zone_location as $k => $v) {
                if (array_key_exists($k, $fields) && $prev_zone_location[$k] != $zone_location[$k]) {
                    $update_vals[$k] = "`" . $k . "` = " . secho($v, 1);
                }
            }

            unset($update_vals["id"]);
            if (count($update_vals)) {
                $update_vals["modified"] = "`modified` = NOW()";
                $DB->query("UPDATE " . $this->database_prefix . "`zone_locations` SET " . implode(", ", $update_vals) . " WHERE `id` = " . intval($zone_location["id"]) . " LIMIT 1");
            }
            return $zone_location["id"];
        }
    }

    //-------------------------------------------------------------------------

    public function zoneLocationsDelete($zone_id)
    {
        global $DB;

        /**
         * Ensure an array of id's is specified
         */
        if (!is_array($zone_id)) {
            $zone_id = preg_split("/[^0-9]+/", $zone_id);
        }

        /**
         * Sanitize id list for database
         */
        foreach ($zone_id as $k => $v) {
            if (0 != $v) {
                $zone_id[$k] = intval($v);
            } else {
                unset($zone_id[$k]);
            }
        }

        /**
         * Execute the deletion
         */
        if (count($zone_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_locations` WHERE `zone_id` IN(" . implode(", ", $zone_id) . ") LIMIT " . count($zone_id));
        }

    }
    public function zoneLocationsFromDatabase($zone_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT * FROM " . $this->database_prefix . "`zone_locations` WHERE `zone_id` = " . intval($zone_id));
        while ($result = $DB->fetch_assoc($query)) {
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

            $results[] = $result;
        }
        $DB->free_result($query);
        return $results;
    }
    public function zoneLocationsFromPost($zone_id, $posted)
    {
        $result = $this->zoneLocationsFromDatabase($zone_id);
        if (!is_array($posted)) {
            return $result;
        }

        $result["affected"] = preg_split("/[^0-9]+/", $posted["affected"]);
        foreach ($result as $k => $v) {
            if (is_numeric($k) && in_array($v["id"], $result["affected"])) {
                unset($result[$k]);
            }
        }

        foreach ($posted as $k => $v) {
            if (is_numeric($k)) {
                $zone_location            = $this->zoneLocationFromPost($v["id"], $v);
                $zone_location["zone_id"] = $zone_id;
                $result[]                 = $zone_location;
            }
        }

        return $result;
    }

    //-------------------------------------------------------------------------

    public function zoneRateDelete($zone_rate_id)
    {
        global $DB;
        if (!is_array($zone_rate_id)) {
            $zone_rate_id = array(intval($zone_rate_id));
        } else {
            foreach ($zone_rate_id as $k => $v) {
                $zone_rate_id[$k] = intval($v);
            }
        }

        if (count($zone_rate_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_rates` WHERE `id` IN(" . implode(", ", $zone_rate_id) . ") LIMIT " . count($zone_rate_id));
        }

    }
    public function zoneRateFromDatabase($zone_rate_id)
    {
        global $DB;
        $result = $DB->query_first("SELECT * FROM " . $this->database_prefix . "`zone_rates` WHERE `id` = " . intval($zone_rate_id), MYSQLI_ASSOC);
        if ($result) {
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

        }
        return $result;
    }
    public function zoneRateFromPost($zone_rate_id, $posted)
    {
        global $_GLOBALS;
        global $DB;

        $defaults  = $DB->table_defaults($this->database_prefix . "`zone_rates`");
        $zone_rate = array();
        if (0 != $zone_rate_id) {
            $zone_rate = $this->zoneRateFromDatabase($zone_rate_id);
        } else {
            $zone_rate = $defaults;
            $zone_rate["staff_id"]      = $_GLOBALS["staff"]["id"];
            $zone_rate["created_time"]  = time();
            $zone_rate["modified_time"] = null;
            $zone_rate["created"]       = formatDateTime($zone_rate["created_time"]);
            unset($zone_rate["id"]);
        }
        foreach ($defaults as $k => $v) {
            if (array_key_exists($k, $posted)) {
                $zone_rate[$k]              = $posted[$k];
                $zone_rate["staff_id"]      = $_GLOBALS["staff"]["id"];
                $zone_rate["modified_time"] = time();
                $zone_rate["modified"]      = formatDateTime($zone_rate["modified_time"]);
            }
        }

        return $zone_rate;
    }
    public function zoneRateToDatabase($zone_rate)
    {
        global $DB;
        $fields = getTableFields($this->database_prefix . "`zone_rates`");
        if (0 == $zone_rate["id"]) {
            $insert_vals = array();
            foreach ($zone_rate as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    $insert_vals[$k] = secho($v, 1);
                }
            }

            $DB->query("INSERT INTO " . $this->database_prefix . "`zone_rates` (`" . implode("`, `", array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
            return $DB->insert_id();
        } else {
            $prev_zone_rate = $this->zoneRateFromDatabase($zone_rate["id"]);
            $update_vals    = array();
            foreach ($zone_rate as $k => $v) {
                if (array_key_exists($k, $fields) && $prev_zone_rate[$k] != $zone_rate[$k]) {
                    $update_vals[$k] = "`" . $k . "` = " . secho($v, 1);
                }
            }

            unset($update_vals["id"]);
            if (count($update_vals)) {
                $update_vals["modified"] = "`modified` = NOW()";
                $DB->query("UPDATE " . $this->database_prefix . "`zone_rates` SET " . implode(", ", $update_vals) . " WHERE `id` = " . intval($zone_rate["id"]) . " LIMIT 1");
            }
            return $zone_rate["id"];
        }
    }

    //-------------------------------------------------------------------------

    public function zoneRatesDelete($zone_id)
    {
        global $DB;

        /**
         * Ensure an array of id's is specified
         */
        if (!is_array($zone_id)) {
            $zone_id = preg_split("/[^0-9]+/", $zone_id);
        }

        /**
         * Sanitize id list for database
         */
        foreach ($zone_id as $k => $v) {
            if (0 != $v) {
                $zone_id[$k] = intval($v);
            } else {
                unset($zone_id[$k]);
            }
        }

        /**
         * Execute the deletion
         */
        if (count($zone_id)) {
            $DB->query("DELETE FROM " . $this->database_prefix . "`zone_rates` WHERE `zone_id` IN(" . implode(", ", $zone_id) . ") LIMIT " . count($zone_id));
        }

    }
    public function zoneRatesFromDatabase($zone_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT * FROM " . $this->database_prefix . "`zone_rates` WHERE `zone_id` = " . intval($zone_id));
        while ($result = $DB->fetch_assoc($query)) {
            $result["weight_lbs"]   = bcmul($result["weight"], "2.20462262", 3);
            $result["created_time"] = null;
            if (!is_null($result["created"])) {
                $result["created_time"] = strtotime($result["created"] . " GMT");
            }

            $result["modified_time"] = null;
            if (!is_null($result["modified"])) {
                $result["modified_time"] = strtotime($result["modified"] . " GMT");
            }

            $results[] = $result;
        }
        $DB->free_result($query);
        return $results;
    }
    public function zoneRatesFromPost($zone_id, $posted)
    {
        $result = $this->zoneRatesFromDatabase($zone_id);
        if (!is_array($posted)) {
            return $result;
        }

        $result["affected"] = preg_split("/[^0-9]+/", $posted["affected"]);
        foreach ($result as $k => $v) {
            if (is_numeric($k) && in_array($v["id"], $result["affected"])) {
                unset($result[$k]);
            }
        }

        foreach ($posted as $k => $v) {
            if (is_numeric($k)) {
                $zone_rate            = $this->zoneRateFromPost($v["id"], $v);
                $zone_rate["zone_id"] = $zone_id;
                $result[]             = $zone_rate;
            }
        }

        return $result;
    }

    //-------------------------------------------------------------------------


    /**
     * Find hints at which services may provide for the given shipping country
     *
     * @param String  $country_from Source country
     * @param String  $country_to   Destination country
     * @param String  $width        The width of the package (in cm)
     * @param String  $depth        The depth of the package (in cm)
     * @param String  $height       The height of the package (in cm)
     * @param String  $mass         The mass of the package (in kg)
     * @param Boolean $active_only  Include only active services
     *
     * @return Array[]  A list of relevant service results
     */
    public function hintInternational(
        $country_from,
        $country_to,
        $width = '0.0',
        $depth = '0.0',
        $height = '0.0',
        $mass = '0.000',
        $active_only = true
    ) {
        global $_GLOBALS;
        global $DB;

        $country_list = getCountryList(false);

        $volume = bcmul(bcmul($width, $depth, 3), $height, 3);
        $dim1   = array($width, $depth, $height);
        usort($dim1, function ($a, $b) {return bccomp($a, $b, 1);});
        $results = array();
        if ($query = $DB->query(sprintf(
            // Original

            // Modified
            "SELECT `s`.`max_width`, `s`.`max_height`, `s`.`max_depth`, `zr`.`weight` AS `max_weight`, `s`.`forwarder_id`, `ff`.`name` AS `forwarder`, `z`.`service_id`, `s`.`name` AS `service`, `zr`.`zone_id`, `z`.`name` AS `zone`, MIN(`zr`.`cost_price`) AS `zone_price`, `s`.`currency`, `zc`.`delivery_time_id`, `dt`.`name` AS `delivery_time` FROM " . $this->database_prefix . "`zone_rates` AS `zr` INNER JOIN " . $this->database_prefix . "`zones` AS `z` ON `z`.`id` = `zr`.`zone_id` INNER JOIN " . $this->database_prefix . "`zone_countries` AS `zc` ON `zc`.`zone_id` = `z`.`id` LEFT JOIN " . $this->database_prefix . "`delivery_times` AS `dt` ON `dt`.`id` = `zc`.`delivery_time_id` INNER JOIN " . $this->database_prefix . "`services` AS `s` ON `s`.`id` = `z`.`service_id` LEFT JOIN `coinop`.`freight_forwarders` AS `ff` ON `ff`.`id` = `s`.`forwarder_id` WHERE `zr`.`weight` >= %s AND `zr`.`weight` * `s`.`cubic_divisor` >= %s AND `zc`.`country` = %s AND `s`.`service_from` = %s AND (`s`.`max_weight` >= %s AND `s`.`max_weight` * `s`.`cubic_divisor` >= %s)%s GROUP BY `zr`.`zone_id` ORDER BY `zr`.`cost_price` ASC",
            $DB->escape($mass),
            $DB->escape($volume),
            $DB->escape($country_to),
            $DB->escape($country_from),
            $DB->escape($mass),
            $DB->escape($volume),
            ($active_only) ? (" AND FIND_IN_SET(" . $DB->escape($_GLOBALS['site_settings']['site_id']) . ", `s`.`active_id`)") : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $row['country_id'] = $country_to;
                $row['country']    = $country_list[$country_to] ?? $country_to;

                if (bccomp($dim1[0], "0.0", 1) > 0) {
                    $dim2 = array($row['max_width'], $row['max_depth'], $row['max_height']);
                    usort($dim2, function ($a, $b) {return bccomp($a, $b, 1);});
                    if (bccomp($dim2[0], "0.0", 1) > 0) //Box dimensions
                    {
                        foreach (range(0, 2) as $i) {
                            if (bccomp($dim1[$i], $dim2[$i], 1) > 0) {
                                continue (2);
                            }
                        }
                    } elseif (bccomp($dim2[1], "0.0", 1) > 0) //Satchel dimensions
                    {
                        if (bccomp(bcadd($dim1[2], $dim1[0], 1), $dim2[2], 1) > 0) {
                            continue;
                        }
                        if (bccomp(bcadd($dim1[1], $dim1[0], 1), $dim2[1], 1) > 0) {
                            continue;
                        }
                    }
                }
                $results[] = $row;
            }
            $DB->free_result($query);
        }

        return $results;
    }

    /**
     * Find hints at which services may provide for the given shipping country
     *
     * @param String  $country_from Source country
     * @param String  $state        Destination state
     * @param String  $suburb       Destination suburb
     * @param String  $post_code    Destination post_code
     * @param String  $width        The width of the package (in cm)
     * @param String  $depth        The depth of the package (in cm)
     * @param String  $height       The height of the package (in cm)
     * @param String  $mass         The mass of the package (in kg)
     * @param Boolean $active_only  Include only active services
     *
     * @return Array[]  A list of relevant service ids
     */
    public function hintDomestic(
        $country_from,
        $state,
        $suburb,
        $post_code,
        $width = '0.0',
        $depth = '0.0',
        $height = '0.0',
        $mass = '0.000',
        $active_only = true
    ) {
        global $_GLOBALS, $DB;
        $volume = bcmul(bcmul($width, $depth, 3), $height, 3);
        $dim1   = array($width, $depth, $height);
        sort($dim1);
        $results = array();
        while ($row = $DB->fetch_assoc($query)) {
            if (bccomp($dim1[0], "0.0", 1) > 0) {
                $dim2 = array($row['max_width'], $row['max_depth'], $row['max_height']);
                sort($dim2);
                if (bccomp($dim2[0], "0.0", 1) > 0) //Box dimensions
                {
                    foreach (range(0, 2) as $i) {
                        if (bccomp($dim1[$i], $dim2[$i], 1) > 0) {
                            continue (2);
                        }
                    }
                } elseif (bccomp($dim2[1], "0.0", 1) > 0) //Satchel dimensions
                {
                    if (bccomp(bcadd($dim1[2], $dim1[0], 1), $dim2[2], 1) > 0) {
                        continue;
                    }
                    if (bccomp(bcadd($dim1[1], $dim1[0], 1), $dim2[1], 1) > 0) {
                        continue;
                    }
                }
            }
            unset($row['max_width']);
            unset($row['max_depth']);
            unset($row['max_height']);
            $results[] = $row;
        }
        $DB->free_result($query);
        return $results;
    }

    /**
     * Calculate and sort pricing for destination location
     *
     * @param String  $service_list List of hinted services
     * @param String  $value        Value of the goods (for insurance calc)
     * @param String  $currency     Currency of the calculations
     *
     * @return int[]  A list of relevant service ids
     */
    public function calculateServices($service_list, $value = '0.00', $currency = 'USD')
    {
        $results = array();
        foreach ($service_list as $row) {
            $service = $this->fromDatabase($row['service_id']);

            $breakup = array();
            $row['cost_price']   = '0.00';
            $row['online_price'] = '0.00';
            $row['offer_price']  = '0.00';

            /**
             * If service has a max weight, check if our goods fit
             */
           

            /**
             * Add the per-destination pricing
             */
            $row["cost_price"]   = bcadd($row["cost_price"], $row["zone_price"], 2);
            $row["online_price"] = bcadd($row["online_price"], $row["zone_price"], 2);
            $row["offer_price"]  = bcadd($row["offer_price"], $row["zone_price"], 2);

            /**
             * Add the consignment pricing
             */
            $consignment_cost        = $service['consignment_cost'];
            $row["cost_price"]       = bcadd($row["cost_price"], $consignment_cost, 2);
            $row["online_price"]     = bcadd($row["online_price"], $consignment_cost, 2);
            $row["offer_price"]      = bcadd($row["offer_price"], $consignment_cost, 2);
            $row['consignment_cost'] = $consignment_cost;

            /**
             * Add the retail discount
             */
            $retail_discount   = bcmul(bcmul($service['retail_discount'], $row['cost_price'], 2), '0.01', 2);
            $row["cost_price"] = bcsub($row["cost_price"], $retail_discount, 2);
            //$row["online_price"] = bcsub($row["online_price"], $retail_discount, 2);
            //$row["offer_price"]  = bcsub($row["offer_price"], $retail_discount, 2);
            $row['retail_discount'] = $retail_discount;

            /**
             * Add the additional charges
             */
            /*
            $row["cost_price"]   = bcadd($row["cost_price"], $service["other_charge"], 2);
            $row["online_price"] = bcadd($row["online_price"], $service["other_charge"], 2);
            $row["offer_price"]  = bcadd($row["offer_price"], $service["other_charge"], 2);
             */

            /**
             * Add the fuel surcharge
             */
            $fuel_surcharge        = bcmul(bcmul($service["fuel_charge"], $row["cost_price"], 2), "0.01", 2);
            $row["cost_price"]     = bcadd($row["cost_price"], $fuel_surcharge, 2);
            $row["online_price"]   = bcadd($row["online_price"], $fuel_surcharge, 2);
            $row["offer_price"]    = bcadd($row["offer_price"], $fuel_surcharge, 2);
            $row['fuel_surcharge'] = $fuel_surcharge;

            /**
             * Add the insurance value
             */
            /*
            $row["insurance_cost"] = "";
            if ("y" == $service["insurance"]) {
            $row["insurance_cost"] = bcmul(bcsub($value, $service["insurance_cost"], 2), bcmul($service["insurance_percent"], "0.01", 4), 2);
            if (bccomp($row["insurance_cost"], "0.00", 2) == 0) {
            $row["insurance_cost"] = sprintf('Yes (upto $%.2f)', $row["insurance_value"]);
            }
            }
             */

            /**
             * Round the retail price
             */
            /*
            if (bccomp($row["online_price"], bcadd($row["online_price"], "0.00", 0), 2) > 0)
            {
            $old_price = $row["offer_price"];
            $row["online_price"] = bcadd(bcadd($row["online_price"], "0.00", 0), "1.00", 2);
            $row["offer_price"] = bcadd($row["offer_price"], bcsub($row["online_price"], $old_price, 2), 2);
            }
             */

            /**
             * Add the handling fee
             */
            $handling_fee        = bcadd($service["handling_fee"], bcmul(bcmul($service["handling_percent"], $row["cost_price"], 2), "0.01", 2), 2);
            $row["offer_price"]  = bcadd($row["offer_price"], $handling_fee, 2);
            $row['handling_fee'] = $handling_fee;

            /**
             * Round the handling fee
             */
            /*
            if (bccomp($row["offer_price"], bcadd($row["offer_price"], "0.00", 0), 2) > 0)
            {
            $old_price = $row["offer_price"];
            $row["offer_price"] = bcadd(bcadd($row["offer_price"], "0.00", 0), "1.00", 2);
            $handling_fee = bcadd($handling_fee, bcsub($row["offer_price"], $old_price, 2), 2);
            }
             */

            // $row["profit"] = bcsub($row["offer_price"], $row["cost_price"], 2);

            /**
             * Add details to breakup
             */
            $breakup[] = sprintf('Cost: $%.2f %s', $row["cost_price"], $row['currency']);
            if (bccomp($consignment_cost, "0.00", 2) > 0) {
                $breakup[] = sprintf('Consignment Charge: $%.2f %s', $consignment_cost, $row['currency']);
            }

            if (bccomp($handling_fee, "0.00", 2) > 0) {
                $breakup[] = sprintf('Handling Fee: $%.2f %s (%0.1f%%)', $handling_fee, $row['currency'], bcmul("100.0", bcdiv($handling_fee, bcsub($row["offer_price"], $handling_fee, 2), 3), 1));
            }

            if (bccomp($fuel_surcharge, "0.00", 2) > 0) {
                //$breakup[] = sprintf('Fuel Surcharge: $%.2f (%0.1f%%)', $fuel_surcharge, $row["fuel_charge"]);
                $breakup[] = sprintf('Fuel Surcharge: $%.2f %s (%0.1f%%)', $fuel_surcharge, $row['currency'], $service["fuel_charge"]);
            }

            /*
            if (bccomp($row["other_charge"], "0.00", 2) > 0) {
            $breakup[] = sprintf('Additional Charges: $%.2f', $row["other_charge"]);
            }
             */

            //if ($row["s_notes"] != "") $breakup[] = "<br />\n<b>Notes: " . hecho($row["s_notes"], 1) . "</b><br />\n";

            $row['cost_price']   = convertCurrency($row['cost_price'], $row['currency'], $currency);
            $row['offer_price']  = convertCurrency($row['offer_price'], $row['currency'], $currency);
            $row['online_price'] = convertCurrency($row['online_price'], $row['currency'], $currency);
            $row['currency']     = $currency;

            $row["profit"] = bcsub($row["offer_price"], $row["cost_price"], 2);

            $breakup[] = sprintf('<b>Final Price: $%.2f %s</b>', $row["offer_price"], $row['currency']);

            $row["display"] = true;

            $row["break_up"] = implode(", ", $breakup);


            $bifurcation = array($row);
            foreach (array(
                'tailgate'   => 'tailgate_cost',
                'registered' => 'registered_cost',
                'receipted'  => 'receipted_cost',
                'tracking'   => 'tracking_cost',
                'express'    => 'express_cost',
                'insurance'  => 'insurance_cost',
                'freight_notes' => 'freight_notes',
                'sign_on_delivery' => 'sign_on_delivery',
            ) as $set => $get) {
                foreach ($bifurcation as $k => $row) {
                    $row[$set] = $service[$set];
                    if (bccomp($service[$get], '0.00', 2) > 0) {
                        $temp                 = $row;
                        $temp["cost_price"]   = bcadd($temp["cost_price"], $service[$get], 2);
                        $temp["online_price"] = bcadd($temp["online_price"], $service[$get], 2);
                        $temp["offer_price"]  = bcadd($temp["offer_price"], $service[$get], 2);
                        $temp[$set]           = 'y';
                        $bifurcation[]        = $temp;
                        $row[$set]            = 'n';
                    }
                    $bifurcation[$k] = $row;
                }
            }
            foreach ($bifurcation as $row) {
                $row["ship_number"] = sprintf("F%02d", $row["service_id"]) . (("y" == $row["insurance"]) ? "I" : "N") . (("y" == $row["registered"]) ? "R" : "") . (("y" == $row["receipted"]) ? "A" : "") . (("y" == $row["express"]) ? "E" : "");
                $results[]          = $row;
            }
        }

        return $results;
    }

    public function expandServices($service_list)
    {
        foreach ($service_list as $k => $v) {
            $service_list[$k]['staff_display'] = true;
            if ('y' == $v['insurance'] && $v['insurance_cost'] >= 1 && !strpos($v['insurance_cost'], '(')) {
                $new_array                     = $v;
                $new_array['offer_price']      = $new_array['offer_price'] + $new_array['insurance_cost'];
                $new_array['insurance']        = 'y';
                $new_array['staff_display']    = false;
                $service_list[$k]['insurance'] = 'n';
                $service_list[]                = $new_array;
            }
        }
        return $service_list;
    }

}

//@todo temporary include
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/new_freight/query.inc.php';
