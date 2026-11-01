<!-- details.tpl.php -->

<style type="text/css">
/*<![CDATA[*/
.table_search td {padding-bottom:5px}
/*]]>*/
</style>

<script type="text/javascript">
//<![CDATA[
function setMemberData(data)
{
	if (!data) {
		return;
	}
	setValue('item[company]', data.company_name);
	setValue('item[member_id]', data.member_id);
	setValue('item[name]', data.first_name);
	setValue('item[address_1]', data.address_1);
	setValue('item[surname]', data.last_name);
	setValue('item[email_address]', data.email_address);
	setValue('item[city]', data.city);
	setValue('item[phone_number]', data.phone_no);
	setValue('item[state]', data.state);
	setValue('item[phone_no2]', data.phone_no);
	setValue('item[zip]', data.post_code);
	setValue('item[fax_number]', data.fax_no);
	setValue('item[country]', data.country);
	setValue('item[mobile_number]', data.mobile_no);
	setValue('item[vat_number]', data.vat_number);
	setValue('item[web_page_url]', data.website);
}
function searchMember()
{
    var url = "/webadmin/member/?action=search&return_message=member&jsonp=setMemberData&search_for=" + escape(getValue("search_for"));
   	$("input:checkbox[name=site_id]:checked").each(function () {
		url +='&site_id[]=' + escape($(this).val());
	})
    window.open(url);
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDetails\');return false">Warehouse Details</a>')?>

<div id="sectionDetails">
	<table class="table_fields">
		<tr>
			<th style="width:18%;">Company Name :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[company]" value="<?=htmlspecialchars($item['company'])?>" /></td>
			<th style="width:18%;">First Name :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[name]" value="<?=htmlspecialchars($item['name'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;" rowspan="2">Address :</th>
			<td rowspan="2">
				<textarea class="wide" name="item[address_1]" cols="40" rows="2"><?=htmlspecialchars(trim($item['address_1'] . "\n" . $item['address_2']))?></textarea>
				<input type="hidden" name="item[address_2]" value="" />
			</td>
			<th style="width:18%">Last Name :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[surname]" value="<?=htmlspecialchars($item['surname'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">Email :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[email_address]" value="<?=htmlspecialchars($item['email_address'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ' onchange="updateLoginName()"'?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">City :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[city]" value="<?=htmlspecialchars($item['city'])?>" /></td>
			<th style="width:18%;">Phone :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[phone_number]" value="<?=htmlspecialchars($item['phone_number'])?>"<?=('add' == $action) ? ' onchange="updateContactName()"' : ''?> /></td>
		</tr>
		<tr>
			<th style="width:18%;">State :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[state]" value="<?=htmlspecialchars($item['state'])?>" /></td>
			<th style="width:18%;">2nd Contact Number :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[phone_no2]" value="<?=htmlspecialchars($item['phone_no2'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Post Code :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[zip]" value="<?=htmlspecialchars($item['zip'])?>" /></td>
			<th style="width:18%;">Fax :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[fax_number]" value="<?=htmlspecialchars($item['fax_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Country :</th>
			<td style="width:32%;"><select name="item[country]" class="wide">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(wareHouseGetOptionList('country'), $item['country'])?>
			</select></td>
			<th style="width:18%;">Mobile :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[mobile_number]" value="<?=htmlspecialchars($item['mobile_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">VAT/Company# :</th>
			<td style="width:32%;"><input class="wide" type="text" name="item[vat_number]" value="<?=htmlspecialchars($item['vat_number'])?>" /></td>
			<th style="width:18%;"><?=('' != $item['web_page_url']) ? ('<a href="' . formatWebsite($item['web_page_url']) . '" target="_blank">') : ''?>Website :<?=('' != $item['web_page_url']) ? '</a>' : ''?></th>
			<td style="width:32%;"><input class="wide" type="text" name="item[web_page_url]" value="<?=htmlspecialchars($item['web_page_url'])?>" /></td>
		</tr>
		<tr>
			<th style="width:18%;">Member Type :</th>
			<td style="width:32%;"><select name="item[member_type]" class="wide">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(wareHouseGetOptionList('member_type'), $item['member_type'])?>
			</select></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionDetails_hideNote">Distributor Details can be viewed by Clicking "Distributor Details" above<br /></div>

<!-- /details.tpl.php -->
