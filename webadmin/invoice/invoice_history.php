<?php
include_once __DIR__ . '/common.inc.php';

$invoice_id   = intval($posted['invoice_id']);
$invoice      = invoiceFromDatabase($invoice_id);
$invoice_type = array_search($invoice['invoice_status'], array(
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
if ($invoice_type < 3) {
    $display_inquiry = true;
}

include_once __DIR__ . '/header.inc.php';

$action     = 'history';
$invoice_id = intval($posted['invoice_id']);

$search_types = explode('|', $posted['message_types']);
$search_for   = trim($posted['search_for']);

$message_types = array();
if ($query = $DB->query(sprintf(
    "SELECT `message_type_id`, `message_description` FROM `highwaygroup`.`invoice_message_types` WHERE (`message_public` & 1) = 1 ORDER BY `message_description` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $message_types[$row['message_type_id']] = $row['message_description'];
    }
    $DB->free_result($query);
}

$search_types = array_intersect($search_types, array_keys($message_types));
if (count($search_types) == 0) {
    $search_types = array_keys($message_types);
}

$criteria = array('invoice_id' => $invoice_id, 'message_type_id' => $search_types);
if ('' != $search_for) {
    $criteria['any_text'] = $search_for;
}

$invoice_history = invoiceHistorySearch($criteria);

$invoice['history_count'] = count($invoice_history);

?>
<h2>History for Invoice <?=invoiceFormatNumber($invoice_id, $invoice['revision_id'])?> - <?=date(_('d/m/Y'))?></h2>

<form action="" method="post"><table style="border:1px solid #f90;width:95%;margin:auto">
	<tr><td colspan="2" style="text-align:center;background-color:#ffc"><h2 style="color:#f90">Search Invoice History</h2></td></tr>
	<tr>
		<td style="text-align:right;background-color:#ffc;width:40%">Message Type:</td>
		<td style="text-align:left;background-color:#ffc;width:60%"><select name="message_types" style="width:256px">
			<option value="">All</option>
			<?=formatHTMLOptions($message_types, $posted['message_types'])?>
		</select></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#ffc;">Search For:</td>
		<td style="text-align:left;background-color:#ffc;"><input type="text" style="width:256px;" name="search_for" value="<?=htmlspecialchars($search_for)?>" /></td>
	</tr>
	<tr><td colspan="2" style="text-align:center;background-color:#ffc;"><input type="submit" value="Search" /></td></tr>
</table></form>

<?php

include __DIR__ . '/templates/message_history.tpl.php';

include_once __DIR__ . '/footer.inc.php';
