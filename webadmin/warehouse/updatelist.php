<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

echo formatHTMLTitle('Media Updates');
$page    = intval($_GET['page'] ?? 0);
$order   = ('asc' == ($_GET['order'] ?? '')) ? 'asc' : 'desc';
$sort_by = [
    'id' => 'id',
][$_GET['sort_by'] ?? ''] ?? 'id';

$eq         = $_GET['eq'] ?? []; //array_merge($_GET['eq'] ?? [], $_GLOBALS['criteria_options']['distributor'] ?? []);
$lt         = $_GET['lt'] ?? null;
$gt         = $_GET['gt'] ?? null;
$item_query = socialMediaUpdateQuery($eq, $lt, $gt);
$item_list  = socialMediaUpdateSort($item_query, $sort_by);
if ('desc' != $order) {
    $item_list = array_reverse($item_list);
}
$staff_list = getStaffList();
$edit_url = 'sendupdate.php?id=';
$makenew_url = 'sendupdate.php?makenew=true&id=';
?>
<table class="table_grid" style="width:95%;margin:auto">
	 <tr>
		<th>Date Created</th>
		<th>Title</th>
		<th>Attachments</th>
		<th>Date Sent</th>
		<th>Sent By</th>
		<th>Total Member</th>
		<th></th>
	</tr>
<?php 
	foreach ($item_list as $id) {
		 $item = mediaPartnedNewsletterFromDatabase($id);
		 $partner_list = mediaPartnermailinglistFromDatabase($id);
	?>	 
	<tr>
		<td style="text-align:center;"><?php echo date("d/m/y",strtotime($item['created_at'])) ?></td>
		<td style="text-align:center;"><?php echo formatHTMLSafe($item['subject']) ?></td>
		<td style="text-align:center;">
			<?php
				$attachment = '';
				if($item['attached_file']){
                    foreach(explode(',',$item['attached_file']) as $key => $file) {
                         $fdata = explode('-', $file);
                      $attachment = $attachment.'<a href="screenshot/'.$fdata[0].'" target="_blank"><img width="50px" src="screenshot/'.$fdata[0].'"></a>';   
                    }
                }
                echo $attachment;
			?>
		</td>
		<td style="text-align:center;"><?php echo date("d/m/y",strtotime($item['created_at'])) ?></td>
		<td style="text-align:center;"><?php echo formatHTMLSafe($staff_list[$item['created_by']]) ?></td>
		<td><?= count($partner_list);?></td>
		<td><a href="<?php echo formatHTMLSafe($edit_url.$id) ?>" target="_blank">Edit</a>
		<a target="_blank" href="<?php echo formatHTMLSafe($makenew_url.$id) ?>">Make&nbsp;New</a>	
		</td>
	</tr>
	<?php }
?>
</table>