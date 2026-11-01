<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include '../currency.php';
include "freight_menu.php";
$Cur = ($_POST['currency']) ? ($_POST['currency']) : "USD";
?>
<script>
	function InitValues(){
		var current = '<?=$_POST['freight_type']?>';
		var freight = '<?=$_POST['cargo']?>';
		//Changes Cargo Type
		for(a=0;a<document.foo.cargo.options.length;a++){
			if(freight == document.foo.cargo.options[a].value)
				document.foo.cargo.options.selectedIndex = a;
		}
		ChangeFreight()
		//Changes Freight Type
		for(a=0;a<document.foo.freight_type.options.length;a++){
			if(current == document.foo.freight_type.options[a].value){
				document.foo.freight_type.options.selectedIndex = a;
			}
		}
	}
	function ChangeFreight(){
		//Clears Box
		for(a = document.foo.freight_type.options.length; a >= 0; a--){
			document.foo.freight_type.options[a] = null;
		}

		//Checks For New Values, Addes Options For Them
		if(document.foo.cargo.value=="1"){
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('','');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('LCL','LCL');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('20ft','20ft');
			document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('40ft','40ft');
		}else{
			if(document.foo.cargo.value=="0"){
				document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('','');
				document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('Direct','Direct');
				document.foo.freight_type.options[document.foo.freight_type.options.length] = new Option('Indirect','Indirect');
			}
		}
	}
	function searchCustomer() {
		var name=document.foo.cust_name.value;
		var url='membersearch.php?search='+name;
		var features='scrollbars, resizable, status, height=400, width=600';
		window.open(url,'Search',features);
	}
</script>
<form accept-charset="utf-8" autocomplete="off" method="post" name="foo" action="search.php">
    <table align="center" cellspacing="0" cellpadding="5">
    	<tr>
    		<td colspan="2"></td>
    	</tr>
    	<tr>
    		<td bgcolor="#FFFFCC">Customer Name</td>
    		<td bgcolor="#CCCCCC">
				<input type="hidden" name="cust" value="" />
				<input type="text" name="cust_name" value="" />
				<input onclick="javascript:searchCustomer();" type="button" value="Search" />
			</td>
    	</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship From</td>
			<td bgcolor="#CCCCCC">
				<select name="from">
					<option value=''></option>
					<?=formatHTMLOptions(getCountryList(false), $_POST['from'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Ship To</td>
			<td bgcolor="#CCCCCC">
				<select name="to">
					<option value=''></option>
					<?=formatHTMLOptions(getCountryList(false), $_POST['to'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Shipment Type</td>
			<td bgcolor="#CCCCCC">
				<select name="cargo" onchange="javascript:ChangeFreight()">
					<option value=""<?=$_POST['fs_ship_type'] == '' ? "SELECTED" : ""?>></option>
					<option value="0" <?=$_POST['fs_ship_type'] == '0' ? "SELECTED" : ""?>>Air Cargo</option>
					<option value="1" <?=$_POST['fs_ship_type'] == '1' ? "SELECTED" : ""?>>Sea Cargo</option>
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
			<td bgcolor="#FFFFCC">Show Currency</td>
			<td bgcolor="#CCCCCC">
				<select name="currency">
					<?=formatHTMLOptions(getCurrencyList(false), $Cur)?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value=" Search " />
			</td>
		</tr>
    </table>
	<input type="hidden" name="search" value="true" />
</form>
<script>
	InitValues();
</script>
<?php
//Makes Data
$FTMP  = trim($_POST['freight_type']);
$Cust  = (trim($_POST['cust']) != "") ? "AND fs_customer    ='" . trim($_POST['cust']) . "'" : "";
$From  = (trim($_POST['from']) != "") ? "AND fs_from        ='" . trim($_POST['from']) . "'" : "";
$To    = (trim($_POST['to']) != "") ? "AND fs_to          ='" . trim($_POST['to']) . "'" : "";
$Cargo = (trim($_POST['cargo']) != "") ? "AND fs_ship_type   ='" . trim($_POST['cargo']) . "'" : "";
$FT    = (trim($_POST['freight_type']) != "") ? "AND fs_freight_type='" . trim($_POST['freight_type']) . "'" : "";

//Gets Search Data
?>
<table width="80%" align="center" cellspacing="0">
	<tr bgcolor="#CCCCCC">
		<td>ID</td>
		<td>Name</td>
		<td>To</td>
		<td>From</td>
		<td>Weight</td>
		<td>Price(USD)</td>
		<td>Price(<?=$Cur?>)</td>
		<td></td>
	</tr>
<?php
$country_list = getCountryList(false);

if ($data = $DB->query(sprintf(
	"SELECT `fs`.`fs_id`, `m`.`name`, `m`.`surname`, `fs`.`fs_weight_kgs`, `fs_usd_price` FROM `freight_shipped` AS `fs` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `fs`.`fs_customer` = `m`.`member_id` WHERE 1 %s %s %s %s %s",
	$Cust,
	$From,
	$To,
	$Cargo,
	$FT
))) {
	$alt = false;
	while ($row = $DB->fetch_assoc($data)) {
?>
	<tr<?= ($alt = !$alt) ? ' bgcolor="#ffffcc"' : '' ?>>
		<td><?=htmlspecialchars($row['fs_id'])?></td>
		<td><?=htmlspecialchars(trim($row['name'] . ' ' . $row['surname']))?></td>
		<td><?=htmlspecialchars($country_list[$row['fs_from']] ?? $row['fs_from'])?></td>
		<td><?=htmlspecialchars($country_list[$row['fs_to']] ?? $row['fs_to'])?></td>
		<td><?=htmlspecialchars($row['fs_weight_kgs'])?></td>
		<td><?=htmlspecialchars($row['fs_usd_price'])?></td>
		<td><?=showPriceFrom($row['fs_usd_price'], "USD", $Cur)?></td>
		<td><input type="button" value="edit" onclick="document.location.href='edit_freight.php?id=<?=$row['fs_id']?>'" /></td>
	</tr>
<?php
	}
	$DB->free_result($data);
}
?>
</table>
<?php
include '../footer.inc.php';
