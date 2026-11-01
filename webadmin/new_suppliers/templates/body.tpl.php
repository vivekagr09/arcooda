<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/header.inc.php";
?>
<!-- end common header -->
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%"><div align="center"><?php echo $title ?></div></td>
		<td class="pageMenu" width="80%"><div align="right">
<?php
if (is_array($headerlinks_array) && count($headerlinks_array)) {
    $i = 0;
    foreach ($headerlinks_array as $t => $u) {
        echo ($i) ? " | " : "";
?>
			<a href="<?php echo htmlspecialchars($u) ?>"><?php echo $t ?></a>
<?php
        if ($i == $linksperline - 1) {
            echo "<br />";
            $i = -1;
        }
        $i++;
    }
}
?>
		</div></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>
<div id="sposcontent">
	<!-- messages -->
	<div>
		<h1><?php echo $header ?></h1>
		<?php echo $messages ?>
	</div>
	<!-- end messages -->
	<!-- page content -->
	<div align="center">
		<?php echo $content ?>
	</div>
	<!-- end page content -->
</div>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>