<!-- config_0.tpl -->

<?php
include __DIR__ . '/common_0.inc.php';
foreach ($sections as $k => $v) {
    if (!isset($item_settings[$k])) {
        $item_settings[$k] = $v['defaults'];
    }
}

?>

<table class="table_fields">
<?php
if (array_key_exists('as_member_comment', $item_settings) || array_key_exists('check_member', $item_settings)) {
?>
	<tr>
		<th>Advanced :</th>
		<td>
			<input type="hidden" name="<?=$section_name?>_settings[as_member_comment][value]" value="0" />
			<input id="as_member_comment" type="checkbox" name="<?=$section_name?>_settings[as_member_comment][value]"<?=($item_settings['as_member_comment']['value']) ? ' checked="checked"' : ''?> value="1" />
			<label for="as_member_comment">Copy of the email in the member's ID</label><br />
			<input type="hidden" name="<?=$section_name?>_settings[check_member][value]" value="0" />
			<input id="check_member" type="checkbox" name="<?=$section_name?>_settings[check_member][value]"<?=($item_settings['check_member']['value']) ? ' checked="checked"' : ''?> value="1" />
			<label for="check_member">Member Checked Ticked</label><br />
		</td>
	</tr>
<?php
}
?>

	<tr>
		<th>Comments / Description</th>
		<td><?php echo formatHTMLTextarea($section_name . '_settings[paragraph][value]', $item_settings['paragraph']['value'], true) ?></td>
	</tr>
</table>

<!-- /config_0.tpl -->
