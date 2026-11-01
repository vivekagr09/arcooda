<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']='list';
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url);
$sort_url.='&order_by=';
?>
<!-- packing_container_list.tpl.php -->

<input type="hidden" name="container_list" value="b64_gz_ser:<?= base64_encode(gzcompress(serialize($container_list))) ?>" />

<table class="wide" align="center">
	<tr>
		<th style="width:100%;">Crate Name<?= ($order_by=='name')?$ord_img:'' ?></th>
		<th>Weight<br />(kg)<?= ($order_by=='weight')?$ord_img:'' ?></th>
		<th>Space<br />(cm&sup3;)<?= ($order_by=='cubic')?$ord_img:'' ?></th>
		<th>Pieces<?= ($order_by=='qty')?$ord_img:'' ?></th>
		<th>Sub-Weight<br />(kg)<?= ($order_by=='unit_weight')?$ord_img:'' ?></th>
		<th>Sub-Space<br />(cm&sup3;)<?= ($order_by=='unit_cubic')?$ord_img:'' ?></th>
	</tr>
<?php
foreach($container_list as $container_id=>$container)
{
	$item_list=array();
	foreach($container->crate_list as $crate)
	{
		$item=array(
			'name'=>$crate->getName(),
			'weight'=>$crate->getWeight(),
			'size'=>$crate->getVolume(),
			'qty'=>1,
			'total_weight'=>$crate->getWeight(),
			'total_size'=>$crate->getVolume()
		);
		foreach($item_list as $key=>$test)
			if($test['name']==$item['name'] && $test['weight']==$item['weight'] && $test['size']==$item['size'])
			{
				$item['qty']+=$test['qty'];
				$item['total_weight']+=$test['total_weight'];
				$item['total_size']+=$test['total_size'];
				unset($item_list[$key]);
			}
		$item_list[]=$item;
	}
	$sort_list=array();
	if($order_by!='' && isset($crate[$order_by]))
		foreach($item_list as $key=>$item)
			$sort_list[$key]=$item[$order_by];
	else
		foreach($item_list as $key=>$item)
			$sort_list[$key]=$item['name'];
	array_multisort($item_list,$sort_list);
?>
	<tr>
		<td class="alt" colspan="6"><?= $container_id+1 ?> - <?= htmlspecialchars($container->getName()) ?>:</th>
	</tr>
<?php
	foreach($item_list as $item)
	{
?>
	<tr>
		<td><?= htmlspecialchars($item['name']) ?></td>
		<td style="text-align:right;"><?= sprintf('%.2f',$item['weight']) ?></td>
		<td style="text-align:right;"><?= sprintf('%.2f',$item['size']) ?></td>
		<td style="text-align:center;"><?= intval($item['qty']) ?></td>
		<td style="text-align:right;"><?= sprintf('%.2f',$item['total_weight']) ?></td>
		<td style="text-align:right;"><?= sprintf('%.2f',$item['total_size']) ?></td>
	</tr>
<?php
	}
}
?>
</table>

<br /><br />

<!-- /packing_container_list.tpl.php -->
