<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('member'); //memberWarningsFromDatabase

$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" alt="' . (('desc' == $order) ? 'desc' : 'asc') . '" />';

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
unset($sort_url["page_id"]);
$sort_url = '?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['invoice_id']);
unset($edit_url["page_id"]);
$edit_url = '.?' . http_build_query($edit_url) . '&invoice_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['invoice_id']);
unset($view_url["page_id"]);
$view_url = '.?' . http_build_query($view_url) . '&invoice_id=';

$view2_url           = $get_vars;
$view2_url['action'] = 'tax_view';
unset($view2_url['invoice_id']);
unset($view2_url["page_id"]);
$view2_url = '.?' . http_build_query($view2_url) . '&invoice_id=';

$member_url           = $get_vars;
$member_url['action'] = 'edit';
unset($member_url['member_id']);
unset($member_url["page_id"]);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['invoice_id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&invoice_id=';

?>
<table class="wide table_grid">
	<tr>
		<th><a href="<?=htmlspecialchars($sort_url . 'tax_id')?>">Tax Invoice</a><?=('tax_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'tax_date')?>">Tax Date</a><?=('tax_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_id')?>">Invoice #</a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'open_date')?>">Open Date</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'bill_name')?>">Customer</a><?=('bill_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_offerau')?>">Invoice Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
<?php
//if (!in_array($posted['site_id'], array(20, 26)))
if (false) {
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_offerau')?>">AUD Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
<?php
}
if (false) {
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'invoice_offerus')?>">USD Value</a><?=('invoice_offerus' == $order_by) ? $ord_img : ''?></th>
<?php
}
?>
		<th><a href="<?=htmlspecialchars($sort_url . 'status')?>">Status</a><?=('status' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$invoice_locks = invoiceGetLocks();

$alt = false;
foreach ($invoice_list as $invoice_id) {
    $invoice = invoiceFromDatabase($invoice_id);
    //$invoice_lock = invoiceGetLock($invoice_id);
    $invoice_lock = false;
    if (array_key_exists($invoice_id, $invoice_locks)) {
        $invoice_lock = $invoice_locks[$invoice_id];
    }

    $invoice_jobs     = invoiceJobsFromDatabase($invoice_id);
    $invoice_products = invoiceProductsFromDatabase($invoice_id);
    $invoice_payments = invoicePaymentsFromDatabase($invoice_id);
    $member_warnings  = (0 != $invoice['member_id']) ? memberWarningsFromDatabase($invoice['member_id']) : array(array('comments' => 'Invoice is not linked to a member'));

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

    if (!empty($invoice_lock)) {
        $class = ' class="locked"';
    }

    if (!empty($invoice_lock) && $invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
        $class = ' class="mylock"';
    }

    ob_start();
?>
Office Invoice<br />
--------------------------<br />
<table>
<?php
foreach ($invoice_products as $product) {
?>
	<tr>
		<td><?=htmlspecialchars($product['product_qty'])?></td>
		<td>x</td>
		<td>#<?=htmlspecialchars(invoiceProductFormatNumber($product['product_id'], $product['sub_id']))?>: <?=htmlspecialchars($product['product_name'])?></td>
		<td>@</td>
		<td><?=convertCurrency($product['product_offer'], $invoice["invoice_currency"])?></td>
		<td>=<?=getCurrencySymbol($invoice["invoice_currency"], 'HTML')?></td>
		<td><?=convertCurrency($product['product_offer'] * $product['product_qty'], $invoice["invoice_currency"])?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>
<?php
}
    if ('' != $invoice['ship_product_name']) {
?>
	<tr>
		<td>1</td>
		<td>x</td>
		<td><?php printf("F%02d%s", $invoice["ship_id"], ('y' == $invoice['ship_insurance']) ? 'I' : 'N')?> <?=htmlspecialchars($invoice['ship_product_name'])?></td>
		<td>@</td>
		<td><?=convertCurrency($invoice['ship_offer'], $invoice["invoice_currency"])?></td>
		<td>=<?=getCurrencySymbol($invoice["invoice_currency"], 'HTML')?></td>
		<td><?=convertCurrency($invoice['ship_offer'], $invoice["invoice_currency"])?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>
<?php
    }
?>
</table>
<?php
    $invoice_id_tooltip = ob_get_clean();

    ob_start();
?>
<?=nl2br(htmlspecialchars($invoice["bill_address_full"]))?><br />
<?php
    if (count($member_warnings)) {
?>
<br />
Member Warnings :<br />
<?php
        foreach ($member_warnings as $warning) {
            if ('' != $warning['comments']) {
                echo nl2br(htmlspecialchars('-' . $warning['comments'])) . "<br />";
            }
        }
    }
    $customer_tooltip = ob_get_clean();

    $action_tooltip = '';
    if (!empty($invoice_lock)) {
        $staff_list     = invoiceGetOptionList('staff_id');
        $action_tooltip = htmlspecialchars('This invoice/inquiry is currently locked to ' . $staff_list[$invoice_lock['staff_id']]);
    }
?>
	<tr<?=$class?>>
		<td>
			<input type="hidden" name="invoices[<?=htmlspecialchars($invoice_id)?>][invoice_id]" value="<?=htmlspecialchars($invoice_id)?>" />
			<input type="text" size="7" maxlength="10" name="invoices[<?=htmlspecialchars($invoice_id)?>][tax_id]" value="<?=htmlspecialchars((0 == $invoice["tax_id"]) ? "" : sprintf("%06d", $invoice["tax_id"]))?>" />
		</td>
		<td><input type="text" size="11" maxlength="10" onclick="if (this.value == '') {this.value='<?=(0 != $invoice["payments_locate_time"]) ? date("Y-m-d", $invoice["payments_locate_time"]) : ''?>';gfPop.fPopCalendar(this);this.value='';} else gfPop.fPopCalendar(this)" name="invoices[<?=htmlspecialchars($invoice_id)?>][tax_date]" value="<?=htmlspecialchars((is_null($invoice["tax_date"])) ? "" : $invoice["tax_date"])?>" /></td>
		<td<?=($invoice['invoice_profit'] < 0 || $invoice['invoice_profitau'] < 0) ? ' style="background:#cf6;"' : ''?> onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($invoice_id_tooltip) . ')')?>" onmouseout="tt_out()"><a href="<?=htmlspecialchars($view_url . $invoice_id)?>" target="_blank"><?=htmlspecialchars(invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']))?></a></td>
		<td><?=date('d/m/Y', $invoice['open_time'])?></td>
		<td class="wide" style="text-align:center;<?=(count($member_warnings)) ? 'border:2px solid #f19;' : ''?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($customer_tooltip) . ")")?>" onmouseout="tt_out()">
<?php
    if (0 != $invoice['member_id']) {
?>
				<a href="<?=htmlspecialchars($member_url . $invoice['member_id'])?>" target="_blank">
<?php
    }
?>
					<?=htmlspecialchars(('' != $invoice['bill_company']) ? $invoice['bill_company'] : $invoice['bill_name'])?>
<?php
    if (0 != $invoice['member_id']) {
?>
				</a>
<?php
    }
?>
		</td>
		<td style="text-align:center;<?=(0 == $invoice['invoice_cost'] || 0 == $invoice['invoice_costau']) ? 'background:#c6f;' : ''?>"><?=getCurrencySymbol($invoice["invoice_currency"], 'HTML') . convertCurrency($invoice['invoice_offer'], $invoice["invoice_currency"])?> <?=htmlspecialchars($invoice['invoice_currency'])?></td>
		<td<?=("" == $invoice["handler_note"]) ? '' : (' onmouseover="' . htmlspecialchars('tt_in(' . json_encode($invoice['handler_note']) . ')') . '" onmouseout="tt_out()"')?>><img src="images/status/<?=$invoice['invoice_action']?>.gif" alt="<?=htmlspecialchars($invoice['invoice_action'])?>" /></td>
		<td<?=('' == $action_tooltip) ? '' : (' onmouseover="' . htmlspecialchars('tt_in(' . json_encode($action_tooltip) . ')') . '" onmouseout="tt_out()"')?>>
<?php
    if (empty($invoice_lock) || $invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
			<a href="<?=htmlspecialchars($edit_url . $invoice_id)?>" target="_blank">Edit</a>
<?php
    }
?>
			<a href="<?=htmlspecialchars($view2_url . $invoice_id)?>" target="_blank">View&nbsp;TAS</a>
<?php
    if (!empty($invoice_lock) && $invoice_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
			<a href="<?=htmlspecialchars($unlock_url . $invoice_id)?>">Unlock</a>
<?php
    }
?>
		</td>
	</tr>
<?php
}
?>
</table>

<br />

<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="background:#8e9;">green</span> have new messages<br />
Rows marked in <span style="background:#e89;">red</span> are locked by another user<br />
Rows marked in <span style="background:#89e;">blue</span> are locked by you<br />
Cells marked in <span style="background:#ff0;">yellow</span> have been idle for <?=$_GLOBALS['site_options']['WARNING_IDLE_DAYS']?> or more days<br />
Cells marked in <span style="background:#c6f;">purple</span> don't have a cost price.<br />
Cells marked in <span style="border:2px solid #f70;">orange</span> have special freight requests from the customer<br />
Cells marked in <span style="border:2px solid #f19;">deep pink</span> have member warnings.<br />
Cells marked in <span style="background:#c56;">light green</span> indicate a loss for the invoice.<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
