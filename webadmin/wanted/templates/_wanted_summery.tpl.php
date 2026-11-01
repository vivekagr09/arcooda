<?php
moduleLoad('member'); //memberGetValue
moduleLoad('product');

$active_list   = wantedGetOptionList('active');
$approved_list = wantedGetOptionList('active');

$member_id   = intval($wanted['member_id']);
$member_name = 'N/A';
if ($member_id && function_exists('memberGetValue')) {
	$member_name = memberGetValue($member_id, 'full_name') ?? $member_id;
}

$product_id   = intval(($wanted['online_item_id']) ? $wanted['online_item_id'] : $wanted['item_id']);
$product_name = 'N/A';
if ($product_id && function_exists('productGetValue')) {
	$product_name = productGetValue($product_id, 'product_name') ?? $product_id;
}
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSummary\');return false">Wanted Summary</a>')?>

<div id="sectionSummary">
	<table class="table_fields">
	    <tr>
	        <th>Product ID :</th>
	        <td style="width:25%"><a href="/webadmin/products_new/edit.php?id=<?=$product_id?>" target="_blank"><?=htmlspecialchars($product_name)?></a></td>
	        <th>Customer :</th>
	        <td style="width:25%"><a href="/webadmin/member/?action=edit&amp;member_id=<?=$member_id?>" target="_blank"><?=htmlspecialchars($member_name)?></a></td>
	    </tr>
	     <tr>
	        <th>Wanted Name :</th>
	        <td style="width:25%">
				<a href="/webadmin/products_new/edit.php?id=<?=$product_id?>" target="_blank">
					<?=htmlspecialchars(($wanted['online_item']) ? $wanted['online_item'] : $wanted['item'])?>
				</a>
	        </td>
	        <th>Time Online :</th>
	        <td style="width:25%">N/A</td>
	    </tr>
		<tr>
	        <th>Target Price :</th>
	        <td style="width:25%"><?=$wanted['target_price']?> <?=$wanted['target_currency']?></td>
	        <th>Approved:</th>
	        <td style="width:25%"><?=htmlspecialchars($active_list[$wanted['approved']])?></td>
	    </tr>
	     <tr>
	        <th>Offers Received :</th>
	        <td style="width:25%"><?=count($wanted_offers)?></td>
	        <th>Active Online:</th>
	        <td style="width:25%;"><?=htmlspecialchars($approved_list[$wanted['active']])?></td>
	    </tr>
	</table>
</div>
