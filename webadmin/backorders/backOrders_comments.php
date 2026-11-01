<?php
include_once __DIR__ . '/common.inc.php';
?>
<script LANGUAGE="JavaScript">
<!--
function printpage() {
        if (window.print)
        {
			window.print();
        }
}
function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
-->
</script>
<?php
if (false == $print) {
?>
     <table width="100%" border="0" cellspacing="2" cellpadding="2">
		<tr><td>&nbsp;</td></tr>
		<tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Comments Admin</b></div>
          </td>
		  <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="backOrders_comments.php?less=<?=$less?>&action=add&backorder=<?=$backorder?>">Add New Comment</a> |
			<a href="backOrders_comments.php?less=<?=$less?>&list=true&backorder=<?=$backorder?>">List</a>
			</div>
          </td>
        </tr>
      </table>
<?php
}
if (true == $list && !empty($backorder)) {

?>
<p>
	<table align='center' border='1' width="650px">

	<tr><td colspan=6>&nbsp;</td></tr>
	<tr bgColor=#eeeeee>
		<td valign='top'><b>Date</b></td>
		<td valign='top'><b>Staff</b></td>
		<td valign='top'><b>Comment / Notes</b></td>
<?php
if (false == $print) {?>
			<td valign='top' colspan=2>&nbsp;</td>
		<?php }?>
	</tr>


<?php
    if ($comment_list = $DB->query(sprintf(
        "SELECT * FROM `highwaygroup`.`backorder_comment` WHERE `backorder` = %d",
        $backorder
    ))) {
        while ($comment_row = $DB->fetch_array($comment_list)) {
?>
		<tr bgColor=#f3f7fc >
		<td valign='top'><b>
<?php
            $date    = substr($comment_row['date'], 0, 10);
            $year    = substr($date, 0, 4);
            $month   = substr($date, 5, 2);
            $day     = substr($date, 8, 2);
            $newdate = $day . '-' . $month . '-' . $year;
            echo $newdate;
?>
			&nbsp;
			</b>
		</td>
		<td valign='top'>
<?php
            echo titleCase($comment_row['person']);
?>
			&nbsp;
		</td>
		<td valign='top'>
<?php
            $comment = nl2br(htmlspecialchars($comment_row['comment']));
            echo $comment;
?>
			&nbsp;
		</td>
<?php
            if (false == $print) {
?>
			<td>
				<a href='backOrders_comments.php?less=<?=$less?>&action=edit&backorder=<?=$backorder?>&id=<?=$comment_row['id']?>'>Edit</a>
			</td>
			<td>
				<a href='backOrders_comments.php?less=<?=$less?>&action=delete&backorder=<?=$backorder?>&id=<?=$comment_row['id']?>'>Delete</a>
			</td>
			</tr>

<?php
            }
        }
        $DB->free_result($comment_list);
    }
    echo '</table>';
    if (false == $print) {?>
		<table align='center'>
		<tr>
			<td><a href='backOrders_comments.php?less=<?=$less?>&list=true&print=true&backorder=<?=$backorder?>' target='_blank'>Print</a></td>
		</tr>
		</table>
<?php
} else {
?>
		<script LANGUAGE="JavaScript">doPrint();</script>

<?php
}
} else if (empty($backorder)) {
    echo 'Sorry, i have lost the back order you were refering to, Try again';
}
function add_new()
{
    global $DB, $comment, $backorder;

    $DB->query(sprintf(
        "INSERT into `highwaygroup`.`backorder_comment` (`backorder`, `date`, `person`, `comments`) VALUES (%d, NOW(), %s, %s)",
        $backorder,
        $DB->escape(get_user()),
        $DB->escape($comment)
    ));

    $id = $DB->insert_id();
    echo "Comment number:$id has been added.<br>";
}
function edit()
{
    global $DB, $comment, $id;

    $DB->query(sprintf(
        "UPDATE `highwaygroup`.`backorder_comment` SET `comment` = %s WHERE `id` = %d LIMIT 1",
        $DB->escape($comment),
        $id
    ));
    echo "Comment number:$id has been updated.<br />";

}
function delete()
{
    global $DB, $comment, $id;
    $DB->query(sprintf(
        "DELETE FROM `highwaygroup`.`backorder_comment` WHERE `id` = %d",
        $id
    ));
    echo "Comment number:$id has been deleted.<br>";
}
switch ($action) {
    case "add":
        include 'backOrders_comments_add_form.inc.php';
        break;
    case "edit":
        include 'backOrders_comments_edit_form.inc.php';
        break;
    case "delete":
        include 'backOrders_comments_delete_form.inc.php';
        break;
    case "do_add":
        add_new();
        break;
    case "do_edit":
        edit();
        break;
    case "do_delete":
        delete();
        break;

}
?>