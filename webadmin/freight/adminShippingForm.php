<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

?>
 <link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
    <script src="tooltip.js" type="text/javascript"></script>

	<META HTTP-EQUIV="refresh"><br>
	<table width="95%" align='center' cellpadding='5' cellpadding='1'>
	<tr>
		<td class='pageMenu'><b>Freight Admin</b></td><td class='pageMenu'><a href="AdminMenu.php" tooltip="Click here to make changes to the Freight Calculator database">Add/Edit Services</a></td>
	</tr>
	</table>
			<!-- Freight Calculator __________________________________________________________________-->
		<form accept-charset="utf-8" autocomplete="off" method="post" action="adminShippingPrice.php" target="_blank">
			<table align="center" border='1' cellspacing="1" cellpadding="10">
				<tr><td colspan='2' class='tableHeader' tooltip="This tool will help calculate freight costs"><b>Freight Calculator</b></td><tr>
				<tr>
					<td class="left">Source: </td>
					<td class="right">
						<select name="source" style="width:190px" tooltip="This is where a package will be sent from">
<?php
$result = $DB->query("SELECT DISTINCT source FROM shipping_services ORDER BY source ASC");
if (empty($result)) {
    echo "<option value=''>No Services Added</option>";
} else {
    while ($row = $DB->fetch_array($result)) {
        echo "<option value='$row[source]'>";
        $country_code  = $row[source];
        $country_query = $DB->query("SELECT * FROM shipping_location WHERE code = '$country_code';");
        $country_row   = $DB->fetch_array($country_query);
        $country_name  = $country_row[name];
        echo $country_name;
        echo "</option>";
    }
}
?>
					</td>
				</tr>
				<tr>
					<td class="left">
						Destination:
					</td>
					<td class="right">
						<select name="country" style="width:190px" tooltip="This is where a package will be sent to">
<?php
$result = $DB->query("SELECT * FROM shipping_location ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo "<option value='$row[code]'>$row[name]</option>\n";
}
?>
					</td>
				</tr>
				<tr>
					<td class="left">
						Weight:
					</td>
					<td class="right">
						<input type="text" name="weight" size="23" value="1" tooltip="Is the greater of either the weight or the volumetric weight, which = (length(cm) &times; breadth(cm) &times; height(cm)) &divide; 6000">kg
					</td>
				</tr>
				<tr>
					<td class="left">
						Currency:
					</td>
					<td class="right">
						<select name="currency" style="width:190px" tooltip="This is the currency that you would like to view the prices in">
							<?=formatHTMLOptions(getCurrencyList(false), 'USD')?>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<input type="submit" name="Submit" value="Calculate" tooltip="Calculates the shipping charges from the above values">
					</td>
				</tr>
			</table>
		</form>
