<?php
$month_end = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
function profitInvoicesFromDatabase($start_time = 0, $end_time = 0, $site_id = 0, $count = false, $type = 'monthly', $open = false)
{
    global $DB;
    $start_time = intval($start_time);
    $site_id    = intval($site_id);

    $criteria                   = array();
    $criteria['invoice_status'] = 'processed';
    if ($open) {
        $criteria['invoice_status'] = array("open", "proforma", "sales");
    }

    if ($site_id) {
        $criteria['site_id'] = $site_id;
    }

    $c_type = 'open_month';
    if ('weekly' == $type) {
        $c_type = 'open_week';
    } elseif ('yearly' == $type) {
        $c_type = 'open_year';
    } elseif ('quarterly' == $type) {
        $c_type = 'open_quarter';
    }

    $criteria[$c_type] = $start_time;

    $invoice_list = invoiceSearch($criteria);
    if ($count) {
        return count($invoice_list);
    }

    return $invoice_list;
}
function profitViewsFromDatabase($start_time = 0, $end_time = 0, $site_id = null, $product_id = null, $sub_id = null, $country = null)
{
    global $DB;
    $start_time = intval($start_time);
    $end_time   = intval($end_time);

    $whereAND = array("1");
    //if (!is_null($site_id) && $site_id != 0) $whereAND['site_id'] = "`site_id` = " . intval($site_id);
    if (!is_null($product_id) && 0 != $product_id) {
        $whereAND["product_id"] = "`product_id` = " . intval($product_id);
    }

    if (!is_null($sub_id)) {
        $whereAND["sub_id"] = "`sub_id` = " . intval($sub_id);
    }

    if (!is_null($country)) {
        $whereAND["country"] = "`country` = " . $DB->escape($sub_id);
    }

    if (0 != $start_time) {
        $whereAND["start_time"] = "`created` >= " . formatSQLDateTime($start_time);
    }

    if (0 != $end_time) {
        $whereAND["end_time"] = "`created` <= " . formatSQLDateTime($end_time);
    }

    $views_list = array();
    $query      = $DB->query("SELECT `product_id`, `sub_id`, `country`, SUM(`hit_count`) AS `total` FROM `hg_products`.`products_hits` WHERE " . implode(" AND ", $whereAND) . " GROUP BY `product_id`, `sub_id`, `country`");
    while ($row = $DB->fetch_assoc($query)) {
        $views_list[] = $row;
    }

    $DB->free_result($query);
    return $views_list;
}
function profitSalesMonths($start_time = 0, $end_time = 0)
{
    $month       = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
    $start_month = ($start_time) ? idate("m", $start_time) : 3;
    $start_year  = ($start_time) ? idate("Y", $start_time) : 2004;
    $end_month   = ($end_time) ? idate("m", $end_time) : idate("m");
    $end_year    = ($end_time) ? idate("Y", $end_time) : idate("Y");
    $months      = array();
    while ($end_year >= $start_year) {
        if ($end_year == $start_year) {
            if ($end_month < $start_month) {
                break;
            }
        }
        $mon_str          = $month[$end_month] . '-' . $end_year;
        $months[$mon_str] = getMonthStamp($end_month, $end_year);
        $end_month--;
        if ($end_month <= 0) {
            $end_year--;
            $end_month = 12;
        }
    }
    return $months;
}
function profitSalesMonthsForYear($year)
{
    $month  = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
    $months = array();
    foreach ($month as $id => $mon) {
        $months[$mon . '-' . $year] = getMonthStamp($id, $year);
    }

    return $months;
}
function profitSalesYears()
{
    $start_year = 2004;
    $end_year   = idate("Y");
    $years      = array();
    while ($end_year > $start_year) {
        $years[$end_year]['start'] = mktime(0, 0, 0, 1, 1, $end_year);
        $years[$end_year]['end']   = mktime(23, 59, 59, 12, 31, $end_year);
        $end_year--;
    }
    $years[2004] = array('start' => mktime(0, 0, 0, 1, 1, 2004), 'end' => mktime(23, 59, 59, 12, 31, 2004));
    return $years;
}
function profitBreakup($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, $type);
    $profit       = 0;
    if (count($invoice_list)) {
        $profit = invoiceSumValue($invoice_list, 'invoice_profitau');
    } else {
        $profit = 'NA';
    }

    return $profit;
}
function profitSalesBreakup($start_time, $end_time, $site_id = 0, $prev = false, $type = 'monthly')
{
    global $month_end;
    $breakup = array(
        "total"            => 0,
        "pending"          => 0,
        "sales_cost"       => "NA",
        "sales_total"      => "NA",
        "sales_total_dom"  => "NA",
        "sales_total_int"  => "NA",
        "sales_profit"     => "NA",
        "sales_profit_dom" => "NA",
        "sales_profit_int" => "NA",
        "gp_ratio"         => "NA",
        "gp_ratio_dom"     => "NA",
        "gp_ratio_int"     => "NA",
        "last_year"        => "NA",
        "last_year2"       => "NA",
    );

    $breakup["pending"] = profitInvoicesFromDatabase($start_time, $end_time, $site_id, true, $type, true);

    $invoice_list     = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, $type, false);
    $breakup['total'] = count($invoice_list);

    if (count($invoice_list)) {
        $breakup["sales_cost"]   = invoiceSumValue($invoice_list, "products_costau");
        $breakup["sales_total"]  = invoiceSumValue($invoice_list, "invoice_offerau");
        $breakup["sales_profit"] = invoiceSumValue($invoice_list, "invoice_profitau");
        foreach (invoiceChunk($invoice_list, "bill_country") as $country_id => $sub_list) {
            if ("AU" == $country_id && is_array($sub_list) && count($sub_list)) {
                $breakup["sales_cost_dom"]   = invoiceSumValue($sub_list, "products_costau");
                $breakup["sales_total_dom"]  = invoiceSumValue($sub_list, "invoice_offerau");
                $breakup["sales_profit_dom"] = invoiceSumValue($sub_list, "invoice_profitau");
            }
        }

        $breakup["sales_cost_int"]   = bcsub($breakup["sales_cost"], $breakup["sales_cost_dom"], 2);
        $breakup["sales_total_int"]  = bcsub($breakup["sales_total"], $breakup["sales_total_dom"], 2);
        $breakup["sales_profit_int"] = bcsub($breakup["sales_profit"], $breakup["sales_profit_dom"], 2);
        foreach (array("", "_dom", "_int") as $k) {
            if (bccomp("0.00", $breakup["sales_total" . $k], 2) != 0) {
                $breakup["gp_ratio" . $k] = bcmul("100.0", bcdiv($breakup["sales_profit" . $k], $breakup["sales_total" . $k], 2), 0);
            }
        }
    }

    if ($prev) {
        if ('monthly' == $type) {
            list($mon, $yr)         = explode('-', date("n-Y", $start_time));
            $last_stamp             = array(
                'start' => mktime(0, 0, 0, $mon, 1, $yr - 1),
                'end'   => mktime(0, 0, -1, $mon + 1, 1, $yr - 1),
            );
            $last_stamp2            = array(
                'start' => mktime(0, 0, 0, $mon, 1, $yr - 2),
                'end'   => mktime(0, 0, -1, $mon + 1, 1, $yr - 2),
            );
            $breakup['last_stamp']  = $last_stamp;
            $breakup['last_stamp2'] = $last_stamp2;
        } elseif ('weekly' == $type) {
            list($day, $mon, $yr)   = explode('-', date("d-m-Y", $start_time));
            $lys                    = explode('-', date("d-m-Y-w", mktime(0, 0, 0, $mon, $day, $yr - 1)));
            $lys2                   = explode('-', date("d-m-Y-w", mktime(0, 0, 0, $mon, $day + 1, $yr - 2)));
            $start1                 = mktime(0, 0, 0, $lys[1], $lys[0] + 1 - $lys[3], $lys[2]);
            $end1                   = mktime(0, 0, -1, idate('m', $start1), idate('d', $start1) + 7 - idate('w', $start1), idate('Y', $start1));
            $start2                 = mktime(0, 0, 0, $lys2[1], $lys2[0] + 1 - $lys2[3], $lys2[2]);
            $end2                   = mktime(0, 0, -1, idate('m', $start2), idate('d', $start2) + 7 - idate('w', $start2), idate('Y', $start2));
            $last_stamp             = array('start' => $start1, 'end' => $end1);
            $last_stamp2            = array('start' => $start2, 'end' => $end2);
            $breakup['last_stamp']  = $last_stamp;
            $breakup['last_stamp2'] = $last_stamp2;
            $break                  = profitSalesBreakUp($last_stamp['start'], $last_stamp['end'], $site_id, false, 'weekly');
            $breakup['last_year']   = $break['sales_profit'];
            $break                  = profitSalesBreakUp($last_stamp2['start'], $last_stamp2['end'], $site_id, false, 'weekly');
            $breakup['last_year2']  = $break['sales_profit'];
        } elseif ('yearly' == $type) {
            $yr                     = idate("Y", $start_time);
            $last_stamp             = array('start' => mktime(0, 0, 0, 1, 1, $yr - 1), 'end' => mktime(23, 59, 59, 12, 31, $yr - 1));
            $last_stamp2            = array('start' => mktime(0, 0, 0, 1, 1, $yr - 2), 'end' => mktime(23, 59, 59, 12, 31, $yr - 2));
            $breakup['last_stamp']  = $last_stamp;
            $breakup['last_stamp2'] = $last_stamp2;
        }
        if ('quarterly' != $type && 'weekly' != $type) {
            $break                 = profitSalesBreakUp($last_stamp['start'], $last_stamp['end'], $site_id, false, $type);
            $break2                = profitSalesBreakUp($last_stamp2['start'], $last_stamp2['end'], $site_id, false, $type);
            $breakup['last_year']  = $break['sales_profit'];
            $breakup['last_year2'] = $break2['sales_profit'];
        }
        if ('quarterly' == $type) {
            $prev_list1     = array();
            $prev_list2     = array();
            $last_year      = 0;
            $last_year2     = 0;
            list($mon, $yr) = explode('-', date("n-Y", $start_time));
            $yr1            = $yr - 1;
            $yr2            = $yr - 2;
            $count          = 0;
            while ($count < 3) {
                $start1     = mktime(0, 0, 0, $mon, 1, $yr1);
                $start2     = mktime(0, 0, 0, $mon, 1, $yr2);
                $end1       = mktime(0, 0, -1, $mon + 1, 1, $yr1);
                $end2       = mktime(0, 0, -1, $mon + 1, 1, $yr2);
                $prev_list1 = array_merge(profitInvoicesFromDatabase($start1, $end1, $site_id, false, 'monthly'), $prev_list1);
                $prev_list2 = array_merge(profitInvoicesFromDatabase($start2, $end2, $site_id, false, 'monthly'), $prev_list2);
                $mon++;
                if ($mon > 12) {
                    $mon = 1;
                    $yr++;
                }
                $count++;
            }
            $breakup['last_year']  = count($prev_list1) ? invoiceSumValue($prev_list1, 'invoice_profitau') : 'NA';
            $breakup['last_year2'] = count($prev_list2) ? invoiceSumValue($prev_list2, 'invoice_profitau') : 'NA';
        }
    }
    return $breakup;
}
function profitAddMonth($array, $value)
{
    global $DB;
    if (is_array($array)) {
        foreach ($array as $times) {
            $start_time = $times['start_time'];
            $end_time   = $times['end_time'];
            $row        = $DB->query_first("SELECT `id` FROM `break_even` WHERE `start` = " . $start_time . " AND `end` = " . $end_time, MYSQLI_ASSOC);
            if (!$row) {
                $DB->query("INSERT INTO `break_even` (`start`, `end`, `weekly_cost`) VALUES (" . $start_time . ", " . $end_time . ", '" . sprintf('%.2f', $value) . "')");
            }
        }
    }
}
function profitAddWeek($start_time, $end_time, $value)
{
    global $DB;
    $row = $DB->query_first("SELECT `id` FROM `break_even` WHERE `start` = " . $start_time . " AND `end` = " . $end_time, MYSQLI_ASSOC);
    if (!$row) {
        $DB->query("INSERT INTO `break_even` (`start`, `end`, `weekly_cost`) VALUES (" . $start_time . ", " . $end_time . ", '" . sprintf('%.2f', $value) . "')");
    }
}
function profitGetMonth($month, $year)
{
    global $DB;
    if (0 == $month || 0 == $year) {
        return array();
    }

    $start            = mktime(0, 0, 0, $month, 1, $year);
    $end              = mktime(0, 0, -1, $month + 1, 1, $year);
    $month_start_time = mktime(0, 0, 0, idate('m', $start), idate('d', $start) + 1 - idate('w', $start), idate('Y', $start));
    $week_end_time    = mktime(0, 0, -1, idate('m', $month_start_time), idate('d', $month_start_time) + 8 - idate('w', $month_start_time), idate('Y', $month_start_time));
    $weeks            = array();
    $row              = $DB->query_first("SELECT * FROM `break_even` WHERE `start` = " . $month_start_time . " AND `end` = " . $week_end_time, MYSQLI_ASSOC);
    if ($row) {
        $weeks[$row['id']] = $row;
    }

    while ($week_end_time < $end) {
        $week_start_time = mktime(0, 0, 0, idate('m', $week_end_time), idate('d', $week_end_time) + 1 - idate('w', $week_end_time), idate('Y', $week_end_time));
        $week_end_time   = mktime(0, 0, -1, idate('m', $week_start_time), idate('d', $week_start_time) + 8 - idate('w', $week_start_time), idate('Y', $week_start_time));
        $row             = $DB->query_first("SELECT * FROM `break_even` WHERE `start` = " . $week_start_time . " AND `end` = " . $week_end_time, MYSQLI_ASSOC);
        if ($row) {
            $weeks[$row['id']] = $row;
        }
    }
    return $weeks;
}
function profitUpdateBreakEvens($posted)
{
    global $DB, $_GLOBALS;
    $site_options = array(
        0  => array('`highwaygroup`.`values`', '`name`', '`value`'),
        1  => array('`site_options`', '`option_name`', '`option_value`'),
        2  => array('`site_options`', '`option_name`', '`option_value`'),
        9  => array('`site_options`', '`option_name`', '`option_value`'),
        20 => array('`site_options`', '`option_name`', '`option_value`'),
    );
    $row = $site_options[0];
    if (array_key_exists($_GLOBALS["site_options"]["site_id"], $site_options)) {
        $row = $site_options[$_GLOBALS["site_options"]["site_id"]];
    }

    $DB->query(sprintf(
        "UPDATE %s SET %s = %s WHERE %s = 'DEFAULT_WEEKLY_COST' LIMIT 1",
        $row[0], $row[2], $DB->escape($posted["default_cost"]), $row[1]
    ));
    foreach ($posted['cost'] as $id => $cost) {
        $DB->query("UPDATE `break_even` SET `weekly_cost` = " . $DB->escape($cost) . " WHERE `id` = " . $id . " LIMIT 1");
    }
}
function profitBreakEvenMonth($month, $year)
{
    $weeks = profitGetMonth($month, $year);
    if (is_array($weeks) && count($weeks)) {
        $total = 0;
        foreach ($weeks as $id => $array) {
            $total += $array['weekly_cost'];
        }

        return $total;
    }
    return false;
}
function profitGetQuarters($year)
{
    $start_month = 7;
    $quarters    = array();
    $count       = 0;
    while ($count < 4) {
        $end_month = $start_month + 2;
        $end_year  = $year;
        if ($end_month > 12) {
            $end_month = $end_month % 12;
            $end_year++;
        }
        $start_time     = mktime(0, 0, 0, $start_month, 1, $year);
        $end_time       = mktime(0, 0, -1, $end_month + 1, 1, $end_year);
        $key            = date("M Y", $start_time) . ' - ' . date("M Y", $end_time);
        $quarters[$key] = array('start' => $start_time, 'end' => $end_time);
        $start_month    = $end_month + 1;
        $year           = $end_year;
        $count++;
    }
    return $quarters;
}
function profitProductSearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    $invoice_list  = array();
    $previous_list = array();
    $views_list    = array();
    list($sm, $sy) = explode('-', date("n-Y", $start_time));
    list($em, $ey) = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $em) {
                break;
            }

            $mst           = mktime(0, 0, 0, $sm, 1, $sy);
            $met           = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst           = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let           = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
            $views_list    = array_merge($views_list, profitViewsFromDatabase($mst, $met, $site_id));
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $views_list    = array_merge($views_list, profitViewsFromDatabase($mst, $met, $site_id));
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst           = mktime(0, 0, 0, $pm, 1, $py);
        $let           = mktime(0, 0, -1, $pm + 1, 1, $py);
        $previous_list = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
        $views_list    = array_merge($views_list, profitViewsFromDatabase($start_time, $end_time, $site_id));
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $previous_list           = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'weekly');
        $views_list              = array_merge($views_list, profitViewsFromDatabase($start_time, $end_time, $site_id));
    }
    $invoice_list = array_unique($invoice_list);
    $product_list = array();
    foreach ($invoice_list as $invoice_id) {
        foreach (invoiceProductsFromDatabase($invoice_id) as $array) {
            $profit = ($array['product_offerau'] - $array['product_costau']) * $array['product_qty'];
            $product_list[$array['product_id']]['total_qty'] += $array['product_qty'];
            $product_list[$array['product_id']]['product_name'] = $array['product_name'];
            $product_list[$array['product_id']]['total_sales'] += ($array['product_offerau'] * $array['product_qty']);
            $product_list[$array['product_id']]['total_profit'] += $profit;
            $product_list[$array["product_id"]]["product_str"] = sprintf("%d %d", $array["product_id"], $array["sub_id"]);
        }
    }

    foreach ($product_list as $k => $v) {
        $product_list[$k]["total_views"]     = 0;
        $product_list[$k]["total_views_int"] = 0;
        $product_list[$k]["total_views_dom"] = 0;
    }
    foreach ($views_list as $array) {
        $product_list[$array['product_id']]['total_qty'] += 0;
        //$product_list[$array['product_id']]['product_name'] = $array['product_name'];
        $product_list[$array['product_id']]['total_sales'] += 0;
        $product_list[$array['product_id']]['total_profit'] += 0;
        $product_list[$array["product_id"]]["product_str"] = sprintf("%d %d", $array["product_id"], $array["sub_id"]);
        $product_list[$array["product_id"]]["total_views"] += $array["total"];
        if ("AU" == $array["country"]) {
            $product_list[$array["product_id"]]["total_views_dom"] += $array["total"];
        } elseif ("--" != $array["country"]) {
            $product_list[$array["product_id"]]["total_views_int"] += $array["total"];
        }
    }
    $product_list2 = array();
    foreach ($previous_list as $p_invoice_id) {
        foreach (invoiceProductsFromDatabase($p_invoice_id) as $array) {
            if ('' != $array['product_name']) {
                $product_list2[$array['product_id']] += $array['product_qty'];
            }
        }
    }

    asort($product_list2);
    $product_list2 = array_reverse($product_list2, true);
    foreach ($product_list as $product_id => $array) {
        $product_list[$product_id]['last_ranking'] = '';
    }

    $count = 1;
    foreach ($product_list2 as $product_id => $qty) {
        $product_list[$product_id]['last_ranking'] = $count;
        $count++;
    }
    asort($product_list);
    foreach ($product_list as $product_id => $array) {
        if ('' == $array['product_name']) {
            $product_list[$product_id]["product_name"] = $array["product_name"] = productGetValue($array["product_str"], "product_name");
            if ("" == $array["product_name"]) {
                unset($product_list[$product_id]);
            }
        }
    }

    return $product_list;
}
function profitProductViewSearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    $invoice_list  = array();
    $previous_list = array();
    $views_list    = array();
    $views_list2   = array();
    list($sm, $sy) = explode('-', date("n-Y", $start_time));
    list($em, $ey) = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $em) {
                break;
            }

            $mst          = mktime(0, 0, 0, $sm, 1, $sy);
            $met          = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst          = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let          = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list         = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $invoice_list = array_merge($invoice_list, $list);
            $views_list   = array_merge($views_list, profitViewsFromDatabase($mst, $met, $site_id));
            $views_list2  = array_merge($views_list2, profitViewsFromDatabase($lst, $let, $site_id));
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list         = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $invoice_list = array_merge($invoice_list, $list);
            $views_list   = array_merge($views_list, profitViewsFromDatabase($mst, $met, $site_id));
            $views_list2  = array_merge($views_list2, profitViewsFromDatabase($lst, $let, $site_id));
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst         = mktime(0, 0, 0, $pm, 1, $py);
        $let         = mktime(0, 0, -1, $pm + 1, 1, $py);
        $views_list  = array_merge($views_list, profitViewsFromDatabase($start_time, $end_time, $site_id));
        $views_list2 = array_merge($views_list2, profitViewsFromDatabase($lst, $let, $site_id));
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $views_list              = array_merge($views_list, profitViewsFromDatabase($start_time, $end_time, $site_id));
        $views_list2             = array_merge($views_list2, profitViewsFromDatabase($lst, $let, $site_id));
    }
    $invoice_list = array_unique($invoice_list);
    $product_list = array();
    foreach ($invoice_list as $invoice_id) {
        foreach (invoiceProductsFromDatabase($invoice_id) as $array) {
            $profit = ($array['product_offerau'] - $array['product_costau']) * $array['product_qty'];
            $product_list[$array['product_id']]['total_qty'] += $array['product_qty'];
            $product_list[$array['product_id']]['product_name'] = $array['product_name'];
            $product_list[$array['product_id']]['total_sales'] += ($array['product_offerau'] * $array['product_qty']);
            $product_list[$array['product_id']]['total_profit'] += $profit;
            $product_list[$array["product_id"]]["product_str"] = sprintf("%d %d", $array["product_id"], $array["sub_id"]);
        }
    }

    foreach ($product_list as $k => $v) {
        $product_list[$k]["total_views"]     = 0;
        $product_list[$k]["total_views_int"] = 0;
        $product_list[$k]["total_views_dom"] = 0;
    }
    foreach ($views_list as $array) {
        $product_list[$array['product_id']]['total_qty'] += 0;
        //$product_list[$array['product_id']]['product_name'] = $array['product_name'];
        $product_list[$array['product_id']]['total_sales'] += 0;
        $product_list[$array['product_id']]['total_profit'] += 0;
        $product_list[$array["product_id"]]["product_str"] = sprintf("%d %d", $array["product_id"], $array["sub_id"]);
        $product_list[$array["product_id"]]["total_views"] += $array["total"];
        if ("AU" == $array["country"]) {
            $product_list[$array["product_id"]]["total_views_dom"] += $array["total"];
        } elseif ("--" != $array["country"]) {
            $product_list[$array["product_id"]]["total_views_int"] += $array["total"];
        }
    }

    $product_list2 = array();
    foreach ($views_list2 as $array) {
        $product_list2[$array['product_id']] += $array['total_views'];
    }

    asort($product_list2);
    $product_list2 = array_reverse($product_list2, true);

    foreach ($product_list as $product_id => $array) {
        $product_list[$product_id]['last_ranking'] = '';
    }

    $count = 1;
    foreach ($product_list2 as $product_id => $qty) {
        $product_list[$product_id]['last_ranking'] = $count;
        $count++;
    }

    $temp = array();
    foreach ($product_list as $k => $v) {
        $temp[$v["total_views"]][$k] = $v;
    }

    krsort($temp);
    $product_list = array();
    $count        = 1;
    foreach ($temp as $v1) {
        foreach ($v1 as $k2 => $v2) {
            $v2["ranking"] = $count;
            $count++;
            $product_list[$k2] = $v2;
        }
    }

    unset($temp);

    //asort($product_list);
    foreach ($product_list as $product_id => $array) {
        if ('' == $array['product_name']) {
            $product_list[$product_id]["product_name"] = $array["product_name"] = productGetValue($array["product_str"], "product_name");
            if ("" == $array["product_name"]) {
                unset($product_list[$product_id]);
            }
        }
    }

    return $product_list;
}
function profitGetOptionList($option)
{
    switch ($option) {
        case 'payment_type':
            return array(
                "AE" => "American Express",
                "MC" => "MasterCard",
                "VC" => "Visa",
                "PP" => "PayPal",
            );
    }
    return array();
}
function profitPaymentTypeSearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    global $DB;
    $payment_type_list       = profitGetOptionList("payment_type");
    $payment_type_list["??"] = "Other";
    $invoice_list            = array();
    $previous_list           = array();
    list($sm, $sy)           = explode('-', date("n-Y", $start_time));
    list($em, $ey)           = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $ey) {
                break;
            }

            $mst           = mktime(0, 0, 0, $sm, 1, $sy);
            $met           = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst           = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let           = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst           = mktime(0, 0, 0, $pm, 1, $py);
        $let           = mktime(0, 0, -1, $pm + 1, 1, $py);
        $previous_list = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $previous_list           = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'weekly');
    }
    $invoice_list = array_unique($invoice_list);
    $count_list   = array();
    $list2        = array();
    foreach ($invoice_list as $invoice_id) {
        $offer  = invoiceGetValue($invoice_id, 'invoice_offerau');
        $cost   = invoiceGetValue($invoice_id, 'invoice_costau');
        $profit = $offer - $cost;
        foreach (invoicePaymentsFromDatabase($invoice_id) as $payment) {
            if ($payment["payment_amountau"] > 0.0) {
                $offer        = $payment["payment_amountau"];
                $payment_type = '??';
                if ("" != $payment["TransactionNo"]) {
                    if ($row = $DB->query_first(sprintf(
                        "SELECT `Card` FROM `highwaygroup`.`credit_transactions` WHERE `TransactionNo` = %s AND `TxnResponseCode` = '0' AND `AcqResponseCode` = '00' ORDER BY `id` DESC LIMIT 1",
                        $DB->escape($payment["TransactionNo"])
                    ), MYSQLI_ASSOC)) {
                        $payment_type = $row["Card"];
                    } elseif ($row = $DB->query_first("SELECT `txn_id` FROM `highwaygroup`.`paypal_transaction` WHERE `txn_id` = " . $DB->escape($payment["TransactionNo"]) . " AND `payment_status` = 'Completed' LIMIT 1", MYSQLI_ASSOC)) {
                        $payment_type = "PP";
                    }
                } elseif (preg_match("/(Visa|Mastercard|Amex|PayPal)/i", $payment["payment_text"], $match)) {
                    $payment_type = $match[1];
                    if (strtolower($payment_type) == "visa") {
                        $payment_type = "VC";
                    }

                    if (strtolower($payment_type) == "mastercard") {
                        $payment_type = "MC";
                    }

                    if (strtolower($payment_type) == "amex") {
                        $payment_type = "AE";
                    }

                    if (strtolower($payment_type) == "paypal") {
                        $payment_type = "PP";
                    }
                } elseif (preg_match("/ ([^ ]{3}) (Bank )?Account$/i", $payment["payment_text"], $match)) {
                    $payment_type = $match[1] . " Bank Deposit";
                } elseif (preg_match("/^Payment via (.+)$/", $payment["payment_text"], $match)) {
                    $payment_type = $match[1];
                }
                //else $payment_type = $payment["payment_text"];
                $count_list[$payment_type]['total_qty']++;
                $count_list[$payment_type]['payment_type_name'] = (array_key_exists($payment_type, $payment_type_list)) ? $payment_type_list[$payment_type] : $payment_type;
                $count_list[$payment_type]['total_sales'] += $offer;
                $count_list[$payment_type]['total_profit'] += $profit;
            }
        }
    }
    foreach ($previous_list as $p_invoice_id) {
        foreach (invoicePaymentsFromDatabase($p_invoice_id) as $payment) {
            if ($payment["payment_amountau"] > 0.0) {
                $payment_type = '??';
                if ("" != $payment["TransactionNo"]) {
                    if ($row = $DB->query_first(sprintf(
                        "SELECT `Card` FROM `highwaygroup`.`credit_transactions` WHERE `TransactionNo` = %s AND `TxnResponseCode` = '0' AND `AcqResponseCode` = '00' ORDER BY `id` DESC LIMIT 1",
                        $DB->escape($payment["TransactionNo"])
                    ), MYSQLI_ASSOC)) {
                        $payment_type = $row["Card"];
                    } elseif ($row = $DB->query_first("SELECT `txn_id` FROM `highwaygroup`.`paypal_transaction` WHERE `txn_id` = " . $DB->escape($payment["TransactionNo"]) . " AND `payment_status` = 'Completed' LIMIT 1", MYSQLI_ASSOC)) {
                        $payment_type = "PP";
                    }
                } elseif (preg_match("/(Visa|Mastercard|Amex|PayPal)/i", $payment["payment_text"], $match)) {
                    $payment_type = $match[1];
                    if (strtolower($payment_type) == "visa") {
                        $payment_type = "VC";
                    }

                    if (strtolower($payment_type) == "mastercard") {
                        $payment_type = "MC";
                    }

                    if (strtolower($payment_type) == "amex") {
                        $payment_type = "AE";
                    }

                    if (strtolower($payment_type) == "paypal") {
                        $payment_type = "PP";
                    }
                } elseif (preg_match("/ ([^ ]{3}) (Bank )?Account$/i", $payment["payment_text"], $match)) {
                    $payment_type = $match[1] . " Bank Deposit";
                } elseif (preg_match("/^Payment via (.+)$/", $payment["payment_text"], $match)) {
                    $payment_type = $match[1];
                }
                $list2[$payment_type]++;
            }
        }
    }

    asort($list2);
    $list2 = array_reverse($list2, true);
    foreach ($count_list as $payment_type => $array) {
        $count_list[$payment_type]['last_ranking'] = '';
    }

    $count = 1;
    foreach ($list2 as $payment_type => $qty) {
        $count_list[$payment_type]['last_ranking'] = $count;
        $count++;
    }
    asort($count_list);
    foreach ($count_list as $payment_type => $array) {
        if ('' == $array['payment_type_name']) {
            unset($count_list[$payment_type]);
        }
    }

    return $count_list;
}
function profitFreightSearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    global $DB;

    $invoice_list  = array();
    $previous_list = array();
    list($sm, $sy) = explode('-', date("n-Y", $start_time));
    list($em, $ey) = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $ey) {
                break;
            }

            $mst           = mktime(0, 0, 0, $sm, 1, $sy);
            $met           = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst           = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let           = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst           = mktime(0, 0, 0, $pm, 1, $py);
        $let           = mktime(0, 0, -1, $pm + 1, 1, $py);
        $previous_list = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $previous_list           = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'weekly');
    }
    $invoice_list = array_unique($invoice_list);
    $count_list   = array();
    $list2        = array();
    foreach ($invoice_list as $invoice_id) {
        $invoice = invoiceFromDatabase($invoice_id);
        if (0 != $invoice["ship_id"]) {
            $row = $DB->query_first("SELECT `type` FROM `highwaygroup`.`freight_services` WHERE `id` = " . intval($invoice["ship_id"]) . " LIMIT 1", MYSQLI_ASSOC);

            $ship_id = sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : "");
            $count_list[$ship_id]['total_qty']++;
            $count_list[$ship_id]['ship_product_name'][] = $invoice["ship_product_name"];
            $count_list[$ship_id]['total_sales'] += $invoice["ship_offerau"];
            $count_list[$ship_id]['total_profit'] += $invoice["ship_offerau"] - $invoice["ship_costau"];
        }
    }
    foreach ($previous_list as $p_invoice_id) {
        $invoice = invoiceFromDatabase($p_invoice_id);
        $row = $DB->query_first("SELECT `type` FROM `highwaygroup`.`freight_services` WHERE `id` = " . intval($invoice["ship_id"]) . " LIMIT 1", MYSQLI_ASSOC);

        $ship_id = sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : "");
        if ('' != $ship_id) {
            $list2[$ship_id]++;
        }
    }
    asort($list2);
    $list2 = array_reverse($list2, true);
    foreach ($count_list as $ship_id => $array) {
        $count_list[$ship_id]["ship_product_name"] = implode("\n", array_unique($array["ship_product_name"]));
        $count_list[$ship_id]['last_ranking']      = '';
    }
    $count = 1;
    foreach ($list2 as $ship_id => $qty) {
        $count_list[$ship_id]['last_ranking'] = $count;
        $count++;
    }
    asort($count_list);
    foreach ($count_list as $ship_id => $array) {
        if ('' == $array['ship_product_name']) {
            unset($count_list[$ship_id]);
        }
    }

    return $count_list;
}
function profitCountrySearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    $country_list  = getCountryList(false);
    $invoice_list  = array();
    $previous_list = array();
    list($sm, $sy) = explode('-', date("n-Y", $start_time));
    list($em, $ey) = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $ey) {
                break;
            }

            $mst           = mktime(0, 0, 0, $sm, 1, $sy);
            $met           = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst           = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let           = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst           = mktime(0, 0, 0, $pm, 1, $py);
        $let           = mktime(0, 0, -1, $pm + 1, 1, $py);
        $previous_list = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $previous_list           = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'weekly');
    }
    $invoice_list = array_unique($invoice_list);
    $count_list   = array();
    $list2        = array();
    foreach ($invoice_list as $invoice_id) {
        $offer   = invoiceGetValue($invoice_id, 'invoice_offerau');
        $cost    = invoiceGetValue($invoice_id, 'invoice_costau');
        $country = invoiceGetValue($invoice_id, 'bill_country');
        $profit  = $offer - $cost;
        $count_list[$country]['total_qty']++;
        $count_list[$country]['country_name'] = trim($country_list[$country]);
        $count_list[$country]['total_sales'] += $offer;
        $count_list[$country]['total_profit'] += $profit;
    }
    foreach ($previous_list as $p_invoice_id) {
        $country = invoiceGetValue($p_invoice_id, 'bill_country');
        if ('' != $country) {
            $list2[$country]++;
        }
    }
    asort($list2);
    $list2 = array_reverse($list2, true);
    foreach ($count_list as $country => $array) {
        $count_list[$country]['last_ranking'] = '';
    }

    $count = 1;
    foreach ($list2 as $country => $qty) {
        $count_list[$country]['last_ranking'] = $count;
        $count++;
    }
    asort($count_list);
    foreach ($count_list as $country => $array) {
        if ('' == $array['country_name']) {
            unset($count_list[$country]);
        }
    }

    return $count_list;
}
function profitMemberSearch($start_time, $end_time, $site_id = 0, $type = 'monthly')
{
    $invoice_list  = array();
    $previous_list = array();
    list($sm, $sy) = explode('-', date("n-Y", $start_time));
    list($em, $ey) = explode('-', date("n-Y", $end_time));
    if ('yearly' == $type) {
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $ey) {
                break;
            }

            $mst           = mktime(0, 0, 0, $sm, 1, $sy);
            $met           = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst           = mktime(0, 0, 0, $sm, 1, $sy - 1);
            $let           = mktime(0, 0, -1, $sm + 1, 1, $sy - 1);
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('quarterly' == $type) {
        $match = array(1 => 10, 4 => 1, 7 => 4, 10 => 7);
        $sm2   = $match[$sm];
        $sy2   = $sy;
        if (10 == $sm2) {
            $sy2--;
        }

        while ($sm < $em) {
            $mst = mktime(0, 0, 0, $sm, 1, $sy);
            $met = mktime(0, 0, -1, $sm + 1, 1, $sy);
            $lst = mktime(0, 0, 0, $sm2, 1, $sy2);
            $let = mktime(0, 0, -1, $sm2 + 1, 1, $sy2);
            $sm++;
            $sm2++;
            $list          = profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly');
            $list2         = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
            $invoice_list  = array_merge($invoice_list, $list);
            $previous_list = array_merge($previous_list, $list2);
        }
    } elseif ('monthly' == $type) {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'monthly');
        $pm           = ($sm - 1) % 12;
        if (0 == $pm) {
            $pm = 12;
        }

        $py = $sy;
        if (1 == $sm) {
            $py--;
        }

        $lst           = mktime(0, 0, 0, $pm, 1, $py);
        $let           = mktime(0, 0, -1, $pm + 1, 1, $py);
        $previous_list = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'monthly');
    } elseif ('weekly' == $type) {
        $invoice_list            = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, 'weekly');
        list($ld, $lm, $ly, $lw) = explode('-', date('d-m-Y-w', strtotime('-1 week', $start_time)));
        $lst                     = mktime(0, 0, 0, $lm, $ld + 1 - $lw, $ly);
        $let                     = mktime(0, 0, 0, idate('m', $lst), idate('d', $lst) + 7 - idate('w', $lst), idate('Y', $lst));
        $previous_list           = profitInvoicesFromDatabase($lst, $let, $site_id, false, 'weekly');
    }
    $invoice_list = array_unique($invoice_list);
    $count_list   = array();
    $list2        = array();
    foreach ($invoice_list as $invoice_id) {
        $offer  = invoiceGetValue($invoice_id, 'invoice_offerau');
        $cost   = invoiceGetValue($invoice_id, 'invoice_costau');
        $member = invoiceGetValue($invoice_id, 'member_id');
        $profit = $offer - $cost;
        $count_list[$member]['total_profit'] += $profit;
        $count_list[$member]['total_qty']++;
        $count_list[$member]['total_sales'] += $offer;
    }
    foreach ($previous_list as $p_invoice_id) {
        $offer  = invoiceGetValue($p_invoice_id, 'invoice_offerau');
        $cost   = invoiceGetValue($p_invoice_id, 'invoice_costau');
        $member = invoiceGetValue($p_invoice_id, 'member_id');
        $profit = $offer - $cost;
        if (0 != $member) {
            $list2[$member] += $profit;
        }
    }
    asort($list2);
    $list2 = array_reverse($list2, true);
    foreach ($count_list as $member_id => $array) {
        $count_list[$member_id]['last_ranking'] = '';
    }

    $count = 1;
    foreach ($list2 as $member_id => $total_profit) {
        if (array_key_exists($member_id, $count_list)) {
            $count_list[$member_id]['last_ranking'] = $count;
        }

        $count++;
    }
    asort($count_list);
    foreach ($count_list as $member_id => $array) {
        if (0 == $member_id) {
            unset($count_list[$country]);
        }
    }

    return $count_list;
}
function profitProductDetails($product_id, $site_id = 0)
{
    $product_id = intval($product_id);
    if (!$product_id) {
        return;
    }

    $criteria['invoice_status'] = 'processed';
    if (0 != $site_id) {
        $criteria['site_id'] = $site_id;
    }

    $invoice_list = invoiceSearch($criteria);
    $product      = array();
    foreach ($invoice_list as $invoice_id) {
        foreach (invoiceProductsFromDatabase($invoice_id) as $array) {
            if ($array['product_id'] == $product_id) {
                $product['product_name'] = $array['product_name'];
                $profit                  = ($array['product_offerau'] - $array['product_costau']) * $array['product_qty'];
                $product['total_qty'] += $array['product_qty'];
                $product['product_name'] = $array['product_name'];
                $product['total_sales'] += ($array['product_offerau'] * $array['product_qty']);
                $product['total_profit'] += $profit;
            }
        }
    }
    $product['date_added'] = productGetValue($product_id, 'date_added');
    return $product;
}
function profitSalesSearchList($type = 'yearly', $start_time = 0, $end_time = 0)
{
    $month         = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
    $quarter_start = array(1 => 1, 2 => 1, 3 => 1, 4 => 4, 5 => 4, 6 => 4, 7 => 7, 8 => 7, 9 => 7, 10 => 10, 11 => 10, 12 => 10);
    if (0 == $start_time) {
        $start_time = mktime(0, 0, 0, 3, 1, 2004);
    }

    if (0 == $end_time) {
        $end_time = mktime(0, 0, -1, idate("m") + 1, 1, idate("Y"));
    }

    $list = array();
    if ('weekly' == $type) {
        $week_start_time                                                               = mktime(0, 0, 0, idate('m', $start_time), idate('d', $start_time) + 1 - idate('w', $start_time), idate('Y', $start_time));
        $week_end_time                                                                 = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
        $list[date("d/m/Y", $week_start_time) . ' - ' . date("d/m/Y", $week_end_time)] = array('start' => $week_start_time, 'end' => $week_end_time);
        while ($week_end_time < $end_time) {
            $week_start_time                                                               = mktime(0, 0, 0, idate('m', $week_end_time), idate('d', $week_end_time) + 1 - idate('w', $week_end_time), idate('Y', $week_end_time));
            $week_end_time                                                                 = mktime(23, 59, 59, idate('m', $week_start_time), idate('d', $week_start_time) + 7 - idate('w', $week_start_time), idate('Y', $week_start_time));
            $list[date("d/m/Y", $week_start_time) . ' - ' . date("d/m/Y", $week_end_time)] = array('start' => $week_start_time, 'end' => $week_end_time);
        }
    } elseif ('monthly' == $type) {
        list($sm, $sy) = explode('-', date("n-Y", $start_time));
        list($em, $ey) = explode('-', date("n-Y", $end_time));
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $em) {
                break;
            }

            $mst                           = mktime(0, 0, 0, $sm, 1, $sy);
            $met                           = mktime(23, 59, 59, $sm, cal_days_in_month(CAL_GREGORIAN, $sm, $sy), $sy);
            $list[$month[$sm] . ' ' . $sy] = array('start' => $mst, 'end' => $met);
            $sm++;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    } elseif ('yearly' == $type) {
        $sy = idate("Y", $start_time);
        $ey = idate("Y", $end_time);
        while ($sy <= $ey) {
            $list[$sy] = array('start' => mktime(0, 0, 0, 1, 1, $sy), 'end' => mktime(23, 59, 59, 12, 31, $sy));
            $sy++;
        }
    } elseif ('quarterly' == $type) {
        list($sm, $sy) = explode('-', date("n-Y", $start_time));
        list($em, $ey) = explode('-', date("n-Y", $end_time));
        while ($sy <= $ey) {
            if ($sy == $ey && $sm > $em) {
                break;
            }

            $ps                                                              = $quarter_start[$sm];
            $pe                                                              = $ps + 2;
            $pst                                                             = mktime(0, 0, 0, $ps, 1, $sy);
            $pet                                                             = mktime(23, 59, 59, $pe, cal_days_in_month(CAL_GREGORIAN, $pe, $sy), $sy);
            $list[$month[$ps] . ' ' . $sy . ' - ' . $month[$pe] . ' ' . $sy] = array('start' => $pst, 'end' => $pet);
            $sm                                                              = $pe + 1;
            if ($sm > 12) {
                $sm = 1;
                $sy++;
            }
        }
    }
    return $list;
}
function profitSalesDetails($product_id, $start_time, $end_time, $site_id = 0, $type = 'yearly')
{
    $product_id = intval($product_id);
    if (!$product_id) {
        return;
    }

    $invoice_list = array();
    if ('quarterly' == $type) {
        list($sm, $sy) = explode('-', date("n-Y", $start_time));
        $em            = idate("m", $end_time);
        while ($sm <= $em) {
            $mst          = mktime(0, 0, 0, $sm, 1, $sy);
            $met          = mktime(23, 59, 59, $sm, cal_days_in_month(CAL_GREGORIAN, $sm, $sy), $sy);
            $invoice_list = array_merge($invoice_list, profitInvoicesFromDatabase($mst, $met, $site_id, false, 'monthly'));
            $sm++;
        }
    } else {
        $invoice_list = profitInvoicesFromDatabase($start_time, $end_time, $site_id, false, $type);
    }

    $breakup = array();
    foreach ($invoice_list as $invoice_id) {
        foreach (invoiceProductsFromDatabase($invoice_id) as $array) {
            if ($array['product_id'] == $product_id) {
                $breakup['total_qty'] += $array['product_qty'];
                $profit = ($array['product_offerau'] - $array['product_costau']) * $array['product_qty'];
                $breakup['total_sales'] += ($array['product_offerau'] * $array['product_qty']);
                $breakup['total_profit'] += $profit;
            }
        }
    }
    return $breakup;
}
function getMonthStamp($month, $year)
{
    global $month_end;
    $end_day     = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $start_stamp = mktime(0, 0, 0, $month, 1, $year);
    $end_stamp   = mktime(23, 59, 59, $month, $end_day, $year);
    $month_array = array('start' => $start_stamp, 'end' => $end_stamp);
    return $month_array;
}
