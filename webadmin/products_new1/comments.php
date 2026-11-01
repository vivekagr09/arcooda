<?php
include_once __DIR__ . '/common.inc.php';
?>

<script type="text/javascript">
//<![CDATA[
function printpage() {
    if (window.print) window.print();
    else alert('To print this page, select Print Frame from the file menu.');
}

function doPrint() {
    if (confirm("Click OK to print this document")) printpage();
}
//]]>
</script>

<?php
if ('' != $_REQUEST['print']) {
    $style = "background:#fff;";
?>
<body onLoad="doPrint()" style="<?=$style?>">
<?php
}
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content"><br />
<?php
$staff_list         = productGetOptionList('staff_id');
$id                 = intval($_REQUEST['id']);
$item_comment_query = productCommentQuery(array(
    'product_id'   => $id,
    'comment_type' => ('' != $_REQUEST['type']) ? $_REQUEST['type'] : 'admin',
));
$item_admin_notes = array_reverse(productCommentSearch($item_comment_query));
if (count($item_admin_notes) > 0) {
?>
<table class="table_grid">
    <tr>
        <th colspan="3"><b>All Admin Notes</b></td>
    </tr>
    <tr style="<?=('' != $style) ? $style : 'background:#E3EBF7'?>">
        <td width="10%"><b>Date</b></td>
        <td width="10%"><b>User</b></td>
        <td width="80%"><b>Comments</b></td>
    </tr>
<?php
foreach ($item_admin_notes as $comment_id) {
        $row = productCommentFromDatabase($comment_id);
?>
        <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td><?=date(_('Y-m-d'), (is_null($row['created_time'])) ? time() : $row['created_time'])?></td>
            <td><?php hecho($staff_list[$row['created_by']])?></td>
            <td style="text-align:left"><?=nl2br(hecho($row['comments'], 1))?></td>
        </tr>
<?php
}
?>
</table>
<?php
}
?>
</div>