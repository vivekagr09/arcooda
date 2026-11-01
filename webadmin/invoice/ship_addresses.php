<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceGetOptionList

$invoice_id = intval($posted['invoice_id']);
$invoice    = invoiceFromDatabase($invoice_id);
$country_list                 = invoiceGetOptionList('bill_country');
$invoice['bill_address_full'] = trim(preg_replace("/\\n\\n+/", "\n\n", implode("\n", array(
    $invoice['bill_company'],
    (('' != $invoice['bill_company']) ? 'Attn: ' : '') . $invoice['bill_name'],
    $invoice['bill_address'],
    $invoice['bill_city'] . (('' != $invoice['bill_state']) ? (', ' . $invoice['bill_state']) : '') . ' ' . $invoice['bill_postcode'],
    $country_list[$invoice['bill_country']],
    '',
    ('' != $invoice['bill_phone']) ? ('Ph: ' . $invoice['bill_phone']) : '',
    ('' != $invoice['bill_email']) ? ('Email: ' . $invoice['bill_email']) : '',
))));

$country_list                 = invoiceGetOptionList('ship_country');
$invoice['ship_address_full'] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
    $invoice['ship_company'],
    (('' != $invoice['ship_company']) ? 'Attn: ' : '') . $invoice['ship_name'],
    $invoice['ship_address'],
    $invoice['ship_city'] . (('' != $invoice['ship_state']) ? (', ' . $invoice['ship_state']) : '') . ' ' . $invoice['ship_postcode'],
    $country_list[$invoice['ship_country']],
    '',
    ('' != $invoice['ship_phone']) ? ('Ph: ' . $invoice['ship_phone']) : '',
    ('' != $invoice['ship_email']) ? ('Email: ' . $invoice['ship_email']) : '',
))));

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	</head>
	<body><div class="container">

<?php
?>
<h2>Bill To</h2>
<p class="well"><?=nl2br(hecho($invoice['bill_address_full'], 1))?></p>
<h2>Ship To</h2>
<p class="well"><?=nl2br(hecho($invoice['ship_address_full'], 1))?></p>

	</div></body>
</html>
