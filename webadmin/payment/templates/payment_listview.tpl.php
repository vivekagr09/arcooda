<!-- payment_listview.tpl -->

<?php
$group_by                = array('year', 'staff_id');
$payment_list            = paymentSearch();
$year_staff_payment_list = paymentChunk($payment_list, $group_by);

ksort($year_staff_payment_list);
$year_staff_payment_list = array_reverse($year_staff_payment_list, true);

foreach ($year_staff_payment_list as $start_time => $staff_payment_list) {
    $package_list = array();
    foreach ($staff_payment_list as $staff_id => $payment_list) {
        $package                 = paymentMerged($payment_list);
        $package['view_url']     = $staff_id . '_' . $start_time;
        $package['name']         = $all_staff_list[$staff_id];
        $package_list[$staff_id] = $package;
    }
?>
	<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'Year_' . $start_time . '\');return false">Time Sheets</a>', date('Y', $start_time) . '-' . (date('Y', $start_time) + 1))?><br />

<div id="Year_<?=$start_time?>">
<?php
include 'payment_packagelist2.tpl.php';
?>
</div>
<?php
}
?>

<!-- /payment_listview.tpl -->
