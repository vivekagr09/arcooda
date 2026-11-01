<!-- new_link_table.tpl -->

<?php
if (!is_array($link_list) || count($link_list) == 0) {
    return;
}

$invoice_viewurl = "invoice_admin.php?action=print&invoice_id=";
$invoice_editurl = "invoice_admin.php?action=edit&invoice_id=";
$status_array    = array(
    'enquiry'   => '<span style="color:green;font-weight:bold;">Open</span>',
    'closed'    => '<span style="color:red;font-weight:bold;">Closed</span>',
    'proforma'  => '<span style="color:blue;font-weight:bold;">Proforma</span>',
    'sales'     => '<span style="color:blue;font-weight:bold;">Sales</span>',
    'processed' => '<span style="color:blue;font-weight:bold;">Processed</span>',
    'cancelled' => '<span style="color:red;font-weight:bold;">Cancelled</span>',
);
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionNewLinker\');">Linked Invoices</a>') ?>

<div id="sectionNewLinker">
<?php
if (!$print_me)
//-----------------------------------------------------------------------------
//                                BEGIN EDIT INVOICE VERSION
{
?>
<table class="wide">
	<tr>
		<td class="left_title">Other Invoices</td>
		<td><table style="width:100%;border-width:0px;">
			<input type="hidden" name="new_linker" value="true" />

<?php
if (count($group_list) > 0) {
        foreach ($group_list as $gid => $lw_id) {
?>
			<tr style="border:1px solid #999999;">
				<td><input type="checkbox" name="linkinvoice_id[]" value="<?= $lw_id ?>"<?= ($group_id == $gid) ? ' checked="checked"' : '' ?> /></td>
				<td>
<?php
foreach ($link_list as $link) {
                if ($link['group_id'] == $gid) {
?>
					<a href="<?= $invoice_editurl . $link['invoice_id'] ?>" target="_blank"><?= invoiceFormatNumber($link['invoice_id']) ?></a> - <?= $link['status'] . $status_array[$link['invoice_status']] ?> - <?= formatDate($link['open_time']) ?> - <?= $product_types[$link['producttype_id']] ?><br />
<?php
}
            }

?>
				</td>
			</tr>
<?php
}
    }
?>

<?php
if (count($link_list) > 0) {
        foreach ($link_list as $link) {
            if ('' == $link['group_id']) {
?>
			<tr style="border:1px solid #999999;">
				<td><input type="checkbox" name="linkinvoice_id[]" value="<?= $link['invoice_id'] ?>" /></td>
				<td><a href="<?= $invoice_editurl ?><?= $link['invoice_id'] ?>" target="_blank"><?= invoiceFormatNumber($link['invoice_id']) ?></a> - <?= $status_array[$link['invoice_status']] ?> - <?= formatDate($link['open_time']) ?> - <?= $product_types[$link['producttype_id']] ?></td>
			</tr>
<?php
}
        }
    }

?>
		</table></td>
	</tr>
</table>
<?php
} //if(!$print_me)
//                                END EDIT INVOICE VERSION
else //------------------------------------------------------------------------
//                                BEGIN PRINT ONLY VERSION
{
?>
<table class="wide">
<?php
if ($group_id) {
?>
	<tr>
		<td style="text-align:center;">This invoice is linked with
<?php
foreach ($link_list as $link) {
            if ($link['group_id'] == $group_id) {
?>
			<?= invoiceFormatNumber($link['invoice_id']) ?>
<?php
}
        }
?>
		</td>
	</tr>
<?php
} //if($group_id)
    else {
?>
	<tr><td style="text-align:center;">This invoice has not been linked with any other invoice</td></tr>
<?php
} //if($group_id) - else
} //if(!$print_me) - else
//                                END PRINT ONLY VERSION
//-----------------------------------------------------------------------------
?>
</div><br /><br />
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionNewLinker_hideNote">
All Linked Invoices can be viewed by Clicking "Linked Invoices" above
</div><br /><br />

<!-- /link_table.tpl -->
