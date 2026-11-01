<!-- menu.inc -->

<?php
global $_GLOBALS;
global $DB;

$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/news_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 300) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr bgcolor="#00CCFF"><td nowrap="nowrap" align="center"><b><?=htmlspecialchars(_('News'))?></b></td></tr>
    <tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
        <li><a class="td_options" href="/webadmin/news/?action=list"><?=htmlspecialchars(_('Add/Edit News'))?></a></li>
    </ul></td></tr>
</table>
<br />
<?php
} elseif (39 == $_GLOBALS['site_settings']['site_id']) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
    <tr class="scellheader"><td><b><?=htmlspecialchars(_('News'))?></b></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/news/?action=list"><?=htmlspecialchars(_('Add/Edit News'))?></a></li>
    </ul></td></tr>
</table>
<?php
} else {
?>
<h3><?=htmlspecialchars(_('News'))?></h3>
<ul>
    <li><a href="/webadmin/news/?action=list"><?=htmlspecialchars(_('Add/Edit News'))?></a></li>
</ul>
<?php
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
