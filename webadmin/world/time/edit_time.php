<?php
include_once '../world_functions.php';
?>
<script language="Javascript">
function check(obj){
	if(obj.elements['data[country]'].value == ""){
		alert("You have not selected a country");
		return false;
	}
	else if(obj.elements['data[offset]'].value == ""){
		alert("You have not selected the GMT Offset");
		return false;
	}
	else
		return true;
}
</script>
<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="times.php" method="POST" onSubmit="return check(this);">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>Time Zone Name :</b></td>
<?php
$str = "SELECT * FROM `world`.`time_zones` WHERE id = '$id'";
$res = $DB->query_first($str);
?>
    <td class="right"><input type="text" name="data[name]" value="<?=$res['name']?>" size="40" style="width:400px"></td>
  </tr>
  <tr>
    <td class="left" nowrap><b>Offset :</b></td>
	<td class="right">
		<select class="input2" name="data[offset]">
			<option value="">[SELECT ONE]</option>
<?php
$offset = -12;
while ($offset <= 12) {
    if (trim($res['offset']) == $offset) {
        echo '<option value="' . $offset . '" selected>';
    } else {
        echo '<option value="' . $offset . '">';
    }

    if (0.5 == $offset) {
        echo '(GMT +0:30)';
    } else if ($offset > 0) {
        if (sizeof(split_str($offset, '.')) > 1) {
            $st = split_str($offset, '.');
            echo '(GMT +' . $st[0] . ':30)';
        } else {
            echo '(GMT +' . $offset . ':00)';
        }

    } else {
        if (sizeof(split_str($offset, '.')) > 1) {
            $st = split_str($offset, '.');
            echo '(GMT ' . $st[0] . ':30)';
        } else {
            echo '(GMT ' . $offset . ':00)';
        }

    }
    echo '</option>';
    $offset += 0.5;
}
?>
		</select>
	</td>
  </tr>
  <tr>
	<td class="left" nowrap><b>Observing Daylight savings :</b></td>
	<td class="right">
		<select class="input2" name="data[day_light]">
<?php
if ('n' == $res['day_light']) {
    echo '<option value="y">YES</option><option value="n" selected>NO</option>';
} else {
    echo '<option value="y" selected>YES</option><option value="n">NO</option>';
}

?>
		</select>
	</td>
  </tr>
  <tr>
	<td class="left" nowrap><b>Country :</b></td>
    <td class="right">
		<select name="data[country]" style="width:400px">
			<option value="" selected>[SELECT ONE]</option>
      <?=formatHTMLOptions(getCountryList(false), $res['country'])?>
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
		<input type="hidden" name="id" value="<?=$id?>" />
        <input type="hidden" name="action" value="do_edit" />
        <input type="submit" name="do_action" value="Update" />
        <input type="reset" name="clear" value="Clear" />
      </p></td>
  </tr>
  </form>
</table>