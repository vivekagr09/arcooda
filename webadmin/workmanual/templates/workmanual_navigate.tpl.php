<?php
$chunked_list=workmanualChunk($workmanual_list,array($list_by1,$list_by2));

switch($list_by1)
{
case 'status':
	$key_list1=$status_list;
	break;
case 'assigned':
case 'reporter':
	$key_list1=$all_staff_list;
	break;
case 'website':
	$key_list1=$website_list;
	break;
}

switch($list_by2)
{
case 'status':
	$key_list2=$status_list;
	break;
case 'assigned':
case 'reporter':
	$key_list2=$all_staff_list;
	break;
case 'website':
	$key_list2=$website_list;
	break;
}

$keys1=array_keys($chunked_list);
$keys2=array();
foreach($chunked_list as $sub_list)
	$keys2=array_merge($keys2,array_keys($sub_list));
$keys2=array_unique($keys2);

$keys1=array_keys($key_list1);
foreach($keys1 as $key1)
	if(!array_key_exists($key1,$chunked_list))
		unset($keys1[array_search($key1,$keys1)]);
foreach(array_keys($chunked_list) as $key1)
	if(!in_array($key1,$keys1))
		$keys1[]=$key1;
$keys1=array_values($keys1);

$temp=$keys2;
$keys2=array_keys($key_list2);
foreach($keys2 as $key2)
	if(!in_array($key2,$temp))
		unset($keys2[array_search($key2,$keys2)]);
foreach($temp as $key2)
	if(!in_array($key2,$keys2))
		$keys2[]=$key2;
$keys2=array_values($keys2);

?>
<table align="center" style="width:auto;">
	<tr>
		<th style="height:32px;width:32px;"></th>
<?php
foreach($keys2 as $key2)
{
	$total_url=$get_vars;
	$total_url['action']='list';
	$total_url[$list_by2]=$key2;
	$total_url='index.php?'.http_build_query($total_url);
?>
		<th style="width:32px;"><a href="<?= $total_url ?>"><?= htmlspecialchars($key_list2[$key2]) ?></a></th>
<?php
}
?>
		<th style="width:32px;">TOTAL</th>
	</tr>
<?php
$alt=false;
foreach($keys1 as $key1)
{
	$sub_list=$chunked_list[$key1];
	$total_url=$get_vars;
	$total_url['action']='list';
	$total_url[$list_by1]=$key1;
	$total_url='index.php?'.http_build_query($total_url);
?>
	<tr<?= ($alt=!$alt)?'':' class="alt"' ?>>
		<th style="height:32px;"><a href="<?= $total_url ?>"><?= htmlspecialchars($key_list1[$key1]) ?></a></th>
<?php
	$sub_total=0;
	foreach($keys2 as $key2)
	{
		$total_url=$get_vars;
		$total_url['action']='list';
		$total_url[$list_by1]=$key1;
		$total_url[$list_by2]=$key2;
		$total_url='index.php?'.http_build_query($total_url);
		$sub_total+=count($sub_list[$key2]);
?>
		<td style="text-align:center;"><a href="<?= $total_url ?>"><?= count($sub_list[$key2]) ?></a></td>
<?php
	}
	$total_url=$get_vars;
	$total_url['action']='list';
	$total_url[$list_by1]=$key1;
	$total_url='index.php?'.http_build_query($total_url);
?>
		<td style="text-align:center;"><a href="<?= $total_url ?>"><?= $sub_total ?></a></th>
	</tr>
<?php
}
?>
	<tr<?= ($alt=!$alt)?'':' class="alt"' ?>>
		<th style="height:32px;">TOTAL</th>
<?php
foreach($keys2 as $key2)
{
	$total_url=$get_vars;
	$total_url['action']='list';
	$total_url[$list_by2]=$key2;
	$total_url='index.php?'.http_build_query($total_url);
	$sub_total=0;
	foreach($chunked_list as $key1=>$sub_list)
		$sub_total+=count($sub_list[$key2]);
?>
		<td style="text-align:center;"><a href="<?= $total_url ?>"><?= $sub_total ?></a></th>
<?php
}
$total_url=$get_vars;
$total_url['action']='list';
$total_url='index.php?'.http_build_query($total_url);
?>
		<td style="text-align:center;"><a href="<?= $total_url ?>"><?= count($workmanual_list) ?></a></th>
	</tr>
</table>

<br />
<br />
