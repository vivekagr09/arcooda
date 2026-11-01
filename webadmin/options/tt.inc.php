<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionTwitter\');">Twitter</a>') ?>

<div id="sectionTwitter">
	<table class="wide spacetable">
		<tr>
			<th><span style="font-size: 12px;">Recommended size: 1200x675</span></th>
			<th style="width: 75%;">Image</th>
		</tr>

		<tr>
			<td>
				<form action="/webadmin/options/social_media_sharing.php" method="post" id="tt-form" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="tt" onchange="this.style.display='none'; document.getElementById('tt-uploading').style.display='block'; document.getElementById('tt-form').submit();">
					<span id="tt-uploading" style="display: none;">Uploading...</span>
				</form>
			</td>
			<td>
<?php
					if (ttImgExists('default')) {
						echo "<img src='/img_tt/". ttImgGet('default') ."' style='width: 100%; max-width: 1200px;'>";
					}
?>
			</td>
		</tr>
	</table>
</div>