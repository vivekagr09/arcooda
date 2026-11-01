<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

//include $_SERVER['DOCUMENT_ROOT'] . '/thumbs.php';

//include_once __DIR__ . '/options.php';

//include_once __DIR__ . '/session_lock.php';
if (!function_exists('locked')) {
    function locked($table, $id, $msg = false)
    {
        global $DB;
        global $session_key;

        static $session_locks = null;

        if (is_null($session_locks)) {
            $DB->query(sprintf(
                "DELETE FROM `session_locks` WHERE UNIX_TIMESTAMP(`last_access`) + `timeout` < UNIX_TIMESTAMP(NOW())"
            ));

            $session_locks = [];
            if ($query = $DB->query(sprintf(
                "SELECT * FROM `session_locks` WHERE `mod_table` = %s AND UNIX_TIMESTAMP(`last_access`) + `timeout` > UNIX_TIMESTAMP(NOW())",
                $DB->escape($table)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $session_locks[] = $row;
                }
                $DB->free_result($query);
            }
        }

        foreach ($session_locks as $v) {
            if ($v['row_id'] == $id && $v['mod_table'] == $table && $v['session_key'] != $session_key) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('lock')) {
    function lock()
    {
        global $DB;
        global $session_key, $table, $id;

        if (locked($table, $id)) {
            return;
        }
        if (intval($id) > 0 && '' != $table && '' != $session_key) {
            unlock();
            $user = get_user();
            $DB->query(sprintf(
                "INSERT INTO `session_locks` (`session_key`, `user`, `mod_table`, `row_id`, `last_access`) VALUES (%s, %s, %s, %d, NOW())",
                $DB->escape($session_key),
                $DB->escape($user),
                $DB->escape($table),
                $id
            ));
        }
    }
}
if (!function_exists('unlock')) {
    function unlock()
    {
        global $DB;
        global $session_key, $table, $id;

        if ('' != $table && '' != $session_key) {
            $DB->query(sprintf(
                "DELETE FROM `session_locks` WHERE `session_key` = %s AND mod_table = %s AND row_id = %d",
                $DB->escape($session_key),
                $DB->escape($table),
                $id
            ));
        }
    }
}

if (!function_exists('time_till_free')) {
    function time_till_free($table, $id)
    {
        global $DB;
        global $session_key;

        $time = $DB->query_first(sprintf(
            "SELECT UNIX_TIMESTAMP(`last_access`) + `timeout` - UNIX_TIMESTAMP(NOW()) AS `time_left` FROM `session_locks` WHERE `mod_table` = %s AND `row_id` = %d AND `session_key` <> %s ORDER BY `last_access` DESC",
            $DB->escape($table),
            $id,
            $DB->escape($session_key)
        ), MYSQLI_ASSOC);

        $time_left = $time['time_left'];
        $time_str = '';
        if ($time_left > 3600) {
            $time_str .= intval($time_left / 3600)." Hours ";
        }
        if ($time_left > 60) {
            $time_str .= intval(($time_left % 3600) / 60)." Minutes ";
        }
        $time_str .= ($time_left % 60)." Seconds";

        return $time_str;
    }
}

if (!function_exists('get_locking_user')) {
    function get_locking_user($table, $id)
    {
        global $DB;
        global $session_key;

        $result = $DB->query_first(sprintf(
            "SELECT `user` FROM `session_locks` WHERE `mod_table` = %s AND row_id = %d AND session_key <> %s ORDER BY `last_access` DESC",
            $DB->escape($table),
            $id,
            $DB->escape($session_key)
        ), MYSQLI_ASSOC);

        return $result['user'];

    }
}

if (!function_exists('locked_msg')) {
    function locked_msg($table, $id)
    {
?>
<table width="90%" border="0" cellspacing="0" cellpadding="1" align="center"><tr><td bgcolor="#000000"><table width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td bgcolor="pink"><div align="center"><strong>This record is currently accessed by <?= get_locking_user($table, $id) ?>, please wait for <?= time_till_free($table, $id) ?> and try again.</strong></div></td></tr></table></td></tr></table><br /><br />
<?php
    }
}

if (!function_exists('show_locked_msg')) {
    function show_locked_msg($table, $id)
    {
        if (locked($table, $id)) {
            locked_msg($table, $id);
        }
    }
}

//include_once __DIR__ . '/errors.php';

//lock();

//include __DIR__ . '/more_links.php';


if (!function_exists('formatSQLString')) {
    function formatSQLString($instring)
    {
        global $DB;
        $instring = str_replace("\r\n", "\n", $instring);
        $instring = str_replace("\n\r", "\n", $instring);
        $instring = str_replace("\r", "\n", $instring);
        $result   = $DB->escape_string($instring);
        return "'" . $result . "'";
    }
}

if (!function_exists('niceusername')) {
    function niceusername($user)
    {
        global $DB;

        $row  = $DB->query_first("SELECT `user`, `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `user` = " . $DB->escape($user) . " LIMIT 1");
        $name = trim($row['first_name'] . ' ' . $row['last_name']);
        if ('' == $row['first_name']) {
            return $user;
        } else {
            return $name;
        }

    }
}

if (!function_exists('titleCase')) {
    function titleCase($string)
    {
        $len    = strlen($string);
        $i      = 0;
        $last   = "";
        $new    = "";
        $string = strtoupper($string);
        while ($i < $len) {
            $char = substr($string, $i, 1);
            if (preg_match("/[A-Z]/", $last)) {
                $new .= strtolower($char);
            } else {
                $new .= strtoupper($char);
            }

            $last = $char;
            $i++;
        }
        $new = str_replace('_', ' ', $new);
        return ($new);
    }
}

if (!function_exists('check_image_dir')) {
    function check_image_dir($img_dir, $create = true)
    {
        if (!is_dir($img_dir)) {
            if (true == $create) {
                return mkdir($img_dir, 0777);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

if (!function_exists('add_image')) {
    function add_image($img_source, $img_dest)
    {
        global $img_location_abs, $img_location;
        if (!(file_exists($img_source) && is_file($img_source))) {
            echo "Image Source Error. Uploaded file not found.<br>";
            return '';
        }
        $image_info = getImageSize($img_source);
        if (1 == $image_info[2]) {
            $file_ext = '.gif';
        } elseif (2 == $image_info[2]) {
            $file_ext = '.jpg';
        } elseif (3 == $image_info[2]) {
            $file_ext = '.png';
        }

        $img_dest_abs = $img_location_abs . $img_dest . $file_ext;
        $fp           = copy($img_source, $img_dest_abs);
        if (!(file_exists($img_dest_abs) && is_file($img_dest_abs))) {
            echo "Image Destionation Error. Uploaded file not found.<br> $img_dest_abs<br><Br>";
            return '';
        }
        echo "Image Process Successful.<br>";
        return $img_location . $img_dest . $file_ext;
    }
}

if (!function_exists('process_image')) {
    function process_image($img_source, $dst_img_name, $width = 200, $height = 200, $img_loc = '')
    {
        if ('' == $img_loc) {
            global $img_location_abs;
        } else {
            $img_location_abs = $img_loc;
        }

        if (!(file_exists($img_source) && is_file($img_source))) {
            echo "Image Source Error. Uploaded file not found.<br>";
            return false;
        }
        $image_info = getImageSize($img_source);
        if (1 == $image_info[2]) {
            $file_ext = '.gif';
        } elseif (2 == $image_info[2]) {
            $file_ext = '.jpg';
        } elseif (3 == $image_info[2]) {
            $file_ext = '.png';
        }

        if (isset($file_ext) == false) {
            return false;
        }

        $img_dst = $img_location_abs . $dst_img_name . $file_ext;
        echo "::: UPLOADING $img_source TO $img_dst :::";
        $fp = copy($img_source, $img_dst);
        if (!(file_exists($img_dst) && is_file($img_dst))) {
            echo "Image Destination Error. Uploaded file not found.<br>";
            return false;
        }
        resize($img_dst, $img_location_abs . $dst_img_name . '_sml' . $file_ext);
        resize($img_dst, $img_location_abs . $dst_img_name . '_med' . $file_ext, 140, 140, 0);
        echo "Image Process Successful.<br>";
        return $dst_img_name . $file_ext;
    }
}

if (!function_exists('fitImage')) {
    function fitImage($img, $width = 200, $height = 200)
    {
        global $img_location_abs;
        resize($img_location_abs . $img, $img_location_abs . $img, $width, $height);
    }
}

if (!function_exists('show_links')) {
    function show_links($links)
    {
        //show more links.
        if (!(is_array($links) && count($links) > 1)) {
            return;
        }

        //the << link to a previous set of links
        echo "<DIV ALIGN='center'><FONT FACE='Verdana, Arial, Helvetica, sans-serif' SIZE='2'><b>Showing : $links[start_from] - $links[ends_at] of $links[max] | </b>";
        if (isset($links['url_prev_page']) && $prev_page = $links['url_prev_page']) {
            echo " <A HREF=\"$prev_page\"><<</A> | ";
        }

        //for each of the links
        for ($i = 0; $i < count($links['links']); $i++) {
            //get the link ID
            $key = key($links['links']);
            //if the link contains an URL, show it
            if (isset($links['links'][$key]['url']) && $url = $links['links'][$key]['url']) {
                echo " <A HREF=\"$url\">" . $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'] . "</A>";
            } else
            //else just show where the records starts and ends
            {
                echo $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'];
            }

            echo ($i < count($links['links']) - 1) ? " | " : "";
            next($links['links']);
        }
        //the >> link to the next set of links
        if (isset($links['url_next_page']) && $next_page = $links['url_next_page']) {
            echo " <A HREF=\"$next_page\">>></A> ";
        }

        echo '</FONT></DIV>';
    }
}

if (!function_exists('load_hidden_vars')) {
    function load_hidden_vars()
    {
        /*
        for ($i = 0;$i < sizeof($_POST); $i++)
        {
        $key = key($_POST);
        //if ($_POST[$key] != '')
        echo '<input type="hidden" name="'.$key.'" value="'.$_POST[$key].'">';
        next($_POST);
        }*/
        for ($i = 0; $i < sizeof($_GET); $i++) {
            $key = key($_GET);
            if ('' != $_GET[$key]);
            echo '<input type="hidden" name="' . $key . '" value="' . $_GET[$key] . '">';
            next($_GET);
        }
    }
}

if (!function_exists('get_vars')) {
    function get_vars()
    {
        $gets = '?';
        for ($i = 0; $i < sizeof($_GET); $i++) {
            $key = key($_GET);
            if ('action' != $key && 'id' != $key) {
                if ('' != $_GET[$key]);
                $gets .= $key . '=' . $_GET[$key];
                if ($i < sizeof($_GET) - 1) {
                    $gets .= '&';
                }

            }
            next($_GET);
        }
        return $gets;
    }
}

if (!function_exists('addCalendarHeader')) {
    function addCalendarHeader()
    {
    }
}

if (!function_exists('addCalendarBtn')) {
    function addCalendarBtn($field_name)
    {
        echo "<input name=\"popcal\" type=\"button\" value=\" V \" onClick=\"gfPop.fPopCalendar(this.form.$field_name)\">";
    }
}

if (!function_exists('initCurConvert')) {
    function initCurConvert()
    {
        global $DB;
?>
<script type="text/javascript">
//<![CDATA[
var cValues = <?=json_encode(getExchangeRateList())?>;
function setRate(tRate, tCurrency) {
	var tIndex = tCurrency.options[tCurrency.selectedIndex].value;
	tRate.value = cValues[tIndex];
}
function getRate(tCurrency) {
	return cValues[tCurrency];
}
function convert(fValue, fCurrency, tValue, tCurrency) {
	var fIndex = fCurrency.options[fCurrency.selectedIndex].value;
	var tIndex = tCurrency.options[tCurrency.selectedIndex].value;
	result = (cValues[fIndex]) / ((cValues[tIndex]) / (fValue.value));
	tValue.value = (Math.round(result * 100) / 100);
}
//]]>
</script>
<?php
}
}

if (!function_exists('unhtmlentities')) {
    function unhtmlentities($string)
    {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }
}

if (!function_exists('encode_product_url')) {
    function encode_product_url($item_name, $id)
    {
        $item_name = unhtmlentities($item_name);

        $item_name = str_replace(['/', '-', '_', '+', '=', '&'], ' ', $item_name);

        // Remove more than one white-spaces
        $item_name = preg_replace('/\s\s+/', ' ', $item_name);

        $item_name = str_replace(' ', '_', $item_name); // Original _
        $item_name = str_replace('\\', '', $item_name);
        $item_name = str_replace('/', '-', $item_name);
        $item_name = urlencode($item_name);

        return $item_name . '-' . $id; // Original _
    }
}

if (!function_exists('decode_product_url')) {
    function decode_product_url($url)
    {
        $end_url = strrchr($url, '_');
        $id      = substr($end_url, 1, strlen($end_url) - strrpos($end_url, '.'));
        return intval($id);
    }
}

if (!function_exists('html_or_bar')) {
    function html_or_bar($text = 'OR')
    {
?>
<!-- OR -->
<table width="100%" border="0" cellspacing="2" cellpadding="1"><tr>
	<td width="49%"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000"><img src="/_.gif" width="1" height="1" alt="" /></td></tr></table></td>
	<td width="2%" nowrap><div align="center">&nbsp;&nbsp;<?=$text?>&nbsp;&nbsp;</div></td>
	<td width="49%"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000"><img src="/_.gif" width="1" height="1" alt="" /></td></tr></table></td>
</tr></table>
<!-- //OR -->
<?php
}
}

/*****************************************************************/
/*****************************************************************/

if (!function_exists('add_slashes_array')) {
    function add_slashes_array($array, $htmlspecialchars = false)
    {
        if (is_string($array)) {
            if (true == $htmlspecialchars) {
                return addslashes(htmlspecialchars($array));
            } else {
                return addslashes($array);
            }
        } elseif (is_array($array)) {
            for ($i = 0; $i < count($array); $i++) {
                $key         = key($array);
                $array[$key] = add_slashes_array($array[$key], $htmlspecialchars);
                next($array);
            }
            reset($array);
            return $array;
        }
    }
}

if (!function_exists('strip_slashes_array')) {
    function strip_slashes_array($array, $htmlspecialchars = false)
    {
        if (is_string($array)) {
            if (htmlspecialchars == true) {
                return stripslashes(htmlspecialchars($array));
            } else {
                return stripslashes($array);
            }
        } elseif (is_array($array)) {
            for ($i = 0; $i < count($array); $i++) {
                $key         = key($array);
                $array[$key] = strip_slashes_array($array[$key], $htmlspecialchars);
                next($array);
            }
            reset($array);
            return $array;
        }
    }
}

if (!function_exists('get_fields')) {
    function get_fields($array)
    {
        if (!is_array($array)) {
            echo 'data not an array';
            return null;
        }
        $str = [];
        foreach ($array as $k => $v) {
            $str[] = "`" . $k . "`";
        }

        return implode(", ", $str);
    }
}

if (!function_exists('get_values')) {
    function get_values($array)
    {
        global $DB;

        if (!is_array($array)) {
            echo 'data not an array';
            return null;
        }
        $str = [];
        foreach ($array as $k => $v) {
            $str[] = $DB->escape($v);
        }

        return implode(", ", $str);
    }
}

if (!function_exists('get_set_values')) {
    function get_set_values($array)
    {
        global $DB;

        if (!is_array($array)) {
            echo 'data not an array';
            return null;
        }
        $str = [];
        foreach ($array as $k => $v) {
            $str[] = "`" . $k . "` = " . $DB->escape($v);
        }

        return implode(", ", $str);
    }
}

/*****************************************************************/
/*****************************************************************/

if (!function_exists('requests')) {
    function requests($var, $type = 'POST', $addslashes = false, $htmlspecialchars = false)
    {
        if ('REQUEST' == $type) {
            $type = 'GET';
        }

        if ('POST' == $type && isset($_POST[$var])) {
            $variable = $_POST[$var];
        } elseif ($type = 'GET' && isset($_GET[$var])) {
            $variable = $_GET[$var];
        } elseif ($type = 'SERVER' && isset($_SERVER[$var])) {
            $variable = $_SERVER[$var];
        } elseif ($type = 'REQUEST' && isset($_REQUEST[$var])) {
            $variable = $_REQUEST[$var];
        } elseif ($type = 'COOKIE' && isset($_COOKIE[$var])) {
            $variable = $_COOKIE[$var];
        } elseif ($type = 'FILES' && isset($_FILES[$var])) {
            $variable = $_FILES[$var];
        } else {
            return null;
        }

        if (true == $addslashes) {
            return addslashesArray($variable, $htmlspecialchars);
        } else {
            return $variable;
        }

        return null;
    }
}

//===========================================================================
// Encode the section/category to URL string
//
// PARAMS: the section string
// RETURN: encode section/category URL string
//===========================================================================
if (!function_exists('encode_section_url')) {
    function encode_section_url($section)
    {
        $section = str_replace(['/', '-', '_', '+', '=', '&'], ' ', $section);

        // Remove more than one white-spaces
        $section = preg_replace('/\s\s+/', ' ', $section);

        $section = str_replace(" ", '-', $section); // Original _
        $section = strtolower($section);
        $section = urlencode($section);

        return $section;
    }
}

//===========================================================================
//
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
if (!function_exists('tooltip')) {
    function tooltip($string)
    {
        //$string = ereg_replace("\"", "&quot;", $string);
        $string = str_replace("\"", "&quot;", $string);
        //$string = ereg_replace("'", "\\'", $string);
        $string = str_replace("'", "\\'", $string);
        echo "onMouseOver=\"showTooltip('dHTMLToolTip',event, '$string', '#ffff99','#000000','#000000','16000')\"";
        echo "onMouseOut=\"hideTooltip('dHTMLToolTip')\"";
    }
}

if (!function_exists('get_agent')) {
    function get_agent()
    {
        $AGT       = $_SERVER["HTTP_USER_AGENT"];
        $st_sys    = '';
        $st_ver    = '';
        $st_sysver = '';
        /* Browser detection */
        if (preg_match("/(opera) (\\d{1,2}.\\d{1,3}){0,1}/i", $AGT, $st_regs) || preg_match("@(opera/)(\\d{1,2}.\\d{1,3}){0,1}@i", $AGT, $st_regs)) {
            $st_brows = "OP";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("@(konqueror)/(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "KONQ";
            $st_ver   = $st_regs[2];
            $st_sys   = "Linux";
        }
        elseif (preg_match("@(lynx)/(\\d{1,2}.\\d{1,2}.\\d{1,2})@i", $AGT, $st_regs)) {
            $st_brows = "Lynx";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("/(links) \((\\d{1,2}.\\d{1,3})/i", $AGT, $st_regs)) {
            $st_brows = "Links";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("@(omniweb/)(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "OmniWeb";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("@(webtv/)(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "WebTV";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("/(msie) (\\d{1,2}.\\d{1,3})/i", $AGT, $st_regs)) {
            $st_brows = "IE";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("@(netscape6)/(6.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "NS";
            $st_ver   = $st_regs[2];
        }
        elseif (preg_match("@mozilla/5@i", $AGT)) {
            $st_brows = "NS";
            $st_ver   = '6';
        }
        elseif (preg_match("@(mozilla)/(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "NS";
            $st_ver   = $st_regs[2];
        } elseif (false !== stripos($AGT, "w3m")) {
            $st_brows = "w3m";
        } elseif (preg_match("/(scooter)-(\\d{1,2}.\\d{1,3})/i", $AGT, $st_regs)) {
            $st_brows = "Scooter";
            $st_ver   = $st_regs[2];
        } elseif (preg_match("@(w3c_validator)/(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "W3C";
            $st_ver   = $st_regs[2];
        } elseif (preg_match("@(googlebot)/(\\d{1,2}.\\d{1,3})@i", $AGT, $st_regs)) {
            $st_brows = "Google";
            $st_ver   = $st_regs[2];
        } else {
            $st_brows = "";
            $st_ver   = "";
        }
        /* System detection */
        if (false !== stripos($AGT, "linux")) {
            $st_sys = "Linux";
        } elseif (false !== stripos($AGT, "Win 9x 4.90")) {
            $st_sys = "WinMe";
        } elseif (false !== stripos($AGT, "win32")) {
            $st_sys = "Win";
        } elseif (false !== stripos($AGT, "windows 2000")) {
            $st_sys = "Win2000";
        } elseif ((preg_match("/(win)(\\d{2})/i", $AGT, $st_regs)) || (preg_match("/(windows) (\\d{2})/i", $AGT, $st_regs))) {
            $st_sys = "Win" . $st_regs[2];
        } elseif (preg_match("/(windows nt)( ){0,1}(5.0)/i", $AGT, $st_regs)) {
            $st_sys = "Win2000";
        } elseif (preg_match("/(windows nt)( ){0,1}(5.1)/i", $AGT, $st_regs)) {
            $st_sys = "WinXP";
        } elseif (preg_match("/(winnt)(\\d{1,2}.\\d{1,2}){0,1}/i", $AGT, $st_regs)) {
            $st_sys = "WinNT" . $st_regs[2];
        } elseif (preg_match("/(windows nt)( ){0,1}(\\d{1,2}.\\d{1,2}){0,1}/i", $AGT, $st_regs)) {
            $st_sys = "WinNT" . $st_regs[3];
        } elseif (false !== stripos($AGT, "PPC") || false !== stripos($AGT, "Mac_PowerPC")) {
            $st_sys = "MacPPC";
        } elseif (false !== stripos($AGT, "mac")) {
            $st_sys = "Mac";
        } elseif (preg_match("/(sunos) (\\d{1,2}.\\d{1,2}){0,1}/i", $AGT, $st_regs)) {
            $st_sys    = "SunOS";
            $st_sysver = $st_regs[2];
        } elseif (preg_match("/(beos) r(\\d{1,2}.\\d{1,2}){0,1}/i", $AGT, $st_regs)) {
            $st_sys    = "BeOS";
            $st_sysver = $st_regs[2];
        } elseif (false !== stripos($AGT, "freebsd")) {
            $st_sys = "FreeBSD";
        } elseif (false !== stripos($AGT, "openbsd")) {
            $st_sys = "OpenBSD";
        } elseif (false !== stripos($AGT, "irix")) {
            $st_sys = "IRIX";
        } elseif (false !== stripos($AGT, "os/2")) {
            $st_sys = "OS/2";
        } elseif (false !== stripos($AGT, "plan9")) {
            $st_sys = "Plan9";
        } elseif (false !== stripos($AGT, "unix") || false !== stripos($AGT, "hp-ux")) {
            $st_sys = "Unix";
        } elseif (false !== stripos($AGT, "osf")) {
            $st_sys = "OSF";
        } elseif (false !== stripos($AGT, "X11") && !isset($st_sys)) {
            $st_sys = "Unix";
        }
        if ('' != $st_brows || '' != $st_sys) {
            $new_agt['browser'] = $st_brows; // browser
            $new_agt['version'] = $st_ver; // version
            $new_agt['os']      = $st_sys; // system

        } else {
            $new_agt['browser'] = $AGT;
            $new_agt['version'] = -1; // version
            $new_agt['os']      = 'unknown'; // system

        }
        return ($new_agt);
    }
}

//===========================================================================
// displays the alphabetical links
//
// PARAMS: the SQL statement, the url link, the current alphabet
// RETURN: n/a
//===========================================================================
if (!function_exists('show_alphabets')) {
    function show_alphabets($statement, $link, $alpha)
    {
        global $DB;
        $result       = $DB->query($statement);
        $alpha_exists = [];
        while ($row = $DB->fetch_array($result)) {
            $alphabet = $row['alphabets'];
            if (preg_match('/^[a-zA-Z]/i', $alphabet)) {
                $alpha_exists[$alphabet] = 1;
            } else {
                $alpha_exists['#'] = 1;
            }

        }
        echo "<br /><table width=90% align=center cellpadding=7><tr><td bgcolor=#FAFAFA><div align=\"center\"><b>";
        show_alpha_links($alpha_exists, $alpha, $link);
        echo "</div></td></tr></table>";
    }
}

//===========================================================================
// displays the alphabetical links
//
// PARAMS: the alphabets, the current alphabet, and the url link
// RETURN: true if alphabets is an array, false otherwise
//===========================================================================
if (!function_exists('show_alpha_links')) {
    function show_alpha_links($alphas, $current, $link)
    {
        if (!is_array($alphas)) {
            return false;
        }

        if (isset($alphas['#'])) {
            echo '<a href="' . $link . '0">';
            echo $current == '0' ? '<font color="black">#</font>' : '#';
            echo '</a>&nbsp;' . "\n";
        } else {
            echo '<font color="#999999">#</font>&nbsp;' . "\n";
        }

        //for each of the 26 alphabets
        for ($i = 0; $i < 26; $i++) {
            $alpha = chr(65 + $i);
            if (isset($alphas[$alpha])) {
                echo '<a href="' . $link . $alpha . '">';
                echo $current == $alpha ? '<font color="black">' . $alpha . '</font>' : $alpha;
                echo '</a>&nbsp;' . "\n";
            } else {
                echo '<font color="#999999">' . $alpha . '</font>&nbsp;' . "\n";
            }

        }
        return true;
    }
}

if (!function_exists('getMIMEType')) {
    function getMIMEType($filename)
    {
        // get base name of the filename provided by user
        $filename = basename($filename);
        // break file into parts seperated by .
        $filename = explode('.', $filename);
        // take the last part of the file to get the file extension
        $filename = $filename[count($filename) - 1];
        // find mime type
        return privFindType($filename);
    }
}

if (!function_exists('privFindType')) {
    function privFindType($ext)
    {
        // create mimetypes array
        $mimetypes = privBuildMimeArray();
        // return mime type for extension
        if (isset($mimetypes[$ext])) {
            return $mimetypes[$ext];
        } else {
            return 'application/octet-stream';
        }
    }
}

if (!function_exists('privBuildMimeArray')) {
    function privBuildMimeArray()
    {
        return [
            "ai"      => "application/postscript",
            "aif"     => "audio/x-aiff",
            "aifc"    => "audio/x-aiff",
            "aiff"    => "audio/x-aiff",
            "asc"     => "text/plain",
            "au"      => "audio/basic",
            "avi"     => "video/x-msvideo",
            "bcpio"   => "application/x-bcpio",
            "bin"     => "application/octet-stream",
            "bmp"     => "image/bmp",
            "cdf"     => "application/x-netcdf",
            "class"   => "application/octet-stream",
            "cpio"    => "application/x-cpio",
            "cpt"     => "application/mac-compactpro",
            "csh"     => "application/x-csh",
            "css"     => "text/css",
            "dcr"     => "application/x-director",
            "dir"     => "application/x-director",
            "djv"     => "image/vnd.djvu",
            "djvu"    => "image/vnd.djvu",
            "dll"     => "application/octet-stream",
            "dms"     => "application/octet-stream",
            "doc"     => "application/msword",
            "dvi"     => "application/x-dvi",
            "dxr"     => "application/x-director",
            "eps"     => "application/postscript",
            "etx"     => "text/x-setext",
            "exe"     => "application/octet-stream",
            "ez"      => "application/andrew-inset",
            "gif"     => "image/gif",
            "gtar"    => "application/x-gtar",
            "hdf"     => "application/x-hdf",
            "hqx"     => "application/mac-binhex40",
            "htm"     => "text/html",
            "html"    => "text/html",
            "ice"     => "x-conference-xcooltalk",
            "ief"     => "image/ief",
            "iges"    => "model/iges",
            "igs"     => "model/iges",
            "jpe"     => "image/jpeg",
            "jpeg"    => "image/jpeg",
            "jpg"     => "image/jpeg",
            "js"      => "application/x-javascript",
            "kar"     => "audio/midi",
            "latex"   => "application/x-latex",
            "lha"     => "application/octet-stream",
            "lzh"     => "application/octet-stream",
            "m3u"     => "audio/x-mpegurl",
            "man"     => "application/x-troff-man",
            "me"      => "application/x-troff-me",
            "mesh"    => "model/mesh",
            "mid"     => "audio/midi",
            "midi"    => "audio/midi",
            "mov"     => "video/quicktime",
            "movie"   => "video/x-sgi-movie",
            "mp2"     => "audio/mpeg",
            "mp3"     => "audio/mpeg",
            "mpe"     => "video/mpeg",
            "mpeg"    => "video/mpeg",
            "mpg"     => "video/mpeg",
            "mpga"    => "audio/mpeg",
            "ms"      => "application/x-troff-ms",
            "msh"     => "model/mesh",
            "mxu"     => "video/vnd.mpegurl",
            "nc"      => "application/x-netcdf",
            "oda"     => "application/oda",
            "pbm"     => "image/x-portable-bitmap",
            "pdb"     => "chemical/x-pdb",
            "pdf"     => "application/pdf",
            "pgm"     => "image/x-portable-graymap",
            "pgn"     => "application/x-chess-pgn",
            "png"     => "image/png",
            "pnm"     => "image/x-portable-anymap",
            "ppm"     => "image/x-portable-pixmap",
            "ps"      => "application/postscript",
            "qt"      => "video/quicktime",
            "ra"      => "audio/x-realaudio",
            "ram"     => "audio/x-pn-realaudio",
            "ras"     => "image/x-cmu-raster",
            "rgb"     => "image/x-rgb",
            "rm"      => "audio/x-pn-realaudio",
            "roff"    => "application/x-troff",
            "rpm"     => "audio/x-pn-realaudio-plugin",
            "rtf"     => "text/rtf",
            "rtx"     => "text/richtext",
            "sgm"     => "text/sgml",
            "sgml"    => "text/sgml",
            "sh"      => "application/x-sh",
            "shar"    => "application/x-shar",
            "silo"    => "model/mesh",
            "sit"     => "application/x-stuffit",
            "skd"     => "application/x-koan",
            "skm"     => "application/x-koan",
            "skp"     => "application/x-koan",
            "skt"     => "application/x-koan",
            "smi"     => "application/smil",
            "smil"    => "application/smil",
            "snd"     => "audio/basic",
            "so"      => "application/octet-stream",
            "spl"     => "application/x-futuresplash",
            "src"     => "application/x-wais-source",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc"  => "application/x-sv4crc",
            "swf"     => "application/x-shockwave-flash",
            "t"       => "application/x-troff",
            "tar"     => "application/x-tar",
            "tcl"     => "application/x-tcl",
            "tex"     => "application/x-tex",
            "texi"    => "application/x-texinfo",
            "texinfo" => "application/x-texinfo",
            "tif"     => "image/tif",
            "tiff"    => "image/tiff",
            "tr"      => "application/x-troff",
            "tsv"     => "text/tab-seperated-values",
            "txt"     => "text/plain",
            "ustar"   => "application/x-ustar",
            "vcd"     => "application/x-cdlink",
            "vrml"    => "model/vrml",
            "wav"     => "audio/x-wav",
            "wbmp"    => "image/vnd.wap.wbmp",
            "wbxml"   => "application/vnd.wap.wbxml",
            "wml"     => "text/vnd.wap.wml",
            "wmlc"    => "application/vnd.wap.wmlc",
            "wmls"    => "text/vnd.wap.wmlscript",
            "wmlsc"   => "application/vnd.wap.wmlscriptc",
            "wrl"     => "model/vrml",
            "xbm"     => "image/x-xbitmap",
            "xht"     => "application/xhtml+xml",
            "xhtml"   => "application/xhtml+xml",
            "xml"     => "text/xml",
            "xpm"     => "image/x-xpixmap",
            "xsl"     => "text/xml",
            "xwd"     => "image/x-windowdump",
            "xyz"     => "chemical/x-xyz",
            "zip"     => "application/zip",
        ];
    }
}



if (!function_exists("get_user")) {
    function get_user()
    {
        global $_GLOBALS;

        return $_GLOBALS['staff']['user'];
    }
}

if (!function_exists("get_allowed_sections")) {
    /**
     * @param string $user
     *
     * @return string[]
     */
    function get_allowed_sections($user = null, $site_id = null)
    {
        global $_GLOBALS;
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `as`.`sect_key` FROM `highwaygroup`.`admin_users` AS `au` INNER JOIN `highwaygroup`.`admin_sections` AS `as` ON `au`.`section_id` = `as`.`section_id` WHERE `au`.`user` = %s AND `au`.`site_id` = %d",
            $DB->escape($user ?? $_GLOBALS['staff']['user']),
            $site_id ?? $_GLOBALS['site_settings']['site_id'] ?? 0
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['sect_key'];
            }
            $DB->free_result($query);
        }

        // @todo Remove this once permissions are all organized
        foreach ([
            'WORLD2'     => 'WORLD',
            'SUPPLIER'   => 'SUP',
            'STOCK'      => 'PO',
            'SOFTUP'     => 'SITEUP',
            'PURCHASE'   => 'PO',
            'PROBUILDER' => 'PRODUCT',
            'PRD_CFG'    => 'PCONFIG',
            'ORDERS'     => 'PO',
            'OPTIONS'    => 'SITES',
            'NEW_SUPP'   => 'SUP',
            'MEMBER'     => 'MEMBERS',
            'INQUIRY'    => 'INVOICES',
            'CUSTOMERS'  => 'MEMBERS',
            'COE_REPORT' => 'REPORT',
            'COE_REPORT' => 'REPORTADM',
            'BACKORDER'  => 'INVOICES',
            'METATAGS'   => 'PCONFIG',
            'metatags'   => 'PCONFIG',
            'EMAILS'     => 'NEWSLETTER',
            'CURRENCY'   => 'SITES',
            'BIGBROTHER' => 'ADMIN',
            'BANNERS'    => 'NEWS',
            'ALL_JOBS'   => 'RMA',
        ] as $k => $v) {
            if (in_array($v, $result)) {
                $result[] = $k;
            }
        }

        return $result;
    }
}

if (!function_exists("get_all_sections")) {
    function get_all_sections($site_id = null)
    {
        global $_GLOBALS;
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `sect_key` FROM `highwaygroup`.`admin_sections` WHERE FIND_IN_SET('%d', `sites`)",
            $site_id ?? $_GLOBALS['site_settings']['site_id']
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['sect_key'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists("has_permission")) {
    function has_permission($sections, $allowed)
    {
        //@todo Sort out the permissions and remove this line

        if (!is_array($allowed)) {
            return false;
        }

        if (is_array($sections)) {
            foreach ($sections as $section) {
                if (has_permission($section, $allowed)) {
                    return true;
                }
            }
            return false;
        }

        return in_array($sections, $allowed);
    }
}

if (!function_exists('get_links')) {
    function get_links($max, $url_link, $inc = 25, $max_page = 5, $var_at = 'at', $var_page = 'page')
    {
        global $$var_at, $$var_page;

        $start = $$var_at + 1;
        $end   = $$var_at + $inc;
        if ($end > $max) {
            $end = $max;
        }

        if ($max <= 0) {
            return;
        }

        if (strpos($url_link, "?") === false) {
            $url_link .= '?';
        }

        $contents['start_from'] = $start;
        $contents['ends_at']    = $end;
        $contents['max']        = $max;

        if (1 != $start) {
            $contents['first'] = "$url_link&$var_at=0&$var_page=0";
        }

        $very_last_at   = floor(($max - 1) / $inc) * $inc;
        $very_last_page = floor(($max - 1) / ($inc * $max_page));
        if ($start - 1 != $very_last_at) {
            $contents['last'] = "$url_link&$var_at=$very_last_at&$var_page=$very_last_page";
        }

        $next_page = intval($max_page * $$var_page * $inc);

        if ($next_page > 0) {
            $new_at                    = $next_page - $inc;
            $new_page                  = $$var_page - 1;
            $contents['url_prev_page'] = "$url_link&$var_at=$new_at&$var_page=$new_page";
        }

        for ($i = 0; $i < $max_page; $i++) {
            $count  = $i;
            $start  = $next_page + ($i * $inc) + 1;
            $end    = $next_page + ($i + 1) * $inc;
            $new_at = $next_page + $i * $inc;
            if ($end > $max || $new_at + $inc >= $max) {
                $end = $max;
                $i   = $max_page;
            }
            if ($max <= 0) {
                $start = 0;
            }

            $contents['links'][$i]['start'] = $start;
            $contents['links'][$i]['end']   = $end;
            if (!(($$var_at - $next_page) / $inc == $count)) {
                $contents['links'][$i]['url'] = "$url_link&$var_at=$new_at&$var_page=" . intval($$var_page);
            }

        }

        if ($end < $max) {
            $new_at                    = $new_at + $inc;
            $new_page                  = intval($$var_page + 1);
            $contents['url_next_page'] = "$url_link&at=$new_at&page=$new_page";
        }

        return $contents;
    }
}
