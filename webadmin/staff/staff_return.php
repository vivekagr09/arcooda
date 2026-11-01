<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$staff_id = intval($posted['staff_id']);

$staff = staffFromDatabase($staff_id);
?>
<html><head></head><body>
<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($staff)?>;
if (window.opener != null && window.opener.setStaffData != null) {
    window.opener.setStaffData(<?=json_encode($posted['return_message'])?>, data);
<?php
if ('options_change' == $posted['return_message']) {
?>
} else {
    alert('Changes have been saved, you may need to refresh the page');
<?php
}
?>
}
window.close();
//]]>
</script>
</body></html>
