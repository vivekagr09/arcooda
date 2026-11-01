<!-- _navigate.tpl -->

<?php
/**
 * Navigation Template
 *
 * Inputs:
 * - $list_by1
 * - $list_by2
 * - $item_query
 */

moduleLoad('product');

if ($list_by1 == '') {
    return;
}
 
    $chunked_list = wantedChunk($wanted_list,$list_by1);
    $key_list1    = productGetOptionList('category_section');
    $keys1        = array_keys($chunked_list);
    $keys1        = array();
    foreach ($key_list1 as $k => $v) {
        if (isset($chunked_list[$k])) {
            $keys1[$k] = $k;
        }
    }

    foreach ($chunked_list as $k => $v) {
        $keys1[$k] = $k;
    }

    $width = isset($list_columns) ? floor(100 / $list_columns) : 25;
?>
<table class="table_grid">
<?php
    foreach (array_chunk($keys1, isset($list_columns) ? $list_columns : 4) as $chunked_keys1) {
?>
    <tr>
<?php
        foreach ($chunked_keys1 as $key1) {
            $total_url            = $_GET;
            $total_url['action']  = 'list';
            $total_url[$list_by1] = $key1;
            $total_url            = '.?' . http_build_query($total_url);
?>
        <td style="width:<?=$width?>%;text-align:left;"><a href="<?=$total_url?>"><?=(array_key_exists($key1, $key_list1) && '' != $key_list1[$key1]) ? hecho($key_list1[$key1], 1) : '[Untitled]'?>(<?=count($chunked_list[$key1])?>)</a>
<?php
        }
?>
    </tr>
<?php
    }
?>
</table>
<br />
<br />

<!-- /_navigate.tpl -->
