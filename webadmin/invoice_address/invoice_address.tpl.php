<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Invoice Addresses</a>','<a onclick="addAddress();return false">Add New </a>', false)?>

<script type="text/javascript">
function invoice_address_data_delete(rowid,dataid) {
	var isMain = getValue('address['+rowid+'][main]');
	if(isMain == 'y'){
		alert('You can not delete this address this is a default address');
		return false;
	}
	if (confirm('Are you sure you want to delete the address?')) {
		var theRow = getElement("invoice_address_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("invoice_address_delete", getValue("invoice_address_delete") + "," + dataid);
        }
	}
}
var addess_count="<?=count($invoice_addresses); ?>";
function addAddress()
{

html='<tr ssid="'+addess_count+'" id="invoice_address_rowa['+addess_count+']">\
	<td style="text-align:left"><input type="text" name="address['+addess_count+'][dropdown_name]"  style="width:90%"></td>\
	<td style="text-align:left"><textarea rows="5" name="address['+addess_count+'][address]"  style="width:90%"></textarea></td>\
	<td><input type="button" value="Delete" onclick="invoice_address_data_delete('+addess_count+',0)" /></td>\
</tr>'
	
$('#invoiceAddresses').append(html);
addess_count++;
}
//]]>
</script>

<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:15%">Dropdown Name</th>
			<th style="width:70%">Address</th>
			<th style="width:80%">Default</th>
			<th style="width:5%">Delete</th></tr>
		</tr>
	</thead>
	<tbody id="invoiceAddresses">
		
<?php
$count = 0;
foreach ($invoice_addresses as $address) {
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="invoice_address_rowa[<?=$count ?>]" ssid="<?=$count ?>">
		<td style="text-align:left">
			<input type="text" name="address[<?= $count ?>][dropdown_name]" value="<?=$address['dropdown_name'] ?>" style="width:90%">
			<input type="hidden" name="address[<?= $count ?>][id]" value="<?=$address['id'] ?>">
		</td>
		<td style="text-align:left">
			<textarea rows="5" name="address[<?= $count ?>][address]" value="<?=$address['address'] ?>" style="width:90%"><?=$address['address'] ?></textarea>
			
		</td>
		<td style="text-align:center;">
			<select name="address[<?= $count ?>][main]" class="setdefaultvalue">
				<option value="y" <?=($address['main']=='y')?'selected':'' ?>>Yes</option>
				<option value="n" <?=($address['main']=='n')?'selected':'' ?>>No</option>
			</select>
			
		</td>
		<td><input type="button" value="Delete" onclick="invoice_address_data_delete(<?= $count ?>,<?=$address['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="invoice_address_delete" value="" />
	</tfoot>
</table>
</div>

<br />
<script>
$('.setdefaultvalue').change(function(){
	$('.setdefaultvalue').val('n');
	$(this).val('y');
})
function validateaddressForm(){
	var selectFields = document.getElementsByClassName("setdefaultvalue");
    var anySelected = false;
    for (var i = 0; i < selectFields.length; i++) {
        if (selectFields[i].value == "y") {
            anySelected = true;
            break;
        }
    }
    if (!anySelected) {
        alert("Please set default address.");
        return false;
    }
    return true;
}
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
