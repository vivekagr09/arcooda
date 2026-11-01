<div align="center">
<?php
$copy_fields = array('manufacturer','address','city','state','post_code','country','phone','fax','mobile','email');
$copy = "";
foreach($copy_fields as $field)
{
	$number = $shipping[0]['id'];
	if($add || (count($shipping) == 0)) $number = 0;
	$copy .= "setValue('warehouse[$number][$field]',getValue('supplier[$field]'));";
}
$extra = "<small><i><a style=\"color:blue\" href=\"#\" onclick=\"javascript: ".$copy." return false;\">Copy from Above</a> | <a style=\"color:blue\" href=\"#\" onclick=\"javascript:addWarehouse() ; return false;\">Add New Address</a></i></small>";
echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWarehouse\');">'.$dis_name.' Warehouse Details</a>',$extra);
$warehouses = supplierWarehousesFromDatabase($supplier_id);
?>
</div>
<br />
<div id="sectionWarehouse">
<table id="warehouse" class="wide" align="center" style="width:98%;" cellpadding=0 cellspacing=0>
<?php
// $country_list ------> defined in addedit_suppliers.php file....
if(count($warehouses) > 0)
{
	foreach($warehouses as $warehouse)
	{
?>
	<tr id="warehouse_row[<?=$warehouse['id']?>]">
		<td>
			<table class="wide" style="width:100%" cellpadding=0 cellspacing=0>
				<tr>
					<td class="left_title" style="width:18%;">Company Name :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][manufacturer]" value="<?=$warehouse['manufacturer']?>"></td>
					<td class="left_title" style="width:18%;">Phone :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][phone]" value="<?=$warehouse['phone']?>"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Address :</td>
					<td style="width:32%;" rowspan=2>
						<textarea class="in" style="width:100%;height:100%" name="warehouse[<?=$warehouse['id']?>][address]"><?=$warehouse['address']?></textarea>
					</td>
					<td class="left_title" style="width:18%;">Fax :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][fax]" value="<?=$warehouse['fax']?>"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">&nbsp;</td>
					<td class="left_title" style="width:18%;">Mobile :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][mobile]" value="<?=$warehouse['mobile']?>"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">City :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][city]" value="<?=$warehouse['city']?>"></td>
					<td class="left_title" style="width:18%;">Email :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][email]" value="<?=$warehouse['email']?>"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">State :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][state]" value="<?=$warehouse['state']?>"></td>
					<td class="left_title" style="width:18%;">Website :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][website]" value="<?=$warehouse['website']?>"></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Post Code :</td>
					<td style="width:32%;"><input type="text" style="width:100%;" name="warehouse[<?=$warehouse['id']?>][post_code]" value="<?=$warehouse['post_code']?>"></td>
					<td class="left_title" style="width:18%;">&nbsp;</td><td style="width:32%;">&nbsp;</td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Country :</td>
					<td style="width:32%;">
						<select style="width:100%;" name="warehouse[<?=$warehouse['id']?>][country]">
							<option value="">Not Selected</option>
							<?=formatHTMLOptions($country_list,$warehouse['country'])?>
						</select>
					</td>
					<td class="left_title" style="width:18%;">&nbsp;</td><td style="width:32%;">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
	}
}
else
{
?>
<script>addWarehouse()</script>
<?php
}
?>
</table>
</div>
<br />