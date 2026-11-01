<?php
include_once __DIR__ . '/common.inc.php';

$qry     = "SELECT COUNT(*) AS `total` FROM `world`.`language`";
$section = $DB->query_first($qry);
?>
 <table width="100%" border="0" cellspacing="1" cellpadding="5">
   <tr>
     <td bgcolor="#EEEEEE" width="20%">
       <div align="center"><b>Voltage</b></div>
     </td>
     <td bgcolor="#EEEEEE" width="60%">
        <div align="right">
		<a href="/webadmin/world/language/language.php?action=add">Add New Language</a> |
		<a href="/webadmin/world/language/language.php?action=view">View All Languages (<?=$section['total']?>)</a>
	 </div>
     </td>
    </tr>
 </table>