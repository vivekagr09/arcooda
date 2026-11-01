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
	else if(obj.elements['data[name]'].value == 'City/Region name' || obj.elements['data[name]'].value == ""){
		alert("No Timezone name has been entered");
		return false;
	}
	else
		return true;
}
function updateLink(){
	var val = document.add.elements['data[name]'].value;
	var val_link = "View Time Zone Details for "+val;
	if(val == "")
		val_link = "";
	var link = "http://www.timeanddate.com/worldclock/results.html?query="+val;
	document.getElementById('a').innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target=\"_blank\" 	href=\""+link+"\">"+val_link+"</a>";
}
function popup(){
	var win = window.open("help.html","Print","location=yes, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, width=650, height=850");
	//win.show();
	win.focus();
}
</script>
<table border="0" align="center" cellpadding="4" cellspacing="0">
<form name="add" action="times.php" method="POST" onSubmit="return check(this);">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>New Time Zone Name :</b></td>
    <td class="right"><input type="text" name="data[name]" size="40" value="City/Region name" style="width:400px" onFocus="if(this.value == 'City/Region name') this.value='';" onKeyUp="javascript:updateLink()"></td>
  </tr>
  <tr>
    <td class="left" nowrap><b>Offset :</b></td>
    <td class="right">
		<select class="input2" name="data[offset]">
			<option value="">[SELECT ONE]</option>
<?php
$offset = -12;
while ($offset <= 12) {
    echo '<option value="' . $offset . '">';
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
		<span id="a">
		</span>
	</td>
  </tr>
  <tr>
	<td class="left" nowrap><b>Observing Daylight savings :</b></td>
	<td class="right">
		<select class="input2" name="data[day_light]">
			<option value="y">YES</option>
			<option value="n" selected>NO</option>
		</select>
	</td>
  </tr>
  <tr>
    <td class="left" nowrap><b>Country :</b></td>
    <td class="right">
		<select name="data[country]" style="width:400px">
			<option value="">[SELECT ONE]</option>
      <?=formatHTMLOptions(getCountryList(false))?>
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
        <input type="hidden" name="action" value="do_add">
        <input type="submit" name="do_action" value="Add">
        <input type="reset" name="clear" value="Clear">
		<input type="button" name="help" value="Help" onClick="javascript:popup()">
      </p></td>
  </tr>
  </form>
</table>