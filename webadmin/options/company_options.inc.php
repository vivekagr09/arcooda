<?php
$company_options=array();
if ($query = $DB->query(sprintf(
	"SELECT * FROM `highwaygroup`.`company_info`"
))) {
	while ($row = $DB->fetch_assoc($query)) {
		$company_options[] = $row;
	}
	$DB->free_result($query);
}

switch($action)
{

case 'list':
?>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionCompanyOptions\');">Company Options</a>') ?>

<div id="sectionCompanyOptions">
	<table class="wide spacetable">
		<tr><th>Description</th><th>Value</th><th>Action</th></tr>
<?php

	foreach($company_options as $option)
	{
?>
		<tr>
			<td style="vertical-align:top;width:20%;white-space:nowrap;"><?= nl2br(html_safe($option['company_name']."\nInvoice&nbsp;Header")) ?></td>
			<td style="width:80%;"><?= nl2br(html_safe($option['company_invoiceheader'])) ?></td>
			<td style="text-align:center;width:0px;"><a  href="?action=edit&opt_type=company&type=header&option_id=<?= $option['company_id'] ?>">Edit</a></td>
		</tr>
		<tr style="height:48px;" class="alt">
			<td style="vertical-align:top;width:20%;white-space:nowrap;"><?= nl2br(html_safe($option['company_name']."\nEmail&nbsp;Signature")) ?></td>
			<td style="width:80%;"><?= nl2br(html_safe($option['company_signature'])) ?></td>
			<td style="text-align:center;width:0px;"><a  href="?action=edit&opt_type=company&type=signature&option_id=<?= $option['company_id'] ?>">Edit</a></td>
		</tr>
<?php
	}
?>
		<form action="?action=add&opt_type=company">
		<tr class="alt"><td colspan="3" style="text-align:center;">
<!--			<input type="submit" value="Add New Company" /> -->
		</td></tr>
		</form>
	</table>
</div>

<br />

<?php
	break;

case 'add':
?>
Display Company Info Add form here
<?php
	break;

case 'edit':
	$row = $DB->query_first(sprintf(
		"SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
		$posted['option_id']
	), MYSQLI_ASSOC);
?>
<form action="?action=do_edit&opt_type=company&option_id=<?= $posted['option_id'] ?>&type=<?= $posted['type'] ?>" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Company Name :</td>
			<td><input type ="text" name="company_name" value="<?= ucfirst($row['company_name']) ?>" style="width:100%;"></td>
		</tr>
		<tr>
			<?php if($_GET['type']=='header'){ ?>
			<td class="left_title">Invoice Header :</td>
			<td><textarea name="company_invoiceheader" rows="10"><?= $row['company_invoiceheader'] ?></textarea></td>
			<?php } if($_GET['type']=='signature'){ ?>
			<td class="left_title">Email Signature :</td>
			<td><textarea name="company_signature" rows="10"><?= $row['company_signature'] ?></textarea></td>
			<?php } ?>
		</tr>
		
	</table>
	<p align="center">
		<input type="button" value="Go Back" onclick="javascript:history.back();" />
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
	break;

case 'do_edit':
	if ($_GET['type'] == 'header') {
		$sql = sprintf(
			"UPDATE `highwaygroup`.`company_info` SET `company_invoiceheader` = %s,`company_name` = %s WHERE `company_id` = %d LIMIT 1",
			$DB->escape($posted['company_invoiceheader']),
			$DB->escape($posted['company_name']),
			$posted['option_id']
		);
	} else {
		$sql = sprintf(
			"UPDATE `highwaygroup`.`company_info` SET `company_signature` = %s,`company_name` = %s WHERE `company_id` = %d LIMIT 1",
			$DB->escape($posted['company_signature']),
			$DB->escape($posted['company_name']),
			$posted['option_id']
		);
	}
	$DB->query($sql);
	echo 'Successfully Updated Value';
	break;
}
?>