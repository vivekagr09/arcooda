<?php
/**
 * @var int $supplier_id
 */

moduleLoad('invoice'); //invoiceGetOptionList

global $_GLOBALS;

$default        = array(0 => 'Not Selected');
$warranty_list  = $default + invoiceGetOptionList('warranty_terms_id');
$conditions     = $default + invoiceGetOptionList('condition');
$packing_terms  = $default + invoiceGetOptionList('packing_requirement');
$voltage_terms  = $default + invoiceGetOptionList('voltage_id2');
$shipping_terms = $default + invoiceGetOptionList('terms_id');
$plugs_list     = $default + getInvoicePlugList();
$coin_mech      = $default + getCoinMechList();
$version_type   = $default + getVersionType();
$company        = array(1 => 'Highway Games', 2 => 'Highway Entertainment', 9 => 'Converters.tv', 11 => 'Highway Group', 16 => 'Car Converters');
//$condition=array(1=>'Brand New', 2=>'Used');
$sections    = getProductSections();
$sections[0] = 'N/A';

$default_values = supplierValuesFromDatabase($supplier_id);
if (!is_array($default_values)) {
    $default_values = array();
}

?>
<script>
var sections   = <?=json_encode($sections)?>;
var companies  = <?=json_encode($company)?>;
var conditions = <?=json_encode($conditions)?>;
var values     = 0;
function addSupplierValues(company, section, condition)
{
	var table = getElement('default');
	if(table == null) return;
	var remote_user = <?=htmlspecialchars($_GLOBALS['staff']['user'])?>;
	var newRow = addTableRow(table,'default_row['+values+']');
	var newCell=addTableCell(newRow,'<table class="wide" align="center" style="width:100%" border=0><tr><td class="left_title" style="width:20%;">Supplier For :</td><td style="width:30%;">'+companies[company]+'</td><td class="left_title" style="width:20%;">Entered By :</td><td style="width:30%;">'+remote_user+'</td></tr><tr><td class="left_title" style="width:20%;">Section :</td><td style="width:30%;">'+sections[section]+'</td><td class="left_title" style="width:20%;">Date :</td><td style="width:30%;"><?=date('d/m/y');?></td></tr><tr><td class="left_title" style="width:20%;">Product Condition :</td><td style="width:30%;">'+conditions[condition]+'</td><td class="left_title" style="width:20%;"></td><td style="width:30%;"></td></tr><tr><td colspan="4">&nbsp;</td></tr><tr><td style="width:20%;" class="left_title">Warranty :</td><td style="width:30%;"><select name="values['+values+'][warranty]"><?=formatHTMLOptions($warranty_list, "", "a")?></select></td><td style="width:20%;" class="left_title">Voltage :</td><td style="width:30%;"><select name="values['+values+'][voltage]"><?=formatHTMLOptions($voltage_terms, "", "a")?></select></td></tr><tr><td style="width:20%;" class="left_title">Plug Type :</td><td style="width:30%;"><select name="values['+values+'][plug]"><?=formatHTMLOptions($plugs_list, "", "a")?></select></td><td style="width:20%;" class="left_title">Condition :</td><td style="width:30%;"><select name="values['+values+'][condition]"><?=formatHTMLOptions($conditions, "", "a")?></select></td></tr><tr><td style="width:20%;" class="left_title">Packing :</td><td style="width:30%;"><select name="values['+values+'][packing]"><?=formatHTMLOptions($packing_terms, "", "a")?></select></td><td style="width:20%;" class="left_title">Terms :</td><td style="width:30%;"><select name="values['+values+'][terms]"><?=formatHTMLOptions($shipping_terms, "", "a")?></select></td></tr><tr><td style="width:20%;" class="left_title">Version Type :</td><td style="width:30%;"><select name="values['+values+'][version]"><?=formatHTMLOptions($version_type, "", "a")?></select></td><td style="width:20%;" class="left_title">Coin Mech :</td><td style="width:30%;"><select name="values['+values+'][coin]"><?=formatHTMLOptions($coin_mech, "", "a")?></select></td></tr></table><input type="hidden" name="values['+values+'][company]" value="'+company+'"/><input type="hidden" name="values['+values+'][section]" value="'+section+'"/>');
	// now have to set the value of the condition field
	setValue('values['+values+'][condition]', condition);
	values--;
}
</script>
<?php $extra='<a href="#" onclick="openWindow(\'addDefault.php\',400,250,true);return false;">Add Default Values</a>';?>
<div align="center"><?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionDef\');">Supplier Default Values</a>', $extra)?></div>
<div id="sectionDef">
<table class="wide" style="width:98%;text-align: center" align="center" cellpadding=0 cellspacing=0>
	<tbody id="default">
<?php
foreach ($default_values as $value) {
    $id = $value['id'];
?>
	<tr id="default_row[<?=$id?>]">
		<td><table class="wide" align="center" style="width:100%" border=0>
			<tr>
				<td class="left_title" style="width:20%;">Supplier For :</td>
				<td style="width:30%;"><?=$company[$value['company']]?></td>
				<td class="left_title" style="width:20%;">Entered By :</td><td style="width:30%;"><?=$value['staff']?></td>
			</tr>
			<tr>
				<td class="left_title" style="width:20%;">Section :</td><td style="width:30%;"><?=$sections[$value['section']]?></td>
				<td class="left_title" style="width:20%;">Date :</td><td style="width:30%;"><?=date('d/m/y', $value['time_added']);?></td>
			</tr>
			<tr>
				<td class="left_title" style="width:20%;">Product Condition :</td>
				<td style="width:30%;"><?=$conditions[$value['condition']]?></td>
				<td class="left_title" style="width:20%;"></td><td style="width:30%;"></td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr>
				<td style="width:20%;" class="left_title">Warranty :</td>
				<td style="width:30%;">
					<select name="values[<?=$id?>][warranty]"><?=formatHTMLOptions($warranty_list, $value['warranty'])?></select>
				</td>
				<td style="width:20%;" class="left_title">Voltage :</td>
				<td style="width:30%;"><select name="values[<?=$id?>][voltage]"><?=formatHTMLOptions($voltage_terms, $value['voltage'])?></select></td>
			</tr>
			<tr>
				<td style="width:20%;" class="left_title">Plug Type :</td>
				<td style="width:30%;"><select name="values[<?=$id?>][plug]"><?=formatHTMLOptions($plugs_list, $value['plug'])?></select></td>
				<td style="width:20%;" class="left_title">Condition :</td>
				<td style="width:30%;"><select name="values[<?=$id?>][condition]"><?=formatHTMLOptions($conditions, $value['condition'])?></select></td>
			</tr>
			<tr>
				<td style="width:20%;" class="left_title">Packing :</td>
				<td style="width:30%;">
					<select name="values[<?=$id?>][packing]"><?=formatHTMLOptions($packing_terms, $value['packing'])?></select>
				</td>
				<td style="width:20%;" class="left_title">Terms :</td>
				<td style="width:30%;">
					<select name="values[<?=$id?>][terms]"><?=formatHTMLOptions($shipping_terms, $value['terms'])?></select>
				</td>
			</tr>
			<tr>
				<td style="width:20%;" class="left_title">Version Type :</td>
				<td style="width:30%;">
					<select name="values[<?=$id?>][version]"><?=formatHTMLOptions($version_type, $value['version'])?></select>
				</td>
				<td style="width:20%;" class="left_title">Coin Mech :</td>
				<td style="width:30%;">
					<select name="values[<?=$id?>][coin]"><?=formatHTMLOptions($coin_mech, $value['coin'])?></select>
				</td>
			</tr>
		</table></td>
	</tr>
<?php
}
?>
	</tbody>
</table>
</div>
<br />