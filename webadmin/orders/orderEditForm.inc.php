<?php
/**
 * @var int $id
 */
?><?php
global $DB;

$item = $DB->query_first(sprintf(
    "SELECT `oc`.*, `o`.*, `p`.`product_name` FROM `orders` AS `o` INNER JOIN `order_customers` AS `oc` INNER JOIN `products` AS `p` WHERE `o`.`id` = %d AND `o`.`customer_id` = `oc`.`id` AND `p`.`id` = `o`.`product_id`",
    $id
), MYSQLI_ASSOC);

$customers = array();
if ($query = $DB->query(sprintf(
    "SELECT `id`, `name` FROM `order_customers` ORDER BY `name` ASC"
))) {
    while ($row = $DB->fetch_array($query)) {
        $customers[$row['id']] = $row['name'];
    }
    $DB->free_result($query);
}

$countries = getCountryList(false);

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
<table width="70%" align="center" cellspacing="1" cellpadding="4">
	<tr>
		<td width="30%" class="left">Customer :</td>
		<td width="70%" class="right"><select name="order[customer_id]">
			<option value=""></option>
			<?=showOptions($customers, $item['customer_id'])?>
		</select></td>
	</tr>
	<tr>
		<td width="30%" class="left">Product ID :</td>
		<td width="70%" class="right">
			<input type="hidden" name="order[old_id]" value="<?=$item['product_id']?>">
			<input type="text" name="order[pid]" value="<?=$item['product_id']?>">
		</td>
	</tr>
	<tr>
		<td width="30%" class="left">Product Name :</td>
		<td width="70%" class="right"><?=$item['product_name']?></td>
	</tr>
	<tr>
		<td width="30%" class="left">Qty :</td>
		<td width="70%" class="right"><input type="text" name="order[qty]" value="<?=$item['qty']?>"></td>
	</tr>
	<tr>
		<td width="30%" class="left">Sell Price :</td>
		<td width="70%" class="right"><table cellpadding="3"><tr>
			<td><input type="text" name="order[sales_price]" value="<?=$item['sales_price']?>"></td>
			<td><select name="order[sales_currency]">
<?php
showCurrencyList($item['sales_currency']);
?>
			</select></td>
		</tr></table></td>
	</tr>
	<tr>
		<td width="30%" class="left">Buy Price :</td>
		<td width="70%" class="right"><table cellpadding="3"><tr>
			<td><input type="text" name="order[buy_price]" value="<?=$item['buy_price']?>"></td>
			<td><select name="order[buy_currency]">
<?php
showCurrencyList($item['buy_currency']);
?>
			</select></td>
		</tr></table></td>
	</tr>
	<tr>
		<td width="30%" class="left">Status :</td>
		<td width="70%" class="right"><select name="order[status]">
			<option value="open" <?=$item['status'] == "open" ? "selected" : ""?>>Open</option>
			<option value="closed" <?=$item['status'] == "closed" ? "selected" : ""?>>Closed</option>
		</select></td>
	</tr>
	<tr>
		<td width="30%" class="left">Country :</td>
		<td width="70%" class="right"><select name="order[stock_country]">
			<option value=""></option>
			<?=showOptions($countries, $item['stock_country'])?>
		</select></td>
	</tr>
	<tr>
		<td width="30%" class="left">Notes :</td>
		<td width="70%" class="right"><textarea name="order[notes]" cols="40" rows="5"><?=htmlspecialchars($item['notes'])?></textarea></td>
	</tr>
</table>
<br />
<br />
<div style="text-align:center">
	<input type="hidden" name="order[id]" value="<?=$id?>">
	<input type="hidden" name="action" value="do_edit">
	<input type="submit" value="Update">
</div>
