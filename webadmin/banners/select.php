<?php
include_once __DIR__ . '/common.inc.php';

$js = array();
if (is_array($_REQUEST['item_list']) && 'y' == $_REQUEST['multiple']) {
    $item_list       = array_unique($_REQUEST['item_list']);
    $js['item_list'] = array();
    foreach ($item_list as $id) {
        $row                 = bannerFromDatabase($id);
        $row['date_str']     = date('d/m/Y', $row['time_added']);
        $js['item_list'][] = $row;
    }
} else {
    $id             = intval($_REQUEST['id']);
    $js             = bannerFromDatabase($id);
    $js['date_str'] = date('d/m/Y', $js['time_added']);
}
$js['return_message'] = $_REQUEST['return_message'];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title></title>
    <script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($js)?>;
if (window.opener != null && window.opener.setBannerData != null) {
    window.opener.setBannerData(data);
    window.close();
}
else
    alert('Unable to send banner back to form');
//]]>
	</script>
  </head>
  <body></body>
</html>