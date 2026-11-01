<!-- order_information.tpl -->

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOrderInfo\');">Order Information</a>') ?>
<div id="sectionOrderInfo">

<table class="wide">
	<tr>
		<td class="left_title">Our Refrences</td>
		<td><input type="text" name="order_refrence" value="<?= $invoice['order_refrence'] ?>" class="intext" /></td>
	</tr>
	<tr>
		<td class="left_title">Link Your Sales Invoices</td>
		<td><input type="text" name="purchaseorder_invoice" value="<?= $invoice['proforma_purchaseorder'] ?>" class="intext" style="width:80%"/>
			<input onclick="searchinvoiceById(getValue('purchaseorder_invoice'))" type="button" value="Search" /></td>
	</tr>
	<tr>
		<td class="left_title"><input type="hidden" value="" name="linked_invoice"></td>
		<td id="seleted_invoice"><?= $invoice['linked_invoice']?></td>
	</tr>
	<tr>
		<td class="left_title">Goods Needed By</td>
		<td>
			<input name="needby_date" id="needby_date" type="text" size="24" readonly="readonly" value="<?= $invoice['needby_date'] ?>" />
			<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('needby_date'));" />
			<input type="button" value="Clear" onclick="setValue('needby_date','');" />
		</td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<?php $arraydata=explode(',', $invoice['certificate']); ?>
		<td>Certificate of Origin: <input type="checkbox" name="certificate[]" value="origin" <?= in_array('origin', $arraydata)?'checked':'' ?> >  Fumigation: <input type="checkbox" name="certificate[]" value="fumigation" <?= in_array('fumigation', $arraydata)?'checked':'' ?>></td>
	</tr>
	<tr>
		<td class="left_title">Special Requiest <br \> will be listed on P/O</td>
		<td><textarea name="special_request" value="<?= $invoice['special_request'] ?>" class="intext" rows="7"><?= $invoice['special_request'] ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="left_title">
            <h4 style="padding: 5px;">Image Refrences</h4>
            <input onclick="addgenericimage(0,2);return false;" type="button" value="Add Image">
        </td>
         <td>
            <table class="wide" style="border: 0px;margin-top: 10px;">
                <tr class="alt" id="supplier_order_information_attachment">
                   <?php 
                            $proimages=purchaseOrderimagesFromDatabase($order_id,2,0);
                            foreach ($proimages as $key => $images) { ?>
                                <td style="width:20%" id="generic_product_attach_id_<?=$images['id']?>"> 
                                    <table class="wide" style="border: 0px;margin-top: 10px;"> 
                                        <tr class="alt"> <td colspan="2"> 
                                            <img src="addGenericAttachment.php?action=view&id=<?=$images['id']?>" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;"><?=$images['caption']?></td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage(<?=$images['id']?>);"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="order_image[<?=$images['id']?>][id]" value="<?=$images['id']?>"><input type="hidden" name="order_image[<?=$images['id']?>][type]" value="<?=$images['image_type']?>"></div> <div><input type="checkbox" name="order_image[<?=$images['id']?>][email_attachment]" value="1" <?php echo ($images['email_attachment']==1)?'checked':'';?> > Email Attachment</div> </td> </tr> </table> 
                                    </td>
                           <?php } ?>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
        <td class="left_title">ORDER AUTHORISED <br \> FOR PAYMENT</td>
        <td>
            <select name="order_outth_payemnt" style="background-color: rgb(255, 0, 0);" onchange="updateSelect(this)">
                <?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'),$invoice['order_outth_payemnt']) ?>
            </select>
        </td>
    </tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOrderInfo_hideNote">
Order Information can be viewed by Clicking "Order Information" above
<br />
</div>
<script type="text/javascript">
	function searchinvoiceById(id) {
	    //var url='/webadmin/products/index.php?action=list';
	    //var url='productsearch.php?type='+producttype;
	    var url = '/webadmin/invoice/?';
	    url += 'search_for=' + id;
	    url += '&search=true';
	    url += '&action=list';
	    url += '&return_message=1';
	    //if(site_id!=null) url+='&site_id='+site_id;
	    //if(member_id!=null) url+='&member_id='+member_id;
	    var features = ''; //'scrollbars, resizable';//height=700, width=800
	    window.open(url, 'product_popup', features);
	}
	function setInvoiceData(data){
		console.log(data);
		if(data.invoice_number){
			if(getValue('linked_invoice')){
				setValue('linked_invoice', getValue('linked_invoice')+','+data.invoice_number);
			}else{
				setValue('linked_invoice', data.invoice_number);
			}
			$('#seleted_invoice').append('<a style="padding:5px;" href="/webadmin/purchase_order/?action=view&invoice_id='+data.id+'" target="_blank">'+data.invoice_number+'</a>');
		}
	}
</script>
<!-- /order_information.tpl -->
