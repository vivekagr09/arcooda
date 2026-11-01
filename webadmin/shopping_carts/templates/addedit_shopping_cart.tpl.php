<?php
/**
 * @var int     $shopping_cart_id
 * @var mixed[] $shopping_cart
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('View Shopping Cart')?>

<div id="sectionDetails">
<?php
// any global variables to be defined here..
$status = array('open'=>'Open', 'ordered'=>'Order Completed', 'saved'=>'Saved', 'closed'=>'Cancelled/Deleted');
$stage = array(1=>'Open', 2=>'Delivery Address', 3=>'Shipping Details', 4=>'Payment Method', 5=>'Review Order', 6=>'Confirmation', 7=>'Order Completed');
$yesno=array('n'=>'No','y'=>'Yes');
$products=shopping_cartProductsFromDatabase($shopping_cart_id);
$checkout=$stage[$shopping_cart['stage']];
if ($shopping_cart['status']=='closed') $checkout='Order Cancelled';
?>
<table class="wide" align="center" style="width:95%">
	<tr><th>Product Name</th><th>Sub-Total</th><th>Qty</th><th>Total</th><th>Currency</th></tr>
<?php
$alt=false;
foreach ($products as $array)
{
	$class="";
	if ($alt=!$alt)		$class=' class="alt"';
	$product_id=$array['product_id'];
	$product=productFromDatabase($product_id);
?>
	<tr<?=$class?> style="text-align:center">
		<td style="text-align:left;padding-left:5px;">
			ID#: <?=$product_id?> <a href="<?=$product['product_url']?>" target="_blank"><?=htmlspecialchars($product['product_name'])?></a>
		</td>
		<td><?=$array['product_offer']?></td>
		<td><?=$array['product_qty']?></td>
		<td><?=sprintf('%.2f',$array['product_offer']*$array['product_qty'])?></td>
		<td><?=$array['offer_currency']?></td>
	</tr>
<?php
}
?>
</table>
<br /><br />
<table class="wide" align="center" style="width:95%">
	<tr>
		<td class="left_title">Cart Status :</td><td><?=$status[$shopping_cart['status']]?></td>
		<td class="left_title">Checkout Progress :</td><td><?=$checkout?></td>
	</tr>
	<tr>
		<td class="left_title">Payment Made :</td><td><?=$yesno[$shopping_cart['payment_made']]. (($shopping_cart['payment_time']) ? ' - '.date('d/m/Y', $shopping_cart['payment_time']) : '')?></td>
		<td class="left_title">Payment Rejected :</td><td><?=$yesno[$shopping_cart['payment_rejected']]. (($shopping_cart['rejected_time']) ? ' - '.date('d/m/Y', $shopping_cart['rejected_time']) : '')?></td>
	</tr>
</table></div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
