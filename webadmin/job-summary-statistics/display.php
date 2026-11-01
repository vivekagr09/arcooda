<?php
include_once __DIR__ . '/common.inc.php';
$currentMonth = date('F');
$year = date('Y');
$month = date('m');
$totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$weeksInMonth = 4;
include_once __DIR__ . '/header.inc.php';
$count = 0;
$stats_type = array();
$query = $DB->query(sprintf(
    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  %s",
    '`highwaygroup`.`job_handle_type`',
    'ORDER BY `id` ASC'
));
$mediaicons = array(
    7 => '<i class="sicon fab fa-youtube"></i>',
    6 => '<i class="sicon fab fa-facebook-f"></i>',
    5 =>'<i class="sicon fab fa-twitter"></i>',
);
while ($row = $DB->fetch_assoc($query)) {
        $stats_type[] = $row; 
}
$DB->free_result($query);
$startOfMonth = strtotime("$year-$month-01");
$all_staff_list = array();
if ($query = $DB->query(
    "SELECT id, first_name, last_name FROM highwaygroup.users"
)) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
    $DB->free_result($query);
}
?>
<style type="text/css">
	#statistics-table td{
		border: 1px solid black;
	}
	#statistics-table th{
		border: 1px solid black;
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/js/all.min.js" integrity="sha256-IKPvaXm75eTeGvrsxwPh00y8Xjzqs203hTlQYydpLXI=" crossorigin="anonymous"></script>
<script type="text/javascript">
	function addlink(type) {
		window.open('addurllink.php?type='+type+'&action=add','','width=700,height=700')
	}
	function closeLinkPopup(){
		window.location.reload();
	}
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">'.$currentMonth.' '.$year.'</a>', false)?>
<div id="sectionSS">
	<table class="table_grid" id="statistics-table">
		<thead>
		<tr>
			<th style="width:12%"></th>
		
			<?php 
				for ($i=1; $i <= $weeksInMonth; $i++) {
					echo '<th>WK '.$i.'</th>';
					echo '<th>Target</th>';
				}
			?>
			<th>Total Target</th>
			<th>Total</th>
			<th>Shortage</th>
			<th>Manager</th>
		</tr>
	</thead>
		<?php 
			$addbreak = 3;
			foreach ($stats_type as $key => $stats) {
				$totalTarget = 0;
				$totalAchive = 0;
				echo '<tr>';
				echo '<td >'.$stats["type"].' '.($key > $addbreak ? "<span style='border: 1px solid black; border-radius: 8px; padding: 2px; font-size: 7px;'><i class='sicon  fas fa-plus' onclick='addlink(".$stats['id'].")'></i></span>":"" ).'</td>';
				for ($i=1; $i <= $weeksInMonth; $i++) {
					$startDate = date('Y-m-d', strtotime("+".(($i - 1) * 7) . " days", $startOfMonth));
					$endDate = date('Y-m-d', strtotime("+".($i * 7 - 1) . " days", $startOfMonth));
					$lastDateOfMonth = date('Y-m-d', strtotime("$year-$month-$totalDays"));
				    if ($endDate > $lastDateOfMonth) {
				        $endDate = $lastDateOfMonth;
				    }
				    $totalCount = 0;
				    if(in_array($stats['id'],array(1,2,3,4))){
				    	 $query = "SELECT count(*) as total FROM `highwaygroup`.`job_handle` WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND type_id = ".$stats['id']." AND site_id = ".$_GLOBALS['site_settings']['site_id']."";
              	  		$result = $DB->query_first($query, MYSQLI_ASSOC);
              	  		 $totalCount = $result["total"];
              	    $DB->free_result($result);
				    }else{
				    	$mediaLink = '';
				    	$sql = "SELECT id,link FROM `highwaygroup`.`job_handle` WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' AND type_id = ".$stats['id']." AND site_id = ".$_GLOBALS['site_settings']['site_id']."";
				    	$query = $DB->query($sql);
				    	while ($row = $DB->fetch_assoc($query)) {
				    		$totalCount++;
						$mediaLink = $mediaLink.'<a href="'.$row['link'].'" target="_blank"><span style="font-size: 20px;15px;padding:3px;">'.$mediaicons[$stats['id']].'</span></a>';
						}
						$DB->free_result($query);
				    }
				   

              	  echo '<td style="text-align: center;">'.((in_array($stats['id'],array(1,2,3,4)) || $totalCount == 0) ? $totalCount : $mediaLink).'</td>';	
              	  $backgroundClr = $stats["target"] >= $totalCount ? '#efbcf0':'#c9e5cb';
              	  echo '<td style="background-color:'.$backgroundClr.';text-align: center">'.$stats["target"].'</td>';
              	  $totalAchive = $totalAchive + $totalCount;
              	  $totalTarget = $totalTarget + $stats['target'];
				}
				echo '<td style="text-align: center;">'.$totalTarget.'</td>';
				$backgroundClr = $totalTarget >= $totalAchive ? '#efbcf0':'#c9e5cb';
				echo '<td style="text-align: center;background-color:'.$backgroundClr.'" >'.$totalAchive.'</td>';
				echo '<td style="text-align: center;">'.($totalTarget - $totalAchive).'</td>';
				echo '<td style="text-align: center;">'.$all_staff_list[$stats['manager']].'</td>';
				echo '</tr>';
				if($addbreak == $key){
					echo '<tr><td colspan="13" style="padding: 10px;"></td></tr>';
				}
			}
		?>
	</table>
</div>