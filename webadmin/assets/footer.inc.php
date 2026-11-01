	</div>
</div>
<?php
$popup_window = array_key_exists('return_message', $_GET);

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
}
