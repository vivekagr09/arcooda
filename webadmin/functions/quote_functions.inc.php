<?php
if (!function_exists('padQuoteNum')) {
    function padQuoteNum($quoteID)
    {
        while (strlen($quoteID) < 6) {
            $quoteID = "0" . $quoteID;
        }

        return $quoteID;
    }
}

if (!function_exists('getUserList')) {
    function getUserList($selectedID = "")
    {
        global $DB;

        $retString = "";
        if ($result = $DB->query(sprintf(
            "SELECT DISTINCT `user` FROM `highwaygroup`.`admin_users` ORDER BY `user` ASC"
        ))) {
            while ($row = $DB->fetch_array($result)) {
                $selected = strcasecmp($row['user'], $selectedID) == 0 ? 'SELECTED' : '';
                $retString .= "<option value='" . ucwords($row['user']) . "' $selected>" . ucwords($row['user']) . "</option>\r\n";
            }
            $DB->free_result($result);
        }

        return $retString;
    }
}

if (!function_exists('getJobPriorityList')) {
    function getJobPriorityList($selectedID = 2)
    {
        global $DB;
        $sql       = "SELECT * FROM jobs_priority ORDER BY priority_id DESC";
        $result    = $DB->query($sql);
        $retString = "";
        while ($row = $DB->fetch_array($result)) {
            $selected = intval($row['priority_id']) == intval($selectedID) ? 'SELECTED' : '';
            $retString .= "<option value='" . $row['priority_id'] . "' $selected>" . ucwords($row['priority']) . "</option>\r\n";
        }
        return $retString;
    }
}

if (!function_exists('getJobStatusList')) {
    function getJobStatusList($selectedID = 0)
    {
        global $DB;
        $sql       = "SELECT * FROM staff_jobs_status ORDER BY status_id";
        $result    = $DB->query($sql);
        $retString = "";
        while ($row = $DB->fetch_array($result)) {
            $selected = intval($row['status_id']) == intval($selectedID) ? 'SELECTED' : '';
            $retString .= "<option value='" . $row['status_id'] . "' $selected>" . ucwords($row['status']) . "</option>\r\n";
        }
        return $retString;
    }
}

if (!function_exists('getQuoteProducts')) {
    function getQuoteProducts($qid, $DB)
    {
        $sql = "SELECT qd.product_id, qd.qty, qd.product_name, qd.unit_price,
					qd.currency, q.currency AS quote_currency
			FROM quotes q,quotes_details qd
			WHERE q.quote_id=$qid AND qd.quote_id=q.quote_id";
        $result   = $DB->query($sql);
        $products = array();
        for ($i = 0; $row = $DB->fetch_array($result); $i++) {
            $row['sales_price'] = showPriceFrom($row['unit_price'], $row['currency'], $row['quote_currency']);
            $row['currency']    = $row['quote_currency'];
            $products[$i]       = $row;
        }
        return $products;
    }
}

if (!function_exists('getQuoteWeight')) {
    function getQuoteWeight($qid, $DB)
    {
        $sql = "SELECT p.weight, p.weight_inc, qd.qty
			FROM products p, quotes_details qd
			WHERE qd.quote_id=$qid AND qd.product_id=p.id";
        $result       = $DB->query($sql);
        $weight       = 0;
        $properWeight = true;
        // ensure that all products in the cart have a proper weight value and if not
        // display NA instead of an incorrect weight
        while ($row = $DB->fetch_array($result)) {
            if (floatval($row['weight']) > 0) {
                if (floatval($row['weight_inc']) <= 0) {
                    $weight += intval($row['qty']) * floatval($row['weight']);
                } else {
                    if (intval($row['qty']) > 1) {
                        $weight += floatval($row['weight']);
                        $weight += (intval($row['qty']) - 1) * floatval($row['weight_inc']);
                    } else {
                        $weight += intval($row['qty']) * floatval($row['weight']);
                    }

                }
            } else {
                return -1;
            }
            // will be displayed as NA
        }
        return $weight;
    }
}

if (!function_exists('htmlSafe')) {
    function htmlSafe($string)
    {
        return htmlspecialchars(stripslashes($string), ENT_QUOTES);
    }
}

if (!function_exists('showJobHistory')) {
    function showJobHistory($jobID)
    {
        global $DB;
        if (intval($jobID) <= 0) {
            return;
        }

        $sql = "SELECT jd.*,jp.priority,j.status,TO_DAYS(j.date_completed) - TO_DAYS(j.date_added) AS days_closed,
				TO_DAYS(NOW()) - TO_DAYS(j.date_added) AS days_open
			FROM jobs j
			LEFT JOIN jobs_details jd ON j.job_id = jd.job_id
			LEFT JOIN jobs_priority jp ON jp.priority_id = jd.priority_id
			WHERE jd.job_id='$jobID'
			ORDER BY id";
        $result = $DB->query($sql);
        while ($row = $DB->fetch_array($result)) {
            $history[] = $row;
        }

        if (count($history) > 0) {
?>
<br />
<table border="0" width="85%" cellspacing="1" cellpadding="5" align="center">
	<tr><td colspan="5" style="font-size: 12px"><u>All comments about this job</u></td></tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr class="tableHeader">
		<td width="13%" nowrap>Date Added</td>
		<td width="10%">Time</td>
		<td width="13%">Sender</td>
		<td width="13%">Receiver</td>
		<td width="51%">Comments</td>
	</tr>
<?php
            $shaded = true;
            foreach ($history as $job) {
                $shaded = !$shaded;
                echo $shaded ? '<tr class="tableRowAlt">' : '<tr class="tableRow">';
?>
			<td nowrap><?=formatDate(strtotime($job['date_added']))?></td>
			<td><?=formatTime(strtotime($job['date_added']))?></td>
			<td><?=$job['sender'];?></td>
			<td><?=$job['receiver'];?></td>
			<td><?=$job['description'];?></td>
		</tr>
<?php
            }
            $first = reset($history);
            if ("open" == $first['status']) {
                $daysMsg = "This job has been open for " . $first['days_open'] . " days.";
            } else {
                $daysMsg = "This job was open for " . $first['days_closed'] . " days.";
            }

            echo "<tr><td colspan=\"5\">&nbsp;</td></tr>";
            echo "<tr><td colspan=\"5\" style=\"font-size: 12px\">$daysMsg</td></tr>\r\n";
            echo "</table>\r\n";
            return true;
        } else {
            return false;
        }

    }
}

if (!function_exists('showTitleStr')) {
    function showTitleStr($title, $outerAlign = "center", $innerAlign = "center", $width = "100")
    {
?>
	<br />
	<table width="<?=$width;?>%" align="<?=$outerAlign;?>" border=0 cellpadding=1 cellsapcing=1>
	<tr>
		<td align="<?=$innerAlign;?>" class="title"><?=$title;?></td>
	</tr>
	</table>
	<br>
<?php
    }
}

if (!function_exists('getStatus')) {
    function getStatus($statID)
    {
        global $DB;
        $sql    = "SELECT name FROM quotes_status WHERE status_id=" . $statID;
        $result = $DB->query_first($sql);
        return $result['name'];
    }
}

if (!function_exists('showShippingOptions')) {
    function showShippingOptions($selected = "")
    {
        global $DB;
        $statement = "SELECT sv_id, via FROM ship_via ORDER BY sv_id ASC";
        $result    = $DB->query($statement);
        while ($row = $DB->fetch_array($result)) {
            echo '<option value="' . $row['sv_id'] . '"';
            echo $row['sv_id'] == $selected ? ' selected' : '';
            echo '>' . stripslashes($row['via']) . '</option>' . "\r\n";
        }
    }
}

if (!function_exists('getCourierPriceInfo')) {
    function getCourierPriceInfo($shipCourierID, $countryTo, $weight)
    {
        global $DB;
        $sql = "SELECT scp.*, sc.*
			FROM shipping_services scp
			LEFT JOIN shipping_countries sc ON scp.prop_id = sc.prop_id
			WHERE scp.prop_id='$shipCourierID' AND sc.code='$countryTo'";
        $courier = $DB->query_first($sql);
        $sql     = "SELECT " . $courier['zone'] . " AS price, (shipping_weight - $weight) AS temp
			FROM shipping_$shipCourierID
			WHERE (shipping_weight - $weight) >= 0 ORDER BY temp";
        $price = $DB->query_first($sql);

        $normal = showPriceFrom($price['price'], $courier['base_currency']);
        $cost   = $normal - ($normal * ($courier['store_discount'] / 100));
        $surcharge   = 'y' == $courier['surcharge'] ? " (plus surcharge)" : "";
        $courierName = ucwords(stripslashes($courier['name']));

        $temp = array('normal' => $normal, 'cost' => $cost, 'surcharge' => $surcharge, 'courierName' => $courierName);
        return $temp;
    }
}

if (!function_exists('getOrderPriceInfo')) {
    function getOrderPriceInfo($qid)
    {
        global $DB;
        $sql = "SELECT * FROM quotes_details WHERE quote_id = '$qid' ORDER BY product_id";

        $result   = $DB->query($sql);
        $products = array();
        while ($product = $DB->fetch_array($result)) {
            $product['sales_price']    = $product['unit_price'];
            $product['sales_currency'] = $product['currency'];

            $prices                   = product_prices($product['product_id']);
            $priceQty                 = getPriceQty($product['qty'], $prices);
            $priceArray               = $prices[$priceQty];
            $product['cost_price']    = $priceArray['cost_price'];
            $product['cost_currency'] = $priceArray['cost_currency'];

            $products[] = $product;
        }
        return $products;
    }
}

if (!function_exists('getProductType')) {
    function getProductType($pid)
    {
        global $DB;
        $sql = "SELECT pt.name
			FROM product_types pt, products p
			WHERE p.type = pt.id AND p.id = '$pid'";
        $type = $DB->query_first($sql);
        return stripslashes($type['name']);
    }
}

if (!function_exists('getProductAvailability')) {
    function getProductAvailability($pid)
    {
        global $DB;
        $sql = "SELECT pa.availability
			FROM product_availability pa, products p
			WHERE p.id='$pid' AND pa.id = p.available";
        $res = $DB->query_first($sql);
        return $res['availability'];
    }
}

if (!function_exists('showPaymentOptions')) {
    function showPaymentOptions($selectedID = "")
    {
        global $DB;
        $sql       = "SELECT * FROM payment_method";
        $results   = $DB->query($sql);
        $retString = "";
        while ($row = $DB->fetch_array($results)) {
            $selected = $row['id'] == $selectedID ? 'SELECTED' : '';
            echo "<option value='" . $row['id'] . "' $selected >" . $row['payment_method'] . "</option>\r\n";
        }
    }
}

if (!function_exists('showProductCategories')) {
    function showProductCategories($selectedID = "")
    {
        global $DB;
        $sql = "SELECT * FROM product_types WHERE parent=0 ORDER BY description";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_array($res)) {
            $sql  = "SELECT * FROM product_types WHERE parent=" . $row['id'] . " ORDER BY name";
            $res2 = $DB->query($sql);
            while ($row2 = $DB->fetch_array($res2)) {
                echo "<option value=\"" . $row2['id'] . "\" ";
                if ($selectedID == $row2['id']) {
                    echo "selected ";
                }

                echo ">" . stripslashes($row['description'] . " - " . $row2['name']) . "</option>\r\n";
            }
        }
    }
}

if (!function_exists('showProductsByType')) {
    function showProductsByType($type = "")
    {
        global $DB;
        if ("" != $type) {
            $sql = "SELECT p.id, product_name, name AS location
				FROM products p, product_country_location pcl
				WHERE p.type='$type'
					AND p.country_location=pcl.id
					AND active='y'
				ORDER BY product_name";
            $result = $DB->query($sql);
            while ($row = $DB->fetch_array($result)) {
                echo "<option value=\"" . $row['id'] . "\">" . stripslashes($row['product_name']) . "&nbsp;&nbsp;&nbsp;&nbsp;(" . $row['location'] . ")</option>\r\n";
            }
        } else {
            echo "<option value=\"\"></option>\r\n";
        }
    }
}
