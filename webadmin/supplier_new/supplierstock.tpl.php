<style type="text/css">@media print{ 
    body, html, #sectionSupplierstok {
        height: 100% !important;
        width: 100% !important;
        display: inline-block;
   </style>
   <script type="text/javascript">
	//<![CDATA[
	function selectAll(checked)
	{
		var collection=getElements('orderstock[]');
		if(checked.checked){
			for(i=0;i<collection.length;i++) collection[i].checked=checked;
		}else{
			for(i=0;i<collection.length;i++) collection[i].checked=false;
		}
		
	}
	//]]>
	function supplier_products_add() {
	    var url = "master_products.php?return_message=related";
	    url += "&multiple=y";
	    url += "&supplier_id=<?=$supplier_id?>";
	    url += "&callback=supplier_products_data";
	    url += "&search_for=";
	    window.open(url);
	}
	function supplier_products_data(){
		location.reload();
	}
</script>
<?php
moduleLoad('product');

	function supplier_products_row($count = '[ROW_COUNT]', $stock = array())
	{
	    global $DB;
	   	$stockhistory=getsupplieritemfullhistorynew($stock['supplier_id'],$stock['product_id']);  
	   	$other_supplier  = OtherSupplierForProduct($stock['supplier_id'],$stock['product_id']);  			
    	ob_start();
	    include __DIR__ . '/__product_tooltip.tpl.php';
	    $product_tooltip = ob_get_clean();			
    	?>
		<tr id="item_suppliers_row[<?= $count ?>]">
			<td><input type="checkbox" name="orderstock[]" value="<?= $stock['id']?>" >
				<input type="hidden" value="<?= $stock['id']?>" name="stock[<?= $count?>][id]">
				<input type="hidden" value="<?= $stock['id']?>" name="stock[<?= $count?>][stock_id]">
			</td>
			<td>
				<a href="/webadmin/products_new/edit.php?action=edit&id=<?= $stock['pro_id'] ?>" target="_blank"><?= $stock['pro_id'] ?></a>
				<input type="hidden" value="<?= $stock['pro_id']?>" name="stock[<?= $count?>][product_id]">
				<input type="hidden" value="<?= $stock['supplier_id']?>" name="stock[<?= $count?>][supplier_id]">
			</td>
			<td>
	    		<?php
	           $url=sprintf(
		            '/buy/%s-%d',
		            urlencode(urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $stock['product_name']), '-'))),
		            $stock['pro_id']
		        ); ?>
				<a href="<?= $url ?>" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();" target="_blank"><?= $stock['product_name'] ?></a>
			</td>
			<td>
				<input type="text" value="<?= $stock['qty']?>" name="stock[<?= $count?>][qty]" size="6">
			</td>
			<td>
				<input type="text" value="<?= $stock['cost_price']?>" name="stock[<?= $count?>][cost_price]" size="6" class="update_currency" rowid="<?= $count?>" curtype="other">
			</td>
			<td>
				<select name="stock[<?= $count?>][sup_currency]" class="update_currency" rowid="<?= $count?>" curtype="other">
					<?=formatHTMLOptions(getCurrencyList(), $stock['sup_currency'])?>
				</select>
			</td>
			<td>
				<input type="text" value="<?= $stock['cprice_usd']?>" name="stock[<?= $count?>][cprice_usd]" size="6" class="update_currency updatemarkup" rowid="<?= $count?>" curtype="usd" changetype="salesprice">
			</td>
			<td>
				<input type="text" value="<?= $stock['sales_price']?>" name="stock[<?= $count?>][sales_price]" size="6" rowid="<?= $count?>" changetype="salesprice" class="updatemarkup">
			</td>
			<td>
				<input type="text" value="<?= $stock['markup']?>" name="stock[<?= $count?>][markup]" size="6" rowid="<?= $count?>" changetype="markup" class="updatemarkup">
			</td>
			<td>
				<select name="stock[<?= $count?>][location]">
					<?=formatHTMLOptions(getCountryList(), $stock['location'])?>
				</select>
			</td>
			<td>
				<?= ($stock['updated_at'])?date('d-m-Y',strtotime($stock['updated_at'])):'N/A';?>
			</td>
			<td>
				<?= ($stockhistory)?$stockhistory['movement'].' %':'N/A';?>    				</td>
			<td>
				<input type="text" value="<?= $stock['notes']?>" name="stock[<?= $count?>][notes]" size="10">
			</td>
			<td style="background-color: <?= ($stock['main']=='y')?'#90eea0':'#fee6ee'?>">
				<?= ($stock['active']=='y')?'Active':'Deactive' ?>
			</td>
			<td>
				<input type="checkbox"  name="stock[<?= $count?>][discontinued]" <?= ($stock['discontinued']=='y')?'checked':'' ?>>
			</td>
			<td>
				<a onclick="supplierProductDelete(<?= $count?>,<?= $other_supplier?>)" style="cursor:pointer;" target="_blank">Delete</a>
			</td>
		</tr>
	<?php
	return ob_get_clean();
	}
	$order_by=($_GET['order_by'])?$_GET['order_by']:'id';
	$order=($_GET['order'])?$_GET['order']:'asc';
	$supplier_stock         = supplierStockFromDatabase($supplier_id,$order_by,$order);
	$sort_url = $_GET;
	unset($sort_url['order_by']);
	$sort_url['order_by'] = '';
	$sort_url = 'edit.php?' . http_build_query(array(
	    'order' => ('desc' == $order) ? 'asc' : 'desc'
	) + $sort_url);
	$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';
	if (isset($supplier_stock) && is_array($supplier_stock)) { ?>
		<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSupplierstok\');return false">Offer List</a>', '<i><a href="#" class="unfinished" onclick="supplier_products_add();return false">Add</a></i>')?>
		<div id="sectionSupplierstok">
				<table class="table_grid">
					<tr>
						<th>#</th>
						<th><a href="<?php hecho($sort_url) ?>p.id">Product ID</a><?=('p.id' == $order_by) ? $ord_img : '' ?></th>
						<th><a href="<?php hecho($sort_url) ?>p.product_name">Name of Product</a><?=('p.product_name' == $order_by) ? $ord_img : '' ?></th>
						<th>Qty Available</th>
						<th>Cost Price</th>
						<th>Cost Currnecy</th>
						<th>Cost (USD)</th>
						<th>Sales Price (USD)</th>
						<th>Markup %</th>
						<th>Location</th>
						<th>Last Update</th>
						<th>Last Movement</th>
						<th>Note</th>
						<th>Status</th>
						<th>Discontinued</th>
						<th>Action</th>
					</tr>
					<?php
					$count=0;
					if(count($supplier_stock)){
	    				foreach ($supplier_stock as $stock) {
	    				echo supplier_products_row($count,$stock);
	    				 $count++; } ?>
	    			<?php } ?>
	    		</table>
	    		<?php if(count($supplier_stock)){ ?>
	    		<table class="table_grid">
		    		<tr>
		    			<th  style="width: 100px;"><input type="checkbox" name="select_all" onclick="selectAll(this)" />Select All</th>
		    			<th colspan="15" style="text-align:center;">
					        <input type="button" value="Update"   onclick="updatedatastock('full');">
					        <input type="button" value="Order" class="item_suppliers_delete" style="" onclick="orderStock();" >
					        <input type="button" value="Print" onclick="printDivNew();">
					        <input type="button" value="Print Supplier List" onclick="printDiv();" style="float:right">
				        </th>
				        <th  style="width: 150px;"><div style="padding: 5px; width: 5px; background-color: #90eea0; position: absolute; vertical-align: middle;"></div>Default Supplier</th>
				    </tr>
			    </table>
				<?php } ?>
		</div>
		<input type="hidden" name="supplier_product_delete" value="">

		<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionSupplierstok_hideNote">Supplier Stock can be viewed by Clicking "Stocklist " above<br /></div>
<?php	} ?>
<script type="text/javascript">
	function orderStock(){
		var proid = $('input[name="orderstock[]"]:checked').map(function() { 
			return this.value; 
		}).get().join(',');
		if(proid){
			window.location.href = "/webadmin/purchase_order/?action=add&status=inquiry&supplier_id="+getValue('supplier[supplier_id]')+"&supplierproduct="+proid+"";
		}else{
			alert('Please checked at least one product');
		}
	}
	function updatedatastock(stockid){
		if ($('#submitdataform').length) {
			$('#submitdataform').remove();
		}
		$('<form action=".?action=updatesupplierproduct" method="post" id="submitdataform"></form>').appendTo('body');
		if(stockid=='full'){
			$("input[name^='stock").map(function (idx, ele) {
			var name=$(ele).attr('name');
			var valdata=$(ele).val();
			if(name.search("discontinued")<0){
				$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
			}
			  //console.log($(ele).attr('name'), $(ele).val());
			}).get();
			var count=<?=$count ?>;
			for(var i=0;i<count;i++){
				var name='stock['+i+'][sup_currency]';
				var valdata=$('select[name="'+name+'"]').val()
				$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
				var name='stock['+i+'][location]';
				var valdata=$('select[name="'+name+'"]').val()
				$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
				var ischecked = $('input[name="stock['+i+'][discontinued]"]:checked').length;
				console.log(ischecked);
				var name='stock['+i+'][discontinued]';
				if(ischecked){
					$('#submitdataform').append('<input type="hidden" name="'+name+'" value="y">');
				}else{
					$('#submitdataform').append('<input type="hidden" name="'+name+'" value="n">');
				}
			}

		}else{
			$("input[name^='stock["+stockid+"]']").map(function (idx, ele) {
			var name=$(ele).attr('name');
			var valdata=$(ele).val();
			$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
			  //console.log($(ele).attr('name'), $(ele).val());
			}).get();
			var name='stock['+stockid+'][sup_currency]';
			var valdata=$('select[name="'+name+'"]').val()
			$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
			var name='stock['+stockid+'][location]';
			var valdata=$('select[name="'+name+'"]').val()
			$('#submitdataform').append('<input type="hidden" name="'+name+'" value="'+valdata+'">');
		}
		$('#submitdataform').append('<input type="hidden" name="action" value="updatesupplierproduct">');
		$('#submitdataform').append('<input type="hidden" name="supplier_id" value="'+getValue('supplier[supplier_id]')+'">');
		$('#submitdataform').submit();
	}
	var exRates = <?php jecho(getExchangeRateList()) ?>;
	function sprint_price(price)
	{
	    var sprice=String(price+0.005);
	    var counting=false;
	    var thecount=0;
	    var outtext="";
	    for(var i=0;(i<sprice.length && thecount<2);i++)
	    {
	        var ch=sprice.charAt(i);
	        if(counting) thecount++;
	        if(ch=='.') counting=true;
	        outtext+=ch;
	    }
	    return outtext;
	}
	function toCurrency(price,fromCurrency,toCurrency)
	{
	    fromCurrency=fromCurrency.slice(0,3).toUpperCase();
	    toCurrency=toCurrency.slice(0,3).toUpperCase();
	    if(fromCurrency==toCurrency) return price;
	    var r = exRates[fromCurrency]/exRates[toCurrency];
	    var newP = price * r;
	    return sprint_price(newP);
	}
	$('document').ready(function(){
	    $('body').on('change', '.update_currency', function(){
	        var rowid=$(this).attr('rowid'); 
	        var curtype=$(this).attr('curtype'); 
	        if(curtype=='usd'){
	            var fromElem='stock['+rowid+'][cprice_usd]';
	            var toElem='stock['+rowid+'][cost_price]';
	            var to=getValue('stock['+rowid+'][sup_currency]');
	            var from='USD';
	        }else if(curtype=='other'){
	            var toElem='stock['+rowid+'][cprice_usd]';
	            var fromElem='stock['+rowid+'][cost_price]';
	            var from=getValue('stock['+rowid+'][sup_currency]');
	            var to='USD';
	        }else{
	            //alert('not found');
	        }
	        setValue(toElem, toCurrency(getValue(fromElem), from, to));
	        var salesprice=getValue('stock['+rowid+'][sales_price]');
            var costprice=getValue('stock['+rowid+'][cprice_usd]');
            var markup=(((salesprice-costprice)/costprice)*100).toFixed(2)
            setValue('stock['+rowid+'][markup]', markup);
	        //updateProfits();
	    });
	    $('body').on('change', '.updatemarkup', function(){
	        var rowid=$(this).attr('rowid'); 
	        var changetype=$(this).attr('changetype'); 
	        if(changetype=='markup'){
	            var markup=getValue('stock['+rowid+'][markup]');
	            var costprice=getValue('stock['+rowid+'][cprice_usd]');
	            var salesprice=(((costprice*markup)/100)+parseInt(costprice)).toFixed(2);
	            setValue('stock['+rowid+'][sales_price]', salesprice);
	        }else if(changetype=='salesprice'){
	            var salesprice=getValue('stock['+rowid+'][sales_price]');
	            var costprice=getValue('stock['+rowid+'][cprice_usd]');
	            var markup=(((salesprice-costprice)/costprice)*100).toFixed(2)
	            setValue('stock['+rowid+'][markup]', markup);
	        }else{
	            //alert('not found');
	        }
	    });
	});
	function supplierProductDelete(row,other) {
		if(other==0){
			if (confirm('There are no suppliers for this product, do you still want to continue')) {
	    			setValue("supplier_product_delete", getValue("supplier_product_delete") + "," + getValue("stock["+row+"][id]"));
			        var theRow = getElement('item_suppliers_row[' + row + ']');
			        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	    		}
		}else{
			if(confirm('Are You Sure you want to delete this Product??')) {
	    		setValue("supplier_product_delete", getValue("supplier_product_delete") + "," + getValue("stock["+row+"][id]"));
		        var theRow = getElement('item_suppliers_row[' + row + ']');
		        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	    	}
	    	
	    }
	}
	function printDiv() {
		var url = "print_supplier_product.php?";
	    url += "&supplier_id=<?=$supplier_id?>";
	    window.open(url);
	     
	}
	function printDivNew(){
		var url = "print_supplier_product_new.php?";
	    url += "&supplier_id=<?=$supplier_id?>";
	    window.open(url);
	}
</script>