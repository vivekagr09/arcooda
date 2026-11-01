
<div align="center">
	<?= formatHTMLTitle('Name');?>
</div>

<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Name" name="faqcategory[name]" value="<?= $faqcategory['name'] ?>" style="width:100%" /></td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Comment');?>
</div>
<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Comment" name="faqcategory[comment]" value="<?= $faqcategory['comment'] ?>" style="width:100%" /></td>
	</tr>
</table>
<!-- <div align="center">
	<?= formatHTMLTitle('Linked To');?>
</div>
<table class="wide" align="center" >
	<tr>
		
		<td style="width:100%">
			<table style="width:100%">
			<?php 
				$products=array_chunk($product_list, 3);
				foreach ($products as $key => $chunkvalue) {
					echo '<tr>';
					foreach ($chunkvalue as $key => $product) {
					?>
					<td>
					<input type="checkbox" name="products[]" value="<?= $product['id']?>" <?= in_array($product['id'],$manual_product)?'checked':''?> /> <?= $product['name'] ?></td>
				<?php

					}
					echo '</tr>';
				}


			?>
		</table>
		</td>
	</tr>
</table> -->
<div align="center">
	<?= formatHTMLTitle('Status');?>
</div>
<table class="table_fields" align="center" >
         <tr>
            <td>
	              <select class="form-control col-sm-9" id="status" name="faqcategory[status]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $faqcategory['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], $faqcategory['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $faqcategory['id']?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>