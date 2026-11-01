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

$category_list         = socialMediaPartnerGetOptionList('category');
$country_location_list = socialMediaPartnerGetOptionList('country_location');

$alt = false;
if($_GET['action'] == 'delete' && !empty($_GET['id'])) {
?>
<div id="warning" style="text-align: center;color: black;margin: 0px 32px;margin-bottom: 5px;background: red;font-weight: 600;">Distributor has been deleted.</div>
<?php
}
?>
<table class="table_grid" style="width:95%;margin:auto">
    <tr>
		<th><a href="<?=htmlspecialchars($sort_url . 'company')?>">Name Of Company<?=('company' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=htmlspecialchars($sort_url . 'country_location')?>">Country<?=('country_location' == $order_by) ? $ord_img : ''?></th>
		<th>Type</th>
		<th><a href="<?=htmlspecialchars($sort_url . 'category')?>">Category<?=('category' == $order_by) ? $ord_img : ''?></th>
		<th>Media Type</th>
		<th>Members</th>
		<th>NDA Signed</th>
		<th>Date Joined</th>
        <th>Last Connected</th>
        <th>Active</th>
		<th></th>
	</tr>
<?php
$mediaicons = socialMediaPartnerGetOptionList('media_icons');
foreach ($item_list as $id) {
    $item = socialMediaPartnerFromDatabase($id);
    $media_type_links = partnerlinksFromDatabase($id);
    $totalMember = 0;
    $mediaLink = '';
    foreach ( $media_type_links as $key => $links) {
         $mediaLink = $mediaLink.'<a href="'.$links['media_links'].'" target="_blank"><span style="font-size: 20px;15px;padding:3px;">'.$mediaicons[$links['media_type']].'</span></a>';
         $totalMember = $totalMember+$links['member_count'];
    }
?>
    <tr<?= ($alt = !$alt) ? ' class="alt"' : ''?>>
        <td><a href="/webadmin/member/?action=edit&amp;member_id=<?=$item['member_id']?>"><?=htmlspecialchars($item['company'])?></a></td>
        <td><?=htmlspecialchars($country_location_list[$item['country_location']] ?? $item['country_location'])?></td>
        <td><?=htmlspecialchars(ucfirst($item['member_type']))?></td>
        <td>
<?php
    foreach (json_decode($item['category'], true) ?? [] as $category_id) {
?>
            <?=strtr(htmlspecialchars($category_list[$category_id] ?? $category_id), [' ' => '&nbsp;'])?>,
<?php
    }
?>
        </td>
        <td>
            <?= $mediaLink ?>
        </td>
        <td><?= $totalMember ?></td>
        <td><?= $item['nda_signed'] == 'y'?'yes' : 'No' ?></td>
        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
        <td><?= date('d/m/Y', strtotime($item['last_connected_date'])) ?></td>
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
