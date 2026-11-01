<!-- list.tpl -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" alt="" />';

$sort_url           = $_REQUEST;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $_REQUEST;
$edit_url['action'] = 'edit';
unset($edit_url['contractor_id']);
$edit_url = '.?' . http_build_query($edit_url) . '&contractor_id=';

$view_url           = $_REQUEST;
$view_url['action'] = 'view';
unset($view_url['contractor_id']);
$view_url = '.?' . http_build_query($view_url) . '&contractor_id=';

$unlock_url           = $_REQUEST;
$unlock_url['action'] = 'unlock';
unset($unlock_url['contractor_id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&contractor_id=';

$select_url           = $_REQUEST;
$select_url['action'] = 'select';
unset($select_url['contractor_id']);
$select_url = '.?' . http_build_query($select_url) . '&contractor_id=';

$country_list = getCountryList(false);
$site_list    = getWebsitesList();
?>

<?=formatHTMLTitle('Contractor List')?>

<div>
	<table class="table_grid">
		<tr>
			<th><a href="<?php hecho($sort_url . 'company')?>">Company</a><?= ('company' == $order_by) ? $ord_img : '' ?></th>
			<th><a href="<?php hecho($sort_url . 'full_name')?>">Contact Name</a><?= ('full_name' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url . 'phone_number')?>">Phone Number</a><?= ('phone_number' == $order_by) ? $ord_img : '' ?></th>
			<th><a href="<?php hecho($sort_url . 'email_address')?>">Email</a><?= ('email_address' == $order_by) ? $ord_img : '' ?></th>
			<th><a href="<?php hecho($sort_url . 'country')?>">Country</a><?= ('country' == $order_by) ? $ord_img : '' ?></th>
			<th>Category</th>
			<th>Performance</th>
			<th>Status</th>
			<th>Cost</th>
			<th>Last Updated</th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach ($contractor_list as $contractor_id) {

        $contractor = contractorFromDatabase($contractor_id);
		$contractor_requests      = contractorCommentSearch(array('contractor_id' => $contractor_id, 'comment_type' => 'warning'));
		if (count($contractor_requests) > 0) {
			$request_tooltip = '<table style="width:300px;text-align:center">';
			foreach ($contractor_requests as $request_id) {
				$request = contractorCommentFromDatabase($request_id);

				if($request) {

					$request_tooltip .= '<tr><td>' . date("d M, Y", ($request['comment_time'])) . '</td><td>' . nl2br(hecho($request['comment_text'], 1)) . '</td></tr>';
				}
			}
			$request_tooltip .= '</table>';
		}

    $contractor_lock   = []; //contractorGetLock($contractor_id);
    $contractor_lock['staff_id'] =$_GLOBALS["staff"]["id"];
?>
		<!-- <?=($alt = !$alt) ? '<tr>' : '<tr class="alt">'?> -->
		<?= ($alt = !$alt) ? '<tr style="' . ($contractor['status'] == 1 ? 'background-color: #90EEA0;' : '') . '">' : '<tr class="alt" style="' . ($contractor['status'] == 1 ? 'background-color: #90EEA0;' : '') . '">'; ?>
		<!-- <?= ($alt = !$alt) ? '<tr ' . ((count($contractor_requests) > 0) ? 'style="background-color: #FFC0CB;" onmouseover="tt_in(' . htmlspecialchars(json_encode($request_tooltip)) . ');" onmouseout="tt_out();"' : ($contractor['status'] == 1 ? 'style="background-color: #90EEA0;"' : '')) . '">' : '<tr class="alt" ' . ((count($contractor_requests) > 0 ) ? 'style="background-color: #FFC0CB;" onmouseover="tt_in(' . htmlspecialchars(json_encode($request_tooltip)) . ');" onmouseout="tt_out();"' : ($contractor['status'] == 1 ? 'style="background-color: #90EEA0;"' : '')) . '>';
?> -->

			
			<td style="text-align:center;white-space:nowrap;"><?php hecho($contractor['company'])?></td>
			<td style="text-align:center;white-space:nowrap;"><?php echo ucfirst($contractor['name']). ' '.ucfirst($contractor['surname']); ?></td>
            <td style="text-align:center;white-space:nowrap;"><?php hecho($contractor['phone_number'])?></td>
			<td style="text-align:center;white-space:nowrap;"><?php hecho($contractor['email_address'])?></td>
			<td style="text-align:center;white-space:nowrap;"><i class="flagicon flagicon-<?=strtolower($contractor['country'])?>" title="<?php hecho($country_list[$contractor['country']])?>"></i></td>
			<td style="text-align:center;white-space:nowrap;"><?php echo contractorGetOptionList('contractor_category')[$contractor['contractor_category']]?></td>
            <td <?= (count($contractor_requests) > 0) ? 'style="background-color: #FFC0CB;text-align:center;white-space:nowrap;" onmouseover="tt_in(' . htmlspecialchars(json_encode($request_tooltip)) . ');" onmouseout="tt_out();"' : 'style="text-align:center;white-space:nowrap;"'?>><?php hecho($contractor['performance'])?></td>
			 <td style="text-align:center;white-space:nowrap;"><?php if (0 == $contractor['status']) {
                echo 'Inactive';
                }else {
                    echo 'Active';
                }
                ?>
            </td>
            <td style="text-align:center;white-space:nowrap;"><?= $contractor['cost'].' '.$contractor['currency']?></td>
            <td style="text-align:center;white-space:nowrap;"><?=($contractor['modified']) ? date("d/m/Y", strtotime($contractor['modified'])) : 'N/A'?></td>
			<td style="text-align:center;white-space:nowrap;">
<?php
if ($popup_window) {
?>
				<input type="button" value=" Select " onclick="<?php hecho('window.location = ' . jecho($select_url . $contractor_id, 1))?>" />
				<input type="button" value=" Edit " onclick="<?php hecho('window.open(' . jecho($edit_url . $contractor_id, 1))?>" />
<?php
} else {
        if (false === $contractor_lock || $contractor_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
				<a href="<?php hecho($edit_url . $contractor_id)?>" target="_blank">Edit</a>
<?php
} else {
?>
				<a href="<?php hecho($view_url . $contractor_id)?>" target="_blank">View</a>
<?php
}
        if (false !== $contractor_lock && $contractor_lock['staff_id'] != $_GLOBALS["staff"]["id"]) {
?>
				<a href="<?php hecho($unlock_url . $contractor_id)?>">Unlock</a>
<?php
}
    }
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectioncontractorList_hideNote">List of contractors can be viewed by Clicking "contractor List" above<br /></div>

<!-- /list.tpl -->
