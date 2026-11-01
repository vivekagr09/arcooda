<?php
if (!defined('CONTRACTOR_ATTACHMENT_TABLENAME')) {
    define('CONTRACTOR_ATTACHMENT_TABLENAME', '`contractors_attachments`');
}
if (!defined('CONTRACTOR_ATTACHMENT_PRIMARYKEY')) {
    define('CONTRACTOR_ATTACHMENT_PRIMARYKEY', 'id');
}
if (!defined('CONTRACTOR_ATTACHMENT_SECONDARYKEY')) {
    define('CONTRACTOR_ATTACHMENT_SECONDARYKEY', 'contractor_id');
}

if (!function_exists('contractorAttachmentDefault')) {
    /**
     * @return mixed[]
     */
    function contractorAttachmentDefault()
    {
        global $DB;

        return contractorAttachmentVirtual($DB->modelDefault(
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorAttachmentDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorAttachmentDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorAttachmentExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorAttachmentExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorAttachmentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorAttachmentFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        )) {
            return contractorAttachmentVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorAttachmentGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorAttachmentGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_ATTACHMENT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_ATTACHMENT_TABLENAME,
                CONTRACTOR_ATTACHMENT_PRIMARYKEY
            );
        }

        if ($model = contractorAttachmentFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorAttachmentHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorAttachmentHasValue($id, $field, $value)
    {
        return $value == contractorAttachmentGetValue($id, $field);
    }
}
if (!function_exists('contractorAttachmentSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorAttachmentSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorAttachmentToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorAttachmentToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorAttachmentVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorAttachmentVirtual($row)
    {
        $attachment_path = contractorGetOptionList('attachment_path') . '/' . $row['filename'];

        $row['time_added'] = strtotime($row['date']);
        $row['date_str']   = date('d/m/Y', $row['time_added']);

        if (is_file($attachment_path)) {
            $row['thumb_url'] = $row['image_url'] = substr($attachment_path, strlen($_SERVER['DOCUMENT_ROOT']));
        }

        return $row;
    }
}

if (!function_exists('contractorAttachmentsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorAttachmentsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_SECONDARYKEY
        );
    }
}
if (!function_exists('contractorAttachmentsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorAttachmentsFromDatabase($id)
    {
        global $DB;

        $result = [];
        foreach ($DB->childrenLoad(
            $id,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY,
            CONTRACTOR_ATTACHMENT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = contractorAttachmentVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('contractorAttachmentsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function contractorAttachmentsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            CONTRACTOR_ATTACHMENT_TABLENAME,
            CONTRACTOR_ATTACHMENT_PRIMARYKEY,
            CONTRACTOR_ATTACHMENT_SECONDARYKEY
        );
    }
}
