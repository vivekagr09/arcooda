<?php
/**
 * @var mixed[] $staff
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php

$per_page_list=array();
for ($i=50;$i<=250;$i+=50)
	$per_page_list[$i]=$i;

?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOptions\');">Webadmin Options</a>')?>

<div id="sectionOptions">

<table style="width:50%;">
	<tr>
		<td class="left_title" style="width:50%;">Default Currency</td>
		<td><select name="currency">
			<option value="AUD">Australian Dollars - AUD</option>
			<?=formatHTMLOptions(getCurrencyList(),$staff['currency'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Invoices Per Page</td>
		<td><select name="invoices_per_page">
			<?=(!isset($per_page_list[$staff['invoices_per_page']]))?sprintf('<option value="%1$d">%1$d</option>',$staff['invoices_per_page']):''?>
			<?=formatHTMLOptions($per_page_list,$staff['invoices_per_page'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Members Per Page</td>
		<td><select name="members_per_page">
			<?=(!isset($per_page_list[$staff['members_per_page']]))?sprintf('<option value="%1$d">%1$d</option>',$staff['members_per_page']):''?>
			<?=formatHTMLOptions($per_page_list,$staff['members_per_page'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">News Per Page</td>
		<td><select name="news_per_page">
			<?=(!isset($per_page_list[$staff['news_per_page']]))?sprintf('<option value="%1$d">%1$d</option>',$staff['news_per_page']):''?>
			<?=formatHTMLOptions($per_page_list,$staff['news_per_page'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Products Per Page</td>
		<td><select name="products_per_page">
			<?=(!isset($per_page_list[$staff['products_per_page']]))?sprintf('<option value="%1$d">%1$d</option>',$staff['products_per_page']):''?>
			<?=formatHTMLOptions($per_page_list,$staff['products_per_page'])?>
		</select></td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOptions_hideNote">
Webadmin Options can be viewed by Clicking "Webadmin Options" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
