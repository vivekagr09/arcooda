<blockquote>
	<p>The temporary list create didn't finish sending. That is, there was an error in sending the emails previously.</p>
	<ul>
		<li><a href="sender.php?action2=send&resume=yes">Click here to resume sending using the last created email message</a></li>
		<li><a href="composer.php?action2=new_email">Click here to compose the email, then send it.</a></li>
		<li><a href="index.php?action=reset">Click here to clear the list.</a></li>
		<li><a href="tester.php">Click here to send a test message to a given address</a></li>
	</ul>

	Following is a list of the states:<br /><br />
	<ul>
<?php
$result=$DB->query("SELECT response, count(*) AS num FROM mailout_tmp_list GROUP BY response");
while($row=$DB->fetch_array($result))
{
	$response=$row[response];
	if($response=='')
		$response='Pending';
	echo "<li>$response : $row[num]</li>";
}
?>
	</ul>
</blockquote>
