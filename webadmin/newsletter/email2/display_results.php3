<?php
$title = 'Email List';
include 'email2/include/header.inc';
if (file_exists("email2/variable_list.php3"))
{
	switch ($action)
	{
	case "submit":
		include "email2/variable_list.php3";
		getEmailList($interests, $business, $send_updates, $send_specials, $active, $canceled, $send_product_news, $include_mailinglist);
		show_options();
	break;
	default:
		showError();
	break;
	}
}
else
{
	showError();
}
include 'email2/include/footer.inc';

function show_options()
{
?>
<form action="email.php3?page=email" method="post">
	<div align="center">
		<input type="button" name="Submit" value="&lt;&lt; Back" onclick="history.back(-1)" class="button" />
		<input type="submit" name="Submit" value="Next >>" class="button" />
	</div>
</form>
<?php
}

function getEmailList($interests, $business, $send_updates, $send_specials, $active, $canceled, $send_product_news, $include_mailinglist)
{
	global $DB;
	$DB->query("DELETE FROM mail_list_tmp");
	echo '<blockquote>';
	$vars['send_weekly'] = intval($send_updates);
	$vars['send_specials'] = intval($send_specials);
	$vars['send_press'] = intval($send_product_news);
	$send_clause = ' (A.member_id = 0 ';
	if ($send_updates)
	{
		$send_clause.= 'OR A.send_site_updates = 1 ';
		echo '<LI>Weekly news, latest product, forum report email.';
	}
	if ($send_specials)
	{
		$send_clause.= 'OR A.send_specials = 1 ';
		echo '<LI>Specials from trading companies, members wanted and classifieds email.';
	}
	if ($send_product_news)
	{
		$send_clause.= 'OR A.send_product_news = 1 ';
		echo '<LI>Press releases and product information from manufacturers email.';
	}
	if (!$send_product_news && !$send_specials && !send_updates)
	{
		$send_clause.= 'OR (A.send_site_updates = 2 AND A.send_specials = 2 AND A.send_product_news =2) ';
		echo '<br />You did not select a email type, please click "Back" below<br />and select an email type in Step 1.<br />';
	}
	$send_clause.= ')';
	if ($active == 1)
	{
		$active_clause = 'AND A.active = 1';
		echo '<LI>Sending email to ACTIVE members only.';
	}
	else
	{
		echo '<BR><BR><FONT COLOR="#FF0000">You are sending to NON-ACTIVE members also.</FONT><BR>';
	}
	if ($canceled == 1)
	{
		$statement = 'SELECT member_id FROM cancelled_members ORDER BY member_id ASC';
		$query_result = $DB->query($statement) or die('3rd query failed');
		while ($row = $DB->fetch_array($query_result))
		{
			$size_cancelled = count($cancelled_members);
			$cancelled_members[$size_cancelled] = $row['member_id'];
		}
	}
	else
	{
		echo '<BR><BR><FONT COLOR="#FF0000">You are also sending to CANCELLED members.</FONT><BR>';
	}
	echo '</BLOCKQUOTE>';
	$clause1 = "B.business_type = 0 ";
	for ($i = 0;$i < count($business);$i++)
	{
		if ($business[$i][0] != 0)
		{
			$tmp_var = $business[$i][0];
			$clause1.= "OR B.business_type = $tmp_var ";
		}
	}
	$query = "SELECT DISTINCT A.member_id, A.name, A.email_address from members=A, business_type_xref=B WHERE (($clause1) AND B.member_id = A.member_id) AND $send_clause $active_clause ORDER BY A.member_id ASC";
	$query_results = $DB->query($query) or die('1st query error');
	$count = 0;
	$result1;
	while ($results = @$DB->fetch_array($query_results))
	{
		$needle = $results['member_id'];
		if (!(exist_in_array($needle, $cancelled_members)))
		{
			$result1[$count] = $results;
			$count++;
		}
	}
	$clause2 = "C.interest_id = 0 ";
	for ($i = 0;$i < count($interests);$i++)
	{
		if ($interests[$i][0] != 0)
		{
			$tmp_var = $interests[$i][0];
			$clause2.= "OR C.interest_id = $tmp_var ";
		}
	}
	$query = "SELECT DISTINCT A.member_id, A.name, A.email_address from members=A, business_interests_xref=C WHERE (($clause2) AND C.member_id = A.member_id) AND $send_clause $active_clause ORDER BY A.member_id ASC";
	$query_results = $DB->query($query);
	$count = 0;
	$result2;
	while ($results = @$DB->fetch_array($query_results))
	{
		$needle = $results['member_id'];
		if (!(exist_in_array($needle, $cancelled_members)))
		{
			$result2[$count] = $results;
			$count++;
		}
	}
	$final_result = new_merge_array($result1, $result2, $cancelled_members);
	//Next line was added
	if(!is_array($final_result)) $final_result=array();
	if ($include_mailinglist == 1)
	{
		$statement = "SELECT email FROM mailing_list";
		$result = $DB->query($statement);
		while ($row = $DB->fetch_array($result))
		{
			$array_ptr = count($final_result);
			$final_result[$array_ptr]['name'] = 'HG Member';
			$final_result[$array_ptr]['email_address'] = $row[email];
		}
	}

//Start New Code
/*
	//2492 results
	array_unshift($final_result,array('name'=>'Natalia','email_address'=>'dgault@highwaygroup.net'));
	//array_push($final_result,array('name'=>'Natalia','email_address'=>'dgault@highwaygroup.net'));
	for ($i = 0;$i < count($final_result);$i++)
	{
		$DB->query("INSERT INTO mail_list_tmp (email, name) VALUES(" . formatSQLString($final_result[$i]['email_address']) . ", " . formatSQLString($final_result[$i]['name']) . ")");
	}
*/
//End New Code
//Start Attempt 3
	//2492 results
	$DB->query("INSERT INTO mail_list_tmp (email, name) VALUES('dgault@highwaygroup.net','Natalia')");
	foreach($final_result as $row)
		$DB->query("INSERT INTO mail_list_tmp (email, name) VALUES('" . addslashes($row['email_address']) . "','" . addslashes($row['name']) . "')");
//End Attempt 3
//Start Old Code
/*
	//2495 results
	$tmp_i = count($final_result);
	$final_result[$tmp_i][name] = 'Last email in list';
	$final_result[$tmp_i][email_address] = 'mailinglist@highwaygames.com';
	$tmp_i = count($final_result);
	$final_result[$tmp_i][name] = 'Wei';
	$final_result[$tmp_i][email_address] = 'c9807224@studentmail.newcastle.edu.au';
	$tmp_i = count($final_result);
	$final_result[$tmp_i][name] = 'Stephen Jang';
	$final_result[$tmp_i][email_address] = 'efrangelico@excite.com.au';
	for ($i = 0; $i < count($final_result); $i++)
	{
		$DB->query("INSERT INTO mail_list_tmp (email, name)
					VALUES('" . addslashes($final_result[$i]['email_address']) . "',
					'" . addslashes($final_result[$i]['name']) . "')");
	}
*/
//End Old Code
	$total = $DB->query("SELECT COUNT(*) FROM mail_list_tmp");
	$vars[total] = mysql_result($total, 0);
	$vars[count] = 0;
	$vars[last_activity] = last_activity('Creating Email list');
	set_vars($vars);
	echo '<CENTER><FONT COLOR="#0000FF"><H3>Email Compilation Completed...</H3></FONT><BR><BR>';
	echo "<BR><B>Total Emails Compiled : " . $vars[total];
	echo "</B></CENTER><BR>\n";
}

function new_merge_array($A, $B, $cancelled_members)
{
	$B_pointer = 0;
	for ($i = 0;$i < count($A);$i++)
	{
		$A_element = $A[$i]['member_id'];
		for ($j = $B_pointer;$j < count($B);$j++)
		{
			$B_element = $B[$j]['member_id'];
			if ($A_element == $B_element)
			{
				$B[$j] = 0;
				$B_pointer = $j+1;
				$j = count($B);
			}
			if ($A_element < $B_element)
			{
				$j = count($B);
			}
		}
	}
	for ($k = 0;$k < count($B);$k++)
	{
		if (gettype($B[$k]) == 'array')
		{
			$A_size = count($A);
			$A[$A_size] = $B[$k];
		}
	}
	return $A;
}

function exist_in_array($needle, $cancelled_members)
{
	for ($i = 0;$i < count($cancelled_members);$i++)
	{
		if ($needle == $cancelled_members[$i])
		{
			return true;
		}
	}
	return false;
}

function showError()
{
?>
The variable file has not been created. Did you get here by accident?<br />
Please goto <a href="email.php">Email Functions</a>
<?php
}
?>