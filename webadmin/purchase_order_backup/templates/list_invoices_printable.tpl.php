<!-- list_invoices_printable.tpl -->

<?php
moduleLoad('invoice'); //invoiceJobsFromDatabase

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = 'index.php?' . http_build_query($sort_url);
$sort_url .= '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['invoice_id']);
$edit_url = 'index.php?' . http_build_query($edit_url);
$edit_url .= '&invoice_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['invoice_id']);
$view_url = 'index.php?' . http_build_query($view_url);
$view_url .= '&invoice_id=';

$member_url           = $get_vars;
$member_url['action'] = 'view';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['invoice_id']);
$unlock_url = 'index.php?' . http_build_query($unlock_url);
$unlock_url .= '&invoice_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['invoice_id']);
$select_url = 'index.php?' . http_build_query($select_url);
$select_url .= '&invoice_id=';

?>

<style type="text/css">
/*<![CDATA[*/
td{white-space:nowrap}
/*]]>*/
</style>

<table class="wide spacetable" align="center">
	<tr>
		<th>Site</th>
		<th><?= ($display_inquiry) ? 'Inquiry' : 'Invoice' ?> #</th>
		<th>Open Date</th>
		<th>Customer Name</th>
		<th>Invoice Value</th>
		<th>USD Value</th>
		<th>Products Located</th>
		<th>Freight Located</th>
<?php
if (!$display_inquiry) {
?>
		<th>Payment Made</th>
<?php
}
?>
		<th>Status</th>
		<th>Handler</th>
		<th>Last Staff</th>
		<th>Last Modified</th>
		<th>Days Open</th>
	</tr>

<?php
$site_list = invoiceGetOptionList('site_id');

foreach ($invoice_list as $invoice_id) {
    $invoice      = invoiceFromDatabase($invoice_id);
    $invoice_lock = invoiceGetLock($invoice_id);
    $invoice_jobs = invoiceJobsFromDatabase($invoice_id);

    $class = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (count($invoice_jobs) > 0) {
        $class = ' class="highlight"';
    }

    if ($invoice['staff_id'] != $invoice['handler_id']) {
        $class = ' class="highlight"';
    }

    if (false !== $invoice_lock) {
        $class = ' class="locked"';
    }

    if ($invoice_lock['staff_id'] == $staff_id) {
        $class = ' class="mylock"';
    }

    $products_located = (('y' == $invoice['products_located']) ? 'Yes' : 'No');
    $ship_located     = (('y' == $invoice['ship_located']) ? 'Yes' : 'No');
    $payments_located = (('y' == $invoice['payments_located']) ? 'Yes' : 'No');
    if ('y' != $invoice['products_located']) {
        $products_located = $invoice['products_found'] . '/' . $invoice['products_needed'];
    }

    $invoice_type    = array_search($invoice['invoice_status'], array(
        'inquiry',
        'enquiry',
        'closed',
        'proforma',
        'sales',
        'processed',
        'cancelled',
        'return',
        'completed',
        'purchase',
    ));
    $invoice_actions = array(
        'shipped'    => 'Goods Shipped',
        'approval'   => 'Awaiting Payment Approval',
        'shipping'   => 'Awaiting Shipment',
        'payment'    => 'Awaiting Payment',
        'feedback'   => 'Awaiting Customer Feedback',
        'nointerest' => 'Customer Not Interested',
        'cancelled'  => 'Cancelled',
        'processed'  => 'Processed',
    );

    $status_image = $invoice['invoice_status'];
    if ($invoice_type < 3)
    {
        $status_image = 'q_working';
        if ('feedback' == $invoice['invoice_action']) {
            $status_image = 'q_feedback';
        }

        if (2 == $invoice_type) {
            $status_image = 'q_cancelled';
        }

        if ('nointerest' == $invoice['invoice_action']) {
            $status_image = 'q_nointerest';
        }

    } else //Invoice
    {
        $status_image = $invoice['invoice_action'];
//        if($invoice_type < 5)
        //        {
        //            if($invoice['ship_located']=='n') $status_image='shipping';
        //            if($invoice['payments_located']=='n') $status_image='payment';
        //        }
    }
?>
	<tr<?= $class ?>>
		<td style="text-align:center;"><?php hecho($site_list[$invoice['site_id']])?></td>
		<td style="text-align:right;"><?= invoiceFormatNumber($invoice['invoice_id'], 0, $invoice['site_id']) ?></td>
		<td style="text-align:center;"><?= date('d/m/Y', $invoice['open_time']) ?></td>
		<td style="text-align:center;"><?php hecho($invoice['bill_name'])?></td>
		<td style="text-align:right;"><?= $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'] ?></td>
		<td style="text-align:right;"><?= $invoice['invoice_offerus'] ?></td>
		<td style="text-align:center;"><?= $products_located ?></td>
		<td style="text-align:center;"><?= $ship_located ?></td>
<?php
if (!$display_inquiry) {
?>
		<td style="text-align:center;"><?= $payments_located ?></td>
<?php
}
?>
		<td style="text-align:center;"><?= $invoice_actions[$invoice['invoice_action']] ?></td>
		<td style="text-align:center;"><?php hecho($all_staff_list[$invoice['handler_id']])?></td>
		<td style="text-align:center;"><?php hecho($all_staff_list[$invoice['staff_id']])?></td>
		<td style="text-align:center;"><?= date('d/m/Y', $invoice['write_time']) ?></td>
		<td style="text-align:center;"><?= $invoice['days_open'] ?></td>
	</tr>
<?php
if ('' != $invoice['handler_note']) {
?>
	<tr>
		<td></td>
		<td colspan="12"><?php hecho($invoice['handler_note'])?></td>
	</tr>
<?php
}
}
?>
</table>

<!-- /list_invoices_printable.tpl -->
