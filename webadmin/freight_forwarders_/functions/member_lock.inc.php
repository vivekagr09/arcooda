<?php
if($time_now<1) $time_now=time();

$DB->query("DELETE FROM member_locks WHERE lock_expiry<UNIX_TIMESTAMP(NOW())");

function member_get_lock_name($member_id,$count=1)
{
	global $DB;
	if($count!=1)
	{
		$results=array();
		$query=$DB->query("SELECT lock_member_id, lock_user FROM member_locks");
		while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
			$results[$row['lock_member_id']]=$row['lock_user'];
		return $results;
	}
	else
	{
		$result=$DB->query_first("SELECT lock_user FROM member_locks WHERE lock_member_id=".$member_id);
		return $result['lock_user'];
	}
}

function member_lock($member_id,$user)
{
	global $DB,$time_now;
	member_unlock($member_id);
	$DB->query("INSERT INTO member_locks SET lock_member_id=".$member_id.", lock_user='$user', lock_expiry=(UNIX_TIMESTAMP(NOW())+300)");
}

function member_unlock($member_id)
{
	global $DB;
	$DB->query("DELETE FROM member_locks WHERE lock_member_id=".$member_id);
}

function member_unlock_user($user)
{
	global $DB;
	$DB->query("DELETE FROM member_locks WHERE lock_user=".$user);
}
?>
