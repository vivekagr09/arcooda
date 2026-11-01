<!-- description.tpl.php -->

<?php
?>

<script type="text/javascript">
//<![CDATA[

function addLink() {
	var linkinput = document.getElementById('link-input');
	var textarea = document.getElementsByTagName('textarea')[0];

	textarea.value = textarea.value + " <a href='" + linkinput.value + "' target='_blank'>" + linkinput.value + "</a>";

	linkinput.value="";
}

//]]>
</script>

<?= formatHTMLTitle('Description') ?>

<div id="sectionDescription">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Title</td>
			<td><input type="text" class="intext" name="weeklyspecial[title]" value="<?= htmlspecialchars($weeklyspecial['title']) ?>" /></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Description</td>
			<td>
				<br>
				Link: <input type="text" id="link-input" placeholder="https://www.example.com"> <button type="button" onclick="addLink();">Add to editor</button>
				<br><br>

				<?= formatHTMLTextarea('weeklyspecial[description]',$weeklyspecial['description'],true,80,10) ?>

			</td>
		</tr>
	</table>
	<br /><br />
</div>

<div id="sectionDescription_hideNote">
	<br /><br />
</div>

<!-- /description.tpl.php -->
