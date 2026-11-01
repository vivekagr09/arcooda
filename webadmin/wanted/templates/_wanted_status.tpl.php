<?php
moduleLoad('invoice'); //invoiceGetOptionList, invoiceFromDatabase
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;statusdetail&quot;);return false">Status Detail</a>')?>
<div id="statusdetail">

	<table class="table_fields">
		 <tr>
            <th style="width:25%;">Make into Inquiry:</th>
            <td>
            	<input type="checkbox" name="item[invoice]" <?=($wanted['invoice']=='y')?'checked':''?> id="productinvoice">
                <?php if($wanted['invoice']=='y'){
                    $all_staff_list = invoiceGetOptionList("staff_id");
                    $invoice                = invoiceFromDatabase($wanted['invoice_id']);
                    echo '<span>Inquiry #'.$wanted['invoice_id'].' (added by '.$all_staff_list[$invoice["handler_id"]].')</span>';
                }
	            ?>
            </td>
        </tr>
         <tr>
            <th style="width:25%;">Active Online:</th>
            <td>
	              <select class="form-control col-sm-9" id="active" name="item[active]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $wanted['active']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'No','y'=>'Yes'], $wanted['active']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
</div>