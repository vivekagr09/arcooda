<?php
if (!function_exists("invoiceSurveyToDatabase")) {
    /**
     * @param int $invoice_id
     * @param string $link
     *
     * @return void
     */
    function invoiceSurveyToDatabase($invoice_id, $link)
    {
        global $DB;

        if ("" != $link) {
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`invoice_survey` (`invoice_id`, `link`) VALUES (%d, %s)",
                $invoice_id,
                formatSQLString($link)
            ));
        }
    }
}
if (!function_exists("invoiceSurveyFromDatabase")) {
    /**
     * @param int $invoice_id
     *
     * @return mixed[][]
     */
    function invoiceSurveyFromDatabase($invoice_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`invoice_survey` WHERE `invoice_id` = %d",
            $invoice_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists("invoiceSurveyFromPost")) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed
     */
    function invoiceSurveyFromPost($posted)
    {
        $data = $posted['survey'];

        return $data;
    }
}
