<!-- status.tpl -->

<?php
$customer_type = explode(',', $supplier['supplier_type']);
$product_type = explode(',', $supplier['product_type']);
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='supplier[site_id]']").change(function() {
        if ($(this).val() == '3') $(this).css('background', '#0f0').css('color', '#000');
        else $(this).css('background', '#f00').css('color', '#fff');
    }).change();
})
//]]>
</script>

<?= formatHTMLTitle('Status') ?>

<table class="table_fields">
    <tr style="line-height: 15px">
        <th>Supplier To :</th>
        <td>
			<input type="checkbox" id="member_spammer" name="supplier_type[]" value="3"<?= (in_array(3, $customer_type)) ? ' checked="checked"' : '' ?> /> <label for="member_spammer"><!-- <img src="<?php hecho('/webadmin/images/sites/site_4/logo_16.png')?>" alt="<?php hecho($site_id)?>" /> -->Coinop Express </label>&nbsp;&nbsp;
			<input type="checkbox" id="member_timewaster" name="supplier_type[]" value="2"<?= (in_array(2, $customer_type)) ? ' checked="checked"' : '' ?> /> <label for="member_timewaster"><!-- <img src="<?php hecho('/webadmin/images/sites/site_2/logo_16.png')?>" alt="<?php hecho($site_id)?>" /> -->Fortune Wave</label>&nbsp;&nbsp;
			<input type="checkbox" id="member_tester" name="supplier_type[]" value="1"<?= (in_array(1, $customer_type)) ? ' checked="checked"' : '' ?> /> <label for="member_tester"><!-- <img src="<?php hecho('/webadmin/images/sites/site_1/logo_16.png')?>" alt="<?php hecho($site_id)?>" /> -->Channelbeat </label>&nbsp;&nbsp;
            <input type="checkbox" id="member_tester1" name="supplier_type[]" value="4" <?= (in_array(4, $customer_type)) ? ' checked="checked"' : '' ?> /> <label for="member_tester1">Highway Games </label>&nbsp;&nbsp;
            <input type="hidden" name="supplier_type_affected" value="<?php hecho($supplier['customer_type']) ?>" />
		</td>
    </tr>
    <tr style="line-height: 45px">
        <th>Product Type :</th>
        <td>
            <input type="checkbox" id="member_machines" name="product_type[]" value="machines"<?= (in_array('machines', $product_type)) ? ' checked="checked"' : '' ?> /> <label for="member_machines">Machines</label>&nbsp;&nbsp;
            <input type="checkbox" id="member_pcbs" name="product_type[]" value="pcbs"<?= (in_array('pcbs', $product_type)) ? ' checked="checked"' : '' ?> /> <label for="member_pcbs">PCBs</label>&nbsp;&nbsp;
            
            <input type="checkbox" id="member_parts" name="product_type[]" value="parts" <?= (in_array('parts', $product_type)) ? ' checked="checked"' : '' ?> /> <label for="member_parts">Parts</label>&nbsp;&nbsp;

            <input type="checkbox" id="supplier_merchandies" name="product_type[]" value="merchandies"<?= (in_array('merchandies', $product_type)) ? ' checked="checked"' : '' ?> /> <label for="supplier_merchandies">Merchandies</label>&nbsp;&nbsp;
            <input type="hidden" name="product_type_affected" value="<?php hecho($supplier['product_type']) ?>" />
        </td>
    </tr>
    <tr style="line-height: 20px;">
        <th>Status :</th>
        <td><select name="supplier[site_id]">
            <?= formatHTMLOptions(array('3' => 'Active', '0' => 'Not Active'), $supplier['site_id']) ?>
        </select></td>
    </tr>
</table>
<br /><br />

<!-- /status.tpl -->
