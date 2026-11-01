<!-- menu.inc -->

<?php
global $_GLOBALS;
global $DB;

$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/distributor_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 300) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $distributor_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `distributor`"
    ), MYSQLI_ASSOC);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr bgcolor="#00CCFF"><td nowrap="nowrap" align="center"><b><?=htmlspecialchars(_('Distributors'))?></b></td></tr>
    <tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
        <li><a class="td_options" href="/webadmin/distributor/?action=add"><?=htmlspecialchars(_('Add New Distributor'))?></a></li>
        <li><a class="td_options" href="/webadmin/distributor/?action=list"><?=htmlspecialchars(sprintf(_('List Distributors (%d)'), $distributor_counts['total']))?></a></li>
    </ul></td></tr>
</table>
<br />
<?php
} elseif (39 == $_GLOBALS['site_settings']['site_id']) {
    $distributor_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `distributor`"
    ), MYSQLI_ASSOC);
?>
<table cellspacing="0" cellpadding="4" width="100%">
    <tr class="scellheader"><td><b><?=htmlspecialchars(_('Distributors'))?></b></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/distributor/?action=add"><?=htmlspecialchars(_('Add New Distributors'))?></a></li>
        <li><a href="/webadmin/distributor/?action=list"><?=htmlspecialchars(sprintf(_('List Distributors (%d)'), $distributor_counts['total']))?></a></li>
        <br>
        <li><a href="/webadmin/maplocate/backend/?p=maps" target="_blank" > <?=htmlspecialchars(_('Map Setup '))?></a></li>
        <li><a href="/webadmin/maplocate/viewer/index.php?code=65ea1c4f1750c" target="_blank" ><?=htmlspecialchars(_('Live Map '))?></a></li>
    </ul></td></tr>
</table>
<?php
} else {
    $distributor_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `distributor`"
    ), MYSQLI_ASSOC);
?>
<h3><?=htmlspecialchars(_('Distributors'))?></h3>
<ul>
    <li><a href="/webadmin/distributor/?action=add"><?=htmlspecialchars(_('Add New Distributors'))?></a></li>
    <li><a href="/webadmin/distributor/?action=list"><?=htmlspecialchars(sprintf(_('List Distributors (%d)'), $distributor_counts['total']))?></a></li>
     <li><a href="/webadmin/maplocate/backend/?p=maps" target="_blank" ><?=htmlspecialchars(_('Map Setup '))?></a></li>
        <li><a href="/webadmin/maplocate/viewer/index.php?code=65ea1c4f1750c" target="_blank" ><?=htmlspecialchars(_('Live Map '))?></a></li>
</ul>
<?php
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
