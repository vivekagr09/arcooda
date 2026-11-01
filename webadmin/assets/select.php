<?php
/**
 * @var int      $asset_id
 * @var string   $asset_type
 * @var string   $section_singular
 */
include_once __DIR__ . '/common.inc.php';

$action = 'select';
if($_GET['return_message']=='attach_delete'){
	$asset['asset_id']=$asset_id;
	$asset['return_message'] = $_GET['return_message'];
}else{
	$asset                   = assetFromDatabase($asset_type, $asset_id);
	$asset['asset_nicedate'] = date('d/m/Y', $asset['asset_time']);
	$asset                   = assetToCompatible($asset_type, $asset);
	$asset['return_message'] = $_GET['return_message'];
}

?>
<!DOCTYPE html>
<html>
	<head></head>
	<body>

<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($asset) ?>;
if (window.opener != null && window.opener.setAssetData != null) {
	window.opener.setAssetData(data);
} else {
	alert('Unable to send <?= strtolower($section_singular) ?> back to form');
}
window.close();
//]]>
</script>

	</body>
</html>
