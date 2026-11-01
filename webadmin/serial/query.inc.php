<?php
if (!defined('SERIAL_TABLENAME')) {
	define('SERIAL_TABLENAME', '`highwaygroup`.`serial_numbers`');
}
if (!defined('SERIAL_PRIMARYKEY')) {
	define('SERIAL_PRIMARYKEY', 'id');
}
if (!class_exists('Serial_Handler')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';

    class Serial_Handler extends GenericSection_Handler {
        /** @var string */
        public $section_name = 'serial';

        /** @var string */
        public $table_name = 'serial_numbers';

        /** @var string */
        public $database_prefix = '`highwaygroup`.';

        /** @var string[] */
        public $search_bits = ['`a`.`id`', '`a`.`serial_id`', '`a`.`product_str`', '`a`.`invoice_id`'];

        /** @var string */
        public $primary_key = SERIAL_PRIMARYKEY;

        /**
         * @param mixed[] $item
         *
         * @return mixed[]
         */
        public function virtualFields($item)
        {
            moduleLoad('invoice'); //invoiceFromDatabase
            moduleLoad('product'); //productFromDatabase

            if ('' !== $item['product_id'] && moduleLoad('product')) {
                $product                = productFromDatabase($item['product_id']);
                $item['product_number'] = $product['product_number'];
                $item['product_name']   = $product['product_name'];
            }

            $item['bill_address_full'] = '';
            $item['invoice_number']    = '';
    
            if (0 != $item['invoice_id'] && moduleLoad('invoice')) {
                $invoice                   = invoiceFromDatabase($item['invoice_id']);
                $item['bill_address_full'] = $invoice['bill_address_full'];
                $item['invoice_number']    = $invoice['invoice_number'];
                $item['member_id']         = $invoice['member_id'];

                $item['bill_name'] = $invoice['bill_name'];
                if ('' != $invoice['bill_company']) {
                    $item['bill_name'] = $invoice['bill_company'];
                }

                $item['time_to_use'] = ($invoice['proforma_time'] > 0) ? $invoice['proforma_time'] : $invoice['open_time'];
                if (in_array($invoice['invoice_status'], ['inquiry', 'closed'])) {
                    $item['time_to_use'] = time();
                }
            }
            $item['sold']          = (0 != $item['invoice_id']) ? 'y' : 'n';
            $item['serial_number'] = $this->formatNumber($item['serial_id']);

            return $item;
        }

        /**
         * @param int $id
         *
         * @return string
         */
        public function formatNumber($id)
        {
            return sprintf('SN#%06d', $id);
        }

        /**
         * @param int $id
         *
         * @return mixed[]
         */
        public function fromDatabase($id,$randnum=0)
        {
            $result = parent::fromDatabase($id);

            if (0 == $id) {
                $result['serial_id'] = $this->getNextSerial($randnum);
            }
            return $this->virtualFields($result);
        }
         public function toDatabase($item)
        {
            global $DB;
            $id      = intval($item[$this->primary_key]);
            $fields  = $this->getOptionList('base_fields_types');

            if ($this->exists($id)) {
                $set_vals = array();
                foreach ($item as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (is_array($item[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape(implode(",", $item[$key]));
                        } elseif (isset($item[$key])) {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape($item[$key]);
                        } else {
                            $set_vals[$key] = "`" . $key . "` = ''";
                        }

                    }
                }

                unset($set_vals['timestamp']);
                if (count($set_vals)) {
                    if (array_key_exists("modified", $fields) && !array_key_exists("modified", $set_vals)) {
                        $set_vals["modified"] = "`modified` = NOW()";
                    }

                    $sql = "UPDATE " . $this->database_prefix . "`" . $this->table_name . "` SET " . implode(', ', $set_vals) . " WHERE `" . $this->primary_key . "` = " . $id . " LIMIT 1";
                    $DB->query($sql);
                }
                return $id;
            } else {
                $insert_vals = array();
                foreach ($item as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (is_array($item[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                            $insert_vals[$key] = $DB->escape(implode(",", $item[$key]));
                        } elseif (isset($item[$key])) {
                            $insert_vals[$key] = $DB->escape($value);
                        } else {
                            $insert_vals[$key] = "''";
                        }

                    }
                }

                unset($insert_vals['timestamp']);
                if (count($insert_vals)) {
                    if (array_key_exists("created", $fields) && !array_key_exists("created", $insert_vals)) {
                        $insert_vals["created"] = "NOW()";
                    }

                    $sql = "INSERT INTO " . $this->database_prefix . "`" . $this->table_name . "` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
                    $set_vals=[];
                    foreach ($insert_vals as $key => $value) {
                        $set_vals[$key] = "`" . $key . "` = " .$value;
                    }
                    if (count($insert_vals)) {
                        $sql .= " ON DUPLICATE KEY UPDATE " . implode(", ", $set_vals);
                    }
                    $DB->query($sql);
                    return $DB->insert_id();
                }
            }
            return $id;
        }
        /**
         * @param mixed[] $posted
         *
         * @return mixed[]
         */
        public function fromPost($posted)
        {
            return $this->virtualFields(parent::fromPost($posted));
        }

        /**
         * @return int
         */
        public function getNextSerial($reserve_by=0)
        {
            global $DB;
             
            if ($row = $DB->query_first(sprintf(
                "SELECT serial_id,id FROM %s`%s` where `product_id`=0 AND `member_id`=0 AND `invoice_id`=0 AND `reserve_by` != '".$reserve_by."' LIMIT 1",
                $this->database_prefix,
                $this->table_name
                 ), MYSQLI_ASSOC)) {
                     $DB->query(sprintf(
                    "UPDATE  %s`%s` set  `reserve_by`='".$reserve_by."' WHERE id= %d LIMIT 1",
                    $this->database_prefix,
                    $this->table_name,
                    $row['id']
                     ));
                return $row['serial_id'];
            }
            if ($row = $DB->query_first(sprintf(
                "SELECT MAX(`serial_id`) AS `max_serial` FROM %s`%s` LIMIT 1",
                $this->database_prefix,
                $this->table_name
            ), MYSQLI_ASSOC)) {
                return max(8000, $row['max_serial'] + 1);
            }

            return 8000;
        }

        /**
         * @param string $table_name
         *
         * @return mixed
         */
        public function getOptionList($table_name)
        {
            global $DB;

            $table       = '';
            $option_list = [];
            switch ($table_name) {
                case 'id':
                    $table = SERIAL_TABLENAME;
                    $key   = sprintf("`%s`", SERIAL_PRIMARYKEY);
                    $value = "`name`";
                    break;

                case 'staff_id':
                    return getStaffList();

                case 'extra_fields':
                case 'ext_fields':
                    return [
                        'created_time'  => "UNIX_TIMESTAMP(`a`.`created`)",
                        'modified_time' => "UNIX_TIMESTAMP(`a`.`modified`)",
                    ];

                default:
                    return parent::getOptionList($table_name);
            }
            if ('' != $table) {
                if ($query = $DB->query(sprintf(
                    "SELECT %s, %s FROM %s",
                    $key,
                    $value,
                    $table
                ))) {
                    while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                        $option_list[$row[0]] = $row[1];
                    }
                    $DB->free_result($query);
                }
            }
            return $option_list;
        }
    }
}
if (!defined('QUERY_SERIAL')) {
    define('QUERY_SERIAL', 160309);
}
