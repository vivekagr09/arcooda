<?php

if (!function_exists("getBaseProductTypes")) {
    function getBaseProductTypes()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT id, name FROM product_types WHERE parent=0");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = ucfirst($row['name']);
            }

            $DB->free_result($query);

            $temp = array();
            if ('machines' == $outarray[2]) {
                $temp[2] = '';
            }

            if ('pcbs' == $outarray[3]) {
                $temp[3] = '';
            }

            if ('parts' == $outarray[1]) {
                $temp[1] = '';
            }

            if ('merchandises' == $outarray[4]) {
                $temp[4] = '';
            }

            foreach ($outarray as $key => $value) {
                $temp[$key] = $value;
            }

            $outarray = $temp;
        }
        return $outarray;
    }
}
if (!function_exists('getInvoiceVoltages')) {
    /**
     * @return string[]
     */
    function getInvoiceVoltages()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `voltage` FROM `highwaygroup`.`invoice_voltages` ORDER BY `id` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['voltage'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getOutletFrequencies')) {
    function getOutletFrequencies()
    {
        return array(
            1 => '50/60Hz',
            2 => '50Hz',
            3 => '60Hz',
        );
    }
}
if (!function_exists("getOutletPlugs")) {
    function getOutletPlugs()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `value` FROM `world`.`plug_types` ORDER BY `value` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['value'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists("getPersonalCourierName")) {
    function getPersonalCourierName($id)
    {
        $couriers = array(1 => 'EMS', 2 => 'UPS', 3 => 'TNT', 4 => 'Air Borne', 5 => 'DHL', 6 => 'Federal Express');
        if (array_key_exists($id, $couriers)) {
            return $couriers[$id];
        }

        return '';
    }
}
if (!function_exists("getProductCondition")) {
    function getProductCondition()
    {
        global $DB;

        $result = array();
        $query  = $DB->query("SELECT `id`, `condition` FROM `product_conditions` ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['id']] = $row['condition'];
        }
        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists("getSeaPortsList")) {
    function getSeaPortsList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `country`, `name` FROM `world`.`seaports` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['country']][$row['id']] = $row['name'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getStaffList')) {
    /**
     * @param bool $active_only
     *
     * @return string[]
     */
    function getStaffList($active_only = true)
    {
        global $DB;

        $key = ($active_only) ? 'y' : 'n';

        static $result = [];
        if (array_key_exists($key, $result)) {
            return $result[$key];
        }

        $result = ['n' => [], 'y' => []];
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `first_name`, `last_name`, `active` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC, `last_name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result['n'][$row['id']] = trim(sprintf('%s %s', $row['first_name'], $row['last_name']));
                if ('y' == $row['active']) {
                    $result['y'][$row['id']] = trim(sprintf('%s %s', $row['first_name'], $row['last_name']));
                }
            }
            $DB->free_result($query);
        }

        return $result[$key];
    }
}
if (!function_exists('getStaffName')) {
    function getStaffName($user)
    {
        global $DB;
        
        if ($row = $DB->query_first(sprintf(
            "SELECT CONCAT_WS(' ', `first_name`, `last_name`) AS `staff_name` FROM `highwaygroup`.`users` WHERE `user` = %s LIMIT 1",
            $DB->escape($user)
        ))) {
            return $row['staff_name'];
        }

        return '';
    }
}
if (!function_exists('staffEmailFromDatabase')) {
    function staffEmailFromDatabase($id,$site_id)
    {
        global $DB;
        
        if ($row = $DB->query_first(sprintf(
            "SELECT `email` FROM `highwaygroup`.`users` WHERE `id` = %d LIMIT 1",
            $DB->escape($id)
        ))) {
            return $row['email'];
        }

        return '';
    }
}
if (!function_exists('getSupplierName')) {
    function getSupplierName($supplier_id)
    {
        global $DB;
        $supplier_id = intval($supplier_id);
        if (0 == $supplier_id) {
            return 'None';
        }

        $qry = $DB->query_first("SELECT supplier FROM suppliers WHERE id=$supplier_id");
        return $qry['supplier'];
    }
}
if (!function_exists('getSupplierValues')) {
    function getSupplierValues($supplier_id)
    {
        global $DB;
        return array();
    }
}
if (!function_exists('getTableColumns')) {
    /**
     * @param string $table_name
     *
     * @return string[]
     */
    function getTableColumns($table_name)
    {
        $result = array();
        foreach (getTableFields($table_name) as $k => $v) {
            // @todo Use the string index instead of the numeric index
            $result[$k] = $v[0];
        }

        return $result;
    }
}
if (!function_exists('getTableFields')) {
    function getTableFields($table_name)
    {
        global $DB;

        static $cache = array();
        if (array_key_exists($table_name, $cache)) {
            return $cache[$table_name];
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            $table_name
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_BOTH)) {
                $result[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        $cache[$table_name] = $result;

        return $result;
    }
}
if (!function_exists('getTermsList')) {
    function getTermsList()
    {
        global $DB;
        static $outarray = '';
        if ('' == $outarray) {
            $outarray = array();
            $query    = $DB->query("SELECT id, term FROM shipping_terms ORDER BY term");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $outarray[$row['id']] = ucfirst($row['term']);
            }

            $DB->free_result($query);
        }
        return $outarray;
    }
}
if (!function_exists('getTimeZones')) {
    function getTimeZones()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `country`, `name`, `offset`, `day_light` FROM `world`.`time_zones` ORDER BY `name` ASC"
        ))) {
            while ($row = $DB->fetch_array($query)) {
                $result[$row['country']][] = array(
                    'id'        => $row['id'],
                    'zone'      => $row['name'],
                    'offset'    => $row['offset'],
                    'day_light' => $row['day_light'],
                );
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('mime_extension_type')) {
    function mime_extension_type($fname)
    {
        $ext = strtolower(end(explode('.', trim($fname))));
        if ('' != $ext) {
            foreach (file('/etc/mime.types') as $row) {
                $row = trim($row);
                if ('' != $row && substr($row, 0, 1) != '#') {
                    $row  = str_replace("\t", ' ', $row);
                    $test = '';
                    while ($row != $test) {
                        $test = $row;
                        $row  = str_replace("  ", ' ', $row);
                    }
                    $row       = explode(' ', strtolower($row));
                    $mime_type = array_shift($row);
                    if (in_array($ext, $row)) {
                        return $mime_type;
                    }

                }
            }
        }
        return 'application/octet-stream';
    }
}
if (!function_exists("getActiveInvoiceAddressList")) {
    function getActiveInvoiceAddressList()
    {
        global $DB;

        static $invoiceactiveaddress;
        if (!is_null($invoiceactiveaddress)) {
            return $invoiceactiveaddress;
        }

        $invoiceactiveaddress = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `dropdown_name` FROM `invoice_address` Where `flag` = 0 ORDER BY `dropdown_name` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $invoiceactiveaddress[$row['id']] = $row['dropdown_name'];
            }
            $DB->free_result($query);
        }

        return $invoiceactiveaddress;
    }
}
if (!function_exists("getAllInvoiceAddressList")) {
    function getAllInvoiceAddressList()
    {
        global $DB;

        static $invoiceAlladdress;
        if (!is_null($invoiceAlladdress)) {
            return $invoiceAlladdress;
        }

        $invoiceAlladdress = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `address` FROM `invoice_address`  ORDER BY `id` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $invoiceAlladdress[$row['id']] = $row['address'];
            }
            $DB->free_result($query);
        }

        return $invoiceAlladdress;
    }
}

if (!function_exists("deleteMysqlCacheData")) {
    function deleteMysqlCacheData($table,$id)
    {
       $cacheFile = "/home/arcooda/public_html/protected/cache/mysqlcache/{$table}_{$id}_cache.json";
       $cacheFile = str_replace('`', '', $cacheFile);
       if (file_exists($cacheFile)) {
               unlink($cacheFile);
        }
    }
}
if (!function_exists("addValueInJobHandle")) {
    function addValueInJobHandle($type_id,$action)
    {
       global $DB,$_GLOBALS;
       $DB->query(sprintf(
        "INSERT INTO `highwaygroup`.`job_handle` (`staff_id`, `type_id`, `action`, `site_id`) VALUES (%d, %d, %s, %d)",
            $_GLOBALS["staff"]["id"],
            $DB->escape($type_id),
            $DB->escape($action),
            $DB->escape($_GLOBALS['site_settings']['site_id'])
        ));
    }
}
if (!function_exists("deleteProductCachedImages")) {
    function deleteProductCachedImages($productId, $cacheDir = NULL) {
        // Sanitize input
        if($cacheDir){
            $cacheDir = $_SERVER['DOCUMENT_ROOT'].$cacheDir;
            $productId = basename($productId);

            // Build glob pattern to match all variations
            $pattern = $cacheDir . $productId . '_*x*.jpg';
            // Get all matching cached image files
            $files = glob($pattern);
            // Delete each file
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}