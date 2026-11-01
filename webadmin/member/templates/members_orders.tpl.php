<!-- members_orders.tpl -->

<?php
moduleLoad('invoice'); //invoiceSort, invoiceGetOptionList

$all_invoices   = array_reverse(invoiceSort($all_invoices, 'open_date'));
$tmp            = array_chunk($all_invoices, 10);
$invoices_all   = $tmp[0];
$paymentMethods = getPaymentMethodsList();
$status         = invoiceGetOptionList("invoice_action");

if (count($all_invoices)) {
    echo formatHTMLTitle(
        '<a href="#" onclick="toggleSection(\'sectionOrders\');return false">Customer Inquiries and Orders</a>',
        '<i><a style="color:#000;" target="_blank" href="' . htmlspecialchars("/webadmin/invoice/?action=add&status=return&member_id=" . $member_id) . '">Create New Repair</a> |<a style="color:#000;" target="_blank" href="' . htmlspecialchars("/webadmin/invoice/?action=add&status=inquiry&member_id=" . $member_id) . '">Create New Inquiry</a> | <a style="color:#000;" target="_blank" href="' . htmlspecialchars("/webadmin/invoice/?action=add&status=sales&member_id=" . $member_id) . '">Create New Invoice</a></i>'
    );
?>

<div id="sectionOrders">
<?php
include __DIR__ . '/list_orders.tpl.php';
    $countInv = count($all_invoices);
?>
	<br />
	<div style="text-align:center"><input style="<?=($countInv <= 10) ? 'display:none;' : ''?>" type="button" value="View All (<?=$countInv?>)" onclick="window.open('members_orders.php?member_id=<?=$member_id?>')"  /></div>
</div>
<br />
<?php
}
?>

<!-- /members_orders.tpl -->
