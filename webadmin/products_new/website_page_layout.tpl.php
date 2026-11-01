<!-- website_page_layout.tpl -->



<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;website_page_layout&quot;);return false" class="">Website Page Layout</a>')?>

<div id="website_page_layout">
    <table class="table_fields">
    	<tr>
	    	<th>360 Viewer (<small><i><a href="/webadmin/product_360/?action=add" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[360_view]">
	        	<option value=""></option>
	            <?=formatHTMLOptions(productGetOptionList('360_view'), $item['360_view'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Product Slider (<small><i><a href="/webadmin/product_slider/addedit.php?action=do_add" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[product_slider]">
	        	<option value=""></option>
	            <?=formatHTMLOptions(productGetOptionList('product_slider'), $item['product_slider'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Product Builder (<small><i><a href="/webadmin/product_builder/addbuilder.php?action=add_builder" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[product_builder]">
	        	<option value=""></option>
	            <?=formatHTMLOptions(getproductBuilder($item['id']), $item['product_builder'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th >3D Carousel:</th>
	        <td><select name="item[product_carousel]">
	        	<option value=""></option>
	            <?=formatHTMLOptions(productGetOptionList('product_carousel'), $item['product_carousel'])?>
	        </select></td>
	    </tr>
    </table>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionItem_hideNote">
  Website Page Layout can be viewed by Clicking "Website Page Layout" above
</div>

<!-- /website_page_layout.tpl -->
