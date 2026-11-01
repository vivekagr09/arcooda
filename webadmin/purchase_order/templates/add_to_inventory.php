<!-- invoice_comments.tpl -->
<script type="text/javascript">
 $('body').on('click', '#add_to_inventory_checkbox', function(){ 
        if($(this).is(":checked")){
            $('#supplier_inventory').show();
        }else{
            $('#supplier_inventory').hide();
        }
    });
</script>
<?php
$proforma_comments = $invoice['proforma_comments'];
if (!is_array($proforma_comments)) {
    $proforma_comments = explode(',', $proforma_comments);
}

?>
<?php 
$stock = productOrderFromDatabase($invoice['inventory_order_id']);
?>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'add_to_inventory\');">Add to Inventory</a>') ?>
<script type="text/javascript">
//<![CDATA[
function validateInventoryForm(form)
{
    var pass = true;
    $(".required", form).each(function() {
        if ($(this).val() == "")
        {
            pass = false;
            $(this).css("background", "#faa").css("border", "2px solid #f00");
        }
        else $(this).css("background", "").css("border", "");
    });
    if (!pass)
    {
        alert("Please fill the details highlighted in red before continuing");
        return false;
    }

    var rows = [];
    $("[id^='stock_additions_rowa[']").each(function ()
    {
        rows.push($(this).attr("id").substr(20));
    });
    for (var i = 0;i < rows.length;i++)
    {
        var name_key = "stock_additions" + rows[i];
        weight = parseFloat($("[name='" + name_key + "[product_weight]']").val());
        if (isNaN(weight) || weight == 0.0)
        {
            alert("One of your products has undefined weight.\n\nPlease contact the product manager before proceeding");
            return false;
        }
    }

    return confirm("Are you sure you wish to enter this data into the system?\n\nThere will be no way to undo these changes");
    return true;
}
//]]>
</script>
<div id="add_to_inventory">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;white-space:nowrap;">
				Add to Inventory
			</td>
			<td>
				<input type="checkbox" name="add_to_inventory" value="y" <?= ($invoice['add_to_inventory']=='y') ? ' checked="checked"' : '' ?> style="margin-left: 5px;" id="add_to_inventory_checkbox" />
				<?=  count($stock)?'Added By '.$all_staff_list[$stock['created_by']].' on  '.date("d/m/Y",strtotime($stock['created_at'])).'':'' ?>
			</td>
		</tr>
	</table>
	<div style=" <?= ($invoice['add_to_inventory']!='y') ? 'display:none;' : '' ?>" id="supplier_inventory">
		<?php
		include __DIR__ . '/supplier_details.tpl.php';
		include __DIR__ . '/stock_additions.tpl.php';
		?>
	</div>
	<div style="text-align:center;padding: 5px 10px 63px 5px;"><input type="button" onclick="if(validateInventoryForm(this.form)) this.form.submit();" value="Add Inventory" /></div>
</div>


<!-- /invoice_comments.tpl -->
