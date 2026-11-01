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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php hecho($item['subject']) ?></title>
<base href="https://<?php echo $domain_name ?>/" />
<style type="text/css">
.has_css { display:none; }
.hasnt_css { display:block; }
</style>
<link rel="stylesheet" type="text/css" href="<?php hecho(relative_url("/css/newsletter_4.css", $track_url)) ?>" />
</head>
<body><div class="hasnt_css">If you cannot see the content below, please click here to <a href="<?php hecho(relative_url("/newsletter/" . formatURLSafe($item["subject"]) . "_" . $item["id"] . ".html", $track_url)) ?>">view this newsletter online</a></div><div id="bottom_grad"></div><div id="doc" class="has_css">

<div id="hd">
	<h1 style="margin-bottom:0;"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/"><img src="http://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_4_logo.png" alt="Converters.TV Newsletter" /></a></h1>
	<span class="header_date"><?php echo date('jS - F - Y', $item['time_added']) ?></span>
</div>
<div id="bd">
<?php

if($item_settings['paragraph']['value']!='' && $item_settings['paragraph']['value'] != $sections['paragraph']['defaults']['value'])
{
?>
	<div class="section" style="padding:16px;margin-top:0;">
		<?php echo formatHTMLDescription2($item_settings['paragraph']['value']) ?>
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
		$temp=bannerFromDatabase($id);
?>
<div style="text-align:center;padding:4px 0;"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]<?php hecho($temp['click_url']) ?>"><img src="http://<?php echo $domain_name . formatHTMLSafe(str_replace('.jpg', ($settings['option']=='full')?'_0x2d8.jpg':'_0x150.jpg', $temp['image_url'])) ?>" alt="<?php hecho($temp['alt']) ?>" title="<?php hecho($temp['alt']) ?>" /></a></div>
<?php
		$packets[]=array('size' => (($k=='large_banner')?'thin':'wide'), 'content' => ob_get_clean());
	}
	break;

case 'news':
	ob_start();
?>
<h2><?php hecho($sections[$k]['title']) ?></h2>
<?php
	if (is_array($settings['items'])) foreach($settings['items'] as $first=>$v) if($v['str'] != '')
	{
		$id=$v['str'];
		$temp=newsFromDatabase($id);
		if($settings['option'] == 'brief')
		{
?>
<table class="news_item brief"<?php echo ($first)?' style="border-top:1px dashed #ccc;"':'' ?>>
	<tr>
		<td><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($temp['news_title']) ?>" title="<?php hecho($temp['news_title']) ?>" /></a></td>
		<td style="padding-bottom:0;">
			<h3><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><?php hecho($temp['news_title']) ?></a></h3>
			<p><?php echo formatHTMLDescription2($temp['short_description']) ?></p>
			<a style="float:right;" href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>">More &raquo;</a>
		</td>
	</tr>
</table>
<?php
		}
		else
		{
?>
<div class="news_item">
	<a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>" style="float:left;margin:0 16px 16px 16px;"><img src="<?php hecho(relative_url($temp["image_180_url"], $base_url)) ?>" alt="<?php hecho($temp['news_title']) ?>" title="<?php hecho($temp['news_title']) ?>" /></a>
	<h3><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>"><?php hecho($temp['news_title']) ?></a></h3>
	<p><?php echo formatHTMLDescription2($temp['long_description']) ?></p>
	<span><a href="<?php hecho(relative_url($temp["news_url"], $track_url)) ?>">More &raquo;</a></span>
	<br style="clear:both;" />
</div>
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
<h2><?php hecho($sections[$k]['title']) ?></h2>
<?php
	switch($settings['option'])
	{
	case 'summary':
		if (is_array($temp = $settings['items'])) foreach($temp as $k => $v) if ($v['str'] == '') unset($temp[$k]);
		if (is_array($temp)) foreach(array_chunk($temp,2) as $row)
		{
?>
<table style="width:100%;"><tr>
<?php
			foreach($row as $first=>$v)
			{
				$temp=fetchProduct($v['str']);
?>
	<td style="width:50%;"><table class="product_item summary"<?php echo ($first)?' style="border-left:1px dashed #ccc;"':'' ?>>
		<tr><td colspan="2"><h3><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3></td></tr>
		<tr>
			<td><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" title="<?php hecho($temp['product_name']) ?>" /></a><br /><br /><br /></td>
			<td><p><?php echo formatHTMLDescription2($temp['short_desc']) ?></p></td>
		</tr>
		<tr>
			<td style="text-align:center;">
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
			</td>
			<td style="text-align:center;">
<?php
				if($temp['price_online']=='y')
				{
?>
				<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" class="buy_button">Now:<br /><big><sup>$</sup><?php echo str_replace('.','.<sup>',convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></a>
<?php
				}
				else
				{
?>
				<a href="#" class="mail_button" style="padding-top:20px;">PLEASE ASK</a>
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
<table class="product_item full"<?php echo ($first)?' style="border-top:1px dashed #ccc;"':'' ?>><tr>
	<td><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["image_180_url"], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" title="<?php hecho($temp['product_name']) ?>" /></a></td>
	<td>
		<h3><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3>
		<p><?php echo formatHTMLDescription2($temp['short_desc']) ?></p>
	</td>
	<td style="text-align:center;">
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
		<a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" class="buy_button">Now:<br /><big><sup>$</sup><?php echo str_replace('.','.<sup>',convertCurrency($temp['final_price'], 'USD', '', false, $temp['precision'])) ?></sup></big></a>
<?php
			}
			else
			{
?>
		<a href="#" class="mail_button" style="padding-top:20px;">PLEASE ASK</a>
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
<table style="width:100%;">
	<tr>
<?php
			foreach($row as $first=>$v)
			{
				$temp=fetchProduct($v['str']);
?>
		<td<?php echo ($first)?' style="width:20%;border-left:1px dashed #ccc;"':'' ?>><table class="product_item brief">
			<tr><th><h3><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><?php hecho($temp['product_name']) ?></a></h3></th></tr>
			<tr><td style="text-align:center;"><a href="<?php hecho(relative_url($temp["product_url"], $track_url)) ?>"><img src="<?php hecho(relative_url($temp["thumb_url"], $base_url)) ?>" alt="<?php hecho($temp['product_name']) ?>" title="<?php hecho($temp['product_name']) ?>" /></a></td></tr>
			<tr><td style="padding-top:0;"><table class="bulk_table">
<?php
				if($temp['final_price']>0)
				{
?>
				<tr>
					<td colspan="2" style="font-size:18px;color:#f00;font-weight:bold;">$<?php echo convertCurrency($temp['final_price'], 'USD') ?></td>
					<td><a href="<?php hecho(relative_url("/checkout/addtocart_1_" . $temp["id"] . ".html", $track_url)) ?>" class="unit_button"><img src="http://<?php echo $domain_name ?>/images/shim.gif" alt="" /></a></td>
				</tr>
<?php
				}
				if(count($temp['bulk_pricing'])) foreach($temp['bulk_pricing'] as $k => $v)
				{
?>
				<tr>
					<td><?php echo intval($v['qty']) ?> x</td>
					<td>$<?php echo convertCurrency($v['unit_sale_price_us'], 'USD') ?></td>
					<td><a href="<?php hecho(relative_url("/checkout/addtocart_" . $v["qty"] . "_" . $temp["id"] . ".html", $track_url)) ?>" class="bulk_button"><img src="http://<?php echo $domain_name ?>/images/shim.gif" alt="" /></a></td>
				</tr>
<?php
				}
?>
			</table></td></tr>
		</table></td>
<?php
			}
?>
	</tr>
</table>
<?php
		}
		break;
	}
	$packets[]=array('size' => 'wide', 'content' => ob_get_clean());
	break;
}

foreach($packets as $k=>$v) $packets[$k]['content']='<div class="section">'.$packets[$k]['content'].'</div>';
$prev=false;
$next=false;
foreach($packets as $k=>$v)
{
	$prev=$next;
	$next=$k;
	if ($prev!==false && $packets[$prev]['size']=='thin' && $v['size']=='thin')
	{
		$packets[$k]['size']='wide';
		$packets[$k]['content']='<table style="width:100%;"><tr><td style="width:50%;">' . $packets[$prev]['content'] . '</td><td style="width:50%;">' . $packets[$next]['content'] . '</td></tr></table>';
		unset($packets[$prev]);
		$prev = false;
	}
}
foreach($packets as $v) echo $v['content'];
?>
</div>

<div id="ft"><div class="section">
<?php
$row = $DB->query_first("SELECT company_invoiceheader FROM highwaygroup.company_info WHERE company_id=9");
$row = explode("\n", $row['company_invoiceheader']);
array_shift($row);
?>
	<table class="contact_details">
<?php
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
	else foreach(explode(", ", $v) as $v)
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
?>
	</table>
	<table class="newsletter_actions"><tr>
		<td><a href="javascript:window.print()" class="print_button"><img src="http://<?php echo $domain_name ?>/images/shim.gif" alt="" /></a>Print</td>
		<td><a href="<?php hecho(relative_url("/messages/newsletter_forward.html?id=" . $item["id"] . "&from_name=[FIRST_NAME]+[LAST_NAME]&from_email=[EMAIL_ADDRESS]", $track_url)) ?>" class="forward_button" target="_blank"><img src="http://<?php echo $domain_name ?>/images/shim.gif" alt="" /></a>Email Friend</td>
		<td><a href="<?php hecho(relative_url("/messages/unsubscribe.html?email=[EMAIL_ADDRESS]", $track_url)) ?>" class="unsubscribe_button" target="_blank"><img src="http://<?php echo $domain_name ?>/images/shim.gif" alt="" /></a>Unsubscribe</td>
	</tr></table>
</div></div>

</div></body>
</html>
<?php
function fetchProduct($id)
{
	/**
	 * Fields needed:
	 * N bulk_pricing => qty,unit_sale_price_us
	 * N final_price
	 * N image_180_url
	 * N precision
	 * N product_offerus
	 * Y id
	 * Y price_online
	 * Y product_name
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
	$temp['min_order'] = max(1,$temp['min_order']);
	$temp['precision'] = 2;
	$temp['product_offerus'] = $temp['final_price'] = convertCurrency($unit_pricing[(strtoupper($unit_pricing['selected']) == 'AUD') ? 'sales_price_au' : 'sales_price'], strtoupper($unit_pricing['selected']), 'USD', false, $temp['precision']);
	foreach($temp['bulk_pricing'] as $k=>$v) if($v['qty']==1)
	{
		unset($temp['bulk_pricing'][$k]);
		$temp['product_offerus'] = $temp['final_price'] = min($v['unit_sale_price_us'],$temp['final_price']);
	}
	$temp['special_priceus'] = $temp['special_price'];
	$temp['price_online'] = $temp['active'];
	$temp['special'] = $temp['special_flag'];
	if($temp['special']=='y') $temp['final_price'] = $temp['special_priceus'];


	$temp['low_price'] = $temp['final_price'];
	foreach($temp['bulk_pricing'] as $bulk) $temp['low_price'] = min($temp['low_price'], convertCurrency($bulk['unit_sale_price_us'], 'USD', '', false, $temp['precision']));

	$temp['saving'] = 0;
	if ($temp['special'] == 'y' && $temp['special_priceus'] > 0 && $temp['special_priceus'] < $temp['product_offerus'] && $temp['product_offerus'] > 0) $temp['saving'] = 100 * (1 - ($temp['special_priceus'] / $temp['product_offerus']));

	if ($temp['final_price'] <= 0) $temp['price_online'] = 'n';
	if ($temp['price_online'] == 'n') $temp['special'] = 'n';
	if ($temp['thumb_url'] == '') $temp['thumb_url'] = '/images/noimage_sml.jpg';

	return $temp;
}
?>