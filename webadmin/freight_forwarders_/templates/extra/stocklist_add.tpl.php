<script type="text/javascript" src="functions/trcloner.js"></script>
<script type="text/javascript" src="functions/buysellmarkup.js"></script>
<script type="text/javascript">
<!-- //Search Window Rubbish
	function openSearchWindow(pos_id)
	{
		var searchStr = document.forms[0].elements["stock[" + pos_id + "][id]"].value;
		if((searchStr == "") || (searchStr == "N/A"))
		{
			document.forms[0].elements["stock[" + pos_id + "][id]"].value = "";
			searchStr = document.forms[0].elements["stock[" + pos_id + "][name]"].value;
		}
		var windowURL = "../productSearch.php?param="+searchStr+"&pos_id="+pos_id;
		searchWnd = window.open(windowURL,name,"titlebar,resizable,scrollbars,width=650,height=400");
		searchWnd.focus();
	}

	function selectProduct(prodID,name,pos_id)
	{
		document.forms[0].elements["stock["+pos_id+"][id]"].value = prodID;
		document.forms[0].elements["stock["+pos_id+"][name]"].value = name;
	}

	function noResult(pos_id)
	{
	}

	function openSupplierSearchWindow()
	{
		var windowURL = "supplier_search.php";
		ssearchWnd = window.open(windowURL,name,"titlebar,resizable,scrollbars,width=650,height=400");
		ssearchWnd.focus();
	}

	function selectSupplier(id,name)
	{
		//supplier_id
		document.forms[0].elements["supplier_id"].value = id;
		showSupplierDetails();
	}

	function showSupplierDetails()
	{
		var details_frame = document.getElementById("details_buffer");
		var supp_id = document.getElementById("supplier_id").value;
		details_frame.src = "suppliers_admin.php?action=details&id="+supp_id;
	}

	// called when documents loaded into iframe (from their body's onload attribute)
	function displayExternal()
	{
		var div = document.getElementById? document.getElementById("display_details"): document.all? document.all["display_details"]: null;
		var buf = document.getElementById("details_buffer");
		if(buf.contentDocument)
		{
			div.innerHTML = document.getElementById("details_buffer").contentDocument.body.innerHTML;
		}
		else
		{
			div.innerHTML = document.frames["details_buffer"].document.body.innerHTML;
		}
	}

//-->
</script>

<form accept-charset="utf-8" action="?action=do_add" method="post" name="addstockform">
	<table>
		<tr>
			<td class="left_title">Supplier:</td>
			<td>
				<select name="supplier_id" id="supplier_id" class="inselect" onchange="javascript: showSupplierDetails()">
<?php
foreach($suppliers as $s) : ?>
					<option value="<?= $s['supplier_id']; ?>"<?=($s['supplier_id']==$sid)?" selected=\"selected\"":"";?>><?= trim($s['supplier_name']); ?></option>
<?php
endforeach; ?>
				</select>
			</td>
			<td class="collapse">
				<input name="supp_search" type="button" id="supp_search" value="Search" onclick="javascript: openSupplierSearchWindow();" />
			</td>
		</tr>
   	</table>
	<br />
	<iframe id="details_buffer" src=""></iframe>
	<div id="display_details"></div>
	<br />
	<table class="wide vbox">
		<tr>
			<th>Our ID</th>
			<th>Qty</th>
			<th>Name of Product</th>
			<th>Buy Price</th>
			<th>Sell Price</th>
			<th>% Markup</th>
			<th>A</th>
			<th>F</th>
			<th>Notes</th>
			<th>&nbsp;</th>
		</tr>
<?php
for($i = 0; $i < (count($stock) ? count($stock) : 1); $i++): ?>
		<tr id="addstock<?=$i?"_$i":""?>" <?= ($i % 2)?" class=\"alt\"":""?>>
			<td><input name="stock[<?=$i?>][id]" type="text" id="stock[<?=$i?>][id]" value="<?=$stock[$i]['id']?>" class="intext" style="width: 60px" /></td>
			<td><input name="stock[<?=$i?>][qty]" type="text" id="stock[<?=$i?>][qty]" value="<?=$stock[$i]['qty']?>" class="intext" style="width: 40px" /></td>
			<td><input name="stock[<?=$i?>][name]" type="text" id="stock[<?=$i?>][name]" value="<?=$stock[$i]['name']?>" class="intext" style="width: 200px" /></td>
			<td><input name="stock[<?=$i?>][buy]" type="text" id="stock[<?=$i?>][buy]" value="<?=$stock[$i]['buy']?>" class="intext" style="width: 60px" onkeyup="calcSellMark(<?=$i?>);" /></td>
			<td><input name="stock[<?=$i?>][sell]" type="text" id="stock[<?=$i?>][sell]" value="<?=$stock[$i]['sell']?>" class="intext" style="width: 60px" onkeyup="calcMark(<?=$i?>);" /></td>
			<td><input name="stock[<?=$i?>][markup]" type="text" id="stock[<?=$i?>][markup]" value="<?=$stock[$i]['markup']?>" class="intext" style="width: 60px" onkeyup="calcSell(<?=$i?>);" /></td>
			<td><input name="stock[<?=$i?>][active]" type="checkbox" id="stock[<?=$i?>][active]" value="1"<?=$stock[$i]['active']?" checked=\"checked\"":""?> /></td>
			<td><input name="stock[<?=$i?>][price_flux]" type="checkbox" id="stock[<?=$i?>][price_flux]" value="1"<?=$stock[$i]['price_flux']?" checked=\"checked\"":""?> /></td>
			<td><input name="stock[<?=$i?>][notes]" type="text" id="stock[<?=$i?>][notes]" value="<?=$stock[$i]['notes']?>" class="intext" style="width: 150px" /></td>
			<td><input type="button" name="search[<?=$i?>]" id="search[<?=$i?>]" value="Search" onclick="javascript: openSearchWindow(<?=$i?>);"/></td>
		</tr>
<?php
endfor; ?>
	</table>
	<input type="button" value="Add Another Product" onclick="javascript: cloneRow('addstock');" />
	<p><em>Notes:</em><br />
	The checkbox labelled <strong>A</strong> sets whether the item is active or not.<br />
	The checkbox labelled <strong>F</strong> turns Price Flux on or off.</p>
	<table>
		<tr><!-- TODO: Repost from here on -->
			<td class="left_title">Currency:</td>
			<td><select name="currency" id="currency">
<?php
foreach($currencies as $c): ?>
				<option value="<?=$c['code']?>"<?=($c['code']==$defcurrency)?" selected=\"selected\"":""?>><?=trim($c['name'])?></option>
<?php
endforeach; ?>
   			</select></td>
   		</tr>
		<tr>
			<td class="left_title">Terms:</td>
			<td><select name="terms" id="terms">
<?php
foreach($terms as $t): ?>
				<option value="<?=$t['term_id']?>"><?=trim($t['term'])?></option>
<?php
endforeach; ?>
   			</select></td>
   		</tr>
		<tr>
			<td class="left_title">Location:</td>
			<td><select name="location" id="location">
<?php
foreach($countries as $c): ?>
				<option value="<?=$c['code']?>"<?=($c['code']==$defcountry)?" selected=\"selected\"":""?>><?=trim($c['name'])?></option>
<?php
endforeach; ?>
   			</select></td>
   		</tr>
	</table>
	<br />
	<table class="buttons">
		<tr>
			<td><input name="add" type="submit" id="add" value="Add" /><input name="clear" type="reset" id="clear" value="Clear" /></td>
   		</tr>
	</table>
</form>
