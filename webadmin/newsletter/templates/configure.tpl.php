<!-- configure.tpl -->

<?php
$config_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/newsletter/newsletters/config_' . $item['style_id'] . '.tpl.php';
?>
<div class="checkTemplate" style="display:block;">
<?php echo formatHTMLTitle('Configure Template') ?>
</div>
<div id="sectionConfigure" class="checkTemplate" style="display:block;">
<?php
if (is_file($config_file)) include $config_file;
else
{
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
