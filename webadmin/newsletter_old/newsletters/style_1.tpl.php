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
$style_id = 1;
include __DIR__ . "/common_" . $style_id . ".inc.php";
$domain_name = "www.coinopexpress.com";

if (!function_exists("newsFromDatabase")) {
    /**
     * @param int $news_str
     *
     * @return mixed[]
     */
    function newsFromDatabase($news_str)
    {
        global $DB;
        $row              = $DB->query_first("SELECT * FROM `news` WHERE `news_id` = " . intval($news_str) . " LIMIT 1", MYSQLI_ASSOC);
        $row['thumb_url'] = sprintf(
            'https://static.coinopexpress.com/images/news/thumb/%d.jpg',
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
?>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<table border="0" cellspacing="0" cellpadding="5"  bgcolor="#000099" align="center" width="100%"><tr><td><table border="0" cellspacing="0" cellpadding="5"  bgcolor="#FFFFFF" align="center" width="100%">
	<tr><td><table width="100%" border="0" cellspacing="0" cellpadding="3"><tr bgcolor="#FFFFFF">
		<td width="30%" align="left"><a href="https://<?php echo $domain_name ?>/"><img src="https://<?php echo $domain_name ?>/img_coe/coinopexpress_weblogo.gif" alt="Coinop Express" width="241" height="55" border="0" /></a></td>
		<td width="50%" align="center" nowrap="nowrap"><font color="#000099" size="+3"><b>Weekly Sales Report</b></font></td>
		<td width="20%" align="right" nowrap="nowrap"><font color="#000099" size="-1"><?php echo date("D, F d, Y", $item["time_added"]) ?></font></td>
	</tr></table></td></tr>

<!--
<?php
/*
if ($item_settings["paragraph"]["value"] != "" && formatCleanString($item_settings["paragraph"]["value"]) != formatCleanString($sections["paragraph"]["defaults"]["value"]))
{
?>
<div style="position:relative;margin:4px 4px 0">
<h1 style="margin:0"><a href="https://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item["subject"]) ?>_<?php echo $item["id"] ?>.html"><img src="https://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Highway Entertainment Newsletter" border="0" /></a></h1>
<i style="font-size:10px;position:absolute;top:12px;right:18px;color:#999"><?php echo date("jS - F - Y", $item["time_added"]) ?></i>
</div>
<p style="padding:16px;margin:0 4px 4px;font-size:12px"><?php echo formatHTMLDescription($item_settings["paragraph"]["value"]) ?></p>
<?php
}
else
{
?>
<div style="position:relative;margin:4px">
<h1 style="margin:0"><a href="https://<?php echo $domain_name ?>[TRACKER_KEY]/newsletters/<?php echo formatURLSafe($item["subject"]) ?>_<?php echo $item["id"] ?>.html"><img src="https://<?php echo $domain_name ?>[TRACKER_KEY]/images/newsletter_<?php echo $style_id ?>/logo.png" alt="Highway Entertainment Newsletter" border="0" /></a></h1>
<i style="font-size:11px;position:absolute;top:12px;right:18px;color:#999"><?php echo date("jS - F - Y", $item["time_added"]) ?></i>
</div>
<?php
}
 */
?>
-->

<?php
$packets = array();
foreach ($item_settings as $k => $settings) {
    if ("y" == $settings["enable"]) {
        switch ($k) {
            case "latest_offers":
            case "arcade_machines":
            case "arcade_boards":
            case "arcade_parts":
            case "arcade_merchandise":
            case "featured_products":
            case "specials":
                ob_start();
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td bgcolor="#0000FF"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td nowrap="nowrap"><font size="+1" color="#FFFFFF"><strong><?php hecho($sections[$k]["title"])?></strong></font></td></tr></table></td></tr></table>
<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#000099" width="100%"><tr><td><table border="0" cellspacing="0" cellpadding="15" align="center" bgcolor="#FFFFFF" width="100%"><tr><td>
<?php
switch ($settings["option"]) {
                    default:
                    case "brief":
                        if (is_array($temp = $settings["items"])) {
                            foreach ($temp as $k2 => $v) {
                                if ("" == $v["str"]) {
                                    unset($temp[$k2]);
                                }
                            }
                        }

                        if (is_array($temp)) {
                            foreach (array_chunk($temp, 5) as $first1 => $row) {
                                $cache = array();
                                foreach ($row as $v) {
                                    $temp = productFromDatabase($v["str"]);
                                    if ("" != $v["name"]) {
                                        $temp["product_name"] = $v["name"];
                                    }

                                    if ("" != $v["text"]) {
                                        $temp["short_desc"] = $v["text"];
                                    }

                                    $cache[] = $temp;
                                }
?>
	<table border="0" cellspacing="1" cellpadding="5" align="center">
		<tr bgcolor="#EEEEEE">
<?php
foreach ($cache as $v) {
?>
			<td align="center" width="20%"><b><?php hecho($v["product_name"])?></b></td>
<?php
}
?>
		</tr>
		<tr>
<?php
foreach ($cache as $v) {
?>
			<td align="center" width="20%"><a href="<?php hecho("https://" . $domain_name . $v["product_url"])?>"><img src="https://<?php hecho($domain_name . $v["thumb_url"])?>" width="96" height="96" alt="<?php hecho($v["product_name"])?>" border="0" /></a></td>
<?php
}
?>
		</tr>
		<tr>
<?php
foreach ($cache as $v) {
?>
			<td align="center" width="20%" valign="top"><font size="-2"><?php echo nl2br($v["short_desc"]) ?></font></td>
<?php
}
?>
		</tr>
		<tr bgcolor="#EEEEEE">
<?php
foreach ($cache as $v) {
                                    $price = convertCurrency($v["secondary_price"], $v["secondary_currency"], "USD");
                                    if ("n" != $temp["price_online"] && bccomp($price, "0.00", 2) > 0) {
?>
			<td align="center" width="20%" nowrap="nowrap"><b>$<?php echo $price ?> USD</b></td>
<?php
} else {
?>
			<td align="center" width="20%" nowrap="nowrap"><b>Please Ask</b></td>
<?php
}
                                }
?>
		</tr>
	</table>
<?php
}
                        }

                        break;
                }
?>
</td></tr></table></td></tr></table>
<br />
<?php
$packets[] = array("size" => "wide", "content" => ob_get_clean());
                break;

            case "news":
                ob_start();
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td bgcolor="#0000FF"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td nowrap="nowrap"><font size="+1" color="#FFFFFF"><strong><?php hecho($sections[$k]["title"])?></strong></font></td></tr></table></td></tr></table>
<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#000099" width="100%"><tr><td><table border="0" cellspacing="0" cellpadding="15" align="center" bgcolor="#FFFFFF" width="100%"><tr><td>
<?php
if (is_array($settings["items"])) {
                    foreach ($settings["items"] as $v) {
                        if ("" != $v["str"]) {
                            $id   = $v["str"];
                            $temp = newsFromDatabase($id);
                            if ("" == $v["name"]) {
                                $v["name"] = $temp["news_title"];
                            }

                            if ("" == $v["text"]) {
                                $v["text"] = trim($temp["long_description"]);
                            }

                            $v["view_url"]  = "https://" . $domain_name . "[TRACKER_KEY]" . $temp["news_url"];
                            $v["image_url"] = "https://" . $domain_name . $temp["image_url"];
?>
	<table width="90%" border="0" cellspacing="3" cellpadding="0" align="center"><tr>
		<td width="5%" nowrap="nowrap"><a href="https://<?php hecho($domain_name . $temp["news_url"])?>"><img src="https://<?php hecho($domain_name . $temp["thumb_url"])?>" width="96" height="96" border="0" alt="<?php hecho($v["name"])?>" /></a><img src="https://<?php hecho($domain_name)?>/_.gif" width="20" height="1" alt="" /></td>
		<td width="95%" valign="top">
			<table width="100%" border="0" cellspacing="1" cellpadding="5"><tr bgcolor="#EEEEEE">
				<td width="90%"><b><a href="https://<?php hecho($domain_name . $temp["news_url"])?>"><?php hecho($v["name"])?></a></b></td>
				<td width="10%" nowrap="nowrap"><div align="right"><font size="-2"><?php echo date("j M, Y", strtotime($temp["date_added"])) ?></font></div></td>
			</tr></table>
			<p>
				<?php echo nl2br($v["text"]) ?><br />
				[ <a href="https://<?php hecho($domain_name . $temp["news_url"])?>"><b><font size="-2">more information</font></b></a> ]<br />
			</p>
		</td>
	</tr></table>
	<br />
	<hr width="90%" size="1" />
	<br />
<?php
}
                    }
                }

?>
</td></tr></table></td></tr></table>
<br />
<?php
$packets[] = array("size" => (("brief" == $settings["option"]) ? "thin" : "wide"), "content" => ob_get_clean());
                break;

            case "wanted":
            case "coming_soon":
            case "top_sellers":
            case "latest_sections":
                break;

/*
?>
<!--
<?php
case "disclaimer":
if (trim($settings["value"]) != "")
{
ob_start();
?>
<div style="border:2px solid #8c9">
<p style="text-align:center;font-size:11px;font-weight:bold;color:#071"><?php echo formatHTMLDescription($settings["value"]) ?></p>
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
$v["view_url"] = "https://" . $domain_name . "[TRACKER_KEY]" . $temp["click_url"];
$v["image_url"] = "https://" . $domain_name . str_replace(".jpg", ($settings["option"] == "wide") ? "_0x2d8.jpg" : "_0x150.jpg", $temp["image_url"]);
?>
<div style="padding:4px 0;margin:0;text-align:center">
<a href="<?php hecho($v["view_url"]) ?>"><img title="<?php hecho($v["name"]) ?>" src="<?php hecho($v["image_url"]) ?>" alt="<?php hecho($v["name"]) ?>" border="0" /></a>
</div>
<?php
$packets[] = array("size" => (($settings["option"] == "wide") ? "wide" : "thin"), "content" => ob_get_clean());
}
break;
?>
-->
<?php
 */
        }
    }
}

//foreach($packets as $k => $v) $packets[$k]["content"] = '<div style="border:1px solid #ccc;margin:4px;-webkit-border-top-left-radius:5px;-webkit-border-top-right-radius:5px;-moz-border-radius-topleft:4px;-moz-border-radius-topright:5px;border-top-left-radius:5px;border-top-right-radius:5px">' . $packets[$k]["content"] . "</div>";
$prev = false;
$next = false;
foreach ($packets as $k => $v) {
    $prev = $next;
    $next = $k;
    if (false !== $prev && "thin" == $packets[$prev]["size"] && "thin" == $v["size"]) {
        $packets[$k]["size"]    = "wide";
        $packets[$k]["content"] = '<table style="border-collapse:collapse" width="100%" cellpadding="0"><tr>' . "\n\t" . '<td width="50%" valign="top">' . $packets[$prev]["content"] . "</td>" . "\n\t" . '<td width="50%" valign="top">' . $packets[$next]["content"] . "</td>" . "\n" . "</tr></table>";
        unset($packets[$prev]);
        $prev = false;
    }
}
?>
	<tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>
<?php
foreach ($packets as $v) {
    echo $v["content"] . "\n\n";
}

?>
	</td></tr></table></td></tr>

	<tr><td><table width="100%" border="0" bgcolor="#000099" cellspacing="0" cellpadding="2">
		<tr align="center"><td><a href="https://<?php echo $domain_name ?>/"><img src="https://<?php echo $domain_name ?>/img_coe/logo_coinop_footer.gif" alt="Coinop Express" width="270" height="30" border="0" /></a></td></tr>
		<tr align="center"><td><font color="#FFFFFF" size="-1">Phone: (+61) 02 4968 9313</font></td></tr>
		<tr align="center"><td><font color="#FFFFFF" size="-1">Fax: (+61) 02 4968 9314</font></td></tr>
		<tr align="center"><td><font size="-1"><a href="mailto:sales@arcooda.com">sales@arcooda.com</a></font></td></tr>
	</table></td></tr>
</table></td></tr></table>
