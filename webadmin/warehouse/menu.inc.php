<!-- menu.inc -->

<?php
global $_GLOBALS;
global $DB;

/*$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/socialmedia_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 300) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}*/
ob_start();
    $partner_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `social_media_partner`"
    ), MYSQLI_ASSOC);
?>
<table cellspacing="0" cellpadding="4" width="100%">
    <tr class="scellheader"><td><b><?=htmlspecialchars(_('Media Partners'))?></b></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/socialmedia/?action=add"><?=htmlspecialchars(_('Add New Media Partner'))?></a></li>
        <li><a href="/webadmin/socialmedia/?action=list"><?=htmlspecialchars(sprintf(_('List All Media Partners (%d)'), $partner_counts['total']))?></a></li>
        <br>
        <li><a href="/webadmin/socialmedia/build.php"><?=htmlspecialchars(_('Send Media Update '))?></a></li>
         <li><a href="/webadmin/socialmedia/updatelist.php"><?=htmlspecialchars(_('View All Media Updates'))?></a></li>
    </ul></td></tr>
</table>
<?php
//file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
