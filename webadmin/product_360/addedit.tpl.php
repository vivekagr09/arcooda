<?php
/**
 * @var int     $id
 * @var mixed[] $item
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if($action=='add'){
	include __DIR__ . '/details.tpl.php';
	include __DIR__ . '/product_images.php';
	include __DIR__ . '/marker.tpl.php';
	include __DIR__ . '/status_detail.php';
}else{
	include __DIR__ . '/details.tpl.php';
	include __DIR__ . '/product_images.php';
	include __DIR__ . '/marker.tpl.php';
	include __DIR__ . '/activate_marker.php';
	include __DIR__ . '/status_detail.php';
}

?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
