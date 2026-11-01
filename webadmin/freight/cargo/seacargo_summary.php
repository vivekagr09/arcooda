<?php
/**
 * @var string    $ship_from
 * @var string    $ship_to
 * @var bool      $print
 * @var string    $width
 * @var bool      $detailed
 * @var string    $container_type
 * @var string    $currency
 * @var mixed[][] $data_array
 */

include_once __DIR__ . '/common.inc.php';

global $DB;

?>
<html>
<head>
<title> Seacargo Summary </title>
	<link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
	<script type="text/javascript">
//<![CDATA[
function printpage() {
	if (window.print) {
		window.print();
	} else {
		alert('To print this page, select Print Frame from the file menu.');
	}
}
function doPrint() {
	if (confirm("Click OK to print this document")) {
		printpage();
	}
}
//]]>
	</script>

	<style type="text/css">
/*<![CDATA[*/
.printTableHeader{background:#fff;color:#000;font-family:Verdana;font-size:14;font-weight:bold;text-align:center}
.printTableRow{background:#fff;color:#000;font-family:Verdana;font-size:12;font-weight:bold}
.printTableRowAlt{background:#fff;color:#000;font-family:Verdana;font-size:12;font-weight:bold}
/*]]>*/
</style>
</head>
<body>
<?php
include '../../currency.php';
if ('ALL' == $ship_from) {
    $ship_from_name = "ALL";
} else {
    $from_country   = $DB->query("SELECT `name` FROM shipping_location WHERE code = '$ship_from';");
    $row            = $DB->fetch_array($from_country);
    $ship_from_name = $row['name'];
}

if ('ALL' == $ship_to) {
    $ship_to_name = "ALL";
} else {
    $to_country   = $DB->query("SELECT `name` FROM shipping_location WHERE code = '$ship_to';");
    $row          = $DB->fetch_array($to_country);
    $ship_to_name = $row['name'];
}

if (true == $print) {
    $width       = "width='575px' border='1' style='border-width:thin;' bordercolor='#000000'";
    $tableHeader = "printTableHeader";
    $tableRow    = "printTableRow";
    $tableRowAlt = "printTableRowAlt";

} else {
    $tableHeader = "tableHeader";
    $tableRow    = "tableRow";
    $tableRowAlt = "tableRowAlt";
}
?>

	<table <?=$width?> align='center' cellpadding='0' cellpadding='0'>
	<tr>
<?php
if (true == $detailed) {
?>
				<td colspan='6' class='<?=$tableHeader?>' align='center'><h4>Seacargo Full Results for Previous Shipments from <?=$ship_from_name?> TO <?=$ship_to_name;?></h4>
<?php
} else {
?>
				<td colspan='6' class='<?=$tableHeader?>' align='center'><h4>Seacargo Summary for Previous Shipments from <?=$ship_from_name?> TO <?=$ship_to_name;?></h4>
<?php
}
?>
	</tr>
	</table><p>

		<table align="center" width=<?=$width?> cellspacing=2 cellpadding=3>
<?php
if ('ALL' == $ship_from && 'ALL' != $ship_to && 'ALL' != $container_type) {
    $sql = "SELECT * FROM shipping_seacargo WHERE ship_to ='" . $ship_to . "' AND container_type ='" . $container_type . "' ORDER BY date DESC";
} else if ('ALL' == $ship_from && 'ALL' != $ship_to && 'ALL' == $container_type) {
    $sql = "SELECT * FROM shipping_seacargo WHERE ship_to ='" . $ship_to . "' ORDER BY date DESC";
} else if ('ALL' != $ship_from && 'ALL' == $ship_to && 'ALL' != $container_type) {
    $sql = "SELECT * FROM shipping_seacargo WHERE ship_from = '" . $ship_from . "' AND container_type = '" . $container_type . "' ORDER BY date DESC";
} else if ('ALL' != $ship_from && 'ALL' == $ship_to && 'ALL' == $container_type) {
    $sql = "SELECT * FROM shipping_seacargo WHERE ship_from = '" . $ship_from . "' ORDER BY date DESC";
} else if ('ALL' == $ship_from && 'ALL' == $ship_to && 'ALL' == $container_type) {
    $sql = "SELECT * FROM shipping_seacargo ORDER BY date DESC";
} else if ('ALL' == $ship_from && 'ALL' == $ship_to && 'ALL' != $container_type) {
    $sql = "SELECT * FROM shipping_seacargo WHERE container_type = '" . $container_type . "' ORDER BY date DESC";
} else {
    $sql = "SELECT * FROM shipping_seacargo WHERE ship_from = '" . $ship_from . "' AND ship_to ='" . $ship_to . "' AND container_type = '" . $container_type . "' ORDER BY date DESC";
}
//echo $sql;
$shipments = $DB->query($sql);

$i = 0;
while ($row = $DB->fetch_array($shipments)) {
    $date = $row['date'];
    $temp_ship_from      = $row['ship_from'];
    $temp_ship_to        = $row['ship_to'];
    $customer            = $row['cust_name'];
    $goods_name          = $row['goods_name'];
    $weight              = $row['weight'];
    $c_weight            = $row['c_weight'];
    $quantity            = $row['quantity'];
    $p                   = ('0.00' == $row['s_price'] ? $row['price'] : $row['s_price']);
    $price               = round(showPriceFrom($p, $row['currency'], $currency), 0) . '.00';
    $type                = $row['type'];
    $temp_service_type   = $row['service_type'];
    $temp_container_type = $row['container_type'];
    $tit                 = $row['tit'];
    $supplier            = $row['supplier'];
    $carrier             = $row['carrier'];
    $notes               = $row['notes'];
    $terms               = ('checked' == $row['fob'] ? '- FOB<br/>' : '') . ('checked' == $row['exw'] ? '- Ex Works<br/>' : '') . ('checked' == $row['dtd'] ? '- Door To Door<br/>' : '') .
        ('checked' == $row['dta'] ? '- Door To Port<br/>' : '');

    $data_array["Date"][$i]           = $date;
    $data_array["Ship_From"][$i]      = $temp_ship_from;
    $data_array["Ship_To"][$i]        = $temp_ship_to;
    $data_array["Customer"][$i]       = $customer;
    $data_array["GoodsName"][$i]      = $goods_name;
    $data_array["Weight"][$i]         = $weight;
    $data_array["C_Weight"][$i]       = $c_weight;
    $data_array["Quantity"][$i]       = $quantity;
    $data_array["Price"][$i]          = $price;
    $data_array["Type"][$i]           = $type;
    $data_array["Service_Type"][$i]   = $temp_service_type;
    $data_array["Container_Type"][$i] = $temp_container_type;
    $data_array["Tit"][$i]            = $tit;
    $data_array["Supplier"][$i]       = $supplier;
    $data_array["Carrier"][$i]        = $carrier;
    $data_array["Notes"][$i]          = $notes;
    $data_array["Terms"][$i]          = $terms;

    $i++;
}
if (0 == $i) {
    echo "<tr><td colspan = 5 align = 'center'> <br><b> No Shipments like this have occured before";
    echo "</b><br></td></tr>";
} else if ($i > 1) {
 
}

if (false == $detailed && 0 != $i) {
?>
					<tr class="<?=$tableHeader?>">
						<td >
							<b>Date</b>
						</td>
						<td >
							<b>From</b>
						</td>
						<td >
							<b>To</b>
						</td>
						<td>
							<b>Weight</b>
						</td>
						<td>
							<b>Type</b>
						</td>
						<td>
							<b>Service Type</b>
						</td>
						<td>
							<b>Container Type</b>
						</td>
						<td>
							<b>Transit Time</b>
						</td>
						<td>
							<b>Price (<?=$currency;?>)</b>
						</td>
					</tr>
<?php
    if (true == $print) {
?>
					<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php
    }
    $shaded = false;
    for ($j = 0; $j < $i; $j++) {
        if (false == $shaded) {echo "<tr class='" . $tableRow . "'>";} else {echo "<tr class='" . $tableRowAlt . "'>";}

        $from_country        = $DB->query("SELECT name FROM shipping_location WHERE code = '" . $data_array['Ship_From'][$j] . "'");
        $row                 = $DB->fetch_array($from_country);
        $nice_ship_from_name = $row['name'];
        $to_country        = $DB->query("SELECT name FROM shipping_location WHERE code = '" . $data_array['Ship_To'][$j] . "'");
        $row               = $DB->fetch_array($to_country);
        $nice_ship_to_name = $row['name'];
?>

							<td align = 'center' valign='top'>
								<?=$data_array["Date"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$nice_ship_from_name?>
							</td>
							<td align = 'center' valign='top'>
								<?=$nice_ship_to_name?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Weight"][$j];?>kg
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Service_Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Container_Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Tit"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Price"][$j];?>
							</td>
						</tr>
<?php
        $shaded = !$shaded;
    }
} else if (0 != $i) {
?>
					<tr class="<?=$tableHeader?>">
						<td>
							<b>Date</b>
						</td>
						<td>
							<b>From</b>
						</td>
						<td>
							<b>To</b>
						</td>
						<td>
							<b>Terms</b>
						</td>
						<td>
							<b>Weight</b>
						</td>
						<td>
							<b>Cubic Weight</b>
						</td>
						<td>
							<b>Quantity</b>
						</td>
						<td>
							<b>Type</b>
						</td>
						<td>
							<b>Service Type</b>
						</td>
						<td>
							<b>Container Type</b>
						</td>
						<td>
							<b>Transit Time</b>
						</td>
						<td>
							<b>Price (<?=$currency;?>)</b>
						</td>
						<td>
							<b>Customer Name</b>
						</td>
						<td>
							<b>Name of Goods</b>
						</td>
						<td>
							<b>Supplier</b>
						</td>
						<td>
							<b>Carrier</b>
						</td>
						<td>
							<b>Notes</b>
						</td>
					</tr>
<?php
    $shaded = true;
    for ($j = 0; $j < $i; $j++) {
        if (false == $shaded) {echo "<tr class='" . $tableRow . "'>";} else {echo "<tr class='" . $tableRowAlt . "'>";}
        $from_country        = $DB->query("SELECT * FROM `coinop`.`city` WHERE `city_id` = '" . $data_array['Ship_From'][$j] . "'");
        $row                 = $DB->fetch_array($from_country);
        $nice_ship_from_name = $row['city_name'];
        $to_country          = $DB->query("SELECT * FROM `coinop`.`city` WHERE `city_id` = '" . $data_array['Ship_To'][$j] . "'");
        $row                 = $DB->fetch_array($to_country);
        $nice_ship_to_name   = $row['city_name'];
?>

							<td nowrap align = 'center' valign='top'>
								<?=$data_array["Date"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$nice_ship_from_name?>
							</td>
							<td align = 'center' valign='top'>
								<?=$nice_ship_to_name?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Terms"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Weight"][$j];?> kg
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["C_Weight"][$j];?> cm<sup>3</sup>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Quantity"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Service_Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Container_Type"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Tit"][$j];?>
							</td>
							<td align = 'center' valign='top'>
								<?=$data_array["Price"][$j];?>
							</td>
							<td nowrap align = 'center' valign='top'>
								<?=$data_array["Customer"][$j];?>
							</td>
							<td nowrap align = 'center' valign='top'>
								<?=$data_array["GoodsName"][$j];?>
							</td>
							<td nowrap align = 'center' valign='top'>
								<?=$data_array["Supplier"][$j];?>
							</td>
							<td nowrap align = 'center' valign='top'>
								<?=$data_array["Carrier"][$j];?>
							</td>
							<td nowrap align = 'center' valign='top'>
<?php
        $notes = stripslashes($data_array["Notes"][$j]);
        $notes = nl2br($notes);
?>
								<?=$notes?>
							</td>
						</tr>

<?php
    $shaded = !$shaded;}
}
?>
		</table>
<?php
if (false == $print || true == $detailed) {
    if (0 != $i) {
?>
				<br><center><a href='seacargo_summary.php?ship_from=<?=$ship_from?>&ship_to=<?=$ship_to?>&container_type=<?=$container_type?>&currency=<?=$currency?>&print=true' target='_blank'>Print Version</a></center>
<?php
    }
}
if (false == $print && false == $detailed) {
?>
			<br><center><a href='seacargo_summary.php?ship_from=<?=$ship_from?>&ship_to=<?=$ship_to?>&container_type=<?=$container_type?>&currency=<?=$currency?>&detailed=true' target='_blank'>View Full Results</a></center>
<?php
}
if (true == $print) {
?>
			<script language="javascript">
				doPrint();
			</script>
<?php
}
?>
	</body>
</html>