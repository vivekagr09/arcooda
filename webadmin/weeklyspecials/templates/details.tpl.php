<!-- details.tpl.php -->

<?php
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialDetails\');">WeeklySpecial Details</a>') ?>

<div id="sectionWeeklySpecialDetails">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:18%;">Company Name :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[company]" value="<?= htmlspecialchars($weeklyspecial['company']) ?>" /></td>
			<td class="left_title" style="width:18%;">First Name :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[name]" value="<?= htmlspecialchars($weeklyspecial['name']) ?>"<?= ($action=='add')?' onchange="updateContactName()"':'' ?> /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;" rowspan="2">Address :</td>
			<td rowspan="2"><textarea name="weeklyspecial[address_1]" cols="80" rows="2"><?= htmlspecialchars(trim($weeklyspecial['address_1']."\n".$weeklyspecial['address_2'])) ?></textarea><input type="hidden" name="weeklyspecial[address_2]" value="" /></td>
			<td class="left_title" style="width:18%">Last Name :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[surname]" value="<?= htmlspecialchars($weeklyspecial['surname']) ?>"<?= ($action=='add')?' onchange="updateContactName()"':'' ?> /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Email :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[email_address]" value="<?= htmlspecialchars($weeklyspecial['email_address']) ?>"<?= ($action=='add')?' onchange="updateContactName()"':' onchange="updateLoginName()"' ?> /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">City :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[city]" value="<?= htmlspecialchars($weeklyspecial['city']) ?>" /></td>
			<td class="left_title" style="width:18%;">Phone :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[phone_number]" value="<?= htmlspecialchars($weeklyspecial['phone_number']) ?>"<?= ($action=='add')?' onchange="updateContactName()"':'' ?> /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">State :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[state]" value="<?= htmlspecialchars($weeklyspecial['state']) ?>" /></td>
			<td class="left_title" style="width:18%;">2nd Contact Number :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[phone_no2]" value="<?= htmlspecialchars($weeklyspecial['phone_no2']) ?>" /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Post Code :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[zip]" value="<?= htmlspecialchars($weeklyspecial['zip']) ?>" /></td>
			<td class="left_title" style="width:18%;">Fax :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[fax_number]" value="<?= htmlspecialchars($weeklyspecial['fax_number']) ?>" /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Country :</td>
			<td style="width:32%;"><select name="weeklyspecial[country]">
				<option value="">Not Selected</option>
				<?= formatHTMLOptions($section_handler->getOptionList('country'),$weeklyspecial['country']) ?>
			</select></td>
			<td class="left_title" style="width:18%;">Mobile :</td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[mobile_number]" value="<?= htmlspecialchars($weeklyspecial['mobile_number']) ?>" /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;"></td>
			<td style="width:32%;"></td>
			<td class="left_title" style="width:18%;"><?= ($weeklyspecial['web_page_url']!='')?('<a href="'.formatWebsite($weeklyspecial['website']).'" target="_blank">'):'' ?>Website :<?= ($weeklyspecial['web_page_url']!='')?'</a>':'' ?></td>
			<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial[web_page_url]" value="<?= htmlspecialchars($weeklyspecial['web_page_url']) ?>" /></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialDetails_hideNote">WeeklySpecial Details can be viewed by Clicking "WeeklySpecial Details" above<br /></div>

<!-- /details.tpl.php -->
