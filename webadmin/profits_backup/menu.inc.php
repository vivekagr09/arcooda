<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (!moduleExists("profit")) {
    return;
}

$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));

$temp = $_SERVER["DOCUMENT_ROOT"] . "/protected/cache" . $stub . "_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 3600) > time()) {
    readfile($temp);
    echo "<!-- #menu.inc -->\n";
    return;
}

ob_start();
?>
<div class="accordion-item">
	<div class="accordion-header">
		<button type="button" data-bs-toggle="collapse" data-bs-target="#webadmin-menu-profit">
			<?=htmlspecialchars(_("Profits"))?>
		</button>
	</div>
	<div id="webadmin-menu-profit" class="accordion-collapse collapse show">

<ul>
	<li><a href="<?=htmlspecialchars($stub)?>/?action=add">
		<?=htmlspecialchars(_("Add New [Module]"))?>
	</a></li>
	<li><a href="<?=htmlspecialchars($stub)?>/?action=list">
		<?=htmlspecialchars(_("List All Profits"))?>
	</a></li>
</ul>

	</div>
</div>
<?php
file_put_contents($temp, ob_get_flush());
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
