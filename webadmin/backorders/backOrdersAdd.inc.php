<script>
function openSearchWindow()
{
	var product_name = document.main.product_name.value
	var url = 'backOrdersSearch.php?name=' + product_name;
	window.open(url ,'Search');
}
</script>
<?php
	if($errorMsg != "")
		echo "<font color=\"#FF0000\"><b>The following errors occured</b><br><br>$errorMsg</font><br>";
?>
<br>
<table align="center" width="70%" cellspacing="1" cellpadding="3">
	<tr>
		<td width="25%" class="left">Customer Name :</td>
		<td width="75%" class="right"><input type="text" name="data[customer]" value="<?= $_POST['data']['customer'] ?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">Email Address :</td>
		<td class="right"><input type="text" name="data[email]" value="<?= $_POST['data']['email'] ?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">Phone :</td>
		<td class="right"><input type="text" name="data[phone]" value="<?= $_POST['data']['phone'] ?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
<!--
	<tr>
		<td class="left">Product ID :</td>
		<td class="right"><input type="text" name="data[product_id]" value="<?= $_POST['data']['product_id'] ?>" style="width: 200px"></td>
	</tr>
	<tr>
		<td class="left">&nbsp;</td>
		<td class="right"><b>OR</b></td>
	<tr>
-->
	<tr>
		<td class="left">Product Name :</td>
		<td class="right" nowrap>
			<input type="text" name="product_name" value="" style="width: 300px">
			<input type="button" name="searchButton" value="Search" onclick="openSearchWindow()">
		</td>
	</tr>
	<tr>
		<td class="left">Buy Price :</td>
		<td class="right">
			<input type="text" name="buy_price" value="" style="width: 200px">
		</td>
	</tr>
	<tr>
		<td class="left">Online Sell Price :</td>
		<td class="right">
			<input type="text" name="online_sell_price" value="" style="width: 200px">
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	<tr>
	<tr>
		<td class="left">Qty :</td>
		<td class="right"><input type="text" name="data[qty]" value="<?= $_POST['data']['qty'] ?>" ></td>
	</tr>
	<tr>
		<td class="left">Offer Price :</td>
		<td class="right"><input type="text" name="data[offer_price]" value="<?= $_POST['data']['offer_price'] ?>"></td>
	</tr>
	<tr>
		<td class="left">Currency :</td>
		<td class="right">
			<select name="data[currency]">
				<?=formatHTMLOptions(getCurrencyList(false), 'AUD')?>
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
				$user = get_user();

				while($row = $DB->fetch_array($result))
				{

					echo '<option value="'.$row['id'].'"';
					if($row['user'] == $user){echo ' selected ';}
					echo '>'.niceusername($row['user']).'</option>';
				}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Notes :</td>
		<td class="right"><textarea name="data[notes]" rows="4" cols="30"><?= $_POST['data']['notes'] ?></textarea></td>
	</tr>
  <tr>
     <td class="left" align="left"><b>Comments :</b> <br><font color="red">(comments will be shown <br>on admin section only)</font></td>
	 <td class="right"><textarea name="comment" cols="40" rows="5" style="width:300px" ></textarea></td>
  </tr>
</table>
<br><br>
<div align="center">
	<input type="hidden" name="action" value="do_add">
	<input type="submit" name="add" value="Add">
	<input type="reset" value="Clear">
</div>
<br>