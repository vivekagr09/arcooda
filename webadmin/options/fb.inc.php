<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionFacebook\');">Facebook</a>') ?>

<div id="sectionFacebook">
	<table class="wide spacetable">
		<tr>
			<th><span style="font-size: 12px;">Recommended size: 1200x630</span></th>
			<th style="width: 75%;">Image</th>
		</tr>

		<tr>
			<td>
				<form action="/webadmin/options/social_media_sharing.php" method="post" id="fb-form" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="fb" onchange="this.style.display='none'; document.getElementById('fb-uploading').style.display='block'; document.getElementById('fb-form').submit();">
					<span id="fb-uploading" style="display: none;">Uploading...</span>
				</form>
			</td>
			<td>
<?php
					if (fbImgExists('default')) {
						echo "<img src='/img_fb/". fbImgGet('default') ."' style='width: 100%; max-width: 1200px;'>";
					}
?>
			</td>
		</tr>
	</table>
</div>