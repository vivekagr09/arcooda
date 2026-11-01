<?php
include_once __DIR__ . '/common.inc.php';
?>
	</div>
</div>
<?php
$popup_window = isset($_GET['return_message']);

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
}
