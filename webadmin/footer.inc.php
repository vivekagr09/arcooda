<!-- footer.inc -->

<?php

global $_GLOBALS;

if (!($_GLOBALS['popup_window'] ?? false)) {
?>
        </td>
	</tr>
</table>
<?php
}
?>

<iframe name="gToday:normal:agenda.min.js" id="gToday:normal:agenda.min.js" src="/webadmin/calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible;z-index:999;position:absolute;top:-500px;left:-500px;"></iframe>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js" integrity="sha512-DAVSi/Ovew9ZRpBgHs6hJ+EMdj1fVKE+csL7mdf9v7tMbzM1i4c/jAvHE8AhcKYazlFl7M8guWuO3lDNzIA48A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	</body>
</html>
<?php
global $DB;
if (isset($DB)) {
    $DB->close();
}

if (array_key_exists('REQUEST_MICROTIME', $_SERVER) && is_object($DB)) {
    $temp = explode(' ', microtime());
    printf(
        '<!-- Loaded %d queries in %s seconds -->',
        count($DB->all_queries),
        bcsub(bcadd($temp[0], $temp[1], 8), $_SERVER["REQUEST_MICROTIME"], 8)
    );
}
