</div>
<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/footer.inc.php');
?>