<?php
/**
 * Car Converters Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */

/**
 * Re-organise the settings for the template
 */
$style_id = 6;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.carconverters.com.au';
$base_url = 'https://www.carconverters.com.au';
$track_url = 'https://www.carconverters.com.au[TRACKER_KEY]';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item['subject']) ?></title>
</head>
<body style="font:13px arial, helvetica, clean, sans-serif;width:750px;margin:auto;font-size:11px" alink="#660000" link="#003333" text="#000000" vlink="#003333">

<br />

<?php
if ($item_settings['paragraph']['value'] != '' && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value']))
{
?>
<div style="position:relative;margin:4px 4px 0">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/logo.png', $track_url)) ?>" alt="Car Converters Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<p style="padding:16px;border:1px solid #ccc;margin:0 4px 4px;font-size:12px">
	<?php echo formatHTMLDescription($item_settings['paragraph']['value']) ?>
</p>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
	<h1 style="margin:0"><a href="<?php hecho(relative_url('/newsletters/' . formatURLSafe($item['subject']) . '_' . $item['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/logo.png', $track_url)) ?>" alt="Car Converters Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date('jS - F - Y', $item['time_added']) ?></i>
</div>
<?php
}

$packets=array();
foreach($item_settings as $k => $settings) if ($settings['enable'] == 'y') switch ($k)
{
case 'large_banner':
case 'bottom_banner':
	if (is_array($settings['items']) && moduleLoad('banner')) foreach($settings['items'] as $v) if ($v['str'] != '')
	{
		$id=$v['str'];
		ob_start();
		$static_path = $_GLOBALS['site_settings']['static_path'];
		$_GLOBALS['site_settings']['static_path'] = '';
		$temp = bannerFromDatabase($id);
		$_GLOBALS['site_settings']['static_path'] = $static_path;
		$v['view_url'] = relative_url($temp['click_url'], $track_url);
		$v['image_url'] = relative_url(str_replace('.jpg', ($settings['option'] == 'full') ? '_0x2d8.jpg' : '_0x150.jpg', $temp['image_url']), $base_url);
?>
<div style="padding:4px 0;margin:0;text-align:center">
	<a href="<?php hecho($v['view_url']) ?>"><img title="<?php hecho($temp['alt']) ?>" src="<?php hecho($v['image_url']) ?>" alt="<?php hecho($temp['alt']) ?>" border="0" /></a>
</div>
<?php
		$packets[]=array('size' => (($k=='large_banner')?'thin':'wide'), 'content' => ob_get_clean());
	}
	break;

case 'news':
	ob_start();
?>
	<h2 style="color:#fff;background:#043 url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px 0px;font-size:16px;margin:0;padding:14px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	if (is_array($settings['items'])) foreach($settings['items'] as $first=>$v) if($v['str'] != '')
	{
		$id=$v['str'];
		$temp=newsFromDatabase($id);
		if($settings['option'] == 'brief')
		{
?>
	<table style="border-collapse:collapse<?php echo ($first)?';border-top:1px dashed #ccc':'' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>"><img src="<?php hecho(relative_url($temp['thumb_url'], $base_url)) ?>" alt="<?php hecho($temp['news_title']) ?>" title="<?php hecho($temp['news_title']) ?>" border="0" /></a></td>
		<td style="font-size:12px">
			<h3 style="margin:0 0 4px;font-size:11px;text-transform:uppercase"><a href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>" style="text-decoration:none"><?php hecho($temp['news_title']) ?></a></h3>
			<?php echo formatHTMLDescription(trim($temp['short_description'])) ?>
			<a style="float:right" href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
?>
	<table style="border-collapse:collapse<?php echo ($first)?';border-top:1px dashed #ccc':'' ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>"><img src="<?php hecho(relative_url($temp['image_180_url'], $base_url)) ?>" alt="<?php hecho($temp['news_title']) ?>" title="<?php hecho($temp['news_title']) ?>" border="0" /></a></td>
		<td style="font-size:12px">
			<h3 style="margin:0 0 4px;font-size:11px;text-transform:uppercase"><a href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>" style="text-decoration:none"><?php hecho($temp['news_title']) ?></a></h3>
			<?php echo formatHTMLDescription(trim($temp['long_description'])) ?>
			<a style="float:right" href="<?php hecho(relative_url($temp['news_url'], $track_url)) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}

	}
	$packets[]=array('size' => (($settings['option']=='brief')?'thin':'wide'), 'content' => ob_get_clean());
	break;

case 'latest_offers':
case 'arcade_boards':
case 'arcade_parts':
case 'featured_products':
case 'specials':
	ob_start();
?>
	<h2 style="color:#fff;background:#043 url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px 0px;font-size:16px;margin:0;padding:14px"><?php hecho($sections[$k]['title']) ?></h2>
<?php
	switch($settings['option'])
	{
	case 'summary':
		if (is_array($temp = $settings['items'])) foreach($temp as $k => $v) if ($v['str'] == '') unset($temp[$k]);
		if (is_array($temp)) foreach(array_chunk($temp,2) as $row)
		{
?>
	<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first=>$v)
			{
				$temp=fetchProduct($v['str']);
?>
		<td<?php echo ($first)?' style="border-left:1px dashed #ccc"':'' ?> width="50%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
			<tr><td colspan="2"><h3 style="margin:0;font-size:13px"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3></td></tr>
			<tr>
				<td valign="top"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><img title="<?php hecho($temp['product_name']) ?>" src="<?php hecho(relative_url($temp['thumb_url'], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" border="0" /></a><br /><br /><br /></td>
				<td valign="top"><?php echo formatHTMLDescription(trim($temp['short_desc'])) ?></td>
			</tr>
			<tr>
				<td valign="top" align="center">
<?php
				if($temp['price_online']=='y' && $temp['saving']>0)
				{
?>
					<span style="font-size:17px;color:#888">Don't Pay<br /><strike><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.','.<sup style="vertical-align:text-top">',convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
<?php
					if($temp['final_price']<$temp['product_offerus']*1.01)
					{
?>
					<b><i style="color:#c00">Save <?php echo floor(($temp['product_offerus']-$temp['final_price'])*100.0/$temp['product_offerus']) ?>%</i></b>
<?php
					}
				}
?>
				</td>
				<td valign="top" align="center">
<?php
				if($temp['price_online']=='y')
				{
?>
					<a href="<?php hecho(relative_url('/checkout/addtocart_1_' . $temp['id'] . '.html', $track_url)) ?>" style="width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/sprites.png', $base_url)) ?>) no-repeat 0px 0px scroll;height:54px"><b><i>Now:<br /><big style="font-size:24px"><sup style="vertical-align:text-top">$</sup><?php echo str_replace('.','.<sup style="vertical-align:text-top">',convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></i></b></a>
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
		if (is_array($settings['items'])) foreach($settings['items'] as $first=>$v) if ($v['str'] != '')
		{
			$temp=fetchProduct($v['str']);
?>
	<table style="border-collapse:collapse<?php echo ($first)?';border-top:1px dashed #ccc':'' ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><img src="<?php hecho(relative_url($temp['image_180_url'], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" title="<?php hecho($temp['product_name']) ?>" border="0" /></a></td>
	<td>
		<h3><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3>
		<p><?php echo formatHTMLDescription(trim($temp['short_desc'])) ?></p>
	</td>
	<td style="text-align:center">
		<br />
		<br />
<?php
			if($temp['price_online']=='y' && $temp['saving']>0)
			{
?>
		<span class="dont_pay">Don't Pay<br /><strike><sup>$</sup><?php echo str_replace('.','.<sup>',convertCurrency($temp['product_offerus'], 'USD', '', false, $temp['precision'])) ?></sup> USD</strike></span><br />
<?php
				if($temp['final_price']<$temp['product_offerus']*1.01)
				{
?>
		<span class="save_percent">Save <?php echo floor(($temp['product_offerus']-$temp['final_price'])*100.0/$temp['product_offerus']) ?>%</span>
<?php
				}
			}
?>
		<br />
		<br />
		<br />
		<br />
<?php
			if($temp['price_online']=='y')
			{
?>
		<a href="<?php hecho(relative_url('/checkout/addtocart_1_' . $temp['id'] . '.html', $track_url)) ?>" class="buy_button">Now:<br /><big><sup>$</sup><?php echo str_replace('.','.<sup>',convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></a>
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
		if (is_array($temp)) foreach(array_chunk($temp,5) as $row)
		{
?>
	<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first=>$v)
			{
				$temp=fetchProduct($v['str']);
?>
		<td<?php echo ($first)?' style="border-left:1px dashed #ccc"':'' ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
			<tr><th style="background:#eee url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px -48px" height="51"><h3 style="margin:0;font-size:11px"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>" style="text-decoration:none"><?php hecho($temp['product_name']) ?></a></h3></th></tr>
			<tr><td valign="top" align="center"><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><img title="<?php hecho($temp['product_name']) ?>" src="<?php hecho(relative_url($temp['thumb_url'], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" border="0" /></a></td></tr>
			<tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
<?php
				if($temp['section']==3)
				{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i>1 x</i></td>
					<td align="center"><i>$<?php echo convertCurrency($temp['final_price'], 'USD') ?></i></td>
					<td align="center"><a href="<?php hecho(relative_url('/checkout/addtocart_1_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/bulk_button.png', $base_url)) ?>" alt="Buy" border="0" /></a></td>
				</tr>
<?php
				}
				else
				{
?>
				<tr>
					<td style="color:#f00;font-size:18px" colspan="2" align="center"><b><i>$<?php echo convertCurrency($temp['final_price'], 'USD') ?></i></b></td>
					<td align="center"><a href="<?php hecho(relative_url('/checkout/addtocart_1_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/unit_button.png', $base_url)) ?>" alt="Buy Now!" border="0" /></a></td>
				</tr>
<?php
				}
				if(count($temp['bulk_pricing'])) foreach($temp['bulk_pricing'] as $k => $v)
				{
?>
				<tr style="border:1px solid #ccc">
					<td align="center"><i><?php echo intval($v['qty']) ?> x</i></td>
					<td align="center"><i>$<?php echo convertCurrency($v['unit_sale_price_us'], 'USD') ?></i></td>
					<td align="center"><a href="<?php hecho(relative_url('/checkout/addtocart_' . $v['qty'] . '_' . $temp['id'] . '.html', $track_url)) ?>"><img src="<?php hecho(relative_url('/images/newsletter_' . $style_id . '/bulk_button.png', $base_url)) ?>" alt="Buy" border="0" /></a></td>
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
	$packets[]=array('size' => 'wide', 'content' => ob_get_clean());
	break;
}

foreach($packets as $k=>$v) $packets[$k]['content']='<div style="border:1px solid #ccc;margin:4px">'.$packets[$k]['content'].'</div>';
$prev=false;
$next=false;
foreach($packets as $k=>$v)
{
	$prev=$next;
	$next=$k;
	if ($prev!==false && $packets[$prev]['size']=='thin' && $v['size']=='thin')
	{
		$packets[$k]['size']='wide';
		$packets[$k]['content']='<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$prev]['content'] . '</td>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$next]['content'] . '</td>' . "\n" . '</tr></table>';
		unset($packets[$prev]);
		$prev = false;
	}
}
foreach($packets as $v) echo $v['content']."\n\n";
?>

<div style="background:#eee url(<?php hecho(relative_url('/images/newsletter_' . $style_id . '/htiles.png', $base_url)) ?>) repeat-x 0px -112px;border:1px solid #ccc;color:#000;height:100px;margin:4px;position:relative">
<?php
$row = $DB->query_first('SELECT company_invoiceheader FROM highwaygroup.company_info WHERE company_id=20');
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
	if ($temp['special'] == 'y' && $temp['special_priceus'] > 0 && $temp['special_priceus'] < $temp['product_offerus'] && $temp['product_offerus'] > 0) $temp['saving'] = 100 * (1 - ($temp['special_priceus'] / $temp['product_offerus']));

	if ($temp['final_price'] <= 0) $temp['price_online'] = 'n';
	if ($temp['price_online'] == 'n') $temp['special'] = 'n';
	if ($temp['thumb_url'] == '') $temp['thumb_url'] = '/images/noimage_sml.jpg';

	return $temp;
}

