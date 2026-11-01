<?php
if (!defined('MEDIA_PARTNER_NEWSLETTER')) {
    define('MEDIA_PARTNER_NEWSLETTER', '`media_partner_newsletter`');
}
if (!defined('MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY')) {
    define('MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY', 'id');
}
if (!defined('MEDIA_UPDATE_EMAILS')) {
    define('MEDIA_UPDATE_EMAILS', '`media_update_emails`');
}
if (!defined('MEDIA_UPDATE_EMAILS_PRIMARYKEY')) {
    define('MEDIA_UPDATE_EMAILS_PRIMARYKEY', '`id`');
}
if (!defined('MEDIA_UPDATE_EMAILS_SECONDRYKEY')) {
    define('MEDIA_UPDATE_EMAILS_SECONDRYKEY', '`partner_id `');
}

if (!function_exists('mediaPartnedNewsletterFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function mediaPartnedNewsletterFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            MEDIA_PARTNER_NEWSLETTER,
            MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY
        )) {
            return $row;
        }

        return null;
    }
}

if (!function_exists('mediaPartnedNewsletterGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function mediaPartnedNewsletterGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            MEDIA_PARTNER_NEWSLETTER
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                MEDIA_PARTNER_NEWSLETTER,
                MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY
            );
        }

        if ($model = mediaPartnedNewsletterFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}

if (!function_exists('mediaPartnedNewsletterSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function mediaPartnedNewsletterSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            MEDIA_PARTNER_NEWSLETTER,
            MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY
        );
    }
}
if (!function_exists('mediaPartnedNewsletterToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function mediaPartnedNewsletterToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            MEDIA_PARTNER_NEWSLETTER,
            MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY
        );
    }
}

if (!function_exists('mediaPartnedNewsletterDefault')) {
    /**
     * @return mixed[]
     */
    function mediaPartnedNewsletterDefault()
    {
        global $DB;

        return $DB->modelDefault(
            MEDIA_PARTNER_NEWSLETTER,
            MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY
        );
    }
}

if (!function_exists('mediaPartnedNewsletterFromPost')) {
    /**
     * @return mixed[]
     */
    function mediaPartnedNewsletterFromPost($posted)
    {
         global $_GLOBALS;
            $result = mediaPartnedNewsletterFromDatabase($posted['id']) ?? mediaPartnedNewsletterDefault();
            foreach ($posted as $k => $v) {
                if (is_array($v)) {
                    $result[$k] = implode(',', $v);
                } else {
                    $result[$k] = $v;
                }
            }
            $result['created_by'] = $_GLOBALS['staff']['id'];
            return $result;
    }
}
if (!function_exists('mediaUpdateEmailsToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function mediaUpdateEmailsToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            MEDIA_UPDATE_EMAILS,
            MEDIA_UPDATE_EMAILS_PRIMARYKEY
        );
    }
}
if (!function_exists('mediaUpdateEmailsToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function mediaUpdateEmailsToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            MEDIA_UPDATE_EMAILS,
            MEDIA_UPDATE_EMAILS_PRIMARYKEY
        );
    }
}
if (!function_exists('mediaPartnedNewsletterSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function mediaUpdateEmailsSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            MEDIA_UPDATE_EMAILS,
            MEDIA_UPDATE_EMAILS_PRIMARYKEY
        );
    }
}
if (!function_exists('mediaPartnermailinglistFromDatabase')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function mediaPartnermailinglistFromDatabase($update_id)
    {
        global $DB;

        $results = array();
        $query = $DB->query("SELECT `id`, `update_id`, `partner_id`, `email_status` FROM `media_update_emails` WHERE `update_id` = " . intval($update_id));
        while ($row = $DB->fetch_assoc($query)) $results[$row['id']] = $row;
        $DB->free_result($query);
        return $results;
    }
}

if (!function_exists('socialMediaUpdateQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function socialMediaUpdateQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(MEDIA_PARTNER_NEWSLETTER);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = array();
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (1 == count($whereOR)) {
                        $whereAND[] = implode("", $whereOR);
                    } elseif (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('socialMediaUpdateSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaUpdateSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = socialMediaPartnerQuery(array(
                MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY => $where,
            ));
        }

        return socialMediaUpdateSearch($where, $order_by);
    }
    if (!function_exists('socialMediaUpdateSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaUpdateSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = socialMediaUpdateQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            MEDIA_PARTNER_NEWSLETTER_PRIMARYKEY,
            MEDIA_PARTNER_NEWSLETTER,
            (!is_null($where)) ? sprintf(
                " WHERE %s",
                $where
            ) : "",
            (!is_null($order_by)) ? sprintf(
                " ORDER BY `%s`",
                $order_by
            ) : ""
        ));
    }
}
}

