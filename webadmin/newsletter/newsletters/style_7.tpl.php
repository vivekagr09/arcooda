<?php
/**
 * Arcade Spare Parts Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */

/**
 * Re-organise the settings for the template
 */
$style_id = 7;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.arcadespareparts.com';
$base_url = 'https://www.arcadespareparts.com';
$track_url = 'https://www.arcadespareparts.com[TRACKER_KEY]';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item['subject']) ?></title>
<meta name="viewport" content="width=750px" />
</head>
<body style="font:12px arial, helvetica, clean, sans-serif;width:750px;margin:auto" alink="#660000" link="#003333" text="#000000" vlink="#003333">

<br />

<?php
if ($item_settings['paragraph']['value'] != '' && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value']))
{
?>
<div style="position:relative;margin:4px 4px 0">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/logo.png', $track_url)) ?>" alt="Arcade Spare Parts Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<div style="padding:16px;border:1px solid #ccc;margin:0 4px 4px;font-size:12px">
	<?php echo formatHTMLDescription($item_settings['paragraph']['value']) ?>
</div>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/logo.png', $track_url)) ?>" alt="Arcade Spare Parts Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
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
<div style="border:2px solid #8c9">
	<p style="text-align:center;font-size:11px;font-weight:bold;color:#071"><?php echo formatHTMLDescription($settings['value']) ?></p>
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
		$_GLOBALS['site_settings']['static_path'] = $static_path;
		if ($v['name'] == '') $v['name'] = $temp['alt'];
		$v['view_url'] = relative_url($temp['click_url'], $track_url);
		$v['image_url'] = relative_url(/*str_replace('.jpg', ($settings['option'] == 'wide') ? '_0x2d8.jpg' : '_0x150.jpg',*/ $temp['image_url']/*)*/, $base_url);
?>
<div style="padding:4px 0;margin:0;text-align:center">
	<a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a>
</div>
<?php
		$packets[] = array('size' => (($k == 'large_banner') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	}
	break;

case 'news':
	ob_start();
?>
	<h2 style="color:#fff;background:#043 url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px 0px;font-size:16px;margin:0;padding:14px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	$first = true;
	if (is_array($settings['items'])) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		$temp = newsFromDatabase($id);
		if ($v['name'] == '') $v['name'] = $temp['news_title'];
		if ($settings['option'] == 'brief')
		{
			if ($v['text'] == '') $v['text'] = trim($temp['short_description']);
			$v['view_url'] = relative_url($temp['news_url'], $track_url);
			$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
		<td style="font-size:12px">
			<h3 style="margin:0 0 4px;font-size:11px;text-transform:uppercase"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none"><?php hecho($v['name']) ?></a></h3>
			<?php echo formatHTMLDescription($v['text']) ?>
			<a style="float:right" href="<?php hecho($v['view_url']) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
			if ($v['text'] == '') $v['text'] = trim($temp['long_description']);
			$v['view_url'] = relative_url($temp['news_url'], $track_url);
			$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
?>
	<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
		<td style="font-size:12px">
			<h3 style="margin:0 0 4px;font-size:11px;text-transform:uppercase"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none"><?php hecho($v['name']) ?></a></h3>
			<?php echo formatHTMLDescription($v['text']) ?>
			<a style="float:right" href="<?php hecho($v['view_url']) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		$first = false;
	}
	$packets[] = array('size' => (($settings['option'] == 'brief') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	break;

case 'promotion':
	if (!moduleLoad('promotion')) break;
	$promotion_handler = new Promotion_Handler();
	if (is_array($temp = $settings['items'])) foreach($temp as $k2 => $v) if ($v['str'] == '') unset($temp[$k2]);
	if (is_array($temp) && count($temp)) foreach($temp as $master_v)
	{
		ob_start();
		$temp = $promotion_handler->fromDatabase($master_v['str']);
		if ($master_v['name'] == '') $master_v['name'] = $temp['name'];
		if ($master_v['text'] == '') $master_v['text'] = trim($temp['description']);
		$master_v['view_url'] = relative_url($temp['promotion_url'], $track_url);
		$master_v['image_url'] = relative_url($temp['thumb_url'], $base_url);

		$promotion_products = array();
		foreach($promotion_handler->productsFromDatabase($master_v['str']) as $v) $promotion_products[] = array('str' => $v['product_str']);

?>
	<h2 style="color:#fff;background:#092 url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px 0px;font-size:21px;margin:0;padding:11px 15px 12px;font-weight:normal;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><?php hecho($master_v['name']) ?></h2>
	<div style="border-bottom:1px solid #ccc;padding:8px;">
		<p style="padding:16px;margin:0 4px 4px;font-size:12px;"><?php echo formatHTMLDescription($master_v['text']) ?></p>
		<div style="text-align:right;font-size:9px;clear:both;">sale ends <?php echo date('d/m/y', $temp['time_end']) ?></div>
	</div>
<?php
		switch ($settings['option'])
		{
		case 'summary':
			if (is_array($temp = $promotion_products)) foreach($temp as $k2 => $v) if ($v['str'] == '') unset($temp[$k2]);
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
/*
					if ($k == 'bulk_offers')
					{
						$temp = fetchBulk($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['title'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
					}
					else
					{
*/
						$temp = fetchProduct($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['product_name'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
/*
					}
*/
					$name_height = max($name_height, estimate_p_height(15, $v['name'], $cell_width, true));
					$text_height = max($text_height, estimate_p_height(12, $v['text'], $cell_width));
				}

				foreach($row as $first => $v)
				{
/*
					if ($k == 'bulk_offers')
					{
						$temp = fetchBulk($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['title'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

						$price_au = fetchBulkPricing($v['str'], 'AUD');
						$price_aup = fetchBulkPricing($v['str'], 'AUD+');

						$price_us = fetchBulkPricing($v['str'], 'USD');

						$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
						$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
						$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
					}
					else
					{
*/
						$temp = fetchProduct($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['product_name'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
/*
						$price_au = fetchProductPricing($v['str'], 'AUD');
						$price_aup = fetchProductPricing($v['str'], 'AUD+');

						$price_us = fetchProductPricing($v['str'], 'USD');
*/
						$v['view_url'] = relative_url($temp['product_url'], $track_url);
						$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
						$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);
/*
					}
*/
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
					if ($temp['price_online'] == 'y' && $temp['saving'] >= 1.0)
					{
?>
				<?php echo dont_pay_text('$' . $price_us['normal_price'] . ' USD') ?>
				<b><i style="color:#c00">Save <?php echo $temp['saving'] ?>%</i></b><br /><br />
<?php
					}
					if ($temp['price_online'] == 'y')
					{
?>
				<?php echo small_buy_button($v['purchase_url'], $price_us['current_price'], 'USD') ?>
<?php
					}
					else echo small_mail_button($v['purchase_url']);
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
			if (is_array($promotion_products)) foreach($promotion_products as $v) if ($v['str'] != '')
			{
/*
				if ($k == 'bulk_offers')
				{
					$temp = fetchBulk($v['str']);
					if ($v['name'] == '') $v['name'] = $temp['title'];
					if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);

					$price_au = fetchBulkPricing($v['str'], 'AUD');
					$price_aup = fetchBulkPricing($v['str'], 'AUD+');

					$price_us = fetchBulkPricing($v['str'], 'USD');

					$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
					$v['image_url'] = relative_url($temp['image_url'], $base_url);
					$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
					$shots = array();
				}
				else
				{
*/
					$temp = fetchProduct($v['str']);
					if ($v['name'] == '') $v['name'] = $temp['product_name'];
					if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);
/*
					$price_au = fetchProductPricing($v['str'], 'AUD');
					$price_aup = fetchProductPricing($v['str'], 'AUD+');

					$price_us = fetchProductPricing($v['str'], 'USD');
*/
					$v['view_url'] = relative_url($temp['product_url'], $track_url);
					$v['image_url'] = relative_url($temp['image_url'], $base_url);
					$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);

					$actual = array();
					$reference = array();
					foreach (productScreenshotsFromDatabase($v['str'], 'y') as $a => $b) if ($b['ss_type'] == 2) $reference[$a] = $b;
					else $actual[$a] = $b;
					$shots = array_merge($actual, $reference);
					if (count($shots)) $shots = reset(array_chunk($shots, 10));
/*
				}
*/
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td>
		<a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a>
<?php
				ob_start();
				if ($temp['price_online'] == 'y' && $temp['saving'] >= 1.0)
				{
?>
<div style="text-align:center;">
		<?php echo dont_pay_text('$' . $price_us['normal_price'] . ' USD') ?>
		<b><i style="color:#c00">Save <?php echo $temp['saving'] ?>%</i></b><br /><br />
</div>
<?php
				}
				if ($temp['price_online'] == 'y')
				{
?>
		<?php echo buy_button($v['purchase_url'], $price_us['current_price'], 'USD') ?>
<?php
				}
				else echo mail_button($v['purchase_url']);
				$price_html = ob_get_clean();
				if ($v['price_position'] != 'right') echo '<br /><br /><br />' . $price_html;
?>
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
			<li style="float:left;padding:12px;margin:0"><a href="<?php hecho(relative_url(str_replace('/screenshots/', '/screenshots2/', $shot['screenshot_url']), $track_url)) ?>" target="_blank"><img src="<?php hecho(relative_url(str_replace('-sml.', '-0x40.', $shot['thumb_url']), $base_url)) ?>" alt="<?php hecho($shot['caption']) ?>" style="border:1px solid #ccc" /></a></li>
<?php
					}
?>
		</ul>
		<br style="clear:both" />
<?php
				}
				if ($v['price_position'] == 'right') echo $price_html;
?>
	</td>
</tr></table>
<?php
				$first = false;
			}
			break;

		case 'full':
			$first = true;
			if (is_array($promotion_products)) foreach($promotion_products as $v) if ($v['str'] != '')
			{
/*
				if ($k == 'bulk_offers')
				{
					$temp = fetchBulk($v['str']);
					if ($v['name'] == '') $v['name'] = $temp['title'];
					if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

					$price_au = fetchBulkPricing($v['str'], 'AUD');
					$price_aup = fetchBulkPricing($v['str'], 'AUD+');

					$price_us = fetchBulkPricing($v['str'], 'USD');

					$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
					$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
					$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
				}
				else
				{
*/
					$temp = fetchProduct($v['str']);
					if ($v['name'] == '') $v['name'] = $temp['product_name'];
					if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
/*
					$price_au = fetchProductPricing($v['str'], 'AUD');
					$price_aup = fetchProductPricing($v['str'], 'AUD+');

					$price_us = fetchProductPricing($v['str'], 'USD');
*/
					$v['view_url'] = relative_url($temp['product_url'], $track_url);
					$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
					$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);
/*
				}
*/
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
	<td valign="top" width="100%">
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v['view_url']) ?>" style="text-decoration:none;"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription($v['text']) ?></p>
	</td>
	<td style="text-align:center">
		<br />
<?php
				if ($temp['price_online'] == 'y' && $temp['saving'] >= 1.0)
				{
?>
		<br />
		<?php echo dont_pay_text('$' . $price_us['normal_price'] . ' USD') ?>
		<b><i style="color:#c00">Save <?php echo $temp['saving'] ?>%</i></b><br /><br />
<?php
				}
				elseif ($temp['price_online'] == 'n') echo please_ask_text();
				else
				{
?>
		<br />
		<br />
		<br />
		<br />
<?php
				}
				if ($temp['price_online'] == 'y')
				{
?>
		<?php echo buy_button($v['purchase_url'], $price_us['current_price'], 'USD') ?>
<?php
				}
				else echo mail_button($v['purchase_url']);
?>
	</td>
</tr></table>
<?php
				$first = false;
			}
			break;

		case 'brief':
			if (is_array($temp = $promotion_products)) foreach($temp as $k2 => $v) if ($v['str'] == '') unset($temp[$k2]);
			if (is_array($temp)) foreach(array_chunk($temp, 5) as $first1 => $row)
			{
				$cell_width = 740 / count($row) - 8;
				$name_height = 0;
				foreach($row as $v)
				{
/*
					if ($k == 'bulk_offers')
					{
						$temp = fetchBulk($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['title'];
					}
					else
					{
*/
						$temp = fetchProduct($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['product_name'];
/*
					}
*/
					$name_height = max($name_height, estimate_p_height(12, $v['name'], $cell_width, true));
				}

				$style1 = '';
				if ($first1) echo dividing_bar();
?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
				foreach($row as $first2 => $v)
				{
/*
					if ($k == 'bulk_offers')
					{
						$temp = fetchBulk($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['title'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

						$price_au = fetchBulkPricing($v['str'], 'AUD');
						$price_aup = fetchBulkPricing($v['str'], 'AUD+');

						$price_us = fetchBulkPricing($v['str'], 'USD');

						$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
						$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
						$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
					}
					else
					{
*/
						$temp = fetchProduct($v['str']);
						if ($v['name'] == '') $v['name'] = $temp['product_name'];
						if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
/*
						$price_au = fetchProductPricing($v['str'], 'AUD');
						$price_aup = fetchProductPricing($v['str'], 'AUD+');

						$price_us = fetchProductPricing($v['str'], 'USD');
*/
						$v['view_url'] = relative_url($temp['product_url'], $track_url);
						$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
						$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);
/*
					}
*/
					$style2 = '';
					if ($first2) $style2 = 'border-left:1px solid #ccc;';
					$style = rtrim($style1 . $style2, ';');
?>
	<td<?php echo ($style != '') ? (' style="' . $style . '"') : '' ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
		<tr><th height="<?php echo ceil($name_height + 8) ?>"><h3 style="margin:0;font-size:12px"><a href="<?php hecho($v['view_url']) ?>" style="font-weight:bold;text-decoration:none"><?php hecho($v['name']) ?></a></h3></th></tr>
		<tr><td valign="top" align="center"><a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a></td></tr>
		<tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
			<tr>
				<td style="color:#f00;font-size:18px" colspan="3" align="center">
<?php
					if($temp['price_online'] == 'y' && $price_us['current_price'] != 0.0)
					{
?>
					<b><i>$<?php echo $price_us['current_price'] ?></i></b> <span style="font-size:10px;white-space:nowrap;">USD</span>
<?php
					}
					else
					{
?>
					<b><i>Please Ask</i></b>
<?php
					}
					if ($temp['saving'] >= 1.0)
					{
?>
					<br />
					<i style="color:#f00;font-size:10px;">(Save <?php echo $temp['saving'] ?>%)</i>
<?php
					}
?>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="3"><a href="<?php hecho($v['purchase_url']) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/unit_button.png', $base_url)) ?>" alt="Buy Now!" border="0" /></a></td>
			</tr>
<?php
					if (count($temp['bulk_pricing'])) foreach($temp['bulk_pricing'] as $v)
					{
						if ($v['unit_sale_price_us'] == 0.0) $v['unit_sale_price_us'] = convertCurrency($v['unit_sale_price'], 'AUD', 'USD', false, $temp['precision']);
						if ($v['unit_sale_price'] == 0.0) $v['unit_sale_price'] = convertCurrency($v['unit_sale_price_us'], 'USD', 'AUD', false, $temp['precision']);
?>
			<tr style="border:1px solid #ccc">
				<td align="center"><i><?php echo intval($v['qty']) ?> x</i></td>
				<td align="center">
				<i>$<?php echo convertCurrency($v['unit_sale_price_us'], 'USD', false, $temp['precision']) ?></i>
				</td>
				<td align="center"><a href="<?php hecho(relative_url('/cart/addtocart_' . $v['qty'] . '_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/bulk_button.png', $base_url)) ?>" alt="Buy" border="0" /></a></td>
			</tr>
<?php
					}
?>
		</table></td></tr>
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
	}
	break;

case 'bulk_offers':
case 'latest_offers':
case 'arcade_machines':
case 'arcade_boards':
case 'arcade_parts':
case 'arcade_merchandise':
case 'featured_products':
case 'specials':
	ob_start();
?>
	<h2 style="color:#fff;background:#043 url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px 0px;font-size:16px;margin:0;padding:14px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	switch ($settings['option'])
	{
	case 'summary':
		if (is_array($temp = $settings['items'])) foreach($temp as $k => $v) if ($v['str'] == '') unset($temp[$k]);
		if (is_array($temp)) foreach(array_chunk($temp, 2) as $row)
		{
?>
	<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first => $v)
			{
					$temp = fetchProduct($v['str']);
					if ($v['name'] == '') $v['name'] = $temp['product_name'];
					if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
					$v['view_url'] = relative_url($temp['product_url'], $track_url);
					$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
					$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);
?>
	<td<?php echo ($first) ? ' style="border-left:1px dashed #ccc"' : '' ?> width="50%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
		<tr><td colspan="2">
			<h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3>
		</td></tr>
		<tr>
			<td valign="top"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a><br /><br /><br /></td>
			<td valign="top"><?php echo formatHTMLDescription($v['text']) ?></td>
		</tr>
		<tr>
			<td valign="top" align="center">
<?php
				if ($temp['price_online'] == 'y' && $temp['saving'] > 0)
				{
?>
					<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
<?php
					if ($temp['final_price'] < $temp['product_offerus'] * 1.01)
					{
?>
					<b><i style="color:#c00">Save <?php echo floor(($temp['product_offerus'] - $temp['final_price']) * 100.0 / $temp['product_offerus']) ?>%</i></b>
<?php
					}
				}
?>
				</td>
				<td valign="top" align="center">
<?php
				if ($temp['price_online'] == 'y')
				{
?>
					<a href="<?php hecho(relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/sprites.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></i></b></a>
<?php
				}
				else
				{
?>
					<a href="#" class="mail_button" style="padding-top:20px">PLEASE ASK</a>
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
/*
			if ($k == 'bulk_offers')
			{
				$temp = fetchBulk($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['title'];
				if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);

				$price_au = fetchBulkPricing($v['str'], 'AUD');
				$price_aup = fetchBulkPricing($v['str'], 'AUD+');

				$price_us = fetchBulkPricing($v['str'], 'USD');

				$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
				$v['image_url'] = relative_url($temp['image_url'], $base_url);
				$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
				$shots = array();
			}
			else
			{
*/
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['long_desc']);
/*
				$price_au = fetchProductPricing($v['str'], 'AUD');
				$price_aup = fetchProductPricing($v['str'], 'AUD+');

				$price_us = fetchProductPricing($v['str'], 'USD');
*/
				$v['view_url'] = relative_url($temp['product_url'], $track_url);
				$v['image_url'] = relative_url($temp['image_url'], $base_url);
				$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);

				$actual = array();
				$reference = array();
				foreach (productScreenshotsFromDatabase($v['str'], 'y') as $a => $b) if ($b['ss_type'] == 2) $reference[$a] = $b;
				else $actual[$a] = $b;
				$shots = array_merge($actual, $reference);
				if (count($shots)) $shots = reset(array_chunk($shots, 10));
/*
			}
*/
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px solid #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td>
		<a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a>
<?php
			ob_start();
			if ($temp['price_online'] == 'y' && $temp['saving'] >= 1.0)
			{
?>
<div style="text-align:center;">
		<?php echo dont_pay_text('$' . $price_us['normal_price'] . ' USD') ?>
		<b><i style="color:#c00">Save <?php echo $temp['saving'] ?>%</i></b><br /><br />
</div>
<?php
			}
			if ($temp['price_online'] == 'y')
			{
?>
		<?php echo buy_button($v['purchase_url'], $price_us['current_price'], 'USD') ?>
<?php
				if ($k == 'bulk_offers' && $temp['bulk_qty'] > 1)
				{
?>
<div style="text-align:center;">
		<br />
		<?php echo average_price_text('$' . convertCurrency($price_us['current_price'] / $temp['bulk_qty'], 'USD') . ' USD') ?>
</div>
<?php
				}
			}
			else echo mail_button($v['purchase_url']);
			$price_html = ob_get_clean();
			if ($v['price_position'] != 'right') echo '<br /><br /><br />' . $price_html;
?>
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
			<li style="float:left;padding:12px;margin:0"><a href="<?php hecho(relative_url(str_replace('/screenshots/', '/screenshots2/', $shot['screenshot_url']), $track_url)) ?>" target="_blank"><img src="<?php hecho(relative_url(str_replace('-sml.', '-0x40.', $shot['thumb_url']), $base_url)) ?>" alt="<?php hecho($shot['caption']) ?>" style="border:1px solid #ccc" /></a></li>
<?php
				}
?>
		</ul>
		<br style="clear:both" />
<?php
			}
			if ($v['price_position'] == 'right') echo $price_html;
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
/*
			if ($k == 'bulk_offers')
			{
				$temp = fetchBulk($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['title'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

				$price_au = fetchBulkPricing($v['str'], 'AUD');
				$price_aup = fetchBulkPricing($v['str'], 'AUD+');

				$price_us = fetchBulkPricing($v['str'], 'USD');

				$v['view_url'] = relative_url($temp['bulk_url'], $track_url);
				$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
				$v['purchase_url'] = relative_url('/bulk_offers/addtocart_' . $temp['id'] . '.html', $track_url);
			}
			else
			{
*/
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
/*
				$price_au = fetchProductPricing($v['str'], 'AUD');
				$price_aup = fetchProductPricing($v['str'], 'AUD+');

				$price_us = fetchProductPricing($v['str'], 'USD');
*/
				$v['view_url'] = relative_url($temp['product_url'], $track_url);
				$v['image_url'] = relative_url($temp['image_180_url'], $base_url);
				$v['purchase_url'] = relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url);
/*
			}
*/
?>
<table style="border-collapse:collapse<?php echo (!$first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho($v['view_url']) ?>"><img src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
	<td valign="top" style="font-size:12px;">
		<h3 style="font-size:13px;"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3>
		<p><?php echo formatHTMLDescription($v['text']) ?></p>
	</td>
	<td style="text-align:center">
		<br />
		<br />
<?php
			if ($temp['price_online'] == 'y' && $temp['saving'] > 0)
			{
?>
		<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
<?php
				if ($temp['final_price'] < $temp['product_offerus'] * 1.01)
				{
?>
		<b><i style="color:#c00">Save <?php echo floor(($temp['product_offerus'] - $temp['final_price']) * 100.0 / $temp['product_offerus']) ?>%</i></b>
<?php
				}
			}
?>
		<br />
		<br />
		<br />
		<br />
<?php
			if ($temp['price_online'] == 'y')
			{
?>
		<a href="<?php hecho(relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/sprites.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></i></b></a>
<?php
			}
			else
			{
?>
		<a href="#" class="mail_button" style="padding-top:20px">PLEASE ASK</a>
<?php
			}
?>
	</td>
</tr></table>
<?php
		}
		break;

	case 'brief':
		if (is_array($temp = $settings['items'])) foreach($temp as $k => $v) if ($v['str'] == '') unset($temp[$k]);
		if (is_array($temp)) foreach(array_chunk($temp, 5) as $first1 => $row)
		{
			$style1 = '';
			//if ($first1) $style1 = 'border-top:1px solid #ccc;';
?>
	<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first2 => $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
				$v['image_url'] = relative_url($temp['thumb_url'], $base_url);
				$style2 = '';
				if ($first2) $style2 = 'border-left:1px dashed #ccc;';
				$style = rtrim($style1 . $style2, ';');
?>
		<td<?php echo ($style != '') ? (' style="' . $style . '"') : '' ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
			<tr><th style="background:#eee url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px -48px" height="51"><h3 style="margin:0;font-size:11px"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>" style="text-decoration:none"><?php hecho($v['name']) ?></a></h3></th></tr>
			<tr><td valign="top" align="center"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a></td></tr>
			<tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
<?php
				if ($temp['section'] == 3)
				{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i>1 x</i></td>
<?php
					if ($temp['price_online'] == 'n')
					{
?>
					<td align="center"><i>Please Ask</i></td>
<?php
					}
					else
					{
?>
					<td align="center"><i>$<?php echo convertCurrency($temp['final_price'], 'USD') ?></i></td>
<?php
					}
?>
					<td align="center"><a href="<?php hecho(relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/bulk_button.png', $base_url)) ?>" alt="Buy" border="0" /></a></td>
				</tr>
<?php
				}
				else
				{
?>
				<tr>
					<td style="color:#f00;font-size:18px" colspan="2" align="center">
<?php
					if ($temp['price_online'] == 'n')
					{
?>
						<b><i style="font-size:14px;">Please Ask</i></b>
<?php
					}
					else
					{
?>
						<b><i>$<?php echo convertCurrency($temp['final_price'], 'USD') ?></i></b><br />
<?php
					}
					if ($temp['price_online'] == 'y' && $temp['saving'] > 0 && $temp['final_price'] < $temp['product_offerus'] * 1.01)
					{
?>
						<i style="font-size:9px">Save <?php echo floor(($temp['product_offerus'] - $temp['final_price']) * 100.0 / $temp['product_offerus']) ?>%</i>
<?php
					}
?>
					</td>
					<td align="center"><a href="<?php hecho(relative_url('/cart/addtocart_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/unit_button.png', $base_url)) ?>" alt="Buy Now!" border="0" /></a></td>
				</tr>
<?php
				}
				if (count($temp['bulk_pricing'])) foreach($temp['bulk_pricing'] as $k => $v)
				{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i><?php echo intval($v['qty']) ?> x</i></td>
					<td align="center"><i>$<?php echo convertCurrency($v['unit_sale_price_us'], 'USD') ?></i></td>
					<td align="center"><a href="<?php hecho(relative_url('/cart/addtocart_' . $v['qty'] . '_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/bulk_button.png', $base_url)) ?>" alt="Buy" border="0" /></a></td>
				</tr>
<?php
				}
?>
			</table></td></tr>
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
foreach($packets as $k => $v) $packets[$k]['content'] = '<div style="border:1px solid #ccc;margin:4px">' . $packets[$k]['content'] . '</div>';
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
foreach($packets as $v) echo $v['content'] . "\n\n";
?>

<div style="background:#eee url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px -112px;border:1px solid #ccc;color:#000;height:100px;margin:4px;position:relative">
<?php
$row = $DB->query_first('SELECT `company_invoiceheader` FROM `highwaygroup`.`company_info` WHERE `company_id` = 20 LIMIT 1', MYSQL_ASSOC);
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table style="border-collapse:collapse;font-size:10px;position:absolute;right:16px;top:20px" cellpadding="0">
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
			<th width="50%" valign="top" align="right"><?php hecho(trim($row[0])) ?>:</th>
			<td valign="top" align="right"><?php hecho(trim($row[1])) ?></td>
		</tr>
<?php
	}
	else foreach(explode(', ', $v) as $v)
	{
		$row = explode(':', $v);
?>
		<tr>
			<th width="50%" valign="top" align="right"><?php hecho(trim($row[0])) ?>:</th>
			<td valign="top" align="right"><?php hecho(trim($row[1])) ?></td>
		</tr>
<?php
	}
}
?>
	</table>
	<table style="border-collapse:collapse;font-size:10px;left:16px;position:absolute;top:20px" cellpadding="0"><tr>
		<td height="64" width="64" valign="top" align="center"><a href="javascript:window.print()" style="text-decoration:none"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/print_button.png', $base_url)) ?>" alt="" border="0" /><br />Print</a></td>
		<td height="64" width="64" valign="top" align="center"><a href="<?php hecho(relative_url('/messages/newsletter_forward.html?id=' . $item['id'] . '&from_name=[FIRST_NAME]+[LAST_NAME]&from_email=[EMAIL_ADDRESS]', $track_url)) ?>" target="_blank" style="text-decoration:none"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/forward_button.png', $base_url)) ?>" alt="" border="0" /><br />Email Friend</a></td>
		<td height="64" width="64" valign="top" align="center"><a href="<?php hecho(relative_url('/messages/unsubscribe.html?email=[EMAIL_ADDRESS]', $track_url)) ?>" target="_blank" style="text-decoration:none"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/unsubscribe_button.png', $base_url)) ?>" alt="" border="0" /><br />Unsubscribe</a></td>
	</tr></table>
</div>

</body>
</html>
<?php
function dividing_bar()
{
	ob_start();
?>
<hr style="background:#183;height:6px;border-style:none;border-top:1px solid #596;border-bottom:1px solid #bdc" />
<?php
	return ob_get_clean();
}

function please_ask_text($text = '')
{
	ob_start();
?>
<p style="font-size:9px;color:#777">
	<b style="font-size:16px">PLEASE ASK</b><br />
	<i>Product Pricing is currently unavailable.<br />If you wish to be notified when pricing becomes available, click the button and add your name to our list.</i><br />
</p>
<?php
	return ob_get_clean();
}

function average_price_text($text = '')
{
	ob_start();
?>
<p style="font-size:17px;color:#888;margin:0">
	Average Price<br />
	<?php echo $text ?><br />
</p>
<?php
	return fancy_currency(ob_get_clean());
}

function dont_pay_text($text = '')
{
	ob_start();
?>
<p style="font-size:17px;color:#888;margin:0">
	Don't Pay<br />
	<strike><?php echo $text ?></strike><br />
</p>
<?php
	return fancy_currency(ob_get_clean());
}

function mail_button($url = '#', $text = 'Coming Soon<br /><span style="font-size:22px">NOTIFY ME</span>')
{
	global $domain_name, $style_id;
	ob_start();
?>
<a target="_blank" href="<?php hecho($url) ?>" style="text-align:center;width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/mail_button.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i><?php echo $text ?></i></b></a>
<?php
	return ob_get_clean();
}

function small_mail_button($url = '#', $text = 'Coming Soon<br /><span style="font-size:16px">NOTIFY ME</span>')
{
	global $domain_name, $style_id;
	ob_start();
?>
<a target="_blank" href="<?php hecho($url) ?>" style="text-align:center;width:95px;padding:5px 52px 5px 5px;font-size:13px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/small_mail_button.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:36px"><b><i><?php echo $text ?></i></b></a>
<?php
	return ob_get_clean();
}

function buy_button($url = '#', $price = '', $currency = '')
{
	global $domain_name, $style_id;
	$currency_symbol = '';
	if ($currency != '') $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), 'HTML');
	if (strlen($price) > 7  && substr($price, -3) == '.00') $price = substr($price, 0, -3);
	$font_size = min(30, 44 - strlen($price) * 2);
	ob_start();
?>
<a target="_blank" href="<?php hecho($url) ?>" style="text-align:center;width:121px;padding:8px 73px 8px 8px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/buy_button.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:46px;vertical-align:middle"><b><i><big style="font-size:<?php echo $font_size ?>px"><?php echo $currency_symbol . $price ?></big><br /><span style="float:right;font-size:12px"><?php echo substr($currency, 0, 3) . (($currency == 'AUD') ? ' exc. GST' : '') . (($currency == 'AUD+') ? ' inc. GST' : '') ?></span></i></b></a>
<?php
	return fancy_currency(ob_get_clean());
}

function small_buy_button($url = '#', $price = '', $currency = '')
{
	global $domain_name, $style_id;
	$currency_symbol = '';
	if ($currency != '') $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), 'HTML');
	if (strlen($price) > 8 && substr($price, -3) == '.00') $price = substr($price, 0, -3);
	$font_size = min(22, 54 - strlen($price) * 4);
	ob_start();
?>
<a target="_blank" href="<?php hecho($url) ?>" style="text-align:center;width:92px;padding:6px 54px 6px 6px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/small_buy_button.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:34px"><b><i><big style="font-size:<?php echo $font_size ?>px"><?php echo $currency_symbol . $price ?></big><br /><span style="float:right;font-size:10px"><?php echo substr($currency, 0, 3) . (($currency == 'AUD') ? ' exc. GST' : '') . (($currency == 'AUD+') ? ' inc. GST' : '') ?></span></i></b></a>
<?php
	return fancy_currency(ob_get_clean());
}

function fetchProduct($id)
{
	/**
	 * Fields needed:
	 * Y bulk_pricing => qty,unit_sale_price_us
	 * Y final_price
	 * Y id
	 * Y image_180_url
	 * Y precision
	 * Y price_online
	 * Y product_name
	 * Y product_offerus
	 * Y product_url
	 * Y saving
	 * Y short_desc
	 * Y thumb_url
	 */
	$temp = productFromDatabase($id);
	$temp['bulk_pricing'] = productBulkPricingFromDatabase($id);
	$temp['low_price'] = $temp['final_price'];
	foreach($temp['bulk_pricing'] as $bulk) $temp['low_price'] = min($temp['low_price'], convertCurrency($bulk['unit_sale_price_us'], 'USD', '', false, $temp['precision']));
	$temp['saving'] = 0;
	if ($temp['on_special'] == 'y') $temp['saving'] = 100 * (1 - ($temp['special_priceus'] / $temp['product_offerus']));
	if ($temp['final_price'] <= 0) $temp['price_online'] = 'n';
	if ($temp['price_online'] == 'n') $temp['on_special'] = 'n';
	if ($temp['thumb_url'] == '') $temp['thumb_url'] = '/images/noimage_sml.jpg';
	return $temp;
}
function estimate_p_height($font_size, $text, $width = 256, $bold = false)
{
	$font_size = floatval($font_size * 0.7656247);
	$font_file = '/home/common/fonts/Arial.ttf';
	if ($bold) $font_file = '/home/common/fonts/Arial Bold.ttf';
	$font_angle = 0.0;
	$result_text = array();
	foreach(explode("\n", $text) as $paragraph)
	{
		$words = preg_split('/[\\t ]+/', $paragraph);
		$line = array();
		while(count($words))
		{
			$word = array_shift($words);
			$test = $line;
			$test[] = $word;
			$test = trim(implode(' ', $test));
			$test = imagettfbbox($font_size, $font_angle, $font_file, $test);
			if ((max($test[2], $test[4]) - min($test[0], $test[6])) > $width)
			{
				$result_text[] = implode(' ', $line);
				$chars = '';
				while (strlen($word) > 0)
				{
					$char = substr($word, 0, 1);
					$word = substr($word, 1);

					$test = imagettfbbox($font_size, $font_angle, $font_file, $chars . $char);
					if ((max($test[2], $test[4]) - min($test[0], $test[6])) > $width)
					{
						$result_text[] = $chars;
						$chars = $char;
					}
					else $chars .= $char;
				}
				if (strlen($chars)) $line = array($chars);
			}
			else $line[] = $word;
		}
		if (count($line)) $result_text[] = implode(' ', $line);
	}

	$rawr = '';
	for($i = 32;$i < 255;$i++) $rawr .= chr($i);
	$test = imagettfbbox($font_size, $font_angle, $font_file, $rawr . implode("\n", $result_text) . $rawr);
	return 0.945390 * max($test[1], $test[3]) - min($test[5], $test[7]);
}

function fancy_currency($html)
{
	return preg_replace('/\\$([0-9]+)\\.([0-9]+)/', '<sup style="vertical-align:text-top">\\$</sup>$1.<sup style="vertical-align:text-top">$2</sup>', $html);
}

