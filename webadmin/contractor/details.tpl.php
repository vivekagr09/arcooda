<!-- details.tpl.php -->

<?php
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionContractorDetails\');return false">Contractor Details</a>')?>

<div id="sectionContractorDetails">
	<table class="table_fields">
		<tr>
			<th style="width:18%;">Company Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[company]" value="<?php hecho($contractor['company'])?>" /></td>
			<th style="width:18%;">First Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[name]" value="<?php hecho($contractor['name'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;" rowspan="2">Address :</th>
			<td rowspan="2"><textarea name="contractor[address_1]" cols="80" rows="2"><?php hecho(trim($contractor['address_1'] . "\n" . $contractor['address_2']))?></textarea><input type="hidden" name="contractor[address_2]" value="" /></td>
			<th style="width:18%">Last Name :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[surname]" value="<?php hecho($contractor['surname'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">Email :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[email_address]" value="<?php hecho($contractor['email_address'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ' onchange="updateLoginName()"'?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">City :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[city]" value="<?php hecho($contractor['city'])?>" /></td>
			<th style="width:18%;">Phone :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[phone_number]" value="<?php hecho($contractor['phone_number'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">State :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[state]" value="<?php hecho($contractor['state'])?>" /></td>
			<th style="width:18%;">2nd Contact Number :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[phone_no2]" value="<?php hecho($contractor['phone_no2'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Post Code :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[zip]" value="<?php hecho($contractor['zip'])?>" /></td>
			<th style="width:18%;">Fax :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[fax_number]" value="<?php hecho($contractor['fax_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Country :</th>
			<td style="width:32%;"><select name="contractor[country]">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(contractorGetOptionList('country'), $contractor['country'])?>
			</select></td>
			<th style="width:18%;">Mobile :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[mobile_number]" value="<?php hecho($contractor['mobile_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">VAT/Company# :</th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[vat_number]" value="<?php hecho($contractor['vat_number'])?>" /></td>
			<th style="width:18%;"><?=('' != $contractor['web_page_url']) ? ('<a href="' . formatWebsite($contractor['website']) . '" target="_blank">') : ''?>Website :<?=('' != $contractor['web_page_url']) ? '</a>' : ''?></th>
			<td style="width:32%;"><input class="intext" type="text" name="contractor[web_page_url]" value="<?php hecho($contractor['web_page_url'])?>" /></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectioncontractorDetails_hideNote">Contractor Details can be viewed by Clicking "Contractor Details" above<br /></div>
<!-- /details.tpl.php -->
