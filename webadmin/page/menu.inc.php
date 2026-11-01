<!-- menu.inc -->

<?php
$temp = sys_get_temp_dir() . "/page_menu.inc.php_" . md5($_GLOBALS["staff"]["id"]);
if (is_file($temp) && (filemtime($temp) + 3600) > time()) {
    readfile($temp);
    echo "\n<!-- #menu.inc -->\n";
    return;
}
ob_start();

if (moduleLoad("page")) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Static Pages</b></td></tr>
	<tr><td><ul class="blocklist">
        <li><a href="/webadmin/page/?action=add">Add New Page</a></li>
        <li><a href="/webadmin/page/">List All Pages (<?=count(pageSearch())?>)</a></li>
    </ul></td></tr>
</table>
<?php
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->