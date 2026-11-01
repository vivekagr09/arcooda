<!-- active_status.tpl -->

<?= formatHTMLTitle('<a href="#" onclick="return toggleSection(\'sectionStatus\')">Page Status</a>') ?>

<div id="sectionStatus"><table class="table_fields">
	<tr>
		<th>Active Online :</th>
		<td><select style="width:auto;" name="item[active]"><?= formatHTMLOptions(pageGetOptionList('active'), $item['active']) ?></select></td>
	</tr>
</table></div>

<br /><br />

<!-- /active_status.tpl -->
