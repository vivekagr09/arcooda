<?php
include_once 'world_common.inc.php';

$sql = "";
if ($var == 'fum') {
	$sql = sprintf(
		"SELECT * FROM `world`.`fumigation_notes` WHERE `country` = %s",
		$DB->escape($con)
	);
} elseif ($var == 'cer') {
	$sql = sprintf(
		"SELECT * FROM `world`.`certificates` WHERE `country` = %s",
		$DB->escape($con)
	);
} else {
	$sql = sprintf(
		"SELECT * FROM `world`.`shipping_notes` WHERE `country` = %s",
		$DB->escape($con)
	);
}

$query=$DB->query($sql);
?>
<ul>
<?php
while($row=$DB->fetch_assoc($query))
	echo '<li>'.htmlspecialchars($row['comments'])."</li>\n";
?>
</ul>
