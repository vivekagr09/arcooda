<?php
if (!function_exists('assetTableInfoFromType')) {
    /**
     * @param string $asset_type
     * @param bool   $all
     *
     * @return string[]
     */
    function assetTableInfoFromType($asset_type, $all = false)
    {
        $asset_types = array(
            'invoice'          => 'Invoice Attachments',
            'purchaseorder'    => 'Purchase Order Attachments',
            'softjob'          => 'Software Job Attachments',
            'workmanual_pages' => 'Work Manual Pages',
            'project_pages'    => 'Project Pages',
            'media_partner'    => 'Partner Attachments',
            'warehouse'         => 'Warehouse Attachments',
        );
        if ($all) {
            return $asset_types;
        }

        $avail = array(
            'invoice'             => array(
                'table'         => "`highwaygroup`.`invoice_assets`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/invoice/attachment/',
                'asset_id'      => 'asset_id',
                'owner_id'      => 'invoice_id',
                'staff_id'      => 'staff_id',
                'asset_rank'    => 'asset_rank',
                'asset_date'    => 'asset_date',
                'asset_caption' => 'asset_caption',
                'filename'      => 'file_name',
                'filetype'      => 'file_type',
                'fileorig'      => 'file_orig',
                'filesize'      => 'file_size',
            ),
            'purchaseorder'       => array(
                'table'         => "`purchase_order_assets`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/purchaseorder/attachment/',
                'asset_id'      => 'asset_id',
                'owner_id'      => 'order_id',
                'staff_id'      => 'staff_id',
                'asset_rank'    => 'asset_rank',
                'asset_date'    => 'asset_date',
                'asset_caption' => 'asset_caption',
                'filename'      => 'file_name',
                'filetype'      => 'file_type',
                'fileorig'      => 'file_orig',
                'filesize'      => 'file_size',
            ),
            'project_pages'       => array(
                'table'         => "`highwaygroup`.`project_pages`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/project/page/',
                'asset_id'      => 'page_id',
                'owner_id'      => 'chapter_id',
                'staff_id'      => 'change_staff',
                'asset_rank'    => 'page_rank',
                'asset_date'    => 'change_date',
                'asset_caption' => 'page_content',
                'filename'      => 'image_file',
                'filetype'      => 'image_type',
                'fileorig'      => 'image_orig',
                'filesize'      => 'image_size',
            ),
            'softjob'             => array(
                'table'         => "`highwaygroup`.`softjob_assets`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/softjob/asset/',
                'asset_id'      => 'asset_id',
                'owner_id'      => 'softjob_id',
                'staff_id'      => 'staff_id',
                'asset_rank'    => 'asset_rank',
                'asset_date'    => 'asset_date',
                'asset_caption' => 'asset_caption',
                'filename'      => 'file_name',
                'filetype'      => 'file_type',
                'fileorig'      => 'file_orig',
                'filesize'      => 'file_size',
            ),
             'media_partner' => array(
                'table'         => "`media_partner_assets`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/mediapartner/',
                'asset_id'      => 'asset_id',
                'owner_id'      => 'owner_id',
                'staff_id'      => 'staff_id',
                'asset_rank'    => 'asset_rank',
                'asset_date'    => 'asset_date',
                'asset_caption' => 'asset_caption',
                'filename'      => 'file_name',
                'filetype'      => 'file_type',
                'fileorig'      => 'file_orig',
                'filesize'      => 'file_size',
            ),
            'warehouse' => array(
                'table'         => "`warehouse_attachments`",
                'directory'     => $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/warehouse/',
                'asset_id'      => 'asset_id',
                'owner_id'      => 'warehouse_id',
                'staff_id'      => 'staff_id',
                'asset_rank'    => 'asset_rank',
                'asset_date'    => 'asset_date',
                'asset_caption' => 'asset_caption',
                'filename'      => 'file_name',
                'filetype'      => 'file_type',
                'fileorig'      => 'file_orig',
                'filesize'      => 'file_size',
            ),
        );
        if (isset($avail[$asset_type])) {
            return $avail[$asset_type];
        }

        return false;
    }
}
if (!function_exists('assetExists')) {
    /**
     * @param string $asset_type
     * @param int    $asset_id
     *
     * @return bool
     */
    function assetExists($asset_type, $asset_id)
    {
        global $DB;

        $table_info = assetTableInfoFromType($asset_type);
        if (!$table_info) {
            return false;
        }

        return !!$DB->query_first(sprintf(
            "SELECT %s FROM %s WHERE %s = %d LIMIT 1",
            $table_info['asset_id'],
            $table_info['table'],
            $table_info['asset_id'],
            $asset_id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('assetSearch')) {
    /**
     * @param string $asset_type
     * @param mixed  $criteria
     *
     * @return int[]
     */
    function assetSearch($asset_type, $criteria = '')
    {
        global $DB;

        $table_info = assetTableInfoFromType($asset_type);
        if (!$table_info) {
            return array();
        }

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            $table_info['table']
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $fields[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "`" . $field . "` = " . $DB->escape($sub_value);
                            }
                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "`" . $field . "` = " . $DB->escape($value);
                    }
                    unset($fields[$field]);
                } elseif (is_string($criteria) && '' != $criteria) {
                    $whereAND[] = " " . $table_info['owner_id'] . "  = " . $DB->escape($criteria);
                }
            }
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s%s ORDER BY `%s` DESC",
            $table_info['asset_id'],
            $table_info['table'],
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : "",
            $table_info['asset_id']
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row[$table_info['asset_id']];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('assetGenerateUniqueName')) {
    /**
     * @param string  $asset_type
     * @param mixed[] $asset
     *
     * @return string
     */
    function assetGenerateUniqueName($asset_type, $asset)
    {
        $ext = pathinfo($asset['fileorig'], PATHINFO_EXTENSION);

        return formatFileSafe($asset_type . '_' . $asset['owner_id'] . '_' . $asset['asset_id'] . '.' . $ext);
    }
}
if (!function_exists('assetToCompatible')) {
    /**
     * @param string  $asset_type
     * @param mixed[] $asset
     *
     * @return mixed[]|null
     */
    function assetToCompatible($asset_type, $asset)
    {
        $table_info = assetTableInfoFromType($asset_type);
        if (!$table_info) {
            return null;
        }

        $result = $asset;
        foreach ($table_info as $key1 => $key2) {
            if ('table' != $key1 && 'directory' != $key1) {
                unset($result[$key1]);
            }
        }

        foreach ($table_info as $key1 => $key2) {
            if ('table' != $key1 && 'directory' != $key1 && array_key_exists($key1, $asset)) {
                $result[$key2] = $asset[$key1];
            }
        }

        return $result;
    }
}
if (!function_exists('assetFromCompatible')) {
    /**
     * @param string  $asset_type
     * @param mixed[] $asset
     *
     * @return mixed[]|false
     */
    function assetFromCompatible($asset_type, $asset)
    {
        $table_info = assetTableInfoFromType($asset_type);
        if (!$table_info) {
            return false;
        }

        $result = $asset;
        foreach ($table_info as $key1 => $key2) {
            if ('table' != $key1 && 'directory' != $key1) {
                unset($result[$key2]);
            }
        }

        foreach ($table_info as $key1 => $key2) {
            if ('table' != $key1 && 'directory' != $key1 && array_key_exists($key2, $asset)) {
                $result[$key1] = $asset[$key2];
            }
        }

        return $result;
    }
}
if (!function_exists('assetFromDatabase')) {
    /**
     * @param string $asset_type
     * @param int    $asset_id
     *
     * @return mixed[]|false
     */
    function assetFromDatabase($asset_type, $asset_id)
    {
        global $DB;

        $table_info = assetTableInfoFromType($asset_type);
        if (!$table_info) {
            return false;
        }

        $sql = "SELECT *,UNIX_TIMESTAMP(" . $table_info['asset_date'] . ") AS asset_time FROM " . $table_info['table'] . " WHERE " . $table_info['asset_id'] . "=" . intval($asset_id) . " LIMIT 1";
        $row = $DB->query_first($sql, MYSQLI_ASSOC);

        if (!$row) {
            $row   = array();
            $query = $DB->query("SHOW COLUMNS FROM " . $table_info['table']);
            while ($field = $DB->fetch_array($query, MYSQLI_NUM)) {
                $row[$field[0]] = $field[2];
            }

            $DB->free_result($query);
            $row['asset_time'] = 0;
            $row['staff_id']   = staffFromUser($row['staff_id']);
        }
        $row['asset_type'] = $asset_type;

        $row = assetFromCompatible($asset_type, $row);
        return $row;
    }
}
if (!function_exists('assetFromPost')) {
    /**
     * @param string  $asset_type
     * @param mixed[] $posted
     *
     * @return mixed[]
     */
    function assetFromPost($asset_type, $posted)
    {
        global $_GLOBALS;

        $asset_id = $posted['asset_id'];

        $old_vals = assetFromDatabase($asset_type, $asset_id);
        $new_vals = $old_vals;

        foreach ($old_vals as $key => $value) {
            if (isset($posted[$key])) {
                $new_vals[$key] = $posted[$key];
            }
        }

        if ($asset_id < 1) {
            $new_vals['asset_time'] = time();
            $new_vals['asset_date'] = formatDateTime();
            $new_vals['staff_id']   = $_GLOBALS['staff']['id'];
            unset($new_vals['asset_id']);
        }

        return $new_vals;
    }
}
if (!function_exists('assetToDatabase')) {
    /**
     * @param string  $asset_type
     * @param mixed[] $new_asset
     *
     * @return int
     */
    function assetToDatabase($asset_type, $new_asset)
    {
        global $DB;

        $table_info = assetTableInfoFromType($asset_type);
        if (false == $table_info) {
            return;
        }

        $asset_id  = intval($new_asset['asset_id']);
        $old_asset = assetFromDatabase($asset_type, $asset_id);

        $new_asset['asset_time'] = time();
        $new_asset['asset_date'] = formatDateTime();

        $new_asset = array_intersect_key($new_asset, $old_asset);
        foreach ($old_asset as $key => $value) {
            if ($new_asset[$key] === $value) {
                unset($new_asset[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM " . $table_info['table']);
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $new_asset = assetToCompatible($asset_type, $new_asset);

        if (count($new_asset) > 0) {
            if (assetExists($asset_type, $asset_id)) {
                $set_vals = array();
                foreach ($new_asset as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($new_asset[$key])) {
                            $set_vals[$key] = $key . "=" . $DB->escape($value);
                        } else {
                            $set_vals[$key] = $key . "=NULL";
                        }
                    }
                }

                $sql = "UPDATE " . $table_info['table'] . " SET " . implode(', ', $set_vals) . " WHERE " . $table_info['asset_id'] . "=" . $asset_id . " LIMIT 1";
                $DB->query($sql);
            } else {
                $set_vals = array();
                foreach ($new_asset as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($new_asset[$key])) {
                            $set_vals[$key] = $DB->escape($value);
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $sql      = "INSERT INTO " . $table_info['table'] . "(" . implode(', ', array_keys($set_vals)) . ") VALUES(" . implode(", ", $set_vals) . ")";
                $query    = $DB->query($sql);
                $asset_id = $DB->insert_id();
            }
        }
        return $asset_id;
    }
}
