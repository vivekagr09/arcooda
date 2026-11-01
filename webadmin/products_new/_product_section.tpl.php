<!-- _product_section.tpl -->

<?php
/**
 * Product Section Template
 *
 * Inputs:
 * - $item
 * - - reference_id
 * - - section
 */

if (!isset($item) || !is_array($item)) {
    return;
}
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $(Product.selector(["section"])).change(function() {
        var prev = $(this).data("prev");
        var next = $(this).val();
        $("[class^='section-'],[class*=' section-']").stop().fadeOut();
        $("[class^='optgrp_section-'],[class*=' optgrp_section-']").attr("disabled", "disabled").stop().toggle(false);
        $(".section-" + next).stop().fadeIn();
        $(".optgrp_section-" + next).removeAttr("disabled").stop().fadeIn();

        if (typeof ProductCategory != "undefined") {
            $(ProductCategory.selector("category_id", $(this).data("prev"))).removeAttr("checked").change();
            $(ProductCategory.selector("category_id", $(this).val())).attr("checked", "checked").change();
        }

        $(this).data("prev", next);
    }).change();
    $(".product_section_copy_button").click(function() {
        var http_vars = <?=jecho($_GET)?>;
        http_vars.action  = "add";
        http_vars.section = Product.get("section");
        http_vars.id      = Product.get("reference_id");
        window.location = '.?' + $.param(http_vars);
        return false;
    }).toggle(true);
    $(".product_section_search_button").click(function() {
        var http_vars = {};
        http_vars.action         = "list";
        http_vars.return_message = "reference_id";
        http_vars.section        = Product.get("section");
        http_vars.search_for     = Product.get("reference_id");
        window.location = ".?" + $.param(http_vars);
        return false;
    }).toggle(true);
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionSection&quot;);return false">Product Section</a>')?>

<div id="sectionSection">
    <table class="table_fields">
        <tr>
            <th>Product Section :</th>
            <td><select name="item[section]">
                <option value="0"></option>
                <?=formatHTMLOptions(productGetOptionList('section'), $item['section'])?>
            </select></td>
        </tr>
        <tr>
            <th>Copy From Product :</th>
            <td>
                <input name="item[reference_id]" type="text" value="<?php hecho($item['reference_id'])?>" />
                <button class="product_section_search_button unfinished" style="display:none;">
                    <i class="fas fa-search"></i>
                </button>
                <button class="product_section_copy_button unfinished" style="display:none;">
                    <i class="fas fa-clone"></i>
                </button>
            </td>
        </tr>
    </table>
    <br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionSection_hideNote">
    Product section can be viewed by Clicking "Product Section" above<br />
</div>

<!-- /_product_section.tpl -->
