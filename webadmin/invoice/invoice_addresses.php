<?php
include_once __DIR__ . '/common.inc.php';

$invoice = invoiceFromPost($posted);
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
if (!array_key_exists("bill", $posted) || 0 != $posted["bill"]) {
?>
<h2>Bill To</h2>
<p class="well"><?=nl2br(htmlspecialchars($invoice["bill_address_full"]))?></p>
<?php
}

if (!array_key_exists("ship_addr_id", $posted) || 0 != $posted["ship_addr_id"]) {
?>
<h2>Ship To</h2>
<p class="well"><?=nl2br(htmlspecialchars($invoice["ship_address_full"]))?></p>
<?php
}

if (!array_key_exists("notify_addr_id", $posted) || 0 != $posted["notify_addr_id"]) {
?>
<h2>Notify</h2>
<p class="well"><?=nl2br(htmlspecialchars($invoice["notify_address_full"]))?></p>
<?php
}
?>

	</div></body>
</html>
