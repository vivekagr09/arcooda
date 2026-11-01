<!-- bare_footer.tpl -->

<?php
global $_GLOBALS;

$invoices_end_time = explode(" ", microtime());
if ('y' == $_GLOBALS['staff']['developer']) {
?>
<div class="print_hide">
	<br />
	<div style="color:#bbb;">Page generated in <?= bcadd(bcsub($invoices_end_time[0], $invoices_start_time[0], 6), bcsub($invoices_end_time[1], $invoices_start_time[1], 0), 6) ?> seconds from <?= count($DB->all_queries) ?> queries (<?php printf("%0.2f", array_sum($DB->time_queries))?> seconds)</div>
</div>
<?php
}
?>
</div>
</body>
</html>
