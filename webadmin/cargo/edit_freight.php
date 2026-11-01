<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include '../currency.php';
include "freight_menu.php";

if ("true" == $_POST['edit']) {

    //Sets Variables
    $ID   = $_POST['id'];
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
    $Note = addslashes($_POST['notes']);

    //Makes Query
    $DB->query(sprintf(
		"UPDATE `freight_shipped` SET `fs_ship_type` = %s, `fs_from` = %s, `fs_to` = %s, `fs_freight_type` = %s, `fs_weight_kgs` = %s, `fs_weight_cm3` = %s, `fs_tit` = %s, `fs_goods` = %s, `fs_customer` = %s, `fs_fowarder` = %s, `fs_price` = %s, `fs_Currency` = %s, `fs_notes` = %s, `fs_date` = %s WHERE `fs_id` = %d LIMIT 1",
		$DB->escape($ST),
		$DB->escape($From),
		$DB->escape($To),
		$DB->escape($FT),
		$DB->escape($Kgs),
		$DB->escape($CM3),
		$DB->escape($TIT),
		$DB->escape($GT),
		$DB->escape($Cust),
		$DB->escape($Fowd),
		$DB->escape($Pri),
		$DB->escape($Cur),
		$DB->escape($Note),
		$DB->escape($Date),
		$ID
	));

    //Displays Message Saying Its Been Added
    print "<font color='red'>Edited Freight(" . $ID . ")</font>\n";
}

//
$data = $DB->query_first(sprintf(
	"SELECT * FROM `freight_shipped` AS `fs` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `fs`.`fs_customer` WHERE `fs`.`fs_id` = %d LIMIT 1",
	$_REQUEST['id']
), MYSQLI_ASSOC);
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

		var current = '<?=$data['fs_freight_type']?>';
		for(a=0;a<document.foo.freight_type.options.length;a++){
			if(current == document.foo.freight_type.options[a].value)
				document.foo.freight_type.options.selectedIndex = a;
		}
	}
	function searchCustomer() {
		var name=document.foo.cust_name.value;
		var url='membersearch.php?search='+name;
		var features='scrollbars, resizable, status, height=400, width=600';
		window.open(url,'Search',features);
	}
</script>
<form accept-charset="utf-8" autocomplete="off" method="post" action="edit_freight.php" name="foo">
	<table align="center" cellspacing="0" cellpadding="5">
		<tr>
			<td bgcolor="#FFFFCC">Shipment Type</td>
			<td bgcolor="#CCCCCC">
				<select name="cargo" onchange="javascript:ChangeFreight()">
					<option value="0" <?=$data['fs_ship_type'] == 0 ? "SELECTED" : ""?>>Air Cargo</option>
					<option value="1" <?=$data['fs_ship_type'] == 1 ? "SELECTED" : ""?>>Sea Cargo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Date</td>
			<td bgcolor="#CCCCCC"><input type="text" name="dated" value="<?=$data['fs_date']?>" /><input type="button" value=" \/ " onclick="gfPop.fPopCalendar(document.foo.dated)" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship From</td>
			<td bgcolor="#CCCCCC">
				<select name="from">
					<?=formatHTMLOptions(getCountryList(false), $data['fs_from'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship To</td>
			<td bgcolor="#CCCCCC">
				<select name="to">
					<?=formatHTMLOptions(getCountryList(false), $data['fs_to'])?>
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
			<td bgcolor="#CCCCCC"><input type="text" name="weightgks" value="<?=$data['fs_weight_kgs']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Shipping Weight CM3</td>
			<td bgcolor="#CCCCCC"><input type="text" name="weightcms" value="<?=$data['fs_weight_cm3']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Transit Time</td>
			<td bgcolor="#CCCCCC"><input type="text" name="tit" value="<?=$data['fs_tit']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Customer Name</td>
			<td bgcolor="#CCCCCC">
				<input type="hidden" name="cust" value="<?=$data['fs_customer']?>" />
				<input type="text" name="cust_name" value="<?=$data['name']?> <?=$data['surname']?>" />
				<input onclick="javascript:searchCustomer();" type="button" value="Search" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Fowarder</td>
			<td bgcolor="#CCCCCC">
				<select name="fowarder">
<?php
if ($data1 = $DB->query(sprintf(
	"SELECT `fow_id`, `fow_company` FROM `fowarder` ORDER BY `fow_company` ASC"
))) {
	while ($row = $DB->fetch_assoc($data1)) {
		printf(
			"<option value=\"%s\"%s>%s</option>\n",
			htmlspecialchars($row['fow_id']),
			($data['fs_fowarder'] == $row['fow_id']) ? ' selected="selected"' : '',
			htmlspecialchars($row['fow_company'])
		);
	}
	$DB->free_result($data1);
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
if ($data1 = $DB->query(sprintf(
	"SELECT `id`, `name` FROM `product_types` ORDER BY `name` ASC"
))) {
	while ($row = $DB->fetch_assoc($data1)) {
		printf(
			"<option value=\"%s\"%s>%s</option>\n",
			htmlspecialchars($row['id']),
			($data['fs_goods'] == $row['id']) ? ' selected="selected"' : '',
			htmlspecialchars($row['name'])
		);
	}
	$DB->free_result($data1);
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Currency</td>
			<td bgcolor="#CCCCCC">
				<select name="currency">
					<?php showCurrencyList($data['fs_currency'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Price</td>
			<td bgcolor="#CCCCCC"><input type="text" name="price" value="<?=$data['fs_price']?>"/></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Notes</td>
			<td bgcolor="#CCCCCC"><textarea name="notes" cols="30" rows="5"><?=stripslashes($data['fs_notes'])?></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Edit Shipment" /></td>
		</tr>
	</table>
	<input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
	<input type="hidden" name="edit" value="true" />
</form>
<script language="javascript">
	ChangeFreight();
</script>
<?php include '../footer.inc.php';?>