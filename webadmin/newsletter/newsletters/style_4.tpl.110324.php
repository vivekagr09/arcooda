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
include "common_" . $style_id . ".inc.php";
$domain_name = "www.converters.tv";
$base_url = "https://www.converters.tv";
$track_url = "https://www.converters.tv[TRACKER_KEY]";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php hecho($item["subject"]) ?></title>
<meta name="viewport" content="width=750px" />
</head>
<body style="font:12px arial, helvetica, clean, sans-serif;width:750px;margin:auto" alink="#cc3366" link="#3366cc" text="#444444" vlink="#3366cc">

<br />

<?php
if ($item_settings["paragraph"]["value"] != "" && formatCleanString($item_settings["paragraph"]["value"]) != formatCleanString($sections["paragraph"]["defaults"]["value"]))
{
?>
<div style="position:relative;margin:4px 4px 0">
	<h1 style="margin:0"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item["subject"]) ?>_<?php echo $item["id"] ?>.html"><img src="http://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Converters.TV Newsletter" border="0" /></a></h1>
	<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#fff"><?php echo date("jS - F - Y", $item["time_added"]) ?></i>
</div>
<p style="padding:16px;margin:0 4px 4px;font-size:12px"><?php echo formatHTMLDescription3($item_settings["paragraph"]["value"]) ?></p>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
	<h1 style="margin:0"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item["subject"]) ?>_<?php echo $item["id"] ?>.html"><img src="http://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Converters.TV Newsletter" border="0" /></a></h1>
	<i style="font-size:11px;position:absolute;top:12px;right:18px;color:#fff"><?php echo date("jS - F - Y", $item["time_added"]) ?></i>
</div>
<?php
}

$packets = array();
foreach($item_settings as $k => $settings) if ($settings["enable"] == "y") switch ($k)
{
case "disclaimer":
	if (trim($settings["value"]) != "")
	{
		ob_start();
?>
<div style="border:2px solid #8ac">
	<p style="text-align:center;font-size:11px;font-weight:bold;color:#069"><?php echo formatHTMLDescription3($settings["value"]) ?></p>
</div>
<?php
		$packets[] = array("size" => "wide", "content" => ob_get_clean());
	}
	break;

case "top_banner":
case "large_banner":
case "bottom_banner":
	if (is_array($settings["items"]) && moduleLoad("banner")) foreach($settings["items"] as $v) if ($v["str"] != "")
	{
		$id = $v["str"];
		ob_start();
		$temp = bannerFromDatabase($id);
		if ($v["name"] == "") $v["name"] = $temp["alt"];
		$v["view_url"] = relative_url($temp["click_url"], $track_url);
		$v["image_url"] = relative_url(str_replace(".jpg", ($settings["option"] == "wide") ? "_0x2d8.jpg" : "_0x150.jpg", $temp["image_url"]), $base_url);
?>
<div style="padding:4px 0;margin:0;text-align:center">
	<a href="<?php hecho($v["view_url"]) ?>"><img title="<?php hecho($v["name"]) ?>" src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" border="0" /></a>
</div>
<?php
		$packets[] = array("size" => (($settings["option"] == "wide") ? "wide" : "thin"), "content" => ob_get_clean());
	}
	break;

case "news":
	ob_start();
?>
	<h2 style="color:#fff;background:#27a url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/h2.png", $base_url)) ?>) repeat-x 0px 0px;font-size:25px;margin:0;padding:11px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><i><?php hecho($sections[$k]["title"]) ?></i></h2>
<?php
	if (is_array($settings["items"])) foreach($settings["items"] as $first => $v) if ($v["str"] != "")
	{
		$id = $v["str"];
		$temp = newsFromDatabase($id);
		if ($v["name"] == "") $v["name"] = $temp["news_title"];
		if ($settings["option"] == "brief")
		{
			if ($v["text"] == "") $v["text"] = trim($temp["short_description"]);
			$v["view_url"] = relative_url($temp["news_url"], $track_url);
			$v["image_url"] = relative_url($temp["thumb_url"], $base_url);
?>
	<table style="border-collapse:collapse<?php echo ($first) ? ";border-top:1px solid #ccc" : "" ?>" width="100%" cellpadding="8"><tr>
		<td valign="top"><a href="<?php hecho($v["view_url"]) ?>"><img src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" title="<?php hecho($v["name"]) ?>" border="0" /></a></td>
		<td>
			<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v["view_url"]) ?>" style="text-decoration:none;"><?php hecho($v["name"]) ?></a></h3>
			<p style="font-size:12px"><?php echo formatHTMLDescription3($v["text"]) ?></p>
			<a style="float:right;font-weight:normal" href="<?php hecho($v["view_url"]) ?>">More &raquo;</a>
		</td>
	</tr></table>
<?php
		}
		else
		{
			if ($v["text"] == "") $v["text"] = trim($temp["long_description"]);
			$v["view_url"] = relative_url($temp["news_url"], $track_url);
			$v["image_url"] = relative_url($temp["image_url"], $base_url);
?>
	<table style="border-collapse:collapse<?php echo ($first) ? ";border-top:1px solid #ccc" : "" ?>" width="100%" cellpadding="8"><tr><td valign="top">
		<a href="<?php hecho($v["view_url"]) ?>"><img src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" title="<?php hecho($v["name"]) ?>" border="0" align="left" style="margin:0 22px 22px 0" /></a>
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v["view_url"]) ?>" style="text-decoration:none"><?php hecho($v["name"]) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription3($v["text"]) ?></p>
		<hr clear="left" color="#cccccc" />
		<a style="float:right;text-decoration:none;font-size:11px;font-weight:normal" href="http://<?php echo $domain_name ?>[TRACKER_KEY]/news.html"><i style="color:#999">More News Stories &raquo;</i></a>
	</td></tr></table>
<?php
		}
	}
	$packets[] = array("size" => (($settings["option"] == "brief") ? "thin" : "wide"), "content" => ob_get_clean());
	break;

case "bulk_offers":
case "latest_offers":
case "arcade_machines":
case "arcade_boards":
case "arcade_parts":
case "arcade_merchandise":
case "featured_products":
case "specials":
	ob_start();
?>
	<h2 style="color:#fff;background:#27a url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/h2.png", $base_url)) ?>) repeat-x 0px 0px;font-size:25px;margin:0;padding:11px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:5px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px"><i><?php hecho($sections[$k]["title"]) ?></i></h2>
<?php
	switch ($settings["option"])
	{
	case "summary":
		if (is_array($temp = $settings["items"])) foreach($temp as $k2 => $v) if ($v["str"] == "") unset($temp[$k2]);
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
				if ($k == "bulk_offers")
				{
					$temp = fetchBulk($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["title"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
				}
				else
				{
					$temp = fetchProduct($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["product_name"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
				}
				$name_height = max($name_height, estimate_p_height(15, $v["name"], $cell_width, true));
				$text_height = max($text_height, estimate_p_height(12, $v["text"], $cell_width));
			}

			foreach($row as $first => $v)
			{
				if ($k == "bulk_offers")
				{
					$temp = fetchBulk($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["title"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
					$price_au = fetchBulkPricing($v["str"], "AUD");
					$price_us = fetchBulkPricing($v["str"], "USD");
					$v["view_url"] = relative_url($temp["bulk_url"], $track_url);
					$v["image_url"] = relative_url($temp["image_180_url"], $base_url);
					$v["purchase_url"] = relative_url("/bulk_offers/addtocart_" . $temp["id"] . ".html", $track_url);
				}
				else
				{
					$temp = fetchProduct($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["product_name"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
					$price_au = fetchProductPricing($v["str"], "AUD");
					$price_us = fetchProductPricing($v["str"], "USD");
					$v["view_url"] = relative_url($temp["product_url"], $track_url);
					$v["image_url"] = relative_url($temp["image_180_url"], $base_url);
					$v["purchase_url"] = relative_url("/checkout/addtocart_" . $temp["id"] . "_" . $temp["sub_id"] . ".html", $track_url);
				}
?>
	<td<?php echo ($first) ? ' style="border-left:1px solid #ccc"' : "" ?> width="33%" valign="top"><table style="border-collapse:collapse;font-size:12px" width="100%" cellpadding="8">
		<tr><td style="text-align:center">
			<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v["view_url"]) ?>" style="text-decoration:none;"><?php hecho($v["name"]) ?></a></h3>
<?php
				$est_height = estimate_p_height(15, $v["name"], $cell_width, true);
				if ($est_height < $name_height)
				{
?>
			<div style="height:<?php echo intval($name_height - $est_height) ?>px;padding:0;margin:0"></div>
<?php
				}
?>
			<a href="<?php hecho($v["view_url"]) ?>"><img title="<?php hecho($v["name"]) ?>" src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" border="0" style="margin:12px 0" /></a>
			<p style="font-size:12px;text-align:left"><?php echo formatHTMLDescription3($v["text"]) ?></p>
<?php
				$est_height = estimate_p_height(12, $v["text"], $cell_width);
				if ($est_height < $text_height)
				{
?>
			<div style="height:<?php echo intval($text_height - $est_height) ?>px;padding:0;margin:0"></div>
<?php
				}
				if ($temp["price_online"] == "y" && $temp["saving"] >= 1.0)
				{
?>
<negotiate_list field="country">
<negotiate_item default="default">
				<?php echo dont_pay_text("$" . convertCurrency($price_us["normal_price"], "USD", "USD", true, $temp["precision"]) . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
				<?php echo dont_pay_text("$" . convertCurrency($price_au["normal_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]) . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
				<b><i style="color:#c00">Save <?php echo $temp["saving"] ?>%</i></b><br /><br />
<?php
				}
				if ($temp["price_online"] == "y")
				{
?>
<negotiate_list field="country">
<negotiate_item default="default">
				<?php echo small_buy_button($v["purchase_url"], convertCurrency($price_us["current_price"], "USD", "USD", true, $temp["precision"]), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
				<?php echo small_buy_button($v["purchase_url"], convertCurrency($price_au["current_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]), "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php
				}
				else echo small_mail_button($v["purchase_url"]);
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

	case "detailed":
		if (is_array($settings["items"])) foreach($settings["items"] as $first => $v) if ($v["str"] != "")
		{
			if ($k == "bulk_offers")
			{
				$temp = fetchBulk($v["str"]);
				if ($v["name"] == "") $v["name"] = $temp["title"];
				if ($v["text"] == "") $v["text"] = trim($temp["long_desc"]);
				$price_au = fetchBulkPricing($v["str"], "AUD");
				$price_us = fetchBulkPricing($v["str"], "USD");
				$v["view_url"] = relative_url($temp["bulk_url"], $track_url);
				$v["image_url"] = relative_url($temp["image_url"], $base_url);
				$v["purchase_url"] = relative_url("/bulk_offers/addtocart_" . $temp["id"] . ".html", $track_url);
				$shots = array();
			}
			else
			{
				$temp = fetchProduct($v["str"]);
				if ($v["name"] == "") $v["name"] = $temp["product_name"];
				if ($v["text"] == "") $v["text"] = trim($temp["long_desc"]);
				$price_au = fetchProductPricing($v["str"], "AUD");
				$price_us = fetchProductPricing($v["str"], "USD");
				$v["view_url"] = relative_url($temp["product_url"], $track_url);
				$v["image_url"] = relative_url($temp["image_url"], $base_url);
				$v["purchase_url"] = relative_url("/checkout/addtocart_" . $temp["id"] . "_" . $temp["sub_id"] . ".html", $track_url);
				$shots = reset(array_chunk(productScreenshotsMain($v["str"]), 8));
			}
?>
<table style="border-collapse:collapse<?php echo ($first) ? ";border-top:1px solid #ccc" : "" ?>" width="100%" cellpadding="8"><tr>
	<td>
		<a href="<?php hecho($v["view_url"]) ?>"><img src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" title="<?php hecho($v["name"]) ?>" border="0" /></a>
<?php
			ob_start();
			if ($temp["price_online"] == "y" && $temp["saving"] >= 1.0)
			{
?>
<div style="text-align:center;">
<negotiate_list field="country">
<negotiate_item default="default">
		<?php echo dont_pay_text("$" . convertCurrency($price_us["normal_price"], "USD", "USD", true, $temp["precision"]) . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
		<?php echo dont_pay_text("$" . convertCurrency($price_au["normal_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]) . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
		<b><i style="color:#c00">Save <?php echo $temp["saving"] ?>%</i></b><br /><br />
</div>
<?php
			}
			if ($temp["price_online"] == "y")
			{
?>
<negotiate_list field="country">
<negotiate_item default="default">
		<?php echo buy_button($v["purchase_url"], convertCurrency($price_us["current_price"], "USD", "USD", true, $temp["precision"]), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
		<?php echo buy_button($v["purchase_url"], convertCurrency($price_au["current_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]), "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php
			}
			else echo mail_button($v["purchase_url"]);
			$price_html = ob_get_clean();
			if ($v["price_position"] != "right") echo "<br /><br /><br />" . $price_html;
?>
	</td>
	<td valign="top">
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v["view_url"]) ?>" style="text-decoration:none;"><?php hecho($v["name"]) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription3($v["text"]) ?></p>
<?php
			if (count($shots))
			{
?>
		<ul style="list-style:none;margin:0;padding:0">
<?php
				foreach($shots as $shot)
				{
?>
			<li style="float:left;padding:8px;margin:0"><a href="<?php hecho("http://" . $domain_name . "[TRACKER_KEY]" . $shot["screenshot_url"]) ?>" target="_blank"><img src="<?php hecho("http://" . $domain_name . str_replace("-sml.", "-0x40.", $shot["thumb_url"])) ?>" alt="<?php hecho($shot["caption"]) ?>" style="border:1px solid #ccc" /></a></li>
<?php
				}
?>
		</ul>
		<br style="clear:both" />
<?php
			}
			if ($v["price_position"] == "right") echo $price_html;
?>
	</td>
</tr></table>
<?php
		}
		break;

	case "full":
		if (is_array($settings["items"])) foreach($settings["items"] as $first => $v) if ($v["str"] != "")
		{
			if ($k == "bulk_offers")
			{
				$temp = fetchBulk($v["str"]);
				if ($v["name"] == "") $v["name"] = $temp["title"];
				if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
				$price_au = fetchBulkPricing($v["str"], "AUD");
				$price_us = fetchBulkPricing($v["str"], "USD");
				$v["view_url"] = relative_url($temp["bulk_url"], $track_url);
				$v["image_url"] = relative_url($temp["image_180_url"], $base_url);
				$v["purchase_url"] = relative_url("/bulk_offers/addtocart_" . $temp["id"] . ".html", $track_url);
			}
			else
			{
				$temp = fetchProduct($v["str"]);
				if ($v["name"] == "") $v["name"] = $temp["product_name"];
				if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
				$price_au = fetchProductPricing($v["str"], "AUD");
				$price_us = fetchProductPricing($v["str"], "USD");
				$v["view_url"] = relative_url($temp["product_url"], $track_url);
				$v["image_url"] = relative_url($temp["image_180_url"], $base_url);
				$v["purchase_url"] = relative_url("/checkout/addtocart_" . $temp["id"] . "_" . $temp["sub_id"] . ".html", $track_url);
			}
?>
<table style="border-collapse:collapse<?php echo ($first) ? ";border-top:1px solid #ccc" : "" ?>" width="100%" cellpadding="8"><tr>
	<td><a href="<?php hecho($v["view_url"]) ?>"><img src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" title="<?php hecho($v["name"]) ?>" border="0" /></a></td>
	<td valign="top" width="100%">
		<h3 style="margin:0.5em 0;font-size:15px"><a href="<?php hecho($v["view_url"]) ?>" style="text-decoration:none;"><?php hecho($v["name"]) ?></a></h3>
		<p style="font-size:12px"><?php echo formatHTMLDescription3($v["text"]) ?></p>
	</td>
	<td style="text-align:center">
		<br />
<?php
			if ($temp["price_online"] == "y" && $temp["saving"] >= 1.0)
			{
?>
		<br />
<negotiate_list field="country">
<negotiate_item default="default">
		<?php echo dont_pay_text("$" . convertCurrency($price_us["normal_price"], "USD", "USD", true, $temp["precision"]) . " USD") ?>
</negotiate_item>
<negotiate_item value="AU">
		<?php echo dont_pay_text("$" . convertCurrency($price_au["normal_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]) . " AUD inc. GST") ?>
</negotiate_item>
</negotiate_list>
		<b><i style="color:#c00">Save <?php echo $temp["saving"] ?>%</i></b><br /><br />
<?php
			}
			elseif ($temp["price_online"] == "n") echo please_ask_text();
			else
			{
?>
		<br />
		<br />
		<br />
		<br />
<?php
			}
			if ($temp["price_online"] == "y")
			{
?>
<negotiate_list field="country">
<negotiate_item default="default">
		<?php echo buy_button($v["purchase_url"], convertCurrency($price_us["current_price"], "USD", "USD", true, $temp["precision"]), "USD") ?>
</negotiate_item>
<negotiate_item value="AU">
		<?php echo buy_button($v["purchase_url"], convertCurrency($price_au["current_price"] * 1.1, "AUD", "AUD", true, $temp["precision"]), "AUD+") ?>
</negotiate_item>
</negotiate_list>
<?php
			}
			else echo mail_button($v["purchase_url"]);
?>
	</td>
</tr></table>
<?php
		}
		break;

	case "brief":
		if (is_array($temp = $settings["items"])) foreach($temp as $k2 => $v) if ($v["str"] == "") unset($temp[$k2]);
		if (is_array($temp)) foreach(array_chunk($temp, 5) as $first1 => $row)
		{
			$cell_width = 740 / count($row) - 8;
			$name_height = 0;
			foreach($row as $v)
			{
				if ($k == "bulk_offers")
				{
					$temp = fetchBulk($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["title"];
				}
				else
				{
					$temp = fetchProduct($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["product_name"];
				}
				$name_height = max($name_height, estimate_p_height(12, $v["name"], $cell_width, true));
			}

			$style1 = "";
			if ($first1) echo dividing_bar();
?>
<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>
<?php
			foreach($row as $first2 => $v)
			{
				if ($k == "bulk_offers")
				{
					$temp = fetchBulk($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["title"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
					$price_au = fetchBulkPricing($v["str"], "AUD");
					$price_us = fetchBulkPricing($v["str"], "USD");
					$v["view_url"] = relative_url($temp["bulk_url"], $track_url);
					$v["image_url"] = relative_url($temp["thumb_url"], $base_url);
					$v["purchase_url"] = relative_url("/bulk_offers/addtocart_" . $temp["id"] . ".html", $track_url);
				}
				else
				{
					$temp = fetchProduct($v["str"]);
					if ($v["name"] == "") $v["name"] = $temp["product_name"];
					if ($v["text"] == "") $v["text"] = trim($temp["short_desc"]);
					$price_au = fetchProductPricing($v["str"], "AUD");
					$price_us = fetchProductPricing($v["str"], "USD");
					$v["view_url"] = relative_url($temp["product_url"], $track_url);
					$v["image_url"] = relative_url($temp["thumb_url"], $base_url);
					$v["purchase_url"] = relative_url("/checkout/addtocart_" . $temp["id"] . "_" . $temp["sub_id"] . ".html", $track_url);
				}
				$style2 = "";
				if ($first2) $style2 = "border-left:1px solid #ccc;";
				$style = rtrim($style1 . $style2, ";");
?>
	<td<?php echo ($style != "") ? (' style="' . $style . '"') : "" ?> width="20%" valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="4">
		<tr><th height="<?php echo ceil($name_height + 8) ?>"><h3 style="margin:0;font-size:12px"><a href="<?php hecho($v["view_url"]) ?>" style="font-weight:bold;text-decoration:none"><?php hecho($v["name"]) ?></a></h3></th></tr>
		<tr><td valign="top" align="center"><a href="<?php hecho($v["view_url"]) ?>"><img title="<?php hecho($v["name"]) ?>" src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" border="0" /></a></td></tr>
		<tr><td valign="top"><table style="border-collapse:collapse" width="100%" cellpadding="3">
			<tr>
				<td style="color:#f00;font-size:18px" colspan="3" align="center">
<negotiate_list field="country">
<negotiate_item default="default">
					<b><i>$<?php echo convertCurrency($price_us["current_price"], "USD", "USD", $temp["precision"] != 4, $temp["precision"]) ?></i></b>
</negotiate_item>
<negotiate_item value="AU">
					<b><i>$<?php echo convertCurrency($price_au["current_price"] * 1.1, "AUD", "AUD", $temp["precision"] != 4, $temp["precision"]) ?></i></b> <span style="font-size:10px;white-space:nowrap;">inc GST</span>
</negotiate_item>
</negotiate_list>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="3"><a href="<?php hecho($v["purchase_url"]) ?>"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/unit_button.png", $base_url)) ?>" alt="Buy Now!" border="0" /></a></td>
			</tr>
<?php
				if (count($temp["bulk_pricing"])) foreach($temp["bulk_pricing"] as $v)
				{
					if ($v["unit_sale_price_us"] == 0.0) $v["unit_sale_price_us"] = convertCurrency($v["unit_sale_price"], "AUD", "USD", $temp["precision"] != 4, $temp["precision"]);
					if ($v["unit_sale_price"] == 0.0) $v["unit_sale_price"] = convertCurrency($v["unit_sale_price_us"], "USD", "AUD", $temp["precision"] != 4, $temp["precision"]);
?>
			<tr style="border:1px solid #ccc">
				<td align="center"><i><?php echo intval($v["qty"]) ?> x</i></td>
				<td align="center">
<negotiate_list field="country">
<negotiate_item default="default">
				<i>$<?php echo convertCurrency($v["unit_sale_price"] / 1.1, "AUD", $temp["precision"] != 4, $temp["precision"]) ?></i>
</negotiate_item>
<negotiate_item value="AU">
				<i>$<?php echo convertCurrency($v["unit_sale_price"], "AUD", $temp["precision"] != 4, $temp["precision"]) ?></i>
</negotiate_item>
</negotiate_list>
				</td>
				<td align="center"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/checkout/addtocart_<?php echo $temp["id"] ?>_<?php echo $temp["sub_id"] ?>_<?php echo $v["qty"] ?>.html"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/bulk_button.png", $base_url)) ?>" alt="Buy" border="0" /></a></td>
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
	$packets[] = array("size" => "wide", "content" => ob_get_clean());
	break;
}
foreach($packets as $k => $v) $packets[$k]["content"] = '<div style="border:1px solid #ccc;margin:4px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:4px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px;-webkit-border-bottom-left-radius:5px;-webkit-border-bottom-right-radius:5px;-moz-border-radius-bottomleft:5px;-moz-border-radius-bottomright:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px">' . $packets[$k]["content"] . "</div>";
$prev = false;
$next = false;
foreach($packets as $k => $v)
{
	$prev = $next;
	$next = $k;
	if ($prev !== false && $packets[$prev]["size"] == "thin" && $v["size"] == "thin")
	{
		$packets[$k]["size"] = "wide";
		$packets[$k]["content"] = '<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$prev]["content"] . "</td>" . "\n\t" . '<td width="50%" valign="top">' . $packets[$next]["content"] . "</td>" . "\n" . "</tr></table>";
		unset($packets[$prev]);
		$prev = false;
	}
}

/**
 * Apply superscript to $ and decimals in prices
 */
//foreach($packets as $k => $v) $packets[$k] = fancy_currency($v);

//foreach($packets as $v) echo $v["content"] . "\n\n";
$temp = array();
foreach($packets as $v) $temp[] = $v["content"];
echo implode('<div style="height:0.5em"></div>' . "\n\n", $temp) . "\n\n";
?>

<div style="background:#eee url(<?php hecho(relative_url("/images/newsletter_" . $style_id . "/htiles.png", $base_url)) ?>) repeat-x 0px 0px;border:1px solid #ccc;color:#000;height:85px;margin:4px;position:relative">
<?php
$row = $DB->query_first("SELECT company_invoiceheader FROM highwaygroup.company_info WHERE company_id=9");
$row = explode("\n", $row["company_invoiceheader"]);
array_shift($row);
?>
	<table style="border-collapse:collapse;font-size:10px;position:absolute;right:10px;top:19px" cellpadding="0">
<?php
foreach($row as $v)
{
	$row = explode(":", $v);
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
		$row = explode(":", $v);
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
		<td height="64" width="80" valign="top" align="center"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/messages/newsletter_forward.html?id=<?php echo $item["id"] ?>&amp;from_name=[FIRST_NAME]+[LAST_NAME]&amp;from_email=[EMAIL_ADDRESS]" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/forward_button.png", $base_url)) ?>" alt="" border="0" /><br />Email Friend</a></td>
		<td height="64" width="80" valign="top" align="center"><a href="<?php hecho(relative_url("/messages/unsubscribe.html?email=[EMAIL_ADDRESS]", $track_url)) ?>" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/unsubscribe_button.png", $base_url)) ?>" alt="" border="0" /><br />Cancel your<br />Subscription</a></td>
		<td height="64" width="80" valign="top" align="center"><a href="http://<?php echo $domain_name ?>[TRACKER_KEY]/contactus.html?email=[EMAIL_ADDRESS]" target="_blank" style="color:#000;text-decoration:none"><img src="<?php hecho(relative_url("/images/newsletter_" . $style_id . "/inquire_button.png", $base_url)) ?>" alt="" border="0" /><br />Send an<br />Inquiry</a></td>
	</tr></table>
</div>

</body>
</html>
<?php
function dividing_bar()
{
	ob_start();
?>
<hr style="background:#158;height:6px;border-style:none;border-top:1px solid #579;border-bottom:1px solid #bdd" />
<?php
	return ob_get_clean();
}

function please_ask_text($text = "")
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

function dont_pay_text($text = "")
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

function mail_button($url = "#", $text = 'Coming Soon<br /><span style="font-size:22px">NOTIFY ME</span>')
{
	global $domain_name, $style_id;
	ob_start();
?>
<a target="_blank" href="<?php hecho("http://" . $domain_name . "[TRACKER_KEY]" . $url) ?>" style="text-align:center;width:130px;padding:4px 67px 4px 4px;font-size:18px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/mail_button.png) no-repeat 0px 0px scroll;height:54px"><b><i><?php echo $text ?></i></b></a>
<?php
	return ob_get_clean();
}

function small_mail_button($url = "#", $text = 'Coming Soon<br /><span style="font-size:16px">NOTIFY ME</span>')
{
	global $domain_name, $style_id;
	ob_start();
?>
<a target="_blank" href="<?php hecho("http://" . $domain_name . "[TRACKER_KEY]" . $url) ?>" style="text-align:center;width:95px;padding:5px 52px 5px 5px;font-size:13px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/small_mail_button.png) no-repeat 0px 0px scroll;height:36px"><b><i><?php echo $text ?></i></b></a>
<?php
	return ob_get_clean();
}

function buy_button($url = "#", $price = "", $currency = "")
{
	global $domain_name, $style_id;
	$currency_symbol = "";
	if ($currency != "") $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), "HTML");
	if (strlen($price) > 7  && substr($price, -3) == ".00") $price = substr($price, 0, -3);
	$font_size = min(30, 44 - strlen($price) * 2);
	ob_start();
?>
<a target="_blank" href="<?php hecho("http://" . $domain_name . "[TRACKER_KEY]" . $url) ?>" style="text-align:center;width:121px;padding:8px 73px 8px 8px;color:#fff;text-decoration:none;display:block;margin:auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/buy_button.png) no-repeat 0px 0px scroll;height:46px;vertical-align:middle"><b><i><big style="font-size:30px"><?php echo $currency_symbol . $price ?></big><br /><span style="float:right;font-size:12px"><?php echo substr($currency, 0, 3) . (($currency == "AUD") ? " exc. GST" : "") . (($currency == "AUD+") ? " inc. GST" : "") ?></span></i></b></a>
<?php
	return fancy_currency(ob_get_clean());
}

function small_buy_button($url = "#", $price = "", $currency = "")
{
	global $domain_name, $style_id;
	$currency_symbol = "";
	if ($currency != "") $currency_symbol = getCurrencySymbol(substr($currency, 0, 3), "HTML");
	if (strlen($price) > 8 && substr($price, -3) == ".00") $price = substr($price, 0, -3);
	$font_size = min(22, 54 - strlen($price) * 4);
	ob_start();
?>
<a target="_blank" href="<?php hecho("http://" . $domain_name . "[TRACKER_KEY]" . $url) ?>" style="text-align:center;width:92px;padding:6px 54px 6px 6px;color:#fff;text-decoration:none;display:block;margin:0 auto;background:transparent url(http://<?php echo $domain_name ?>/images/newsletter_<?php echo $style_id ?>/small_buy_button.png) no-repeat 0px 0px scroll;height:34px"><b><i><big style="font-size:<?php echo $font_size ?>px"><?php echo $currency_symbol . $price ?></big><br /><span style="float:right;font-size:10px"><?php echo substr($currency, 0, 3) . (($currency == "AUD") ? " exc. GST" : "") . (($currency == "AUD+") ? " inc. GST" : "") ?></span></i></b></a>
<?php
	return fancy_currency(ob_get_clean());
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
	$temp["bulk_pricing"] = productBulkPricingFromDatabase($id);

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

function fetchBulk($id)
{
	/**
	 * Fields needed:
	 * Y title
	 * Y thumb_url
	 * Y short_desc
	 * Y saving
	 * N product_offerus
	 * N price_online
	 * Y precision
	 * Y image_180_url
	 * Y id
	 * N final_price
	 * Y bulk_url
	 * Y bulk_pricing
	 */
	$temp = bulkFromDatabase($id);
	$temp["bulk_pricing"] = array();
	$temp["precision"] = 2;

	$temp["price_online"] = ($temp["status"] == "y") ? "y" : "n";
	$other = fetchBulkPricing($id, "AUD");
	$temp["saving"] = $other["savings"];
	$temp["product_offerus"] = $other["normal_price"];
	$temp["final_price"] = $other["current_price"];

	if ($temp["final_price"] <= 0) $temp["price_online"] = "n";
	if ($temp["price_online"] == "n") $temp["special"] = "n";
	if ($temp["image_url"] == "") $temp["image_url"] = "/new_images/noimage.gif";
	if ($temp["thumb_url"] == "") $temp["thumb_url"] = "/new_images/noimage_small.gif";

	return $temp;
}

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

function fetchBulkPricing($bulk_id, $wanted_currency = "USD")
{
	$bulk = bulkFromDatabase($bulk_id);

	$primary_price = convertCurrency($bulk["bulk_offer"] / (($bulk["bulk_currency"] == "AUD") ? 1.1 : 1.0), $bulk["bulk_currency"], "", true);
	$primary_currency = $bulk["bulk_currency"];

	$normal_price = convertCurrency($bulk["bulk_online"], $bulk["bulk_currency"], "", true);
	$normal_currency = $bulk["bulk_currency"];

	$secondary_price = $primary_price;
	$secondary_currency = $primary_currency;

	$current_price = convertCurrency($secondary_price, $secondary_currency, $wanted_currency, true);
	$current_currency = $wanted_currency;

	$savings = 0.0;
	if ($current_price != 0.0 && $normal_price != 0.0) $savings = ceil(($normal_price - $current_price) * 100.0 / $normal_price);
	//$savings = ceil(100.0 * ($normal_price / $current_price) - 100.0);

	$result = array();
	$result["primary_price"] = $primary_price;
	$result["primary_currency"] = $primary_currency;
	$result["normal_price"] = $normal_price;
	$result["normal_currency"] = $normal_currency;
	$result["secondary_price"] = $secondary_price;
	$result["secondary_currency"] = $secondary_currency;
	$result["current_price"] = $current_price;
	$result["current_currency"] = $current_currency;
	$result["savings"] = $savings;
	return $result;
}

function estimate_p_height($font_size, $text, $width = 256, $bold = false)
{
	$font_size = floatval($font_size * 0.7656247);
	$font_file = "/home/common/fonts/Arial.ttf";
	if ($bold) $font_file = "/home/common/fonts/Arial Bold.ttf";
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
			$test = trim(implode(" ", $test));
			$test = imagettfbbox($font_size, $font_angle, $font_file, $test);
			if ((max($test[2], $test[4]) - min($test[0], $test[6])) > $width)
			{
				$result_text[] = implode(" ", $line);
				$chars = "";
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
		if (count($line)) $result_text[] = implode(" ", $line);
	}

	$rawr = "";
	for($i = 32;$i < 255;$i++) $rawr .= chr($i);
	$test = imagettfbbox($font_size, $font_angle, $font_file, $rawr . implode("\n", $result_text) . $rawr);
	return 0.945390 * max($test[1], $test[3]) - min($test[5], $test[7]);
}

function fancy_currency($html)
{
	return preg_replace('/\\$([0-9]+)\\.([0-9]+)/', '<sup style="vertical-align:text-top">\\$</sup>$1.<sup style="vertical-align:text-top">$2</sup>', $html);
}
function formatHTMLDescription3($text)
{
	return str_replace("<li>", '<li style="font-size:12px">', formatHTMLDescription2($text));
}
?>