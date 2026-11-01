<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include '../currency.php';

if ("true" == $_POST['add']) {

    //Sets Variables
    $ST   = $_POST['cargo'];
    $From = $_POST['from'];
    $To   = $_POST['to'];
    $FT   = $_POST['freight_type'];
    $Kgs  = $_POST['weightgks'];
    $CM3  = $_POST['weightcms'];
    $TIT  = $_POST['tit'];
    $Cust = $_POST['cust'];
    $Fowd = $_POST['fowarder'];
    $Date = $_POST['dated'];
    $Car  = "";
    $GT   = $_POST['type'];
    $Cur  = $_POST['currency'];
    $Pri  = $_POST['price'];
    $USD  = showPriceFrom($_POST['price'], $_POST['currency'], "USD");
    $Note = addslashes($_POST['notes']);

    //Makes Query
	$DB->query(/*sprintf*/(
    	"INSERT INTO freight_shipped(fs_ship_type, fs_from, fs_to, fs_freight_type, fs_weight_kgs, fs_weight_cm3, fs_tit, fs_customer, fs_goods, fs_fowarder, fs_price, fs_currency, fs_notes, fs_date, fs_usd_price)" .
        "                    VALUES (      '$ST',  '$From', '$To',           '$FT',        '$Kgs',        '$CM3', '$TIT',     '$Cust',    '$GT',     '$Fowd',   '$Pri',      '$Cur',  '$Note', '$Date',       '$USD')"
	));

    //Displays Message Saying Its Been Added
    print "<font color='red'>Added New Freight(" . $DB->insert_id() . ")</font>\n";
}
?>
<script>
	function ChangeFreight(){
		//Clears Box
		for(a = document.foo.freight_type.options.length; a >= 0; a--){
			document.foo.freight_type.options[a] = null;
		}

		//Checks For New Values, Addes Options For Them
		if(document.foo.cargo.value=="1"){
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('LCL','LCL');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('20ft','20ft');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('40ft','40ft');
		}else{
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('Direct','Direct');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('Indirect','Indirect');
		}
	}
	function searchCustomer() {
		var name=document.foo.cust_name.value;
		var url='membersearch.php?search='+name;
		var features='scrollbars, resizable, status, height=400, width=600';
		window.open(url,'Search',features);
	}
</script>
<form accept-charset="utf-8" autocomplete="off" method="post" action="add_freight.php" name="foo">
	<table align="center" cellspacing="0" cellpadding="5">
		<tr>
			<td bgcolor="#FFFFCC">Shipment Type</td>
			<td bgcolor="#CCCCCC">
				<select name="cargo" onchange="javascript:ChangeFreight()">
					<option value="0">Air Cargo</option>
					<option value="1">Sea Cargo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Date</td>
			<td bgcolor="#CCCCCC"><input type="text" name="dated" /><input type="button" value=" \/ " onclick="gfPop.fPopCalendar(document.foo.dated)" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship From</td>
			<td bgcolor="#CCCCCC">
				<select name="from">
					<?=formatHTMLOptions(getCountryList(false), $_REQUEST['from'] ?? 'HK')?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship To</td>
			<td bgcolor="#CCCCCC">
				<select name="to">
					<?=formatHTMLOptions(getCountryList(false), $_REQUEST['to'] ?? 'HK')?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Freight Type</td>
			<td bgcolor="#CCCCCC">
				<select name="freight_type">
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Shipping Weight KGS</td>
			<td bgcolor="#CCCCCC"><input type="text" name="weightgks" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Shipping Weight CM3</td>
			<td bgcolor="#CCCCCC"><input type="text" name="weightcms" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Transit Time</td>
			<td bgcolor="#CCCCCC"><input type="text" name="tit" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Customer Name</td>
			<td bgcolor="#CCCCCC">
				<input type="hidden" name="cust" />
				<input type="text" name="cust_name" />
				<input onclick="javascript:searchCustomer();" type="button" value="Search" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Fowarder</td>
			<td bgcolor="#CCCCCC">
				<select name="fowarder">
<?php
if ($data = $DB->query(sprintf(
	"SELECT `fow_id`, `fow_company` FROM `fowarder` ORDER BY `fow_company` ASC"
))) {
	while ($row = $DB->fetch_assoc($data)) {
		printf(
			"<option value=\"%s\" %s>%s</option>\n",
			htmlspecialchars($row['fow_id']),
			$_REQUEST['extra'],
			htmlspecialchars($row['fow_company'])
		);
	}
	$DB->free_result($data);
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="yellow">Carrier</td>
			<td bgcolor="yellow"></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Goods Type</td>
			<td bgcolor="#CCCCCC">
				<select name="type">
<?php
if ($data = $DB->query(sprintf(
	"SELECT `id`, `name` FROM `product_types` ORDER BY `name` ASC"
))) {
	while ($row = $DB->fetch_assoc($data)) {
		printf(
			"<option value=\"%s\" %s>%s</option>\n",
			htmlspecialchars($row['id']),
			$_REQUEST['extra'],
			htmlspecialchars($row['name'])
		);
	}
	$DB->free_result($data);
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Currency</td>
			<td bgcolor="#CCCCCC">
				<select name="currency">
					<?php showCurrencyList("USD")?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Price</td>
			<td bgcolor="#CCCCCC"><input type="text" name="price" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Notes</td>
			<td bgcolor="#CCCCCC"><textarea name="notes" cols="30" rows="5"></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Add New Shipment" /></td>
		</tr>
	</table>
	<input type="hidden" name="add" value="true" />
</form>
<script language="javascript">
	ChangeFreight();
</script>
<?php include '../footer.inc.php';?>