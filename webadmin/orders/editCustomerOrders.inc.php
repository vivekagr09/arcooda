<?php
/**
 * @var int $id
 */
?><?php

global $_GLOBALS;
global $DB;

?>
<br />
<?php

if ("" != $GLOBALS['errorMsg']) {
?>
<div><span color="#FF0000">
	<b>The following errors occured</b><br />
	<br />
	<?=$GLOBALS['errorMsg']?>
</span></div>
<?php
}
?>
<br />
<br />
<?php

$country_list = getCountryList(false);

$orders = array();
if ($query = $DB->query(sprintf(
    "SELECT `o`.*, `p`.`product_name` FROM `orders` AS `o`, `products` AS `p` WHERE `o`.`customer_id` = %d AND `o`.`product_id` = `p`.`id`",
    $id
))) {
    while ($row = $DB->fetch_assoc($query)) {
		$row['country'] = $country_list[$row['stock_country']] ?? $row['stock_country'];
        $orders[]       = $row;
    }
    $DB->free_result($query);
}

if (count($orders) > 0) {
?>
	<table width="90%" cellspacing="1" cellpadding="4" style="margin:auto">
		<tr class="tableHeader">
			<td>Product ID</td>
			<td>Product Name</td>
			<td>Qty</td>
			<td>Sell Price</td>
			<td>Buy Price</td>
			<td>Country</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
<?php
    $classTgl = false;
    foreach ($orders as $order) {
        $classTgl = !$classTgl;
        $class    = $classTgl ? "tableRow" : "tableRowAlt";
?>
	<tr class="<?=$class?>" style="text-align:center">
		<td><?=$order['product_id']?></td>
		<td><?=$order['product_name']?></td>
		<td><?=$order['qty']?></td>
		<td><?=printf("%01.2f", $order['sales_price']) . " " . $order['sales_currency']?></td>
		<td><?=printf("%01.2f", $order['buy_price']) . " " . $order['buy_currency']?></td>
		<td><?=$order['status']?></td>
		<td><?=$order['country']?></td>
		<td><a href="orderAdmin.php?action=edit&id=<?=$order['id']?>">Edit</a></td>
	</tr>
<?php
    }
?>
	</table>
<?php
} else {
    echo "<div align=\"center\">This customer has no orders.</div>";
}

$countries = getCountryList(false);

$customer = $DB->query_first(sprintf(
    "SELECT * FROM `order_customers` WHERE `id` = %d",
    $id
), MYSQLI_ASSOC);

?>
<br />
<br />
<table width="70%" cellspacing="1" cellpadding="4" style="margin:auto">
	<tr>
		<td width="30%" class="left">Customer Name :</td>
		<td width="70%" class="right"><input type="text" name="customer[name]" value="<?=$customer['name']?>" style="width: 250px" /></td>
	</tr>
	<tr>
		<td class="left">Customer Country :</td>
		<td class="right"><select name="customer[country]">
			<option value=""></option>
			<?=showOptions($countries, $customer['country'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Customer Email :</td>
		<td class="right"><input type="text" name="customer[email]" value="<?=$customer['email']?>" style="width: 250px" /></td>
	</tr>
	<tr>
		<td class="left">Customer Phone :</td>
		<td class="right"><input type="text" name="customer[phone]" value="<?=$customer['phone']?>" style="width: 250px" /></td>
	</tr>
</table>
<br />
<br />
<div style="text-align:center">
	<input type="hidden" name="custID" value="<?=$id?>" />
	<input type="hidden" name="action" value="edit_cust" />
	<input type="submit" value="Update" />
</div>
