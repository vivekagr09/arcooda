<?php
/**
 * Converters Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */

/**
 * Re-organise the settings for the template
 */
$style_id = 4;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.converters.tv';
$base_url = "https://www.converters.tv";
$track_url = "https://www.converters.tv[TRACKER_KEY]";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item['subject']) ?></title>
<meta name="viewport" content="width=750px" />
</head>
<body style="font:11px arial, helvetica, clean, sans-serif;width:750px;margin:auto;font-size:11px" alink="#cc3366" link="#3366cc" text="#444444" vlink="#3366cc">

<br />

<?php
if ($item_settings['paragraph']['value'] != '' && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value']))
{
?>
<div style="position:relative;margin:4px 4px 0">
	<h1 style="margin:0"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item['subject']) ?>_<?php echo $item['id'] ?>.html"><img src="http://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Converters.TV Newsletter" border="0" /></a></h1>
	<i style="font-size:11px;position:absolute;top:12px;right:18px;color:#fff"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<p style="padding:16px;margin:0 4px 4px;font-size:12px"><?php echo formatHTMLDescription3($item_settings['paragraph']['value']) ?></p>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
	<h1 style="margin:0"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item['subject']) ?>_<?php echo $item['id'] ?>.html"><img src="http://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Converters.TV Newsletter" border="0" /></a></h1>
	<i style="font-size:11px;position:absolute;top:12px;right:18px;color:#fff"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<?php
}

$packets = array();
foreach($item_settings as $k => $settings) if ($settings['enable'] == 'y') switch ($k)
{
case 'top_banner':
case 'large_banner':
case 'bottom_banner':
	if (is_array($settings['items']) && moduleLoad('banner')) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		ob_start();
		$temp = bannerFromDatabase($id);
		if ($v['name'] == '') $v['name'] = $temp['alt'];
?>
<div style="padding:4px 0;margin:0;text-align:center">
	<a href="http://<?php echo $domain_name ?>[TRACKER_KEY]<?php hecho($temp['click_url']) ?>"><img title="<?php hecho($v['name']) ?>" src="http://<?php echo $domain_name . formatHTMLSafe(str_replace('.jpg', ($settings['option'] == 'full') ? '_0x2d8.jpg' : '_0x150.jpg', $temp['image_url'])) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a>
</div>
<?php
		$packets[] = array('size' => (($k == 'large_banner') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	}
	break;

case 'news':
	ob_start();
?>
	<h2 style="color:#fff;background:#27a url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/h2.png", $base_url)) ?>) repeat-x 0px 0px;font-size:25px;margin:0;padding:11px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><i><?php hecho($sections[$k]['title']) ?></i></h2>
<?php
	if (is_array($settings['items'])) foreach($settings['items'] as $first => $v) if ($v['str'] != '')
	{
		$id = $v['str'];
		$temp = newsFromDatabase($id);
		if ($v['name'] == '') $v['name'] = $temp['news_title'];
		if ($settings['option'] == 'brief')
		{
			if ($v['text'] == '') $v['text'] = trim($temp['short_description']);
?>
	<table style="border-collapse:collapse<?php echo ($first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
		<td>
			<h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3>
<?php
			/*
?>
			<a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" align="left" style="margin:12px 12px 12px 0" /></a>
<?php
			*/
?>
			<p style="font-size:12px"><?php echo formatHTMLDescription3($v['text']) ?></p>
			<a style="float:right" href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
			if ($v['text'] == '') $v['text'] = trim($temp['long_description']);
?>
	<table style="border-collapse:collapse<?php echo ($first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr><td valign="top">
		<a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><img src="http://<?php echo $domain_name . formatHTMLSafe($temp['image_url']) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" align="left" style="margin:0 22px 22px 0" /></a>
		<h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription3($v['text']) ?></p>
		<hr clear="left" color="#cccccc" />
		<a style="float:right;text-decoration:none;font-size:11px" href="http://<?php echo $domain_name ?>[TRACKER_KEY]/news.html"><i style="color:#999">More News Stories &raquo;</i></a>
	</td></tr></table>
<?php
		}
	}
	$packets[] = array('size' => (($settings['option'] == 'brief') ? 'thin' : 'wide'), 'content' => ob_get_clean());
	break;

case 'latest_offers':
case 'arcade_boards':
case 'arcade_parts':
case 'featured_products':
case 'specials':
	ob_start();
?>
	<h2 style="color:#fff;background:#27a url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/h2.png", $base_url)) ?>) repeat-x 0px 0px;font-size:25px;margin:0;padding:11px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><i><?php hecho($sections[$k]['title']) ?></i></h2>
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

				$price_au = fetchProductPricing($v['str'], 'AUD');
				$price_us = fetchProductPricing($v['str'], 'USD');
?>
	<td<?php echo ($first) ? ' style="border-left:1px dashed #ccc"' : '' ?> width="50%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
		<tr><td colspan="2"><h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3></td></tr>
		<tr>
			<td valign="top" colspan="2">
				<a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho(relative_url($temp["image_180_url"], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" border="0" align="left" style="margin:12px 12px 12px 0" /></a>
				<p style="font-size:12px"><?php echo formatHTMLDescription3($v['text']) ?></p>
			</td>
		</tr>
		<tr>
			<td valign="top" align="center" style="font-size:12px">
<?php
				if ($temp['price_online'] == 'y' && $temp['saving'] > 0)
				{
?>
<negotiate_list field="country">
<negotiate_item default="default">
				<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
</negotiate_item>
<negotiate_item value="AU">
				<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($price_au['normal_price'] * 1.1, 'AUD', 'AUD', true, $temp['precision'])) ?></sup> inc. GST</strike></span><br />
</negotiate_item>
<negotiate_item value="US">
				<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', $price_us['normal_price']) ?></sup> USD</strike></span><br />
</negotiate_item>
</negotiate_list>
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
			<td valign="top" align="right">
<?php
				if ($temp['price_online'] == 'y')
				{
?>
<negotiate_list field="country">
<negotiate_item default="default">
				<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:88px;padding:2px 52px 2px 5px;font-size:12px;color:#fff;text-decoration:none;display:block;margin:0 16px 0 auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/small_buy_button.png) no-repeat 0px 0px scroll;height:41px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></i></b></a>
</negotiate_item>
<negotiate_item value="AU">
				<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:88px;padding:2px 52px 2px 5px;font-size:12px;color:#fff;text-decoration:none;display:block;margin:0 16px 0 auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/small_buy_button.png) no-repeat 0px 0px scroll;height:41px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($price_au['current_price'] * 1.1, 'AUD', 'AUD', true, $temp['precision'])) ?></sup></big></i></b></a>
</negotiate_item>
<negotiate_item value="US">
				<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:88px;padding:2px 52px 2px 5px;font-size:12px;color:#fff;text-decoration:none;display:block;margin:0 16px 0 auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/small_buy_button.png) no-repeat 0px 0px scroll;height:41px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', $price_us['current_price']) ?></sup></big></i></b></a>
</negotiate_item>
</negotiate_list>
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

	case 'full':
		if (is_array($settings['items'])) foreach($settings['items'] as $first => $v) if ($v['str'] != '')
		{
			$temp = fetchProduct($v['str']);
			if ($v['name'] == '') $v['name'] = $temp['product_name'];
			if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

			$price_au = fetchProductPricing($v['str'], 'AUD');
			$price_us = fetchProductPricing($v['str'], 'USD');
?>
<table style="border-collapse:collapse<?php echo ($first) ? ';border-top:1px dashed #ccc' : '' ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["image_180_url"], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" title="<?php hecho($v['name']) ?>" border="0" /></a></td>
	<td valign="top">
		<h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription3($v['text']) ?></p>
	</td>
	<td style="text-align:center">
		<br />
		<br />
<?php
			if ($temp['price_online'] == 'y' && $temp['saving'] > 0)
			{
?>
<negotiate_list field="country">
<negotiate_item default="default">
		<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
</negotiate_item>
<negotiate_item value="AU">
		<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($price_au['original_price']*1.1, 'AUD', 'AUD', true, $temp['precision'])) ?></sup> AUD</strike></span><br />
		<i style="color:#888">inc. GST</i><br />
</negotiate_item>
<negotiate_item value="US">
		<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', $price_us['normal_price']) ?></sup> USD</strike></span><br />
</negotiate_item>
</negotiate_list>
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
<negotiate_list field="country">
<negotiate_item default="default">
		<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/sprites.png", $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></i></b></a>
</negotiate_item>
<negotiate_item value="AU">
		<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/sprites.png", $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($price_au['current_price']*1.1, 'AUD', 'AUD', true, $temp['precision'])) ?></sup></big></i></b></a>
</negotiate_item>
<negotiate_item value="US">
		<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/sprites.png", $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', $price_us['current_price']) ?></sup></big></i></b></a>
</negotiate_item>
</negotiate_list>
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
		if (is_array($temp)) foreach(array_chunk($temp, 4) as $first1 => $row)
		{
			$style1 = '';
			if ($first1) $style1 = 'border-top:1px solid #ccc;';
?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first2 => $v)
			{
				$temp = fetchProduct($v['str']);
				if ($v['name'] == '') $v['name'] = $temp['product_name'];
				if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);

				$price_au = fetchProductPricing($v['str'], 'AUD');
				$price_us = fetchProductPricing($v['str'], 'USD');

				$style2 = '';
				if ($first2) $style2 = 'border-left:1px solid #ccc;';
				$style = rtrim($style1 . $style2, ';');
?>
	<td<?php echo ($style != '') ? (' style="' . $style . '"') : '' ?> width="25%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="8">
		<tr><th height="51" colspan="2"><h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($v['name']) ?></a></h3></th></tr>
		<tr>
			<td valign="top" align="center"><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a></td>
			<td style="color:#f00" align="center">
<negotiate_list field="country">
<negotiate_item default="default">
				<b style="font-size:14px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($temp['final_price'], 'USD')) ?></sup></b><br />
				<i style="font-size:11px">USD</i><br />
</negotiate_item>
<negotiate_item value="AU">
				<b style="font-size:14px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', convertCurrency($price_au['current_price'] * 1.1, 'AUD', 'AUD', true, $temp['precision'])) ?></sup></b><br />
				<i style="font-size:11px">inc. GST</i><br />
</negotiate_item>
<negotiate_item value="US">
				<b style="font-size:14px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.', '.<sup style="vertical-align:text-top">', $price_us['current_price']) ?></sup></b><br />
				<i style="font-size:11px">USD</i><br />
</negotiate_item>
</negotiate_list>
				<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/unit_button.png", $base_url)) ?>" alt="Buy Now!" border="0" /></a>
			</td>
		</tr>
<?php
				if (count($temp['bulk_pricing']))
				{
?>
		<tr><td valign="top" colspan="2">
<negotiate_list field="country">
<negotiate_item default="default">
			<table style="border-collapse:collapse" width="100%" cellpadding="3">
<?php
					foreach($temp['bulk_pricing'] as $k => $v)
					{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i><?php echo intval($v['qty']) ?> x</i></td>
					<td align="center"><i>$<?php echo convertCurrency($v['unit_sale_price_us'], 'USD') ?></i></td>
					<td align="center"><a href="<?php hecho(relative_url("/checkout/addtocart_" . $v["qty"] . "_" . $temp["id"] . ".html", $track_url)) ?>"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/bulk_button.png", $base_url)) ?>" alt="Buy" border="0" /></a></td>
				</tr>
<?php
					}
?>
			</table>
</negotiate_item>
<negotiate_item value="AU">
			<table style="border-collapse:collapse" width="100%" cellpadding="3">
<?php
					foreach($temp['bulk_pricing'] as $k => $v)
					{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i><?php echo intval($v['qty']) ?> x</i></td>
					<td align="center"><i>$<?php echo convertCurrency($v['unit_sale_price_us'] * 1.1, 'USD', 'AUD') ?></i></td>
					<td align="center"><a href="<?php hecho(relative_url("/checkout/addtocart_" . $v["qty"] . "_" . $temp["id"] . ".html", $track_url)) ?>"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/bulk_button.png", $base_url)) ?>" alt="Buy" border="0" /></a></td>
				</tr>
<?php
					}
?>
			</table>
</negotiate_item>
</negotiate_list>
		</td></tr>
<?php
				}
				else
				{
?>
		<tr><td valign="top" colspan="2"><p style="font-size:12px"><?php echo formatHTMLDescription3($v['text']) ?></p></td></tr>
<?php
				}
?>
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
foreach($packets as $k => $v) $packets[$k]['content'] = '<div style="border:1px solid #ccc;margin:4px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:4px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px;-webkit-border-bottom-left-radius:5px;-webkit-border-bottom-right-radius:5px;-moz-border-radius-bottomleft:5px;-moz-border-radius-bottomright:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px">' . $packets[$k]['content'] . '</div>';
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

<div style="background:#eee url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)) ?>) repeat-x 0px 0px;border:1px solid #ccc;color:#000;height:85px;margin:4px;position:relative">
<?php
$row = $DB->query_first("SELECT company_invoiceheader FROM highwaygroup.company_info WHERE company_id=9");
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table style="border-collapse:collapse;font-size:10px;position:absolute;right:10px;top:19px" cellpadding="0">
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
	else foreach(explode(", ", $v) as $v)
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
	<table style="border-collapse:collapse;font-size:10px;left:11px;position:absolute;top:5px" cellpadding="0"><tr>
		<td height="64" width="80" valign="top" align="center"><a href="javascript:window.print()" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/print_button.png", $base_url)) ?>" alt="" border="0" /><br />Print</a></td>
		<td height="64" width="80" valign="top" align="center"><a href="<?php hecho(relative_url("/messages/newsletter_forward.html?id=" . $item["id"] . "&from_name=[FIRST_NAME]+[LAST_NAME]&from_email=[EMAIL_ADDRESS]", $track_url)) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/forward_button.png", $base_url)) ?>" alt="" border="0" /><br />Email Friend</a></td>
		<td height="64" width="80" valign="top" align="center"><a href="<?php hecho(relative_url("/messages/unsubscribe.html?email=[EMAIL_ADDRESS]", $track_url)) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/unsubscribe_button.png", $base_url)) ?>" alt="" border="0" /><br />Cancel your<br />Subscription</a></td>
		<td height="64" width="80" valign="top" align="center"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/contactus.html?email=[EMAIL_ADDRESS]" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/inquire_button.png", $base_url)) ?>" alt="" border="0" /><br />Send an<br />Inquiry</a></td>
	</tr></table>
</div>

</body>
</html>
<?php
function fetchProductPricing($product_str, $wanted_currency = "USD")
{
	$product = productFromDatabase($product_str);

	$normal_currency = $wanted_currency;
	$normal_price = convertCurrency($product["primary_price"], $product["primary_currency"], $wanted_currency, true, $product["precision"]);

	$current_currency = $wanted_currency;
	$current_price = convertCurrency($product["secondary_price"], $product["secondary_currency"], $wanted_currency, true, $product["precision"]);

	/**
	 * Stupid rounding method
	 */
	if ($product["section"] == 1 || $product["section"] == 2)
	{
		$normal_price = convertCurrency(round($normal_price), $normal_currency);
		$current_price = convertCurrency(round($current_price), $current_currency);
	}

	$savings = 0.0;
	if ($current_price != 0.0 && $normal_price != 0.0) $savings = bcdiv(bcmul(bcsub($normal_price, $current_price, 4), 100.0, 2), $normal_price, 0);

	$result = array();
	$result["primary_price"] = $product["primary_price"];
	$result["primary_currency"] = $product["primary_currency"];
	$result["normal_price"] = $normal_price;
	$result["normal_currency"] = $normal_currency;
	$result["secondary_price"] = $product["secondary_price"];
	$result["secondary_currency"] = $product["secondary_currency"];
	$result["current_price"] = $current_price;
	$result["current_currency"] = $current_currency;
	$result["savings"] = $savings;
	return $result;
}
function fetchProduct($id)
{
	/**
	 * Fields needed:
	 * N bulk_pricing => qty,unit_sale_price_us
	 * N final_price
	 * Y id
	 * N image_180_url
	 * N precision
	 * Y price_online
	 * Y product_name
	 * N product_offerus
	 * Y product_url
	 * Y saving
	 * Y short_desc
	 * Y thumb_url
	 */
	$temp = productFromDatabase($id);
	$temp['bulk_pricing'] = productBulkPricingFromDatabase($id);

	$other = fetchProductPricing($id, "USD");
	$temp["saving"] = $other["savings"];
	$temp["product_offerus"] = $other["normal_price"];
	$temp["final_price"] = $other["current_price"];

	if ($temp["final_price"] <= 0) $temp["price_online"] = "n";
	if ($temp["price_online"] == "n") $temp["on_special"] = "n";
	//if ($temp["image_url"] == "") $temp["image_url"] = "/images/noimage.jpg";
	//if ($temp["thumb_url"] == "") $temp["thumb_url"] = "/images/noimage_sml.jpg";


	foreach($temp["bulk_pricing"] as $k => $v)
	{
		if (bccomp($v["unit_sale_price_us"], "0.0", $temp["precision"]) <= 0) $temp["bulk_pricing"][$k]["unit_sale_price_us"] = convertCurrency($v["unit_sale_price"], "AUD", "USD", true, $temp["precision"]);
		if (bccomp($v["unit_sale_price"], "0.0", $temp["precision"]) <= 0) $temp["bulk_pricing"][$k]["unit_sale_price"] = convertCurrency($v["unit_sale_price_us"], "USD", "AUD", true, $temp["precision"]);
	}
	foreach($temp["bulk_pricing"] as $k => $v)
	{
		if (convertCurrency($v["unit_sale_price_us"], "USD", $other["current_currency"], true, $temp["precision"]) >= $other["current_price"]) unset($temp["bulk_pricing"][$k]);
		elseif ($v["qty"] <= 1) unset($temp["bulk_pricing"][$k]);
		elseif (bccomp($v["unit_sale_price_us"], "0.0", $temp["precision"]) <= 0) unset($temp["bulk_pricing"][$k]);
	}

	return $temp;
}
function formatHTMLDescription3($text)
{
	return str_replace('<li>', '<li style="font-size:12px">', formatHTMLDescription2($text));
}
?>