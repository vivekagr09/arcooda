<!-- login.tpl.php -->

<?php
if(is_array($weeklyspecial_login_history))
{
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialLogin\');">Login History</a>','<a href="login_track.php?id='.$id.'">View All</a>') ?>

<div id="sectionWeeklySpecialLogin">
	<table class="wide">
		<tr>
			<th style="width:25%">Date</th>
			<th style="width:35%">IP Address</th>
			<th style="width:10%">Country</th>
			<th style="width:20%">Site</th>
			<th style="width:10%">Browser</th>
		</tr>
<?php
	$count=0;
	foreach($weeklyspecial_login_history as $track)
	{
		$count++;
		list($date,$time) = explode(' ', $track['login_date']);
		$c_array=getCountryInfo($track['login_ip']);
		$country=$c_array['country'];
		if($c_array['a2']=='') $c_array['a2']='zz';
		if($country!='Unknown') $country='<img src="/webadmin/images/flags/'.strtolower($c_array['a2']).'.gif" border="0" alt="'.$country.'" title="'.$country.'" />';
		$brow=$track['browser'];
		$agent=$track['user_agent'];
?>
		<tr style="text-align:center" <?=($count%2==1)?'class="alt"':''?>>
			<td><?=date("jS M, Y",strtotime($date))?></td>
			<td><a href="https://www.ip2location.com/<?= $track['login_ip'] ?>" target="_blank"><?= $track['login_ip'] ?></a></td>
			<td><?=$country?></td>
			<td><?=$sites[$track['site_id']]?></td>
			<td><?= ($brow!='')?('<img src="/webadmin/images/browsers/'.strtolower($brow).'.svg" alt="'.$brow.'" title="'.$agent.'" width="16" height="16" />'):'' ?></td>
		</tr>
<?php
	}
?>
	</table>
	<br /><br />
</div>

<?php
}
?>

<!-- /login.tpl.php -->
