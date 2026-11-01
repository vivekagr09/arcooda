<!-- menu.inc -->

<?php
global $_GLOBALS;
global $DB;

$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/member_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 300) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $member_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`new_members`"
    ), MYSQLI_ASSOC);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr bgcolor="#00CCFF"><td nowrap="nowrap" align="center"><b><?=htmlspecialchars(_('Members / Customers'))?></b></td></tr>
    <tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
        <li><a class="td_options" href="/webadmin/member/?action=add"><?=htmlspecialchars(_('Add New Members'))?></a></li>
        <li><a class="td_options" href="/webadmin/member/?action=list"><?=htmlspecialchars(sprintf(_('List Members (%d)'), $member_counts['total']))?></a></li>
        <li><a class="td_options" href="/webadmin/member/?action=search"><?=htmlspecialchars(_('Search Members'))?></a></li>
        <li><a class="td_options" href="/webadmin/member/?action=sales_search"><?=htmlspecialchars(_('Sales Search'))?></a></li>
        <li><a class="td_options" href="/webadmin/mailinglist_admin.php"><?=htmlspecialchars(_('Mailing List Members'))?></a></li>
    </ul></td></tr>
</table>
<br />
<?php
} elseif (39 == $_GLOBALS['site_settings']['site_id']) {
    $member_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`new_members`"
    ), MYSQLI_ASSOC);
?>
<table cellspacing="0" cellpadding="4" width="100%">
    <tr class="scellheader"><td><b><?=htmlspecialchars(_('Members / Customers'))?></b></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/member/?action=add"><?=htmlspecialchars(_('Add New Members'))?></a></li>
        <li><a href="/webadmin/member/?action=list"><?=htmlspecialchars(sprintf(_('List Members (%d)'), $member_counts['total']))?></a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/member/?action=search"><?=htmlspecialchars(_('Search Members'))?></a></li>
        <li><a href="/webadmin/member/?action=sales_search"><?=htmlspecialchars(_('Sales Search'))?></a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/mailinglist_admin.php"><?=htmlspecialchars(_('Mailing List Members'))?></a></li>
    </ul></td></tr>
</table>
<?php
} else {
    $member_counts = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`new_members`"
    ), MYSQLI_ASSOC);
?>
<h3><?=htmlspecialchars(_('Members / Customers'))?></h3>
<ul>
    <li><a href="/webadmin/member/?action=add"><?=htmlspecialchars(_('Add New Members'))?></a></li>
    <li><a href="/webadmin/member/?action=list"><?=htmlspecialchars(sprintf(_('List Members (%d)'), $member_counts['total']))?></a></li>
    <li><a href="/webadmin/member/?action=search"><?=htmlspecialchars(_('Search Members'))?></a></li>
    <li><a href="/webadmin/member/?action=sales_search"><?=htmlspecialchars(_('Sales Search'))?></a></li>
    <li><a href="/webadmin/mailinglist_admin.php"><?=htmlspecialchars(_('Mailing List Members'))?></a></li>
</ul>
<?php
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->
