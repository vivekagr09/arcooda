<?php
/**
 * Arcade Forums Newsletter Template
 *
 * Expects:
 *  $item - The newsletter settings
 *  $item_settings - A list of settings
 */

/**
 * Re-organise the settings for the template
 */
$style_id = 1;
include 'common_' . $style_id . '.inc.php';
$domain_name = 'www.highwaygames.com';
$base_url = 'https://www.highwaygames.com';
$track_url = 'https://www.highwaygames.com[TRACKER_KEY]';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item['subject']) ?></title>
<style type="text/css">
body	{ font-family:sans-serif; font-size:small; width:800px; margin:auto; }
h1,h2,h3,h4,h5,h6	{ color:#006; margin:0px; padding:0px; }
table	{ border-collapse:collapse; }
th	{ padding:8px; text-align:center; vertical-align:top; }
td	{ padding:8px; text-align:left; vertical-align:top; }
td.date	{ text-align:right; font-size:small; white-space:nowrap; }
h4 span	{ display:inline-block; width:26%; text-align:center; color:#fff; padding:2px; font-size:small; }
a img	{ border-style:none; }
div.notes	{ background-color:#eef; }
#classifieds h4 span	{ background-color:#09f; }
#classifieds h4	{ border-bottom:1px solid #09f; }
#news h4 span	{ background-color:#006; }
#news h4	{ border-bottom:1px solid #006; }
#products h4 span	{ background-color:#006; }
#products h4	{ border-bottom:1px solid #006; }
#topics h4 span	{ background-color:#006; }
#topics h4	{ border-bottom:1px solid #006; }
#tradeshows h4 span	{ background-color:#006; }
#tradeshows h4	{ border-bottom:1px solid #006; }
#updates h4 span	{ background-color:#699; }
#updates h4	{ border-bottom:1px solid #699; }
#wanted h4 span	{ background-color:#9c0; }
#wanted h4	{ border-bottom:1px solid #9c0; }
</style>
</head>
<body>
<br />
<h2><a href="<?php hecho(relative_url('/', $track_url)) ?>">HIGHWAYGAMES REPORT</a><span style="font-size:small;"> - <?php echo date('jS F, Y',$item['time_added']) ?></span></h2>
<br />
<br />

<?php
if ($item_settings['paragraph']['value'] != '' && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value']))
{
?>
<p style="padding:16px;margin:0 4px 4px;font-size:12px">
	<?php echo nl2br(formatHTMLSafe($item_settings['paragraph']['value'])) ?>
</p>
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
		$static_path = $_GLOBALS['site_settings']['static_path'];
		$_GLOBALS['site_settings']['static_path'] = '';
		$temp = bannerFromDatabase($id);
		$_GLOBALS['site_settings']['static_path'] = $static_path;
		if ($v['name'] == '') $v['name'] = $temp['alt'];
?>
<br />
<a href="<?php hecho(relative_url($temp['click_url'], $track_url)) ?>"><img title="<?php hecho($v['name']) ?>" src="<?php hecho(relative_url($temp['image_Url'], $base_url)) ?>" alt="<?php hecho($v['name']) ?>" border="0" /></a><br />
<?php
		$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	}
	break;

case 'news':
	if (is_array($settings['items']) && moduleLoad('news'))
	{
		ob_start();
?>
<div id="news">
	<h4><span>Latest News Stories</span></h4>
	<table>
		<tr>
			<td></td>
			<td style="width:100%;"></td>
			<td></td>
		</tr>
<?php
		foreach($settings['items'] as $first => $v) if ($v['str'] != '')
		{
			$id = $v['str'];
			$temp = newsFromDatabase($id);
			if ($v['name'] == '') $v['name'] = $temp['news_title'];
			if ($v['text'] == '') $v['text'] = trim($temp['short_description']);
?>
		<tr>
			<th rowspan="2" style="vertical-align:middle;">
<?php
			if ($temp['thumb_url'] != '')
			{
?>
				<a href="<?php hecho($temp['news_url']) ?>"><img src="<?php hecho($temp['thumb_url']) ?>" style="width:96px;height:96px;" alt="<?php hecho($temp['news_title']) ?>" /></a>
<?php
			}
?>
			</th>
			<td><h3><a href="<?php hecho($temp['news_url']) ?>"><?php hecho($temp['news_title']) ?></a></h3></td>
			<td class="date"><?php echo date('jS F, Y', $temp['time_added']) ?></td>
		</tr>
		<tr><td colspan="2" style="height:100%;"><?php echo nl2br(formatHTMLSafe($v['text'])) ?><br /><a href="<?php hecho($temp['news_url']) ?>">Click here for more information</a><br /><br /></td></tr>
<?php
		}
?>
		<tr>
			<th></th>
			<td colspan="2"><h3><a href="/news/"><i>More News Stories <span style="font-size:x-small;">(please click here to view other news this week)</span></i></a></h3><br /><br /></td>
		</tr>
	</table>
</div>
<?php
		$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	}
	break;

case 'latest_offers':
case 'arcade_boards':
case 'arcade_parts':
case 'featured_products':
case 'specials':
	ob_start();
	if (is_array($settings['items']) && moduleLoad('product'))
	{
?>
<div id="products">
	<h4><span>Latest Products</span></h4>
	<table>
		<tr>
			<td></td>
			<td style="width:100%;"></td>
		</tr>
<?php
		foreach($settings['items'] as $first => $v) if ($v['str'] != '')
		{
			$temp = productFromDatabase($v['str']);
			if ($v['name'] == '') $v['name'] = $temp['product_name'];
			if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
?>
		<tr>
			<th rowspan="2" style="vertical-align:middle;">
<?php
			if ($temp['thumb_url'] != '')
			{
?>
				<a href="<?php hecho(relative_url($temp['product_url']), $track_url) ?>"><img src="<?php hecho(relative_url($temp['thumb_url']), $base_url) ?>" style="width:96px;height:96px;" alt="<?php hecho($v['name']) ?>" /></a>
<?php
			}
?>
			</th>
			<td><h3><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3></td>
		</tr>
		<tr><td style="height:100%;"><?php echo nl2br(formatHTMLSafe($v['text'])) ?><br /><a href="<?php hecho(relative_url($temp['product_url'], $track_url)) ?>">Click here for more information</a><br /><br /></td></tr>
<?php
		}
?>
		<tr>
			<th></th>
			<td><h3><a href="<?php hecho(relative_url('/products/', $track_url)) ?>"><i>More Latest Products <span style="font-size:x-small;">(please follow this link to the product database)</span></i></a></h3><br /><br /></td>
		</tr>
	</table>
</div>
<?php
	}
	$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	break;

case 'tradeshow':
	ob_start();
	if (is_array($settings['items']) && moduleLoad('tradeshow'))
	{
?>
<div id="tradeshows">
	<h4><span>Upcoming Trade Shows</span></h4>
	<table>
		<tr>
			<td></td>
			<td style="width:100%;"></td>
		</tr>
<?php
		foreach($settings['items'] as $first => $v) if ($v['str'] != '')
		{
			$temp = tradeshowFromDatabase($v['str']);
			if ($v['name'] == '') $v['name'] = $temp['tradeshow_name'];
			if ($v['text'] == '') $v['text'] = trim($temp['short_desc']);
			$dates = array();
			foreach(tradeshowDatesFromDatabase($tradeshow_id) as $tradeshow_date) $dates[] = $tradeshow_date['full_date_str'];
?>
		<tr><td colspan="2"><h3><a href="<?php hecho(relative_url($temp['view_url'], $track_url)) ?></a></h3></td></tr>
		<tr>
			<th style="vertical-align:middle;">
<?php
			if ($temp['thumb_url'] != '')
			{
?>
				<a href="<?php hecho(relative_url($temp['view_url'], $track_url)) ?>"><img src="<?php hecho(relative_url($temp['thumb_url'], $base_url)) ?>" style="width:96px;height:96px;" alt="<?php hecho($v['name']) ?>" /></a>
<?php
			}
?>
			</th>
			<td><?php echo nl2br(formatHTMLSafe($v['text'])) ?></td>
		</tr>
<?php
			if (count($dates))
			{
?>
		<tr>
			<th style="white-space:nowrap;">Hours & Event Dates</th>
			<td><?php echo nl2br(formatHTMLSafe(implode("\n", $dates))) ?></td>
		</tr>
<?php
			}
?>
		<tr><td colspan="2"><br /><br /></td></tr>
<?php
		}
?>
		<tr>
			<th></th>
			<td><h3><a href="/tradeshows/"><i>More Tradeshows <span style="font-size:x-small;">(please follow this link to the tradeshow database)</span></i></a></h3><br /><br /></td>
		</tr>
	</table>
</div>
<?php
	}
	$packets[] = array('size' => 'wide', 'content' => ob_get_clean());
	break;
}
foreach($packets as $k => $v) $packets[$k]['content'] = '<div style="margin:4px">' . $packets[$k]['content'] . '</div>';
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

<div class="notes">
	<i><b style="color:red;">Notes:</b> All prices quoted are in US dollars, unless specified otherwise</i><br />
	<br />
	1. To unsubscribe from this report please <a href="<?php hecho(relative_url('/?action=prefs', $track_url)) ?>">click here</a><br />
	2. To cancel your membership please email us with your member details by <a href="mailto:cancelled@highwaygames.com?subject=Cancel My Membership">clicking here</a>
</div>

</body>
</html>
<?php
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
	/**
	 * Try to fill some gaps
	 */
	$unit_pricing = productPricingFromDatabase($id);
	$temp['min_order'] = max(1, $temp['min_order']);
	$temp['precision'] = 2;
	$temp['product_offerus'] = $temp['final_price'] = convertCurrency($unit_pricing[(strtoupper($unit_pricing['selected']) == 'AUD') ? 'sales_price_au' : 'sales_price'], strtoupper($unit_pricing['selected']), 'USD', false, $temp['precision']);
	foreach($temp['bulk_pricing'] as $k => $v) if ($v['qty'] == 1)
	{
		unset($temp['bulk_pricing'][$k]);
		$temp['product_offerus'] = $temp['final_price'] = min($v['unit_sale_price_us'], $temp['final_price']);
	}
	$temp['special_priceus'] = $temp['special_price'];
	$temp['price_online'] = $temp['active'];
	$temp['special'] = $temp['special_flag'];
	if ($temp['special'] == 'y') $temp['final_price'] = $temp['special_priceus'];
	$temp['low_price'] = $temp['final_price'];
	foreach($temp['bulk_pricing'] as $bulk) $temp['low_price'] = min($temp['low_price'], convertCurrency($bulk['unit_sale_price_us'], 'USD', '', false, $temp['precision']));
	$temp['saving'] = 0;
	if ($temp['special'] == 'y' && $temp['special_priceus'] > 0 && $temp['special_priceus'] < $temp['product_offerus'] && $temp['product_offerus'] > 0) $temp['saving'] = 100 * (1 - ($temp['special_priceus'] / $temp['product_offerus']));
	if ($temp['final_price'] <= 0) $temp['price_online'] = 'n';
	if ($temp['price_online'] == 'n') $temp['special'] = 'n';
	if ($temp['thumb_url'] == '') $temp['thumb_url'] = '/images/noimage_sml.jpg';
	return $temp;
}

return;






$country_list = getCountryList();

if (is_array($update_list) && count($update_list))
{
?>
<div id="updates">
	<h4><span>Trading Company Updates</span></h4>
	<table>
		<tr>
			<td></td>
			<td style="width:100%;"></td>
			<td></td>
		</tr>
<?php
	foreach($update_list as $update_id)
	{
		$update = updateFromDatabase($update_id);
?>
		<tr>
			<th colspan="2"><a href="<?php hecho($update['view_url']) ?>"><?php hecho($update['update_title']) ?></a></th>
			<td class="date"><?php echo date('jS F, Y', $update['update_time']) ?></td>
		</tr>
		<tr>
			<td>
<?php
		if ($update['thumb_url'] != '')
		{
?>
				<a href="<?php hecho($update['view_url']) ?>"><img src="<?php hecho($update['thumb_url']) ?>" style="width:96px;height:96px;" alt="<?php hecho($update['update_title']) ?>" /></a>
<?php
		}
?>
			</td>
			<td colspan="2"><?php echo nl2br(formatHTMLSafe($update['update_description'])) ?><br /><a href="/<?php hecho($update['view_url']) ?>">Click here for more information</a></td>
		</tr>
<?php
	}
?>
	</table>
	<hr />
	<p>The news listed above is from Trading Companies who advertise on HG. To read any news point please follow the above links or visit the marketplace section.<br /><a href="/resellers/">BECOME A TRADER &amp; LIST <i>YOUR</i> NEWS!</a></p>
</div>
<?php
}
if (is_array($classifieds_list) && count($classifieds_list))
{
?>
<div id="classifieds">
	<h4><span>Classified Items Closing Soon</span></h4>
	<table>
		<tr>
			<th style="width:100%;">Items for sale</th>
			<th style="white-space:nowrap;">Selling Price</th>
			<th style="white-space:nowrap;">Country</th>
		</tr>
<?php
	foreach($classifieds_list as $classified_id)
	{
		$classified = classifiedFromDatabase($classified_id);
?>
		<tr>
			<td><a href="<?php hecho($classified['view_url']) ?>"><?php hecho($classified['classified_title']) ?></a></td>
			<td><?php echo getCurrencySymbol($classified['sell_currency']) . convertCurrency($classified['sell_price'], $classified['sell_currency']) ?> <?php echo strtoupper($classified['sell_currency']) ?></td>
			<td><?php hecho($country_list[$classified['sell_country']]) ?></td>
		</tr>
<?php
	}
?>
	</table>
	<hr />
	<p>The goods above are classified items from HG members and will expire soon. If you would like to buy any of these products please follow the links or visit the classified section.<br /><a href="/classifieds/">CLICK HERE TO ADD <i>YOUR</i> FREE CLASSIFIEDS!</a></p>
</div>
<?php
}
if (is_array($wanted_list) && count($wanted_list))
{
?>
<div id="wanted">
	<h4><span>Wanted Items Closing Soon</span></h4>
	<table>
		<tr>
			<th style="width:100%;">Name Of Wanted Item</th>
			<th style="white-space:nowrap;">Willing To Pay</th>
			<th style="white-space:nowrap;">Country</th>
		</tr>
<?php
	foreach($wanted_list as $wanted_id)
	{
		$wanted = wantedFromDatabase($wanted_id);
?>
		<tr>
			<td><a href="<?php hecho($wanted['view_url']) ?>"><?php hecho($wanted['wanted_title']) ?></a></td>
			<td><?php echo getCurrencySymbol($wanted['buy_currency']) . convertCurrency($wanted['buy_price'], $wanted['buy_currency']) ?> <?php echo strtoupper($wanted['buy_currency']) ?></td>
			<td><?php hecho($country_list[$wanted['buy_country']]) ?></td>
		</tr>
<?php
	}
?>
	</table>
	<hr />
	<p>The goods above are wanted items from HG members and will expire soon. If you can offer any of these products please follow the links or visit the wanted section.<br /><a href="/wanted/">CLICK HERE TO ADD <i>YOUR</i> FREE WANTEDS!</a></p>
</div>
<?php
}
if (is_array($forum_list) && count($forum_list))
{
?>
<div id="forum">
	<h4><span>Latest Forum Discussion Topics</span></h4>
	<table>
<?php
	foreach($forum_list as $forum_id)
	{
		$forum = forumFromDatabase($forum_id);
		$topic_id = end(forumTopicSearch(array('forum_id' => $forum_id)));
		$topic = forumTopicFromDatabase($topic_id);
		$post = forumPostFromDatabase($topic['first_post_id']);
?>
		<tr><td>
			<h5><a href="<?php hecho($topic['view_url']) ?>"><?php hecho($topic['topic_title']) ?></a> - <a href="<?php hecho($forum['view_url']) ?>"><?php hecho($forum['forum_name']) ?></a></h5>
			<?php echo nl2br(formatHTMLSafe($post['post_text'])) ?><br /><a href="/<?php hecho($topic['view_url']) ?>">Click here for more information</a>
		</td></tr>
<?php
	}
?>
		<tr><td>
			<h5><a href="/forum/">More Forum Topics</a></h5>
			<a href="/forum/"><i>(to view the latest topics and participate please follow this link)</i></a>
		</td></tr>
	</table>
</div>
<?php
}
if (is_array($tradeshow_list) && count($tradeshow_list))
{
?>
<?php
}

