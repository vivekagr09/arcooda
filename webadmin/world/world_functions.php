<?php
function display_countries()
{
    global $DB, $order, $by, $limit, $fields, $allowed_sections;
    $time = false;
    if ("" == $by) {
        $by = "name";
    }

    if ("" == $order) {
        $order = "asc";
    }

    if ("time" == $by) {
        $time = true;
        $by   = "name";
    }
    if ("country" == $by) {
        $by = "name";
    }

    if ("capital" == $by) {
        $by = "capital_city";
    }

    $start = $_REQUEST['start'];
    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    settype($limit, "float");
    $qry = sprintf(
        "SELECT * FROM `world`.`country` ORDER BY %s %s LIMIT %d, %d",
        $by,
        $order,
        $start,
        $limit
    );
    if ("name" == $by && $time) {
        $by = "time";
    } else if ("name" == $by) {
        $by = "country";
    }

    if ("capital_city" == $by) {
        $by = "capital";
    }

    draw_header_row();
    $shaded = true;
    $count  = 1;
    $result = $DB->query($qry);
    while ($row = $DB->fetch_array($result)) {
        // print each of the rows of the array..
        if (!$shaded) {
            echo "<tr style=\"text-align:center\" class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
            $shaded = !$shaded;
        } else {
            echo "<tr style=\"text-align:center;\" class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
            $shaded = !$shaded;
        }
        $capital = $row['capital_city'];
        echo '<td align="' . $fields['country'] . '"><a href="?action=summary&code=' . $row['code'] . '" target="_blank">' . $row['name'] . '</a></td>';
        echo '<td align="' . $fields['capital'] . '">' . $capital . '</td>';
        if (ava_time($capital)) {
            echo '' . display_time($capital, $count) . '';
        } else {
            echo '<td align="' . $fields['time'] . '">N/A</td>';
        }

        echo '<td align="' . $fields['population'] . '">' . $row['population'] . '</td>';
        echo '<td width="20%" align="' . $fields['languages'] . '">' . format_str_sum($row['languages']) . '</td>';
        if (has_permission('WORLD2', $allowed_sections)) {
            echo '<td align="center"><a href="?code=' . $row['code'] . '" target="_blank">Edit</a></td>';
        } else {
            echo '<td align="center"><a href="?action=summary&code=' . $row['code'] . '">View</a></td>';
        }

        echo '</tr>';
        $count++;
    }
    $DB->free_result($result);
    echo '</table>';
    echo "<br />";
    display_entries_main($start, $limit);
    echo '<br />';
    display_letter();
}
function display_country()
{
    global $DB, $code;

    $row = $DB->query_first(sprintf(
        "SELECT * FROM `world`.`country` WHERE `code` = %s",
        $DB->escape($code)
    ));
?>
<form action="index.php?code=<?php echo $row['code'] ?>" name="country" enctype="multipart/form-data" method="post">
	<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%">
		<tr><td class="country" colspan="2" width="100%"><b><?php echo $row['name'] ?></b></td></tr>
	</table>
	<br />
	<table border="0" align="center" cellpadding="1" cellspacing="0" width="75%">
		<tr><td colspan="2" style="border-bottom:1px solid #ccc;">&nbsp;</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Capital :</td>
			<td class="input">
				<input class="input2" type="text" name="data[capital_city]" value="<?=$row['capital_city']?>" />
				<span id="capital_btn"></span>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" nowrap="nowrap">Country Code :</td>
			<td class="input">
				<input class="input2" type="text" name="data[dial_code]" value="<?php echo $row['dial_code'] ?>" />
				<span id="c_code_btn"></span>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" nowrap="nowrap">Internet Code :</td>
			<td class="input">
				<input class="input2" type="text" name="data[internet_code]" value="<?php echo $row['internet_code'] ?>" />
				<span id="i_code_btn"></span>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" nowrap="nowrap">Currency Code :</td>
			<td class="input">
				<input class="input2" type="text" name="data[currency_code]" value="<?php echo $row['currency_code'] ?>" />
				<span id="cur_code_btn"></span>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Population : </td>
			<td class="input">
				<input class="input2" type="text" name="data[population]" value="<?php echo $row['population'] ?>" />
				<span id="population_btn"></span>
			</td>
		</tr>
		<tr>
			<td class="left1">&nbsp;</td>
			<td class="input"><span style="color:#f00;font-size:10px;align:center;"><i>Last Edited: <?php echo format_date($row['pop_edited'], false) ?></span></td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" nowrap="nowrap">Internet Users :</td>
			<td class="input">
				<input class="input2" type="text" name="data[internet_users]" value="<?php echo $row['internet_users'] ?>" />
				<span id="iu_btn">Please enter in the format xxx,xxx,xxx instaed of "x.xx million"</span>
			</td>
		</tr>
		<tr>
			<td class="left1">&nbsp;</td>
			<td class="input"><span style="color:#f00;font-size:10px;align:center;"><i>Last Edited: <?php echo format_date($row['iu_edited']) ?></span></td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Seaports for Cargo :</td>
			<td class="input">
				<select class="input2" name="seaports">
<?php
    $sea   = "SELECT * FROM `world`.`seaports` WHERE country = '$code' ORDER BY name ASC";
    $sport = $DB->query($sea);
    while ($sp = $DB->fetch_array($sport)) {
        if ("" != $sp['name']) {
            echo '<option value="' . $sp['name'] . '">' . $sp['name'] . '</option>';
        }
    }

?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a target="_blank" href="seaport/seaports.php">ADD | EDIT</a>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Major Airports :</td>
			<td class="input">
				<select class="input2" name="airports">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`airports` WHERE `country` = %s ORDER BY `name` ASC",
        $DB->escape($code)
    ))) {
        while ($ap = $DB->fetch_assoc($query)) {
            if ("" != $ap['name']) {
                echo '<option value="' . htmlspecialchars($ap['name']) . '">' . htmlspecialchars($ap['name']) . '</option>';
            }
        }
        $DB->free_result($query);
    }
?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a target="_blank" href="airport/airports.php">ADD | EDIT</a>
			</td>
		</tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Number of Airports :</td>
			<td class="input">
				<input class="input2" type="text" name="data[airports]" value="<?php echo $row['airports'] ?>" />
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr><td class="top_header" colspan="2"><u>Maps for <?php echo $row['name'] ?></u></td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Map :</td>
			<td class="input">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                <input name="map_file" type="file" class="file" />
            </td>
		</tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">File Description (optional) :</td>
			<td class="input"><input name="map_desc" type="text" class="input2" style="width:400px;" /><span id="map_file_btn"></span><br /></td>
		</tr>
		<tr>
			<td class="left_bottom">&nbsp;</td>
			<td class="right_bottom"><table width="288px" height="100px" border="0" cellspacing="0" cellpadding="2" align="left">
<?php
    $str   = "SELECT * FROM `world`.`map` WHERE country = '$code'";
    $res   = $DB->query($str);
    $count = 1;
    if (mysqli_num_rows($res) == 0) {
        echo '<b><tr bgcolor="#E3EBF7"><td width="96px" height="100px" align="center">No Map Image uploaded yet</b></td></tr>';
    } else {
        while ($im = $DB->fetch_array($res)) {
            echo '<tr bgcolor="#E3EBF7">';
            echo '<td width="96px" height="100px" align="center">';
            echo '<a href="images/map_' . $im['id'] . '.' . end(explode(".", $im["file_path"])) . '" target="_blank">';
            echo '<img border="0" src="images/map_' . $im['id'] . '_sml.jpg" /></a><br />';
            echo '<span class="description"><small>' . $im['description'] . '</small></span>';
            echo '</td>';
            echo '<td><input type="text" value="' . filename($im['file_path']) . '" readonly="readonly" /><br /><span><a href="?code=' . $code . '&action=delete_image&map_id=' . $im['id'] . '" target="_blank">DELETE</a></span></td>';
            echo '</tr>';
        }
    }
?>
			</table></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" class="top_header" nowrap="nowrap">
			<u>Voltage</u>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a target="_blank" href="voltage/voltage.php"><font size="-1">ADD | EDIT</font></a>
		</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Voltage :</td>
			<td class="input">
				<select class="input2" name="voltage">
					<option value="" selected="selected">[SELECT ONE]</option>
<?php
    $vol    = "SELECT * FROM `world`.`voltage` ORDER BY value ASC";
    $result = $DB->query($vol);
    while ($vt = $DB->fetch_array($result)) {
        if ($row['voltage'] == $vt['value']) {
            echo '<option value="' . $vt['value'] . '" selected="selected">' . $vt['value'] . '</option>';
        } else {
            echo '<option value="' . $vt['value'] . '">' . $vt['value'] . '</option>';
        }

    }
?>
				</select>
				<span id="voltage_btn"></span>
			</td>
		</tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Notes :</td>
			<td class="input">
				<textarea class="textarea" name="data[v_comments]" ><?php echo $row['v_comments'] ?></textarea><br />
				<span id="v_comments_btn"></span>
			</td>
		</tr>
		<tr><td style="border-top:1px solid #ccc;" colspan="2">&nbsp;</td></tr>
		<tr><td class="top_header" colspan="2">
			<u>Plug Types in <?php echo $row['name'] ?></u>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a target="_blank" href="plugs/plugs.php"><font size="-1">ADD | EDIT</font></a>
		</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">New Plug Type :</td>
			<td class="input">
				<select class="input2" name="plug">
					<option value="">[SELECT ONE]</option>
<?php
    $plug  = "SELECT * FROM `world`.`plug_types`";
    $plugs = $DB->query($plug);
    while ($pg = $DB->fetch_array($plugs)) {
        echo '<option value="' . $pg['value'] . '">' . $pg['value'] . '</option>';
    }

?>
				</select>
				<span id="plug_btn"></span>
			</td>
		</tr>
		<tr>
			<td class="label">&nbsp;</td>
			<td class="input">
				<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
					<tr>
						<td colspan="3" align="center" class="box"><strong>Plug Types Accepted in <?php echo $row['name'] ?></strong></td>
					</tr>
					<tr bgcolor="#E3EBF7">
						<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">
<?php
    if ("" == $row['plugs']) {
        echo '<ul type="square"><li>None Selected</li></ul>';
    } else {
        echo format_str($row['plugs'], true, 'plugs')
        ;
    }
?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="left_bottom">&nbsp;</td>
			<td class="right_bottom">
				<table width="220px" border="0" cellspacing="0" cellpadding="2" align="left">
<?php
    if ("" != $row['plugs']) {
        echo '<tr bgcolor="#E3EBF7"><td colspan=3 align="center"><b>Images for accepted plug types</b></td></tr>';
        $ar = split_str($row['plugs']);
        echo '<tr bgcolor="#E3EBF7">';
        $c = 0;
        foreach ($ar as $val) {
            $qry = "SELECT * FROM `world`.`plug_types` WHERE value = '" . trim($val) . "'";
            $pt  = $DB->query_first($qry);
            echo '<td width="100px" height="100px" align="center">';
            echo '<a href="images/plug_' . $pt['id'] . '.' . end(explode(".", $pt["file_path"])) . '" target="_blank">';
            echo '<img src="images/plug_' . $pt['id'] . '_sml.jpg" border="0" /></a><br />';
            echo '<span class="description"><small>' . $pt['value'] . '</small></span>';
            echo '</td>';
            $c++;
            if ($c % 3 == 0) {
                echo '</tr><tr bgcolor="#E3EBF7">';
            }

        }
        echo '</tr>';
    }
?>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="left_right"><span style="font:bold 16px verdana,arial,helvetica;"><u>Upload Additional Plug Images</u></td>
		</tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">Plug Image :</td>
			<td class="input">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				<input name="plug_file" type="file" class="file" />
			</td>
		</tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">File Description (optional) :</td>
			<td class="input">
				<input name="plug_desc" type="text" class="input2" style="width:400px" value="" /><span id="plug_file_btn" /></span>
			</td>
		</tr>
		<tr>
			<td class="left_right_bottom" colspan="2">
				<table width="288px" height="100px" border="0" cellspacing="0" cellpadding="2" align="center">
<?php
    // this section will display any additional images that have been uploaded....
    $count = 1;
    $res   = $DB->query(sprintf(
        "SELECT * FROM `world`.`country_plugs` WHERE `country` = %s",
        $DB->escape($code)
    ));
    if (mysqli_num_rows($res) != 0) {
        echo '<tr bgcolor="#E3EBF7"><td colspan="2" align="center"><b>Uploaded Images for plug types</b></td></tr>';
        while ($im = $DB->fetch_array($res)) {
            echo '<tr bgcolor="#E3EBF7">';
            echo '<td width="96px" height="100px" align="center">';
            echo '<a href="images/plug_' . $im['id'] . '.' . end(explode(".", $im["plug_path"])) . '" target="_blank">';
            echo '<img src="images/plug_' . $im['id'] . '_sml.jpg" border="0" /></a><br />';
            echo '<span class="description"><small>' . $im['description'] . '</small></span>';
            echo '</td>';
            echo '<td><input type="text" value="' . filename($im['plug_path']) . '" readonly="readonly" /><br /><span><a href="?code=' . $code . '&action=delete_image&plug_id=' . $im['id'] . '" target="_blank">DELETE</a></span></td>';
            echo '</tr>';
        }
    }
?>
				</table>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="top_header" colspan="2">
			<u>Video Signals for <?php echo $row['name'] ?></u>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a target="_blank" href="video_signal/video.php"><font size="-1">ADD | EDIT</font></a>
		</td></tr>
		<tr>
			<td class="label" align="left" nowrap="nowrap">New Video Signal :</td>
			<td class="input">
				<select class="input2" name="video">
					<option value="">[SELECT ONE]</option>
<?php
    $vid  = "SELECT * FROM `world`.`video_signal`";
    $vids = $DB->query($vid);
    while ($vs = $DB->fetch_array($vids)) {
        echo '<option value="' . $vs['value'] . '">' . $vs['value'] . '</option>';
    }

?>
				</select>
				<span id="video_btn"></span>
			</td>
		</tr>
		<tr>
			<td class="left_bottom">&nbsp;</td>
			<td class="right_bottom">
				<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
					<tr>
						<td colspan="3" align="center" class="box"><strong>Video Signals Used</strong></td>
					</tr>
					<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">
<?php echo format_str($row['video_signal'], true, 'video_signal') ?>
</td>
</tr>
</table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td class="top_header" colspan="2"><u>Languages in <?php echo $row['name'] ?></u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="language/language.php"><font size="-1">ADD | EDIT</a></font></td></tr>
<tr>
<td class="label" nowrap="nowrap">Add New Language :</td>
<td class="input">
<select class="input2" name="language">
<option value="" selected="selected">[SELECT ONE]</option>
<?php
$str = "SELECT * FROM `world`.`language` ORDER BY name ASC";
    $res = $DB->query($str);
    while ($r = $DB->fetch_array($res)) {
        echo '<option value = "' . $r['name'] . '">';
        echo '' . $r['name'] . '</option>';
    }
?>
</select><span id="language_btn"></span>
</td>
</tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="3" align="center" class="box"><strong>Main Languages</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">
<?php echo format_str($row['languages'], true, 'languages') ?>
</td>
</tr>
</table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td class="top_header" colspan="2"><u>Currencies accepted in <?php echo $row['name'] ?></u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="currency/currency.php"><font size="-1">ADD | EDIT</a></font></td></tr>
<tr>
<td class="label" nowrap="nowrap">Add new Currency :</td>
<td class="input">
<select class="input2" name="currency">
<option value="" selected="selected">[SELECT ONE]</option>
<?php
$str = "SELECT * FROM `world`.`currency` ORDER BY name ASC";
    $res = $DB->query($str);
    while ($r = $DB->fetch_array($res)) {
        echo '<option value = "' . $r['code'] . '">';
        echo '' . $r['name'] . '</option>';
    }
?>
</select><span id="cur_btn"></span>
</td>
</tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="3" align="center" class="box"><strong>Currency Accepted</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">
<?php echo format_str($row['currency'], true, 'currency') ?>
</td>
</tr>
</table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" class="top_header"><u>Live Clock for <?php echo $row['name'] ?></u></td></tr>
<tr>
<td class="label" align="center" nowrap="nowrap">Time Zones :</td>
<td class="input" nowrap="nowrap"><a target="_blank" href="time/times.php">ADD | EDIT</a></td>
</tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="3" align="center" class="box"><strong>Current Time Zones</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td align="center" nowrap="nowrap" class="bottomLeft">
<table align="center">
<?php
    $str       = "SELECT * FROM `world`.`time_zones` WHERE `country` = '$code' ORDER BY `name`";
    $tz        = $DB->query($str);
    $count     = 1;
    $city_time = array();
    while ($tz_row = $DB->fetch_array($tz)) {
        $city["Name"]      = $tz_row['name'];
        $city["GMT"]       = $tz_row['offset'];
        $city["actualDST"] = 0.0;
        if ('y' == $tz_row['day_light']) {
            $city["actualDST"] = 1.0;
        }

        $gmt_diff          = $city["GMT"] + $city["actualDST"];
        $city_time[$count] = time() + ($gmt_diff * 3600);
        echo '<tr><td class="time">' . $city["Name"] . ':</td>';
        echo '<td><span id="LiveClockIE' . $count . '"></span></td></tr>';
        $count++;
    }
?>
</table>
<script type="text/javascript">
//<![CDATA[
<?php
    for ($i = 1; $i < $count; $i++) {
        echo 'var strClock' . $i . ' = "' . gmdate("M d, Y H:i:s", $city_time[$i]) . '";';
        echo 'var intClock' . $i . 'Diff = syncClock(' . $i . ');';
    }
?>
function update(){
window.setTimeout ("update()",1000); // updates every second
localTime = new Date();
intLocalTime = localTime.getTime();
dot = '<FONT COLOR=#000000>:</FONT>';
<?php
    for ($i = 1; $i < $count; $i++) {
        update($i, true, false);
    }
?>
}
function syncClock(clockID){
    localTime = new Date();
    intLocalTime = localTime.getTime();
<?php
    for ($i = 1; $i < $count; $i++) {
        echo 'if (clockID == ' . $i . ')';
        echo 'intClockTime = Date.parse(strClock' . $i . ');';
    }
?>
    intClockDifference = intLocalTime - intClockTime;
    return intClockDifference;
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
update();
//]]>
</script>
</td>
</tr>
</table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" class="top_header" nowrap="nowrap"><u>Shipping to <?php echo $row['name'] ?></u></td></tr>
<tr>
<td class="label" nowrap="nowrap">Shipping Notes :</td>
<td class="input">
<textarea class="textarea" name="shipping_notes"></textarea>
<span id="ship_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Shipping Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Shipping Notes</b></td>
</tr>
<?php
    $ship  = "SELECT * FROM `world`.`shipping_notes` WHERE country = '$code'";
    $res   = $DB->query($ship);
    $count = 1;
    while ($sh = $DB->fetch_array($res)) {
        if ($count % 2 == 1) {
            echo '<tr bgcolor = "#FFFFFF">';
        } else {
            echo '<tr bgcolor="#EEEEEE">';
        }

        echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
        echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
        echo '</tr>';
        $count++;
    }
?>
</table>
</td>
</tr>
<tr><td class="left_right" colspan="2">&nbsp;</td></tr>
<tr>
<td class="label" nowrap="nowrap">Fumigation :</td>
<td class="input">
<textarea class="textarea" name="fumigation"></textarea>
<span id="fum_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Fumigation Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Fumigation Notes</b></td>
</tr>
<?php
    $fum   = "SELECT * FROM `world`.`fumigation_notes` WHERE country = '$code'";
    $res   = $DB->query($fum);
    $count = 1;
    while ($sh = $DB->fetch_array($res)) {
        if ($count % 2 == 1) {
            echo '<tr bgcolor = "#FFFFFF">';
        } else {
            echo '<tr bgcolor="#EEEEEE">';
        }

        echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
        echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
        echo '</tr>';
        $count++;
    }
?>
</table>
</td>
</tr>
<tr><td class="left_right" colspan="2">&nbsp;</td></tr>
<tr>
<td class="label" nowrap="nowrap">Certificates :</td>
<td class="input">
<textarea class="textarea" name="certificates"></textarea>
<span id="cert_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Certificate Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Certificate Notes</b></td>
</tr>
<?php
    $count = 1;
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`certificates` WHERE `country` = %s",
        $DB->escape($code)
    ))) {
        while ($sh = $DB->fetch_assoc($query)) {
            if ($count % 2 == 1) {
                echo '<tr bgcolor = "#FFFFFF">';
            } else {
                echo '<tr bgcolor="#EEEEEE">';
            }

            echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
            echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
            echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
            echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
            echo '</tr>';
            $count++;
        }
        $DB->free_result($query);
    }
?>

</table>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" class="top_header" nowrap="nowrap"><u>Shipping from <?php echo $row['name'] ?></u></td></tr>
<tr>
<td class="label" nowrap="nowrap">Shipping Notes :</td>
<td class="input">
<textarea class="textarea" name="shipping_from"></textarea>
<span id="ship_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Shipping Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Shipping Notes</b></td>
</tr>
<?php
    $ship  = "SELECT * FROM `world`.`shipping_from` WHERE country = '$code'";
    $res   = $DB->query($ship);
    $count = 1;
    while ($sh = $DB->fetch_array($res)) {
        if ($count % 2 == 1) {
            echo '<tr bgcolor = "#FFFFFF">';
        } else {
            echo '<tr bgcolor="#EEEEEE">';
        }

        echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
        echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
        echo '</tr>';
        $count++;
    }
?>
</table>
</td>
</tr>
<tr><td class="left_right" colspan="2">&nbsp;</td></tr>
<tr>
<td class="label" nowrap="nowrap">Fumigation :</td>
<td class="input">
<textarea class="textarea" name="fumigation_from"></textarea>
<span id="fum_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Fumigation Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Fumigation Notes</b></td>
</tr>
<?php
    $fum   = "SELECT * FROM `world`.`fumigation_from` WHERE country = '$code'";
    $res   = $DB->query($fum);
    $count = 1;
    while ($sh = $DB->fetch_array($res)) {
        if ($count % 2 == 1) {
            echo '<tr bgcolor = "#FFFFFF">';
        } else {
            echo '<tr bgcolor="#EEEEEE">';
        }

        echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
        echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
        echo '</tr>';
        $count++;
    }
?>
</table>
</td>
</tr>
<tr><td class="left_right" colspan="2">&nbsp;</td></tr>
<tr>
<td class="label" nowrap="nowrap">Certificates :</td>
<td class="input">
<textarea class="textarea" name="certificates_from"></textarea>
<span id="cert_btn"></span>
</td>
</tr>
<tr><td colspan="2" class="left_right">&nbsp;</td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Previous Certificate Notes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Certificate Notes</b></td>
</tr>
<?php
    $count = 1;
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`certificates_from` WHERE `country` = %s",
        $DB->escape($code)
    ))) {
        while ($sh = $DB->fetch_array($query)) {
            if ($count % 2 == 1) {
                echo '<tr bgcolor = "#FFFFFF">';
            } else {
                echo '<tr bgcolor="#EEEEEE">';
            }

            echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
            echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
            echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
            echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
            echo '</tr>';
            $count++;
        }
        $DB->free_result($query);
    }
?>

</table>
</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td colspan="2">
<input type="hidden" name="action" value="do_edit" />
<input type="hidden" name="code" value="<?=htmlspecialchars($code)?>" />
</td>
</tr>
<tr>
<td align="center" colspan="2">
<input type ="submit" value="Update" style="width:300px;text-align:center;vertical-align:bottom;" />
</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td colspan="2" class="top_header"><u>Job History for <?php echo $row['name'] ?></u></td></tr>
<tr>
<td class="left_bottom">&nbsp;</td>
<td class="right_bottom">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="left">
<tr>
<td colspan="4" align="center" class="box"><strong>Last 5 Changes</strong></td>
</tr>
<tr bgcolor="#E3EBF7">
<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><b>Date</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>User</b></td>
<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><b>Site</b></td>
<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight"><b>Changes</b></td>
</tr>
<?php
    $cer   = "SELECT * FROM `world`.`job_history` WHERE country = '$code' ORDER BY id desc LIMIT 0,5";
    $res   = $DB->query($cer);
    $count = 1;
    while ($sh = $DB->fetch_array($res)) {
        if ($count % 2 == 1) {
            echo '<tr bgcolor = "#FFFFFF">';
        } else {
            echo '<tr bgcolor="#EEEEEE">';
        }

        echo '<td width="10%" align="center" nowrap="nowrap" class="bottomLeft">' . format_date($sh['date'], false) . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['user'] . '</td>';
        echo '<td width="5%" align="center" nowrap="nowrap" class="bottomLeft">' . $sh['site'] . '</td>';
        echo '<td width="80%" align="center" nowrap="nowrap" class="bottomLeftRight">' . $sh['comments'] . '</td>';
        echo '</tr>';
        $count++;
    }
?>
</table>
</td>
</tr>
</table>
</form>
<?php
}
function view_summary()
{
	global $_GLOBALS;
    global $DB, $code;

    $row = $DB->query_first(sprintf(
		"SELECT * FROM `world`.`country` WHERE `code` = %s LIMIT 1",
		$DB->escape($code)
	), MYSQLI_ASSOC);

    print_list($row['name'], $_GLOBALS['staff']['name']);
?>
<link rel="stylesheet" type="text/css" href="summary.css" /><br />
<table border="1" align="center" cellpadding="1" cellspacing="0" width="55%">
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Capital :</td>
		<td class="fright" align="left"><?= $row['capital_city'] ?></td>
	</tr>
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Population :</td>
		<td class="fright" align="left"><?= $row['population'] ?> <span style="color:#f00;font-size:10px;align:center;"><i>(Last Edited: <?= format_date($row['pop_edited'], false) ?>)</span></td>
	</tr>
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Languages :</td>
		<td class="fright" align="left"><?=format_str_sum($row['languages'])?></td>
	</tr>
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Maps :</td>
		<td class="fright" align="left" nowrap="nowrap"><table width="288px" height="100px" border="0" cellspacing="0" cellpadding="2" align="center">
<?php
	$count = 0;
    if ($res   = $DB->query(sprintf(
		"SELECT * FROM `world`.`map` WHERE `country` = %s",
		$DB->escape($code)
	))) {
		while ($im = $DB->fetch_assoc($res)) {
			$count++;
?>
			<tr>
				<td width="100px" height="104px" align="center">
					<a target="_blank" href="images/map_<?= $im['id'] ?>.<?= end(explode(".", $im["file_path"])) ?>"><img border="0" src="images/map_<?= $im['id'] ?>_sml.jpg" /></a><br />
					<span class="description"><small><?= $im['description'] ?></small></span>
				</td>
				<td><span class="border"><?= filename($im['file_path']) ?></span><br /></td>
			</tr>
<?php
		}
		$DB->free_result($res);
	}
	if (0 == $count) {
		echo '<tr><td width="96px" height="100px" align="center"><b>No Map Image uploaded yet</b></td></tr>';
	}
?>
		</table></td>
	</tr>
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Seaports :</td>
<?php
    $sports = "";
    if ($sport = $DB->query(sprintf(
		"SELECT * FROM `world`.`seaports` WHERE `country` = %s ORDER BY `name` ASC",
		$DB->escape($code)
	))) {
		$i      = 0;
		while ($sp = $DB->fetch_assoc($sport, MYSQLI_ASSOC)) {
			if ("" != $sp['name']) {
				if (0 == $i) {
					$sports .= $sp['name'];
				} else {
					$sports .= ', ' . $sp['name'];
				}
				$i++;
			}
		}
		$DB->free_result($sport);
	}
?>
		<td class="fright" align="left" nowrap="nowrap"><?php echo $sports ?></td>
	</tr>
	<tr>
		<td width="35%" class="label" align="right" nowrap="nowrap">Airports :</td>
		<td class="fright" align="left" nowrap="nowrap">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`airports` WHERE `country` = %s ORDER BY `name` ASC",
        $DB->escape($code)
    ))) {
        $i = 0;
        while ($ap = $DB->fetch_assoc($query)) {
            if ("" != $ap['name']) {
                if (0 == $i) {
                    echo htmlspecialchars($ap['name']);
                } else {
                    echo ', ' . htmlspecialchars($ap['name']);
                }

                $i++;
            }
        }
        $DB->free_result($query);
    }
?>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Voltage :</td>
<td class="fright" align="left" nowrap="nowrap">
<?php echo $row['voltage'] ?><br /><font style="font:11px verdana;"><?php echo $row['v_comments'] ?></font>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Plug Types :</td>
<td class="fright" align="left" nowrap="nowrap">
<table width="288px" height="100px" border="0" cellspacing="0" cellpadding="2" align="center">
<?php
if ("" != $row['plugs']) {
        $ar = split_str($row['plugs']);
        foreach ($ar as $val) {
            $qry = "SELECT * FROM `world`.`plug_types` WHERE value = '" . trim($val) . "'";
            $pt  = $DB->query_first($qry);
            echo '<tr>';
            echo '<td width="100px" height="100px" align="center">';
            echo '<img src="images/plug_' . $pt['id'] . '_sml.jpg" border="0" /><br />';
            echo '<span class="description"><small>' . $pt['value'] . '</small></span>';
            echo '</td>';
            echo '<td><span class="border">' . filename($pt['file_path']) . '</span><br /></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="2" align="center"><b>None Selected</b></td></tr>';
    }

?>
</table>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Video Signals :</td>
<td class="fright" align="left">
<?php
echo format_str_sum($row['video_signal']) ?>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Currency Accepted :</td>
<td class="fright" align="left">
<?php
echo format_str_sum($row['currency']) ?>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Internet Users :</td>
<td class="fright" align="left">
<?php
echo $row['internet_users'] ?>
<span style="color:#f00;font-size:10px;align:center;"><i>(Last Edited: <?php echo format_date($row['iu_edited'], false) ?>)</span>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Internet Code :</td>
<td class="fright" align="left">
<?php
echo $row['internet_code'] ?>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Country Code :</td>
<td class="fright" align="left">
<?php
echo $row['dial_code'] ?>
</td>
</tr>
<tr><th align="left" colspan="2">Shipping To <?php echo $row['name'] ?></th></tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Shipping Notes :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
$ship = "SELECT * FROM `world`.`shipping_notes` WHERE country = '$code'";
    $res  = $DB->query($ship);
    while ($sh = $DB->fetch_array($res)) {
        echo '<li>' . $sh['comments'] . '</li>';
    }
?>
</ul>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Fumigation :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
$ship = "SELECT * FROM `world`.`fumigation_notes` WHERE country = '$code'";
    $res  = $DB->query($ship);
    while ($sh = $DB->fetch_array($res)) {
        echo '<li>' . $sh['comments'] . '</li>';
    }
?>
</ul>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Certificates :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`certificates` WHERE `country` = %s",
        $DB->escape($code)
    ))) {
        while ($sh = $DB->fetch_array($query)) {
            echo '<li>' . nl2br(htmlspecialchars($sh['comments'])) . '</li>';
        }
        $DB->free_result($query);
    }
?>
</ul>
</td>
</tr>
<tr><th align="left" colspan="2">Shipping From <?php echo $row['name'] ?></th></tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Shipping Notes :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
$ship = "SELECT * FROM `world`.`shipping_from` WHERE country = '$code'";
    $res  = $DB->query($ship);
    while ($sh = $DB->fetch_array($res)) {
        echo '<li>' . $sh['comments'] . '</li>';
    }
?>
</ul>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Fumigation :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
$ship = "SELECT * FROM `world`.`fumigation_from` WHERE country = '$code'";
    $res  = $DB->query($ship);
    while ($sh = $DB->fetch_array($res)) {
        echo '<li>' . $sh['comments'] . '</li>';
    }
?>
</ul>
</td>
</tr>
<tr>
<td width="35%" class="label" align="right" nowrap="nowrap">Certificates :</td>
<td class="fright" align="left" nowrap="nowrap">
<ul type="square">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `world`.`certificates_from` WHERE `country` = %s",
        $DB->escape($code)
    ))) {
        while ($sh = $DB->fetch_assoc($query)) {
            echo '<li>' . nl2br(htmlspecialchars($sh['comments'])) . '</li>';
        }
        $DB->free_result($query);
    }
?>
</ul>
</td>
</tr>
</table>
<?php
}
function do_edit()
{
	global $_GLOBALS;
    global $DB, $data, $code, $shipping_notes, $fumigation, $certificates, $shipping_from, $fumigation_from, $certificates_from;

    $staff_id = $_GLOBALS['staff']['id'];

    $qry = sprintf(
        "SELECT * FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($code)
    );

    $site        = getsite();
    $before      = $DB->query_first($qry);
    $update_str  = sprintf(
        "UPDATE `world`.`country` SET %s WHERE `code` = %s LIMIT 1",
        get_set_values($data),
        $DB->escape($code)
    );
    $job_history = array();
    //the basic update done here...
    $DB->query($update_str);
    // advanced edit now
    $after = $DB->query_first($qry);
    // now compare the before and after to update the dates...
    if ($before['capital_city'] != $after['capital_city']) {
        $job_history[] = array('comments' => 'Updated Capital');
    }

    if ($before['dial_code'] != $after['dial_code']) {
        $job_history[] = array('comments' => 'Updated Country Dial Code');
    }

    if ($before['internet_code'] != $after['internet_code']) {
        $job_history[] = array('comments' => 'Updated Internet Code');
    }

    if ($before['currency_code'] != $after['currency_code']) {
        $job_history[] = array('comments' => 'Updated Currency Code');
    }

    if ($before['airports'] != $after['airports']) {
        $job_history[] = array('comments' => 'Updated Number of Airports');
    }

    if ($before['internet_users'] != $after['internet_users']) {
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `iu_edited` = NOW() WHERE `code` = %s LIMIT 1",
            $DB->escape($code)
        ));
        $job_history[] = array('comments' => 'Updated Internet Users');
    }
    if ($before['population'] != $after['population']) {
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `pop_edited` = NOW() WHERE `code` = %s LIMIT 1",
            $DB->escape($code)
        ));
        $job_history[] = array('comments' => 'Updated Population Users');
    }
    // check currency
    if ("" != $_POST['currency']) {
        $job_history[] = array('comments' => 'Added New Currency');
        $str           = "SELECT * FROM `world`.`currency` WHERE code = '" . $_POST['currency'] . "'";
        $cur           = $DB->query_first($str);
        $new_cur       = $after['currency'] . ", " . $cur['name'] . '(' . $cur['code'] . ')';
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `currency` = %s WHERE `code` = %s LIMIT 1",
            $DB->escape($new_cur),
            $DB->escape($code)
        ));
    }
    // check language
    if ("" != $_POST['language']) {
        $job_history[] = array('comments' => 'Added New Language');
        //$str = "SELECT * FROM `world`.`language` WHERE name = '".$_POST['language']."'";
        //$cur = $DB->query_first($str);
        $new_cur = $after['languages'] . ", " . $_POST['language'];
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `languages` = %s WHERE `code` = %s LIMIT 1",
            $DB->escape($new_cur),
            $DB->escape($code)
        ));
    }
    // check video signal
    if ("" != $_POST['video']) {
        $job_history[] = array('comments' => 'Added New Video Signal');
        $new_str       = $after['video_signal'] . ", " . $_POST['video'];
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `video_signal` = %s WHERE `code` = %s LIMIT 1",
            $DB->escape($new_str),
            $DB->escape($code)
        ));
    }
    // check plug type
    if ("" != $_POST['plug']) {
        $job_history[] = array('comments' => 'Added New Plug Type');
        $new_str       = $after['plugs'] . ", " . $_POST['plug'];
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `plugs` = %s WHERE `code` = %s LIMIT 1",
            $DB->escape($new_str),
            $DB->escape($code)
        ));
    }
    // check for voltage
    if ("" != $_POST['voltage']) {
        $DB->query(sprintf(
            "UPDATE `world`.`country` SET `voltage` = %s WHERE `code` = %s LIMIT 1",
            $DB->escape($_POST['voltate']),
            $DB->escape($code)
        ));
    }
    $af_voltage = $DB->query_first(sprintf(
        "SELECT `voltage` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($code)
    ), MYSQLI_ASSOC);
    if ($before['voltage'] != $af_voltage['voltage']) {
        $job_history[] = array('comments' => 'Updated Voltage used');
    }

    //check for shipping notes
    if ("" != $shipping_notes) {
        $job_history[] = array('comments' => 'Added New Shipping To notes');
        $str           = "INSERT INTO `world`.`shipping_notes` VALUES ('','$code',NOW(),'" . $staff_id . "', '" . $shipping_notes . "','$site')";
        $DB->query($str);
    }
    //check for fumigation
    if ("" != $fumigation) {
        $job_history[] = array('comments' => 'Added New Fumigation To notes');
        $str           = "INSERT INTO `world`.`fumigation_notes` VALUES ('','$code',NOW(),'" . $staff_id . "', '" . $fumigation . "','$site')";
        $DB->query($str);
    }
    //check for certificates
    if ("" != $certificates) {
        $job_history[] = array('comments' => 'Added New Certificates To notes');
        $DB->query(sprintf(
            "INSERT INTO `world`.`certificates` (`country`, `date`, `user`, `comments`, `site`) VALUES (%s, NOW(), %s, %s, %s)",
            $DB->escape($code),
            $DB->escape($staff_id),
            $DB->escape($certificates),
            $DB->escape($site)
        ));
    }
    // Now for the SHIPPING FROM section...
    if ("" != $shipping_from) {
        $job_history[] = array('comments' => 'Added New Shipping From notes');
        $str           = "INSERT INTO `world`.`shipping_from` VALUES ('','$code',NOW(),'" . $staff_id . "', '" . $shipping_from . "','$site')";
        $DB->query($str);
    }
    //check for fumigation
    if ("" != $fumigation_from) {
        $job_history[] = array('comments' => 'Added New Fumigation From notes');
        $str           = "INSERT INTO `world`.`fumigation_from` VALUES ('','$code',NOW(),'" . $staff_id . "', '" . $fumigation_from . "','$site')";
        $DB->query($str);
    }
    //check for certificates
    if ("" != $certificates_from) {
        $job_history[] = array('comments' => 'Added New Certificates From notes');
        $DB->query(sprintf(
            "INSERT INTO `world`.`certificates_from` (`country`, `date`, `user`, `comments`, `site`) VALUES (%s, NOW(), %s, %s, %s)",
            $DB->escape($code),
            $DB->escape($staff_id),
            $DB->escape($certificates_from),
            $DB->escape($site)
        ));
    }
    $map_path  = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/images/maps/"; // target path to store all the map images...
    $plug_path = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/images/plugs/"; // target path to store all the plug images...
    // now check for file upload - map
    if ("" != $_FILES['map_file']['name']) {
        $file_orig   = basename($_FILES['map_file']['name']);
        $file_ext    = substr($file_orig, strrpos($file_orig, '.'));
        $target_path = $map_path . $file_orig;
        //echo 'Map image; Name = '.$_FILES['map_file']['name'].'<br />';
        /*Commented out; code doesn't work on local server --- UNCOMMENT WHEN UPLODING TO LIVE SERVER*/
        if (move_uploaded_file($_FILES['map_file']['tmp_name'], $target_path)) {
            $job_history[] = array('comments' => 'New Map images uploaded');
            echo "The map image " . $file_orig . " has been uploaded";
        } else {
            echo "There was an error uploading the map image, please try again!";
        }

        //*/
        //now update the database
        $desc = $_POST['map_desc'];
        if ("" == $desc) {
            $desc = $_FILES['map_file']['name'];
        }

        $str = "INSERT INTO `world`.`map` VALUES ('','$target_path','$code','" . $desc . "')";
        $DB->query($str);
    }
    // now check for file upload - plug type
    if ("" != $_FILES['plug_file']['name']) {
        $target_path = $plug_path . basename($_FILES['plug_file']['name']);
        //echo 'Plug type; Name = '.$_FILES['plug_file']['name'].'<br />';
        /*Commented out; code doesn't work on local server --- UNCOMMENT WHEN UPLODING TO LIVE SERVER*/
        if (move_uploaded_file($_FILES['plug_file']['tmp_name'], $target_path)) {
            $job_history[] = array('comments' => 'New Plug images uploaded');
            echo "The plug image " . basename($_FILES['plug_file']['name']) . " has been uploaded successfully";
        } else {
            echo "There was an error uploading the plug image, please try again!";
        }

        //*/
        //now update the database
        $desc = $_POST['plug_desc'];
        if ("" == $desc) {
            $desc = $_FILES['plug_file']['name'];
        }

        $DB->query(sprintf(
            "INSERT INTO `world`.`country_plugs` (`country`, `plug_path`, `description`) VALUES (%s, %s, %s)",
            $DB->escape($code),
            $DB->escape($target_path),
            $DB->escape($desc)
        ));
    }
    //Now update the Job History.
    for ($i = 0; $i < count($job_history); $i++) {
        $sql = "INSERT INTO `world`.`job_history` VALUES ('','$code',CURDATE(),'" . $staff_id . "','$site','" . $job_history[$i]['comments'] . "')";
        $DB->query($sql);
    }
    echo '<br />Country update Sucessfully<br />';
    display_country();
}
function do_delete()
{
    global $DB, $what, $value, $code;

    $str = sprintf(
        "SELECT %s FROM `world`.`country` WHERE `code` = %s",
        $what,
        $code
    );

    $rz  = $DB->query_first($str);
    $ar  = split_str($rz[$what]);
    $new_str = '';
    $value   = replace_decode($value);
    $i       = 0;
    foreach ($ar as $v) {
        if (trim($v) != trim($value)) {
            if (0 == $i) {
                $new_str .= $v;
            } else {
                $new_str .= ',' . $v;
            }

            $i++;
        }
    }
    //echo 'Value passed = '.$value.'<br />';
    //echo '<h1>Updated String = '.$new_str.'</h1>';
    $up_str = sprintf(
        "UPDATE `world`.`country` SET %s = %s WHERE `code` = %s LIMIT 1",
        $what,
        $DB->escape($new_str),
        $DB->escape($code)
    );
    $DB->query($up_str);
?>
<script type="text/javascript">
//<![CDATA[
refresh_close();
//]]>
</script>
<?php
}
function delete_image()
{
    global $DB, $code, $map_id, $plug_id;
    $up_str  = "";
    $del_str = "";
    $map     = false;
    $plug    = false;
    // firstly have to get the file name and then delete the reference form the database and then unlink the file
    if ("" != $map_id) {
        $map     = true;
        $up_str  = "SELECT * FROM `world`.`map` WHERE id = '$map_id'";
        $del_str = "DELETE FROM `world`.`map` WHERE id = '$map_id'";
    } else if ("" != $plug_id) {
        $up_str  = sprintf(
            "SELECT * FROM `world`.`country_plugs` WHERE `id` = %d LIMIT 1",
            $plug_id
        );
        $del_str = sprintf(
            "DELETE FROM `world`.`country_plugs` WHERE `id` = %d LIMIT 1",
            $plug_id
        );
    }
    $row = $DB->query_first($up_str);
    $DB->query($del_str);
    if ($map) {
        unlink($row['file_path']);
    } else {
        unlink($row['plug_path']);
    }
?>
Image deleted successfully......Page will automatically close in 3 seconds
<script type="text/javascript">
//<![CDATA[
window.setTimeout("refresh_close()", 3000);
//]]>
</script>
<?php
}
/***********************************************************************************************/
// This function draws the header of the World Summary
/***********************************************************************************************/
function draw_header_row()
{
    global $fields, $order, $by;
    reset($fields);
    echo "<br />";
    echo '<table aligh="left" width="95%" align="center" cellpadding="4" cellspacing="1">';
    echo '<tr bgcolor="#3399FF">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
        echo "<a href=\"?&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo ucwords(str_replace('_', ' ', $field));
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="../' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>';
        next($fields);
    }
    echo '</tr>';
}
/***********************************************************************************************/
// This function draws the header when print summary is selected
/***********************************************************************************************/
function print_list($country, $name)
{
    global $DB;
?>
<table width="10%" align="center" cellpadding="3" cellspacing="2">
<tr align="center" >
<td nowrap="nowrap" colspan="2" align="center"><h2>
<?php
if ('HGR' == $GLOBALS['site']) {
        echo 'HIGHWAY GROUP:';
    } else {
        echo 'COINOP EXPRESS';
    }

?>
<br />Country Summary
</h2></td><br />
</tr>
<tr align="center">
<td nowrap="nowrap" align="right">Printed By :</td>
<td nowrap="nowrap" align="left"><?php
echo $name; ?> </td>
</tr>
<tr align="center">
<td nowrap="nowrap" align="right">Print Date :</td>
<td nowrap="nowrap" align="left"><?php
echo date("l d of F Y"); ?> </td>
</tr>
<tr align="center">
<td nowrap="nowrap" align="right">Country :</td>
<td nowrap="nowrap" align="left"><?php echo $country ?>
</td>
</tr>
</table>
<?php
}
function display_entries_main($start, $limit)
{
    global $DB, $order, $by, $page, $what, $value, $action;
    $table = 'country';
    $qry   = "";
    if ("" != $what) {
        $qry = "SELECT COUNT(*) FROM `world`.$table WHERE $what LIKE '%$value%' LIMIT 0,$limit";
    } else if ("" != $value) {
        if ("time" == $value) {
            $qry = "SELECT COUNT(*) FROM `world`.$table LIMIT 0,$limit";
        } else {
            $qry = "SELECT COUNT($value) FROM `world`.$table LIMIT 0,$limit";
        }

    } else {
        $qry = "SELECT COUNT(*) FROM `world`.$table LIMIT 0," . $limit;
    }

    $res   = $DB->query_first($qry);
    $pages = '';
    $count = $res[0];
    $limit = number_format($limit, 0, '.', '');
    $count = number_format($count, 0, '.', '');
    $pages = floor($count / $limit);
    if (0 != $count) {
        if ($count < $limit) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' </b>';
        } else if ($start + $limit > $count) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
        } else {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
        }

    }
    $orig = $start;
    $new  = 0;
    for ($i = 0; $i <= $pages; $i++) {
        if (0 == $new && 0 == $orig || $orig == $new) {
            if ($count > $limit) {
                if ($i == $pages) {
                    echo '<b>' . ($start + 1) . ' - ' . ($count) . ' | </b>';
                } else {
                    echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
                }

            }
            $new = $start + $limit;
        } else {
            $link = "action=$action&start=$new&by=$by&order=$order&what=$what&value=$value";
            if ($i == $pages) {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($count) . '</a>';
            } else {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
            }

            $new += $limit;
        }
    }
    echo '</center>';
}
function display_letter()
{
    global $DB;
    echo '<table width=95% align="center" cellpadding=7><td bgcolor=#FAFAFA><div align="center"><b>By Letter :</b> ';
    for ($i = 'A';; $i++) {
        $qry = sprintf(
            "SELECT * FROM `world`.`country` WHERE `name` LIKE %s",
            $DB->escape($i . '%')
        );
        $res = $DB->query($qry);
        if (mysqli_num_rows($res) > 0) {
            echo '<a href="report.php?what=alpha&value=' . $i . '" target="_blank">' . $i . '</a>&nbsp;';
        } else {
            echo '<font color="#999999">' . $i . '</font>&nbsp;';
        }

        if ("Z" == $i) {
            break;
        }

    }
    echo '</div></td></table>';
}
function ava_time($zone)
{
    global $DB;

    return !!$DB->query_first(sprintf(
        "SELECT 1 FROM %s WHERE `name` = %s LIMIT 1",
        "`world`.`time_zones`",
        $DB->escape($zone)
    ), MYSQLI_ASSOC);
}
function display_time($zone, $i)
{
    global $DB;

    $tz = $DB->query_first(sprintf(
        "SELECT * FROM %s WHERE `name` = %s LIMIT 1",
        "`world`.`time_zones`",
        $DB->escape($zone)
    ), MYSQLI_ASSOC);

    $city_time         = array();
    $city["Name"]      = $tz['name'];
    $city["GMT"]       = $tz['offset'];
    $city["actualDST"] = 0.0;
    if ('y' == $tz['day_light']) {
        $city["actualDST"] = 1.0;
    }

    $gmt_diff      = $city["GMT"] + $city["actualDST"];
    $city_time[$i] = time() + ($gmt_diff * 3600);
    echo '<td><!--<img id="hrs1' . $i . '" height="20" src="clock/0.gif"><img id="hrs2' . $i . '" height="20" src="clock/0.gif"><img id="col1' . $i . '" height="20" src="clock/col.gif"><img id="mins1' . $i . '" height="20" src="clock/0.gif"><img id="mins2' . $i . '" height="20" src="clock/0.gif"><img id="col2' . $i . '" height="20" src="clock/col.gif"><img id="sec1' . $i . '" height="20" src="clock/0.gif"><img id="sec2' . $i . '" height="20" src="clock/0.gif"><img id="ampm' . $i . '" height="20" src="clock/am.gif"><br />--><span id="LiveClockIE' . $i . '"></span></td>';
?>
<script type="text/javascript">
//<![CDATA[
var strClock<?=$i?>     = <?=json_encode(gmdate("M d, Y H:i:s", $city_time[$i]))?>;
var intClock<?=$i?>Diff = syncClock(<?=$i?>);
function update<?=$i?>()
{
    //window.setTimeout(update<?=$i?>, 1000);
    localTime    = new Date();
    intLocalTime = localTime.getTime();
<?php
update($i);
?>
}
function syncClock(clockID)
{
    localTime    = new Date();
    intLocalTime = localTime.getTime();
    if (clockID == <?=$i?>) {
        intClockTime = Date.parse(strClock<?=$i?>);
    }
    intClockDifference = intLocalTime - intClockTime;
    return intClockDifference;
}
<?php
//splay_clock();
?>
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
update<?=$i?>();
//]]>
</script>
<?php
}
function update($i, $date = true, $display = false)
{
    echo 'intRequiredTime' . $i . ' = intLocalTime - intClock' . $i . 'Diff;';
    echo 'var clock' . $i . ' = new Date();';
    echo 'clock' . $i . '.setTime(intRequiredTime' . $i . ');';
    echo 'var dot = ":";';
    if ($display) {
        echo 'display_clock(' . $i . ',clock' . $i . ');';
    }

    echo 'var date' . $i . ' = clock' . $i . '.getDate();';
    echo 'var month' . $i . ' = clock' . $i . '.getMonth();';
    echo 'var year' . $i . ' = clock' . $i . '.getYear();';
    echo 'var hours' . $i . ' = clock' . $i . '.getHours();';
    echo 'if(hours' . $i . ' <= 9){ hours' . $i . ' = \'0\'+hours' . $i . ';}';
    echo 'var minutes' . $i . ' = clock' . $i . '.getMinutes();';
    echo 'if(minutes' . $i . ' <= 9){ minutes' . $i . ' = \'0\'+minutes' . $i . ';}';
    echo 'var seconds' . $i . ' = clock' . $i . '.getSeconds();';
    echo 'if(seconds' . $i . ' <= 9){ seconds' . $i . ' = \'0\'+seconds' . $i . ';}';
    if (!$display) {
        echo 'var strClock' . $i . ' = hours' . $i . '+dot+minutes' . $i . ';'; //+dot+seconds'.$i.'
        echo 'strClock' . $i . ' += \' \'+ DaysOfWeek[clock' . $i . '.getDay()]+" "+date' . $i . '+", "+monthname[month' . $i . '];';
    } else {
        echo 'strClock' . $i . ' = \' \'+ DaysOfWeek[clock' . $i . '.getDay()]+" "+date' . $i . '+", "+monthname[month' . $i . '];';
    }

    if ($date) {
        echo 'LiveClockIE' . $i . '.innerHTML = "<b>"+strClock' . $i . '+"<b>";';
    }

}
/* Function for the live graphic clock, but not needed anymore as static clock is prefered......JS (4/10/05)
function display_clock(){
echo '
function display_clock(id,clock){
ampmtime = clock.getHours() - 12;
thisHrs = \'\' + clock.getHours() + \'\';
thisMin = \'\' + clock.getMinutes() + \'\';
thisSec = \'\' + clock.getSeconds() + \'\';
var ap = "ampm"+id;
var h1 = "hrs1"+id;
var h2 = "hrs2"+id;
var m1 = "mins1"+id;
var m2 = "mins2"+id;
var s1 = "sec1"+id;
var s2 = "sec2"+id;

if (thisHrs > 9) {
if (thisHrs >= 12) {
document.getElementById(ap).src = \'clock/pm.gif\';
if (thisHrs==12)
newHrs=\'\'+12+\'\';
if (thisHrs > 12) {
newHrs = \'\' + ampmtime + \'\';
}
if (newHrs <= 9) {
document.getElementById(h1).src = dig[0];
document.getElementById(h2).src = dig[newHrs.charAt(0)];
}
if (newHrs > 9) {
document.getElementById(h1).src = dig[newHrs.charAt(0)];
document.getElementById(h2).src = dig[newHrs.charAt(1)];
}
}
else {
document.getElementById(ap).src = \'clock/am.gif\';
document.getElementById(h1).src = dig[thisHrs.charAt(0)];
document.getElementById(h2).src = dig[thisHrs.charAt(1)];
}
}
if (thisHrs <= 9) {
document.getElementById(ap).src = \'clock/am.gif\';
if (thisHrs == 0) {
document.getElementById(h1).src = dig[1];
document.getElementById(h2).src = dig[2];
}
else {
document.getElementById(h1).src = dig[0];
document.getElementById(h2).src = dig[thisHrs.charAt(0)];
}
}
if (thisMin > 9) {
document.getElementById(m1).src = dig[thisMin.charAt(0)];
document.getElementById(m2).src = dig[thisMin.charAt(1)];
}
if (thisMin <= 9) {
document.getElementById(m1).src = dig[0];
document.getElementById(m2).src = dig[thisMin.charAt(0)];
}
if (thisSec > 9) {
document.getElementById(s1).src = dig[thisSec.charAt(0)];
document.getElementById(s2).src = dig[thisSec.charAt(1)];
}
if (thisSec <= 9) {
document.getElementById(s1).src = dig[0];
document.getElementById(s2).src = dig[thisSec.charAt(0)];
}
}
';
}*/
function write_global_JS_funcs()
{
?>
<html>
<script type="text/javascript">
//<![CDATA[
var DaysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
var monthname  = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

dig = new Image();
dig[0] = 'clock/0.gif';
dig[1] = 'clock/1.gif';
dig[2] = 'clock/2.gif';
dig[3] = 'clock/3.gif';
dig[4] = 'clock/4.gif';
dig[5] = 'clock/5.gif';
dig[6] = 'clock/6.gif';
dig[7] = 'clock/7.gif';
dig[8] = 'clock/8.gif';
dig[9] = 'clock/9.gif';

function conf()
{
    if (confirm('Are you sure you to delete the value selected')) {
        return true;
    } else {
        return false;
    }
}
function refresh_close()
{
    opener.location.href = opener.location.href;
    this.close();
}
function refresh()
{
    opener.location.href = opener.location.href;
}
function test(zone)
{
    alert("Time zone = " + zone);
}
function pviiClassNew(obj, new_style)
{
    obj.className = new_style;
}
//]]>
</script>
<?php
}
function filename($str)
{
    $tok = strtok($str, "/");
    $ret = '';
    while ("" != $tok) {
        $ret = $tok;
        $tok = strtok("/");
    }
    return $ret;
}
function format_date($d, $t = true)
{
    list($date, $time)    = explode(' ', $d);
    list($yr, $mon, $day) = explode('-', $date);
    if ($t) {
        return ($day . '-' . $mon . '-' . $yr . ' ' . $time);
    } else {
        return ($day . '-' . $mon . '-' . $yr);
    }

}
function format_str($str, $link = false, $what = "")
{
    // formats a string based on commas....
    global $code;
    $ar  = split_str($str);
    $ret = '<ul type="square">';
    foreach ($ar as $val) {
        if ("" != $val) {
            if (!$link) {
                $ret .= '<li>' . trim($val) . '</li>';
            } else {
                $ret .= '<li>' . trim($val) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="?code=' . $code . '&action=delete&what=' . $what . '&value=' . replace(trim($val)) . '" onClick="return conf()">Delete</a></li>';
            }

        }
    }
    $ret .= '</ul>';
    return $ret;
}
function replace($val)
{
    return str_replace("#", "{num}", $val);
}
function replace_decode($val)
{
    return str_replace("{num}", "#", $val);
}
function format_str_sum($str)
{
    // makes sure that when summary is printed, the output is proper...
    $ar  = split_str($str, ',');
    $i   = 0;
    $ret = '';
    foreach ($ar as $a) {
        if ("" != $a) {
            if (0 == $i) {
                $ret .= $a;
            } else {
                $ret .= ', ' . $a;
            }

            $i++;
        }
    }
    return $ret;
}
function split_str($str, $delim = ',')
{
    // function splits a str based on a delimiter (default is a comma)....and returns as an array
    $ret = array();
    $c   = 0;
    $tok = strtok($str, $delim);
    while ($tok) {
        if ("" != $tok) {
            $ret[$c] = $tok;
            $c++;
        }
        $tok = strtok($delim);
    }
    return $ret;
}
function getsite()
{
    return $GLOBALS['site'];
}
