<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<div id="sectionCouriers" style="<?=($hideCouriers) ? 'display:none' : ''?>">

<?=formatHTMLTitle('Courier Quotation')?>

<table class="wide">
<?php
if (count($selected_courier)) {
    $courier_name = $invoice['ship_product_name'] . (($invoice['ship_id'] > 100) ? ' (Personal Account)' : '');
    $price        = ('USD' == $invoice['invoice_currency']) ? $selected_courier['sell_price'] : $selected_courier['sell_priceau'];
    if ('AUD' != $invoice['invoice_currency'] && 'USD' != $invoice['invoice_currency']) {
        $price = convertCurrency($price, 'USD', $invoice['invoice_currency']);
    }

    $insurance = ('y' == $selected_courier['insurance']) ? 'Yes' : (('n' == $selected_courier['insurance']) ? 'No' : htmlspecialchars($selected_courier['insurance']));
?>
	<tr>
		<td class="left_title" style="width:25%">Courier</td>
		<td><input style="width:98%" type="text" name="ship_courier_name" value="<?=htmlspecialchars($courier_name)?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Sale Price :</td>
		<td><input style="width:98%" name="ship_courier_price" value="$<?=htmlspecialchars($invoice['ship_offer'] . ' ' . $invoice['invoice_currency'])?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Transit Time :</td>
		<td><input style="width:98%" type="text" name="ship_courier_transit" value="<?=htmlspecialchars($selected_courier['del_time'])?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Insurance :</td>
		<td><input style="width:98%" type="text" name="ship_courier_insurance" value="<?=$insurance?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title" colspan="2" style="text-align:center"><input type="button" value=" View Other Courier Methods " onclick="toggleSection('other_couriers')"></td>
	</tr>
<?php
}
?>
	<tbody id="other_couriers" style="<?=count($selected_courier) ? "display:none" : ""?>">
	<tr>
		<td class="left_title" style="width:25%">Shipping Via</td>
		<td colspan="2"><input type="button" value="Search Couriers" onclick="javascript:freightCourierSearch(this.form);" /> - Results for <span name="freightcourier_locality">.</span> <span name="freightcourier_postcode">.</span> <span name="freightcourier_state">.</span> <span name="freightcourier_country">.</span></td>
	</tr>
	<tr>
		<td class="left_title"><iframe name="freight_search" style="width:16px;height:16px;visibility:hidden;"></iframe></td>
		<td colspan="2"><table name="freight_courier_table" class="spacetable" style="width:100%;text-align:center;">
			<tr>
				<th></th>
				<th>Service</th>
				<th>Delivery Time</th>
				<th>Track</th>
				<th>Insure</th>
				<th>Tail Gate</th>
				<th>Cost<br />Price<br />(<span name="product_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span>)</th>
				<th>Sell<br />Price<br />(<span name="product_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span>)</th>
<?php
if ('AUD' != $invoice['invoice_currency']) {
?>
				<th>Cost<br />Price<br />(AUD)</th>
				<th>Sell<br />Price<br />(AUD)</th>
<?php
}
?>
			</tr>
			<tbody id="freight_couriers" name="freight_couriers">
<?php
$courier_list = searchCouriers(
	$invoice['ship_country'],
	$invoice['ship_postcode'],
	$invoice['ship_state'],
	$invoice['ship_city'],
	$invoice['ship_weight'],
	$invoice['products_offerau'],
	'AUD',
	$invoice['site_id']
);
$count = 0;
if (is_array($invoice_courier_quotes)) {
    $flagFound = false;
    foreach ($invoice_courier_quotes as $quote) {
        $quote['id'] = freightServiceId($quote['name']);
        if (!$flagFound) {
            if ('y' == $quote['flag']) {
                $flagFound = true;
            }

            if ($invoice['ship_id'] == $quote['id'] && $invoice['ship_insurance'] == $quote['insurance'] && $invoice['ship_tailgate'] == $quote['tail_gate']) {
                $flagFound     = true;
                $quote['flag'] = 'y';
            }
        }
        $class = '';
        if ($count % 2) {
            $class = "alt";
        }

        echo freightcourier_row($count, $quote, $class);
        $count++;
    }
}
?>
			</tbody>
			<tfoot id="members_couriers" name="members_couriers">
<?php
$member_selected = false;
if (is_array($member_couriers)) {
    foreach ($member_couriers as $member_courier) {
        $class = ($count % 2) ? "alt" : '';
        $name  = ($member_courier['courier_id'] < 100) ? getFreightServiceName($member_courier['courier_id']) : getFreightServiceProviderName($member_courier['courier_id']);
        $name .= " (" . $member_courier['account_num'] . ')';
        $quote = array('code' => 0.0, 'id' => $member_courier['courier_id'], 'name' => $member_courier['courier_name'], 'del_time' => 'Personal Account (' . $member_courier['account_num'] . ')', 'track' => 'N/A', 'insurance' => 'N/A', 'cost_price' => '0.00', 'retail_price' => '0.00', 'sell_price' => '0.00', 'cost_priceau' => '0.00', 'sell_priceau' => '0.00', 'supplier' => $member_courier['courier_id'], 'acc_num' => $member_courier['account_num'], 'supplier_name' => $member_courier['account_num']);
        if ($invoice['ship_id'] == $member_courier['courier_id']) {
            $quote['flag']   = 'y';
            $member_selected = true;
        }
        echo freightcourier_row($count, $quote, $class, true);
        $count++;
    }
}
?>
			</tfoot>
<?php
if (count($couriers_list) > 0 || count($member_couriers) > 0) {
?>
			<tr><td><input type="button" onclick="javascript:clearFreightCouriers();" value="Clear" /></td></tr>
<?php
}
?>
		</table></td>
	</tr>
	</tbody>
<?php
$yesno = array('y' => 'Yes', 'n' => 'No');
?>
	<tr>
		<td class="left_title">Invoice Date</td>
		<td><input name="proforma_date" id="proforma_date" type="text" size="24" readonly="readonly" value="<?=$invoice['proforma_date']?>" /><input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(this.form.proforma_date);" /><input type="button" value="Clear" onclick="javascript:setValue('proforma_date','');" /></td>
	</tr>
	<tr id="account_num_tr" name="account_num_tr" style="<?=($member_selected) ? '' : 'display:none'?>">
		<td class="left_title">Shipping Account Number</td>
		<td><input name="ship_account_num" style="width:98%;" type="text" value="<?=htmlspecialchars($invoice['ship_account_num'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Goods Packed By Me</td>
		<td><select onchange="updateGoodsExtra('packed')" name="packed_by"><?=formatHTMLOptions($yesno, $invoice['packed_by'])?></select></td>
	</tr>
	<tr id="packed_by_tr" name="packed_by_tr" style="<?=('y' == $invoice['packed_by'] || '' == $invoice['packed_by']) ? 'display:none' : ''?>">
		<td class="left_title">&nbsp;</td>
		<td><select name="goods_packed_by"><?=formatHTMLOptions(invoiceGetOptionList('goods_packed_by'), $invoice['goods_packed_by'])?></select></td>
	</tr>
	<tr>
		<td class="left_title">Goods Shipped By Me</td>
		<td><select onchange="updateGoodsExtra('shipped')" name="shipped_by"><?=formatHTMLOptions($yesno, $invoice['shipped_by'])?></select></td>
	</tr>
	<tr id="shipped_by_tr" name="shipped_by_tr" style="<?=('y' == $invoice['shipped_by'] || '' == $invoice['shipped_by']) ? 'display:none' : ''?>">
		<td class="left_title">&nbsp;</td>
		<td><select name="goods_shipped_by"><?=formatHTMLOptions(invoiceGetOptionList('goods_shipped_by'), $invoice['goods_shipped_by'])?></select></td>
	</tr>
	<tr id="process_invoice_tr" name="process_invoice_tr" style="display:none">
		<td class="left_title">Process Invoice</td>
		<td><input type="radio" name="process_invoice" value="n" checked>&nbsp;No&nbsp;<input type="radio" name="process_invoice" value="y">&nbsp;Yes&nbsp;</td>
	</tr>
</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
