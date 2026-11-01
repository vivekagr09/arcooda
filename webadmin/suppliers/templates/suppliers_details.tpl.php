<div align="center">
	<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionDetails\');">'.$dis_name.' Details</a>');?>
</div>
<div id="sectionDetails">
<table class="wide" align="center" style="width:98%">
	<tr>
		<td class="left_title" style="width:18%;"><?=$dis_name?> Name :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[manufacturer]" value="<?=$supplier['supplier_name']?>">
		</td>
		<td class="left_title" style="width:18%;">Phone :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[phone]" value="<?=$supplier['phone']?>">
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">Address :</td>
		<td style="width:32%;" rowspan="2">
			<textarea style="width:100%;height:100%" name="supplier[address]"><?=$supplier['address']?></textarea>
		</td>
		<td class="left_title" style="width:18%;">Fax :</td>
		<td><input type="text" style="width:100%;" name="supplier[fax]" value="<?=$supplier['fax']?>"></td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">&nbsp;</td>
		<td class="left_title" style="width:18%;">Mobile :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[mobile]" value="<?=$supplier['mobile']?>">
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">City :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[city]" value="<?=$supplier['city']?>">
		</td>
		<td class="left_title" style="width:18%;">Email :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[email]" value="<?=$supplier['email']?>">
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">State :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[state]" value="<?=$supplier['state']?>">
		</td>
		<td class="left_title" style="width:18%;"><?=($supplier['website'])?'<a href="'.formatWebsite($supplier['website']).'" target="_blank">':''?>Website :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[website]" value="<?=$supplier['website']?>">
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">Post Code :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[post_code]" value="<?=$supplier['post_code']?>">
		</td>
		<td class="left_title" style="width:18%;">ABN :</td>
		<td style="width:32%;">
			<input type="text" style="width:100%;" name="supplier[abn]" value="<?=$supplier['abn']?>">
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">Country :</td>
		<td style="width:32%;">
			<select style="width:100%;" name="supplier[country]" onchange="setSelectOptions('supplier[seaport]',countrySeaports(this.value)); setSelectOptions('supplier[airport]',countryAirports(this.value));">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions($country_list,$supplier['country'])?>
			</select>
		</td>
		<td class="left_title" style="width:18%;">Preferred Currency :</td>
		<td style="width:32%;">
			<select style="width:100%;" name="supplier[currency]">
<?php
$cur = ($supplier['currency'] != '') ? $supplier['currency'] : 'AUD' ; ?>
				<?= formatHTMLOptions($currency_list, $cur) ?>
			</select>
		</td>
	</tr>
</table>
</div>
<br />