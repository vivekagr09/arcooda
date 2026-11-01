<?php
if(count($search_results)): ?>
<script type="text/javascript">
//<![CDATA[
function setmod(id)
{
	document.forms["modactive"].elements["mod["+id+"]"].value = 1;
}
//]]>
</script>
<!--   result table   -->
<br /><b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br /><br />
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="modactive">
<table style="white-space: nowrap; text-align: center" class="wide vbox">
	<tr>
		<th>Date</th>
		<th>Name of Product</th>
		<th>Buy Price</th>
		<th>Sell Price</th>
		<th>% Markup</th>
		<th>Supplier</th>
		<th>Category</th>
		<th>Movement</th>
		<th>A</th>
		<th>F</th>
		<th>&nbsp;</th>
	</tr>
<?php
foreach($search_results as $s): ?>
	<tr<?php echo ($i++ % 2)?" class=\"alt\"":""?>>
		<td><?php echo date("d/m/Y", $s['last_update']);?></td>
		<td><a href="/webadmin/products/admin.php?table=products&amp;parent=<?php echo $s['parent'];?>&amp;action=edit&amp;id=<?php echo $s['product_id'];?>"><?php echo trimString($s['product_name'], 0, $s['product_id']);?></a></td>
		<td><?php echo sprintf("%0.2f", $s['buy_price']);?></td>
		<td<?php echo $s['sp_notset']?" class=\"calcvalue\"":""?>><?php echo sprintf("%0.2f", $s['sell_price']);?></td>
		<td<?php echo $s['pm_notset']?" class=\"calcvalue\"":""?>><?php echo sprintf("%0.2f", $s['percent_markup']);?></td>
<?php
$sfname = trim($s['supplier_name']) . ((trim($s['supplier_name_ch'])) ? " (" . trim($s['supplier_name_ch']) . ")" : ""); ?>
		<td><a href="?action=edit&amp;supplier_id=<?php echo $s['supplier_id']?>"><?php echo trimString($sfname, 0, sprintf("%06d", $s['supplier_id']));?></a></td>
		<td><?php echo $s['product_category'];?></td>
		<td><?php echo sprintf("%+02.2f", $s['movement']);?>%</td>
		<td>
			<input name="act[<?=$i?>]" type="checkbox" value="1" onchange="javascript:setmod(<?=$i?>);"<?=($s['active']) ? " checked=\"checked\"" : ""?> />
		</td>
		<td>
			<input name="flux[<?=$i?>]" type="checkbox" value="1" onchange="javascript:setmod(<?=$i?>);"<?=($s['price_flux']) ? " checked=\"checked\"" : ""?> disabled="disabled" />
		</td>
		<td>
			<input name="pid[<?=$i?>]" type="hidden" value="<?=$s['product_id']?>" />
			<input name="sid[<?=$i?>]" type="hidden" value="<?=$s['supplier_id']?>" />
			<input name="mod[<?=$i?>]" type="hidden" value="0" />
			<a href="/products/machines/fsl/x_<?php echo $s['product_id'];?>.html?preview=true">View</a> |
			<a href="?action=delete_item&product_id=<?php echo $s['product_id'];?>&amp;supplier_id=<?php echo $s['supplier_id'];?>&amp;from=view">Delete</a>
		</td>
	</tr>
<?php
endforeach; ?>
</table>
<p><em>Notes:</em><br />
The checkbox labelled <strong>A</strong> sets whether the item is active or not.<br />
The checkbox labelled <strong>F</strong> indicates whether Price Flux is on or off.</p>
<div class="buttons">
<input type="submit" name="activesubmit" value="Update" /><input type="reset" name="activereset" value="Reset" />
</div>
<b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br />
<!-- end result table -->
<br />
<?php
endif; ?>
<?php
if($show_search): ?>
<!--   search boxes   -->
<form action="?action=do_search" method="post" name="searchstockform">
	<input name="searching" type="hidden" value="1" />
	<table>
		<tr>
			<td class="left_title">Name of Product:</td>
			<td><input name="pname" type="text" class="intext" value="<?=$pname?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Supplier:</td>
			<td><input name="sname" type="text" class="intext" value="<?=$sname?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Item Category</td>
			<td>
				<select name="category">
					<option value="0">(Any Category)</option>
<?php
foreach($categories as $c): ?>
					<option value="<?php echo $c['id']?>"><?php echo $c['description']?></option>
<?php
endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left_title">Country</td>
			<td>
				<select name="country">
					<option value="0">(Any Country)</option>
<?php
foreach($countries as $c): ?>
					<option value="<?php echo $c['code']?>"<?php echo ($c['code']=$country)?" selected=\"selected\"":""?>><?php echo trim($c['name'])?></option>
<?php
endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	<table class="buttons"><tr><td><input name="Search" type="submit" /></td></tr></table>
</form>
<!-- end search boxes -->
<?php
endif; ?>
