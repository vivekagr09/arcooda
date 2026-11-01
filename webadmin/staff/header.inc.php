<?php
include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
?>
<div class="staff_content">
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td style="width:30%;background:#eee;text-align:center"><b><?=htmlspecialchars(_('Staff Administration'))?></b></td>
            <td style="width:70%;background:#eee;text-align:right">
                <a href=".?action=add">Add New [Module]</a>
                | <a href=".?action=list">List All Staff</a>
            </td>
        </tr>
    </table>
    <br />
