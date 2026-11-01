<?php
include_once __DIR__ . '/common.inc.php';
$supplier_id       = intval($posted['supplier_id']);
$supplier          = supplierFromDatabase($supplier_id);
$country_list    = supplierGetOptionList("country");

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	</head>
	<body><div class="container">

    <?php 
    if (!array_key_exists("english", $posted) || 0 != $posted["english"]) {
    ?>
    <p class="well" style="width:300px;"><b><?= ucwords($supplier ['company'])?></b><br>
        Attn: <?= $supplier ['full_name']?><br>
        <?= $supplier['address_1'] ?>
        <br>
        <?= $supplier['city'] ?>,<?= $supplier['state'] ?> <?= $supplier['post_code'] ?> 
         <br>
        <?= $country_list[$supplier["country"]]?> - <?= $supplier['country'] ?><br>
        <br>
        Ph: <?= $supplier ['phone_number']?><br>
        Email: <?= $supplier ['email_address']?>
    </p>
    <?php
    }

    if (!array_key_exists("chinese", $posted) || 0 != $posted["chinese"]) {
    ?>
     <p class="well" style="width:300px;"><b><?= $supplier ['company_chinese']?></b><br>
        联系人: <?= $supplier ['name_chinese'].' '.$supplier ['surname_chinese']?><br>
        <?= $supplier['address_1_chinese'] ?>
        <br>
        <?= $supplier['city_chinese'] ?>,<?= $supplier['state_chinese'] ?> <?= $supplier['post_code_chinese'] ?> 
         <br>
        <?= $country_list[$supplier["country"]]?> - <?= $supplier['country'] ?><br>
        <br>
        电话: <?= $supplier ['phone_number']?><br>
        电子邮件: <?= $supplier ['email_address']?>
    </p>
    <?php
    }
    ?>

	</div></body>
</html>
