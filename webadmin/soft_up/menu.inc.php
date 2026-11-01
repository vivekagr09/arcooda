<?php
/**
 * @var string[] $allowed_sections
 */

global $_GLOBALS;
global $DB;

if (has_permission('SOFTUP', $allowed_sections) && 'y' == $_GLOBALS['staff']['developer']) {

    $temp2 = '';
    if ($temp = $DB->query_first(sprintf(
        "SELECT `visited` FROM `software_visit` WHERE `user` = %s",
        $DB->escape($_GLOBALS['staff']['id'])
    ), MYSQLI_ASSOC)) {
        if ($temp = ($DB->query_first(sprintf(
            "SELECT * FROM `software_updates` WHERE `up_date` > %s",
            $DB->escape($temp['visited'])
        ), MYSQLI_ASSOC))) {
            $temp2 = ' bgcolor="red"';
        }
    }

?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"<?=$temp2?>><b>Software Updates</b></td></tr>
	<tr><td<?=$temp?>><ul class="blocklist">
		<li><a href="/webadmin/soft_up/add.php">Add Software Update</a></li>
		<li><a href="/webadmin/soft_up/">View Software Updates</a></li>
	</ul></td></tr>
</table>
<?php
}
