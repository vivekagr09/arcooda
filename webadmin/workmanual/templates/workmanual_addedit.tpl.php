<input type="hidden" name="manual[manual_id]" value="<?= $manual_id ?>" />

<table>
	<tr>
		<td class="left_title" style="width:25%;">
			Manual Title
		</td>
		<td>
			<input type="text" class="intext" name="manual[manual_title]" value="<?= htmlspecialchars($manual['manual_title']) ?>" />
		</td>
	</tr>
</table>

<br />
<br />

<input type="submit" value="Update Changes" />
