<!-- members_statement.tpl.php -->
<?php
/*
if ($only_invoices) {
*/
    $company_name = ('' != $member['company_name']) ? $member['company_name'] : ($member['first_name'] . ' ' . $member['last_name']);
    // now restrict the display to the last 10
    $alltogether      = is_array($alltogether) ? $alltogether : array();
    $total_statements = count($alltogether);
    $tmp              = array_chunk($alltogether, 10, true);
    if (count($alltogether)) {
        $alltogether = reset($tmp);
    }
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatement\');return false;">Customer Statement</a>')?>
<div id="sectionStatement">
<?php
include __DIR__ . '/list_statement.tpl.php';
?>
<br /><div style="text-align:center"><input style="<?=($total_statements <= 10) ? 'display:none;' : ''?>" type="button" value="View All (<?=$total_statements?>)" onclick="window.open('members_statement.php?member_id=<?=$member_id?>')" /><input type="button" onclick="window.open('members_statementXLS.php?member_id=<?=$member_id?>')" value="Export To Excel" /></div></div><br />
<?php
/*
}
*/
?>
<!-- /members_statement.tpl.php -->