<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 * @var mixed[]        $item
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->


<script type="text/javascript">
//<![CDATA[
function set_products_data(dataarray){
    dataarray.item_list.forEach(setProductData);
} 
function setProductData(data) {
	console.log(data);
	if (data.id === undefined) {
		return;
	}
	console.log()
	$("[name='<?=$section_name?>[product_str]']").val(data.product_name.split(" ", 2).join(" "));
	$("[name='<?=$section_name?>[product_number]']").text(data.product_number);
	$("[name='<?=$section_name?>[product_id]']").val(data.id);
	$("[name='<?=$section_name?>[product_name]']").text(data.product_name);
}
function setInvoiceData(data) {
	if (data.invoice_id === null) {
		return;
	}
	$("[name='<?=$section_name?>[sold]'][value='y']").attr("checked", true);
	$("[name='<?=$section_name?>[invoice_id]']").val(data.invoice_id);
	$("[name='<?=$section_name?>[member_id]']").val(data.member_id);
	$("[name='<?=$section_name?>[invoice_number]']").text(data.invoice_number);
	$("[name='<?=$section_name?>[bill_name]']").text(data.bill_name);
}
function detailsAddSerial() {
	$.ajax({
		url:     "/ajax/getNewSerialNumber.php",
		type:    "post",
		dataType: "json",
		data:    { reserve_by: "<?= $randumnumber?>" },
		success: function (serialdata) {
			var $serials = $("[name='<?=$section_name?>[serial_id]'], [name='<?=$section_name?>[serial_id2][]']");
			var next_id=serialdata.serial_id;
			var type=serialdata.type;
			console.log(serialdata.serial_id);
			if(type=='new'){
				$serials.each(function() {
					if (parseInt($(this).val()) >= next_id) next_id = parseInt($(this).val()) + 1;
				});
			}
			$serials.last().after("<input size=\"8\" style=\"background:#ddd;border:1px solid #bbb;\" type=\"input\" value=\"" + next_id + "\" name=\"<?=$section_name?>[serial_id2][]\" readonly=\"readonly\" />");
		}
	});
	return false;
	
}
$(function() {
	$("[name='details_product_search']").click(function() {
		window.open('/webadmin/products_new/?' + $.param({
			return_message : 'blah',
			section      : 2,
			search_for     : $("input[name='<?=$section_name?>[product_str]']").val()
		}));
	}).show();
	$("[name='details_invoice_search']").click(function() {
		window.open('/webadmin/invoice/?' + $.param({
			return_message : 'blah',
			search_for     : $("input[name='<?=$section_name?>[invoice_id]']").val()
		}));
	}).show();
	$("[name='<?=$section_name?>[product_str]']").change(function() {
		$("[name='<?=$section_name?>[product_number]']").text("");
		$("[name='<?=$section_name?>[product_name]']").text("");
		$.getJSON('/webadmin/products/ajax_request.php', {
			id      : $("input[name='<?=$section_name?>[product_str]']").val(),
			action  : 'productFromDatabase'
		}, function (data, textStatus) {
			if (textStatus == 'success') {
				setProductData(data.result);
			}
		});
	}).change();
	$("[name='<?=$section_name?>[invoice_id]']").change(function() {
		$("[name='<?=$section_name?>[sold]'][value='n']").attr("checked", true);
		$("[name='<?=$section_name?>[invoice_number]']").text("");
		$("[name='<?=$section_name?>[bill_name]']").text("");
		$.getJSON('/webadmin/invoice/ajax_request.php', {
			id     : $("input[name='<?=$section_name?>[invoice_id]']").val(),
			action : 'invoiceFromDatabase'
		}, function (data, textStatus) {
			if (textStatus == 'success') setInvoiceData(data.result);
		});
	}).change();
	$("[name='<?=htmlspecialchars($section_name . "[sold]")?>']").change(function() {
		if (this.checked) {
			switch($(this).val()) {
			case "y":
				$(<?=json_encode("." . $section_name . "_sold_y")?>).stop().show();
				$(<?=json_encode("." . $section_name . "_sold_n")?>).stop().hide();
				break;

			case "n":
				$(<?=json_encode("." . $section_name . "_sold_y")?>).stop().hide();
				$(<?=json_encode("." . $section_name . "_sold_n")?>).stop().show();
				$("[name='<?=$section_name?>[invoice_id]']").val("").change();
				break;
			}
		}
	});
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionDetails&quot;);return false">' . htmlspecialchars(_('Details')) . '</a>')?>

<div id="sectionDetails">
	<table class="table table_fields">
		<tr>
			<th><?=htmlspecialchars(_('Serial Number'))?> (<a href="#" onclick="detailsAddSerial()">Add</a>)</th>
			<td><input readonly="readonly" style="background:#ddd;border:1px solid #bbb" type="text" size="8" name="<?=htmlspecialchars($section_name . '[serial_id]')?>" value="<?=htmlspecialchars($item['serial_id'])?>" /></td>
		</tr>
		<tr>
			<th><?=htmlspecialchars(_('Machine ID'))?></th>
			<td>
				<input type="text" size="8" name="<?=htmlspecialchars($section_name . '[product_str]')?>" value="<?=htmlspecialchars($item['product_str'])?>" />
				<input type="hidden" size="8" name="<?=htmlspecialchars($section_name . '[product_id]')?>" value="<?=htmlspecialchars($item['product_id'])?>" />
				<input type="button" name="details_product_search" value="S" style="display:none" />
				<div>
					<span name="<?=htmlspecialchars($section_name . '[product_number]')?>"></span>
					<span name="<?=htmlspecialchars($section_name . '[product_name]')?>"></span>
				</div>
			</td>
		</tr>
	</table>
	<br /><br />

	<table class="table table_fields">
		<tr>
			<th></th>
			<td>
				<input type="radio" id="<?=htmlspecialchars($section_name . '_sold_n')?>" name="<?=htmlspecialchars($section_name . '[sold]')?>" value="n" />
				<label for="<?=htmlspecialchars($section_name . '_sold_n')?>">
					Stock
				</label>

				<input type="radio" id="<?=htmlspecialchars($section_name . '_sold_y')?>" name="<?=htmlspecialchars($section_name . '[sold]')?>" value="y" />
				<label for="<?=htmlspecialchars($section_name . '_sold_y')?>">
					Invoice
				</label>
			</td>
		</tr>
		<tr class="<?=htmlspecialchars($section_name . '_sold_y')?>">
			<th><?=htmlspecialchars(_('Invoice ID'))?></th>
			<td>
				<input type="text" size="8" name="<?=htmlspecialchars($section_name . '[invoice_id]')?>" value="<?=htmlspecialchars($item['invoice_id'])?>" />
				<input type="hidden" name="<?=htmlspecialchars($section_name . '[member_id]')?>" value="<?=htmlspecialchars($item['member_id'])?>" />
				<input type="button" value="S" name="details_invoice_search" style="display:none" />
				<div>
					<span name="<?=htmlspecialchars($section_name . '[invoice_number]')?>"></span>
					<span name="<?=htmlspecialchars($section_name . '[bill_name]')?>"></span>
				</div>
			</td>
		</tr>
	</table>
	<br /><br />

</div>
<div id="sectionDetails_hideNote" style="background:#fff;text-align:center;padding:8px 8px 8px 8px;display:none">
	<?=htmlspecialchars(sprintf(_('%s can be viewed by clicking "%s" above'), _('Details'), _('Details')))?><br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
