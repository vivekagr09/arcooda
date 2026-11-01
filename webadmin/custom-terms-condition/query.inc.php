<?php

if (!defined('CUSTOMTERMSCONDITION_TABLENAME')) {
    define('CUSTOMTERMSCONDITION_TABLENAME', '`terms_condition_custom_templates`');
}
if (!defined('CUSTOMTERMSCONDITION_PRIMARYKEY')) {
    define('CUSTOMTERMSCONDITION_PRIMARYKEY', 'id');
}


/**
 * Checks whether metatag exists.
 * @param mixed $id
 * @return boolean
 */
function customTemplateExists($id)
{
    global $DB;
    if ($DB->query_first("SELECT `id` FROM  `terms_condition_custom_templates` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC)) {
        return true;
    }

    return false;
}

/**
 * Return array containing row with given id.
 * @param mixed $id
 * @return mixed
 */
function customTemplateFromDatabase($id)
{
    global $DB;
    $id = intval($id);
    if (0 == $id) {
        return;
    }

    $sql = "SELECT *, UNIX_TIMESTAMP(updated_at) as last_modified_unix FROM `terms_condition_custom_templates` WHERE id=$id";
    $row = $DB->query_first($sql, MYSQLI_ASSOC);
    if ($row) {
        return $row;
    }

    return false;
}

/**
 * Add metatag to database.
 * Update existing row or add new.
 * @param array $posted
 * @return mixed
 */
function customTemplateToDatabase($posted)
{
    global $_GLOBALS;
    global $DB;
    $add    = ('do_add' == $posted['action']) ? true : false;
    $id     = intval($posted['id']);
    $before = ($add) ? array() : customTemplateFromDatabase($id);

    $sql = (($add) ? "INSERT INTO " : "UPDATE ") . " `terms_condition_custom_templates`";

    $changes = array();
    $table   = $posted['item'];
    $set     = array();

    if (is_array($table)) {
        foreach ($table as $key => $value) {
            if (!$add) {
                if (htmlspecialchars($DB->escape($before[$key])) != htmlspecialchars($DB->escape($value))) {
                    $changes[$key] = array('before' => $before[$key], 'after' => $value);
                }

            }
            $set[] = $key . '=' . $DB->escape($value);
        }
        if ($add || count($changes)) {
            $set[] = "`created_at` = NOW(), `created_by` = " . $DB->escape($_GLOBALS['staff']['user']);
        }else{
            $set[] = "`updated_at` = NOW(), `updated_by` = " . $DB->escape($_GLOBALS['staff']['user']);
        }

        if (is_array($set) && count($set)) {
            $sql .= " SET " . implode(', ', $set) . (($add) ? '' : " WHERE id=$id");
            $DB->query($sql);
            if ($add) {
                return  $DB->insert_id;
            }

        }
    }

}
function customTemplateDeleteFromDatabase($id){
     global $DB;
        $DB->query(sprintf(
            "DELETE FROM `terms_condition_custom_templates` WHERE `id` = %d LIMIT 1",
            $id
        ));
}

if (!function_exists('customTemplateSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function customTemplateSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CUSTOMTERMSCONDITION_TABLENAME,
            CUSTOMTERMSCONDITION_PRIMARYKEY
        );
    }
}
if (!function_exists('customTemplateGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function customTemplateGetValue($id, $field)
    {
        global $DB;
        if (array_key_exists($field, $DB->table_columns(
            CUSTOMTERMSCONDITION_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CUSTOMTERMSCONDITION_TABLENAME,
                CUSTOMTERMSCONDITION_PRIMARYKEY
            );
        }

        if ($model = customTemplateFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('customTemplateHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function customTemplateHasValue($id, $field, $value)
    {
        return $value == customTemplateGetValue($id, $field);
    }
}
if (!function_exists('customTemplateTotalProduct')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function customTemplateTotalProduct($id)
    {
       global $DB;
       $row = $DB->query_first("SELECT count(*) as total FROM products WHERE terms_condition_custom=".$id." LIMIT 1",MYSQLI_ASSOC);
       return $row['total'];
    }
}

/************************************ End: Site meta Functions ***************************************************************/
