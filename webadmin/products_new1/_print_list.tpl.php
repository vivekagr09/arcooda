<!-- _print_list.tpl -->

<?php
$products_by_type = productCount(productQuery(), 'type');
$temp             = array();
foreach (productGetOptionList('type') as $k => $v) {
    if (array_key_exists($k, $products_by_type)) {
        $temp[] = sprintf(
            '<a href="print.php?type=%d" target="_blank">%s (%d)</a>',
            $k,
            hecho($v, 1),
            (array_key_exists($k, $products_by_type)) ? $products_by_type[$k] : 0
        );
    }
}
?>
<div style="text-align:center;">
    <a href=".?action=print"><b>Print All</b></a>
    <a href=".?action=export"><b>Export All To Excel</b></a>
    <a href=".?action=export&export=stocklist"><b>Export Stocklist</b></a>
</div>

<table class="table_fields">
<?php
foreach (array_chunk($temp, 3) as $row) {
    echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\n";
}
?>
</table>
<br /><br />

<!-- /_print_list.tpl -->
