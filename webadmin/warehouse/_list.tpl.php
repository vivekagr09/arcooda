<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$alt = false;

$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';

$sort_url           = $_GET;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $_GET;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = '.?' . http_build_query($edit_url) . '&id=';

$select_url           = $_GET;
$select_url['action'] = 'select';
unset($select_url['id']);
$select_url = '.?' . http_build_query($select_url) . '&id=';

$toggle_url           = $_GET;
$toggle_url['action'] = 'do_toggle';
unset($toggle_url['id']);
$toggle_url = '.?' . http_build_query($toggle_url) . '&id=';

$delete_url           = $_GET;
$delete_url['action'] = 'delete';
unset($delete_url['id']);
$delete_url = '.?' . http_build_query($delete_url) . '&id=';

$category_list         = wareHouseGetOptionList('category');
$country_location_list = wareHouseGetOptionList('country_location');

$alt = false;
if($_GET['action'] == 'delete' && !empty($_GET['id'])) {
?>
<div id="warning" style="text-align: center;color: black;margin: 0px 32px;margin-bottom: 5px;background: red;font-weight: 600;">Distributor has been deleted.</div>
<?php
}
?>
<table class="table_grid" style="width:95%;margin:auto">
    <tr>
        <th><a href="<?=htmlspecialchars($sort_url . 'country')?>">Country<?=('country' == $order_by) ? $ord_img : ''?></th>
		<th>City</th>
		<th>State</th>
        <th><a href="<?=htmlspecialchars($sort_url . 'company')?>">Name Of Company<?=('company' == $order_by) ? $ord_img : ''?></th>
		<th>Stock</th>
		<th>Value USD</th>
        <th>Last Stock Received</th>
        <th>Last Stock Shipped</th>
        <th>Active</th>
		<th></th>
	</tr>
<?php
foreach ($item_list as $id) {
    $item = wareHouseFromDatabase($id);
?>
    <tr<?= ($alt = !$alt) ? ' class="alt"' : ''?>>
        <td><?=htmlspecialchars($country_location_list[$item['country']] ?? $item['country'])?></td>
        
        <td><?=htmlspecialchars(ucfirst($item['city']))?></td>
        <td><?=htmlspecialchars(ucfirst($item['state']))?></td>
        <td><a href="<?=$item['web_page_url']?>" target="_blank"><?=htmlspecialchars($item['company'])?></a></td>
        <td>Na</td>
        <td>Na</td>
        <td>Na</td>
        <td>Na</td>
        <td style="<?= $item['active'] == 'y' ? 'background-color:#92d892;' : 'background-color:pink;' ?>"><?= $item['active'] == 'y' ? 'yes' : 'No' ?></td>
        <td style="white-space:nowrap">
            <a href="<?=htmlspecialchars($edit_url . $id)?>">Edit</a>
            <a href="<?=htmlspecialchars($delete_url . $id)?>" onclick="javascript:return confirm('You are deleting <?=htmlspecialchars($item['company'])?> – are you sure you want to go ahead?');">Delete</a>
        </td>
    </tr>
<?php
}
?>
</table>
<script>
    // Get a reference to the warning div and close button
    var warningDiv = document.getElementById("warning");
    var closeButton = document.getElementById("closeButton");

    // Function to hide the warning div after 10 seconds
    function hideWarning() {
        warningDiv.style.display = "none";
    }

    // Set a timeout to hide the warning div after 10 seconds
    var timeoutID = setTimeout(hideWarning, 10000);
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
