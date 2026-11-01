<?php
if (!function_exists('invoiceEmailTemplatesList')) {
    /**
     * @return array{template_id:int, template_name:string, template_subject:string, template_text:string}[]
     */
    function invoiceEmailTemplatesList()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `template_id`, `template_name`, `template_subject`, `template_text` FROM `highwaygroup`.`email_templates` WHERE FIND_IN_SET('invoice', `template_group`)"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['template_id']] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists("invoiceEmailFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[]|false
     */
    function invoiceEmailFromDatabase($id)
    {
        global $DB;

        return $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`invoice_emails` WHERE `id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists("invoiceEmailToDatabase")) {
    /**
     * @param int     $invoice_id
     * @param mixed[] $content
     *
     * @return void
     */
    function invoiceEmailToDatabase($invoice_id, $content)
    {
        global $DB;
        if (0 != $content["id"]) {
            $DB->query(sprintf("UPDATE `highwaygroup`.`invoice_emails` SET `modified` = NOW(), `content` = %s WHERE `id` = %d LIMIT 1", formatSQLString($content), $invoice_id));
        } else {
            $DB->query(sprintf("INSERT INTO `highwaygroup`.`invoice_emails` (`invoice_id`, `content`, `created`) VALUES (%d, %s, NOW())", $invoice_id, formatSQLString($content)));
        }
    }
}
if (!function_exists("invoiceEmailsFromDatabase")) {
    /**
     * @param int $id
     *
     * @return int[]
     */
    function invoiceEmailsFromDatabase($id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id` FROM `highwaygroup`.`invoice_emails` WHERE `invoice_id` = %d ORDER BY `id` ASC",
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
