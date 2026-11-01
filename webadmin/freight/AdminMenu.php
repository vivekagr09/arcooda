<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

?><link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
		<br>
		<table width="95%"  align="center" cellpadding='5' cellpadding='1'>
			<tr class="pageMenu">
				<td><a href="adminShippingForm.php">Freight Admin</a></td><td align='left' width="312px"><a href="adminShippingForm.php">Back</a></td>
			</tr>
		</table>
		<blockquote>
		<!-- Collapsable Menu -->
		<SCRIPT LANGUAGE="JavaScript" SRC="../expandToggle.js"></SCRIPT>
		<!-- Add Edit services -->
		<A HREF="#" onClick="expandIt('types'); return false"><IMG NAME="imEx" SRC="triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>Add/Edit Services</b></A> <!-- - <a href='freightdocs/Service.html' target=_blank>Help</a> -->
		<br>
		<DIV ID="typesChild" CLASS="child">
			<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>
					<a href="addService.php">Add Service</a>
				</td>
				<td>
					<a href="edit_Service_admin.php">Edit Service</a>
				</td>
				<td>
					<a href="delete_wizard1.php">Delete Service</a>
				</td>
			</tr>
			</table>
		</DIV>
		<hr width=200 align=left>


		<!-- Prices -->
		<A HREF="#" onClick="expandIt('location'); return false"><IMG NAME="imEx" SRC="triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>Edit Pricing For:</b></A> <!-- - <a href='freightdocs/Prices.html' target=_blank>Help</a> -->
		<br>
		<DIV ID="locationChild" CLASS="child">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
<?php
/*

 */
?>
				<Script Language="JavaScript">
					<!-- Hide the script from old browsers --
					function goto(form)
					{
						var index=form.select.selectedIndex
						if (form.select.options[index].value != "0")
						{
							location=form.select.options[index].value;
						}
					}
					//-->
				</SCRIPT>
				<form accept-charset="utf-8" autocomplete="off" method="post" name="form1">
					<td>
					<SELECT NAME="select"  <!--onchange="goto(this.form)"--> style="width:190px">
					<OPTION VALUE=""></option>
<?php
$couriers = $DB->query("SELECT * FROM shipping_services ORDER BY name ASC");

while ($row = $DB->fetch_array($couriers)) {
    echo "<OPTION VALUE=";
    echo "pricing_admin.php?table=shipping_";
    echo htmlspecialchars($row[prop_id]);
    echo "&query=&name_id=" . $row[prop_id] . ">";
    echo htmlspecialchars($row[name]);
    echo "	-	";
    $country_code  = $row[source];
    $country_query = $DB->query("SELECT * FROM shipping_location WHERE code = '$country_code';");
    $country_row   = $DB->fetch_array($country_query);
    $country_name  = $country_row[name];
    echo $country_name;
    echo "</option>";
}
?>
					</SELECT>
					<input type='button' onclick="goto(this.form)" value="Go" ></input>
					</td>
				</FORM>
			 </tr>
		</table>
		</DIV>
		<hr width=200 align=left>


		<!-- Zones -->
		<A HREF="#" onClick="expandIt('value'); return false"><IMG NAME="imEx" SRC="triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>Edit Destinations For:</b></A> <!-- - <a href='freightdocs/Destination.html' target=_blank>Help</a> -->
		<br>
		<DIV ID="valueChild" CLASS="child">
		<table border="0" cellspacing="1" cellpadding="2">
			<form accept-charset="utf-8" autocomplete="off" method="post"action="zoning_admin.php">
				<tr>
					<td>
						<select name="courier" style="width:190px" <!--onChange=submit()-->>
						<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM shipping_services ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="';
    echo $row[prop_id];
    echo '">';
    echo $row[name];
    echo "	-	";
    $country_code  = $row[source];
    $country_query = $DB->query("SELECT * FROM shipping_location WHERE code = '$country_code';");
    $country_row   = $DB->fetch_array($country_query);
    $country_name  = $country_row[name];
    echo $country_name;
    echo "</option>";
}
?>
						<input type='submit' value="Go" ></input>
					</td>
				</tr>
			</form>
		</table>
		</DIV>
		<hr width=200 align=left>
		<P>
		</blockquote>

