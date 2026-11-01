<?php
$sort_url=$get_vars;
$sort_url['action']='list';
unset($sort_url['order_by']);
unset($sort_url['order']);
unset($sort_url['page_id']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url).'&id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['id']);
$view_url='index.php?'.http_build_query($view_url).'&id=';

$toggle_url=$get_vars;
$toggle_url['action']='do_toggle';
unset($toggle_url['id']);
$toggle_url='index.php?'.http_build_query($toggle_url).'&id=';
?>
<table class="wide spacetable" align="center">
	<tr>
		<th>Rank</th>
		<th><a href="<?= $sort_url ?>priority&order=<?= ($order_by=='priority' && $order=='asc')?'desc':'asc' ?>">Priority</a></th>
		<th><a href="<?= $sort_url ?>title&order=<?= ($order_by=='title' && $order=='asc')?'desc':'asc' ?>">Title</a></th>
		<th><a href="<?= $sort_url ?>website&order=<?= ($order_by=='website' && $order=='asc')?'desc':'asc' ?>">Section</a></th>
		<th><a href="<?= $sort_url ?>reporter&order=<?= ($order_by=='reporter' && $order=='asc')?'desc':'asc' ?>">From</a></th>
		<th><a href="<?= $sort_url ?>assigned&order=<?= ($order_by=='assigned' && $order=='asc')?'desc':'asc' ?>">Assigned To</a></th>
		<th><a href="<?= $sort_url ?>time_guess&order=<?= ($order_by=='time_guess' && $order=='asc')?'desc':'asc' ?>">Time Required</a></th>
		<th><a href="<?= $sort_url ?>status&order=<?= ($order_by=='status' && $order=='asc')?'desc':'asc' ?>">Status</a></th>
		<th><a href="<?= $sort_url ?>date_added&order=<?= ($order_by=='date_added' && $order=='asc')?'desc':'asc' ?>">Days Open</a></th>
	</tr>
<?php
	foreach($softjob_list as $softjob_id)
	{
		$softjob=softjobFromDatabase($softjob_id);
		if(trim($softjob['title'])=='') $softjob['title']='[Untitled]';


		$shade='#66ff66';
		if($softjob['priority']<8) $shade='#66cc99';
		if($softjob['priority']<4) $shade='#669966';

		$priority='Low';
		if($softjob['priority']<8) $priority='Medium';
		if($softjob['priority']<4) $priority='High';

		$time_value=$softjob['time_guess'];
		if($softjob['status']==3) $time_value=0;
		$time_units='seconds';
		if($time_value>60)
		{
			$time_value=intval($time_value/60);
			$time_units='minutes';
			if($time_value>60)
			{
				$time_value=intval($time_value/60);
				$time_units='hours';
				if($time_value>24)
				{
					$time_value=intval($time_value/24);
					$time_units='days';
					if($time_value>365)
					{
						$time_value=intval($time_value/365.25);
						$time_units='years';
					}
					elseif($time_value>30)
					{
						$time_value=intval($time_value/30.445833);
						$time_units='months';
					}
					elseif($time_value>7)
					{
						$time_value=intval($time_value/7);
						$time_units='weeks';
					}
				}
			}
		}
?>
	<tr<?= ($alt=!$alt)?'':' class="alt"' ?>>
		<td style="text-align:center;"><input type="text" style="text-align:right;" size="4" name="report_rank[<?= $softjob_id ?>]" value="" /></td>
		<td style="text-align:center;<?= ($action=='print')?'':('font-weight:bold;background-color:'.$shade) ?>"><?= $priority ?></td>
		<td style="text-align:center;"><a href="<?= $edit_url.$softjob_id ?>" target="_blank"><?= htmlspecialchars($softjob['title']) ?></a></td>
		<td style="text-align:center;"><?= htmlspecialchars($website_list[$softjob['website']]) ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($all_staff_list[$softjob['reporter']]) ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($all_staff_list[$softjob['assigned']]) ?></td>
		<td style="text-align:center;"><?= ($time_value>0)?($time_value.' '.$time_units):'' ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($status_list[$softjob['status']]) ?></td>
		<td style="text-align:center;"><?= floor((time()-$softjob['time_added'])/(60*60*24)) ?></td>
	</tr>
<?php
	}
?>
</table>
