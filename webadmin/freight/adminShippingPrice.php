
<link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
<?php
include_once __DIR__ . '/common.inc.php';

include '../currency.php';
include 'courierPriceFunctions.php';

//details from previous form
$form_country_code = $_POST["country"];
$form_weight       = $_POST["weight"];
$form_currency     = $_POST["currency"];
$form_source       = $_POST["source"];

//retrieve some of the data for niceties
if ($form_weight <= 0 || '' == $form_weight) {
    $form_weight = 1;
}
$country = $DB->query("SELECT * FROM shipping_location WHERE code = '$form_country_code';");
$row     = $DB->fetch_array($country);
$country = $row[name];

$source_country = $DB->query("SELECT * FROM shipping_location WHERE code = '$source';");
$row            = $DB->fetch_array($source_country);
$source_country = $row[name];

?>
<!-- Display Data______________________________________________________________________________________________________________ -->

<br><br>
	<table width="95%" align='center' cellpadding='5' cellpadding='1'>
	<tr>
		<td colspan='6' class='pageMenu' align='center'><h4>Staff Freight Summary for shipping <?=$form_weight?>kg from <?=$source_country;?> to <?=$country;?></h4>
	</tr>
	</table><p>

		<table align="center" width="95%" cellspacing=1 cellpadding=3>

			<tr class="tableHeader">
				<td >
					<b>Customer Price (<?=$form_currency;?>)</b>
				</td>
				<td>
					<b>Courier</b>
				</td>
				<td>
					<b>Approx Delivery</b>
				</td>

				<td>
					<b>Normal Price (<?=$form_currency;?>)</b>
				</td>

				<td>
					<b>Our Price (<?=$form_currency;?>)</b>
				</td>
				<td>
					<b>Profit (<?=$form_currency;?>)</b>
				</td>
			</tr>

<?php
$statement = "SELECT * FROM shipping_services WHERE source = '$form_source'";
$couriers  = $DB->query($statement);
//$data_array;
$i = 0;
while ($row = $DB->fetch_array($couriers)) {
    $prop_id = $row[prop_id];
    $avail   = service_available($prop_id, $form_country_code, $form_weight);
    if ("true" == $avail) {
        $curr        = generic_base_currency($prop_id);
        $normalPrice = generic_price($prop_id, $form_country_code, $form_weight, '');
        if ($normalPrice < 0) {
            continue;
        }

        $normal    = showPriceFrom($normalPrice, $curr, $form_currency);
        $custPrice = generic_price($prop_id, $form_country_code, $form_weight, 'customer');

        $customer = round(showPriceFrom($custPrice, $curr, $form_currency), 0);
        $customer = $customer . ".00";

        $storePrice = generic_price($prop_id, $form_country_code, $form_weight, 'store');
        $store      = showPriceFrom($storePrice, $curr, $form_currency);
        //$profit = showPriceFrom(showPriceFrom($custPrice,$curr,$form_currency) - $store,$curr,$form_currency);
        //courier
        $courier = $row[name];

        //subtract convert
        $profitPrice = $custPrice - $storePrice;
        $profit      = showPriceFrom($profitPrice, $curr, $form_currency);

        //delivery time
        if (generic_tit($prop_id, $form_country_code) != '') {
            $deliver = generic_tit($prop_id, $form_country_code);
        } else {
            $deliver = 'NA';
        }
        $data_array["Customer"][$i] = $customer;
        $data_array["Courier"][$i]  = $courier;
        $data_array["Delivery"][$i] = $deliver;
        $data_array["Normal"][$i]   = $normal;
        $data_array["Store"][$i]    = $store;
        $data_array["Profit"][$i]   = $profit;
        $i++;
    }
}
if ($i <= 0 && '' != $form_source) {
    echo "<tr><td colspan = 5 align = 'center'> <br><b> Delivery Not Available from ";
    echo $source_country;
    echo " to ";
    echo $country;
    echo "</b><br></td></tr>";
}
if ('' == $form_source) {
    echo "<tr><td colspan = 5 align = 'center'> <br><b> No Services Added ";
    echo "</b><br></td></tr>";
} else if ($i > 1) {
    array_multisort($data_array["Customer"], SORT_NUMERIC, SORT_ASC, $data_array["Courier"], $data_array["Delivery"], $data_array["Normal"], $data_array["Store"], $data_array["Profit"]);
}
$shaded = false;
for ($j = 0; $j <= $i; $j++) {
    echo $shaded ? '<tr class="tableRow">' : '<tr class="tableRowAlt">';
    echo "<td align = 'left' valign='top'><b>";
    echo $data_array["Customer"][$j];
    echo "</b></td>";
    echo "<td align = 'left' valign='top'><b>";
    echo $data_array["Courier"][$j];
    echo "</b></td>";
    echo "<td align = 'left' valign='top'><b>";
    echo $data_array["Delivery"][$j];
    echo "</b></td>";
    echo "<td align = 'left' valign='top'>";
    echo $data_array["Normal"][$j];
    echo "</td>";
    echo "<td align = 'left' valign='top'>";
    echo $data_array["Store"][$j];
    echo "</td>";
    echo "<td align = 'left' valign='top'>";
    echo $data_array["Profit"][$j];
    echo "</td>";
    echo "</tr>";
    $shaded = !$shaded;
}
?>
		</table>
	<p><hr width="95%" align="center"><p>
	<center>
<table>
	<tr><td class="tableHeader">
		<b>Customer Freight Summary</b>
	</td></tr>
	<tr><td><tt>
<?php
if ($i <= 0 && '' != $form_source) {
    echo "Delivery Not Available from ";
    echo $source_country;
    echo " to ";
    echo $country;
} else if ('' == $form_source) {
    echo "No Services Added";
} else {
    //determine string lengths for email format
    $maxcustlength     = 0;
    $maxcourierlength  = 0;
    $maxdeliverylength = 0;
    for ($j = 0; $j < $i; $j++) {
        //customer table width stuff
        $length = strlen($data_array["Customer"][$j]);
        if ($maxcustlength < $length) {
            $maxcustlength = $length;
        }
        $length = strlen("Price");
        if ($maxcustlength < $length) {
            $maxcustlength = $length;
        }

        //courier table width stuff
        $length = strlen($data_array["Courier"][$j]);
        if ($maxcourierlength < $length) {
            $maxcourierlength = $length;
        }
        $length = strlen("Courier");
        if ($maxcourierlength < $length) {
            $maxcourierlength = $length;
        }

        //delivery time table width stuff
        $length = strlen($data_array["Delivery"][$j]);
        if ($maxdeliverylength < $length) {
            $maxdeliverylength = $length;
        }

        $length = strlen("Delivery Time");
        if ($maxdeliverylength < $length) {
            $maxdeliverylength = $length;
        }

    }

    //table header
    echo "Price........";
    for ($k = 0; $k < ($maxcustlength - strlen("Price")); $k++) {
        echo ".";
    }
    echo "Courier...";
    for ($k = 0; $k < ($maxcourierlength - strlen("Courier")); $k++) {
        echo ".";
    }
    echo "Delivery Time<br>";

    //table data
    for ($j = 0; $j < $i; $j++) {
        echo "$";
        echo $data_array["Customer"][$j];
        echo " " . $form_currency . "---";
        for ($k = 0; $k < ($maxcustlength - strlen($data_array["Customer"][$j])); $k++) {
            echo "-";
        }

        echo $data_array["Courier"][$j];
        echo "---";
        for ($k = 0; $k < ($maxcourierlength - strlen($data_array["Courier"][$j])); $k++) {
            echo "-";
        }

        echo "(";
        echo $data_array["Delivery"][$j];
        
        echo ")";

        echo "</br>";
    }
}
?></tt></td></tr></table>
	</center>



