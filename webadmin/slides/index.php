<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>

<div id="optcontent">
	<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSlides\');">Slides</a>')?>

	<div id="sectionSlides">

		<div style="width: 90%; margin: auto; text-align: left;">
			<form action="/webadmin/slides/addSlide.php" method="post" enctype="multipart/form-data" onsubmit="document.getElementById('add-btn').setAttribute('disabled', 'disabled');">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				<input type="file" name="slide" accept="image/*" required="required" />
				<input type="submit" value="Upload New Slide" id="add-btn">
			</form>
		</div>

		<table class="wide spacetable">
			<tr>
				<th>Slides</th>
				<th>Action</th>
			</tr>

<?php
foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/images/slides/*.*') as $i => $slide) {
    $chunk      = explode('/', $slide);
    $slideImage = end($chunk);
?>

					<tr>
						<td><img src="/images/slides/<?=$slideImage?>" style="width: 100%;" /></td>
						<td>
							<form action="/webadmin/slides/deleteSlide.php" method="post" onsubmit="document.getElementById('delete-btn-<?=$i?>').setAttribute('disabled', 'disabled'); document.getElementById('delete-btn-<?=$i?>').value='Deleting...';">
								<input type="hidden" name="slide" value="<?=$slide?>">
								<input type="submit" value="Delete" id="delete-btn-<?=$i?>">
							</form>
						</td>
					</tr>

<?php
}
?>

		</table>

	</div>
</div>