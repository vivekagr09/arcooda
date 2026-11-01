<!-- forwarders_warehouse.tpl.php -->

<?php
function warehouse_row($wcount='[WAREHOUSE_COUNT]',$warehouse=array())
{
	ob_start();
?>
<table style="width:100%;">
	<tr>
		<td class="left_title" style="width:10%;">Company Name</td>
		<td style="width:40%;"><input type="hidden" name="warehouse[<?php echo $wcount ?>][id]" value="<?php hecho($warehouse['id']) ?>" /><input name="warehouse[<?php echo $wcount ?>][name]" type="text" style="width:98%;" value="<?php hecho($warehouse['name']) ?>" /></td>
		<td class="left_title" style="width:10%;">Phone</td>
		<td style="width:40%;"><input name="warehouse[<?php echo $wcount ?>][phone]" type="text" style="width:98%;" value="<?php hecho($warehouse['phone']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title" rowspan="2">Address</td>
		<td rowspan="2"><textarea name="warehouse[<?php echo $wcount ?>][address]" style="width:98%;height:98%;"><?php hecho($warehouse['address']) ?></textarea></td>
		<td class="left_title">Fax</td>
		<td><input name="warehouse[<?php echo $wcount ?>][fax]" type="text" style="width:98%;" value="<?php hecho($warehouse['fax']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Mobile</td>
		<td><input name="warehouse[<?php echo $wcount ?>][mobile]" type="text" style="width:98%;" value="<?php hecho($warehouse['mobile']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">City</td>
		<td><input name="warehouse[<?php echo $wcount ?>][city]" type="text" style="width:98%;" value="<?php hecho($warehouse['city']) ?>" /></td>
		<td class="left_title"></td>
		<td></td>
	</tr>
	<tr>
		<td class="left_title">State</td>
		<td><input name="warehouse[<?php echo $wcount ?>][state]" type="text" style="width:98%;" value="<?php hecho($warehouse['state']) ?>" /></td>
		<td class="left_title"></td>
		<td></td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input name="warehouse[<?php echo $wcount ?>][post_code]" type="text" style="width:98%;" value="<?php hecho($warehouse['post_code']) ?>" /></td>
		<td class="left_title">Email</td>
		<td><input name="warehouse[<?php echo $wcount ?>][email]" type="text" style="width:98%;" value="<?php hecho($warehouse['email']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td><select name="warehouse[<?php echo $wcount ?>][country]"><option value=""></option><?php echo formatHTMLOptions(getCountryList(),$warehouse['country']) ?></select></td>
		<td class="left_title">Website</td>
		<td><input name="warehouse[<?php echo $wcount ?>][website]" type="text" style="width:98%;" value="<?php hecho($warehouse['website']) ?>" /></td>
	</tr>
</table>
<?php
	return ob_get_clean();
}
?>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWarehouses\');">Freight Forwarder Warehouse Details</a>','<a href="javascript:copyWarehouse();">Copy From Above</a> | <a href="javascript:addWarehouse();">Add New Address</a>') ?>
<div id="sectionWarehouses">

<script type="text/javascript">
var firstWarehouse=1;
var lastWarehouse=<?php echo max(0,count($forwarder_warehouses)) ?>;

function addWarehouse()
{
	lastWarehouse++;
	var wcount=String(lastWarehouse);
	var ware_table=getElement('warehouse_table');
	if(ware_table==null) return;

	var newRow1=addTableRow(ware_table,'warehouse_row['+wcount+']');
	addTableCell(newRow1,<?php echo str_replace('[WCOUNT]', '" + wcount + "', jecho(warehouse_row('[WCOUNT]'), 1)) ?>);
}

function copyWarehouse()
{
<?php
foreach(array('name','phone','address','fax','address2','mobile','city','state','post_code','email','country','website') as $key)
	echo "	setValue('warehouse[0][".$key."]',getValue('forwarder[".$key."]'));\n";
?>
}
</script>

<table class="wide" id="warehouse_table">
<?php
$wcount=0;
$warehouse_affected=array();
if(count($forwarder_warehouses)>0)
	foreach($forwarder_warehouses as $warehouse)
	{
		echo '<tr id="warehouse_row['.$wcount.']"><td>'.warehouse_row($wcount++,$warehouse)."</td></tr>\n";
		$warehouse_affected[]=$warehouse['id'];
	}
else
{
	$temp=$forwarder;
	unset($temp['id']);
	echo '<tr id="warehouse_row[0]"><td>'.warehouse_row(0,$temp)."</td></tr>\n";
}
?>
</table>

<input type="hidden" name="warehouse_affected" value="<?php echo implode(',',$warehouse_affected) ?>" />

<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWarehouses_hideNote">
Freight Forwarder Warehouse Details can be viewed by Clicking "Freight Forwarder Office Details" above
<br />
</div>

<!-- /forwarders_warehouse.tpl.php -->
