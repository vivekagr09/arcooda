<!-- end common header -->

<h1><?php echo $title ?></h1>

<!--
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center"><?=$title?></div>
		</td>
		<td class="pageMenu" width="80%">
			<div align="right">
<?php
if (is_array($headerlinks_array) && count($headerlinks_array)) {
	$i = 0;
	foreach ($headerlinks_array as $t => $u) {
?>
				<?=($i) ? " | " : ""?><a href="<?=$u?>"><?=$t?></a>
<?php
		if ($i == $linksperline - 1) {
			echo "<br />";
			$i = -1;
		}
		$i++;
	}
}
?>
			</div>
		</td>
        </tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>
-->

<div id="sposcontent">
<!-- messages -->
<div>
<h1><?=$header?></h1>
<?=$messages?>
</div>
<!-- end messages -->
<!-- page content -->
<div align="center">
<?=$content?>
</div>
<!-- end page content -->
</div>
