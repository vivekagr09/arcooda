<?php
$id=intval($id);
show_locked_msg($table, $id);
?>
<script type="text/javascript">
//<!--
<?php include_once('stocklist_funcs.js'); ?>

function searchProduct()
{
//	var url='/webadmin/products/Update.php?id='+urlEncode(getValue('data1[coe_id]'));
	var url='productsearch.php?search_id='+urlEncode(getValue('data1[coe_id]'));
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'ChildWindow',features);
}

function setProductData(data)
{
	if(data==null) return;
	setValue('data1[coe_id]',		data['id']);
	setValue('data1[product_name]',	data['product_name']);
	setValue('data1[product_type]', data['type']);
	setValue('data1[product_catagory]',data['producttype_id']);
	setValue('data1[condition]',	data['condition']);
	setValue('data1[weight]',		data['weight']);
	setValue('data1[unit_price]',	data['cost_price']);
	setValue('data1[cost_currency]',data['cost_currency']);
	setValue('data1[supplier]',		data['preferred_supplier']);

	setValue('data1[country_location]',data['country_location']);
	setValue('data1[active]',		data['active']);

	approximateCost();
}

function approximateCost()
{
	setValue('data1[approx_cost_price]',toCurrency(getValue('data1[unit_price]'),getValue('data1[cost_currency]'),getValue('data1[approx_cost_currency]')));
}

function addColourQty(colourName,colourQty)
{
	var theTable=document.getElementById('colour_qty_table');
	if(!theTable) return;
	var theRow=theTable.insertRow(theTable.rows.length);
	addTableCell(theRow,'<input type="text" class="text" name="data3[colour][]" value="'+colourName+'" />');
	addTableCell(theRow,'<input type="text" class="number" name="data3[quantity][]" value="'+colourQty+'" />');
	addTableCell(theRow,'<a href="javascript:void(0);" onclick="javascript:deleteColourQty(this.parentNode.parentNode.rowIndex);">Delete</a>');
	return;
}

function deleteColourQty(theIndex)
{
	var theTable=document.getElementById('colour_qty_table');
	if(!theTable) return;
	theTable.deleteRow(theIndex);
}

function addProductSize()
{
	var theTable=document.getElementById('product_size_table');
	if(!theTable) return;
	var theRow=theTable.insertRow(theTable.rows.length);
	addTableCell(theRow,'<input type="text" class="text" name="data4[dimensions][]" value="" />');
	addTableCell(theRow,'<input type="text" class="number" name="data4[qty][]" value="" />');
	addTableCell(theRow,'<a href="javascript:void(0);" onclick="javascript:deleteProductSize(this.parentNode.parentNode.rowIndex);">Delete</a>');
	return;
}

function deleteProductSize(theIndex)
{
	var theTable=document.getElementById('product_size_table');
	if(!theTable) return;
	theTable.deleteRow(theIndex);
}
// -->
</script>
<?php
addCalendarHeader();
?>
<table border="0" align="center" cellpadding="4" cellspacing="0" class="admin_content">
<form accept-charset="utf-8" action="" method="post">
	<input type="hidden" name="action" value="do_edit" />
	<input type="hidden" name="data1[id]" value="<?= $data1['id'] ?>" />

	<tr class="group_top">
		<th>Purchase Order No :</th>
		<td><input type="text" class="text" name="data1[purchase_no]" value="<?= $data1['purchase_no'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>Stock COD :</th>
		<td><input type="text" class="text" name="data1[stock_cod]" value="<?= $data1['stock_cod'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>COE ID :</th>
		<td><input type="text" class="number" name="data1[coe_id]" value="<?= $data1['coe_id'] ?>" /><input type="button" class="button" value="Find" onClick="javascript:searchProduct();" /></td>
	</tr>
	<tr class="group_mid">
		<th>Company Stock :</th>
		<td>
			<select name="data1[company_stock]">
<?php
$query=$DB->query("SELECT id, name FROM stock_company_stock");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['company_stock']==$row['id'])?' selected="selected"':'' ?>><?= $row['name'] ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=stock_company_stock&action=add" target="_blank">Add</a> |
			<a href="/webadmin/generic_admin.php?table=stock_company_stock&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr class="group_mid">
		<th>Product Name :</th>
		<td><input type="text" class="text" name="data1[product_name]" value="<?= $data1['product_name'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>Product Category :</th>
		<td>
			<select name="data1[product_catagory]">
<?php
$query=$DB->query("SELECT id, name FROM stock_product_catagories ORDER BY id");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['product_catagory']==$row['id'])?' selected="selected"':'' ?>><?= $row['name'] ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=stock_product_catagories&action=add" target="_blank">Add</a> |
			<a href="/webadmin/generic_admin.php?table=stock_product_catagories&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr class="group_bot">
		<th>Product Type :</th>
		<td>
			<select name="data1[product_type]">
<?php
$query=$DB->query("SELECT id, name FROM product_types ORDER BY name ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['product_type']==$row['id'])?' selected="selected"':'' ?>><?= $row['name'] ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/products/sections_admin.php?table=product_types&action=add" target="_blank">Add</a> |
			<a href="/webadmin/products/sections_admin.php?table=product_types&query=" target="_blank">Edit</a>
		</td>
	</tr>

	<tr class="group_spacer"><td colspan="2">&nbsp;</td></tr>

	<tr class="group_top">
		<th>Storage Location :</th>
		<td>
			<select name="data1[storage]">
				<option value="0"></option>
<?php
$query=$DB->query("SELECT * FROM product_storage_location ORDER BY location ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['storage']==$row['id'])?' selected="selected"':'' ?>><?= $row['location'] ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_storage_location&action=add" target="_blank">Add</a> |
			<a href="/webadmin/generic_admin.php?table=product_storage_location&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr class="group_mid">
		<th>Floor Location :</th>
		<td><input type="text" class="text" name="data1[floor_location]" value="<?= $data1['floor_location'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>Country/Location :</th>
		<td>
			<select name="data1[country_location]">
				<option value="0"></option>
<?php
$query=$DB->query("SELECT * FROM product_country_location ORDER BY name ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['country_location']==$row['id'])?' selected="selected"':'' ?>><?= $row['name'] ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_country_location&action=add" target="_blank">Add</a> |
			<a href="/webadmin/generic_admin.php?table=product_country_location&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr class="group_bot">
		<th>Date Received :</th>
		<td>
			<input name="data1[date_received]" type="text" class="text" id="date_available" value="<?= $data1['date_received'] ?>" />
			<?php addCalendarBtn("date_available"); ?>
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_one">
		<th>Product Details :</th>
		<td><textarea name="data1[product_details]" rows="8"><?= $data1['product_details'] ?></textarea></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_one">
		<th>
			Admin Notes :<br />
			<span style="color:red;font-size:8pt;">Admin notes are never<br />displayed on the main site</span>
		</th>
		<td><textarea name="data2[admin_note]" rows="8"></textarea></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td colspan="2" style="text-align:center;">
			<table class="summary_table" align="center" id="admin_notes_table">
				<tr><th style="background-color:white;" colspan="3">Admin Notes</th></tr>
				<tr>
					<th style="width:10%;">Date Added</th>
					<th style="width:5%;">User</th>
					<th style="width:85%;">Notes</th>
				</tr>
<?php
foreach($data2 as $row)
{
?>
				<tr>
					<td style="white-space:nowrap;"><?= date('d M, Y',strtotime($row['date_added'])) ?></td>
					<td style="white-space:nowrap;"><?= $row['user'] ?></td>
					<td><?= str_replace('<','&lt;',$row['note']) ?></td>
				</tr>
<?php
}
?>
			</table>
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_top">
		<th>Condition :</th>
		<td><select name="data1[condition]">
			<option value="0"></option>
<?php
$query=$DB->query("SELECT * FROM product_conditions");
while($row=$DB->fetch_array($query))
{
?>
			<option value="<?= $row['id'] ?>"<?= ($data1['condition']==$row['id'])?' selected="selected"':'' ?>><?= $row['condition'] ?></option>
<?php
}
?>
		</select></td>
	</tr>
	<tr class="group_mid">
		<th>Actual Product Weight (kgs) :</th>
		<td><input type="text" class="number" name="data1[weight]" value="<?= $data1['weight'] ?>" /></td>
	</tr>
	<tr class="group_bot">
		<th>Total Weight (kgs) :</th>
		<td><input type="text" class="number" name="data1[total_weight]" value="<?= $data1['total_weight'] ?>" /></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_top">
		<th>Supplier :</th>
		<td>
			<select readonly="readonly" name="data1[supplier]">
				<option value="0"></option>
<?php
$query=$DB->query("SELECT id, supplier FROM suppliers ORDER BY supplier ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['id'] ?>"<?= ($data1['supplier']==$row['id'])?' selected="selected"':'' ?>><?= stripslashes($row['supplier']) ?></option>
<?php
	}
?>
			</select>
			<a href="/webadmin/suppliers_admin.php?table=suppliers&action=add" target="_blank">Add</a> |
			<a href="/webadmin/suppliers_admin.php?table=suppliers&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr class="group_mid">
		<th>Unit Price :</th>
		<td>
			<input type="text" class="number" name="data1[unit_price]" onchange="javascript:approximateCost();" value="<?= $data1['unit_price'] ?>" />
			<select name="data1[cost_currency]" onchange="javascript:approximateCost();">
<?php
$query=$DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['code'] ?>"<?= ($data1['cost_currency']==$row['code'])?' selected="selected"':'' ?>><?= strtoupper($row['code']) ?> - <?= $row['name'] ?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr class="group_bot">
		<th>Approx Cost Price :</th>
		<td>
			<input type="text" class="number" name="data1[approx_cost_price]" value="<?= $data1['approx_cost_price'] ?>" readonly="readonly" />
			<select name="data1[approx_cost_currency]" onchange="javascript:approximateCost();">
<?php
$query=$DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while($row=$DB->fetch_array($query))
{
?>
				<option value="<?= $row['code'] ?>"<?= ($data1['approx_cost_currency']==$row['code'])?' selected="selected"':'' ?>><?= strtoupper($row['code']) ?> - <?= $row['name'] ?></option>
<?php
}
?>
			</select>
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_top">
		<th>Sample :</th>
		<td><input type="text" class="number" name="data1[sample_qty]" value="<?= $data1['sample_qty'] ?>"<?= (!has_permission('STOCK',$allowed_sections) && $data1['id']>0)?' readonly="readonly"':'' ?> /></td>
	</tr>
	<tr class="group_mid">
		<th>Stock :</th>
		<td><input type="text" class="number" name="data1[stock_qty]" value="<?= $data1['stock_qty'] ?>"<?= (!has_permission('STOCK',$allowed_sections) && $data1['id']>0)?' readonly="readonly"':'' ?> /></td>
	</tr>
	<tr class="group_mid">
		<th>Order :</th>
		<td><input type="text" class="number" name="data1[order_qty]" value="<?= $data1['order_qty'] ?>"<?= (!has_permission('STOCK',$allowed_sections) && $data1['id']>0)?' readonly="readonly"':'' ?> /></td>
	</tr>
	<tr class="group_mid">
		<th>Customer Name :</th>
		<td><input type="text" class="text" name="data1[customer_name]" value="<?= $data1['customer_name'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>Invoice Reference :</th>
		<td><input type="text" class="text" name="data1[invoice_ref]" value="<?= $data1['invoice_ref'] ?>" /></td>
	</tr>
	<tr class="group_mid">
		<th>Colour Quantities :</th>
		<td><table id="colour_qty_table" class="summary_table">
			<tr>
				<td style="width:300px;">Colour</td>
				<td style="width:64px;">Quantity</td>
				<td style="width:38px;"><a href="javascript:void(0);" onclick="javascript:addColourQty('','');">Add</a></td>
			</tr>
<?php
foreach($data3 as $item)
{
?>
			<tr>
				<td><input type="text" class="text" name="data3[colour][]" value="<?= $item['colour'] ?>" /></td>
				<td><input type="text" class="number" name="data3[quantity][]" value="<?= $item['quantity'] ?>" /></td>
				<td><a href="javascript:void(0);" onclick="javascript:deleteColourQty(this.parentNode.parentNode.rowIndex);">Delete</a></td>
			</tr>
<?php
}
?>
		</table></td>
	</tr>
	<tr style="display:none;" name="endOfColours"><td colspan="2"></td></tr>
	<tr class="group_bot">
		<th>Product Size :</th>
		<td><table id="product_size_table" class="summary_table">
			<tr>
				<td style="width:300px;">Size</td>
				<td style="width:64px;">Quantity</td>
				<td style="width:38px;"><a href="javascript:void(0);" onclick="javascript:addProductSize();">Add</a></td>
			</tr>
<?php
foreach($data4 as $item)
{
?>
			<tr>
				<td><input type="text" class="text" name="data4[dimensions][]" value="<?= $item['dimensions'] ?>" /></td>
				<td><input type="text" class="number" name="data4[qty][]" value="<?= $item['qty'] ?>" /></td>
				<td><a href="javascript:void(0);" onclick="javascript:deleteProductSize(this.parentNode.parentNode.rowIndex);">Delete</a></td>
			</tr>
<?php
}
?>
		</table></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr class="group_one">
		<th>Active :</th>
		<td><select name="data1[active]" style="width:64px;">
			<option value="y"<?= ($data1['active']=='y')?' selected="selected"':'' ?>>Yes</option>
			<option value="n"<?= ($data1['active']=='n')?' selected="selected"':'' ?>>No</option>
		</select></td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td style="text-align:right;"><input type="radio" class="radio" name="go_next" value="true"<?= (isset($go_next) && $go_next=='true')?' checked="checked"':'' ?> /></td>
		<td><?= ($data1['id']>0)?'Edit Next Product':'Add Another Product' ?></td>
	</tr>
	<tr>
		<td style="text-align:right;"><input type="radio" class="radio" name="go_next" value="false"<?= (!(isset($go_next) && $go_next=='true'))?' checked="checked"':'' ?> /></td>
		<td>Go Back To Listings</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="id" value="<?= $id ?>" />
			<input type="hidden" name="action" value="do_edit" />
			<input type="submit" class="button" value="Update" />
			<input type="reset" class="button" value="Clear" />
			<input type="button" class="button" value="Test Post" onclick="this.form.action='developer/testpost.php';this.form.target='_blank';this.form.submit();" />
		</td>
	</tr>
</form>
</table>

<script type="text/javascript">
approximateCost();
</script>
