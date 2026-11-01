<?php
/**
 * @var mixed[] $item
 * @var bool $add
 * @var bool $edit
 */
?>
<!-- addedit.tpl -->

<?php
$banner_type_list = bannerGetOptionList('banner_type');
?>

<?= formatHTMLTitle('Banner Details') ?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='image_file']").change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('img.image_url').attr('src', e.target.result);
                $('span.image_url').css('background-image', 'url(' + e.target.result + ')');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("[name='mobile_file']").change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('img.mobile_url').attr('src', e.target.result);
                $('span.mobile_url').css('background-image', 'url(' + e.target.result + ')');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("[name='hover_file']").change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('img.hover_url').attr('src', e.target.result);
                $('span.hover_url').css('background-image', 'url(' + e.target.result + ')');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("[name='item[offer]']").change(function() {
        $('span.offer').html($(this).html().replace(/\n/, "<br />\n")).toggle($(this).val() != '');
    }).change();
    $("[name='item[title]']").change(function() {
        $('span.title').html($(this).html().replace(/\n/, "<br />\n")).toggle($(this).val() != '');
    }).change();
    $("[name='item[details]']").change(function() {
        $('span.details').html($(this).html().replace(/\n/, "<br />\n") + "<br /><br />").toggle($(this).val() != '');
    }).change();
    $("[name='item[button]']").change(function() {
        $('span.button').text($(this).val()).toggle($(this).val() != '');
    }).change();
    $("[name='item[banner_type]']").change(function() {
        var $all = $('.banner_type');
        $all.filter('.banner_type_' + $(this).val()).fadeIn();
        $all.not('.banner_type_' + $(this).val()).toggle(false);

        var m = /\(\s*([0-9]+)\s*x\s*([0-9]+)\s*\)/.exec($(":selected", this).text());
        if (m) {
            $("#example_img").css({"max-width" : m[1] + "px", "max-height" : m[2] + "px"});
            $("#canvas").css({"width" : m[1] + "px", "height" : m[2] + "px"});
        } else {
            $("#example_img").css({"max-width" : null, "max-height" : null});
            $("#canvas").css({"width" : null, "height" : null});
        }
    }).change();
})
//]]>
</script>

<table class="table_fields">
  <tr>
    <th>Banner Type :</th>
    <td><select name="item[banner_type]" style="width:100%;">
      <option value=""></option>
<?php
foreach ($banner_type_list as $k => $v) {
?>
      <option style="font-family:courier;" value="<?= htmlspecialchars($k) ?>"<?= ($item['banner_type'] == $k) ? ' selected="selected"' : '' ?>><?= str_replace(' ', '&nbsp;', htmlspecialchars($v)) ?></option>
<?php
}
?>
    </select></td>
  </tr>
  <tr>
    <th>Image :</th>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="image_file" accept="image/*" />
<?php
if ($item['image']) {
    $image_url = substr(bannerGetOptionList('image_path'), strlen($_SERVER['DOCUMENT_ROOT'])) . '/' . $item['image'];
?>
      <a href="<?=htmlspecialchars($image_url)?>" target="_blank"><img class="image_url" src="<?=htmlspecialchars($item['image_url'])?>" style="vertical-align:middle;max-width:468px;max-height:96px" /></a>
<?php
}
?>
    </td>
  </tr>
  <tr class="banner_type banner_type_24 banner_type_25 banner_type_26">
    <th>Hover Image :</th>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="hover_file" accept="image/*" />
<?php
if ($item['hover_image']) {
    $image_url = substr(bannerGetOptionList('hover_path'), strlen($_SERVER['DOCUMENT_ROOT'])) . '/' . $item['hover_image'];
?>
      <a href="<?=htmlspecialchars($image_url)?>" target="_blank"><img class="hover_url" src="<?=htmlspecialchars($item['hover_url'])?>" style="vertical-align:middle;max-width:468px;max-height:96px" /></a>
<?php
}
?>
    </td>
  </tr>
  <tr class="banner_type banner_type_23">
    <th>Mobile Image :</th>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="mobile_file" accept="image/*" />
<?php
if ($item['mobile_image']) {
    $image_url = substr(bannerGetOptionList('mobile_path'), strlen($_SERVER['DOCUMENT_ROOT'])) . '/' . $item['mobile_image'];
?>
      <a href="<?=htmlspecialchars($image_url)?>" target="_blank"><img class="mobile_url" src="<?=htmlspecialchars($item['mobile_url'])?>" style="vertical-align:middle;max-width:468px;max-height:96px" /></a>
<?php
}
?>
    </td>
  </tr>
  <tr>
    <th>Banner Name :</th>
    <td><input type="text" style="width:100%" name="item[text]" value="<?= htmlspecialchars($item['text']) ?>" /></td>
  </tr>
  <tr class="banner_type banner_type_23">
    <th>Banner Offer :<br /><span style="color:#f00;">(yellow highlight)</span></th>
    <td><textarea style="width:100%" name="item[offer]"><?= htmlspecialchars($item['offer']) ?></textarea></td>
  </tr>
  <tr class="banner_type banner_type_23 banner_type_24 banner_type_25 banner_type_26">
    <th>Banner Title :<br /><span style="color:#f00;">(big text)</span></th>
    <td><textarea style="width:100%" name="item[title]"><?= htmlspecialchars($item['title']) ?></textarea></td>
  </tr>
  <tr class="banner_type banner_type_23 banner_type_24 banner_type_25 banner_type_26">
    <th>Banner Details :<br /><span style="color:#f00;">(small text)</span></th>
    <td><textarea style="width:100%" name="item[details]"><?= htmlspecialchars($item['details']) ?></textarea></td>
  </tr>
  <tr class="banner_type banner_type_23 banner_type_24 banner_type_25 banner_type_26">
    <th>Banner Button :</th>
    <td><input type="text" style="width:100%" name="item[button]" value="<?= htmlspecialchars($item['button']) ?>" /></td>
  </tr>
  <tr>
    <th>Link To :</th>
    <td><input type="text" style="width:100%" name="item[url]" value="<?= htmlspecialchars($item['url']) ?>" /></td>
  </tr>
  <tr>
    <th>Mouse Over Text :</th>
    <td><input type="text" style="width:100%" name="item[alt]" value="<?= htmlspecialchars($item['alt']) ?>" /></td>
  </tr>
    <tr class="banner_type banner_type_26 banner_type_24 banner_type_25">
      <th>Turn Off Black Shading :</th>
      <td><input type="checkbox" name="item[black_shading]" value="n" <?= $item['black_shading'] == 'n'?'checked':'' ?>></td>
    </tr>
    <tr class="banner_type banner_type_26 banner_type_24 banner_type_25">
      <th>Turn Off Image Movement :</th>
      <td><input type="checkbox" name="item[image_movement]" value="n" <?= $item['image_movement'] == 'n'?'checked':'' ?>></td>
    </tr>

  <tr>
    <th>Expiry Date :</th>
    <td>
      <input type="text" name="item[expiry]" value="<?= $item['expiry'] ?>" style="width:30%;" />
      <input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('item[expiry]'))" />
      <input type="button" value="Clear" onclick="setValue('item[expiry]','')" />
    </td>
  </tr>
<?php
if (!$add && 0 != $item['date_added_unix']) {
?>
  <tr>
    <th>Entered By :</th>
    <td><?= getStaffName($item['staff']) ?> on <?= date('d/m/y', $item['date_added_unix']) ?></td>
  </tr>
<?php
}
?>

  <tr style="line-height: 50px;">
    <th>Active :</th>
    <td>
      <select name="item[active]" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" style="width: 100%;<?= $item['active'] == 'y' ?'background:rgb(0, 255, 0);':'background:rgb(255, 0, 0);' ?>">
            <option value="y" <?= $item['active'] == 'y' ? 'selected="selected"':'' ?>>Yes</option>
            <option value="n" <?= $item['active'] == 'n' ? 'selected="selected"':'' ?>>No</option>       
        </select>
  </tr>
</table>
<br />

<?php
include __DIR__ . '/prevent_banners.tpl.php';
?>

<div style="text-align:center;">
	<input type="hidden" name="id" value="<?= $item['id'] ?>">
	<input type="button" value="<?= ($add) ? 'Add' : 'Update' ?>" onclick="this.form.submit()" />
</div>

<div class="banner_type banner_type_5 banner_type_6 banner_type_7 banner_type_8 banner_type_9 banner_type_10 banner_type_11 banner_type_12 banner_type_13 banner_type_14 banner_type_15 banner_type_16 banner_type_17 banner_type_18 banner_type_19 banner_type_20 banner_type_21 banner_type_22" style="display:none;">
  <?= formatHTMLTitle('Example') ?>
  <div style="width:95%;margin:auto;">
    <div id="canvas" style="position:absolute;border:1px solid #f00;"></div>
    <img id="example_img" class="image_url" src="<?=htmlspecialchars($item['image_url'])?>" alt="<?=htmlspecialchars($item['alt'])?>" style="border:1px solid #007" />
  </div>
</div>

<div class="banner_type banner_type_25 banner_type_26" style="display:none;">
  <?= formatHTMLTitle('Example (RedParts Banner)') ?>
  <div style="width:95%;margin:auto;">
    <div class="block-banners__list">
      <a href="#" class="block-banners__item block-banners__item--style--two" style="border:1px solid #007;">
        <span class="block-banners__item-image"><img class="image_url" src="<?=htmlspecialchars($item['image_url'])?>" alt="<?=htmlspecialchars($item['alt'])?>" /></span>
        <span class="block-banners__item-image block-banners__item-image--blur"><img class="hover_url" src="<?= htmlspecialchars($item['hover_url']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" /></span>
        <span class="block-banners__item-title title"><?= nl2br(htmlspecialchars($item['title'])) ?></span>
        <span class="block-banners__item-details details"><?= nl2br(htmlspecialchars($item['details'])) ?></span>
        <span class="block-banners__item-button btn btn-primary btn-sm button"><?= nl2br(htmlspecialchars($item['button'])) ?></span>
      </a>
    </div>
  </div>
</div>

<div class="banner_type banner_type_24" style="display:none;">
  <?= formatHTMLTitle('Example (RedParts Banner)') ?>
  <div style="width:95%;margin:auto;">
    <div class="block-banners__list">
      <a href="#" class="block-banners__item block-banners__item--style--one" style="border:1px solid #007;">
        <span class="block-banners__item-image"><img class="image_url" src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" /></span>
        <span class="block-banners__item-image block-banners__item-image--blur"><img class="hover_url" src="<?= htmlspecialchars($item['hover_url']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" /></span>
        <span class="block-banners__item-title title"><?= nl2br(htmlspecialchars($item['title'])) ?></span>
        <span class="block-banners__item-details details"><?= nl2br(htmlspecialchars($item['details'])) ?></span>
        <span class="block-banners__item-button btn btn-primary btn-sm button"><?= nl2br(htmlspecialchars($item['button'])) ?></span>
      </a>
    </div>
  </div>
</div>

<div class="banner_type banner_type_23" style="display:none;">
  <?= formatHTMLTitle('Example (RedParts Slide)') ?>
  <div style="width:95%;margin:auto;">
    <div class="block-slideshow__carousel" style="border:1px solid #007;">
      <div class="owl-carousel">
        <div class="owl-item active">
          <a class="block-slideshow__item" href="#">
            <span class="block-slideshow__item-image block-slideshow__item-image--desktop image_url" style="background-image: url('<?= htmlspecialchars($item['image_url']) ?>')"></span>
            <span class="block-slideshow__item-image block-slideshow__item-image--mobile mobile_url" style="background-image: url('<?= htmlspecialchars($item['mobile_url']) ?>')"></span>
            <span class="block-slideshow__item-offer offer"><?= nl2br(htmlspecialchars($item['offer'])) ?></span>
            <span class="block-slideshow__item-title title"><?= nl2br(htmlspecialchars($item['title'])) ?></span>
            <span class="block-slideshow__item-details details"><?= nl2br(htmlspecialchars($item['details'])) ?></span>
            <span class="block-slideshow__item-button button"><?= nl2br(htmlspecialchars($item['button'])) ?></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- /addedit.tpl -->
