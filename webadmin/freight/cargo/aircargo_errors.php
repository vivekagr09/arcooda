<?php
//echo "I got here";
	$ship_from = $_POST[ship_from];
	$ship_to = $_POST[ship_to];
	$service_type = $_POST[service_type];
	$currency = $_POST[currency];
?>
		<script language="javascript">
			function openWindow()
			{
				window.open('aircargo_summary.php?ship_from=<?= $ship_from?>&ship_to=<?= $ship_to?>&service_type=<?= $service_type?>&currency=<?= $currency?>');
			}
			function goBack()
			{
				window.location='aircargo_admin.php?ship_from=<?= $ship_from?>&ship_to=<?= $ship_to?>&service_type=<?= $service_type?>&currency=<?= $currency?>&action=mainpage';
			}
		</script>
<?php
	if($ship_from == $ship_to && ($ship_from != 'ALL' || $ship_to != 'ALL'))
	{
		echo "<blockquote>Generally no one bothers to ship to the same location <br>";
		echo "<form accept-charset=\"utf-8\" autocomplete=\"off\" method=\"post\"><input type='button' value='Go Back' onclick='goBack();'></form></blockquote>";
	}
	else if ($ship_from != '' && $ship_to != '')
	{
?>
			<script language="javascript">
				openWindow();
			    goBack();
			</script>
<?php

	}
	else
	{
		echo "<blockquote>There was an erro processing your information please go back and try again <br>";
		echo "<form accept-charset=\"utf-8\" autocomplete=\"off\" method=\"post\"><input type='button' value='Go Back' onclick='goBack();'></form></blockquote>";
	}
?>