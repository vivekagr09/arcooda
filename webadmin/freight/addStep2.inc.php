<br><br>
<table align="center" width="50%" cellpadding="3" cellspacing="1">
<?php
	 $numZones = $_POST["numZones"];
	 for ($i=0; $i < $numZones ; $i++)
	 {
			echo "<tr><td class=\"left\">Zone Name:";
			echo $i+1;
			echo "</td>";
			echo "<td class=\"right\"><input type=\"text\" name=\"zones[$i]\" tooltip=\"Give each zone a name\"></td></tr>";
	 }
?>
</table>
<br>
<input type="hidden" name="step" value="2">
<input type="hidden" name="name" value="<?= stripslashes($_POST['name']) ?>">
<input type="hidden" name="accountno" value="<?= stripslashes($_POST['accountno']) ?>">
<input type="hidden" name="base_currency" value="<?= $_POST['base_currency'] ?>">
<input type="hidden" name="customer_discount" value="<?= $_POST['customer_discount'] ?>">
<input type="hidden" name="store_discount" value="<?= $_POST['store_discount'] ?>">
<input type="hidden" name="handling" value="<?= $_POST['handling'] ?>">
<input type="hidden" name="hndasperc" value="<?= $_POST['hndasperc'] ?>">
<input type="hidden" name="source" value="<?= $_POST['source'] ?>">
<input type="hidden" name="numZones" value="<?= $_POST['numZones'] ?>">
<div align="center">
	<input type="submit" value="OK">
</div>