<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Monitor Help Text</a>', false)?>


<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:20%">Name</th>
			<th style="width:80%">Help Text</th>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$count = 0;
foreach ($help_text_list as $shot) {
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="freight_tracking_rowa[<?=$count ?>]" ssid="<?=$count ?>">
	<td style="text-align:left">
		<input type="hidden" name="monitor_help_text[<?= $count ?>][id]" value="<?=$shot['id'] ?>" style="width:90%">
		<input type="text" name="monitor_help_text[<?= $count ?>][name]" value="<?=$shot['name'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<textarea  name="monitor_help_text[<?= $count ?>][support_text]" value="<?=$shot['support_text'] ?>" style="width:90%" row="5" oninput="adjustTextareaHeight(this);"><?=$shot['support_text'] ?></textarea>
		
	</td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>
</table>
</div>

<br />
<script type="text/javascript">
    $(function () {
        $('textarea').each(function () {
            adjustTextareaHeight(this);
        });
    });
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
