    </div>
</div>

        <script type="text/javascript" src="common.js"></script>
<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
} else {
?>
    </body>
</html>
<?php
}

global $_GLOBALS;
if ($_GLOBALS['staff']['developer'] == 'y') {
    $invoices_end_time = explode(" ", microtime());
    echo bcadd(bcsub($invoices_end_time[0], $invoices_start_time[0], 6), bcsub($invoices_end_time[1], $invoices_start_time[1], 0), 6) . " seconds";
}
