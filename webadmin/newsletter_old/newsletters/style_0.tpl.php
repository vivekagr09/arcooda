<?php
/**
 * @var mixed[]   $item
 * @var mixed[][] $item_settings
 */

/**
 * General Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */
/**
 * Re-organise the settings for the template
 */
include __DIR__ . '/common_0.inc.php';

global $DB;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php hecho($item['subject'])?></title>
<base href="https://www.coinopexpress.com/" />
<link rel="stylesheet" type="text/css" href="https://www.coinopexpress.com[TRACKER_KEY]/css/newsletter_0.css" />
</head>
<body><div id="bottom_grad"></div><div id="doc">
<?php/*
<div id="hd">
	<h1><a href="https://www.coinopexpress.com[TRACKER_KEY]/" class="header_logo"><img src="/_.gif" alt="Highway Group Newsletter" /></a></h1>
	<span class="header_date"><?=date('jS - F - Y', $item['time_added'])?></span>
</div>
*/?>
<div id="bd">
<?php
if ('' != $item_settings['paragraph']['value']) {
?>
	<div class="section" style="border-style:none;">
		<?=formatHTMLDescription($item_settings['paragraph']['value'])?>
	</div>
<?php
}
?>
</div>
<div id="ft"><div class="section">
<?php
$row = $DB->query_first(sprintf(
	"SELECT `company_invoiceheader` FROM `highwaygroup`.`company_info` WHERE `company_id` = 11"
));
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table>
<?php
foreach ($row as $v) {
    $row = explode(':', $v);
    if (count($row) == 1) {
?>
		<tr><td colspan="2"><?php hecho($v)?></td></tr>
<?php
} elseif (count($row) == 2) {
?>
		<tr>
			<th><?php hecho($row[0])?>:</th>
			<td><?php hecho($row[1])?></td>
		</tr>
<?php
} else {
        foreach (explode(", ", $v) as $v) {
            $row = explode(':', $v);
?>
		<tr>
			<th><?php hecho($row[0])?>:</th>
			<td><?php hecho($row[1])?></td>
		</tr>
<?php
}
    }

}
?>
	</table>
</div></div>

</div></body>
</html>
