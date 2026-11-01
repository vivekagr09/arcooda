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
<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="airports.php" method="POST" onSubmit="return check(this);">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>Seaport Name :</b></td>
<?php
$airport = $DB->query_first(sprintf(
    "SELECT * FROM `world`.`airports` WHERE `id` = %d LIMIT 1",
    $posted['id']
), MYSQLI_ASSOC);
?>
    <td class="right"><input type="text" name="data[name]" value="<?=htmlspecialchars($airport['name'])?>" size="40" style="width:400px"></td>
  </tr>
  <tr>
	<td class="left" nowrap><b>Country :</b></td>
    <td class="right">
		<select name="data[country]" style="width:400px">
			<option value="" selected>[SELECT ONE]</option>
      <?=formatHTMLOptions(getCountryList(false), $airport['country'])?>
<?php
echo '</select>';
?>
	</td>
  </tr>
    <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
		<input type="hidden" name="id" value="<?php echo $id?>">
        <input type="hidden" name="action" value="do_edit">
        <input type="submit" name="do_action" value="Update">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
  </form>
</table>