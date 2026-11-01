<?php 
include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);

$section = (isset($posted['section']) && !empty($posted['section'])) ? strtolower($posted['section']) : 'options';
$id = (isset($posted['id']) && !empty($posted['id'])) ? $posted['id'] : '0';

$history = $pb->getHistory($id, $section);
?>

<br>
<div class="flagHeader"><span>History</span></div>
<br>

<?php if (count($history) > 0 ) { ?>
<table class="view-table">
	<tr>
		<th>Date</th>
		<th>User</th>
		<th>History</th>
	</tr>

	<?php
	foreach($history as $h) {
		echo "<tr>";
		echo "<td>". date("d/m/Y", strtotime($h['added_date'])) ."</td>";
		echo "<td>". $all_staff_list[$h['staff']] ."</td>";
		echo "<td style='text-align: left;'>";
		echo $h['history'];
		echo "</td>";
		echo "</tr>";
	}
	?>
</table>
<?php } else { ?>
<p style='text-align: center; width: 95%; margin: auto; color: #ff0000;'>
	<i>History does not exist</i>
</p>
<?php } ?>