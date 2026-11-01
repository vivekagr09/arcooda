<!-- addedit_invoice.tpl -->

<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

?>

<script type="text/javascript">
//<![CDATA[
function validateInvoice()
{
	var site_id = getValue("site_id");
	if (site_id != null && (site_id == "" || site_id < 1))
	{
		alert("Please choose a company for this <?=($display_inquiry) ? "Inquiry" : "Invoice"?>");
		getElement("site_id").focus();
		return false;
	}
	var sts = getValue("invoice_status");
	var ac = getValue("invoice_action");
	var member_id = getValue("supplier_id");
	var check_member = false;
	if (sts == "inquiry" || sts == "inquiry" || sts == "closed")
	{
		if (ac == "sales") check_member = true;
	}
	else check_member = true;
	if ((member_id == "" || member_id < 1))
	{
		console.log('sdgfs');
		alert("Please choose a Supplier from the database");
		getElement("bill_name").focus();
		return false;
	}
	var pay_made = getValue("payment_made");
	if (pay_made != "" && pay_made > 0)
	{
		if (getValue("payment_notes") == "" && getValue("payment_options") == 0)
		{
			alert("Please enter Payment notes for the payment that was made.");
			getElement("payment_notes").select();
			getElement("payment_notes").focus();
			return false;
		}
	}

	var producttype_id = getValue("producttype_id");
	if (producttype_id == "" || producttype_id <= 0) {
		alert("Please choose product type.");
		getElement("producttype_id").select();
		getElement("producttype_id").focus();
		return false;
	}

	var invoice_currency = getValue("invoice_currency");
	if (invoice_currency == "")
	{
		alert("Please choose a currency");
		getElement("invoice_currency").focus();
		return false;
	}
	var rows = getElement("product_table").rows;
	var str = "";
	var ids = [];
	var count = 0;
	for (key in rows)
	{
		var n = rows[key].id;
		if (n && n != null)
		{
			var t1 = n.split("[");
			var t2 = t1[1].split("]");
			if (!searchArray(t2[0], ids))
			{
				ids[count] = t2[0];
				count++;
			}
		}
	}
	for (key in ids)
	{
		var gen = getValue("generic[" + ids[key] + "]");
		if (gen == "n")
		{
			var id = getValue("product_id[" + ids[key] + "]");
			if (id == 0)
			{
				alert("Products need to have an ID number. Please enter one");
				return false;
			}
		}
	}
	var email_checked = checkEmailAccountSelected();
	if (!email_checked) return false;

	var ship_to = getValue('shipto_country[0]');
	//if (invoice_currency == 'AUD' && ship_to == 'AU' && !gstAdded) return confirm('There is no GST added for this AUD value <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?>. Are you sure you want to continue??');

	return true;
}
function addWantedItems()
{
	openWindow("add_wanted_items.php?invoice_id=<?=$invoice_id?>", 600, 700, true, "scrollbars=1");
}
//]]>
</script>

<form accept-charset="utf-8" method="post" action="index.php?action=<?=$form_action?>">
<?php
// need to display the changes that were made in the last 5 mins or so.
if ('add' != $action) {
    $query = $DB->query("SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_type_id`, `message_title`, `message_notes` FROM `invoice_messages` WHERE `invoice_id` = " . intval($invoice_id) . " AND `message_date` > " . formatSQLDateTime(time() - 255) . " AND `message_sender` = " . intval($staff_id) . " ORDER BY `message_date` DESC");
    if ($DB->num_rows($query)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
while ($message = $DB->fetch_assoc($query)) {
            $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
            $notes = explode(" ", $message['message_notes']);
            foreach ($notes as $key => $string) {
                $notes[$key] = trim(chunk_split($string, 80));
            }

            $notes = implode(" ", $notes);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?php hecho($message['message_title'])?> : <?=nl2br(htmlspecialchars($notes))?></li>
</div>
<br /><br />
<?php
}
    }
}
?>

<?php
if ($invoice_lock['staff_id'] != $staff_id && 'add' != $action) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
if (false === $invoice_lock) {
        echo 'This ' . (($display_inquiry) ? 'inquiry' : 'invoice') . ' is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $invoice_lock['expiry_time'] - time();
        echo 'This ' . (($display_inquiry) ? 'Inquiry' : 'Invoice') . ' is currently locked to ' . $all_staff_list[$invoice_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
	</td></tr>
</table>
<br /><br />
<?php
} elseif ('add' != $action) {
?>
<script type="text/javascript">
//<![CDATA[
if (!Date.now) Date.now = function() {
	return new Date().getTime();
};
$(function() {
	var lock_interval;
	var lock_refresh = function() {
		$.getJSON("/webadmin/purchase_order/?action=json_lock&order_id=<?=$order_id?>&timestamp=" + Math.floor(Date.now() / 1000), function(data) {
			if (data === false)
			{
				alert("There was an issue when trying to re-lock this invoice for a longer period\n\nPossible Reasons:\n - Another user has locked this invoice\n - You are having internet connection issues");
				window.clearInterval(lock_interval);
				$("[value='Submit Changes']").add("[value='Update Invoice']").add("[value='Update']").attr("disabled", "disabled");
			}
		});
	};
	lock_interval = window.setInterval(lock_refresh, 10000);
	lock_refresh();
});
//]]>
</script>
<?php
}
if ('JPY' == $invoice['invoice_currency']) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
		Please note that Japanese Yen does not have cents<br />
		The customer may be unable to pay if the invoice total is not a whole number
	</td></tr>
</table>
<br /><br />
<?php
}
switch ($form_action) {
    case 'do_edit':
        include __DIR__ . "/summary_table.tpl.php";
        include __DIR__ . "/member_table.tpl.php";
        include __DIR__ . "/product_supplier.tpl.php";
        if (count($invoice_porders)) {
            include __DIR__ . "/invoice_purchase_orders.tpl.php";
        }

        include __DIR__ . "/order_information.tpl.php";
       /* include __DIR__ . "/shipping_information_new.tpl.php";
        include __DIR__ . "/freight_quotation_new.tpl.php";*/
        include __DIR__ . "/payment_information.tpl.php";
        include __DIR__ . "/attachments.tpl.php";
        include __DIR__ . "/goods_received.tpl.php";
        //include __DIR__ . "/new_link_table.tpl.php";
        include __DIR__ . "/message_sending.tpl.php";
        include __DIR__ . "/admin_history.tpl.php";
        include __DIR__ . "/message_history.tpl.php";
        include __DIR__ . "/invoice_comments.tpl.php";
        if($invoice['order_action']=='order-working' || $invoice['order_action']=='supplier-waiting' || $invoice['order_action']=='payment' || $invoice['order_action']=='goods' || $invoice['order_action']=='checking-goods' || $invoice['order_action']=='order' || $invoice['order_action']=='processed'){
	        include __DIR__ . "/add_to_inventory.php";
	    }
        //if (in_array($invoice['invoice_status'], array('proforma', 'sales', 'processed', 'cancelled'))) include __DIR__ . '/proforma_table.tpl.php';
        include __DIR__ . "/action_buttons.tpl.php";
        break;

    case 'do_add':
        include __DIR__ . "/member_table.tpl.php";
        include __DIR__ . "/product_supplier.tpl.php";
        include __DIR__ . "/order_information.tpl.php";
        //include __DIR__ . "/shipping_information_new.tpl.php";
        //include __DIR__ . "/freight_quotation_new.tpl.php";
        include __DIR__ . "/payment_information.tpl.php";
        include __DIR__ . "/message_sending.tpl.php";

        //if (in_array($invoice['invoice_status'], array('proforma', 'sales', 'processed', 'cancelled'))) include __DIR__ . '/proforma_table.tpl.php';
        break;

    default:
        break;
}
$invoice_status = $invoice['invoice_status'];
switch ($invoice['order_action']) {
    case 'working':
    case 'cancelled':
    case 'supplier':
        $status_list = array(
            'working'    => 'Working on Purchase Inquiry',
            'supplier'   => 'Awaiting Supplier',
            'order'   => 'Make Purchase Order',
            'cancelled'  => 'Cancelled',
        );
        break;

    case 'order-working':
    case 'supplier-waiting':
    case 'payment':
    case 'goods':
    case 'checking-goods':
    case 'processed':
    case 'order':
        $status_list = array(
            'order-working'    => 'Working on Purchase Order',
            'supplier-waiting'   => 'Waiting Supplier',
            'payment'   => 'Arranging Payment',
            'goods'  => 'Waiting Goods',
            'checking-goods'  => 'Checking Goods',
            'processed'  => 'Processed',
        );
        break;
    default:
    $status_list = array(
        'working'    => 'Working on Purchase Inquiry',
        'supplier'   => 'Awaiting Supplier',
        'order'   => 'Make Purchase Order',
        'cancelled'  => 'Cancelled',
    );
    break;
}
if ('' != $invoice['invoice_status']) {
?>
<input type="hidden" name="invoice_status" value="<?=$invoice['invoice_status']?>" />
<?php
}
// New Code to take the Wanted Ids into account...
$wanted = explode(',', $invoice['wanted_id']);
?>
<p>Status: <select style="width:auto;" name="order_action">
	<?=formatHTMLOptions($status_list, $invoice['order_action'])?>
</select></p>

<br /><br />

<input type="button" value="Submit Changes"<?=($invoice_lock['staff_id'] == $staff_id || 'add' == $action || (!isset($invoice_lock['staff_id']))) ? ' onclick="if(validateInvoice()) this.form.submit();"' : ' disabled="disabled"'?> />
<input type="button" value="Print" onclick="window.print();" />
<?=('inquiry' == $invoice_status && 2 == $posted['site_id'] && count($invoice_products) && '' == $invoice['wanted_id']) ? '<input type="button" value="Add Wanted Items" onclick="addWantedItems()" />' : ''?>

</form>

<!-- /addedit_invoice.tpl -->
