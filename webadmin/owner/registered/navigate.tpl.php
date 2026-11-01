<!-- navigate.tpl -->

<?php
moduleLoad('product');

    $chunked_list = $section_handler->groupCount($item_list_product, $list_by1);
    $keys1        = array_keys($chunked_list);
?>
<div class="wide">
<?php
    foreach ($keys1 as $key1) {
         $product   = productFromDatabase($key1);
        $total_url            = $get_vars;
        $total_url['action']  = 'list';
        $total_url[$list_by1] = $key1;
        $total_url            = '.?' . http_build_query($total_url);
?>
		<div style="width:256px;padding:4px;border:1px solid #aaa;background:#fff;display:inline-block;vertical-align:bottom;">
            <a href="<?php hecho($total_url)?>">
                <?= $product['product_name'] ?> (<?=$chunked_list[$key1]?>)
            </a>
        </div>
<?php
    }
?>
</div>
<?php


?>
<br />
<br />

<!-- /navigate.tpl -->
