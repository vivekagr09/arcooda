<?php

if (true == $print) {
    echo '<tr>';
} else {
    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
}
for ($i = 0; $i < sizeof($fields); $i++) {
    $key = key($fields);
    if ('company_id' != $key && 'job_type' != $key && "id" != $key && "user" != $key && "date_added" != $key && 'bonus_ratio' != $key && (false == $print || ("post_code" != $key && "address" != $key))) {
        if (true == $print) {
            echo "<td nowrap class='data' align=\"" . $fields[$key] . "\">";
            echo stripslashes($cols[$key]) . '&nbsp;</td>';
        } else {
            echo "<td nowrap align=\"" . $fields[$key] . "\">";
            echo stripslashes($cols[$key]) . '&nbsp;</td>';
        }
    }

    next($fields);
}
if (has_permission('USERS', $allowed_sections) || $user == $rowuser) {
    if (false == $print) {
        echo '<td nowrap>';
        $type = str_replace(' ', '_', strtolower($cols['type_name']));
        for ($i = 0; $i < sizeof($actions); $i++) {

            if (0 != $i) {
                echo " | ";
            }

            $str = addslashes($actions[$i]);
            eval("\$str = \"$str\";");
            echo $str;

        }
        echo '</td>';
    }
}
echo "</tr>";
