<?php
if (!function_exists('destinationSearch')) {
    function destinationSearch($destination)
    {
        global $DB;
        $sql = "SELECT LPAD(Pcode,4,'0') as Pcode, Locality, State FROM post_codes WHERE";
        if (is_numeric($destination)) {
            $sql .= " Pcode=$destination";
        } else {
            $sql .= " Locality LIKE '%$destination%'";
        }

        $sql .= " ORDER BY Locality, PCode, State DESC";
        $res  = $DB->query($sql);
        $list = array();
        while ($row = $DB->fetch_assoc($res)) {
            $list[] = array('Locality' => $row['Locality'], 'State' => $row['State'], 'PCode' => $row['Pcode']);
        }

        $results = array();
        $scores  = array();
        foreach ($list as $key => $output) {
            $str             = implode(', ', $output);
            $score           = levenshtein(strtolower($destination), $str);
            $output['score'] = $score;
            $results[]       = $output;
            $scores[]        = $score;
        }
        array_multisort($scores, $results);
        return $results;
    }
}
if (!function_exists('freightAlphaList')) {
    function freightAlphaList($order_by, $order, $alpha_value = '', $page_url = '', $count_pages = 1, $page = 0)
    {
        global $DB;
        echo '<table class="wide" style="width:98%;"><td bgcolor=#FAFAFA style="text-align: center; width: 83%"><b>By Product Name :</b> ';
        for ($i = 'A';; $i++) {
            $qry = "SELECT * FROM freight_services WHERE name LIKE '$i%'";
            $res = $DB->query($qry);
            if ($DB->num_rows($res) > 0) {
                if ($i == $alpha_value) {
                    echo '<span style="background-color:#7FFFD4">' . $i . '</span>&nbsp;';
                } else {
                    echo '<a href="' . $page_url . '&order_by=' . $order_by . '&order=' . $order . '&by=alpha&alpha_value=' . $i . '">' . $i . '</a>&nbsp;';
                }

            } else {
                echo '<font color="#999999">' . $i . '</font>&nbsp;';
            }

            if ("Z" == $i) {
                break;
            }

        }
        echo '</td>';
        echo '<td bgcolor=#FAFAFA style="text-align:right; width: 17%">';
        $pages = $count_pages;
        $page_url .= "&page=";
        if ($page > 0) {
            echo '[<a style="color:blue" href="' . $page_url . '0">Start</a>][<a style="color:blue" href="' . $page_url . ($page - 1) . '">Back</a>]';
        }

        if ($page < ($pages - 1)) {
            echo '[<a style="color:blue" href="' . $page_url . ($page + 1) . '">Next</a>][<a style="color:blue" href="' . $page_url . ($pages - 1) . '">End</a>]';
        }

        echo '</td>';
        echo '</table>';
        $DB->free_result();
    }
}
if (!function_exists('freightDomesticProducts')) {
    function freightDomesticProducts($destination, $state, $code, $weight, $service_from)
    {
        global $DB;

        $product_list = array();
        $query        = $DB->query("SELECT s.*, d.* FROM `freight_services` s INNER JOIN `freight_services_dom_destinations` d ON s.`id`=d.`product_id` WHERE s.`active`='y' AND s.`location_type`='dom' AND s.`service_from`=" . formatSQLString($service_from));
        while ($row = $DB->fetch_assoc($query)) {
            $product_list[] = $row;
        }

        $DB->free_result($query);

        return $product_list;
    }
}
if (!function_exists('freightExists')) {
    function freightExists($id)
    {
        static $results = array();
        if (array_key_exists($id, $results)) {
            return $results[$id];
        }

        global $DB;
        $row = $DB->query_first("SELECT id FROM freight_services WHERE id=" . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row) {
            return $results[$id] = true;
        }

        return false;
    }
}
if (!function_exists('freightFromDatabase')) {
    function freightFromDatabase($id)
    {
        global $DB;
        if (freightExists($id)) {
            $fields = array('*' => 's.*');
            foreach (freightGetOptionList('ext_fields') as $key => $value) {
                $fields[$key] = $value . " AS " . $key;
            }

            $result = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM freight_services s WHERE s.id=" . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            return $result;
        } else {
            $result = array();
            foreach (freightGetOptionList('all_fields_types') as $key => $row) {
                $result[$key] = $row[4];
            }

            return $result;
        }
    }
}
if (!function_exists('freightInternationalProducts')) {
    function freightInternationalProducts($destination, $service_from)
    {
        global $DB;

        $product_list = array();
        $query        = $DB->query("SELECT s.*, d.* FROM `freight_services` s INNER JOIN `freight_services_int_destinations` d ON s.`id`=d.`product_id` WHERE s.`active`='y' AND s.`location_type`='int' AND s.`service_from`=" . formatSQLString($service_from) . " AND d.`country`=" . formatSQLString($destination));
        while ($row = $DB->fetch_assoc($query)) {
            $product_list[] = $row;
        }

        $DB->free_result($query);

        return $product_list;
    }
}
if (!function_exists('freightCalculatePrices')) {
    function freightCalculatePrices($product_list, $weight, $width = 0.0, $height = 0.0, $depth = 0.0, $goods_currency = '', $goods_value = '')
    {
        //Note: Still need to split products with optional insurance
        global $DB;

        $zone_list  = freightGetOptionList('zone_id');
        $price_list = array();
        foreach ($product_list as $v) {
            $real_weight = $weight;
            if (0.0 != $v['cubic_divisor']) {
                $real_weight = max($real_weight, $width * $height * $depth / $v['cubic_divisor']);
            }

            /**
             * Check that the dimensions are suitable for this product
             */
            $fit_inside = true;
            $temp1      = array($width, $height, $depth);
            $temp2      = array(floatval($v['width']), floatval($v['height']), floatval($v['depth']));
            rsort($temp1);
            rsort($temp2);
            if (0.0 != $temp2[1] && 0.0 == $temp2[2]) {
                $fit_inside = ($temp2[0] > ($temp1[0] - $temp1[2]) && $temp2[1] > ($temp1[1] - $temp1[2]));
            } else {
                $fit_inside = ($temp1[0] < $temp2[0]) && ($temp1[1] < $temp2[1]) && ($temp1[2] < $temp2[2]);
            }

            if ((0.0 == $temp2[0] || $fit_inside) && (0.0 == $v['weight'] || $real_weight <= $v['weight'])) {
                switch ($v['service_type']) {
                    case 'pp':
                    case 'truck':
                    case 'acc':
                    case 'sea':
                    case 'air':
                    case 'sea_air':
                    case 'personal':
                    default:
                        if (0.0 != $v['retail_price'] || 0.0 != $v['cost_price'] || 0.0 != $v['sell_price'] || ($row = $DB->query_first("SELECT price FROM freight_services_zone_prices WHERE zone_id=" . intval($v['zone_id']) . " AND weight>=" . formatSQLString($real_weight) . " ORDER BY price ASC LIMIT 1"))) {
                            if (0.0 == $v['retail_price']) {
                                $v['retail_price'] = convertCurrency($row['price'] * (1.0 + ($v['fuel_percent'] / 100.0)) + $v['fuel_fee'], $v['currency']);
                                $v['cost_price']   = 0.0;
                                $v['sell_price']   = 0.0;
                            }
                            if (0.0 == $v['cost_price']) {
                                $v['cost_price'] = convertCurrency($v['retail_price'] * (1.0 - ($v['retail_discount'] / 100.0)), $v['currency']);
                                $v['sell_price'] = 0.0;
                            }
                            if (0.0 == $v['sell_price']) {
                                $v['sell_price'] = convertCurrency($v['cost_price'] * (1.0 + ($v['handling_percent'] / 100.0)) + $v['handling_fee'], $v['currency']);
                            }
                            $v['profit'] = $v['sell_price'] - $v['cost_price'];

                            $symbol        = getCurrencySymbol($v['currency']);
                            $str           = array();
                            $str[]         = "Break-up in " . $v['currency'] . ":";
                            $str[]         = "Original Cost: " . $symbol . convertCurrency($row['price'], $v['currency']);
                            $str[]         = "Fuel Surcharge: " . $symbol . convertCurrency($v['retail_price'] - $row['price'], $v['currency']);
                            $str[]         = "Retail Discount: " . $symbol . convertCurrency($v['retail_price'] - $v['cost_price'], $v['currency']);
                            $str[]         = "Handling Charges: " . $symbol . convertCurrency($v['sell_price'] - $v['retail_price'], $v['currency']);
                            $str[]         = "Zone: " . $zone_list[$v['zone_id']];
                            $v['break_up'] = implode("\n", $str);
                            $price_list[]  = $v;
                        }
                }
            }

        }
        return $price_list;
    }
}
if (!function_exists('freightGetOptionList')) {
    function freightGetOptionList($table_name)
    {
        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        global $DB;
        $table       = null;
        $key         = null;
        $value       = null;
        $option_list = array();
        switch ($table_name) {
            case 'service_from':
            case 'destination':
            case 'country':
                return getCountryList(false);

            case 'service_type':
                return array('pp' => 'Express - Prepaid', 'truck' => 'Truck Courier', 'acc' => 'Express - Account', 'air' => 'Air Freight Provider', 'sea' => 'Sea Freight Provider', 'sea_air' => 'Air/Sea Freight Provider', 'personal' => 'Personal Courier');

            case 'currency':
                return getCurrencyList(false);

            case 'tracking':
                return array('y' => 'Yes', 'n' => 'No');

            case 'zone_id':
            case 'shipping_zone':
                $table = 'freight_services_zones';
                $key   = 'id';
                $value = 'name';
                break;

            case 'supplier_id':
            case 'forwarder_id':
            case 'freight_forwarder':
                $table = '`coinop`.`freight_forwarders`';
                $key   = 'id';
                $value = 'name';
                break;

            case 'provider_id':
            case 'service_id':
            case 'services':
                $table = "freight_services";
                $key   = 'id';
                $value = 'name';
                break;

            case 'del_time_id':
                $table = "freight_services_delivery_time";
                $key   = "id";
                $value = "del_time";
                break;

            case 'personal_services':
                $table = "freight_services WHERE type='personal'";
                $key   = 'id';
                $value = 'name';
                break;

            case 'prepaid_services':
                $table = "freight_services WHERE type='pp'";
                $key   = 'id';
                $value = 'name';
                break;

            case 'truck_services':
                $table = "freight_services WHERE type='truck'";
                $key   = 'id';
                $value = 'name';
                break;

            case 'sea_services':
                $table = "freight_services WHERE type='sea' OR type='sea_air'";
                $key   = 'id';
                $value = 'name';
                break;

            case 'air_services':
                $table = "freight_services WHERE type='air' OR type='sea_air'";
                $key   = 'id';
                $value = 'name';
                break;

            case 'all_fields':
                return array_merge(freightGetOptionList('base_fields'), freightGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(freightGetOptionList('base_fields_types'), freightGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns('freight_services') as $key) {
                    $fields[$key] = 's.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields('freight_services');
            case 'ext_fields':
            case 'extra_fields':
                return array();
            case 'ext_fields_types':
                return array();
            default:
                trigger_error('freightGetOptionList(' . json_encode($table_name) . ') is undefined');
                break;
        }
        if (!is_null($table)) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('freightGroupCount')) {
    function freightGroupCount($item_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($item_list) || count($item_list) < 1) {
            return $item_list;
        }

        if ('' === $group_by) {
            return count($item_list);
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        if (count($group_by) == 0) {
            return count($item_list);
        }

        $fields      = freightGetOptionList('all_fields');
        $field_types = freightGetOptionList('all_fields_types');
        $field_list  = array('row_count' => 'COUNT(*) AS row_count');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS " . $field;
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }

        $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM freight_services s WHERE id IN(" . implode(", ", $item_list) . ") GROUP BY " . implode(', ', array_keys($group_list)));
        $results = array();
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' == $key) {
                    $key = null;
                }

                $new_pointers = array();
                foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
                    foreach ($pointers as $key1 => $pointer) {
                        if (!isset($pointers[$key1][$key2])) {
                            $pointers[$key1][$key2] = array();
                        }

                        $new_pointers[] = &$pointers[$key1][$key2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                if (is_array($pointers[$pointer_key])) {
                    $pointers[$pointer_key] = 0;
                }

                $pointers[$pointer_key] += $row['row_count'];
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('freightPricingSort')) {
    function freightPricingSort($pricing_list)
    {
        $values = array();
        foreach ($pricing_list as $k => $v) {
            $values[$k] = convertCurrency($v['sell_price'], $v['currency'], 'USD');
        }

        ksort($values);
        return array_intersect_key($pricing_list, $values);
    }
}

if (!function_exists('freightSearch')) {
    function freightSearch($criteria = '')
    {
        global $DB;
        $results    = array();
        $sql        = " SELECT s.id FROM freight_services s";
        $joins      = array();
        $fields     = getTableFields("freight_services");
        $ext_fields = freightGetOptionList('extra_fields');
        $whereAND   = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($fields[$field][1], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . ",s.`" . $field . "`)";
                                }

                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	s.`" . $field . "`=" . formatSQLString($sub_value);
                                }

                            }
                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        if (substr($fields[$field][1], 0, 4) == 'set(') {
                            $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . ",s.`" . $field . "`)";
                        } else {
                            $whereAND[] = "	s.`" . $field . "`=" . formatSQLString($value);
                        }
                    }
                    unset($fields[$field]);
                } elseif (isset($ext_fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	" . $ext_fields[$field] . "=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "	" . $ext_fields[$field] . "=" . formatSQLString($value);
                    }
                    unset($ext_fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }
        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('s.id', 's.name');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '" . (($alpha) ? "" : "%") . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

                if (count($whereOR) > 0) {
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }

            }
        }
        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY s.id ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('freightSearchCouriers')) {
    function freightSearchCouriers($country, $code, $state, $destination, $weight, $goods_value, $currency)
    {
        $forwarders_list = freightGetOptionList('forwarder_id');
        $state           = str_replace('.', '', $state);
        $results         = array();
        if ('HK' == $country) {
            $code = substr('000000' . intval($code), -6);
            if (0 == $code) {
                $code = '------';
            }

            $search_order = array($destination . ' , ' . $code . ' , ' . $state, $destination . ' , , ' . $state, ' , ' . $code . ' , ' . $state, $destination . ' , ' . $code . ' , ', ' , ' . $code . ' , ', $destination . ' , , ');
            foreach ($search_order as $search_for) {
                if (count($postcodes = postcodeSearch($search_for))) {
                    break;
                }
            }

            if (count($postcodes)) {
                $postcode    = reset($postcodes);
                $destination = $postcode['Locality'];
                $state       = $postcode['State'];
                $code        = $postcode['Pcode'];
            }
            foreach (freightCalculatePrices(freightDomesticCalculate($destination, $state, $code, $weight), $weight, $goods_value) as $key => $value) {
                if ('' == $value['currency']) {
                    $value['currency'] = 'HKD';
                }

                $value['Locality']      = $destination;
                $value['State']         = $state;
                $value['Pcode']         = $code;
                $value['Country']       = 'HK';
                $value['supplier_name'] = $forwarders_list[$value['supplier']];
                $results[]              = $value;
            }
        }
        foreach (freightCalculateIntPrices(freightInternationalCalculate($country), $weight, $currency, $goods_value) as $key => $value) {
            if ('' == $value['currency']) {
                $value['currency'] = $currency;
            }

            $value['Country']       = $country;
            $value['supplier_name'] = $forwarders_list[$value['supplier']];
            $results[]              = $value;
        }
        $add_list = array();
        foreach ($results as $key => $value) {
            if ('y' == $value['insurance'] && $value['insurance_cost'] > 0) {
                $value['sell_price'] += $value['insurance_cost'];
                $value['cost_price'] += $value['insurance_cost'];
                $value['retail_price'] += $value['insurance_cost'];
                $value['insurance'] = 'y';
                $add_list[]         = $value;
            }
            if (0.0 != $value['insurance_cost']) {
                $results[$key]['insurance'] = 'n';
                if ($value['insurance_cost'] < 1.0) {
                    unset($results[$key]);
                }

            }
        }
        foreach ($add_list as $value) {
            $results[] = $value;
        }

        $add_list = array();
        foreach ($results as $key => $value) {
            if ($value['tail_gate_charge'] > 0 && $weight > 25) {
                $value['sell_price'] += $value['tail_gate_charge'];
                $value['cost_price'] += $value['tail_gate_charge'];
                $value['retail_price'] += $value['tail_gate_charge'];
                $value['tail_gate'] = 'y';
                $value['notes']     = trim($value['notes'] . "\n" . 'Tail Gate Included');
                $add_list[]         = $value;
            }
            if (0.0 != $value['tail_gate_charge']) {
                $results[$key]['tail_gate'] = 'n';
                if ($value['tail_gate_charge'] < 1.0) {
                    unset($results[$key]);
                }

            }
        }
        foreach ($add_list as $value) {
            $results[] = $value;
        }

        foreach ($results as $key => $value) {
            if (!isset($value['cost_priceus'])) {
                $results[$key]['cost_priceus'] = convertCurrency($value['cost_price'], $value['currency'], 'USD');
            }

            if (!isset($value['sell_priceus'])) {
                $results[$key]['sell_priceus'] = convertCurrency($value['sell_price'], $value['currency'], 'USD');
            }

        }
        $results = freightSortArray($results);
        return $results;
    }
}
if (!function_exists('freightServiceFromDatabase')) {
    function freightServiceFromDatabase($product_id)
    {
        global $DB;
        $product_id = intval($product_id);
        return $DB->query_first("SELECT *, UNIX_TIMESTAMP(last_sold) as last_unix FROM freight_services f LEFT JOIN freight_services_shipping s ON s.product_id=f.id WHERE id=$product_id", MYSQLI_ASSOC);
    }
}
if (!function_exists('freightServiceId')) {
    function freightServiceId($name)
    {
        global $DB;
        if ('' == $name) {
            return 0;
        }

        $row = $DB->query_first("SELECT id FROM freight_services WHERE name LIKE " . formatSQLString("%" . $name . "%"), MYSQLI_ASSOC);
        if ($row) {
            return $row['id'];
        }

        return 0;
    }
}
if (!function_exists('freightSort')) {
    function freightSort($product_list, $order_by = 'id')
    {
        global $DB;
        if (!is_array($product_list) || count($product_list) < 2) {
            return $product_list;
        }

        $sql     = " SELECT s.id FROM freight_services s";
        $choices = array('id' => 's.id', 'name' => 's.name', 'supplier' => 's.supplier', 'country' => 's.country', 'qty' => 's.qty', 'cost_price' => 's.cost_price', 'sell_price' => 's.sell_price', 'status' => 's.active');
        if (isset($choices[$order_by])) {
            $in = '(';
            foreach ($product_list as $id => $value) {
                $in .= ((0 == $id || count($product_list) == $id) ? '' : ', ') . $value;
            }

            $in .= ')';
            $where        = " WHERE s.id IN $in";
            $product_list = array();
            $order        = " ORDER BY " . $choices[$order_by] . " ASC";
            $query        = $DB->query($sql . $where . $order);
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $product_list[] = $row['id'];
            }

        } else {
            asort($product_list);
        }

        return $product_list;
    }
}
if (!function_exists('postcodeSearch')) {
    function postcodeSearch($search_string)
    {
        global $DB;
        $sql     = "SELECT LPAD(Pcode,4,'0') as Pcode, Locality, State FROM post_codes WHERE CONCAT_WS(' , ',Locality,Pcode,State) LIKE " . formatSQLString('%' . str_replace(' ', '%', $search_string) . '%');
        $query   = $DB->query($sql);
        $results = array();
        $scores  = array();
        while ($row = $DB->fetch_assoc($query)) {
            $similar_name = strtolower($row['Locality'] . ' , ' . $row['Pcode'] . ' , ' . $row['State']);
            $row['score'] = levenshtein(strtolower($search_string), $similar_name);
            $results[]    = $row;
            $scores[]     = $row['score'];
        }
        array_multisort($scores, $results);
        return $results;
    }
}
if (!function_exists('freightWarningsSearch')) {
    /**
     * To search warning in database
     *
     * @param String $criteria The criteria to search warnings
     *
     * @return Object[] Returns the result of the query
     */
    function freightWarningsSearch($criteria = '')
    {
        global $DB;
        $results = array();
        $sql     = "SELECT `a`.`id` FROM `highwaygroup`.`freight_warnings` AS `a` ";
        $fields  = getTableColumns('`highwaygroup`.`freight_warnings`');
        foreach ($fields as $k => $v) {
            $fields[$k] = '`a`.`' . $v . '`';
        }

        $whereAND = array("`a`.`active` = 'y'");
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                    }

                    unset($fields[$field]);
                }
            }
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY `a`.`country` ASC, `a`.`created` ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }

        $DB->free_result($query);
        return $results;
    }
}

if (!function_exists('freightWarningFromDatabase')) {
    /**
     * Get a warning from database
     *
     * @param int $id Id for the query
     *
     * @return Object Returns the query result
     */
    function freightWarningFromDatabase($id)
    {
        global $DB;
        return $DB->query_first("SELECT * FROM `highwaygroup`.`freight_warnings` WHERE `id` = " . intval($id), MYSQL_ASSOC);
    }
}
if (!function_exists('freightSortArray')) {
    /**
     * To sort array
     *
     * @param Object[] $list List of product data
     *
     * @return Object[] Returns new list after sorted
     * @return array(
     *     array(
     *         'break_up' => '',
     *         'cost_price' => 1,
     *         'del_time' => '',
     *         'express' => true,
     *         'id' => '',
     *         'insurance_cost' => '',
     *         'insurance' => '',
     *         'name' => '',
     *         'notes' => '',
     *         'profit' => 1,
     *         'receipted' => true,
     *         'registered' => true,
     *         'retail_price' => 1,
     *         'sell_price' => 1,
     *         'ship_number' => '',
     *         'staff_display' => true,
     *         'supplier' => '',
     *         'track' => '',
     *         'type' => '',
     *         ...
     *     ),
     *     ...
     * )
     */
    function freightSortArray($list)
    {
        // Function to sort the prices from cheapest to highest.
        $to_sort_array = array();
        foreach ($list as $id => $array) {
            $to_sort_array[] = array('id' => $id, 'sell_price' => $array['sell_price']);
        }
        $sell_price = array();
        $id         = array();
        foreach ($to_sort_array as $key => $row) {
            $sell_price[$key] = $row['sell_price'];
            $id[$key]         = $row['id'];
        }
        array_multisort($sell_price, SORT_ASC, $id, SORT_ASC, $to_sort_array);
        //print_array($id);
        //print_array($sell_price);
        $new_list = array();
        foreach ($id as $id_val) {
            $new_list[$id_val] = $list[$id_val];
        }

        foreach ($new_list as $k => $v) {
            $new_list[$k]["ship_number"] = sprintf("F%02d", $v["id"]) . ((array_key_exists("insurance", $v) && "y" == $v["insurance"]) ? "I" : "N") . ((array_key_exists("type", $v) && "truck" == $v["type"]) ? "T" : "") . ((array_key_exists("registered", $v) && "y" == $v["registered"]) ? "R" : "") . ((array_key_exists("receipted", $v) && "y" == $v["receipted"]) ? "A" : "") . ((array_key_exists("express", $v) && "y" == $v["express"]) ? "E" : "");
        }

        return $new_list;
    }
}
if (!function_exists('freightCalculateIntPrices')) {
    /**
     * To calculate international prices
     *
     * @param Object[] $list        List of goods data
     * @param float  $weight       Weight of box
     * @param float  $goods_value  Value of goods
     * @param String $service_from The country of service, initial value is "AU"
     *
     * @return Object[] Returns new list after calculating prices
     * @example array(
     *     array(
     *         'break_up' => '',
     *         'cost_price' => 1,
     *         'del_time' => '',
     *         'express' => true,
     *         'fuel_charge' => '',
     *         'handling_fee' => '',
     *         'handling_percent' => '',
     *         'id' => '',
     *         'insurance_cost' => '',
     *         'insurance_percent' => '',
     *         'insurance_value' => '',
     *         'insurance' => '',
     *         'max_weight' => '',
     *         'name' => '',
     *         'notes' => '',
     *         'profit' => 1,
     *         'receipted' => true,
     *         'registered' => true,
     *         'retail_discount' => '',
     *         'retail_price' => 1,
     *         'sell_price' => 1,
     *         'staff_display' => true,
     *         'supplier' => '',
     *         'threshold_amount' => '',
     *         'threshold_value' => '',
     *         'track' => '',
     *         'type' => '',
     *         'weight' => '',
     *         'zone_id' => '',
     *         ...
     *     ),
     *     ...
     * )
     */
    function freightCalculateIntPrices($list, $weight, $currency, $goods_value = '', $service_from = 'AU')
    {
        global $DB;
        $urls = array('pp' => 'express', 'truck' => 'truck', 'acc' => 'account');
        if (!is_array($list)) {
            return $list;
        }

        $new_list = array();
        foreach ($list as $id => $array) {
            foreach ($array as $key => $value) {
                $new_list[$id][$key] = $value;
            }
            $display = true;
            if ('acc' != $array['type']) {
                if ($weight > $array['weight']) {
                    $display = false;
                }

            } else {
                if (bccomp($array["max_weight"], "0.0", 1) > 0 && bccomp($weight, $array["max_weight"], 1) > 0) {
                    $display = false;
                } elseif ($weight <= $array['max_weight']) {
                    //if ($weight < 10) $weight = ceil($weight * 2) / 2;
                    //else $weight = ceil($weight);
                    $row = $DB->query_first("SELECT * FROM highwaygroup.freight_services_zone_prices WHERE zone_no=" . intval($array['zone_id']) . " AND weight>=" . formatSQLString($weight) . " ORDER BY price ASC LIMIT 1", MYSQL_ASSOC);
                    if ($row) {
                        $cost                          = $row['price'] * (1 + ($array['fuel_charge'] / 100)); // The Base Rate + Fuel Charge
                        $new_list[$id]['retail_price'] = ceil($cost); // The retail price is now set.
                        $cost                          = $cost * (1 - ($array['retail_discount'] / 100)); // If we are eligible for any discount...
                        $cost                          = ceil($cost);
                        // Now work out our handling charges..
                        $handling = $array['handling_fee']; // the basic handling charge
                        if (0 != $array['handling_percent']) {
                            $handling += ($cost * $array['handling_percent']) / 100;
                        }
                        // if any handling percent entered.
                        if ($handling > 0) {
                            $handling = ceil($handling);
                        }

                        $sell_price = $cost + $handling; // Selling price should be a sum of the cost price + our handling..
                        // now round the sell price up to the nearest dollar value...
                        $sell_price                  = ceil($sell_price);
                        $new_list[$id]['sell_price'] = $sell_price;
                        $new_list[$id]['cost_price'] = $cost; // round up to the nearest dollar
                        $new_list[$id]['profit']     = $handling; // no fuel surcharge added to the profit...
                        //Now add the Break-up values...
                        $str = "<b>Break-up in AUD :</b>\n";
                        $str .= "Original Cost: $" . sprintf("%.2f", $cost) . ". Retail Discount: {$array['retail_discount']}%. Our Handling Charges: $" . sprintf("%.2f", $handling);
                        $str .= ". Fuel Surcharge: {$array['fuel_charge']}%\n==================================================================================\n";
                        if ('AUD' != $currency) {
                            $str .= "<b>Break-up in $currency :</b>\n";
                            $str .= "Original Cost: $" . sprintf("%.2f", convertCurrency($row['price'], 'AUD', $currency)) . ". Retail Discount: {$array['retail_discount']}%. Our Handling Charges: $" . sprintf("%.2f", convertCurrency($handling, 'AUD', $currency));
                            $str .= ". Fuel Surcharge: {$array['fuel_charge']}%\n==================================================================================\n";
                        }
                        $str .= "Zone: " . getShippingZoneName($array['zone_id']);
                        $new_list[$id]['break_up'] = $str;
                    } else {
                        $display = false;
                    }

                }
            }
            if ($display) {
                if ('' != $goods_value && floatval($goods_value) > 0 && 'y' == $array['insurance']) {
                    // need to convert the goods value to AUD first...
                    $value_goods    = sprintf('%.2f', convertCurrency($goods_value, $currency, 'AUD'));
                    $insurance_cost = '';
                    $unset          = false;
                    if ($value_goods <= $array['insurance_value']) {
                        $new_list[$id]['insurance_cost'] = 0;
                    } elseif ($value_goods <= $array['threshold_value'] && $array['threshold_value'] > 0) {
                        $new_list[$id]['insurance_cost'] = $array['threshold_amount'];
                    } elseif ($array['insurance_percent'] > 0) {
                        $new_list[$id]['insurance_cost'] = sprintf('%.2f', $value_goods * ($array['insurance_percent'] / 100));
                    }

                    /*
                else
                $new_list[$id]['insurance_cost'] = 'Yes (upto $'.$array['insurance_value'].')';
                if($value_goods <= $array['insurance_value'])
                $new_list[$id]['insurance_cost'] = 0;
                elseif($array['insurance_percent'] > 0) {
                if($array['insurance_value'] > 0)
                $value_goods -= $array['insurance_value'];
                $new_list[$id]['insurance_cost'] = sprintf('%.2f',$value_goods*($array['insurance_percent']/100));
                }
                else
                $new_list[$id]['insurance_cost'] = 'Yes (upto $'.$array['insurance_value'].')';
                 */
                }
                $new_list[$id]['display']     = true;
                $new_list[$id]['edit_url']    = "/webadmin/" . $urls[$array['type']] . "/index.php?action=edit&" . (('truck' != $array['type']) ? "product" : "truck") . "_id=" . $array['id'];
                $new_list[$id]['forward_url'] = "/webadmin/forwarders/index.php?action=edit&forwarder_id=" . $array['supplier'];

            } else {
                unset($new_list[$id]);
            }

        }

        //print_array($new_list);
        return $new_list;
    }
}
if (!function_exists('freightInternationalCalculate')) {
    /**
     * To calculate international prices
     *
     * @param String $country
     * @param String $service_from The country of service, initial value is "AU"
     *
     * @return Object[] Returns new list after calculated
     * @example array(
     *     array(
     *           'abn' => '',
     *           'account_no' => '',
     *           'active' => '',
     *           'address' => '',
     *           'alert_enable' => '',
     *           'alert_level' => '',
     *           'alert_recommend' => '',
     *           'book_phone' => '',
     *           'city' => '',
     *           'con_charge' => '',
     *           'cost_price' => '',
     *           'country' => '',
     *           'created' => '',
     *           'cubic' => '',
     *           'currency' => '',
     *           'cut_off' => '',
     *           'date_added' => '',
     *           'del_time' => '',
     *           'depth' => '',
     *           'divisor' => '',
     *           'email' => '',
     *           'fax' => '',
     *           'fuel_charge' => '',
     *           'handling_fee' => '',
     *           'handling_percent' => '',
     *           'height' => '',
     *           'id' => '',
     *           'insurance_percent' => '',
     *           'insurance_value' => '',
     *           'insurance' => '',
     *           'last_sold' => '',
     *           'link_id' => '',
     *           'max_weight' => '',
     *           'min_order' => '',
     *           'mobile' => '',
     *           'modified' => '',
     *           'name' => '',
     *           'notes' => '',
     *           'other_charge' => '',
     *           'other_comments' => '',
     *           'pack_depth' => '',
     *           'pack_height' => '',
     *           'pack_width' => '',
     *           'phone' => '',
     *           'pickup' => '',
     *           'post_code' => '',
     *           'product_id' => '',
     *           'product_type' => '',
     *           'qty' => '',
     *           'retail_discount' => '',
     *           'retail_price' => '',
     *           'sell_price' => '',
     *           'service_type' => '',
     *           'state' => '',
     *           'stock' => '',
     *           'supplier_code' => '',
     *           'supplier' => '',
     *           'tail_gate_charge' => '',
     *           'tail_gate' => '',
     *           'threshold_amount' => '',
     *           'threshold_value' => '',
     *           'total_sales' => '',
     *           'track_website' => '',
     *           'track' => '',
     *           'type' => '',
     *           'website' => '',
     *           'weight' => '',
     *           'width' => '',
     *           'zone_id' => '',
     *       ),
     * )
     */
    function freightInternationalCalculate($country, $service_from = 'AU')
    {
        global $DB, $_GLOBALS;
        $sql = "SELECT id, product_id, del_time, zone_no, notes FROM highwaygroup.freight_services_int_destinations WHERE country=" . formatSQLString($country);
        $res = $DB->query($sql);
        $ids = array();
        while ($row = $DB->fetch_assoc($res)) {
            $ids[$row['product_id']] = array('del_time' => $row['del_time'], 'zone_id' => $row['zone_no'], 'notes' => $row['notes']);
        }
        $new_list = array();
        $sql      = "SELECT * FROM highwaygroup.freight_services f LEFT JOIN highwaygroup.freight_services_shipping s ON s.product_id=f.id";
        $extra    = " AND f.service_type='int' AND f.active='y'";
        foreach ($ids as $id => $array) {
            $qry = $sql . " WHERE f.id=" . intval($id) . $extra;
            $row = $DB->query_first($qry, MYSQL_ASSOC);
            if ($row) {
                $new_list[$id] = $array + $row;
                //echo $qry."<br />";

            }
        }
        //Hack: Disallow Air Mail on Converters.tv
        if (is_array($_GLOBALS) && 9 == $_GLOBALS['site_settings']['site_id']) {
            unset($new_list[36]);
        }

        return $new_list;
    }
}