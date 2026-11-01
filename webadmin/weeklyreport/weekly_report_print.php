<?php
$id = $_POST['idarray'];
if ($id) {
    $items = implode(",", $id);
    //echo $somedate;
    //echo "<form><input type='button' value='Printer Version' onclick=\"'); \"/></form>";
    ?>
			<script language="javascript">
			function openWindow()
			{
				window.open('weekly_report_print_window.php?email=<?=$email?>&from=<?=$from?>&to=<?=$to?>&somedate=<?=$somedate?>&ids=<?=$items?>');
			}
				openWindow();
			    history.back();
			</script>
<?php

} else {
    echo "<blockquote>You must select items to before they can be printed <br>";
    echo "<form><input type='button' value='Go Back' onclick='history.back()'></form></blockquote>";
}
