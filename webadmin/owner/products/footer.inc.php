    </div>
</div>
<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
}

if (array_key_exists('print', $_REQUEST) && 'true' == $_REQUEST['print']) {
?>

<script type="text/javascript">
//<![CDATA[
window.print();
//]]>
</script>

<?php
}
