<?php
$title = 'Highwaygames Email System';
$require_invert_script = true;
include 'email2/include/header.inc';
if ($do == 'reset') reset_list();
$statement = "SELECT COUNT(*) FROM mail_list_tmp WHERE response != 'Sent'";
$result = $DB->query($statement) or die($DB->error());
if (mysql_result($result, 0) > 0) 
{
	include 'email2/list_not_empty.inc.php';
	include 'email2/include/footer.inc';
	exit();
}
switch ($action) 
{
case "submit":
	display_forms();
	break;
default:
	display_forms();
	break;
}
include 'email2/include/footer.inc';

function display_forms() 
{
	global $DB;
	$query = "SELECT * FROM business_interests";
	$query_results = $DB->query($query);
?>
<form method="post" action="email.php3?page=display_results&action=submit" name="selections">
	<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr bgcolor="#eeeeee"><td colspan="2"><b><font size="2" color="#000066">Step 1</font></b><font size="2" color="#000066"> - Choose the type of email to send</font></td></tr>
		<tr>
			<td width="50%"><input type="checkbox" name="send_updates" value="1" /> Weekly news, latest product, forum report</td>
			<td width="50%"><input type="checkbox" name="active" value="1" checked="checked" /> Send to active memebers only</td>
		</tr>
		<tr>
			<td width="50%"><input type="checkbox" name="send_specials" value="1" /> Specials from trading companies, members' wanted and classifieds</td>
			<td width="50%"><input type="checkbox" name="canceled" value="1" checked="checked" /> Do NOT send to Canceled members</td>
		</tr>
		<tr>
			<td width="50%"><input type="checkbox" name="send_product_news" value="1" /> Press releases and product information from manufacturers</td>
			<td width="50%"><input type="checkbox" name="include_mailinglist" value="1" checked="checked" /> Include members from the mailing list</td>
		</tr>
	</table>
	<table border="0" width="100%">
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" bgcolor="#eeeeee"><b><font size="2" color="#000066">Step 2</font></b><font size="2" color="#000066"> - Chose which interests groups to send to</font></td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" bgcolor="#eeeeee"><div align="center"><font size="2" color="#000066"><b>Interests Groups :</b> <input type="checkbox" name="checkbox1" value="checkbox1" onclick="doSelectAll('checkList1', 'selections')" /> Invert Selection Below</font></div></td>
<?php
	$count = 0;
	$variable_tmp = "<" . "? ";
	$list1 = '<script>document.cookie="checkList1=0.';
	while ($interest_list = @$DB->fetch_array($query_results)) 
	{
		$tmp_id = $interest_list['interests_id'];
		$variable_tmp.= 'if (isset($interest_' . $tmp_id . ')){';
		$variable_tmp.= '$interests[' . $count . '][0] = $interest_' . $tmp_id . ";";
		$variable_tmp.= '$interests[' . $count . '][1] = "' . $interest_list['interests_type'] . '";';
		$variable_tmp.= '}else{ $interests[' . $count . "][0] = 0;\n";
		$variable_tmp.= '$interests[' . $count . '][1] = "' . $interest_list['interests_type'] . '";}';
		$count++;
		if ($count%2 != 0) 
		{
			echo "<TR>";
		}
		$list1.= 'interest_' . $tmp_id . '.';
		echo "<TD WIDTH=50%>";
		echo '<INPUT TYPE="checkbox" NAME="interest_';
		echo $tmp_id;
		echo '"VALUE="' . $tmp_id . '">';
		echo $interest_list['interests_type'];
		echo "</TD>\n";
		if ($count%2 == 0) 
		{
			echo "</TR>";
		}
	}
	$list1.= '0";</script>';
	echo $list1;
?>
	</table>
	<table border="0" width="100%">
		<tr>
			<td colspan="2" bgcolor="#eeeeee"><div align="center"><font size="2" color="#000066"><b>Business Types :</b> <input type="checkbox" name="checkbox2" value="checkbox2" onclick="doSelectAll('checkList2', 'selections')" /> Invert Selection Below</font></div></td>
<?php
	$query = "SELECT * FROM business_types";
	$query_results = $DB->query($query);
	$count = 0;
	$list2 = '<script>document.cookie="checkList2=0.';
	while ($business_list = @$DB->fetch_array($query_results)) 
	{
		$tmp_id = $business_list['business_id'];
		$variable_tmp.= 'if (isset($business_' . $tmp_id . ')){';
		$variable_tmp.= '$business[' . $count . '][0] = $business_' . $tmp_id . ";";
		$variable_tmp.= '$business[' . $count . '][1] = "' . $business_list['business_type'] . '";}';
		$variable_tmp.= 'else { $business[' . $count . "][0] = 0;\n";
		$variable_tmp.= '$business[' . $count . '][1] = "' . $business_list['business_type'] . '";}';
		$count++;
		if ($count%2 != 0) 
		{
			echo "<TR>";
		}
		$list2.= 'business_' . $tmp_id . '.';
		echo "<TD WIDTH=50%>";
		echo '<INPUT TYPE="checkbox" NAME="business_';
		echo $tmp_id;
		echo '"VALUE="' . $tmp_id . '">';
		echo $business_list['business_type'];
		echo "<br />\n";
		if ($count%2 == 0) 
		{
			echo "</TR>";
		}
	}
	$list2.= '0";</script>';
	echo $list2;
?>
		<tr>
			<td width="50%">&nbsp;</td>
			<td width="50%">&nbsp;</td>
		</tr>
		<tr bgcolor="#eeeeee"><td colspan="2"><font size="3"><b><font color="#000066" size="2">Step 3</font></b><font color="#000066" size="2"> - Click &quot;Next&quot; below to compile the email list, this could take a while</font></font></td></tr>
	</table>
	<br />
	<center><input type="submit" value="Next >>" class="button" /></center>
</form>
<br />
<?php
	$variable_tmp.= '?' . '>';
	$file_pointer = fopen("email2/variable_list.php3", "w");
	fwrite($file_pointer, $variable_tmp);
}

function reset_list() 
{
	global $DB;
	$statement = "DELETE FROM mail_list_tmp";
	$DB->query($statement) or die($DB->error());
	$var['count'] = '0';
	$var['last_activity'] = last_activity('Reseting list');
	set_vars($var);
	echo "<font color='red'>Mailing list reset done.</font>";
}
?>