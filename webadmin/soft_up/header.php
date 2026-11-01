<?php
include_once __DIR__ . '/common.inc.php';

global $DB;
global $_GLOBALS;

//Creates A New User If Need Be
$DB->query(sprintf(
	"INSERT INTO `software_visit` (`user`, `visited`) VALUES (%s, NOW()) ON DUPLICATE KEY UPDATE `visited` = NOW()",
	$DB->escape($_GLOBALS['staff']['id'])
));

if (!function_exists('formatHTMLTitle')) {
	/**
	 * @param string $title
	 *
	 * @return string
     */
    function formatHTMLTitle($title)
    {
        return "<br />\n<div class=\"flagHeader\"><span>" . $title . "</span></div>\n<br />\n";
    }
}

$row = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM `software_updates`"
), MYSQLI_ASSOC);
?>
<br><br>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td class="pageMenu" width="20%"><div align="center">Software Updates</div></td>
		<td class="pageMenu" width="80%">
			<div align="right">
				<a href="add.php">Add New Update</a> |
				<a href="index.php">View All Updates (<?=$row['total']?>)</a>
			</div>
		</td>
	</tr>
</table>
<br>

<!--<p align="right">Software Updates: <a href="add.php">Add</a> | <a href="index.php">View</a></p>-->

<script type="text/javascript">
function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}

function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	if(theSect==null) return;
	if(theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
</script>

<div id="supcontent" align="center">
