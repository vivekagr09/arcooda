<!--
Created and modified by Mingze Xi@ Feb 16, 2015
Last Update: Feb 16, 2015

Update log: 1.

Problem:


Finished: 100%

Unfinished job:

The product status (tag:PS) section starts here, based on products_status.tpl.php @AGS/products/template
-->
<div align="center" id="headerStatus">
	<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Product Status</a>') ?>
</div>

<input type="hidden" name="ignore_price_drop" value="n" />

<div id="sectionStatus">
	<table class="wide" align="center" style="width:95%">
		<tr>
			<td nowrap="nowrap" class="left_title" style="text-align:right;"><b>Minimum Order:</b></td>
			<td><input type="text" name="data1[min_order]" size="7" value="<?php echo $product['min_order'] ?>" /></td>
		</tr>
		<!--Display price online. Not sure if it is useul or not-->
<?php
		$options=array('y'=>'Yes','n'=>'No');
		if(in_array($_GET['parent'],array('4','1'))) $options=array_reverse($options,true);
?>		<tr>
			<td class="left_title" style="text-align:right;"><b>Display Price Online :</b></td>
			<td >
				<select name="data1[price_online]">
					<?=formatHTMLOptions($options,$product['price_online'])?>
				</select>
			</td>
		</tr>
		<!--Active -->
		<tr>
			<td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
		<tr>
			<td nowrap="nowrap" class="left"><b>Active :</b></td>
			<td class="right"><select name="data1[active]" style="width:70px;">
				<option value="y"<?php echo ($product['active'] == 'y') ? ' selected="selected"' : '' ?>>Yes</option>
				<option value="n"<?php echo ($product['active'] == 'n') ? ' selected="selected"' : '' ?>>No</option>
			</select></td>
		</tr>


		<!--update product form-->
		<tr>
		<td colspan="2" class="top">&nbsp;</td>
		</tr>
		<tr>
			<td align="right"><input type="radio" name="go_next" value="true"<?php echo (isset($go_next) && $go_next == 'true')?' checked="checked"':'' ?> /></td>
			<td>Edit Next Product</td>
		</tr>
		<tr>
			<td align="right"><input type="radio" name="go_next" value="false"<?php echo (!isset($go_next) || (isset($go_next) && $go_next != 'true'))?' checked="checked"':'' ?> /></td>
			<td>Go Back To Listings</td>
		</tr>
		<tr>
			<td nowrap="nowrap">&nbsp;</td>
			<td>
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<input type="hidden" name="action" value="do_edit" />
				<input type="submit" name="add" value="Update" />
				<input type="reset" name="clear" value="Clear" />
			</td>
		</tr>
<?php
		if ($id<=0)
		{
?>
			<table border="0" align="center">
				<tr>
					<td nowrap="nowrap">&nbsp;</td>
					<td>
						<br /><br /><b>No more products from the lists.</b><br />
						<p>
							<input type="hidden" name="go_next" value="false" />
							<input type="hidden" name="id" value="<?php echo $id ?>" />
							<input type="hidden" name="action" value="do_edit" />
							<input type="submit" name="add" value="Back To Listings" />
						</p>
						</td>
				</tr>
<?php
		}
?>
	</table>

	<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='data1[price_online']").change(function() {
    	$(this).css({
            "background": ("n" == $(this).val()) ? "pink" : "",
        });
	}).change();
});
//]]>
	</script>

</div>
