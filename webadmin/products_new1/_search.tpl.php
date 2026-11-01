<!-- _search.tpl -->

<?php
/**
 * Product Search Template
 *
 * Inputs:
 * - $search_for
 */

if (!isset($search_for) || !is_string($search_for)) {
    return;
}

$search_url = $_GET;
$search_url['action'] = 'list';
unset($search_url['page_id']);
unset($search_url['search_for']);
$search_url = '.?' . http_build_query($search_url);
?>
<form action="<?php hecho($search_url)?>" method="get">
    <table class="table_search">
        <tr><th>Search Products</th></tr>
        <tr><td>Enter <b>Product ID</b> or <b>Product Name</b></td></tr>
        <tr><td>Search For: <input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for)?>" /></td></tr>
        <tr><td><input type="submit" value="Search" /></td></tr>
    </table>
</form>
<br /><br />

<!-- /_search.tpl -->
