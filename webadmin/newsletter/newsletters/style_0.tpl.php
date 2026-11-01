<?php
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
$style_id = 0;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.highwaygroup.net';
$base_url = 'https://www.highwaygroup.net';
$track_url = 'https://www.highwaygroup.net[TRACKER_KEY]';

$site_id = $_GLOBALS['site_settings']['site_id'];
foreach(explode(',', $item['subscr_list']) as $subscr)
{
	$k = explode('_', $subscr);
	$v = array_pop($k);
	$k = implode('_', $k);
	switch($k)
	{
	case 'sales':
	case 'site':
		$item['site_id'] = intval($v);
		break;
	}
}
if ($site_id != 11) $item['site_id'] = $site_id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php hecho($item['subject']) ?></title>
<base href="<?php hecho(relative_url('/', $base_url)) ?>" />
<link rel="stylesheet" type="text/css" href="<?php hecho(relative_url('/css/newsletter_0.css', $track_url)) ?>" />
</head>
<body><div id="bottom_grad"></div><div id="doc">

<!-- <div id="hd">
	<h1><a href="<?php hecho(relative_url('/', $track_url)) ?>" class="header_logo"><img src="/images/shim.gif" alt="<?php hecho($item['subject']) ?>" /></a></h1>
	<span class="header_date"><?php echo date('jS - F - Y', $item['time_added']) ?></span>
</div> -->
<div id="bd">
<?php
if($item_settings['paragraph']['value']!='')
{
?>
	<div class="section" style="border-style:none;">
<?php
	if (function_exists('formatHTMLDescription2')) echo formatHTMLDescription2(nl2br($item_settings['paragraph']['value']));
	// else echo formatHTMLDescription(nl2br($item_settings['paragraph']['value']));
	else echo nl2br(htmlspecialchars($item_settings['paragraph']['value']));
?>
	</div>
<?php
}
?>
</div><br>
<div id="ft"><div class="section">
<?php
$row = $DB->query_first('SELECT company_invoiceheader FROM highwaygroup.company_info WHERE company_id=' . (($item['site_id'] != 0) ? intval($item['site_id']) : 11) . ' LIMIT 1');
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table>
		<tr><td colspan=2 style="font-size:18px;"><strong>ARCOODA PTY LTD</strong></td></tr>
		<tr><td colspan=2>38-48 Maitland Road, Mayfield East,</td></tr>
		<tr><td colspan=2>NSW, 2304, Australia</td></tr>
		<tr><td colspan=2>Phone +61 249 689 313</td></tr>
		<tr><td colspan=2>Fax +61 249 689 314</td></tr><br>
		<!-- <tr><td colspan=2><a href="www.arcooda.com" target="_blank">www.arcooda.com</a></td></tr> -->
<!-- <?php
foreach($row as $v)
{
	$row = explode(':', $v);
	if (count($row) == 1)
	{
?>
		<tr><td colspan="2"><?php hecho($v) ?></td></tr>
<?php
	}
	elseif (count($row) == 2)
	{
?>
		<tr>
			<th><?php hecho($row[0]) ?>:</th>
			<td><?php hecho($row[1]) ?></td>
		</tr>
<?php
	}
	else foreach(explode(', ', $v) as $v)
	{
		$row = explode(':', $v);
?>
		<tr>
			<th><?php hecho($row[0]) ?>:</th>
			<td><?php hecho($row[1]) ?></td>
		</tr>
<?php
	}
}
?> -->
	</table>
	<span style="margin-top:10px;"><a href="www.arcooda.com" target="_blank">www.arcooda.com</a></span>
</div></div>

</div></body>
</html>