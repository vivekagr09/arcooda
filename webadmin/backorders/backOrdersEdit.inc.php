<script>
function openSearchWindow()
{
	var product_name = document.main.product_name.value
	var url = 'backOrdersSearch.php?name=' + product_name;
	window.open(url ,'Search');
}
</script>

<?php
if ("" != $errorMsg) {
    echo "<font color=\"#FF0000\"><b>The following errors occured</b><br><br>$errorMsg</font><br>";
}

$order = $DB->query_first(sprintf(
	"SELECT * FROM `highwaygroup`.`back_orders` WHERE `id` = %d LIMIT 1",
	$_GET['id']
), MYSQLI_ASSOC);
?>

<br>
<?php
if (true == $print) {
    echo '<table width="90%" border="1" cellspacing="1" cellpadding="3" align="center">';
    //echo '<tr class="titles">';
} else {
    echo '<table align="center" width="70%" cellspacing="1" cellpadding="3">';
}
?>
	<tr>
		<td width="25%" class="left">Customer Name :</td>
		<td width="75%" class="right"><input type="text" name="data[customer]" value="<?=$order['customer']?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">Email Address :</td>
		<td class="right"><input type="text" name="data[email]" value="<?=$order['email']?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">Phone :</td>
		<td class="right"><input type="text" name="data[phone]" value="<?=$order['phone']?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<!--
	<tr>
		<td class="left">Product ID :</td>
		<td class="right"><input type="text" name="data[product_id]" value="<?=$order['product_id']?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">&nbsp;</td>
		<td class="right"><b>OR</b></td>
	<tr>
-->
	<tr>
		<td class="left">Product Name :</td>
		<td class="right" nowrap>
			<input type="text" name="product_name" value="<?=$order['product_name']?>" style="width: 300px">
			<input type="button" name="searchButton" value="Search" onclick="openSearchWindow()">
		</td>
	</tr>
	<tr>
		<td class="left">Buy Price :</td>
		<td class="right">
			<input type="text" name="buy_price" value="<?=$order['buy_price']?>" style="width: 200px">
		</td>
	</tr>
	<tr>
		<td class="left">Online Sell Price :</td>
		<td class="right">
			<input type="text" name="online_sell_price" value="<?=$order['online_sell_price']?>" style="width: 200px">
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	<tr>
	<tr>
		<td class="left">Qty :</td>
		<td class="right"><input type="text" name="data[qty]" value="<?=$order['qty']?>" ></td>
	</tr>
	<tr>
		<td class="left">Offer Price :</td>
		<td class="right"><input type="text" name="data[offer_price]" value="<?=$order['offer_price']?>"></td>
	</tr>
	<tr>
		<td class="left">Currency :</td>
		<td class="right">
			<select name="data[currency]">
				<?=formatHTMLOptions(getCurrencyList(false), $order['currency'])?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Handler :</td>
		<td class="right">
			<select name="data[handler]">
			<option value="0" selected>Unknown</option>
<?php
$result = $DB->query("SELECT `user`, `id` FROM `highwaygroup`.`users`");
$user   = get_user();

while ($row = $DB->fetch_array($result)) {

    echo '<option value="' . $row['id'] . '"';
    if ($row['id'] == $order['handler']) {echo ' selected ';}
    echo '>' . niceusername($row['user']) . '</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Notes :</td>
		<td class="right"><textarea name="data[notes]" rows="4" cols="30"><?=$order['notes']?></textarea></td>
	</tr>
	<tr>
		<td class="left">Status :</td>
		<td class="right">
			<select name="data[status]">
				<option value="open" <?=$order['status'] == "open" ? "selected" : ""?>>Open</option>
				<option value="closed" <?=$order['status'] == "closed" ? "selected" : ""?>>Closed</option>
			</select>
		</td>
	</tr>
<?php
//if($print != true){
?>
  <tr>
     <td class="left" align="left"><b>Comments :</b> <br><font color="red">(comments will be shown <br>on admin section only)</font></td>
	 <td class="right"><textarea name="comment" cols="40" rows="5" style="width:300px" ></textarea></td>
  </tr>
<?php
//}
?>
</table>
<?php
if (true != $print) {
?>
<br><br>
<div align="center">
	<input type="hidden" name="data[id]" value="<?=$order['id']?>">
	<input type="hidden" name="action" value="do_edit">
	<input type="submit" name="add" value="Update">
	<input type="reset" value="Reset">
</div>
<?php
}
?>
</form>
<?php
include 'backOrdersEmail.inc.php';
include 'backOrdersComment.inc.php';
