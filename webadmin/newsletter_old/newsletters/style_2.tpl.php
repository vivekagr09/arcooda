<?php
/**
 * Coinop Express Newsletter Template
 *
 * @var mixed[] $item          The newsletter settings
 * @var mixed[] $item_settings A list of settings
 * @var mixed[] $sections
 */

moduleLoad('product'); //productFromDatabase

/**
 * Re-organise the settings for the template
 */
$style_id = 2;
include __DIR__ . "/common_" . $style_id . ".inc.php";

moduleLoad('banner');

global $_GLOBALS;

$domain_name = "www.coinopexpress.com";
$base_url    = 'https://www.coinopexpress.com';
$track_url   = 'https://www.coinopexpress.com[TRACKER_KEY]';

if (!function_exists("newsFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[]
     */
    function newsFromDatabase($id)
    {
        global $DB;
        $row                  = $DB->query_first("SELECT * FROM `news` WHERE `news_id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        $row['image_180_url'] = sprintf(
            'https://static.coinopexpress.com/images/news/large/%d.jpg',
            $row['news_id']
        );
        $row['thumb_url'] = sprintf(
            'https://static.coinopexpress.com/images/news/thumb/%d.jpg',
            $row['news_id']
        );
        $row['image_url'] = sprintf(
            'https://static.coinopexpress.com/images/news/large/%d.jpg',
            $row['news_id']
        );
        $row["news_url"] = "/news/" . $row["news_id"] . ".html";
        return $row;
    }
}
if (!function_exists("formatHTMLDescription")) {
    /**
     * @param string $intext
     *
     * @return string
     */
    function formatHTMLDescription($intext)
    {
        $string = $intext;
        $string = stripcslashes($string);
        $string = stripcslashes($string);
        $string = stripcslashes($string);
        $string = stripcslashes($string);
        $string = str_replace("\r\n", "\n", $string);
        $string = str_replace("\n\r", "\n", $string);
        $string = str_replace("\r", "\n", $string);
        $string = html_entity_decode($string, ENT_QUOTES, "UTF-8");
        $string = str_replace('’', "'", $string);
        $string = str_replace('–', '-', $string);
        if (mb_check_encoding($string, "CP1252")) {
            $temp = mb_convert_encoding($string, "CP1252", "UTF-8");
            if (utf8ToUnicode($temp) !== false) {
                $string = $temp;
            }

        }
        $string = str_replace("<br>", "<br />", $string);
        $string = str_replace("<BR>", "<br />", $string);
        $string = str_replace("<br >", "<br />", $string);
        $string = str_replace("<br/>", "<br />", $string);
        //$string = str_replace("<br />\n", "\n", $string);
        //$string = str_replace("<br />", "\n", $string);
        $string = str_replace("<hr>", "<br />", $string);
        $string = str_replace("<HR>", "<br />", $string);
        $string = str_replace("<hr >", "<br />", $string);
        $string = str_replace("<hr/>", "<br />", $string);
        //$string = str_replace("<html>", "", $string);
        //$string = str_replace("<body>", "", $string);
        //$string = str_replace("</html>", "", $string);
        //$string = str_replace("</body>", "", $string);
        $string = str_replace("<center>", '<span style="text-align:center">', $string);
        $string = str_replace("</center>", '</span>', $string);
        $string = str_replace("<C>", '<span style="text-align:center">', $string);
        $string = str_replace("</C>", '</span>', $string);
        $string = preg_replace('/<font[^>]+>([^<]+)<\\/font>/i', '$1', $string);
        $string = preg_replace('/<mailto:[^>]+@[^>]+>/i', '', $string);
        //$string = preg_replace('/<sup>([^<]+)<\\/sup\\/>/i', '$1', $string);
        $string = preg_replace('/(<\\/tr>)[^<]+(<tr[^>]*>)/i', '$1' . "\n" . '$2', $string);
        $string = preg_replace('/(<\\/td>)[^<]+(<td[^>]*>)/i', '$1' . "\t" . '$2', $string);
        $string = preg_replace('/(<\\/td>)[^<]+(<\\/tr>)/i', '$1$2', $string);
        $string = preg_replace('/(<tr[^>]*>)[^<]+(<td[^>]*>)/i', '$1$2', $string);
        $string = preg_replace('/(<\\/tr>)[^<]+(<\\/table>)/i', '$1$2', $string);
        $string = preg_replace('/(<table[^>]*>)[^<]+(<tr[^>]*>)/i', '$1$2', $string);
        $string = preg_replace('/(<img[^>]+[^\\/>])>/', '$1 />', $string);
        $string = preg_replace('/  +/', ' ', $string);
        $string = trim($string);
        $string = preg_replace("/[ \\t]+\\n/", "\n", $string);
        $string = preg_replace("/\\n\\n\\n+/", "\n\n", $string);
        //$string = preg_replace_callback("/( href=\")([^\"]+)(\")/", "escape_href", $string);
        //$string = preg_replace_callback("/( src=\")([^\"]+)(\")/", "escape_href", $string);
        //$string = preg_replace_callback("/s:([0-9]+):\"([^\"]*)\";/", "escape_serialized", $string);
        $intext = $string;

        $outtext = array();
        $intext  = explode("\n", preg_replace("/\\<[bB][rR][ \\t]?[\\/]?\\>/", "\n", $intext));
        $depth   = 0;
        foreach ($intext as $line) {
            $line = trim($line);
            switch ($depth) {
                case 0:
                    if (substr($line, 0, 1) == '-') {
                        $depth++;
                        $outtext[] = '<ul>';
                        $outtext[] = '<li>' . trim(substr($line, 1)) . '</li>';
                    } else {
                        $outtext[] = $line . "<br />";
                    }

                    break;
                case 1:
                    if (substr($line, 0, 1) != '-') {
                        $depth--;
                        $outtext[] = '</ul>';
                        $outtext[] = $line . "<br />";
                    } else {
                        $outtext[] = '<li>' . trim(substr($line, 1)) . '</li>';
                    }

                    break;
            }
        }
        while ($depth > 0) {
            $depth--;
            $outtext[] = '</ul>';
        }
        return implode("\n", $outtext);
    }
}

ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php hecho($item["subject"])?>  - <?php echo date("D, F d, Y", $item["time_added"]) ?></title>
<link href="<?php hecho(relative_url('/webfont/MisterEarlBTRegular/font.css', $base_url))?>" rel="stylesheet" type="text/css" />
<style type="text/css">
ul	{ padding:0; margin:0; list-style-position:inside; }
ul li	{ padding:5px 0; margin:0; }
</style>
</head>
<body style="padding:0;margin:0;font-family:sans-serif;font-size:12px;"><table style="margin:24px auto;width:788px;border-collapse:collapse" cellpadding="0">

<?php
//-----------------------------------------------------------------------------
?>
<thead><tr><td style="background:#fff;color:#000">
	<a href="<?php hecho(relative_url('/', $track_url))?>" title="<?php hecho($_GLOBALS["site_options"]["META_TITLE"])?>" target="_blank"><img border="0" src="<?php hecho(relative_url('/img_coe/coinopexpress_largerlogo.png', $track_url))?>" alt="Coinop Express" /></a>
<?php
foreach ($item_settings as $k => $settings) {
    if ("y" == $settings["enable"]) {
        switch ($k) {
            case "banner":
                $temp = $settings["items"];
                foreach ($temp as $v) {
                    $temp           = bannerFromDatabase($v["str"]);
                    $v["view_url"]  = relative_url($temp['click_url'], $track_url);
                    $v["image_url"] = relative_url($temp["image_url"], $base_url);
?>
	<a href="<?php hecho($v["view_url"])?>" target="_blank" style="float:right;"><img border="0" src="<?php hecho($v["image_url"])?>" alt="<?php hecho($v["name"])?>" /></a>
<?php
}
                break;
        }
    }
}

?>
</td></tr>
<tr><td>
<?php
if ('' != $item_settings['paragraph']['value'] && formatCleanString($item_settings['paragraph']['value']) != formatCleanString($sections['paragraph']['defaults']['value'])) {
?>
<br />
<p style="padding:16px;margin:0 4px 4px;font-size:12px"><?php echo formatHTMLDescription($item_settings['paragraph']['value']) ?></p>
<?php
}
?>
	<br />
</td></tr></thead>
<?php
//-----------------------------------------------------------------------------
?>
<tfoot><tr><td style="background:#eee;color:#666;position:relative">
<?php
foreach ($item_settings as $k => $settings) {
    if ("y" == $settings["enable"]) {
        switch ($k) {
            case "wanted":
                $temp = $settings["items"];
                switch ($settings["option"]) {
                    default:
                        break;

                    case "footer":
?>
	<h2 style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat top left;color:#fd0;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:39px;font-weight:normal;text-transform:uppercase;margin:0 -9px 14px -12px;padding:0 18px;box-shadow:0px 14px 16px -8px #666;text-shadow:3px 3px 1px rgba(0, 0, 0, 0.5)"><?php hecho($sections[$k]["title"])?></h2>
	<table><tr>
<?php
foreach (array_chunk($temp, (count($temp) > 8) ? (count($temp) / 2 + (count($temp) & 1)) : count($temp)) as $temp) {
?>
		<td style="font-family:sans-serif/*;font-size:14px*/"><ul style="margin:0;padding:0 8px 16px 16px;list-style-position:inside">
<?php
if (is_array($temp)) {
                                foreach ($temp as $v) {
                                    $v["view_url"] = relative_url('/wanted_offers.html?data%5Bname%5D=[NAME_URL_ENCODED]&data%5Bemail%5D=[EMAIL_URL_ENCODED]&wanted_item=' . $v["str"], $track_url);
?>
				<li style="margin:0;padding:0;font-style:italic"><a href="<?php hecho($v["view_url"])?>" target="_blank" style="color:inherit;text-decoration:none;"><?php hecho($v["name"])?></a></li>
<?php
}
                            }

?>
		</ul></td>
<?php
}
?>
	</tr></table>
<?php
break;
                }
                break;
        }
    }
}

if ("y" != $item_settings["wanted"]["enable"] || "footer" != $item_settings["wanted"]["option"]) {
?>
	<img src="<?php hecho(relative_url('/images/newsletter/ft_h2.png', $base_url))?>" alt="Coinopexpress" /><br />
	<br />
	<table style="font-family:sans-serif/*;font-size:12px;*/">
		<tr>
			<th style="text-align:left;padding:1px 32px;">Address</th>
			<td>44 Maitland Rd</td>
			<th style="text-align:left;padding:1px 32px;">Phone</th>
			<td>(+61) 02 4968 9313</td>
		</tr>
		<tr>
			<td></td>
			<td>(Downstairs)</td>
			<th style="text-align:left;padding:1px 32px;">Fax</th>
			<td>(+61) 02 4968 9314</td>
		</tr>
		<tr>
			<td></td>
			<td>Mayfield East, NSW 2304</td>
			<th style="text-align:left;padding:1px 32px;">Web</th>
			<td>www.arcooda.com</td>
		</tr>
		<tr>
			<td></td>
			<td>Australia</td>
			<th style="text-align:left;padding:1px 32px;">Email</th>
			<td>sales@arcooda.com</td>
		</tr>
	</table>
<?php
/*
?>
<a target="_blank" href="https://hc2.humanclick.com/hc/16891471/?cmd=file&file=visitorWantsToChat&site=16891471&byhref=1" style="right:6px;top:26px;position:absolute"><img border="0" src="<?php hecho(relative_url('/images/newsletter/ft_representative2.png', $base_url)) ?>" alt="Contact Us" /></a>
<?php
 */
} else {
/*
?>
<a target="_blank" href="https://hc2.humanclick.com/hc/16891471/?cmd=file&file=visitorWantsToChat&site=16891471&byhref=1" style="right:6px;top:80px;position:absolute"><img border="0" src="<?php hecho(relative_url('/images/newsletter/ft_representative2.png', $base_url)) ?>" alt="Contact Us" /></a>
<?php
 */
}
?>
	<div style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat bottom left;color:#fff;margin-top:16px;height:44px;position:relative">
<?php
if ("y" == $item_settings["wanted"]["enable"] && "footer" == $item_settings["wanted"]["option"]) {
?>
		<a target="_blank" href="<?php hecho(relative_url('/request_wanted.html?data%5Bname%5D=[NAME_URL_ENCODED]&data%5Bemail%5D=[EMAIL_URL_ENCODED]', $track_url))?>" style="right:343px;top:-16px;position:absolute"><img border="0" src="<?php hecho(relative_url('/images/newsletter/send_your_offer.png', $base_url))?>" alt="Send Your Offer" /></a>
<?php
}
?>
		<a target="_blank" href="#" onclick="window.print();return false" style="right:183px;top:-16px;position:absolute"><img border="0" src="<?php hecho(relative_url('/images/newsletter/print_newsletter.png', $base_url))?>" alt="Print Newsletter" /></a>
		<a target="_blank" href="<?php hecho(relative_url('/messages/unsubscribe.html?email=[EMAIL_URL_ENCODED]', $track_url))?>" style="right:23px;top:-16px;position:absolute"><img border="0" src="<?php hecho(relative_url('/images/newsletter/unsubscribe.png', $base_url))?>" alt="Unsubscribe" /></a>
	</div>
</td></tr></tfoot>
<?php
//-----------------------------------------------------------------------------
?>
<tbody>

<?php
foreach ($item_settings as $k => $settings) {
    if ("y" == $settings["enable"]) {
        switch ($k) {
            case "text_offers":
                if (is_array($temp = $settings["items"])) {
                    //foreach($temp as $k2 => $v) if ($v["str"] == "") unset($temp[$k2]);
                    if (count($temp)) {
?>
<tr><td style="background:#eee;color:#666">
	<h2 style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat top left;color:#fd0;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:39px;font-weight:normal;text-transform:uppercase;margin:0 -9px 14px -12px;padding:0 18px;box-shadow:0px 14px 16px -8px #666;text-shadow:3px 3px 1px rgba(0, 0, 0, 0.5)"><?php hecho($sections[$k]["title"])?></h2>
	<div style="padding:0 5px 16px"><table style="border-collapse:separate;width:100%" cellspacing="10" cellpadding="13">
<?php
foreach (array_chunk($temp, 3) as $row) {
?>
		<tr>
<?php
$row_height = 0;
                            foreach ($row as $v) {
                                $row_height = max($row_height, intval(strlen($v["name"]) / 25));
                            }

                            foreach ($row as $v) {
                                $cell_height = intval(strlen($v["name"]) / 25);

                                $inquiry_url = relative_url('/special_offers.html?name=[NAME_URL_ENCODED]&email=[EMAIL_URL_ENCODED]&subject=' . urlencode($v["name"]), $track_url);
?>
			<td style="background:#fff;color:#000;font-family:sans-serif;/*font-size:12px;*/font-style:italic;border:1px solid #bbb;vertical-align:top;width:33%;border-radius:5px;box-shadow:0 0 3px 3px #bbb">
				<h3 style="background:#eee url(<?php hecho(relative_url('/images/newsletter/h3.png', $base_url))?>) repeat top left;color:#d44;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:26px;font-style:normal;font-weight:normal;border:1px solid #bbb;margin:0 -17px 16px;padding:2px 0 5px;text-align:center;box-shadow:0 3px 3px 0 #bbb"><?php hecho($v["name"])?><?php echo str_repeat("<br />", 1 + $row_height - $cell_height) ?></h3>
				<?php echo formatHTMLDescription($v["text"]) ?>
				<br />
				<div style="text-align:center;"><a href="<?php hecho($inquiry_url)?>" onclick="<?php hecho("window.open(" . jecho($inquiry_url, 1) . ", " . jecho("special_offer", 1) . ", " . jecho("width=500,height=450,scrollbars=yes", 1) . ");return false")?>"><img src="<?php hecho(relative_url('/images/newsletter/inquire_now.png', $base_url))?>" alt="Inquire Now &gt;" /></a></div>
			</td>
<?php
}
?>
		</tr>
<?php
}
?>
	</table></div>
</td></tr>
<?php
}
                }

                break;

            case "featured_products":
            case "promotion_products":
                if (is_array($temp = $settings["items"])) {
                    foreach ($temp as $k2 => $v) {
                        if ("" == $v["str"]) {
                            unset($temp[$k2]);
                        }
                    }
                }

                if (count($temp)) {
?>
<tr><td style="background:#eee;color:#666">
	<h2 style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat top left;color:#fd0;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:39px;font-weight:normal;text-transform:uppercase;margin:0 -9px 14px -12px;padding:0 18px;box-shadow:0px 14px 16px -8px #666;text-shadow:3px 3px 1px rgba(0, 0, 0, 0.5)"><?php hecho($sections[$k]["title"])?></h2>
	<div style="padding:0 5px 16px">
<?php
switch ($settings["option"]) {
                        default:
                            echo "Todo: " . $k . " -> " . $settings["option"] . "<br />\n";
                            break;

                        case "brief":
?>
		<div style="background:#fff;color:#888;border:1px solid #bbb;margin:10px;border-radius:15px;box-shadow:1px 1px 3px 3px #bbb"><table style="border-collapse:separate;width:100%" cellspacing="10" cellpadding="0">
<?php
foreach (array_chunk($temp, 4) as $row) {
?>
			<tr>
<?php
foreach ($row as $v) {
                                    $temp = productFromDatabase($v["str"]);
                                    if ("" == $v["name"]) {
                                        $v["name"] = $temp["product_name"];
                                    }

                                    if ("" == $v["text"]) {
                                        $v["text"] = $temp["short_desc"];
                                    }

                                    $v["view_url"]  = relative_url($temp["product_url"], $track_url);
                                    $v["thumb_url"] = relative_url($temp["thumb_url"], $base_url);
                                    $v["image_url"] = relative_url($temp["image_url"], $base_url);

                                    $v["normal_currency"]    = "USD";
                                    $v["normal_price"]       = convertCurrency($temp["primary_price"], $temp["primary_currency"], "USD");
                                    $v["effective_currency"] = "USD";
                                    $v["effective_price"]    = convertCurrency($temp["secondary_price"], $temp["secondary_currency"], "USD");
                                    $v["effective_savings"]  = bcsub($v["normal_price"], $v["effective_price"], 2);

                                    $percent = "0.00";
                                    if (bccomp($v["normal_price"], "0.00", 2) != 0) {
                                        $percent = bcdiv($v["effective_savings"], $v["normal_price"], 2);
                                    }

?>
				<td style="background:#fff;color:#888;font-family:sans-serif;/*font-size:12px;*/border-bottom:1px solid #888;border-right:1px solid #888;vertical-align:top;width:25%;box-shadow:4px 4px 4px -2px #888">
					<div style="background:#999;color:#fff;text-align:right;padding:2px 4px"><?php printf("ID #%06d", $v["str"])?></div>
					<div style="height:28px;float:right"></div>
					<h3 style="font-size:12px;font-weight:bold;margin:0;padding:0;text-align:center"><a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#000;text-decoration:none"><?php hecho($v["name"])?></a></h3>
					<div style="clear:both"></div>
					<div style="text-align:right;padding:4px">
						<a target="_blank" href="<?php hecho($v["view_url"])?>" title="<?php hecho($v["name"])?>" style="float:left;margin:0"><img border="0" src="<?php hecho($v["thumb_url"])?>" alt="" /></a>
						<br />
<?php
if ("n" != $temp["price_online"] && bccomp($v["effective_price"], "0.00", 2) != 0) {
                                        if (false && bccomp($percent, "0.01", 2) >= 0) {
?>
						<s style="color:#888;font-size:21px">$<span style="font-size:27px"><?php hecho($v["normal_price"])?></span>USD</s><br />
<?php
}
?>
						<b style="color:#c05;font-size:20px;text-shadow:1px 1px 2px rgba(0, 0, 0, 0.5)"><?php hecho($v["effective_price"])?></b><br />
						<b style="color:#c05;font-size:9px;text-shadow:1px 1px 1px rgba(0, 0, 0, 0.5)">USD</b>
<?php
} else {
?>
						<br />
						<div style="text-align:center;"><b style="color:#c05;font-size:9px;text-shadow:1px 1px 2px rgba(0, 0, 0, 0.5)">Please Ask</b></div>
<?php
}
?>
						<br style="clear:both" />
					</div>
				</td>
<?php
}
?>
			</tr>
<?php
}
?>
		</table></div>
<?php
break;

                        case "full":
?>
		<div style="padding:0 0"><table style="border-collapse:separate;width:100%" cellspacing="15" cellpadding="13">
<?php
foreach (array_chunk($temp, 1) as $row) {
?>
			<tr>
<?php
foreach ($row as $v) {
                                    $temp = productFromDatabase($v["str"]);
                                    if ("" == $v["name"]) {
                                        $v["name"] = $temp["product_name"];
                                    }

                                    if ("" == $v["text"]) {
                                        $v["text"] = $temp["short_desc"];
                                    }

                                    $v["view_url"]  = relative_url($temp["product_url"], $track_url);
                                    $v["thumb_url"] = relative_url($temp["thumb_url"], $base_url);
                                    $v["image_url"] = relative_url($temp["image_url"], $base_url);
                                    $tabs           = array("clearance", "hot_price", "special", "new_release");

                                    $v["normal_currency"]    = "USD";
                                    $v["normal_price"]       = convertCurrency($temp["primary_price"], $temp["primary_currency"], "USD");
                                    $v["effective_currency"] = "USD";
                                    $v["effective_price"]    = convertCurrency($temp["secondary_price"], $temp["secondary_currency"], "USD");
                                    $v["effective_savings"]  = bcsub($v["normal_price"], $v["effective_price"], 2);

                                    $percent = "0.00";
                                    if (bccomp($v["normal_price"], "0.00", 2) != 0) {
                                        $percent = bcdiv($v["effective_savings"], $v["normal_price"], 2);
                                    }

?>
				<td style="background:#fff;color:#888;font-family:sans-serif;/*font-size:14px;*/border:1px solid #bbb;margin:6px;border-radius:15px;box-shadow:1px 1px 3px 3px #bbb">
<?php
if (in_array($v["ribbon_label"], $tabs)) {
?>
					<div style="position:relative;"><img border="0" src="<?php hecho(relative_url('/images/newsletter/tab_' . $v["ribbon_label"] . '.png', $base_url))?>" alt="" style="position:absolute;top:-24px;right:-23px" /></div>
<?php
$step = 21;
                                        for ($x = 0; $x < 154; $x += $step) {
                                            echo '<div style="float:right;height:' . (154 - $x) . 'px;width:' . $step . 'px"></div>';
                                        }

                                    }
?>
					<a target="_blank" href="<?php hecho($v["view_url"])?>" style="float:left;margin:0 96px 8px 0" title="<?php hecho($v["name"])?>"><img border="0" src="<?php hecho($v["image_url"])?>" alt="" /></a>
					<h3 style="font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:33px;font-weight:normal;margin:0 0 14px 0;padding:0"><a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#00f;text-decoration:none"><?php hecho($v["name"])?></a></h3>
					<i>
						<?php echo formatHTMLDescription($v["text"]) ?>
<?php
if ("n" != $temp["price_online"] && bccomp($v["effective_price"], "0.00", 2) != 0) {
                                        if (bccomp($percent, "0.01", 2) >= 0) {
?>
						<br />
						<s style="color:#888;font-size:21px">$<span style="font-size:27px"><?php hecho($v["normal_price"])?></span>USD</s>
<?php
}
?>
						<br />
						<a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#25b;text-decoration:none"><b style="font-size:26px">$</b><b style="font-size:38px"><?php hecho($v["effective_price"])?></b><b style="font-size:26px">USD</b></a>
<?php
} else {
?>
						<br />
						<a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#25b;text-decoration:none"><b style="font-size:26px">$</b> <b style="font-size:38px">Please Ask</b> <b style="font-size:26px">USD</b></a>
<?php
}
?>
					</i>
					<br style="clear:both" />
				</td>
<?php
}
?>
			</tr>
<?php
}
?>
		</table></div>
<?php
break;

                        case "twin":
?>
		<div style="padding:0 0"><table style="border-collapse:separate;width:100%" cellspacing="15" cellpadding="13">
<?php
foreach (array_chunk($temp, 2) as $row) {
?>
			<tr>
<?php
foreach ($row as $v) {
                                    $temp = productFromDatabase($v["str"]);
                                    if ("" == $v["name"]) {
                                        $v["name"] = $temp["product_name"];
                                    }

                                    if ("" == $v["text"]) {
                                        $v["text"] = $temp["short_desc"];
                                    }

                                    $v["view_url"]  = relative_url($temp["product_url"], $track_url);
                                    $v["thumb_url"] = relative_url($temp["thumb_url"], $base_url);
                                    $v["image_url"] = relative_url($temp["image_url"], $base_url);
                                    $tabs           = array("clearance", "hot_price", "special", "new_release");

                                    $v["normal_currency"]    = "USD";
                                    $v["normal_price"]       = convertCurrency($temp["primary_price"], $temp["primary_currency"], "USD");
                                    $v["effective_currency"] = "USD";
                                    $v["effective_price"]    = convertCurrency($temp["secondary_price"], $temp["secondary_currency"], "USD");
                                    $v["effective_savings"]  = bcsub($v["normal_price"], $v["effective_price"], 2);

                                    $bracket = bcdiv($v["effective_savings"], "1000.00", 0);

                                    $percent = "0.00";
                                    if (bccomp($v["normal_price"], "0.00", 2) != 0) {
                                        $percent = bcdiv($v["effective_savings"], $v["normal_price"], 2);
                                    }

?>
				<td style="background:#fff;color:#888;font-family:sans-serif;/*font-size:18px;*/border:1px solid #bbb;margin:6px;border-radius:15px;vertical-align:top;width:50%;box-shadow:1px 1px 3px 3px #bbb">
<?php
if (in_array($v["ribbon_label"], $tabs)) {
?>
					<div style="position:relative;"><img border="0" src="<?php hecho(relative_url('/images/newsletter/tab_' . $v["ribbon_label"] . '.png', $base_url))?>" alt="" style="position:absolute;top:-24px;right:-23px" /></div>
<?php
$step = 19;
                                        for ($x = 0; $x < 152/*152*/; $x += $step) {
                                            echo '<div style="float:right;height:' . (152/*152*/ - $x) . 'px;width:' . $step . 'px"></div>';
                                        }

                                    }
?>
					<h3 style="font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:33px;font-weight:normal;margin:0 0 14px 0;padding:0"><a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#00f;text-decoration:none"><?php hecho($v["name"])?></a></h3>
					<div style="text-align:center;">
						<a target="_blank" href="<?php hecho($v["view_url"])?>" style="float:left;margin:0 8px 8px 0" title="<?php hecho($v["name"])?>"><img border="0" src="<?php hecho($v["thumb_url"])?>" alt="" /></a>
						<br />
						<i>
<?php
if ("n" != $temp["price_online"] && bccomp($v["effective_price"], "0.00", 2) != 0) {
                                        if (bccomp($bracket, '0', 0) > 0) {
?>
							<b style="color:#b23;/*font-size:14px;*/text-transform:uppercase">Save Over<br />$<?php echo bcmul($bracket, "1000", 0) ?></b><br />
<?php
} elseif (bccomp($percent, "0.01", 2) >= 0) {
?>
							<s style="color:#b23;font-size:14px">$<?php hecho($v["normal_price"])?></s><br />
<?php
} else {
?>
							<br />
<?php
}
?>
							<a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#25b;text-decoration:none"><b style="font-size:14px">$</b><b style="font-size:23px"><?php hecho(preg_replace("/([0-9])([0-9]{3}[\.,])/", '$1,$2', $v["effective_price"]))?></b><b style="font-size:14px">USD</b></a>
<?php
} else {
?>
							<a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#25b;text-decoration:none"><b style="font-size:14px">$</b> <b style="font-size:23px">Please Ask</b> <b style="font-size:14px">USD</b></a>
<?php
}
?>
						</i><br />
						<br style="clear:left;" />
					</div>
					<i><?php echo formatHTMLDescription($v["text"]) ?></i>
				</td>
<?php
}
?>
			</tr>
<?php
}
?>
		</table></div>
<?php
break;
                    }
?>
	</div>
</td></tr>
<?php
}
                break;

            case "news":
                if (is_array($temp = $settings["items"])) {
                    foreach ($temp as $k2 => $v) {
                        if ("" == $v["str"]) {
                            unset($temp[$k2]);
                        }
                    }
                }

                if (count($temp)) {
?>
<tr><td style="background:#eee;color:#666">
	<h2 style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat top left;color:#fd0;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:39px;font-weight:normal;text-transform:uppercase;margin:0 -9px 14px -12px;padding:0 18px;box-shadow:0px 14px 16px -8px #666;text-shadow:3px 3px 1px rgba(0, 0, 0, 0.5)"><?php hecho($sections[$k]["title"])?></h2>
	<div style="padding:0 5px 16px">
<?php
switch ($settings["option"]) {
                        default:
                            echo "Todo: " . $k . " -> " . $settings["option"] . "<br />\n";
                            break;

                        case "twin":
?>
		<div style="background:#fff;color:#888;border:1px solid #bbb;margin:10px;border-radius:15px;box-shadow:1px 1px 3px 3px #bbb"><table style="border-collapse:separate;width:100%" cellspacing="10" cellpadding="13">
<?php
foreach (array_chunk($temp, 2) as $row) {
?>
			<tr>
<?php
foreach ($row as $v) {
                                    $temp = newsFromDatabase($v["str"]);
                                    if ("" == $v["name"]) {
                                        $v["name"] = $temp["news_title"];
                                    }

                                    if ("" == $v["text"]) {
                                        $v["text"] = $temp["long_description"];
                                    }

                                    $v["view_url"]  = relative_url($temp["news_url"], $track_url);
                                    $v["thumb_url"] = relative_url($temp["thumb_url"], $base_url);
                                    $v["image_url"] = relative_url($temp["image_url"], $base_url);
                                    if ("" != $temp["image_180_url"]) {
                                        $v["thumb_url"] = relative_url($temp["image_180_url"], $base_url);
                                    }

?>
			<td style="background:#fff;color:#888;font-family:sans-serif;/*font-size:10px;*/border-bottom:1px solid #888;border-right:1px solid #888;vertical-align:top;width:50%;box-shadow:4px 4px 4px -2px #888">
				<h3 style="color:#00f;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:22px;font-weight:normal;margin:0 0 14px 0;padding:0;"><a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#00f;text-decoration:none"><?php hecho($v["name"])?></a></h3>
				<div style="text-align:center;margin:0 0 16px 0;">
					<a target="_blank" href="<?php hecho($v["view_url"])?>" title="<?php hecho($v["name"])?>"><img border="0" src="<?php hecho($v["thumb_url"])?>" alt="" style="background:#fff;border-bottom:1px solid #bbb;border-right:1px solid #bbb;padding:8px;box-shadow:4px 4px 16px -2px #888;" /></a><br />
				</div>
				<?php echo formatHTMLDescription($v["text"]) ?>
				<br style="clear:both" />
			</td>
<?php
}
?>
		</tr>
<?php
}
?>
		</table></div>
<?php
break;
                        case "full":
?>
		<div style="background:#fff;color:#888;border:1px solid #bbb;margin:10px;border-radius:15px;box-shadow:1px 1px 3px 3px #bbb"><table style="border-collapse:separate;width:100%" cellspacing="10" cellpadding="13">
<?php
foreach ($temp as $v) {
                                $temp = newsFromDatabase($v["str"]);
                                if ("" == $v["name"]) {
                                    $v["name"] = $temp["news_title"];
                                }

                                if ("" == $v["text"]) {
                                    $v["text"] = $temp["long_description"];
                                }

                                $v["view_url"]  = relative_url($temp["news_url"], $track_url);
                                $v["thumb_url"] = relative_url($temp["thumb_url"], $base_url);
                                $v["image_url"] = relative_url($temp["image_url"], $base_url);
                                if ("" != $temp["image_180_url"]) {
                                    $v["thumb_url"] = relative_url($temp["image_180_url"], $base_url);
                                }

?>
			<tr><td style="background:#fff;color:#888;font-family:sans-serif;/*font-size:10px;*/border-bottom:1px solid #888;border-right:1px solid #888;vertical-align:top;width:50%;box-shadow:4px 4px 4px -2px #888">
<?php
if ($temp['news_id']) {
?>
				<div style="float:left;margin:0 32px 32px 0;text-align:center;width:250px;height:250px;text-align:center;">
					<a target="_blank" href="<?php hecho($v["view_url"])?>" title="<?php hecho($v["name"])?>"><img border="0" src="<?php hecho($v["image_url"])?>" alt="" style="background:#fff;border-bottom:1px solid #bbb;border-right:1px solid #bbb;padding:8px;box-shadow:4px 4px 16px -2px #888;max-width:250px;max-height:250px;" /></a>
				</div>
				<h3 style="color:#00f;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:22px;font-weight:normal;margin:0 0 14px 0;padding:0;"><a target="_blank" href="<?php hecho($v["view_url"])?>" style="color:#00f;text-decoration:none"><?php hecho($v["name"])?></a></h3>
<?php
} else {
?>
				<h3 style="color:#00f;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:22px;font-weight:normal;margin:0 0 14px 0;padding:0;"><span style="color:#00f;text-decoration:none"><?php hecho($v["name"])?></span></h3>
<?php
}
?>
				<?php echo formatHTMLDescription($v["text"]) ?>
				<br style="clear:both" />
			</td></tr>
<?php
}
?>
		</table></div>
<?php
break;
                    }
?>
	</div>
</td></tr>
<?php
}
                break;

            case "wanted":
                $temp = $settings["items"];
                switch ($settings["option"]) {
                    default:
                        echo "Todo: " . $k . " -> " . $settings["option"] . "<br />\n";
                        break;

                    case "footer":
                        break;

                    case "normal":
?>
<tr><td style="background:#eee;color:#666">
	<h2 style="background:#00a url(<?php hecho(relative_url('/images/newsletter/h2.png', $base_url))?>) repeat top left;color:#fd0;font-family:'Mister Earl BT',MisterEarlBTRegular,fantasy;font-size:39px;font-weight:normal;text-transform:uppercase;margin:0 -9px 14px -12px;padding:0 18px;box-shadow:0px 14px 16px -8px #666;text-shadow:3px 3px 1px rgba(0, 0, 0, 0.5)"><?php hecho($sections[$k]["title"])?></h2>
	<div style="padding:0 5px 16px">
		<table style="width:100%;"><tr>
<?php
$temp = array_chunk($temp, intval(count($temp) / 3) + (($temp % 3) ? 1 : 0));
                        foreach ($temp as $temp3 => $temp2) {
?>
			<td style="font-family:sans-serif;/*font-size:14px;*/vertical-align:top;">
				<ul style="margin:0;padding:0 8px 16px 16px;list-style-position:inside">
<?php
foreach ($temp2 as $v) {
                                $v["view_url"] = relative_url('/wanted_offers.html?data%5Bname%5D=[NAME_URL_ENCODED]&data%5Bemail%5D=[EMAIL_URL_ENCODED]&wanted_item=' . $v["str"], $track_url);
?>
					<li style="margin:0;padding:0;font-style:italic"><a href="<?php hecho($v["view_url"])?>" target="_blank" style="color:inherit;text-decoration:none;"><?php hecho($v["name"])?></a></li>
<?php
}
?>
				</ul>
<?php
$temp4 = array_keys($temp);
                            if (end($temp4) == $temp3) {
?>
				<a target="_blank" href="<?php hecho(relative_url('/request_wanted.html', $track_url))?>" style="margin:0 32px;"><img border="0" src="<?php hecho(relative_url('/images/newsletter/send_your_offer.png', $base_url))?>" alt="Send Your Offer" /></a>
<?php
}
?>
			</td>
<?php
}
?>
		</tr></table>
		<p style="margin:16px;">If you can offer us any of the above products, please click onto the product link and send us your offers. If you want Coinop Express to sell your products, please contact our sales team at sales@coinopexpress.com.</p>
	</div>
</td></tr>
<?php
}
                break;

            case "banner":
                //Already in header
                break;

            case 'top_banner':
            case 'large_banner':
            case 'bottom_banner':
                if (is_array($settings['items']) && moduleLoad('banner')) {
                    foreach ($settings['items'] as $v) {
                        if ('' != $v['str']) {
                            $id = $v['str'];

                            $temp = bannerFromDatabase($id);
                            if ('' == $v['name']) {
                                $v['name'] = $temp['alt'];
                            }

                            $v['view_url']  = relative_url($temp['click_url'], $track_url);
                            $v['image_url'] = relative_url($temp['image_url'], $base_url);
?>
<tr><td style="background:#eee;color:#666">

<div style="padding:4px 0;margin:0;text-align:center">
	<a href="<?php hecho($v['view_url'])?>"><img title="<?php hecho($v['name'])?>" src="<?php hecho($v['image_url'])?>" alt="<?php hecho($v['name'])?>" border="0" /></a>
</div>

</td></tr>
<?php
}
                    }
                }

                break;

            default:
                hecho($k);
                echo "<br />\n";
                break;
        }
    }
}

?>
</tbody>
<?php
//-----------------------------------------------------------------------------
?>
	</table></body>
</html>
<?php

$string = ob_get_clean();
$string = str_replace("\r\n", "\n", $string);
$string = str_replace("\n\r", "\n", $string);
$string = str_replace("\r", "\n", $string);
$string = str_replace("<br>", "<br />", $string);
$string = str_replace("<BR>", "<br />", $string);
$string = str_replace("<br >", "<br />", $string);
$string = str_replace("<br/>", "<br />", $string);
$string = str_replace("<hr>", "<br />", $string);
$string = str_replace("<HR>", "<br />", $string);
$string = str_replace("<hr >", "<br />", $string);
$string = str_replace("<hr/>", "<br />", $string);
$string = str_replace("<center>", '<div style="text-align:center">', $string);
$string = str_replace("</center>", '</div>', $string);
$string = str_replace("<C>", '<div style="text-align:center">', $string);
$string = str_replace("</C>", '</div>', $string);
$string = preg_replace('/<font[^>]+>([^<]+)<\\/font>/i', '$1', $string);
$string = preg_replace('/<mailto:[^>]+@[^>]+>/i', '', $string);
$string = preg_replace('/  +/', ' ', $string);
$string = trim($string);
$string = preg_replace("/[ \\t]+\\n/", "\n", $string);
$string = preg_replace("/\\n\\n+/", "\n", $string);
$string = preg_replace("/\\n[ \\t]+/", "\n", $string);
echo $string;
?>