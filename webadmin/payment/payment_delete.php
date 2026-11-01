<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$payment_id = intval($posted['payment_id']);
$portal     = $posted['portal'];
$payment    = paymentFromDatabase($payment_id, $portal);

$dodelete_url           = $get_vars;
$dodelete_url['action'] = 'do_delete';
$dodelete_url           = '.?' . http_build_query($dodelete_url);

$action = 'delete';
?>
<form action="<?=$dodelete_url?>" method="post">
	<h1 style="text-align:center;">Delete this Payment?</h1>
	<?=print_table($payment, 1)?>
	<br />
	<br />
	<input type="button" value="Delete" onclick="this.form.submit()" />
	<input type="button" value="Cancel" onclick="document.location='.'" />
</form>
<?php
include __DIR__ . '/templates/view_payment.tpl.php';

include_once __DIR__ . '/footer.inc.php';
