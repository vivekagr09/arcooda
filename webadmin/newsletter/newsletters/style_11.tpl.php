<?php
/**
 * Highway Games Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */

/**
 * Re-organise the settings for the template
 */
$style_id = 11;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.highwaygames.com';
$base_url = 'https://www.highwaygames.com';
$track_url = 'https://www.highwaygames.com[TRACKER_KEY]';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item['subject']) ?></title>
<meta name="viewport" content="width=750px" />
</head>
<body style="font:12px arial, helvetica, clean, sans-serif;width:750px;margin:auto" alink="#003333" link="#003333" text="#000000" vlink="#003333">

<br />

<?php
$logo = 'logo.jpg';
/*
$logos = glob($_SERVER["DOCUMENT_ROOT"] . "/images/newsletter_" . $style_id . "/logo*.jpg");
if (count($logos))
{
	$row = $DB->query_first("SELECT COUNT(*) AS `count` FROM `highwaygroup`.`newsletters` WHERE `date_sent` <> '0000-00-00 00:00:00' AND `style_id` = " . $style_id, MYSQL_ASSOC);
	$logo = basename($logos[$row["count"] % count($logos)]);
}
*/
if (array_key_exists('header', $item_settings) && $item_settings['header']['value'] != '') $logo = $item_settings['header']['value'];

if ($item_settings['paragraph']['value'] != '' && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value']))
{
?>
<div style="position:relative;margin:4px 4px 0">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/' . $logo, $track_url)) ?>" alt="Highway Games Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<p style="padding:16px;margin:0 4px 4px;font-size:12px"><?php echo formatHTMLDescription($item_settings['paragraph']['value']) ?></p>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/' . $logo, $track_url)) ?>" alt="Highway Games Newsletter" border="0" /></a></h1>
	<i style="font-size:11px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<?php
}

$packets = array();
foreach($item_settings as $k => $settings) if ($settings['enable'] == 'y') switch ($k)
{
case 'disclaimer':
	if (trim($settings['value']) != '')
	{
		ob_start();
?>
<div style="border:2px solid #89c">
	<p style="text-align:center;font-size:11px;font-weight:bold;color:#017"><?php echo formatHTMLDescription($settings['value']) ?></p>
</div>
<?php
		$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	}
	break;

case 'top_banner':
case 'large_banner':
case 'bottom_banner':
	if (is_array($settings['items']) && moduleLoad('banner')) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		ob_start();
		$static_path = $_GLOBALS['site_settings']['static_path'];
		$_GLOBALS['site_settings']['static_path'] = '';
		$temp = bannerFromDatabase($id);
		foreach($temp as $temp_k => $temp_v) if (substr($temp_v, 0, 29) == 'https://www.highwaygames.com/') $temp[$temp_k] = substr($temp_v, 28);
		$_GLOBALS['site_settings']['static_path'] = $static_path;
		if ($v['name'] == '') $v['name'] = $temp['alt'];
		$v['view_url'] = relative_url($temp['click_url'], $track_url);
		$v['image_url'] = relative_url(str_replace('.jpg', ($settings['option'] == 'wide') ? '_0x2d8.jpg' : '_0x150.jpg', $temp['image_url']), $base_url);
?>
<div style="padding:4px 0;margin:0;text-align:center">
	<a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a>
</div>
<?php
		$packets[] = array('size' => (($settings['option'] == 'wide') ? 'wide' : 'thin'), 'content' => ob_get_clean());
	}
	break;

case 'news':
	ob_start();
?>
	<h2 style="color:#fff;background:#138 url(https://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/h2.jpg) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	$first = true;
	if (is_array($settings['items'])) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		$temp = newsFromDatabase($id);
		foreach($temp as $temp_k => $temp_v) if (substr($temp_v, 0, 29) == 'https://www.highwaygames.com/') $temp[$temp_k] = substr($temp_v, 28);
		if ($v['name'] == '') $v['name'] = $temp['news_title'];
		if ($settings['option'] == 'brief')
		{
			if ($v['text'] == '') $v['text'] = trim($temp['short_description']);
			$v['view_url'] = relative_url($temp['news_url'], $track_url);
			$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
		<td>
			<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
			<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
			<a style="float:right;font-weight:normal" href="<?php hecho($v['view_url']) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
			if ($v['text'] == '') $v['text'] = trim($temp['long_description']);
			$v['view_url'] = relative_url($temp['news_url'], $track_url);
			$v['image_url'] = relative_url($temp['image_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr><td valign="top">
		<a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" align="left" style="margin:0 22px 22px 0" /></a>
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
		<hr clear="left" color="#cccccc" />
		<a style="float:right;text-decoration:none;font-size:11px;font-weight:normal" href="<?php hecho(relative_url('/arcade-news/', $track_url)) ?>"><i style="color:#999">More News Stories &raquo;</i></a>
	</td></tr></table>
<?php
		}
		$first = false;
	}
	$packets[] = array('size' => (($settings['option'] == 'brief') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	break;

case 'tradeshow':
	ob_start();
?>
	<h2 style="color:#fff;background:#138 url(https://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/h2.jpg) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	$first = true;
	if (is_array($settings['items'])) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		$temp = tradeshowFromDatabase($id);
		foreach($temp as $temp_k => $temp_v) if (is_string($temp_v) && substr($temp_v, 0, 29) == 'https://www.highwaygames.com/') $temp[$temp_k] = substr($temp_v, 28);
		if ($v['name'] == '') $v['name'] = $temp['tradeshow_name'];
		if ($settings['option'] == 'brief')
		{
			if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
			$v['view_url'] = relative_url($temp['tradeshow_url'], $track_url);
			$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
		<td>
			<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
			<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
			<a style="float:right;font-weight:normal" href="<?php hecho($v['view_url']) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
			if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);
			$v['view_url'] = relative_url($temp['tradeshow_url'], $track_url);
			$v['image_url'] = relative_url($temp['image_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr><td valign="top">
		<a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" align="left" style="margin:0 22px 22px 0" /></a>
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
		<hr clear="left" color="#cccccc" />
		<a style="float:right;text-decoration:none;font-size:11px;font-weight:normal" href="<?php hecho(relative_url('/arcade-tradeshows/', $track_url)) ?>"><i style="color:#999">More Trade Shows &raquo;</i></a>
	</td></tr></table>
<?php
		}
		$first = false;
	}
	$packets[] = array('size' => (($settings['option'] == 'brief') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	break;

case 'latest_offers':
case 'arcade_machines':
case 'arcade_boards':
case 'arcade_parts':
case 'arcade_merchandise':
case 'featured_products':
case 'specials':
	ob_start();
?>
	<h2 style="color:#fff;background:#138 url(https://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/h2.jpg) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	switch ($settings['option'])
	{
	case 'summary':
		if (is_array($temp = $settings['items'])) foreach($temp as $k2 => $v) if ($v['str'] == '') unset($temp[$k2]);
		if (is_array($temp)) foreach(array_chunk($temp, 3) as $first1 => $row)
		{
			if ($first1) echo dividing_bar();
?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			$cell_width = 740 / count($row) - 17;
			$name_height = 0;
			$text_height = 0;
			foreach($row as $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
				$name_height = max($name_height, estimate_p_height(15, $v['name'], $cell_width, true));
				$text_height = max($text_height, estimate_p_height(12, $v['text'], $cell_width));
			}

			foreach($row as $first => $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
				$v['view_url'] = relative_url($temp['product_url'], $track_url);
				$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
?>
	<td<?php echo ($first) ? ' style="border-left:1px solid #ccc"' : '' ?> width="33%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
		<tr><td style="text-align:center">
			<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
<?php
				$est_height = estimate_p_height(15, $v['name'], $cell_width, true);
				if ($est_height < $name_height)
				{
?>
			<div style="height:<?php echo intval($name_height - $est_height) ?>px;padding:0;margin:0"></div>
<?php
				}
?>
			<a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" style="margin:12px 0" /></a>
			<p style="font-size:12px;text-align:left"><?php echo formatHTMLDescription($v['text']) ?></p>
<?php
				$est_height = estimate_p_height(12, $v['text'], $cell_width);
				if ($est_height < $text_height)
				{
?>
			<div style="height:<?php echo intval($text_height - $est_height) ?>px;padding:0;margin:0"></div>
<?php
				}
?>
			</td>
		</tr>
	</table></td>
<?php
			}
?>
</tr></table>
<?php
		}
		break;

	case 'detailed':
		$first = true;
		if (is_array($settings['items'])) foreach($settings['items'] as $v) if ($v['str'] != '')
		{
			$temp = fetchProduct($v['str']);
			if ($v['name'] == '') $v['name'] = $temp['product_name'];
			if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);
			$v['view_url'] = relative_url($temp['product_url'], $track_url);
			$v['image_url'] = relative_url($temp['image_url'], $base_url);

			$actual = array();
			$reference = array();
			foreach (productScreenshotsFromDatabase($v['str'], 'y') as $a => $b) if ($b['ss_type'] == 2) $reference[$a] = $b;
			else $actual[$a] = $b;
			$shots = array_merge($actual, $reference);
			if (count($shots)) $shots = reset(array_chunk($shots, 10));
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td>
		<a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a>
	</td>
	<td valign="top">
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
<?php
			if (count($shots))
			{
?>
		<ul style="list-style:none;margin:0;padding:0">
<?php
				foreach($shots as $shot) if ($shot['sub_id'] != 0)
				{
?>
			<li style="float:left;padding:12px;margin:0"><a href="<?php hecho(relative_url($shot['screenshot_url'], $track_url)) ?>" target="_blank"><img src="<?php hecho(relative_url(str_replace('-sml.', '-0x40.', $shot['thumb_url']), $base_url)) ?>" alt="<?php hecho($shot['caption']) ?>" style="border:1px solid #ccc" /></a></li>
<?php
				}
?>
		</ul>
		<br style="clear:both" />
<?php
			}
?>
	</td>
</tr></table>
<?php
			$first = false;
		}
		break;

	case 'full':
		$first = true;
		if (is_array($settings['items'])) foreach($settings['items'] as $v) if ($v['str'] != '')
		{
			$temp = fetchProduct($v['str']);
			if ($v['name'] == '') $v['name'] = $temp['product_name'];
			if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
			$v['view_url'] = relative_url($temp['product_url'], $track_url);
			$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
	<td valign="top" width="100%">
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
	</td>
</tr></table>
<?php
			$first = false;
		}
		break;

	case 'brief':
		if (is_array($temp = $settings['items'])) foreach($temp as $k2 => $v) if ($v['str'] == '') unset($temp[$k2]);
		if (is_array($temp)) foreach(array_chunk($temp, 5) as $first1 => $row)
		{
			$cell_width = 740 / count($row) - 8;
			$name_height = 0;
			foreach($row as $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				$name_height = max($name_height, estimate_p_height(12, $v['name'], $cell_width, true));
			}

			$style1 = '';
			if ($first1) echo dividing_bar();
?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first2 => $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
				$v['view_url'] = relative_url($temp['product_url'], $track_url);
				$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
				$style2 = '';
				if ($first2) $style2 = 'border-left:1px solid #ccc;';
				$style = rtrim($style1 . $style2, ';');
?>
	<td<?php echo ($style != '') ? (' style="' . $style . '"') : '' ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
		<tr><th height="<?php echo ceil($name_height + 8) ?>"><h3 style="margin:0;font-size:12px"><a href="<?php hecho($v['view_url']) ?>" style="font-weight:bold;text-decoration:none"><?php hecho($v['name']) ?></a></h3></th></tr>
		<tr><td valign="top" align="center"><a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a></td></tr>
	</table></td>
<?php
			}
?>
</tr></table>
<?php
		}
		break;
	}
	$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	break;
}
foreach($packets as $k => $v) $packets[$k]['content'] = '<div style="border:1px solid #ccc;margin:4px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:4px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px">' . $packets[$k]['content'] . '</div>';
$prev = false;
$next = false;
foreach($packets as $k => $v)
{
	$prev = $next;
	$next = $k;
	if ($prev !== false && $packets[$prev]['size'] == 'thin' && $v['size'] == 'thin')
	{
		$packets[$k]['size'] = 'wide';
		$packets[$k]['content'] = '<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$prev]['content'] . '</td>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$next]['content'] . '</td>' . "\n" . '</tr></table>';
		unset($packets[$prev]);
		$prev = false;
	}
}

$temp = array();
foreach($packets as $v) $temp[] = $v['content'];
echo implode('<div style="height:0.5em"></div>' . "\n\n", $temp) . "\n\n";
?>

<div style="background:#eee url(https://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/htiles.png) repeat-x 0px -112px;border:1px solid #ccc;color:#000;height:100px;margin:4px;position:relative">
<?php
$row = $DB->query_first('SELECT `company_invoiceheader` FROM `highwaygroup`.`company_info` WHERE `company_id` = 1');
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table style="border-collapse:collapse;font-size:12px;position:absolute;right:16px;top:20px" cellpadding="0">
<?php
foreach($row as $v)
{
	$row = explode(':', $v);
	if (count($row) == 1)
	{
?>
		<tr><td valign="top" colspan="2" align="right"><?php hecho(trim($v)) ?></td></tr>
<?php
	}
	elseif (count($row) == 2)
	{
?>
		<tr>
			<th valign="top" align="right"><?php hecho(trim($row[0])) ?>:</th>
			<td valign="top" align="right"><?php hecho(trim($row[1])) ?></td>
		</tr>
<?php
	}
	else foreach(explode(', ', $v) as $v)
	{
		$row = explode(':', $v);
?>
		<tr>
			<th valign="top" align="right"><?php hecho(trim($row[0])) ?>:</th>
			<td valign="top" align="right"><?php hecho(trim($row[1])) ?></td>
		</tr>
<?php
	}
}
?>
	</table>
	<table style="border-collapse:collapse;font-size:9px;left:16px;position:absolute;top:20px" cellpadding="0"><tr>
		<td height="80" width="64" valign="top" align="center"><a href="<?php hecho(relative_url('/messages/newsletter_forward.html?id=' . $item['id'] . '&from_name=[FIRST_NAME]+[LAST_NAME]&from_email=[EMAIL_ADDRESS]', $track_url)) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/forward_button.png', $base_url)) ?>" alt="" border="0" /><br />Email to<br />Friend</a></td>
		<td height="80" width="64" valign="top" align="center"><a href="<?php hecho(relative_url('/messages/unsubscribe.html?email=[EMAIL_ADDRESS]', $track_url)) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/unsubscribe_button.png', $base_url)) ?>" alt="" border="0" /><br />Unsubscribe</a></td>
	</tr></table>
</div>

</body>
</html>
<?php
function dividing_bar()
{
	ob_start();
?>
<hr style="background:#138;height:6px;border-style:none;border-top:1px solid #569;border-bottom:1px solid #bcd" />
<?php
	return ob_get_clean();
}

function fetchProduct($id)
{
	/**
	 * Fields needed:
	 * Y id
	 * Y image_180_url
	 * Y product_name
	 * Y product_url
	 * Y short_desc
	 * Y thumb_url
	 */
	$temp = productFromDatabase($id);
	foreach($temp as $temp_k => $temp_v) if (substr($temp_v, 0, 28) == 'https://www.highwaygames.com/') $temp[$temp_k] = substr($temp_v, 27);

	if ($temp['image_url'] == '') $temp['image_url'] = '/new_images/noimage.gif';
	if ($temp['thumb_url'] == '') $temp['thumb_url'] = '/new_images/noimage_small.gif';

	return $temp;
}

function estimate_p_height($size, $text, $width = 256, $bold = false)
{
	static $cache = array();
	$key = implode('_', func_get_args());
	if (array_key_exists($key, $cache)) return $cache[$key];

	$font_file = '/home/common/fonts/Arial.ttf';
	if ($bold) $font_file = '/home/common/fonts/Arial Bold.ttf';
	$width = ceil($width);

	$text = preg_replace("/[^ -\\x7f]+/", ' ', $text);

	$scale = 1.021;
	$png = shell_exec(sprintf(
		'convert -background white -size %dx -gravity Center -font %s -pointsize %d caption:%s png:-',
		$width * 5 * $scale,
		escapeshellarg($font_file),
		$size * 5,
		escapeshellarg($text)
	));
	if (trim($png) !== '')
	{
		$img = imagecreatefromstring($png);
		$height = ceil(imagesy($img) / (5 * $scale));
		imagedestroy($img);
		//echo '<img height="' . $height . '" width="' . $width . '" src="data:image/png,' . rawurlencode($png) . '" />';
	}
	else
	{
		$height = count(explode("\n", wordwrap($text, $width / 8))) * $size;
	}

	$cache[$key] = $height;
	return $height;
}

