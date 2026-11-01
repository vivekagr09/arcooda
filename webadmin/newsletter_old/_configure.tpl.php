<!-- configure.tpl -->

<?php
$config_file = __DIR__ . '/newsletters/config_' . $item['style_id'] . '.tpl.php';
?>

<?=formatHTMLTitle('Configure Template')?>

<div id="sectionConfigure">
<?php
if (is_file($config_file)) {
    include $config_file;
} else {
?>
	No configuration files currently available for this template
	<br /><br />
<?php
}
?>
</div>

<div id="sectionConfigure_hideNote" style="display:none;">
	<br /><br />
</div>

<!-- /configure.tpl -->
