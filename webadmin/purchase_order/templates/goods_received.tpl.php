<!-- product_supplier.tpl -->
<style>
    .invoice_generic{
        background-color: #ffccf6de!important;
    }
</style>
<script type="text/javascript">
    function updatecheckedby(row,cb){
        if(cb.checked==true){
            setValue("checked_by["+row+"]","<?= $_GLOBALS['staff']['id'] ?>");
        }else{
            setValue("checked_by["+row+"]",0);
        }

    }
    function updateapprovedby(row,cb){
         if(cb.checked==true){
            setValue("approved_by["+row+"]","<?= $_GLOBALS['staff']['id'] ?>");
        }else{
            setValue("approved_by["+row+"]",0);
        }
        
    }
</script>
<?php
function product_row_received($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = false)
{
   
    ob_start();

    global $invoice, $_GLOBALS, $show_usd;
    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'USD';
    }
    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }
    ob_start();
?>
<tr style="line-height: 20px; font-size: 14px;">
       <th style="width:10%">Supplier Id</th>
        <th style="width:50%">Supplier Product Name</th>
        <th style="width:10%">Qty.</th>
        <th style="width:10%">Cost Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        <th style="width:10%">Offer <?=($show_usd) ? 'USD' : 'AUD'?></th>
    </tr>
<tr>
    <td>
        <?=formatHTMLInputs($hidden)?>
        <input  type="text" value="<?php hecho($product['product_id'])?>" style="text-align:right" size="10" maxlength="15" />
    </td>
    <td><input type="text" value="<?php hecho($product['product_name'])?>" style="width:100%" /></td>
    <td><input  type="text" value="<?php hecho($product['product_qty'])?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <td><input  type="text" value="<?php hecho(convertCurrency($product['product_cost'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" /></td>
    <td>
        <input name="product_offerus[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pofferus')" type="text" value="<?php hecho(convertCurrency($product['product_offerus'], 'USD', '', false, $product['decimal']))?>" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" size="10" readonly="readonly" /></td>
</tr>
?>
<?php
return ob_get_clean();
}
function product_row_generic_received($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = false,$delete=false,$search=true)
{
    
    ob_start();
     global $invoice, $warranty_terms, $condition_terms, $packing_terms, $voltage_terms, $shipping_terms, $plugs_list, $products_need_update, $site_id, $_GLOBALS, $show_usd;
    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'USD';
    }
    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }
    ob_start();
?>
<tr  class="invoice_generic">
    <th class="invoice_generic">Our Id</th>
    <th class="invoice_generic">Our Product Name</th>
    <th class="invoice_generic"></th>
    <th class="invoice_generic"></th>
    <th class="invoice_generic"></th>
</tr>
<tr class="invoice_generic" >
    <td>
        <input  type="text"  style="text-align:right" size="10" maxlength="15" value="<?php echo ($delete)? 'GME':hecho($product['product_id']);?>"/>
    </td>
    <td colspan="3">
        <input class="invoice_product_name"  type="text" value="<?php hecho($product['supplier_productname'])?>" style="width:100%" />
    </td>
    <td style="text-align: right;padding: 10px;">
        <input type="hidden" value="<?= $product['checked_by']?>" name="checked_by[<?=$pcount?>]">
        <input type="checkbox" value="y" name="checked[<?=$pcount?>]" <?php echo  ($product['checked']=='y')?'checked':''; ?>  onclick="updatecheckedby(<?=$pcount?>,this)"> CHECKED
            <?php if($product['checked_by']>0){
            $staff=staffFromDatabase($product['checked_by']);
            if($staff){
               echo '<p>'.$staff["first_name"]." ".$staff["last_name"].'</p>';
            }
        } ?>
    </td>
</tr>
<tr class="alt invoice_generic" id="supplier_rowb[<?=$pcount?>]">
    <td>
    Notes</td>
    <td colspan="3"><textarea cols="80" name="product_received_notes[<?=$pcount?>]" rows="<?=max(3, count(explode("\n", trim($product['product_received_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" onchange="setValue('product_updated[<?=$pcount?>]', 'y')" style="width:100%"><?php hecho(trim($product['product_received_notes']))?></textarea></td>
    <td style="text-align: right;padding: 10px;">
        <input type="hidden" value="<?= $product['approved_by']?>" name="approved_by[<?=$pcount?>]">
        <input type="checkbox" value="y" name="approved[<?=$pcount?>]" <?php echo  ($product['approved']=='y')?'checked':''; ?> onclick="updateapprovedby(<?=$pcount?>,this)"> APPROVED
        <?php if($product['approved_by']>0){
            $staff=staffFromDatabase($product['approved_by']);
            if($staff){
               echo '<p>'.$staff["first_name"]." ".$staff["last_name"].'</p>';
            }
        }
        ?>
    </td>
</tr>

<tr class="alt" id="supplier_rowh[<?=$pcount?>]">
    <td colspan="5">
        <table class="wide" style="border: 0px;margin-top: 10px;">
            <tr class="alt">
                <td style="width:15%">
                    <h4 style="padding: 5px;">Image Refrences</h4>
                    <input onclick="addgenericimage(<?=$pcount?>,4);return false;" type="button" value="Add Image">
                </td>
                <td style="width:85%;">
                    <table class="wide" style="border: 0px;margin-top: 10px;">
                        <tr class="alt" id="supplier_generic_product_image_received_<?=$pcount?>">
                           <?php 
                            $proimages=purchaseOrderimagesFromDatabase($product['order_id'],4,$product['id']);
                            foreach ($proimages as $key => $images) { ?>
                                <td style="width:20%" id="generic_product_attach_id_<?=$images['id']?>"> 
                                    <table class="wide" style="border: 0px;margin-top: 10px;"> 
                                        <tr class="alt"> <td colspan="2"> 
                                            <img src="addGenericAttachment.php?action=view&id=<?=$images['id']?>" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;"><?=$images['caption']?></td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage(<?=$images['id']?>);"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="product_image_received[<?=$pcount?>][<?=$images['id']?>][id]" value="<?=$images['id']?>">
                                                <input type="hidden" name="product_image_received[<?=$pcount?>][<?=$images['id']?>][product_id]" value="<?=$images['product_id']?>">
                                                <input type="hidden" name="product_image_received[<?=$pcount?>][<?=$images['id']?>][type]" value="<?=$images['image_type']?>"><input type="checkbox" name="product_image_received[<?=$pcount?>][<?=$images['id']?>][sales_invoice_received]" value="y" <?php echo ($images['sales_invoice_received']=='y')?'checked':'';?>> Share with sales invoice</div> <div><input type="checkbox" name="product_image_received[<?=$pcount?>][<?=$images['id']?>][email_attachment_received]" value="y" <?php echo ($images['email_attachment_received']=='y')?'checked':'';?> > Email Attachment</div> </td> </tr> </table> 
                                    </td>
                           <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<?php
return ob_get_clean();
}
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'reservedProducts\')">Goods Receved & Checked </a>')?> 
<div id="reservedProducts">


<table class="wide">
  
    <tbody id="product_table" name="product_table">
<?php
$pcount = 1;
foreach ($invoice_products as $product) {
    $generic = ('GME' == $product['product_id']) ? true : false;
    if($generic){
        echo product_row_generic_received($pcount,$product,$generic,1,0,0);
    }else{
        echo product_row_received($pcount, $product, $generic);
        echo product_row_generic_received($pcount,$product, $generic);
    }
    $pcount++;
}
?>
    </tbody>
</table>
<table class="wide total_invoice_total" style="width: 80%;margin-top: 10px;">
     <tr class="alt ">
        <td style="font-weight:bold;text-align: right;width: 40%;">Goods Received Checked </br>And Approved For Shipping :</td>
        <td>
            <select name="goods_checked" style="background-color:<?= ('y' == $invoice['goods_checked']) ? '#00FF00' : '#FF0000' ?>;" onchange="updateSelect(this);return false;"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['goods_checked']) ?></select>
        </td>
    </tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none" id="reservedProducts_hideNote">
    All Goods Receved & Checked product can be viewed by Clicking "Products Requested" above<br />
</div>

<!-- /product_supplier.tpl -->
