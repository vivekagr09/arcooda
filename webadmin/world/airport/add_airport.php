<script language="Javascript">
function check(obj){
	if(obj.elements['data[country]'].value == ""){
		alert("You have not selected a country");
		return false;
	}
	else
		return true;
}
</script>
<?php
function display_fields($posted){
	global $DB,$num;
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="airports.php" method="POST" onSubmit="return check(this);">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
<?php
	for($i = 0; $i < $num; $i++){
?>
  <tr>
    <td class="left" nowrap><b>New Airport Name :</b></td>
    <td class="right"><input type="text" name="data[<?php echo $i?>][name]" size="40" style="width:400px"></td>
  </tr>
<?php
	}
?>
  <tr>
    <td class="left" nowrap><b>Country :</b></td>
    <td class="right">
		<select name="data[country]" style="width:400px">
			<option value="">[SELECT ONE]</option>
      <?=formatHTMLOptions(getCountryList(false))?>
	</td>
  </tr>
    <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
		<input type="hidden" name="num" value="<?php echo $posted['num']?>">
        <input type="hidden" name="action" value="do_add">
        <input type="submit" name="do_action" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
  </form>
</table>
<?php
}
function display_num(){
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="airports.php" method="POST" onSubmit="return check(this);">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>Number of Airports to be entered:</b></td>
    <td class="right"><input type="text" name="num" size="40" style="width:400px"></td>
  </tr>
  <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
		<input type="hidden" name="action" value="add">
        <input type="hidden" name="step" value="2">
        <input type="submit" name="do_action" value="OK">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
  </form>
</table>
<?php
}
?>

<?php
$step = $_REQUEST['step'];
$number = $_REQUEST['num'];
if($step == '1' || $step == "")
	display_num();
else
	display_fields($posted);
?>