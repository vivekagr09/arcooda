<?php
include_once __DIR__ . '/common.inc.php';

function create_report()
{
    global $DB, $what, $value, $order, $by, $query, $max, $at, $page, $script;
    global $status, $website, $print, $summary, $action, $user, $limit;
    $fields = array('country' => 'center', $what => 'center');
    $start  = $_REQUEST['start'];
    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    settype($limit, "float");
    if ("" == $order) {
        $order = "desc";
    }

    if ("" == $by) {
        if ("" == $what) {
            $by = $value;
        } else {
            $by = $what;
        }

    }
    if ("country" == $by) {
        $by = "name";
    }

    $str = "";
    if ("" == $what) {
        $fields = array('country' => 'center', $value => 'center');
        $str    = sprintf(
            "SELECT `name`, %s FROM `world`.`country` ORDER BY %s %s LIMIT %d, %d",
            $value,
            $by,
            $order,
            $start,
            $limit
        );
    } else {
        $tmp = $value . (('SECAM K' == $value) ? '' : '%');
        $str = sprintf(
            "SELECT * FROM `world`.`country` WHERE %s LIKE %s ORDER BY %s %s LIMIT %d, %d",
            $what,
            $DB->escape('%' . $tmp),
            $by,
            $order,
            $start,
            $limit
        );
    }
    //echo "String to be executed = $str<br />";
    if ("name" == $by) {
        $by = "country";
    }

    draw_header_row_report($fields);
    $res    = $DB->query($str);
    $shaded = true;
    while ($row = $DB->fetch_array($res)) {
        // print each of the rows of the array..
        if (!$shaded) {
            echo draw_row(false);
            $shaded = !$shaded;
        } else {
            echo draw_row(true);
            $shaded = !$shaded;
        }
        echo '<td align="center">' . $row['name'] . '</td>';
        if ("" == $what) {
            echo '<td align="center">' . $row[$value] . '</td>';
        } else {
            echo '<td align="center">' . format_str_sum($row[$what]) . '</td>';
        }

        echo '</tr>';
    }
    echo '</table>';
    display_entries_main($start, $limit);
}

function country()
{
    global $DB;
    global $what, $value, $order, $by, $limit, $allowed_sections;
    if ('search' == $what) {
        echo '<div align="center">Searching in the World Summary for "<b>' . $value . '</b>"</div>';
    }

    $fields['country']      = 'center';
    $fields['capital_city'] = 'center';
    $fields['time']         = 'center';
    $fields['population']   = 'center';
    $fields['languages']    = 'center';
    if ("" == $by || "country" == $by) {
        $by = "name";
    }

    if ("" == $order) {
        $order = "asc";
    }

    if ("time" == $by) {
        $by   = "name";
    }
    if ("capital" == $by) {
        $by = "capital_city";
    }

    $start = $_REQUEST['start'];
    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    settype($limit, "float");

    if ("" == $order) {
        $order = "desc";
    }

    if ("" == $by || "country" == $by) {
        $by = "code";
    }

    if ('search' == $what) {
        $sql = sprintf(
            "SELECT `c`.* FROM `world`.`country` AS `c` LEFT JOIN `world`.`time_zones` AS `tz` ON `tz`.`country` = `c`.`code` LEFT JOIN `world`.`airports` AS `ap` ON `ap`.`country` = `c`.`code` LEFT JOIN `world`.`seaports` AS `sp` ON `sp`.`country` = `c`.`code` WHERE `c`.`name` LIKE %s OR `tz`.`name` LIKE %s OR `ap`.`name` LIKE %s OR `sp`.`name` LIKE %s GROUP BY `c`.`code` ORDER BY `c`.%s %s LIMIT %d, %d",
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $by,
            $order,
            $start,
            $limit
        );
    } else {
        $sql = sprintf(
            "SELECT * FROM `world`.`country` WHERE `name` LIKE %s ORDER BY %s %s LIMIT %d, %d",
            $DB->escape($value . '%'),
            $by,
            $order,
            $start,
            $limit
        );
    }

    if ("code" == $by || "name" == $by) {
        $by = "country";
    }

    draw_header_row_report($fields);
    $res    = $DB->query($sql);
    $shaded = true;
    $count  = 1;
    while ($row = $DB->fetch_array($res)) {
        // print each of the rows of the array..
        if (!$shaded) {
            echo draw_row(false);
        } else {
            echo draw_row(true);
        }

        $shaded  = !$shaded;
        $capital = $row['capital_city'];
        echo '<td align="' . $fields['country'] . '"><a href="index.php?action=summary&code=' . $row['code'] . '" target="_blank">' . $row['name'] . '</a></td>';
        echo '<td align="' . $fields['capital_city'] . '">' . $capital . '</td>';
        if (ava_time($capital)) {
            echo '' . display_time($capital, $count) . '';
        } else {
            echo '<td align="' . $fields['time'] . '">N/A</td>';
        }

        echo '<td align="' . $fields['population'] . '">' . $row['population'] . '</td>';
        echo '<td width="20%" align="' . $fields['languages'] . '">' . format_str_sum($row['languages']) . '</td>';
        if (has_permission('WORLD2', $allowed_sections)) {
            echo '<td align="center"><a href="index.php?code=' . $row['code'] . '" target="_blank">Edit</a></td>';
        } else {
            echo '<td align="center"><a href="index.php?action=summary&code=' . $row['code'] . '">View</a></td>';
        }

        echo '</tr>';
        $count++;
    }
    echo '</table><br />';
    display_entries($start, $limit);
}

// Function to send the information to Molly, as others don't have access to edit the World Summary
function send()
{
    global $_GLOBALS;

    global $DB, $what, $value, $order, $by, $query, $max, $at, $page, $script;
    global $status, $website, $print, $summary, $action, $user, $limit;
    $mail_ad = 'sales@arcooda.com'; // just for testing purposes at the moment....change to sales@arcooda.com
    $subject = 'World Summary Information Update:';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    /* additional headers */
    $headers .= "To: Molly <" . $mail_ad . ">\r\n";
    $headers .= "From: " . $_GLOBALS['staff']['name'] . " <" . $_GLOBALS['staff']['email'] . ">\r\n";
    $to_be_sent = '<pre>Information entered on Coinop World Summary Section:<br />' . $value . '</pre>';
    mail($mail_ad, $subject, $to_be_sent, $headers);
    echo 'Message successfully sent to ' . $mail_ad;
}

function world_times()
{
    global $DB, $what, $value, $order, $by, $query, $max, $at, $page, $script;
    global $status, $website, $print, $summary, $action, $user, $limit;
    write_global_JS_funcs();
    $fields['country'] = 'center';
    $fields['times']   = 'center';
    if ("" == $order) {
        $order = "asc";
    }

    $by    = "code";
    $start = $_REQUEST['start'];
    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    settype($limit, "float");
    $limit = 30;
    $str   = sprintf(
        "SELECT * FROM `world`.`country` ORDER BY %s %s LIMIT %d, %d",
        $by,
        $order,
        $start,
        $limit
    );
    $by    = "country";
    draw_header_row_report($fields);
    $res           = $DB->query($str);
    $shaded        = true;
    $country_count = 1;
    while ($row = $DB->fetch_array($res)) {
        $tz_qry = "SELECT * FROM `world`.`time_zones` WHERE `country` = '" . $row['code'] . "' ORDER BY `name`";
        $tz     = $DB->query($tz_qry);
        $count  = mysqli_num_rows($tz);
        if (!$shaded) {
            if ($count > 0) {
                echo "<tr rowspan=\"$count\" class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
            } else {
                echo draw_row(false);
            }

            $shaded = !$shaded;
        } else {
            if ($count > 0) {
                echo "<tr rowspan=\"$count\" class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
            } else {
                echo draw_row(true);
            }

            $shaded = !$shaded;
        }
        echo '<td align="center" nowrap>' . $row['name'] . '</td>';
        echo '<td align="center" nowrap><table align="center">';
        $zone_count = 1;
        while ($tz_row = $DB->fetch_array($tz)) {
            $city["Name"]      = $tz_row['name'];
            $city["GMT"]       = $tz_row['offset'];
            $city["actualDST"] = 0.0;
            if ('y' == $tz_row['day_light']) {
                $city["actualDST"] = 1.0;
            }

            $gmt_diff                               = $city["GMT"] + $city["actualDST"];
            $city_time[$country_count][$zone_count] = time() + ($gmt_diff * 3600);
            $i                                      = $country_count;
            $j                                      = $zone_count;
            echo '<tr><td class="time">' . $tz_row['name'] . ':</td><td><span id="LiveClockIE' . $i . $j . '"></span></td></tr>';
            $zone_count++;
            /*<!--<td><img id="hrs1'.$i.$j.'" height="20" src="clock/0.gif"><img id="hrs2'.$i.$j.'" height="20" src="clock/0.gif"><img id="col1'.$i.$j.'" height="20" src="clock/col.gif"><img id="mins1'.$i.$j.'" height="20" src="clock/0.gif"><img id="mins2'.$i.$j.'" height="20" src="clock/0.gif"><img id="col2'.$i.$j.'" height="20" src="clock/col.gif"><img id="sec1'.$i.$j.'" height="20" src="clock/0.gif"><img id="sec2'.$i.$j.'" height="20" src="clock/0.gif"><img id="ampm'.$i.$j.'" height="20" src="clock/am.gif"></td>-->*/
        }
        if ($zone_count > 0) {
?>
		<script type="text/javascript">
//<![CDATA[
<?php
            for ($i = 1; $i < $zone_count; $i++) {
                echo 'var strClock' . $country_count . $i . ' = "' . gmdate("M d, Y H:i:s", $city_time[$country_count][$i]) . '";';
                echo 'var intClock' . $country_count . $i . 'Diff = syncClock' . $country_count . '(' . $country_count . $i . ');';
            }
?>
function update<?=$country_count?>()
{
    //window.setTimeout(update<?=$country_count?>, 1000);*/
    localTime    = new Date();
    intLocalTime = localTime.getTime();

    dot = '<FONT COLOR=#000000>:</FONT>';
<?php
            for ($i = 1; $i < $zone_count; $i++) {
                update($country_count . $i, true, false);
            }
?>
}
function syncClock<?=$country_count?>(clockID)
{
    localTime    = new Date();
    intLocalTime = localTime.getTime();
<?php
            for ($i = 1; $i < $zone_count; $i++) {
                echo 'if (clockID == ' . $country_count . $i . ')';
                echo 'intClockTime = Date.parse(strClock' . $country_count . $i . ');';
            }
?>
    intClockDifference = intLocalTime - intClockTime;
    return intClockDifference;
}
//display_clock();
//]]>
		</script>
		<?= '<script>update' . $country_count . '()</script>' ?>
<?php
}
        $country_count++;
        echo '</table></td></tr>';
    }
    echo '</table>';
    display_entries_main($start, $limit);
    //echo 'Funciton being constructed...';

}

function pop_users()
{
    global $DB, $value, $order, $by, $limit, $start;
    $fields['country'] = 'center';
    $fields[$value]    = 'center';
    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    settype($limit, "float");
    if ('' == $order) {
        $order = 'desc';
    }

    if ('' == $by) {
        $by = $value;
    }

    $sql  = sprintf(
        "SELECT `name`, %s FROM `world`.`country` ORDER BY %s %s",
        $value,
        $value,
        $order
    );
    $data = array();
    $res  = $DB->query($sql);
    while ($row = $DB->fetch_array($res)) {
        $v      = str_replace(',', '', $row[$value]);
        $data[] = array('name' => $row['name'], $value => $v);
    }
    foreach ($data as $key => $row) {
        $name[$key] = $row['name'];
        $val[$key]  = $row[$value];
    }
    if ("desc" == $order) {
        array_multisort($val, SORT_DESC, SORT_NUMERIC, $name, SORT_ASC, $data);
    } else {
        array_multisort($val, SORT_ASC, SORT_NUMERIC, $name, SORT_ASC, $data);
    }

    //array_multisort($ar,SORT_DESC,$nm); // get the array sorted...
    draw_header_row_report($fields);
    $shaded = true;
    for ($i = $start; $i < ($start + $limit); $i++) {
        if (!$shaded) {
            echo draw_row(false);
        } else {
            echo draw_row(true);
        }

        $shaded = !$shaded;
        if ("" != $name[$i]) {
            echo '<td align="center">' . $name[$i] . '</td>';
            echo '<td align="center">' . number_format($val[$i]) . '</td>';
        }
        echo '</tr>';
    }
    echo '</table><br />';
    display_entries_main($start, $limit);
}

function draw_search()
{
    $inc = false;
    include_once 'world_search.php';
    echo "<script>document.getElementsByName('value')[0].select();</script>";
}

function draw_row($alt)
{
    if ($alt) {
        return "<tr style=\"text-align:center\" class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
    } else {
        return "<tr style=\"text-align:center\" class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
    }

}

function draw_header_row_report($fields)
{
    global $order, $by, $what, $value;

    reset($fields);
    echo "<br />";
    echo '<table aligh="left" width="95%" align="center" cellpadding="4" cellspacing="1">';
    echo '<tr bgcolor="#3399FF">';

    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
        echo "<a href=\"?what=$what&value=$value&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo ucwords(str_replace('_', ' ', $field));
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="../' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>';
        next($fields);
    }
    echo '</tr>';

}
function display_entries($start, $limit)
{
    global $DB, $user, $fields, $order, $by, $query, $max, $at, $page, $script, $status, $website, $print, $what, $value, $action;
    $table = 'country';
    if ("" == $by || "country" == $by) {
        $by = "name";
    }

    if ("" == $order) {
        $order = "asc";
    }

    if ('search' == $what) {
        $qry = sprintf(
            "SELECT `c`.`name` FROM `world`.`country` AS `c` LEFT JOIN `world`.`time_zones` AS `tz` ON `tz`.`country` = `c`.`code` LEFT JOIN `world`.`airports` AS `ap` ON `ap`.`country` = `c`.`code` LEFT JOIN `world`.`seaports` AS `sp` ON `sp`.`country` = `c`.`code` WHERE `c`.`name` LIKE %s OR `tz`.`name` LIKE %s OR `ap`.`name` LIKE %s OR `sp`.`name` LIKE %s GROUP BY `c`.`code` ORDER BY `c`.`%s` %s",
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $DB->escape('%' . $value . '%'),
            $by,
            $order
        );
    } else {
        $qry = sprintf(
            "SELECT * FROM `world`.`country` WHERE `name` LIKE %s ORDER BY %s %s LIMIT %d, %d",
            $DB->escape($value . '%'),
            $by,
            $order,
            $start,
            $limit
        );
    }

    $res   = $DB->query($qry);
    $pages = '';
    $count = mysqli_num_rows($res);
    $limit = number_format($limit, 0, '.', '');
    $count = number_format($count, 0, '.', '');
    $pages = floor($count / $limit);
    if (0 != $count) {
        if ($count < $limit) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' </b>';
        } else if ($start + $limit > $count) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
        } else {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
        }

    }
    $orig = $start;
    $new  = 0;
    for ($i = 0; $i <= $pages; $i++) {
        if (0 == $new && 0 == $orig || $orig == $new) {
            if ($count > $limit) {
                if ($i == $pages) {
                    echo '<b>' . ($start + 1) . ' - ' . ($count) . ' | </b>';
                } else {
                    echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
                }

            }
            $new = $start + $limit;
        } else {
            $link = "action=$action&start=$new&by=$by&order=$order&what=$what&value=$value";
            if ($i == $pages) {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($count) . '</a>';
            } else {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
            }

            $new += $limit;
        }
    }
    echo '</center>';
}

include_once '../connection.inc.php';
include_once 'world_functions.php';
include_once 'header.php';
?>
<script type="text/javascript">
//<![CDATA[
function pviiClassNew(obj, new_style) { //v2.6 by PVII
  obj.className=new_style;
}
//]]>
</script>
<?php
write_global_JS_funcs();
$limit = 50;
echo '<link rel="stylesheet" type="text/css" href="style.css" />';
echo '<style>.TableRow{background-color:#FFFFFF;}.TableRowAlt{background-color:#EEEEEE;}</style>';
if ("alpha" == $what) {
    echo '<script>document.title = "Countries By Letter ' . ucfirst($value) . '";</script>';
} else if ("send" == $what) {
    echo '<script>document.title = "Sending Information";</script>';
} else if ("" != $what) {
    echo '<script>document.title = "Report: ' . ucfirst($what) . ' (' . ucfirst($value) . ')";</script>';
} else {
    if ("time" == $value) {
        echo '<script>document.title = "World Times";</script>';
    } else {
        echo '<script>document.title = "Report: ' . ucfirst($value) . '";</script>';
    }

}
echo '<br />';
if ("search" == $what) {
    draw_search();
    country();
} elseif ("send" == $what) {
    send();
} elseif ("time" == $value) {
    world_times();
} elseif ("population" == $value || "internet_users" == $value) {
    pop_users();
} elseif ("alpha" == $what) {
    country();
} else {
    create_report();
}
