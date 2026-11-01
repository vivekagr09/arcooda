<?php
$type=$_GET['type'];
$site_id=$_GET['site_id'];
$countries = supplierCountries($site_id, $type);
$category = supplierSites($type);
//print_array($category);
$show = $_REQUEST['show'];
$sub = $_REQUEST['sub'];
?>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr><td style="width:25%;background-color:#F0F4F9"><img name="conImEx" src="<?=($show == 'country') ? 'triUp.gif' : 'triDown.gif'?>" width="16" height="16" border="0" alt="Expand/Collapse All"><a href="#" onclick="toggleSection('con');togglePic('con');return false"><b>Display By Country</b></a></td></tr>
</table>
<div id="con" <?= 'style="display:'.(($show=='country') ? '' : 'none').'"' ?>>
<br />
<?php
if(is_array($countries) && count($countries))
{
?>
	<table style="width:95%;border: 0px" cellspacing="0" cellpadding="0" align="center">
		<tr>
<?php
	$count = 0;
	foreach($countries as $country)
	{
		if($country['name']=='') $country['name']='None';
		if($count%4==0 && $count>0) echo '</tr><tr>';
		echo '<td style="width:25%;background-color:'.(($show=='country' && $country['code']==$sub)?'#FFCC00':'#F0F4F9').'">';
		echo '<a href="?type='.$type.'&site_id='.$site_id.'action='.$action.'&show=country&sub='.$country['code'].'">'. $country['name'].'</a> ('.$country['total'].')</td>';
		$count++;
	}
?>
		</tr>
	</table>
<?php
}
else echo 'No Results Found.';
?>
<br />
</div>
<?php
if($_GET['site_id']==0)
{
?>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr><td style="width:25%;background-color:#F0F4F9"><img name="siteImEx" src="<?=($show == 'site') ? 'triUp.gif' : 'triDown.gif'?>" width="16" height="16" border="0" alt="Expand/Collapse All"><a href="#" onclick="toggleSection('site');togglePic('site');return false"><b>Display By Site</b></a></td></tr>
</table>
<div id="site" <?='style="display:'.(($show=='site') ? '' : 'none').'"'?>>
<br />
<?php
	if(is_array($category) && count($category))
	{
		$count = 0;
?>
	<table style="width:95%;border: 0px" cellspacing="0" cellpadding="0" align="center">
		<tr>
<?php
		foreach($category as $site)
		{
			if($count%4==0 && $count>0) echo '</tr><tr>';
			echo '<td style="width:25%;background-color:'.(($show=='site' && $site['code']==$sub)?'#FFCC00':'#F0F4F9').'">';
			echo '<a href="?type='.$type.'&action='.$action.'&show=site&sub='.$site['code'].'">'.$site['name'].'</a> ('.$site['total'].')';
			echo '</td>';
			$count++;
		}
?>
		</tr>
	</table>
<?php
	}
?>
<br />
</div>
<?php
}
?>