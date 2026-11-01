<?php
/**
 * @var string        $action
 * @var bool          $display_inquiry
 * @var int           $invoice_id
 * @var mixed[]       $invoice
 * @var mixed[]|false $invoice_lock
 * @var string[]      $all_staff_list
 * @var mixed[]       $posted
 * @var int[]         $invoice_porders
 * @var mixed[][]     $invoice_products
 */

?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
global $DB;

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
function validateInvoice() {
	var site_id = getValue("site_id");
	if (site_id != null && (site_id == "" || site_id < 1)) {
		alert("Please choose a company for this <?=($display_inquiry) ? "Inquiry" : "Invoice"?>");
		getElement("site_id").focus();
		return false;
	}
	var sts          = getValue("invoice_status");
	var ac           = getValue("invoice_action");
	var member_id    = getValue("member_id");
	var check_member = false;
	if (sts == "inquiry" || sts == "enquiry" || sts == "closed") {
		if (ac == "sales") {
            check_member = true;
        }
	} else {
        check_member = true;
    }
	if (check_member && (member_id == "" || member_id < 1)) {
		alert("Please choose a member from the database");
		getElement("bill_name").focus();
		return false;
	}
	var pay_made = getValue("payment_made");
	if (pay_made != "" && pay_made > 0) {
		if (getValue("payment_notes") == "" && getValue("payment_options") == 0) {
			alert("Please enter Payment notes for the payment that was made.");
			getElement("payment_notes").select();
			getElement("payment_notes").focus();
			return false;
		}
	}
	var invoice_currency = getValue("invoice_currency");
	if (invoice_currency == "") {
		alert("Please choose a currency");
		getElement("invoice_currency").focus();
		return false;
	}
	var rows  = getElement("product_table").rows;
	var str   = "";
	var ids   = [];
	var count = 0;
	for (key in rows) {
		var n = rows[key].id;
		if (n && n != null) {
			var t1 = n.split("[");
			var t2 = t1[1].split("]");
			if (!searchArray(t2[0], ids)) {
				ids[count] = t2[0];
				count++;
			}
		}
	}
	for (key in ids) {
		var gen = getValue("generic[" + ids[key] + "]");
		if (gen == "n") {
			var id = getValue("product_id[" + ids[key] + "]");
			if (id == 0) {
				alert("Products need to have an ID number. Please enter one");
				return false;
			}
		}
	}
	var email_checked = checkEmailAccountSelected();
	if (!email_checked) {
        return false;
    }
	var ship_to = getValue('shipto_country[0]');
	if (invoice_currency == 'AUD' && ship_to == 'AU' && !gstAdded) {
        return confirm('There is no GST added for this AUD value <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?>. Are you sure you want to continue?');
    }
	return true;
}
function addWantedItems() {
	openWindow("add_wanted_items.php?invoice_id=<?=$invoice_id?>", 600, 700, true, "scrollbars=1");
}
//]]>
</script>

<form accept-charset="utf-8" method="post" action=".?action=<?=$form_action?>" id="invoice-form">
<?php
// need to display the changes that were made in the last 5 mins or so.
if ('add' != $action) {
    if ($query = $DB->query(sprintf(
        "SELECT UNIX_TIMESTAMP(`message_date`) AS `message_time`, `message_type_id`, `message_title`, `message_notes` FROM `highwaygroup`.`invoice_messages` WHERE `invoice_id` = %d AND `message_date` > %s  ORDER BY `message_date` DESC",
        $invoice_id,
        formatSQLDateTime(time() - 255),
    ))) {
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
	<li><?=htmlspecialchars($message['message_title'])?> : <?=nl2br(htmlspecialchars($notes))?></li>
</div>
<br />
<br />
<?php
}
        }
    }
}
?>

<?php
if ('edit' == $action && (empty($invoice_lock) || $invoice_lock['staff_id'] != $_GLOBALS['staff']['id'])) {
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
    if (empty($invoice_lock)) {
        echo 'This ' . (($display_inquiry) ? 'inquiry' : 'invoice') . ' is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $invoice_lock['expiry_time'] - time();
        echo 'This ' . (($display_inquiry) ? 'Inquiry' : 'Invoice') . ' is currently locked to ' . $all_staff_list[$invoice_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
	</td></tr>
</table>

<br />
<br />

<?php
} elseif ('add' != $action) {
?>
<script type="text/javascript">
//<![CDATA[
if (!Date.now) {
    Date.now = function() {
        return new Date().getTime();
    };
}
$(function() {
	var lock_interval;
	var lock_refresh = function() {
		$.getJSON("/webadmin/invoice/?action=json_lock&invoice_id=<?=$invoice_id?>&timestamp=" + Math.floor(Date.now() / 1000), function(data) {
			if (data === false || data.staff_id != <?=$_GLOBALS['staff']['id']?>) {
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

<br />
<br />

<?php
}

?>
<script type="text/javascript">
//<![CDATA[
let Invoice = {
    field: function (elem) {
        return $(elem).attr("name");
        //return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    selector: function (field) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop()));
            }
            return result.join(",");
        }
        return "[name='" + field + "']";
        //return "[name='item[" + field + "]']";
    },
    get: function (field) {
        return $(this.selector(field)).val();
    },
    set: function (field, value, bubble) {
        bubble = (typeof bubble !== 'undefined') ? bubble : true;
        var $elem = $(this.selector(field));
        $elem.text(value);
        if (value !== $elem.val()) {
            if (bubble) {
                $elem.val(value).change();
            } else {
                $elem.val(value);
            }
        }
    }
};
//]]>
</script>
<?php

switch ($form_action) {
    case 'do_edit':
        include __DIR__ . '/summary_table.tpl.php';
        include __DIR__ . '/member_table.tpl.php';
        include __DIR__ . '/product_supplier.tpl.php';
        if (count($invoice_porders)) {
            //include __DIR__ . '/invoice_purchase_orders.tpl.php';
        }
        include __DIR__ . '/order_information.tpl.php';
        include __DIR__ . '/shipping_information.tpl.php';
        include __DIR__ . '/invoice_comment.tpl.php';
        include __DIR__ . '/freight_quotation.tpl.php';
        include __DIR__ . '/packing_information.tpl.php';
        include __DIR__ . '/packing_information_new.tpl.php';
        include __DIR__ . '/payment_information.tpl.php';
        include __DIR__ . '/attachments.tpl.php';
        include __DIR__ . '/new_link_table.tpl.php';
        include __DIR__ . '/message_sending.tpl.php';
        include __DIR__ . '/dashboard_history.tpl.php';
        include __DIR__ . '/admin_history.tpl.php';
        include __DIR__ . '/survey.tpl.php';
        include __DIR__ . '/message_history.tpl.php';
        include __DIR__ . '/action_buttons.tpl.php';
        break;

    case 'do_add':
        include __DIR__ . '/member_table.tpl.php';
        include __DIR__ . '/product_supplier.tpl.php';
        include __DIR__ . '/order_information.tpl.php';
        include __DIR__ . '/shipping_information.tpl.php';
        include __DIR__ . '/invoice_comment.tpl.php';
        include __DIR__ . '/freight_quotation.tpl.php';
        include __DIR__ . '/payment_information.tpl.php';
        include __DIR__ . '/message_sending.tpl.php';
        break;

    default:
        break;
}
if ('y' == $_GLOBALS['staff']['developer']) {
    include __DIR__ . '/developer_tools.tpl.php';
}

$invoice_status = $invoice['invoice_status'];
switch ($invoice['invoice_status']) {
    default:
    case 'enquiry':
    case 'inquiry':
    case 'closed':
        $status_list = array(
            'working'    => 'Working On Inquiry',
            'feedback'   => 'Waiting Customer Feedback',
            'sales'      => 'Invoice',
            'nointerest' => 'Closed - Customer Not Interested',
            'nohelp'     => 'Closed - Unable to help Customer',
            'cancelled'  => 'Closed - Cancelled',
        );
        break;

    case 'proforma':
    case 'sales':
    case 'processed':
    case 'cancelled':
        $status_list = array(
            'payment'    => 'Awaiting Payment',
            'approval'   => 'Awaiting Payment Approval',
            'shipping'   => 'Awaiting Shipping',
            'shipped'    => 'Goods Shipped',
            'shipped-loan'    => 'Goods Shipped – Loan',
            'processed'  => 'Processed',
            'nohelp'     => 'Closed - Unable to help Customer',
            'nointerest' => 'Closed - Customer Not Interested',
            'cancelled'  => 'Closed - Cancelled',
            'returned'   => 'Closed - Returned',
        );
        //if ($invoice['invoice_action'] == 'returned') $status_list = array('returned' => 'Closed - Returned');
        break;

    case 'return':
        $status_list = array(
           /*'prepare_factory'  => 'Goods preparing to send to Factory',
            'sent_factory'     => 'Goods sent to Factory',
            'prepare_customer' => 'Goods preparing to send to Customer',
            'sent_customer'    => 'Goods sent to Customer',
            'return_completed' => 'Processed',
            'return_cancelled' => 'Cancelled',*/
            'waiting_customer'  => 'Waiting Customer',
            'Working_on_solution' => 'Working On Solution',
            'return_completed' => 'Processed',
            'return_cancelled' => 'Cancelled',
             'working'          => 'Change to Inquiry',
        );
     break;
    case 'completed':
        $status_list = array(
           /*'prepare_factory'  => 'Goods preparing to send to Factory',
            'sent_factory'     => 'Goods sent to Factory',
            'prepare_customer' => 'Goods preparing to send to Customer',
            'sent_customer'    => 'Goods sent to Customer',
            'return_completed' => 'Processed',
            'return_cancelled' => 'Cancelled',*/
            'waiting_customer'  => 'Waiting Customer',
            'Working_on_solution' => 'Working On Solution',
            'return_completed' => 'Processed',
            'return_cancelled' => 'Cancelled',
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
<!-- <p>Status: <select style="width:auto;" name="invoice_action">
	<?=formatHTMLOptions($status_list, $invoice['invoice_action'])?>
</select></p> -->

            <table style="width: 25%; background: #eeeee0;">
            <tr style="background: #eeeee0;">
                 <td style="text-align:center;"><input type="checkbox" name="is_product_harmonised_code" value="y" <?= $invoice['is_product_harmonised_code'] == 'y' ? 'checked' : '' ?>/>
                 </td>
                 <td ><b>Harmonised Code</b></td>
            </tr>
             <tr style="background: #eeeee0;">
                 <td style="text-align:center;"><input type="checkbox" name="is_product_origin_country" value="y" <?= $invoice['is_product_origin_country'] == 'y' ? 'checked' : '' ?>/>
                 </td>
                 <td ><b>Country Of Origin</b></td>
            </tr>
             <tr style="background: #eeeee0;">
                 <td style="text-align:center;"><input type="checkbox" name="is_product_materials" value="y" <?= $invoice['is_product_materials'] == 'y' ? 'checked' : '' ?>/>
                 </td>
                 <td ><b>Materials</b></td>
            </tr>
             <tr style="background: #eeeee0;">
                 <td style="text-align:center;"><input type="checkbox" name="is_product_usage" value="y" <?= $invoice['is_product_usage'] == 'y' ? 'checked' : '' ?>/>
                 </td>
                 <td ><b>Usage</b></td>
            </tr>
            </tr>
            <tr style="background: #eeeee0;"><td></td><td></td></tr>
            <tr style="background: #eeeee0;"><td></td><td></td></tr>
             <tr style="background: #eeeee0;">
                 <td style="text-align:center;">Address
                 </td>
                 <td ><select style="width:auto;" name="invoice_address">
                        <option value="0"> Select Address</option>
                        <?=formatHTMLOptions(getActiveInvoiceAddressList(), $invoice_addresses)?>
                    </select>
                </td>
            </tr>
            <tr style="background: #eeeee0;"><td></td><td></td></tr>
            <tr style="background: #eeeee0;"><td></td><td></td></tr>
            </table>
            <!-- <p>Address: <select style="width:auto;" name="invoice_address">
                <option value="0"> Select Address</option>
                <?=formatHTMLOptions(getActiveInvoiceAddressList(), $invoice['invoice_address'])?>
            </select></p> -->
<br />
<br />
<p>Status: <select style="width:auto;" name="invoice_action">
	<?=formatHTMLOptions($status_list, $invoice['invoice_action'])?>
</select></p>
<!-- <p>Address: <select style="width:auto;" name="invoice_address">
    <option value="0"> Select Address</option>
    <?=formatHTMLOptions(getActiveInvoiceAddressList(), $invoice['invoice_address'])?>
</select></p> -->
<br />
<br />
<input type="button" value="Submit Changes"<?=('edit' == $action && (empty($invoice_lock) || $invoice_lock['staff_id'] != $_GLOBALS['staff']['id'])) ? ' disabled="disabled"' : ' onclick="if(validateInvoice()) this.form.submit()"'?> />
<input type="button" value="Print" onclick="window.print();" />
<?php
if ('y' == $_GLOBALS['staff']['developer']) {
?>
<input type="button" value="Form Preview" onclick="testPost(this.form);" />
<?php
}
?>
<?=('inquiry' == $invoice_status && 2 == $posted['site_id'] && count($invoice_products) && '' == $invoice['wanted_id']) ? '<input type="button" value="Add Wanted Items" onclick="addWantedItems()" />' : ''?>
<br />
<br />
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
