<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceGetOptionList

$member_id                = intval($posted['member_id']);
$member                   = memberFromDatabase($member_id);
$member_notifyparties     = memberNotifyPartiesFromDatabase($member_id);
$member_shippingaddresses = memberShippingAddressesFromDatabase($member_id);
$country_list             = memberGetOptionList('country');

/**
 * Simulate a default invoice
 */
$invoice = array();

/**
 * Copy the member details
 */
foreach (array(
    'bill_address'     => 'address',
    'bill_city'        => 'city',
    'bill_company'     => 'company_name',
    'bill_country'     => 'country',
    'bill_email'       => 'email_address',
    'bill_phone'       => 'phone_no',
    'bill_postcode'    => 'post_code',
    'bill_state'       => 'state',
    'invoice_currency' => 'currency',
    'member_id'        => 'id',
    'notify_address'   => 'address',
    'notify_city'      => 'city',
    'notify_company'   => 'company_name',
    'notify_country'   => 'country',
    'notify_email'     => 'email_address',
    'notify_phone'     => 'phone_no',
    'notify_postcode'  => 'post_code',
    'notify_state'     => 'state',
    'ship_address'     => 'address',
    'ship_city'        => 'city',
    'ship_company'     => 'company_name',
    'ship_country'     => 'country',
    'ship_email'       => 'email_address',
    'ship_phone'       => 'phone_no',
    'ship_postcode'    => 'post_code',
    'ship_state'       => 'state',
    'timezone'         => 'timezone',
    'vat_number'       => 'vat_number',
) as $k => $v) {
    if (array_key_exists($v, $member)) {
        $invoice[$k] = $member[$v];
    }
}

$invoice['bill_name']   = trim($member['first_name'] . ' ' . $member['last_name']);
$invoice['notify_name'] = trim($member['first_name'] . ' ' . $member['last_name']);
$invoice['ship_name']   = trim($member['first_name'] . ' ' . $member['last_name']);

/**
 * Set the default shipping address
 */
foreach ($member_shippingaddresses as $member_shippingaddress) {
    foreach (array(
        'ship_address'  => 'address',
        'ship_city'     => 'city',
        'ship_company'  => 'company_name',
        'ship_country'  => 'country',
        'ship_email'    => 'email',
        'ship_name'     => 'contact',
        'ship_needs'    => 'instructions',
        'ship_phone'    => 'phone_no',
        'ship_postcode' => 'post_code',
        'ship_state'    => 'state',
    ) as $k => $v) {
        $invoice[$k] = $member_shippingaddress[$v];
    }

    if ('y' == $member_shippingaddress['default']) {
        break;
    }

}

/**
 * Set the default notify address
 */
foreach ($member_notifyparties as $member_notifyparty) {
    foreach (array(
        'notify_address'  => 'address',
        'notify_city'     => 'city',
        'notify_company'  => 'company_name',
        'notify_country'  => 'country',
        'notify_email'    => 'email', 
        'notify_name'     => 'contact', 
        'notify_needs'    => 'instructions',
        'notify_phone'    => 'phone_no',
        'notify_postcode' => 'post_code',
        'notify_state'    => 'state',
    ) as $k => $v) {
        $invoice[$k] = $member_notifyparty[$v];
    }
    if ('y' == $member_notifyparty['default']) {
        break;
    }
}

/**
 * Override shipping address
 */
if (array_key_exists('ship_addr_id', $posted)) {
    foreach ($member_shippingaddresses as $member_shippingaddress) {
        if ($posted['ship_addr_id'] == $member_shippingaddress['id']) {
            foreach (array(
                'ship_address'  => 'address',
                'ship_city'     => 'city',
                'ship_company'  => 'company_name',
                'ship_country'  => 'country',
                'ship_email'    => 'email',
                'ship_name'     => 'contact',
                'ship_phone'    => 'phone_no',
                'ship_postcode' => 'post_code',
                'ship_state'    => 'state',
            ) as $k => $v) {
                $invoice[$k] = $member_shippingaddress[$v];
            }
        }
    }
}

/**
 * Override notify address
 */
if (array_key_exists('notify_addr_id', $posted)) {
    foreach ($member_notifyparties as $member_notifyparty) {
        if ($posted['notify_addr_id'] == $member_notifyparty['id']) {
            foreach (array(
                'notify_address'  => 'address',
                'notify_city'     => 'city',
                'notify_company'  => 'company_name',
                'notify_country'  => 'country',
                'notify_email'    => 'email',
                'notify_name'     => 'contact',
                'notify_phone'    => 'phone_no',
                'notify_postcode' => 'post_code',
                'notify_state'    => 'state',
            ) as $k => $v) {
                $invoice[$k] = $member_notifyparty[$v];
            }
        }
    }
}

/**
 * Build the addresses
 */
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

$country_list                   = invoiceGetOptionList('notify_country');
$invoice['notify_address_full'] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
    $invoice['notify_company'],
    (('' != $invoice['notify_company']) ? 'Attn: ' : '') . $invoice['notify_name'],
    $invoice['notify_address'],
    $invoice['notify_city'] . (('' != $invoice['notify_state']) ? (', ' . $invoice['notify_state']) : '') . ' ' . $invoice['notify_postcode'],
    $country_list[$invoice['notify_country']],
    '',
    ('' != $invoice['notify_phone']) ? ('Ph: ' . $invoice['notify_phone']) : '',
    ('' != $invoice['notify_email']) ? ('Email: ' . $invoice['notify_email']) : '',
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
if (!array_key_exists('bill', $posted) || 0 != $posted['bill']) {
?>
<h2>Bill To</h2>
<p class="well"><?=nl2br(hecho($invoice['bill_address_full'], 1))?></p>
<?php
}

if (!array_key_exists('ship_addr_id', $posted) || 0 != $posted['ship_addr_id']) {
?>
<h2>Ship To</h2>
<p class="well"><?=nl2br(hecho($invoice['ship_address_full'], 1))?></p>
<?php
}

if (!array_key_exists('notify_addr_id', $posted) || 0 != $posted['notify_addr_id']) {
?>
<h2>Notify</h2>
<p class="well"><?=nl2br(hecho($invoice['notify_address_full'], 1))?></p>
<?php
}
?>

	</div></body>
</html>
