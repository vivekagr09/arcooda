<!-- details.tpl.php -->

<?php
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberDetails\');">Supplier Details</a>')?>
<div id="sectionMemberDetails">
	<table class="table_fields">
		<tr>
			<th style="width:18%;">Company Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[company]" value="<?php hecho($supplier['company'])?>" /></td>
			<th style="width:18%;">First Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[name]" value="<?php echo $supplier['name']?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">公司名 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[company_chinese]" value="<?php hecho($supplier['company_chinese'])?>" /></td>
			<th style="width:18%;">名 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[name_chinese]" value="<?php echo $supplier['name_chinese']?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;" >Address :</th>
			<td ><textarea name="supplier[address_1]" ><?php hecho(trim($supplier['address_1'] . "\n" . $supplier['address_2']))?></textarea><input type="hidden" name="supplier[address_2]" value="" /></td>
			<th style="width:18%">Last Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[surname]" value="<?php hecho($supplier['surname'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">地址 :</th>
			<td ><textarea name="supplier[address_1_chinese]" ><?php hecho(trim($supplier['address_1_chinese'] . "\n" . $supplier['address_2_chinese']))?></textarea><input type="hidden" name="supplier[address_2_chinese]" value="" /></td>
			<th style="width:18%;">姓 :</th>
			<td style="width:32%;"><input  class="intext" type="text" name="supplier[surname_chinese]" value="<?php hecho($supplier['surname_chinese'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>	
			<th style="width:18%;">City :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[city]" value="<?php hecho($supplier['city'])?>" /></td>
			<th style="width:18%;">Email / 电子邮件 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[email_address]" value="<?php hecho($supplier['email_address'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ' onchange="updateLoginName()"'?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">城市 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[city_chinese]" value="<?php hecho($supplier['city_chinese'])?>" /></td>

			<th style="width:18%;">Phone / 电话 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[phone_number]" value="<?php hecho($supplier['phone_number'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">State :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[state]" value="<?php hecho($supplier['state'])?>" /></td>
			<th style="width:18%;">2nd Contact Number /第二联络号码 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[phone_no2]" value="<?php hecho($supplier['phone_no2'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">状态 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[state_chinese]" value="<?php hecho($supplier['state_chinese'])?>" /></td>
			<th style="width:18%;">Fax / 传真:</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[fax_number]" value="<?php hecho($supplier['fax_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Post Code :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[post_code]" value="<?php hecho($supplier['post_code'])?>" /></td>
			<th style="width:18%;">Mobile /移动的:</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[mobile_number]" value="<?php hecho($supplier['mobile_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">邮政编码 :</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[post_code_chinese]" value="<?php hecho($supplier['post_code_chinese'])?>" /></td>
			<th style="width:18%;">Wechat / 微信:</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[wechat]" value="<?php hecho($supplier['wechat'])?>" /></td>

		</tr>
		<tr>
			<th style="width:18%;">Country / 国家:</th>
			<td style="width:32%;">
				<select name="supplier[country]">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(supplierGetOptionList('country'), $supplier['country'])?>
			</select>
		</td>
			<th style="width:18%;"><?=('' != $supplier['web_page_url']) ? ('<a href="' . formatWebsite($supplier['website']) . '" target="_blank">') : ''?>Website / 网站:<?=('' != $supplier['web_page_url']) ? '</a>' : ''?></th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[web_page_url]" value="<?php hecho($supplier['web_page_url'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">VAT/Company / 国家增值税/公司:</th>
			<td style="width:32%;"><input class="intext" type="text" name="supplier[vat_number]" value="<?php hecho($supplier['vat_number'])?>" /></td>
			<th style="width:18%;">Default Currency / 默认货币:</th>
			<td style="width:32%;"><select name="supplier[currency]">
				<option value="">Not Selected</option>
				<?= formatHTMLOptions(supplierGetOptionList('currency'), $supplier['currency']) ?>
			</select></td>
		</tr>
	</table>
	<br /><br />
</div>

<!-- /details.tpl.php -->
