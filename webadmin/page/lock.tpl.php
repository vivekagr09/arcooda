<!-- lock.tpl -->

<?php
if ($item_lock['staff_id'] != $_GLOBALS['staff']['staff_id']) {
?>
<div style="border:1px solid black;font-weight:bold;background-color:pink;text-align:center;padding:8px;">
<?php
    if (false === $item_lock) {
        echo 'This topic is not locked to you; You cannot submit any changes.';
    } else {
        $temp         = pageGetOptionList('staff_id');
        $time_to_live = $item_lock['expiry_time'] - time();
        echo 'This topic is currently locked to ' . $temp[$item_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
</div>
<br /><br />
<?php
}
?>

<!-- /lock.tpl -->
