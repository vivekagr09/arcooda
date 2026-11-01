<?php
/**
 * @var int     $id
 * @var mixed[] $item
 */
?><?php

moduleLoad('product');
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->


<?= formatHTMLTitle('Product Details') ?>

<div>
	<table class="table_fields">
		<tr>
			<th><span style="color:red;"><small>*</small></span>Name :</th>
			<td><input class="wide" type="text" name="item[name]"  value="<?= $item['name']?>" style="width:98%;" /></td>
		</tr>
		
		<tr>
			<th>Comment :</th>
			<td><textarea name="item[comment]" onkeypress="textareaAutoSize(this, 3, 3, 20);" style="width:98%;" rows="5" value="<?= $item['comment']?>"><?= $item['comment']?></textarea></td>
		</tr>
		<tr>
			<th>Type :</th>
			<td><select class="wide" name="item[type]" id="carousel_type">
	                <?= formatHTMLOptions(['image'=>'Image','video'=>'Video'], $item['type']) ?>
	        </td>
		</tr>
		<tr>
            <th>Center Image :</th>
            <td>
                <div id="productPictureRemote">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input style="width:272px;" name="center_image" type="file" accept="image/*" />
                    <!-- <input type="button" value="Link Thumbnail" class="unfinished" onclick="window.open(&quot;.?action=image&amp;return_message=product_image_local&quot;);" /> -->
                </div>
                <div id="productPictureLocal" style="display:none;">
                    <input style="width:272px;" type="hidden" name="center_image" value="<?=('add' == $action && is_file($test = productGetOptionList('image_path') . '/' . $item['center_image'])) ? hecho($test, 1) : ''?>" />
                    <span id="product_image_note"></span>
                    <!-- <input type="button" value="Link Thumbnail" onclick="window.open(&quot;.?action=image&amp;return_message=product_image_local&quot;);" /> -->
                </div>
                <div id="sectionItemImage">
                    <a href="<?php hecho(array_key_exists('center_image', $item) ? $item['center_image'] : '#')?>" target="_blank" id="item_image_a"><img id="item_image_img" src="addImages.php?name=<?=$item['center_image'] ?>&action=view" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
<?php
if (array_key_exists('center_image', $item)) {
?>
                    <br />
                    <small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image, Center image will display actual size</i><br/>
                    <i>Recommended image size 650px wide x 250px height or less</i>
                    </small>
<?php
}
?>
                </div>
            </td>
        </tr>
	</table>
	<br />
	<br />
</div>
<script type="text/javascript">
	$("[name='center_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_image_img").attr("src", e.target.result);
            $("#item_image_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
