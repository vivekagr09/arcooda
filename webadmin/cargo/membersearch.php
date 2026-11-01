<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

moduleLoad('invoice');

if ("send" == $_POST['action']) {
	if ($row = $DB->query_first(sprintf(
    	"SELECT `member_id`, `name`, `surname` FROM `highwaygroup`.`new_members` WHERE `member_id` = %d LIMIT 1",
		$_POST['id']
	))) {
?>
<script type="text/javascript">
//<![CDATA[
window.opener.foo.cust.value = <?= jecho($row['member_id'], 1) ?>;
window.opener.foo.cust_name.value = <?= jecho(trim($row['name'] . ' ' . $row['surname']), 1) ?>;
window.close();
//]]>
</script>
<?php
	} else {
		print("Member not found\n");
	}
	die();
}

$Where = '';
$words = explode(" ", $_GET['search']);
foreach ($words as $word) {
    $word = trim($word);
    if ("" != $Where) {
        $Where .= " AND ";
    }
    $Where .= "(name LIKE '%{$word}%' OR surname LIKE '%{$word}%' OR member_id = '{$word}')";
}
?>
<script type="text/javascript">
//<![CDATA[
function SendForm(id){
	document.foo.id.value = id;
	document.foo.submit();
}
//]]>
</script>

<form accept-charset="utf-8" autocomplete="off" method="post" action="membersearch.php?search=<?= urlencode($_GET['search']) ?>" name="foo">
	<input type="hidden" name="id">
	<input type="hidden" name="action" value="send">
	<table width="100%" cellpadding="1" cellspacing="0">
		<tr bgcolor="#FFFFCC">
			<td>ID</td>
			<td>Name</td>
			<td>Email</td>
			<td>&nbsp;</td>
		</tr>
<?php
if ($data = $DB->query(sprintf(
	"SELECT * FROM `highwaygroup`.`new_members` WHERE %s LIMIT 50",
	$Where
))) {
	$alt = false;
	while ($row = $DB->fetch_assoc($data)) {
?>
		<tr<?= ($alt = !$alt) ? ' bgcolor="#cccccc"' : '' ?>>
			<td><?= htmlspecialchars($row['member_id']) ?></td>
			<td><?= htmlspecialchars(trim($row['name'] . ' ' . $row['surname'])) ?></td>
			<td><?= htmlspecialchars($row['email_address']) ?></td>
			<td><input type="button" value="Select" onclick="<?= htmlspecialchars('SendForm(' . jecho($row['member_id'], 1) . ')') ?>" /></td>
		</tr>
<?php
	}
	$DB->free_result($data);
}
?>
	</table>
</form>
