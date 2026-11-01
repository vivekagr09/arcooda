<!-- members_serial_numbers.tpl -->

<?php
moduleLoad('invoice'); //invoiceFromDatabase

$section_name = 'product_serial';

function item_serial_row($count = "[ROW_COUNT]", $row_data = array())
{
    $section_name = 'product_serial';
    $name_key     = $section_name . "[" . $count . "]";
    $hidden       = array(
        $name_key . "[id]"         => $row_data["id"],
        $name_key . "[serial_id]"  => $row_data["serial_id"],
        $name_key . "[product_id]" => $row_data["product_id"],
        $name_key . "[invoice_id]" => $row_data["invoice_id"],
        $name_key . "[member_id]"  => $row_data["member_id"],
    );
    ob_start();
?>
	<tr id="item_serial_rowa[<?=$count?>]"<?=((intval($count) & 1) ? ' class="alt"' : "")?>>
		<td style="white-space:nowrap;" id="member_id_<?=$count?>">

<?php
    if ($row_data['product_id']) {
        echo "<a href='/webadmin/products_new/edit.php?action=do_edit&amp;id=" . $row_data['product_id'] . "' target='_blank'>" . $row_data['product_str'] . "</a>";
    }
?>
		</td>
		<td style="white-space:nowrap;" id="member_id_<?=$count?>">

<?php
    if ($row_data['product_id']) {
        echo "<a href='/webadmin/products_new/edit.php?action=do_edit&amp;id=" . $row_data['product_id'] . "' target='_blank'>" . $row_data['product_id'] . "</a>";
    }
?>
		</td>
		<td style="white-space:nowrap;">
				<?=formatHTMLInputs($hidden)?>
			<?=sprintf('SN#00%s', $row_data['serial_id'])?>
		</td>

		<td style="white-space:nowrap;">
<?php
    echo "<a href='/webadmin/invoice/invoice_view.php?invoice_id=" . $row_data['invoice_id'] . "' target='_blank'>" . $row_data['invoice_number'] . "</a>";
?>
		</td>
		<td style="white-space:nowrap;">
			<?=$row_data['created']?>
		</td>
		<td>
			<a style="cursor:pointer;" onclick="return item_serialno_delete(<?=$count?>,<?=$row_data['id']?>)">Remove</a>
		</td>
	</tr>
<?php
    return ob_get_clean();
}

$randumnumber = rand();
?>
<script type="text/javascript">
//<![CDATA[
function assignSerialNumber(){
	window.open('addSerialNumber.php?id=&action=add','','width=700,height=700')
}
var serial_count="<?=count($member_serials) + 1?>";
function addSerialNumber(data){
	data.forEach(addserial_number);
}
function addserial_number(data){
	console.log(data);
	var row_string = <?=json_encode(item_serial_row('[SERIAL_LINKING_COUNT]', array('member_id' => $member['id'], 'serial_id' => '[SERIAL_ID]', 'id' => '[ROW_ID]', 'created' => '[CREATED]', 'invoice_id' => '[INVOICE_ID]', 'invoice_number' => '[INVOICE_NUMBER]', 'product_id' => '[PRODUCT_ID]', 'serial_number' => '[SERIAL_NUMBER]', 'product_str' => '[PRODUCT_STR]')))?>;
		data.product_str=(data.product_str)?data.product_str:'';
		data.product_id=(data.product_id)?data.product_id:'';
		data.invoice_number=(data.invoice_number)?data.invoice_number:'';
		data.invoice_id=(data.invoice_id)?data.invoice_id:'';
		row_string = row_string.replace(/\[SERIAL_LINKING_COUNT\]/g, serial_count);
		row_string = row_string.replace(/\[SERIAL_ID\]/g, data.serial_id);
		row_string = row_string.replace(/\[ROW_ID\]/g, data.id);
		row_string = row_string.replace(/\[CREATED\]/g, data.created);
		row_string = row_string.replace(/\[INVOICE_ID\]/g, data.invoice_id);
		row_string = row_string.replace(/\[INVOICE_NUMBER\]/g, data.invoice_number);
		row_string = row_string.replace(/\[PRODUCT_ID\]/g, data.product_id);
		row_string = row_string.replace(/\[SERIAL_NUMBER\]/g, data.serial_number);
		row_string = row_string.replace(/\[PRODUCT_STR\]/g, data.product_str);
		$("#item_serial_table").append(row_string);
		serial_count++;
}
function item_serialno_delete(id,dataid) {
	if (confirm("Are you sure you want to delete  this serial no ??")) {
		var theRow = getElement("item_serial_rowa["+id+"]");
		if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		if(dataid>0)
		setValue("item_sereal_id_deleted", getValue("item_sereal_id_deleted") + "," + dataid);
	}
}
function serachserialmember(retunmsg){

	var url = "list.php?return_message="+retunmsg
	url += "&search_for=";
	window.open(url);
}
function setMemberData(data){
	if(data.return_message){
		setValue('product_serial['+data.return_message+'][member_id]',data.member_id);
		$('#member_id_'+data.return_message).html('<a href=".?action=edit&amp;member_id='+data.id+'" target="_blank">'+data.first_name+' '+data.last_name+'</a>');

	}
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;machine_serial_number&quot;);return false" class="unfinished">Machine And Software Serial Number</a>', '<a stylr="cursor:pointer;" onclick="assignSerialNumber()">Add</a><i class="far fa-question-circle" style="margin-left: 10px;font-size:15px;" onmouseover="tt_in(\'If the customer has invoice number, add the serial number through the invoice section. If the customer has no invoice, please use the add link\')" onmouseout="tt_out();"></i>')?>

<div id="machine_serial_number">
    <table class="table_grid" id="item_serial_table">
		<tr>
			<th>Product Name</th>
			<th>Product ID</th>
			<th>Serial #</th>
			<th>Invoice</th>
			<th>Date Ordered</th>
			<th></th>
		</tr>
<?php
$count    = 0;
$affected = array();

foreach ($member_serials as $links) {
    $links['created'] = ($links['created']) ? date(_('d/m/Y'), strtotime($links['created'])) : 'Now';
    if ($links['invoice_id']) {
        $invoice                 = invoiceFromDatabase($links['invoice_id']);
        $links['invoice_number'] = $invoice['invoice_number'];
    } else {
        $links['invoice_number'] = '';
    }
    echo item_serial_row($count++, $links);
}
?>
	</table>
	<input type="hidden" name="item_sereal_id_deleted" value="" />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionItem_hideNote">
   We will work on it further<br />
</div>

<!-- /members_serial_numbers.tpl -->
