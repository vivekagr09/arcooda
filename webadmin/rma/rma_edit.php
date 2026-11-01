<?php
$table = 'rma_service';

include_once __DIR__ . '/common.inc.php';

$script = 'rma_edit.php';
$gets   = get_vars();
?>

<form action="<?=$script . $gets?>" method="post">
	<br>
      <table width="100%" border="0" cellspacing="1" cellpadding="5">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Editing RMA</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="60%">
            <div align="right">
			<a href="<?=$script?>?action=add">Add New RMA</a> |
			<a href="rma_view_all.php?table=rma_service&query=">List All RMA</a>
			</div>
          </td>
        </tr>
      </table>


<?php

//search statement
$statement = "SELECT * FROM rma_service WHERE id='$id'";

//echo "Id_selected: $id";
$result   = $DB->query($statement);
$rma      = $DB->fetch_array($result);
$customer = $rma['customer'];
$cust_ref = $rma['cust_ref'];
$item     = $rma['item'];
$serial   = $rma['serial'];
$fault    = $rma['fault'];
$priority = $rma['priority'];
$sent_to  = $rma['sent_to'];
$status   = $rma['status'];
$comments = $rma['comments'];

function do_add()
{
    global $DB, $data, $user;

    $statement = "INSERT INTO rma_service( " . get_fields($data) . ", date_added) " .
    "VALUES( " . get_values($data) . ",NOW())";
    //echo $statement."<BR>";
    $DB->query($statement);

    $comment = addslashes(trim($data['comments']));
    //echo $comment;
    if ('' != $comment) {
        $id        = $DB->insert_id();
        $statement = "INSERT INTO rma_comments (rma, date, person, comment)
						VALUES ('$id', NOW(), '$user', '$comment')";
        $DB->query($statement);
    }

    echo "New RMA record added.<br />";
}

function do_edit()
{
    global $DB, $data, $id, $comment;
    $user = get_user();

    $statement = "UPDATE rma_service SET " . get_set_values($data) . " WHERE id='$id'";
    $DB->query($statement);

    $comment = addslashes(trim($data['comments']));
    //echo $comment;
    if ('' != $comment) {
        $statement = "INSERT INTO rma_comments (rma, date, person, comment)
						VALUES ('$id', NOW(), '$user', '$comment')";
        $DB->query($statement);
    }

    echo "ID: $id Updated<br />";
}

if (trim($query) == '') {

    switch ($action) {
        case "do_add":
            do_add();
            break;
        case "do_edit":
            do_edit();
            break;
        case "do_delete":
            do_delete();
            break;
        case "add":
            include 'rma_add_form.inc.php';
            break;
        case "edit_rma":
            //echo "id= $id" ;
            include 'rma_edit_form.inc.php';
            include 'rma_comment.inc.php';
            break;
        case "delete":
            include '../generic_delete_form.inc.php';
            break;
        case "print":
            include 'rma_print_form.inc.php';
            break;
    }
}

?>


</form>



<?php
include '../footer.inc.php';

?>
