<!-- _factory_meta_tags.tpl -->

<?php
/**
 * Factory META Data template
 *
 * Inputs:
 * - $item
 * - - factory_meta_at
 * - - factory_meta_by
 * - - factory_meta_description
 * - - factory_meta_keywords
 * - - factory_meta_title
 * - - factory_slug
 *
 * Overwrites:
 * - $temp
 */

if (!isset($item) || !is_array($item)) {
    return;
}

?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $(Product.selector("factory_meta_title")      ).change(function() { $(".factory_meta_title_length"      ).text($(this).val().length);            }).change();
    $(Product.selector("factory_meta_keywords")   ).change(function() { $(".factory_meta_keywords_count"    ).text($(this).val().split(',').length); }).change();
    $(Product.selector("factory_meta_description")).change(function() { $(".factory_meta_description_length").text($(this).val().length);            }).change();
    $(Product.selector("factory_slug")            ).change(function() { $(".factory_slug_length"            ).text($(this).val().length);            }).change();
    $(".factory_meta_tags_update").click(function() {
        var meta_description = Product.get("factory_part_name");

        Product.set("factory_meta_title", Product.get("factory_part_name"));
        Product.set("factory_meta_keywords", Product.get("factory_part_name").replace(/[^a-z0-9\-\"]+/gi, ', ').replace(/[,\s]+$/, ''));
        Product.set("factory_meta_description", meta_description);
        Product.set("factory_slug", Product.get("factory_part_name").replace(/[^a-z0-9]+/gi, '-'));
    }).fadeIn();
    $(".factory_meta_tags_clear").click(function() {
        Product.set("factory_meta_title", "");
        Product.set("factory_meta_keywords", "");
        Product.set("factory_meta_description", "");
        Product.set("factory_slug", "");
        return false;
    }).fadeIn();
});
//]]>
</script>

<div>
    <?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionFactoryMeta&quot;);return false">Factory META Tags</a>')?>
    <div id="sectionFactoryMeta">
        <table class="table_fields">
            <tr>
                <th><a href="#" onclick="<?php hecho('openWindow("help.php?action=title", 400, 400, true);return false')?>">Title :</a></th>
                <td><input class="wide" type="text" name="item[factory_meta_title]" value="<?php hecho($item['factory_meta_title'])?>" /></td>
                <td><span class="factory_meta_title_length"><?=strlen($item['factory_meta_title'])?></span></td>
            </tr>
            <tr>
                <th><a href="#" onclick="<?php hecho('openWindow("help.php?action=keywords", 400, 400, true);return false')?>">Keywords :</a></th>
                <td><?=formatHTMLTextarea('item[factory_meta_keywords]', $item['factory_meta_keywords'], true)?></td>
                <td><span class="factory_meta_keywords_count"><?=count(explode(',', $item['factory_meta_keywords']))?></span></td>
            </tr>
            <tr>
                <th><a href="#" onclick="<?php hecho('openWindow("help.php?action=desc", 400, 400, true);return false')?>">Description :</a></th>
                <td><?=formatHTMLTextarea('item[factory_meta_description]', $item['factory_meta_description'], true)?></td>
                <td><span class="factory_meta_description_length"><?=strlen($item['factory_meta_description'])?></span></td>
            </tr>
            <tr>
                <th><a href="#" onclick="<?php hecho('openWindow("help.php?action=url", 400, 400, true);return false')?>">URL Keywords :</a></th>
                <td><input class="wide" type="text" name="item[factory_slug]" value="<?php hecho($item['factory_slug'])?>" /></td>
                <td><span class="factory_slug_length"><?=strlen($item['factory_slug'])?></span></td>
            </tr>
<?php

$temp = productGetOptionList('factory_meta_by');
?>
            <tr>
                <th>Factory META Tags Last Changed :</th>
                <td><?php hecho(($item['factory_meta_by']) ? sprintf(
                    "%s, By : %s",
                    date(_('Y-m-d'), strtotime($item['factory_meta_at'])),
                    $temp[$item['factory_meta_by']]
                ) : 'Never')?></td>
                <td></td>
            </tr>
<?php
unset($temp);

?>
        </table>
        <br />
        <br />
        <div style="text-align:center;">
            <input class="factory_meta_tags_update" style="display:none;" type="button" value="Auto Update Tags" />
            <button class="factory_meta_tags_clear" style="display:none;"><i class="fas fa-trash-alt"></i></button>
        </div>
        <br />
        <br />
    </div>
    <div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionFactoryMeta_hideNote">
        Factory META Tags can be viewed by Clicking "Factory META Tags" above<br />
    </div>
</div>

<!-- /_factory_meta_tags.tpl -->
