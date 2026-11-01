<!-- members_login.tpl.php -->

<?php
$clogins = count($member_logins);
if ($clogins > 10) {
    $member_logins = array_chunk($member_logins, 10);
    $member_logins = reset($member_logins);
}

?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionLogin\');return false">Login History</a>')?>

<div id="sectionLogin">
<?php

include __DIR__ . '/list_logins.tpl.php';

?>
	<br />
	<div style="text-align:center"><input type="button" value="View All (<?=$clogins?>)" onclick="window.open('.?action=login&amp;member_id=<?=$member_id?>')" /></div>
</div><br />
